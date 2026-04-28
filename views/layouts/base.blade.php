@php
  $themeConfig = $themeConfig ?? [
    'enabled' => true,
    'showToggle' => true,
    'showThemes' => true,
    'defaultLight' => 'evolight',
    'defaultDark' => 'evodark',
    'storageKey' => 'evo.blogDaisyui.theme',
    'light' => [
      ['name' => 'evolight', 'label' => 'EVO Light'],
      ['name' => 'evolightness', 'label' => 'EVO Lightness'],
      ['name' => 'light', 'label' => 'Light'],
      ['name' => 'cupcake', 'label' => 'Cupcake'],
      ['name' => 'bumblebee', 'label' => 'Bumblebee'],
      ['name' => 'emerald', 'label' => 'Emerald'],
      ['name' => 'corporate', 'label' => 'Corporate'],
      ['name' => 'retro', 'label' => 'Retro'],
      ['name' => 'cyberpunk', 'label' => 'Cyberpunk'],
      ['name' => 'valentine', 'label' => 'Valentine'],
      ['name' => 'garden', 'label' => 'Garden'],
      ['name' => 'lofi', 'label' => 'Lofi'],
      ['name' => 'pastel', 'label' => 'Pastel'],
      ['name' => 'fantasy', 'label' => 'Fantasy'],
      ['name' => 'wireframe', 'label' => 'Wireframe'],
      ['name' => 'cmyk', 'label' => 'CMYK'],
      ['name' => 'autumn', 'label' => 'Autumn'],
      ['name' => 'acid', 'label' => 'Acid'],
      ['name' => 'lemonade', 'label' => 'Lemonade'],
      ['name' => 'winter', 'label' => 'Winter'],
      ['name' => 'caramellatte', 'label' => 'Caramellatte'],
      ['name' => 'nord', 'label' => 'Nord'],
      ['name' => 'silk', 'label' => 'Silk'],
    ],
    'dark' => [
      ['name' => 'evodark', 'label' => 'EVO Dark'],
      ['name' => 'evodarkness', 'label' => 'EVO Darkness'],
      ['name' => 'dark', 'label' => 'Dark'],
      ['name' => 'synthwave', 'label' => 'Synthwave'],
      ['name' => 'halloween', 'label' => 'Halloween'],
      ['name' => 'forest', 'label' => 'Forest'],
      ['name' => 'aqua', 'label' => 'Aqua'],
      ['name' => 'black', 'label' => 'Black'],
      ['name' => 'luxury', 'label' => 'Luxury'],
      ['name' => 'dracula', 'label' => 'Dracula'],
      ['name' => 'business', 'label' => 'Business'],
      ['name' => 'night', 'label' => 'Night'],
      ['name' => 'coffee', 'label' => 'Coffee'],
      ['name' => 'dim', 'label' => 'Dim'],
      ['name' => 'sunset', 'label' => 'Sunset'],
      ['name' => 'abyss', 'label' => 'Abyss'],
    ],
  ];
  $initialTheme = $themeConfig['defaultLight'] ?? 'evolight';
@endphp
<!doctype html>
<html lang="{{ evo()->getLocale() ?: evo()->getConfig('lang', 'en') }}" data-theme="{{ $initialTheme }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', evo()->getConfig('site_name', 'Evolution CMS'))</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script>
      window.EvoBlogDaisyuiTheme = @json($themeConfig);
      (function () {
        var config = window.EvoBlogDaisyuiTheme || {};
        if (config.enabled === false) return;

        var light = (config.light || []).map(function (item) { return item.name; }).filter(Boolean);
        var dark = (config.dark || []).map(function (item) { return item.name; }).filter(Boolean);
        var allowed = light.concat(dark);
        var defaultLight = config.defaultLight || light[0] || 'light';
        var defaultDark = config.defaultDark || dark[0] || defaultLight;
        var storageKey = config.storageKey || 'evo.blogDaisyui.theme';
        var storageLight = storageKey + '.light';
        var storageDark = storageKey + '.dark';

        function read(key) {
          try {
            return window.localStorage.getItem(key);
          } catch (error) {
            return null;
          }
        }

        function allowedTheme(theme) {
          return allowed.length === 0 || allowed.indexOf(theme) !== -1;
        }

        var theme = read(storageKey);
        var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (!theme || !allowedTheme(theme)) {
          theme = prefersDark ? read(storageDark) || defaultDark : read(storageLight) || defaultLight;
        }
        if (!allowedTheme(theme)) {
          theme = prefersDark ? defaultDark : defaultLight;
        }

        document.documentElement.setAttribute('data-theme', theme);
      })();
    </script>
    <link rel="stylesheet" href="/themes/{{ env('EVO_PRESET_NAME', 'blog-daisyui') }}/css/themes.css">
    <link rel="stylesheet" href="/themes/{{ env('EVO_PRESET_NAME', 'blog-daisyui') }}/css/app.css">
    <script defer src="/themes/{{ env('EVO_PRESET_NAME', 'blog-daisyui') }}/js/theme.js"></script>
  </head>
  <body class="min-h-screen bg-base-200 text-base-content antialiased">
    <div class="grid min-h-screen grid-rows-[auto_1fr]">
      @include('partials.header')

      <main class="mx-auto w-full max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        @yield('content')
      </main>
    </div>
  </body>
</html>
