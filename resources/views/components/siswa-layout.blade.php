<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Calakan - SMKN 1 Ciamis</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js + Collapse Plugin --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Dashboard CSS --}}
    <link rel="stylesheet" href="{{ asset('themes/ramadhan/css/dashboard.css') }}?v={{ time() }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; padding: 0; background: #f1f5f9; font-family: 'Inter', system-ui, -apple-system, sans-serif; -webkit-font-smoothing: antialiased; }
        .font-arabic { font-family: 'Traditional Arabic', 'Scheherazade New', 'Amiri', serif; }
    </style>
</head>
<body>
    {{ $slot }}

    {{-- Dashboard JS --}}
    <script src="{{ asset('themes/ramadhan/js/dashboard.js') }}?v={{ time() }}"></script>
</body>
</html>
