<?php

namespace App\Services\News\Sources;

use App\DataTransferObjects\NewsArticleDto;
use App\Traits\LazyCollectionProcessor;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Support\Collection;

final class NewsApiProvider extends BaseNewsProvider
{
    use LazyCollectionProcessor;

    public function __construct()
    {
        $newsApiConfig = config('news_sources.newsapi');
        $this->apiKey = $newsApiConfig['key'] ?? '';
        $defaultBaseUrl = 'https://newsapi.org/v2';
        $configuredBaseUrl = $newsApiConfig['base_url'] ?? $defaultBaseUrl;
        $this->baseUrl = rtrim($configuredBaseUrl, '/') . '/';
    }

    public function fetchArticles(): Collection
    {
        $apiResponse = $this->fetchApiData('top-headlines');

        $articlesList = $apiResponse['articles'] ?? [];
        $transformationCallback = $this->mapCallBack();

        return $this->processItemsLazily($articlesList, $transformationCallback);
    }

    public function getName(): string
    {
        return 'newsapi';
    }

    public function mapCallBack(): Closure
    {
        $sourceCategories = $this->fetchSourceCategories();

        return function ($newsApiArticle) use ($sourceCategories) {
            $articleSource = $newsApiArticle['source'] ?? [];
            $sourceName = $articleSource['name'] ?? 'Unknown';
            $sourceCategory = $sourceCategories->firstWhere('name', $sourceName)['category'] ?? null;

            return NewsArticleDto::from([
                'title' => $newsApiArticle['title'] ?? '',
                'description' => $newsApiArticle['description'] ?? null,
                'content' => $newsApiArticle['content'] ?? null,
                'author' => $newsApiArticle['author'] ?? null,
                'category' => $sourceCategory,
                'source' => 'NewsAPI - ' . $sourceName,
                'url' => $newsApiArticle['url'] ?? '',
                'image' => $newsApiArticle['urlToImage'] ?? null,
                'published_at' => CarbonImmutable::parse($newsApiArticle['publishedAt'] ?? now()),
            ]);
        };
    }

    private function fetchSourceCategories(): Collection
    {
        $sourcesData = $this->fetchApiData('top-headlines/sources');

        return collect($sourcesData['sources'] ?? []);
    }

    private function fetchApiData(string $endpoint): array
    {
        return $this->fetch($endpoint, [
            'apiKey' => $this->apiKey,
            'language' => 'en',
        ]);
    }
}
