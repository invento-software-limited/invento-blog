<?php


use Illuminate\Support\Facades\Route;
use Invento\Blog\Controllers\BlogController;
use Invento\Blog\Controllers\CategoryController;
use Invento\Blog\Controllers\ConfigController;

Route::group(['middleware' => ['check.banned.ip','throttle:40,1','web', 'auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {

    Route::group(['prefix' => 'blogs', 'as' => 'blogs.'], function () {
        Route::resource('categories', CategoryController::class);
    });

    Route::get('packages/config-blog',[ConfigController::class,'index'])->name('packages.config-blog');
    Route::post('packages/blog-store',[ConfigController::class,'store'])->name('packages.blog.store');

    Route::resource('blogs', BlogController::class);

});

