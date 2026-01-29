<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Article as ArticleModel;
use App\Services\News\NewsArticleService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class HomeController extends Controller
{
    public function __construct(
        private readonly NewsArticleService $articleService
    ) {
    }

    public function index(Request $request): Response
    {
        $articles = $this->articleService->getArticles();
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

        return Inertia::render('Welcome', [
            'articles' => $articles,
            'categories' => $categories,
            'sources' => $sources,
            'authors' => $authors,
            'filters' => [
                'title' => ($request->query('filter') ?? [])['title'] ?? '',
                'source' => ($request->query('filter') ?? [])['source'] ?? '',
                'categories.name' => ($request->query('filter') ?? [])['categories.name'] ?? '',
                'authors.name' => ($request->query('filter') ?? [])['authors.name'] ?? '',
                'published_at' => ($request->query('filter') ?? [])['published_at'] ?? '',
            ],
            'sort' => $request->query('sort', '-published_at'),
        ]);
    }
}
