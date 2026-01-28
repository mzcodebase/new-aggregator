<?php

namespace App\Services\News;

use App\Interfaces\RetryHandlerInterface;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

final class ApiRequestRetryHandler implements RetryHandlerInterface
{
    private array $exceptionRetryRules;

    private int $maximumRetryAttempts;

    public function __construct(array $exceptionRetryRules = [], int $maximumRetryAttempts = 3)
    {
        $this->exceptionRetryRules = $exceptionRetryRules;
        $this->maximumRetryAttempts = $maximumRetryAttempts;
    }

    /**
     * @throws Exception
     */
    public function execute(callable $operation): mixed
    {
        $currentAttempt = 0;

        while (true) {
            try {
                return $operation();
            } catch (Exception $exception) {
                $exceptionType = get_class($exception);

                if (!isset($this->exceptionRetryRules[$exceptionType])) {
                    throw $exception;
                }

                $currentAttempt++;
                $this->recordRetryAttempt($exception, $currentAttempt);

                if ($currentAttempt >= $this->maximumRetryAttempts) {
                    throw $exception;
                }

                $waitTimeMs = $this->computeRetryDelay($currentAttempt, $this->exceptionRetryRules[$exceptionType]);
                usleep($waitTimeMs * 1000);
            }
        }
    }

    private function computeRetryDelay(int $attemptNumber, array $retryConfig): int
    {
        if ($retryConfig['exponentialBackoff']) {
            return $retryConfig['delayMs'] * (2 ** ($attemptNumber - 1));
        }

        return $retryConfig['delayMs'];
    }

    private function recordRetryAttempt(Exception $exception, int $attemptNumber): void
    {
        $exceptionType = get_class($exception);
        $logMessage = "Retry attempt {$attemptNumber} failed with [{$exceptionType}]: {$exception->getMessage()}";

        if ($exception instanceof ConnectionException) {
            Log::warning($logMessage, ['exception' => $exception]);
        } elseif ($exception instanceof RequestException) {
            Log::error($logMessage, ['exception' => $exception]);
        } else {
            Log::info($logMessage, ['exception' => $exception]);
        }
    }
}
