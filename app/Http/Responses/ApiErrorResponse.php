<?php

namespace App\Http\Responses;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class ApiErrorResponse implements Responsable
{
    public function __construct(
        private string $message,
        private array $errors = [],
        private null|Throwable|Exception $exception = null,
        private int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        private array $headers = []
    ) {
    }

    public function toResponse($request): JsonResponse|Response
    {
        $responseData = [
            'success' => false,
            'message' => $this->message,
            'statusCode' => $this->statusCode,
        ];

        if (count($this->errors) > 0) {
            $responseData['errors'] = $this->errors;
        }

        if ($this->exception !== null && config('app.debug') && empty($this->errors)) {
            $responseData['debug'] = [
                'message' => $this->exception->getMessage(),
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'code' => $this->exception->getCode(),
                'trace' => $this->exception->getTraceAsString(),
            ];
        }

        return response()->json(
            data: $responseData,
            status: $this->statusCode > 0 ? $this->statusCode : 500,
            headers: $this->headers
        );
    }
}
