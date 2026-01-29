<?php

namespace App\Services\News\Sources;

use App\DataTransferObjects\NewsArticleDto;
use App\Traits\LazyCollectionProcessor;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Support\Collection;

final class NewYorkTimesProvider extends BaseNewsProvider
{
    use LazyCollectionProcessor;

    public function __construct()
    {
        $nytConfig = config('news_sources.nyt');
        $this->apiKey = $nytConfig['key'] ?? '';
        $defaultBaseUrl = 'https://api.nytimes.com/svc';
        $configuredBaseUrl = $nytConfig['base_url'] ?? $defaultBaseUrl;
        $this->baseUrl = rtrim($configuredBaseUrl, '/') . '/search/v2/';

        parent::__construct($this->apiKey);
    }

    public function fetchArticles(): Collection
    {
        $apiResponse = $this->fetch('articlesearch.json', [
            'api-key' => $this->apiKey,
            'q' => 'news',
        ]);

        $articlesList = $apiResponse['response']['docs'] ?? [];
        $transformationCallback = $this->mapCallBack();

        return $this->processItemsLazily($articlesList, $transformationCallback);
    }

    public function getName(): string
    {
        return 'nyt';
    }

    public function mapCallBack(): Closure
    {
        return function ($nytArticle) {
            $headline = $nytArticle['headline'] ?? [];
            $byline = $nytArticle['byline'] ?? [];

            return NewsArticleDto::from([
                'title' => $headline['main'] ?? '',
                'description' => $nytArticle['abstract'] ?? null,
                'content' => $nytArticle['lead_paragraph'] ?? null,
                'author' => $byline['original'] ?? 'Unknown Author',
                'category' => $nytArticle['news_desk'] ?? $nytArticle['section_name'] ?? 'Uncategorized',
                'source' => 'The New York Times',
                'url' => $nytArticle['web_url'] ?? null,
                'image' => $this->extractImageUrl($nytArticle),
                'published_at' => isset($nytArticle['pub_date']) 
                    ? CarbonImmutable::parse($nytArticle['pub_date']) 
                    : CarbonImmutable::now(),
            ]);
        };
    }

    private function extractImageUrl(array $article): ?string
    {
        $multimediaItems = $article['multimedia'] ?? [];
        $mediaWithUrl = collect($multimediaItems)->first(function ($mediaItem) {
            return isset($mediaItem['url']);
        });

        if ($mediaWithUrl && isset($mediaWithUrl['url'])) {
            return 'https://www.nytimes.com/' . $mediaWithUrl['url'];
        }

        return null;
    }
}
