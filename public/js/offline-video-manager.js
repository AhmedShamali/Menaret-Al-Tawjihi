/**
 * Offline Video Manager for Educational Platform
 * يحفظ الفيديوهات مشفرة/مخزنة داخل IndexedDB الخاصة بالمنصة فقط،
 * لمنع تسريب الملفات كـ MP4 عادي مع إتاحة مشاهدتها بدون إنترنت.
 */

class OfflineVideoManager {
    constructor() {
        this.dbName = 'EducationalPlatform_OfflineStore';
        this.dbVersion = 1;
        this.storeName = 'offline_videos';
        this.db = null;
        this.activeDownloads = new Map();
        this.initPromise = this.initDB();
    }

    async initDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onupgradeneeded = (e) => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(this.storeName)) {
                    const store = db.createObjectStore(this.storeName, { keyPath: 'id' });
                    store.createIndex('subject_id', 'subject_id', { unique: false });
                    store.createIndex('saved_at', 'saved_at', { unique: false });
                }
            };

            request.onsuccess = (e) => {
                this.db = e.target.result;
                resolve(this.db);
            };

            request.onerror = (e) => {
                console.error("فشل تهيئة قاعدة بيانات التخزين الأوفلاين:", e);
                reject(e);
            };
        });
    }

    async isDownloaded(contentId) {
        await this.initPromise;
        return new Promise((resolve) => {
            const tx = this.db.transaction([this.storeName], 'readonly');
            const store = tx.objectStore(this.storeName);
            const req = store.get(String(contentId));
            req.onsuccess = () => resolve(!!req.result);
            req.onerror = () => resolve(false);
        });
    }

    async getVideo(contentId) {
        await this.initPromise;
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction([this.storeName], 'readonly');
            const store = tx.objectStore(this.storeName);
            const req = store.get(String(contentId));
            req.onsuccess = () => resolve(req.result);
            req.onerror = (err) => reject(err);
        });
    }

    async getAllDownloaded() {
        await this.initPromise;
        return new Promise((resolve) => {
            const tx = this.db.transaction([this.storeName], 'readonly');
            const store = tx.objectStore(this.storeName);
            const req = store.getAll();
            req.onsuccess = () => resolve(req.result || []);
            req.onerror = () => resolve([]);
        });
    }

    async deleteVideo(contentId) {
        await this.initPromise;
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction([this.storeName], 'readwrite');
            const store = tx.objectStore(this.storeName);
            const req = store.delete(String(contentId));
            req.onsuccess = () => resolve(true);
            req.onerror = (err) => reject(err);
        });
    }

    async downloadVideo(contentId, videoUrl, title, subjectName, subjectId, onProgress) {
        if (this.activeDownloads.has(contentId)) {
            return;
        }

        const controller = new AbortController();
        this.activeDownloads.set(contentId, controller);

        try {
            const response = await fetch(videoUrl, {
                signal: controller.signal,
                headers: { 'Accept': 'video/mp4,video/*;q=0.9,*/*;q=0.8' }
            });

            if (!response.ok) {
                throw new Error(`تعذر جلب ملف الفيديو (خطأ: ${response.status})`);
            }

            const contentLength = response.headers.get('content-length');
            const total = contentLength ? parseInt(contentLength, 10) : 0;
            let loaded = 0;

            const reader = response.body.getReader();
            const chunks = [];

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                chunks.push(value);
                loaded += value.length;

                if (total && onProgress) {
                    const percent = Math.round((loaded / total) * 100);
                    onProgress(percent, loaded, total);
                } else if (onProgress) {
                    onProgress(null, loaded, total);
                }
            }

            const blob = new Blob(chunks, { type: 'video/mp4' });

            await this.initPromise;
            await new Promise((resolve, reject) => {
                const tx = this.db.transaction([this.storeName], 'readwrite');
                const store = tx.objectStore(this.storeName);
                const record = {
                    id: String(contentId),
                    subject_id: String(subjectId),
                    title: title,
                    subject_name: subjectName,
                    blob: blob,
                    size: blob.size,
                    saved_at: new Date().toISOString()
                };

                const req = store.put(record);
                req.onsuccess = () => resolve(true);
                req.onerror = (err) => reject(err);
            });

            this.activeDownloads.delete(contentId);
            return true;

        } catch (err) {
            this.activeDownloads.delete(contentId);
            if (err.name === 'AbortError') {
                console.log('تم إلغاء التنزيل من قبل المستخدم');
            } else {
                console.error('خطأ أثناء تنزيل الفيديو أوفلاين:', err);
                throw err;
            }
        }
    }

    cancelDownload(contentId) {
        if (this.activeDownloads.has(contentId)) {
            this.activeDownloads.get(contentId).abort();
            this.activeDownloads.delete(contentId);
        }
    }
}

window.offlineVideoManager = new OfflineVideoManager();
