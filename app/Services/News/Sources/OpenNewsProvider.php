<?php

namespace App\Services\News\Sources;

use App\DataTransferObjects\NewsArticleDto;
use App\Traits\LazyCollectionProcessor;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Support\Collection;

final class OpenNewsProvider extends BaseNewsProvider
{
    use LazyCollectionProcessor;

    public function __construct()
    {
        $config = config('news_sources.opennews');
        $this->apiKey = '';
        $this->baseUrl = rtrim($config['base_url'] ?? 'https://opennewsapi.herokuapp.com', '/') . '/';

        parent::__construct($this->apiKey);
    }

    public function fetchArticles(): Collection
    {
        if (! config('news_sources.opennews.enabled', false)) {
            return collect();
        }

        $endpoint = config('news_sources.opennews.articles_endpoint', 'api/articles');
        $apiResponse = $this->fetch($endpoint, []);

        $articlesList = $apiResponse['data'] ?? $apiResponse['articles'] ?? $apiResponse['latest'] ?? $apiResponse['news'] ?? [];
        if (! is_array($articlesList)) {
            $articlesList = [];
        }

        $transformationCallback = $this->mapCallBack();

        return $this->processItemsLazily($articlesList, $transformationCallback);
    }

    public function getName(): string
    {
        return 'opennews';
    }

    public function mapCallBack(): Closure
    {
        return function ($item) {
            $url = $item['url'] ?? $item['link'] ?? $item['news_link'] ?? '';
            if (empty($url)) {
                return null;
            }

            return NewsArticleDto::from([
                'title' => $item['title'] ?? $item['headline'] ?? '',
                'description' => $item['description'] ?? $item['summary'] ?? $item['snippet'] ?? null,
                'content' => $item['content'] ?? $item['body'] ?? $item['summary'] ?? null,
                'author' => $item['author'] ?? $item['byline'] ?? null,
                'category' => $item['category'] ?? $item['section'] ?? null,
                'source' => $item['source'] ?? 'OpenNews',
                'url' => $url,
                'image' => $item['image'] ?? $item['image_link'] ?? $item['urlToImage'] ?? null,
                'published_at' => ($date = $item['published_at'] ?? $item['publishedAt'] ?? $item['date'] ?? null)
                    ? CarbonImmutable::parse($date)
                    : CarbonImmutable::now(),
            ]);
        };
    }
}
