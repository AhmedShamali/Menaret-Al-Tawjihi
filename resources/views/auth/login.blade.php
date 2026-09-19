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

            --shadow-card: 0 4px 14px rgba(15, 23, 42, 0.05);
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
            font-size: 14px;
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

        /* الشريط العلوي الرفيع بنمط كلاسيكي فاتح */
        .top-info-bar {
            width: 100%;
            background-color: #f1f5f9;
            color: #475569;
            padding: 8px 32px;
            font-size: 12.5px;
            border-bottom: 1px solid var(--ed-border);
        }
        .top-bar-inner {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ترويسة الصفحة الرسمية الفاتحة الأكاديمية */
        .page-header {
            width: 100%;
            background: #ffffff;
            color: var(--ed-text-main);
            padding: 16px 32px;
            border-bottom: 1px solid var(--ed-border);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
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
            color: var(--ed-text-main);
            text-decoration: none;
        }
        .brand-logo-square {
            width: 46px;
            height: 46px;
            background: #eff6ff;
            color: var(--ed-primary);
            border-radius: var(--radius-sm);
            display: grid;
            place-items: center;
            font-size: 22px;
            border: 1px solid var(--ed-primary-border);
        }
        .brand-titles h1 {
            font-size: 19px;
            font-weight: 800;
            color: var(--ed-text-main);
            line-height: 1.2;
        }
        .brand-titles p {
            font-size: 12px;
            color: var(--ed-text-muted);
            margin-top: 2px;
        }
        .supervisor-pill {
            background: #f8fafc;
            border: 1px solid var(--ed-border);
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            color: var(--ed-text-body);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .supervisor-pill span {
            color: var(--ed-primary);
            font-weight: 700;
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
            background-color: #ffffff;
            color: var(--ed-text-main);
            padding: 20px 24px 16px;
            border-bottom: 1px solid var(--ed-border);
            text-align: center;
        }
        .auth-card-header h2 {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 4px;
            color: var(--ed-text-main);
        }
        .auth-card-header p {
            font-size: 13px;
            color: var(--ed-text-muted);
        }

        .auth-card-body {
            padding: 24px;
        }

        /* تبويبات اختيار نوع الحساب */
        .role-tabs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
            margin-bottom: 20px;
            background: var(--ed-surface-alt);
            padding: 4px;
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
            background: #ffffff;
            color: var(--ed-primary);
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.08);
            border: 1px solid var(--ed-border);
        }

        /* شريط توضيح نوع البوابة */
        .role-info-alert {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
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
            height: 42px;
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
            height: 44px;
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
            box-shadow: 0 1px 3px rgba(29, 78, 216, 0.2);
        }
        .btn-submit-login:hover {
            background-color: var(--ed-primary-hover);
            transform: translateY(-1px);
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
            color: var(--ed-primary);
            font-weight: 700;
            margin-inline-start: 4px;
        }
        .btn-to-register:hover {
            color: var(--ed-primary-hover);
            text-decoration: underline;
        }

        /* التذييل البسيط الفاتح */
        .auth-page-footer {
            background-color: #ffffff;
            color: var(--ed-text-muted);
            font-size: 12.5px;
            padding: 16px 20px;
            text-align: center;
            border-top: 1px solid var(--ed-border);
        }

        /* نافذة استعادة كلمة المرور */
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
            color: var(--ed-text-main);
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

    <!-- 1. الشريط العلوي الرفيع الفاتح -->
    <div class="top-info-bar">
        <div class="top-bar-inner">
            <span><i class="fa-regular fa-calendar-check text-primary"></i> {{ __('اليوم:') }} {{ date('Y/m/d') }}{{ app()->getLocale() === 'ar' ? ' م' : ' AD' }} • {{ __('بوابة ومنظومة الثانوية العامة لدولة فلسطين | المنهاج الوزاري المعتمد') }}</span>
            <div style="display: flex; align-items: center; gap: 14px;">
                @php $currentLocale = app()->getLocale(); @endphp
                <a href="{{ route('lang.switch', $currentLocale === 'ar' ? 'en' : 'ar') }}" 
                   title="{{ $currentLocale === 'ar' ? 'Switch to English' : 'Switch to Arabic' }}" 
                   style="background: #ffffff; border: 1px solid var(--ed-border); color: var(--ed-text-main); padding: 3px 10px; border-radius: var(--radius-sm); font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                    <i class="fa-solid fa-globe" style="color: var(--ed-primary);"></i>
                    <span>{{ $currentLocale === 'ar' ? 'EN' : 'AR' }}</span>
                </a>
                <a href="{{ route('home') }}" style="color: var(--ed-primary); font-weight: 600;">
                    <i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i> {{ __('العودة للرئيسية') }}
                </a>
            </div>
        </div>
    </div>

    <!-- 2. الترويسة الرسمية الموحدة الفاتحة -->
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
                <span>{{ __('المشرف العام على المنظومة:') }}</span> {{ __('أ. أحمد حسين شمالي') }}
            </div>
        </div>
    </header>

    <!-- 3. بطاقة تسجيل الدخول المركزية الفاتحة -->
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

                <!-- رابط إنشاء حساب جديد -->
                <div class="auth-card-footer" id="studentRegisterFooter">
                    <span>{{ __('ليس لديك حساب بعد؟') }}</span>
                    <a href="{{ route('students.create') }}" class="btn-to-register">{{ __('إنشاء حساب طالب جديد ←') }}</a>
                </div>

            </div>
        </div>
    </main>

    <!-- 4. تذييل الصفحة الفاتح المعتمد -->
    <footer class="auth-page-footer">
        {{ __('جميع الحقوق محفوظة ©') }} {{ date('Y') }} - {{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }} 🇵🇸 | {{ __('إشراف الأستاذ أحمد حسين شمالي') }}
    </footer>

    <!-- نافذة استعادة كلمة المرور المنبثقة -->
    <div class="modal-backdrop" id="forgotModal">
        <div class="modal-card">
            <div class="modal-head">
                <h3>{{ __('استعادة كلمة المرور الأكاديمية') }}</h3>
                <button type="button" class="modal-close-btn" onclick="closeForgotModal()">&times;</button>
            </div>
            
            <p style="font-size: 13px; color: var(--ed-text-muted); margin-bottom: 16px;">
                {{ __('أدخل بريدك الإلكتروني أو اسم المستخدم مع رقم الهوية الفلسطينية (9 أرقام) للتحقق ومطابقة الحساب.') }}
            </p>

            <form action="{{ route('password.forgot') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">{{ __('البريد الإلكتروني أو اسم المستخدم') }}</label>
                    <input type="text" name="email" class="form-control" placeholder="student@example.com" required style="padding-inline-start: 14px;">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('رقم الهوية الفلسطينية (9 أرقام)') }}</label>
                    <input type="text" name="nid" maxlength="9" pattern="\d{9}" class="form-control" placeholder="{{ __('401234567') }}" required style="padding-inline-start: 14px;">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('كلمة المرور الجديدة (اختياري)') }}</label>
                    <input type="password" name="new_password" minlength="6" class="form-control" placeholder="{{ __('اتركها فارغة أو اكتب الكلمة الجديدة') }}" style="padding-inline-start: 14px;">
                </div>

                <button type="submit" class="btn-submit-login" style="margin-top: 10px;">
                    <span>{{ __('التحقق وتعيين كلمة المرور') }}</span>
                </button>
            </form>

            <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid var(--ed-border); text-align: center;">
                <a href="https://wa.me/970597694385" target="_blank" style="color: var(--ed-success); font-weight: 700; font-size: 13px;">
                    <i class="fa-brands fa-whatsapp"></i> {{ __('تواصل مع المشرف العام للمساعدة الفورية') }}
                </a>
            </div>
        </div>
    </div>

    <!-- السكريبتات المعربة بالكامل ثنائية اللغة -->
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
