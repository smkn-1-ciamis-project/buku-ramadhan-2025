{{-- PWA Install Banner — shown on login pages when app is not installed --}}
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
    </div>

    <div class="pwa-install-actions">
      <button @click="installApp()" class="pwa-install-btn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/>
          <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Install
      </button>
      <button @click="dismissBanner()" class="pwa-install-dismiss">Nanti saja</button>
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

  .pwa-install-dismiss {
    background: none;
    border: none;
    color: #94a3b8;
    font-size: 13px;
    cursor: pointer;
    padding: 10px 12px;
    white-space: nowrap;
    font-family: inherit;
    transition: color 0.2s;
  }

  .pwa-install-dismiss:hover {
    color: #64748b;
  }

  @media (prefers-color-scheme: dark) {
    .pwa-install-banner {
      background: #1e293b;
      border-color: rgba(255,255,255,0.1);
    }
    .pwa-install-title { color: #f1f5f9; }
    .pwa-install-desc { color: #94a3b8; }
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

        // Don't show if user dismissed recently (24 hours)
        const dismissed = localStorage.getItem('pwa-install-dismissed');
        if (dismissed && Date.now() - parseInt(dismissed) < 24 * 60 * 60 * 1000) {
          return;
        }

        window.addEventListener('beforeinstallprompt', (e) => {
          e.preventDefault();
          this.deferredPrompt = e;
          // Small delay so the login page loads first
          setTimeout(() => { this.showBanner = true; }, 1500);
        });

        // Hide banner if app gets installed
        window.addEventListener('appinstalled', () => {
          this.showBanner = false;
          this.deferredPrompt = null;
        });
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
        localStorage.setItem('pwa-install-dismissed', Date.now().toString());
      }
    };
  }
</script>
