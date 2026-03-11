{{-- Push Notification Permission Popup + Subscription Handler --}}
{{-- Included in all panels via BODY_END render hook — only shown when logged in --}}
@guest
{{-- Tidak tampilkan popup notifikasi di halaman login --}}
@endguest
@auth

{{-- Permission Popup --}}
<div id="push-notif-popup" style="display:none;position:fixed;inset:0;z-index:999999;align-items:flex-end;justify-content:center;padding:0 0 24px 0;background:rgba(0,0,0,0.45);backdrop-filter:blur(2px);">
  <div id="push-notif-card" style="background:#fff;border-radius:20px 20px 16px 16px;width:calc(100% - 32px);max-width:420px;padding:24px 22px 20px;box-shadow:0 -4px 40px rgba(0,0,0,0.18),0 8px 32px rgba(0,0,0,0.12);transform:translateY(40px);opacity:0;transition:transform 0.35s cubic-bezier(.22,1,.36,1),opacity 0.35s ease;">
    <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:16px;">
      <div style="flex-shrink:0;width:48px;height:48px;background:linear-gradient(135deg,#1e3a8a,#2563eb);border-radius:14px;display:flex;align-items:center;justify-content:center;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      </div>
      <div style="flex:1;min-width:0;">
        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:4px;">Aktifkan Notifikasi</div>
        <div style="font-size:13px;color:#64748b;line-height:1.5;">Dapatkan pengumuman penting dari sekolah dan pengingat kegiatan Ramadhan langsung di HP kamu.</div>
      </div>
    </div>
    <div style="display:flex;gap:10px;">
      <button id="push-notif-allow" style="flex:1;background:linear-gradient(135deg,#1e3a8a,#2563eb);color:#fff;border:none;border-radius:10px;padding:11px 0;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:opacity 0.2s;">
        Izinkan Notifikasi
      </button>
    </div>
  </div>
</div>

<style>
  @media (prefers-color-scheme: dark) {
    #push-notif-card {
      background: #1e293b !important;
    }
    #push-notif-card > div > div > div[style*="color:#1e293b"] {
      color: #f1f5f9 !important;
    }
    #push-notif-dismiss {
      background: #334155 !important;
      color: #cbd5e1 !important;
    }
  }
</style>

<script>
(function() {
    'use strict';

    const VAPID_PUBLIC_KEY = @json(env('VAPID_PUBLIC_KEY'));
    const SUBSCRIBE_URL = '/api/push/subscribe';

    function getCsrf() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    }

    // Skip jika di Android app atau tidak support
    if (navigator.userAgent.includes('Calakan-Android')) return;
    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) return;

    // Jika sudah granted → langsung subscribe tanpa popup
    if (Notification.permission === 'granted') {
        navigator.serviceWorker.ready.then(registration => doSubscribe(registration));
        return;
    }

    // Jika sudah denied → jangan tampilkan apa-apa
    if (Notification.permission === 'denied') return;


    // Tampilkan popup setelah 2.5 detik
    setTimeout(showPopup, 2500);

    function showPopup() {
        const overlay = document.getElementById('push-notif-popup');
        const card = document.getElementById('push-notif-card');
        if (!overlay || !card) return;

        overlay.style.display = 'flex';
        // Trigger animation on next frame
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                card.style.transform = 'translateY(0)';
                card.style.opacity = '1';
            });
        });

        document.getElementById('push-notif-allow').addEventListener('click', async () => {
            hidePopup();
            try {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') return;
                const registration = await navigator.serviceWorker.ready;
                await doSubscribe(registration);
            } catch (err) {
                console.warn('[Push] Permission request failed:', err);
            }
        });
    }

    function hidePopup() {
        const overlay = document.getElementById('push-notif-popup');
        const card = document.getElementById('push-notif-card');
        if (!overlay || !card) return;
        card.style.transform = 'translateY(40px)';
        card.style.opacity = '0';
        setTimeout(() => { overlay.style.display = 'none'; }, 350);
    }

    async function doSubscribe(registration) {
        try {
            const existingSub = await registration.pushManager.getSubscription();
            if (existingSub) {
                await sendSubscriptionToServer(existingSub);
                return;
            }
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
            });
            await sendSubscriptionToServer(subscription);
        } catch (err) {
            console.warn('[Push] Subscription failed:', err);
        }
    }

    async function sendSubscriptionToServer(subscription) {
        const subJSON = subscription.toJSON();
        try {
            await fetch(SUBSCRIBE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    endpoint: subJSON.endpoint,
                    keys: { p256dh: subJSON.keys.p256dh, auth: subJSON.keys.auth },
                }),
            });
        } catch (err) {
            console.warn('[Push] Failed to save subscription:', err);
        }
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
        return outputArray;
    }
})();
</script>
@endauth
