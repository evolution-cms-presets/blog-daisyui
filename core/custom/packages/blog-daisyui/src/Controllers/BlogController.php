<?php

namespace EvolutionCMS\BlogDaisyui\Controllers;

use EvolutionCMS\Models\SiteContent;

class BlogController extends BaseController
{
    private const PER_PAGE = 3;

    public function render(): void
    {
        $document = $this->currentDocument();
        $documentId = (int) ($document?->id ?? $this->evo->documentIdentifier);
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $query = $this->blogPostsQuery($documentId);
        $total = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $totalPages);

        $posts = $query
            ->forPage($page, self::PER_PAGE)
            ->get()
            ->map(fn (SiteContent $post) => $this->mapPost($post))
            ->all();

        $pages = [];
        for ($i = 1; $i <= $totalPages; $i++) {
            $pages[] = [
                'number' => $i,
                'url' => $this->pageUrl($documentId, $i),
                'current' => $i === $page,
            ];
        }

        $this->data['blog'] = [
            'title' => (string) ($document?->pagetitle ?: 'Blog'),
            'content' => (string) ($document?->content ?: ''),
            'posts' => $posts,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'hasPages' => $totalPages > 1,
                'previous' => $page > 1 ? $this->pageUrl($documentId, $page - 1) : null,
                'next' => $page < $totalPages ? $this->pageUrl($documentId, $page + 1) : null,
                'pages' => $pages,
            ],
        ];
    }
}
