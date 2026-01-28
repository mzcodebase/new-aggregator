<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

final class CategoryData extends Data
{
    public function __construct(
        readonly public ?int $id,
        readonly public string $name,
    ) {
    }
}
