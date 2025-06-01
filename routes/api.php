<?php


use Invento\Blog\Controllers\BlogApiController;

// API routes
Route::prefix('api/v1')->group(function () {
        Route::group(function (){

            Route::get('/blogs/categories', [BlogApiController::class, 'categories'])->name('api.categories.index');

            Route::get('/blogs', [BlogApiController::class, 'index'])->name('api.blogs.index');
            Route::get('/blogs/{slug}', [BlogApiController::class, 'show'])->name('api.blogs.show');
    });
});