<!doctype html>
<html lang="{{ evo()->getLocale() ?: evo()->getConfig('lang', 'en') }}" data-theme="light">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', evo()->getConfig('site_name', 'Evolution CMS'))</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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
