<?php

namespace App\Console\Commands;

use App\Jobs\FetchArticlesJob;
use Illuminate\Console\Command;

final class FetchNewsCommand extends Command
{
    protected $signature = 'news:fetch';

    protected $description = 'Fetch news from all configured providers and store in the database (dispatches one job per source)';

    private const SOURCES = ['newsapi', 'guardian', 'nyt', 'bbc'];

    public function handle(): int
    {
        $this->info('Dispatching fetch jobs for all sources...');

        foreach (self::SOURCES as $source) {
            $this->info("Dispatching fetch job for: {$source}");
            FetchArticlesJob::dispatch($source);
        }

        $this->info('All jobs dispatched successfully.');

        return Command::SUCCESS;
    }
}
