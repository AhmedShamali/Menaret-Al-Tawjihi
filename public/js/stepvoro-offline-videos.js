/**
 * ============================================================================
 * Stepvoro Offline In-App Video & Course Content Manager
 * نظام تحميل وتشغيل الفيديوهات داخل التطبيق بدون إنترنت (IndexedDB Storage)
 * ============================================================================
 * المميزات:
 * 1. حفظ ملفات الفيديو داخل الذاكرة المعزولة للتطبيق (IndexedDB Blob) دون تنزيلها
 *    في استوديو الهاتف لمنع نسخها أو مشاركتها.
 * 2. تشغيل تلقائي مباشر بدون إنترنت فور فتح الدرس عبر ObjectURL.
 * 3. تتبع نسبة التحميل مع شريط تقدم حي (Progress Bar).
 * 4. إدارة المساحة المستهلكة وإمكانية حذف أي درس لتحرير ذاكرة الجهاز.
 * 5. متوافق 100% مع أندرويد، آيفون، وحواسيب سطح المكتب (PWA).
 */

const StepvoroOfflineDB = (function () {
    const DB_NAME = 'StepvoroOfflineStore';
    const DB_VERSION = 1;
    const STORE_NAME = 'offline_videos';
    let dbInstance = null;

    // تهيئة قاعدة بيانات الذاكرة المحلية (IndexedDB)
    function openDB() {
        return new Promise((resolve, reject) => {
            if (dbInstance) {
                return resolve(dbInstance);
            }
            if (!('indexedDB' in window)) {
                return reject(new Error('IndexedDB غير مدعوم في هذا المتصفح.'));
            }

            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = function (e) {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(STORE_NAME)) {
                    const store = db.createObjectStore(STORE_NAME, { keyPath: 'id' });
                    store.createIndex('subject', 'subject', { unique: false });
                    store.createIndex('savedAt', 'savedAt', { unique: false });
                }
            };

            request.onsuccess = function (e) {
                dbInstance = e.target.result;
                resolve(dbInstance);
            };

            request.onerror = function (e) {
                reject(e.target.error);
            };
        });
    }

    // حفظ فيديو محمل كـ Blob داخل قاعدة البيانات
    function saveVideo(record) {
        return openDB().then((db) => {
            return new Promise((resolve, reject) => {
                const tx = db.transaction([STORE_NAME], 'readwrite');
                const store = tx.objectStore(STORE_NAME);
                const req = store.put(record);

                req.onsuccess = () => resolve(record);
                req.onerror = () => reject(req.error);
            });
        });
    }

    // جلب فيديو معين بالمعرف
    function getVideo(id) {
        return openDB().then((db) => {
            return new Promise((resolve, reject) => {
                const tx = db.transaction([STORE_NAME], 'readonly');
                const store = tx.objectStore(STORE_NAME);
                const req = store.get(String(id));

                req.onsuccess = () => resolve(req.result || null);
                req.onerror = () => reject(req.error);
            });
        });
    }

    // جلب قائمة كافة الفيديوهات المحفوظة أوفلاين
    function getAllVideos() {
        return openDB().then((db) => {
            return new Promise((resolve, reject) => {
                const tx = db.transaction([STORE_NAME], 'readonly');
                const store = tx.objectStore(STORE_NAME);
                const req = store.getAll();

                req.onsuccess = () => {
                    const list = (req.result || []).map((item) => ({
                        id: item.id,
                        title: item.title,
                        subject: item.subject,
                        sizeBytes: item.sizeBytes,
                        sizeFormatted: item.sizeFormatted,
                        savedAt: item.savedAt,
                        hasBlob: !!item.blob
                    }));
                    resolve(list);
                };
                req.onerror = () => reject(req.error);
            });
        });
    }

    // حذف فيديو معين لتحرير المساحة
    function deleteVideo(id) {
        return openDB().then((db) => {
            return new Promise((resolve, reject) => {
                const tx = db.transaction([STORE_NAME], 'readwrite');
                const store = tx.objectStore(STORE_NAME);
                const req = store.delete(String(id));

                req.onsuccess = () => resolve(true);
                req.onerror = () => reject(req.error);
            });
        });
    }

    // حساب إجمالي المساحة المستهلكة
    function calculateTotalSize() {
        return getAllVideos().then((videos) => {
            const totalBytes = videos.reduce((sum, v) => sum + (v.sizeBytes || 0), 0);
            return {
                bytes: totalBytes,
                mb: (totalBytes / (1024 * 1024)).toFixed(1),
                count: videos.length
            };
        });
    }

    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 MB';
        const mb = bytes / (1024 * 1024);
        if (mb >= 1) return mb.toFixed(1) + ' MB';
        const kb = bytes / 1024;
        return kb.toFixed(0) + ' KB';
    }

    return {
        openDB,
        saveVideo,
        getVideo,
        getAllVideos,
        deleteVideo,
        calculateTotalSize,
        formatBytes
    };
})();

// ============================================================================
// مشغل التنزيل الحي وتحديث واجهة المستخدم
// ============================================================================
const StepvoroVideoDownloader = {
    activeDownloads: {},

    // التحقق مما إذا كان الطالب داخل التطبيق المثبت (PWA/Standalone) أو المتصفح العادي (Web)
    isAppMode: function() {
        return window.matchMedia('(display-mode: standalone)').matches 
            || window.navigator.standalone === true 
            || document.body.classList.contains('in-standalone-app')
            || window.location.search.includes('mode=pwa');
    },

    // معالجة الضغط على زر التحميل مباشرة وحفظه داخل المنصة فقط
    handleAction: function(id, btnElement) {
        id = String(id);
        const btn = btnElement || document.getElementById('btn_offline_' + id);
        if (!btn) return;

        // في حال كان الفيديو محملاً ومحفوظاً بالفعل:
        if (btn.classList.contains('is-saved')) {
            StepvoroOfflineDB.getVideo(id).then((rec) => {
                if (rec && rec.blob) {
                    this.attachOfflineBlobToPlayer(id, rec.blob);
                    if (typeof window.showPwaToast === 'function') {
                        window.showPwaToast('يتم الآن تشغيل الدرس مباشرة من ذاكرة المنصة بدون إنترنت ⚡', 'success');
                    }
                    const player = document.getElementById('player_' + id);
                    if (player) {
                        player.play().catch(() => {});
                        player.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
            return;
        }

        const isDirect = btn.getAttribute('data-is-direct') === '1';
        const title = btn.getAttribute('data-video-title') || 'درس تعليمي';
        const subject = btn.getAttribute('data-subject-title') || 'المنهاج';
        const url = btn.getAttribute('data-video-url');

        // إذا كان الفيديو من يوتيوب ولا يحوي ملف فيديو مباشر
        if (!isDirect || !url) {
            if (typeof window.Swal !== 'undefined') {
                window.Swal.fire({
                    title: 'المشاهدة المباشرة للمنصة',
                    text: 'هذا الشرح المرئي مهيأ للمشاهدة المباشرة داخل المنصة بأعلى دقة وتوفير للبيانات.',
                    icon: 'info',
                    confirmButtonText: 'حسناً، فهمت',
                    confirmButtonColor: '#2563eb'
                });
            } else if (typeof window.showPwaToast === 'function') {
                window.showPwaToast('هذا الشرح المرئي متاح للمشاهدة المباشرة داخل المنصة.', 'info');
            }
            return;
        }

        this.startDownload(id, title, subject, url, btn);
    },

    // بدء تنزيل الفيديو وتخزينه حصرياً داخل الذاكرة المحلية للتطبيق (IndexedDB)
    startDownload: function (id, title, subject, videoUrl, btnElement) {
        id = String(id);
        if (this.activeDownloads[id]) {
            if (typeof window.showPwaToast === 'function') {
                window.showPwaToast('التحميل جاري بالفعل في الخلفية داخل المنصة...', 'info');
            }
            return;
        }

        const self = this;
        self.updateButtonUI(id, 'downloading', 0, btnElement);

        if (typeof window.showPwaToast === 'function') {
            window.showPwaToast('بدأ تحميل الدرس وحفظه في ذاكرة المنصة أوفلاين ⚡', 'info');
        }

        const xhr = new XMLHttpRequest();
        xhr.open('GET', videoUrl, true);
        xhr.responseType = 'blob';

        self.activeDownloads[id] = xhr;

        xhr.onprogress = function (e) {
            if (e.lengthComputable && e.total > 0) {
                const percent = Math.min(99, Math.round((e.loaded / e.total) * 100));
                self.updateButtonUI(id, 'downloading', percent, btnElement);
            }
        };

        xhr.onload = function () {
            delete self.activeDownloads[id];
            if (xhr.status === 200 || xhr.status === 206) {
                const blob = xhr.response;
                if (!blob || blob.size < 1000 || (blob.type && blob.type.includes('text/html'))) {
                    self.updateButtonUI(id, 'ready', 0, btnElement);
                    if (typeof window.showPwaToast === 'function') {
                        window.showPwaToast('تعذر استلام ملف الفيديو للتخزين الأوفلاين.', 'error');
                    }
                    return;
                }

                const sizeBytes = blob.size;
                const sizeFormatted = StepvoroOfflineDB.formatBytes(sizeBytes);

                const record = {
                    id: id,
                    title: title || 'درس تعليمي',
                    subject: subject || 'المنهاج الوزاري',
                    url: videoUrl,
                    blob: blob,
                    sizeBytes: sizeBytes,
                    sizeFormatted: sizeFormatted,
                    savedAt: new Date().toLocaleDateString('ar-EG', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                };

                // حفظ حصري داخل الذاكرة المحلية للتطبيق (IndexedDB)
                StepvoroOfflineDB.saveVideo(record)
                    .then(() => {
                        self.updateButtonUI(id, 'saved', 100, btnElement);
                        self.attachOfflineBlobToPlayer(id, blob);

                        if (typeof window.showPwaToast === 'function') {
                            window.showPwaToast('تم حفظ الدرس بنجاح داخل المنصة (' + sizeFormatted + ')! ومتاح الآن في واجهة الفيديوهات المحملة بدون نت.', 'success');
                        }
                    })
                    .catch((err) => {
                        console.error('Error saving video to DB:', err);
                        self.updateButtonUI(id, 'ready', 0, btnElement);
                        if (typeof window.showPwaToast === 'function') {
                            window.showPwaToast('حدث خطأ أثناء حفظ الفيديو في ذاكرة المنصة.', 'error');
                        }
                    });
            } else {
                self.updateButtonUI(id, 'ready', 0, btnElement);
                if (typeof window.showPwaToast === 'function') {
                    window.showPwaToast('فشل تحميل الفيديو. يرجى التحقق من اتصالك بالإنترنت.', 'error');
                }
            }
        };

        xhr.onerror = function () {
            delete self.activeDownloads[id];
            self.updateButtonUI(id, 'ready', 0, btnElement);
            if (typeof window.showPwaToast === 'function') {
                window.showPwaToast('تعذر إتمام التحميل، يرجى المحاولة لاحقاً.', 'error');
            }
        };

        xhr.ontimeout = function () {
            delete self.activeDownloads[id];
            self.updateButtonUI(id, 'ready', 0, btnElement);
            if (typeof window.showPwaToast === 'function') {
                window.showPwaToast('انتهت مهلة التحميل، يرجى المحاولة مجدداً.', 'warning');
            }
        };

        xhr.onabort = function () {
            delete self.activeDownloads[id];
            self.updateButtonUI(id, 'ready', 0, btnElement);
        };

        try {
            xhr.send();
        } catch (e) {
            delete self.activeDownloads[id];
            self.updateButtonUI(id, 'ready', 0, btnElement);
        }
    },

    // حذف فيديو محفوظ لتحرير المساحة
    removeOfflineVideo: function (id, btnElement) {
        id = String(id);
        const self = this;
        const doDelete = () => {
            StepvoroOfflineDB.deleteVideo(id).then(() => {
                self.updateButtonUI(id, 'ready', 0, btnElement);
                
                // إزالة الشارة فوق المشغل
                const badge = document.getElementById('offline_badge_' + id);
                if (badge) badge.remove();

                if (typeof window.showPwaToast === 'function') {
                    window.showPwaToast('تم حذف الدرس من الذاكرة المحلية وتحرير المساحة بنجاح.', 'success');
                } else if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحذف',
                        text: 'تم حذف الفيديو من الذاكرة المحلية وتحرير المساحة.',
                        confirmButtonColor: '#1d4ed8'
                    });
                }
                // إذا كنا في نافذة استعراض الفيديوهات المحفوظة
                if (typeof window.renderOfflineVideosList === 'function') {
                    window.renderOfflineVideosList();
                }
            });
        };

        if (window.Swal) {
            Swal.fire({
                title: 'هل تريد حذف هذا الدرس من ذاكرة الهاتف؟',
                text: 'سيتم تحرير المساحة، وستحتاج للاتصال بالإنترنت لإعادة تحميله لاحقاً.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((res) => {
                if (res.isConfirmed) doDelete();
            });
        } else {
            if (confirm('هل أنت متأكد من رغبتك في حذف الفيديو من الهاتف لتحرير المساحة؟')) {
                doDelete();
            }
        }
    },

    // تحديث شكل ومحتوى زر التنزيل بشكل راقٍ ومباشر
    updateButtonUI: function (id, state, percent, btnElement) {
        const btn = btnElement || document.getElementById('btn_offline_' + id);
        if (!btn) return;

        if (state === 'downloading') {
            btn.classList.add('is-downloading');
            btn.classList.remove('is-saved', 'is-web-mode');
            btn.disabled = true;
            btn.innerHTML = `
                <div class="ed-offline-btn-inner">
                    <span class="ed-offline-btn-icon"><i class="fa-solid fa-spinner fa-spin"></i></span>
                    <span class="offline-btn-label">جاري التحميل (${percent}%)</span>
                </div>
                <div class="ed-offline-progress-track">
                    <div class="ed-offline-progress-fill" style="width: ${percent}%;"></div>
                </div>
            `;
        } else if (state === 'saved') {
            btn.classList.remove('is-downloading', 'is-web-mode');
            btn.classList.add('is-saved');
            btn.disabled = false;
            btn.innerHTML = `
                <div class="ed-offline-btn-inner">
                    <span class="ed-offline-btn-icon"><i class="fa-solid fa-circle-check" style="color: #10b981;"></i></span>
                    <span class="offline-btn-label">محفوظ أوفلاين ✓</span>
                    <span class="btn-remove-offline" onclick="event.stopPropagation(); StepvoroVideoDownloader.removeOfflineVideo('${id}', this.closest('button'))" title="حذف من الذاكرة لتحرير المساحة">
                        <i class="fa-solid fa-trash-can"></i>
                    </span>
                </div>
            `;
        } else {
            // جاهز للتحميل
            btn.classList.remove('is-downloading', 'is-saved', 'is-web-mode');
            btn.disabled = false;
            btn.innerHTML = `
                <div class="ed-offline-btn-inner">
                    <span class="ed-offline-btn-icon"><i class="fa-solid fa-cloud-arrow-down"></i></span>
                    <span class="offline-btn-label">تحميل الدرس مباشرة</span>
                </div>
            `;
        }
    },

    // ربط الـ Blob بمشغل الفيديو حتى يشتغل أوفلاين 100%
    attachOfflineBlobToPlayer: function (id, blob) {
        const player = document.getElementById('player_' + id) || document.getElementById('pub_vid_' + id) || document.getElementById('vid_direct_' + id);
        if (!player) return;

        const blobUrl = URL.createObjectURL(blob);
        // إزالة أي وسوم source قديمة لمنع المتصفح من محاولة الاتصال بالإنترنت
        const oldSources = player.querySelectorAll('source');
        oldSources.forEach(function (s) { s.remove(); });
        player.src = blobUrl;
        try {
            player.load();
        } catch (e) {}

        // وضع إشعار فوق المشغل بأنه يعمل محلياً من الذاكرة
        let badge = document.getElementById('offline_badge_' + id);
        if (!badge) {
            badge = document.createElement('div');
            badge.id = 'offline_badge_' + id;
            badge.className = 'player-offline-badge';
            badge.innerHTML = `<i class="fa-solid fa-bolt"></i> يعمل من ذاكرة التطبيق (بدون إنترنت)`;
            if (player.parentElement) {
                player.parentElement.appendChild(badge);
            }
        }
    },

    // الفحص التلقائي عند فتح صفحة الدرس لمعرفة ما إذا كان الفيديو محملاً مسبقاً
    checkAndInitLessonPlayer: function (id) {
        id = String(id);
        const self = this;
        StepvoroOfflineDB.getVideo(id).then((record) => {
            if (record && record.blob) {
                self.updateButtonUI(id, 'saved', 100);
                self.attachOfflineBlobToPlayer(id, record.blob);
            } else {
                self.updateButtonUI(id, 'ready', 0);
            }
        }).catch((e) => {
            console.warn('Error checking offline video status:', e);
        });
    }
};

// تهيئة وفحص حالة التوصيل بالإنترنت وفحص الفيديوهات في الصفحة
window.addEventListener('DOMContentLoaded', function () {
    // 1. مراقبة حالة الاتصال بالإنترنت لعرض شريط التنبيه الذكي
    function updateConnectionStatus() {
        let banner = document.getElementById('stepvoroNetworkStatusPill');
        if (!navigator.onLine) {
            if (!banner) {
                banner = document.createElement('div');
                banner.id = 'stepvoroNetworkStatusPill';
                banner.className = 'network-status-pill offline';
                banner.innerHTML = `
                    <i class="fa-solid fa-wifi-slash"></i>
                    <span>وضع عدم الاتصال: تتصفح التطبيق من الذاكرة المحلية والدروس المحفوظة أوفلاين.</span>
                `;
                document.body.appendChild(banner);
            }
            banner.style.display = 'flex';
        } else {
            if (banner) {
                banner.className = 'network-status-pill online';
                banner.innerHTML = `
                    <i class="fa-solid fa-wifi"></i>
                    <span>تمت استعادة الاتصال بالإنترنت - يتم تحديث المنصة تلقائياً.</span>
                `;
                setTimeout(() => {
                    banner.style.display = 'none';
                }, 3500);
            }
        }
    }

    window.addEventListener('online', updateConnectionStatus);
    window.addEventListener('offline', updateConnectionStatus);
    if (!navigator.onLine) updateConnectionStatus();

    // 2. الفحص التلقائي الشامل لجميع أزرار الدروس الموجودة في الصفحة لتفعيل حالتها أوفلاين
    setTimeout(function() {
        document.querySelectorAll('[id^="btn_offline_"]').forEach(function(btn) {
            const vidId = btn.id.replace('btn_offline_', '');
            if (vidId && window.StepvoroVideoDownloader) {
                StepvoroVideoDownloader.checkAndInitLessonPlayer(vidId);
            }
        });
    }, 150);
});
