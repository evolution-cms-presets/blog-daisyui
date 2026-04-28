@php
  $homeUrl = evo()->makeUrl((int) evo()->getConfig('site_start'));
  $siteName = evo()->getConfig('site_name') ?: 'Evolution CMS';
  $activeIds = array_map('intval', $parentIds ?? []);

  $lightThemes = [
    'light',
    'cupcake',
    'bumblebee',
    'emerald',
    'corporate',
    'retro',
    'cyberpunk',
    'valentine',
    'garden',
    'lofi',
    'pastel',
    'fantasy',
    'wireframe',
    'cmyk',
    'autumn',
    'acid',
    'lemonade',
    'winter',
    'caramellatte',
    'nord',
    'silk',
  ];

  $darkThemes = [
    'dark',
    'synthwave',
    'halloween',
    'forest',
    'aqua',
    'black',
    'luxury',
    'dracula',
    'business',
    'night',
    'coffee',
    'dim',
    'sunset',
    'abyss',
  ];

  sort($lightThemes, SORT_NATURAL | SORT_FLAG_CASE);
  sort($darkThemes, SORT_NATURAL | SORT_FLAG_CASE);
@endphp

<header class="sticky top-0 z-30 border-b border-base-300 bg-base-100/95 shadow-sm backdrop-blur">
  <div class="navbar mx-auto min-h-16 w-full max-w-6xl gap-2 px-4 sm:px-6 lg:px-8">
    <div class="navbar-start min-w-0">
      @if(!empty($menu))
        <div class="dropdown">
          <button tabindex="0" type="button" class="btn btn-ghost btn-square lg:hidden" aria-label="Open navigation">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h16"></path>
            </svg>
          </button>
          <ul tabindex="-1" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-20 mt-3 w-56 p-2 shadow-xl" data-site-menu>
            @foreach($menu as $item)
              @php
                $id = (int) ($item['id'] ?? 0);
                $title = ($item['menutitle'] ?? '') ?: ($item['pagetitle'] ?? '');
                $children = $item['children'] ?? [];
                $isActive = in_array($id, $activeIds, true);
              @endphp

              @if(empty($children))
                <li>
                  <a class="@if($isActive) active @endif" href="{{ evo()->makeUrl($id) }}" @if($isActive) aria-current="page" @endif>
                    {{ $title }}
                  </a>
                </li>
              @else
                <li>
                  <details @if($isActive) open @endif>
                    <summary class="@if($isActive) active @endif">
                      {{ $title }}
                    </summary>
                    <ul class="p-2">
                      @foreach($children as $child)
                        @php
                          $childId = (int) ($child['id'] ?? 0);
                          $childTitle = ($child['menutitle'] ?? '') ?: ($child['pagetitle'] ?? '');
                          $childActive = in_array($childId, $activeIds, true);
                        @endphp
                        <li>
                          <a class="@if($childActive) active @endif" href="{{ evo()->makeUrl($childId) }}" @if($childActive) aria-current="page" @endif>
                            {{ $childTitle }}
                          </a>
                        </li>
                      @endforeach
                    </ul>
                  </details>
                </li>
              @endif
            @endforeach
          </ul>
        </div>
      @endif

      <a class="btn btn-ghost min-w-0 px-2 text-base font-semibold normal-case" href="{{ $homeUrl }}" aria-label="{{ $siteName }}">
        <span class="truncate">{{ $siteName }}</span>
      </a>
    </div>

    @if(!empty($menu))
      <div class="navbar-center hidden min-w-0 lg:flex">
        <ul class="menu menu-horizontal gap-1 px-1" data-site-menu aria-label="Primary navigation">
          @foreach($menu as $item)
            @php
              $id = (int) ($item['id'] ?? 0);
              $title = ($item['menutitle'] ?? '') ?: ($item['pagetitle'] ?? '');
              $children = $item['children'] ?? [];
              $isActive = in_array($id, $activeIds, true);
            @endphp

            @if(empty($children))
              <li>
                <a class="@if($isActive) active @endif" href="{{ evo()->makeUrl($id) }}" @if($isActive) aria-current="page" @endif>
                  {{ $title }}
                </a>
              </li>
            @else
              <li>
                <details @if($isActive) open @endif>
                  <summary class="@if($isActive) active @endif">
                    {{ $title }}
                  </summary>
                  <ul class="p-2 bg-base-100 rounded-box shadow-lg w-44 z-20">
                    @foreach($children as $child)
                      @php
                        $childId = (int) ($child['id'] ?? 0);
                        $childTitle = ($child['menutitle'] ?? '') ?: ($child['pagetitle'] ?? '');
                        $childActive = in_array($childId, $activeIds, true);
                      @endphp
                      <li>
                        <a class="@if($childActive) active @endif" href="{{ evo()->makeUrl($childId) }}" @if($childActive) aria-current="page" @endif>
                          {{ $childTitle }}
                        </a>
                      </li>
                    @endforeach
                  </ul>
                </details>
              </li>
            @endif
          @endforeach
        </ul>
      </div>
    @endif

    <div class="navbar-end shrink-0 gap-1 sm:gap-2">
      <label class="swap swap-rotate btn btn-ghost btn-square" aria-label="Toggle color mode">
        <input id="theme-swap" type="checkbox" class="theme-controller" value="dark">
        <svg xmlns="http://www.w3.org/2000/svg" class="swap-off h-6 w-6 fill-current" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M5.64 17l-1.42 1.39 1.39 1.42L7.03 18.4 5.64 17Zm12.72 0-1.39 1.4 1.42 1.41 1.39-1.42L18.36 17ZM12 4.75A7.25 7.25 0 1 0 19.25 12 7.26 7.26 0 0 0 12 4.75Zm0 12.5A5.25 5.25 0 1 1 17.25 12 5.26 5.26 0 0 1 12 17.25ZM11 1h2v3h-2V1Zm0 19h2v3h-2v-3ZM1 11h3v2H1v-2Zm19 0h3v2h-3v-2ZM4.22 5.61 5.61 4.2l1.42 1.42-1.4 1.4-1.41-1.41Zm12.75 0 1.42-1.42 1.39 1.42-1.41 1.41-1.4-1.41Z"></path>
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" class="swap-on h-6 w-6 fill-current" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M21.64 13.65A9 9 0 0 1 10.35 2.36a.75.75 0 0 0-.86-.96A10.5 10.5 0 1 0 22.6 14.51a.75.75 0 0 0-.96-.86Z"></path>
        </svg>
      </label>

      <div class="dropdown dropdown-end">
        <button tabindex="0" type="button" class="btn btn-ghost btn-square" aria-label="Choose DaisyUI theme">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="13.5" cy="6.5" r="1.5"></circle>
            <circle cx="17.5" cy="10.5" r="1.5"></circle>
            <circle cx="8.5" cy="8.5" r="1.5"></circle>
            <path d="M12 3a9 9 0 1 0 0 18h1.5a2.5 2.5 0 0 0 0-5H12a1.5 1.5 0 0 1 0-3h2a7 7 0 0 0 0-10h-2Z"></path>
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="-ml-2 h-4 w-4 opacity-60" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"></path>
          </svg>
        </button>

        <ul id="theme-menu" tabindex="-1" class="menu menu-sm dropdown-content bg-base-200 rounded-box z-20 mt-3 max-h-[28rem] w-64 flex-nowrap overflow-y-auto overflow-x-hidden p-2 shadow-2xl">
          @foreach($lightThemes as $theme)
            <li data-theme-group="light">
              <button type="button" class="btn btn-sm btn-ghost theme-item w-full justify-start gap-3 px-2" data-theme-item="{{ $theme }}">
                <span class="grid shrink-0 grid-cols-2 grid-rows-2 gap-0.5 rounded-md bg-base-100 p-1 shadow-sm" data-theme="{{ $theme }}">
                  <span class="block h-2 w-2 rounded-full bg-primary"></span>
                  <span class="block h-2 w-2 rounded-full bg-secondary"></span>
                  <span class="block h-2 w-2 rounded-full bg-accent"></span>
                  <span class="block h-2 w-2 rounded-full bg-neutral"></span>
                </span>
                <span class="w-36 truncate text-left">{{ $theme }}</span>
              </button>
            </li>
          @endforeach

          @foreach($darkThemes as $theme)
            <li data-theme-group="dark">
              <button type="button" class="btn btn-sm btn-ghost theme-item w-full justify-start gap-3 px-2" data-theme-item="{{ $theme }}">
                <span class="grid shrink-0 grid-cols-2 grid-rows-2 gap-0.5 rounded-md bg-base-100 p-1 shadow-sm" data-theme="{{ $theme }}">
                  <span class="block h-2 w-2 rounded-full bg-primary"></span>
                  <span class="block h-2 w-2 rounded-full bg-secondary"></span>
                  <span class="block h-2 w-2 rounded-full bg-accent"></span>
                  <span class="block h-2 w-2 rounded-full bg-neutral"></span>
                </span>
                <span class="w-36 truncate text-left">{{ $theme }}</span>
              </button>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</header>
