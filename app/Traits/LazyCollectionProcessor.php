<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

trait LazyCollectionProcessor
{
    protected function processItemsLazily(iterable $items, callable $transformer): Collection
    {
        return LazyCollection::make($items)
            ->map($transformer)
            ->filter()
            ->collect();
    }
}
