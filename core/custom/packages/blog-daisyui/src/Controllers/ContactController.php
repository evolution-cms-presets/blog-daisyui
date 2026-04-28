<?php

namespace EvolutionCMS\BlogDaisyui\Controllers;

class ContactController extends BaseController
{
    public function render(): void
    {
        $status = trim((string) ($_GET['contact'] ?? ''));

        $this->data['contact'] = [
            'action' => $this->absoluteUrl('/contact-submit'),
            'status' => in_array($status, ['sent', 'logged', 'invalid'], true) ? $status : '',
        ];
    }
}
