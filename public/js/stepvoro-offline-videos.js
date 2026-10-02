/**
 * ============================================================================
 * Stepvoro Offline In-App Video & Course Content Manager (v15)
 * نظام تحميل وتشغيل الفيديوهات داخل التطبيق بدون إنترنت (IndexedDB Storage)
 * ============================================================================
 */

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
                    dbInstance = e.target.result;
                    resolve(dbInstance);
                };

                request.onerror = function (e) {
                    reject(e.target.error || new Error('فشل فتح قاعدة البيانات المحلية'));
                };
            } catch (err) {
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
        if (!btn) return;

        // في حال كان الفيديو محملاً ومحفوظاً بالفعل:
        if (btn.classList.contains('is-saved')) {
            StepvoroOfflineDB.getVideo(id).then((rec) => {
                if (rec) {
                    if (rec.blob) {
                        this.attachOfflineBlobToPlayer(id, rec.blob);
                        if (typeof window.showPwaToast === 'function') {
                            window.showPwaToast('يتم الآن تشغيل الدرس مباشرة من ذاكرة المنصة بدون إنترنت ⚡', 'success');
                        }
                        const player = document.getElementById('player_' + id);
                        if (player) {
                            player.play().catch(() => {});
                            player.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    } else {
                        window.location.href = '/offline-videos';
                    }
                }
            });
            return;
        }

        const isDirect = btn.getAttribute('data-is-direct') === '1';
        const title = btn.getAttribute('data-video-title') || 'درس تعليمي';
        const subject = btn.getAttribute('data-subject-title') || 'المنهاج';
        const url = btn.getAttribute('data-video-url');
        const ytEmbed = btn.getAttribute('data-yt-embed');
        const pdfUrl = btn.getAttribute('data-pdf-url');

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

            if (typeof window.Swal !== 'undefined') {
                window.Swal.fire({
                    icon: 'success',
                    title: hasPdf ? 'تم حفظ ملزمة وملاحظات الدرس أوفلاين 📚🎉' : 'تمت إضافة الدرس لمحفوظاتك 📌',
                    html: `
                        <div style="font-size: 0.9rem; color: #334155; line-height: 1.6; text-align: right;">
                            <p>${hasPdf ? 'تم حفظ أوراق عمل وملزمة هذا الدرس في ذاكرة التطبيق لتتمكن من مراجعتها <strong>بدون إنترنت</strong> في أي وقت.' : 'تم حفظ بيانات وملاحظات هذا الدرس في قائمتك لتسهيل الرجوع إليه.'}</p>
                            <div style="background: #f1f5f9; border-radius: 8px; padding: 10px; margin-top: 10px; font-size: 0.8rem; color: #475569;">
                                <i class="fa-solid fa-circle-info" style="color: #0284c7;"></i>
                                <span>ملاحظة: هذا الشرح معروض كبث YouTube مباشر ويتطلب اتصالاً بالإنترنت لمشاهدة الفيديو، في حين أن الشروحات المرفوعة بصيغة MP4 مباشرة تعمل بالكامل بدون نت.</span>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'فتح دروسي المحفوظة ⚡',
                    showCancelButton: true,
                    cancelButtonText: 'متابعة التصفح',
                    confirmButtonColor: '#0b3b6f'
                }).then((res) => {
                    if (res.isConfirmed) {
                        if (typeof window.openOfflineVault === 'function') {
                            window.openOfflineVault();
                        } else {
                            window.location.href = '/offline-videos';
                        }
                    }
                });
            } else if (typeof window.showPwaToast === 'function') {
                window.showPwaToast('تم حفظ الدرس في مكتبتك الأوفلاين!', 'success');
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
                if (!blob || blob.size < 1000 || (blob.type && blob.type.includes('text/html'))) {
                    // في حال كان الرابط محول أو غير مباشر، احفظ الدرس كدرس معتمد
                    self.saveExternalLesson(id, title, subject, '', '', btnElement);
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

                StepvoroOfflineDB.saveVideo(record)
                    .then(() => {
                        self.updateButtonUI(id, 'saved', 100, btnElement);
                        self.attachOfflineBlobToPlayer(id, blob);
                        StepvoroOfflineDB.updateGlobalOfflineBadge();

                        if (typeof window.Swal !== 'undefined') {
                            window.Swal.fire({
                                icon: 'success',
                                title: 'تم التحميل أوفلاين بنجاح! 🎉',
                                text: 'تم حفظ هذا الدرس في ذاكرة التطبيق (' + sizeFormatted + '). يمكنك الآن تشغيله بدون إنترنت من واجهة الفيديوهات المحملة.',
                                confirmButtonText: 'فتح مكتبة الأوفلاين ⚡',
                                showCancelButton: true,
                                cancelButtonText: 'متابعة التصفح',
                                confirmButtonColor: '#2563eb'
                            }).then((res) => {
                                if (res.isConfirmed) {
                                    window.location.href = '/offline-videos';
                                }
                            });
                        } else if (typeof window.showPwaToast === 'function') {
                            window.showPwaToast('تم حفظ الدرس بنجاح داخل المنصة (' + sizeFormatted + ')!', 'success');
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
                self.saveExternalLesson(id, title, subject, '', '', btnElement);
            }
        };

        xhr.onerror = function () {
            delete self.activeDownloads[id];
            self.saveExternalLesson(id, title, subject, '', '', btnElement);
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
            self.saveExternalLesson(id, title, subject, '', '', btnElement);
        }
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

    // تحديث شكل ومحتوى زر التنزيل بشكل راقٍ ومباشر
    updateButtonUI: function (id, state, percent, btnElement) {
        const btn = btnElement || document.getElementById('btn_offline_' + id);
        if (!btn) return;

        if (state === 'downloading') {
            btn.classList.add('is-downloading');
            btn.classList.remove('is-saved');
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
            btn.classList.remove('is-downloading');
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
            btn.classList.remove('is-downloading', 'is-saved');
            btn.disabled = false;
            btn.innerHTML = `
                <div class="ed-offline-btn-inner">
                    <span class="ed-offline-btn-icon"><i class="fa-solid fa-cloud-arrow-down"></i></span>
                    <span class="offline-btn-label">تحميل الدرس أوفلاين</span>
                </div>
            `;
        }
    },

    // ربط الـ Blob بمشغل الفيديو حتى يشتغل أوفلاين 100%
    attachOfflineBlobToPlayer: function (id, blob) {
        id = String(id);
        let player = document.getElementById('player_' + id) || document.getElementById('pub_vid_' + id) || document.getElementById('vid_direct_' + id);

        // إذا كان المشغل الأصلي iframe يوتيوب، ننشئ مشغل فيديو أصلي MP4 أوفلاين فوق الشيلد
        if (!player) {
            const frame = document.getElementById('shield_wrap_' + id) || document.getElementById('player_frame_' + id);
            if (frame) {
                const ifr = frame.querySelector('iframe');
                if (ifr) ifr.style.display = 'none';

                const newVid = document.createElement('video');
                newVid.id = 'player_' + id;
                newVid.controls = true;
                newVid.playsInline = true;
                newVid.setAttribute('controlsList', 'nodownload noplaybackrate');
                newVid.setAttribute('oncontextmenu', 'return false;');
                newVid.style.cssText = 'position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; background: #000; z-index: 40;';
                frame.appendChild(newVid);
                player = newVid;
            }
        }

        if (!player) return;

        const blobUrl = URL.createObjectURL(blob);
        const oldSources = player.querySelectorAll('source');
        oldSources.forEach(function (s) { s.remove(); });
        player.src = blobUrl;
        try {
            player.load();
        } catch (e) {}

        let badge = document.getElementById('offline_badge_' + id);
        if (!badge) {
            badge = document.createElement('div');
            badge.id = 'offline_badge_' + id;
            badge.className = 'player-offline-badge';
            badge.innerHTML = `<i class="fa-solid fa-bolt"></i> تشغيل أوفلاين من ذاكرة التطبيق (بدون إنترنت)`;
            if (player.parentElement) {
                player.parentElement.appendChild(badge);
            }
        }
    },

    checkAndInitLessonPlayer: function (id) {
        id = String(id);
        const self = this;
        StepvoroOfflineDB.getVideo(id).then((record) => {
            if (record) {
                self.updateButtonUI(id, 'saved', 100);
                if (record.blob) {
                    self.attachOfflineBlobToPlayer(id, record.blob);
                }
            } else {
                self.updateButtonUI(id, 'ready', 0);
            }
        }).catch((e) => {
            console.warn('Error checking offline video status:', e);
        });
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

// تهيئة وفحص حالة التوصيل بالإنترنت وفحص الفيديوهات في الصفحة
window.addEventListener('DOMContentLoaded', function () {
    StepvoroOfflineDB.updateGlobalOfflineBadge();

    // الفحص التلقائي لجميع أزرار التحميل
    setTimeout(function() {
        document.querySelectorAll('[id^="btn_offline_"]').forEach(function(btn) {
            const vidId = btn.id.replace('btn_offline_', '');
            if (vidId && window.StepvoroVideoDownloader) {
                StepvoroVideoDownloader.checkAndInitLessonPlayer(vidId);
            }
        });
    }, 150);
});

// ضمان الإتاحة العالمية على المتصفح
if (typeof window !== 'undefined') {
    window.StepvoroOfflineDB = window.StepvoroOfflineDB || StepvoroOfflineDB;
    window.StepvoroVideoDownloader = window.StepvoroVideoDownloader || StepvoroVideoDownloader;
}

