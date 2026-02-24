<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Calakan - SMKN 1 Ciamis</title>

    {{-- PWA Meta Tags --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e3a8a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Calakan">
    <link rel="apple-touch-icon" href="/img/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/img/icons/icon-192x192.png">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js + Collapse Plugin --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Dashboard CSS --}}
    <link rel="stylesheet" href="{{ asset('themes/ramadhan/css/dashboard.css') }}?v={{ filemtime(public_path('themes/ramadhan/css/dashboard.css')) }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; padding: 0; background: #f1f5f9; font-family: 'Inter', system-ui, -apple-system, sans-serif; -webkit-font-smoothing: antialiased; }
        .font-arabic { font-family: 'Traditional Arabic', 'Scheherazade New', 'Amiri', serif; }
    </style>
</head>
<body>
    {{ $slot }}

    {{-- Dashboard JS --}}
    <script src="{{ asset('themes/ramadhan/js/dashboard.js') }}?v={{ filemtime(public_path('themes/ramadhan/js/dashboard.js')) }}" defer></script>

    {{-- PWA Service Worker --}}
    <script>
        if ('serviceWorker' in navigator && !navigator.userAgent.includes('Calakan-Android')) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
