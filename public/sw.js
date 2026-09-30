/**
 * Step by Step Progressive Web App Service Worker (v5)
 * - تشغيل التطبيق بشكل كامل وطبيعي بدون إنترنت مع التحديث التلقائي الفوري فور الاتصال
 * - Stale-While-Revalidate للواجهات والصفحات المخزنة
 * - استثناء طلبات بث الفيديو المباشرة ومسارات الـ API لتتولاها IndexedDB
 */

const CACHE_NAME = 'step-by-step-pwa-v5';

// الأصول الأساسية التي يتم تخزينها فور تثبيت التطبيق
const PRECACHE_ASSETS = [
  '/',
  '/manifest.json',
  '/offline.html',
  '/images/logo.png',
  '/icons/icon.svg',
  '/icons/icon-192.jpg',
  '/icons/icon-512.jpg',
  '/apple-touch-icon.png',
  '/js/stepvoro-offline-videos.js',
  '/tawjihi-calculator',
  '/tawjihi-formulas',
  '/public-flashcards',
  '/catalog',
  'https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&display=swap',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css'
];

// حدث التثبيت: حفظ الموارد الرئيسية مسبقاً وتخطي الانتظار
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PRECACHE_ASSETS).catch((err) => {
        console.warn('Step by Step ServiceWorker: Precache asset notice:', err);
      });
    })
  );
  self.skipWaiting();
});

// حدث التنشيط: حذف النسخ القديمة للكاش تلقائياً لضمان ظهور هوية Step by Step الجديدة
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            console.log('Step by Step: Purging outdated cache:', key);
            return caches.delete(key);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// الاستماع لرسائل التحديث الفوري
self.addEventListener('message', (event) => {
  if (event.data && event.data.action === 'skipWaiting') {
    self.skipWaiting();
  }
});

// حدث الجلب (Fetch): فتح الصفحات فوراً من الكاش مع تحديثها في الخلفية عند توفر الإنترنت
self.addEventListener('fetch', (event) => {
  const request = event.request;

  // لا نتدخل في طلبات البوست أو بث الفيديو الجزئي (Video Range Requests) أو الـ API الحية
  if (
    request.method !== 'GET' ||
    request.url.includes('/video-stream/') ||
    request.url.includes('/logout') ||
    request.headers.get('range')
  ) {
    return;
  }

  // 1. للتعامل مع صفحات التنقل (HTML Navigation Pages):
  // المحاولة من الشبكة أولاً لجلب أحدث إصدار، وعند انقطاع النت يتم استخدام الكاش
  if (request.mode === 'navigate' || (request.headers.get('accept') && request.headers.get('accept').includes('text/html'))) {
    event.respondWith(
      fetch(request)
        .then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(request, responseClone);
            });
          }
          return networkResponse;
        })
        .catch(() => {
          return caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
              return cachedResponse;
            }
            return caches.match('/offline.html');
          });
        })
    );
    return;
  }

  // 2. للملفات الثابتة (CSS, JS, Fonts, Images): Cache-First مع التحديث في الخلفية
  event.respondWith(
    caches.match(request).then((cachedResponse) => {
      const fetchPromise = fetch(request)
        .then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(request, responseClone);
            });
          }
          return networkResponse;
        })
        .catch(() => {
          return cachedResponse;
        });

      return cachedResponse || fetchPromise;
    })
  );
});
