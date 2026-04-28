@extends('layouts.base')

@section('title', $documentObject['longtitle'] ?? $documentObject['pagetitle'] ?? evo()->getConfig('site_name'))

@section('content')
  <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(320px,420px)] lg:items-start">
    @if(!empty($documentObject['content']))
      <article class="content-flow">
        {!! $documentObject['content'] !!}
      </article>
    @endif

    <section id="contact-form" class="rounded-lg border border-base-300 bg-base-100 p-5 shadow-sm" aria-label="Contact form">
      @if(($contact['status'] ?? '') === 'sent')
        <div class="alert alert-success mb-5">
          <span>Message sent.</span>
        </div>
      @elseif(($contact['status'] ?? '') === 'logged')
        <div class="alert alert-warning mb-5">
          <span>Message was logged, but mail delivery needs configuration.</span>
        </div>
      @elseif(($contact['status'] ?? '') === 'invalid')
        <div class="alert alert-error mb-5">
          <span>Check the form fields and try again.</span>
        </div>
      @endif

      <form class="space-y-4" action="{{ $contact['action'] ?? '/contact-submit' }}" method="post">
        <input type="hidden" name="redirect_to" value="{{ request()->getRequestUri() }}">
        <div class="hidden" aria-hidden="true">
          <label>
            Website
            <input type="text" name="company_url" tabindex="-1" autocomplete="off">
          </label>
        </div>

        <label class="form-control w-full">
          <span class="label-text mb-2">Name</span>
          <input class="input input-bordered w-full" type="text" name="name" autocomplete="name" required>
        </label>

        <label class="form-control w-full">
          <span class="label-text mb-2">Email</span>
          <input class="input input-bordered w-full" type="email" name="email" autocomplete="email" required>
        </label>

        <label class="form-control w-full">
          <span class="label-text mb-2">Message</span>
          <textarea class="textarea textarea-bordered min-h-36 w-full" name="message" required></textarea>
        </label>

        <button class="btn btn-primary w-full sm:w-auto" type="submit">Send message</button>
      </form>
    </section>
  </div>
@endsection
