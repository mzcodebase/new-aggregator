<?php

namespace App\Jobs;

use App\Enums\FetchJobStatus;
use App\Models\FetchJob;
use App\Services\News\NewsAggregatorService;
use App\Services\News\Sources\BbcNewsProvider;
use App\Services\News\Sources\GuardianNewsProvider;
use App\Services\News\Sources\NewsApiProvider;
use App\Services\News\Sources\NewYorkTimesProvider;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class FetchArticlesJob implements ShouldQueue
{
    use Queueable;

    private const SOURCES = [
        'newsapi' => NewsApiProvider::class,
        'guardian' => GuardianNewsProvider::class,
        'nyt' => NewYorkTimesProvider::class,
        'bbc' => BbcNewsProvider::class,
    ];

    public function __construct(
        protected string $source
    ) {
    }

    public function handle(): void
    {
        $fetchJob = FetchJob::create([
            'source' => $this->source,
            'status' => FetchJobStatus::PENDING,
        ]);

        try {
            $fetchJob->markAsStarted();

            $providerClass = self::SOURCES[$this->source] ?? null;
            if (! $providerClass) {
                throw new \InvalidArgumentException("Unknown source: {$this->source}");
            }

            $provider = new $providerClass();
            $aggregator = new NewsAggregatorService();
            $aggregator->addProvider($provider);

            $result = $aggregator->fetchAndStore();
            $stored = $result['stored'] ?? 0;

            $fetchJob->markAsCompleted($stored, $stored, 0);
        } catch (RequestException $e) {
            $fetchJob->markAsFailed($e->getMessage());
            Log::error("FetchArticlesJob failed for {$this->source}: " . $e->getMessage());
            $this->release(300);
        } catch (\Throwable $e) {
            $fetchJob->markAsFailed($e->getMessage());
            Log::error("FetchArticlesJob failed for {$this->source}: " . $e->getMessage());
            throw $e;
        }
    }
}
