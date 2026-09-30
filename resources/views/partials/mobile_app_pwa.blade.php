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
            <img src="/icons/icon.svg" alt="Step by Step App Icon" width="46" height="46">
            <span class="app-verified-badge"><i class="fa-solid fa-check"></i></span>
        </div>
        <div class="banner-text">
            <h4>{{ __('تطبيق Step by Step على هاتفك') }}</h4>
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

<!-- 3. نافذة التثبيت الشاملة لجميع الأجهزة (Universal App Install Modal) -->
<div class="stepvoro-ios-modal-overlay" id="stepByStepInstallModal" onclick="closeInstallModal(event)" style="display: none;">
    <div class="stepvoro-ios-sheet" onclick="event.stopPropagation()">
        <div class="ios-sheet-handle"></div>
        <div class="ios-sheet-header">
            <img src="/icons/icon.svg" alt="Step by Step Icon" width="54" height="54" class="ios-app-icon" style="border-radius: 14px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);">
            <div style="flex: 1; text-align: right; margin-right: 12px;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a;">{{ __('تثبيت تطبيق Step by Step') }}</h3>
                <p style="margin: 3px 0 0; font-size: 0.78rem; color: #64748b;">{{ __('يعمل بدون إنترنت • سريع وفوري • لجميع الأجهزة') }}</p>
            </div>
            <button type="button" class="btn-close-sheet" onclick="closeInstallModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <!-- أشرطة اختيار نوع الجهاز -->
        <div class="install-device-tabs">
            <button type="button" class="install-tab-btn" onclick="switchInstallTab('android')" id="tabBtnAndroid">
                <i class="fa-brands fa-android"></i> <span>أندرويد</span>
            </button>
            <button type="button" class="install-tab-btn" onclick="switchInstallTab('ios')" id="tabBtnIos">
                <i class="fa-brands fa-apple"></i> <span>آيفون iOS</span>
            </button>
            <button type="button" class="install-tab-btn" onclick="switchInstallTab('desktop')" id="tabBtnDesktop">
                <i class="fa-solid fa-desktop"></i> <span>الكمبيوتر</span>
            </button>
        </div>

        <!-- محتوى أندرويد -->
        <div class="install-tab-content" id="tabContentAndroid" style="display: none;">
            <div style="margin-bottom: 14px; text-align: center;">
                <button type="button" class="btn-direct-pwa-install" onclick="executeNativeInstallPrompt()">
                    <i class="fa-solid fa-download"></i>
                    <span>{{ __('تثبيت التطبيق بنقرة واحدة (تطبيق الويب الفوري)') }}</span>
                </button>
            </div>
            <div class="ios-steps-list">
                <div class="ios-step-item">
                    <div class="step-num">1</div>
                    <div class="step-text">
                        <span>افتح قائمة خيارات متصفح كروم (الثلاث نقاط <strong>⋮</strong> في زاوية الشاشة):</span>
                        <span class="ios-icon-hint"><i class="fa-solid fa-ellipsis-vertical"></i> خيارات المتصفح</span>
                    </div>
                </div>
                <div class="ios-step-item">
                    <div class="step-num">2</div>
                    <div class="step-text">
                        <span>اضغط على <strong>"تثبيت التطبيق" (Install app)</strong> أو <strong>"إضافة إلى الشاشة الرئيسية"</strong>:</span>
                        <span class="ios-icon-hint"><i class="fa-solid fa-mobile-screen-button"></i> تثبيت التطبيق</span>
                    </div>
                </div>
                <div class="ios-step-item">
                    <div class="step-num">3</div>
                    <div class="step-text">
                        <span>سيظهر التطبيق فوراً بأيقونته الرسمية على هاتفك ويعمل حتى مع انقطاع الإنترنت! 🎉</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- محتوى آيفون iOS -->
        <div class="install-tab-content" id="tabContentIos" style="display: none;">
            <div class="ios-steps-list">
                <div class="ios-step-item">
                    <div class="step-num">1</div>
                    <div class="step-text">
                        <span>اضغط على زر المشاركة <strong>(Share)</strong> في شريط متصفح Safari السفلي:</span>
                        <span class="ios-icon-hint"><i class="fa-solid fa-arrow-up-from-bracket"></i> مربع السهم لأعلى</span>
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
        </div>

        <!-- محتوى الكمبيوتر Desktop -->
        <div class="install-tab-content" id="tabContentDesktop" style="display: none;">
            <div style="margin-bottom: 14px; text-align: center;">
                <button type="button" class="btn-direct-pwa-install" onclick="executeNativeInstallPrompt()">
                    <i class="fa-solid fa-desktop"></i>
                    <span>{{ __('تثبيت التطبيق على جهاز الكمبيوتر الآن') }}</span>
                </button>
            </div>
            <div class="ios-steps-list">
                <div class="ios-step-item">
                    <div class="step-num">1</div>
                    <div class="step-text">
                        <span>انظر إلى شريط العنوان (URL) في متصفحك بالأعلى بجوار النجمة:</span>
                        <span class="ios-icon-hint"><i class="fa-solid fa-arrow-down-to-bracket"></i> ستجد أيقونة التثبيت (⊕ أو رمز التطبيق)</span>
                    </div>
                </div>
                <div class="ios-step-item">
                    <div class="step-num">2</div>
                    <div class="step-text">
                        <span>اضغط عليها ثم اختر <strong>"تثبيت" (Install)</strong>، أو من قائمة المتصفح (الثلاث نقاط <strong>⋮</strong>) اختر <strong>"تثبيت Step by Step"</strong>:</span>
                        <span class="ios-icon-hint"><i class="fa-solid fa-window-maximize"></i> تثبيت Step by Step</span>
                    </div>
                </div>
                <div class="ios-step-item">
                    <div class="step-num">3</div>
                    <div class="step-text">
                        <span>سيفتح التطبيق في نافذة مستقلة وسريعة على سطح المكتب وشريط المهام! 🚀</span>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn-ios-done" onclick="closeInstallModal()" style="margin-top: 14px;">
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
    display: none;
    pointer-events: none;
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

.stepvoro-install-banner.active,
.stepvoro-install-banner[style*="display: block"] {
    display: block !important;
    pointer-events: auto !important;
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
    display: none;
    pointer-events: none;
    align-items: flex-end;
    justify-content: center;
    animation: fadeInModal 0.25s ease-out;
}

.stepvoro-ios-modal-overlay.active,
.stepvoro-ios-modal-overlay[style*="display: flex"] {
    display: flex !important;
    pointer-events: auto !important;
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

/* ==========================================================================
   تنسيقات زر التحميل أوفلاين الحديث وشارات التخزين داخل المنصة
   ========================================================================== */
.ed-offline-action-wrapper {
    display: inline-flex;
    align-items: center;
}

.ed-btn-offline-card {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 16px;
    border-radius: 12px;
    font-size: 0.82rem;
    font-weight: 800;
    font-family: inherit;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1.5px solid #93c5fd;
    color: #1d4ed8;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.12);
}

.ed-btn-offline-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 16px rgba(37, 99, 235, 0.22);
    border-color: #60a5fa;
}

.ed-btn-offline-card:active {
    transform: scale(0.98);
}

/* وضع المتصفح العادي (Web Mode) */
.ed-btn-offline-card.is-web-mode {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-color: #cbd5e1;
    color: #475569;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
}

.ed-btn-offline-card.is-web-mode:hover {
    border-color: #93c5fd;
    color: #1d4ed8;
    background: #f0f7ff;
}

/* حالة جاري التحميل */
.ed-btn-offline-card.is-downloading {
    background: #f0fdfa !important;
    border-color: #5eead4 !important;
    color: #0f766e !important;
    cursor: wait;
    pointer-events: none;
}

/* حالة الحفظ والاكتمال */
.ed-btn-offline-card.is-saved {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%) !important;
    border-color: #6ee7b7 !important;
    color: #065f46 !important;
    box-shadow: 0 2px 10px rgba(16, 185, 129, 0.18);
}

.ed-offline-btn-inner {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    z-index: 2;
}

.ed-offline-btn-icon {
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-remove-offline {
    margin-right: 6px;
    padding: 3px 8px;
    border-radius: 8px;
    background: #fee2e2;
    color: #ef4444;
    cursor: pointer;
    font-size: 0.76rem;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-remove-offline:hover {
    background: #fecaca;
    color: #dc2626;
    transform: scale(1.08);
}

.ed-offline-progress-track {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: rgba(15, 118, 110, 0.15);
}

.ed-offline-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #06b6d4);
    transition: width 0.25s ease;
    border-radius: 0 2px 2px 0;
}

.player-offline-badge {
    position: absolute;
    top: 14px;
    right: 14px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 0.78rem;
    font-weight: 800;
    z-index: 25;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.35);
    display: flex;
    align-items: center;
    gap: 7px;
    border: 1.5px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(8px);
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
    pointer-events: none !important;
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

/* تحسين تجربة التطبيق للشاشات التي تعمل باللمس ووضع الـ Standalone */
html, body {
    -webkit-tap-highlight-color: transparent;
}

body.in-standalone-app {
    padding-top: env(safe-area-inset-top, 0px) !important;
}

/* منع تداخل شريط التنقل مع الامتحانات أو شريط القالب الأساسي */
.no-sidebar .stepvoro-bottom-nav,
body.in-exam .stepvoro-bottom-nav,
body[class*="exam"] .stepvoro-bottom-nav,
.mobile-bottom-nav ~ .stepvoro-bottom-nav {
    display: none !important;
}

/* علامات تبويب اختيار الجهاز في نافذة التثبيت */
.install-device-tabs {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    padding: 5px;
    border-radius: 14px;
    margin: 10px 0 16px;
}

.install-tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 6px;
    border: none;
    background: transparent;
    color: #64748b;
    font-size: 0.8rem;
    font-weight: 700;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.install-tab-btn.active {
    background: #ffffff;
    color: #1d4ed8;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.btn-direct-pwa-install {
    width: 100%;
    padding: 12px 16px;
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #ffffff;
    border: none;
    border-radius: 14px;
    font-size: 0.88rem;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    transition: transform 0.2s, box-shadow 0.2s;
}

.btn-direct-pwa-install:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
}

.btn-direct-pwa-install:active {
    transform: scale(0.98);
}

/* إشعار عائم راقي بدون أي Alert مزعج */
.stepvoro-toast {
    position: fixed;
    top: 24px;
    left: 50%;
    transform: translateX(-50%) translateY(-20px);
    background: rgba(15, 23, 42, 0.95);
    color: #ffffff;
    padding: 12px 22px;
    border-radius: 16px;
    font-size: 0.86rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 999999;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.stepvoro-toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
    pointer-events: auto;
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

    // دالة إشعار عائمة راقية (Toast Notification)
    function showPwaToast(msg, type = 'info') {
        let toast = document.getElementById('stepvoroToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'stepvoroToast';
            toast.className = 'stepvoro-toast';
            document.body.appendChild(toast);
        }
        const icon = type === 'success' ? '<i class="fa-solid fa-circle-check" style="color:#10b981;"></i>' : (type === 'error' ? '<i class="fa-solid fa-circle-exclamation" style="color:#ef4444;"></i>' : '<i class="fa-solid fa-circle-info" style="color:#38bdf8;"></i>');
        toast.innerHTML = icon + '<span>' + msg + '</span>';
        toast.classList.add('show');
        clearTimeout(window.__toastTimer);
        window.__toastTimer = setTimeout(() => {
            toast.classList.remove('show');
        }, 3600);
    }

    // التنقل بين تبويبات الأجهزة داخل نافذة التثبيت
    function switchInstallTab(device) {
        document.querySelectorAll('.install-tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.install-tab-content').forEach(c => c.style.display = 'none');

        if (device === 'android') {
            const btn = document.getElementById('tabBtnAndroid');
            const content = document.getElementById('tabContentAndroid');
            if (btn) btn.classList.add('active');
            if (content) content.style.display = 'block';
        } else if (device === 'ios') {
            const btn = document.getElementById('tabBtnIos');
            const content = document.getElementById('tabContentIos');
            if (btn) btn.classList.add('active');
            if (content) content.style.display = 'block';
        } else {
            const btn = document.getElementById('tabBtnDesktop');
            const content = document.getElementById('tabContentDesktop');
            if (btn) btn.classList.add('active');
            if (content) content.style.display = 'block';
        }
    }

    // فتح نافذة التثبيت الشاملة مع التحديد التلقائي لنوع جهاز المستخدم
    function openInstallModal() {
        const modal = document.getElementById('stepByStepInstallModal');
        if (!modal) return;

        if (isIos) {
            switchInstallTab('ios');
        } else if (/Android/i.test(navigator.userAgent)) {
            switchInstallTab('android');
        } else {
            // الكمبيوتر / سطح المكتب (Windows / Mac / Chrome / Edge)
            switchInstallTab('desktop');
        }

        modal.style.display = 'flex';
    }

    function closeInstallModal(e) {
        if (e && e.target && e.target.closest('.stepvoro-ios-sheet') && !e.target.closest('.btn-close-sheet') && !e.target.closest('.btn-ios-done')) {
            return;
        }
        const modal = document.getElementById('stepByStepInstallModal');
        if (modal) modal.style.display = 'none';
    }

    // تشغيل طلب التثبيت الرسمي عند الضغط على زر التثبيت المباشر
    function executeNativeInstallPrompt() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    dismissPwaBanner();
                    closeInstallModal();
                    showPwaToast('جاري تثبيت تطبيق Step by Step على جهازك...', 'success');
                }
                deferredPrompt = null;
            });
        } else {
            showPwaToast('يرجى اتباع الخطوات الموضحة في النافذة لإكمال التثبيت على متصفحك.', 'info');
        }
    }

    // 3. إطلاق التثبيت التفاعلي المباشر لجميع الأجهزة
    function triggerPwaInstall() {
        if (isStandalone) {
            showPwaToast('أنت تستخدم تطبيق Step by Step بالفعل على جهازك!', 'success');
            return;
        }

        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    dismissPwaBanner();
                    showPwaToast('جاري تثبيت تطبيق Step by Step على جهازك...', 'success');
                }
                deferredPrompt = null;
            });
        } else {
            openInstallModal();
        }
    }

    // دوال التوافق القديمة
    function openIosModal() {
        openInstallModal();
    }

    function closeIosModal(e) {
        closeInstallModal(e);
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
                showPwaToast('ملف الفيديو غير متوفر في الذاكرة المحلية.', 'error');
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
        ['bottomNavOfflineBadge', 'topbarOfflineBadge'].forEach(id => {
            const badge = document.getElementById(id);
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = id === 'topbarOfflineBadge' ? 'inline-block' : 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        });
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

    // ربط الدوال الأساسية بنطاق النافذة العام لضمان استدعائها من أي مكان
    window.triggerPwaInstall = triggerPwaInstall;
    window.openInstallModal = openInstallModal;
    window.closeInstallModal = closeInstallModal;
    window.openOfflineVault = openOfflineVault;
    window.closeOfflineVault = closeOfflineVault;
    window.renderOfflineVideosList = renderOfflineVideosList;
    window.playOfflineVaultVideo = playOfflineVaultVideo;
    window.closeOfflinePlayer = closeOfflinePlayer;
    window.showPwaToast = showPwaToast;
</script>
