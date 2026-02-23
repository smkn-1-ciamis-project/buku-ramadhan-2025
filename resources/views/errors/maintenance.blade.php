<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance — Calakan</title>
    <link rel="icon" href="{{ asset('img/logo_smk.png') }}" type="image/png">
    <style>
        :root {
            --navy: #1e3a5f;
            --blue: #2563eb;
            --blue-light: #3b82f6;
            --blue-hover: #1d4ed8;
            --gold: #b8860b;
            --gold-bg: #fef9ee;
            --gold-border: #f0d68a;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-900: #0f172a;
            --white: #ffffff;
            --radius: 12px;
            --radius-lg: 24px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            background: var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow: hidden;
        }

        /* ── Main card ── */
        .maint-card {
            width: 100%;
            max-width: 520px;
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.07), 0 20px 50px -12px rgba(0,0,0,.12);
            overflow: hidden;
            animation: slideUp .5s cubic-bezier(.22,1,.36,1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Hero gradient top ── */
        .maint-hero {
            background: linear-gradient(160deg, #4b8af0 0%, #2563eb 50%, #1d4ed8 100%);
            padding: 48px 32px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Floating shapes */
        .maint-shape {
            position: absolute;
            border-radius: 50%;
            background: var(--white);
            opacity: .08;
            pointer-events: none;
        }
        .maint-shape--1 { width: 140px; height: 140px; top: -30px; right: -40px; animation: floatA 7s ease-in-out infinite; }
        .maint-shape--2 { width: 80px; height: 80px; bottom: 20px; left: -20px; animation: floatB 9s ease-in-out infinite; }
        .maint-shape--3 { width: 50px; height: 50px; top: 40%; left: 60%; opacity: .05; animation: floatC 11s ease-in-out infinite; }

        @keyframes floatA { 0%,100% { transform: translateY(0) scale(1); } 50% { transform: translateY(-14px) scale(1.04); } }
        @keyframes floatB { 0%,100% { transform: translateY(0) scale(1); } 50% { transform: translateY(10px) scale(.97); } }
        @keyframes floatC { 0%,100% { transform: translate(0,0) scale(1); } 33% { transform: translate(6px,-10px) scale(1.06); } 66% { transform: translate(-4px,6px) scale(.95); } }

        .maint-stars {
            position: absolute;
            color: var(--white);
            opacity: .15;
            pointer-events: none;
            font-size: 20px;
        }
        .maint-stars--1 { top: 14%; left: 12%; animation: twinkle 4s ease-in-out infinite; }
        .maint-stars--2 { bottom: 22%; right: 18%; font-size: 16px; opacity: .1; animation: twinkle 5.5s ease-in-out infinite 1.5s; }

        @keyframes twinkle {
            0%,100% { opacity: .15; transform: scale(1) rotate(0deg); }
            50% { opacity: .3; transform: scale(1.3) rotate(20deg); }
        }

        .maint-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(8px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s ease-in-out infinite;
        }
        .maint-icon svg {
            width: 40px;
            height: 40px;
            color: var(--white);
            stroke: var(--white);
            fill: none;
        }

        @keyframes pulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(255,255,255,.2); }
            50% { box-shadow: 0 0 0 14px rgba(255,255,255,0); }
        }

        .maint-hero-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--white);
            margin-bottom: 6px;
            letter-spacing: -.3px;
        }

        .maint-hero-sub {
            font-size: .85rem;
            color: rgba(255,255,255,.85);
            line-height: 1.5;
        }

        /* ── Body content ── */
        .maint-body {
            padding: 32px 28px 28px;
            text-align: center;
        }

        .maint-message {
            font-size: .9rem;
            color: var(--gray-500);
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .maint-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--gold-bg);
            border: 1px solid var(--gold-border);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: .75rem;
            font-weight: 600;
            color: var(--gold);
            letter-spacing: .2px;
            margin-bottom: 24px;
        }

        .maint-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: var(--radius);
            padding: 16px 18px;
            margin-bottom: 24px;
        }
        .maint-info-title {
            font-size: .78rem;
            font-weight: 700;
            color: #0369a1;
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-bottom: 6px;
        }
        .maint-info-text {
            font-size: .82rem;
            color: #0c4a6e;
            line-height: 1.6;
        }

        .maint-retry {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 44px;
            padding: 0 28px;
            background: var(--blue);
            color: var(--white);
            font-size: .9rem;
            font-weight: 700;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            letter-spacing: .2px;
            box-shadow: 0 1px 3px rgba(37,99,235,.3);
            transition: background .15s, box-shadow .15s, transform .1s;
            text-decoration: none;
        }
        .maint-retry:hover {
            background: var(--blue-hover);
            box-shadow: 0 4px 14px rgba(37,99,235,.35);
            transform: translateY(-1px);
        }
        .maint-retry:active { transform: translateY(0); }
        .maint-retry svg { width: 18px; height: 18px; }

        .maint-footer {
            padding: 0 28px 20px;
            text-align: center;
        }
        .maint-footer-text {
            font-size: .7rem;
            color: var(--gray-400);
        }
        .maint-footer-text a {
            color: var(--gray-400);
            text-decoration: none;
            transition: color .15s;
        }
        .maint-footer-text a:hover { color: var(--blue); }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            body { padding: 12px; }
            .maint-card { border-radius: 16px; }
            .maint-hero { padding: 36px 24px 32px; }
            .maint-icon { width: 64px; height: 64px; }
            .maint-icon svg { width: 32px; height: 32px; }
            .maint-hero-title { font-size: 1.25rem; }
            .maint-hero-sub { font-size: .8rem; }
            .maint-body { padding: 24px 20px 20px; }
        }
    </style>
</head>
<body>
    <div class="maint-card">

        {{-- ── Gradient Hero ── --}}
        <div class="maint-hero">
            <div class="maint-shape maint-shape--1"></div>
            <div class="maint-shape maint-shape--2"></div>
            <div class="maint-shape maint-shape--3"></div>
            <div class="maint-stars maint-stars--1">✦</div>
            <div class="maint-stars maint-stars--2">✦</div>

            <div class="maint-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>

            <div class="maint-hero-title">Sedang Dalam Perbaikan</div>
            <div class="maint-hero-sub">Sistem sedang dalam proses pemeliharaan</div>
        </div>

        {{-- ── Body ── --}}
        <div class="maint-body">
            <div class="maint-badge">🕌 Calakan — SMKN 1 Ciamis</div>

            <p class="maint-message">
                Mohon maaf atas ketidaknyamanannya. Sistem Calakan sedang dalam proses pemeliharaan untuk meningkatkan kualitas layanan. Silakan coba kembali beberapa saat lagi.
            </p>

            <div class="maint-info">
                <div class="maint-info-title">Informasi</div>
                <p class="maint-info-text">
                    {{ $message ?? 'Sistem sedang dalam perbaikan berkala. Semua data Anda tetap aman.' }}
                </p>
            </div>

            <a href="{{ url('/') }}" class="maint-retry">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"/>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                </svg>
                Coba Lagi
            </a>
        </div>

        {{-- ── Footer ── --}}
        <div class="maint-footer">
            <p class="maint-footer-text">
                &copy; {{ date('Y') }} <a href="#">Calakan</a> — SMKN 1 Ciamis
            </p>
        </div>

    </div>
</body>
</html>
