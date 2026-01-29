<?php

namespace App\Services\News\Sources;

use App\Interfaces\NewsProviderInterface;
use Closure;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseNewsProvider implements NewsProviderInterface
{
    protected string $apiKey;

    protected string $baseUrl;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    abstract public function getName(): string;

    abstract public function mapCallBack(): Closure;

    protected function resolveApiBaseUrl(): string
    {
        $sourceIdentifier = $this->getName();
        $sourceConfig = config("news_sources.{$sourceIdentifier}");
        
        return $sourceConfig['base_url'] ?? $this->baseUrl;
    }

    protected function fetch(string $endpoint, array $queryParams = []): array
    {
        if (!check_rate_limit($this->getName())) {
            return [];
        }

        try {
            $apiBaseUrl = $this->resolveApiBaseUrl();
            $fullUrl = $apiBaseUrl . '/' . ltrim($endpoint, '/');
            $httpResponse = Http::get($fullUrl, $queryParams);

            if (!$httpResponse->successful()) {
                throw new RequestException($httpResponse);
            }

            return $httpResponse->json();
        } catch (Exception $exception) {
            Log::error("Failed to fetch from {$this->getName()}", [
                'error' => $exception->getMessage(),
                'endpoint' => $endpoint,
            ]);

            return [];
        }
    }
}
