{{-- PWA Install Banner — shown on all pages when app is not installed --}}
<div x-data="pwaInstall()" x-cloak>
  {{-- Install Banner --}}
  <div x-show="showBanner"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 translate-y-4"
       x-transition:enter-end="opacity-100 translate-y-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100 translate-y-0"
       x-transition:leave-end="opacity-0 translate-y-4"
       class="pwa-install-banner">

    <div class="pwa-install-content">
      <div class="pwa-install-icon">
        <img src="/img/icons/icon-192x192.png" alt="Calakan" width="48" height="48" style="border-radius:12px;">
      </div>
      <div class="pwa-install-text">
        <div class="pwa-install-title">Install Calakan</div>
        <div class="pwa-install-desc">Akses lebih cepat langsung dari layar utama HP kamu</div>
      </div>
      <button @click="dismissBanner()" class="pwa-install-close" aria-label="Tutup">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="pwa-install-actions">
      <button @click="installApp()" class="pwa-install-btn" x-show="deferredPrompt">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/>
          <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Install
      </button>
      <a href="/calakan.apk" class="pwa-install-btn" x-show="!deferredPrompt" style="text-decoration:none;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/>
          <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Download APK
      </a>
    </div>
  </div>
</div>

<style>
  .pwa-install-banner {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 99999;
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15), 0 2px 8px rgba(0,0,0,0.08);
    padding: 16px 20px;
    width: calc(100% - 32px);
    max-width: 420px;
    border: 1px solid rgba(0,0,0,0.06);
  }

  .pwa-install-content {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 14px;
  }

  .pwa-install-icon {
    flex-shrink: 0;
  }

  .pwa-install-icon img {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  .pwa-install-text {
    flex: 1;
    min-width: 0;
  }

  .pwa-install-title {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 2px;
  }

  .pwa-install-desc {
    font-size: 13px;
    color: #64748b;
    line-height: 1.4;
  }

  .pwa-install-close {
    flex-shrink: 0;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    border-radius: 6px;
    transition: color 0.2s, background 0.2s;
    align-self: flex-start;
  }

  .pwa-install-close:hover {
    color: #64748b;
    background: rgba(0,0,0,0.05);
  }

  .pwa-install-actions {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .pwa-install-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
    font-family: inherit;
  }

  .pwa-install-btn:hover {
    opacity: 0.9;
  }

  @media (prefers-color-scheme: dark) {
    .pwa-install-banner {
      background: #1e293b;
      border-color: rgba(255,255,255,0.1);
    }
    .pwa-install-title { color: #f1f5f9; }
    .pwa-install-desc { color: #94a3b8; }
    .pwa-install-close { color: #64748b; }
    .pwa-install-close:hover { color: #94a3b8; background: rgba(255,255,255,0.08); }
  }
</style>

<script>
  function pwaInstall() {
    return {
      showBanner: false,
      deferredPrompt: null,

      init() {
        // Don't show if already installed as PWA or running in Android app
        if (window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true ||
            navigator.userAgent.includes('Calakan-Android')) {
          return;
        }

        // Don't show if user dismissed in this session
        if (sessionStorage.getItem('pwa-install-dismissed')) {
          return;
        }

        // Capture install prompt if available
        window.addEventListener('beforeinstallprompt', (e) => {
          e.preventDefault();
          this.deferredPrompt = e;
        });

        // Hide banner if app gets installed
        window.addEventListener('appinstalled', () => {
          this.showBanner = false;
          this.deferredPrompt = null;
        });

        // Show banner after short delay
        setTimeout(() => { this.showBanner = true; }, 1500);
      },

      async installApp() {
        if (!this.deferredPrompt) return;
        this.deferredPrompt.prompt();
        const { outcome } = await this.deferredPrompt.userChoice;
        if (outcome === 'accepted') {
          this.showBanner = false;
        }
        this.deferredPrompt = null;
      },

      dismissBanner() {
        this.showBanner = false;
        sessionStorage.setItem('pwa-install-dismissed', '1');
      }
    };
  }
</script>
