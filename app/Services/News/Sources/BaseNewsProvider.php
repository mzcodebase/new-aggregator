<?php

namespace App\Services\News\Sources;

use App\Interfaces\NewsProviderInterface;
use App\Services\News\ApiRequestRetryHandler;
use Closure;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

abstract class BaseNewsProvider implements NewsProviderInterface
{
    protected string $apiKey;

    protected string $baseUrl;

    protected ApiRequestRetryHandler $retryHandler;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->retryHandler = new ApiRequestRetryHandler(
            exceptionRetryRules: [
                ConnectionException::class => [
                    'delayMs' => 2000,
                    'exponentialBackoff' => true,
                ],
                RequestException::class => [
                    'delayMs' => 1000,
                    'exponentialBackoff' => false,
                ],
            ]
        );
    }

    abstract public function getName(): string;

    abstract public function mapCallBack(): Closure;

    protected function resolveApiBaseUrl(): string
    {
        $sourceIdentifier = $this->getName();
        $sourceConfig = config("news_sources.{$sourceIdentifier}");
        
        return $sourceConfig['base_url'] ?? $this->baseUrl;
    }

    protected function buildRateLimitCacheKey(): string
    {
        return "rate_limit:{$this->getName()}";
    }

    protected function fetch(string $endpoint, array $queryParams = []): array
    {
        if (!$this->checkRateLimit()) {
            Log::warning("Rate limit exceeded for {$this->getName()}");

            return [];
        }

        try {
            return $this->retryHandler->execute(function () use ($endpoint, $queryParams) {
                $apiBaseUrl = $this->resolveApiBaseUrl();
                $fullUrl = $apiBaseUrl . '/' . ltrim($endpoint, '/');
                $httpResponse = Http::get($fullUrl, $queryParams);

                if (!$httpResponse->successful()) {
                    throw new RequestException($httpResponse);
                }

                return $httpResponse->json();
            });
        } catch (Exception $exception) {
            Log::error("Failed to fetch from {$this->getName()}", [
                'error' => $exception->getMessage(),
                'endpoint' => $endpoint,
            ]);

            return [];
        }
    }

    protected function checkRateLimit(): bool
    {
        $cacheKey = $this->buildRateLimitCacheKey();
        $sourceIdentifier = $this->getName();
        $rateLimitSettings = config("news_sources.{$sourceIdentifier}.rate_limit");

        if ($rateLimitSettings === null) {
            return true;
        }

        $allowedRequests = $rateLimitSettings['max_requests'] ?? 10;
        $timeWindowMinutes = $rateLimitSettings['per_minutes'] ?? 1;

        if (RateLimiter::tooManyAttempts($cacheKey, $allowedRequests)) {
            Log::warning("Rate limit exceeded for {$sourceIdentifier}");

            return false;
        }

        RateLimiter::hit($cacheKey, $timeWindowMinutes * 60);

        return true;
    }
}
