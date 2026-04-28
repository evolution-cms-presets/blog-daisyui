@php
  $siteName = evo()->getConfig('site_name') ?: 'Evolution CMS';
  $year = date('Y');
@endphp

<footer class="border-t border-base-300 bg-base-100/80">
  <div class="mx-auto flex w-full max-w-6xl flex-col gap-4 px-4 py-6 text-sm text-base-content/70 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
    <div>
      &copy; {{ $year }} {{ $siteName }}.
    </div>

    @if(!empty($menu))
      <nav aria-label="Footer navigation">
        <ul class="flex flex-wrap gap-x-4 gap-y-2">
          @foreach($menu as $item)
            @php
              $id = (int) ($item['id'] ?? 0);
              $title = ($item['menutitle'] ?? '') ?: ($item['pagetitle'] ?? '');
            @endphp
            @if($id > 0 && $title !== '')
              <li>
                <a class="link-hover" href="{{ evo()->makeUrl($id) }}">{{ $title }}</a>
              </li>
            @endif
          @endforeach
        </ul>
      </nav>
    @endif
  </div>
</footer>
