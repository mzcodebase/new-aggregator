<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class ApiSuccessResponse implements Responsable
{
    public function __construct(
        public mixed $data,
        public string $message,
        public array $metadata = [],
        private int $statusCode = Response::HTTP_OK,
        private array $headers = []
    ) {
    }

    public function toResponse($request): JsonResponse|Response
    {
        $responseData = [
            'success' => true,
            'message' => $this->message,
            'data' => $this->data,
        ];

        if (!empty($this->metadata)) {
            $responseData['meta'] = $this->metadata;
        }

        return response()->json(
            data: $responseData,
            status: $this->statusCode,
            headers: $this->headers
        );
    }
}
