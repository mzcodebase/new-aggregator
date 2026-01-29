<?php

use App\Jobs\FetchArticlesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$sources = ['newsapi', 'guardian', 'nyt', 'bbc'];
foreach ($sources as $source) {
    Schedule::job(new FetchArticlesJob($source))->hourly();
}
