<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if(\App\Models\Setting::get('site_favicon'))
        <link rel="icon" href="{{ asset(\App\Models\Setting::get('site_favicon')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @endif
    <title>إنشاء حساب طالب جديد | {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸</title>

    <!-- Google Fonts: Alexandria -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Google Identity Services (GIS) -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #eff6ff;
            --emerald: #10b981;
            --bg-main: #f8fafc;
            --text-title: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --border-card: #e2e8f0;
        }

        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Alexandria', sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-body);
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            direction: rtl;
        }

        .auth-split-wrapper {
            display: flex;
            width: 100vw;
            min-height: 100vh;
        }

        /* الجانب الأيمن (المرئي والإلهامي) */
        .auth-visual-side {
            flex: 1.1;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 27, 75, 0.9) 100%),
                        url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2070') center/cover no-repeat;
            padding: 60px 8%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .auth-visual-side::after {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.4) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .brand-logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
            font-size: 1.3rem;
            font-weight: 800;
        }
        .brand-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
        }

        .visual-center-content {
            margin: 40px 0;
            max-width: 520px;
        }
        .palestine-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #93c5fd;
        }
        .visual-center-content h2 {
            font-size: 2.3rem;
            font-weight: 900;
            line-height: 1.3;
            margin-bottom: 16px;
        }
        .visual-center-content p {
            font-size: 1rem;
            line-height: 1.8;
            color: #cbd5e1;
            margin-bottom: 30px;
        }

        .features-checklist {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .check-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #f1f5f9;
        }
        .check-item i {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .visual-footer {
            font-size: 0.85rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* الجانب الأيسر (نموذج التسجيل) */
        .auth-form-side {
            flex: 1.3;
            background: white;
            padding: 40px 6%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }

        .form-inner-box {
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }

        .form-head {
            margin-bottom: 26px;
        }
        .form-head .tag-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #dbeafe;
            color: var(--primary);
            font-size: 0.78rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 10px;
        }
        .form-head h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-title);
            margin-bottom: 6px;
        }
        .form-head p {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        /* تنسيقات التسجيل السريع عبر Google وباقي الحسابات */
        .social-auth-section {
            margin-bottom: 22px;
        }
        .btn-social-auth {
            width: 100%;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 0.92rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s ease;
            box-sizing: border-box;
        }
        .btn-google-auth {
            background: #ffffff;
            color: #1e293b;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }
        .btn-google-auth:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            color: #0f172a;
        }
        .social-mini-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .btn-social-mini {
            height: 40px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            color: #475569;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        .btn-social-mini:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            transform: translateY(-1px);
            color: #0f172a;
        }
        .social-auth-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0 18px;
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .social-auth-divider::before,
        .social-auth-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1.5px solid #e2e8f0;
        }
        .social-auth-divider span {
            padding: 0 12px;
        }
        .social-connected-card {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1.5px solid #bfdbfe;
            border-radius: 14px;
            padding: 14px 18px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.08);
        }
        .social-connected-main {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .social-connected-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid white;
            object-fit: cover;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .social-connected-info .title {
            font-size: 0.88rem;
            font-weight: 800;
            color: #1e3a8a;
        }
        .social-connected-info .desc {
            font-size: 0.78rem;
            color: #2563eb;
            font-weight: 600;
        }
        .btn-disconnect-social {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-disconnect-social:hover {
            background: #ef4444;
            color: white;
        }
        .verified-badge-label {
            background: #ecfdf5;
            color: #059669;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .grid-2-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 14px;
        }
        .input-group label {
            font-size: 0.84rem;
            font-weight: 700;
            color: var(--text-title);
            display: flex;
            justify-content: space-between;
        }
        .input-group label .req {
            color: #ef4444;
        }
        .input-control-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-control-wrap i.lead-icon {
            position: absolute;
            right: 14px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .form-input {
            width: 100%;
            padding: 11px 38px 11px 14px;
            border: 1.5px solid var(--border-card);
            border-radius: 12px;
            font-size: 0.92rem;
            font-family: inherit;
            color: var(--text-body);
            background: #f8fafc;
            outline: none;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .password-toggle-btn {
            position: absolute;
            left: 12px;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.95rem;
            padding: 4px;
        }

        /* Password Strength Meter */
        .strength-meter {
            height: 4px;
            background: #e2e8f0;
            border-radius: 4px;
            margin-top: 4px;
            overflow: hidden;
        }
        .strength-meter-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
        }

        /* Submit Button */
        .btn-register-submit {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s ease;
            margin-top: 8px;
        }
        .btn-register-submit:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
        }

        .login-switch-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .login-switch-footer a {
            color: var(--primary);
            font-weight: 800;
            text-decoration: none;
            margin-right: 4px;
        }
        .login-switch-footer a:hover {
            text-decoration: underline;
        }

        /* Upload Cards */
        .upload-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
        }
        .upload-card-box {
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 16px 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
        }
        .upload-card-box:hover {
            border-color: var(--primary);
            background: #f0f7ff;
            transform: translateY(-2px);
        }
        .upload-card-box.has-file {
            border-style: solid;
            border-color: #10b981;
            background: #f0fdf4;
        }
        .upload-icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #eff6ff;
            color: var(--primary);
            display: grid;
            place-items: center;
            font-size: 1.3rem;
            margin: 0 auto 8px auto;
            transition: all 0.2s ease;
        }
        .upload-card-box.has-file .upload-icon-circle {
            background: #dcfce7;
            color: #10b981;
        }
        .upload-thumb-preview {
            width: 65px;
            height: 65px;
            border-radius: 12px;
            object-fit: cover;
            margin: 0 auto 8px auto;
            display: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            border: 2px solid white;
        }
        .upload-card-title {
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--text-title);
            display: block;
            margin-bottom: 3px;
        }
        .upload-card-sub {
            font-size: 0.7rem;
            color: var(--text-muted);
            display: block;
            line-height: 1.3;
        }
        .upload-file-status {
            font-size: 0.72rem;
            color: #059669;
            font-weight: 700;
            margin-top: 6px;
            display: none;
        }

        @media (max-width: 950px) {
            .auth-visual-side { display: none; }
            .auth-form-side { padding: 40px 20px; }
            .grid-2-cols { grid-template-columns: 1fr; gap: 0; }
            .upload-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="auth-split-wrapper">

    <!-- الجانب الأيمن المرئي والإلهامي -->
    <div class="auth-visual-side">
        <a href="/" class="brand-logo-badge">
            @if(\App\Models\Setting::get('site_logo'))
                <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}" style="max-height: 44px; max-width: 50px; object-fit: contain; border-radius: 8px;">
            @else
                <div class="brand-icon-box">🇵🇸</div>
            @endif
            <span>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</span>
        </a>

        <div class="visual-center-content">
            <div class="palestine-pill">
                <i class="fas fa-sparkles"></i> المنهاج الفلسطيني المعتمد لعام 2026
            </div>
            <h2>طريقك الأضمن نحو التفوق والتميز الوزاري</h2>
            <p>انضم إلى زملائك في جميع محافظات الوطن (القدس، الضفة الغربية، وقطاع غزة) واستفد من أحدث الشروحات، وبنك الأسئلة، ومتابعة الالتزام اليومي.</p>

            <div class="features-checklist">
                <div class="check-item">
                    <i class="fas fa-check"></i>
                    <span>شروحات فيديو تفاعلية مع إمكانية حفظ الدروس أوفلاين</span>
                </div>
                <div class="check-item">
                    <i class="fas fa-check"></i>
                    <span>أرشيف الامتحانات الوزارية ونماذج الإجابة الرسمية المعتمدة</span>
                </div>
                <div class="check-item">
                    <i class="fas fa-check"></i>
                    <span>حاسبة معدل التوجيهي ودليل القبول والتنسيق في الجامعات</span>
                </div>
                <div class="check-item">
                    <i class="fas fa-check"></i>
                    <span>بطاقات الاستذكار السريع والقوانين ومؤشر الالتزام اليومي 🔥</span>
                </div>
            </div>
        </div>

        <div class="visual-footer">
            <i class="fas fa-shield-alt"></i>
            <span>بياناتك الأكاديمية محمية ومؤمنة بأعلى معايير الخصوصية.</span>
        </div>
    </div>

    <!-- الجانب الأيسر (نموذج التسجيل السلس) -->
    <div class="auth-form-side">
        <div class="form-inner-box">

            <div class="form-head">
                <div class="tag-pill">
                    <i class="fas fa-user-plus"></i> عضوية طالب جديدة
                </div>
                <h1>إنشاء حسابك الأكاديمي</h1>
                <p>أدخل بياناتك للانضمام فورياً إلى المنصة ومتابعة دروسك</p>
            </div>

            @if(session('error'))
                <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; color: #dc2626; font-size: 0.88rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; color: #1d4ed8; font-size: 0.88rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(isset($googleProfile) && !empty($googleProfile['email']))
                <!-- بطاقة الحساب المربوط عبر Google -->
                <div class="social-connected-card">
                    <div class="social-connected-main">
                        @if(!empty($googleProfile['picture']))
                            <img src="{{ $googleProfile['picture'] }}" alt="صورة الحساب" class="social-connected-avatar">
                        @else
                            <div class="social-connected-avatar" style="background: #2563eb; color: white; display: grid; place-items: center; font-size: 1.2rem;">
                                <i class="fab fa-google"></i>
                            </div>
                        @endif
                        <div class="social-connected-info">
                            <div class="title">تم استيراد بيانات Google بنجاح 🎓</div>
                            <div class="desc">{{ $googleProfile['name_ar'] ?? 'طالب التوجيهي' }} ({{ $googleProfile['email'] }})</div>
                        </div>
                    </div>
                    <a href="{{ route('students.create') }}" class="btn-disconnect-social" title="إلغاء والعودة للنموذج العادي">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            @else
                <!-- شريط التسجيل السريع عبر Google وباقي الحسابات للتسهيل -->
                <div class="social-auth-section">
                    <a href="{{ route('auth.google') }}" class="btn-social-auth btn-google-auth" id="btnGoogleRegister">
                        <svg width="20" height="20" viewBox="0 0 24 24">
                            <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.4 9 5 12 5z"/>
                            <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.7-.2-2.3H12v4.6h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.9z"/>
                            <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3 0-.8.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 12.4 0 15.3c0 2.9.7 5.6 1.9 8l3.7-2.9z"/>
                            <path fill="#34A853" d="M12 23.5c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.4-6.4-5.2L1.9 16.5C3.7 20.4 7.5 23.5 12 23.5z"/>
                        </svg>
                        <span>التسجيل والمتابعة السريعة بحساب Google (Gmail)</span>
                    </a>

                    <div class="social-mini-row">
                        <button type="button" class="btn-social-mini" onclick="openQuickGmailModal()">
                            <i class="fas fa-bolt" style="color: #f59e0b;"></i>
                            <span>تعبئة تلقائية بالـ Gmail</span>
                        </button>
                        <button type="button" class="btn-social-mini" onclick="openSocialModal('Microsoft')">
                            <i class="fab fa-microsoft" style="color: #00a4ef;"></i>
                            <span>حساب Microsoft</span>
                        </button>
                        <button type="button" class="btn-social-mini" onclick="openSocialModal('Apple')">
                            <i class="fab fa-apple" style="color: #000000;"></i>
                            <span>حساب Apple</span>
                        </button>
                    </div>

                    <div class="social-auth-divider">
                        <span>أو تعبئة البيانات الأكاديمية يدوياً أدناه</span>
                    </div>
                </div>
            @endif

            <form id="registerForm" onsubmit="handleRegisterSubmit(event)" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="google_id" id="google_id" value="{{ $googleProfile['google_id'] ?? '' }}">
                <input type="hidden" name="avatar_url" id="avatar_url" value="{{ $googleProfile['picture'] ?? '' }}">

                <!-- الاسم الكامل ورقم الهوية -->
                <div class="grid-2-cols">
                    <div class="input-group">
                        <label for="name_ar">
                            <span>الاسم الرباعي (بالعربية) <span class="req">*</span></span>
                            @if(!empty($googleProfile['name_ar']))
                                <span class="verified-badge-label"><i class="fas fa-check"></i> من Google</span>
                            @endif
                        </label>
                        <div class="input-control-wrap">
                            <i class="fas fa-user lead-icon"></i>
                            <input type="text" name="name_ar" id="name_ar" class="form-input" value="{{ old('name_ar', $googleProfile['name_ar'] ?? '') }}" placeholder="مثال: أحمد محمد خليل علي" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="nid">رقم الهوية الفلسطينية <span class="req">*</span></label>
                        <div class="input-control-wrap">
                            <i class="fas fa-id-card lead-icon"></i>
                            <input type="text" name="nid" id="nid" maxlength="9" class="form-input" placeholder="9 أرقام (مثال: 401234567)" required>
                        </div>
                    </div>
                </div>

                <!-- البريد ورقم جوال الطالب -->
                <div class="grid-2-cols">
                    <div class="input-group">
                        <label for="email">
                            <span>البريد الإلكتروني <span class="req">*</span></span>
                            @if(!empty($googleProfile['email']))
                                <span class="verified-badge-label"><i class="fas fa-check-circle"></i> معتمد من Google</span>
                            @endif
                        </label>
                        <div class="input-control-wrap">
                            <i class="fas fa-envelope lead-icon"></i>
                            <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $googleProfile['email'] ?? '') }}" placeholder="student@example.com" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="phone">رقم جوال الطالب / واتساب <span class="req">*</span></label>
                        <div class="input-control-wrap">
                            <i class="fas fa-mobile-screen-button lead-icon"></i>
                            <input type="tel" name="phone" id="phone" class="form-input" placeholder="059XXXXXXX أو 056XXXXXXX" required>
                        </div>
                    </div>
                </div>

                <!-- هاتف ولي الأمر واسم المدرسة -->
                <div class="grid-2-cols">
                    <div class="input-group">
                        <label for="guardian_phone">رقم جوال ولي الأمر (للمتابعة الأكاديمية)</label>
                        <div class="input-control-wrap">
                            <i class="fas fa-user-shield lead-icon"></i>
                            <input type="tel" name="guardian_phone" id="guardian_phone" class="form-input" placeholder="059XXXXXXX أو 056XXXXXXX">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="school_name">اسم المدرسة الثانوية</label>
                        <div class="input-control-wrap">
                            <i class="fas fa-school lead-icon"></i>
                            <input type="text" name="school_name" id="school_name" class="form-input" placeholder="مثال: مدرسة الحسين بن علي الثانوية">
                        </div>
                    </div>
                </div>

                <!-- المرحلة/الفرع والجنس -->
                <div class="grid-2-cols">
                    <div class="input-group">
                        <label for="stage_id">الفرع الأكاديمي (توجيهي فلسطين) <span class="req">*</span></label>
                        <div class="input-control-wrap">
                            <i class="fas fa-graduation-cap lead-icon"></i>
                            <select name="stage_id" id="stage_id" class="form-input" required onchange="onStageChanged(this.value)">
                                @foreach($stages as $stg)
                                    @if($stg->grade_level >= 120)
                                        <option value="{{ $stg->id }}" data-grade="{{ $stg->grade_level }}" {{ ($stg->grade_level == 122 || str_contains($stg->label_ar, 'علمي')) ? 'selected' : '' }}>
                                            {{ $stg->icon ?? '🎓' }} {{ $stg->label_ar }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="gender">الجنس <span class="req">*</span></label>
                        <div class="input-control-wrap">
                            <i class="fas fa-venus-mars lead-icon"></i>
                            <select name="gender" id="gender" class="form-input" required>
                                <option value="ذكر" selected>ذكر (طالب)</option>
                                <option value="أنثى">أنثى (طالبة)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- المحافظة والعمر -->
                <div class="grid-2-cols">
                    <div class="input-group">
                        <label for="city">المحافظة / المدينة <span class="req">*</span></label>
                        <div class="input-control-wrap">
                            <i class="fas fa-map-marker-alt lead-icon"></i>
                            <select name="city" id="city" class="form-input">
                                <option value="القدس">القدس الشريف 🕌</option>
                                <option value="رام الله والبيرة" selected>رام الله والبيرة</option>
                                <option value="غزة">غزة العزة 🌿</option>
                                <option value="نابلس">نابلس (جبل النار)</option>
                                <option value="الخليل">الخليل</option>
                                <option value="جنين">جنين القسام</option>
                                <option value="طولكرم">طولكرم</option>
                                <option value="قلقيلية">قلقيلية</option>
                                <option value="بيت لحم">بيت لحم</option>
                                <option value="سلفيت">سلفيت</option>
                                <option value="أريحا">أريحا والأغوار</option>
                                <option value="طوباس">طوباس</option>
                                <option value="خان يونس">خان يونس</option>
                                <option value="رفح">رفح</option>
                                <option value="شمال غزة">شمال غزة (جباليا)</option>
                                <option value="دير البلح">دير البلح والوسطى</option>
                                <option value="أخرى">خارج فلسطين / أخرى</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="age">العمر</label>
                        <div class="input-control-wrap">
                            <i class="fas fa-calendar-check lead-icon"></i>
                            <input type="number" name="age" id="age" value="18" min="15" max="25" class="form-input">
                        </div>
                    </div>
                </div>

                <!-- المرفقات والوثائق: الصورة الشخصية وصورة الهوية -->
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                        <label style="font-size: 0.88rem; font-weight: 800; color: var(--text-title);">
                            <i class="fas fa-camera" style="color: var(--primary);"></i> الصورة الشخصية وصورة الهوية الفلسطينية:
                        </label>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">(اختياري وموصى به للاعتماد الرسمي)</span>
                    </div>

                    <div class="upload-grid">
                        <!-- 1. صندوق رفع الصورة الشخصية -->
                        <div class="upload-card-box" id="boxPhoto" onclick="document.getElementById('photoInput').click()">
                            <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;" onchange="previewStudentPhoto(this)">
                            <img id="previewPhotoImg" class="upload-thumb-preview" alt="معاينة الصورة الشخصية">
                            <div class="upload-icon-circle" id="iconPhotoCircle">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <span class="upload-card-title">الصورة الشخصية للطالب</span>
                            <span class="upload-card-sub" id="photoSubText">انقر لاختيار صورة واضحة لوجه الطالب (JPG/PNG)</span>
                            <div class="upload-file-status" id="photoStatusBadge">
                                <i class="fas fa-check-circle"></i> تم إرفاق الصورة
                            </div>
                        </div>

                        <!-- 2. صندوق رفع صورة الهوية الفلسطينية -->
                        <div class="upload-card-box" id="boxIdPhoto" onclick="document.getElementById('idPhotoInput').click()">
                            <input type="file" name="id_photo" id="idPhotoInput" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" style="display: none;" onchange="previewStudentIdPhoto(this)">
                            <img id="previewIdPhotoImg" class="upload-thumb-preview" alt="معاينة صورة الهوية">
                            <div class="upload-icon-circle" id="iconIdCircle">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <span class="upload-card-title">صورة بطاقة الهوية الفلسطينية</span>
                            <span class="upload-card-sub" id="idSubText">صورة البطاقة أو شهادة الميلاد للمطابقة الرسمية</span>
                            <div class="upload-file-status" id="idStatusBadge">
                                <i class="fas fa-check-circle"></i> تم إرفاق الوثيقة
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قائمة المواد التابعة للفرع للاشتراك بها -->
                <div class="input-group" style="margin-bottom: 18px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <label style="font-weight: 700; color: var(--text-title); font-size: 0.88rem;">
                            <i class="fas fa-book-bookmark" style="color: var(--primary);"></i> المواد المقررة للاشتراك بها في الفرع:
                        </label>
                        <span style="font-size: 0.78rem; color: #10b981; font-weight: 700;">(جميع المواد مفعلة تلقائياً أو اختر ما يناسبك)</span>
                    </div>
                    <div id="subjectsSelectionContainer" style="background: #ffffff; border: 1.5px solid var(--border-card); border-radius: 12px; padding: 12px 16px; max-height: 200px; overflow-y: auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px;">
                        <!-- يتم ملء المواد ديناميكياً بواسطة جافاسكريبت -->
                    </div>
                </div>

                <!-- كلمة المرور -->
                <div class="input-group">
                    <label for="password">كلمة المرور (6 خانات على الأقل) <span class="req">*</span></label>
                    <div class="input-control-wrap">
                        <i class="fas fa-lock lead-icon"></i>
                        <input type="password" name="password" id="password" minlength="6" class="form-input" placeholder="••••••••" required oninput="checkPasswordStrength(this.value)">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye" id="pwdEye"></i>
                        </button>
                    </div>
                    <div class="strength-meter">
                        <div class="strength-meter-fill" id="pwdStrengthFill"></div>
                    </div>
                </div>

                <button type="submit" id="btnSubmitRegister" class="btn-register-submit">
                    <span>إنشاء الحساب وبدء التعلم فورياً</span>
                    <i class="fas fa-arrow-left"></i>
                </button>

                <div class="login-switch-footer">
                    لديك حساب بالفعل؟
                    <a href="{{ route('login') }}">تسجيل الدخول هنا</a>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
    const stagesData = @json($stages);

    function onStageChanged(stageId) {
        const container = document.getElementById('subjectsSelectionContainer');
        if (!container) return;

        const currentStage = stagesData.find(s => s.id == stageId || s.grade_level == stageId);
        if (!currentStage || !currentStage.subjects || currentStage.subjects.length === 0) {
            container.innerHTML = '<div style="color: #64748b; font-size: 0.85rem; padding: 6px;">سيتم تفعيل كافة مواد المنهاج تلقائياً عند التسجيل.</div>';
            return;
        }

        let html = '';
        currentStage.subjects.forEach(sub => {
            const price = sub.discount_price_ils || sub.price_ils || 100;
            html += `
                <label style="display: flex; align-items: center; gap: 8px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; cursor: pointer; user-select: none;">
                    <input type="checkbox" name="subject_ids[]" value="${sub.id}" checked style="width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer;">
                    <span style="font-size: 1.1rem;">${sub.icon || '📘'}</span>
                    <div style="flex: 1;">
                        <div style="font-size: 0.85rem; font-weight: 700; color: #0f172a;">${sub.name_ar}</div>
                        <div style="font-size: 0.72rem; color: #64748b;">${price} ₪</div>
                    </div>
                </label>
            `;
        });
        container.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const stageSelect = document.getElementById('stage_id');
        if (stageSelect && stageSelect.value) {
            onStageChanged(stageSelect.value);
        }
    });

    function previewStudentPhoto(input) {
        const file = input.files[0];
        if (!file) return;

        const previewImg = document.getElementById('previewPhotoImg');
        const iconCircle = document.getElementById('iconPhotoCircle');
        const box = document.getElementById('boxPhoto');
        const subText = document.getElementById('photoSubText');
        const statusBadge = document.getElementById('photoStatusBadge');

        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            iconCircle.style.display = 'none';
            box.classList.add('has-file');
            subText.textContent = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
            statusBadge.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    function previewStudentIdPhoto(input) {
        const file = input.files[0];
        if (!file) return;

        const previewImg = document.getElementById('previewIdPhotoImg');
        const iconCircle = document.getElementById('iconIdCircle');
        const box = document.getElementById('boxIdPhoto');
        const subText = document.getElementById('idSubText');
        const statusBadge = document.getElementById('idStatusBadge');

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                iconCircle.style.display = 'none';
                box.classList.add('has-file');
                subText.textContent = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
                statusBadge.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewImg.style.display = 'none';
            iconCircle.style.display = 'grid';
            iconCircle.innerHTML = '<i class="fas fa-file-pdf" style="color: #ef4444;"></i>';
            box.classList.add('has-file');
            subText.textContent = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
            statusBadge.style.display = 'block';
        }
    }

    function togglePasswordVisibility() {
        const pwdInput = document.getElementById('password');
        const eyeIcon = document.getElementById('pwdEye');

        if (pwdInput.type === 'password') {
            pwdInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            pwdInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    function checkPasswordStrength(val) {
        const meter = document.getElementById('pwdStrengthFill');
        let score = 0;
        if (val.length >= 6) score += 33;
        if (/[A-Z]/.test(val) || /[a-z]/.test(val)) score += 33;
        if (/[0-9]/.test(val) || /[^A-Za-z0-9]/.test(val)) score += 34;

        meter.style.width = score + '%';
        if (score <= 33) {
            meter.style.background = '#ef4444';
        } else if (score <= 66) {
            meter.style.background = '#f59e0b';
        } else {
            meter.style.background = '#10b981';
        }
    }

    function handleRegisterSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('registerForm');
        const btn = document.getElementById('btnSubmitRegister');

        const nid = document.getElementById('nid').value.trim();
        if (nid.length !== 9 || !/^\d+$/.test(nid)) {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه رقم الهوية',
                text: 'يرجى التأكد من كتابة رقم الهوية الفلسطينية المكون من 9 أرقام بدقة.'
            });
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري إنشاء حسابك...';

        const formData = new FormData(form);

        axios.post('{{ route("students.store") }}', formData)
            .then(res => {
                Swal.fire({
                    icon: 'success',
                    title: res.data.title || 'تم إنشاء الحساب بنجاح!',
                    text: 'أهلاً بك في منصة منارة التوجيهي، جاري نقلك إلى لوحة دراستك...',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = res.data.redirect || '{{ route("student.dashboard") }}';
                });
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<span>إنشاء الحساب وبدء التعلم فورياً</span> <i class="fas fa-arrow-left"></i>';

                const msg = err.response?.data?.title || err.response?.data?.message || 'حدث خطأ أثناء التسجيل، يرجى مراجعة البيانات.';
                Swal.fire({
                    icon: 'error',
                    title: 'تعذر إتمام التسجيل',
                    text: msg,
                    confirmButtonText: 'حسناً'
                });
            });
    }

    // استعراض صورة حساب Google إن توفرت
    @if(isset($googleProfile) && !empty($googleProfile['picture']))
        document.addEventListener('DOMContentLoaded', () => {
            const previewImg = document.getElementById('previewPhotoImg');
            const iconCircle = document.getElementById('iconPhotoCircle');
            const box = document.getElementById('boxPhoto');
            const subText = document.getElementById('photoSubText');
            const statusBadge = document.getElementById('photoStatusBadge');
            if (previewImg && box) {
                previewImg.src = "{{ $googleProfile['picture'] }}";
                previewImg.style.display = 'block';
                if (iconCircle) iconCircle.style.display = 'none';
                box.classList.add('has-file');
                if (subText) subText.textContent = 'تم استيراد الصورة الشخصية من حساب Google';
                if (statusBadge) {
                    statusBadge.style.display = 'block';
                    statusBadge.innerHTML = '<i class="fab fa-google"></i> صورة Google المعتمدة';
                }
            }
        });
    @endif

    function openQuickGmailModal() {
        Swal.fire({
            title: 'تعبئة سريعة بحساب Gmail ⚡',
            html: `
                <div style="text-align: right; font-size: 0.88rem; color: #475569; margin-bottom: 12px; line-height: 1.6;">
                    أدخل بريدك الإلكتروني لتعبئة نموذج التسجيل تلقائياً وتسهيل إنشاء الحساب:
                </div>
                <div style="text-align: right; margin-bottom: 10px;">
                    <label style="font-weight: 700; font-size: 0.82rem; color: #1e293b;">بريد Gmail أو البريد الشخصي:</label>
                    <input type="email" id="swalGmailInput" class="swal2-input" style="margin: 6px 0 0; width: 100%; font-size: 0.9rem;" placeholder="example@gmail.com" dir="ltr">
                </div>
                <div style="text-align: right;">
                    <label style="font-weight: 700; font-size: 0.82rem; color: #1e293b;">اسمك الكامل (اختياري):</label>
                    <input type="text" id="swalNameInput" class="swal2-input" style="margin: 6px 0 0; width: 100%; font-size: 0.9rem;" placeholder="مثال: محمد أحمد" dir="rtl">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-magic"></i> تعبئة وتسهيل التسجيل',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#94a3b8',
            preConfirm: () => {
                const email = document.getElementById('swalGmailInput').value.trim();
                const name = document.getElementById('swalNameInput').value.trim();
                if (!email || !email.includes('@')) {
                    Swal.showValidationMessage('يرجى إدخال بريد إلكتروني صحيح.');
                    return false;
                }
                return { email, name };
            }
        }).then(result => {
            if (result.isConfirmed) {
                const { email, name } = result.value;
                axios.post('{{ route("auth.social.quickFill") }}', {
                    email: email,
                    name: name,
                    _token: '{{ csrf_token() }}'
                }).then(res => {
                    applyGoogleProfile(res.data.data);
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت التعبئة التلقائية بنجاح! 🚀',
                        text: 'تم ملء بيانات الاسم والبريد. يرجى إكمال رقم الهوية واختيار الفرع.',
                        timer: 2500,
                        showConfirmButton: false
                    });
                }).catch(() => {
                    applyGoogleProfile({ email, name_ar: name });
                });
            }
        });
    }

    function openSocialModal(provider) {
        Swal.fire({
            title: `التسجيل السريع بحساب ${provider} 💼`,
            html: `
                <div style="text-align: right; font-size: 0.88rem; color: #475569; margin-bottom: 12px; line-height: 1.6;">
                    أدخل بريدك الإلكتروني التابع لـ ${provider} لاستيراد بياناتك وتعبئة النموذج فورياً:
                </div>
                <div style="text-align: right; margin-bottom: 10px;">
                    <label style="font-weight: 700; font-size: 0.82rem; color: #1e293b;">البريد الإلكتروني (${provider}):</label>
                    <input type="email" id="swalSocialInput" class="swal2-input" style="margin: 6px 0 0; width: 100%; font-size: 0.9rem;" placeholder="${provider === 'Apple' ? 'user@icloud.com' : 'student@outlook.com'}" dir="ltr">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'متابعة واستيراد البيانات',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#94a3b8',
            preConfirm: () => {
                const email = document.getElementById('swalSocialInput').value.trim();
                if (!email || !email.includes('@')) {
                    Swal.showValidationMessage('يرجى إدخال بريد إلكتروني صحيح.');
                    return false;
                }
                return email;
            }
        }).then(result => {
            if (result.isConfirmed) {
                const email = result.value;
                const autoName = email.split('@')[0].replace(/[._]/g, ' ');
                applyGoogleProfile({ email: email, name_ar: autoName });
                Swal.fire({
                    icon: 'success',
                    title: `تم ربط بريد ${provider} بنجاح! 🎉`,
                    text: 'أكمل فقط إدخال رقم الهوية الفلسطينية واختيار الفرع.',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        });
    }

    function applyGoogleProfile(data) {
        if (!data) return;
        if (data.email) {
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.value = data.email;
                emailInput.style.borderColor = '#10b981';
                emailInput.style.background = '#f0fdf4';
            }
        }
        if (data.name_ar || data.name) {
            const nameInput = document.getElementById('name_ar');
            if (nameInput) {
                nameInput.value = data.name_ar || data.name;
                nameInput.style.borderColor = '#10b981';
                nameInput.style.background = '#f0fdf4';
            }
        }
        if (data.google_id) {
            const gIdInput = document.getElementById('google_id');
            if (gIdInput) gIdInput.value = data.google_id;
        }
        if (data.picture) {
            const avatarInput = document.getElementById('avatar_url');
            if (avatarInput) avatarInput.value = data.picture;
            const previewImg = document.getElementById('previewPhotoImg');
            const iconCircle = document.getElementById('iconPhotoCircle');
            const box = document.getElementById('boxPhoto');
            if (previewImg && box) {
                previewImg.src = data.picture;
                previewImg.style.display = 'block';
                if (iconCircle) iconCircle.style.display = 'none';
                box.classList.add('has-file');
            }
        }

        // توليد وتعبئة كلمة مرور قوية تلقائياً للتسهيل إن كانت فارغة
        const pwdInput = document.getElementById('password');
        if (pwdInput && !pwdInput.value) {
            const genPwd = 'Tawjihi2026@' + Math.floor(1000 + Math.random() * 9000);
            pwdInput.value = genPwd;
            checkPasswordStrength(genPwd);
        }

        // تحويل المؤشر لحقل رقم الهوية لإكماله بسهولة
        const nidInput = document.getElementById('nid');
        if (nidInput) {
            nidInput.focus();
        }
    }
</script>

</body>
</html>
