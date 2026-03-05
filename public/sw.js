// Calakan - Service Worker
// Catatan Amaliyah Kegiatan Ramadan SMKN 1 Ciamis

const CACHE_NAME = "calakan-cache-v10";
const OFFLINE_URL = "/offline.html";

// Assets yang selalu di-cache saat install
// NOTE: In Android WebView (Calakan-Android), the SW is unregistered
// by the native app to prevent stale cache issues.
const PRECACHE_ASSETS = [
    OFFLINE_URL,
    "/img/logo_smk.png",
    "/manifest.json",
    "/themes/ramadhan/css/dashboard.css",
    "/themes/ramadhan/css/login.css",
    "/themes/ramadhan/css/formulir.css",
    "/themes/ramadhan/js/muslim/dashboard.js",
    "/themes/ramadhan/js/muslim/formulir.js",
    "/themes/ramadhan/js/nonmuslim/kristen/dashboard.js",
    "/themes/ramadhan/js/nonmuslim/kristen/formulir.js",
    "/themes/ramadhan/js/nonmuslim/hindu/dashboard.js",
    "/themes/ramadhan/js/nonmuslim/hindu/formulir.js",
    "/themes/ramadhan/js/nonmuslim/buddha/dashboard.js",
    "/themes/ramadhan/js/nonmuslim/buddha/formulir.js",
    "/themes/ramadhan/js/nonmuslim/konghucu/dashboard.js",
    "/themes/ramadhan/js/nonmuslim/konghucu/formulir.js",
];

// ── Install Event ──────────────────────────────────────────────────
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => cache.addAll(PRECACHE_ASSETS))
            .then(() => self.skipWaiting()),
    );
});

// ── Activate Event ─────────────────────────────────────────────────
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        // Hapus cache lama
                        if (cacheName !== CACHE_NAME) {
                            return caches.delete(cacheName);
                        }
                    }),
                );
            })
            .then(() => self.clients.claim()),
    );
});

// ── Fetch Event ────────────────────────────────────────────────────
self.addEventListener("fetch", (event) => {
    // Hanya handle GET requests
    if (event.request.method !== "GET") return;

    // Skip requests ke external domains
    const url = new URL(event.request.url);
    if (url.origin !== location.origin) return;

    // Skip API requests & Livewire requests
    if (
        url.pathname.startsWith("/api/") ||
        url.pathname.startsWith("/livewire/") ||
        url.pathname.includes("/filament/") ||
        event.request.headers.get("X-Livewire")
    ) {
        return;
    }

    event.respondWith(
        // Network-first strategy
        fetch(event.request)
            .then((response) => {
                // Cache successful responses untuk static assets
                if (response.ok && isStaticAsset(url.pathname)) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Coba ambil dari cache
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // Jika navigation request gagal, tampilkan offline page
                    if (event.request.mode === "navigate") {
                        return caches.match(OFFLINE_URL);
                    }
                    return new Response("", {
                        status: 503,
                        statusText: "Service Unavailable",
                    });
                });
            }),
    );
});

// Helper: tentukan apakah ini static asset yang perlu di-cache
function isStaticAsset(pathname) {
    const staticExtensions = [
        ".css",
        ".js",
        ".png",
        ".jpg",
        ".jpeg",
        ".gif",
        ".svg",
        ".ico",
        ".woff",
        ".woff2",
        ".ttf",
        ".eot",
    ];
    return staticExtensions.some((ext) => pathname.endsWith(ext));
}

// ── Push Event ─────────────────────────────────────────────────────
self.addEventListener("push", (event) => {
    let data = {
        title: "Calakan",
        body: "Ada pemberitahuan baru",
        icon: "/img/icons/icon-192x192.png",
        badge: "/img/icons/icon-72x72.png",
        url: "/",
        tag: "calakan-notification",
    };

    if (event.data) {
        try {
            const payload = event.data.json();
            data = { ...data, ...payload };
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon,
        badge: data.badge,
        tag: data.tag,
        renotify: true,
        requireInteraction: false,
        vibrate: [200, 100, 200],
        data: {
            url: data.url,
        },
        actions: [
            {
                action: "open",
                title: "Buka",
            },
            {
                action: "close",
                title: "Tutup",
            },
        ],
    };

    event.waitUntil(self.registration.showNotification(data.title, options));
});

// ── Notification Click Event ───────────────────────────────────────
self.addEventListener("notificationclick", (event) => {
    event.notification.close();

    if (event.action === "close") {
        return;
    }

    const urlToOpen = event.notification.data?.url || "/";

    event.waitUntil(
        clients
            .matchAll({ type: "window", includeUncontrolled: true })
            .then((clientList) => {
                // Cek apakah ada window yang sudah terbuka
                for (const client of clientList) {
                    if (
                        client.url.includes(self.location.origin) &&
                        "focus" in client
                    ) {
                        client.navigate(urlToOpen);
                        return client.focus();
                    }
                }
                // Jika tidak ada window terbuka, buka baru
                return clients.openWindow(urlToOpen);
            }),
    );
});
