<?php

namespace App\Console\Commands;

use App\Services\News\NewsAggregatorService;
use Illuminate\Console\Command;

final class FetchNewsCommand extends Command
{
    protected $signature = 'news:fetch';

    protected $description = 'Fetch news from all configured providers and store in the database';

    public function handle(NewsAggregatorService $aggregator): int
    {
        $this->info('Fetching news from configured providers...');

        $aggregator->fetchAndStore();

        $this->info('News fetch completed successfully.');

        return Command::SUCCESS;
    }
}
