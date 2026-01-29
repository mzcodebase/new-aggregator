<?php

namespace App\Models;

use App\Enums\FetchJobStatus;
use Illuminate\Database\Eloquent\Model;

class FetchJob extends Model
{
    protected $fillable = [
        'source',
        'status',
        'started_at',
        'completed_at',
        'articles_fetched',
        'articles_stored',
        'articles_skipped',
        'error_message',
    ];

    protected $casts = [
        'status' => FetchJobStatus::class,
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function isRunning(): bool
    {
        return $this->status === FetchJobStatus::RUNNING;
    }

    public function markAsStarted(): void
    {
        $this->update([
            'status' => FetchJobStatus::RUNNING,
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted(int $fetched, int $stored, int $skipped): void
    {
        $this->update([
            'status' => FetchJobStatus::COMPLETED,
            'completed_at' => now(),
            'articles_fetched' => $fetched,
            'articles_stored' => $stored,
            'articles_skipped' => $skipped,
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => FetchJobStatus::FAILED,
            'completed_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }
}
