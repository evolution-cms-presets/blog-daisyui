<?php

namespace EvolutionCMS\BlogDaisyui;

use EvolutionCMS\ServiceProvider;

class BlogDaisyuiServiceProvider extends ServiceProvider
{
    protected $namespace = 'blog-daisyui';

    public function boot(): void
    {
        $routes = __DIR__ . '/Http/routes.php';
        if (is_file($routes)) {
            include $routes;
        }

        $this->loadViewsFrom(dirname(__DIR__) . '/views', 'blog-daisyui');
    }

    public function register(): void
    {
    }
}
