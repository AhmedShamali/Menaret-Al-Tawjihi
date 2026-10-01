/**
 * Step by Step Progressive Web App Engine (v10)
 * - تحديث تلقائي فوري للخادم والتطبيق دون الحاجة لإعادة التثبيت
 * - Network-First ذكي للأصول والواجهات مع العمل أوفلاين 100% فور انقطاع الإنترنت
 * - استثناء طلبات بث الفيديو المباشرة لتتولاها ذاكرة الـ IndexedDB المعزولة
 */

const CACHE_NAME = 'step-by-step-pwa-v12';

// الأصول الأساسية التي يتم تخزينها مسبقاً للعمل بدون إنترنت
const PRECACHE_ASSETS = [
  '/',
  '/?source=pwa',
  '/manifest.json',
  '/offline.html',
  '/offline-videos',
  '/images/logo.png',
  '/images/app-icon.jpg',
  '/icons/icon.svg',
  '/icons/icon-192.jpg',
  '/icons/icon-512.jpg',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
  '/apple-touch-icon.png',
  '/favicon.ico',
  '/favicon.png',
  '/js/stepvoro-offline-videos.js',
  '/tawjihi-calculator',
  '/tawjihi-formulas',
  '/public-flashcards',
  '/catalog',
  'https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&display=swap',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css'
];

// حدث التثبيت: حفظ الأصول الأساسية وتجاوز الانتظار فوراً
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PRECACHE_ASSETS).catch((err) => {
        console.warn('Step by Step ServiceWorker Precache Notice:', err);
      });
    })
  );
  self.skipWaiting();
});

// حدث التنشيط: حذف النسخ القديمة للكاش تلقائياً والسيطرة الفورية على جميع النوافذ المفتوحة
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
    }).then(() => {
      return self.clients.claim();
    }).then(() => {
      // إشعار كافة النوافذ المفتوحة بأن النسخة الأحدث أصبحت نشطة
      return self.clients.matchAll({ type: 'window' }).then((clients) => {
        clients.forEach((client) => {
          client.postMessage({ type: 'PWA_UPDATED', version: CACHE_NAME });
        });
      });
    })
  );
});

// الاستماع لرسائل التحديث الفوري الصادرة من الصفحة
self.addEventListener('message', (event) => {
  if (event.data && event.data.action === 'skipWaiting') {
    self.skipWaiting();
  }
});

// حدث الجلب (Fetch): Network-First ذكي للأصول والصفحات مع استجابة فورية من الكاش عند انقطاع النت
self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);
  const urlLower = request.url.toLowerCase();

  // 1. لا نتدخل في طلبات البوست، بث الفيديو، ملفات الوسائط الكبيرة، أو مسارات تسجيل الخروج
  if (
    request.method !== 'GET' ||
    urlLower.includes('/video-stream/') ||
    urlLower.includes('/logout') ||
    urlLower.includes('educational/videos') ||
    /\.(mp4|webm|ogg|mov|mkv|m4v|avi)(\?|$)/i.test(urlLower) ||
    request.headers.get('range')
  ) {
    return;
  }

  // 2. لصفحات التنقل (HTML Navigation Pages):
  // المحاولة من الشبكة أولاً لجلب أحدث التعديلات، وعند انقطاع النت يتم جلب النسخة المخزنة
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
          return caches.match(request, { ignoreSearch: true }).then((cachedResponse) => {
            if (cachedResponse) {
              return cachedResponse;
            }
            return caches.match('/', { ignoreSearch: true }).then((homeResponse) => {
              if (homeResponse) {
                return homeResponse;
              }
              return caches.match('/offline.html');
            });
          });
        })
    );
    return;
  }

  // 3. لملفات المنصة الداخلية (CSS, JS, Icons, Images, Logo):
  // Network-First مع مهلة سريعة (Network-First with fallback to Cache)
  // لضمان انعكاس أي تعديل أو شعار جديد فوراً دون انتظار، مع العمل أوفلاين
  const isInternalAsset = url.origin === self.location.origin && (
    urlLower.includes('/images/') ||
    urlLower.includes('/icons/') ||
    urlLower.includes('/js/') ||
    urlLower.includes('/css/') ||
    urlLower.includes('manifest.json') ||
    urlLower.includes('favicon')
  );

  if (isInternalAsset) {
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
          return caches.match(request);
        })
    );
    return;
  }

  // 4. للموارد الخارجية الأخرى (CDNs, Google Fonts): Cache-First مع التحديث في الخلفية
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
        .catch(() => cachedResponse);

      return cachedResponse || fetchPromise;
    })
  );
});
