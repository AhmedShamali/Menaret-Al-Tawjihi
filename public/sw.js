/**
 * Step by Step Progressive Web App Engine (v15 - 2026-10-02)
 * - تحديث تلقائي فوري للخادم والتطبيق مع تفريغ الكاش القديم
 * - دعم كامل للتشغيل بدون إنترنت (100% Offline App Shell + Offline Videos)
 * - تنقل سلس بدون شبكة مع استرجاع واجهة الفيديوهات المحملة أوفلاين
 */

const CACHE_NAME = 'step-by-step-v20261002-v35';

// الأصول الأساسية التي يتم تخزينها مسبقاً للعمل بدون إنترنت
const PRECACHE_ASSETS = [
  '/',
  '/?source=pwa',
  '/manifest.json',
  '/manifest.json?v=20261002-v35',
  '/offline.html',
  '/offline-videos',
  '/icons/step-by-step-icon-512.png',
  '/icons/step-by-step-icon-192.png',
  '/icons/icon-512.png',
  '/icons/icon-192.png',
  '/icons/icon-maskable-512.png',
  '/icons/icon-maskable-192.png',
  '/icons/step-by-step-icon-512.png?v=20261002-v33',
  '/icons/step-by-step-icon-192.png?v=20261002-v33',
  '/icons/icon-maskable-512.png?v=20261002-v33',
  '/icons/icon-maskable-192.png?v=20261002-v33',
  '/logo.png',
  '/images/logo.png',
  '/images/app-icon.jpg',
  '/apple-touch-icon.png',
  '/apple-touch-icon.png?v=20261002-v33',
  '/favicon.ico',
  '/favicon.png',
  '/favicon.png?v=20261002-v33',
  '/js/stepvoro-offline-videos.js',
  '/js/stepvoro-offline-videos.js?v=20261002-v33',
  '/js/stepvoro-offline-videos.js?v=20261002-v35',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-solid-900.woff2',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-brands-400.woff2',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-regular-400.woff2',
  '/catalog',
  '/tawjihi-calculator'
];

// حدث التثبيت: حفظ الأصول الأساسية بمرونة عالية (Promise.allSettled) لضمان عدم فشل التثبيت نهائياً
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(async (cache) => {
      await Promise.allSettled(
        PRECACHE_ASSETS.map(async (assetUrl) => {
          try {
            const resp = await fetch(assetUrl, { cache: 'reload' });
            if (resp && (resp.status === 200 || resp.type === 'opaque')) {
              await cache.put(assetUrl, resp);
            }
          } catch (err) {
            console.warn('Step by Step: Skipped asset during precache:', assetUrl);
          }
        })
      );
    })
  );
  self.skipWaiting();
});

// حدث التنشيط: مسح أي نسخ كاش قديمة تلقائياً وتفعيل الكاش الجديد لجميع الصفحات المفتوحة
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            console.log('Step by Step: Deleting stale cache:', key);
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
  if (event.data) {
    if (event.data.action === 'skipWaiting' || event.data.type === 'SKIP_WAITING') {
      self.skipWaiting();
    }
  }
});

// حدث الجلب (Fetch): Network-First ذكي للأصول والصفحات مع استجابة فورية من الكاش عند انقطاع النت
self.addEventListener('fetch', (event) => {
  const request = event.request;
  const urlLower = request.url.toLowerCase();

  // 1. لا نتدخل في طلبات البوست، بث الفيديو، ملفات الوسائط الكبيرة، أو مسارات تسجيل الخروج
  if (
    request.method !== 'GET' ||
    urlLower.includes('/video-stream/') ||
    (urlLower.includes('/educational-contents/') && urlLower.includes('/download-video')) ||
    urlLower.includes('/logout') ||
    urlLower.includes('educational/videos') ||
    /\.(mp4|webm|ogg|mov|mkv|m4v|avi)(\?|$)/i.test(urlLower) ||
    request.headers.get('range')
  ) {
    return;
  }

  // 2. لصفحات التنقل (HTML Navigation Pages):
  // محاولة الشبكة أولاً لجلب أحدث التعديلات، وعند انقطاع النت يتم استرجاع واجهة الأوفلاين فوراً
  if (request.mode === 'navigate' || (request.headers.get('accept') && request.headers.get('accept').includes('text/html'))) {
    event.respondWith(
      fetch(request)
        .then((networkResponse) => {
          if (networkResponse && (networkResponse.status === 200 || networkResponse.type === 'opaque')) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(request, responseClone);
            });
          }
          return networkResponse;
        })
        .catch(async () => {
          // 1. فحص الكاش لنفس الرابط المطلوب
          const directMatch = await caches.match(request, { ignoreSearch: true });
          if (directMatch) return directMatch;

          // 2. فحص صفحة الأوفلاين المعتمدة بكل الصيغ الممكنة
          const offlineHtmlMatch = (await caches.match('/offline.html', { ignoreSearch: true }))
            || (await caches.match(new URL('/offline.html', self.location.origin).href, { ignoreSearch: true }))
            || (await caches.match('/offline-videos', { ignoreSearch: true }))
            || (await caches.match('/', { ignoreSearch: true }));

          if (offlineHtmlMatch) return offlineHtmlMatch;

          return new Response('وضع عدم الاتصال: يرجى التحقق من اتصالك بالإنترنت.', {
            headers: { 'Content-Type': 'text/html; charset=utf-8' }
          });
        })
    );
    return;
  }

  // 3. لملفات المنصة الداخلية (CSS, JS, Icons, Images, Logo):
  // Network-First مع مطابقة فورية من الكاش بدون حساسية لمعلمات البحث
  const isInternalAsset = request.url.startsWith(self.location.origin) && (
    urlLower.includes('/images/') ||
    urlLower.includes('/icons/') ||
    urlLower.includes('/js/') ||
    urlLower.includes('/css/') ||
    urlLower.includes('/build/') ||
    urlLower.includes('manifest.json') ||
    urlLower.includes('favicon')
  );

  if (isInternalAsset) {
    event.respondWith(
      fetch(request)
        .then((networkResponse) => {
          if (networkResponse && (networkResponse.status === 200 || networkResponse.type === 'opaque')) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(request, responseClone);
            });
          }
          return networkResponse;
        })
        .catch(() => {
          return caches.match(request, { ignoreSearch: true });
        })
    );
    return;
  }

  // 4. للموارد الخارجية الأخرى (CDNs, Google Fonts): Stale-While-Revalidate
  event.respondWith(
    caches.match(request, { ignoreSearch: true }).then((cachedResponse) => {
      const fetchPromise = fetch(request)
        .then((networkResponse) => {
          if (networkResponse && (networkResponse.status === 200 || networkResponse.type === 'opaque')) {
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
