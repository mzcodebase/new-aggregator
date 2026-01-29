<?php

namespace App\Services\News\Sources;

use App\DataTransferObjects\NewsArticleDto;
use App\Traits\LazyCollectionProcessor;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Support\Collection;

final class BbcNewsProvider extends BaseNewsProvider
{
    use LazyCollectionProcessor;

    public function __construct()
    {
        $bbcConfig = config('news_sources.bbc');
        $this->apiKey = '';
        $configuredBaseUrl = $bbcConfig['base_url'] ?? 'https://bbc-news-api.vercel.app';
        $this->baseUrl = rtrim($configuredBaseUrl, '/') . '/';

        parent::__construct($this->apiKey);
    }

    public function fetchArticles(): Collection
    {
        $language = config('news_sources.bbc.language', 'english');
        $apiResponse = $this->fetch('news', [
            'lang' => $language,
        ]);

        $articlesList = $apiResponse['latest'] ?? [];
        $transformationCallback = $this->mapCallBack();

        return $this->processItemsLazily($articlesList, $transformationCallback);
    }

    public function getName(): string
    {
        return 'bbc';
    }

    public function mapCallBack(): Closure
    {
        return function ($bbcArticle) {
            return NewsArticleDto::from([
                'title' => $bbcArticle['title'] ?? '',
                'description' => $bbcArticle['summary'] ?? null,
                'content' => $bbcArticle['summary'] ?? null,
                'author' => null,
                'category' => null,
                'source' => 'BBC News',
                'url' => $bbcArticle['news_link'] ?? '',
                'image' => $bbcArticle['image_link'] ?? null,
                'published_at' => CarbonImmutable::now(),
            ]);
        };
    }
}
