@extends('layouts.base')

@section('title', $post['longtitle'] ?? $post['title'] ?? $documentObject['pagetitle'] ?? evo()->getConfig('site_name'))

@section('content')
  <article class="space-y-8">
    <header class="max-w-3xl space-y-4">
      @if(!empty($blogUrl))
        <a class="btn btn-ghost btn-sm px-0" href="{{ $blogUrl }}">Back to blog</a>
      @endif

      @if(!empty($post['date']))
        <div class="text-xs font-medium uppercase tracking-normal text-base-content/50">{{ $post['date'] }}</div>
      @endif

      <h1 class="text-4xl font-semibold leading-tight tracking-normal sm:text-5xl">
        {{ $post['title'] }}
      </h1>

      @if(!empty($post['summary']))
        <p class="text-lg leading-8 text-base-content/70">{{ $post['summary'] }}</p>
      @endif
    </header>

    @if(!empty($post['content']))
      <div class="content-flow">
        {!! $post['content'] !!}
      </div>
    @endif
  </article>
@endsection
