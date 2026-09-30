{{-- =========================================================================
     Stepvoro PWA Mobile Application Engine & Native Navigation UI
     - Supports Android (One-click Native Install via beforeinstallprompt)
     - Supports iOS Safari (Native Add-to-Home-Screen Step-by-Step Guide)
     - Full offline Service Worker registration (v3)
     - In-App Offline Video Vault (Download & Play Videos without Internet)
     - Modern App Bottom Navigation Bar
     ========================================================================= --}}

<!-- 1. شريط التنقل السفلي للهواتف الذكية (Native Mobile Bottom Navigation Bar) -->
<nav class="stepvoro-bottom-nav" id="stepvoroBottomNav" aria-label="Mobile Navigation">
    <a href="{{ route('home') }}" class="nav-tab {{ request()->is('/') ? 'active' : '' }}">
        <div class="nav-tab-icon"><i class="fa-solid fa-house"></i></div>
        <span class="nav-tab-label">{{ __('الرئيسية') }}</span>
    </a>
    <a href="{{ route('courses.catalog') }}" class="nav-tab {{ request()->is('catalog*') ? 'active' : '' }}">
        <div class="nav-tab-icon"><i class="fa-solid fa-graduation-cap"></i></div>
        <span class="nav-tab-label">{{ __('المساقات') }}</span>
    </a>
    <a href="{{ route('tawjihi.calculator') }}" class="nav-tab {{ request()->is('tawjihi-calculator*') ? 'active' : '' }}">
        <div class="nav-tab-icon pulse-accent"><i class="fa-solid fa-calculator"></i></div>
        <span class="nav-tab-label">{{ __('الحاسبة') }}</span>
    </a>
    <button type="button" class="nav-tab" onclick="openOfflineVault()" id="bottomNavOfflineBtn" title="{{ __('دروسي المحفوظة أوفلاين بدون نت') }}">
        <div class="nav-tab-icon offline-vault-highlight">
            <i class="fa-solid fa-cloud-arrow-down"></i>
            <span class="badge-offline-count" id="bottomNavOfflineBadge" style="display: none;">0</span>
        </div>
        <span class="nav-tab-label">{{ __('أوفلاين ⚡') }}</span>
    </button>
    @if(Auth::guard('student')->check() || Auth::check())
        <a href="{{ route('dashboard') }}" class="nav-tab {{ request()->is('student*') || request()->is('admin*') ? 'active' : '' }}">
            <div class="nav-tab-icon"><i class="fa-solid fa-user-circle"></i></div>
            <span class="nav-tab-label">{{ __('حسابي') }}</span>
        </a>
    @else
        <button type="button" class="nav-tab" onclick="triggerPwaInstall()" id="bottomNavInstallBtn">
            <div class="nav-tab-icon install-highlight"><i class="fa-solid fa-mobile-screen-button"></i></div>
            <span class="nav-tab-label">{{ __('التطبيق') }}</span>
        </button>
    @endif
</nav>

<!-- 2. بطاقة التثبيت السريعة العائمة للهواتف (Smart App Install Floating Banner) -->
<aside class="stepvoro-install-banner" id="stepvoroInstallBanner" style="display: none;">
    <div class="banner-content-wrap">
        <div class="banner-app-icon">
            <img src="/icons/icon.svg" alt="Stepvoro App Icon" width="46" height="46">
            <span class="app-verified-badge"><i class="fa-solid fa-check"></i></span>
        </div>
        <div class="banner-text">
            <h4>{{ __('تطبيق Stepvoro على هاتفك') }}</h4>
            <p>{{ __('تصفح فائق السرعة، استهلاك أقل للإنترنت، ودراسة بدون متصفح.') }}</p>
        </div>
        <div class="banner-actions">
            <button type="button" class="btn-pwa-install" onclick="triggerPwaInstall()">
                <i class="fa-solid fa-download"></i>
                <span>{{ __('تثبيت') }}</span>
            </button>
            <button type="button" class="btn-pwa-dismiss" onclick="dismissPwaBanner()" aria-label="إغلاق">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
</aside>

<!-- 3. نافذة إرشاد التثبيت على هواتف آيفون (iOS Safari Native Install Modal) -->
<div class="stepvoro-ios-modal-overlay" id="stepvoroIosModal" onclick="closeIosModal(event)" style="display: none;">
    <div class="stepvoro-ios-sheet" onclick="event.stopPropagation()">
        <div class="ios-sheet-handle"></div>
        <div class="ios-sheet-header">
            <img src="/icons/icon.svg" alt="Stepvoro Icon" width="54" height="54" class="ios-app-icon">
            <div>
                <h3>{{ __('تثبيت تطبيق Stepvoro على iPhone') }}</h3>
                <p>{{ __('احصل على التطبيق مباشرة على شاشتك الرئيسية في خطوتين') }}</p>
            </div>
            <button type="button" class="btn-close-sheet" onclick="closeIosModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="ios-steps-list">
            <div class="ios-step-item">
                <div class="step-num">1</div>
                <div class="step-text">
                    <span>اضغط على زر المشاركة <strong>(Share)</strong> في شريط متصفح Safari السفلي:</span>
                    <span class="ios-icon-hint"><i class="fa-solid fa-arrow-up-from-bracket"></i> أو مربع السهم لأعلى</span>
                </div>
            </div>
            <div class="ios-step-item">
                <div class="step-num">2</div>
                <div class="step-text">
                    <span>مرر القائمة لأسفل ثم اختر <strong>"إضافة إلى الشاشة الرئيسية" (Add to Home Screen)</strong>:</span>
                    <span class="ios-icon-hint"><i class="fa-regular fa-square-plus"></i> إضافة إلى الشاشة الرئيسية</span>
                </div>
            </div>
            <div class="ios-step-item">
                <div class="step-num">3</div>
                <div class="step-text">
                    <span>اضغط على <strong>"إضافة" (Add)</strong> في الزاوية العلوية ومبارك عليك التطبيق! 🎉</span>
                </div>
            </div>
        </div>

        <button type="button" class="btn-ios-done" onclick="closeIosModal()">
            <i class="fa-solid fa-check"></i>
            <span>{{ __('فهمت ذلك، شكراً لك') }}</span>
        </button>
    </div>
</div>

<!-- 4. نافذة الفيديوهات والدروس المحفوظة أوفلاين (In-App Offline Videos Vault Modal) -->
<div class="stepvoro-ios-modal-overlay" id="stepvoroOfflineVaultModal" onclick="closeOfflineVault(event)" style="display: none;">
    <div class="stepvoro-ios-sheet offline-vault-sheet" onclick="event.stopPropagation()">
        <div class="ios-sheet-handle"></div>
        <div class="ios-sheet-header">
            <div class="offline-vault-icon">
                <i class="fa-solid fa-cloud-arrow-down"></i>
            </div>
            <div style="flex: 1; text-align: right;">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0 0 2px;">{{ __('دروسي المحفوظة أوفلاين') }}</h3>
                <p id="offlineVaultStorageSummary" style="font-size: 0.76rem; color: #64748b; margin: 0;">{{ __('جاري فحص الذاكرة المحلية للتطبيق...') }}</p>
            </div>
            <button type="button" class="btn-close-sheet" onclick="closeOfflineVault()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="offline-vault-body" id="offlineVaultList">
            <div style="text-align: center; padding: 30px 10px; color: #94a3b8;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 1.8rem; color: #2563eb;"></i>
                <p style="margin-top: 10px; font-size: 0.85rem;">{{ __('جاري تحميل الدروس المحفوظة...') }}</p>
            </div>
        </div>

        <div style="margin-top: 16px;">
            <button type="button" class="btn-ios-done" onclick="closeOfflineVault()">
                <span>{{ __('إغلاق النافذة') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- 5. مشغل الفيديو المنبثق للدروس المحفوظة أوفلاين -->
<div class="stepvoro-ios-modal-overlay" id="stepvoroOfflinePlayerModal" onclick="closeOfflinePlayer(event)" style="display: none;">
    <div class="stepvoro-offline-player-card" onclick="event.stopPropagation()">
        <div class="offline-player-header">
            <h4 id="offlinePlayerTitle">{{ __('مشاهدة الدرس أوفلاين') }}</h4>
            <button type="button" class="btn-close-sheet" onclick="closeOfflinePlayer()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="offline-player-media-wrap">
            <video id="offlineVaultVideoPlayer" controls playsinline controlsList="nodownload noplaybackrate" style="width: 100%; height: 100%; object-fit: contain; background: #000; border-radius: 12px;"></video>
        </div>
        <div class="offline-player-footer">
            <span class="badge-offline-playing"><i class="fa-solid fa-bolt"></i> {{ __('مشاهدة بدون إنترنت مباشرة من ذاكرة التطبيق') }}</span>
        </div>
    </div>
</div>

<style>
/* ==========================================================================
   تنسيقات شريط التنقل السفلي وشاشات التطبيق المتطورة
   ========================================================================== */
:root {
    --pwa-primary: #1d4ed8;
    --pwa-primary-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    --pwa-gold: #d97706;
}

/* شريط التنقل السفلي للهواتف */
.stepvoro-bottom-nav {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: calc(62px + env(safe-area-inset-bottom, 0px));
    padding-bottom: env(safe-area-inset-bottom, 0px);
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-top: 1px solid #e2e8f0;
    z-index: 995;
    justify-content: space-around;
    align-items: center;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.06);
}

@media (max-width: 768px) {
    .stepvoro-bottom-nav {
        display: flex;
    }
    body {
        padding-bottom: calc(68px + env(safe-area-inset-bottom, 0px)) !important;
    }
}

.nav-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
    height: 100%;
    color: #64748b;
    text-decoration: none;
    background: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    padding: 4px 0;
}

.nav-tab-icon {
    font-size: 1.15rem;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease;
    position: relative;
}

.nav-tab-label {
    font-size: 0.68rem;
    font-weight: 700;
    margin-top: 2px;
}

.nav-tab.active {
    color: var(--pwa-primary);
}

.nav-tab.active .nav-tab-icon {
    transform: translateY(-2px);
}

.nav-tab:active .nav-tab-icon {
    transform: scale(0.88);
}

.pulse-accent i {
    color: #0284c7;
}

.offline-vault-highlight i {
    color: #10b981;
}

.badge-offline-count {
    position: absolute;
    top: -5px;
    right: -10px;
    background: #10b981;
    color: #ffffff;
    font-size: 0.62rem;
    font-weight: 800;
    min-width: 17px;
    height: 17px;
    padding: 0 4px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #ffffff;
}

.install-highlight {
    color: #d97706;
    animation: bounceIcon 2s infinite ease-in-out;
}

@keyframes bounceIcon {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
}

/* بطاقة التثبيت العائمة الذكية */
.stepvoro-install-banner {
    position: fixed;
    bottom: calc(74px + env(safe-area-inset-bottom, 0px));
    left: 14px;
    right: 14px;
    max-width: 440px;
    margin: 0 auto;
    background: rgba(15, 23, 42, 0.94);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    padding: 12px 14px;
    box-shadow: 0 16px 36px rgba(15, 23, 42, 0.35);
    z-index: 996;
    animation: slideUpBanner 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideUpBanner {
    from { opacity: 0; transform: translateY(24px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.banner-content-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

.banner-app-icon {
    position: relative;
    flex-shrink: 0;
}

.banner-app-icon img {
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    display: block;
}

.app-verified-badge {
    position: absolute;
    bottom: -3px;
    right: -3px;
    width: 16px;
    height: 16px;
    background: #10b981;
    color: #ffffff;
    border-radius: 50%;
    font-size: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #0f172a;
}

.banner-text {
    flex: 1;
    min-width: 0;
    text-align: right;
}

.banner-text h4 {
    font-size: 0.85rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.banner-text p {
    font-size: 0.72rem;
    color: #94a3b8;
    margin: 0;
    line-height: 1.4;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.banner-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

.btn-pwa-install {
    background: var(--pwa-primary-gradient);
    color: #ffffff;
    border: none;
    padding: 7px 14px;
    border-radius: 12px;
    font-weight: 800;
    font-size: 0.78rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    transition: transform 0.2s;
}

.btn-pwa-install:active {
    transform: scale(0.95);
}

.btn-pwa-dismiss {
    background: transparent;
    color: #94a3b8;
    border: none;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.85rem;
}

/* نافذة إرشاد هواتف آيفون وقبو الأوفلاين */
.stepvoro-ios-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.65);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 9999;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    animation: fadeInModal 0.25s ease-out;
}

@keyframes fadeInModal {
    from { opacity: 0; }
    to { opacity: 1; }
}

.stepvoro-ios-sheet {
    background: #ffffff;
    width: 100%;
    max-width: 500px;
    border-radius: 28px 28px 0 0;
    padding: 16px 22px calc(24px + env(safe-area-inset-bottom, 12px));
    text-align: right;
    box-shadow: 0 -10px 40px rgba(0,0,0,0.3);
    animation: slideUpSheet 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
}

@keyframes slideUpSheet {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}

.ios-sheet-handle {
    width: 38px;
    height: 5px;
    background: #cbd5e1;
    border-radius: 10px;
    margin: 0 auto 16px;
    flex-shrink: 0;
}

.ios-sheet-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
    flex-shrink: 0;
}

.ios-app-icon {
    border-radius: 14px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
}

.offline-vault-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: #ecfdf5;
    color: #10b981;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
}

.btn-close-sheet {
    background: #f1f5f9;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.offline-vault-body {
    flex: 1;
    overflow-y: auto;
    padding-right: 4px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.offline-lesson-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    transition: transform 0.2s;
}

.offline-card-info {
    flex: 1;
    min-width: 0;
}

.offline-card-info h4 {
    font-size: 0.88rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.offline-card-meta {
    font-size: 0.74rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.offline-card-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

.btn-vault-play {
    background: #10b981;
    color: #ffffff;
    border: none;
    padding: 7px 12px;
    border-radius: 10px;
    font-size: 0.78rem;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-vault-delete {
    background: #fee2e2;
    color: #ef4444;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.82rem;
}

/* مشغل الفيديو المنبثق */
.stepvoro-offline-player-card {
    background: #0f172a;
    width: 100%;
    max-width: 580px;
    border-radius: 20px;
    padding: 16px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    margin: auto 16px;
}

.offline-player-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    color: #ffffff;
}

.offline-player-header h4 {
    font-size: 0.95rem;
    font-weight: 800;
    margin: 0;
}

.offline-player-media-wrap {
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #000;
    border-radius: 12px;
    overflow: hidden;
}

.offline-player-footer {
    margin-top: 10px;
    text-align: center;
}

.badge-offline-playing {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(16, 185, 129, 0.2);
    border: 1px solid rgba(16, 185, 129, 0.4);
    color: #6ee7b7;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
}

/* تنسيق زر التحميل أوفلاين بجانب الفيديو */
.btn-offline-download {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #1d4ed8 !important;
    position: relative;
    overflow: hidden;
}

.btn-offline-download.is-downloading {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0284c7 !important;
    pointer-events: none;
}

.btn-offline-download.is-saved {
    background: #ecfdf5 !important;
    border-color: #a7f3d0 !important;
    color: #059669 !important;
}

.btn-remove-offline {
    margin-right: 6px;
    padding: 2px 6px;
    border-radius: 6px;
    background: #fee2e2;
    color: #ef4444;
    cursor: pointer;
    font-size: 0.72rem;
}

.offline-progress-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: #2563eb;
    transition: width 0.2s ease;
}

.player-offline-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(16, 185, 129, 0.9);
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.72rem;
    font-weight: 800;
    z-index: 10;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    gap: 5px;
}

/* شريط حالة الشبكة الذكي عند انقطاع الاتصال */
.network-status-pill {
    position: fixed;
    top: 14px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 99999;
    padding: 7px 18px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 800;
    display: none;
    align-items: center;
    gap: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    animation: fadeInDown 0.3s ease;
}

.network-status-pill.offline {
    background: #ef4444;
    color: #ffffff;
}

.network-status-pill.online {
    background: #10b981;
    color: #ffffff;
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translate(-50%, -15px); }
    to { opacity: 1; transform: translate(-50%, 0); }
}

.btn-ios-done {
    width: 100%;
    background: var(--pwa-primary);
    color: #ffffff;
    border: none;
    padding: 13px;
    border-radius: 14px;
    font-weight: 800;
    font-size: 0.9rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

/* تحسين تجربة التطبيق الأصلي للشاشات التي تعمل باللمس ووضع الـ Standalone */
html, body {
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}

body.in-standalone-app {
    padding-top: env(safe-area-inset-top, 0px) !important;
}

/* إخفاء أزرار دعوة التثبيت عندما يكون المستخدم داخل التطبيق بالفعل */
body.in-standalone-app .btn-nav-app-install,
body.in-standalone-app .stepvoro-install-banner,
body.in-standalone-app #bottomNavInstallBtn,
body.in-standalone-app .pwa-only-browser {
    display: none !important;
}
</style>

<!-- تضمين مكتبة الذاكرة المعزولة والتحميل بدون إنترنت -->
<script src="/js/stepvoro-offline-videos.js"></script>

<script>
    // =========================================================================
    // محرك تطبيق Stepvoro PWA للتحكم بالتثبيت والخدمة السحابية
    // =========================================================================
    let deferredPrompt = null;
    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent.toLowerCase());
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

    // 1. تسجيل الـ ServiceWorker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').then(function(reg) {
                reg.onupdatefound = function() {
                    const installingWorker = reg.installing;
                    installingWorker.onstatechange = function() {
                        if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            console.log('Stepvoro App: تحديث جديد متاح تم تنزيله في الخلفية.');
                        }
                    };
                };
            }).catch(function(err) {
                console.warn('Stepvoro ServiceWorker Registration:', err);
            });
        });
    }

    // 2. الاستماع لحدث تثبيت التطبيق الأصلي على أندرويد وكروم
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        
        if (!isStandalone && !sessionStorage.getItem('stepvoro_pwa_dismissed')) {
            showPwaBanner();
        }
    });

    function showPwaBanner() {
        const banner = document.getElementById('stepvoroInstallBanner');
        if (banner) banner.style.display = 'block';
    }

    function dismissPwaBanner() {
        const banner = document.getElementById('stepvoroInstallBanner');
        if (banner) banner.style.display = 'none';
        sessionStorage.setItem('stepvoro_pwa_dismissed', '1');
    }

    // 3. إطلاق التثبيت التفاعلي المباشر (أندرويد أو آيفون)
    function triggerPwaInstall() {
        if (isStandalone) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'info',
                    title: 'أنت تستخدم التطبيق بالفعل!',
                    text: 'تطبيق Stepvoro مثبت وجاهز على هاتفك وتعمل في وضع التطبيق المستقل.',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#1d4ed8'
                });
            } else {
                alert('أنت تستخدم التطبيق بالفعل على هاتفك!');
            }
            return;
        }

        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    dismissPwaBanner();
                }
                deferredPrompt = null;
            });
        } else if (isIos) {
            openIosModal();
        } else {
            if (window.Swal) {
                Swal.fire({
                    title: 'تثبيت تطبيق Stepvoro',
                    html: `
                        <div style="text-align: right; font-size: 0.9rem; line-height: 1.7; color: #334155;">
                            لتثبيت التطبيق على جهازك بنقرة واحدة:<br>
                            1. افتح قائمة خيارات المتصفح (الثلاث نقاط <strong>⋮</strong> في الزاوية).<br>
                            2. اضغط على <strong>"تثبيت التطبيق" (Install App)</strong> أو <strong>"إضافة إلى الشاشة الرئيسية"</strong>.<br>
                            3. سيظهر التطبيق فوراً على شاشة هاتفك بأيقونته الرسمية.
                        </div>
                    `,
                    icon: 'question',
                    confirmButtonText: 'ممتاز، سأقوم بذلك',
                    confirmButtonColor: '#1d4ed8'
                });
            } else {
                alert('لتثبيت التطبيق: افتح قائمة خيارات المتصفح واضغط "إضافة إلى الشاشة الرئيسية"');
            }
        }
    }

    function openIosModal() {
        const modal = document.getElementById('stepvoroIosModal');
        if (modal) modal.style.display = 'flex';
    }

    function closeIosModal(e) {
        if (e && e.target && e.target.closest('.stepvoro-ios-sheet') && !e.target.closest('.btn-close-sheet') && !e.target.closest('.btn-ios-done')) {
            return;
        }
        const modal = document.getElementById('stepvoroIosModal');
        if (modal) modal.style.display = 'none';
    }

    // =========================================================================
    // إدارة نافذة الفيديوهات المحفوظة أوفلاين (Offline Video Vault UI)
    // =========================================================================
    function openOfflineVault() {
        const modal = document.getElementById('stepvoroOfflineVaultModal');
        if (modal) {
            modal.style.display = 'flex';
            renderOfflineVideosList();
        }
    }

    function closeOfflineVault(e) {
        if (e && e.target && e.target.closest('.offline-vault-sheet') && !e.target.closest('.btn-close-sheet') && !e.target.closest('.btn-ios-done')) {
            return;
        }
        const modal = document.getElementById('stepvoroOfflineVaultModal');
        if (modal) modal.style.display = 'none';
    }

    function renderOfflineVideosList() {
        const listContainer = document.getElementById('offlineVaultList');
        const summaryText = document.getElementById('offlineVaultStorageSummary');
        if (!listContainer || !window.StepvoroOfflineDB) return;

        StepvoroOfflineDB.getAllVideos().then((videos) => {
            updateOfflineBadgeCount(videos.length);

            if (!videos || videos.length === 0) {
                if (summaryText) summaryText.textContent = 'لا توجد دروس محفوظة حالياً (0 MB مستخدمة)';
                listContainer.innerHTML = `
                    <div style="text-align: center; padding: 36px 16px; color: #64748b;">
                        <div style="width: 70px; height: 70px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                            <i class="fa-solid fa-cloud-arrow-down" style="font-size: 1.8rem; color: #94a3b8;"></i>
                        </div>
                        <h4 style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin-bottom: 6px;">لا توجد دروس محفوظة أوفلاين</h4>
                        <p style="font-size: 0.78rem; line-height: 1.6; margin: 0 auto; max-width: 320px;">
                            يمكنك حفظ أي درس للمشاهدة بدون إنترنت بالضغط على زر <strong>"تحميل أوفلاين"</strong> بجانب مشغل الفيديو أثناء تصفح المادة.
                        </p>
                    </div>
                `;
                return;
            }

            StepvoroOfflineDB.calculateTotalSize().then((stats) => {
                if (summaryText) {
                    summaryText.textContent = `${videos.length} دروس محفوظة (${stats.mb} ميجابايت من ذاكرة الهاتف)`;
                }
            });

            let html = '';
            videos.forEach((v) => {
                html += `
                    <div class="offline-lesson-card" id="vault_card_${v.id}">
                        <div class="offline-card-info">
                            <h4>${v.title}</h4>
                            <div class="offline-card-meta">
                                <span><i class="fa-solid fa-book-open"></i> ${v.subject}</span>
                                <span>•</span>
                                <span><i class="fa-solid fa-hard-drive"></i> ${v.sizeFormatted || 'فيديو'}</span>
                            </div>
                        </div>
                        <div class="offline-card-actions">
                            <button type="button" class="btn-vault-play" onclick="playOfflineVaultVideo('${v.id}')">
                                <i class="fa-solid fa-play"></i> <span>تشغيل</span>
                            </button>
                            <button type="button" class="btn-vault-delete" onclick="deleteFromVault('${v.id}')" title="حذف لتحرير المساحة">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            listContainer.innerHTML = html;
        }).catch((err) => {
            console.error('Failed to load offline videos list:', err);
            listContainer.innerHTML = `<p style="color: #ef4444; font-size: 0.82rem; text-align: center;">تعذر فتح الذاكرة المحلية: ${err.message}</p>`;
        });
    }

    function playOfflineVaultVideo(id) {
        if (!window.StepvoroOfflineDB) return;
        StepvoroOfflineDB.getVideo(id).then((record) => {
            if (!record || !record.blob) {
                alert('ملف الفيديو غير موجود في الذاكرة.');
                return;
            }

            const playerModal = document.getElementById('stepvoroOfflinePlayerModal');
            const playerVideo = document.getElementById('offlineVaultVideoPlayer');
            const playerTitle = document.getElementById('offlinePlayerTitle');

            if (playerVideo && playerModal) {
                playerVideo.src = URL.createObjectURL(record.blob);
                if (playerTitle) playerTitle.textContent = record.title || 'مشاهدة الدرس بدون إنترنت';
                playerModal.style.display = 'flex';
                playerVideo.play().catch(() => {});
            }
        });
    }

    function closeOfflinePlayer(e) {
        if (e && e.target && e.target.closest('.stepvoro-offline-player-card') && !e.target.closest('.btn-close-sheet')) {
            return;
        }
        const playerModal = document.getElementById('stepvoroOfflinePlayerModal');
        const playerVideo = document.getElementById('offlineVaultVideoPlayer');
        if (playerVideo) {
            playerVideo.pause();
            playerVideo.removeAttribute('src');
            playerVideo.load();
        }
        if (playerModal) playerModal.style.display = 'none';
    }

    function deleteFromVault(id) {
        if (!window.StepvoroVideoDownloader) return;
        StepvoroVideoDownloader.removeOfflineVideo(id);
    }

    function updateOfflineBadgeCount(count) {
        const badge = document.getElementById('bottomNavOfflineBadge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    // تحديث عدد الدروس المحفوظة فور تشغيل التطبيق
    window.addEventListener('DOMContentLoaded', function() {
        if (window.StepvoroOfflineDB) {
            StepvoroOfflineDB.getAllVideos().then((list) => {
                updateOfflineBadgeCount(list.length);
            }).catch(() => {});
        }
    });

    // إخفاء خيارات التثبيت تلقائياً عند تشغيل التطبيق في وضع Standalone
    window.addEventListener('appinstalled', () => {
        dismissPwaBanner();
    });

    if (isStandalone) {
        document.body.classList.add('in-standalone-app');
        const b = document.getElementById('stepvoroInstallBanner');
        if (b) b.style.display = 'none';
        const installBtn = document.getElementById('bottomNavInstallBtn');
        if (installBtn) installBtn.style.display = 'none';
    }

    // تأثير اهتزاز لمسي خفيف عند النقر على عناصر شريط التطبيق (Haptic Feedback)
    document.querySelectorAll('.stepvoro-bottom-nav .nav-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            if ('vibrate' in navigator) {
                try { navigator.vibrate(12); } catch (e) {}
            }
        });
    });
</script>
