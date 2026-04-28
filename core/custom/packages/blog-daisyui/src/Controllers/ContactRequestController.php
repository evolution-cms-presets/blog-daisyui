<?php

namespace EvolutionCMS\BlogDaisyui\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ContactRequestController
{
    public function submit(Request $request)
    {
        if (trim((string) $request->input('company_url', '')) !== '') {
            if ($this->wantsJson($request)) {
                return $this->successResponse($request, true);
            }

            return redirect()->to($this->redirectUrl($request, 'sent'));
        }

        $input = $this->cleanInput($request);
        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'message' => ['required', 'string', 'min:10', 'max:4000'],
        ], [
            'name.required' => 'Tell us your name so we know who to reply to.',
            'name.min' => 'Use at least 2 characters for your name.',
            'email.required' => 'Add an email address so we can reply.',
            'email.email' => 'Use a valid email address.',
            'message.required' => 'Tell us a little about your request.',
            'message.min' => 'Add a few more details so the message is useful.',
        ]);

        if ($validator->fails()) {
            if ($this->wantsJson($request)) {
                return Response::json([
                    'ok' => false,
                    'output' => view('partials.contact-form', [
                        'contact' => [
                            'action' => $this->endpoint(),
                            'pageUrl' => $this->baseRedirectUrl($request),
                            'status' => 'invalid',
                        ],
                        'old' => $input,
                        'fieldErrors' => $validator->errors()->toArray(),
                    ])->render(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()
                ->to($this->redirectUrl($request, 'invalid'))
                ->withErrors($validator)
                ->withInput();
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

        if ($this->wantsJson($request)) {
            return $this->successResponse($request, $mailSent);
        }

        return redirect()
            ->to($this->redirectUrl($request, $mailSent ? 'sent' : 'logged'))
            ->with('contact_name', $input['name']);
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

    private function successResponse(Request $request, bool $mailSent)
    {
        return Response::json([
            'ok' => true,
            'output' => view('partials.contact-thanks', [
                'mailSent' => $mailSent,
                'contactUrl' => $this->baseRedirectUrl($request),
            ])->render(),
        ]);
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
        $target = $this->baseRedirectUrl($request);
        $parts = parse_url($target) ?: [];
        $path = (string) ($parts['path'] ?? '/');
        parse_str((string) ($parts['query'] ?? ''), $query);
        $query['contact'] = $status;

        return $path . '?' . http_build_query($query) . '#contact-form';
    }

    private function baseRedirectUrl(Request $request): string
    {
        $target = trim((string) $request->input('redirect_to', '/'));

        if ($target === '' || $target[0] !== '/' || str_starts_with($target, '//')) {
            $target = '/';
        }

        $target = preg_replace('/#.*$/', '', $target) ?: '/';
        $parts = parse_url($target) ?: [];
        $path = (string) ($parts['path'] ?? '/');
        parse_str((string) ($parts['query'] ?? ''), $query);
        unset($query['contact']);

        $queryString = http_build_query($query);

        return $path . ($queryString !== '' ? '?' . $queryString : '');
    }

    private function endpoint(): string
    {
        return rtrim((string) EvolutionCMS()->getConfig('site_url'), '/') . '/contact-submit';
    }

    private function wantsJson(Request $request): bool
    {
        return $request->ajax()
            || $request->expectsJson()
            || str_contains((string) $request->header('Accept'), 'application/json');
    }
}
