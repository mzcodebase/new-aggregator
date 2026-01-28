<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface NewsProviderInterface
{
    public function fetchArticles(): Collection;

    public function getName(): string;
}
