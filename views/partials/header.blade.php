@php
  $homeUrl = evo()->makeUrl((int) evo()->getConfig('site_start'));
  $siteName = evo()->getConfig('site_name') ?: 'Evolution CMS';
  $activeIds = array_map('intval', $parentIds ?? []);
  $themeConfig = $themeConfig ?? [];
  $themeEnabled = (bool) ($themeConfig['enabled'] ?? true);
  $showThemeToggle = $themeEnabled && (bool) ($themeConfig['showToggle'] ?? true);
  $showThemePicker = $themeEnabled && (bool) ($themeConfig['showPicker'] ?? true);
  $lightThemes = $themeConfig['light'] ?? [];
  $darkThemes = $themeConfig['dark'] ?? [];
@endphp

<header class="sticky top-0 z-30 border-b border-base-300 bg-base-100/95 shadow-sm backdrop-blur">
  <div class="navbar mx-auto min-h-16 w-full max-w-6xl gap-2 px-4 sm:px-6 lg:px-8">
    <div class="navbar-start min-w-0">
      @if(!empty($menu))
        <div class="dropdown">
          <button tabindex="0" type="button" class="btn btn-ghost btn-square lg:hidden" aria-label="Open navigation">
            {!! svg('tabler-menu-2', 'h-5 w-5')->toHtml() !!}
          </button>
          <ul tabindex="-1" class="menu menu-sm dropdown-content bg-base-300 rounded-box z-20 mt-3 w-56 p-2 shadow-2xl" data-site-menu>
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
                  <ul class="p-2 bg-base-300 rounded-box shadow-2xl w-44 z-20">
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

    <div class="navbar-end shrink-0 gap-2 sm:gap-3">
      @if($showThemeToggle)
        <label class="swap swap-rotate grid h-10 w-10 cursor-pointer place-items-center rounded-lg text-base-content/80 transition hover:bg-base-content/10 hover:text-base-content" aria-label="Toggle color mode">
          <input id="theme-swap" type="checkbox" class="theme-controller" value="{{ $themeConfig['defaultDark'] ?? 'evodark' }}">
          {!! svg('tabler-sun', 'swap-off h-6 w-6')->toHtml() !!}
          {!! svg('tabler-moon', 'swap-on h-6 w-6')->toHtml() !!}
        </label>
      @endif

      @if($showThemePicker && (!empty($lightThemes) || !empty($darkThemes)))
        <div class="dropdown dropdown-end">
          <button tabindex="0" type="button" class="btn btn-ghost btn-sm gap-1 px-2 text-base-content/80 hover:text-base-content" aria-label="Choose DaisyUI theme">
            {!! svg('tabler-palette', 'h-6 w-6')->toHtml() !!}
            {!! svg('tabler-chevron-down', 'h-4 w-4 opacity-60')->toHtml() !!}
          </button>

          <ul id="theme-menu" tabindex="-1" class="menu menu-sm dropdown-content bg-base-300 rounded-box z-20 mt-3 max-h-[28rem] w-56 flex-nowrap overflow-y-auto overflow-x-hidden p-2 shadow-2xl">
            @foreach($lightThemes as $themeOption)
              <li data-theme-group="light">
                <button type="button" class="btn btn-sm btn-ghost theme-item w-full justify-start gap-3 px-2" data-theme-item="{{ $themeOption['name'] }}">
                  <span class="grid shrink-0 grid-cols-2 grid-rows-2 gap-0.5 rounded-md bg-base-100 p-1 shadow-sm" data-theme="{{ $themeOption['name'] }}">
                    <span class="block h-2 w-2 rounded-full bg-primary"></span>
                    <span class="block h-2 w-2 rounded-full bg-secondary"></span>
                    <span class="block h-2 w-2 rounded-full bg-accent"></span>
                    <span class="block h-2 w-2 rounded-full bg-neutral"></span>
                  </span>
                  <span class="w-32 truncate text-left">{{ $themeOption['label'] }}</span>
                </button>
              </li>
            @endforeach

            @foreach($darkThemes as $themeOption)
              <li data-theme-group="dark">
                <button type="button" class="btn btn-sm btn-ghost theme-item w-full justify-start gap-3 px-2" data-theme-item="{{ $themeOption['name'] }}">
                  <span class="grid shrink-0 grid-cols-2 grid-rows-2 gap-0.5 rounded-md bg-base-100 p-1 shadow-sm" data-theme="{{ $themeOption['name'] }}">
                    <span class="block h-2 w-2 rounded-full bg-primary"></span>
                    <span class="block h-2 w-2 rounded-full bg-secondary"></span>
                    <span class="block h-2 w-2 rounded-full bg-accent"></span>
                    <span class="block h-2 w-2 rounded-full bg-neutral"></span>
                  </span>
                  <span class="w-32 truncate text-left">{{ $themeOption['label'] }}</span>
                </button>
              </li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>
  </div>
</header>
