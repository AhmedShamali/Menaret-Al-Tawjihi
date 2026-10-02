@extends('layouts.app')

@section('title', __('مكتبة الفيديوهات المحملة أوفلاين') . ' | ' . __(\App\Models\Setting::get('site_name', 'Step by Step')))

@section('content')
<div class="offline-vault-page-container">
    {{-- رأس الصفحة --}}
    <div class="offline-page-header">
        <div class="header-content">
            <div class="header-badge">
                <span class="pulse-dot"></span>
                <span id="networkStatusLabel">{{ __('فحص الاتصال...') }}</span>
            </div>
            <h1 class="page-title">
                <i class="fa-solid fa-cloud-arrow-down" style="color: #2563eb; margin-left: 8px;"></i>
                {{ __('الفيديوهات المحملة داخل المنصة') }}
            </h1>
            <p class="page-subtitle">
                {{ __('جميع الدروس والحصص المحفوظة في ذاكرة التطبيق، متاحة للمشاهدة بدون إنترنت في أي وقت ومكان.') }}
            </p>
        </div>

        {{-- إحصائيات الذاكرة والتحكم بتصميم كلاسيكي موحد --}}
        <div class="storage-stats-card">
            <div class="classic-stat-pod">
                <div class="stat-icon-pod"><i class="fa-solid fa-book-bookmark"></i></div>
                <div class="stat-item">
                    <span class="stat-label">{{ __('الدروس المحفوظة') }}</span>
                    <span class="stat-value" id="offlineLessonsCount">0</span>
                </div>
            </div>
            <div class="stat-divider"></div>
            <div class="classic-stat-pod">
                <div class="stat-icon-pod stat-icon-storage"><i class="fa-solid fa-hard-drive"></i></div>
                <div class="stat-item">
                    <span class="stat-label">{{ __('المساحة المستهلكة') }}</span>
                    <span class="stat-value" id="offlineStorageSize">0 MB</span>
                </div>
            </div>
            <div class="stat-actions" style="display: flex; gap: 8px;">
                <button type="button" class="btn-clear-vault" onclick="confirmClearAllOfflineVideos()" id="btnClearAll" style="display: none;" title="{{ __('حذف كافة الفيديوهات لتحرير الذاكرة') }}">
                    <i class="fa-regular fa-trash-can"></i>
                    <span>{{ __('تحرير الذاكرة') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- مشغل الفيديو الأوفلاين المدمج --}}
    <div class="offline-active-player-wrapper" id="offlinePlayerSection" style="display: none;">
        <div class="player-card">
            <div class="player-header">
                <div class="player-meta">
                    <span class="offline-chip"><i class="fa-solid fa-bolt"></i> {{ __('مشاهدة أوفلاين بدون نت') }}</span>
                    <h3 id="currentPlayingTitle" class="current-title">{{ __('عنوان الدرس') }}</h3>
                    <span id="currentPlayingSubject" class="current-subject">{{ __('المادة الدراسية') }}</span>
                </div>
                <button type="button" class="btn-close-player" onclick="closeActiveOfflinePlayer()" title="{{ __('إغلاق المشغل') }}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="video-container" style="position: relative; aspect-ratio: 16/9; background: #000; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                <video id="offlineActiveVideo" controls playsinline controlsList="nodownload noplaybackrate" style="width: 100%; height: 100%; object-fit: contain; background: #000; border-radius: 12px;"></video>
                <iframe id="offlineActiveIframe" style="display: none; width: 100%; height: 100%; border: none;" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                <div id="offlineFallbackContainer" style="display: none; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center; padding: 24px; background: #0f172a; color: #fff;">
                    <i class="fa-solid fa-file-pdf" style="font-size: 2.5rem; color: #ef4444; margin-bottom: 12px;"></i>
                    <h4 style="font-size: 1.05rem; margin-bottom: 6px;">ملزمة الدرس متاحة للمطالعة</h4>
                    <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 16px;">يمكنك قراءة ملزمة وأوراق عمل هذا الدرس بدون إنترنت.</p>
                    <button type="button" id="btnActiveOpenPdf" class="btn-play-offline" style="background: #2563eb; color: #fff; padding: 8px 20px;">
                        <i class="fa-solid fa-book-open"></i> <span>فتح ملزمة الدرس ⚡</span>
                    </button>
                </div>
            </div>
            <div class="player-controls-bar">
                <div class="speed-selector">
                    <span class="speed-label"><i class="fa-solid fa-gauge-high"></i> {{ __('السرعة:') }}</span>
                    <button type="button" class="speed-pill" onclick="setOfflinePlayerSpeed(0.75, this)">0.75x</button>
                    <button type="button" class="speed-pill active" onclick="setOfflinePlayerSpeed(1, this)">1x</button>
                    <button type="button" class="speed-pill" onclick="setOfflinePlayerSpeed(1.25, this)">1.25x</button>
                    <button type="button" class="speed-pill" onclick="setOfflinePlayerSpeed(1.5, this)">1.5x</button>
                    <button type="button" class="speed-pill" onclick="setOfflinePlayerSpeed(2, this)">2x</button>
                </div>
                <button type="button" class="btn-fullscreen-toggle" onclick="toggleOfflinePlayerFullscreen()">
                    <i class="fa-solid fa-expand"></i> <span>{{ __('ملء الشاشة') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- شريط البحث والتصفية --}}
    <div class="search-filter-bar" id="searchFilterBar" style="display: none;">
        <div class="search-input-wrap">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="offlineSearchInput" placeholder="{{ __('ابحث عن درس أو مادة في قائمة المحفوظات...') }}" oninput="filterOfflineVideos()">
        </div>
        <div class="filter-count">
            <span id="filteredCountLabel">{{ __('عرض جميع الدروس') }}</span>
        </div>
    </div>

    {{-- شبكة بطاقات الفيديوهات المحملة --}}
    <div class="offline-videos-grid" id="offlineVideosGrid">
        <div class="offline-loading-state">
            <div class="spinner-pulse"></div>
            <p>{{ __('جاري فحص ذاكرة التطبيق واسترجاع الدروس المحفوظة أوفلاين...') }}</p>
        </div>
    </div>

    {{-- حالة الذاكرة الفارغة (Empty State) --}}
    <div class="offline-empty-state" id="offlineEmptyState" style="display: none;">
        <div class="empty-icon-circle">
            <i class="fa-solid fa-cloud-arrow-down"></i>
        </div>
        <h3>{{ __('لا توجد دروس محملة أوفلاين حتى الآن') }}</h3>
        <p>
            {{ __('عند تصفح أي مادة دراسية، اضغط على زر "تحميل أوفلاين" بجانب أي درس تريده، وسيتم حفظه فوراً في هذه الشاشة لتتمكن من فتحه ودراسته بدون أي اتصال بالإنترنت.') }}
        </p>
        <div class="empty-actions">
            <a href="{{ route('subjects.index') }}" class="btn-browse-courses">
                <i class="fa-solid fa-book-open"></i>
                <span>{{ __('تصفح المواد الدراسية الآن') }}</span>
            </a>
        </div>
    </div>
</div>

<style>
/* ==========================================================================
   تنسيقات شاشة الفيديوهات المحملة أوفلاين الفاخرة (Step by Step Offline Library)
   ========================================================================== */
.offline-vault-page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px 16px 80px;
    font-family: 'Alexandria', 'Tajawal', sans-serif;
    color: #0f172a;
}

.offline-page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 28px;
    margin-bottom: 24px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
    flex-wrap: wrap;
}

.header-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 700;
    margin-bottom: 12px;
    background: #f1f5f9;
    color: #475569;
    transition: all 0.3s ease;
}

.header-badge.online {
    background: #dcfce7;
    color: #15803d;
}

.header-badge.offline {
    background: #fef3c7;
    color: #b45309;
}

.pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
    display: inline-block;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.1);
}

.page-title {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 8px;
    letter-spacing: -0.02em;
}

.page-subtitle {
    font-size: 0.92rem;
    color: #64748b;
    margin: 0;
    line-height: 1.6;
    max-width: 580px;
}

.storage-stats-card {
    display: flex;
    align-items: center;
    gap: 20px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 14px 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.classic-stat-pod {
    display: flex;
    align-items: center;
    gap: 12px;
}

.stat-icon-pod {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    display: grid;
    place-items: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.stat-icon-pod.stat-icon-storage {
    background: #f0fdf4;
    color: #15803d;
    border-color: #bbf7d0;
}

.stat-item {
    display: flex;
    flex-direction: column;
}

.stat-label {
    font-size: 0.76rem;
    color: #64748b;
    font-weight: 600;
}

.stat-value {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
}

.stat-divider {
    width: 1px;
    height: 38px;
    background: #e2e8f0;
}

.btn-clear-vault {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-clear-vault:hover {
    background: #fca5a5;
    color: #991b1b;
}

/* مشغل الفيديو المدمج */
.offline-active-player-wrapper {
    margin-bottom: 24px;
}

.player-card {
    background: #090d16;
    border-radius: 20px;
    padding: 18px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.25);
    border: 1px solid #1e293b;
}

.player-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.player-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.offline-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(37, 99, 235, 0.2);
    color: #60a5fa;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 800;
    width: fit-content;
}

.current-title {
    color: #f8fafc;
    font-size: 1.2rem;
    font-weight: 800;
    margin: 4px 0 0;
}

.current-subject {
    color: #94a3b8;
    font-size: 0.82rem;
}

.btn-close-player {
    background: rgba(255,255,255,0.1);
    color: #f8fafc;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-close-player:hover {
    background: rgba(255,255,255,0.25);
}

.video-container {
    width: 100%;
    aspect-ratio: 16 / 9;
    max-height: 520px;
    background: #000;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 12px;
}

.player-controls-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    padding-top: 6px;
}

.speed-selector {
    display: flex;
    align-items: center;
    gap: 6px;
}

.speed-label {
    font-size: 0.78rem;
    color: #94a3b8;
    font-weight: 700;
}

.speed-pill {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    color: #e2e8f0;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s;
}

.speed-pill.active {
    background: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
    font-weight: 800;
}

.btn-fullscreen-toggle {
    background: rgba(255,255,255,0.1);
    border: none;
    color: #ffffff;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* شريط البحث */
.search-filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.search-input-wrap {
    flex: 1;
    min-width: 260px;
    position: relative;
}

.search-input-wrap .search-icon {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.search-input-wrap input {
    width: 100%;
    padding: 12px 42px 12px 16px;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    background: #ffffff;
    font-size: 0.9rem;
    outline: none;
    transition: border 0.2s;
}

.search-input-wrap input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.filter-count {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
}

/* شبكة الفيديوهات */
.offline-videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 18px;
}

.offline-video-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 14px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    transition: transform 0.2s, box-shadow 0.2s;
}

.offline-video-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
}

.card-top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.card-subject-pill {
    background: #eff6ff;
    color: #1e40af;
    font-size: 0.75rem;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.card-size-badge {
    background: #f1f5f9;
    color: #475569;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 6px;
}

.card-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    line-height: 1.5;
}

.card-saved-time {
    font-size: 0.74rem;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 5px;
}

.card-actions-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-top: 10px;
    border-top: 1px solid #f1f5f9;
}

.btn-play-offline {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #2563eb;
    color: #ffffff;
    border: none;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 0.88rem;
    font-weight: 800;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-play-offline:hover {
    background: #1d4ed8;
}

.btn-delete-offline {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    border: 1px solid #fee2e2;
    background: #fff5f5;
    color: #ef4444;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-delete-offline:hover {
    background: #fee2e2;
    color: #b91c1c;
}

/* Empty State */
.offline-empty-state {
    text-align: center;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 20px;
    padding: 60px 24px;
    margin-top: 20px;
}

.empty-icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    margin: 0 auto 20px;
}

.offline-empty-state h3 {
    font-size: 1.3rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 10px;
}

.offline-empty-state p {
    font-size: 0.95rem;
    color: #64748b;
    max-width: 520px;
    margin: 0 auto 24px;
    line-height: 1.6;
}

.btn-browse-courses {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #2563eb;
    color: #ffffff;
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 800;
    font-size: 0.92rem;
    transition: background 0.2s;
}

.btn-browse-courses:hover {
    background: #1d4ed8;
}

.offline-loading-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: #64748b;
}

.spinner-pulse {
    width: 44px;
    height: 44px;
    border: 4px solid #e2e8f0;
    border-top-color: #2563eb;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 16px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .offline-page-header {
        flex-direction: column;
        padding: 20px;
    }
    .storage-stats-card {
        width: 100%;
        justify-content: space-between;
    }
    .offline-videos-grid {
        grid-template-columns: 1fr;
    }
    .page-title {
        font-size: 1.35rem;
    }
}
</style>

<script>
let cachedOfflineVideos = [];
let activeVideoObjectURL = null;

document.addEventListener('DOMContentLoaded', function() {
    updateNetworkIndicator();
    window.addEventListener('online', updateNetworkIndicator);
    window.addEventListener('offline', updateNetworkIndicator);

    // تحميل الفيديوهات من IndexedDB
    loadOfflineVideos();
});

function updateNetworkIndicator() {
    const badge = document.querySelector('.header-badge');
    const label = document.getElementById('networkStatusLabel');
    if (!badge || !label) return;

    if (navigator.onLine) {
        badge.className = 'header-badge online';
        label.innerText = 'متصل بالإنترنت 🟢';
    } else {
        badge.className = 'header-badge offline';
        label.innerText = 'وضع عدم الاتصال نشط (أوفلاين) ⚡';
    }
}

function loadOfflineVideos(retryCount = 0) {
    const db = window.StepvoroOfflineDB || (typeof StepvoroOfflineDB !== 'undefined' ? StepvoroOfflineDB : null);
    if (!db) {
        if (retryCount < 2) {
            setTimeout(() => loadOfflineVideos(retryCount + 1), 80);
            return;
        }
        readOfflineGridDirectly();
        return;
    }

    let isFinished = false;
    const safetyTimer = setTimeout(() => {
        if (!isFinished) {
            isFinished = true;
            readOfflineGridDirectly();
        }
    }, 250);

    db.getAllVideos().then(function(videos) {
        if (isFinished) return;
        isFinished = true;
        clearTimeout(safetyTimer);
        cachedOfflineVideos = videos || [];
        renderOfflineVideosList(cachedOfflineVideos);
        updateStorageSummary();
    }).catch(function(err) {
        if (isFinished) return;
        isFinished = true;
        clearTimeout(safetyTimer);
        console.error('Error fetching offline videos:', err);
        readOfflineGridDirectly();
    });
}

function readOfflineGridDirectly() {
    if (!('indexedDB' in window)) {
        renderOfflineVideosList([]);
        return;
    }
    let directFinished = false;
    const timeoutId = setTimeout(() => {
        if (!directFinished) {
            directFinished = true;
            renderOfflineVideosList([]);
        }
    }, 200);

    try {
        const req = indexedDB.open('StepvoroOfflineStore', 2);
        req.onblocked = function() {
            if (directFinished) return;
            directFinished = true;
            clearTimeout(timeoutId);
            renderOfflineVideosList([]);
        };
        req.onsuccess = function(e) {
            if (directFinished) return;
            directFinished = true;
            clearTimeout(timeoutId);
            const db = e.target.result;
            if (!db.objectStoreNames.contains('offline_videos')) {
                renderOfflineVideosList([]);
                return;
            }
            try {
                const tx = db.transaction(['offline_videos'], 'readonly');
                const store = tx.objectStore('offline_videos');
                const getReq = store.getAll();
                getReq.onsuccess = function() {
                    cachedOfflineVideos = getReq.result || [];
                    renderOfflineVideosList(cachedOfflineVideos);
                    updateStorageSummary();
                };
                getReq.onerror = function() {
                    renderOfflineVideosList([]);
                };
            } catch(txErr) {
                renderOfflineVideosList([]);
            }
        };
        req.onerror = function() {
            if (directFinished) return;
            directFinished = true;
            clearTimeout(timeoutId);
            renderOfflineVideosList([]);
        };
    } catch(e) {
        if (directFinished) return;
        directFinished = true;
        clearTimeout(timeoutId);
        renderOfflineVideosList([]);
    }
}

function renderOfflineVideosList(videos) {
    const grid = document.getElementById('offlineVideosGrid');
    const emptyState = document.getElementById('offlineEmptyState');
    const searchBar = document.getElementById('searchFilterBar');
    const btnClearAll = document.getElementById('btnClearAll');

    if (!grid) return;

    if (!videos || videos.length === 0) {
        grid.style.display = 'none';
        grid.innerHTML = '';
        if (searchBar) searchBar.style.display = 'none';
        if (emptyState) emptyState.style.display = 'block';
        if (btnClearAll) btnClearAll.style.display = 'none';
        return;
    }

    grid.style.display = 'grid';
    if (emptyState) emptyState.style.display = 'none';
    if (searchBar) searchBar.style.display = 'flex';
    if (btnClearAll) btnClearAll.style.display = 'inline-flex';

    let html = '';
    videos.forEach(function(v) {
        const hasBlob = !!v.hasBlob || !!v.blob;
        const hasPdf = !!v.hasPdf || !!v.pdfBlob;
        html += `
        <article class="offline-video-card" id="offline_card_${v.id}">
            <div>
                <div class="card-top-row">
                    <span class="card-subject-pill">
                        <i class="fa-solid fa-book-bookmark"></i>
                        <span>${escapeHtml(v.subject || 'المنهاج الوزاري')}</span>
                    </span>
                    <span class="card-size-badge" style="${v.isExternalVideo ? 'background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;' : ''}">${v.sizeFormatted || 'فيديو أوفلاين'}</span>
                </div>
                <h3 class="card-title" style="margin-top: 10px;">${escapeHtml(v.title || 'درس تعليمي')}</h3>
                <div class="card-saved-time" style="margin-top: 8px;">
                    <i class="fa-regular fa-clock"></i>
                    <span>حُفظ بتاريخ: ${v.savedAt || 'أوفلاين'}</span>
                </div>
            </div>

            <div class="card-actions-row">
                <button type="button" class="btn-play-offline" onclick="playOfflineVideo('${v.id}')">
                    <i class="fa-solid ${hasBlob ? 'fa-play' : 'fa-book-open-reader'}"></i>
                    <span>${hasBlob ? 'تشغيل أوفلاين' : 'فتح الدرس'}</span>
                </button>
                ${hasPdf ? `
                <button type="button" class="btn-play-offline" style="background: #dc2626;" onclick="openOfflinePdf('${v.id}')" title="فتح ملزمة الدرس المحفوظة">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>الملزمة</span>
                </button>
                ` : ''}
                <button type="button" class="btn-delete-offline" onclick="confirmDeleteOfflineVideo('${v.id}')" title="حذف من الذاكرة">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </div>
        </article>
        `;
    });

    grid.innerHTML = html;
}

function updateStorageSummary() {
    if (!window.StepvoroOfflineDB) return;
    StepvoroOfflineDB.calculateTotalSize().then(function(res) {
        const countSpan = document.getElementById('offlineLessonsCount');
        const sizeSpan = document.getElementById('offlineStorageSize');
        if (countSpan) countSpan.innerText = res.count;
        if (sizeSpan) sizeSpan.innerText = res.mb + ' MB';
    }).catch(() => {});
}

function playOfflineVideo(id) {
    if (!window.StepvoroOfflineDB) return;
    StepvoroOfflineDB.getVideo(id).then(function(record) {
        if (!record) {
            if (typeof showPwaToast === 'function') {
                showPwaToast('تعذر العثور على الدرس في ذاكرة التطبيق.', 'error');
            }
            return;
        }

        const section = document.getElementById('offlinePlayerSection');
        const videoEl = document.getElementById('offlineActiveVideo');
        const iframeEl = document.getElementById('offlineActiveIframe');
        const fallbackEl = document.getElementById('offlineFallbackContainer');
        const titleEl = document.getElementById('currentPlayingTitle');
        const subjectEl = document.getElementById('currentPlayingSubject');
        const btnOpenPdf = document.getElementById('btnActiveOpenPdf');

        if (titleEl) titleEl.innerText = record.title || 'درس تعليمي';
        if (subjectEl) subjectEl.innerText = record.subject || 'المنهاج';

        if (record.blob) {
            if (iframeEl) { iframeEl.style.display = 'none'; iframeEl.src = 'about:blank'; }
            if (fallbackEl) fallbackEl.style.display = 'none';
            if (videoEl) {
                videoEl.style.display = 'block';
                if (activeVideoObjectURL) {
                    URL.revokeObjectURL(activeVideoObjectURL);
                }
                activeVideoObjectURL = URL.createObjectURL(record.blob);
                videoEl.src = activeVideoObjectURL;
                videoEl.play().catch(() => {});
            }
            if (section) {
                section.style.display = 'block';
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } else if (record.ytEmbed && navigator.onLine) {
            if (videoEl) { videoEl.style.display = 'none'; videoEl.pause(); }
            if (fallbackEl) fallbackEl.style.display = 'none';
            if (iframeEl) {
                iframeEl.style.display = 'block';
                iframeEl.src = record.ytEmbed + (record.ytEmbed.includes('?') ? '&autoplay=1' : '?autoplay=1');
            }
            if (section) {
                section.style.display = 'block';
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } else if (record.pdfBlob || record.pdfUrl) {
            if (videoEl) { videoEl.style.display = 'none'; videoEl.pause(); }
            if (iframeEl) { iframeEl.style.display = 'none'; iframeEl.src = 'about:blank'; }
            if (fallbackEl) {
                fallbackEl.style.display = 'flex';
                if (btnOpenPdf) {
                    btnOpenPdf.onclick = function() {
                        if (record.pdfBlob) {
                            window.open(URL.createObjectURL(record.pdfBlob), '_blank');
                        } else if (record.pdfUrl) {
                            window.open(record.pdfUrl, '_blank');
                        }
                    };
                }
            }
            if (section) {
                section.style.display = 'block';
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } else {
            if (videoEl) { videoEl.style.display = 'none'; videoEl.pause(); }
            if (iframeEl) { iframeEl.style.display = 'none'; }
            if (fallbackEl) {
                fallbackEl.style.display = 'flex';
                const h4 = fallbackEl.querySelector('h4');
                if (h4) h4.textContent = 'يتطلب بث الفيديو الاتصال بالإنترنت';
                if (btnOpenPdf) btnOpenPdf.style.display = 'none';
            }
            if (section) {
                section.style.display = 'block';
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }).catch(function(err) {
        console.error('Error playing offline video:', err);
    });
}

function openOfflinePdf(id) {
    if (!window.StepvoroOfflineDB) return;
    StepvoroOfflineDB.getVideo(id).then(function(record) {
        if (record && record.pdfBlob) {
            const blobUrl = URL.createObjectURL(record.pdfBlob);
            window.open(blobUrl, '_blank');
        } else if (record && record.pdfUrl) {
            window.open(record.pdfUrl, '_blank');
        } else {
            if (typeof showPwaToast === 'function') {
                showPwaToast('لا توجد ملزمة PDF مرفقة لهذا الدرس.', 'info');
            }
        }
    });
}

function closeActiveOfflinePlayer() {
    const section = document.getElementById('offlinePlayerSection');
    const videoEl = document.getElementById('offlineActiveVideo');
    const iframeEl = document.getElementById('offlineActiveIframe');
    if (videoEl) {
        videoEl.pause();
        videoEl.src = '';
    }
    if (iframeEl) {
        iframeEl.src = 'about:blank';
    }
    if (activeVideoObjectURL) {
        URL.revokeObjectURL(activeVideoObjectURL);
        activeVideoObjectURL = null;
    }
    if (section) section.style.display = 'none';
}

function setOfflinePlayerSpeed(speed, btn) {
    const videoEl = document.getElementById('offlineActiveVideo');
    if (videoEl) videoEl.playbackRate = speed;
    document.querySelectorAll('.speed-pill').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
}

function toggleOfflinePlayerFullscreen() {
    const videoEl = document.getElementById('offlineActiveVideo');
    if (!videoEl) return;
    if (videoEl.requestFullscreen) {
        videoEl.requestFullscreen();
    } else if (videoEl.webkitRequestFullscreen) {
        videoEl.webkitRequestFullscreen();
    }
}

function confirmDeleteOfflineVideo(id) {
    Swal.fire({
        title: 'حذف الدرس من الذاكرة؟',
        text: 'سيتم مسح هذا الدرس من التخزين المحلي لتحرير مساحة جهازك.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#dc2626'
    }).then(function(result) {
        if (result.isConfirmed) {
            StepvoroOfflineDB.deleteVideo(id).then(function() {
                const card = document.getElementById('offline_card_' + id);
                if (card) card.remove();
                cachedOfflineVideos = cachedOfflineVideos.filter(v => String(v.id) !== String(id));
                updateStorageSummary();
                if (cachedOfflineVideos.length === 0) {
                    renderOfflineVideosList([]);
                }
                Swal.fire('تم الحذف', 'تم تحرير مساحة الدرس من ذاكرة التطبيق.', 'success');
            });
        }
    });
}

function confirmClearAllOfflineVideos() {
    Swal.fire({
        title: 'تحرير كامل الذاكرة الأوفلاين؟',
        text: 'سيتم مسح كافة الدروس المحفوظة بدون إنترنت وإخلاء الذاكرة بالكامل.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، امسح الكل',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#dc2626'
    }).then(function(result) {
        if (result.isConfirmed) {
            Promise.all(cachedOfflineVideos.map(v => StepvoroOfflineDB.deleteVideo(v.id)))
                .then(function() {
                    cachedOfflineVideos = [];
                    renderOfflineVideosList([]);
                    updateStorageSummary();
                    closeActiveOfflinePlayer();
                    Swal.fire('تم الإخلاء', 'تم إفراغ ذاكرة الفيديوهات الأوفلاين بنجاح.', 'success');
                });
        }
    });
}

function filterOfflineVideos() {
    const query = (document.getElementById('offlineSearchInput')?.value || '').toLowerCase().trim();
    const filtered = cachedOfflineVideos.filter(function(v) {
        const title = (v.title || '').toLowerCase();
        const subject = (v.subject || '').toLowerCase();
        return title.includes(query) || subject.includes(query);
    });

    renderOfflineVideosList(filtered);
    const label = document.getElementById('filteredCountLabel');
    if (label) {
        label.innerText = `تم العثور على (${filtered.length}) درس`;
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>'"]/g, 
        tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
    );
}
</script>
@endsection
