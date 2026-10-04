/**
 * Resumable Video & Large File Uploader with Network Disconnection Auto-Resume
 * منصة المنارة للثانوية العامة - نظام رفع الفيديوهات المرن فائق الاستقرار
 * يدعم الاستئناف التلقائي الفوري فور عودة الإنترنت دون فقدان أي جزء ودون إعادة الرفع من البداية
 */

(function (window) {
    'use strict';

    class ResumableUploader {
        constructor(config = {}) {
            this.chunkUrl = config.chunkUrl || '/educational-contents/upload-chunk';
            this.checkStatusUrl = config.checkStatusUrl || '/educational-contents/check-chunk-status';
            this.pingUrl = config.pingUrl || '/ping';
            this.csrfToken = config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            // Callbacks for UI updates
            this.onProgress = config.onProgress || (() => {});
            this.onStatus = config.onStatus || (() => {});
            this.onSpeed = config.onSpeed || (() => {});
            this.onEta = config.onEta || (() => {});
            this.onMeta = config.onMeta || (() => {});
            this.onPart = config.onPart || (() => {});
            this.onNetworkStateChange = config.onNetworkStateChange || (() => {});

            this.customChunkSize = config.chunkSize || null;
            this.isAborted = false;
            this.isPausedForOffline = false;
            this.currentFile = null;
            this.fileId = null;
        }

        /**
         * تنسيق حجم الملفات بشكل أنيق
         */
        static formatBytes(bytes, decimals = 1) {
            if (!bytes || bytes === 0) return '0 B';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        /**
         * تنسيق سرعة الرفع
         */
        static formatSpeed(bytesPerSec) {
            if (!bytesPerSec || bytesPerSec <= 0) return '--';
            if (bytesPerSec >= 1024 * 1024) {
                return (bytesPerSec / (1024 * 1024)).toFixed(1) + ' MB/s';
            }
            return Math.round(bytesPerSec / 1024) + ' KB/s';
        }

        /**
         * تنسيق الوقت المتبقي
         */
        static formatEta(seconds) {
            if (!seconds || seconds <= 0 || !isFinite(seconds)) return '--';
            if (seconds >= 3600) {
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.round((seconds % 3600) / 60);
                return `${hours} س و ${minutes} د`;
            }
            if (seconds >= 60) {
                const minutes = Math.floor(seconds / 60);
                const sec = seconds % 60;
                return `${minutes} د ${sec > 0 ? sec + ' ث' : ''}`;
            }
            return `${Math.round(seconds)} ثانية`;
        }

        /**
         * توليد بصمة فريدة وثابتة للملف لضمان الاستئناف بدقة
         */
        static getFileId(file) {
            const cleanName = encodeURIComponent(file.name).replace(/[^a-zA-Z0-9]/g, '').slice(0, 24) || 'vid';
            return 'vid_' + cleanName + '_' + file.size;
        }

        /**
         * فحص مباشر لوصول الإنترنت عبر طلب خفيف جداً مع جلب أحدث رمز CSRF
         */
        async checkConnection() {
            try {
                const res = await axios.get(this.pingUrl + '?t=' + Date.now(), {
                    timeout: 4000,
                    headers: { 'Cache-Control': 'no-cache, no-store, must-revalidate' }
                });
                if (res.data && (res.data.pong || res.data.status === 'ok')) {
                    if (res.data.csrf) {
                        this.csrfToken = res.data.csrf;
                    }
                    return true;
                }
                return false;
            } catch (err) {
                return false;
            }
        }

        /**
         * تجميد الرفع بذكاء والانتظار التلقائي حتى عودة الإنترنت دون فقدان أي تقدم
         */
        async waitForReconnection(currentProgressPercent = 0) {
            this.isPausedForOffline = true;
            this.onNetworkStateChange(false, currentProgressPercent);
            
            this.onStatus({
                html: `<i class="fa-solid fa-triangle-exclamation fa-beat" style="color: #f59e0b;"></i> <strong style="color: #b45309;">انقطع اتصال الإنترنت!</strong> تم تجميد الرفع مؤقتاً عند (${currentProgressPercent}%). بانتظار عودة النت للاستئناف التلقائي فوراً 🔄`,
                type: 'offline',
                percent: currentProgressPercent
            });
            this.onSpeed('-- (معلّق)');
            this.onEta('بانتظار عودة النت...');

            let checkCount = 0;
            return new Promise((resolve) => {
                let intervalId = null;

                const attemptReconnect = async () => {
                    if (this.isAborted) {
                        clearInterval(intervalId);
                        window.removeEventListener('online', onOnlineEvent);
                        return resolve(false);
                    }

                    checkCount++;
                    if (checkCount % 2 === 0) {
                        this.onStatus({
                            html: `<i class="fa-solid fa-satellite-dish fa-spin" style="color: #d97706;"></i> جاري فحص استجابة الشبكة (${checkCount})... تم حفظ (${currentProgressPercent}%) وسيكتمل الرفع فوراً!`,
                            type: 'offline',
                            percent: currentProgressPercent
                        });
                    }

                    const isOnline = await this.checkConnection();
                    if (isOnline) {
                        clearInterval(intervalId);
                        window.removeEventListener('online', onOnlineEvent);
                        this.isPausedForOffline = false;
                        this.onNetworkStateChange(true, currentProgressPercent);
                        
                        this.onStatus({
                            html: `<i class="fa-solid fa-bolt fa-fade" style="color: #10b981;"></i> <strong style="color: #047857;">عادت خدمة الإنترنت بنجاح! ⚡</strong> جاري استئناف الرفع تلقائياً من حيث توقف (${currentProgressPercent}%)...`,
                            type: 'online_restored',
                            percent: currentProgressPercent
                        });
                        
                        // مهلة قصيرة لاستقرار السوكت والشبكة
                        setTimeout(() => resolve(true), 600);
                    }
                };

                const onOnlineEvent = () => {
                    attemptReconnect();
                };

                window.addEventListener('online', onOnlineEvent);
                intervalId = setInterval(attemptReconnect, 2500);
                
                // فحص فوري مبكر
                attemptReconnect();
            });
        }

        /**
         * فحص الأجزاء المرفوعة مسبقاً من السيرفر مع إعادة المحاولة التلقائية عند التذبذب
         */
        async queryUploadedChunks(fileId, totalChunks) {
            let attempts = 0;
            while (!this.isAborted) {
                try {
                    const res = await axios.post(this.checkStatusUrl, {
                        file_id: fileId,
                        total_chunks: totalChunks,
                        _token: this.csrfToken
                    }, {
                        timeout: 15000,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });

                    if (res.data && Array.isArray(res.data.uploaded_chunks)) {
                        return new Set(res.data.uploaded_chunks);
                    }
                    return new Set();
                } catch (err) {
                    attempts++;
                    const isNetErr = !navigator.onLine || !err.response || err.code === 'ERR_NETWORK' || err.message?.includes('Network Error');
                    
                    if (isNetErr) {
                        await this.waitForReconnection(0);
                    } else if (err.response && err.response.status === 419) {
                        // تجديد رمز الحماية وإعادة المحاولة
                        await this.checkConnection();
                        await new Promise(r => setTimeout(r, 1000));
                    } else {
                        // خطأ غير شبكي: انتظر قليلاً وأعد المحاولة بحد أقصى 3 مرات
                        if (attempts >= 3) return new Set();
                        await new Promise(r => setTimeout(r, 2000));
                    }
                }
            }
            return new Set();
        }

        /**
         * رفع جزء محدد مع المقاومة الكاملة لانقطاع الإنترنت والاستئناف التلقائي
         */
        async uploadSingleChunk(chunkBlob, chunkIdx, totalChunks, fileName, fileId) {
            while (!this.isAborted) {
                const formData = new FormData();
                formData.append('file_id', fileId);
                formData.append('chunk_index', chunkIdx);
                formData.append('total_chunks', totalChunks);
                formData.append('file_name', fileName);
                formData.append('chunk', chunkBlob, 'part_' + chunkIdx);
                formData.append('_token', this.csrfToken);

                try {
                    const res = await axios.post(this.chunkUrl, formData, {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'multipart/form-data',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        timeout: 180000 // مهلة كافية 3 دقائق لكل جزء
                    });

                    return res.data;
                } catch (err) {
                    if (this.isAborted) throw new Error('تم إلغاء عملية الرفع.');

                    // فحص سبب الخطأ
                    const isNetworkOutage = !navigator.onLine || 
                                           !err.response || 
                                           err.code === 'ERR_NETWORK' || 
                                           err.code === 'ECONNABORTED' ||
                                           (err.message && err.message.toLowerCase().includes('network error')) ||
                                           (err.message && err.message.toLowerCase().includes('timeout'));

                    const isCsrfMismatch = err.response && (err.response.status === 419 || err.response.status === 401);

                    if (isNetworkOutage) {
                        const currentPct = Math.round((chunkIdx / totalChunks) * 100);
                        // الانتظار حتى عودة الإنترنت دون استسلام!
                        await this.waitForReconnection(currentPct);
                        // بعد عودة النت نعيد المحاولة للجزء نفسه مباشرة
                        continue;
                    }

                    if (isCsrfMismatch) {
                        // تجديد الرمز وإعادة المحاولة
                        await this.checkConnection();
                        await new Promise(r => setTimeout(r, 1000));
                        continue;
                    }

                    // أخطاء السيرفر المؤقتة (500, 502, 503, 504): إعادة المحاولة بعد 3 ثواني
                    if (err.response && err.response.status >= 500) {
                        this.onStatus({
                            html: `<i class="fa-solid fa-spinner fa-spin" style="color: #f59e0b;"></i> ضغط مؤقت على السيرفر بالجزء ${chunkIdx + 1}. إعادة المحاولة فوراً...`,
                            type: 'server_retry'
                        });
                        await new Promise(r => setTimeout(r, 3000));
                        continue;
                    }

                    // خطأ حقيقي من السيرفر (مثل 422 صيغة غير مدعومة أو غيره)
                    throw err;
                }
            }
        }

        /**
         * الدالة الرئيسية لرفع ملف الفيديو بأكمله بنظام التجزئة والاستئناف
         */
        async upload(file) {
            if (!file) {
                throw new Error('يرجى تحديد ملف الفيديو أولاً.');
            }

            this.isAborted = false;
            this.currentFile = file;
            this.fileId = ResumableUploader.getFileId(file);

            // تحديد حجم القطعة: إذا تم تمرير حجم محدد نستخدمه وإلا 4MB/5MB
            const CHUNK_SIZE = this.customChunkSize || (file.size > (1024 * 1024 * 1024) ? (5 * 1024 * 1024) : (4 * 1024 * 1024));
            const totalChunks = Math.ceil(file.size / CHUNK_SIZE);

            // حفظ تفاصيل الرفع في localStorage لحماية المستخدم في حال إغلاق أو تحديث الصفحة
            try {
                localStorage.setItem('ed_resumable_video_active', JSON.stringify({
                    fileId: this.fileId,
                    fileName: file.name,
                    fileSize: file.size,
                    totalChunks: totalChunks,
                    startedAt: Date.now()
                }));
            } catch (e) {}

            this.onStatus({
                html: '<i class="fa-solid fa-spinner fa-spin"></i> جاري فحص حالة الأجزاء السابقة واستئناف الرفع... 🚀',
                type: 'checking'
            });

            // فحص الأجزاء المرفوعة مسبقاً
            const alreadyUploaded = await this.queryUploadedChunks(this.fileId, totalChunks);
            const uploadedCount = alreadyUploaded.size;

            let uploadedVideoPath = null;
            let formattedVideoSize = null;

            if (uploadedCount > 0 && uploadedCount < totalChunks) {
                const resumedPct = Math.round((uploadedCount / totalChunks) * 100);
                this.onProgress(resumedPct);
                this.onStatus({
                    html: `<i class="fa-solid fa-bolt" style="color: #d97706;"></i> تم العثور على (${uploadedCount}) جزء مرفوع مسبقاً! جاري الاستئناف التلقائي من (${resumedPct}%) 🚀`,
                    type: 'resumed',
                    percent: resumedPct
                });
                this.onPart(`الجزء ${uploadedCount + 1}/${totalChunks} (مستأنف)`);
            }

            const startTime = Date.now();
            let bytesUploadedThisSession = 0;

            for (let i = 0; i < totalChunks; i++) {
                if (this.isAborted) {
                    throw new Error('تم إلغاء عملية الرفع.');
                }

                const start = i * CHUNK_SIZE;
                const end = Math.min(file.size, start + CHUNK_SIZE);
                const chunkLength = end - start;

                // تخطي الأجزاء المرفوعة مسبقاً، باستثناء الجزء الأخير ليقوم السيرفر بعملية الدمج
                if (alreadyUploaded.has(i) && (i < totalChunks - 1)) {
                    const currentPct = Math.round(((i + 1) / totalChunks) * 100);
                    this.onProgress(currentPct);
                    this.onMeta(`${ResumableUploader.formatBytes(end)} / ${ResumableUploader.formatBytes(file.size)}`);
                    this.onPart(`الجزء ${i + 1}/${totalChunks} (مستأنف)`);
                    continue;
                }

                const chunkBlob = file.slice(start, end);
                const chunkRes = await this.uploadSingleChunk(chunkBlob, i, totalChunks, file.name, this.fileId);

                bytesUploadedThisSession += chunkLength;
                const elapsedSec = (Date.now() - startTime) / 1000;
                const speedBps = elapsedSec > 0 ? (bytesUploadedThisSession / elapsedSec) : 0;
                const remainingBytes = file.size - end;
                const etaSec = speedBps > 0 ? Math.round(remainingBytes / speedBps) : 0;

                const currentPct = Math.round(((i + 1) / totalChunks) * 100);
                this.onProgress(currentPct);
                this.onStatus({
                    html: `<i class="fa-solid fa-cloud-arrow-up fa-fade"></i> جاري رفع ومعالجة أجزاء الفيديو: ${currentPct}%`,
                    type: 'uploading',
                    percent: currentPct
                });

                this.onMeta(`${ResumableUploader.formatBytes(end)} / ${ResumableUploader.formatBytes(file.size)}`);
                this.onSpeed(ResumableUploader.formatSpeed(speedBps));
                this.onEta(ResumableUploader.formatEta(etaSec));
                this.onPart(`الجزء ${i + 1}/${totalChunks}`);

                if (chunkRes && (chunkRes.done || chunkRes.completed)) {
                    uploadedVideoPath = chunkRes.uploaded_video_path || chunkRes.file_path;
                    formattedVideoSize = chunkRes.formatted_size;
                }
            }

            // تنظيف بيانات التخزين المؤقت بعد نجاح الرفع
            try {
                localStorage.removeItem('ed_resumable_video_active');
            } catch (e) {}

            return {
                done: true,
                uploaded_video_path: uploadedVideoPath,
                formatted_size: formattedVideoSize,
                file_id: this.fileId
            };
        }

        /**
         * إلغاء الرفع
         */
        abort() {
            this.isAborted = true;
        }
    }

    // إتاحة الكلاس عالمياً في نافذة المتصفح
    window.ResumableUploader = ResumableUploader;

})(window);
