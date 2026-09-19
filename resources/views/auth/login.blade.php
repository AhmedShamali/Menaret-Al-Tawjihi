<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(\App\Models\Setting::get('site_favicon'))
        <link rel="icon" href="{{ asset(\App\Models\Setting::get('site_favicon')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @endif

    <title>{{ __('تسجيل الدخول') }} | {{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }} 🇵🇸</title>

    <!-- الخطوط الموحدة للمنظومة (Tajawal & Alexandria) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800;900&family=Alexandria:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ==========================================================================
           التصميم الأكاديمي الكلاسيكي الرصين - بوابة الدخول الموحد (Classic Academic Portal)
           - إطار كلاسيكي مزدوج رصين (Two-Column Academic Portal Frame)
           - فواتح بالكامل بدون كتل داكنة ضخمة ولا فراغات عشوائية
           - خطوط واضحة وصغيرة 13-14px بأسلوب بوابات الجامعات الكبرى
           ========================================================================== */
        :root {
            --ed-primary: #1d4ed8;
            --ed-primary-dark: #1e3a8a;
            --ed-primary-hover: #1e40af;
            --ed-primary-soft: #eff6ff;
            --ed-primary-border: #bfdbfe;

            --ed-accent-gold: #b45309;
            --ed-accent-gold-soft: #fef3c7;
            --ed-accent-gold-border: #fde68a;

            --ed-success: #16a34a;
            --ed-success-hover: #15803d;
            --ed-success-soft: #ecfdf5;
            --ed-success-border: #a7f3d0;

            --ed-bg: #f8fafc;
            --ed-surface: #ffffff;
            --ed-surface-alt: #f1f5f9;
            --ed-border: #cbd5e1;
            --ed-border-subtle: #e2e8f0;

            --ed-text-main: #0f172a;
            --ed-text-body: #334155;
            --ed-text-muted: #64748b;

            --radius-sm: 6px;
            --radius-md: 8px;

            --shadow-portal: 0 4px 20px rgba(15, 23, 42, 0.08), 0 1px 3px rgba(15, 23, 42, 0.04);
            --transition: all 0.2s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', 'Alexandria', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--ed-bg);
            color: var(--ed-text-body);
            font-size: 13.5px;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        html[dir="rtl"] body { direction: rtl; text-align: right; }
        html[dir="ltr"] body { direction: ltr; text-align: left; }

        html[dir="ltr"] .input-wrap .input-icon { right: auto; left: 14px; }
        html[dir="ltr"] .input-wrap .form-control { padding-right: 14px; padding-left: 42px; }
        html[dir="ltr"] .input-wrap .toggle-pw-btn { left: auto; right: 12px; }

        a {
            color: var(--ed-primary);
            text-decoration: none;
            transition: var(--transition);
        }
        a:hover { color: var(--ed-primary-hover); }

        /* 1. الشريط العلوي الرفيع للمنظومة */
        .top-info-bar {
            width: 100%;
            background-color: #ffffff;
            color: var(--ed-text-muted);
            padding: 7px 32px;
            font-size: 12px;
            border-bottom: 1px solid var(--ed-border-subtle);
        }
        .top-bar-inner {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .top-bar-meta {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* 2. الترويسة الأكاديمية الكلاسيكية الفاتحة */
        .page-header {
            width: 100%;
            background: #ffffff;
            color: var(--ed-text-main);
            padding: 14px 32px;
            border-bottom: 1px solid var(--ed-border-subtle);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
        }
        .header-inner {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
        }
        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--ed-text-main);
            text-decoration: none;
        }
        .brand-logo-square {
            width: 44px;
            height: 44px;
            background: var(--ed-primary-soft);
            color: var(--ed-primary);
            border-radius: var(--radius-sm);
            display: grid;
            place-items: center;
            font-size: 22px;
            border: 1px solid var(--ed-primary-border);
            flex-shrink: 0;
        }
        .brand-titles h1 {
            font-size: 18px;
            font-weight: 800;
            color: var(--ed-text-main);
            line-height: 1.25;
        }
        .brand-titles p {
            font-size: 12px;
            color: var(--ed-text-muted);
            margin-top: 1px;
            font-weight: 500;
        }
        .supervisor-pill {
            background: #f8fafc;
            border: 1px solid var(--ed-border-subtle);
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            color: var(--ed-text-body);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .supervisor-pill strong {
            color: var(--ed-primary-dark);
            font-weight: 700;
        }

        /* 3. الحاوية العامة للإطار الأكاديمي الكلاسيكي */
        .auth-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 36px 20px;
        }
        .classic-portal-frame {
            width: 100%;
            max-width: 1020px;
            background: #ffffff;
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-portal);
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
        }

        /* الجانب الأيمن: اللوحة التعريفية الأكاديمية (Academic Panel) */
        .portal-info-panel {
            background-color: #f8fafc;
            border-inline-end: 1px solid var(--ed-border-subtle);
            padding: 34px 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .panel-brand-box {
            margin-bottom: 22px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--ed-border-subtle);
        }
        .panel-session-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            border: 1px solid var(--ed-primary-border);
            color: var(--ed-primary-dark);
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11.5px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .panel-title {
            font-size: 17px;
            font-weight: 800;
            color: var(--ed-text-main);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .panel-desc {
            font-size: 12.5px;
            color: var(--ed-text-muted);
            line-height: 1.65;
        }

        /* قائمة التعليمات الأكاديمية */
        .portal-instructions-list {
            list-style: none;
            padding: 0;
            margin: 0 0 24px 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .instruction-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12.5px;
            color: var(--ed-text-body);
            line-height: 1.55;
        }
        .instruction-icon {
            width: 22px;
            height: 22px;
            border-radius: 4px;
            background: #ffffff;
            border: 1px solid var(--ed-border);
            color: var(--ed-primary);
            display: grid;
            place-items: center;
            font-size: 11px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* بطاقة التواصل المباشر مع الإشراف */
        .supervisor-contact-box {
            background: #ffffff;
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-sm);
            padding: 14px;
        }
        .supervisor-box-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--ed-text-main);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-whatsapp-compact {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            background-color: var(--ed-success);
            color: #ffffff !important;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: var(--transition);
        }
        .btn-whatsapp-compact:hover {
            background-color: var(--ed-success-hover);
        }
        .alt-contact-text {
            font-size: 11px;
            color: var(--ed-text-muted);
            text-align: center;
            margin-top: 6px;
        }

        /* الجانب الأيسر: نموذج تسجيل الدخول الكلاسيكي (Form Panel) */
        .portal-form-panel {
            background-color: #ffffff;
            padding: 34px 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-head {
            margin-bottom: 18px;
        }
        .form-head h2 {
            font-size: 18px;
            font-weight: 800;
            color: var(--ed-text-main);
            margin-bottom: 4px;
        }
        .form-head p {
            font-size: 12.5px;
            color: var(--ed-text-muted);
        }

        /* أزرار اختيار الصفة الأكاديمية (Segmented Tabs) */
        .role-tabs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 6px;
            margin-bottom: 16px;
            background: var(--ed-surface-alt);
            padding: 4px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--ed-border);
        }
        .role-tab-btn {
            background: transparent;
            border: 1px solid transparent;
            padding: 8px 6px;
            border-radius: 4px;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ed-text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: var(--transition);
        }
        .role-tab-btn.active {
            background: var(--ed-primary-dark);
            color: #ffffff;
            border-color: var(--ed-primary-dark);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.15);
        }

        /* تنبيه الدور */
        .role-info-alert {
            background: var(--ed-primary-soft);
            border: 1px solid var(--ed-primary-border);
            color: var(--ed-primary-dark);
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* الحقول الكلاسيكية */
        .form-group {
            margin-bottom: 14px;
        }
        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ed-text-main);
            margin-bottom: 5px;
        }
        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-icon {
            position: absolute;
            right: 12px;
            color: var(--ed-text-muted);
            font-size: 14px;
            pointer-events: none;
        }
        .form-control {
            width: 100%;
            height: 40px;
            padding: 0 38px 0 12px;
            border: 1px solid var(--ed-border);
            border-radius: 4px;
            font-size: 13px;
            color: var(--ed-text-main);
            background: #ffffff;
            transition: var(--transition);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--ed-primary);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }
        .toggle-pw-btn {
            position: absolute;
            left: 10px;
            background: none;
            border: none;
            color: var(--ed-text-muted);
            cursor: pointer;
            font-size: 13px;
            padding: 4px;
        }

        .form-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            font-size: 12px;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            color: var(--ed-text-body);
            font-weight: 500;
        }
        .forgot-link {
            font-weight: 700;
            color: var(--ed-primary);
        }

        /* زر الدخول الرسمي الكلاسيكي */
        .btn-submit-login {
            width: 100%;
            height: 42px;
            background-color: var(--ed-primary);
            color: #ffffff;
            border: 1px solid var(--ed-primary-hover);
            border-radius: 4px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
        }
        .btn-submit-login:hover {
            background-color: var(--ed-primary-hover);
        }

        /* صندوق تسجيل طالب جديد */
        .new-student-box {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid var(--ed-border-subtle);
            text-align: center;
            font-size: 12.5px;
            color: var(--ed-text-muted);
        }
        .btn-to-register {
            color: var(--ed-primary);
            font-weight: 700;
            margin-inline-start: 4px;
        }
        .btn-to-register:hover {
            color: var(--ed-primary-hover);
            text-decoration: underline;
        }

        /* التذييل الفاتح الرسمي */
        .auth-page-footer {
            background-color: #ffffff;
            color: var(--ed-text-muted);
            font-size: 12px;
            padding: 14px 20px;
            text-align: center;
            border-top: 1px solid var(--ed-border-subtle);
        }

        /* نافذة استعادة كلمة المرور الكلاسيكية */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 1000;
        }
        .modal-backdrop.active { display: flex; }
        .modal-card {
            background: #ffffff;
            border-radius: var(--radius-sm);
            padding: 24px;
            max-width: 420px;
            width: 100%;
            box-shadow: var(--shadow-portal);
            border: 1px solid var(--ed-border);
        }
        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--ed-border-subtle);
        }
        .modal-head h3 {
            font-size: 15px;
            font-weight: 800;
            color: var(--ed-text-main);
        }
        .modal-close-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--ed-text-muted);
        }

        @media (max-width: 860px) {
            .classic-portal-frame {
                grid-template-columns: 1fr;
            }
            .portal-info-panel {
                border-inline-end: none;
                border-bottom: 1px solid var(--ed-border-subtle);
                padding: 24px 20px;
            }
            .portal-form-panel {
                padding: 24px 20px;
            }
            .page-header, .top-info-bar {
                padding-left: 16px;
                padding-right: 16px;
            }
            .header-inner {
                flex-direction: column;
                text-align: center;
            }
            .brand-link {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <!-- 1. الشريط العلوي الرفيع: التاريخ والتقويم الأكاديمي المعتمد -->
    <div class="top-info-bar">
        <div class="top-bar-inner">
            <div class="top-bar-meta">
                <span><i class="fa-regular fa-calendar-check" style="color: var(--ed-accent-gold);"></i> {{ date('Y/m/d') }}{{ app()->getLocale() === 'ar' ? ' م' : ' AD' }}</span>
                <span>•</span>
                <span>{{ __('المنهاج الفلسطيني المعتمد - دورة') }} {{ \App\Models\Setting::tawjihiSession() }} ({{ \App\Models\Setting::academicYear() }}{{ app()->getLocale() === 'ar' ? ' م' : ' AD' }})</span>
            </div>
            <div class="top-bar-actions">
                @php $currentLocale = app()->getLocale(); @endphp
                <a href="{{ route('lang.switch', $currentLocale === 'ar' ? 'en' : 'ar') }}" 
                   title="{{ $currentLocale === 'ar' ? 'Switch to English' : 'Switch to Arabic' }}" 
                   style="background: #ffffff; border: 1px solid var(--ed-border); color: var(--ed-text-main); padding: 3px 9px; border-radius: 4px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; text-decoration: none;">
                    <i class="fa-solid fa-globe" style="color: var(--ed-primary);"></i>
                    <span>{{ $currentLocale === 'ar' ? 'EN' : 'AR' }}</span>
                </a>
                <a href="{{ route('home') }}" style="color: var(--ed-primary); font-weight: 600; font-size: 12px;">
                    <i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i> {{ __('العودة للرئيسية') }}
                </a>
            </div>
        </div>
    </div>

    <!-- 2. الترويسة الأكاديمية الكلاسيكية الفاتحة -->
    <header class="page-header">
        <div class="header-inner">
            <a href="{{ route('home') }}" class="brand-link">
                <div class="brand-logo-square">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="brand-titles">
                    <h1>{{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }} 🇵🇸</h1>
                    <p>{{ __('بوابة ومنظومة الثانوية العامة لدولة فلسطين | المنهاج الوزاري المعتمد') }}</p>
                </div>
            </a>

            <div class="supervisor-pill">
                <span>{{ __('المشرف العام على المنظومة:') }}</span> <strong>{{ __('أ. أحمد حسين شمالي') }}</strong>
            </div>
        </div>
    </header>

    <!-- 3. الإطار الأكاديمي الكلاسيكي المزدوج لتسجيل الدخول -->
    <main class="auth-container">
        <div class="classic-portal-frame">

            <!-- الجانب الأيمن: اللوحة التعريفية الأكاديمية الكلاسيكية -->
            <div class="portal-info-panel">
                <div>
                    <div class="panel-brand-box">
                        <span class="panel-session-badge">
                            <i class="fa-solid fa-award" style="color: var(--ed-accent-gold);"></i>
                            {{ __('العام الدراسي:') }} {{ \App\Models\Setting::academicYear() }} • {{ __('دورة') }} {{ \App\Models\Setting::tawjihiSession() }}
                        </span>
                        <h3 class="panel-title">
                            <i class="fa-solid fa-building-columns" style="color: var(--ed-primary);"></i>
                            {{ __('بوابة الدخول الموحد (SSO)') }}
                        </h3>
                        <p class="panel-desc">
                            {{ __('نظام أكاديمي معتمد لخدمة طلبة وكادر الثانوية العامة في فلسطين (القدس، الضفة الغربية، وقطاع غزة). يتيح الوصول المباشر للشروحات والاختبارات والمتابعة الدراسية.') }}
                        </p>
                    </div>

                    <!-- تعليمات الدخول والاستخدام -->
                    <ul class="portal-instructions-list">
                        <li class="instruction-item">
                            <div class="instruction-icon"><i class="fa-solid fa-id-card"></i></div>
                            <div><strong>{{ __('الدخول المعتمد:') }}</strong> {{ __('متاح بحساب الطالب المعتمد أو البريد الإلكتروني وكلمة المرور.') }}</div>
                        </li>
                        <li class="instruction-item">
                            <div class="instruction-icon"><i class="fa-solid fa-key"></i></div>
                            <div><strong>{{ __('استعادة كلمة المرور:') }}</strong> {{ __('متاحة فوراً برقم الهوية الفلسطينية (9 أرقام) دون الحاجة للانتظار.') }}</div>
                        </li>
                        <li class="instruction-item">
                            <div class="instruction-icon"><i class="fa-solid fa-shield-check"></i></div>
                            <div><strong>{{ __('الاعتماد الأكاديمي:') }}</strong> {{ __('يتم تفعيل مواد واشتراكات الطلاب بإشراف إدارة المنظومة مباشرة.') }}</div>
                        </li>
                    </ul>
                </div>

                <!-- بطاقة الدعم المباشر عبر واتساب الإشراف -->
                <div class="supervisor-contact-box">
                    <div class="supervisor-box-title">
                        <i class="fa-solid fa-headset" style="color: var(--ed-primary);"></i>
                        {{ __('الدعم الفني والأكاديمي المباشر') }}
                    </div>
                    <a href="https://wa.me/970597694385" target="_blank" class="btn-whatsapp-compact">
                        <i class="fa-brands fa-whatsapp"></i> {{ __('واتساب الإشراف العام: 0597694385') }}
                    </a>
                    <div class="alt-contact-text">
                        {{ __('خط اتصال بديل:') }} 0567897212 • {{ __('دولة فلسطين 🇵🇸') }}
                    </div>
                </div>
            </div>

            <!-- الجانب الأيسر: استمارة الدخول الرسمية الكلاسيكية -->
            <div class="portal-form-panel">
                <div class="form-head">
                    <h2>{{ __('تسجيل الدخول للمنظومة') }}</h2>
                    <p>{{ __('أدخل بيانات اعتمادك للمتابعة الأكاديمية') }}</p>
                </div>

                <!-- تبويبات اختيار نوع الحساب الكلاسيكية -->
                <div class="role-tabs-grid">
                    <button type="button" class="role-tab-btn active" data-role="student" onclick="switchRole('student')">
                        <i class="fa-solid fa-user-graduate"></i> {{ __('طالب') }}
                    </button>
                    <button type="button" class="role-tab-btn" data-role="teacher" onclick="switchRole('teacher')">
                        <i class="fa-solid fa-chalkboard-user"></i> {{ __('معلم') }}
                    </button>
                    <button type="button" class="role-tab-btn" data-role="admin" onclick="switchRole('admin')">
                        <i class="fa-solid fa-shield-halved"></i> {{ __('إدارة') }}
                    </button>
                </div>

                <!-- شريط توضيح نوع البوابة -->
                <div class="role-info-alert" id="roleAlertBox">
                    <i class="fa-solid fa-circle-info" id="roleIcon"></i>
                    <span id="roleText">{{ __('بوابة دخول الطلبة — أهلاً بك لمتابعة مساقاتك واختباراتك اليومية.') }}</span>
                </div>

                <!-- تنبيهات الأخطاء -->
                @if($errors->any())
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 9px 12px; border-radius: 4px; font-size: 12.5px; margin-bottom: 14px;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('success'))
                    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 9px 12px; border-radius: 4px; font-size: 12.5px; margin-bottom: 14px;">
                        <i class="fa-solid fa-circle-check me-1"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- نموذج الدخول -->
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" id="role_input" value="student">

                    <div class="form-group">
                        <label class="form-label" id="usernameLabel">{{ __('البريد الإلكتروني أو اسم المستخدم') }}</label>
                        <div class="input-wrap">
                            <i class="fa-regular fa-envelope input-icon"></i>
                            <input type="text" name="email" id="email_field" class="form-control" placeholder="student@example.com" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('كلمة المرور') }}</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" name="password" id="password_field" class="form-control" placeholder="••••••••" required>
                            <button type="button" class="toggle-pw-btn" onclick="togglePasswordVisibility()" aria-label="{{ __('كلمة المرور') }}">
                                <i class="fa-regular fa-eye" id="eye_icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-meta-row">
                        <label class="remember-label">
                            <input type="checkbox" name="remember" value="1">
                            <span>{{ __('تذكرني على هذا الجهاز') }}</span>
                        </label>
                        <a href="javascript:void(0)" onclick="openForgotModal()" class="forgot-link">{{ __('نسيت كلمة المرور؟') }}</a>
                    </div>

                    <button type="submit" class="btn-submit-login" id="submitBtn">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span id="submitLabel">{{ __('تسجيل الدخول كطالب') }}</span>
                    </button>
                </form>

                <!-- رابط إنشاء حساب طالب جديد -->
                <div class="new-student-box" id="studentRegisterFooter">
                    <span>{{ __('ليس لديك حساب بعد؟') }}</span>
                    <a href="{{ route('students.create') }}" class="btn-to-register">
                        {{ __('إنشاء حساب طالب جديد لدورة :session ←', ['session' => \App\Models\Setting::tawjihiSession()]) }}
                    </a>
                </div>
            </div>

        </div>
    </main>

    <!-- 4. تذييل الصفحة الفاتح الكلاسيكي المعتمد -->
    <footer class="auth-page-footer">
        {{ __('جميع الحقوق محفوظة © :year - :site_name 🇵🇸 • العام الأكاديمي :academic م | إشراف الأستاذ أحمد حسين شمالي', [
            'year' => date('Y'),
            'site_name' => __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')),
            'academic' => \App\Models\Setting::academicYear()
        ]) }}
    </footer>

    <!-- نافذة استعادة كلمة المرور الكلاسيكية -->
    <div class="modal-backdrop" id="forgotModal">
        <div class="modal-card">
            <div class="modal-head">
                <h3>{{ __('استعادة كلمة المرور الأكاديمية') }}</h3>
                <button type="button" class="modal-close-btn" onclick="closeForgotModal()">&times;</button>
            </div>
            
            <p style="font-size: 12.5px; color: var(--ed-text-muted); margin-bottom: 14px;">
                {{ __('أدخل بريدك الإلكتروني أو اسم المستخدم مع رقم الهوية الفلسطينية (9 أرقام) للتحقق ومطابقة الحساب.') }}
            </p>

            <form action="{{ route('password.forgot') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">{{ __('البريد الإلكتروني أو اسم المستخدم') }}</label>
                    <input type="text" name="email" class="form-control" placeholder="student@example.com" required style="padding-inline-start: 12px;">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('رقم الهوية الفلسطينية (9 أرقام)') }}</label>
                    <input type="text" name="nid" maxlength="9" pattern="\d{9}" class="form-control" placeholder="{{ __('401234567') }}" required style="padding-inline-start: 12px;">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('كلمة المرور الجديدة (اختياري)') }}</label>
                    <input type="password" name="new_password" minlength="6" class="form-control" placeholder="{{ __('اتركها فارغة أو اكتب الكلمة الجديدة') }}" style="padding-inline-start: 12px;">
                </div>

                <button type="submit" class="btn-submit-login" style="margin-top: 10px;">
                    <span>{{ __('التحقق وتعيين كلمة المرور') }}</span>
                </button>
            </form>

            <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--ed-border-subtle); text-align: center;">
                <a href="https://wa.me/970597694385" target="_blank" style="color: var(--ed-success); font-weight: 700; font-size: 12.5px;">
                    <i class="fa-brands fa-whatsapp"></i> {{ __('تواصل مع المشرف العام للمساعدة الفورية') }}
                </a>
            </div>
        </div>
    </div>

    <!-- السكريبتات المعربة وثنائية اللغة -->
    <script>
        const i18n = {
            studentSubmit: "{{ __('تسجيل الدخول كطالب') }}",
            teacherSubmit: "{{ __('الدخول لبوابة المعلمين') }}",
            adminSubmit: "{{ __('الدخول للوحة الإدارة') }}",
            studentText: "{{ __('بوابة دخول الطلبة — أهلاً بك لمتابعة مساقاتك واختباراتك اليومية.') }}",
            teacherText: "{{ __('بوابة الكادر التعليمي — إدارة المقررات والامتحانات ورصد درجات الطلبة.') }}",
            adminText: "{{ __('بوابة الإدارة المركزية — الإشراف الأكاديمي واعتماد الاشتراكات والإعدادات.') }}"
        };

        function switchRole(role) {
            document.querySelectorAll('.role-tab-btn').forEach(btn => btn.classList.remove('active'));
            const tab = document.querySelector(`.role-tab-btn[data-role="${role}"]`);
            if (tab) tab.classList.add('active');

            document.getElementById('role_input').value = role;

            const emailInput = document.getElementById('email_field');
            const submitLabel = document.getElementById('submitLabel');
            const regBox = document.getElementById('studentRegisterFooter');
            const roleText = document.getElementById('roleText');
            const roleIcon = document.getElementById('roleIcon');

            if (role === 'student') {
                emailInput.placeholder = 'student@example.com';
                submitLabel.innerText = i18n.studentSubmit;
                if (regBox) regBox.style.display = 'block';
                roleIcon.className = 'fa-solid fa-user-graduate';
                roleText.innerText = i18n.studentText;
            } else if (role === 'teacher') {
                emailInput.placeholder = 'teacher@menaret-tawjihi.ps';
                submitLabel.innerText = i18n.teacherSubmit;
                if (regBox) regBox.style.display = 'none';
                roleIcon.className = 'fa-solid fa-chalkboard-user';
                roleText.innerText = i18n.teacherText;
            } else if (role === 'admin') {
                emailInput.placeholder = 'admin@menaret-tawjihi.ps';
                submitLabel.innerText = i18n.adminSubmit;
                if (regBox) regBox.style.display = 'none';
                roleIcon.className = 'fa-solid fa-shield-halved';
                roleText.innerText = i18n.adminText;
            }
        }

        function togglePasswordVisibility() {
            const input = document.getElementById('password_field');
            const icon = document.getElementById('eye_icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye';
            }
        }

        function openForgotModal() {
            document.getElementById('forgotModal').classList.add('active');
        }

        function closeForgotModal() {
            document.getElementById('forgotModal').classList.remove('active');
        }

        window.addEventListener('click', function(e) {
            const modal = document.getElementById('forgotModal');
            if (e.target === modal) closeForgotModal();
        });
    </script>
</body>
</html>
