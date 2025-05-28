<?php

namespace Invento\Blog;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/blog.php', 'blog-config');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/blog.php' => config_path('blog.php')
        ], 'blog-config');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'blog');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/blog')
        ], 'blog-views');

        $this->publishes([
            __DIR__ . '/public' => public_path('vendor/blog')
        ], 'assets');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'blog');

        $this->publishes([
            __DIR__ . '/../migrations/2024_04_26_105140_create_blogs_table.php' => database_path('migrations/' .'2024_04_26_105140_create_blogs_table.php')
        ], 'blog-migrations');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/breadcrumbs.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }


}