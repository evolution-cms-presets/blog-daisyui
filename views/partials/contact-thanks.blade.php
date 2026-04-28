@php
  $mailSent = (bool)($mailSent ?? true);
  $contactUrl = (string)($contactUrl ?? request()->getRequestUri());
@endphp

<div class="space-y-4">
  <div class="alert {{ $mailSent ? 'alert-success' : 'alert-warning' }}">
    <span>{{ $mailSent ? 'Message sent.' : 'Message was logged, but mail delivery needs configuration.' }}</span>
  </div>

  <a class="btn btn-ghost btn-sm" href="{{ $contactUrl }}#contact-form">Send another message</a>
</div>
