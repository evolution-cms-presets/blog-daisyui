<?php

namespace EvolutionCMS\BlogDaisyui;

use EvolutionCMS\ServiceProvider;

class BlogDaisyuiServiceProvider extends ServiceProvider
{
    protected $namespace = 'blog-daisyui';

    public function boot(): void
    {
        $this->loadViewsFrom(dirname(__DIR__) . '/views', 'blog-daisyui');
    }

    public function register(): void
    {
    }
}
