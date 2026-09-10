<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(\App\Models\Setting::get('site_favicon'))
        <link rel="icon" href="{{ asset(\App\Models\Setting::get('site_favicon')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @endif

    <title>تسجيل الدخول | {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸</title>

    <!-- Google Fonts: Readex Pro (العربي الهندسي فائق النقاء) + Plus Jakarta Sans (للأرقام والرموز) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Readex+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            /* درجات الألوان الأساسية */
            --primary: #1d4ed8;
            --primary-hover: #1e40af;
            --primary-light: #eff6ff;
            --primary-glow: rgba(29, 78, 216, 0.18);

            --accent-teacher: #0284c7;
            --accent-teacher-light: #f0f9ff;
            --accent-admin: #0f172a;
            --accent-admin-light: #f1f5f9;

            --bg-page: #f8fafc;
            --surface: #ffffff;
            --surface-subtle: #f8fafc;
            --border-default: #e2e8f0;
            --border-focused: #2563eb;

            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --text-dim: #94a3b8;

            --success: #059669;
            --success-bg: #ecfdf5;
            --success-border: #a7f3d0;

            --danger: #dc2626;
            --danger-bg: #fef2f2;
            --danger-border: #fecaca;

            --radius-box: 14px;
            --radius-card: 24px;
            --shadow-card: 0 20px 40px -15px rgba(15, 23, 42, 0.07), 0 0 0 1px rgba(226, 232, 240, 0.8);
            --shadow-card-hover: 0 25px 50px -12px rgba(15, 23, 42, 0.12);

            --transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* أنماط الوضع الليلي الهادئ (Dark Mode) */
        body.dark-mode {
            --bg-page: #090d16;
            --surface: #0f172a;
            --surface-subtle: #1e293b;
            --border-default: #1e293b;
            --border-focused: #3b82f6;

            --text-heading: #f8fafc;
            --text-body: #cbd5e1;
            --text-muted: #94a3b8;
            --text-dim: #64748b;

            --shadow-card: 0 20px 40px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(30, 41, 59, 0.8);
            --primary-light: rgba(37, 99, 235, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Readex Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 32px 20px;
            position: relative;
            overflow-x: hidden;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* خلفية جمالية هادئة ثلاثية الأبعاد بدون تشويش (Calm Ambient Glow) */
        .ambient-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .ambient-orb-1 {
            position: absolute;
            top: -10%;
            right: 15%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.12) 0%, rgba(37, 99, 235, 0) 70%);
            filter: blur(50px);
            border-radius: 50%;
        }

        .ambient-orb-2 {
            position: absolute;
            bottom: -10%;
            left: 15%;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.1) 0%, rgba(2, 132, 199, 0) 70%);
            filter: blur(60px);
            border-radius: 50%;
        }

        .ambient-grid {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(148, 163, 184, 0.15) 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: 0.7;
        }

        /* شريط الأدوات العلوي في صفحة الدخول */
        .top-nav-bar {
            position: absolute;
            top: 24px;
            left: 24px;
            right: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        .nav-back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--surface);
            border: 1px solid var(--border-default);
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-body);
            text-decoration: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: var(--transition);
        }

        .nav-back-link:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateX(2px);
        }

        .theme-toggle-btn {
            width: 40px;
            height: 40px;
            background: var(--surface);
            border: 1px solid var(--border-default);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-body);
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: var(--transition);
        }

        .theme-toggle-btn:hover {
            border-color: var(--border-focused);
            color: var(--primary);
            transform: rotate(15deg);
        }

        /* حاوية بطاقة الدخول الرئيسية */
        .auth-container {
            width: 100%;
            max-width: 460px;
            position: relative;
            z-index: 5;
            margin: auto;
        }

        .auth-card {
            background: var(--surface);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-card);
            padding: 38px 36px;
            border: 1px solid var(--border-default);
            position: relative;
            transition: var(--transition);
        }

        /* رأس البطاقة والشعار */
        .auth-header {
            text-align: center;
            margin-bottom: 26px;
        }

        .brand-avatar {
            width: 64px;
            height: 64px;
            margin: 0 auto 14px;
            background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.7rem;
            box-shadow: 0 8px 20px -4px rgba(29, 78, 216, 0.35);
            transition: var(--transition);
        }

        .brand-avatar:hover {
            transform: scale(1.05) rotate(-3deg);
        }

        .brand-avatar img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 18px;
            padding: 8px;
        }

        .auth-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--text-heading);
            letter-spacing: -0.01em;
            margin-bottom: 4px;
        }

        .auth-subtitle {
            font-size: 0.86rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* شريط التبديل بين الأدوار (Segmented Control) */
        .role-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            background: var(--surface-subtle);
            border: 1px solid var(--border-default);
            padding: 5px;
            border-radius: 16px;
            margin-bottom: 24px;
        }

        .role-tab-btn {
            background: transparent;
            border: none;
            padding: 10px 6px;
            border-radius: 11px;
            font-size: 0.86rem;
            font-weight: 600;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: var(--transition);
            user-select: none;
        }

        .role-tab-btn i {
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .role-tab-btn:hover:not(.active) {
            color: var(--text-heading);
            background: rgba(148, 163, 184, 0.08);
        }

        .role-tab-btn.active {
            background: var(--surface);
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            font-weight: 700;
        }

        .role-tab-btn.active[data-role="teacher"] {
            color: var(--accent-teacher);
        }

        .role-tab-btn.active[data-role="admin"] {
            color: #4338ca;
        }

        /* رسالة ترحيب مخصصة لكل دور */
        .role-context-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: var(--primary-light);
            border: 1px solid rgba(37, 99, 235, 0.15);
            border-radius: 12px;
            margin-bottom: 22px;
            transition: var(--transition);
        }

        .role-context-icon {
            font-size: 1rem;
            color: var(--primary);
            flex-shrink: 0;
        }

        .role-context-text {
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--text-body);
            line-height: 1.4;
        }

        /* تنبيهات الأخطاء والنجاح */
        .alert-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 0.84rem;
            font-weight: 500;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .alert-box.error {
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: var(--danger);
        }

        .alert-box.success {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success);
        }

        /* بنية حقول الإدخال الهندسية بدون أي تداخل (Zero Overlap Engineering) */
        .form-fields {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .field-label {
            font-size: 0.84rem;
            font-weight: 600;
            color: var(--text-heading);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .field-input-box {
            display: flex;
            align-items: center;
            height: 52px;
            background: var(--surface-subtle);
            border: 1.5px solid var(--border-default);
            border-radius: var(--radius-box);
            transition: var(--transition);
            overflow: hidden;
        }

        .field-input-box:focus-within {
            border-color: var(--border-focused);
            background: var(--surface);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .field-icon-slot {
            width: 46px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dim);
            font-size: 1.05rem;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .field-input-box:focus-within .field-icon-slot {
            color: var(--primary);
        }

        .field-input {
            flex: 1;
            height: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.94rem;
            font-weight: 500;
            color: var(--text-heading);
            padding: 0 4px;
        }

        .field-input::placeholder {
            color: var(--text-dim);
            font-weight: 400;
            font-size: 0.88rem;
        }

        .field-action-slot {
            width: 46px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: var(--text-dim);
            cursor: pointer;
            flex-shrink: 0;
            font-size: 1rem;
            transition: var(--transition);
        }

        .field-action-slot:hover {
            color: var(--text-heading);
        }

        /* خيارات التذكر واستعادة كلمة المرور */
        .form-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            margin-top: 2px;
        }

        .remember-checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-body);
            cursor: pointer;
            user-select: none;
            font-weight: 500;
        }

        .remember-checkbox-label input {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            cursor: pointer;
        }

        .forgot-link:hover {
            text-decoration: underline;
            color: var(--primary-hover);
        }

        /* زر الإرسال الأساسي */
        .submit-btn {
            width: 100%;
            height: 52px;
            margin-top: 8px;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            border: none;
            border-radius: var(--radius-box);
            color: #ffffff;
            font-size: 0.96rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 14px rgba(29, 78, 216, 0.28);
            transition: var(--transition);
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(29, 78, 216, 0.35);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn i {
            transition: transform 0.2s ease;
        }

        .submit-btn:hover i {
            transform: translateX(-4px);
        }

        /* أزرار الدخول الاجتماعي السريع */
        .google-login-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            height: 48px;
            background: var(--surface);
            border: 1.5px solid var(--border-default);
            border-radius: var(--radius-box);
            color: var(--text-heading);
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            margin-top: 10px;
        }

        .google-login-btn:hover {
            border-color: #4285F4;
            background: var(--surface-subtle);
            box-shadow: 0 4px 14px rgba(66, 133, 244, 0.15);
            transform: translateY(-1px);
        }

        .social-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 16px 0 6px;
        }

        .social-divider-line {
            flex: 1;
            height: 1px;
            background: var(--border-default);
        }

        .social-divider-text {
            font-size: 0.78rem;
            color: var(--text-dim);
            font-weight: 500;
        }

        /* أسفل البطاقة والتسجيل */
        .auth-footer {
            margin-top: 26px;
            padding-top: 22px;
            border-top: 1px solid var(--border-default);
            text-align: center;
            font-size: 0.86rem;
            color: var(--text-muted);
        }

        .register-highlight-link {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            margin-right: 5px;
            transition: var(--transition);
        }

        .register-highlight-link:hover {
            text-decoration: underline;
            color: var(--primary-hover);
        }

        /* شارة الهوية الفلسطينية */
        .palestine-badge {
            margin-top: 24px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        /* نافذة استعادة كلمة المرور المودال المنبثقة */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 100;
        }

        .modal-backdrop.active {
            display: flex;
        }

        .modal-card {
            background: var(--surface);
            border-radius: 20px;
            padding: 30px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border-default);
            animation: modalIn 0.25s ease-out;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .modal-head h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-heading);
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: var(--text-dim);
            font-size: 1.2rem;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close-btn:hover {
            color: var(--danger);
            background: var(--danger-bg);
        }

        /* تجاوب الشاشات الصغيرة */
        @media (max-width: 480px) {
            body {
                padding: 16px 14px;
            }
            .auth-card {
                padding: 28px 20px;
                border-radius: 20px;
            }
            .top-nav-bar {
                top: 14px;
                left: 14px;
                right: 14px;
            }
            .auth-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

    <!-- خلفية جمالية تفاعلية -->
    <div class="ambient-bg">
        <div class="ambient-orb-1"></div>
        <div class="ambient-orb-2"></div>
        <div class="ambient-grid"></div>
    </div>

    <!-- شريط التنقل العلوي البسيط -->
    <header class="top-nav-bar">
        <a href="{{ url('/') }}" class="nav-back-link" title="الرجوع للصفحة الرئيسية">
            <i class="fas fa-arrow-right"></i>
            <span>الرئيسية</span>
        </a>

        <button type="button" class="theme-toggle-btn" id="themeBtn" onclick="toggleTheme()" title="تبديل المظهر">
            <i class="fas fa-moon" id="themeIcon"></i>
        </button>
    </header>

    <!-- حاوية تسجيل الدخول الرئيسية -->
    <main class="auth-container">
        <div class="auth-card">
            
            <!-- رأس البطاقة والشعار -->
            <div class="auth-header">
                <div class="brand-avatar">
                    @if(\App\Models\Setting::get('site_logo'))
                        <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="شعار المنصة">
                    @else
                        <i class="fas fa-graduation-cap"></i>
                    @endif
                </div>
                <h1 class="auth-title">{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</h1>
                <p class="auth-subtitle">المنصة التعليمية الشاملة لطلبة الثانوية العامة في فلسطين</p>
            </div>

            <!-- مبدل الأدوار الهندسي فائق النعومة -->
            <div class="role-tabs" role="tablist">
                <button type="button" class="role-tab-btn active" data-role="student" onclick="switchRole('student')">
                    <i class="fas fa-user-graduate"></i>
                    <span>طالب</span>
                </button>
                <button type="button" class="role-tab-btn" data-role="teacher" onclick="switchRole('teacher')">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>معلم</span>
                </button>
                <button type="button" class="role-tab-btn" data-role="admin" onclick="switchRole('admin')">
                    <i class="fas fa-shield-alt"></i>
                    <span>إدارة</span>
                </button>
            </div>

            <!-- شارة توجيه وسياق مخصصة لكل دور -->
            <div class="role-context-badge" id="role_context_banner">
                <i class="fas fa-sparkles role-context-icon" id="role_context_icon"></i>
                <span class="role-context-text" id="role_context_text">
                    بوابة دخول الطلبة — أهلاً بك في فضاء التميز لمتابعة دروسك واختباراتك اليومية.
                </span>
            </div>

            <!-- رسائل وتنبيهات الأخطاء والنجاح -->
            @if(isset($errors) && $errors->has('error'))
                <div class="alert-box error" role="alert">
                    <i class="fas fa-circle-exclamation" style="font-size: 1.1rem; margin-top: 1px;"></i>
                    <span>{{ $errors->first('error') }}</span>
                </div>
            @endif

            @if(session('status'))
                <div class="alert-box success" role="alert">
                    <i class="fas fa-circle-check" style="font-size: 1.1rem; margin-top: 1px;"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- نموذج الدخول -->
            <form action="{{ route('login.post') }}" method="POST" class="form-fields" autocomplete="on">
                @csrf
                <input type="hidden" name="role" id="role_input" value="{{ old('role', 'student') }}">

                <!-- حقل البريد الإلكتروني -->
                <div class="field-group">
                    <label for="email_field" class="field-label">
                        <span>البريد الإلكتروني</span>
                    </label>
                    <div class="field-input-box">
                        <div class="field-icon-slot">
                            <i class="far fa-envelope"></i>
                        </div>
                        <input 
                            type="email" 
                            id="email_field" 
                            name="email" 
                            value="{{ old('email') }}" 
                            class="field-input" 
                            placeholder="student@example.com" 
                            required 
                            autofocus
                        >
                    </div>
                </div>

                <!-- حقل كلمة المرور -->
                <div class="field-group">
                    <label for="password_field" class="field-label">
                        <span>كلمة المرور</span>
                    </label>
                    <div class="field-input-box">
                        <div class="field-icon-slot">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input 
                            type="password" 
                            id="password_field" 
                            name="password" 
                            class="field-input" 
                            placeholder="••••••••" 
                            required
                        >
                        <button type="button" class="field-action-slot" onclick="togglePasswordVisibility()" title="إظهار / إخفاء كلمة المرور" tabindex="-1">
                            <i class="far fa-eye" id="eye_icon"></i>
                        </button>
                    </div>
                </div>

                <!-- صف التذكر واستعادة كلمة المرور -->
                <div class="form-meta-row">
                    <label class="remember-checkbox-label">
                        <input type="checkbox" name="remember" value="1">
                        <span>تذكرني على هذا الجهاز</span>
                    </label>

                    <a href="javascript:void(0)" onclick="openForgotModal()" class="forgot-link">
                        نسيت كلمة المرور؟
                    </a>
                </div>

                <!-- زر تسجيل الدخول الأساسي -->
                <button type="submit" id="submit_action_btn" class="submit-btn">
                    <span id="submit_label">الدخول للمنصة</span>
                    <i class="fas fa-arrow-left"></i>
                </button>
            </form>

            <!-- خيار الدخول السريع عبر حساب Google للطلبة -->
            <div id="student_social_box" style="margin-top: 6px;">
                <div class="social-divider">
                    <span class="social-divider-line"></span>
                    <span class="social-divider-text">أو المتابعة السريعة عبر</span>
                    <span class="social-divider-line"></span>
                </div>
                <a href="{{ route('auth.google') }}" class="google-login-btn" id="googleLoginBtn" title="تسجيل الدخول السريع بحساب Google">
                    <svg width="18" height="18" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>الدخول السريع بحساب Google</span>
                </a>
            </div>

            <!-- أسفل البطاقة وروابط التسجيل -->
            <div class="auth-footer" id="student_register_box">
                <span>ليس لديك حساب بعد؟</span>
                <a href="{{ route('students.create') }}" class="register-highlight-link">
                    إنشاء حساب طالب جديد
                </a>
            </div>
        </div>

        <!-- شارة الفخر بالهوية الفلسطينية -->
        <div class="palestine-badge">
            <span>🇵🇸</span>
            <span>بوابة تعليمية مخصصة لخدمة طلبة التوجيهي في فلسطين</span>
        </div>
    </main>

    <!-- نافذة استعادة كلمة المرور المنبثقة (Forgot Password Modal) -->
    <div class="modal-backdrop" id="forgotModal">
        <div class="modal-card">
            <div class="modal-head">
                <h3>استعادة كلمة المرور</h3>
                <button type="button" class="modal-close-btn" onclick="closeForgotModal()">&times;</button>
            </div>
            <p style="font-size: 0.86rem; color: var(--text-muted); margin-bottom: 20px; line-height: 1.6;">
                أدخل البريد الإلكتروني المرتبط بحسابك وسنقوم بالتحقق منه ومساعدتك في إعادة تعيين كلمة المرور فوراً.
            </p>

            <form action="{{ route('password.forgot') }}" method="POST">
                @csrf
                <div class="field-group" style="margin-bottom: 20px;">
                    <label class="field-label">البريد الإلكتروني المسجل</label>
                    <div class="field-input-box">
                        <div class="field-icon-slot"><i class="far fa-envelope"></i></div>
                        <input type="email" name="email" class="field-input" placeholder="example@domain.com" required>
                    </div>
                </div>

                <button type="submit" class="submit-btn" style="margin-top: 0;">
                    <span>إرسال رابط الاستعادة</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- السكريبتات التفاعلية -->
    <script>
        // تبديل الدور المختار بسلاسة وتحديث واجهة المستخدم
        function switchRole(role) {
            document.querySelectorAll('.role-tab-btn').forEach(btn => btn.classList.remove('active'));
            const currentTab = document.querySelector(`.role-tab-btn[data-role="${role}"]`);
            if (currentTab) currentTab.classList.add('active');

            const roleInput = document.getElementById('role_input');
            roleInput.value = role;

            const emailInput = document.getElementById('email_field');
            const submitBtn = document.getElementById('submit_action_btn');
            const submitLabel = document.getElementById('submit_label');
            const regBox = document.getElementById('student_register_box');
            const socialBox = document.getElementById('student_social_box');
            const banner = document.getElementById('role_context_banner');
            const bannerText = document.getElementById('role_context_text');
            const bannerIcon = document.getElementById('role_context_icon');

            if (role === 'student') {
                emailInput.placeholder = 'student@example.com';
                submitLabel.innerText = 'الدخول كطالب توجيهي';
                regBox.style.display = 'block';
                if (socialBox) socialBox.style.display = 'block';
                submitBtn.style.background = 'linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%)';
                banner.style.background = 'var(--primary-light)';
                bannerIcon.className = 'fas fa-graduation-cap role-context-icon';
                bannerIcon.style.color = '#1d4ed8';
                bannerText.innerText = 'بوابة دخول الطلبة — أهلاً بك في فضاء التميز لمتابعة دروسك واختباراتك اليومية.';
            } else if (role === 'teacher') {
                emailInput.placeholder = 'teacher@menaret-tawjihi.ps';
                submitLabel.innerText = 'الدخول لبوابة المعلمين';
                regBox.style.display = 'none';
                if (socialBox) socialBox.style.display = 'none';
                submitBtn.style.background = 'linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%)';
                banner.style.background = 'rgba(2, 132, 199, 0.1)';
                bannerIcon.className = 'fas fa-chalkboard-teacher role-context-icon';
                bannerIcon.style.color = '#0284c7';
                bannerText.innerText = 'بوابة الكادر التعليمي — إدارة المقررات والامتحانات ورصد درجات وتفاعل الطلبة.';
            } else if (role === 'admin') {
                emailInput.placeholder = 'admin@menaret-tawjihi.ps';
                submitLabel.innerText = 'الدخول للوحة الإدارة';
                regBox.style.display = 'none';
                if (socialBox) socialBox.style.display = 'none';
                submitBtn.style.background = 'linear-gradient(135deg, #1e293b 0%, #334155 100%)';
                banner.style.background = 'rgba(15, 23, 42, 0.08)';
                bannerIcon.className = 'fas fa-shield-alt role-context-icon';
                bannerIcon.style.color = '#1e293b';
                bannerText.innerText = 'بوابة الإدارة المركزية — الإشراف الأكاديمي، اعتماد الحسابات وإعدادات المنصة.';
            }
        }

        // إظهار وإخفاء كلمة المرور
        function togglePasswordVisibility() {
            const input = document.getElementById('password_field');
            const icon = document.getElementById('eye_icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'far fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'far fa-eye';
            }
        }

        // نافذة استعادة كلمة المرور
        function openForgotModal() {
            document.getElementById('forgotModal').classList.add('active');
        }

        function closeForgotModal() {
            document.getElementById('forgotModal').classList.remove('active');
        }

        // إغلاق المودال عند النقر خارج البطاقة
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('forgotModal');
            if (e.target === modal) {
                closeForgotModal();
            }
        });

        // تبديل الوضع الليلي والنهاري
        function toggleTheme() {
            const body = document.body;
            const icon = document.getElementById('themeIcon');
            const isDark = body.classList.toggle('dark-mode');
            localStorage.setItem('menaret_theme', isDark ? 'dark' : 'light');
            icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        }

        // قراءة الثيم المخزن عند فتح الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('menaret_theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
                const icon = document.getElementById('themeIcon');
                if (icon) icon.className = 'fas fa-sun';
            }

            const initialRole = document.getElementById('role_input').value || 'student';
            switchRole(initialRole);
        });
    </script>
</body>
</html>
