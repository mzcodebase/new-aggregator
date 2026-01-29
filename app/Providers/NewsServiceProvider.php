<?php

namespace App\Providers;

use App\Services\News\NewsAggregatorService;
use App\Services\News\Sources\BbcNewsProvider;
use App\Services\News\Sources\GuardianNewsProvider;
use App\Services\News\Sources\NewsApiProvider;
use App\Services\News\Sources\NewsCredProvider;
use App\Services\News\Sources\NewYorkTimesProvider;
use Illuminate\Support\ServiceProvider;

final class NewsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NewsAggregatorService::class, function () {
            $aggregator = new NewsAggregatorService();

            $aggregator->addProvider(new GuardianNewsProvider());
            $aggregator->addProvider(new NewsApiProvider());
            $aggregator->addProvider(new NewYorkTimesProvider());
            $aggregator->addProvider(new BbcNewsProvider());
            $aggregator->addProvider(new NewsCredProvider());

            return $aggregator;
        });
    }
}
