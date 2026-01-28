<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\News\NewsArticleService;
use Exception;

final class NewsArticleController extends Controller
{
    public function __construct(
        private readonly NewsArticleService $articleService
    ) {
    }

    public function __invoke(): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $articles = $this->articleService->getArticles();

            return new ApiSuccessResponse(
                data: $articles,
                message: 'News articles retrieved successfully.'
            );
        } catch (Exception $exception) {
            return new ApiErrorResponse(
                message: 'Failed to retrieve news articles.',
                exception: $exception,
                statusCode: 500
            );
        }
    }
}
