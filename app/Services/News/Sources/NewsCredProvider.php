<?php

namespace App\Services\News\Sources;

use App\DataTransferObjects\NewsArticleDto;
use App\Traits\LazyCollectionProcessor;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Support\Collection;

final class NewsCredProvider extends BaseNewsProvider
{
    use LazyCollectionProcessor;

    public function __construct()
    {
        $config = config('news_sources.newscred');
        $this->apiKey = $config['key'] ?? '';
        $this->baseUrl = rtrim($config['base_url'] ?? 'https://api.newscred.com', '/') . '/';

        parent::__construct($this->apiKey);
    }

    public function fetchArticles(): Collection
    {
        $queryParams = [];
        if ($this->apiKey !== '') {
            $queryParams['api_key'] = $this->apiKey;
        }

        $apiResponse = $this->fetch('v2/articles', $queryParams);

        $articlesList = $apiResponse['articles'] ?? $apiResponse['data'] ?? $apiResponse['results'] ?? [];
        if (! is_array($articlesList)) {
            $articlesList = [];
        }

        $transformationCallback = $this->mapCallBack();

        return $this->processItemsLazily($articlesList, $transformationCallback);
    }

    public function getName(): string
    {
        return 'newscred';
    }

    public function mapCallBack(): Closure
    {
        return function ($item) {
            $url = $item['url'] ?? $item['link'] ?? $item['web_url'] ?? $item['article_url'] ?? '';
            if (empty($url)) {
                return null;
            }

            return NewsArticleDto::from([
                'title' => $item['title'] ?? $item['headline'] ?? '',
                'description' => $item['description'] ?? $item['summary'] ?? $item['snippet'] ?? null,
                'content' => $item['content'] ?? $item['body'] ?? $item['text'] ?? null,
                'author' => $item['author'] ?? $item['byline'] ?? $item['author_name'] ?? null,
                'category' => $item['category'] ?? $item['section'] ?? $item['topic'] ?? null,
                'source' => $item['source'] ?? $item['publication'] ?? 'NewsCred',
                'url' => $url,
                'image' => $item['image'] ?? $item['image_url'] ?? $item['thumbnail'] ?? null,
                'published_at' => ($date = $item['published_at'] ?? $item['publishedAt'] ?? $item['created_at'] ?? $item['date'] ?? null)
                    ? CarbonImmutable::parse($date)
                    : CarbonImmutable::now(),
            ]);
        };
    }
}
