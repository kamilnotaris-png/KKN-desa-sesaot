const CACHE_NAME = 'wisata-sesaot-pages-v1';
const RUNTIME_CACHEABLE_HOSTS = [self.location.host, 'tile.openstreetmap.org'];

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)))
        )
    );
    self.clients.claim();
});

function isCacheableRequest(request) {
    if (request.method !== 'GET') return false;
    const url = new URL(request.url);
    return RUNTIME_CACHEABLE_HOSTS.some((host) => url.host === host || url.host.endsWith('.' + host));
}

async function networkFirst(request) {
    const cache = await caches.open(CACHE_NAME);
    try {
        const response = await fetch(request);
        if (response.ok) cache.put(request, response.clone());
        return response;
    } catch (err) {
        const cached = await cache.match(request);
        if (cached) return cached;
        throw err;
    }
}

async function cacheFirst(request) {
    const cache = await caches.open(CACHE_NAME);
    const cached = await cache.match(request);
    if (cached) return cached;

    const response = await fetch(request);
    if (response.ok) cache.put(request, response.clone());
    return response;
}

self.addEventListener('fetch', (event) => {
    const { request } = event;
    if (!isCacheableRequest(request)) return;

    const url = new URL(request.url);
    // data.json (POI) network-first supaya update Pokdarwis cepat kelihatan;
    // sisanya (shell, tile) cache-first karena jarang berubah.
    const isFreshData = request.mode === 'navigate' || url.pathname.endsWith('data.json');

    event.respondWith(isFreshData ? networkFirst(request) : cacheFirst(request));
});
