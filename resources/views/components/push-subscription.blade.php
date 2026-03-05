{{-- Push Notification Subscription Handler --}}
{{-- Included in all panels via BODY_END render hook --}}
<script>
(function() {
    'use strict';

    // Skip jika di Android app atau tidak support
    if (navigator.userAgent.includes('Calakan-Android')) return;
    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) return;

    const VAPID_PUBLIC_KEY = @json(env('VAPID_PUBLIC_KEY'));
    const SUBSCRIBE_URL = '/api/push/subscribe';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

    // Jangan minta izin jika sudah denied
    if (Notification.permission === 'denied') return;

    // Tunggu sampai service worker ready
    navigator.serviceWorker.ready.then(async (registration) => {
        try {
            // Cek apakah sudah subscribe
            const existingSub = await registration.pushManager.getSubscription();
            if (existingSub) {
                // Sudah subscribe — perbarui data di server
                await sendSubscriptionToServer(existingSub);
                return;
            }

            // Jika belum pernah minta izin, tampilkan prompt setelah 3 detik
            if (Notification.permission === 'default') {
                await new Promise(r => setTimeout(r, 3000));
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') return;
            }

            // Subscribe ke push manager
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
            });

            await sendSubscriptionToServer(subscription);
        } catch (err) {
            console.warn('[Push] Subscription failed:', err);
        }
    });

    async function sendSubscriptionToServer(subscription) {
        const subJSON = subscription.toJSON();
        try {
            await fetch(SUBSCRIBE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    endpoint: subJSON.endpoint,
                    keys: {
                        p256dh: subJSON.keys.p256dh,
                        auth: subJSON.keys.auth,
                    },
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
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
})();
</script>
