const CACHE_NAME = 'ibapos-static-v1';
const ASSETS = [
  '/',
  '/manifest.json',
  '/icon-192.png',
  '/icon-512.png',
  '/favicon.ico'
  // If you use Vite-built assets, consider adding them here or using runtime caching.
];

// Include offline fallback in the precache
if (!ASSETS.includes('/offline.html')) ASSETS.push('/offline.html');

self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS))
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(
      keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
    ))
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  event.respondWith(
    caches.match(event.request).then((cached) => {
      if (cached) return cached;
      return fetch(event.request).then((resp) => {
        return resp;
      }).catch(() => {
        if (event.request.mode === 'navigate') {
          // Return offline fallback page for navigations when network fails
          return caches.match('/offline.html');
        }
      });
    })
  );
});
