@extends('layouts.base')

@section('title', $documentObject['longtitle'] ?? $documentObject['pagetitle'] ?? evo()->getConfig('site_name'))

@section('content')
  <div class="space-y-12">
    @if(!empty($documentObject['content']))
      <article class="content-flow">
        {!! $documentObject['content'] !!}
      </article>
    @endif

    <section class="space-y-5" aria-labelledby="latest-posts-title">
      <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
          <h2 id="latest-posts-title" class="text-2xl font-semibold tracking-normal">Latest news</h2>
          <p class="mt-1 text-sm text-base-content/60">Fresh posts from the blog.</p>
        </div>

        @if(!empty($blogUrl))
          <a class="btn btn-ghost btn-sm" href="{{ $blogUrl }}">All posts</a>
        @endif
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        @forelse($latestPosts ?? [] as $post)
          <article class="rounded-lg border border-base-300 bg-base-100 p-5 shadow-sm">
            @if(!empty($post['date']))
              <div class="text-xs font-medium uppercase tracking-normal text-base-content/50">{{ $post['date'] }}</div>
            @endif
            <h3 class="mt-3 text-lg font-semibold leading-7">
              <a class="link-hover" href="{{ $post['url'] }}">{{ $post['title'] }}</a>
            </h3>
            @if(!empty($post['summary']))
              <p class="mt-3 text-sm leading-6 text-base-content/70">{{ $post['summary'] }}</p>
            @endif
          </article>
        @empty
          <div class="rounded-lg border border-dashed border-base-300 bg-base-100 p-5 text-sm text-base-content/60">
            No posts yet.
          </div>
        @endforelse
      </div>
    </section>
  </div>
@endsection
