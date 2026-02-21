<x-filament-panels::page>
    <style>
        /* ─── Kesiswaan Dashboard ─── */
        .ks-wrap { display:flex; flex-direction:column; gap:1.5rem; }

        /* Hero Banner */
        .ks-hero {
            position:relative; overflow:hidden; border-radius:1rem;
            background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 35%, #2563eb 100%);
            padding:2rem; color:#fff; box-shadow:0 10px 25px -5px rgba(37,99,235,.3);
        }
        .ks-hero::before {
            content:''; position:absolute; top:-3rem; right:-3rem;
            width:16rem; height:16rem; border-radius:50%;
            background:radial-gradient(circle, rgba(37,99,235,.25) 0%, transparent 70%);
            filter:blur(40px);
        }
        .ks-hero::after {
            content:''; position:absolute; bottom:-2rem; left:-2rem;
            width:12rem; height:12rem; border-radius:50%;
            background:radial-gradient(circle, rgba(30,58,138,.3) 0%, transparent 70%);
            filter:blur(30px);
        }
        .ks-hero-stars { position:absolute; inset:0; overflow:hidden; pointer-events:none; }
        .ks-hero-stars span {
            position:absolute; width:2px; height:2px; background:#fff; border-radius:50%;
            animation: ks-twinkle 3s infinite ease-in-out alternate;
        }
        .ks-hero-stars span:nth-child(1) { top:15%; left:10%; animation-delay:0s; }
        .ks-hero-stars span:nth-child(2) { top:25%; left:35%; animation-delay:.5s; }
        .ks-hero-stars span:nth-child(3) { top:10%; left:60%; animation-delay:1s; }
        .ks-hero-stars span:nth-child(4) { top:35%; left:80%; animation-delay:1.5s; }
        .ks-hero-stars span:nth-child(5) { top:20%; left:90%; animation-delay:2s; }
        .ks-hero-stars span:nth-child(6) { top:8%;  left:50%; animation-delay:.7s; }
        .ks-hero-stars span:nth-child(7) { top:30%; left:25%; animation-delay:1.2s; }
        @keyframes ks-twinkle { 0% { opacity:.2; transform:scale(.8); } 100% { opacity:1; transform:scale(1.2); } }

        .ks-hero-content { position:relative; z-index:2; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
        .ks-hero-left .ks-greet { font-size:.825rem; color:#93c5fd; display:flex; align-items:center; gap:.375rem; }
        .ks-hero-left h1 { margin-top:.375rem; font-size:1.75rem; font-weight:800; letter-spacing:-.025em; }
        .ks-hero-left .ks-sub { margin-top:.375rem; font-size:.825rem; color:rgba(147,197,253,.7); }

        .ks-hero-right { display:flex; align-items:center; gap:1rem; }
        .ks-ramadhan-card {
            display:flex; align-items:center; gap:.875rem;
            background:rgba(255,255,255,.07); backdrop-filter:blur(12px);
            border:1px solid rgba(255,255,255,.12); border-radius:1rem;
            padding:1rem 1.5rem;
        }
        .ks-moon { font-size:2.5rem; line-height:1; filter:drop-shadow(0 0 8px rgba(250,204,21,.4)); }
        .ks-ramadhan-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; color:#93c5fd; }
        .ks-ramadhan-day { font-size:2rem; font-weight:900; line-height:1.1; }
        .ks-ramadhan-year { font-size:.75rem; color:rgba(147,197,253,.7); margin-top:.125rem; }

        /* Stats Grid */
        .ks-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; }
        @media(max-width:1024px) { .ks-stats { grid-template-columns:repeat(2,1fr); } }
        @media(max-width:640px) { .ks-stats { grid-template-columns:1fr; } }
        .ks-stat {
            border-radius:1rem; padding:1.25rem; display:flex; align-items:center; gap:.875rem;
            border:1px solid rgba(100,100,100,.12); transition:all .2s;
        }
        .ks-stat:hover { transform:translateY(-2px); box-shadow:0 4px 12px -2px rgba(0,0,0,.1); }
        .ks-stat-icon {
            width:3rem; height:3rem; border-radius:.875rem;
            display:flex; align-items:center; justify-content:center; flex-shrink:0;
        }
        .ks-stat-icon svg { width:1.25rem; height:1.25rem; color:rgba(255,255,255,.9); }
        .ks-stat-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
        .ks-stat-value { font-size:1.5rem; font-weight:700; }

        .ks-bg-blue { background:rgba(37,99,235,.06); border-color:rgba(37,99,235,.2); }
        .ks-icon-blue { background:rgba(37,99,235,.85); }
        .ks-icon-green { background:rgba(16,185,129,.85); }
        .ks-icon-yellow { background:rgba(245,158,11,.85); }
        .ks-icon-red { background:rgba(239,68,68,.85); }

        .dark .ks-bg-blue { background:rgba(37,99,235,.12); border-color:rgba(37,99,235,.25); }
        .dark .ks-stat { border-color:rgba(100,100,100,.25); }

        .ks-text-muted { color:#6b7280; }
        .dark .ks-text-muted { color:#9ca3af; }

        /* Mid row — 3 columns */
        .ks-mid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; align-items:stretch; }
        .ks-mid > .ks-card { display:flex; flex-direction:column; }
        .ks-mid > .ks-card > .ks-card-body { flex:1; }
        @media(max-width:1024px) { .ks-mid { grid-template-columns:1fr; } }

        .ks-card {
            border-radius:1rem; overflow:hidden;
            border:1px solid rgba(100,100,100,.12);
        }
        .dark .ks-card { border-color:rgba(100,100,100,.25); }
        .ks-card-head {
            padding:1rem 1.25rem; display:flex; justify-content:space-between; align-items:center;
            border-bottom:1px solid rgba(100,100,100,.1);
            background:rgba(100,100,100,.03);
        }
        .dark .ks-card-head { background:rgba(0,0,0,.15); border-color:rgba(100,100,100,.18); }
        .ks-card-title { font-size:.875rem; font-weight:700; display:flex; align-items:center; gap:.5rem; }
        .ks-card-body { padding:1.25rem; }

        /* Today card */
        .ks-today-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .ks-today-item { text-align:center; padding:.75rem; border-radius:.75rem; }
        .ks-today-item-blue { background:rgba(37,99,235,.06); }
        .dark .ks-today-item-blue { background:rgba(37,99,235,.12); }
        .ks-today-num { font-size:1.75rem; font-weight:800; }
        .ks-today-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.04em; font-weight:600; margin-top:.25rem; }
        .ks-divider { border:none; border-top:1px solid rgba(100,100,100,.1); margin:1rem 0; }
        .dark .ks-divider { border-color:rgba(100,100,100,.2); }
        .ks-compliance {
            display:flex; align-items:center; gap:.75rem; padding:.75rem;
            border-radius:.75rem; background:rgba(100,100,100,.03);
        }
        .dark .ks-compliance { background:rgba(100,100,100,.08); }
        .ks-compliance-ring { position:relative; width:3.5rem; height:3.5rem; flex-shrink:0; }
        .ks-compliance-ring svg { width:3.5rem; height:3.5rem; transform:rotate(-90deg); }
        .ks-compliance-ring .ks-ring-bg { stroke:rgba(100,100,100,.12); }
        .dark .ks-compliance-ring .ks-ring-bg { stroke:rgba(100,100,100,.25); }
        .ks-compliance-pct {
            position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
            font-size:.7rem; font-weight:700;
        }

        /* Form status */
        .ks-form-item {
            display:flex; align-items:center; justify-content:space-between; padding:.625rem 0;
        }
        .ks-form-item + .ks-form-item { border-top:1px solid rgba(100,100,100,.08); }
        .dark .ks-form-item + .ks-form-item { border-color:rgba(100,100,100,.15); }
        .ks-form-dot { width:.5rem; height:.5rem; border-radius:50%; flex-shrink:0; }
        .ks-form-label { font-size:.8rem; flex:1; margin-left:.625rem; }
        .ks-form-count { font-size:.875rem; font-weight:700; }

        /* Bottom section */
        .ks-bottom { display:grid; grid-template-columns:1.2fr 0.8fr; gap:1rem; }
        @media(max-width:1024px) { .ks-bottom { grid-template-columns:1fr; } }

        /* Table styles */
        .ks-table { width:100%; border-collapse:collapse; font-size:.8rem; }
        .ks-table th {
            text-align:left; padding:.625rem .875rem; font-weight:600; font-size:.7rem;
            text-transform:uppercase; letter-spacing:.05em;
            border-bottom:1px solid rgba(100,100,100,.1);
        }
        .dark .ks-table th { border-color:rgba(100,100,100,.2); }
        .ks-table td { padding:.625rem .875rem; border-bottom:1px solid rgba(100,100,100,.05); }
        .dark .ks-table td { border-color:rgba(100,100,100,.1); }
        .ks-table tr:hover td { background:rgba(100,100,100,.03); }
        .dark .ks-table tr:hover td { background:rgba(100,100,100,.06); }

        /* Badges */
        .ks-badge {
            display:inline-flex; align-items:center; padding:.2rem .5rem;
            border-radius:.375rem; font-size:.7rem; font-weight:600;
        }
        .ks-badge-hari {
            display:inline-flex; align-items:center; justify-content:center;
            width:1.75rem; height:1.75rem; border-radius:50%; font-size:.7rem; font-weight:700;
            background:rgba(37,99,235,.12); color:#2563eb;
        }
        .dark .ks-badge-hari { background:rgba(37,99,235,.2); color:#93c5fd; }
        .ks-badge-pending { background:rgba(245,158,11,.12); color:#d97706; }
        .ks-badge-verified { background:rgba(16,185,129,.12); color:#059669; }
        .ks-badge-rejected { background:rgba(239,68,68,.12); color:#dc2626; }
        .dark .ks-badge-pending { background:rgba(245,158,11,.18); color:#fbbf24; }
        .dark .ks-badge-verified { background:rgba(16,185,129,.18); color:#34d399; }
        .dark .ks-badge-rejected { background:rgba(239,68,68,.18); color:#fca5a5; }

        /* Kelas overview */
        .ks-kelas-item {
            display:flex; align-items:center; gap:.75rem;
            padding:.75rem; border-radius:.75rem;
        }
        .ks-kelas-item + .ks-kelas-item { margin-top:.375rem; }
        .ks-kelas-item:nth-child(odd) { background:rgba(100,100,100,.03); }
        .dark .ks-kelas-item:nth-child(odd) { background:rgba(100,100,100,.07); }
        .ks-kelas-info { flex:1; min-width:0; }
        .ks-kelas-name { font-size:.8rem; font-weight:600; }
        .ks-kelas-wali { font-size:.7rem; }
        .ks-kelas-counts { text-align:right; flex-shrink:0; }
        .ks-kelas-siswa { font-size:.8rem; font-weight:700; }
        .ks-kelas-rate { font-size:.675rem; }

        .ks-kelas-bar { height:.25rem; border-radius:.125rem; background:rgba(100,100,100,.08); margin-top:.375rem; overflow:hidden; }
        .dark .ks-kelas-bar { background:rgba(100,100,100,.18); }
        .ks-kelas-bar-fill { height:100%; border-radius:.125rem; transition:width .5s ease; }

        /* Guru pending */
        .ks-guru-item {
            display:flex; align-items:center; justify-content:space-between;
            padding:.75rem; border-radius:.625rem;
        }
        .ks-guru-item + .ks-guru-item { margin-top:.375rem; }
        .ks-guru-item:nth-child(odd) { background:rgba(100,100,100,.03); }
        .dark .ks-guru-item:nth-child(odd) { background:rgba(100,100,100,.07); }
        .ks-guru-name { font-size:.8rem; font-weight:600; }
        .ks-guru-kelas { font-size:.7rem; }
        .ks-guru-pending-count {
            display:inline-flex; align-items:center; justify-content:center;
            min-width:2rem; padding:.25rem .625rem; border-radius:.5rem;
            font-size:.75rem; font-weight:700;
            background:rgba(245,158,11,.12); color:#d97706;
        }
        .dark .ks-guru-pending-count { background:rgba(245,158,11,.18); color:#fbbf24; }

        /* Empty state */
        .ks-empty { text-align:center; padding:2.5rem 1rem; }
        .ks-empty-icon { font-size:2.5rem; }
        .ks-empty-text { margin-top:.5rem; font-size:.825rem; }

        /* Link */
        .ks-link { font-size:.75rem; font-weight:600; color:#2563eb; text-decoration:none; }
        .ks-link:hover { text-decoration:underline; }
        .dark .ks-link { color:#93c5fd; }
    </style>

    <div class="ks-wrap">

        {{-- ═══ Hero Banner ═══ --}}
        <div class="ks-hero">
            <div class="ks-hero-stars">
                <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
            <div class="ks-hero-content">
                <div class="ks-hero-left">
                    <p class="ks-greet">☪️ Assalamu'alaikum, Kesiswaan</p>
                    <h1>Buku Ramadhan SMKN 1</h1>
                    <p class="ks-sub">Panel Kesiswaan — Validasi & monitoring data siswa</p>
                </div>
                @if ($isRamadhan)
                    <div class="ks-hero-right">
                        <div class="ks-ramadhan-card">
                            <span class="ks-moon">🌙</span>
                            <div>
                                <p class="ks-ramadhan-label">Ramadhan</p>
                                <p class="ks-ramadhan-day">Hari ke-{{ $hariKe }}</p>
                                <p class="ks-ramadhan-year">{{ $hijriDate }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ═══ Stats Grid ═══ --}}
        <div class="ks-stats">
            <div class="ks-stat ks-bg-blue">
                <div class="ks-stat-icon ks-icon-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                </div>
                <div>
                    <p class="ks-stat-label ks-text-muted">Total Siswa</p>
                    <p class="ks-stat-value">{{ $totalSiswa }}</p>
                </div>
            </div>
            <div class="ks-stat ks-bg-blue">
                <div class="ks-stat-icon ks-icon-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                </div>
                <div>
                    <p class="ks-stat-label ks-text-muted">Total Guru</p>
                    <p class="ks-stat-value">{{ $totalGuru }}</p>
                </div>
            </div>
            <div class="ks-stat ks-bg-blue">
                <div class="ks-stat-icon ks-icon-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21"/></svg>
                </div>
                <div>
                    <p class="ks-stat-label ks-text-muted">Total Kelas</p>
                    <p class="ks-stat-value">{{ $totalKelas }}</p>
                </div>
            </div>
            <div class="ks-stat ks-bg-blue">
                <div class="ks-stat-icon ks-icon-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                </div>
                <div>
                    <p class="ks-stat-label ks-text-muted">Total Formulir</p>
                    <p class="ks-stat-value">{{ $totalFormulir }}</p>
                </div>
            </div>
        </div>

        {{-- ═══ Mid Row: Hari Ini | Status Formulir | Verifikasi Rate ═══ --}}
        <div class="ks-mid">
            {{-- Hari Ini --}}
            <div class="ks-card">
                <div class="ks-card-head">
                    <span class="ks-card-title">📊 Hari Ini</span>
                </div>
                <div class="ks-card-body">
                    <div class="ks-today-grid">
                        <div class="ks-today-item ks-today-item-blue">
                            <p class="ks-today-num" style="color:#2563eb;">{{ $siswaSubmitToday }}</p>
                            <p class="ks-today-label ks-text-muted">Siswa Submit</p>
                        </div>
                        <div class="ks-today-item ks-today-item-blue">
                            <p class="ks-today-num" style="color:#d97706;">{{ $belumSubmitToday }}</p>
                            <p class="ks-today-label ks-text-muted">Belum Submit</p>
                        </div>
                    </div>
                    <hr class="ks-divider">
                    <div class="ks-compliance">
                        <div class="ks-compliance-ring">
                            <svg viewBox="0 0 36 36">
                                <circle class="ks-ring-bg" cx="18" cy="18" r="14" fill="none" stroke-width="3.5"/>
                                <circle cx="18" cy="18" r="14" fill="none" stroke-width="3.5"
                                    stroke="{{ $complianceRate >= 70 ? '#10b981' : ($complianceRate >= 40 ? '#f59e0b' : '#ef4444') }}"
                                    stroke-dasharray="{{ $complianceRate * 0.88 }} 88"
                                    stroke-linecap="round"/>
                            </svg>
                            <span class="ks-compliance-pct">{{ $complianceRate }}%</span>
                        </div>
                        <div>
                            <p style="font-size:.75rem; font-weight:600;">Tingkat Kepatuhan</p>
                            <p style="font-size:.7rem; margin-top:.125rem;" class="ks-text-muted">{{ $siswaSubmitToday }} dari {{ $totalSiswa }} siswa</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Formulir --}}
            <div class="ks-card">
                <div class="ks-card-head">
                    <span class="ks-card-title">📋 Status Formulir</span>
                    <span style="font-size:.75rem; font-weight:700;">{{ $totalFormulir }}</span>
                </div>
                <div class="ks-card-body">
                    <div class="ks-form-item">
                        <span class="ks-form-dot" style="background:#f59e0b;"></span>
                        <span class="ks-form-label">Menunggu Validasi</span>
                        <span class="ks-form-count" style="color:#d97706;">{{ $totalPending }}</span>
                    </div>
                    <div class="ks-form-item">
                        <span class="ks-form-dot" style="background:#10b981;"></span>
                        <span class="ks-form-label">Sudah Diverifikasi</span>
                        <span class="ks-form-count" style="color:#059669;">{{ $totalVerified }}</span>
                    </div>
                    <div class="ks-form-item">
                        <span class="ks-form-dot" style="background:#ef4444;"></span>
                        <span class="ks-form-label">Ditolak</span>
                        <span class="ks-form-count" style="color:#dc2626;">{{ $totalRejected }}</span>
                    </div>
                    @if ($totalFormulir > 0)
                        <div style="margin-top:1rem;">
                            <div style="display:flex; height:.375rem; border-radius:.25rem; overflow:hidden; background:rgba(100,100,100,.08);">
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
                    <hr class="ks-divider">
                    <div class="ks-compliance">
                        <div class="ks-compliance-ring">
                            <svg viewBox="0 0 36 36">
                                <circle class="ks-ring-bg" cx="18" cy="18" r="14" fill="none" stroke-width="3.5"/>
                                <circle cx="18" cy="18" r="14" fill="none" stroke-width="3.5"
                                    stroke="#10b981"
                                    stroke-dasharray="{{ $verifyRate * 0.88 }} 88"
                                    stroke-linecap="round"/>
                            </svg>
                            <span class="ks-compliance-pct">{{ $verifyRate }}%</span>
                        </div>
                        <div>
                            <p style="font-size:.75rem; font-weight:600;">Tingkat Verifikasi</p>
                            <p style="font-size:.7rem; margin-top:.125rem;" class="ks-text-muted">{{ $totalVerified }} dari {{ $totalFormulir }} formulir</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="ks-card">
                <div class="ks-card-head">
                    <span class="ks-card-title">⚡ Aksi Cepat</span>
                </div>
                <div class="ks-card-body" style="display:flex; flex-direction:column; gap:.75rem;">
                    <a href="{{ url('/portal-kesiswaan-smkn1/validasi-formulir') }}" style="display:flex; align-items:center; gap:.75rem; padding:.875rem; border-radius:.75rem; background:rgba(245,158,11,.06); border:1px solid rgba(245,158,11,.15); text-decoration:none; color:inherit; transition:all .2s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='none'">
                        <span style="font-size:1.25rem;">📝</span>
                        <div>
                            <p style="font-size:.8rem; font-weight:600;">Validasi Formulir</p>
                            <p style="font-size:.7rem;" class="ks-text-muted">{{ $totalPending }} menunggu validasi</p>
                        </div>
                    </a>
                    <a href="{{ url('/portal-kesiswaan-smkn1/data-siswa') }}" style="display:flex; align-items:center; gap:.75rem; padding:.875rem; border-radius:.75rem; background:rgba(37,99,235,.06); border:1px solid rgba(37,99,235,.15); text-decoration:none; color:inherit; transition:all .2s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='none'">
                        <span style="font-size:1.25rem;">👨‍🎓</span>
                        <div>
                            <p style="font-size:.8rem; font-weight:600;">Data Siswa</p>
                            <p style="font-size:.7rem;" class="ks-text-muted">{{ $totalSiswa }} siswa terdaftar</p>
                        </div>
                    </a>
                    <a href="{{ url('/portal-kesiswaan-smkn1/rekap-kelas') }}" style="display:flex; align-items:center; gap:.75rem; padding:.875rem; border-radius:.75rem; background:rgba(16,185,129,.06); border:1px solid rgba(16,185,129,.15); text-decoration:none; color:inherit; transition:all .2s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='none'">
                        <span style="font-size:1.25rem;">📊</span>
                        <div>
                            <p style="font-size:.8rem; font-weight:600;">Rekap Per Kelas</p>
                            <p style="font-size:.7rem;" class="ks-text-muted">{{ $totalKelas }} kelas terdaftar</p>
                        </div>
                    </a>
                    <a href="{{ url('/portal-kesiswaan-smkn1/data-guru') }}" style="display:flex; align-items:center; gap:.75rem; padding:.875rem; border-radius:.75rem; background:rgba(139,92,246,.06); border:1px solid rgba(139,92,246,.15); text-decoration:none; color:inherit; transition:all .2s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='none'">
                        <span style="font-size:1.25rem;">👩‍🏫</span>
                        <div>
                            <p style="font-size:.8rem; font-weight:600;">Data Guru</p>
                            <p style="font-size:.7rem;" class="ks-text-muted">{{ $totalGuru }} guru terdaftar</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- ═══ Bottom: Verifikasi Terbaru & Guru Pending & Overview Kelas ═══ --}}
        <div class="ks-bottom">
            {{-- Aktivitas Verifikasi Terbaru --}}
            <div class="ks-card">
                <div class="ks-card-head">
                    <span class="ks-card-title">🕐 Verifikasi Terbaru</span>
                    <a href="{{ url('/portal-kesiswaan-smkn1/validasi-formulir') }}" class="ks-link">Lihat Semua →</a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="ks-table">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th style="text-align:center;">Hari</th>
                                <th style="text-align:center;">Status</th>
                                <th>Verifikator</th>
                                <th style="text-align:right;">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentVerified as $sub)
                                <tr>
                                    <td style="font-weight:500;">{{ $sub['user_name'] }}</td>
                                    <td class="ks-text-muted">{{ $sub['user_kelas'] }}</td>
                                    <td style="text-align:center;"><span class="ks-badge-hari">{{ $sub['hari_ke'] }}</span></td>
                                    <td style="text-align:center;">
                                        @if ($sub['status'] === 'verified')
                                            <span class="ks-badge ks-badge-verified">Diverifikasi</span>
                                        @else
                                            <span class="ks-badge ks-badge-rejected">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="ks-text-muted">{{ $sub['verifier'] }}</td>
                                    <td style="text-align:right;" class="ks-text-muted" title="{{ $sub['verified_at_full'] }}">{{ $sub['verified_at'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="ks-empty">
                                            <div class="ks-empty-icon">📭</div>
                                            <p class="ks-empty-text ks-text-muted">Belum ada formulir yang diverifikasi</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Guru Belum Verifikasi & Overview Kelas --}}
            <div style="display:flex; flex-direction:column; gap:1rem;">
                {{-- Guru Pending --}}
                <div class="ks-card">
                    <div class="ks-card-head">
                        <span class="ks-card-title">⚠️ Guru — Pending Terbanyak</span>
                        <a href="{{ url('/portal-kesiswaan-smkn1/data-guru') }}" class="ks-link">Lihat →</a>
                    </div>
                    <div class="ks-card-body" style="padding:.875rem 1.25rem; max-height:14rem; overflow-y:auto;">
                        @forelse ($guruPending as $g)
                            <div class="ks-guru-item">
                                <div>
                                    <p class="ks-guru-name">{{ $g['guru'] }}</p>
                                    <p class="ks-guru-kelas ks-text-muted">{{ $g['kelas'] }}</p>
                                </div>
                                <span class="ks-guru-pending-count">{{ $g['pending'] }} pending</span>
                            </div>
                        @empty
                            <div class="ks-empty">
                                <div class="ks-empty-icon">✅</div>
                                <p class="ks-empty-text ks-text-muted">Semua guru sudah memverifikasi</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Overview Kelas --}}
                <div class="ks-card">
                    <div class="ks-card-head">
                        <span class="ks-card-title">🏫 Overview Kelas</span>
                        <a href="{{ url('/portal-kesiswaan-smkn1/rekap-kelas') }}" class="ks-link">Detail →</a>
                    </div>
                    <div class="ks-card-body" style="padding:.875rem 1.25rem; max-height:16rem; overflow-y:auto;">
                        @forelse ($kelasOverview as $k)
                            <div class="ks-kelas-item">
                                <div class="ks-kelas-info">
                                    <p class="ks-kelas-name">{{ $k['nama'] }}</p>
                                    <p class="ks-kelas-wali ks-text-muted">{{ $k['wali'] }}</p>
                                </div>
                                <div class="ks-kelas-counts">
                                    <p class="ks-kelas-siswa">{{ $k['siswa_count'] }} <span style="font-size:.7rem; font-weight:400;">siswa</span></p>
                                    @if ($isRamadhan)
                                        <p class="ks-kelas-rate" style="color:{{ $k['today_rate'] >= 70 ? '#059669' : ($k['today_rate'] >= 40 ? '#d97706' : '#dc2626') }};">
                                            {{ $k['today_rate'] }}% hari ini
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @if ($isRamadhan)
                                <div class="ks-kelas-bar">
                                    <div class="ks-kelas-bar-fill" style="width:{{ $k['today_rate'] }}%; background:{{ $k['today_rate'] >= 70 ? '#10b981' : ($k['today_rate'] >= 40 ? '#f59e0b' : '#ef4444') }};"></div>
                                </div>
                            @endif
                        @empty
                            <div class="ks-empty">
                                <div class="ks-empty-icon">🏫</div>
                                <p class="ks-empty-text ks-text-muted">Belum ada kelas terdaftar</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>
