<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

final class ArticleCategoryDto extends Data
{
    public function __construct(
        readonly public ?int $id,
        readonly public string $name,
    ) {
    }
}
