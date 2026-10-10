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
    <a href="{{ route('offline.videos') }}" class="nav-tab {{ request()->is('*offline*') ? 'active' : '' }}" id="bottomNavOfflineBtn" title="{{ __('دروسي المحفوظة أوفلاين بدون نت') }}">
        <div class="nav-tab-icon offline-vault-highlight">
            <i class="fa-solid fa-cloud-arrow-down"></i>
            <span class="badge-offline-count" id="bottomNavOfflineBadge" style="display: none;">0</span>
        </div>
        <span class="nav-tab-label">{{ __('المحملة ⚡') }}</span>
    </a>
    @if(Auth::guard('student')->check() || Auth::check())
        <a href="{{ route('dashboard') }}" class="nav-tab {{ request()->is('student*') || request()->is('admin*') || request()->is('teacher*') || request()->is('videographer*') ? 'active' : '' }}" style="position: relative;">
            <div class="nav-tab-icon" style="position: relative;">
                <i class="fa-solid fa-user-circle"></i>
                <span class="app-unread-badge" style="display: none; position: absolute; top: -4px; right: -6px; background: #dc2626; color: #fff; font-size: 0.6rem; min-width: 15px; height: 15px; border-radius: 50px; padding: 0 4px; font-weight: 800; border: 1.5px solid #fff; align-items: center; justify-content: center; line-height: 1;">0</span>
            </div>
            <span class="nav-tab-label">{{ __('حسابي') }}</span>
        </a>
    @else
        <a href="{{ route('login') }}" class="nav-tab nav-tab-login-btn {{ request()->is('login*') ? 'active' : '' }}" title="{{ __('تسجيل الدخول إلى حسابك') }}">
            <div class="nav-tab-icon login-highlight"><i class="fa-solid fa-arrow-right-to-bracket"></i></div>
            <span class="nav-tab-label">{{ __('دخول') }}</span>
        </a>
    @endif
</nav>

<!-- 2. بطاقة التثبيت السريعة العائمة للهواتف (Smart App Install Floating Banner) -->
<aside class="stepvoro-install-banner" id="stepvoroInstallBanner" style="display: none;">
    <div class="banner-content-wrap">
        <div class="banner-app-icon">
            <img src="/icons/step-by-step-icon-192.png?v=20261002-v33" alt="Step by Step App Icon" width="46" height="46" style="border-radius: 50%; object-fit: contain; background: #ffffff; padding: 1px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
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
            <img src="/icons/step-by-step-icon-192.png?v=20261002-v33" alt="Step by Step Icon" width="54" height="54" class="ios-app-icon" style="border-radius: 50%; box-shadow: 0 4px 14px rgba(14, 61, 111, 0.2); background: #ffffff; padding: 2px;">
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
<div class="stepvoro-ios-modal-overlay" id="stepvoroOfflinePlayerModal" onclick="closeOfflinePlayer(event)" style="display: none; z-index: 10001; align-items: center;">
    <div class="stepvoro-offline-player-card" onclick="event.stopPropagation()">
        <div class="offline-player-header">
            <h4 id="offlinePlayerTitle">{{ __('مشاهدة الدرس') }}</h4>
            <button type="button" class="btn-close-sheet" onclick="closeOfflinePlayer()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="offline-player-media-wrap" id="offlinePlayerMediaWrap" style="position: relative; aspect-ratio: 16/9; background: #000; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center;" oncontextmenu="event.preventDefault(); return false;">
            <video id="offlineVaultVideoPlayer" controls playsinline controlsList="nodownload noplaybackrate" oncontextmenu="return false;" style="width: 100%; height: 100%; object-fit: contain; background: #000; border-radius: 12px;"></video>
            <iframe id="offlineVaultIframePlayer" 
                    sandbox="allow-scripts allow-same-origin allow-presentation allow-forms"
                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                    style="display: none; position: absolute; inset: 0; width: 100%; height: 100%; border: none; pointer-events: none !important; z-index: 1;" 
                    allowfullscreen></iframe>
            
            {{-- دروع حماية مشغل المنصة ضد أي وصول لحساب اليوتيوب --}}
            <div id="vaultYtShield" style="display: none; position: absolute; inset: 0; z-index: 15; pointer-events: auto;">
                {{-- درع علوي يمنع النقر على العنوان، صورة الحساب، اسم القناة --}}
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 80px; z-index: 25; cursor: pointer;" onclick="toggleVaultYtPlayback()"></div>
                {{-- درع سفلي أيمن يمنع النقر على شعار يوتيوب أو زر المشاركة --}}
                <div style="position: absolute; bottom: 0; right: 0; width: 150px; height: 70px; z-index: 25; cursor: pointer;" onclick="toggleVaultYtPlayback()"></div>
                {{-- درع سفلي أيسر --}}
                <div style="position: absolute; bottom: 0; left: 0; width: 150px; height: 70px; z-index: 25; cursor: pointer;" onclick="toggleVaultYtPlayback()"></div>
                {{-- درع مركزي تفاعلي للنقر للتشغيل والإيقاف بدون لمس اليوتيوب --}}
                <div style="position: absolute; inset: 0; z-index: 20; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="toggleVaultYtPlayback()">
                    <div id="vaultYtCenterPlay" style="width: 58px; height: 58px; border-radius: 50%; background: rgba(15, 23, 42, 0.85); border: 2px solid rgba(255,255,255,0.85); backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center; color: #fff; font-size: 1.4rem; box-shadow: 0 4px 15px rgba(0,0,0,0.5); pointer-events: none;">
                        <i class="fa-solid fa-play" style="margin-left: 2px;"></i>
                    </div>
                </div>
                {{-- شريط تحكم داخلي خاص بالمنصة مدمج أسفل الفيديو --}}
                <div style="position: absolute; bottom: 0; left: 0; right: 0; z-index: 30; background: linear-gradient(to top, rgba(15,23,42,0.95), transparent); padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <button type="button" onclick="toggleVaultYtPlayback()" id="vaultYtPlayBtn" style="background: none; border: none; color: #fff; font-size: 1.1rem; cursor: pointer; padding: 4px;" title="تشغيل / إيقاف مؤقت">
                        <i class="fa-solid fa-play"></i>
                    </button>
                    <button type="button" onclick="seekVaultYtRelative(-10)" style="background: none; border: none; color: #cbd5e1; font-size: 0.9rem; cursor: pointer; padding: 4px;" title="تأخير 10 ثوانٍ">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                    <button type="button" onclick="seekVaultYtRelative(10)" style="background: none; border: none; color: #cbd5e1; font-size: 0.9rem; cursor: pointer; padding: 4px;" title="تقديم 10 ثوانٍ">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                    <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;" id="vaultYtStatusText">مشغل المنصة المحمي</span>
                    <button type="button" onclick="toggleVaultYtFullscreen()" style="background: none; border: none; color: #cbd5e1; font-size: 0.95rem; cursor: pointer; padding: 4px;" title="ملء الشاشة">
                        <i class="fa-solid fa-expand"></i>
                    </button>
                </div>
            </div>

            <div id="offlineVaultFallbackWrap" style="display: none; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center; padding: 24px 16px; background: #0f172a; color: #fff; text-align: center;">
                <i id="vaultFallbackIcon" class="fa-solid fa-file-pdf" style="font-size: 2.4rem; color: #ef4444; margin-bottom: 10px;"></i>
                <h4 id="vaultFallbackTitle" style="font-size: 1rem; margin-bottom: 6px; font-weight: 800;">ملزمة الدرس متاحة للمراجعة</h4>
                <p id="vaultFallbackDesc" style="font-size: 0.82rem; color: #94a3b8; margin-bottom: 14px; max-width: 360px; line-height: 1.5;">يمكنك قراءة ملزمة وملاحظات الدرس بدون إنترنت.</p>
                <button type="button" id="btnOpenVaultPdf" class="btn-direct-pwa-install" style="font-size: 0.8rem; padding: 7px 18px; margin: 0 auto; display: inline-flex;">
                    <i class="fa-solid fa-book-open"></i> <span>فتح ملزمة الدرس ⚡</span>
                </button>
            </div>
        </div>
        <div class="offline-player-footer">
            <span class="badge-offline-playing" id="offlinePlayerFooterBadge"><i class="fa-solid fa-bolt"></i> {{ __('مشاهدة بدون إنترنت مباشرة من ذاكرة التطبيق') }}</span>
        </div>
    </div>
</div>

<!-- 6. شريط تنبيه انقطاع الإنترنت الملكي الحي (Royal Live Offline Notice Bar) -->
<div id="stepvoroOfflineNoticeBar" class="stepvoro-offline-notice-bar" style="display: none;">
    <div class="offline-notice-inner">
        <div class="offline-notice-left">
            <div class="offline-notice-icon">
                <i class="fa-solid fa-cloud-arrow-down"></i>
            </div>
            <div class="offline-notice-text">
                <strong>{{ __('أنت في وضع عدم الاتصال حالياً (Offline Mode)') }}</strong>
                <span>{{ __('التطبيق يعمل بكفاءة من الذاكرة المحلية لهاتفك. يمكنك متابعة دراسة دروسك وأدواتك المحفوظة.') }}</span>
            </div>
        </div>
        <div class="offline-notice-actions">
            <a href="{{ route('offline.videos') }}" class="btn-notice-vault">
                <i class="fa-solid fa-bolt"></i>
                <span>{{ __('دروسي المحفوظة') }}</span>
            </a>
            <button type="button" class="btn-notice-close" onclick="dismissOfflineNotice()" aria-label="إغلاق">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
</div>

<style>
/* ==========================================================================
   تنسيقات شريط التنبيه الأوفلاين وشاشات التطبيق المتطورة
   ========================================================================== */
.stepvoro-offline-notice-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 999999;
    background: #ffffff;
    border-bottom: 2px solid #d97706;
    box-shadow: 0 4px 20px rgba(11, 59, 111, 0.15);
    padding: 10px 16px;
    animation: slideDownNotice 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideDownNotice {
    from { transform: translateY(-100%); }
    to { transform: translateY(0); }
}

.offline-notice-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
}

.offline-notice-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.offline-notice-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #fef3c7;
    color: #b45309;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.offline-notice-text {
    display: flex;
    flex-direction: column;
}

.offline-notice-text strong {
    font-size: 0.88rem;
    color: #0f172a;
    font-weight: 800;
}

.offline-notice-text span {
    font-size: 0.78rem;
    color: #64748b;
}

.offline-notice-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-notice-vault {
    background: #0b3b6f;
    color: #ffffff;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background 0.2s;
}

.btn-notice-vault:hover {
    background: #072344;
}

.btn-notice-close {
    background: #f1f5f9;
    border: none;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
}
:root {
    --pwa-primary: #0b3b6f;
    --pwa-primary-gradient: linear-gradient(135deg, #0b3b6f 0%, #0284c7 100%);
    --pwa-cyan: #0284c7;
    --pwa-orange: #f27429;
    --pwa-gold: #f27429;
    --pwa-green: #10b981;
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

.nav-tab-login-btn {
    position: relative;
}
.nav-tab-login-btn .login-highlight i {
    color: #1e40af;
}
.nav-tab-login-btn .nav-tab-icon {
    font-size: 1.15rem;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1e40af;
    transition: transform 0.2s ease;
}
.nav-tab-login-btn:hover .nav-tab-icon,
.nav-tab-login-btn.active .nav-tab-icon {
    transform: translateY(-2px);
    color: #1e40af;
}
.nav-tab-login-btn .nav-tab-label {
    color: #1e40af;
    font-weight: 700;
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
    background: #1d4ed8;
    color: #ffffff;
    border: none;
    padding: 7px 14px;
    border-radius: 10px;
    font-size: 0.78rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.btn-vault-play:hover {
    background: #1e40af;
}

.btn-vault-delete {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.82rem;
    transition: all 0.2s ease;
}

.btn-vault-delete:hover {
    background: #fca5a5;
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

/* منع تداخل شريط التنقل مع الامتحانات أو شاشات المحادثة أو شريط القالب الأساسي */
.no-sidebar .stepvoro-bottom-nav,
body.in-exam .stepvoro-bottom-nav,
body[class*="exam"] .stepvoro-bottom-nav,
body.is-chat-page .stepvoro-bottom-nav,
body.in-chat-mode .stepvoro-bottom-nav,
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
<script src="/js/stepvoro-offline-videos.js?v=20261002-v35"></script>

<script>
    // دالة ترميز النصوص بأمان لمنع أي أخطاء برمجية
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
    window.escapeHtml = escapeHtml;

    // =========================================================================
    // محرك تطبيق Stepvoro PWA للتحكم بالتثبيت والخدمة السحابية
    // =========================================================================
    let deferredPrompt = null;
    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent.toLowerCase());
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

    // 1. تسجيل ومراقبة تحديثات الـ ServiceWorker التلقائية فوراً
    if ('serviceWorker' in navigator) {
        let refreshing = false;

        // إعادة تنشيط الواجهة بسلاسة فور استلام كود أحدث من السيرفر
        navigator.serviceWorker.addEventListener('controllerchange', function() {
            if (!refreshing) {
                refreshing = true;
                window.location.reload();
            }
        });

        navigator.serviceWorker.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'PWA_UPDATED') {
                if (!refreshing) {
                    refreshing = true;
                    window.location.reload();
                }
            }
        });

        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js?v=20261002-v35', { updateViaCache: 'none' }).then(function(reg) {
                // تفعيل فوري لأي عامل خدمة في حالة انتظار
                if (reg.waiting) {
                    try { reg.waiting.postMessage({ action: 'skipWaiting' }); } catch(e) {}
                }

                // فحص فوري للتحديثات عند فتح التطبيق
                try { reg.update(); } catch(e) {}

                // فحص دوري للتحديثات كل 60 ثانية لضمان تطبيق أي تعديل يرفعه المشرف فوراً
                setInterval(function() {
                    try { reg.update(); } catch(e) {}
                }, 60000);

                reg.onupdatefound = function() {
                    const installingWorker = reg.installing;
                    if (installingWorker) {
                        installingWorker.onstatechange = function() {
                            if (installingWorker.state === 'installed') {
                                // تفعيل فوري فور اكتمال التثبيت
                                installingWorker.postMessage({ action: 'skipWaiting' });
                            }
                        };
                    }
                };
            }).catch(function(err) {
                console.warn('Step by Step ServiceWorker Registration Notice:', err);
            });
        });

        // عند عودة الطالب للنافذة أو فتح الهاتف، فحص التحديثات تلقائياً
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible' && navigator.serviceWorker.ready) {
                navigator.serviceWorker.ready.then(function(reg) {
                    try { reg.update(); } catch(e) {}
                });
            }
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
            renderModalOfflineVaultList();
        }
    }

    function closeOfflineVault(e) {
        if (e && e.target && e.target.closest('.offline-vault-sheet') && !e.target.closest('.btn-close-sheet') && !e.target.closest('.btn-ios-done')) {
            return;
        }
        const modal = document.getElementById('stepvoroOfflineVaultModal');
        if (modal) modal.style.display = 'none';
    }

    function renderVaultCards(videos, listContainer, summaryText) {
        try {
            updateOfflineBadgeCount(videos ? videos.length : 0);

            if (!videos || videos.length === 0) {
                if (summaryText) summaryText.textContent = 'لا توجد دروس محفوظة حالياً (0 MB مستخدمة)';
                listContainer.innerHTML = `
                    <div style="text-align: center; padding: 36px 16px; color: #64748b;">
                        <div style="width: 70px; height: 70px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                            <i class="fa-solid fa-cloud-arrow-down" style="font-size: 1.8rem; color: #94a3b8;"></i>
                        </div>
                        <h4 style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin-bottom: 6px;">لا توجد دروس محفوظة أوفلاين</h4>
                        <p style="font-size: 0.78rem; line-height: 1.6; margin: 0 auto; max-width: 320px;">
                            يمكنك حفظ أي درس للمشاهدة بدون إنترنت بالضغط على زر <strong>"تحميل الدرس أوفلاين"</strong> بجانب مشغل الفيديو أثناء تصفح المادة.
                        </p>
                    </div>
                `;
                return;
            }

            if (summaryText) {
                summaryText.textContent = `${videos.length} دروس محفوظة في ذاكرة الهاتف`;
            }

            const safeEscape = (typeof escapeHtml === 'function') 
                ? escapeHtml 
                : (str) => String(str || '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m]);

            let html = '';
            videos.forEach((v) => {
                if (!v) return;
                const vid = String(v.id || '').replace(/'/g, "\\'");
                const title = safeEscape(v.title || 'درس تعليمي');
                const subject = safeEscape(v.subject || 'المنهاج الوزاري');
                const hasBlob = !!v.hasBlob || !!v.blob;
                const hasPdf = !!v.hasPdf || !!v.pdfBlob;
                const sizeLabel = safeEscape(v.sizeFormatted || (hasBlob ? 'فيديو أوفلاين' : 'ملزمة'));

                html += `
                    <div class="offline-lesson-card" id="vault_card_${vid}">
                        <div class="offline-card-info">
                            <h4>${title}</h4>
                            <div class="offline-card-meta">
                                <span><i class="fa-solid fa-book-open"></i> ${subject}</span>
                                <span>•</span>
                                <span><i class="fa-solid fa-hard-drive"></i> ${sizeLabel}</span>
                            </div>
                        </div>
                        <div class="offline-card-actions">
                            ${hasBlob ? `
                            <button type="button" class="btn-vault-play" onclick="playOfflineVaultVideo('${vid}')" title="تشغيل أوفلاين بدون إنترنت ⚡">
                                <i class="fa-solid fa-play"></i> <span>تشغيل أوفلاين</span>
                            </button>
                            ` : hasPdf ? `
                            <button type="button" class="btn-vault-play" style="background: #dc2626;" onclick="playOfflineVaultVideo('${vid}')" title="فتح ملزمة الدرس المحفوظة بدون إنترنت">
                                <i class="fa-solid fa-file-pdf"></i> <span>الملزمة</span>
                            </button>
                            ` : `
                            <button type="button" class="btn-vault-play" onclick="playOfflineVaultVideo('${vid}')">
                                <i class="fa-solid fa-book-open-reader"></i> <span>عرض الدرس</span>
                            </button>
                            `}
                            <button type="button" class="btn-vault-delete" onclick="deleteFromVault('${vid}')" title="حذف لتحرير المساحة">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            listContainer.innerHTML = html;
        } catch (renderErr) {
            console.error('Error rendering vault cards:', renderErr);
        }
    }

    function renderModalOfflineVaultList(retryCount = 0) {
        const listContainer = document.getElementById('offlineVaultList');
        const summaryText = document.getElementById('offlineVaultStorageSummary');
        if (!listContainer) return;

        const db = window.StepvoroOfflineDB || (typeof StepvoroOfflineDB !== 'undefined' ? StepvoroOfflineDB : null);

        if (!db) {
            if (retryCount < 8) {
                setTimeout(() => renderModalOfflineVaultList(retryCount + 1), 120);
                return;
            }
            readVaultDirectlyFromIndexedDB(listContainer, summaryText);
            return;
        }

        db.getAllVideos().then((videos) => {
            renderVaultCards(videos, listContainer, summaryText);

            if (videos && videos.length > 0) {
                db.calculateTotalSize().then((stats) => {
                    if (summaryText && stats) {
                        summaryText.textContent = `${videos.length} دروس محفوظة (${stats.mb} ميجابايت من ذاكرة الهاتف)`;
                    }
                }).catch(() => {});
            }
        }).catch((err) => {
            console.warn('StepvoroOfflineDB.getAllVideos fallback to raw IndexedDB:', err);
            readVaultDirectlyFromIndexedDB(listContainer, summaryText);
        });
    }

    function readVaultDirectlyFromIndexedDB(listContainer, summaryText) {
        if (!('indexedDB' in window)) {
            if (summaryText) summaryText.textContent = 'الذاكرة المحلية غير مدعومة';
            listContainer.innerHTML = '<p style="text-align: center; color: #ef4444; padding: 24px;">الذاكرة المحلية غير مدعومة في هذا المتصفح.</p>';
            return;
        }

        try {
            const req = indexedDB.open('StepvoroOfflineStore', 2);
            req.onsuccess = function(e) {
                const db = e.target.result;
                if (!db.objectStoreNames.contains('offline_videos')) {
                    renderVaultCards([], listContainer, summaryText);
                    return;
                }
                const tx = db.transaction(['offline_videos'], 'readonly');
                const store = tx.objectStore('offline_videos');
                const getReq = store.getAll();
                getReq.onsuccess = function() {
                    const videos = getReq.result || [];
                    renderVaultCards(videos, listContainer, summaryText);
                };
                getReq.onerror = function() {
                    showVaultErrorUI(listContainer, summaryText);
                };
            };
            req.onerror = function() {
                showVaultErrorUI(listContainer, summaryText);
            };
        } catch (e) {
            showVaultErrorUI(listContainer, summaryText);
        }
    }

    function showVaultErrorUI(listContainer, summaryText) {
        if (summaryText) summaryText.textContent = 'تعذر فتح الذاكرة المحلية';
        if (listContainer) {
            listContainer.innerHTML = `
                <div style="text-align: center; padding: 24px 16px; color: #ef4444;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.8rem; margin-bottom: 8px;"></i>
                    <p style="font-size: 0.85rem; font-weight: 700; margin-bottom: 12px;">تعذر فتح الذاكرة المحلية للتطبيق</p>
                    <button type="button" onclick="renderModalOfflineVaultList()" class="btn-direct-pwa-install" style="font-size: 0.8rem; padding: 6px 16px; margin: 0 auto; display: inline-flex;">
                        <i class="fa-solid fa-rotate"></i> <span>إعادة المحاولة</span>
                    </button>
                </div>
            `;
        }
    }

    function formatStepvoroYtUrl(rawUrl) {
        if (!rawUrl) return '';
        let url = rawUrl;
        const match = url.match(/(?:v=|youtu\.be\/|embed\/|shorts\/|live\/)([a-zA-Z0-9_\-]{11})/);
        if (match && match[1]) {
            url = 'https://www.youtube-nocookie.com/embed/' + match[1];
        } else {
            url = url.replace('https://www.youtube.com/embed/', 'https://www.youtube-nocookie.com/embed/')
                     .replace('http://www.youtube.com/embed/', 'https://www.youtube-nocookie.com/embed/');
        }
        const params = 'enablejsapi=1&controls=0&rel=0&modestbranding=1&iv_load_policy=3&showinfo=0&fs=0&disablekb=1&playsinline=1&autoplay=1';
        return url + (url.includes('?') ? '&' : '?') + params;
    }

    function playOfflineVaultVideo(id, retryCount = 0) {
        const db = window.StepvoroOfflineDB || (typeof StepvoroOfflineDB !== 'undefined' ? StepvoroOfflineDB : null);
        if (!db) {
            if (retryCount < 6) {
                setTimeout(() => playOfflineVaultVideo(id, retryCount + 1), 120);
                return;
            }
            return;
        }

        db.getVideo(id).then((record) => {
            if (!record) {
                if (typeof showPwaToast === 'function') {
                    showPwaToast('تعذر العثور على الدرس في الذاكرة المحلية.', 'error');
                }
                return;
            }

            const playerModal = document.getElementById('stepvoroOfflinePlayerModal');
            const playerVideo = document.getElementById('offlineVaultVideoPlayer');
            const playerIframe = document.getElementById('offlineVaultIframePlayer');
            const fallbackWrap = document.getElementById('offlineVaultFallbackWrap');
            const playerTitle = document.getElementById('offlinePlayerTitle');
            const footerBadge = document.getElementById('offlinePlayerFooterBadge');
            const btnOpenPdf = document.getElementById('btnOpenVaultPdf');
            const fbIcon = document.getElementById('vaultFallbackIcon');
            const fbTitle = document.getElementById('vaultFallbackTitle');
            const fbDesc = document.getElementById('vaultFallbackDesc');

            if (!playerModal) return;

            if (playerTitle) playerTitle.textContent = record.title || 'مشاهدة الدرس';

            if (record.blob) {
                const ytShield = document.getElementById('vaultYtShield');
                if (ytShield) ytShield.style.display = 'none';
                if (playerIframe) { playerIframe.style.display = 'none'; playerIframe.src = 'about:blank'; }
                if (fallbackWrap) fallbackWrap.style.display = 'none';
                if (playerVideo) {
                    playerVideo.style.display = 'block';
                    playerVideo.src = URL.createObjectURL(record.blob);
                    playerModal.style.display = 'flex';
                    playerVideo.play().catch(() => {});
                }
                if (footerBadge) footerBadge.innerHTML = '<i class="fa-solid fa-bolt"></i> مشغل الفيديو من ذاكرة التطبيق بدون إنترنت ⚡';
            } else if (record.ytEmbed && navigator.onLine) {
                if (playerVideo) { playerVideo.style.display = 'none'; playerVideo.pause(); }
                if (fallbackWrap) fallbackWrap.style.display = 'none';
                if (playerIframe) {
                    playerIframe.style.display = 'block';
                    playerIframe.src = formatStepvoroYtUrl(record.ytEmbed);
                }
                const ytShield = document.getElementById('vaultYtShield');
                if (ytShield) ytShield.style.display = 'block';
                isVaultYtPlaying = true;
                const playBtn = document.getElementById('vaultYtPlayBtn');
                if (playBtn) playBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
                const centerPlay = document.getElementById('vaultYtCenterPlay');
                if (centerPlay) centerPlay.style.display = 'none';

                playerModal.style.display = 'flex';
                if (footerBadge) footerBadge.innerHTML = '<i class="fa-solid fa-shield-halved"></i> مشغل المنصة المحمي كلياً ضد أي وصول خارجي';
            } else if (record.pdfBlob || record.pdfUrl) {
                const ytShield = document.getElementById('vaultYtShield');
                if (ytShield) ytShield.style.display = 'none';
                if (playerVideo) { playerVideo.style.display = 'none'; playerVideo.pause(); }
                if (playerIframe) { playerIframe.style.display = 'none'; playerIframe.src = 'about:blank'; }
                if (fallbackWrap) {
                    fallbackWrap.style.display = 'flex';
                    if (fbIcon) fbIcon.className = 'fa-solid fa-file-pdf';
                    if (fbTitle) fbTitle.textContent = 'ملزمة وأوراق عمل الدرس جاهزة أوفلاين ⚡';
                    if (fbDesc) fbDesc.textContent = record.ytEmbed 
                        ? 'هذا الدرس مضاف كبث YouTube مباشر ويتطلب إنترنت لتشغيل الفيديو، ولكن ملزمته وأوراق عمله محفوظة بالكامل في جهازك ويمكنك دراستها أوفلاين.'
                        : 'يمكنك مطالعة ملزمة وأوراق عمل هذا الدرس المحفوظة بدون إنترنت.';
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
                playerModal.style.display = 'flex';
                if (footerBadge) footerBadge.innerHTML = '<i class="fa-solid fa-file-pdf"></i> ملزمة وأوراق عمل الدرس المحفوظة أوفلاين';
            } else {
                const ytShield = document.getElementById('vaultYtShield');
                if (ytShield) ytShield.style.display = 'none';
                if (playerVideo) { playerVideo.style.display = 'none'; playerVideo.pause(); }
                if (playerIframe) { playerIframe.style.display = 'none'; playerIframe.src = 'about:blank'; }
                if (fallbackWrap) {
                    fallbackWrap.style.display = 'flex';
                    if (fbIcon) fbIcon.className = 'fa-brands fa-youtube';
                    if (fbTitle) fbTitle.textContent = 'بث YouTube مباشر - يتطلب إنترنت 🌐';
                    if (fbDesc) fbDesc.textContent = 'هذا الدرس مدرج كبث فيديو من YouTube ويتطلب اتصالاً نشطاً بالإنترنت لمشاهدته. الدروس المرفوعة بصيغة MP4 هي فقط التي تعمل بدون نت 100% في وضع عدم الاتصال.';
                    if (btnOpenPdf) btnOpenPdf.style.display = 'none';
                }
                playerModal.style.display = 'flex';
                if (footerBadge) footerBadge.innerHTML = '<i class="fa-solid fa-wifi"></i> بث مباشر يتطلب الاتصال بالشبكة';
            }
        }).catch((err) => {
            console.error('Error fetching video for playback:', err);
        });
    }

    let isVaultYtPlaying = true;
    function toggleVaultYtPlayback() {
        const ifr = document.getElementById('offlineVaultIframePlayer');
        const playBtn = document.getElementById('vaultYtPlayBtn');
        const centerPlay = document.getElementById('vaultYtCenterPlay');
        if (!ifr || !ifr.contentWindow) return;

        if (isVaultYtPlaying) {
            ifr.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
            isVaultYtPlaying = false;
            if (playBtn) playBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
            if (centerPlay) centerPlay.style.display = 'flex';
        } else {
            ifr.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
            isVaultYtPlaying = true;
            if (playBtn) playBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
            if (centerPlay) centerPlay.style.display = 'none';
        }
    }

    function seekVaultYtRelative(seconds) {
        const ifr = document.getElementById('offlineVaultIframePlayer');
        if (!ifr || !ifr.contentWindow) return;
        // إرسال أمر التقديم والتأخير عبر API
        ifr.contentWindow.postMessage(JSON.stringify({
            event: 'command',
            func: seconds > 0 ? 'fastForward' : 'rewind',
            args: ''
        }), '*');
    }

    function toggleVaultYtFullscreen() {
        const wrap = document.getElementById('offlinePlayerMediaWrap');
        if (!wrap) return;
        if (!document.fullscreenElement) {
            wrap.requestFullscreen().catch(() => {});
        } else {
            document.exitFullscreen().catch(() => {});
        }
    }

    function closeOfflinePlayer(e) {
        if (e && e.target && e.target.closest('.stepvoro-offline-player-card') && !e.target.closest('.btn-close-sheet')) {
            return;
        }
        const playerModal = document.getElementById('stepvoroOfflinePlayerModal');
        const playerVideo = document.getElementById('offlineVaultVideoPlayer');
        const playerIframe = document.getElementById('offlineVaultIframePlayer');
        const ytShield = document.getElementById('vaultYtShield');
        if (ytShield) ytShield.style.display = 'none';

        if (playerVideo) {
            playerVideo.pause();
            playerVideo.removeAttribute('src');
            playerVideo.load();
        }
        if (playerIframe) {
            playerIframe.src = 'about:blank';
        }
        if (playerModal) playerModal.style.display = 'none';
    }

    function deleteFromVault(id) {
        const card = document.getElementById('vault_card_' + id);
        if (card) {
            card.style.opacity = '0.5';
            card.style.pointerEvents = 'none';
        }

        const finalizeDelete = () => {
            if (card) card.remove();
            renderModalOfflineVaultList();
            if (typeof showPwaToast === 'function') {
                showPwaToast('تم حذف الدرس من المحفوظات بنجاح', 'info');
            }
        };

        if (window.StepvoroOfflineDB) {
            StepvoroOfflineDB.deleteVideo(id).then(finalizeDelete).catch(() => {
                if (window.StepvoroVideoDownloader && typeof StepvoroVideoDownloader.removeOfflineVideo === 'function') {
                    StepvoroVideoDownloader.removeOfflineVideo(id);
                }
                finalizeDelete();
            });
        } else if (window.StepvoroVideoDownloader && typeof StepvoroVideoDownloader.removeOfflineVideo === 'function') {
            StepvoroVideoDownloader.removeOfflineVideo(id);
            finalizeDelete();
        } else {
            finalizeDelete();
        }
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

    function dismissOfflineNotice() {
        const bar = document.getElementById('stepvoroOfflineNoticeBar');
        if (bar) bar.style.display = 'none';
    }

    // الاستماع لحدث انقطاع وعودة الاتصال بالإنترنت بشكل حي ومباشر
    window.addEventListener('offline', function() {
        const bar = document.getElementById('stepvoroOfflineNoticeBar');
        if (bar) bar.style.display = 'block';
        if (typeof showPwaToast === 'function') {
            showPwaToast('تم تفعيل وضع عدم الاتصال • التطبيق يعمل من الذاكرة المحلية ⚡', 'info');
        }
    });

    window.addEventListener('online', function() {
        const bar = document.getElementById('stepvoroOfflineNoticeBar');
        if (bar) bar.style.display = 'none';
        if (typeof showPwaToast === 'function') {
            showPwaToast('تم استعادة الاتصال بالإنترنت بنجاح! 🎉', 'success');
        }
    });

    if (typeof navigator !== 'undefined' && !navigator.onLine) {
        const bar = document.getElementById('stepvoroOfflineNoticeBar');
        if (bar) bar.style.display = 'block';
    }

    // ربط الدوال الأساسية بنطاق النافذة العام لضمان استدعائها من أي مكان
    window.dismissOfflineNotice = dismissOfflineNotice;
    window.triggerPwaInstall = triggerPwaInstall;
    window.openInstallModal = openInstallModal;
    window.closeInstallModal = closeInstallModal;
    window.openOfflineVault = openOfflineVault;
    window.closeOfflineVault = closeOfflineVault;
    window.renderModalOfflineVaultList = renderModalOfflineVaultList;
    window.playOfflineVaultVideo = playOfflineVaultVideo;
    window.closeOfflinePlayer = closeOfflinePlayer;
    window.showPwaToast = showPwaToast;
</script>
