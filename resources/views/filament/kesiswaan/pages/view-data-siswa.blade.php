<x-filament-panels::page>
  <style>
    /* ─── View Data Siswa ─── */
    .ds-wrap { display:flex; flex-direction:column; gap:1.25rem; }

    /* Hero Banner */
    .ds-hero {
      position:relative; overflow:hidden; border-radius:1rem;
      background:linear-gradient(160deg, #0f172a 0%, #1e3a5f 35%, #2563eb 100%);
      padding:1.75rem 2rem; color:#fff;
      box-shadow:0 10px 25px -5px rgba(37,99,235,.3);
    }
    .ds-hero::before {
      content:''; position:absolute; top:-3rem; right:-3rem;
      width:14rem; height:14rem; border-radius:50%;
      background:radial-gradient(circle, rgba(37,99,235,.25) 0%, transparent 70%);
      filter:blur(40px); pointer-events:none;
    }
    .ds-hero-content { position:relative; z-index:2; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .ds-hero-left { flex:1; min-width:0; }
    .ds-hero-name { font-size:1.65rem; font-weight:800; letter-spacing:-.02em; margin:0; }
    .ds-hero-meta { font-size:.825rem; color:#93c5fd; margin-top:.25rem; display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
    .ds-hero-meta svg { width:1rem; height:1rem; }
    .ds-hero-tag {
      display:inline-flex; align-items:center; gap:.25rem;
      background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.15);
      border-radius:.5rem; padding:.2rem .6rem; font-size:.7rem; font-weight:600;
      backdrop-filter:blur(8px);
    }
    .ds-hero-right { display:flex; align-items:center; gap:.75rem; }
    .ds-hero-badge {
      display:flex; align-items:center; gap:.5rem;
      background:rgba(255,255,255,.1); backdrop-filter:blur(12px);
      border:1px solid rgba(255,255,255,.15); border-radius:.875rem;
      padding:.75rem 1.25rem; white-space:nowrap;
    }
    .ds-hero-badge-icon { font-size:1.5rem; line-height:1; filter:drop-shadow(0 0 6px rgba(250,204,21,.4)); }
    .ds-hero-badge-label { font-size:.65rem; text-transform:uppercase; letter-spacing:.06em; color:#93c5fd; }
    .ds-hero-badge-value { font-size:1.35rem; font-weight:800; line-height:1.1; }

    /* Stats Grid */
    .ds-stats { display:grid; grid-template-columns:repeat(5,1fr); gap:.875rem; }
    @media(max-width:1024px) { .ds-stats { grid-template-columns:repeat(3,1fr); } }
    @media(max-width:640px) { .ds-stats { grid-template-columns:repeat(2,1fr); } }

    .ds-stat {
      border-radius:1rem; padding:1.125rem 1rem; display:flex; align-items:center; gap:.75rem;
      border:1px solid transparent; transition:all .2s; background:#fff;
    }
    .dark .ds-stat { background:rgba(30,41,59,.6); }
    .ds-stat:hover { transform:translateY(-2px); box-shadow:0 4px 12px -2px rgba(0,0,0,.08); }
    .dark .ds-stat:hover { box-shadow:0 4px 12px -2px rgba(0,0,0,.3); }
    .ds-stat-icon {
      width:2.75rem; height:2.75rem; border-radius:.75rem;
      display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .ds-stat-icon svg { width:1.2rem; height:1.2rem; color:#fff; }
    .ds-stat-label { font-size:.65rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; color:#6b7280; }
    .dark .ds-stat-label { color:#9ca3af; }
    .ds-stat-value { font-size:1.35rem; font-weight:700; color:#1e293b; }
    .dark .ds-stat-value { color:#f1f5f9; }

    .ds-stat-blue { border-color:rgba(37,99,235,.15); background:rgba(37,99,235,.04); }
    .dark .ds-stat-blue { border-color:rgba(37,99,235,.25); background:rgba(37,99,235,.1); }
    .ds-stat-blue .ds-stat-icon { background:rgba(37,99,235,.85); }

    .ds-stat-green { border-color:rgba(16,185,129,.15); background:rgba(16,185,129,.04); }
    .dark .ds-stat-green { border-color:rgba(16,185,129,.25); background:rgba(16,185,129,.1); }
    .ds-stat-green .ds-stat-icon { background:rgba(16,185,129,.85); }

    .ds-stat-yellow { border-color:rgba(245,158,11,.15); background:rgba(245,158,11,.04); }
    .dark .ds-stat-yellow { border-color:rgba(245,158,11,.25); background:rgba(245,158,11,.1); }
    .ds-stat-yellow .ds-stat-icon { background:rgba(245,158,11,.85); }

    .ds-stat-red { border-color:rgba(239,68,68,.15); background:rgba(239,68,68,.04); }
    .dark .ds-stat-red { border-color:rgba(239,68,68,.25); background:rgba(239,68,68,.1); }
    .ds-stat-red .ds-stat-icon { background:rgba(239,68,68,.85); }

    .ds-stat-purple { border-color:rgba(139,92,246,.15); background:rgba(139,92,246,.04); }
    .dark .ds-stat-purple { border-color:rgba(139,92,246,.25); background:rgba(139,92,246,.1); }
    .ds-stat-purple .ds-stat-icon { background:rgba(139,92,246,.85); }

    /* Mid row: 2 columns */
    .ds-mid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; align-items:stretch; }
    @media(max-width:768px) { .ds-mid { grid-template-columns:1fr; } }

    /* Card */
    .ds-card {
      border-radius:1rem; overflow:hidden;
      border:1px solid rgba(100,100,100,.12);
      background:#fff; display:flex; flex-direction:column;
    }
    .dark .ds-card { border-color:rgba(100,100,100,.25); background:rgba(30,41,59,.6); }

    .ds-card-head {
      padding:.875rem 1.25rem; display:flex; justify-content:space-between; align-items:center;
      border-bottom:1px solid rgba(100,100,100,.08);
      background:rgba(100,100,100,.02);
    }
    .dark .ds-card-head { background:rgba(0,0,0,.12); border-color:rgba(100,100,100,.18); }
    .ds-card-title { font-size:.85rem; font-weight:700; display:flex; align-items:center; gap:.5rem; color:#1e293b; }
    .dark .ds-card-title { color:#f1f5f9; }
    .ds-card-body { padding:1.25rem; flex:1; }

    /* Ring */
    .ds-ring-wrap { display:flex; align-items:center; gap:1.25rem; }
    .ds-ring { position:relative; width:5rem; height:5rem; flex-shrink:0; }
    .ds-ring svg { width:5rem; height:5rem; transform:rotate(-90deg); }
    .ds-ring-bg { stroke:rgba(100,100,100,.1); }
    .dark .ds-ring-bg { stroke:rgba(100,100,100,.25); }
    .ds-ring-pct {
      position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
      font-size:1rem; font-weight:800; color:#1e293b;
    }
    .dark .ds-ring-pct { color:#f1f5f9; }
    .ds-ring-info { flex:1; }
    .ds-ring-label { font-size:.75rem; font-weight:600; color:#6b7280; }
    .dark .ds-ring-label { color:#9ca3af; }
    .ds-ring-desc { font-size:.7rem; color:#94a3b8; margin-top:.125rem; }
    .dark .ds-ring-desc { color:#64748b; }

    .ds-divider { border:none; border-top:1px solid rgba(100,100,100,.08); margin:.875rem 0; }
    .dark .ds-divider { border-color:rgba(100,100,100,.18); }

    /* Info Items */
    .ds-info-item {
      display:flex; align-items:center; justify-content:space-between;
      padding:.5rem 0;
    }
    .ds-info-item + .ds-info-item { border-top:1px solid rgba(100,100,100,.06); }
    .dark .ds-info-item + .ds-info-item { border-color:rgba(100,100,100,.12); }
    .ds-info-label { font-size:.8rem; color:#6b7280; display:flex; align-items:center; gap:.375rem; }
    .dark .ds-info-label { color:#9ca3af; }
    .ds-info-label svg { width:.875rem; height:.875rem; }
    .ds-info-value { font-size:.85rem; font-weight:600; color:#1e293b; }
    .dark .ds-info-value { color:#f1f5f9; }

    /* Calendar grid (7-column, matches validasi page) */
    .ds-cal-header { font-size:10px; font-weight:600; text-align:center; color:#9ca3af; padding:4px 0; }
    .dark .ds-cal-header { color:#6b7280; }

    /* Legend dots */
    .leg-dot { width:.625rem; height:.625rem; border-radius:.25rem; flex-shrink:0; }
    .leg-dot-verified  { background:#dcfce7; border:1px solid #86efac; }
    .leg-dot-pending   { background:#fef3c7; border:1px solid #fcd34d; }
    .leg-dot-rejected  { background:#fee2e2; border:1px solid #fca5a5; }
    .leg-dot-empty     { background:#f8fafc; border:1px solid #cbd5e1; }
    .leg-dot-future    { background:#f9fafb; border:1px solid #e5e7eb; }
    .dark .leg-dot-verified  { background:#14532d; border-color:#22c55e; }
    .dark .leg-dot-pending   { background:#78350f; border-color:#d97706; }
    .dark .leg-dot-rejected  { background:#7f1d1d; border-color:#ef4444; }
    .dark .leg-dot-empty     { background:#1f2937; border-color:#4b5563; }
    .dark .leg-dot-future    { background:#374151; border-color:#4b5563; }

    /* Table */
    .ds-table-card {
      border-radius:1rem; overflow:hidden;
      border:1px solid rgba(100,100,100,.12); background:#fff;
    }
    .dark .ds-table-card { border-color:rgba(100,100,100,.25); background:rgba(30,41,59,.6); }
    .ds-table-head {
      padding:.875rem 1.25rem; display:flex; justify-content:space-between; align-items:center;
      border-bottom:1px solid rgba(100,100,100,.08); background:rgba(100,100,100,.02);
    }
    .dark .ds-table-head { background:rgba(0,0,0,.12); border-color:rgba(100,100,100,.18); }
    .ds-table-title { font-size:.85rem; font-weight:700; display:flex; align-items:center; gap:.5rem; color:#1e293b; }
    .dark .ds-table-title { color:#f1f5f9; }
    .ds-table-count {
      font-size:.7rem; font-weight:600; padding:.2rem .5rem; border-radius:.375rem;
      background:rgba(37,99,235,.08); color:#2563eb;
    }
    .dark .ds-table-count { background:rgba(37,99,235,.2); color:#93c5fd; }
    .ds-table-wrap { overflow-x:auto; }
    .ds-table { width:100%; border-collapse:collapse; font-size:.8rem; }
    .ds-table th {
      text-align:left; padding:.625rem 1rem; font-weight:600; font-size:.7rem;
      text-transform:uppercase; letter-spacing:.05em;
      border-bottom:2px solid rgba(100,100,100,.08); color:#6b7280; background:rgba(100,100,100,.02);
    }
    .dark .ds-table th { border-color:rgba(100,100,100,.18); color:#9ca3af; background:transparent; }
    .ds-table td {
      padding:.625rem 1rem; border-bottom:1px solid rgba(100,100,100,.05); color:#374151;
    }
    .dark .ds-table td { border-color:rgba(100,100,100,.1); color:#d1d5db; }
    .ds-table tr:hover td { background:rgba(100,100,100,.03); }
    .dark .ds-table tr:hover td { background:rgba(100,100,100,.06); }

    /* Badges */
    .ds-badge {
      display:inline-flex; align-items:center; justify-content:center; padding:.175rem .5rem;
      border-radius:.375rem; font-size:.7rem; font-weight:600; min-width:1.5rem;
    }
    .ds-badge-green { background:rgba(16,185,129,.1); color:#059669; }
    .dark .ds-badge-green { background:rgba(16,185,129,.2); color:#34d399; }
    .ds-badge-yellow { background:rgba(245,158,11,.1); color:#d97706; }
    .dark .ds-badge-yellow { background:rgba(245,158,11,.2); color:#fbbf24; }
    .ds-badge-red { background:rgba(239,68,68,.1); color:#dc2626; }
    .dark .ds-badge-red { background:rgba(239,68,68,.2); color:#fca5a5; }
    .ds-badge-gray { background:rgba(100,116,139,.1); color:#64748b; }
    .dark .ds-badge-gray { background:rgba(100,116,139,.2); color:#94a3b8; }

    /* Missing days alert */
    .ds-alert {
      display:flex; align-items:flex-start; gap:.75rem;
      padding:1rem 1.25rem; border-radius:.75rem;
      border:1px solid rgba(239,68,68,.15); background:rgba(239,68,68,.04);
    }
    .dark .ds-alert { border-color:rgba(239,68,68,.25); background:rgba(239,68,68,.08); }
    .ds-alert-icon { flex-shrink:0; width:1.25rem; height:1.25rem; color:#ef4444; margin-top:.1rem; }
    .ds-alert-text { font-size:.8rem; color:#991b1b; line-height:1.5; }
    .dark .ds-alert-text { color:#fca5a5; }
    .ds-alert-success {
      border-color:rgba(16,185,129,.15); background:rgba(16,185,129,.04);
    }
    .dark .ds-alert-success { border-color:rgba(16,185,129,.25); background:rgba(16,185,129,.08); }
    .ds-alert-success .ds-alert-icon { color:#10b981; }
    .ds-alert-success .ds-alert-text { color:#065f46; }
    .dark .ds-alert-success .ds-alert-text { color:#34d399; }

    /* Responsive */
    @media(max-width:640px) {
      .ds-hero { padding:1.25rem 1rem; }
      .ds-hero-name { font-size:1.25rem; }
      .ds-hero-content { flex-direction:column; align-items:flex-start; }
      .ds-stats { grid-template-columns:1fr 1fr; }
      .ds-table th, .ds-table td { padding:.5rem .625rem; font-size:.725rem; }
    }
    @media(max-width:380px) {
      .ds-stats { grid-template-columns:1fr; }
    }
  </style>

  <div class="ds-wrap">

    {{-- ═══ Hero Banner ═══ --}}
    <div class="ds-hero">
      <div class="ds-hero-content">
        <div class="ds-hero-left">
          <h1 class="ds-hero-name">{{ $user->name }}</h1>
          <div class="ds-hero-meta">
            <span class="ds-hero-tag">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:.85rem;height:.85rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" /></svg>
              NISN: {{ $user->nisn ?? '-' }}
            </span>
            <span class="ds-hero-tag">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:.85rem;height:.85rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
              {{ $user->kelas?->nama ?? '-' }}
            </span>
            <span class="ds-hero-tag">
              {{ $user->jenis_kelamin === 'L' ? '👨 Laki-laki' : ($user->jenis_kelamin === 'P' ? '👩 Perempuan' : '-') }}
            </span>
          </div>
        </div>
        <div class="ds-hero-right">
          <div class="ds-hero-badge">
            <span class="ds-hero-badge-icon">🌙</span>
            <div>
              <p class="ds-hero-badge-label">Ramadhan</p>
              <p class="ds-hero-badge-value">{{ $hariKe > 0 ? "Hari ke-{$hariKe}" : 'Belum dimulai' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ═══ Stat Cards ═══ --}}
    <div class="ds-stats">
      {{-- Total Formulir --}}
      <div class="ds-stat ds-stat-blue">
        <div class="ds-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
        </div>
        <div>
          <p class="ds-stat-label">Total Formulir</p>
          <p class="ds-stat-value">{{ $totalSubmit }}</p>
        </div>
      </div>

      {{-- Terverifikasi --}}
      <div class="ds-stat ds-stat-green">
        <div class="ds-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
          <p class="ds-stat-label">Terverifikasi</p>
          <p class="ds-stat-value">{{ $verified }}</p>
        </div>
      </div>

      {{-- Menunggu --}}
      <div class="ds-stat ds-stat-yellow">
        <div class="ds-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
          <p class="ds-stat-label">Menunggu</p>
          <p class="ds-stat-value">{{ $pending }}</p>
        </div>
      </div>

      {{-- Ditolak --}}
      <div class="ds-stat ds-stat-red">
        <div class="ds-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
          <p class="ds-stat-label">Ditolak</p>
          <p class="ds-stat-value">{{ $rejected }}</p>
        </div>
      </div>

      {{-- Streak --}}
      <div class="ds-stat ds-stat-purple">
        <div class="ds-stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" /></svg>
        </div>
        <div>
          <p class="ds-stat-label">Streak Hari</p>
          <p class="ds-stat-value">{{ $streak }} hari</p>
        </div>
      </div>
    </div>

    {{-- ═══ Mid Row: Progress + Info ═══ --}}
    <div class="ds-mid">

      {{-- Kepatuhan & Verifikasi --}}
      <div class="ds-card">
        <div class="ds-card-head">
          <h3 class="ds-card-title">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.1rem;height:1.1rem;color:#2563eb;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
            Tingkat Kepatuhan
          </h3>
        </div>
        <div class="ds-card-body">
          {{-- Progress ring --}}
          <div class="ds-ring-wrap">
            <div class="ds-ring">
              <svg viewBox="0 0 36 36">
                <circle class="ds-ring-bg" fill="none" stroke-width="3.8" cx="18" cy="18" r="15.9155" />
                <circle fill="none" stroke-width="3.8" cx="18" cy="18" r="15.9155"
                  stroke="{{ $progress >= 80 ? '#10b981' : ($progress >= 50 ? '#f59e0b' : '#ef4444') }}"
                  stroke-dasharray="{{ $progress }}, 100"
                  stroke-linecap="round" />
              </svg>
              <span class="ds-ring-pct">{{ $progress }}%</span>
            </div>
            <div class="ds-ring-info">
              <p class="ds-ring-label">Kepatuhan Pengisian</p>
              <p class="ds-ring-desc">{{ $totalSubmit }} dari {{ $hariKe }} hari yang diharapkan</p>
            </div>
          </div>

          <hr class="ds-divider">

          {{-- Verification ring --}}
          <div class="ds-ring-wrap">
            <div class="ds-ring">
              <svg viewBox="0 0 36 36">
                <circle class="ds-ring-bg" fill="none" stroke-width="3.8" cx="18" cy="18" r="15.9155" />
                <circle fill="none" stroke-width="3.8" cx="18" cy="18" r="15.9155"
                  stroke="#2563eb"
                  stroke-dasharray="{{ $verifyRate }}, 100"
                  stroke-linecap="round" />
              </svg>
              <span class="ds-ring-pct">{{ $verifyRate }}%</span>
            </div>
            <div class="ds-ring-info">
              <p class="ds-ring-label">Tingkat Verifikasi</p>
              <p class="ds-ring-desc">{{ $verified }} dari {{ $totalSubmit }} formulir terkirim</p>
            </div>
          </div>

          <hr class="ds-divider">

          {{-- Status distribution bar --}}
          <p style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; margin-bottom:.5rem;" class="dark:!text-gray-400">Distribusi Status</p>
          @php
            $barTotal = max($totalSubmit, 1);
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

      {{-- Profil detail --}}
      <div class="ds-card">
        <div class="ds-card-head">
          <h3 class="ds-card-title">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.1rem;height:1.1rem;color:#2563eb;"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
            Informasi Siswa
          </h3>
        </div>
        <div class="ds-card-body">
          <div class="ds-info-item">
            <span class="ds-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
              Nama Lengkap
            </span>
            <span class="ds-info-value">{{ $user->name }}</span>
          </div>
          <div class="ds-info-item">
            <span class="ds-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" /></svg>
              NISN
            </span>
            <span class="ds-info-value" style="font-family:ui-monospace,monospace;">{{ $user->nisn ?? '-' }}</span>
          </div>
          <div class="ds-info-item">
            <span class="ds-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
              Email
            </span>
            <span class="ds-info-value">{{ $user->email ?? '-' }}</span>
          </div>
          <div class="ds-info-item">
            <span class="ds-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
              Kelas
            </span>
            <span class="ds-info-value">{{ $user->kelas?->nama ?? '-' }}</span>
          </div>
          <div class="ds-info-item">
            <span class="ds-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-7 9 7M4.5 9v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V20.25h4.125c.621 0 1.125-.504 1.125-1.125V9" /></svg>
              Agama
            </span>
            <span class="ds-info-value">{{ $user->agama ?? '-' }}</span>
          </div>
          <div class="ds-info-item">
            <span class="ds-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
              No. HP
            </span>
            <span class="ds-info-value">
              @if($user->no_hp)
                {{ is_numeric($user->no_hp) && str_starts_with($user->no_hp, '8') ? '0'.$user->no_hp : $user->no_hp }}
              @else
                -
              @endif
            </span>
          </div>
          <div class="ds-info-item">
            <span class="ds-info-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
              Wali Kelas
            </span>
            <span class="ds-info-value">{{ $user->kelas?->wali?->name ?? '-' }}</span>
          </div>
        </div>
      </div>

    </div>

    {{-- ═══ Calendar Grid (same as validasi page) ═══ --}}
    <div class="ds-card">
      <div class="ds-card-head">
        <h3 class="ds-card-title">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.1rem;height:1.1rem;color:#2563eb;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
          Peta Pengisian 30 Hari Ramadhan
        </h3>
      </div>
      <div class="ds-card-body">

        {{-- Calendar header --}}
        <div class="flex items-center justify-center mb-3">
          <span class="text-[10px] lg:text-xs font-semibold text-gray-600 dark:text-gray-300">Kalender Ramadhan 1447 H &mdash; Feb &ndash; Mar 2026</span>
        </div>

        {{-- Weekday labels --}}
        <div class="grid grid-cols-7 gap-1 mb-1">
          @foreach(['SEN','SEL','RAB','KAM','JUM','SAB','MIN'] as $label)
            <div class="ds-cal-header">{{ $label }}</div>
          @endforeach
        </div>

        {{-- Calendar grid --}}
        <div class="grid grid-cols-7 gap-2">
          @foreach($calendarCells as $cell)
            @if($cell === null)
              <div></div>
            @else
              @php
                $d       = $cell['ramadanDay'];
                $isPast  = $cell['isPast'];
                $isToday = $cell['isToday'];
                $status  = $cell['status'];

                if ($isToday && $status) {
                  // Hari ini & sudah submit
                  if ($status === 'verified') {
                    $lightBg = '#dcfce7'; $lightBorder = '#34d399'; $lightText = '#065f46';
                    $darkBg  = '#064e3b'; $darkBorder  = '#10b981'; $darkText  = '#a7f3d0';
                  } elseif ($status === 'pending') {
                    $lightBg = '#fef3c7'; $lightBorder = '#34d399'; $lightText = '#92400e';
                    $darkBg  = '#78350f'; $darkBorder  = '#10b981'; $darkText  = '#fde68a';
                  } elseif ($status === 'rejected') {
                    $lightBg = '#fee2e2'; $lightBorder = '#34d399'; $lightText = '#991b1b';
                    $darkBg  = '#7f1d1d'; $darkBorder  = '#10b981'; $darkText  = '#fecaca';
                  } else {
                    $lightBg = '#d1fae5'; $lightBorder = '#34d399'; $lightText = '#065f46';
                    $darkBg  = '#064e3b'; $darkBorder  = '#10b981'; $darkText  = '#a7f3d0';
                  }
                  $borderW = '2px';
                } elseif ($isToday && !$status) {
                  // Hari ini tapi belum submit
                  $lightBg = '#ffffff'; $lightBorder = '#34d399'; $lightText = '#065f46';
                  $darkBg  = '#1f2937'; $darkBorder  = '#10b981'; $darkText  = '#9ca3af';
                  $borderW = '2px';
                } elseif (!$isPast) {
                  // Belum tiba
                  $lightBg = '#f9fafb'; $lightBorder = '#e5e7eb'; $lightText = '#9ca3af';
                  $darkBg  = '#374151'; $darkBorder  = '#4b5563'; $darkText  = '#9ca3af';
                  $borderW = '1px';
                } elseif (!$status) {
                  // Sudah lewat tapi belum mengisi
                  $lightBg = '#f8fafc'; $lightBorder = '#cbd5e1'; $lightText = '#475569';
                  $darkBg  = '#1f2937'; $darkBorder  = '#4b5563'; $darkText  = '#9ca3af';
                  $borderW = '1px';
                } elseif ($status === 'pending') {
                  $lightBg = '#fef3c7'; $lightBorder = '#fcd34d'; $lightText = '#92400e';
                  $darkBg  = '#78350f'; $darkBorder  = '#d97706'; $darkText  = '#fde68a';
                  $borderW = '1px';
                } elseif ($status === 'rejected') {
                  $lightBg = '#fee2e2'; $lightBorder = '#fca5a5'; $lightText = '#991b1b';
                  $darkBg  = '#7f1d1d'; $darkBorder  = '#ef4444'; $darkText  = '#fecaca';
                  $borderW = '1px';
                } elseif ($status === 'verified') {
                  $lightBg = '#dcfce7'; $lightBorder = '#86efac'; $lightText = '#166534';
                  $darkBg  = '#14532d'; $darkBorder  = '#22c55e'; $darkText  = '#a7f3d0';
                  $borderW = '1px';
                } else {
                  $lightBg = '#ffffff'; $lightBorder = '#e5e7eb'; $lightText = '#9ca3af';
                  $darkBg  = '#1f2937'; $darkBorder  = '#4b5563'; $darkText  = '#9ca3af';
                  $borderW = '1px';
                }
              @endphp

              <div
                title="Hari ke-{{ $d }} &mdash; {{ $cell['masehiDay'] }} {{ $cell['masehiMonthShort'] }}{{ $status ? ' — ' . ucfirst($status) : ($isPast ? ' — Belum mengisi' : '') }}"
                class="rounded-lg p-2 flex items-center justify-center w-full transition"
                style="min-height:3.25rem;background:{{ $lightBg }};border:{{ $borderW }} solid {{ $lightBorder }};"
              >
                <span class="text-sm font-bold leading-none" style="color:{{ $lightText }}">{{ $cell['masehiDay'] }}</span>
              </div>

              <style>
                .dark div[title="Hari ke-{{ $d }} — {{ $cell['masehiDay'] }} {{ $cell['masehiMonthShort'] }}{{ $status ? ' — ' . ucfirst($status) : ($isPast ? ' — Belum mengisi' : '') }}"] {
                  background: {{ $darkBg }} !important;
                  border-color: {{ $darkBorder }} !important;
                }
                .dark div[title="Hari ke-{{ $d }} — {{ $cell['masehiDay'] }} {{ $cell['masehiMonthShort'] }}{{ $status ? ' — ' . ucfirst($status) : ($isPast ? ' — Belum mengisi' : '') }}"] span {
                  color: {{ $darkText }} !important;
                }
              </style>
            @endif
          @endforeach
        </div>



      </div>
    </div>

    {{-- ═══ Missing days alert ═══ --}}
    @if(count($missingDays) > 0)
      <div class="ds-alert">
        <svg class="ds-alert-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
        <div class="ds-alert-text">
          <strong>{{ count($missingDays) }} hari belum mengisi formulir:</strong>
          {{ implode(', ', array_map(fn($d) => "Hari {$d}", $missingDays)) }}
        </div>
      </div>
    @elseif($hariKe > 0)
      <div class="ds-alert ds-alert-success">
        <svg class="ds-alert-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        <div class="ds-alert-text">
          <strong>Semua hari sudah terisi!</strong> Siswa ini sudah mengisi formulir untuk semua {{ $hariKe }} hari Ramadhan yang telah berlalu.
        </div>
      </div>
    @endif

    {{-- ═══ Detail Table ═══ --}}
    @if(count($dayDetails) > 0)
    <div class="ds-table-card">
      <div class="ds-table-head">
        <h3 class="ds-table-title">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.1rem;height:1.1rem;color:#2563eb;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M10.875 12c-.621 0-1.125.504-1.125 1.125M12 12c.621 0 1.125.504 1.125 1.125m0 0v1.5c0 .621-.504 1.125-1.125 1.125M12 13.125c-.621 0-1.125.504-1.125 1.125M12 13.125c.621 0 1.125.504 1.125 1.125m0 0c0 .621-.504 1.125-1.125 1.125m0 0c-.621 0-1.125.504-1.125 1.125" /></svg>
          Riwayat Formulir Per Hari
        </h3>
        <span class="ds-table-count">{{ $hariKe }} hari</span>
      </div>
      <div class="ds-table-wrap">
        <table class="ds-table">
          <thead>
            <tr>
              <th style="width:3rem;">Hari</th>
              <th>Tanggal</th>
              <th style="text-align:center;">Status</th>
              <th>Waktu Kirim</th>
              <th>Waktu Verifikasi</th>
              <th>Catatan Guru</th>
            </tr>
          </thead>
          <tbody>
            @foreach($dayDetails as $detail)
              <tr>
                <td style="font-weight:700; text-align:center;">{{ $detail['hari'] }}</td>
                <td>{{ $detail['tanggal'] }}</td>
                <td style="text-align:center;">
                  @if($detail['status'] === 'verified')
                    <span class="ds-badge ds-badge-green">Terverifikasi</span>
                  @elseif($detail['status'] === 'pending')
                    <span class="ds-badge ds-badge-yellow">Menunggu</span>
                  @elseif($detail['status'] === 'rejected')
                    <span class="ds-badge ds-badge-red">Ditolak</span>
                  @else
                    <span class="ds-badge ds-badge-gray">Belum Mengisi</span>
                  @endif
                </td>
                <td>{{ $detail['created_at'] ?? '-' }}</td>
                <td>{{ $detail['verified_at'] ?? '-' }}</td>
                <td style="max-width:12rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $detail['catatan_guru'] ?? '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif

  </div>
</x-filament-panels::page>
