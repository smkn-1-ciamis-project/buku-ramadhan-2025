<x-filament-panels::page>
    <style>
        /* ─── Role Edit Page ─── */
        .re-wrap { display:flex; flex-direction:column; gap:1.25rem; }

        /* Back link */
        .re-back {
            display:inline-flex; align-items:center; gap:.375rem;
            font-size:.8rem; font-weight:600; color:#6b7280;
            text-decoration:none; transition:color .15s;
        }
        .re-back:hover { color:#2563eb; }
        .dark .re-back { color:#9ca3af; }
        .dark .re-back:hover { color:#93c5fd; }
        .re-back svg { width:.875rem; height:.875rem; }

        /* Hero Banner */
        .re-hero {
            position:relative; overflow:hidden; border-radius:1rem;
            background:linear-gradient(160deg, #0f172a 0%, #1e3a5f 35%, #2563eb 100%);
            padding:1.75rem 2rem; color:#fff;
            box-shadow:0 8px 20px -4px rgba(37,99,235,.25);
        }
        .re-hero::before {
            content:''; position:absolute; top:-3rem; right:-3rem;
            width:14rem; height:14rem; border-radius:50%;
            background:radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%);
            filter:blur(30px);
        }
        .re-hero::after {
            content:''; position:absolute; bottom:-2rem; left:-2rem;
            width:10rem; height:10rem; border-radius:50%;
            background:radial-gradient(circle, rgba(30,58,138,.3) 0%, transparent 70%);
            filter:blur(25px);
        }
        .re-hero-stars {
            position:absolute; inset:0; overflow:hidden; pointer-events:none;
        }
        .re-hero-stars span {
            position:absolute; width:2px; height:2px; background:#fff; border-radius:50%;
            animation:re-twinkle 3s infinite ease-in-out alternate;
        }
        .re-hero-stars span:nth-child(1) { top:15%; left:12%; animation-delay:0s; }
        .re-hero-stars span:nth-child(2) { top:25%; left:38%; animation-delay:.6s; }
        .re-hero-stars span:nth-child(3) { top:10%; left:62%; animation-delay:1.1s; }
        .re-hero-stars span:nth-child(4) { top:32%; left:82%; animation-delay:1.6s; }
        .re-hero-stars span:nth-child(5) { top:20%; left:92%; animation-delay:2.1s; }
        @keyframes re-twinkle { 0% { opacity:.2; transform:scale(.8); } 100% { opacity:1; transform:scale(1.2); } }

        .re-hero-content {
            position:relative; z-index:2;
            display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap;
        }
        .re-hero-icon {
            width:3.5rem; height:3.5rem; border-radius:1rem; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            background:rgba(255,255,255,.12); backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,.18);
        }
        .re-hero-icon svg { width:1.5rem; height:1.5rem; color:#fff; }
        .re-hero-text { flex:1; min-width:0; }
        .re-hero-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; color:#93c5fd; }
        .re-hero-title { font-size:1.375rem; font-weight:800; margin-top:.125rem; letter-spacing:-.02em; }
        .re-hero-desc { font-size:.825rem; color:rgba(147,197,253,.75); margin-top:.25rem; line-height:1.5; }

        .re-hero-meta {
            margin-left:auto; display:flex; align-items:center; gap:.75rem; flex-shrink:0;
        }
        .re-hero-badge {
            display:inline-flex; align-items:center; gap:.375rem;
            padding:.375rem .875rem; border-radius:.625rem;
            background:rgba(255,255,255,.1); backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,.15);
            font-size:.75rem; font-weight:600;
        }
        .re-hero-badge svg { width:.875rem; height:.875rem; }

        @media(max-width:640px) {
            .re-hero { padding:1.25rem; }
            .re-hero-content { gap:.875rem; }
            .re-hero-title { font-size:1.125rem; }
            .re-hero-meta { margin-left:0; width:100%; }
        }

        /* Form wrapper styling */
        .re-form-wrap {
            display:flex; flex-direction:column; gap:1.25rem;
        }

        /* Override Filament section styling */
        .re-form-wrap .fi-section {
            border-radius:1rem !important;
            border:1px solid rgba(100,100,100,.12) !important;
            overflow:hidden !important;
            box-shadow:none !important;
            background:transparent !important;
        }
        .dark .re-form-wrap .fi-section {
            border-color:rgba(100,100,100,.25) !important;
        }

        .re-form-wrap .fi-section-header {
            padding:.875rem 1.25rem !important;
            border-bottom:1px solid rgba(100,100,100,.1) !important;
            background:rgba(100,100,100,.03) !important;
        }
        .dark .re-form-wrap .fi-section-header {
            background:rgba(0,0,0,.15) !important;
            border-color:rgba(100,100,100,.18) !important;
        }

        .re-form-wrap .fi-section-header-heading {
            font-size:.85rem !important; font-weight:700 !important;
            text-transform:uppercase !important; letter-spacing:.04em !important;
        }

        .re-form-wrap .fi-section-header-description {
            font-size:.75rem !important;
        }

        .re-form-wrap .fi-section-content {
            padding:1.25rem !important;
        }

        /* Override Filament toggle styling for better look */
        .re-form-wrap .fi-toggle-input:checked {
            background-color:#2563eb !important;
        }
        .dark .re-form-wrap .fi-toggle-input:checked {
            background-color:#3b82f6 !important;
        }

        /* Action buttons styling */
        .re-actions {
            display:flex; align-items:center; gap:.75rem;
            padding-top:.5rem;
        }
        .re-btn {
            display:inline-flex; align-items:center; gap:.5rem;
            padding:.625rem 1.5rem; border-radius:.75rem;
            font-size:.825rem; font-weight:700; cursor:pointer;
            transition:all .2s; border:none; text-decoration:none;
        }
        .re-btn svg { width:1rem; height:1rem; }
        .re-btn-primary {
            background:linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%);
            color:#fff; box-shadow:0 4px 12px -2px rgba(37,99,235,.4);
        }
        .re-btn-primary:hover { transform:translateY(-1px); box-shadow:0 6px 16px -2px rgba(37,99,235,.5); }
        .re-btn-danger {
            background:linear-gradient(135deg, #991b1b 0%, #dc2626 50%, #ef4444 100%);
            color:#fff; box-shadow:0 4px 12px -2px rgba(220,38,38,.3);
        }
        .re-btn-danger:hover { transform:translateY(-1px); box-shadow:0 6px 16px -2px rgba(220,38,38,.4); }
        .re-btn-secondary {
            background:rgba(100,100,100,.08); color:#6b7280;
            border:1px solid rgba(100,100,100,.15);
        }
        .dark .re-btn-secondary { background:rgba(100,100,100,.12); color:#9ca3af; border-color:rgba(100,100,100,.25); }
        .re-btn-secondary:hover { background:rgba(100,100,100,.15); }
        .dark .re-btn-secondary:hover { background:rgba(100,100,100,.2); }

        /* Info cards grid */
        .re-info-grid {
            display:grid; grid-template-columns:repeat(4, 1fr); gap:1rem;
        }
        @media(max-width:1024px) { .re-info-grid { grid-template-columns:repeat(2, 1fr); } }
        @media(max-width:640px) { .re-info-grid { grid-template-columns:1fr; } }

        .re-info-card {
            display:flex; align-items:center; gap:.75rem;
            padding:1rem 1.25rem; border-radius:1rem;
            border:1px solid rgba(100,100,100,.12);
            transition:all .2s;
        }
        .re-info-card:hover { transform:translateY(-2px); box-shadow:0 4px 12px -2px rgba(0,0,0,.08); }
        .dark .re-info-card { border-color:rgba(100,100,100,.25); }

        .re-info-icon {
            width:2.75rem; height:2.75rem; border-radius:.75rem; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            background:rgba(37,99,235,.85);
        }
        .re-info-icon svg { width:1.125rem; height:1.125rem; color:rgba(255,255,255,.9); }
        .re-info-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.04em; font-weight:600; color:#6b7280; }
        .dark .re-info-label { color:#9ca3af; }
        .re-info-value { font-size:.875rem; font-weight:700; margin-top:.125rem; }

        /* Footer info */
        .re-footer {
            text-align:center; font-size:.7rem; color:#9ca3af; padding:.25rem 0;
        }
        .re-footer code {
            font-family:ui-monospace, SFMono-Regular, monospace;
            background:rgba(100,100,100,.06); padding:.125rem .375rem; border-radius:.25rem;
            font-size:.675rem;
        }
        .dark .re-footer code { background:rgba(100,100,100,.12); }

        /* Hide default Filament page header & form actions (we render our own) */
        .fi-header { display:none !important; }
    </style>

    @php
        $record = $this->record;
        $usersCount = $record->users()->count();
        $roleBadge = match(strtolower($record->name ?? '')) {
            'super admin', 'superadmin' => ['color' => '#ef4444', 'bg' => 'rgba(239,68,68,.15)'],
            'guru' => ['color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.15)'],
            'kesiswaan' => ['color' => '#3b82f6', 'bg' => 'rgba(59,130,246,.15)'],
            'siswa' => ['color' => '#10b981', 'bg' => 'rgba(16,185,129,.15)'],
            default => ['color' => '#6b7280', 'bg' => 'rgba(100,100,100,.15)'],
        };
    @endphp

    <div class="re-wrap">
        {{-- Back link --}}
        <div>
            <a href="{{ \App\Filament\Superadmin\Resources\RoleResource::getUrl('index') }}" class="re-back">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                Kembali ke Daftar Role
            </a>
        </div>

        {{-- Hero Banner --}}
        <div class="re-hero">
            <div class="re-hero-stars">
                <span></span><span></span><span></span><span></span><span></span>
            </div>
            <div class="re-hero-content">
                <div class="re-hero-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <div class="re-hero-text">
                    <div class="re-hero-label">Ubah Role</div>
                    <div class="re-hero-title">{{ $record->name }}</div>
                    <div class="re-hero-desc">Edit konfigurasi dan visibilitas menu untuk role ini</div>
                </div>
                <div class="re-hero-meta">
                    <div class="re-hero-badge">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        {{ $usersCount }} Pengguna
                    </div>
                </div>
            </div>
        </div>

        {{-- Info cards --}}
        <div class="re-info-grid">
            <div class="re-info-card">
                <div class="re-info-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <div>
                    <div class="re-info-label">Nama Role</div>
                    <div class="re-info-value">{{ $record->name }}</div>
                </div>
            </div>
            <div class="re-info-card">
                <div class="re-info-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <div>
                    <div class="re-info-label">Total Pengguna</div>
                    <div class="re-info-value">{{ $usersCount }} pengguna</div>
                </div>
            </div>
            <div class="re-info-card">
                <div class="re-info-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="re-info-label">Dibuat</div>
                    <div class="re-info-value">{{ $record->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}</div>
                </div>
            </div>
            <div class="re-info-card">
                <div class="re-info-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                </div>
                <div>
                    <div class="re-info-label">Terakhir Diubah</div>
                    <div class="re-info-value">{{ $record->updated_at?->diffForHumans() ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <x-filament-panels::form wire:submit="save">
            <div class="re-form-wrap">
                {{ $this->form }}
            </div>

            <div class="re-actions">
                <button type="submit" class="re-btn re-btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Simpan Perubahan
                </button>
                <a href="{{ \App\Filament\Superadmin\Resources\RoleResource::getUrl('index') }}" class="re-btn re-btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Batal
                </a>
                <div style="margin-left:auto;">
                    {{ $this->deleteAction }}
                </div>
            </div>
        </x-filament-panels::form>

        {{-- Footer --}}
        <div class="re-footer">
            ID: <code>{{ $record->id }}</code>
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
