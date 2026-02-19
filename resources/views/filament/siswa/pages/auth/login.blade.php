<div class="login-page" x-data="{ showDevicePopup: $wire.entangle('showDevicePopup') }">

  {{-- ── Inject Login CSS ── --}}
  @once
    @push('styles')
      <link rel="stylesheet" href="{{ asset('themes/ramadhan/css/login.css') }}" />
      <style>
        html, body { height: 100% !important; background: #f1f5f9 !important; padding: 0 !important; margin: 0 !important; overflow: hidden !important; }
        .fi-simple-layout { background: transparent !important; min-height: 100vh !important; overflow: hidden !important; }
        .fi-simple-main-ctn { max-width: none !important; padding: 0 !important; width: 100% !important; overflow: hidden !important; flex-grow: 0 !important; }
        .fi-simple-main { max-width: none !important; width: 100% !important; background: transparent !important; box-shadow: none !important; ring: none !important; padding: 0 !important; --tw-ring-shadow: none !important; --tw-shadow: none !important; margin: 0 !important; }
        .login-form-area .fi-ac-btn-action { width: 100% !important; }
        .login-form-area .fi-form-actions { margin-top: 6px !important; }
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
      <div class="login-form-subtitle">Masuk ke akun Buku Ramadan Anda</div>

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
            <span wire:loading.remove>Masuk</span>
            <span wire:loading style="display:none;">Memproses…</span>
          </x-filament::button>
        </form>
      </div>

      {{-- Footer --}}
      <div class="login-form-footer">
        <div class="login-form-footer-copy">
          &copy; {{ date('Y') }} SMKN 1 Ciamis. Semua hak cipta dilindungi.
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
        <div class="illust-masjid-icon">
          {{-- Simple masjid + crescent icon --}}
          <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
            {{-- Crescent moon --}}
            <circle cx="85" cy="22" r="12" fill="white"/>
            <circle cx="90" cy="18" r="10" fill="#2563eb"/>
            {{-- Main dome --}}
            <ellipse cx="60" cy="60" rx="32" ry="28"/>
            {{-- Dome finial --}}
            <line x1="60" y1="32" x2="60" y2="25" stroke="white" stroke-width="3" stroke-linecap="round"/>
            <circle cx="60" cy="23" r="3"/>
            {{-- Body --}}
            <rect x="28" y="60" width="64" height="40" rx="2"/>
            {{-- Door arch --}}
            <path d="M50 100 L50 78 Q60 65 70 78 L70 100 Z" fill="#2563eb"/>
            {{-- Left minaret --}}
            <rect x="10" y="48" width="10" height="52" rx="1"/>
            <polygon points="10,48 15,36 20,48"/>
            <circle cx="15" cy="34" r="3"/>
            {{-- Right minaret --}}
            <rect x="100" y="48" width="10" height="52" rx="1"/>
            <polygon points="100,48 105,36 110,48"/>
            <circle cx="105" cy="34" r="3"/>
            {{-- Windows --}}
            <circle cx="42" cy="74" r="4" fill="#2563eb"/>
            <circle cx="78" cy="74" r="4" fill="#2563eb"/>
          </svg>
        </div>

        <div class="illust-title">Buku Ramadan</div>
        <div class="illust-subtitle">Catatan ibadah digital siswa</div>
      </div>

      {{-- Bottom masjid silhouette --}}
      <div class="illust-masjid-bottom">
        <svg viewBox="0 0 400 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
          <rect x="0" y="70" width="400" height="10"/>
          <rect x="0" y="40" width="60" height="40"/>
          <polygon points="60,40 80,20 100,40"/>
          <rect x="60" y="40" width="40" height="40"/>
          <rect x="110" y="30" width="8" height="50"/>
          <polygon points="110,30 114,18 118,30"/>
          <rect x="130" y="35" width="80" height="45"/>
          <ellipse cx="170" cy="35" rx="40" ry="25"/>
          <rect x="166" y="8" width="8" height="27"/>
          <circle cx="170" cy="8" r="4"/>
          <rect x="220" y="30" width="8" height="50"/>
          <polygon points="220,30 224,18 228,30"/>
          <rect x="240" y="40" width="50" height="40"/>
          <ellipse cx="265" cy="40" rx="25" ry="18"/>
          <rect x="300" y="30" width="8" height="50"/>
          <polygon points="300,30 304,18 308,30"/>
          <rect x="320" y="45" width="80" height="35"/>
          <polygon points="340,45 355,28 370,45"/>
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

</div>{{-- /.login-page --}}
