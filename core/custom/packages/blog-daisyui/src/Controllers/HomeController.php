<?php

namespace EvolutionCMS\BlogDaisyui\Controllers;

class HomeController extends BaseController
{
    public function render(): void
    {
        $this->data['preset'] = [
            'name' => 'blog-daisyui',
            'theme' => '/themes/blog-daisyui',
        ];
    }
}
