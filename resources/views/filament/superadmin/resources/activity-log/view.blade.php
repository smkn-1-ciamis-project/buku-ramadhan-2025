<x-filament-panels::page>
    <style>
        /* ─── Activity Log View ─── */
        .al-wrap { display:flex; flex-direction:column; gap:1.25rem; }

        /* Back link */
        .al-back {
            display:inline-flex; align-items:center; gap:.375rem;
            font-size:.8rem; font-weight:600; color:#6b7280;
            text-decoration:none; transition:color .15s;
        }
        .al-back:hover { color:#2563eb; }
        .dark .al-back { color:#9ca3af; }
        .dark .al-back:hover { color:#93c5fd; }
        .al-back svg { width:.875rem; height:.875rem; }

        /* Hero banner */
        .al-hero {
            position:relative; overflow:hidden; border-radius:1rem;
            padding:1.75rem 2rem; color:#fff;
            box-shadow:0 8px 20px -4px rgba(0,0,0,.15);
        }
        .al-hero::before {
            content:''; position:absolute; top:-3rem; right:-3rem;
            width:14rem; height:14rem; border-radius:50%;
            background:radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%);
            filter:blur(30px);
        }
        .al-hero-login { background:linear-gradient(135deg, #065f46 0%, #059669 50%, #34d399 100%); }
        .al-hero-logout { background:linear-gradient(135deg, #374151 0%, #4b5563 50%, #6b7280 100%); }
        .al-hero-danger { background:linear-gradient(135deg, #7f1d1d 0%, #dc2626 50%, #f87171 100%); }
        .al-hero-warning { background:linear-gradient(135deg, #78350f 0%, #d97706 50%, #fbbf24 100%); }
        .al-hero-success { background:linear-gradient(135deg, #064e3b 0%, #059669 50%, #34d399 100%); }
        .al-hero-info { background:linear-gradient(135deg, #1e3a5f 0%, #2563eb 50%, #60a5fa 100%); }
        .al-hero-default { background:linear-gradient(135deg, #0f172a 0%, #334155 50%, #64748b 100%); }

        .al-hero-content { position:relative; z-index:2; display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap; }
        .al-hero-icon {
            width:3.5rem; height:3.5rem; border-radius:1rem; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            background:rgba(255,255,255,.15); backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,.2);
        }
        .al-hero-icon svg { width:1.5rem; height:1.5rem; color:#fff; }
        .al-hero-icon .al-hero-emoji { font-size:1.5rem; line-height:1; }
        .al-hero-text { flex:1; min-width:0; }
        .al-hero-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; opacity:.75; }
        .al-hero-title { font-size:1.375rem; font-weight:800; margin-top:.125rem; letter-spacing:-.02em; }
        .al-hero-desc { font-size:.825rem; opacity:.85; margin-top:.25rem; line-height:1.5; }
        .al-hero-time {
            margin-left:auto; text-align:right; flex-shrink:0;
            display:flex; flex-direction:column; align-items:flex-end; gap:.25rem;
        }
        .al-hero-date { font-size:.7rem; opacity:.65; text-transform:uppercase; letter-spacing:.04em; }
        .al-hero-clock { font-size:1.5rem; font-weight:800; letter-spacing:-.02em; }
        .al-hero-ago { font-size:.7rem; opacity:.6; }

        @media(max-width:640px) {
            .al-hero { padding:1.25rem; }
            .al-hero-content { gap:.875rem; }
            .al-hero-title { font-size:1.125rem; }
            .al-hero-time { margin-left:0; text-align:left; align-items:flex-start; width:100%; flex-direction:row; gap:.75rem; }
        }

        /* Cards grid */
        .al-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        @media(max-width:768px) { .al-grid { grid-template-columns:1fr; } }

        .al-card {
            border-radius:1rem; overflow:hidden;
            border:1px solid rgba(100,100,100,.12);
        }
        .dark .al-card { border-color:rgba(100,100,100,.25); }
        .al-card-head {
            padding:.875rem 1.25rem; display:flex; align-items:center; gap:.5rem;
            border-bottom:1px solid rgba(100,100,100,.1);
            background:rgba(100,100,100,.03);
        }
        .dark .al-card-head { background:rgba(0,0,0,.15); border-color:rgba(100,100,100,.18); }
        .al-card-head svg { width:1rem; height:1rem; color:#6b7280; flex-shrink:0; }
        .dark .al-card-head svg { color:#9ca3af; }
        .al-card-title { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
        .al-card-body { padding:1.25rem; }

        /* Info rows */
        .al-info-row {
            display:flex; align-items:flex-start; padding:.625rem 0;
        }
        .al-info-row + .al-info-row { border-top:1px solid rgba(100,100,100,.06); }
        .dark .al-info-row + .al-info-row { border-color:rgba(100,100,100,.12); }
        .al-info-label {
            width:7.5rem; flex-shrink:0; font-size:.75rem; font-weight:600;
            text-transform:uppercase; letter-spacing:.04em; color:#6b7280; padding-top:.125rem;
        }
        .dark .al-info-label { color:#9ca3af; }
        .al-info-value { flex:1; font-size:.85rem; word-break:break-word; }
        .al-info-value a { color:#2563eb; text-decoration:none; }
        .al-info-value a:hover { text-decoration:underline; }
        .dark .al-info-value a { color:#93c5fd; }

        /* Badges */
        .al-badge {
            display:inline-flex; align-items:center; padding:.25rem .625rem;
            border-radius:.5rem; font-size:.75rem; font-weight:600; gap:.25rem;
        }
        .al-badge-success { background:rgba(16,185,129,.12); color:#059669; }
        .al-badge-danger { background:rgba(239,68,68,.12); color:#dc2626; }
        .al-badge-warning { background:rgba(245,158,11,.12); color:#d97706; }
        .al-badge-info { background:rgba(37,99,235,.12); color:#2563eb; }
        .al-badge-gray { background:rgba(100,100,100,.1); color:#6b7280; }
        .dark .al-badge-success { background:rgba(16,185,129,.18); color:#34d399; }
        .dark .al-badge-danger { background:rgba(239,68,68,.18); color:#fca5a5; }
        .dark .al-badge-warning { background:rgba(245,158,11,.18); color:#fbbf24; }
        .dark .al-badge-info { background:rgba(37,99,235,.18); color:#93c5fd; }
        .dark .al-badge-gray { background:rgba(100,100,100,.18); color:#9ca3af; }

        /* Metadata card */
        .al-meta-item {
            display:flex; align-items:center; gap:.75rem;
            padding:.75rem; border-radius:.75rem;
        }
        .al-meta-item + .al-meta-item { margin-top:.375rem; }
        .al-meta-item:nth-child(odd) { background:rgba(100,100,100,.03); }
        .dark .al-meta-item:nth-child(odd) { background:rgba(100,100,100,.07); }
        .al-meta-key {
            min-width:6rem; font-size:.75rem; font-weight:600; color:#6b7280;
            text-transform:uppercase; letter-spacing:.04em;
        }
        .dark .al-meta-key { color:#9ca3af; }
        .al-meta-val { font-size:.825rem; font-weight:500; word-break:break-all; }

        /* User Agent box */
        .al-ua-box {
            font-size:.75rem; font-family:ui-monospace, SFMono-Regular, monospace;
            background:rgba(100,100,100,.04); border:1px solid rgba(100,100,100,.1);
            border-radius:.75rem; padding:.875rem 1rem; word-break:break-all;
            line-height:1.6; color:#475569;
        }
        .dark .al-ua-box { background:rgba(100,100,100,.08); border-color:rgba(100,100,100,.18); color:#94a3b8; }

        /* Full-width card */
        .al-full { grid-column: 1 / -1; }

        /* Description box */
        .al-desc-box {
            background:rgba(100,100,100,.03); border:1px solid rgba(100,100,100,.08);
            border-radius:.75rem; padding:1rem 1.25rem;
            font-size:.85rem; line-height:1.7; color:#374151;
        }
        .dark .al-desc-box { background:rgba(100,100,100,.06); border-color:rgba(100,100,100,.15); color:#d1d5db; }

        /* Device visual */
        .al-device-visual {
            display:flex; align-items:center; gap:1rem;
            padding:1rem; border-radius:.75rem;
            background:rgba(37,99,235,.04); border:1px solid rgba(37,99,235,.1);
        }
        .dark .al-device-visual { background:rgba(37,99,235,.08); border-color:rgba(37,99,235,.15); }
        .al-device-icon {
            width:2.75rem; height:2.75rem; border-radius:.75rem; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            background:rgba(37,99,235,.1);
        }
        .dark .al-device-icon { background:rgba(37,99,235,.2); }
        .al-device-icon svg { width:1.25rem; height:1.25rem; color:#2563eb; }
        .dark .al-device-icon svg { color:#93c5fd; }
        .al-device-info {}
        .al-device-name { font-size:.85rem; font-weight:700; }
        .al-device-detail { font-size:.725rem; color:#6b7280; margin-top:.125rem; }
        .dark .al-device-detail { color:#9ca3af; }
    </style>

    @php
        $record = $this->record;
        $activity = $record->activity;
        $user = $record->user;
        $metadata = $record->metadata ?? [];
        $createdAt = $record->created_at;

        // Categorize activity for hero color
        $heroClass = match(true) {
            $activity === 'login' => 'al-hero-login',
            $activity === 'logout' => 'al-hero-logout',
            in_array($activity, ['login_failed', 'delete_siswa', 'delete_guru', 'delete_kesiswaan', 'delete_kelas', 'delete_role', 'delete_form_setting', 'bulk_delete_siswa', 'bulk_delete_guru', 'bulk_delete_kesiswaan', 'reject_submission', 'reject_validation', 'bulk_reject_submission', 'bulk_reject_validation', 'backup_and_delete_data', 'delete_submission', 'bulk_delete_submission']) => 'al-hero-danger',
            in_array($activity, ['verify_submission', 'validate_submission', 'bulk_verify_submission', 'bulk_validate_submission', 'create_siswa', 'create_guru', 'create_kesiswaan', 'create_kelas', 'create_role', 'create_form_setting']) => 'al-hero-success',
            in_array($activity, ['edit_siswa', 'edit_guru', 'edit_kesiswaan', 'edit_kelas', 'edit_role', 'edit_form_setting', 'reset_password', 'reset_session', 'reset_submission', 'reset_validation', 'add_siswa_to_kelas', 'remove_siswa_from_kelas', 'bulk_remove_siswa_from_kelas']) => 'al-hero-warning',
            in_array($activity, ['import_siswa', 'import_guru', 'import_kesiswaan', 'import_kelas', 'export_siswa', 'export_guru', 'export_kesiswaan', 'export_rekap', 'submit_form']) => 'al-hero-info',
            default => 'al-hero-default',
        };

        // Activity labels with emoji
        $activityLabel = match($activity) {
            'login' => 'Login Berhasil',
            'logout' => 'Logout',
            'login_failed' => 'Gagal Login',
            'verify_submission' => 'Verifikasi Formulir',
            'reject_submission' => 'Tolak Verifikasi',
            'reset_submission' => 'Reset Verifikasi',
            'bulk_verify_submission' => 'Verifikasi Massal',
            'validate_submission' => 'Validasi Formulir',
            'reject_validation' => 'Tolak Validasi',
            'reset_validation' => 'Reset Validasi',
            'bulk_validate_submission' => 'Validasi Massal',
            'bulk_reject_submission' => 'Tolak Massal Verifikasi',
            'bulk_reject_validation' => 'Tolak Massal Validasi',
            'create_siswa' => 'Tambah Data Siswa',
            'edit_siswa' => 'Edit Data Siswa',
            'delete_siswa' => 'Hapus Data Siswa',
            'bulk_delete_siswa' => 'Hapus Massal Siswa',
            'create_guru' => 'Tambah Data Guru',
            'edit_guru' => 'Edit Data Guru',
            'delete_guru' => 'Hapus Data Guru',
            'bulk_delete_guru' => 'Hapus Massal Guru',
            'create_kesiswaan' => 'Tambah Data Kesiswaan',
            'edit_kesiswaan' => 'Edit Data Kesiswaan',
            'delete_kesiswaan' => 'Hapus Data Kesiswaan',
            'bulk_delete_kesiswaan' => 'Hapus Massal Kesiswaan',
            'create_kelas' => 'Tambah Kelas',
            'edit_kelas' => 'Edit Kelas',
            'delete_kelas' => 'Hapus Kelas',
            'import_siswa' => 'Import Data Siswa',
            'import_guru' => 'Import Data Guru',
            'import_kesiswaan' => 'Import Data Kesiswaan',
            'import_kelas' => 'Import Data Kelas',
            'export_siswa' => 'Export Data Siswa',
            'export_guru' => 'Export Data Guru',
            'export_kesiswaan' => 'Export Data Kesiswaan',
            'export_rekap' => 'Export Data Rekap',
            'reset_password' => 'Reset Password',
            'reset_session' => 'Reset Sesi Login',
            'update_profile' => 'Update Profil',
            'change_password' => 'Ubah Password',
            'submit_form' => 'Submit Formulir Harian',
            'backup_data' => 'Backup Data',
            'backup_and_delete_data' => 'Backup & Hapus Data',
            'delete_submission' => 'Hapus Formulir',
            'bulk_delete_submission' => 'Hapus Massal Formulir',
            'create_role' => 'Tambah Role',
            'edit_role' => 'Edit Role',
            'delete_role' => 'Hapus Role',
            'create_form_setting' => 'Tambah Pengaturan Formulir',
            'edit_form_setting' => 'Edit Pengaturan Formulir',
            'delete_form_setting' => 'Hapus Pengaturan Formulir',
            'add_siswa_to_kelas' => 'Tambah Siswa ke Kelas',
            'remove_siswa_from_kelas' => 'Keluarkan Siswa dari Kelas',
            'bulk_remove_siswa_from_kelas' => 'Keluarkan Siswa Massal',
            default => ucfirst(str_replace('_', ' ', $activity)),
        };

        // Descriptive sentence about what happened
        $activityDescription = match(true) {
            $activity === 'login' => ($user?->name ?? 'Seseorang') . ' berhasil masuk ke sistem melalui panel ' . ucfirst($record->panel ?? 'tidak diketahui') . '.',
            $activity === 'logout' => ($user?->name ?? 'Seseorang') . ' keluar dari sistem.',
            $activity === 'login_failed' => 'Percobaan login gagal terdeteksi dari alamat IP ' . ($record->ip_address ?? 'tidak diketahui') . '.',
            str_starts_with($activity, 'verify_') || str_starts_with($activity, 'bulk_verify_') => ($user?->name ?? 'Seseorang') . ' melakukan verifikasi terhadap formulir submission siswa.',
            str_starts_with($activity, 'validate_') || str_starts_with($activity, 'bulk_validate_') => ($user?->name ?? 'Seseorang') . ' melakukan validasi terhadap formulir submission siswa.',
            str_starts_with($activity, 'reject_') || str_starts_with($activity, 'bulk_reject_') => ($user?->name ?? 'Seseorang') . ' menolak formulir submission siswa.',
            str_starts_with($activity, 'reset_submission') || str_starts_with($activity, 'reset_validation') => ($user?->name ?? 'Seseorang') . ' mereset status formulir submission siswa.',
            str_starts_with($activity, 'create_') => ($user?->name ?? 'Seseorang') . ' menambahkan data baru ke dalam sistem.',
            str_starts_with($activity, 'edit_') => ($user?->name ?? 'Seseorang') . ' mengubah data yang ada di sistem.',
            str_starts_with($activity, 'delete_') || str_starts_with($activity, 'bulk_delete_') => ($user?->name ?? 'Seseorang') . ' menghapus data dari sistem.',
            str_starts_with($activity, 'import_') => ($user?->name ?? 'Seseorang') . ' mengimpor data ke dalam sistem.',
            str_starts_with($activity, 'export_') => ($user?->name ?? 'Seseorang') . ' mengekspor data dari sistem.',
            $activity === 'reset_password' => ($user?->name ?? 'Seseorang') . ' mereset password pengguna lain.',
            $activity === 'reset_session' => ($user?->name ?? 'Seseorang') . ' mereset sesi login pengguna lain.',
            $activity === 'submit_form' => ($user?->name ?? 'Seseorang') . ' mengirimkan formulir harian.',
            $activity === 'backup_data' => ($user?->name ?? 'Seseorang') . ' membuat backup data sistem.',
            $activity === 'backup_and_delete_data' => ($user?->name ?? 'Seseorang') . ' membuat backup data kemudian menghapus data asli.',
            $activity === 'change_password' => ($user?->name ?? 'Seseorang') . ' mengubah password akun sendiri.',
            $activity === 'update_profile' => ($user?->name ?? 'Seseorang') . ' memperbarui profil akun.',
            $activity === 'add_siswa_to_kelas' => ($user?->name ?? 'Seseorang') . ' menambahkan siswa ke dalam kelas.',
            str_contains($activity, 'remove_siswa_from_kelas') => ($user?->name ?? 'Seseorang') . ' mengeluarkan siswa dari kelas.',
            default => ($user?->name ?? 'Seseorang') . ' melakukan aktivitas di sistem.',
        };

        // Badge class mapping
        $roleBadge = match(strtolower($record->role ?? '')) {
            'super admin', 'superadmin' => 'al-badge-danger',
            'guru' => 'al-badge-warning',
            'kesiswaan' => 'al-badge-info',
            'siswa' => 'al-badge-success',
            default => 'al-badge-gray',
        };

        $panelLabel = match($record->panel) {
            'superadmin' => 'Portal Superadmin',
            'guru' => 'Portal Guru',
            'kesiswaan' => 'Portal Kesiswaan',
            'siswa' => 'Portal Siswa',
            default => $record->panel ?? '-',
        };

        // Device icon
        $device = $record->device;
        $browser = $record->browser;

        // Separate internal metadata keys from display
        $displayMeta = collect($metadata)->except(['lat', 'lon'])->filter(fn($v) => $v !== null && $v !== '');
    @endphp

    <div class="al-wrap">
        {{-- Back link --}}
        <div>
            <a href="{{ \App\Filament\Superadmin\Resources\ActivityLogResource::getUrl('index') }}" class="al-back">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                Kembali ke Log Aktivitas
            </a>
        </div>

        {{-- Hero Banner --}}
        <div class="al-hero {{ $heroClass }}">
            <div class="al-hero-content">
                <div class="al-hero-icon">
                    @switch(true)
                        @case($activity === 'login')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                            @break
                        @case($activity === 'logout')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                            @break
                        @case($activity === 'login_failed')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                            @break
                        @case(str_contains($activity, 'delete') || str_contains($activity, 'reject'))
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            @break
                        @case(str_contains($activity, 'create'))
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            @break
                        @case(str_contains($activity, 'edit') || str_contains($activity, 'reset'))
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                            @break
                        @case(str_contains($activity, 'import'))
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            @break
                        @case(str_contains($activity, 'export'))
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            @break
                        @case(str_contains($activity, 'verify') || str_contains($activity, 'validate'))
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                            @break
                        @case(str_contains($activity, 'submit'))
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            @break
                        @case(str_contains($activity, 'backup'))
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>
                            @break
                        @default
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endswitch
                </div>
                <div class="al-hero-text">
                    <div class="al-hero-label">Log Aktivitas</div>
                    <div class="al-hero-title">{{ $activityLabel }}</div>
                    <div class="al-hero-desc">{{ $activityDescription }}</div>
                </div>
                <div class="al-hero-time">
                    <div class="al-hero-date">{{ $createdAt->translatedFormat('l, d F Y') }}</div>
                    <div class="al-hero-clock">{{ $createdAt->format('H:i:s') }}</div>
                    <div class="al-hero-ago">{{ $createdAt->diffForHumans() }}</div>
                </div>
            </div>
        </div>

        {{-- Cards Grid --}}
        <div class="al-grid">
            {{-- Card 1: User Info --}}
            <div class="al-card">
                <div class="al-card-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    <span class="al-card-title">Informasi Pengguna</span>
                </div>
                <div class="al-card-body">
                    @if($user)
                        <div class="al-info-row">
                            <span class="al-info-label">Nama</span>
                            <span class="al-info-value" style="font-weight:600;">{{ $user->name }}</span>
                        </div>
                        @if($user->email)
                        <div class="al-info-row">
                            <span class="al-info-label">Email</span>
                            <span class="al-info-value">{{ $user->email }}</span>
                        </div>
                        @endif
                        @if($user->nisn)
                        <div class="al-info-row">
                            <span class="al-info-label">NISN</span>
                            <span class="al-info-value" style="font-family:ui-monospace,monospace;">{{ $user->nisn }}</span>
                        </div>
                        @endif
                        <div class="al-info-row">
                            <span class="al-info-label">Role</span>
                            <span class="al-info-value">
                                <span class="al-badge {{ $roleBadge }}">{{ $record->role ?? '-' }}</span>
                            </span>
                        </div>
                        <div class="al-info-row">
                            <span class="al-info-label">Panel</span>
                            <span class="al-info-value">
                                <span class="al-badge al-badge-gray">{{ $panelLabel }}</span>
                            </span>
                        </div>
                    @else
                        <div style="text-align:center; padding:1.5rem; color:#9ca3af;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" style="width:2rem;height:2rem;margin:0 auto .5rem;display:block;opacity:.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            <div style="font-size:.825rem; font-weight:600;">Pengguna Tidak Diketahui</div>
                            <div style="font-size:.725rem; margin-top:.25rem;">Akun mungkin telah dihapus atau aktivitas dilakukan oleh sistem.</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card 2: Network & Location --}}
            <div class="al-card">
                <div class="al-card-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03v.568c0 .334.148.65.405.864l1.068.89c.442.369.535 1.01.216 1.49l-.51.766a2.25 2.25 0 01-1.161.886l-.143.048a1.107 1.107 0 00-.57 1.664c.369.555.169 1.307-.427 1.605L9 13.125l.423 1.059a.956.956 0 01-1.652.928l-.679-.906a1.125 1.125 0 00-1.906.172L4.5 15.75l-.612.153M12.75 3.031a9 9 0 00-8.862 12.872M12.75 3.031a9 9 0 016.69 14.036m0 0l-.177-.529A2.25 2.25 0 0017.128 15H16.5l-.324-.324a1.453 1.453 0 00-2.328.377l-.036.073a1.586 1.586 0 01-.982.816l-.99.282c-.55.157-.894.702-.8 1.267l.073.438c.08.474.49.821.97.821.846 0 1.598.542 1.865 1.345l.215.643m5.276-3.67a9.012 9.012 0 01-5.276 3.67m0 0a9 9 0 01-10.275-4.835M15.898 21l-5.16-5.16"/></svg>
                    <span class="al-card-title">Jaringan & Lokasi</span>
                </div>
                <div class="al-card-body">
                    <div class="al-info-row">
                        <span class="al-info-label">IP Address</span>
                        <span class="al-info-value" style="font-family:ui-monospace,monospace; font-weight:600;">{{ $record->ip_address ?? '-' }}</span>
                    </div>
                    <div class="al-info-row">
                        <span class="al-info-label">Lokasi</span>
                        <span class="al-info-value">
                            @if($record->location)
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:.875rem;height:.875rem;display:inline;vertical-align:middle;margin-right:.25rem;color:#dc2626;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                {{ $record->location }}
                                @if(($metadata['lat'] ?? null) && ($metadata['lon'] ?? null))
                                    <br><a href="https://maps.google.com/?q={{ $metadata['lat'] }},{{ $metadata['lon'] }}" target="_blank" rel="noopener" style="font-size:.725rem;">
                                        Buka di Google Maps
                                    </a>
                                @endif
                            @else
                                <span style="color:#9ca3af;">Tidak diketahui</span>
                            @endif
                        </span>
                    </div>

                    {{-- Device visual --}}
                    <div style="margin-top:.75rem;">
                        <div class="al-device-visual">
                            <div class="al-device-icon">
                                @if($device === 'Android' || $device === 'iOS')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                                @else
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25z"/></svg>
                                @endif
                            </div>
                            <div class="al-device-info">
                                <div class="al-device-name">{{ $browser }} — {{ $device }}</div>
                                <div class="al-device-detail">Terdeteksi dari User Agent browser pengguna</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Description (full width if exists) --}}
            @if($record->description)
            <div class="al-card al-full">
                <div class="al-card-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                    <span class="al-card-title">Keterangan Aktivitas</span>
                </div>
                <div class="al-card-body">
                    <div class="al-desc-box">{{ $record->description }}</div>
                </div>
            </div>
            @endif

            {{-- Card 4: Metadata (full width if exists) --}}
            @if($displayMeta->isNotEmpty())
            <div class="al-card al-full">
                <div class="al-card-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/></svg>
                    <span class="al-card-title">Detail Metadata</span>
                </div>
                <div class="al-card-body">
                    @foreach($displayMeta as $key => $value)
                        <div class="al-meta-item">
                            <span class="al-meta-key">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                            <span class="al-meta-val">
                                @if(is_array($value))
                                    <code style="font-size:.75rem; background:rgba(100,100,100,.06); padding:.125rem .375rem; border-radius:.25rem;">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                                @else
                                    {{ $value }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Card 5: User Agent (full width) --}}
            <div class="al-card al-full">
                <div class="al-card-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z"/></svg>
                    <span class="al-card-title">User Agent</span>
                </div>
                <div class="al-card-body">
                    <div class="al-ua-box">{{ $record->user_agent ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Footer info --}}
        <div style="text-align:center; font-size:.7rem; color:#9ca3af; padding:.5rem 0;">
            ID: <span style="font-family:ui-monospace,monospace;">{{ $record->id }}</span>
        </div>
    </div>
</x-filament-panels::page>
