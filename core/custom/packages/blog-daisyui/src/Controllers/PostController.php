<?php

namespace EvolutionCMS\BlogDaisyui\Controllers;

class PostController extends BaseController
{
    public function render(): void
    {
        $document = $this->currentDocument();
        $blog = $this->blogRoot();

        $this->data['post'] = [
            'title' => (string) ($document?->pagetitle ?: ''),
            'longtitle' => (string) ($document?->longtitle ?: $document?->pagetitle ?: ''),
            'summary' => $document ? $this->documentSummary($document) : '',
            'date' => $document ? $this->formatDate($this->documentTimestamp($document)) : '',
            'content' => (string) ($document?->content ?: ''),
        ];
        $this->data['blogUrl'] = $blog ? $this->evo->makeUrl((int) $blog->id) : '';
    }
}
