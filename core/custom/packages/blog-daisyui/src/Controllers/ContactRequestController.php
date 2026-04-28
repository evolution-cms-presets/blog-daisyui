<?php

namespace EvolutionCMS\BlogDaisyui\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ContactRequestController
{
    public function submit(Request $request)
    {
        if (trim((string) $request->input('company_url', '')) !== '') {
            return redirect()->to($this->redirectUrl($request, 'sent'));
        }

        $input = $this->cleanInput($request);
        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'message' => ['required', 'string', 'min:10', 'max:4000'],
        ]);

        if ($validator->fails()) {
            return redirect()->to($this->redirectUrl($request, 'invalid'));
        }

        EvolutionCMS()->logEvent(
            0,
            1,
            $this->formatEventLogMessage($input, $request),
            'Contact request'
        );

        $mailSent = $this->sendNotification($input, $request);

        if (!$mailSent) {
            EvolutionCMS()->logEvent(
                0,
                2,
                '<pre>Contact request was logged, but mail delivery was not completed.</pre>',
                'Contact request mail'
            );
        }

        return redirect()->to($this->redirectUrl($request, $mailSent ? 'sent' : 'logged'));
    }

    private function cleanInput(Request $request): array
    {
        return [
            'name' => trim((string) $request->input('name', '')),
            'email' => trim((string) $request->input('email', '')),
            'message' => trim((string) $request->input('message', '')),
        ];
    }

    private function sendNotification(array $input, Request $request): bool
    {
        $recipient = trim((string) EvolutionCMS()->getConfig('emailsender'));

        if ($recipient === '') {
            return false;
        }

        try {
            return (bool) EvolutionCMS()->sendmail([
                'to' => $recipient,
                'from' => $recipient,
                'fromname' => EvolutionCMS()->getConfig('site_name') ?: 'Evolution CMS',
                'subject' => 'New contact request from ' . $input['name'],
                'body' => $this->formatMailMessage($input, $request),
                'type' => 'text',
            ]);
        } catch (Throwable $exception) {
            EvolutionCMS()->logEvent(
                0,
                2,
                '<pre>' . htmlspecialchars($exception->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>',
                'Contact request mail'
            );

            return false;
        }
    }

    private function formatEventLogMessage(array $input, Request $request): string
    {
        return '<pre>' . htmlspecialchars(
            $this->formatMessageLines($input, $request),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        ) . '</pre>';
    }

    private function formatMailMessage(array $input, Request $request): string
    {
        return $this->formatMessageLines($input, $request);
    }

    private function formatMessageLines(array $input, Request $request): string
    {
        return implode("\n", [
            'New contact request from the public contact form.',
            '',
            'Name: ' . $input['name'],
            'Email: ' . $input['email'],
            'IP: ' . ($request->ip() ?: '-'),
            'User agent: ' . (string) $request->userAgent(),
            '',
            'Message:',
            $input['message'],
        ]);
    }

    private function redirectUrl(Request $request, string $status): string
    {
        $target = trim((string) $request->input('redirect_to', '/'));

        if ($target === '' || $target[0] !== '/' || str_starts_with($target, '//')) {
            $target = '/';
        }

        $target = preg_replace('/#.*$/', '', $target) ?: '/';
        $parts = parse_url($target) ?: [];
        $path = (string) ($parts['path'] ?? '/');
        parse_str((string) ($parts['query'] ?? ''), $query);
        $query['contact'] = $status;

        return $path . '?' . http_build_query($query) . '#contact-form';
    }
}
