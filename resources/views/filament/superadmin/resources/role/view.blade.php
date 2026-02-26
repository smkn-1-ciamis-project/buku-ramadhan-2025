<x-filament-panels::page>
    <style>
        /* ─── Role View Page ─── */
        .rv-wrap { display:flex; flex-direction:column; gap:1.25rem; }

        /* Back link */
        .rv-back {
            display:inline-flex; align-items:center; gap:.375rem;
            font-size:.8rem; font-weight:600; color:#6b7280;
            text-decoration:none; transition:color .15s;
        }
        .rv-back:hover { color:#2563eb; }
        .dark .rv-back { color:#9ca3af; }
        .dark .rv-back:hover { color:#93c5fd; }
        .rv-back svg { width:.875rem; height:.875rem; }

        /* Hero Banner */
        .rv-hero {
            position:relative; overflow:hidden; border-radius:1rem;
            background:linear-gradient(160deg, #0f172a 0%, #1e3a5f 35%, #2563eb 100%);
            padding:1.75rem 2rem; color:#fff;
            box-shadow:0 8px 20px -4px rgba(37,99,235,.25);
        }
        .rv-hero::before {
            content:''; position:absolute; top:-3rem; right:-3rem;
            width:14rem; height:14rem; border-radius:50%;
            background:radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%);
            filter:blur(30px);
        }
        .rv-hero::after {
            content:''; position:absolute; bottom:-2rem; left:-2rem;
            width:10rem; height:10rem; border-radius:50%;
            background:radial-gradient(circle, rgba(30,58,138,.3) 0%, transparent 70%);
            filter:blur(25px);
        }
        .rv-hero-stars {
            position:absolute; inset:0; overflow:hidden; pointer-events:none;
        }
        .rv-hero-stars span {
            position:absolute; width:2px; height:2px; background:#fff; border-radius:50%;
            animation:rv-twinkle 3s infinite ease-in-out alternate;
        }
        .rv-hero-stars span:nth-child(1) { top:15%; left:12%; animation-delay:0s; }
        .rv-hero-stars span:nth-child(2) { top:25%; left:38%; animation-delay:.6s; }
        .rv-hero-stars span:nth-child(3) { top:10%; left:62%; animation-delay:1.1s; }
        .rv-hero-stars span:nth-child(4) { top:32%; left:82%; animation-delay:1.6s; }
        .rv-hero-stars span:nth-child(5) { top:20%; left:92%; animation-delay:2.1s; }
        @keyframes rv-twinkle { 0% { opacity:.2; transform:scale(.8); } 100% { opacity:1; transform:scale(1.2); } }

        .rv-hero-content {
            position:relative; z-index:2;
            display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap;
        }
        .rv-hero-icon {
            width:3.5rem; height:3.5rem; border-radius:1rem; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            background:rgba(255,255,255,.12); backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,.18);
        }
        .rv-hero-icon svg { width:1.5rem; height:1.5rem; color:#fff; }
        .rv-hero-text { flex:1; min-width:0; }
        .rv-hero-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; color:#93c5fd; }
        .rv-hero-title { font-size:1.375rem; font-weight:800; margin-top:.125rem; letter-spacing:-.02em; }
        .rv-hero-desc { font-size:.825rem; color:rgba(147,197,253,.75); margin-top:.25rem; line-height:1.5; }

        .rv-hero-actions {
            margin-left:auto; display:flex; align-items:center; gap:.625rem; flex-shrink:0;
        }
        .rv-hero-btn {
            display:inline-flex; align-items:center; gap:.375rem;
            padding:.5rem 1.125rem; border-radius:.625rem;
            font-size:.775rem; font-weight:600; cursor:pointer;
            transition:all .2s; text-decoration:none; border:none;
        }
        .rv-hero-btn svg { width:.875rem; height:.875rem; }
        .rv-hero-btn-edit {
            background:rgba(255,255,255,.15); backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,.2); color:#fff;
        }
        .rv-hero-btn-edit:hover { background:rgba(255,255,255,.25); color:#fff; }

        @media(max-width:640px) {
            .rv-hero { padding:1.25rem; }
            .rv-hero-content { gap:.875rem; }
            .rv-hero-title { font-size:1.125rem; }
            .rv-hero-actions { margin-left:0; width:100%; }
        }

        /* Stat cards grid */
        .rv-stats {
            display:grid; grid-template-columns:repeat(4, 1fr); gap:1rem;
        }
        @media(max-width:1024px) { .rv-stats { grid-template-columns:repeat(2, 1fr); } }
        @media(max-width:640px) { .rv-stats { grid-template-columns:1fr; } }

        .rv-stat {
            display:flex; align-items:center; gap:.75rem;
            padding:1rem 1.25rem; border-radius:1rem;
            border:1px solid rgba(100,100,100,.12);
            transition:all .2s;
        }
        .rv-stat:hover { transform:translateY(-2px); box-shadow:0 4px 12px -2px rgba(0,0,0,.08); }
        .dark .rv-stat { border-color:rgba(100,100,100,.25); }

        .rv-stat-icon {
            width:2.75rem; height:2.75rem; border-radius:.75rem; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
        }
        .rv-stat-icon svg { width:1.125rem; height:1.125rem; color:rgba(255,255,255,.9); }
        .rv-stat-icon-blue { background:rgba(37,99,235,.85); }
        .rv-stat-icon-green { background:rgba(16,185,129,.85); }
        .rv-stat-icon-amber { background:rgba(245,158,11,.85); }
        .rv-stat-icon-purple { background:rgba(139,92,246,.85); }

        .rv-stat-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.04em; font-weight:600; color:#6b7280; }
        .dark .rv-stat-label { color:#9ca3af; }
        .rv-stat-value { font-size:.875rem; font-weight:700; margin-top:.125rem; }

        /* Cards */
        .rv-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        @media(max-width:768px) { .rv-grid { grid-template-columns:1fr; } }

        .rv-card {
            border-radius:1rem; overflow:hidden;
            border:1px solid rgba(100,100,100,.12);
        }
        .dark .rv-card { border-color:rgba(100,100,100,.25); }
        .rv-card-head {
            padding:.875rem 1.25rem; display:flex; align-items:center; gap:.5rem;
            border-bottom:1px solid rgba(100,100,100,.1);
            background:rgba(100,100,100,.03);
        }
        .dark .rv-card-head { background:rgba(0,0,0,.15); border-color:rgba(100,100,100,.18); }
        .rv-card-head svg { width:1rem; height:1rem; color:#6b7280; flex-shrink:0; }
        .dark .rv-card-head svg { color:#9ca3af; }
        .rv-card-title { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
        .rv-card-body { padding:1.25rem; }

        .rv-full { grid-column:1 / -1; }

        /* Info rows */
        .rv-info-row {
            display:flex; align-items:flex-start; padding:.625rem 0;
        }
        .rv-info-row + .rv-info-row { border-top:1px solid rgba(100,100,100,.06); }
        .dark .rv-info-row + .rv-info-row { border-color:rgba(100,100,100,.12); }
        .rv-info-label {
            width:8rem; flex-shrink:0; font-size:.75rem; font-weight:600;
            text-transform:uppercase; letter-spacing:.04em; color:#6b7280; padding-top:.125rem;
        }
        .dark .rv-info-label { color:#9ca3af; }
        .rv-info-value { flex:1; font-size:.85rem; word-break:break-word; }

        /* Badges */
        .rv-badge {
            display:inline-flex; align-items:center; padding:.25rem .625rem;
            border-radius:.5rem; font-size:.75rem; font-weight:600; gap:.25rem;
        }
        .rv-badge-danger { background:rgba(239,68,68,.12); color:#dc2626; }
        .rv-badge-warning { background:rgba(245,158,11,.12); color:#d97706; }
        .rv-badge-info { background:rgba(37,99,235,.12); color:#2563eb; }
        .rv-badge-success { background:rgba(16,185,129,.12); color:#059669; }
        .rv-badge-gray { background:rgba(100,100,100,.1); color:#6b7280; }
        .dark .rv-badge-danger { background:rgba(239,68,68,.18); color:#fca5a5; }
        .dark .rv-badge-warning { background:rgba(245,158,11,.18); color:#fbbf24; }
        .dark .rv-badge-info { background:rgba(37,99,235,.18); color:#93c5fd; }
        .dark .rv-badge-success { background:rgba(16,185,129,.18); color:#34d399; }
        .dark .rv-badge-gray { background:rgba(100,100,100,.18); color:#9ca3af; }

        /* Menu visibility grid */
        .rv-menu-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:.75rem; }
        @media(max-width:768px) { .rv-menu-grid { grid-template-columns:repeat(2, 1fr); } }
        @media(max-width:480px) { .rv-menu-grid { grid-template-columns:1fr; } }

        .rv-menu-item {
            display:flex; align-items:center; gap:.75rem;
            padding:.75rem 1rem; border-radius:.75rem;
            border:1px solid rgba(100,100,100,.08);
            transition:all .15s;
        }
        .rv-menu-item:hover { background:rgba(100,100,100,.03); }
        .dark .rv-menu-item { border-color:rgba(100,100,100,.15); }
        .dark .rv-menu-item:hover { background:rgba(100,100,100,.06); }

        .rv-menu-dot {
            width:2rem; height:2rem; border-radius:.5rem; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
        }
        .rv-menu-dot svg { width:1rem; height:1rem; }
        .rv-menu-dot-on { background:rgba(16,185,129,.12); }
        .rv-menu-dot-on svg { color:#059669; }
        .dark .rv-menu-dot-on { background:rgba(16,185,129,.2); }
        .dark .rv-menu-dot-on svg { color:#34d399; }
        .rv-menu-dot-off { background:rgba(239,68,68,.1); }
        .rv-menu-dot-off svg { color:#dc2626; }
        .dark .rv-menu-dot-off { background:rgba(239,68,68,.18); }
        .dark .rv-menu-dot-off svg { color:#fca5a5; }

        .rv-menu-label { font-size:.825rem; font-weight:600; }
        .rv-menu-status { font-size:.7rem; color:#6b7280; margin-top:.125rem; }
        .dark .rv-menu-status { color:#9ca3af; }

        /* Users table */
        .rv-table { width:100%; border-collapse:collapse; font-size:.8rem; }
        .rv-table th {
            text-align:left; padding:.625rem .875rem; font-weight:600; font-size:.7rem;
            text-transform:uppercase; letter-spacing:.05em;
            border-bottom:1px solid rgba(100,100,100,.1);
            color:#6b7280;
        }
        .dark .rv-table th { border-color:rgba(100,100,100,.2); color:#9ca3af; }
        .rv-table td { padding:.625rem .875rem; border-bottom:1px solid rgba(100,100,100,.05); }
        .dark .rv-table td { border-color:rgba(100,100,100,.1); }
        .rv-table tr:hover td { background:rgba(100,100,100,.03); }
        .dark .rv-table tr:hover td { background:rgba(100,100,100,.06); }

        .rv-table-empty {
            text-align:center; padding:2rem; color:#9ca3af; font-size:.825rem;
        }

        .rv-jk-badge {
            display:inline-flex; padding:.125rem .5rem;
            border-radius:.375rem; font-size:.7rem; font-weight:600;
        }
        .rv-jk-l { background:rgba(37,99,235,.12); color:#2563eb; }
        .rv-jk-p { background:rgba(239,68,68,.12); color:#dc2626; }
        .dark .rv-jk-l { background:rgba(37,99,235,.18); color:#93c5fd; }
        .dark .rv-jk-p { background:rgba(239,68,68,.18); color:#fca5a5; }

        /* Footer */
        .rv-footer {
            text-align:center; font-size:.7rem; color:#9ca3af; padding:.25rem 0;
        }
        .rv-footer code {
            font-family:ui-monospace, SFMono-Regular, monospace;
            background:rgba(100,100,100,.06); padding:.125rem .375rem; border-radius:.25rem;
            font-size:.675rem;
        }
        .dark .rv-footer code { background:rgba(100,100,100,.12); }

        /* Pagination */
        .rv-pag {
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;
            padding:.75rem 1rem; border-top:1px solid rgba(100,100,100,.08);
        }
        .dark .rv-pag { border-color:rgba(100,100,100,.15); }
        .rv-pag-info { font-size:.75rem; color:#6b7280; }
        .dark .rv-pag-info { color:#9ca3af; }
        .rv-pag-links { display:flex; align-items:center; gap:.25rem; }
        .rv-pag-btn {
            display:inline-flex; align-items:center; justify-content:center;
            min-width:2rem; height:2rem; padding:0 .5rem;
            border-radius:.5rem; font-size:.75rem; font-weight:600;
            border:1px solid rgba(100,100,100,.12); background:transparent;
            color:#374151; cursor:pointer; text-decoration:none; transition:all .15s;
        }
        .rv-pag-btn:hover { background:rgba(37,99,235,.08); border-color:rgba(37,99,235,.2); color:#2563eb; }
        .rv-pag-btn-active { background:rgba(37,99,235,.1); border-color:rgba(37,99,235,.3); color:#2563eb; }
        .rv-pag-btn-disabled { opacity:.4; pointer-events:none; }
        .dark .rv-pag-btn { border-color:rgba(100,100,100,.25); color:#d1d5db; }
        .dark .rv-pag-btn:hover { background:rgba(37,99,235,.15); border-color:rgba(37,99,235,.3); color:#93c5fd; }
        .dark .rv-pag-btn-active { background:rgba(37,99,235,.2); border-color:rgba(37,99,235,.4); color:#93c5fd; }
        .rv-pag-btn svg { width:.75rem; height:.75rem; }

        /* Hide default Filament page header */
        .fi-header { display:none !important; }
    </style>

    @php
        $record = $this->record;
        $usersCount = $record->users()->count();
        $users = $record->users()->orderBy('name')->paginate(15);
        $isSiswa = strtolower($record->name ?? '') === 'siswa';

        $roleBadge = match(strtolower($record->name ?? '')) {
            'super admin', 'superadmin' => 'rv-badge-danger',
            'guru' => 'rv-badge-warning',
            'kesiswaan' => 'rv-badge-info',
            'siswa' => 'rv-badge-success',
            default => 'rv-badge-gray',
        };

        $menus = \App\Filament\Superadmin\Resources\RoleResource::getMenusForRole($record->name);
    @endphp

    <div class="rv-wrap">
        {{-- Back link --}}
        <div>
            <a href="{{ \App\Filament\Superadmin\Resources\RoleResource::getUrl('index') }}" class="rv-back">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                Kembali ke Daftar Role
            </a>
        </div>

        {{-- Hero Banner --}}
        <div class="rv-hero">
            <div class="rv-hero-stars">
                <span></span><span></span><span></span><span></span><span></span>
            </div>
            <div class="rv-hero-content">
                <div class="rv-hero-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <div class="rv-hero-text">
                    <div class="rv-hero-label">Detail Role</div>
                    <div class="rv-hero-title">{{ $record->name }}</div>
                    <div class="rv-hero-desc">Informasi lengkap dan konfigurasi untuk role ini</div>
                </div>
                <div class="rv-hero-actions">
                    <a href="{{ \App\Filament\Superadmin\Resources\RoleResource::getUrl('edit', ['record' => $record]) }}" class="rv-hero-btn rv-hero-btn-edit">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                        Edit Role
                    </a>
                </div>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="rv-stats">
            <div class="rv-stat">
                <div class="rv-stat-icon rv-stat-icon-blue">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <div>
                    <div class="rv-stat-label">Nama Role</div>
                    <div class="rv-stat-value">{{ $record->name }}</div>
                </div>
            </div>
            <div class="rv-stat">
                <div class="rv-stat-icon rv-stat-icon-green">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <div>
                    <div class="rv-stat-label">Total Pengguna</div>
                    <div class="rv-stat-value">{{ $usersCount }} pengguna</div>
                </div>
            </div>
            <div class="rv-stat">
                <div class="rv-stat-icon rv-stat-icon-amber">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="rv-stat-label">Dibuat</div>
                    <div class="rv-stat-value">{{ $record->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}</div>
                </div>
            </div>
            <div class="rv-stat">
                <div class="rv-stat-icon rv-stat-icon-purple">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                </div>
                <div>
                    <div class="rv-stat-label">Terakhir Diubah</div>
                    <div class="rv-stat-value">{{ $record->updated_at?->diffForHumans() ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Cards Grid --}}
        <div class="rv-grid">
            {{-- Card: Detail Role --}}
            <div class="rv-card">
                <div class="rv-card-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    <span class="rv-card-title">Detail Role</span>
                </div>
                <div class="rv-card-body">
                    <div class="rv-info-row">
                        <span class="rv-info-label">Nama Role</span>
                        <span class="rv-info-value">
                            <span class="rv-badge {{ $roleBadge }}">{{ $record->name }}</span>
                        </span>
                    </div>
                    <div class="rv-info-row">
                        <span class="rv-info-label">Persetujuan</span>
                        <span class="rv-info-value">
                            @if($record->need_approval)
                                <span class="rv-badge rv-badge-warning">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:.75rem;height:.75rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    Butuh Persetujuan
                                </span>
                            @else
                                <span class="rv-badge rv-badge-success">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:.75rem;height:.75rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Tidak Perlu Persetujuan
                                </span>
                            @endif
                        </span>
                    </div>
                    <div class="rv-info-row">
                        <span class="rv-info-label">Pengguna</span>
                        <span class="rv-info-value" style="font-weight:700;">{{ $usersCount }} pengguna</span>
                    </div>
                    <div class="rv-info-row">
                        <span class="rv-info-label">Dibuat</span>
                        <span class="rv-info-value">{{ $record->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}</span>
                    </div>
                    <div class="rv-info-row">
                        <span class="rv-info-label">Diubah</span>
                        <span class="rv-info-value">{{ $record->updated_at?->translatedFormat('d M Y, H:i') ?? '-' }} ({{ $record->updated_at?->diffForHumans() }})</span>
                    </div>
                </div>
            </div>

            {{-- Card: Menu Visibility --}}
            @if(count($menus) > 0)
            <div class="rv-card">
                <div class="rv-card-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    <span class="rv-card-title">Visibilitas Menu Sidebar</span>
                </div>
                <div class="rv-card-body">
                    <div class="rv-menu-grid">
                        @foreach($menus as $key => $label)
                            @php $isVisible = $record->isMenuVisible($key); @endphp
                            <div class="rv-menu-item">
                                <div class="rv-menu-dot {{ $isVisible ? 'rv-menu-dot-on' : 'rv-menu-dot-off' }}">
                                    @if($isVisible)
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    @else
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="rv-menu-label">{{ $label }}</div>
                                    <div class="rv-menu-status">{{ $isVisible ? 'Aktif' : 'Nonaktif' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Card: Users (full width) --}}
            <div class="rv-card rv-full">
                <div class="rv-card-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    <span class="rv-card-title">Daftar Pengguna ({{ $usersCount }})</span>
                </div>
                <div class="rv-card-body" style="padding:0;">
                    @if($users->count() > 0)
                        <table class="rv-table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    @if($isSiswa)<th>NISN</th>@endif
                                    <th>JK</th>
                                    <th>Terdaftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td style="font-weight:600;">{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        @if($isSiswa)
                                            <td style="font-family:ui-monospace,monospace;">{{ $user->nisn ?? '-' }}</td>
                                        @endif
                                        <td>
                                            @if($user->jenis_kelamin)
                                                <span class="rv-jk-badge {{ $user->jenis_kelamin === 'L' ? 'rv-jk-l' : 'rv-jk-p' }}">
                                                    {{ $user->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at?->diffForHumans() ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- Pagination --}}
                        @if($users->hasPages())
                            <div class="rv-pag">
                                <div class="rv-pag-info">
                                    Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna
                                </div>
                                <div class="rv-pag-links">
                                    {{-- Previous --}}
                                    @if($users->onFirstPage())
                                        <span class="rv-pag-btn rv-pag-btn-disabled">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                                        </span>
                                    @else
                                        <a href="{{ $users->previousPageUrl() }}" class="rv-pag-btn">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                                        </a>
                                    @endif

                                    {{-- Page numbers --}}
                                    @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                                        <a href="{{ $url }}" class="rv-pag-btn {{ $page == $users->currentPage() ? 'rv-pag-btn-active' : '' }}">
                                            {{ $page }}
                                        </a>
                                    @endforeach

                                    {{-- Next --}}
                                    @if($users->hasMorePages())
                                        <a href="{{ $users->nextPageUrl() }}" class="rv-pag-btn">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                        </a>
                                    @else
                                        <span class="rv-pag-btn rv-pag-btn-disabled">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="rv-table-empty">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" style="width:2.5rem;height:2.5rem;margin:0 auto .75rem;display:block;opacity:.4;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            Belum ada pengguna dengan role ini
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="rv-footer">
            ID: <code>{{ $record->id }}</code>
        </div>
    </div>
</x-filament-panels::page>
