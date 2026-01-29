<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

if (!function_exists('build_rate_limit_cache_key')) {
    function build_rate_limit_cache_key(string $identifier): string
    {
        return "rate_limit:{$identifier}";
    }
}

if (!function_exists('check_rate_limit')) {
    function check_rate_limit(string $identifier, string $configPath = 'news_sources'): bool
    {
        $cacheKey = build_rate_limit_cache_key($identifier);
        $rateLimitSettings = config("{$configPath}.{$identifier}.rate_limit");

        if ($rateLimitSettings === null) {
            return true;
        }

        $allowedRequests = $rateLimitSettings['max_requests'] ?? 10;
        $timeWindowMinutes = $rateLimitSettings['per_minutes'] ?? 1;
        $decaySeconds = $timeWindowMinutes * 60;

        if (RateLimiter::tooManyAttempts($cacheKey, $allowedRequests)) {
            Log::warning("Rate limit exceeded for {$identifier}");

            return false;
        }

        RateLimiter::hit($cacheKey, $decaySeconds);

        return true;
    }
}
