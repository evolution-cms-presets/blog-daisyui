@extends('layouts.base')

@section('title', $blog['title'] ?? $documentObject['pagetitle'] ?? evo()->getConfig('site_name'))

@section('content')
  <div class="space-y-10">
    @if(!empty($blog['content']))
      <article class="content-flow">
        {!! $blog['content'] !!}
      </article>
    @endif

    <section class="space-y-4" aria-label="Blog posts">
      @forelse($blog['posts'] ?? [] as $post)
        <article class="rounded-lg border border-base-300 bg-base-100 p-5 shadow-sm">
          @if(!empty($post['date']))
            <div class="text-xs font-medium uppercase tracking-normal text-base-content/50">{{ $post['date'] }}</div>
          @endif
          <h2 class="mt-3 text-2xl font-semibold leading-8">
            <a class="link-hover" href="{{ $post['url'] }}">{{ $post['title'] }}</a>
          </h2>
          @if(!empty($post['summary']))
            <p class="mt-3 max-w-3xl text-base leading-7 text-base-content/70">{{ $post['summary'] }}</p>
          @endif
          <div class="mt-4">
            <a class="btn btn-primary btn-sm" href="{{ $post['url'] }}">Read</a>
          </div>
        </article>
      @empty
        <div class="rounded-lg border border-dashed border-base-300 bg-base-100 p-5 text-sm text-base-content/60">
          No posts yet.
        </div>
      @endforelse
    </section>

    @if(!empty($blog['pagination']['hasPages']))
      <nav class="join" aria-label="Blog pagination">
        @if(!empty($blog['pagination']['previous']))
          <a class="btn join-item btn-sm" href="{{ $blog['pagination']['previous'] }}">Previous</a>
        @else
          <span class="btn join-item btn-sm btn-disabled">Previous</span>
        @endif

        @foreach($blog['pagination']['pages'] as $page)
          <a class="btn join-item btn-sm @if($page['current']) btn-active @endif" href="{{ $page['url'] }}" @if($page['current']) aria-current="page" @endif>
            {{ $page['number'] }}
          </a>
        @endforeach

        @if(!empty($blog['pagination']['next']))
          <a class="btn join-item btn-sm" href="{{ $blog['pagination']['next'] }}">Next</a>
        @else
          <span class="btn join-item btn-sm btn-disabled">Next</span>
        @endif
      </nav>
    @endif
  </div>
@endsection
