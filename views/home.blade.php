@extends('layouts.base')

@section('title', $documentObject['longtitle'] ?? $documentObject['pagetitle'] ?? evo()->getConfig('site_name'))

@section('content')
  <article class="mx-auto max-w-3xl">
    @if(!empty($documentObject['content']))
      <div class="content-flow">
        {!! $documentObject['content'] !!}
      </div>
    @else
      <section class="space-y-8">
        <div class="space-y-4">
          <div class="badge badge-primary badge-outline">Evolution CMS Blog + DaisyUI</div>
          <h1 class="max-w-2xl text-4xl font-semibold tracking-normal sm:text-5xl">
            {{ evo()->getConfig('site_name') ?: 'Evolution CMS' }}
          </h1>
          <p class="max-w-2xl text-lg leading-8 text-base-content/70">
            A compact blog starter with DaisyUI components, theme switching, and rich text editing ready after install.
          </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
          <div class="card border border-base-300 bg-base-100">
            <div class="card-body p-5">
              <h2 class="card-title text-base">Blog Editing</h2>
              <p class="text-sm text-base-content/70">The preset requires eTinyMCE so editors can start writing immediately.</p>
            </div>
          </div>
          <div class="card border border-base-300 bg-base-100">
            <div class="card-body p-5">
              <h2 class="card-title text-base">Clean Baseline</h2>
              <p class="text-sm text-base-content/70">SEO tooling can be added when the project is ready for its metadata workflow.</p>
            </div>
          </div>
          <div class="card border border-base-300 bg-base-100">
            <div class="card-body p-5">
              <h2 class="card-title text-base">Theme Picker</h2>
              <p class="text-sm text-base-content/70">The palette menu remembers the last light and dark DaisyUI theme choice.</p>
            </div>
          </div>
        </div>
      </section>
    @endif
  </article>
@endsection
