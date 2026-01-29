<?php

namespace App\Console\Commands;

use App\Jobs\FetchArticlesJob;
use App\Services\News\Sources\BbcNewsProvider;
use App\Services\News\Sources\GuardianNewsProvider;
use App\Services\News\Sources\NewsApiProvider;
use App\Services\News\Sources\NewYorkTimesProvider;
use Illuminate\Console\Command;

final class FetchArticlesCommand extends Command
{
    protected $signature = 'fetch:articles
                            {--source= : The news source to fetch from (newsapi, guardian, nyt, bbc)}';

    protected $description = 'Fetch articles from a single news source and store in the database (dispatches a queued job)';

    private const SOURCES = [
        'newsapi' => NewsApiProvider::class,
        'guardian' => GuardianNewsProvider::class,
        'nyt' => NewYorkTimesProvider::class,
        'bbc' => BbcNewsProvider::class,
    ];

    public function handle(): int
    {
        $source = $this->option('source');

        if (empty($source)) {
            $this->error('Please specify a source with --source=newsapi|guardian|nyt|bbc');

            return Command::FAILURE;
        }

        $source = strtolower($source);

        if (! array_key_exists($source, self::SOURCES)) {
            $this->error("Unknown source '{$source}'. Allowed: newsapi, guardian, nyt, bbc");

            return Command::FAILURE;
        }

        $this->info("Dispatching fetch job for: {$source}");
        FetchArticlesJob::dispatch($source);
        $this->info('Job dispatched successfully.');

        return Command::SUCCESS;
    }
}
