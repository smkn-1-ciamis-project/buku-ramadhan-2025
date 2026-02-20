<x-filament-panels::page>
    <style>
        /* ─── Dashboard Layout ─── */
        .gd-wrap { display: flex; flex-direction: column; gap: 1.5rem; }

        /* Hero */
        .gd-hero {
            position: relative; overflow: hidden; border-radius: 1rem;
            background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 35%, #2563eb 100%);
            padding: 2rem; color: #fff; box-shadow: 0 10px 15px -3px rgba(0,0,0,.25);
        }
        .gd-hero-blur1 { position:absolute; right:-2.5rem; top:-2.5rem; width:10rem; height:10rem; border-radius:50%; background:rgba(37,99,235,.2); filter:blur(50px); }
        .gd-hero-blur2 { position:absolute; left:-1.5rem; bottom:-1.5rem; width:8rem; height:8rem; border-radius:50%; background:rgba(15,23,42,.3); filter:blur(30px); }
        .gd-hero-content { position:relative; z-index:2; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
        .gd-hero-left {}
        .gd-hero-left .gd-greet { font-size:.875rem; color:#93c5fd; }
        .gd-hero-left h1 { margin-top:.25rem; font-size:1.875rem; font-weight:800; letter-spacing:-.025em; }
        .gd-hero-left .gd-sub { margin-top:.5rem; font-size:.875rem; color:rgba(147,197,253,.7); }
        .gd-hero-day {
            display:flex; align-items:center; gap:.75rem;
            background:rgba(255,255,255,.08); backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,.15); border-radius:1rem;
            padding:1rem 1.5rem; text-align:left;
        }
        .gd-hero-day .gd-day-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.05em; color:#93c5fd; }
        .gd-hero-day .gd-day-num { font-size:2.25rem; font-weight:900; line-height:1; }

        /* Stats */
        .gd-stats { display:grid; grid-template-columns: repeat(4,1fr); gap:1rem; }
        @media(max-width:768px) { .gd-stats { grid-template-columns: repeat(2,1fr); } }
        .gd-stat-card {
            border-radius:1rem; padding:1.25rem;
            border:1px solid rgba(100,100,100,.15);
            display:flex; align-items:center; gap:.75rem;
            transition: box-shadow .2s;
        }
        .gd-stat-card:hover { box-shadow:0 4px 6px -1px rgba(0,0,0,.1); }
        .gd-stat-icon {
            width:3rem; height:3rem; border-radius:.75rem;
            display:flex; align-items:center; justify-content:center;
            flex-shrink:0; color:rgba(255,255,255,.85);
        }
        .gd-stat-icon svg { width:1.25rem; height:1.25rem; }
        .gd-stat-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
        .gd-stat-value { font-size:1.5rem; font-weight:700; }
        .gd-stat-sub { font-size:.8rem; font-weight:400; }

        /* Card backgrounds — blue accent matching siswa theme */
        .gd-bg-default { background:rgba(37,99,235,.06); border-color:rgba(37,99,235,.2); }
        .gd-icon-default { background:rgba(29,78,216,.9); }
        .gd-text-green { color:#059669; }
        .gd-text-red { color:#dc2626; }
        .gd-text-muted { color:#6b7280; }

        .dark .gd-bg-default { background:rgba(37,99,235,.12); border-color:rgba(37,99,235,.25); }
        .dark .gd-icon-default { background:rgba(37,99,235,.75); }
        .dark .gd-text-green { color:#34d399; }
        .dark .gd-text-red { color:#f87171; }
        .dark .gd-text-muted { color:#9ca3af; }
        .dark .gd-stat-card { border-color:rgba(100,100,100,.25); }

        /* Kelas card */
        .gd-kelas {
            border-radius:1rem; overflow:hidden;
            border:1px solid rgba(100,100,100,.15);
        }
        .dark .gd-kelas { border-color:rgba(100,100,100,.25); }
        .gd-kelas-header {
            padding:1.25rem 1.5rem; display:flex; justify-content:space-between;
            align-items:center; flex-wrap:wrap; gap:1rem;
            border-bottom:1px solid rgba(100,100,100,.12);
            background:rgba(100,100,100,.04);
        }
        .dark .gd-kelas-header { background:rgba(0,0,0,.2); border-color:rgba(100,100,100,.2); }
        .gd-kelas-name { font-size:1.125rem; font-weight:700; }
        .gd-kelas-sub { font-size:.8rem; margin-top:.15rem; }

        /* Progress ring */
        .gd-ring-wrap { display:flex; align-items:center; gap:1rem; }
        .gd-ring { position:relative; width:4rem; height:4rem; }
        .gd-ring svg { width:4rem; height:4rem; transform:rotate(-90deg); }
        .gd-ring-bg { stroke:rgba(100,100,100,.15); }
        .dark .gd-ring-bg { stroke:rgba(100,100,100,.25); }
        .gd-ring-pct { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:700; }
        .gd-ring-stats { text-align:right; font-size:.8rem; font-weight:600; line-height:1.5; }

        /* Tabs */
        .gd-tabs { display:flex; gap:.375rem; padding:1rem 1.5rem 0; flex-wrap:wrap; }
        .gd-tab {
            padding:.5rem 1rem; border-radius:.75rem; font-size:.8rem;
            cursor:pointer; border:none; transition:all .15s;
            background:rgba(100,100,100,.08); color:inherit;
        }
        .gd-tab:hover { background:rgba(100,100,100,.15); }
        .gd-tab.active-belum { background:rgba(239,68,68,.12); color:#dc2626; font-weight:600; }
        .gd-tab.active-sudah { background:rgba(16,185,129,.12); color:#059669; font-weight:600; }
        .gd-tab.active-progress { background:rgba(59,130,246,.12); color:#2563eb; font-weight:600; }
        .dark .gd-tab { background:rgba(100,100,100,.15); }
        .dark .gd-tab.active-belum { background:rgba(239,68,68,.15); color:#f87171; }
        .dark .gd-tab.active-sudah { background:rgba(16,185,129,.15); color:#34d399; }
        .dark .gd-tab.active-progress { background:rgba(59,130,246,.15); color:#60a5fa; }
        .gd-tab-count { font-size:.7rem; opacity:.7; margin-left:.25rem; }

        /* Tab content */
        .gd-tab-body { padding:1.5rem; }

        /* Student grid */
        .gd-siswa-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; }
        @media(max-width:1024px) { .gd-siswa-grid { grid-template-columns:repeat(2,1fr); } }
        @media(max-width:640px) { .gd-siswa-grid { grid-template-columns:1fr; } }

        .gd-siswa-card {
            display:flex; align-items:center; gap:.75rem;
            padding:.75rem; border-radius:.75rem; border:1px solid;
        }
        .gd-siswa-belum { background:rgba(239,68,68,.05); border-color:rgba(239,68,68,.15); }
        .gd-siswa-sudah { background:rgba(16,185,129,.05); border-color:rgba(16,185,129,.15); }
        .dark .gd-siswa-belum { background:rgba(239,68,68,.08); border-color:rgba(239,68,68,.12); }
        .dark .gd-siswa-sudah { background:rgba(16,185,129,.08); border-color:rgba(16,185,129,.12); }

        .gd-avatar {
            width:2.5rem; height:2.5rem; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:.8rem; font-weight:700; flex-shrink:0;
        }
        .gd-avatar-l { background:rgba(59,130,246,.2); color:#2563eb; }
        .gd-avatar-p { background:rgba(236,72,153,.2); color:#db2777; }
        .dark .gd-avatar-l { background:rgba(59,130,246,.25); color:#93c5fd; }
        .dark .gd-avatar-p { background:rgba(236,72,153,.25); color:#f9a8d4; }

        .gd-siswa-info { flex:1; min-width:0; }
        .gd-siswa-name { font-size:.875rem; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .gd-siswa-nisn { font-size:.75rem; }

        .gd-badge {
            font-size:.7rem; font-weight:600; padding:.25rem .5rem; border-radius:.5rem;
            white-space:nowrap; flex-shrink:0;
        }
        .gd-badge-red { background:rgba(239,68,68,.15); color:#dc2626; }
        .gd-badge-green { background:rgba(16,185,129,.15); color:#059669; }
        .dark .gd-badge-red { background:rgba(239,68,68,.2); color:#fca5a5; }
        .dark .gd-badge-green { background:rgba(16,185,129,.2); color:#6ee7b7; }

        /* Progress list */
        .gd-progress-item {
            display:flex; align-items:center; gap:1rem;
            padding:.75rem; border-radius:.75rem;
            background:rgba(100,100,100,.04);
        }
        .dark .gd-progress-item { background:rgba(100,100,100,.1); }
        .gd-progress-item + .gd-progress-item { margin-top:.75rem; }
        .gd-progress-bar-wrap { width:100%; height:.5rem; border-radius:.25rem; background:rgba(100,100,100,.12); }
        .dark .gd-progress-bar-wrap { background:rgba(100,100,100,.2); }
        .gd-progress-bar { height:.5rem; border-radius:.25rem; transition:width .5s; }
        .gd-bar-green { background:#2563eb; }
        .gd-bar-amber { background:#f59e0b; }
        .gd-bar-red { background:#ef4444; }
        .gd-progress-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:.25rem; }
        .gd-progress-label { font-size:.75rem; font-weight:600; }

        /* Empty state */
        .gd-empty { text-align:center; padding:4rem 1rem; }
        .gd-empty-icon { font-size:3rem; }
        .gd-empty-title { margin-top:1rem; font-size:1.125rem; font-weight:600; }
        .gd-empty-sub { margin-top:.25rem; font-size:.875rem; }

        /* Celebrate */
        .gd-celebrate { text-align:center; padding:2rem; }
        .gd-celebrate-icon { font-size:2.5rem; }
        .gd-celebrate-text { margin-top:.5rem; font-size:.875rem; font-weight:500; }

        /* === Pending & Belum Mengisi sections === */
        .gd-section-card {
            border-radius:1rem; overflow:hidden;
            border:1px solid rgba(100,100,100,.15);
        }
        .dark .gd-section-card { border-color:rgba(100,100,100,.25); }
        .gd-section-header {
            padding:1rem 1.5rem; display:flex; justify-content:space-between;
            align-items:center; flex-wrap:wrap; gap:.75rem;
            border-bottom:1px solid rgba(100,100,100,.12);
            background:rgba(100,100,100,.04);
        }
        .dark .gd-section-header { background:rgba(0,0,0,.2); border-color:rgba(100,100,100,.2); }
        .gd-section-title { font-size:1rem; font-weight:700; display:flex; align-items:center; gap:.5rem; }
        .gd-section-count {
            font-size:.75rem; font-weight:700; padding:.25rem .625rem;
            border-radius:9999px; display:inline-flex; align-items:center; justify-content:center;
        }
        .gd-count-amber { background:rgba(245,158,11,.15); color:#d97706; }
        .gd-count-red { background:rgba(239,68,68,.15); color:#dc2626; }
        .dark .gd-count-amber { background:rgba(245,158,11,.2); color:#fbbf24; }
        .dark .gd-count-red { background:rgba(239,68,68,.2); color:#fca5a5; }
        .gd-section-body { padding:1rem 1.5rem; max-height:36rem; overflow-y:auto; }
        .gd-section-link {
            font-size:.8rem; font-weight:600; color:#2563eb; text-decoration:none; white-space:nowrap;
        }
        .gd-section-link:hover { text-decoration:underline; }
        .dark .gd-section-link { color:#60a5fa; }

        /* Pending table */
        .gd-table { width:100%; border-collapse:collapse; font-size:.8rem; }
        .gd-table th {
            text-align:left; padding:.5rem .75rem; font-weight:600; font-size:.7rem;
            text-transform:uppercase; letter-spacing:.05em;
            border-bottom:1px solid rgba(100,100,100,.12);
        }
        .gd-table td { padding:.6rem .75rem; border-bottom:1px solid rgba(100,100,100,.06); }
        .dark .gd-table th { border-color:rgba(100,100,100,.2); }
        .dark .gd-table td { border-color:rgba(100,100,100,.1); }
        .gd-table tr:hover td { background:rgba(100,100,100,.04); }
        .dark .gd-table tr:hover td { background:rgba(100,100,100,.08); }
        .gd-hari-badge {
            display:inline-flex; align-items:center; justify-content:center;
            width:1.75rem; height:1.75rem; border-radius:50%;
            font-size:.7rem; font-weight:700;
            background:rgba(59,130,246,.12); color:#2563eb;
        }
        .dark .gd-hari-badge { background:rgba(59,130,246,.2); color:#93c5fd; }
        .gd-status-pending {
            font-size:.7rem; font-weight:600; padding:.2rem .5rem; border-radius:.375rem;
            background:rgba(245,158,11,.12); color:#d97706;
        }
        .dark .gd-status-pending { background:rgba(245,158,11,.2); color:#fbbf24; }

        /* Missing days */
        .gd-missing-row {
            display:flex; align-items:center; gap:.75rem;
            padding:.65rem .75rem; border-radius:.5rem;
        }
        .gd-missing-row + .gd-missing-row { margin-top:.375rem; }
        .gd-missing-row:hover { background:rgba(100,100,100,.04); }
        .dark .gd-missing-row:hover { background:rgba(100,100,100,.08); }
        .gd-missing-days {
            display:flex; flex-wrap:wrap; gap:.25rem; flex:1;
        }
        .gd-day-chip {
            display:inline-flex; align-items:center; justify-content:center;
            min-width:1.5rem; height:1.5rem; padding:0 .35rem;
            border-radius:.375rem; font-size:.65rem; font-weight:600;
            background:rgba(239,68,68,.1); color:#dc2626;
        }
        .dark .gd-day-chip { background:rgba(239,68,68,.15); color:#fca5a5; }
        .gd-missing-count {
            font-size:.7rem; font-weight:700; white-space:nowrap;
        }
        .gd-two-col { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        @media(max-width:1024px) { .gd-two-col { grid-template-columns:1fr; } }
    </style>

    <div class="gd-wrap" x-data="{}">

        {{-- ═══ Hero Banner ═══ --}}
        <div class="gd-hero">
            <div class="gd-hero-blur1"></div>
            <div class="gd-hero-blur2"></div>
            <div class="gd-hero-content">
                <div class="gd-hero-left">
                    <p class="gd-greet">Assalamu'alaikum 👋</p>
                    <h1>{{ $guru->name }}</h1>
                    <p class="gd-sub">Wali Kelas — {{ $hijriDate }}</p>
                </div>
                @if ($isRamadhan)
                    <div class="gd-hero-day">
                        <div>
                            <p class="gd-day-label">Ramadhan {{ $hijriYear }} H</p>
                            <p class="gd-day-num">Hari ke-{{ $hariKe }}</p>
                        </div>
                        <span style="font-size:2.25rem">🌙</span>
                    </div>
                @else
                    <div class="gd-hero-day">
                        <div>
                            <p class="gd-day-label">Tanggal Hijriah</p>
                            <p class="gd-day-num" style="font-size:1.25rem">{{ $hijriDate }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ═══ Stats Grid ═══ --}}
        <div class="gd-stats">
            <div class="gd-stat-card gd-bg-default">
                <div class="gd-stat-icon gd-icon-default"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg></div>
                <div>
                    <p class="gd-stat-label gd-text-muted">Total Kelas</p>
                    <p class="gd-stat-value">{{ $totalKelas }}</p>
                </div>
            </div>
            <div class="gd-stat-card gd-bg-default">
                <div class="gd-stat-icon gd-icon-default"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg></div>
                <div>
                    <p class="gd-stat-label gd-text-muted">Total Siswa</p>
                    <p class="gd-stat-value">{{ $totalSiswa }}</p>
                </div>
            </div>
            <div class="gd-stat-card gd-bg-default">
                <div class="gd-stat-icon gd-icon-default"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div>
                    <p class="gd-stat-label gd-text-green">Sudah Kirim</p>
                    <p class="gd-stat-value gd-text-green">{{ $totalSubmissionsToday }}<span class="gd-stat-sub">/{{ $totalSiswa }}</span></p>
                </div>
            </div>
            <div class="gd-stat-card gd-bg-default">
                <div class="gd-stat-icon gd-icon-default"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div>
                    <p class="gd-stat-label gd-text-red">Belum Kirim</p>
                    <p class="gd-stat-value gd-text-red">{{ $totalBelumToday }}<span class="gd-stat-sub">/{{ $totalSiswa }}</span></p>
                </div>
            </div>
        </div>

        {{-- ═══ Pending Verifikasi & Belum Mengisi ═══ --}}
        @if ($hariKe > 0)
        <div class="gd-two-col">
            {{-- Menunggu Verifikasi --}}
            <div class="gd-section-card">
                <div class="gd-section-header">
                    <div class="gd-section-title">
                        ⏳ Menunggu Verifikasi
                        @if ($totalPending > 0)
                            <span class="gd-section-count gd-count-amber">{{ $totalPending }}</span>
                        @endif
                    </div>
                    <a href="{{ url('/portal-guru-smkn1/verifikasi?tableFilters[status][value]=pending') }}" class="gd-section-link">Lihat Semua →</a>
                </div>
                <div class="gd-section-body">
                    @if ($pendingSubmissions->isEmpty())
                        <div class="gd-celebrate">
                            <div class="gd-celebrate-icon">✅</div>
                            <div class="gd-celebrate-text gd-text-green">Semua formulir sudah diverifikasi!</div>
                        </div>
                    @else
                        <table class="gd-table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NISN</th>
                                    <th style="text-align:center">Hari</th>
                                    <th>Dikirim</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingSubmissions->take(10) as $sub)
                                    <tr>
                                        <td style="font-weight:500;">{{ $sub['user_name'] }}</td>
                                        <td class="gd-text-muted">{{ $sub['user_nisn'] }}</td>
                                        <td style="text-align:center"><span class="gd-hari-badge">{{ $sub['hari_ke'] }}</span></td>
                                        <td class="gd-text-muted">{{ $sub['created_at'] }}</td>
                                        <td><span class="gd-status-pending">Menunggu</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if ($pendingSubmissions->count() > 10)
                            <div style="text-align:center; padding-top:.75rem;">
                                <a href="{{ url('/portal-guru-smkn1/verifikasi?tableFilters[status][value]=pending') }}" class="gd-section-link">
                                    +{{ $pendingSubmissions->count() - 10 }} lainnya →
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Belum Mengisi --}}
            <div class="gd-section-card">
                <div class="gd-section-header">
                    <div class="gd-section-title">
                        📝 Belum Mengisi Formulir
                        @if (count($belumMengisiDetail) > 0)
                            <span class="gd-section-count gd-count-red">{{ count($belumMengisiDetail) }} siswa</span>
                        @endif
                    </div>
                </div>
                <div class="gd-section-body">
                    @if (count($belumMengisiDetail) === 0)
                        <div class="gd-celebrate">
                            <div class="gd-celebrate-icon">🎉</div>
                            <div class="gd-celebrate-text gd-text-green">Semua siswa sudah mengisi formulir lengkap!</div>
                        </div>
                    @else
                        @foreach ($belumMengisiDetail as $item)
                            <div class="gd-missing-row">
                                <div class="gd-avatar {{ $item['jk'] === 'L' ? 'gd-avatar-l' : 'gd-avatar-p' }}" style="width:2rem;height:2rem;font-size:.7rem;">{{ $item['jk'] }}</div>
                                <div style="min-width:0; flex-shrink:0; width:8rem;">
                                    <div class="gd-siswa-name" style="font-size:.8rem;">{{ $item['name'] }}</div>
                                    <div class="gd-text-muted" style="font-size:.7rem;">{{ $item['nisn'] }}</div>
                                </div>
                                <div class="gd-missing-days">
                                    @foreach ($item['missing_days'] as $day)
                                        <span class="gd-day-chip">{{ $day }}</span>
                                    @endforeach
                                </div>
                                <div class="gd-missing-count gd-text-red">{{ $item['missing_count'] }}/{{ $item['total_days'] }}</div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- ═══ Per Kelas ═══ --}}
        @foreach ($kelasOverview as $idx => $overview)
            @php
                $pct = $overview['siswa_count'] > 0 ? round(($overview['sudah_submit'] / $overview['siswa_count']) * 100) : 0;
                $ringColor = $pct >= 80 ? '#2563eb' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="gd-kelas" x-data="{ tab: 'belum' }">
                {{-- Header --}}
                <div class="gd-kelas-header">
                    <div>
                        <div class="gd-kelas-name">{{ preg_replace('/\s*KLOTER\s*\d*/i', '', $overview['kelas']->nama) }}</div>
                        <div class="gd-kelas-sub gd-text-muted">{{ $overview['siswa_count'] }} siswa terdaftar</div>
                    </div>
                    <div class="gd-ring-wrap">
                        <div class="gd-ring">
                            <svg viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="15.5" fill="none" stroke-width="3" class="gd-ring-bg"></circle>
                                <circle cx="18" cy="18" r="15.5" fill="none" stroke-width="3"
                                    stroke-dasharray="{{ $pct }}, 100" stroke-linecap="round"
                                    style="stroke: {{ $ringColor }};"></circle>
                            </svg>
                            <div class="gd-ring-pct">{{ $pct }}%</div>
                        </div>
                        <div class="gd-ring-stats">
                            <div class="gd-text-green">{{ $overview['sudah_submit'] }} sudah</div>
                            <div class="gd-text-red">{{ $overview['belum_submit'] }} belum</div>
                        </div>
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="gd-tabs">
                    <button class="gd-tab" @click="tab = 'belum'"
                        :class="tab === 'belum' ? 'active-belum' : ''">
                        ⏳ Belum Kirim <span class="gd-tab-count">({{ $overview['belum_submit'] }})</span>
                    </button>
                    <button class="gd-tab" @click="tab = 'sudah'"
                        :class="tab === 'sudah' ? 'active-sudah' : ''">
                        ✅ Sudah Kirim <span class="gd-tab-count">({{ $overview['sudah_submit'] }})</span>
                    </button>
                    <button class="gd-tab" @click="tab = 'progress'"
                        :class="tab === 'progress' ? 'active-progress' : ''">
                        📊 Progress
                    </button>
                </div>

                {{-- Tab Content --}}
                <div class="gd-tab-body">
                    {{-- Belum Kirim --}}
                    <div x-show="tab === 'belum'" x-transition.opacity>
                        @php $belumList = $overview['siswa_data']->where('today_submitted', false); @endphp
                        @if ($belumList->isEmpty())
                            <div class="gd-celebrate">
                                <div class="gd-celebrate-icon">🎉</div>
                                <div class="gd-celebrate-text gd-text-green">Semua siswa sudah mengirim formulir hari ini!</div>
                            </div>
                        @else
                            <div class="gd-siswa-grid">
                                @foreach ($belumList as $siswa)
                                    <div class="gd-siswa-card gd-siswa-belum">
                                        <div class="gd-avatar {{ $siswa['jk'] === 'L' ? 'gd-avatar-l' : 'gd-avatar-p' }}">{{ $siswa['jk'] }}</div>
                                        <div class="gd-siswa-info">
                                            <div class="gd-siswa-name">{{ $siswa['name'] }}</div>
                                            <div class="gd-siswa-nisn gd-text-muted">{{ $siswa['nisn'] }}</div>
                                        </div>
                                        <span class="gd-badge gd-badge-red">Belum</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Sudah Kirim --}}
                    <div x-show="tab === 'sudah'" x-transition.opacity>
                        @php $sudahList = $overview['siswa_data']->where('today_submitted', true); @endphp
                        @if ($sudahList->isEmpty())
                            <div class="gd-celebrate">
                                <div class="gd-celebrate-icon">😔</div>
                                <div class="gd-celebrate-text gd-text-muted">Belum ada siswa yang mengirim formulir hari ini.</div>
                            </div>
                        @else
                            <div class="gd-siswa-grid">
                                @foreach ($sudahList as $siswa)
                                    <div class="gd-siswa-card gd-siswa-sudah">
                                        <div class="gd-avatar {{ $siswa['jk'] === 'L' ? 'gd-avatar-l' : 'gd-avatar-p' }}">{{ $siswa['jk'] }}</div>
                                        <div class="gd-siswa-info">
                                            <div class="gd-siswa-name">{{ $siswa['name'] }}</div>
                                            <div class="gd-siswa-nisn gd-text-muted">{{ $siswa['nisn'] }}</div>
                                        </div>
                                        <span class="gd-badge gd-badge-green">✓ Kirim</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Progress --}}
                    <div x-show="tab === 'progress'" x-transition.opacity>
                        @foreach ($overview['siswa_data']->sortByDesc('total_submitted') as $siswa)
                            @php
                                $barClass = $siswa['progress'] >= 80 ? 'gd-bar-green' : ($siswa['progress'] >= 50 ? 'gd-bar-amber' : 'gd-bar-red');
                                $txtClass = $siswa['progress'] >= 80 ? 'gd-text-green' : ($siswa['progress'] >= 50 ? '' : 'gd-text-red');
                            @endphp
                            <div class="gd-progress-item">
                                <div class="gd-avatar {{ $siswa['jk'] === 'L' ? 'gd-avatar-l' : 'gd-avatar-p' }}">{{ $siswa['jk'] }}</div>
                                <div class="gd-siswa-info">
                                    <div class="gd-progress-header">
                                        <div class="gd-siswa-name">{{ $siswa['name'] }}</div>
                                        <div class="gd-progress-label {{ $txtClass }}">{{ $siswa['total_submitted'] }}/{{ $hariKe }} hari</div>
                                    </div>
                                    <div class="gd-progress-bar-wrap">
                                        <div class="gd-progress-bar {{ $barClass }}" style="width:{{ $siswa['progress'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Empty state --}}
        @if (count($kelasOverview) === 0)
            <div class="gd-kelas">
                <div class="gd-empty">
                    <div class="gd-empty-icon">📚</div>
                    <div class="gd-empty-title">Belum ada kelas</div>
                    <div class="gd-empty-sub gd-text-muted">Anda belum ditugaskan sebagai wali kelas manapun.</div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
