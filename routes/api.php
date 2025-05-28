<?php


use Invento\Blog\Controllers\BlogApiController;

// API routes
Route::prefix('api/v1')->group(function () {
        Route::middleware('auth:sanctum')->group(function (){
        Route::get('/blogs', [BlogApiController::class, 'index'])->name('api.blogs.index');
        Route::get('/blogs/{slug}', [BlogApiController::class, 'show'])->name('api.blogs.show');
        Route::get('/blogs/categories', [BlogApiController::class, 'categories'])->name('api.categories.index');
    });
});