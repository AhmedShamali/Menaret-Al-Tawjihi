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

    <title>{{ __('تسجيل الدخول') }} | {{ \App\Models\Setting::get('site_name', __('منارة التوجيهي')) }} 🇵🇸</title>

    <!-- الخطوط الموحدة للمنظومة (Alexandria & Tajawal) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --ed-primary: #1d4ed8;
            --ed-primary-dark: #1e3a8a;
            --ed-primary-deep: #0f172a;
            --ed-primary-hover: #1e40af;
            --ed-primary-soft: #eff6ff;
            --ed-primary-border: #bfdbfe;

            --ed-accent-gold: #f59e0b;
            --ed-accent-gold-dark: #d97706;

            --ed-success: #16a34a;
            --ed-success-hover: #15803d;

            --ed-bg: #f8fafc;
            --ed-surface: #ffffff;
            --ed-surface-alt: #f1f5f9;
            --ed-border: #e2e8f0;
            --ed-border-hover: #cbd5e1;

            --ed-text-main: #0f172a;
            --ed-text-body: #334155;
            --ed-text-muted: #64748b;

            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;

            --shadow-card: 0 4px 14px rgba(15, 23, 42, 0.08);
            --transition: all 0.2s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Alexandria', 'Tajawal', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--ed-bg);
            color: var(--ed-text-body);
            font-size: 14.5px;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        html[dir="rtl"] body {
            direction: rtl;
            text-align: right;
        }

        html[dir="ltr"] body {
            direction: ltr;
            text-align: left;
        }

        html[dir="ltr"] .input-wrap .input-icon {
            right: auto;
            left: 14px;
        }
        html[dir="ltr"] .input-wrap .form-control {
            padding-right: 14px;
            padding-left: 42px;
        }
        html[dir="ltr"] .input-wrap .toggle-pw-btn {
            left: auto;
            right: 12px;
        }

        a {
            color: var(--ed-primary);
            text-decoration: none;
            transition: var(--transition);
        }
        a:hover {
            color: var(--ed-primary-hover);
        }

        /* الشريط العلوي الرفيع */
        .top-info-bar {
            width: 100%;
            background-color: var(--ed-primary-deep);
            color: #cbd5e1;
            padding: 7px 32px;
            font-size: 12.5px;
            border-bottom: 1px solid #1e293b;
        }
        .top-bar-inner {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ترويسة الصفحة الرسمية الموحدة */
        .page-header {
            width: 100%;
            background: linear-gradient(135deg, #172554 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 16px 32px;
            border-bottom: 3px solid var(--ed-accent-gold);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.1);
        }
        .header-inner {
            width: 100%;
            max-width: 1200px;
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
            gap: 14px;
            color: #ffffff;
            text-decoration: none;
        }
        .brand-logo-square {
            width: 48px;
            height: 48px;
            background: #ffffff;
            color: var(--ed-primary-dark);
            border-radius: var(--radius-sm);
            display: grid;
            place-items: center;
            font-size: 24px;
            border: 2px solid var(--ed-accent-gold);
        }
        .brand-titles h1 {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
        }
        .brand-titles p {
            font-size: 12.5px;
            color: #bfdbfe;
            margin-top: 2px;
        }
        .supervisor-pill {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: #ffffff;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .supervisor-pill span {
            color: var(--ed-accent-gold);
        }

        /* الحاوية المركزية لبطاقة الدخول */
        .auth-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .auth-card {
            width: 100%;
            max-width: 460px;
            background: var(--ed-surface);
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }

        .auth-card-header {
            background-color: var(--ed-primary-dark);
            color: #ffffff;
            padding: 18px 24px;
            border-bottom: 3px solid var(--ed-accent-gold);
            text-align: center;
        }
        .auth-card-header h2 {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 4px;
        }
        .auth-card-header p {
            font-size: 13px;
            color: #bfdbfe;
        }

        .auth-card-body {
            padding: 24px;
        }

        /* تبويبات اختيار نوع الحساب */
        .role-tabs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
            margin-bottom: 22px;
            background: var(--ed-surface-alt);
            padding: 5px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--ed-border);
        }
        .role-tab-btn {
            background: transparent;
            border: none;
            padding: 8px 6px;
            border-radius: var(--radius-sm);
            font-size: 13px;
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
            background: var(--ed-primary);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(29, 78, 216, 0.2);
        }

        /* شريط توضيح نوع البوابة */
        .role-info-alert {
            background: var(--ed-primary-soft);
            border: 1px solid var(--ed-primary-border);
            color: var(--ed-primary-dark);
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* حقول النموذج */
        .form-group {
            margin-bottom: 16px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--ed-text-main);
            margin-bottom: 6px;
        }
        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-icon {
            position: absolute;
            right: 14px;
            color: var(--ed-text-muted);
            font-size: 15px;
            pointer-events: none;
        }
        .form-control {
            width: 100%;
            height: 44px;
            padding: 0 42px 0 14px;
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            color: var(--ed-text-main);
            background: #ffffff;
            transition: var(--transition);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--ed-primary);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }
        .toggle-pw-btn {
            position: absolute;
            left: 12px;
            background: none;
            border: none;
            color: var(--ed-text-muted);
            cursor: pointer;
            font-size: 14px;
            padding: 4px;
        }

        .form-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            color: var(--ed-text-body);
        }
        .forgot-link {
            font-weight: 600;
            color: var(--ed-primary);
        }

        /* زر الدخول الرئيسي */
        .btn-submit-login {
            width: 100%;
            height: 46px;
            background-color: var(--ed-primary);
            color: #ffffff;
            border: 1px solid var(--ed-primary-hover);
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
            box-shadow: 0 2px 6px rgba(29, 78, 216, 0.2);
        }
        .btn-submit-login:hover {
            background-color: var(--ed-primary-hover);
            transform: translateY(-1px);
        }

        /* زر جوجل */
        .google-auth-box {
            margin-top: 16px;
        }
        .divider-strip {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 16px 0;
            color: var(--ed-text-muted);
            font-size: 12px;
        }
        .divider-strip::before, .divider-strip::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--ed-border);
        }
        .divider-strip span {
            padding: 0 10px;
        }
        .btn-google-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            height: 44px;
            background: #ffffff;
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-sm);
            color: var(--ed-text-main);
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }
        .btn-google-login:hover {
            border-color: #4285F4;
            background: var(--ed-surface-alt);
        }

        /* أسفل البطاقة والتسجيل */
        .auth-card-footer {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--ed-border);
            text-align: center;
            font-size: 13px;
            color: var(--ed-text-muted);
        }
        .btn-to-register {
            color: var(--ed-success);
            font-weight: 700;
            margin-right: 4px;
        }
        .btn-to-register:hover {
            color: var(--ed-success-hover);
            text-decoration: underline;
        }

        /* التذييل البسيط */
        .auth-page-footer {
            background-color: var(--ed-primary-deep);
            color: #94a3b8;
            font-size: 12px;
            padding: 14px 20px;
            text-align: center;
            border-top: 1px solid #1e293b;
        }

        /* نافذة استعادة كلمة المرور */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 1000;
        }
        .modal-backdrop.active {
            display: flex;
        }
        .modal-card {
            background: #ffffff;
            border-radius: var(--radius-md);
            padding: 24px;
            max-width: 420px;
            width: 100%;
            box-shadow: var(--shadow-card);
            border: 1px solid var(--ed-border);
        }
        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--ed-border);
        }
        .modal-head h3 {
            font-size: 16px;
            font-weight: 800;
            color: var(--ed-primary-dark);
        }
        .modal-close-btn {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: var(--ed-text-muted);
        }

        @media (max-width: 640px) {
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

    <!-- 1. الشريط العلوي الرفيع -->
    <div class="top-info-bar">
        <div class="top-bar-inner">
            <span><i class="fa-regular fa-calendar-check text-warning"></i> {{ __('اليوم:') }} {{ date('Y/m/d') }} م • {{ __('بوابة ومنظومة الثانوية العامة لدولة فلسطين | المنهاج الوزاري المعتمد') }}</span>
            <div style="display: flex; align-items: center; gap: 14px;">
                @php $currentLocale = app()->getLocale(); @endphp
                <a href="{{ route('lang.switch', $currentLocale === 'ar' ? 'en' : 'ar') }}" 
                   title="{{ $currentLocale === 'ar' ? 'Switch to English' : 'التبديل إلى العربية' }}" 
                   style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); color: #ffffff; padding: 3px 10px; border-radius: var(--radius-sm); font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                    <i class="fa-solid fa-globe" style="color: var(--ed-accent-gold);"></i>
                    <span>{{ $currentLocale === 'ar' ? 'EN' : 'عربي' }}</span>
                </a>
                <a href="{{ route('home') }}" style="color: #93c5fd;"><i class="fa-solid fa-arrow-right"></i> {{ __('العودة للرئيسية') }}</a>
            </div>
        </div>
    </div>

    <!-- 2. الترويسة الرسمية الموحدة -->
    <header class="page-header">
        <div class="header-inner">
            <a href="{{ route('home') }}" class="brand-link">
                <div class="brand-logo-square">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="brand-titles">
                    <h1>{{ \App\Models\Setting::get('site_name', __('منارة التوجيهي')) }} 🇵🇸</h1>
                    <p>{{ __('بوابة ومنظومة الثانوية العامة لدولة فلسطين | المنهاج الوزاري المعتمد') }}</p>
                </div>
            </a>

            <div class="supervisor-pill">
                <span>{{ __('المشرف العام على المنظومة:') }}</span> {{ __('أ. أحمد حسين شمالي') }}
            </div>
        </div>
    </header>

    <!-- 3. بطاقة تسجيل الدخول المركزية -->
    <main class="auth-container">
        <div class="auth-card">
            <div class="auth-card-header">
                <h2>{{ __('تسجيل الدخول للمنظومة') }}</h2>
                <p>{{ __('أدخل بيانات اعتمادك للمتابعة الأكاديمية') }}</p>
            </div>

            <div class="auth-card-body">

                <!-- أزرار تبديل نوع الحساب -->
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

                <!-- شريط توضيحي للدور -->
                <div class="role-info-alert" id="roleAlertBox">
                    <i class="fa-solid fa-circle-info" id="roleIcon"></i>
                    <span id="roleText">{{ __('بوابة دخول الطلبة — أهلاً بك لمتابعة مساقاتك واختباراتك اليومية.') }}</span>
                </div>

                <!-- تنبيهات الأخطاء -->
                @if($errors->any())
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('success'))
                    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px;">
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

                <!-- خيار الدخول السريع عبر Google للطلبة -->
                <div id="googleAuthSection" class="google-auth-box">
                    <div class="divider-strip">
                        <span>{{ __('أو المتابعة السريعة عبر') }}</span>
                    </div>
                    <a href="{{ route('auth.google') }}" class="btn-google-login">
                        <svg width="18" height="18" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>{{ __('الدخول بحساب Google') }}</span>
                    </a>
                </div>

                <!-- رابط إنشاء حساب جديد -->
                <div class="auth-card-footer" id="studentRegisterFooter">
                    <span>{{ __('ليس لديك حساب بعد؟') }}</span>
                    <a href="{{ route('students.create') }}" class="btn-to-register">{{ __('إنشاء حساب طالب جديد ←') }}</a>
                </div>

            </div>
        </div>
    </main>

    <!-- 4. تذييل الصفحة -->
    <footer class="auth-page-footer">
        جميع الحقوق محفوظة © {{ date('Y') }} - {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸 | إشراف الأستاذ أحمد حسين شمالي
    </footer>

    <!-- نافذة استعادة كلمة المرور المنبثقة -->
    <div class="modal-backdrop" id="forgotModal">
        <div class="modal-card">
            <div class="modal-head">
                <h3>استعادة كلمة المرور الأكاديمية</h3>
                <button type="button" class="modal-close-btn" onclick="closeForgotModal()">&times;</button>
            </div>
            
            <p style="font-size: 13px; color: var(--ed-text-muted); margin-bottom: 16px;">
                أدخل بريدك الإلكتروني أو اسم المستخدم مع رقم الهوية الفلسطينية (9 أرقام) للتحقق ومطابقة الحساب.
            </p>

            <form action="{{ route('password.forgot') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني أو اسم المستخدم</label>
                    <input type="text" name="email" class="form-control" placeholder="student@example.com" required style="padding-right: 14px;">
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهوية الفلسطينية (9 أرقام)</label>
                    <input type="text" name="nid" maxlength="9" pattern="\d{9}" class="form-control" placeholder="401234567" required style="padding-right: 14px;">
                </div>

                <div class="form-group">
                    <label class="form-label">كلمة المرور الجديدة (اختياري)</label>
                    <input type="password" name="new_password" minlength="6" class="form-control" placeholder="اتركها فارغة أو اكتب الكلمة الجديدة" style="padding-right: 14px;">
                </div>

                <button type="submit" class="btn-submit-login" style="margin-top: 10px;">
                    <span>التحقق وتعيين كلمة المرور</span>
                </button>
            </form>

            <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid var(--ed-border); text-align: center;">
                <a href="https://wa.me/970597694385" target="_blank" style="color: var(--ed-success); font-weight: 700; font-size: 13px;">
                    <i class="fa-brands fa-whatsapp"></i> تواصل مع المشرف العام للمساعدة الفورية
                </a>
            </div>
        </div>
    </div>

    <!-- السكريبتات -->
    <script>
        function switchRole(role) {
            document.querySelectorAll('.role-tab-btn').forEach(btn => btn.classList.remove('active'));
            const tab = document.querySelector(`.role-tab-btn[data-role="${role}"]`);
            if (tab) tab.classList.add('active');

            document.getElementById('role_input').value = role;

            const emailInput = document.getElementById('email_field');
            const submitLabel = document.getElementById('submitLabel');
            const googleBox = document.getElementById('googleAuthSection');
            const regBox = document.getElementById('studentRegisterFooter');
            const roleText = document.getElementById('roleText');
            const roleIcon = document.getElementById('roleIcon');

            if (role === 'student') {
                emailInput.placeholder = 'student@example.com';
                submitLabel.innerText = 'تسجيل الدخول كطالب';
                if (googleBox) googleBox.style.display = 'block';
                if (regBox) regBox.style.display = 'block';
                roleIcon.className = 'fa-solid fa-user-graduate';
                roleText.innerText = 'بوابة دخول الطلبة — أهلاً بك لمتابعة مساقاتك واختباراتك اليومية.';
            } else if (role === 'teacher') {
                emailInput.placeholder = 'teacher@menaret-tawjihi.ps';
                submitLabel.innerText = 'الدخول لبوابة المعلمين';
                if (googleBox) googleBox.style.display = 'none';
                if (regBox) regBox.style.display = 'none';
                roleIcon.className = 'fa-solid fa-chalkboard-user';
                roleText.innerText = 'بوابة الكادر التعليمي — إدارة المقررات والامتحانات ورصد درجات الطلبة.';
            } else if (role === 'admin') {
                emailInput.placeholder = 'admin@menaret-tawjihi.ps';
                submitLabel.innerText = 'الدخول للوحة الإدارة';
                if (googleBox) googleBox.style.display = 'none';
                if (regBox) regBox.style.display = 'none';
                roleIcon.className = 'fa-solid fa-shield-halved';
                roleText.innerText = 'بوابة الإدارة المركزية — الإشراف الأكاديمي واعتماد الاشتراكات والإعدادات.';
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
