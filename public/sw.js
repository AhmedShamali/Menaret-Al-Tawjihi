const CACHE_NAME = 'stepvoro-app-v2';
const ASSETS_TO_CACHE = [
  '/',
  '/manifest.json',
  '/offline.html',
  '/icons/icon.svg',
  '/icons/icon-192.jpg',
  '/icons/icon-512.jpg',
  '/tawjihi-calculator',
  '/tawjihi-formulas',
  '/public-flashcards',
  'https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&display=swap',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
  'https://cdn.jsdelivr.net/npm/sweetalert2@11'
];

// Install Event: Pre-cache essential offline assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE).catch((err) => {
        console.warn('Stepvoro ServiceWorker: Some assets failed pre-caching:', err);
      });
    })
  );
  self.skipWaiting();
});

// Activate Event: Clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch Event: Network-first for fresh dynamic content, fallback to Cache and Offline Page
self.addEventListener('fetch', (event) => {
  // Do not intercept non-GET requests or video streaming range requests or admin purge/post actions
  if (event.request.method !== 'GET' || event.request.url.includes('/video-stream/') || event.request.url.includes('/api/')) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        // Cache successful GET responses for pages, images, styles
        if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
          const responseClone = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseClone);
          });
        }
        return networkResponse;
      })
      .catch(() => {
        // Network failed (device is offline) -> check cache
        return caches.match(event.request).then((cachedResponse) => {
          if (cachedResponse) {
            return cachedResponse;
          }
          // If requesting an HTML navigation page while offline, return the branded offline page
          if (event.request.mode === 'navigate' || event.request.headers.get('accept')?.includes('text/html')) {
            return caches.match('/offline.html');
          }
        });
      })
  );
});
