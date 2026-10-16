/**
 * ============================================================================
 * Stepvoro Offline In-App Video & Course Content Manager (v15)
 * نظام تحميل وتشغيل الفيديوهات داخل التطبيق بدون إنترنت (IndexedDB Storage)
 * ============================================================================
 */

(function () {
    'use strict';

    if (window.StepvoroOfflineDB && window.StepvoroVideoDownloader) {
        return;
    }

    const StepvoroOfflineDB = (function () {
        const DB_NAME = 'StepvoroOfflineStore';
        const DB_VERSION = 2;
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

            let isSettled = false;
            const timeoutId = setTimeout(() => {
                if (!isSettled) {
                    isSettled = true;
                    reject(new Error('IndexedDB timeout'));
                }
            }, 4000);

            try {
                const request = indexedDB.open(DB_NAME, DB_VERSION);

                request.onupgradeneeded = function (e) {
                    const db = e.target.result;
                    if (!db.objectStoreNames.contains(STORE_NAME)) {
                        const store = db.createObjectStore(STORE_NAME, { keyPath: 'id' });
                        store.createIndex('subject', 'subject', { unique: false });
                        store.createIndex('savedAt', 'savedAt', { unique: false });
                    }
                };

                request.onblocked = function () {
                    console.warn('StepvoroOfflineDB: ترقية قاعدة البيانات محجوزة بواسطة نافذة أخرى.');
                };

                request.onsuccess = function (e) {
                    if (isSettled) return;
                    isSettled = true;
                    clearTimeout(timeoutId);
                    dbInstance = e.target.result;
                    resolve(dbInstance);
                };

                request.onerror = function (e) {
                    if (isSettled) return;
                    isSettled = true;
                    clearTimeout(timeoutId);
                    reject(e.target.error || new Error('فشل فتح قاعدة البيانات المحلية'));
                };
            } catch (err) {
                if (isSettled) return;
                isSettled = true;
                clearTimeout(timeoutId);
                reject(err);
            }
        });
    }

    // حفظ فيديو أو درس محمل داخل قاعدة البيانات
    function saveVideo(record) {
        return openDB().then((db) => {
            return new Promise((resolve, reject) => {
                const tx = db.transaction([STORE_NAME], 'readwrite');
                const store = tx.objectStore(STORE_NAME);
                const req = store.put(record);

                req.onsuccess = () => {
                    updateGlobalOfflineBadge();
                    resolve(record);
                };
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
                        hasBlob: !!item.blob,
                        isExternalVideo: !!item.isExternalVideo,
                        ytEmbed: item.ytEmbed || '',
                        pdfUrl: item.pdfUrl || '',
                        hasPdf: !!item.pdfBlob
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

                req.onsuccess = () => {
                    updateGlobalOfflineBadge();
                    resolve(true);
                };
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

    function updateGlobalOfflineBadge() {
        getAllVideos().then(function(videos) {
            const count = videos ? videos.length : 0;
            const badges = document.querySelectorAll('#bottomNavOfflineBadge, .badge-offline-count');
            badges.forEach(b => {
                b.innerText = count;
                b.style.display = count > 0 ? 'inline-flex' : 'none';
            });
        }).catch(() => {});
    }

    return {
        openDB,
        saveVideo,
        getVideo,
        getAllVideos,
        deleteVideo,
        calculateTotalSize,
        formatBytes,
        updateGlobalOfflineBadge
    };
})();

// تصدير مباشر إلى كائن window لضمان الوصول من كافة مكونات المنصة
window.StepvoroOfflineDB = StepvoroOfflineDB;

// ============================================================================
// مشغل التنزيل الحي وتحديث واجهة المستخدم
// ============================================================================
const StepvoroVideoDownloader = {
    activeDownloads: {},

    isAppMode: function() {
        return window.matchMedia('(display-mode: standalone)').matches 
            || window.navigator.standalone === true 
            || document.body.classList.contains('in-standalone-app')
            || window.location.search.includes('mode=pwa');
    },

    handleAction: function(id, btnElement) {
        id = String(id);
        const btn = btnElement || document.getElementById('btn_offline_' + id);
        if (btn) btn.remove();
        if (typeof window.showPwaToast === 'function') {
            window.showPwaToast('الدروس متاحة للمشاهدة المباشرة فائقة الدقة عبر مشغل المنصة المحمي.', 'info');
        }
        return;
    },

        const isDirect = btn.getAttribute('data-is-direct') === '1';
        const isYouTube = btn.getAttribute('data-is-youtube') === '1';
        const prepareUrl = btn.getAttribute('data-prepare-url');
        const title = btn.getAttribute('data-video-title') || 'درس تعليمي';
        const subject = btn.getAttribute('data-subject-title') || 'المنهاج';
        const url = btn.getAttribute('data-video-url');
        const ytEmbed = btn.getAttribute('data-yt-embed');
        const pdfUrl = btn.getAttribute('data-pdf-url');

        // إذا كان فيديو يوتيوب ويحتاج تجهيز أوفلاين على الخادم
        if (isYouTube && prepareUrl) {
            const self = this;
            self.updateButtonUI(id, 'downloading', 15, btn);
            if (typeof window.showPwaToast === 'function') {
                window.showPwaToast('جاري تجهيز فيديو YouTube للتحميل والمشاهدة بدون إنترنت ⏳...', 'info');
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            fetch(prepareUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.ready && data.download_url) {
                    if (typeof window.showPwaToast === 'function') {
                        window.showPwaToast('تم تجهيز الفيديو بنجاح! يبدأ حفظه في هاتفك الآن ⚡', 'success');
                    }
                    self.startDownload(id, title, subject, data.download_url, btn);
                } else {
                    if (typeof window.showPwaToast === 'function') {
                        window.showPwaToast(data.message || 'جاري استكمال محرك التحميل. تم حفظ بيانات وملاحظات الدرس أوفلاين.', 'info');
                    }
                    self.saveExternalLesson(id, title, subject, ytEmbed, pdfUrl, btn);
                }
            })
            .catch(err => {
                console.warn('Prepare offline failed, falling back to external save:', err);
                self.saveExternalLesson(id, title, subject, ytEmbed, pdfUrl, btn);
            });
            return;
        }

        if (isDirect && url) {
            this.startDownload(id, title, subject, url, btn);
        } else {
            this.saveExternalLesson(id, title, subject, ytEmbed, pdfUrl, btn);
        }
    },

    // حفظ درس خارجي / يوتيوب مع ملزمة الـ PDF في المكتبة الأوفلاين
    saveExternalLesson: function (id, title, subject, ytEmbed, pdfUrl, btnElement) {
        id = String(id);
        const self = this;
        self.updateButtonUI(id, 'downloading', 50, btnElement);

        const record = {
            id: id,
            title: title || 'درس تعليمي',
            subject: subject || 'المنهاج الوزاري',
            isExternalVideo: true,
            ytEmbed: ytEmbed || '',
            pdfUrl: pdfUrl || '',
            sizeBytes: 1024 * 1024,
            sizeFormatted: 'محفوظ أوفلاين',
            savedAt: new Date().toLocaleDateString('ar-EG', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })
        };

        const onSaved = () => {
            self.updateButtonUI(id, 'saved', 100, btnElement);
            StepvoroOfflineDB.updateGlobalOfflineBadge();
            const hasPdf = !!record.pdfBlob;

            if (typeof window.showPwaToast === 'function') {
                window.showPwaToast(hasPdf ? 'تم حفظ ملزمة وملاحظات الدرس أوفلاين بنجاح ⚡' : 'تم حفظ الدرس في مكتبتك الأوفلاين بنجاح ⚡', 'success');
            }
        };

        if (pdfUrl) {
            fetch(pdfUrl)
                .then(r => r.ok ? r.blob() : null)
                .then(pdfBlob => {
                    if (pdfBlob) record.pdfBlob = pdfBlob;
                    return StepvoroOfflineDB.saveVideo(record);
                })
                .catch(() => StepvoroOfflineDB.saveVideo(record))
                .then(onSaved);
        } else {
            StepvoroOfflineDB.saveVideo(record).then(onSaved);
        }
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

                // فحص ما إذا كانت الاستجابة JSON وليست ملف MP4 ثنائي
                if (blob && (blob.type.includes('application/json') || (blob.type.includes('text/') && blob.size < 4000) || blob.size < 1000)) {
                    const reader = new FileReader();
                    reader.onload = function () {
                        try {
                            const data = JSON.parse(reader.result);
                            if (data && data.url) {
                                self.startDirectBlobDownload(id, title, subject, data.url, btnElement);
                                return;
                            }
                            if (data && data.message) {
                                self.updateButtonUI(id, 'ready', 0, btnElement);
                                if (typeof window.showPwaToast === 'function') {
                                    window.showPwaToast(data.message, data.corrupted ? 'error' : 'info');
                                }
                                return;
                            }
                        } catch (e) {}

                        self.updateButtonUI(id, 'ready', 0, btnElement);
                        if (typeof window.showPwaToast === 'function') {
                            window.showPwaToast('تعذر تنزيل ملف الفيديو (الملف غير مكتمل أو 0 بايت).', 'error');
                        }
                    };
                    reader.readAsText(blob);
                    return;
                }

                self.saveVideoBlobToDB(id, title, subject, videoUrl, blob, btnElement);
            } else if (xhr.status === 422) {
                self.updateButtonUI(id, 'ready', 0, btnElement);
                const blob = xhr.response;
                if (blob) {
                    const reader = new FileReader();
                    reader.onload = function () {
                        try {
                            const data = JSON.parse(reader.result);
                            if (typeof window.showPwaToast === 'function') {
                                window.showPwaToast(data.message || 'ملف هذا الدرس غير مكتمل (0 بايت). يرجى من المعلم إعادة رفع الفيديو.', 'error');
                            }
                            return;
                        } catch (e) {}
                        if (typeof window.showPwaToast === 'function') {
                            window.showPwaToast('ملف هذا الدرس غير مكتمل (0 بايت). يرجى من المعلم إعادة رفع الفيديو.', 'error');
                        }
                    };
                    reader.readAsText(blob);
                } else if (typeof window.showPwaToast === 'function') {
                    window.showPwaToast('ملف هذا الدرس غير مكتمل (0 بايت). يرجى من المعلم إعادة رفع الفيديو.', 'error');
                }
            } else {
                self.updateButtonUI(id, 'ready', 0, btnElement);
                if (typeof window.showPwaToast === 'function') {
                    if (xhr.status === 400 || xhr.status === 404) {
                        window.showPwaToast('هذا الشرح متاح للمشاهدة المباشرة داخل المنصة.', 'info');
                    } else {
                        window.showPwaToast('تعذر تحميل ملف الفيديو (كود: ' + xhr.status + ').', 'error');
                    }
                }
            }
        };

        xhr.onerror = function () {
            delete self.activeDownloads[id];
            self.updateButtonUI(id, 'ready', 0, btnElement);
            if (typeof window.showPwaToast === 'function') {
                window.showPwaToast('حدث خطأ في الاتصال بالشبكة أثناء تحميل الفيديو.', 'error');
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
            if (typeof window.showPwaToast === 'function') {
                window.showPwaToast('تعذر بدء تحميل الفيديو.', 'error');
            }
        }
    },

    // تنزيل مباشر للـ Blob من الرابط السحابي عند إرجاع JSON URL
    startDirectBlobDownload: function (id, title, subject, directUrl, btnElement) {
        id = String(id);
        const self = this;
        self.updateButtonUI(id, 'downloading', 10, btnElement);

        const xhr = new XMLHttpRequest();
        xhr.open('GET', directUrl, true);
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
                if (!blob || blob.size < 1000) {
                    self.updateButtonUI(id, 'ready', 0, btnElement);
                    if (typeof window.showPwaToast === 'function') {
                        window.showPwaToast('تعذر استلام بيانات الفيديو من التخزين السحابي (0 بايت).', 'error');
                    }
                    return;
                }
                self.saveVideoBlobToDB(id, title, subject, directUrl, blob, btnElement);
            } else {
                self.updateButtonUI(id, 'ready', 0, btnElement);
                if (typeof window.showPwaToast === 'function') {
                    window.showPwaToast('تعذر تحميل الفيديو من الرابط السحابي.', 'error');
                }
            }
        };

        xhr.onerror = function () {
            delete self.activeDownloads[id];
            self.updateButtonUI(id, 'ready', 0, btnElement);
            if (typeof window.showPwaToast === 'function') {
                window.showPwaToast('تعذر استكمال الاتصال بالرابط السحابي.', 'error');
            }
        };

        xhr.send();
    },

    // حفظ الـ Blob في IndexedDB وتحديث الواجهة
    saveVideoBlobToDB: function (id, title, subject, videoUrl, blob, btnElement) {
        const self = this;
        const sizeBytes = blob.size;
        const sizeFormatted = StepvoroOfflineDB.formatBytes(sizeBytes);

        const record = {
            id: id,
            title: title || 'درس تعليمي',
            subject: subject || 'المنهاج الوزاري',
            url: videoUrl,
            blob: blob,
            hasBlob: true,
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

        StepvoroOfflineDB.saveVideo(record)
            .then(() => {
                self.updateButtonUI(id, 'saved', 100, btnElement);
                self.attachOfflineBlobToPlayer(id, blob);
                StepvoroOfflineDB.updateGlobalOfflineBadge();

                if (typeof window.showPwaToast === 'function') {
                    window.showPwaToast('تم حفظ الدرس بنجاح في ذاكرة المنصة (' + sizeFormatted + ') ⚡', 'success');
                }
            })
            .catch((err) => {
                console.error('Error saving video to DB:', err);
                self.updateButtonUI(id, 'ready', 0, btnElement);
                if (typeof window.showPwaToast === 'function') {
                    window.showPwaToast('حدث خطأ أثناء حفظ الفيديو في ذاكرة المنصة.', 'error');
                }
            });
    },

    // حذف فيديو محفوظ لتحرير المساحة
    removeOfflineVideo: function (id, btnElement) {
        id = String(id);
        const self = this;
        const doDelete = () => {
            StepvoroOfflineDB.deleteVideo(id).then(() => {
                self.updateButtonUI(id, 'ready', 0, btnElement);
                
                const badge = document.getElementById('offline_badge_' + id);
                if (badge) badge.remove();

                StepvoroOfflineDB.updateGlobalOfflineBadge();

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
                if (typeof window.loadOfflineVideos === 'function') {
                    window.loadOfflineVideos();
                }
            });
        };

        if (window.Swal) {
            Swal.fire({
                title: 'هل تريد حذف هذا الدرس من ذاكرة الهاتف؟',
                text: 'سيتم تحرير المساحة، ويمكنك إعادة تحميله في أي وقت.',
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

    // إزالة أزرار التنزيل أوفلاين نهائياً
    updateButtonUI: function (id, state, percent, btnElement) {
        const btn = btnElement || document.getElementById('btn_offline_' + id);
        if (btn) btn.remove();
        const quickLbl = document.getElementById('quick_offline_lbl_' + id);
        if (quickLbl) quickLbl.remove();
        const quickBtn = document.getElementById('quick_offline_btn_' + id);
        if (quickBtn) quickBtn.remove();
    },

    attachOfflineBlobToPlayer: function (id, blob) {
        // تم إلغاء التخزين أوفلاين للفيديوهات والاعتماد التام على البث المباشر
        return;
    },

    checkAndInitLessonPlayer: function (id) {
        const btn = document.getElementById('btn_offline_' + id);
        if (btn) btn.remove();
        const quickBtn = document.getElementById('quick_offline_btn_' + id);
        if (quickBtn) quickBtn.remove();
        const quickLbl = document.getElementById('quick_offline_lbl_' + id);
        if (quickLbl) quickLbl.remove();
    }
};

// تصدير كائن مشغل التنزيل إلى نطاق window
window.StepvoroVideoDownloader = StepvoroVideoDownloader;

// الدالة العامة لتحديث التطبيق فورياً وتفريغ الكاش
window.forceUpdateApp = async function(btn) {
    if (btn) {
        btn.disabled = true;
        const icon = btn.querySelector('i');
        if (icon) icon.classList.add('fa-spin');
        const label = btn.querySelector('span');
        if (label) label.textContent = 'جاري التحديث...';
    }

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'جاري تحديث المنصة والتطبيق 🔄',
            text: 'يتم الآن فحص أحدث التحديثات والشعارات وتفريغ الذاكرة المؤقتة...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    try {
        if ('serviceWorker' in navigator) {
            const regs = await navigator.serviceWorker.getRegistrations();
            for (let reg of regs) {
                await reg.update().catch(() => {});
                if (reg.waiting) {
                    reg.waiting.postMessage({ action: 'skipWaiting' });
                }
                await reg.unregister().catch(() => {});
            }
        }
        if ('caches' in window) {
            const keys = await caches.keys();
            for (let k of keys) {
                await caches.delete(k);
            }
        }
        sessionStorage.clear();

        setTimeout(() => {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('v_updated', Date.now());

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'تم تحديث المنصة والتطبيق بنجاح 🎉',
                    text: 'تم تثبيت أحدث نسخة من المنصة والشعار، جاري إعادة التحميل...',
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = currentUrl.toString();
                });
            } else {
                window.location.href = currentUrl.toString();
            }
        }, 800);
    } catch (e) {
        console.error('Update app error:', e);
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('v_updated', Date.now());
        window.location.href = currentUrl.toString();
    }
};

// إزالة أي أزرار تحميل أوفلاين متبقية في الصفحة
window.addEventListener('DOMContentLoaded', function () {
    try {
        document.querySelectorAll('[id^="btn_offline_"], [id^="quick_offline_"], .ed-btn-offline-card, .btn-offline-quick-save, .topbar-offline-vault-btn').forEach(function(el) {
            el.remove();
        });
    } catch (e) {}
});

// ضمان الإتاحة العالمية على المتصفح
if (typeof window !== 'undefined') {
    window.StepvoroOfflineDB = window.StepvoroOfflineDB || StepvoroOfflineDB;
    window.StepvoroVideoDownloader = window.StepvoroVideoDownloader || StepvoroVideoDownloader;
}
})();

