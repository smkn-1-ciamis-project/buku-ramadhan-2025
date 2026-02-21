<x-filament-panels::page>
    <style>
        /* ─── Superadmin Dashboard ─── */
        .sa-wrap { display:flex; flex-direction:column; gap:1.5rem; }

        /* Hero Banner */
        .sa-hero {
            position:relative; overflow:hidden; border-radius:1rem;
            background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 35%, #2563eb 100%);
            padding:2rem; color:#fff; box-shadow:0 10px 25px -5px rgba(37,99,235,.3);
        }
        .sa-hero::before {
            content:''; position:absolute; top:-3rem; right:-3rem;
            width:16rem; height:16rem; border-radius:50%;
            background:radial-gradient(circle, rgba(37,99,235,.25) 0%, transparent 70%);
            filter:blur(40px);
        }
        .sa-hero::after {
            content:''; position:absolute; bottom:-2rem; left:-2rem;
            width:12rem; height:12rem; border-radius:50%;
            background:radial-gradient(circle, rgba(30,58,138,.3) 0%, transparent 70%);
            filter:blur(30px);
        }
        .sa-hero-stars {
            position:absolute; inset:0; overflow:hidden; pointer-events:none;
        }
        .sa-hero-stars span {
            position:absolute; width:2px; height:2px; background:#fff; border-radius:50%;
            animation: sa-twinkle 3s infinite ease-in-out alternate;
        }
        .sa-hero-stars span:nth-child(1) { top:15%; left:10%; animation-delay:0s; }
        .sa-hero-stars span:nth-child(2) { top:25%; left:35%; animation-delay:.5s; }
        .sa-hero-stars span:nth-child(3) { top:10%; left:60%; animation-delay:1s; }
        .sa-hero-stars span:nth-child(4) { top:35%; left:80%; animation-delay:1.5s; }
        .sa-hero-stars span:nth-child(5) { top:20%; left:90%; animation-delay:2s; }
        .sa-hero-stars span:nth-child(6) { top:8%;  left:50%; animation-delay:.7s; }
        .sa-hero-stars span:nth-child(7) { top:30%; left:25%; animation-delay:1.2s; }
        @keyframes sa-twinkle { 0% { opacity:.2; transform:scale(.8); } 100% { opacity:1; transform:scale(1.2); } }

        .sa-hero-content { position:relative; z-index:2; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
        .sa-hero-left {}
        .sa-hero-left .sa-greet { font-size:.825rem; color:#93c5fd; display:flex; align-items:center; gap:.375rem; }
        .sa-hero-left h1 { margin-top:.375rem; font-size:1.75rem; font-weight:800; letter-spacing:-.025em; }
        .sa-hero-left .sa-sub { margin-top:.375rem; font-size:.825rem; color:rgba(147,197,253,.7); }

        .sa-hero-right { display:flex; align-items:center; gap:1rem; }
        .sa-ramadhan-card {
            display:flex; align-items:center; gap:.875rem;
            background:rgba(255,255,255,.07); backdrop-filter:blur(12px);
            border:1px solid rgba(255,255,255,.12); border-radius:1rem;
            padding:1rem 1.5rem;
        }
        .sa-moon { font-size:2.5rem; line-height:1; filter:drop-shadow(0 0 8px rgba(250,204,21,.4)); }
        .sa-ramadhan-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; color:#93c5fd; }
        .sa-ramadhan-day { font-size:2rem; font-weight:900; line-height:1.1; }
        .sa-ramadhan-year { font-size:.75rem; color:rgba(147,197,253,.7); margin-top:.125rem; }

        /* Stats Grid */
        .sa-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; }
        @media(max-width:1024px) { .sa-stats { grid-template-columns:repeat(2,1fr); } }
        @media(max-width:640px) { .sa-stats { grid-template-columns:1fr; } }
        .sa-stat {
            border-radius:1rem; padding:1.25rem; display:flex; align-items:center; gap:.875rem;
            border:1px solid rgba(100,100,100,.12); transition:all .2s;
        }
        .sa-stat:hover { transform:translateY(-2px); box-shadow:0 4px 12px -2px rgba(0,0,0,.1); }

        .sa-stat-icon {
            width:3rem; height:3rem; border-radius:.875rem;
            display:flex; align-items:center; justify-content:center; flex-shrink:0;
            background:rgba(37,99,235,.85);
        }
        .sa-stat-icon svg { width:1.25rem; height:1.25rem; color:rgba(255,255,255,.9); }
        .sa-stat-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
        .sa-stat-value { font-size:1.5rem; font-weight:700; }

        .sa-bg-blue { background:rgba(37,99,235,.06); border-color:rgba(37,99,235,.2); }

        .dark .sa-bg-blue { background:rgba(37,99,235,.12); border-color:rgba(37,99,235,.25); }
        .dark .sa-stat { border-color:rgba(100,100,100,.25); }

        .sa-text-muted { color:#6b7280; }
        .dark .sa-text-muted { color:#9ca3af; }

        /* Mid row — 3 columns, equal height */
        .sa-mid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; align-items:stretch; }
        .sa-mid > .sa-card { display:flex; flex-direction:column; }
        .sa-mid > .sa-card > .sa-card-body { flex:1; }
        @media(max-width:1024px) { .sa-mid { grid-template-columns:1fr; } }

        .sa-card {
            border-radius:1rem; overflow:hidden;
            border:1px solid rgba(100,100,100,.12);
        }
        .dark .sa-card { border-color:rgba(100,100,100,.25); }
        .sa-card-head {
            padding:1rem 1.25rem; display:flex; justify-content:space-between; align-items:center;
            border-bottom:1px solid rgba(100,100,100,.1);
            background:rgba(100,100,100,.03);
        }
        .dark .sa-card-head { background:rgba(0,0,0,.15); border-color:rgba(100,100,100,.18); }
        .sa-card-title { font-size:.875rem; font-weight:700; display:flex; align-items:center; gap:.5rem; }
        .sa-card-body { padding:1.25rem; }

        /* Today card */
        .sa-today-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .sa-today-item { text-align:center; padding:.75rem; border-radius:.75rem; }
        .sa-today-item-blue { background:rgba(37,99,235,.06); }
        .dark .sa-today-item-blue { background:rgba(37,99,235,.12); }
        .sa-today-num { font-size:1.75rem; font-weight:800; }
        .sa-today-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.04em; font-weight:600; margin-top:.25rem; }
        .sa-divider { border:none; border-top:1px solid rgba(100,100,100,.1); margin:1rem 0; }
        .dark .sa-divider { border-color:rgba(100,100,100,.2); }
        .sa-compliance {
            display:flex; align-items:center; gap:.75rem; padding:.75rem;
            border-radius:.75rem; background:rgba(100,100,100,.03);
        }
        .dark .sa-compliance { background:rgba(100,100,100,.08); }
        .sa-compliance-ring { position:relative; width:3.5rem; height:3.5rem; flex-shrink:0; }
        .sa-compliance-ring svg { width:3.5rem; height:3.5rem; transform:rotate(-90deg); }
        .sa-compliance-ring .sa-ring-bg { stroke:rgba(100,100,100,.12); }
        .dark .sa-compliance-ring .sa-ring-bg { stroke:rgba(100,100,100,.25); }
        .sa-compliance-pct {
            position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
            font-size:.7rem; font-weight:700;
        }
        .sa-compliance-info {}
        .sa-compliance-title { font-size:.75rem; font-weight:600; }
        .sa-compliance-desc { font-size:.7rem; margin-top:.125rem; }

        /* Formulir status card */
        .sa-form-item {
            display:flex; align-items:center; justify-content:space-between;
            padding:.625rem 0;
        }
        .sa-form-item + .sa-form-item { border-top:1px solid rgba(100,100,100,.08); }
        .dark .sa-form-item + .sa-form-item { border-color:rgba(100,100,100,.15); }
        .sa-form-dot { width:.5rem; height:.5rem; border-radius:50%; flex-shrink:0; }
        .sa-form-label { font-size:.8rem; flex:1; margin-left:.625rem; }
        .sa-form-count { font-size:.875rem; font-weight:700; }

        .sa-form-bar-wrap { height:.375rem; border-radius:.25rem; background:rgba(100,100,100,.08); margin-top:.625rem; overflow:hidden; }
        .dark .sa-form-bar-wrap { background:rgba(100,100,100,.18); }
        .sa-form-bar { height:100%; border-radius:.25rem; transition:width .5s ease; }

        /* System info card */
        .sa-sys-item {
            display:flex; align-items:center; justify-content:space-between;
            padding:.75rem; border-radius:.625rem;
        }
        .sa-sys-item + .sa-sys-item { margin-top:.375rem; }
        .sa-sys-item:nth-child(odd) { background:rgba(100,100,100,.03); }
        .dark .sa-sys-item:nth-child(odd) { background:rgba(100,100,100,.07); }
        .sa-sys-dot { width:.5rem; height:.5rem; border-radius:50%; flex-shrink:0; margin-right:.75rem; }
        .sa-sys-label { font-size:.8rem; flex:1; }
        .sa-sys-val { font-size:.875rem; font-weight:700; }

        /* Bottom section */
        .sa-bottom { display:grid; grid-template-columns:1.2fr 0.8fr; gap:1rem; }
        @media(max-width:1024px) { .sa-bottom { grid-template-columns:1fr; } }

        /* Table styles */
        .sa-table { width:100%; border-collapse:collapse; font-size:.8rem; }
        .sa-table th {
            text-align:left; padding:.625rem .875rem; font-weight:600; font-size:.7rem;
            text-transform:uppercase; letter-spacing:.05em;
            border-bottom:1px solid rgba(100,100,100,.1);
        }
        .dark .sa-table th { border-color:rgba(100,100,100,.2); }
        .sa-table td { padding:.625rem .875rem; border-bottom:1px solid rgba(100,100,100,.05); }
        .dark .sa-table td { border-color:rgba(100,100,100,.1); }
        .sa-table tr:hover td { background:rgba(100,100,100,.03); }
        .dark .sa-table tr:hover td { background:rgba(100,100,100,.06); }

        /* Badges */
        .sa-badge {
            display:inline-flex; align-items:center; padding:.2rem .5rem;
            border-radius:.375rem; font-size:.7rem; font-weight:600;
        }
        .sa-badge-hari {
            display:inline-flex; align-items:center; justify-content:center;
            width:1.75rem; height:1.75rem; border-radius:50%; font-size:.7rem; font-weight:700;
            background:rgba(37,99,235,.12); color:#2563eb;
        }
        .dark .sa-badge-hari { background:rgba(37,99,235,.2); color:#93c5fd; }
        .sa-badge-pending { background:rgba(245,158,11,.12); color:#d97706; }
        .sa-badge-verified { background:rgba(16,185,129,.12); color:#059669; }
        .sa-badge-rejected { background:rgba(239,68,68,.12); color:#dc2626; }
        .dark .sa-badge-pending { background:rgba(245,158,11,.18); color:#fbbf24; }
        .dark .sa-badge-verified { background:rgba(16,185,129,.18); color:#34d399; }
        .dark .sa-badge-rejected { background:rgba(239,68,68,.18); color:#fca5a5; }

        /* Kelas overview */
        .sa-kelas-item {
            display:flex; align-items:center; gap:.75rem;
            padding:.75rem; border-radius:.75rem;
        }
        .sa-kelas-item + .sa-kelas-item { margin-top:.375rem; }
        .sa-kelas-item:nth-child(odd) { background:rgba(100,100,100,.03); }
        .dark .sa-kelas-item:nth-child(odd) { background:rgba(100,100,100,.07); }
        .sa-kelas-info { flex:1; min-width:0; }
        .sa-kelas-name { font-size:.8rem; font-weight:600; }
        .sa-kelas-wali { font-size:.7rem; }
        .sa-kelas-counts { text-align:right; flex-shrink:0; }
        .sa-kelas-siswa { font-size:.8rem; font-weight:700; }
        .sa-kelas-rate { font-size:.675rem; }

        .sa-kelas-bar { height:.25rem; border-radius:.125rem; background:rgba(100,100,100,.08); margin-top:.375rem; overflow:hidden; }
        .dark .sa-kelas-bar { background:rgba(100,100,100,.18); }
        .sa-kelas-bar-fill { height:100%; border-radius:.125rem; background:rgba(37,99,235,.7); transition:width .5s ease; }

        /* Empty state */
        .sa-empty { text-align:center; padding:2.5rem 1rem; }
        .sa-empty-icon { font-size:2.5rem; }
        .sa-empty-text { margin-top:.5rem; font-size:.825rem; }

        /* Link */
        .sa-link { font-size:.75rem; font-weight:600; color:#2563eb; text-decoration:none; }
        .sa-link:hover { text-decoration:underline; }
        .dark .sa-link { color:#93c5fd; }
    </style>

    <div class="sa-wrap">

        {{-- ═══ Hero Banner ═══ --}}
        <div class="sa-hero">
            <div class="sa-hero-stars">
                <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
            <div class="sa-hero-content">
                <div class="sa-hero-left">
                    <p class="sa-greet">☪️ Assalamu'alaikum, Admin</p>
                    <h1>Buku Ramadhan SMKN 1</h1>
                    <p class="sa-sub">Panel Superadmin — Kelola seluruh data sekolah</p>
                </div>
                @if ($isRamadhan)
                    <div class="sa-ramadhan-card">
                        <span class="sa-moon">🌙</span>
                        <div>
                            <p class="sa-ramadhan-label">Ramadhan</p>
                            <p class="sa-ramadhan-day">Hari ke-{{ $hariKe }}</p>
                            <p class="sa-ramadhan-year">1447 Hijriah</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ═══ Stats Grid ═══ --}}
        <div class="sa-stats">
            <div class="sa-stat sa-bg-blue">
                <div class="sa-stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                </div>
                <div>
                    <p class="sa-stat-label sa-text-muted">Total Guru</p>
                    <p class="sa-stat-value">{{ $totalGuru }}</p>
                </div>
            </div>
            <div class="sa-stat sa-bg-blue">
                <div class="sa-stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                </div>
                <div>
                    <p class="sa-stat-label sa-text-muted">Total Siswa</p>
                    <p class="sa-stat-value">{{ $totalSiswa }}</p>
                </div>
            </div>
            <div class="sa-stat sa-bg-blue">
                <div class="sa-stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21"/></svg>
                </div>
                <div>
                    <p class="sa-stat-label sa-text-muted">Total Kelas</p>
                    <p class="sa-stat-value">{{ $totalKelas }}</p>
                </div>
            </div>
            <div class="sa-stat sa-bg-blue">
                <div class="sa-stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                </div>
                <div>
                    <p class="sa-stat-label sa-text-muted">Total Formulir</p>
                    <p class="sa-stat-value">{{ $totalFormulir }}</p>
                </div>
            </div>
        </div>

        {{-- ═══ Mid Row: Hari Ini | Status Formulir | Ringkasan ═══ --}}
        <div class="sa-mid">
            {{-- Hari Ini --}}
            <div class="sa-card">
                <div class="sa-card-head">
                    <span class="sa-card-title">Hari Ini</span>
                </div>
                <div class="sa-card-body">
                    <div class="sa-today-grid">
                        <div class="sa-today-item sa-today-item-blue">
                            <p class="sa-today-num" style="color:#2563eb;">{{ $formulirHariIni }}</p>
                            <p class="sa-today-label sa-text-muted">Formulir Masuk</p>
                        </div>
                        <div class="sa-today-item sa-today-item-blue">
                            <p class="sa-today-num" style="color:#2563eb;">{{ $siswaSubmitHariIni }}</p>
                            <p class="sa-today-label sa-text-muted">Siswa Submit</p>
                        </div>
                    </div>
                    <hr class="sa-divider">
                    <div class="sa-compliance">
                        <div class="sa-compliance-ring">
                            <svg viewBox="0 0 36 36">
                                <circle class="sa-ring-bg" cx="18" cy="18" r="14" fill="none" stroke-width="3.5"/>
                                <circle cx="18" cy="18" r="14" fill="none" stroke-width="3.5"
                                    stroke="{{ $complianceRate >= 70 ? '#10b981' : ($complianceRate >= 40 ? '#f59e0b' : '#ef4444') }}"
                                    stroke-dasharray="{{ $complianceRate * 0.88 }} 88"
                                    stroke-linecap="round"/>
                            </svg>
                            <span class="sa-compliance-pct">{{ $complianceRate }}%</span>
                        </div>
                        <div class="sa-compliance-info">
                            <p class="sa-compliance-title">Tingkat Kepatuhan</p>
                            <p class="sa-compliance-desc sa-text-muted">{{ $siswaSubmitHariIni }} dari {{ $totalSiswa }} siswa</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Formulir --}}
            <div class="sa-card">
                <div class="sa-card-head">
                    <span class="sa-card-title">Status Formulir</span>
                    <span style="font-size:.75rem; font-weight:700;">{{ $totalFormulir }}</span>
                </div>
                <div class="sa-card-body">
                    <div class="sa-form-item">
                        <span class="sa-form-dot" style="background:#f59e0b;"></span>
                        <span class="sa-form-label">Menunggu</span>
                        <span class="sa-form-count" style="color:#d97706;">{{ $totalPending }}</span>
                    </div>
                    <div class="sa-form-item">
                        <span class="sa-form-dot" style="background:#10b981;"></span>
                        <span class="sa-form-label">Diverifikasi</span>
                        <span class="sa-form-count" style="color:#059669;">{{ $totalVerified }}</span>
                    </div>
                    <div class="sa-form-item">
                        <span class="sa-form-dot" style="background:#ef4444;"></span>
                        <span class="sa-form-label">Ditolak</span>
                        <span class="sa-form-count" style="color:#dc2626;">{{ $totalRejected }}</span>
                    </div>
                    @if ($totalFormulir > 0)
                        <div class="sa-form-bar-wrap" style="margin-top:1rem;">
                            <div style="display:flex; height:.375rem; border-radius:.25rem; overflow:hidden;">
                                @if ($totalVerified > 0)
                                    <div style="width:{{ ($totalVerified / $totalFormulir) * 100 }}%; background:#10b981;"></div>
                                @endif
                                @if ($totalPending > 0)
                                    <div style="width:{{ ($totalPending / $totalFormulir) * 100 }}%; background:#f59e0b;"></div>
                                @endif
                                @if ($totalRejected > 0)
                                    <div style="width:{{ ($totalRejected / $totalFormulir) * 100 }}%; background:#ef4444;"></div>
                                @endif
                            </div>
                        </div>
                    @endif
                    <hr class="sa-divider">
                    <div class="sa-compliance">
                        <div class="sa-compliance-ring">
                            <svg viewBox="0 0 36 36">
                                <circle class="sa-ring-bg" cx="18" cy="18" r="14" fill="none" stroke-width="3.5"/>
                                <circle cx="18" cy="18" r="14" fill="none" stroke-width="3.5"
                                    stroke="#10b981"
                                    stroke-dasharray="{{ $verifyRate * 0.88 }} 88"
                                    stroke-linecap="round"/>
                            </svg>
                            <span class="sa-compliance-pct">{{ $verifyRate }}%</span>
                        </div>
                        <div class="sa-compliance-info">
                            <p class="sa-compliance-title">Tingkat Verifikasi</p>
                            <p class="sa-compliance-desc sa-text-muted">{{ $totalVerified }} dari {{ $totalFormulir }} formulir</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ringkasan Sistem --}}
            <div class="sa-card">
                <div class="sa-card-head">
                    <span class="sa-card-title">Ringkasan Sistem</span>
                </div>
                <div class="sa-card-body" style="padding:.875rem 1.25rem;">
                    <div class="sa-sys-item">
                        <span class="sa-sys-dot" style="background:#2563eb;"></span>
                        <span class="sa-sys-label">Role Terdaftar</span>
                        <span class="sa-sys-val">{{ $totalRole }}</span>
                    </div>
                    <div class="sa-sys-item">
                        <span class="sa-sys-dot" style="background:#059669;"></span>
                        <span class="sa-sys-label">Guru Aktif</span>
                        <span class="sa-sys-val">{{ $totalGuru }}</span>
                    </div>
                    <div class="sa-sys-item">
                        <span class="sa-sys-dot" style="background:#0284c7;"></span>
                        <span class="sa-sys-label">Siswa Aktif</span>
                        <span class="sa-sys-val">{{ $totalSiswa }}</span>
                    </div>
                    <div class="sa-sys-item">
                        <span class="sa-sys-dot" style="background:#d97706;"></span>
                        <span class="sa-sys-label">Kelas Terdaftar</span>
                        <span class="sa-sys-val">{{ $totalKelas }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ Bottom: Aktivitas Terbaru & Overview Kelas ═══ --}}
        <div class="sa-bottom">
            {{-- Aktivitas Terbaru --}}
            <div class="sa-card">
                <div class="sa-card-head">
                    <span class="sa-card-title">Aktivitas Terbaru</span>
                    <a href="{{ url('/portal-admin-smkn1/log-formulir') }}" class="sa-link">Lihat Semua →</a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="sa-table">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th style="text-align:center;">Hari</th>
                                <th style="text-align:center;">Status</th>
                                <th style="text-align:right;">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentSubmissions as $sub)
                                <tr>
                                    <td style="font-weight:500;">{{ $sub['user_name'] }}</td>
                                    <td class="sa-text-muted">{{ $sub['user_kelas'] }}</td>
                                    <td style="text-align:center;"><span class="sa-badge-hari">{{ $sub['hari_ke'] }}</span></td>
                                    <td style="text-align:center;">
                                        @if ($sub['status'] === 'pending')
                                            <span class="sa-badge sa-badge-pending">Menunggu</span>
                                        @elseif ($sub['status'] === 'verified')
                                            <span class="sa-badge sa-badge-verified">Diverifikasi</span>
                                        @else
                                            <span class="sa-badge sa-badge-rejected">Ditolak</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right;" class="sa-text-muted" title="{{ $sub['created_at_full'] }}">{{ $sub['created_at'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="sa-empty">
                                            <div class="sa-empty-icon">📭</div>
                                            <p class="sa-empty-text sa-text-muted">Belum ada formulir yang masuk</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Overview Kelas --}}
            <div class="sa-card">
                <div class="sa-card-head">
                    <span class="sa-card-title">Overview Kelas</span>
                    <a href="{{ url('/portal-admin-smkn1/kelas') }}" class="sa-link">Kelola →</a>
                </div>
                <div class="sa-card-body" style="padding:.875rem 1.25rem; max-height:24rem; overflow-y:auto;">
                    @forelse ($kelasOverview as $k)
                        <div class="sa-kelas-item">
                            <div class="sa-kelas-info">
                                <p class="sa-kelas-name">{{ $k['nama'] }}</p>
                                <p class="sa-kelas-wali sa-text-muted">{{ $k['wali'] }}</p>
                            </div>
                            <div class="sa-kelas-counts">
                                <p class="sa-kelas-siswa">{{ $k['siswa_count'] }}<span style="font-size:.7rem; font-weight:400;"> siswa</span></p>
                                @if ($isRamadhan)
                                    <p class="sa-kelas-rate" style="color:{{ $k['rate'] >= 70 ? '#059669' : ($k['rate'] >= 40 ? '#d97706' : '#dc2626') }};">
                                        {{ $k['rate'] }}% hari ini
                                    </p>
                                @endif
                            </div>
                        </div>
                        @if ($isRamadhan)
                            <div class="sa-kelas-bar">
                                <div class="sa-kelas-bar-fill" style="width:{{ $k['rate'] }}%; background:{{ $k['rate'] >= 70 ? '#10b981' : ($k['rate'] >= 40 ? '#f59e0b' : '#ef4444') }};"></div>
                            </div>
                        @endif
                    @empty
                        <div class="sa-empty">
                            <div class="sa-empty-icon">🏫</div>
                            <p class="sa-empty-text sa-text-muted">Belum ada kelas terdaftar</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>
