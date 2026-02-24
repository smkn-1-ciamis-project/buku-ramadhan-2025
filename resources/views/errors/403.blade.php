<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>403 — Akses Ditolak</title>
    <link rel="icon" href="{{ asset('img/logo_smk.png') }}" type="image/png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e3a8a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Calakan">
    <link rel="apple-touch-icon" href="/img/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/img/icons/icon-192x192.png">
    <style>
        :root {
            --navy: #1e3a5f;
            --blue: #2563eb;
            --blue-hover: #1d4ed8;
            --red: #dc2626;
            --red-light: #fef2f2;
            --red-border: #fecaca;
            --gold: #b8860b;
            --gold-bg: #fef9ee;
            --gold-border: #f0d68a;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
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

        .err-card {
            width: 100%;
            max-width: 480px;
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

        .err-hero {
            background: linear-gradient(160deg, #f87171 0%, #dc2626 50%, #b91c1c 100%);
            padding: 44px 32px 36px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .err-shape {
            position: absolute;
            border-radius: 50%;
            background: var(--white);
            opacity: .08;
            pointer-events: none;
        }
        .err-shape--1 { width: 120px; height: 120px; top: -25px; right: -35px; animation: floatA 7s ease-in-out infinite; }
        .err-shape--2 { width: 70px; height: 70px; bottom: 15px; left: -20px; animation: floatB 9s ease-in-out infinite; }

        @keyframes floatA { 0%,100% { transform: translateY(0) scale(1); } 50% { transform: translateY(-12px) scale(1.04); } }
        @keyframes floatB { 0%,100% { transform: translateY(0) scale(1); } 50% { transform: translateY(8px) scale(.97); } }

        .err-stars {
            position: absolute;
            color: var(--white);
            opacity: .15;
            pointer-events: none;
            font-size: 18px;
        }
        .err-stars--1 { top: 16%; left: 14%; animation: twinkle 4s ease-in-out infinite; }
        .err-stars--2 { bottom: 20%; right: 15%; font-size: 14px; opacity: .1; animation: twinkle 5.5s ease-in-out infinite 1.5s; }

        @keyframes twinkle {
            0%,100% { opacity: .15; transform: scale(1) rotate(0deg); }
            50% { opacity: .3; transform: scale(1.3) rotate(20deg); }
        }

        .err-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 16px;
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(8px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: shake 3s ease-in-out infinite;
        }
        .err-icon svg {
            width: 40px;
            height: 40px;
            color: var(--white);
            stroke: var(--white);
            fill: none;
        }

        @keyframes shake {
            0%,100% { transform: rotate(0deg); }
            5% { transform: rotate(-5deg); }
            10% { transform: rotate(5deg); }
            15% { transform: rotate(-3deg); }
            20% { transform: rotate(3deg); }
            25% { transform: rotate(0deg); }
        }

        .err-code {
            font-size: 4.5rem;
            font-weight: 900;
            color: var(--white);
            line-height: 1;
            letter-spacing: -2px;
            margin-bottom: 8px;
            text-shadow: 0 4px 20px rgba(0,0,0,.15);
        }

        .err-hero-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 4px;
            letter-spacing: -.2px;
        }

        .err-hero-sub {
            font-size: .82rem;
            color: rgba(255,255,255,.8);
            line-height: 1.5;
        }

        .err-body {
            padding: 28px 28px 24px;
            text-align: center;
        }

        .err-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--gold-bg);
            border: 1px solid var(--gold-border);
            border-radius: 20px;
            padding: 5px 12px;
            font-size: .72rem;
            font-weight: 600;
            color: var(--gold);
            letter-spacing: .2px;
            margin-bottom: 20px;
        }

        .err-alert {
            background: var(--red-light);
            border: 1px solid var(--red-border);
            border-radius: var(--radius);
            padding: 16px 18px;
            margin-bottom: 20px;
            text-align: left;
        }
        .err-alert-title {
            font-size: .78rem;
            font-weight: 700;
            color: #991b1b;
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-bottom: 6px;
        }
        .err-alert-text {
            font-size: .82rem;
            color: #b91c1c;
            line-height: 1.6;
        }

        .err-message {
            font-size: .88rem;
            color: var(--gray-500);
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .err-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .err-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 44px;
            padding: 0 24px;
            font-size: .88rem;
            font-weight: 700;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            letter-spacing: .2px;
            transition: background .15s, box-shadow .15s, transform .1s;
            text-decoration: none;
        }
        .err-btn:active { transform: translateY(0) !important; }
        .err-btn svg { width: 16px; height: 16px; }

        .err-btn--primary {
            background: var(--blue);
            color: var(--white);
            box-shadow: 0 1px 3px rgba(37,99,235,.3);
        }
        .err-btn--primary:hover {
            background: var(--blue-hover);
            box-shadow: 0 4px 14px rgba(37,99,235,.35);
            transform: translateY(-1px);
        }

        .err-btn--secondary {
            background: var(--gray-100);
            color: var(--gray-500);
            border: 1px solid var(--gray-200);
        }
        .err-btn--secondary:hover {
            background: var(--gray-200);
            transform: translateY(-1px);
        }

        .err-footer {
            padding: 0 28px 18px;
            text-align: center;
        }
        .err-footer-text {
            font-size: .7rem;
            color: var(--gray-400);
        }
        .err-footer-text a {
            color: var(--gray-400);
            text-decoration: none;
            transition: color .15s;
        }
        .err-footer-text a:hover { color: var(--blue); }

        @media (max-width: 480px) {
            body { padding: 12px; }
            .err-card { border-radius: 16px; }
            .err-hero { padding: 32px 24px 28px; }
            .err-code { font-size: 3.5rem; }
            .err-icon { width: 64px; height: 64px; }
            .err-icon svg { width: 32px; height: 32px; }
            .err-hero-title { font-size: 1.05rem; }
            .err-hero-sub { font-size: .78rem; }
            .err-body { padding: 22px 18px 18px; }
            .err-actions { flex-direction: column; }
            .err-btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="err-card">

        {{-- ── Gradient Hero (Red) ── --}}
        <div class="err-hero">
            <div class="err-shape err-shape--1"></div>
            <div class="err-shape err-shape--2"></div>
            <div class="err-stars err-stars--1">✦</div>
            <div class="err-stars err-stars--2">✦</div>

            <div class="err-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    <line x1="12" y1="15" x2="12" y2="18"/>
                </svg>
            </div>

            <div class="err-code">403</div>
            <div class="err-hero-title">Akses Ditolak</div>
            <div class="err-hero-sub">Anda tidak memiliki izin untuk mengakses halaman ini</div>
        </div>

        {{-- ── Body ── --}}
        <div class="err-body">
            <div class="err-badge">🕌 Calakan — SMKN 1 Ciamis</div>

            <div class="err-alert">
                <div class="err-alert-title">Perhatian</div>
                <p class="err-alert-text">
                    {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses untuk melihat halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.' }}
                </p>
            </div>

            <p class="err-message">
                Pastikan Anda sudah login dengan akun yang memiliki izin yang sesuai untuk mengakses halaman ini.
            </p>

            <div class="err-actions">
                <a href="{{ url('/') }}" class="err-btn err-btn--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Ke Halaman Utama
                </a>
                <button onclick="history.back()" class="err-btn err-btn--secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    Kembali
                </button>
            </div>
        </div>

        {{-- ── Footer ── --}}
        <div class="err-footer">
            <p class="err-footer-text">
                &copy; {{ date('Y') }} <a href="{{ url('/tim-pengembang') }}">Calakan</a> — SMKN 1 Ciamis
            </p>
        </div>

    </div>
</body>
</html>
