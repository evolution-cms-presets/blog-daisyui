<?php

namespace EvolutionCMS\BlogDaisyui\Controllers;

class HomeController extends BaseController
{
    public function render(): void
    {
        $blog = $this->blogRoot();

        $this->data['preset'] = [
            'name' => 'blog-daisyui',
            'theme' => '/themes/blog-daisyui',
        ];
        $this->data['latestPosts'] = $this->latestPosts(3);
        $this->data['blogUrl'] = $blog ? $this->evo->makeUrl((int) $blog->id) : '';
    }
}
