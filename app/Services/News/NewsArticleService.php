<?php

namespace App\Services\News;

use App\DataTransferObjects\NewsArticleDto;
use App\Models\Article;
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

    private function buildCacheKey(array $queryParams): string
    {
        ksort($queryParams);

        return 'news_articles_' . md5(json_encode($queryParams));
    }
}
