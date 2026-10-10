/**
 * Step by Step - محرك شارات أيقونة التطبيق الذكي (App Badging & Dynamic Favicon Engine)
 * - تحديث شارة أيقونة التطبيق في شريط مهام الويندوز / شاشة الجوال (OS App Badging API)
 * - رسم شارة رقمية ديناميكية ملونة فوق أيقونة التبويب (Dynamic Favicon Badge)
 * - تحديث عنوان الصفحة وعداد جرس التنبيهات بصورة تفاعلية فورية ومستمرة
 */

(function () {
    'use strict';

    let originalFaviconUrl = null;
    let baseDocumentTitle = null;
    let lastKnownCount = -1;
    let isDrawingFavicon = false;

    // استخراج وحفظ الأيقونة الأصلية للموقع
    function initOriginalFavicon() {
        if (originalFaviconUrl) return;

        const link = document.getElementById('dynamicSiteFavicon') || 
                     document.querySelector("link[rel='icon']") || 
                     document.querySelector("link[rel*='icon']");
        
        if (link && link.href && !link.href.startsWith('data:')) {
            originalFaviconUrl = link.href;
        } else {
            originalFaviconUrl = '/favicon.png';
        }

        if (!baseDocumentTitle) {
            baseDocumentTitle = document.title.replace(/^\(\d+\+?\)\s*/, '');
        }
    }

    // 1. تحديث شارة أيقونة التطبيق في نظام التشغيل (W3C App Badging API)
    function updateNativeAppBadge(count) {
        try {
            if ('setAppBadge' in navigator) {
                if (count > 0) {
                    navigator.setAppBadge(count).catch(function () {});
                } else {
                    navigator.clearAppBadge().catch(function () {});
                }
            }
        } catch (e) {}

        // إشعار Service Worker أيضاً إذا كان التطبيق مثبتاً كـ PWA
        try {
            if (navigator.serviceWorker && navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage({
                    type: 'SET_BADGE',
                    count: count
                });
            }
        } catch (e) {}
    }

    // 2. رسم شارة الإشعارات الديناميكية فوق Favicon المتصفح
    function drawFaviconBadge(count) {
        initOriginalFavicon();

        const links = document.querySelectorAll("link[rel*='icon']");
        if (!links || links.length === 0) return;

        if (count <= 0) {
            links.forEach(function (link) {
                if (originalFaviconUrl) {
                    link.href = originalFaviconUrl;
                }
            });
            return;
        }

        if (isDrawingFavicon) return;
        isDrawingFavicon = true;

        const img = new Image();
        img.crossOrigin = 'anonymous';

        img.onload = function () {
            try {
                const canvas = document.createElement('canvas');
                canvas.width = 64;
                canvas.height = 64;
                const ctx = canvas.getContext('2d');

                // رسم الأيقونة الأصلية أولاً
                ctx.drawImage(img, 0, 0, 64, 64);

                // إعدادات الشارة الرقمية
                const badgeText = count > 9 ? '9+' : String(count);
                const centerX = 46;
                const centerY = 18;
                const radius = 16;

                // 1. إطار خارجي أبيض للشارة لضمان وضوحها على أي خلفية
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius + 3, 0, 2 * Math.PI);
                ctx.fillStyle = '#ffffff';
                ctx.fill();

                // 2. دائرة حمراء فاقعة جذابة
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
                ctx.fillStyle = '#dc2626';
                ctx.fill();

                // 3. كتابة رقم الإشعارات باللون الأبيض وبخط عريض
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 20px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(badgeText, centerX, centerY + 1);

                const newFaviconData = canvas.toDataURL('image/png');
                links.forEach(function (link) {
                    link.href = newFaviconData;
                });
            } catch (err) {
                console.warn('App Badging: Favicon canvas render skipped:', err);
            } finally {
                isDrawingFavicon = false;
            }
        };

        img.onerror = function () {
            isDrawingFavicon = false;
        };

        img.src = originalFaviconUrl;
    }

    // 3. تحديث عنوان التبويب ليظهر الرقم بجانبه: (3) عنوان الصفحة
    function updateDocumentTitle(count) {
        if (!baseDocumentTitle) {
            baseDocumentTitle = document.title.replace(/^\(\d+\+?\)\s*/, '');
        }

        if (count > 0) {
            const badgeStr = count > 99 ? '99+' : count;
            document.title = '(' + badgeStr + ') ' + baseDocumentTitle;
        } else {
            document.title = baseDocumentTitle;
        }
    }

    // 4. تحديث عدادات واجهة المستخدم (جرس الإشعارات في الشريط العلوي)
    function updateDomBadges(count) {
        const navBadge = document.getElementById('navUnreadBadge');
        if (navBadge) {
            if (count > 0) {
                navBadge.textContent = count > 99 ? '99+' : count;
                navBadge.style.display = 'inline-block';
            } else {
                navBadge.style.display = 'none';
            }
        }

        const mobileBadges = document.querySelectorAll('.app-unread-badge, .pwa-unread-badge');
        mobileBadges.forEach(function (b) {
            if (count > 0) {
                b.textContent = count > 99 ? '99+' : count;
                b.style.display = 'inline-flex';
            } else {
                b.style.display = 'none';
            }
        });
    }

    // الدالة الرئيسية العامة لتحديث الشارة بكافة مستوياتها
    window.updateAppNotificationBadge = function (rawCount) {
        const count = Math.max(0, parseInt(rawCount, 10) || 0);
        lastKnownCount = count;

        // 1. نظام التشغيل والتطبيق المثبت (OS / PWA App Icon Badge)
        updateNativeAppBadge(count);

        // 2. شارة أيقونة التبويب (Browser Tab Favicon Badge)
        drawFaviconBadge(count);

        // 3. عنوان التبويب (Tab Title)
        updateDocumentTitle(count);

        // 4. جرس الإشعارات بالواجهة
        updateDomBadges(count);
    };

    // مزامنة فورية خفيفة لعدد الإشعارات من الخادم
    function syncUnreadCount() {
        const unreadEndpoint = window.APP_UNREAD_COUNT_URL || '/notifications/unread-count';

        fetch(unreadEndpoint, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(function (res) {
            if (!res.ok) throw new Error('Network status: ' + res.status);
            return res.json();
        })
        .then(function (data) {
            if (data && typeof data.count !== 'undefined') {
                window.updateAppNotificationBadge(data.count);
            }
        })
        .catch(function () {});
    }

    // تهيئة وتشغيل المحرك عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function () {
        initOriginalFavicon();

        const initialCount = typeof window.INITIAL_UNREAD_COUNT !== 'undefined' 
            ? window.INITIAL_UNREAD_COUNT 
            : 0;

        window.updateAppNotificationBadge(initialCount);

        // مزامنة دورية كل 30 ثانية دون أي إثقال على الخادم
        setInterval(syncUnreadCount, 30000);

        // عند عودة المستخدم للتطبيق أو نافذة المتصفح، تحديث فوري للشارة
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') {
                syncUnreadCount();
            }
        });
    });

})();
