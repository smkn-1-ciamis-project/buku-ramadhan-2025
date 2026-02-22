<x-filament-panels::page>
    <style>
        .sa-profil-container {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 768px) {
            .sa-profil-container {
                grid-template-columns: 1fr;
            }
        }
        .sa-profil-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .sa-profil-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #3b82f6 100%);
            padding: 20px 20px;
            text-align: center;
            position: relative;
        }
        .sa-profil-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            border: 2px solid rgba(255,255,255,0.4);
        }
        .sa-profil-avatar span {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
        }
        .sa-profil-header h3 {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 2px;
        }
        .sa-profil-header p {
            color: rgba(255,255,255,0.8);
            font-size: 12px;
            margin: 0;
        }
        .sa-profil-body {
            padding: 16px 20px;
        }
        .sa-profil-field {
            margin-bottom: 12px;
        }
        .sa-profil-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .sa-profil-input {
            width: 100%;
            padding: 8px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            color: #1e293b;
            background: #f8fafc;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            box-sizing: border-box;
        }
        .sa-profil-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
            background: #fff;
        }
        .sa-profil-hint {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 4px;
        }
        .sa-profil-btn {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
            margin-top: 4px;
        }
        .sa-profil-btn:hover {
            opacity: 0.9;
        }
        .sa-profil-btn:active {
            transform: scale(0.98);
        }
        .sa-profil-role {
            padding: 10px 20px;
            background: #eff6ff;
            border-top: 1px solid #bfdbfe;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sa-profil-role-icon {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            background: linear-gradient(135deg, #bfdbfe, #93c5fd);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sa-profil-role-icon svg {
            width: 16px;
            height: 16px;
            color: #1e3a8a;
        }
        .sa-profil-role-text {
            flex: 1;
        }
        .sa-profil-role-label {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .sa-profil-role-value {
            font-size: 13px;
            font-weight: 700;
            color: #1e3a8a;
        }
        /* Password section */
        .sa-profil-pw-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sa-profil-pw-header-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sa-profil-pw-header-icon svg {
            width: 20px;
            height: 20px;
            color: #fff;
        }
        .sa-profil-pw-header-text h4 {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 2px;
        }
        .sa-profil-pw-header-text p {
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            margin: 0;
        }
        .sa-profil-pw-toggle {
            position: relative;
        }
        .sa-profil-pw-toggle .sa-profil-pw-eye {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 4px;
            display: flex;
            align-items: center;
        }
        .sa-profil-pw-toggle .sa-profil-pw-eye:hover {
            color: #3b82f6;
        }
        .sa-profil-pw-toggle .sa-profil-input {
            padding-right: 40px;
        }
    </style>

    @php
        $admin = auth()->user();
        $roleName = $admin->role_user?->name ?? 'Superadmin';
    @endphp

    <div class="sa-profil-container">
        {{-- Profil Card --}}
        <div class="sa-profil-card">
            <div class="sa-profil-header">
                <div class="sa-profil-avatar">
                    <span>{{ strtoupper(substr($admin->name ?? 'A', 0, 1)) }}</span>
                </div>
                <h3>{{ $admin->name }}</h3>
                <p>{{ $admin->email ?? 'Superadmin' }}</p>
            </div>

            <div class="sa-profil-role">
                <div class="sa-profil-role-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                </div>
                <div class="sa-profil-role-text">
                    <div class="sa-profil-role-label">Role</div>
                    <div class="sa-profil-role-value">{{ $roleName }}</div>
                </div>
            </div>

            <form wire:submit="simpan" class="sa-profil-body">
                <div class="sa-profil-field">
                    <label class="sa-profil-label">Nama Lengkap</label>
                    <input type="text" wire:model="name" class="sa-profil-input" placeholder="Nama lengkap">
                    @error('name') <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <div class="sa-profil-field">
                    <label class="sa-profil-label">Email</label>
                    <input type="email" wire:model="email" class="sa-profil-input" placeholder="email@contoh.com">
                    @error('email') <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <div class="sa-profil-field">
                    <label class="sa-profil-label">No. HP / WhatsApp</label>
                    <input type="text" wire:model="no_hp" class="sa-profil-input" placeholder="08xxxxxxxxxx">
                    @error('no_hp') <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="sa-profil-btn">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        {{-- Ubah Password Card --}}
        <div class="sa-profil-card" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
            <div class="sa-profil-pw-header">
                <div class="sa-profil-pw-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                </div>
                <div class="sa-profil-pw-header-text">
                    <h4>Ubah Password</h4>
                    <p>Perbarui kata sandi akun Anda</p>
                </div>
            </div>

            <form wire:submit="ubahPassword" class="sa-profil-body">
                <div class="sa-profil-field">
                    <label class="sa-profil-label">Password Saat Ini</label>
                    <div class="sa-profil-pw-toggle">
                        <input :type="showCurrent ? 'text' : 'password'" wire:model="current_password" class="sa-profil-input" placeholder="Masukkan password saat ini">
                        <button type="button" class="sa-profil-pw-eye" @click="showCurrent = !showCurrent">
                            <svg x-show="!showCurrent" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            <svg x-show="showCurrent" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                    @error('current_password') <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <div class="sa-profil-field">
                    <label class="sa-profil-label">Password Baru</label>
                    <div class="sa-profil-pw-toggle">
                        <input :type="showNew ? 'text' : 'password'" wire:model="new_password" class="sa-profil-input" placeholder="Masukkan password baru">
                        <button type="button" class="sa-profil-pw-eye" @click="showNew = !showNew">
                            <svg x-show="!showNew" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            <svg x-show="showNew" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                    <div class="sa-profil-hint">Minimal 8 karakter</div>
                    @error('new_password') <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <div class="sa-profil-field">
                    <label class="sa-profil-label">Konfirmasi Password Baru</label>
                    <div class="sa-profil-pw-toggle">
                        <input :type="showConfirm ? 'text' : 'password'" wire:model="new_password_confirmation" class="sa-profil-input" placeholder="Ulangi password baru">
                        <button type="button" class="sa-profil-pw-eye" @click="showConfirm = !showConfirm">
                            <svg x-show="!showConfirm" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            <svg x-show="showConfirm" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                    @error('new_password_confirmation') <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="sa-profil-btn">
                    Ubah Password
                </button>
            </form>
        </div>
    </div>
</x-filament-panels::page>
