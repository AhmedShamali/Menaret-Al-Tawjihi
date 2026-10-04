/**
 * Global Background Uploader & Seamless Portal Navigation
 * منصة المنارة للثانوية العامة - محرك الرفع المستمر بالخلفية عبر كافة الصفحات
 * يتيح للمصور، المدير، والمعلم رفع الفيديوهات الضخمة بالخلفية مع حرية كاملة للتنقل بين الصفحات
 */

(function (window) {
    'use strict';

    class BackgroundUploadManager {
        constructor() {
            this.state = {
                status: 'idle', // 'idle' | 'uploading' | 'completed' | 'error' | 'paused'
                file: null,
                fileId: null,
                fileName: '',
                fileSize: 0,
                fileSizeFormatted: '',
                progress: 0,
                speed: '--',
                eta: '--',
                partText: '',
                portal: '', // 'videographer' | 'admin' | 'teacher'
                originUrl: '',
                chunkUrl: '',
                checkStatusUrl: '',
                result: null, // { uploaded_video_path, formatted_size }
                error: null
            };

            this.uploaderInstance = null;
            this.pageSyncCallbacks = new Set();
            this.isSeamlessEnabled = true;

            // استعادة أي بيانات رفع سابقة من sessionStorage إن وجدت
            this.restoreSessionState();

            // تهيئة واجهة الودجت العائم
            this.initWidgetDOM();

            // تفعيل الملاحة السلسة عبر الروابط (SPA Transitions)
            this.initSeamlessNavigation();

            // حماية من إغلاق أو تحديث التبويب أثناء الرفع
            this.initBeforeUnloadProtection();

            // مزامنة مبدئية مع الصفحة الحالية
            document.addEventListener('DOMContentLoaded', () => {
                this.syncWithCurrentPage();
            });
        }

        /**
         * تنسيق حجم الملفات
         */
        static formatBytes(bytes) {
            if (!bytes || bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return (bytes / Math.pow(k, i)).toFixed(1) + ' ' + sizes[i];
        }

        /**
         * حفظ حالة الرفع في sessionStorage
         */
        saveSessionState() {
            try {
                if (this.state.status === 'completed' && this.state.result) {
                    sessionStorage.setItem('ed_bg_upload_completed', JSON.stringify({
                        portal: this.state.portal,
                        fileName: this.state.fileName,
                        fileSizeFormatted: this.state.fileSizeFormatted,
                        originUrl: this.state.originUrl,
                        result: this.state.result,
                        completedAt: Date.now()
                    }));
                } else if (this.state.status === 'idle') {
                    sessionStorage.removeItem('ed_bg_upload_completed');
                }
            } catch (e) {}
        }

        /**
         * استعادة حالة اكتمال الرفع السابقة
         */
        restoreSessionState() {
            try {
                const saved = sessionStorage.getItem('ed_bg_upload_completed');
                if (saved) {
                    const parsed = JSON.parse(saved);
                    // صالحة لمدة 6 ساعات
                    if (Date.now() - parsed.completedAt < 6 * 3600 * 1000) {
                        this.state.status = 'completed';
                        this.state.portal = parsed.portal;
                        this.state.fileName = parsed.fileName;
                        this.state.fileSizeFormatted = parsed.fileSizeFormatted;
                        this.state.originUrl = parsed.originUrl;
                        this.state.result = parsed.result;
                        this.state.progress = 100;
                    } else {
                        sessionStorage.removeItem('ed_bg_upload_completed');
                    }
                }
            } catch (e) {}
        }

        /**
         * التحقق هل يوجد رفع حالياً أو فيديو مكتمل جاهز للنشر
         */
        hasActiveUpload() {
            return this.state.status === 'uploading' || this.state.status === 'paused';
        }

        hasCompletedUpload() {
            return this.state.status === 'completed' && this.state.result && this.state.result.uploaded_video_path;
        }

        /**
         * بدء رفع فيديو جديد بالخلفية
         */
        async start(options = {}) {
            const { file, portal = 'videographer', chunkUrl, checkStatusUrl, originUrl = window.location.href, chunkSize } = options;

            if (!file) return;

            // إذا كان هناك رفع جاري لنفس الملف لا نكرر
            if (this.state.file && this.state.file.name === file.name && this.state.file.size === file.size && this.hasActiveUpload()) {
                return;
            }

            // إيقاف أي عملية سابقة إن كانت تعمل
            if (this.uploaderInstance) {
                try { this.uploaderInstance.abort(); } catch (e) {}
            }

            this.state.status = 'uploading';
            this.state.file = file;
            this.state.fileName = file.name;
            this.state.fileSize = file.size;
            this.state.fileSizeFormatted = BackgroundUploadManager.formatBytes(file.size);
            this.state.portal = portal;
            this.state.chunkUrl = chunkUrl;
            this.state.checkStatusUrl = checkStatusUrl;
            this.state.originUrl = originUrl;
            this.state.progress = 0;
            this.state.speed = 'جاري الحساب...';
            this.state.eta = 'بانتظار البدء...';
            this.state.partText = 'تهيئة الأجزاء...';
            this.state.result = null;
            this.state.error = null;

            this.showWidget();
            this.updateWidgetUI();
            this.notifyPageListeners();

            try {
                this.uploaderInstance = new window.ResumableUploader({
                    chunkUrl: chunkUrl,
                    checkStatusUrl: checkStatusUrl,
                    pingUrl: '/ping',
                    chunkSize: chunkSize || (3 * 1024 * 1024), // 3MB متوازن جداً
                    onProgress: (pct) => {
                        this.state.progress = pct;
                        this.updateWidgetUI();
                        this.notifyPageListeners();
                    },
                    onSpeed: (speed) => {
                        this.state.speed = speed;
                        this.updateWidgetUI();
                        this.notifyPageListeners();
                    },
                    onEta: (eta) => {
                        this.state.eta = eta;
                        this.updateWidgetUI();
                        this.notifyPageListeners();
                    },
                    onPart: (part) => {
                        this.state.partText = part;
                        this.updateWidgetUI();
                        this.notifyPageListeners();
                    },
                    onStatus: (statusObj) => {
                        if (statusObj && statusObj.type === 'offline') {
                            this.state.status = 'paused';
                        } else if (this.state.status === 'paused') {
                            this.state.status = 'uploading';
                        }
                        this.updateWidgetUI();
                        this.notifyPageListeners();
                    },
                    onNetworkStateChange: (isOnline) => {
                        this.state.status = isOnline ? 'uploading' : 'paused';
                        this.updateWidgetUI();
                        this.notifyPageListeners();
                    }
                });

                const uploadRes = await this.uploaderInstance.upload(file);

                if (uploadRes && (uploadRes.done || uploadRes.completed)) {
                    this.state.status = 'completed';
                    this.state.progress = 100;
                    this.state.result = {
                        uploaded_video_path: uploadRes.uploaded_video_path || uploadRes.file_path,
                        formatted_size: uploadRes.formatted_size || this.state.fileSizeFormatted
                    };
                    this.saveSessionState();
                    this.updateWidgetUI();
                    this.notifyPageListeners();

                    // إشعار نجاح فوري للمستخدم
                    if (window.Swal) {
                        window.Swal.fire({
                            toast: true,
                            position: 'bottom-start',
                            icon: 'success',
                            title: '🎉 اكتمل رفع الفيديو بنجاح!',
                            text: 'يمكنك الآن نشر وتوزيع المحاضرة فوراً.',
                            showConfirmButton: true,
                            confirmButtonText: 'العودة للمحاضرة',
                            timer: 6000
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.returnToUploadPage();
                            }
                        });
                    }
                }
            } catch (err) {
                if (this.uploaderInstance && this.uploaderInstance.isAborted) {
                    this.state.status = 'idle';
                } else {
                    this.state.status = 'error';
                    this.state.error = err.message || 'حدث خطأ أثناء رفع الفيديو';
                }
                this.updateWidgetUI();
                this.notifyPageListeners();
            }
        }

        /**
         * إلغاء الرفع الحالي
         */
        cancel() {
            if (!this.hasActiveUpload() && !this.hasCompletedUpload()) return;

            const confirmMsg = this.hasActiveUpload() 
                ? 'هل أنت متأكد من إلغاء عملية رفع الفيديو الجارية؟' 
                : 'هل تريد حذف نتيجة رفع الفيديو الحالية؟';

            if (confirm(confirmMsg)) {
                if (this.uploaderInstance) {
                    this.uploaderInstance.abort();
                }
                this.state.status = 'idle';
                this.state.file = null;
                this.state.result = null;
                this.saveSessionState();
                this.hideWidget();
                this.notifyPageListeners();
            }
        }

        /**
         * العودة لصفحة الرفع ونشر المحاضرة
         */
        returnToUploadPage() {
            if (this.state.originUrl) {
                this.navigate(this.state.originUrl);
            }
        }

        /**
         * بناء ويدجت الرفع العائم في الدوم
         */
        initWidgetDOM() {
            if (document.getElementById('ed-bg-uploader-widget')) return;

            const widget = document.createElement('div');
            widget.id = 'ed-bg-uploader-widget';
            widget.className = 'ed-bg-widget-collapsed-hidden';
            widget.innerHTML = `
                <div class="ed-bg-card" id="edBgCard">
                    <!-- رأس الويدجت -->
                    <div class="ed-bg-header" id="edBgHeader">
                        <div class="ed-bg-title-wrap">
                            <span class="ed-bg-pulse-icon" id="edBgStatusIcon">
                                <i class="fa-solid fa-cloud-arrow-up fa-fade"></i>
                            </span>
                            <div>
                                <strong id="edBgWidgetTitle" style="font-size: 0.86rem; color: #0f172a; display: block; font-family: 'Alexandria', sans-serif;">
                                    جاري رفع الفيديو بالخلفية
                                </strong>
                                <span id="edBgWidgetSubtitle" style="font-size: 0.72rem; color: #64748b; display: block;">
                                    يمكنك تصفح الموقع بحرية دون انقطاع الرفع
                                </span>
                            </div>
                        </div>
                        <div class="ed-bg-actions">
                            <button type="button" class="ed-bg-btn-icon" id="edBgToggleMinBtn" title="تصغير / تكبير">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <button type="button" class="ed-bg-btn-icon ed-bg-btn-danger" id="edBgCloseBtn" title="إلغاء الرفع">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <!-- محتوى الويدجت (الملف وشريط التقدم) -->
                    <div class="ed-bg-body" id="edBgBody">
                        <div class="ed-bg-file-info">
                            <div class="ed-bg-file-icon"><i class="fa-solid fa-video"></i></div>
                            <div class="ed-bg-file-text">
                                <div class="ed-bg-file-name" id="edBgFileName">-</div>
                                <div class="ed-bg-file-size" id="edBgFileSize">-</div>
                            </div>
                            <div class="ed-bg-file-pct" id="edBgPct">0%</div>
                        </div>

                        <!-- شريط التقدم -->
                        <div class="ed-bg-progress-track">
                            <div class="ed-bg-progress-bar" id="edBgBar" style="width: 0%;"></div>
                        </div>

                        <!-- إحصائيات السرعة والوقت المتبقي -->
                        <div class="ed-bg-meta">
                            <span id="edBgSpeed"><i class="fa-solid fa-gauge-high"></i> --</span>
                            <span id="edBgEta"><i class="fa-regular fa-clock"></i> --</span>
                            <span id="edBgPart"><i class="fa-solid fa-cubes"></i> --</span>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="ed-bg-footer" id="edBgFooter">
                            <button type="button" class="ed-bg-btn-primary" id="edBgReturnBtn">
                                <i class="fa-solid fa-arrow-turn-up fa-rotate-270"></i>
                                <span id="edBgReturnBtnText">العودة لشاشة نشر المحاضرة</span>
                            </button>
                        </div>
                    </div>

                    <!-- شريط مصغر يظهر عند التصغير -->
                    <div class="ed-bg-mini-bar" id="edBgMiniBar" style="display: none;">
                        <span class="ed-bg-mini-icon"><i class="fa-solid fa-cloud-arrow-up fa-bounce"></i></span>
                        <span class="ed-bg-mini-name" id="edBgMiniName">الفيديو</span>
                        <strong class="ed-bg-mini-pct" id="edBgMiniPct">0%</strong>
                        <button type="button" class="ed-bg-mini-expand" id="edBgMiniExpand" title="تكبير"><i class="fa-solid fa-up-right-and-down-left-from-center"></i></button>
                    </div>
                </div>
            `;

            // إضافة الأنماط الجمالية للويدجت
            const style = document.createElement('style');
            style.textContent = `
                #ed-bg-uploader-widget {
                    position: fixed;
                    bottom: 24px;
                    left: 24px;
                    z-index: 99999;
                    font-family: 'Alexandria', 'Tajawal', sans-serif;
                    direction: rtl;
                    text-align: right;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                }
                @media (max-width: 768px) {
                    #ed-bg-uploader-widget {
                        bottom: 78px;
                        left: 12px;
                        right: 12px;
                    }
                }
                .ed-bg-widget-collapsed-hidden {
                    display: none !important;
                }
                .ed-bg-card {
                    background: #ffffff;
                    border: 1.5px solid #bfdbfe;
                    border-radius: 16px;
                    box-shadow: 0 12px 36px -4px rgba(29, 78, 216, 0.18), 0 4px 12px rgba(0, 0, 0, 0.06);
                    width: 380px;
                    max-width: calc(100vw - 32px);
                    overflow: hidden;
                    animation: edFadeSlideUp 0.35s ease;
                }
                @keyframes edFadeSlideUp {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .ed-bg-header {
                    padding: 12px 16px;
                    background: linear-gradient(135deg, #eff6ff, #dbeafe);
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    border-bottom: 1px solid #bfdbfe;
                }
                .ed-bg-title-wrap {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .ed-bg-pulse-icon {
                    width: 32px;
                    height: 32px;
                    border-radius: 8px;
                    background: #1d4ed8;
                    color: #ffffff;
                    display: grid;
                    place-items: center;
                    font-size: 0.95rem;
                }
                .ed-bg-actions {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                }
                .ed-bg-btn-icon {
                    background: #ffffff;
                    border: 1px solid #cbd5e1;
                    width: 28px;
                    height: 28px;
                    border-radius: 6px;
                    cursor: pointer;
                    color: #475569;
                    display: grid;
                    place-items: center;
                    font-size: 0.75rem;
                    transition: all 0.15s;
                }
                .ed-bg-btn-icon:hover {
                    background: #f1f5f9;
                    color: #0f172a;
                }
                .ed-bg-btn-danger:hover {
                    background: #fee2e2;
                    color: #dc2626;
                    border-color: #fca5a5;
                }
                .ed-bg-body {
                    padding: 16px;
                }
                .ed-bg-file-info {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 10px;
                }
                .ed-bg-file-icon {
                    width: 32px;
                    height: 32px;
                    border-radius: 8px;
                    background: #f1f5f9;
                    color: #1d4ed8;
                    display: grid;
                    place-items: center;
                    font-size: 0.9rem;
                    flex-shrink: 0;
                }
                .ed-bg-file-text {
                    flex: 1;
                    min-width: 0;
                }
                .ed-bg-file-name {
                    font-size: 0.83rem;
                    font-weight: 700;
                    color: #0f172a;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                .ed-bg-file-size {
                    font-size: 0.72rem;
                    color: #64748b;
                }
                .ed-bg-file-pct {
                    font-size: 1.1rem;
                    font-weight: 800;
                    color: #1d4ed8;
                    font-family: monospace;
                }
                .ed-bg-progress-track {
                    width: 100%;
                    height: 8px;
                    background: #e2e8f0;
                    border-radius: 99px;
                    overflow: hidden;
                    margin-bottom: 10px;
                }
                .ed-bg-progress-bar {
                    height: 100%;
                    background: linear-gradient(90deg, #1d4ed8, #3b82f6, #059669);
                    border-radius: 99px;
                    transition: width 0.25s ease;
                }
                .ed-bg-meta {
                    display: flex;
                    justify-content: space-between;
                    font-size: 0.72rem;
                    color: #475569;
                    margin-bottom: 12px;
                    background: #f8fafc;
                    padding: 6px 10px;
                    border-radius: 8px;
                    border: 1px solid #f1f5f9;
                }
                .ed-bg-footer {
                    display: flex;
                    gap: 8px;
                }
                .ed-bg-btn-primary {
                    flex: 1;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                    background: #1d4ed8;
                    color: #ffffff;
                    border: none;
                    padding: 8px 14px;
                    border-radius: 8px;
                    font-size: 0.8rem;
                    font-weight: 700;
                    cursor: pointer;
                    transition: background 0.15s;
                }
                .ed-bg-btn-primary:hover {
                    background: #1e40af;
                }
                .ed-bg-card.completed {
                    border-color: #86efac;
                }
                .ed-bg-card.completed .ed-bg-header {
                    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
                    border-bottom-color: #bbf7d0;
                }
                .ed-bg-card.completed .ed-bg-pulse-icon {
                    background: #16a34a;
                }
                .ed-bg-card.completed .ed-bg-file-pct {
                    color: #16a34a;
                }
                .ed-bg-card.completed .ed-bg-btn-primary {
                    background: #16a34a;
                }
                .ed-bg-card.completed .ed-bg-btn-primary:hover {
                    background: #15803d;
                }
                .ed-bg-mini-bar {
                    padding: 10px 14px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    background: #ffffff;
                }
                .ed-bg-mini-icon {
                    color: #1d4ed8;
                    font-size: 0.95rem;
                }
                .ed-bg-mini-name {
                    font-size: 0.8rem;
                    color: #0f172a;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    max-width: 150px;
                }
                .ed-bg-mini-pct {
                    color: #1d4ed8;
                    font-size: 0.88rem;
                    margin-right: auto;
                }
                .ed-bg-mini-expand {
                    background: none;
                    border: none;
                    cursor: pointer;
                    color: #64748b;
                    font-size: 0.8rem;
                }
            `;

            document.head.appendChild(style);
            document.body.appendChild(widget);

            // إضافة الأحداث
            document.getElementById('edBgToggleMinBtn').addEventListener('click', () => this.toggleMinimize());
            document.getElementById('edBgMiniExpand').addEventListener('click', () => this.toggleMinimize());
            document.getElementById('edBgCloseBtn').addEventListener('click', () => this.cancel());
            document.getElementById('edBgReturnBtn').addEventListener('click', () => this.returnToUploadPage());
        }

        showWidget() {
            const w = document.getElementById('ed-bg-uploader-widget');
            if (w) w.classList.remove('ed-bg-widget-collapsed-hidden');
        }

        hideWidget() {
            const w = document.getElementById('ed-bg-uploader-widget');
            if (w) w.classList.add('ed-bg-widget-collapsed-hidden');
        }

        toggleMinimize() {
            const body = document.getElementById('edBgBody');
            const header = document.getElementById('edBgHeader');
            const mini = document.getElementById('edBgMiniBar');

            if (body.style.display === 'none') {
                body.style.display = 'block';
                header.style.display = 'flex';
                mini.style.display = 'none';
            } else {
                body.style.display = 'none';
                header.style.display = 'none';
                mini.style.display = 'flex';
            }
        }

        /**
         * تحديث محتوى الودجت العائم
         */
        updateWidgetUI() {
            const card = document.getElementById('edBgCard');
            const title = document.getElementById('edBgWidgetTitle');
            const subtitle = document.getElementById('edBgWidgetSubtitle');
            const statusIcon = document.getElementById('edBgStatusIcon');
            const fileName = document.getElementById('edBgFileName');
            const fileSize = document.getElementById('edBgFileSize');
            const pct = document.getElementById('edBgPct');
            const bar = document.getElementById('edBgBar');
            const speed = document.getElementById('edBgSpeed');
            const eta = document.getElementById('edBgEta');
            const part = document.getElementById('edBgPart');
            const returnBtn = document.getElementById('edBgReturnBtn');
            const returnBtnText = document.getElementById('edBgReturnBtnText');
            const miniName = document.getElementById('edBgMiniName');
            const miniPct = document.getElementById('edBgMiniPct');

            if (!card) return;

            fileName.textContent = this.state.fileName || 'ملف فيديو';
            miniName.textContent = this.state.fileName || 'فيديو';
            fileSize.textContent = this.state.fileSizeFormatted || '-';
            pct.textContent = this.state.progress + '%';
            miniPct.textContent = this.state.progress + '%';
            bar.style.width = this.state.progress + '%';
            speed.innerHTML = `<i class="fa-solid fa-gauge-high"></i> ${this.state.speed || '--'}`;
            eta.innerHTML = `<i class="fa-regular fa-clock"></i> ${this.state.eta || '--'}`;
            part.innerHTML = `<i class="fa-solid fa-cubes"></i> ${this.state.partText || '--'}`;

            if (this.state.status === 'completed') {
                card.classList.add('completed');
                statusIcon.innerHTML = `<i class="fa-solid fa-circle-check"></i>`;
                title.textContent = 'اكتمل رفع الفيديو بنجاح!';
                subtitle.textContent = 'جاهز للتثبيت والنشر الفوري للمحاضرة';
                returnBtnText.textContent = 'استكمال ونشر المحاضرة الآن 🚀';
                returnBtn.style.background = '#16a34a';
            } else if (this.state.status === 'paused') {
                card.classList.remove('completed');
                statusIcon.innerHTML = `<i class="fa-solid fa-triangle-exclamation fa-beat" style="color: #f59e0b;"></i>`;
                title.textContent = 'انقطع الاتصال (معلّق)';
                subtitle.textContent = 'بانتظار عودة الإنترنت للاستئناف التلقائي...';
            } else if (this.state.status === 'error') {
                card.classList.remove('completed');
                statusIcon.innerHTML = `<i class="fa-solid fa-circle-xmark" style="color: #dc2626;"></i>`;
                title.textContent = 'تعثر الرفع';
                subtitle.textContent = this.state.error || 'يرجى التحقق من الشبكة وإعادة المحاولة';
            } else {
                card.classList.remove('completed');
                statusIcon.innerHTML = `<i class="fa-solid fa-cloud-arrow-up fa-fade"></i>`;
                title.textContent = 'جاري رفع الفيديو بالخلفية';
                subtitle.textContent = 'يمكنك تصفح الموقع بحرية دون توقف الرفع';
                returnBtnText.textContent = 'العودة لشاشة نشر المحاضرة';
                returnBtn.style.background = '#1d4ed8';
            }

            if (this.hasActiveUpload() || this.hasCompletedUpload()) {
                this.showWidget();
            }
        }

        /**
         * إشعار واجهة الصفحة الحالية بأحدث البيانات
         */
        notifyPageListeners() {
            this.pageSyncCallbacks.forEach(cb => {
                try { cb(this.state); } catch (e) {}
            });
        }

        /**
         * تسجيل مستمع لمزامنة صفحة معينة مع الرفع الجاري
         */
        onStateChange(callback) {
            this.pageSyncCallbacks.add(callback);
            // إرسال الحالة الحالية فوراً
            callback(this.state);
            return () => this.pageSyncCallbacks.delete(callback);
        }

        /**
         * مزامنة الصفحة المفتوحة حالياً تلقائياً إذا كانت تحتوي على نموذج رفع
         */
        syncWithCurrentPage() {
            // 1. مزامنة صفحة المصور (videographer/contents/create)
            const vgForm = document.getElementById('videographerUploadForm');
            if (vgForm) {
                this.syncVideographerPage();
            }

            // 2. مزامنة صفحة الإدارة / المعلم (educational_contents/create)
            const edForm = document.getElementById('educationalContentForm');
            if (edForm) {
                this.syncEducationalContentPage();
            }

            // 3. مزامنة صفحة مكتبة فيديوهات المعلم (teacher/videos/index)
            const teacherVideoModal = document.getElementById('uploadVideoModal');
            if (teacherVideoModal) {
                this.syncTeacherVideosPage();
            }
        }

        /**
         * مزامنة خاصة بصفحة المصور
         */
        syncVideographerPage() {
            const progressWrap = document.getElementById('chunkUploadProgressWrap');
            const fileNameEl = document.getElementById('uploadFileName');
            const fileSizeEl = document.getElementById('uploadFileSize');
            const percentageEl = document.getElementById('uploadPercentage');
            const progressBar = document.getElementById('uploadProgressBar');
            const statusText = document.getElementById('uploadStatusText');
            const completedBadge = document.getElementById('uploadCompletedBadge');
            const uploadedVideoPathInput = document.getElementById('uploadedVideoPath');
            const formattedSizeInput = document.getElementById('formattedSize');
            const btnSubmit = document.getElementById('btnSubmitForm');
            const videoFileInput = document.getElementById('videoFileInput');

            if (!progressWrap) return;

            // إذا كان هناك فيديو مكتمل أو قيد الرفع تابع لبوابة المصور
            if (this.hasActiveUpload() || this.hasCompletedUpload()) {
                progressWrap.style.display = 'block';
                if (fileNameEl) fileNameEl.textContent = this.state.fileName;
                if (fileSizeEl) fileSizeEl.textContent = this.state.fileSizeFormatted;
                if (percentageEl) percentageEl.textContent = this.state.progress + '%';
                if (progressBar) progressBar.style.width = this.state.progress + '%';

                if (this.state.status === 'completed' && this.state.result) {
                    if (uploadedVideoPathInput) uploadedVideoPathInput.value = this.state.result.uploaded_video_path;
                    if (formattedSizeInput) formattedSizeInput.value = this.state.result.formatted_size;
                    if (completedBadge) completedBadge.style.display = 'flex';
                    if (statusText) statusText.textContent = 'اكتمل رفع الفيديو بنجاح! جاهز للنشر والتوزيع.';
                    if (btnSubmit) {
                        btnSubmit.disabled = false;
                        btnSubmit.style.opacity = '1';
                        btnSubmit.innerHTML = `<i class="fa-solid fa-cloud-arrow-up"></i> <span>نشر وتوزيع المحاضرة فوراً</span>`;
                    }
                } else if (this.state.status === 'uploading') {
                    if (completedBadge) completedBadge.style.display = 'none';
                    if (statusText) statusText.textContent = `جاري رفع أجزاء الفيديو في الخلفية (${this.state.partText})...`;
                    if (btnSubmit) {
                        btnSubmit.disabled = true;
                        btnSubmit.style.opacity = '0.6';
                        btnSubmit.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>جاري رفع أجزاء الفيديو (${this.state.progress}%)...</span>`;
                    }
                }
            }

            // منع إرسال ملف الفيديو الضخم مرتين عبر الفورم التقليدي بعد رفعه مجزأً
            vgForm.addEventListener('submit', function (e) {
                if (window.EdBackgroundUploader.hasActiveUpload()) {
                    e.preventDefault();
                    alert('يرجى الانتظار حتى اكتمال رفع الفيديو في الخلفية أولاً.');
                    return false;
                }
                if (uploadedVideoPathInput && uploadedVideoPathInput.value) {
                    // تفريغ الملف لكي لا يرفعه المتصفح مجدداً في طلب الـ POST العادي
                    if (videoFileInput) {
                        videoFileInput.removeAttribute('name');
                    }
                }
            });
        }

        /**
         * مزامنة صفحة الإدارة / المعلم للمحتوى التعليمي
         */
        syncEducationalContentPage() {
            const uploadedVideoPathInput = document.getElementById('uploaded_video_path');
            const progressBarFill = document.querySelector('.progress-bar-fill');
            const progressPercentText = document.querySelector('.progress-percent');
            const progressStatusText = document.querySelector('.progress-status');

            if (this.hasCompletedUpload() && uploadedVideoPathInput) {
                uploadedVideoPathInput.value = this.state.result.uploaded_video_path;
                if (progressBarFill) progressBarFill.style.width = '100%';
                if (progressPercentText) progressPercentText.textContent = '100%';
                if (progressStatusText) progressStatusText.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #10b981;"></i> تم رفع الفيديو مسبقاً بنجاح في الخلفية!';
            }
        }

        /**
         * مزامنة صفحة مكتبة فيديوهات المعلم
         */
        syncTeacherVideosPage() {
            // يمكن استكمال المزامنة عند الحاجة
        }

        /**
         * حماية المتصفح من الإغلاق غير المقصود أثناء الرفع
         */
        initBeforeUnloadProtection() {
            window.addEventListener('beforeunload', (e) => {
                if (this.hasActiveUpload()) {
                    e.preventDefault();
                    e.returnValue = 'يوجد ملف فيديو قيد الرفع في الخلفية حالياً. هل أنت متأكد من مغادرة المنصة؟';
                    return e.returnValue;
                }
            });
        }

        /**
         * محرك الملاحة السلسة عبر الروابط (SPA Transitions)
         * يتيح للمستخدم الانتقال لأي صفحة داخل البوابة بدون تفريغ الصفحة أو إيقاف الرفع
         */
        initSeamlessNavigation() {
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
                if (link.target === '_blank' || link.hasAttribute('download')) return;
                if (link.classList.contains('no-pjax') || link.dataset.noPjax) return;

                let targetUrl;
                try {
                    targetUrl = new URL(link.href, window.location.origin);
                } catch (err) {
                    return;
                }

                // الروابط الداخلية فقط على نفس الدومين
                if (targetUrl.origin !== window.location.origin) return;

                // استثناء روابط الخروج أو التبديل اللغوي أو التوثيق
                if (targetUrl.pathname.includes('/logout') || targetUrl.pathname.includes('/lang/')) return;

                // مسارات اللوحات الداخلية المدعومة: videographer, admin, teacher, student
                const supportedPrefixes = ['/videographer', '/admin', '/teacher', '/student'];
                const isSupported = supportedPrefixes.some(p => targetUrl.pathname.startsWith(p));

                if (isSupported) {
                    e.preventDefault();
                    this.navigate(targetUrl.href);
                }
            });

            // دعم زري الرجوع والتقدم في المتصفح
            window.addEventListener('popstate', (e) => {
                if (e.state && e.state.edPjaxUrl) {
                    this.navigate(e.state.edPjaxUrl, false);
                }
            });
        }

        /**
         * تحميل الصفحة الجديدة واستبدال المحتوى بسلاسة دون إيقاف الرفع
         */
        async navigate(url, pushState = true) {
            // شريط تحميل علوي نحيف مثل يوتيوب
            let topLoader = document.getElementById('ed-top-loader');
            if (!topLoader) {
                topLoader = document.createElement('div');
                topLoader.id = 'ed-top-loader';
                topLoader.style.cssText = 'position:fixed;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#2563eb,#3b82f6,#10b981);z-index:999999;transition:width 0.2s;width:0%;';
                document.body.appendChild(topLoader);
            }
            topLoader.style.width = '35%';
            topLoader.style.display = 'block';

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                topLoader.style.width = '75%';

                if (!response.ok) {
                    window.location.href = url;
                    return;
                }

                const htmlText = await response.text();
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(htmlText, 'text/html');

                const newContentBody = newDoc.querySelector('.content-body');
                const currentContentBody = document.querySelector('.content-body');

                if (newContentBody && currentContentBody) {
                    // تحديث عنوان الصفحة
                    document.title = newDoc.title;

                    // استبدال المحتوى
                    currentContentBody.innerHTML = newContentBody.innerHTML;

                    // إعادة تنفيذ السكربتات المضمنة في المحتوى الجديد
                    const scripts = currentContentBody.querySelectorAll('script');
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                        newScript.textContent = oldScript.textContent;
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });

                    // تحديث رابط المتصفح
                    if (pushState) {
                        window.history.pushState({ edPjaxUrl: url }, '', url);
                    }

                    // تحديث الحالة النشطة في القائمة الجانبية وشريط الجوال
                    this.updateActiveNavLinks(url);

                    // الصعود لأعلى الصفحة بسلاسة
                    window.scrollTo({ top: 0, behavior: 'smooth' });

                    // إعادة تشغيل المزامنة مع الصفحة الجديدة
                    this.syncWithCurrentPage();

                    topLoader.style.width = '100%';
                    setTimeout(() => { topLoader.style.display = 'none'; topLoader.style.width = '0%'; }, 250);
                } else {
                    // إذا لم نجد محتوى متوافق نذهب بالمتصفح بشكل طبيعي
                    window.location.href = url;
                }
            } catch (err) {
                // فشل الطلب: انتقال عادي بالمتصفح
                window.location.href = url;
            }
        }

        /**
         * تحديث تمييز الرابط النشط في القوائم
         */
        updateActiveNavLinks(currentUrl) {
            const path = new URL(currentUrl, window.location.origin).pathname;
            document.querySelectorAll('.nav-item, .bottom-nav-item').forEach(el => {
                const href = el.getAttribute('href');
                if (href) {
                    try {
                        const linkPath = new URL(href, window.location.origin).pathname;
                        if (linkPath === path || (path.length > 2 && linkPath.startsWith(path))) {
                            el.classList.add('active');
                        } else {
                            el.classList.remove('active');
                        }
                    } catch (e) {}
                }
            });
        }
    }

    // إنشاء الكائن وإتاحته عاماً في النافذة
    window.EdBackgroundUploader = new BackgroundUploadManager();

})(window);
