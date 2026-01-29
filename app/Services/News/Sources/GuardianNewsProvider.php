<?php

namespace App\Services\News\Sources;

use App\DataTransferObjects\NewsArticleDto;
use App\Traits\LazyCollectionProcessor;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Support\Collection;

final class GuardianNewsProvider extends BaseNewsProvider
{
    use LazyCollectionProcessor;

    public function __construct()
    {
        $guardianConfig = config('news_sources.guardian');
        $this->apiKey = $guardianConfig['key'] ?? '';
        $configuredBaseUrl = $guardianConfig['base_url'] ?? 'https://content.guardianapis.com';
        $this->baseUrl = rtrim($configuredBaseUrl, '/') . '/';

        parent::__construct($this->apiKey);
    }

    public function fetchArticles(): Collection
    {
        $apiResponse = $this->fetch('search', [
            'api-key' => $this->apiKey,
            'show-fields' => 'all',
        ]);

        $articlesList = $apiResponse['response']['results'] ?? [];
        $transformationCallback = $this->mapCallBack();

        return $this->processItemsLazily($articlesList, $transformationCallback);
    }

    public function getName(): string
    {
        return 'guardian';
    }

    public function mapCallBack(): Closure
    {
        return function ($guardianArticle) {
            $articleFields = $guardianArticle['fields'] ?? [];
            
            return NewsArticleDto::from([
                'title' => $guardianArticle['webTitle'] ?? '',
                'description' => $articleFields['trailText'] ?? null,
                'content' => $articleFields['bodyText'] ?? null,
                'author' => $articleFields['byline'] ?? null,
                'category' => $guardianArticle['sectionName'] ?? null,
                'source' => 'The Guardian' . ($articleFields['publication'] ? ' - ' . $articleFields['publication'] : ''),
                'url' => $guardianArticle['webUrl'] ?? '',
                'image' => $articleFields['thumbnail'] ?? null,
                'published_at' => CarbonImmutable::parse($guardianArticle['webPublicationDate'] ?? now()),
            ]);
        };
    }
}
