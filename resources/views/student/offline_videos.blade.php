@extends('layouts.app')

@section('title', __('مكتبة الفيديوهات المحملة أوفلاين') . ' | ' . __(\App\Models\Setting::get('site_name', 'Step by Step')))

@section('content')
<div class="offline-vault-page-container">
    {{-- رأس الصفحة الكلاسيكي الملكي المعتمد --}}
    <div class="offline-page-header">
        <div class="header-content">
            <div class="header-brand-row">
                <img src="/icons/step-by-step-icon-192.png?v=20261002-v33" alt="Step by Step" class="header-seal-icon" width="46" height="46">
                <div>
                    <div class="header-badge" id="headerNetworkBadge">
                        <span class="pulse-dot"></span>
                        <span id="networkStatusLabel">{{ __('فحص الاتصال...') }}</span>
                    </div>
                    <h1 class="page-title">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0b3b6f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-left: 6px;"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path><path d="M12 12v9"></path><path d="m8 17 4 4 4-4"></path></svg>
                        {{ __('الفيديوهات المحملة داخل المنصة') }}
                    </h1>
                </div>
            </div>
            <p class="page-subtitle">
                {{ __('جميع الدروس والحصص المحفوظة في ذاكرة التطبيق، متاحة للمشاهدة بدون إنترنت في أي وقت ومكان.') }}
            </p>
        </div>

        {{-- إحصائيات الذاكرة والتحكم بتصميم كلاسيكي موحد --}}
        <div class="storage-stats-card">
            <div class="classic-stat-pod">
                <div class="stat-icon-pod">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </div>
                <div class="stat-item">
                    <span class="stat-label">{{ __('الدروس المحفوظة') }}</span>
                    <span class="stat-value" id="offlineLessonsCount">0</span>
                </div>
            </div>
            <div class="stat-divider"></div>
            <div class="classic-stat-pod">
                <div class="stat-icon-pod stat-icon-storage">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2"></rect><rect x="2" y="14" width="20" height="8" rx="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                </div>
                <div class="stat-item">
                    <span class="stat-label">{{ __('المساحة المستهلكة') }}</span>
                    <span class="stat-value" id="offlineStorageSize">0 MB</span>
                </div>
            </div>
            <div class="stat-actions" style="display: flex; gap: 8px;">
                <button type="button" class="btn-clear-vault" onclick="confirmClearAllOfflineVideos()" id="btnClearAll" style="display: none;" title="{{ __('حذف كافة الفيديوهات لتحرير الذاكرة') }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    <span>{{ __('تحرير الذاكرة') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- مشغل الفيديو الأوفلاين المدمج الفاخر --}}
    <div class="offline-active-player-wrapper" id="offlinePlayerSection" style="display: none;">
        <div class="player-card">
            <div class="player-header">
                <div class="player-meta">
                    <span class="offline-chip">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        {{ __('مشاهدة أوفلاين بدون نت') }}
                    </span>
                    <h3 id="currentPlayingTitle" class="current-title">{{ __('عنوان الدرس') }}</h3>
                    <span id="currentPlayingSubject" class="current-subject">{{ __('المادة الدراسية') }}</span>
                </div>
                <button type="button" class="btn-close-player" onclick="closeActiveOfflinePlayer()" title="{{ __('إغلاق المشغل') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="video-container" style="position: relative; aspect-ratio: 16/9; background: #000; border-radius: 14px; overflow: hidden; display: flex; align-items: center; justify-content: center;" oncontextmenu="event.preventDefault(); return false;">
                <video id="offlineActiveVideo" controls playsinline controlsList="nodownload noplaybackrate" oncontextmenu="return false;" style="width: 100%; height: 100%; object-fit: contain; background: #000; border-radius: 14px;"></video>
                <iframe id="offlineActiveIframe" 
                        sandbox="allow-scripts allow-same-origin allow-presentation allow-forms"
                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                        style="display: none; position: absolute; inset: 0; width: 100%; height: 100%; border: none; pointer-events: none !important; z-index: 1;" 
                        allowfullscreen></iframe>
                
                {{-- دروع حماية تمنع الدخول على يوتيوب نهائياً --}}
                <div id="offlineActiveYtShield" style="display: none; position: absolute; inset: 0; z-index: 15; pointer-events: auto;">
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 80px; z-index: 25; cursor: pointer;" onclick="toggleOfflineActiveYt()"></div>
                    <div style="position: absolute; bottom: 0; right: 0; width: 150px; height: 70px; z-index: 25; cursor: pointer;" onclick="toggleOfflineActiveYt()"></div>
                    <div style="position: absolute; bottom: 0; left: 0; width: 150px; height: 70px; z-index: 25; cursor: pointer;" onclick="toggleOfflineActiveYt()"></div>
                    <div style="position: absolute; inset: 0; z-index: 20; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="toggleOfflineActiveYt()">
                        <div id="offlineActiveYtCenterPlay" style="width: 58px; height: 58px; border-radius: 50%; background: rgba(15, 23, 42, 0.85); border: 2px solid rgba(255,255,255,0.85); backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.5); pointer-events: none;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="margin-left: 2px;"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        </div>
                    </div>
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; z-index: 30; background: linear-gradient(to top, rgba(15,23,42,0.95), transparent); padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <button type="button" onclick="toggleOfflineActiveYt()" id="btnOfflineYtPlay" style="background: none; border: none; color: #fff; font-size: 1.1rem; cursor: pointer; padding: 4px;" title="تشغيل / إيقاف مؤقت">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        </button>
                        <button type="button" onclick="seekOfflineActiveYt(-10)" style="background: none; border: none; color: #cbd5e1; font-size: 0.9rem; cursor: pointer; padding: 4px;" title="تأخير 10 ثوانٍ">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                        </button>
                        <button type="button" onclick="seekOfflineActiveYt(10)" style="background: none; border: none; color: #cbd5e1; font-size: 0.9rem; cursor: pointer; padding: 4px;" title="تقديم 10 ثوانٍ">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                        </button>
                        <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">مشغل المنصة المحمي</span>
                        <button type="button" onclick="toggleOfflinePlayerFullscreen()" style="background: none; border: none; color: #cbd5e1; font-size: 0.95rem; cursor: pointer; padding: 4px;" title="ملء الشاشة">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg>
                        </button>
                    </div>
                </div>

                <div id="offlineFallbackContainer" style="display: none; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center; padding: 24px 16px; background: #0f172a; color: #fff; text-align: center;">
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.8" style="margin-bottom: 12px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <h4 id="offlineFallbackTitle" style="font-size: 1.05rem; margin-bottom: 6px; font-weight: 800;">ملزمة الدرس متاحة للمطالعة</h4>
                    <p id="offlineFallbackDesc" style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 16px; max-width: 440px; line-height: 1.5;">يمكنك قراءة ملزمة وأوراق عمل هذا الدرس بدون إنترنت.</p>
                    <button type="button" id="btnActiveOpenPdf" class="btn-play-offline" style="background: #0b3b6f; color: #fff; padding: 8px 20px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        <span>فتح ملزمة الدرس ⚡</span>
                    </button>
                </div>
            </div>
            <div class="player-controls-bar">
                <div class="speed-selector">
                    <span class="speed-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                        {{ __('السرعة:') }}
                    </span>
                    <button type="button" class="speed-pill" onclick="setOfflinePlayerSpeed(0.75, this)">0.75x</button>
                    <button type="button" class="speed-pill active" onclick="setOfflinePlayerSpeed(1, this)">1x</button>
                    <button type="button" class="speed-pill" onclick="setOfflinePlayerSpeed(1.25, this)">1.25x</button>
                    <button type="button" class="speed-pill" onclick="setOfflinePlayerSpeed(1.5, this)">1.5x</button>
                    <button type="button" class="speed-pill" onclick="setOfflinePlayerSpeed(2, this)">2x</button>
                </div>
                <button type="button" class="btn-fullscreen-toggle" onclick="toggleOfflinePlayerFullscreen()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg>
                    <span>{{ __('ملء الشاشة') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- شريط البحث والتصفية --}}
    <div class="search-filter-bar" id="searchFilterBar" style="display: none;">
        <div class="search-input-wrap">
            <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="offlineSearchInput" placeholder="{{ __('ابحث عن درس أو مادة في قائمة المحفوظات...') }}" oninput="filterOfflineVideos()">
        </div>
        <div class="filter-count">
            <span id="filteredCountLabel">{{ __('عرض جميع الدروس') }}</span>
        </div>
    </div>

    {{-- شبكة بطاقات الفيديوهات المحملة --}}
    <div class="offline-videos-grid" id="offlineVideosGrid">
        <div class="offline-loading-state" id="offlineInitialLoader">
            <div class="spinner-pulse"></div>
            <p>{{ __('جاري فحص ذاكرة التطبيق واسترجاع الدروس المحفوظة أوفلاين...') }}</p>
        </div>
    </div>

    {{-- حالة الذاكرة الفارغة (Empty State) الأكاديمية الملكية الفاخرة --}}
    <div class="offline-empty-state" id="offlineEmptyState" style="display: none;">
        <div class="empty-icon-circle">
            <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#0b3b6f" stroke-width="2"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path><path d="M12 12v9"></path><path d="m8 17 4 4 4-4"></path></svg>
        </div>
        <h3>{{ __('لا توجد دروس محملة أوفلاين حتى الآن') }}</h3>
        <p>
            {{ __('عند تصفح أي مادة دراسية، اضغط على زر "تحميل أوفلاين" بجانب أي درس تريده، وسيتم حفظه فوراً في هذه الشاشة لتتمكن من فتحه ودراسته بدون أي اتصال بالإنترنت.') }}
        </p>
        <div class="empty-actions">
            <a href="{{ route('subjects.index') }}" class="btn-browse-courses">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 1 3-3h7z"></path></svg>
                <span>{{ __('تصفح المواد الدراسية الآن') }}</span>
            </a>
        </div>

        {{-- أدوات أوفلاين فورية متاحة بدون إنترنت --}}
        <div class="offline-smart-tools-section">
            <h4 class="smart-tools-heading">{{ __('أدوات وخدمات متاحة دائماً بدون إنترنت ⚡') }}</h4>
            <div class="smart-tools-grid">
                <a href="{{ route('tawjihi.calculator') }}" class="smart-tool-box">
                    <div class="smart-tool-icon" style="background: #eff6ff; color: #0b3b6f;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="16" y1="14" x2="16" y2="18"></line><path d="M16 10h.01"></path><path d="M12 10h.01"></path><path d="M8 10h.01"></path><path d="M12 14h.01"></path><path d="M8 14h.01"></path><path d="M12 18h.01"></path><path d="M8 18h.01"></path></svg>
                    </div>
                    <div>
                        <strong>{{ __('حاسبة معدل التوجيهي') }}</strong>
                        <span>{{ __('احتساب دقيق وفق ضوابط وزارة التربية والتعليم') }}</span>
                    </div>
                </a>
                <a href="{{ route('courses.catalog') }}" class="smart-tool-box">
                    <div class="smart-tool-icon" style="background: #fef3c7; color: #d97706;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    </div>
                    <div>
                        <strong>{{ __('دليل المقررات والكتب') }}</strong>
                        <span>{{ __('تصفح الفهرس والمناهج الوزارية المعتمدة') }}</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* ==========================================================================
   تنسيقات شاشة الفيديوهات المحملة أوفلاين الملكية الكلاسيكية الفاخرة
   (Royal Academic Classic Offline Vault Design System)
   ========================================================================== */
.offline-vault-page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px 16px 80px;
    font-family: 'Tajawal', 'Alexandria', sans-serif;
    color: #0f172a;
}

.offline-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-top: 3px solid #d97706;
    border-radius: 18px;
    padding: 24px 28px;
    margin-bottom: 24px;
    box-shadow: 0 4px 16px rgba(11, 59, 111, 0.05);
    flex-wrap: wrap;
}

.header-brand-row {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 8px;
}

.header-seal-icon {
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid rgba(217, 119, 6, 0.35);
    padding: 2px;
    box-shadow: 0 3px 10px rgba(11, 59, 111, 0.12);
    object-fit: contain;
    flex-shrink: 0;
}

.header-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 6px;
    background: #f1f5f9;
    color: #475569;
    transition: all 0.3s ease;
}

.header-badge.online {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

.header-badge.offline {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}

.pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
    display: inline-block;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.08);
}

.page-title {
    font-size: 1.55rem;
    font-weight: 800;
    color: #0b3b6f;
    margin: 0;
    letter-spacing: -0.01em;
    display: flex;
    align-items: center;
}

.page-subtitle {
    font-size: 0.9rem;
    color: #64748b;
    margin: 4px 0 0;
    line-height: 1.6;
    max-width: 580px;
}

.storage-stats-card {
    display: flex;
    align-items: center;
    gap: 18px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px 18px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.classic-stat-pod {
    display: flex;
    align-items: center;
    gap: 12px;
}

.stat-icon-pod {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #eff6ff;
    color: #0b3b6f;
    border: 1px solid #bfdbfe;
    display: grid;
    place-items: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.stat-icon-pod.stat-icon-storage {
    background: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
}

.stat-item {
    display: flex;
    flex-direction: column;
}

.stat-label {
    font-size: 0.74rem;
    color: #64748b;
    font-weight: 700;
}

.stat-value {
    font-size: 1.3rem;
    font-weight: 800;
    color: #0b3b6f;
}

.stat-divider {
    width: 1px;
    height: 36px;
    background: #cbd5e1;
}

.btn-clear-vault {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 0.78rem;
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
    background: #061329;
    border-radius: 20px;
    padding: 18px;
    box-shadow: 0 12px 36px rgba(11, 59, 111, 0.25);
    border: 1px solid #1e293b;
    border-top: 3px solid #d97706;
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
    background: rgba(217, 119, 6, 0.2);
    color: #fde68a;
    border: 1px solid rgba(217, 119, 6, 0.4);
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
    display: flex;
    align-items: center;
    gap: 4px;
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
    background: #0b3b6f;
    color: #ffffff;
    border-color: #d97706;
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
    pointer-events: none;
}

.search-input-wrap input {
    width: 100%;
    padding: 12px 42px 12px 16px;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    background: #ffffff;
    font-size: 0.9rem;
    outline: none;
    transition: border 0.2s, box-shadow 0.2s;
}

.search-input-wrap input:focus {
    border-color: #0b3b6f;
    box-shadow: 0 0 0 3px rgba(11, 59, 111, 0.12);
}

.filter-count {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
}

/* شبكة بطاقات الدروس المحملة */
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
    box-shadow: 0 2px 8px rgba(11, 59, 111, 0.04);
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    position: relative;
}

.offline-video-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(11, 59, 111, 0.08);
    border-color: #cbd5e1;
}

.card-top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.card-subject-pill {
    background: #eff6ff;
    color: #0b3b6f;
    border: 1px solid #bfdbfe;
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
    border: 1px solid #e2e8f0;
}

.card-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin: 10px 0 0;
    line-height: 1.5;
}

.card-saved-time {
    font-size: 0.74rem;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 8px;
}

.card-actions-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}

.btn-play-offline {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #0b3b6f 0%, #1e40af 100%);
    color: #ffffff;
    border: none;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 0.88rem;
    font-weight: 800;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
    box-shadow: 0 2px 8px rgba(11, 59, 111, 0.2);
}

.btn-play-offline:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(11, 59, 111, 0.3);
}

.btn-play-offline.btn-pdf-offline {
    flex: 0 0 auto;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2);
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
    flex-shrink: 0;
}

.btn-delete-offline:hover {
    background: #fee2e2;
    color: #b91c1c;
}

/* حالة الذاكرة الفارغة (Empty State) */
.offline-empty-state {
    text-align: center;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 20px;
    padding: 48px 24px;
    margin-top: 20px;
    box-shadow: 0 2px 8px rgba(11, 59, 111, 0.03);
}

.empty-icon-circle {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    background: linear-gradient(135deg, #eff6ff 0%, #fef3c7 100%);
    border: 2px solid rgba(217, 119, 6, 0.3);
    color: #0b3b6f;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 4px 12px rgba(11, 59, 111, 0.08);
}

.offline-empty-state h3 {
    font-size: 1.3rem;
    font-weight: 800;
    color: #0b3b6f;
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
    background: linear-gradient(135deg, #0b3b6f 0%, #1e40af 100%);
    color: #ffffff;
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 800;
    font-size: 0.92rem;
    box-shadow: 0 4px 12px rgba(11, 59, 111, 0.2);
    transition: all 0.2s;
}

.btn-browse-courses:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(11, 59, 111, 0.3);
    color: #ffffff;
}

/* الأدوات الذكية المتاحة أوفلاين داخل شاشة الفيديوهات */
.offline-smart-tools-section {
    margin-top: 36px;
    padding-top: 28px;
    border-top: 1px solid #f1f5f9;
}

.smart-tools-heading {
    font-size: 0.95rem;
    font-weight: 800;
    color: #0b3b6f;
    margin-bottom: 16px;
}

.smart-tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 14px;
    max-width: 720px;
    margin: 0 auto;
}

.smart-tool-box {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 16px;
    text-decoration: none;
    color: inherit;
    text-align: right;
    transition: all 0.2s;
}

.smart-tool-box:hover {
    background: #ffffff;
    border-color: #d97706;
    box-shadow: 0 4px 12px rgba(11, 59, 111, 0.06);
    transform: translateY(-2px);
}

.smart-tool-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.smart-tool-box strong {
    display: block;
    font-size: 0.88rem;
    font-weight: 800;
    color: #0f172a;
}

.smart-tool-box span {
    display: block;
    font-size: 0.76rem;
    color: #64748b;
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
    border-top-color: #0b3b6f;
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
        align-items: flex-start;
        padding: 18px;
    }
    .storage-stats-card {
        width: 100%;
        justify-content: space-between;
    }
    .offline-videos-grid {
        grid-template-columns: 1fr;
    }
    .page-title {
        font-size: 1.3rem;
    }
}
</style>

<script>
let cachedOfflineVideos = [];
let activeVideoObjectURL = null;

function safeEscapeString(str) {
    return String(str || '').replace(/[&<>"']/g, function(m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
}

document.addEventListener('DOMContentLoaded', function() {
    updateNetworkIndicator();
    window.addEventListener('online', updateNetworkIndicator);
    window.addEventListener('offline', updateNetworkIndicator);

    // بدء فحص وجلب الفيديوهات المحفوظة في IndexedDB
    loadOfflineVideos();
});

function updateNetworkIndicator() {
    const badge = document.getElementById('headerNetworkBadge');
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
    const db = window.StepvoroOfflineDB;
    if (db && typeof db.getAllVideos === 'function') {
        db.getAllVideos().then(function(videos) {
            cachedOfflineVideos = videos || [];
            renderDedicatedOfflineVideosGrid(cachedOfflineVideos);
            updateStorageSummary();
        }).catch(function(err) {
            console.warn('Error fetching via db, trying direct IndexedDB:', err);
            readOfflineGridDirectly();
        });
        return;
    }

    if (retryCount < 10) {
        setTimeout(() => loadOfflineVideos(retryCount + 1), 100);
        return;
    }

    readOfflineGridDirectly();
}

function readOfflineGridDirectly() {
    if (!('indexedDB' in window)) {
        renderDedicatedOfflineVideosGrid([]);
        return;
    }

    try {
        const req = indexedDB.open('StepvoroOfflineStore', 2);
        req.onsuccess = function(e) {
            const db = e.target.result;
            if (!db.objectStoreNames.contains('offline_videos')) {
                renderDedicatedOfflineVideosGrid([]);
                return;
            }
            try {
                const tx = db.transaction(['offline_videos'], 'readonly');
                const store = tx.objectStore('offline_videos');
                const getReq = store.getAll();
                getReq.onsuccess = function() {
                    cachedOfflineVideos = getReq.result || [];
                    renderDedicatedOfflineVideosGrid(cachedOfflineVideos);
                    updateStorageSummary();
                };
                getReq.onerror = function() {
                    renderDedicatedOfflineVideosGrid([]);
                };
            } catch(txErr) {
                console.warn('Direct tx error:', txErr);
                renderDedicatedOfflineVideosGrid([]);
            }
        };
        req.onerror = function() {
            renderDedicatedOfflineVideosGrid([]);
        };
        req.onblocked = function() {
            renderDedicatedOfflineVideosGrid([]);
        };
    } catch(e) {
        console.warn('Direct open error:', e);
        renderDedicatedOfflineVideosGrid([]);
    }
}

function renderDedicatedOfflineVideosGrid(videos) {
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
        if (!v) return;
        const id = String(v.id || '').replace(/'/g, "\\'");
        const title = safeEscapeString(v.title || 'درس تعليمي');
        const subject = safeEscapeString(v.subject || 'المنهاج الوزاري');
        const savedAt = safeEscapeString(v.savedAt || 'أوفلاين');
        const hasBlob = !!v.hasBlob || !!v.blob;
        const hasPdf = !!v.hasPdf || !!v.pdfBlob;
        const sizeFormatted = safeEscapeString(v.sizeFormatted || (hasBlob ? 'فيديو أوفلاين' : 'ملزمة'));

        html += `
        <article class="offline-video-card" id="offline_card_${id}">
            <div>
                <div class="card-top-row">
                    <span class="card-subject-pill">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        <span>${subject}</span>
                    </span>
                    <span class="card-size-badge">${sizeFormatted}</span>
                </div>
                <h3 class="card-title">${title}</h3>
                <div class="card-saved-time">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>حُفظ بتاريخ: ${savedAt}</span>
                </div>
            </div>

            <div class="card-actions-row">
                <button type="button" class="btn-play-offline" onclick="playOfflineVideo('${id}')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    <span>${hasBlob ? 'تشغيل أوفلاين' : 'فتح الدرس'}</span>
                </button>
                ${hasPdf ? `
                <button type="button" class="btn-play-offline btn-pdf-offline" onclick="openOfflinePdf('${id}')" title="فتح ملزمة الدرس المحفوظة">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    <span>الملزمة</span>
                </button>
                ` : ''}
                <button type="button" class="btn-delete-offline" onclick="confirmDeleteOfflineVideo('${id}')" title="حذف من الذاكرة">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
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
        const fbTitle = document.getElementById('offlineFallbackTitle');
        const fbDesc = document.getElementById('offlineFallbackDesc');

        if (titleEl) titleEl.innerText = record.title || 'درس تعليمي';
        if (subjectEl) subjectEl.innerText = record.subject || 'المنهاج';

        const ytShield = document.getElementById('offlineActiveYtShield');

        if (record.blob) {
            if (ytShield) ytShield.style.display = 'none';
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
        } else if (record.pdfBlob || record.pdfUrl) {
            if (ytShield) ytShield.style.display = 'none';
            if (videoEl) { videoEl.style.display = 'none'; videoEl.pause(); }
            if (iframeEl) { iframeEl.style.display = 'none'; iframeEl.src = 'about:blank'; }
            if (fallbackEl) {
                fallbackEl.style.display = 'flex';
                if (fbTitle) fbTitle.textContent = 'ملزمة وأوراق عمل الدرس جاهزة أوفلاين ⚡';
                if (fbDesc) fbDesc.textContent = 'يمكنك دراسة ملزمة وأوراق عمل هذا الدرس المحفوظة في ذاكرة هاتفك بدون أي اتصال بالإنترنت.';
                if (btnOpenPdf) {
                    btnOpenPdf.style.display = 'inline-flex';
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
            if (ytShield) ytShield.style.display = 'none';
            if (videoEl) { videoEl.style.display = 'none'; videoEl.pause(); }
            if (iframeEl) { iframeEl.style.display = 'none'; iframeEl.src = 'about:blank'; }
            if (fallbackEl) {
                fallbackEl.style.display = 'flex';
                if (fbTitle) fbTitle.textContent = 'بث YouTube مباشر - يتطلب إنترنت 🌐';
                if (fbDesc) fbDesc.textContent = 'هذا الشرح مسجل كبث مباشر من YouTube ويتطلب اتصالاً بالإنترنت لتشغيل الفيديو. الفيديوهات المرفوعة بصيغة MP4 هي فقط التي تعمل أوفلاين بدون نت بنسبة 100%.';
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
        if (!record) return;
        if (record.pdfBlob) {
            window.open(URL.createObjectURL(record.pdfBlob), '_blank');
        } else if (record.pdfUrl) {
            window.open(record.pdfUrl, '_blank');
        } else {
            if (typeof showPwaToast === 'function') {
                showPwaToast('لا توجد ملزمة مرفقة لهذا الدرس.', 'info');
            }
        }
    });
}

function closeActiveOfflinePlayer() {
    const section = document.getElementById('offlinePlayerSection');
    const videoEl = document.getElementById('offlineActiveVideo');
    const iframeEl = document.getElementById('offlineActiveIframe');
    const ytShield = document.getElementById('offlineActiveYtShield');
    if (ytShield) ytShield.style.display = 'none';

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

function toggleOfflineActiveYt() {
    const videoEl = document.getElementById('offlineActiveVideo');
    const iframeEl = document.getElementById('offlineActiveIframe');
    const centerPlay = document.getElementById('offlineActiveYtCenterPlay');

    if (videoEl && videoEl.style.display !== 'none') {
        if (videoEl.paused) {
            videoEl.play();
            if (centerPlay) centerPlay.style.display = 'none';
        } else {
            videoEl.pause();
            if (centerPlay) centerPlay.style.display = 'flex';
        }
        return;
    }

    if (iframeEl && iframeEl.contentWindow) {
        try {
            iframeEl.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
        } catch (e) {}
    }
}

function seekOfflineActiveYt(sec) {
    const videoEl = document.getElementById('offlineActiveVideo');
    if (videoEl && !isNaN(videoEl.duration)) {
        videoEl.currentTime = Math.max(0, Math.min(videoEl.duration, videoEl.currentTime + sec));
    }
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
    const doDelete = confirm('هل تريد حذف هذا الدرس من ذاكرة الهاتف لتحرير المساحة؟');
    if (!doDelete) return;

    if (window.StepvoroOfflineDB) {
        StepvoroOfflineDB.deleteVideo(id).then(function() {
            const card = document.getElementById('offline_card_' + id);
            if (card) card.remove();
            cachedOfflineVideos = cachedOfflineVideos.filter(v => String(v.id) !== String(id));
            updateStorageSummary();
            if (cachedOfflineVideos.length === 0) {
                renderDedicatedOfflineVideosGrid([]);
            }
            if (typeof showPwaToast === 'function') {
                showPwaToast('تم حذف الدرس من الذاكرة بنجاح.', 'success');
            }
        }).catch(function(err) {
            console.error("Error deleting offline video:", err);
            if (typeof showPwaToast === 'function') {
                showPwaToast('حدث خطأ أثناء حذف الدرس من الذاكرة.', 'error');
            } else {
                alert('حدث خطأ أثناء حذف الدرس من الذاكرة.');
            }
        });
    }
}

function confirmClearAllOfflineVideos() {
    const doClear = confirm('هل أنت متأكد من رغبتك في مسح كافة الدروس المحفوظة أوفلاين وإخلاء الذاكرة؟');
    if (!doClear) return;

    if (window.StepvoroOfflineDB) {
        Promise.all(cachedOfflineVideos.map(v => StepvoroOfflineDB.deleteVideo(v.id)))
            .then(function() {
                cachedOfflineVideos = [];
                renderDedicatedOfflineVideosGrid([]);
                updateStorageSummary();
                closeActiveOfflinePlayer();
                if (typeof showPwaToast === 'function') {
                    showPwaToast('تم إفراغ ذاكرة الفيديوهات الأوفلاين بالكامل.', 'success');
                }
            })
            .catch(function(err) {
                console.error("Error clearing offline videos:", err);
                if (typeof showPwaToast === 'function') {
                    showPwaToast('حدث خطأ أثناء إفراغ الذاكرة.', 'error');
                } else {
                    alert('حدث خطأ أثناء إفراغ الذاكرة.');
                }
            });
    }
}

function filterOfflineVideos() {
    const query = (document.getElementById('offlineSearchInput')?.value || '').toLowerCase().trim();
    const filtered = cachedOfflineVideos.filter(function(v) {
        const title = String(v.title || '').toLowerCase();
        const subject = String(v.subject || '').toLowerCase();
        return title.includes(query) || subject.includes(query);
    });

    renderDedicatedOfflineVideosGrid(filtered);
    const label = document.getElementById('filteredCountLabel');
    if (label) {
        label.innerText = `تم العثور على (${filtered.length}) درس`;
    }
}
</script>
@endsection
