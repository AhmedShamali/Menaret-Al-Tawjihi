const CACHE_NAME = 'tawjihi-platform-cache-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/manifest.json',
  '/js/offline-video-manager.js',
  'https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800&display=swap',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
  'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js',
  'https://cdn.jsdelivr.net/npm/sweetalert2@11'
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE).catch((err) => {
        console.warn('بعض الموارد لم يتم تخزينها مؤقتاً في sw:', err);
      });
    })
  );
  self.skipWaiting();
});

// Activate Event
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

// Fetch Event: Network first with Cache fallback for pages and offline support
self.addEventListener('fetch', (event) => {
  // Do not intercept non-GET requests or video streaming range requests
  if (event.request.method !== 'GET' || event.request.url.includes('/video-stream/')) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        if (response && response.status === 200) {
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseClone);
          });
        }
        return response;
      })
      .catch(() => {
        return caches.match(event.request).then((cachedResponse) => {
          if (cachedResponse) {
            return cachedResponse;
          }
          // If HTML page and completely offline, return cached dashboard or root
          if (event.request.headers.get('accept')?.includes('text/html')) {
            return caches.match('/');
          }
        });
      })
  );
});
