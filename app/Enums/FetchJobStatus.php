<?php

namespace App\Enums;

enum FetchJobStatus: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::RUNNING => 'Running',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }
}
