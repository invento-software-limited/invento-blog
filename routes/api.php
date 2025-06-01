<?php

use Illuminate\Support\Facades\Route; // It's good practice to import Route facade
use App\Controllers\BlogApiController; // Assuming this is the correct namespace

Route::prefix('api/v1')->group(function () {
    Route::get('/blogs/categories', [BlogApiController::class, 'categories'])->name('api.categories.index');
    Route::get('/blogs', [BlogApiController::class, 'index'])->name('api.blogs.index');
    Route::get('/blogs/{slug}', [BlogApiController::class, 'show'])->name('api.blogs.show');
});