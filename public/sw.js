// Calakan - Service Worker
// Catatan Amaliyah Kegiatan Ramadan SMKN 1 Ciamis

const CACHE_NAME = "calakan-cache-v1";
const OFFLINE_URL = "/offline.html";

// Assets yang selalu di-cache saat install
const PRECACHE_ASSETS = [OFFLINE_URL, "/img/logo_smk.png", "/manifest.json"];

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
