<x-filament-panels::page>
  <style>
    /* ─── Rekap Siswa Detail ─── */
    .rs-wrap { display:flex; flex-direction:column; gap:1.25rem; }

    /* Hero Banner */
    .rs-hero {
      position:relative; overflow:hidden; border-radius:1rem;
      background:linear-gradient(160deg, #0f172a 0%, #1e3a5f 35%, #2563eb 100%);
      padding:1.75rem 2rem; color:#fff;
      box-shadow:0 10px 25px -5px rgba(37,99,235,.3);
    }
    .rs-hero::before {
      content:''; position:absolute; top:-3rem; right:-3rem;
      width:14rem; height:14rem; border-radius:50%;
      background:radial-gradient(circle, rgba(37,99,235,.25) 0%, transparent 70%);
      filter:blur(40px); pointer-events:none;
    }
    .rs-hero-content { position:relative; z-index:2; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .rs-hero-left { flex:1; min-width:0; }
    .rs-hero-name { font-size:1.65rem; font-weight:800; letter-spacing:-.02em; margin:0; }
    .rs-hero-sub { font-size:.825rem; color:#93c5fd; margin-top:.25rem; display:flex; align-items:center; gap:.375rem; }
    .rs-hero-sub svg { width:1rem; height:1rem; }
    .rs-hero-meta { display:flex; gap:1rem; margin-top:.5rem; flex-wrap:wrap; }
    .rs-hero-tag {
      display:inline-flex; align-items:center; gap:.375rem;
      background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12);
      border-radius:.5rem; padding:.25rem .625rem; font-size:.725rem; color:#cbd5e1;
    }
    .rs-hero-tag svg { width:.8rem; height:.8rem; }
    .rs-hero-right { display:flex; align-items:center; gap:.75rem; }
    .rs-hero-badge {
      display:flex; align-items:center; gap:.5rem;
      background:rgba(255,255,255,.1); backdrop-filter:blur(12px);
      border:1px solid rgba(255,255,255,.15); border-radius:.875rem;
      padding:.75rem 1.25rem; white-space:nowrap;
    }
    .rs-hero-badge-icon { font-size:1.5rem; line-height:1; filter:drop-shadow(0 0 6px rgba(250,204,21,.4)); }
    .rs-hero-badge-label { font-size:.65rem; text-transform:uppercase; letter-spacing:.06em; color:#93c5fd; }
    .rs-hero-badge-value { font-size:1.35rem; font-weight:800; line-height:1.1; }

    /* Stats Grid */
    .rs-stats { display:grid; grid-template-columns:repeat(6,1fr); gap:.875rem; }
    @media(max-width:1024px) { .rs-stats { grid-template-columns:repeat(3,1fr); } }
    @media(max-width:640px) { .rs-stats { grid-template-columns:repeat(2,1fr); } }

    .rs-stat {
      border-radius:1rem; padding:1.125rem 1rem; display:flex; align-items:center; gap:.75rem;
      border:1px solid transparent; transition:all .2s; background:#fff;
    }
    .dark .rs-stat { background:rgba(30,41,59,.6); }
    .rs-stat:hover { transform:translateY(-2px); box-shadow:0 4px 12px -2px rgba(0,0,0,.08); }
    .dark .rs-stat:hover { box-shadow:0 4px 12px -2px rgba(0,0,0,.3); }
    .rs-stat-icon {
      width:2.75rem; height:2.75rem; border-radius:.75rem;
      display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .rs-stat-icon svg { width:1.2rem; height:1.2rem; color:#fff; }
    .rs-stat-label { font-size:.65rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; color:#6b7280; }
    .dark .rs-stat-label { color:#9ca3af; }
    .rs-stat-value { font-size:1.35rem; font-weight:700; color:#1e293b; }
    .dark .rs-stat-value { color:#f1f5f9; }

    .rs-stat-blue { border-color:rgba(37,99,235,.15); background:rgba(37,99,235,.04); }
    .dark .rs-stat-blue { border-color:rgba(37,99,235,.25); background:rgba(37,99,235,.1); }
    .rs-stat-blue .rs-stat-icon { background:rgba(37,99,235,.85); }

    .rs-stat-green { border-color:rgba(16,185,129,.15); background:rgba(16,185,129,.04); }
    .dark .rs-stat-green { border-color:rgba(16,185,129,.25); background:rgba(16,185,129,.1); }
    .rs-stat-green .rs-stat-icon { background:rgba(16,185,129,.85); }

    .rs-stat-yellow { border-color:rgba(245,158,11,.15); background:rgba(245,158,11,.04); }
    .dark .rs-stat-yellow { border-color:rgba(245,158,11,.25); background:rgba(245,158,11,.1); }
    .rs-stat-yellow .rs-stat-icon { background:rgba(245,158,11,.85); }

    .rs-stat-red { border-color:rgba(239,68,68,.15); background:rgba(239,68,68,.04); }
    .dark .rs-stat-red { border-color:rgba(239,68,68,.25); background:rgba(239,68,68,.1); }
    .rs-stat-red .rs-stat-icon { background:rgba(239,68,68,.85); }

    .rs-stat-purple { border-color:rgba(139,92,246,.15); background:rgba(139,92,246,.04); }
    .dark .rs-stat-purple { border-color:rgba(139,92,246,.25); background:rgba(139,92,246,.1); }
    .rs-stat-purple .rs-stat-icon { background:rgba(139,92,246,.85); }

    .rs-stat-orange { border-color:rgba(249,115,22,.15); background:rgba(249,115,22,.04); }
    .dark .rs-stat-orange { border-color:rgba(249,115,22,.25); background:rgba(249,115,22,.1); }
    .rs-stat-orange .rs-stat-icon { background:rgba(249,115,22,.85); }

    /* Mid row */
    .rs-mid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; align-items:stretch; }
    @media(max-width:768px) { .rs-mid { grid-template-columns:1fr; } }

    /* Card */
    .rs-card {
      border-radius:1rem; overflow:hidden;
      border:1px solid rgba(100,100,100,.12); background:#fff; display:flex; flex-direction:column;
    }
    .dark .rs-card { border-color:rgba(100,100,100,.25); background:rgba(30,41,59,.6); }
    .rs-card-head {
      padding:.875rem 1.25rem; display:flex; justify-content:space-between; align-items:center;
      border-bottom:1px solid rgba(100,100,100,.08); background:rgba(100,100,100,.02);
    }
    .dark .rs-card-head { background:rgba(0,0,0,.12); border-color:rgba(100,100,100,.18); }
    .rs-card-title { font-size:.85rem; font-weight:700; display:flex; align-items:center; gap:.5rem; color:#1e293b; }
    .dark .rs-card-title { color:#f1f5f9; }
    .rs-card-body { padding:1.25rem; flex:1; }

    /* Ring */
    .rs-ring-wrap { display:flex; align-items:center; gap:1.25rem; }
    .rs-ring { position:relative; width:5rem; height:5rem; flex-shrink:0; }
    .rs-ring svg { width:5rem; height:5rem; transform:rotate(-90deg); }
    .rs-ring-bg { stroke:rgba(100,100,100,.1); }
    .dark .rs-ring-bg { stroke:rgba(100,100,100,.25); }
    .rs-ring-pct {
      position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
      font-size:1rem; font-weight:800; color:#1e293b;
    }
    .dark .rs-ring-pct { color:#f1f5f9; }
    .rs-ring-info { flex:1; }
    .rs-ring-label { font-size:.75rem; font-weight:600; color:#6b7280; }
    .dark .rs-ring-label { color:#9ca3af; }
    .rs-ring-desc { font-size:.7rem; color:#94a3b8; margin-top:.125rem; }
    .dark .rs-ring-desc { color:#64748b; }

    .rs-divider { border:none; border-top:1px solid rgba(100,100,100,.08); margin:.875rem 0; }
    .dark .rs-divider { border-color:rgba(100,100,100,.18); }

    /* Info item */
    .rs-info-item { display:flex; align-items:center; justify-content:space-between; padding:.5rem 0; }
    .rs-info-item + .rs-info-item { border-top:1px solid rgba(100,100,100,.06); }
    .dark .rs-info-item + .rs-info-item { border-color:rgba(100,100,100,.12); }
    .rs-info-label { font-size:.8rem; color:#6b7280; display:flex; align-items:center; gap:.375rem; }
    .dark .rs-info-label { color:#9ca3af; }
    .rs-info-label svg { width:.875rem; height:.875rem; }
    .rs-info-value { font-size:.85rem; font-weight:600; color:#1e293b; }
    .dark .rs-info-value { color:#f1f5f9; }

    /* Daily Grid */
    .rs-grid-card {
      border-radius:1rem; overflow:hidden;
      border:1px solid rgba(100,100,100,.12); background:#fff;
    }
    .dark .rs-grid-card { border-color:rgba(100,100,100,.25); background:rgba(30,41,59,.6); }
    .rs-grid-head {
      padding:.875rem 1.25rem; display:flex; justify-content:space-between; align-items:center;
      border-bottom:1px solid rgba(100,100,100,.08); background:rgba(100,100,100,.02);
    }
    .dark .rs-grid-head { background:rgba(0,0,0,.12); border-color:rgba(100,100,100,.18); }
    .rs-grid-title { font-size:.85rem; font-weight:700; display:flex; align-items:center; gap:.5rem; color:#1e293b; }
    .dark .rs-grid-title { color:#f1f5f9; }
    .rs-grid-count {
      font-size:.7rem; font-weight:600; padding:.2rem .5rem; border-radius:.375rem;
      background:rgba(37,99,235,.08); color:#2563eb;
    }
    .dark .rs-grid-count { background:rgba(37,99,235,.2); color:#93c5fd; }

    .rs-grid-wrap { overflow-x:auto; }
    .rs-grid-table { width:100%; border-collapse:collapse; font-size:.8rem; }
    .rs-grid-table th {
      text-align:left; padding:.625rem 1rem; font-weight:600; font-size:.7rem;
      text-transform:uppercase; letter-spacing:.05em;
      border-bottom:2px solid rgba(100,100,100,.08); color:#6b7280; background:rgba(100,100,100,.02);
    }
    .dark .rs-grid-table th { border-color:rgba(100,100,100,.18); color:#9ca3af; background:transparent; }
    .rs-grid-table td {
      padding:.625rem 1rem; border-bottom:1px solid rgba(100,100,100,.05); color:#374151;
    }
    .dark .rs-grid-table td { border-color:rgba(100,100,100,.1); color:#d1d5db; }
    .rs-grid-table tr:hover td { background:rgba(100,100,100,.03); }
    .dark .rs-grid-table tr:hover td { background:rgba(100,100,100,.06); }

    /* Daily status badges */
    .rs-day-badge {
      display:inline-flex; align-items:center; justify-content:center; padding:.25rem .625rem;
      border-radius:.375rem; font-size:.7rem; font-weight:600; gap:.25rem;
    }
    .rs-day-verified { background:rgba(16,185,129,.1); color:#059669; }
    .dark .rs-day-verified { background:rgba(16,185,129,.2); color:#34d399; }
    .rs-day-pending { background:rgba(245,158,11,.1); color:#d97706; }
    .dark .rs-day-pending { background:rgba(245,158,11,.2); color:#fbbf24; }
    .rs-day-rejected { background:rgba(239,68,68,.1); color:#dc2626; }
    .dark .rs-day-rejected { background:rgba(239,68,68,.2); color:#fca5a5; }
    .rs-day-belum { background:rgba(100,116,139,.08); color:#94a3b8; }
    .dark .rs-day-belum { background:rgba(100,116,139,.15); color:#64748b; }

    .rs-day-badge svg { width:.75rem; height:.75rem; }

    .rs-catatan { font-size:.7rem; color:#6b7280; font-style:italic; max-width:12rem; }
    .dark .rs-catatan { color:#9ca3af; }

    /* Export button */
    .rs-export-btn {
      display:inline-flex; align-items:center; gap:.5rem;
      padding:.5rem 1rem; border-radius:.625rem; font-size:.8rem; font-weight:600;
      background:linear-gradient(135deg, #059669 0%, #10b981 100%);
      color:#fff; border:none; cursor:pointer; text-decoration:none;
      transition:all .2s; box-shadow:0 2px 8px -2px rgba(16,185,129,.4);
    }
    .rs-export-btn:hover { transform:translateY(-1px); box-shadow:0 4px 12px -2px rgba(16,185,129,.5); color:#fff; }
    .rs-export-btn svg { width:1rem; height:1rem; }

    /* Responsive */
    @media(max-width:640px) {
      .rs-hero { padding:1.25rem 1rem; }
      .rs-hero-name { font-size:1.25rem; }
      .rs-hero-content { flex-direction:column; align-items:flex-start; }
      .rs-stats { grid-template-columns:1fr 1fr; }
      .rs-grid-table th, .rs-grid-table td { padding:.5rem .625rem; font-size:.725rem; }
    }
    @media(max-width:380px) {
      .rs-stats { grid-template-columns:1fr; }
    }
  </style>

  <div class="rs-wrap">

    {{-- ═══ Hero Banner ═══ --}}
    <div class="rs-hero">
      <div class="rs-hero-content">
        <div class="rs-hero-left">
          <h1 class="rs-hero-name">{{ $siswa->name }}</h1>
          <p class="rs-hero-sub">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
            Rekap Aktivitas Harian Siswa
          </p>
          <div class="rs-hero-meta">
            <span class="rs-hero-tag">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
              {{ $siswa->kelas?->nama ?? '-' }}
            </span>
            @if($siswa->nisn)
            <span class="rs-hero-tag">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" /></svg>
              NISN: {{ $siswa->nisn }}
            </span>
            @endif
          </div>
        </div>
        <div class="rs-hero-right">
          <div class="rs-hero-badge">
            <span class="rs-hero-badge-icon">🌙</span>
            <div>
              <p class="rs-hero-badge-label">Ramadhan</p>
              <p class="rs-hero-badge-value">{{ $hariKe > 0 ? "Hari ke-{$hariKe}" : 'Belum dimulai' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ═══ Stat Cards ═══ --}}
    <div class="rs-stats">
      {{-- Total Laporan --}}
      <div class="rs-stat rs-stat-blue">
        <div class="rs-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
        </div>
        <div>
          <p class="rs-stat-label">Total Laporan</p>
          <p class="rs-stat-value">{{ $totalSubmissions }}</p>
        </div>
      </div>

      {{-- Terverifikasi --}}
      <div class="rs-stat rs-stat-green">
        <div class="rs-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
          <p class="rs-stat-label">Terverifikasi</p>
          <p class="rs-stat-value">{{ $verified }}</p>
        </div>
      </div>

      {{-- Menunggu --}}
      <div class="rs-stat rs-stat-yellow">
        <div class="rs-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
          <p class="rs-stat-label">Menunggu</p>
          <p class="rs-stat-value">{{ $pending }}</p>
        </div>
      </div>

      {{-- Ditolak --}}
      <div class="rs-stat rs-stat-red">
        <div class="rs-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
          <p class="rs-stat-label">Ditolak</p>
          <p class="rs-stat-value">{{ $rejected }}</p>
        </div>
      </div>

      {{-- Belum Lapor --}}
      <div class="rs-stat rs-stat-orange">
        <div class="rs-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
        </div>
        <div>
          <p class="rs-stat-label">Belum Lapor</p>
          <p class="rs-stat-value">{{ $belumLapor }}</p>
        </div>
      </div>

      {{-- Kepatuhan --}}
      <div class="rs-stat rs-stat-purple">
        <div class="rs-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
        </div>
        <div>
          <p class="rs-stat-label">Kepatuhan</p>
          <p class="rs-stat-value">{{ $complianceRate }}%</p>
        </div>
      </div>
    </div>

    {{-- ═══ Mid Row: Kepatuhan + Info ═══ --}}
    <div class="rs-mid">

      {{-- Kepatuhan & Verifikasi Ring --}}
      <div class="rs-card">
        <div class="rs-card-head">
          <h3 class="rs-card-title">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.1rem;height:1.1rem;color:#2563eb;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
            Tingkat Kepatuhan
          </h3>
        </div>
        <div class="rs-card-body">
          <div class="rs-ring-wrap">
            <div class="rs-ring">
              <svg viewBox="0 0 36 36">
                <circle class="rs-ring-bg" fill="none" stroke-width="3.8" cx="18" cy="18" r="15.9155" />
                <circle fill="none" stroke-width="3.8" cx="18" cy="18" r="15.9155"
                  stroke="{{ $complianceRate >= 80 ? '#10b981' : ($complianceRate >= 50 ? '#f59e0b' : '#ef4444') }}"
                  stroke-dasharray="{{ $complianceRate }}, 100"
                  stroke-linecap="round" />
              </svg>
              <span class="rs-ring-pct">{{ $complianceRate }}%</span>
            </div>
            <div class="rs-ring-info">
              <p class="rs-ring-label">Pengisian Formulir</p>
              <p class="rs-ring-desc">{{ $totalSubmissions }} dari {{ max($hariKe, 1) }} hari yang diharapkan</p>
            </div>
          </div>

          <hr class="rs-divider">

          <div class="rs-ring-wrap">
            <div class="rs-ring">
              <svg viewBox="0 0 36 36">
                <circle class="rs-ring-bg" fill="none" stroke-width="3.8" cx="18" cy="18" r="15.9155" />
                <circle fill="none" stroke-width="3.8" cx="18" cy="18" r="15.9155"
                  stroke="#2563eb"
                  stroke-dasharray="{{ $verifyRate }}, 100"
                  stroke-linecap="round" />
              </svg>
              <span class="rs-ring-pct">{{ $verifyRate }}%</span>
            </div>
            <div class="rs-ring-info">
              <p class="rs-ring-label">Tingkat Verifikasi</p>
              <p class="rs-ring-desc">{{ $verified }} dari {{ $totalSubmissions }} formulir terkirim</p>
            </div>
          </div>
        </div>
      </div>

      {{-- Info Detail --}}
      <div class="rs-card">
        <div class="rs-card-head">
          <h3 class="rs-card-title">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.1rem;height:1.1rem;color:#2563eb;"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
            Informasi Siswa
          </h3>
        </div>
        <div class="rs-card-body">
          <div class="rs-info-item">
            <span class="rs-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
              Nama
            </span>
            <span class="rs-info-value">{{ $siswa->name }}</span>
          </div>
          <div class="rs-info-item">
            <span class="rs-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" /></svg>
              NISN
            </span>
            <span class="rs-info-value">{{ $siswa->nisn ?? '-' }}</span>
          </div>
          <div class="rs-info-item">
            <span class="rs-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
              Kelas
            </span>
            <span class="rs-info-value">{{ $siswa->kelas?->nama ?? '-' }}</span>
          </div>
          <div class="rs-info-item">
            <span class="rs-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" /></svg>
              Agama
            </span>
            <span class="rs-info-value">{{ ucfirst($siswa->agama ?? '-') }}</span>
          </div>

          <hr class="rs-divider">

          @php
            $barTotal = max($totalSubmissions, 1);
            $vPct = round(($verified / $barTotal) * 100);
            $pPct = round(($pending / $barTotal) * 100);
            $rPct = round(($rejected / $barTotal) * 100);
          @endphp

          <p style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; margin-bottom:.5rem;" class="dark:!text-gray-400">Distribusi Status</p>

          <div style="display:flex; gap:.25rem; height:.5rem; border-radius:.25rem; overflow:hidden; background:rgba(100,100,100,.08);" class="dark:!bg-white/10">
            @if($vPct > 0)<div style="width:{{ $vPct }}%; background:#10b981; border-radius:.25rem;"></div>@endif
            @if($pPct > 0)<div style="width:{{ $pPct }}%; background:#f59e0b; border-radius:.25rem;"></div>@endif
            @if($rPct > 0)<div style="width:{{ $rPct }}%; background:#ef4444; border-radius:.25rem;"></div>@endif
          </div>
          <div style="display:flex; gap:1rem; margin-top:.5rem; flex-wrap:wrap;">
            <span style="display:flex; align-items:center; gap:.25rem; font-size:.7rem; font-weight:500; color:#6b7280;" class="dark:!text-gray-400">
              <span style="width:.5rem; height:.5rem; border-radius:50%; background:#10b981; flex-shrink:0;"></span> Verifikasi {{ $vPct }}%
            </span>
            <span style="display:flex; align-items:center; gap:.25rem; font-size:.7rem; font-weight:500; color:#6b7280;" class="dark:!text-gray-400">
              <span style="width:.5rem; height:.5rem; border-radius:50%; background:#f59e0b; flex-shrink:0;"></span> Menunggu {{ $pPct }}%
            </span>
            <span style="display:flex; align-items:center; gap:.25rem; font-size:.7rem; font-weight:500; color:#6b7280;" class="dark:!text-gray-400">
              <span style="width:.5rem; height:.5rem; border-radius:50%; background:#ef4444; flex-shrink:0;"></span> Ditolak {{ $rPct }}%
            </span>
          </div>
        </div>
      </div>

    </div>

    {{-- ═══ Daily Progress Grid ═══ --}}
    <div class="rs-grid-card">
      <div class="rs-grid-head">
        <h3 class="rs-grid-title">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.1rem;height:1.1rem;color:#2563eb;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
          Riwayat Laporan Harian
        </h3>
        <div style="display:flex; align-items:center; gap:.75rem;">
          <span class="rs-grid-count">{{ count($dailyGrid) }} hari</span>
          <a href="{{ route('guru.rekap-siswa.export-detail', $siswa->id) }}" class="rs-export-btn" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
            Export Excel
          </a>
        </div>
      </div>
      <div class="rs-grid-wrap">
        <table class="rs-grid-table">
          <thead>
            <tr>
              <th style="width:3.5rem;">Hari</th>
              <th>Tanggal</th>
              <th style="text-align:center;">Status</th>
              <th style="text-align:center;">Dikirim</th>
              <th style="text-align:center;">Diverifikasi</th>
              <th>Catatan Guru</th>
            </tr>
          </thead>
          <tbody>
            @forelse($dailyGrid as $day)
              <tr>
                <td style="font-weight:700; color:#2563eb;">{{ $day['hari'] }}</td>
                <td>{{ $day['tanggal'] }}</td>
                <td style="text-align:center;">
                  @switch($day['status'])
                    @case('verified')
                      <span class="rs-day-badge rs-day-verified">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        Terverifikasi
                      </span>
                      @break
                    @case('pending')
                      <span class="rs-day-badge rs-day-pending">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5" /></svg>
                        Menunggu
                      </span>
                      @break
                    @case('rejected')
                      <span class="rs-day-badge rs-day-rejected">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        Ditolak
                      </span>
                      @break
                    @default
                      <span class="rs-day-badge rs-day-belum">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" /></svg>
                        Belum Lapor
                      </span>
                  @endswitch
                </td>
                <td style="text-align:center; font-size:.75rem; color:#6b7280;">{{ $day['submitted_at'] ?? '-' }}</td>
                <td style="text-align:center; font-size:.75rem; color:#6b7280;">{{ $day['verified_at'] ?? '-' }}</td>
                <td>
                  @if($day['catatan'])
                    <span class="rs-catatan">{{ $day['catatan'] }}</span>
                  @else
                    <span style="font-size:.75rem; color:#94a3b8;">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align:center; padding:2rem; color:#94a3b8;">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:2.5rem;height:2.5rem;margin:0 auto .5rem;opacity:.5;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                  <p>Ramadhan belum dimulai.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
</x-filament-panels::page>
