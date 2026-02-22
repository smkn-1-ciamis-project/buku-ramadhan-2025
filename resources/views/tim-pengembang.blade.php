<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tim Pengembang — Buku Ramadhan SMKN 1 Ciamis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            color: #1e293b;
        }

        /* -- Header -- */
        .page-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #1d4ed8 100%);
            padding: 56px 24px 72px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            color: rgba(255,255,255,0.9);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .header-badge svg { width: 14px; height: 14px; }

        .page-title {
            font-size: clamp(28px, 5vw, 42px);
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
        }

        .page-subtitle {
            font-size: 15px;
            color: rgba(255,255,255,0.75);
            max-width: 480px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* -- Main content -- */
        .page-content {
            max-width: 680px;
            margin: -36px auto 0;
            padding: 0 16px 64px;
            position: relative;
            z-index: 1;
        }

        /* -- Team Cards -- */
        .team-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .team-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            border: 1px solid #e2e8f0;
        }

        .card-top {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 16px;
        }

        /* Photo */
        .card-photo {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e2e8f0;
            display: block;
            flex-shrink: 0;
        }

        .card-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .card-avatar--2 { background: linear-gradient(135deg, #7c3aed, #6d28d9); }

        .card-avatar span {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
        }

        /* Info */
        .card-meta { flex: 1; min-width: 0; }

        .card-name {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .card-role {
            font-size: 13px;
            font-weight: 600;
            color: #2563eb;
            margin-bottom: 12px;
        }

        /* Social links */
        .card-socials {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .social-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .social-btn svg { width: 14px; height: 14px; flex-shrink: 0; }

        .social-btn--linkedin  { background: #e8f4fd; color: #0077b5; }
        .social-btn--github    { background: #f0f0f0; color: #1b1f23; }
        .social-btn--instagram { background: #fce7f3; color: #c2185b; }
        .social-btn--email     { background: #ecfdf5; color: #059669; }

        /* Tanggung Jawab */
        .card-divider {
            border: none;
            border-top: 1px solid #f1f5f9;
            margin: 16px 0;
        }

        .card-responsibility {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }

        .card-responsibility strong {
            color: #475569;
            font-weight: 600;
        }

        /* -- Footer -- */
        .page-footer {
            text-align: center;
            padding: 0 16px 40px;
        }

        .page-footer p {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 10px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #2563eb;
            text-decoration: none;
            transition: opacity 0.15s;
        }

        .back-link:hover { opacity: 0.75; }
        .back-link svg { width: 14px; height: 14px; }

        /* -- Responsive -- */
        @media (max-width: 480px) {
            .card-top { flex-direction: column; align-items: center; text-align: center; }
            .card-socials { justify-content: center; }
        }
    </style>
</head>
<body>

    <div class="page-header">
        <div class="header-badge">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5.477-3.717M17 20H7m10 0v-2c0-.768-.195-1.49-.536-2.117M7 20H2v-2a4 4 0 015.477-3.717M7 20v-2c0-.768.195-1.49.536-2.117m0 0A5.002 5.002 0 0112 10a5.002 5.002 0 014.463 5.883M12 10a4 4 0 110-8 4 4 0 010 8z"/>
            </svg>
            Tim Pengembang
        </div>
        <h1 class="page-title">Buku Ramadhan</h1>
        <p class="page-subtitle">Aplikasi catatan ibadah digital siswa SMKN 1 Ciamis — dibangun dari nol, dirancang untuk generasi terbaik.</p>
    </div>

    <div class="page-content">
        <div class="team-grid">

            {{-- Muhammad Fikri Haikal --}}
            <div class="team-card">
                <div class="card-top">
                    <img
                        src="https://avatars.githubusercontent.com/fikrihaikal17"
                        alt="Muhammad Fikri Haikal"
                        class="card-photo"
                        onerror="this.style.display='none';this.insertAdjacentHTML('afterend','<div class=\'card-avatar\'><span>MF</span></div>');">
                    <div class="card-meta">
                        <div class="card-name">Muhammad Fikri Haikal</div>
                        <div class="card-role">Project Lead &amp; Full Stack Developer</div>
                        <div class="card-socials">
                            <a href="https://www.linkedin.com/in/fikriihaikall"
                               target="_blank" rel="noopener noreferrer" class="social-btn social-btn--linkedin">
                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                LinkedIn
                            </a>
                            <a href="https://github.com/fikrihaikal17"
                               target="_blank" rel="noopener noreferrer" class="social-btn social-btn--github">
                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61-.546-1.385-1.335-1.755-1.335-1.755-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                                GitHub
                            </a>
                            <a href="https://instagram.com/fikrii_haikalll17"
                               target="_blank" rel="noopener noreferrer" class="social-btn social-btn--instagram">
                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                Instagram
                            </a>
                            <a href="mailto:fikrihaikal170308@email.com"
                               class="social-btn social-btn--email">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Galuh Surya Putra --}}
            <div class="team-card">
                <div class="card-top">
                    <img
                        src="https://avatars.githubusercontent.com/Ptragaluhhh28"
                        alt="Galuh Surya Putra"
                        class="card-photo"
                        onerror="this.style.display='none';this.insertAdjacentHTML('afterend','<div class=\'card-avatar card-avatar--2\'><span>GS</span></div>');">
                    <div class="card-meta">
                        <div class="card-name">Galuh Surya Putra</div>
                        <div class="card-role">Frontend Developer</div>
                        <div class="card-socials">
                            <a href="https://github.com/Ptragaluhhh28"
                               target="_blank" rel="noopener noreferrer" class="social-btn social-btn--github">
                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61-.546-1.385-1.335-1.755-1.335-1.755-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                                GitHub
                            </a>
                            <a href="https://instagram.com/luhptraa28"
                               target="_blank" rel="noopener noreferrer" class="social-btn social-btn--instagram">
                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                Instagram
                            </a>
                            <a href="mailto:putragaluh28@email.com"
                               class="social-btn social-btn--email">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="page-footer">
        <p>&copy; {{ date('Y') }} SMKN 1 Ciamis. Semua hak cipta dilindungi.</p>
        <a href="{{ url('/') }}" onclick="if(document.referrer){history.back();return false;}" class="back-link">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

</body>
</html>
