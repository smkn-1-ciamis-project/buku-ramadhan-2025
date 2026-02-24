<x-filament-panels::page>
  <style>
    /* ─── Rekap Kelas Detail ─── */
    .rk-wrap { display:flex; flex-direction:column; gap:1.25rem; }

    /* Hero Banner */
    .rk-hero {
      position:relative; overflow:hidden; border-radius:1rem;
      background:linear-gradient(160deg, #0f172a 0%, #1e3a5f 35%, #2563eb 100%);
      padding:1.75rem 2rem; color:#fff;
      box-shadow:0 10px 25px -5px rgba(37,99,235,.3);
    }
    .rk-hero::before {
      content:''; position:absolute; top:-3rem; right:-3rem;
      width:14rem; height:14rem; border-radius:50%;
      background:radial-gradient(circle, rgba(37,99,235,.25) 0%, transparent 70%);
      filter:blur(40px); pointer-events:none;
    }
    .rk-hero-content { position:relative; z-index:2; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .rk-hero-left { flex:1; min-width:0; }
    .rk-hero-kelas { font-size:1.65rem; font-weight:800; letter-spacing:-.02em; margin:0; }
    .rk-hero-sub { font-size:.825rem; color:#93c5fd; margin-top:.25rem; display:flex; align-items:center; gap:.375rem; }
    .rk-hero-sub svg { width:1rem; height:1rem; }
    .rk-hero-right { display:flex; align-items:center; gap:.75rem; }
    .rk-hero-badge {
      display:flex; align-items:center; gap:.5rem;
      background:rgba(255,255,255,.1); backdrop-filter:blur(12px);
      border:1px solid rgba(255,255,255,.15); border-radius:.875rem;
      padding:.75rem 1.25rem; white-space:nowrap;
    }
    .rk-hero-badge-icon { font-size:1.5rem; line-height:1; filter:drop-shadow(0 0 6px rgba(250,204,21,.4)); }
    .rk-hero-badge-label { font-size:.65rem; text-transform:uppercase; letter-spacing:.06em; color:#93c5fd; }
    .rk-hero-badge-value { font-size:1.35rem; font-weight:800; line-height:1.1; }

    /* Stats Grid */
    .rk-stats { display:grid; grid-template-columns:repeat(5,1fr); gap:.875rem; }
    @media(max-width:1024px) { .rk-stats { grid-template-columns:repeat(3,1fr); } }
    @media(max-width:640px) { .rk-stats { grid-template-columns:repeat(2,1fr); } }

    .rk-stat {
      border-radius:1rem; padding:1.125rem 1rem; display:flex; align-items:center; gap:.75rem;
      border:1px solid transparent; transition:all .2s;
      background:#fff;
    }
    .dark .rk-stat { background:rgba(30,41,59,.6); }
    .rk-stat:hover { transform:translateY(-2px); box-shadow:0 4px 12px -2px rgba(0,0,0,.08); }
    .dark .rk-stat:hover { box-shadow:0 4px 12px -2px rgba(0,0,0,.3); }
    .rk-stat-icon {
      width:2.75rem; height:2.75rem; border-radius:.75rem;
      display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .rk-stat-icon svg { width:1.2rem; height:1.2rem; color:#fff; }
    .rk-stat-label { font-size:.65rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; color:#6b7280; }
    .dark .rk-stat-label { color:#9ca3af; }
    .rk-stat-value { font-size:1.35rem; font-weight:700; color:#1e293b; }
    .dark .rk-stat-value { color:#f1f5f9; }

    .rk-stat-blue { border-color:rgba(37,99,235,.15); background:rgba(37,99,235,.04); }
    .dark .rk-stat-blue { border-color:rgba(37,99,235,.25); background:rgba(37,99,235,.1); }
    .rk-stat-blue .rk-stat-icon { background:rgba(37,99,235,.85); }

    .rk-stat-green { border-color:rgba(16,185,129,.15); background:rgba(16,185,129,.04); }
    .dark .rk-stat-green { border-color:rgba(16,185,129,.25); background:rgba(16,185,129,.1); }
    .rk-stat-green .rk-stat-icon { background:rgba(16,185,129,.85); }

    .rk-stat-yellow { border-color:rgba(245,158,11,.15); background:rgba(245,158,11,.04); }
    .dark .rk-stat-yellow { border-color:rgba(245,158,11,.25); background:rgba(245,158,11,.1); }
    .rk-stat-yellow .rk-stat-icon { background:rgba(245,158,11,.85); }

    .rk-stat-red { border-color:rgba(239,68,68,.15); background:rgba(239,68,68,.04); }
    .dark .rk-stat-red { border-color:rgba(239,68,68,.25); background:rgba(239,68,68,.1); }
    .rk-stat-red .rk-stat-icon { background:rgba(239,68,68,.85); }

    .rk-stat-purple { border-color:rgba(139,92,246,.15); background:rgba(139,92,246,.04); }
    .dark .rk-stat-purple { border-color:rgba(139,92,246,.25); background:rgba(139,92,246,.1); }
    .rk-stat-purple .rk-stat-icon { background:rgba(139,92,246,.85); }

    /* Mid row: 2 columns */
    .rk-mid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; align-items:stretch; }
    @media(max-width:768px) { .rk-mid { grid-template-columns:1fr; } }

    /* Card */
    .rk-card {
      border-radius:1rem; overflow:hidden;
      border:1px solid rgba(100,100,100,.12);
      background:#fff; display:flex; flex-direction:column;
    }
    .dark .rk-card { border-color:rgba(100,100,100,.25); background:rgba(30,41,59,.6); }

    .rk-card-head {
      padding:.875rem 1.25rem; display:flex; justify-content:space-between; align-items:center;
      border-bottom:1px solid rgba(100,100,100,.08);
      background:rgba(100,100,100,.02);
    }
    .dark .rk-card-head { background:rgba(0,0,0,.12); border-color:rgba(100,100,100,.18); }
    .rk-card-title { font-size:.85rem; font-weight:700; display:flex; align-items:center; gap:.5rem; color:#1e293b; }
    .dark .rk-card-title { color:#f1f5f9; }
    .rk-card-body { padding:1.25rem; flex:1; }

    /* Compliance ring */
    .rk-ring-wrap { display:flex; align-items:center; gap:1.25rem; }
    .rk-ring { position:relative; width:5rem; height:5rem; flex-shrink:0; }
    .rk-ring svg { width:5rem; height:5rem; transform:rotate(-90deg); }
    .rk-ring-bg { stroke:rgba(100,100,100,.1); }
    .dark .rk-ring-bg { stroke:rgba(100,100,100,.25); }
    .rk-ring-pct {
      position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
      font-size:1rem; font-weight:800; color:#1e293b;
    }
    .dark .rk-ring-pct { color:#f1f5f9; }
    .rk-ring-info { flex:1; }
    .rk-ring-label { font-size:.75rem; font-weight:600; color:#6b7280; }
    .dark .rk-ring-label { color:#9ca3af; }
    .rk-ring-desc { font-size:.7rem; color:#94a3b8; margin-top:.125rem; }
    .dark .rk-ring-desc { color:#64748b; }

    .rk-divider { border:none; border-top:1px solid rgba(100,100,100,.08); margin:.875rem 0; }
    .dark .rk-divider { border-color:rgba(100,100,100,.18); }

    /* Info Items */
    .rk-info-item {
      display:flex; align-items:center; justify-content:space-between;
      padding:.5rem 0;
    }
    .rk-info-item + .rk-info-item { border-top:1px solid rgba(100,100,100,.06); }
    .dark .rk-info-item + .rk-info-item { border-color:rgba(100,100,100,.12); }
    .rk-info-label { font-size:.8rem; color:#6b7280; display:flex; align-items:center; gap:.375rem; }
    .dark .rk-info-label { color:#9ca3af; }
    .rk-info-label svg { width:.875rem; height:.875rem; }
    .rk-info-value { font-size:.85rem; font-weight:600; color:#1e293b; }
    .dark .rk-info-value { color:#f1f5f9; }

    /* Progress Table */
    .rk-table-card {
      border-radius:1rem; overflow:hidden;
      border:1px solid rgba(100,100,100,.12);
      background:#fff;
    }
    .dark .rk-table-card { border-color:rgba(100,100,100,.25); background:rgba(30,41,59,.6); }

    .rk-table-head {
      padding:.875rem 1.25rem; display:flex; justify-content:space-between; align-items:center;
      border-bottom:1px solid rgba(100,100,100,.08);
      background:rgba(100,100,100,.02);
    }
    .dark .rk-table-head { background:rgba(0,0,0,.12); border-color:rgba(100,100,100,.18); }
    .rk-table-title { font-size:.85rem; font-weight:700; display:flex; align-items:center; gap:.5rem; color:#1e293b; }
    .dark .rk-table-title { color:#f1f5f9; }
    .rk-table-count {
      font-size:.7rem; font-weight:600; padding:.2rem .5rem; border-radius:.375rem;
      background:rgba(37,99,235,.08); color:#2563eb;
    }
    .dark .rk-table-count { background:rgba(37,99,235,.2); color:#93c5fd; }

    .rk-table-wrap { overflow-x:auto; }
    .rk-table { width:100%; border-collapse:collapse; font-size:.8rem; }
    .rk-table th {
      text-align:left; padding:.625rem 1rem; font-weight:600; font-size:.7rem;
      text-transform:uppercase; letter-spacing:.05em;
      border-bottom:2px solid rgba(100,100,100,.08);
      color:#6b7280; background:rgba(100,100,100,.02);
    }
    .dark .rk-table th { border-color:rgba(100,100,100,.18); color:#9ca3af; background:transparent; }
    .rk-table td {
      padding:.625rem 1rem; border-bottom:1px solid rgba(100,100,100,.05);
      color:#374151;
    }
    .dark .rk-table td { border-color:rgba(100,100,100,.1); color:#d1d5db; }
    .rk-table tr:hover td { background:rgba(100,100,100,.03); }
    .dark .rk-table tr:hover td { background:rgba(100,100,100,.06); }

    .rk-table .rk-name { font-weight:600; color:#1e293b; }
    .dark .rk-table .rk-name { color:#f1f5f9; }
    .rk-table .rk-nisn { font-size:.75rem; color:#94a3b8; font-family:ui-monospace, monospace; }

    /* Badges */
    .rk-badge {
      display:inline-flex; align-items:center; justify-content:center; padding:.175rem .5rem;
      border-radius:.375rem; font-size:.7rem; font-weight:600; min-width:1.5rem;
    }
    .rk-badge-blue { background:rgba(37,99,235,.1); color:#2563eb; }
    .dark .rk-badge-blue { background:rgba(37,99,235,.2); color:#93c5fd; }
    .rk-badge-green { background:rgba(16,185,129,.1); color:#059669; }
    .dark .rk-badge-green { background:rgba(16,185,129,.2); color:#34d399; }
    .rk-badge-yellow { background:rgba(245,158,11,.1); color:#d97706; }
    .dark .rk-badge-yellow { background:rgba(245,158,11,.2); color:#fbbf24; }
    .rk-badge-red { background:rgba(239,68,68,.1); color:#dc2626; }
    .dark .rk-badge-red { background:rgba(239,68,68,.2); color:#fca5a5; }
    .rk-badge-gray { background:rgba(100,116,139,.1); color:#64748b; }
    .dark .rk-badge-gray { background:rgba(100,116,139,.2); color:#94a3b8; }

    /* Progress bar in table */
    .rk-progress-bar { height:.375rem; border-radius:.25rem; background:rgba(100,100,100,.08); overflow:hidden; min-width:4rem; }
    .dark .rk-progress-bar { background:rgba(100,100,100,.2); }
    .rk-progress-fill { height:100%; border-radius:.25rem; transition:width .5s ease; }

    /* Empty state */
    .rk-empty { text-align:center; padding:2rem 1rem; color:#94a3b8; }
    .dark .rk-empty { color:#64748b; }
    .rk-empty svg { width:2.5rem; height:2.5rem; margin:0 auto .5rem; opacity:.5; }

    /* Responsif */
    @media(max-width:640px) {
      .rk-hero { padding:1.25rem 1rem; }
      .rk-hero-kelas { font-size:1.25rem; }
      .rk-hero-content { flex-direction:column; align-items:flex-start; }
      .rk-stats { grid-template-columns:1fr 1fr; }
      .rk-table th, .rk-table td { padding:.5rem .625rem; font-size:.725rem; }
    }
    @media(max-width:380px) {
      .rk-stats { grid-template-columns:1fr; }
    }
  </style>

  <div class="rk-wrap">

    {{-- ═══ Hero Banner ═══ --}}
    <div class="rk-hero">
      <div class="rk-hero-content">
        <div class="rk-hero-left">
          <h1 class="rk-hero-kelas">{{ $kelas->nama }}</h1>
          <p class="rk-hero-sub">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
            Wali Kelas: {{ $kelas->wali?->name ?? 'Belum ditentukan' }}
          </p>
        </div>
        <div class="rk-hero-right">
          <div class="rk-hero-badge">
            <span class="rk-hero-badge-icon">🌙</span>
            <div>
              <p class="rk-hero-badge-label">Ramadhan</p>
              <p class="rk-hero-badge-value">{{ $hariKe > 0 ? "Hari ke-{$hariKe}" : 'Belum dimulai' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ═══ Stat Cards ═══ --}}
    <div class="rk-stats">
      {{-- Total Siswa --}}
      <div class="rk-stat rk-stat-blue">
        <div class="rk-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>
        </div>
        <div>
          <p class="rk-stat-label">Total Siswa</p>
          <p class="rk-stat-value">{{ $totalSiswa }}</p>
        </div>
      </div>

      {{-- Total Formulir --}}
      <div class="rk-stat rk-stat-blue">
        <div class="rk-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
        </div>
        <div>
          <p class="rk-stat-label">Total Formulir</p>
          <p class="rk-stat-value">{{ $totalSubmissions }}</p>
        </div>
      </div>

      {{-- Verified --}}
      <div class="rk-stat rk-stat-green">
        <div class="rk-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
          <p class="rk-stat-label">Terverifikasi</p>
          <p class="rk-stat-value">{{ $verified }}</p>
        </div>
      </div>

      {{-- Pending --}}
      <div class="rk-stat rk-stat-yellow">
        <div class="rk-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
          <p class="rk-stat-label">Menunggu</p>
          <p class="rk-stat-value">{{ $pending }}</p>
        </div>
      </div>

      {{-- Rejected --}}
      <div class="rk-stat rk-stat-red">
        <div class="rk-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
          <p class="rk-stat-label">Ditolak</p>
          <p class="rk-stat-value">{{ $rejected }}</p>
        </div>
      </div>
    </div>

    {{-- ═══ Mid Row: Kepatuhan + Info ═══ --}}
    <div class="rk-mid">

      {{-- Kepatuhan & Verifikasi --}}
      <div class="rk-card">
        <div class="rk-card-head">
          <h3 class="rk-card-title">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.1rem;height:1.1rem;color:#2563eb;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
            Tingkat Kepatuhan
          </h3>
        </div>
        <div class="rk-card-body">
          {{-- Compliance ring --}}
          <div class="rk-ring-wrap">
            <div class="rk-ring">
              <svg viewBox="0 0 36 36">
                <circle class="rk-ring-bg" fill="none" stroke-width="3.8" cx="18" cy="18" r="15.9155" />
                <circle fill="none" stroke-width="3.8" cx="18" cy="18" r="15.9155"
                  stroke="{{ $complianceRate >= 80 ? '#10b981' : ($complianceRate >= 50 ? '#f59e0b' : '#ef4444') }}"
                  stroke-dasharray="{{ $complianceRate }}, 100"
                  stroke-linecap="round" />
              </svg>
              <span class="rk-ring-pct">{{ $complianceRate }}%</span>
            </div>
            <div class="rk-ring-info">
              <p class="rk-ring-label">Kepatuhan Pengisian</p>
              <p class="rk-ring-desc">{{ $totalSubmissions }} dari {{ $totalSiswa * max($hariKe, 1) }} formulir yang diharapkan</p>
            </div>
          </div>

          <hr class="rk-divider">

          {{-- Verify rate ring --}}
          <div class="rk-ring-wrap">
            <div class="rk-ring">
              <svg viewBox="0 0 36 36">
                <circle class="rk-ring-bg" fill="none" stroke-width="3.8" cx="18" cy="18" r="15.9155" />
                <circle fill="none" stroke-width="3.8" cx="18" cy="18" r="15.9155"
                  stroke="#2563eb"
                  stroke-dasharray="{{ $verifyRate }}, 100"
                  stroke-linecap="round" />
              </svg>
              <span class="rk-ring-pct">{{ $verifyRate }}%</span>
            </div>
            <div class="rk-ring-info">
              <p class="rk-ring-label">Tingkat Verifikasi</p>
              <p class="rk-ring-desc">{{ $verified }} dari {{ $totalSubmissions }} formulir terkirim</p>
            </div>
          </div>
        </div>
      </div>

      {{-- Quick Info --}}
      <div class="rk-card">
        <div class="rk-card-head">
          <h3 class="rk-card-title">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.1rem;height:1.1rem;color:#2563eb;"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
            Informasi Detail
          </h3>
        </div>
        <div class="rk-card-body">
          <div class="rk-info-item">
            <span class="rk-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
              Kelas
            </span>
            <span class="rk-info-value">{{ $kelas->nama }}</span>
          </div>
          <div class="rk-info-item">
            <span class="rk-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
              Wali Kelas
            </span>
            <span class="rk-info-value">{{ $kelas->wali?->name ?? '-' }}</span>
          </div>
          <div class="rk-info-item">
            <span class="rk-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>
              Jumlah Siswa
            </span>
            <span class="rk-info-value">{{ $totalSiswa }} siswa</span>
          </div>
          <div class="rk-info-item">
            <span class="rk-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
              Submit Hari Ini
            </span>
            <span class="rk-info-value">{{ $todaySubmit }}/{{ $totalSiswa }}</span>
          </div>

          <hr class="rk-divider">

          {{-- Status breakdown bars --}}
          <p style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; margin-bottom:.5rem;" class="dark:!text-gray-400">Distribusi Status</p>

          @php
            $barTotal = max($totalSubmissions, 1);
            $vPct = round(($verified / $barTotal) * 100);
            $pPct = round(($pending / $barTotal) * 100);
            $rPct = round(($rejected / $barTotal) * 100);
          @endphp

          <div style="display:flex; gap:.25rem; height:.5rem; border-radius:.25rem; overflow:hidden; background:rgba(100,100,100,.08);" class="dark:!bg-white/10">
            @if($vPct > 0)<div style="width:{{ $vPct }}%; background:#10b981; border-radius:.25rem;"></div>@endif
            @if($pPct > 0)<div style="width:{{ $pPct }}%; background:#f59e0b; border-radius:.25rem;"></div>@endif
            @if($rPct > 0)<div style="width:{{ $rPct }}%; background:#ef4444; border-radius:.25rem;"></div>@endif
          </div>
          <div style="display:flex; gap:1rem; margin-top:.5rem; flex-wrap:wrap;">
            <span style="display:flex; align-items:center; gap:.25rem; font-size:.7rem; font-weight:500; color:#6b7280;" class="dark:!text-gray-400">
              <span style="width:.5rem; height:.5rem; border-radius:50%; background:#10b981; flex-shrink:0;"></span> Terverifikasi {{ $vPct }}%
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

    {{-- ═══ Progress Table ═══ --}}
    <div class="rk-table-card">
      <div class="rk-table-head">
        <h3 class="rk-table-title">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.1rem;height:1.1rem;color:#2563eb;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M10.875 12c-.621 0-1.125.504-1.125 1.125M12 12c.621 0 1.125.504 1.125 1.125m0 0v1.5c0 .621-.504 1.125-1.125 1.125M12 13.125c-.621 0-1.125.504-1.125 1.125M12 13.125c.621 0 1.125.504 1.125 1.125m0 0c0 .621-.504 1.125-1.125 1.125m0 0c-.621 0-1.125.504-1.125 1.125" /></svg>
          Progress Per Siswa
        </h3>
        <span class="rk-table-count">{{ $totalSiswa }} siswa</span>
      </div>
      <div class="rk-table-wrap">
        <table class="rk-table">
          <thead>
            <tr>
              <th style="width:2.5rem;">No</th>
              <th>Nama Siswa</th>
              <th>NISN</th>
              <th style="text-align:center;">Total</th>
              <th style="text-align:center;">Terverifikasi</th>
              <th style="text-align:center;">Menunggu</th>
              <th style="text-align:center;">Ditolak</th>
              <th style="text-align:center; min-width:6rem;">Kepatuhan</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($siswaProgress as $index => $siswa)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td class="rk-name">{{ $siswa['name'] }}</td>
                <td class="rk-nisn">{{ $siswa['nisn'] }}</td>
                <td style="text-align:center;"><span class="rk-badge rk-badge-blue">{{ $siswa['total'] }}</span></td>
                <td style="text-align:center;"><span class="rk-badge rk-badge-green">{{ $siswa['verified'] }}</span></td>
                <td style="text-align:center;">
                  <span class="rk-badge {{ $siswa['pending'] > 0 ? 'rk-badge-yellow' : 'rk-badge-gray' }}">{{ $siswa['pending'] }}</span>
                </td>
                <td style="text-align:center;">
                  <span class="rk-badge {{ $siswa['rejected'] > 0 ? 'rk-badge-red' : 'rk-badge-gray' }}">{{ $siswa['rejected'] }}</span>
                </td>
                <td style="text-align:center;">
                  <div style="display:flex; align-items:center; gap:.375rem; justify-content:center;">
                    <div class="rk-progress-bar" style="flex:1;">
                      <div class="rk-progress-fill" style="width:{{ $siswa['rate'] }}%; background:{{ $siswa['rate'] >= 80 ? '#10b981' : ($siswa['rate'] >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                    </div>
                    <span style="font-size:.7rem; font-weight:600; min-width:2rem; color:#6b7280;" class="dark:!text-gray-400">{{ $siswa['rate'] }}%</span>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="rk-empty">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>
                  <p>Tidak ada data siswa di kelas ini.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
</x-filament-panels::page>
