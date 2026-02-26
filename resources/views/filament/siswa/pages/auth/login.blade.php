<div class="login-page" x-data="{ showDevicePopup: $wire.entangle('showDevicePopup'), showErrorPopup: $wire.entangle('showErrorPopup'), showLockPopup: $wire.entangle('showLockPopup'), lockSeconds: $wire.entangle('lockSeconds'), lockTimer: null }"
     x-effect="if (showLockPopup && lockSeconds > 0 && !lockTimer) { lockTimer = setInterval(() => { lockSeconds--; if (lockSeconds <= 0) { clearInterval(lockTimer); lockTimer = null; showLockPopup = false; } }, 1000); }">

  {{-- ── Inject Login CSS ── --}}
  @once
    @push('styles')
      <link rel="stylesheet" href="{{ asset('themes/ramadhan/css/login.css') }}" />
      <style>
        html, body { height: 100% !important; background: #f1f5f9 !important; padding: 0 !important; margin: 0 !important; }
        @media(min-width:768px) { html, body { overflow: hidden !important; } }
        @media(max-width:767px) { html, body { overflow-y: auto !important; overflow-x: hidden !important; height: auto !important; min-height: 100% !important; } }
        .fi-simple-layout { background: transparent !important; min-height: 100vh !important; }
        @media(min-width:768px) { .fi-simple-layout { overflow: hidden !important; } }
        @media(max-width:767px) { .fi-simple-layout { overflow: visible !important; min-height: auto !important; height: auto !important; } }
        .fi-simple-main-ctn { max-width: none !important; padding: 0 !important; width: 100% !important; overflow: hidden !important; flex-grow: 0 !important; }
        @media(max-width:767px) { .fi-simple-main-ctn { overflow: visible !important; } }
        .fi-simple-main { max-width: none !important; width: 100% !important; background: transparent !important; box-shadow: none !important; ring: none !important; padding: 0 !important; --tw-ring-shadow: none !important; --tw-shadow: none !important; margin: 0 !important; }
        .login-form-area .fi-ac-btn-action { width: 100% !important; }
        .login-form-area .fi-form-actions { margin-top: 6px !important; }
        /* Hide inline validation errors — use popup notification instead */
        .login-form-area .fi-fo-field-wrp-error-message { display: none !important; }
        .login-form-area .fi-input-wrp.fi-invalid { --tw-ring-color: rgba(59,130,246,.3) !important; }
        /* ── Remember-me checkbox alignment ── */
        .login-form-area .fi-fo-checkbox label,
        .login-form-area .fi-checkbox-label { display: flex !important; align-items: center !important; gap: 0.5rem !important; }
        .login-form-area .fi-fo-checkbox input[type="checkbox"] { margin: 0 !important; flex-shrink: 0; align-self: center !important; }
      </style>
    @endpush
  @endonce

  {{-- ═══════════════════════════════
      MAIN 2-COLUMN CARD
  ═══════════════════════════════ --}}
  <div class="login-container">

    {{-- ── LEFT: Form Column ── --}}
    <div class="login-form-col">

      {{-- Form Title --}}
      <div class="login-form-title">Login</div>
      <div class="login-form-subtitle">Masuk ke akun Calakan Anda</div>

      {{-- Session Expired Alert --}}
      @if(session('session_expired'))
        <div class="login-alert">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.345 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
          </svg>
          <span>{{ session('session_expired') }}</span>
        </div>
      @endif

      {{-- Filament Form --}}
      <div class="login-form-area">
        <form wire:submit="authenticate">
          {{ $this->form }}

          <x-filament::button
            type="submit"
            class="w-full"
            wire:loading.attr="disabled"
          >
            <span wire:loading.remove style="color: white !important;">Masuk</span>
            <span wire:loading style="display:none; color: white !important;">Memproses…</span>
          </x-filament::button>
        </form>
      </div>

      {{-- Footer --}}
      <div class="login-form-footer">
        <div class="login-form-footer-copy">
          <a href="{{ route('tim-pengembang') }}" target="_blank" rel="noopener noreferrer">
            &copy; {{ date('Y') }} Calakan — SMKN 1 Ciamis
          </a>
        </div>
      </div>

    </div>{{-- /.login-form-col --}}

    {{-- ── RIGHT: Illustration Column ── --}}
    <div class="login-illust-col">

      {{-- Decorative shapes --}}
      <div class="illust-shape illust-shape--1"></div>
      <div class="illust-shape illust-shape--2"></div>
      <div class="illust-shape illust-shape--3"></div>

      {{-- Decorative stars --}}
      <div class="illust-star illust-star--1">✦</div>
      <div class="illust-star illust-star--2">✦</div>
      <div class="illust-star illust-star--3">✦</div>
      <div class="illust-star illust-star--4">✦</div>

      {{-- Check circles --}}
      <div class="illust-check illust-check--1">
        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div class="illust-check illust-check--2">
        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      </div>

      {{-- Main illustration --}}
      <div class="illust-main">
        <div class="illust-logo-icon">
          <img src="{{ asset('img/logo_smk.png') }}" alt="Logo SMKN 1 Ciamis">
        </div>

        <div class="illust-title">Calakan</div>
        <div class="illust-subtitle">Catatan Amaliyah Kegiatan Ramadan<br>SMKN 1 Ciamis</div>

        {{-- Religion icons row --}}
        <div class="illust-religion-icons">
          {{-- Islam: Bulan sabit & bintang --}}
          <div class="illust-religion-item" title="Islam">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M17.715 15.15A6.5 6.5 0 0 1 9 6.035C6.106 6.922 4 9.645 4 12.867c0 3.94 3.153 7.136 7.042 7.136 3.101 0 5.734-2.032 6.673-4.853Z"/>
              <polygon points="18.5,1 19.1,2.9 21,2.9 19.5,4.2 20.2,6.1 18.5,4.8 16.8,6.1 17.5,4.2 16,2.9 17.9,2.9"/>
            </svg>
          </div>
          {{-- Kristen: Salib Latin --}}
          <div class="illust-religion-item" title="Kristen">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 2v5H5v4h5v11h4V11h5V7h-5V2z"/></svg>
          </div>
          {{-- Katolik: Salib Keltik (salib + lingkaran) --}}
          <div class="illust-religion-item" title="Katolik">
            <svg viewBox="0 0 24 24">
              <rect x="10.75" y="1.5" width="2.5" height="21" rx="1" fill="currentColor"/>
              <rect x="2.5" y="8.75" width="19" height="2.5" rx="1" fill="currentColor"/>
              <circle cx="12" cy="10" r="5.2" fill="none" stroke="currentColor" stroke-width="2"/>
            </svg>
          </div>
          {{-- Hindu: Om --}}
          <div class="illust-religion-item" title="Hindu">
            <svg viewBox="0 0 24 24" fill="currentColor"><text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" font-size="20" font-family="serif">ॐ</text></svg>
          </div>
          {{-- Buddha: Dharmachakra --}}
          <div class="illust-religion-item" title="Buddha">
            <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="2.5"/><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.8"/><line x1="12" y1="3" x2="12" y2="21" stroke="currentColor" stroke-width="1.5"/><line x1="3" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="1.5"/><line x1="5.6" y1="5.6" x2="18.4" y2="18.4" stroke="currentColor" stroke-width="1.5"/><line x1="18.4" y1="5.6" x2="5.6" y2="18.4" stroke="currentColor" stroke-width="1.5"/></svg>
          </div>
          {{-- Konghucu: Yin-Yang --}}
          <div class="illust-religion-item" title="Konghucu">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 1a9 9 0 010 18c-2.5 0-4.5-2.02-4.5-4.5S9.5 12 12 12s4.5-2.02 4.5-4.5S14.5 3 12 3z"/><circle cx="12" cy="7.5" r="1.5" fill="white"/><circle cx="12" cy="16.5" r="1.5"/></svg>
          </div>
        </div>
      </div>

      {{-- Bottom decorative wave --}}
      <div class="illust-bottom-wave">
        <svg viewBox="0 0 400 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
          <path d="M0,40 Q50,10 100,30 T200,25 T300,35 T400,20 L400,60 L0,60 Z" />
          <path d="M0,50 Q60,25 120,40 T240,35 T360,45 T400,30 L400,60 L0,60 Z" opacity="0.5"/>
        </svg>
      </div>

    </div>{{-- /.login-illust-col --}}

  </div>{{-- /.login-container --}}

  {{-- ═══════════════════════════════
       POPUP: Akun aktif di perangkat lain
  ═══════════════════════════════ --}}
  <div x-show="showDevicePopup"
       x-cloak
       class="login-popup-overlay"
       @click.self="showDevicePopup = false"
       x-transition:enter="popup-enter"
       x-transition:enter-start="popup-enter-start"
       x-transition:enter-end="popup-enter-end"
       x-transition:leave="popup-leave"
       x-transition:leave-start="popup-leave-start"
       x-transition:leave-end="popup-leave-end">

    <div class="login-popup-card">

      {{-- Icon --}}
      <div class="login-popup-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
          <line x1="8" y1="21" x2="16" y2="21"/>
          <line x1="12" y1="17" x2="12" y2="21"/>
          <path d="M7.5 10.5 L10 8 L7.5 5.5" stroke-width="2"/>
          <circle cx="16" cy="8" r="2.5" fill="#ef4444" stroke="#ef4444"/>
        </svg>
      </div>

      {{-- Title --}}
      <div class="login-popup-title">Akun Sedang Aktif</div>

      {{-- Message --}}
      <div class="login-popup-message">
        Akun ini sedang digunakan di perangkat lain. Silakan logout dari perangkat tersebut terlebih dahulu, atau tunggu hingga sesi berakhir otomatis.
      </div>

      {{-- Button --}}
      <button type="button" class="login-popup-btn" @click="showDevicePopup = false">
        Mengerti
      </button>
    </div>
  </div>

  {{-- ═══════════════════════════════
       POPUP: Error / Validasi Gagal
  ═══════════════════════════════ --}}
  <div x-show="showErrorPopup"
       x-cloak
       class="login-popup-overlay"
       @click.self="showErrorPopup = false"
       x-transition:enter="popup-enter"
       x-transition:enter-start="popup-enter-start"
       x-transition:enter-end="popup-enter-end"
       x-transition:leave="popup-leave"
       x-transition:leave-start="popup-leave-start"
       x-transition:leave-end="popup-leave-end">

    <div class="login-popup-card">

      {{-- Icon --}}
      <div class="login-popup-icon login-popup-icon--error">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="13"/>
          <circle cx="12" cy="16.5" r="0.5" fill="currentColor"/>
        </svg>
      </div>

      {{-- Title --}}
      <div class="login-popup-title">Login Gagal</div>

      {{-- Message --}}
      <div class="login-popup-message">{{ $errorPopupMessage }}</div>

      {{-- Button --}}
      <button type="button" class="login-popup-btn" @click="showErrorPopup = false">
        Mengerti
      </button>
    </div>
  </div>

  {{-- ═══════════════════════════════
       POPUP: Terlalu Banyak Percobaan
  ═══════════════════════════════ --}}
  <div x-show="showLockPopup"
       x-cloak
       class="login-popup-overlay"
       x-transition:enter="popup-enter"
       x-transition:enter-start="popup-enter-start"
       x-transition:enter-end="popup-enter-end"
       x-transition:leave="popup-leave"
       x-transition:leave-start="popup-leave-start"
       x-transition:leave-end="popup-leave-end">

    <div class="login-popup-card">

      {{-- Icon --}}
      <div class="login-popup-icon login-popup-icon--error">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
      </div>

      {{-- Title --}}
      <div class="login-popup-title">Terlalu Banyak Percobaan</div>

      {{-- Message --}}
      <div class="login-popup-message">
        Anda sudah salah memasukkan NISN atau password sebanyak 3 kali. Silakan coba lagi dalam <strong x-text="lockSeconds"></strong> detik.
      </div>

      {{-- Countdown --}}
      <div style="margin:16px auto 8px;width:64px;height:64px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
        <span style="font-size:24px;font-weight:700;color:#ef4444;" x-text="lockSeconds"></span>
      </div>
    </div>
  </div>

</div>{{-- /.login-page --}}
