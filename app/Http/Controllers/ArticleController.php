<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Article as ArticleModel;
use App\Services\News\NewsArticleService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ArticleController extends Controller
{
    public function __construct(
        private readonly NewsArticleService $articleService
    ) {
    }

    public function feed(Request $request): Response
    {
        $articles = $this->articleService->getArticlesForFeed($request->user());
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);
        $sources = ArticleModel::query()
            ->distinct()
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->orderBy('source')
            ->pluck('source')
            ->values()
            ->toArray();
        $authors = Author::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Articles/Index', [
            'articles' => $articles,
            'categories' => $categories,
            'sources' => $sources,
            'authors' => $authors,
            'filters' => [
                'title' => '',
                'source' => '',
                'categories.name' => '',
                'authors.name' => '',
                'published_at' => '',
            ],
            'sort' => '-published_at',
            'isFeed' => true,
        ]);
    }
}
