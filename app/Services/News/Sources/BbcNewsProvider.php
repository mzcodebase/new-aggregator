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
        if (! config('news_sources.bbc.enabled', true)) {
            return collect();
        }

        $language = config('news_sources.bbc.language', 'english');
        $apiResponse = $this->fetch('news', [
            'lang' => $language,
        ]);

        $articlesList = $this->extractNewsItemsFromResponse($apiResponse);
        $transformationCallback = $this->mapCallBack();

        return $this->processItemsLazily($articlesList, $transformationCallback);
    }

    private function extractNewsItemsFromResponse(array $apiResponse): array
    {
        $metaKeys = ['status', 'elapsed time', 'timestamp'];
        $seenUrls = [];
        $items = [];

        foreach ($apiResponse as $key => $value) {
            if (in_array($key, $metaKeys, true) || ! is_array($value)) {
                continue;
            }

            foreach ($value as $entry) {
                if (! is_array($entry) || empty($entry['title'])) {
                    continue;
                }

                $items[] = $entry;
            }
        }

        return $items;
    }

    public function getName(): string
    {
        return 'bbc';
    }

    public function mapCallBack(): Closure
    {
        return function ($bbcArticle) {
            $url = $bbcArticle['news_link'] ?? '';
            $url = mb_substr((string) $url, 0, 255);

            return NewsArticleDto::from([
                'title' => $bbcArticle['title'] ?? '',
                'description' => $bbcArticle['summary'] ?? null,
                'content' => $bbcArticle['summary'] ?? null,
                'author' => null,
                'category' => null,
                'source' => 'BBC News',
                'url' => $url,
                'image' => $bbcArticle['image_link'] ?? null,
                'published_at' => CarbonImmutable::now(),
            ]);
        };
    }
}
