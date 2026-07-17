<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="theme-color" content="#1a6b3c">
    <title>@yield('title', __('peta.judul_situs'))</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col bg-white text-gray-900 antialiased">
    <header class="bg-wisata-green-700 text-white px-4 py-3 flex items-center justify-between shrink-0 gap-2">
        <a href="{{ route('peta.index') }}" class="font-semibold truncate">
            🗺️ {{ __('peta.judul_situs') }}
        </a>
        <div class="flex items-center gap-3 shrink-0">
            <span class="text-xs opacity-80 hidden sm:inline">{{ __('peta.sub_judul') }}</span>
            @include('partials.language-switcher')
        </div>
    </header>

    <main class="flex-1 min-h-0 @yield('main-class', 'overflow-y-auto')">
        @yield('content')
    </main>
</body>
</html>
