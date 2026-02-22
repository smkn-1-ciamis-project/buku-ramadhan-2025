{{-- ══════════════════════════════════════════════
     Password Change Popup Modal (non-closable)
     Include this in all Siswa dashboard views:
     @include('components.password-change-modal')
══════════════════════════════════════════════ --}}

<div
    x-show="$wire.showPasswordModal"
    x-cloak
    class="pcm-overlay"
    style="display: none;"
>
    <div class="pcm-backdrop"></div>

    <div class="pcm-modal" x-data="{
        showPw: false,
        showPwConf: false,
        pw: $wire.entangle('new_password'),
        pwConf: $wire.entangle('new_password_confirmation'),
        nisn: '{{ auth()->user()?->nisn ?? '' }}',
        get minLen() { return this.pw.length >= 8 },
        get match() { return this.pw.length > 0 && this.pw === this.pwConf },
        get notNisn() { return this.nisn === '' || this.pw !== this.nisn }
    }" @click.outside="">

        {{-- Header --}}
        <div class="pcm-header">
            <div class="pcm-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>
            <h3 class="pcm-title">Ganti Password</h3>
            <p class="pcm-subtitle">Untuk keamanan akun, silakan buat password baru sebelum melanjutkan.</p>
        </div>

        {{-- Body --}}
        <div class="pcm-body">

            {{-- Info box --}}
            <div class="pcm-info" style="background: #fef2f2; border-color: #fca5a5; color: #991b1b;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="color: #dc2626;">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                <span><strong>PERINGATAN!</strong> Password Anda masih menggunakan NISN. Demi keamanan akun, Anda <strong>WAJIB</strong> mengganti password sekarang!</span>
            </div>

            <form wire:submit="simpanPasswordBaru">

                {{-- Password Baru --}}
                <div class="pcm-field">
                    <label for="pcm-pw">Password Baru</label>
                    <div class="pcm-input-wrap">
                        <input
                            id="pcm-pw"
                            :type="showPw ? 'text' : 'password'"
                            wire:model.live="new_password"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                        />
                        <button type="button" class="pcm-eye" @click="showPw = !showPw" tabindex="-1">
                            <svg x-show="!showPw" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <svg x-show="showPw" style="display:none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                        </button>
                    </div>
                    @error('new_password') <div class="pcm-error">{{ $message }}</div> @enderror
                </div>

                {{-- Konfirmasi --}}
                <div class="pcm-field">
                    <label for="pcm-pw-conf">Konfirmasi Password Baru</label>
                    <div class="pcm-input-wrap">
                        <input
                            id="pcm-pw-conf"
                            :type="showPwConf ? 'text' : 'password'"
                            wire:model.live="new_password_confirmation"
                            placeholder="Ketik ulang password baru"
                            autocomplete="new-password"
                        />
                        <button type="button" class="pcm-eye" @click="showPwConf = !showPwConf" tabindex="-1">
                            <svg x-show="!showPwConf" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <svg x-show="showPwConf" style="display:none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                        </button>
                    </div>
                    @error('new_password_confirmation') <div class="pcm-error">{{ $message }}</div> @enderror
                </div>

                {{-- Checklist --}}
                <ul class="pcm-reqs">
                    <li :class="{ 'met': minLen }"><span class="req-dot"></span> Minimal 8 karakter</li>
                    <li :class="{ 'met': match }"><span class="req-dot"></span> Password & konfirmasi cocok</li>
                    <li :class="{ 'met': notNisn, 'fail': !notNisn && pw.length > 0 }"><span class="req-dot"></span> Password tidak boleh sama dengan NISN</li>
                </ul>

                {{-- Submit --}}
                <button type="submit" class="pcm-btn" wire:loading.attr="disabled" :disabled="!minLen || !match || !notNisn">
                    <span wire:loading.remove wire:target="simpanPasswordBaru">Simpan Password Baru</span>
                    <span wire:loading wire:target="simpanPasswordBaru" style="display:none">Menyimpan…</span>
                </button>
            </form>

        </div>
    </div>
</div>

<style>
    .pcm-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .pcm-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }
    .pcm-modal {
        position: relative;
        background: #fff;
        border-radius: 20px;
        width: 100%;
        max-width: 430px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: pcmSlideIn 0.35s ease-out;
    }
    @keyframes pcmSlideIn {
        from { opacity: 0; transform: translateY(30px) scale(0.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Header */
    .pcm-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #3b82f6 100%);
        padding: 28px 24px 22px;
        text-align: center;
        position: relative;
    }
    .pcm-header::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 0; right: 0;
        height: 18px;
        background: #fff;
        border-radius: 18px 18px 0 0;
    }
    .pcm-icon {
        width: 50px; height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 12px;
    }
    .pcm-icon svg { width: 26px; height: 26px; color: #fff; }
    .pcm-title { color: #fff; font-size: 1.2rem; font-weight: 700; margin: 0 0 4px; }
    .pcm-subtitle { color: rgba(255,255,255,0.85); font-size: 0.8rem; margin: 0; line-height: 1.45; }

    /* Body */
    .pcm-body { padding: 24px; }
    .pcm-info {
        background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px;
        padding: 10px 12px; margin-bottom: 18px;
        display: flex; gap: 8px; align-items: flex-start;
    }
    .pcm-info svg { width: 16px; height: 16px; color: #1d4ed8; flex-shrink: 0; margin-top: 2px; }
    .pcm-info span { font-size: 0.78rem; color: #1e40af; line-height: 1.45; }

    /* Form */
    .pcm-field { margin-bottom: 14px; }
    .pcm-field label { display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 5px; }
    .pcm-input-wrap { position: relative; }
    .pcm-input-wrap input {
        width: 100%;
        padding: 9px 38px 9px 12px;
        border: 1.5px solid #d1d5db;
        border-radius: 10px;
        font-size: 0.88rem;
        color: #1f2937;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
        box-sizing: border-box;
    }
    .pcm-input-wrap input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    }
    .pcm-eye {
        position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
        cursor: pointer; color: #9ca3af; background: none; border: none; padding: 3px;
        display: flex; align-items: center;
    }
    .pcm-eye:hover { color: #6b7280; }
    .pcm-eye svg { width: 17px; height: 17px; }

    .pcm-error { color: #dc2626; font-size: 0.75rem; margin-top: 3px; }

    .pcm-reqs { margin: 0 0 16px; padding: 0; list-style: none; font-size: 0.75rem; color: #6b7280; }
    .pcm-reqs li { display: flex; align-items: center; gap: 5px; margin-bottom: 3px; }
    .pcm-reqs li .req-dot { width: 6px; height: 6px; background: #d1d5db; border-radius: 50%; flex-shrink: 0; transition: background 0.2s; }
    .pcm-reqs li.met .req-dot { background: #22c55e; }
    .pcm-reqs li.met { color: #16a34a; }
    .pcm-reqs li.fail .req-dot { background: #ef4444; }
    .pcm-reqs li.fail { color: #dc2626; font-weight: 600; }

    .pcm-btn {
        width: 100%; padding: 11px;
        background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
        color: #fff; font-size: 0.9rem; font-weight: 600;
        border: none; border-radius: 10px; cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .pcm-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(29,78,216,0.3); }
    .pcm-btn:disabled { opacity: 0.55; cursor: not-allowed; transform: none; box-shadow: none; }

    @media (max-width: 480px) {
        .pcm-modal { border-radius: 16px; max-width: 100%; }
        .pcm-header { padding: 22px 18px 18px; }
        .pcm-body { padding: 18px; }
    }
</style>
