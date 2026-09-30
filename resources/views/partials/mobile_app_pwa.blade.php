{{-- =========================================================================
     Stepvoro PWA Mobile Application Engine & Native Navigation UI
     - Supports Android (One-click Native Install via beforeinstallprompt)
     - Supports iOS Safari (Native Add-to-Home-Screen Step-by-Step Guide)
     - Full offline Service Worker registration
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
    <a href="{{ route('smart.learning.flashcards') }}" class="nav-tab {{ request()->is('public-flashcards*') ? 'active' : '' }}">
        <div class="nav-tab-icon"><i class="fa-solid fa-clone"></i></div>
        <span class="nav-tab-label">{{ __('البطاقات') }}</span>
    </a>
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

/* نافذة إرشاد هواتف آيفون (iOS Bottom Sheet) */
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
}

.ios-sheet-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 20px;
}

.ios-app-icon {
    border-radius: 14px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
}

.ios-sheet-header h3 {
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
}

.ios-sheet-header p {
    font-size: 0.76rem;
    color: #64748b;
    margin: 0;
}

.btn-close-sheet {
    margin-right: auto;
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

.ios-steps-list {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 14px;
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.ios-step-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.step-num {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--pwa-primary);
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.step-text {
    font-size: 0.82rem;
    color: #1e293b;
    line-height: 1.5;
}

.ios-icon-hint {
    display: block;
    font-size: 0.74rem;
    color: #2563eb;
    margin-top: 3px;
    font-weight: 700;
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
                // فحص التحديثات تلقائياً
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
        
        // إذا لم يكن التطبيق مثبتاً ولم يغلق المستخدم البانر مؤخراً
        if (!isStandalone && !sessionStorage.getItem('stepvoro_pwa_dismissed')) {
            showPwaBanner();
        }
    });

    function showPwaBanner() {
        const banner = document.getElementById('stepvoroInstallBanner');
        if (banner) {
            banner.style.display = 'block';
        }
    }

    function dismissPwaBanner() {
        const banner = document.getElementById('stepvoroInstallBanner');
        if (banner) {
            banner.style.display = 'none';
        }
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
            // أجهزة أندرويد وكمبيوتر
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted the Stepvoro app install');
                    dismissPwaBanner();
                }
                deferredPrompt = null;
            });
        } else if (isIos) {
            // هواتف آيفون Safari
            openIosModal();
        } else {
            // في حال فتح الرابط من داخل متصفح لا يدعم beforeinstallprompt مباشرة
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

    // إخفاء خيارات التثبيت تلقائياً عند تشغيل التطبيق في وضع Standalone
    window.addEventListener('appinstalled', () => {
        dismissPwaBanner();
        console.log('Stepvoro App was installed successfully');
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
