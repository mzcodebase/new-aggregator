<?php

use App\Http\Controllers\Api\NewsArticleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/articles', NewsArticleController::class);
});
