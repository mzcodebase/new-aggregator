<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'preferred_sources',
        'preferred_category_ids',
        'preferred_author_ids',
    ];

    protected function casts(): array
    {
        return [
            'preferred_sources' => 'array',
            'preferred_category_ids' => 'array',
            'preferred_author_ids' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
