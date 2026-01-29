<?php

namespace App\Services\News;

use App\DataTransferObjects\NewsArticleDto;
use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class NewsArticleService
{
    public function getArticles(): array|Paginator|CursorPaginator
    {
        $cacheKey = $this->buildCacheKey(Request::query());

        return Cache::remember($cacheKey, 300, function () {
            $queryBuilder = QueryBuilder::for(Article::class)
                ->allowedFilters([
                    AllowedFilter::partial('title'),
                    AllowedFilter::exact('source'),
                    AllowedFilter::partial('authors.name'),
                    AllowedFilter::partial('categories.name'),
                    AllowedFilter::exact('published_at'),
                ])
                ->allowedIncludes('authors', 'categories')
                ->allowedSorts([
                    'title',
                    'authors.name',
                    'categories.name',
                    'source',
                    'published_at'
                ]);

            $paginator = match (Request::has('cursor')) {
                true => $queryBuilder->cursorPaginate(),
                default => $queryBuilder->paginate()
            };

            return NewsArticleDto::collect($paginator->appends(Request::query()));
        });
    }

    /**
     * Get articles filtered by user feed preferences (sources, categories, authors).
     */
    public function getArticlesForFeed(User $user): LengthAwarePaginator
    {
        $prefs = $user->preferences;
        $query = Article::query()->with(['authors', 'categories']);

        if ($prefs) {
            if (! empty($prefs->preferred_sources)) {
                $query->whereIn('source', $prefs->preferred_sources);
            }
            if (! empty($prefs->preferred_category_ids)) {
                $query->whereHas('categories', function ($q) use ($prefs) {
                    $q->whereIn('categories.id', $prefs->preferred_category_ids);
                });
            }
            if (! empty($prefs->preferred_author_ids)) {
                $query->whereHas('authors', function ($q) use ($prefs) {
                    $q->whereIn('authors.id', $prefs->preferred_author_ids);
                });
            }
        }

        $paginator = $query->orderByDesc('published_at')->paginate(15)->appends(Request::query());

        return NewsArticleDto::collect($paginator);
    }

    private function buildCacheKey(array $queryParams): string
    {
        ksort($queryParams);

        return 'news_articles_' . md5(json_encode($queryParams));
    }
}
