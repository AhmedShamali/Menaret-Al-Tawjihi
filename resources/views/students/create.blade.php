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
    <title>{{ __('إنشاء حساب طالب جديد') }} | {{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }}</title>

    <!-- الخطوط الرسمية المعتمدة للمنظومة (Tajawal & Alexandria) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800;900&family=Alexandria:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        if (window.axios) {
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
            }
        }
    </script>

    <style>
        :root {
            --ed-primary: #1d4ed8;
            --ed-primary-hover: #1e40af;
            --ed-primary-soft: #eff6ff;
            --ed-primary-border: #bfdbfe;

            --ed-accent-gold: #d97706;
            --ed-accent-gold-soft: #fef3c7;

            --ed-success: #16a34a;
            --ed-success-hover: #15803d;
            --ed-success-soft: #ecfdf5;

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

            --shadow-card: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
            --transition: all 0.2s ease;
        }

        * {
            margin: 0; padding: 0; box-sizing: border-box;
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

        a {
            color: var(--ed-primary);
            text-decoration: none;
            transition: var(--transition);
        }
        a:hover { color: var(--ed-primary-hover); }

        /* الشريط العلوي */
        .top-nav-bar {
            background: #ffffff;
            border-bottom: 1px solid var(--ed-border);
            padding: 12px 32px;
            width: 100%;
        }
        .top-nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--ed-text-main);
            font-weight: 800;
            font-size: 16px;
        }
        .brand-icon {
            width: 38px;
            height: 38px;
            background: var(--ed-primary-soft);
            color: var(--ed-primary);
            border: 1px solid var(--ed-primary-border);
            border-radius: var(--radius-sm);
            display: grid;
            place-items: center;
            font-size: 18px;
        }
        .nav-actions-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-lang {
            background: #ffffff;
            border: 1px solid var(--ed-border);
            color: var(--ed-text-body);
            padding: 5px 12px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-lang:hover {
            background: var(--ed-surface-alt);
            border-color: var(--ed-border-hover);
        }

        /* حاوية التسجيل */
        .register-page-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px 60px;
        }
        .register-card-box {
            background: var(--ed-surface);
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            width: 100%;
            max-width: 780px;
            padding: 36px 40px;
        }

        .reg-header {
            text-align: center;
            margin-bottom: 26px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--ed-border);
        }
        .reg-badge-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--ed-primary-soft);
            color: var(--ed-primary);
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 50px;
            margin-bottom: 8px;
            border: 1px solid var(--ed-primary-border);
        }
        .reg-header h1 {
            font-size: 20px;
            font-weight: 800;
            color: var(--ed-text-main);
            margin-bottom: 4px;
        }
        .reg-header p {
            font-size: 13px;
            color: var(--ed-text-muted);
        }

        /* شبكة الحقول */
        .grid-2-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 14px;
        }
        .input-group label {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ed-text-main);
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
            color: var(--ed-text-muted);
            font-size: 13px;
        }
        html[dir="rtl"] .input-control-wrap i.lead-icon { right: 12px; }
        html[dir="ltr"] .input-control-wrap i.lead-icon { left: 12px; }

        .form-input {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-family: inherit;
            color: var(--ed-text-main);
            background: #ffffff;
            outline: none;
            transition: var(--transition);
        }
        html[dir="rtl"] .form-input.has-icon { padding-right: 36px; padding-left: 12px; }
        html[dir="ltr"] .form-input.has-icon { padding-left: 36px; padding-right: 12px; }

        .form-input:focus {
            border-color: var(--ed-primary);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }

        .password-toggle-btn {
            position: absolute;
            background: none;
            border: none;
            color: var(--ed-text-muted);
            cursor: pointer;
            font-size: 13px;
            padding: 4px;
        }
        html[dir="rtl"] .password-toggle-btn { left: 10px; }
        html[dir="ltr"] .password-toggle-btn { right: 10px; }

        /* بطاقات الرفع */
        .upload-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }
        .upload-card-box {
            background: #ffffff;
            border: 1px dashed var(--ed-border-hover);
            border-radius: var(--radius-sm);
            padding: 12px 10px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }
        .upload-card-box:hover {
            border-color: var(--ed-primary);
            background: var(--ed-primary-soft);
        }
        .upload-card-box.has-file {
            border-style: solid;
            border-color: var(--ed-success);
            background: var(--ed-success-soft);
        }
        .upload-icon-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--ed-primary-soft);
            color: var(--ed-primary);
            display: grid;
            place-items: center;
            font-size: 15px;
            margin: 0 auto 6px auto;
        }
        .upload-card-box.has-file .upload-icon-circle {
            background: #dcfce7;
            color: var(--ed-success);
        }
        .upload-thumb-preview {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            margin: 0 auto 6px auto;
            display: none;
            border: 1px solid var(--ed-border);
        }
        .upload-card-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--ed-text-main);
            display: block;
            margin-bottom: 2px;
        }
        .upload-card-sub {
            font-size: 11px;
            color: var(--ed-text-muted);
            display: block;
        }
        .upload-file-status {
            font-size: 11px;
            color: var(--ed-success);
            font-weight: 700;
            margin-top: 4px;
            display: none;
        }

        /* زر الإرسال */
        .btn-register-submit {
            width: 100%;
            padding: 11px;
            border: 1px solid var(--ed-primary);
            border-radius: var(--radius-sm);
            background: var(--ed-primary);
            color: white;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
            margin-top: 6px;
        }
        .btn-register-submit:hover {
            background: var(--ed-primary-hover);
            border-color: var(--ed-primary-hover);
        }

        .login-switch-footer {
            text-align: center;
            margin-top: 16px;
            font-size: 12.5px;
            color: var(--ed-text-muted);
        }

        @media (max-width: 680px) {
            .register-card-box { padding: 24px 18px; }
            .grid-2-cols { grid-template-columns: 1fr; gap: 0; }
            .upload-grid { grid-template-columns: 1fr; }
            .top-nav-bar { padding: 10px 16px; }
        }
    </style>
</head>
<body>

    <!-- الشريط العلوي الرفيع -->
    <header class="top-nav-bar">
        <div class="top-nav-inner">
            <a href="/" class="brand-link">
                @if(\App\Models\Setting::get('site_logo'))
                    <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }}" style="max-height: 36px; max-width: 44px; object-fit: contain;">
                @else
                    <div class="brand-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                @endif
                <span>{{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }}</span>
            </a>

            <div class="nav-actions-right">
                <!-- زر تبديل اللغة (AR / EN) خالي تماماً من الكلمات العربية في وضع الإنجليزية -->
                @php $currentLocale = app()->getLocale(); @endphp
                <a href="{{ route('lang.switch', $currentLocale === 'ar' ? 'en' : 'ar') }}" 
                   class="btn-lang"
                   title="{{ $currentLocale === 'ar' ? 'Switch to English' : 'Switch to Arabic' }}">
                    <i class="fa-solid fa-globe" style="color: var(--ed-primary);"></i>
                    <span>{{ $currentLocale === 'ar' ? 'EN' : 'AR' }}</span>
                </a>

                <a href="{{ route('login') }}" class="btn-lang" style="color: var(--ed-primary); font-weight: 700;">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>{{ __('تسجيل الدخول') }}</span>
                </a>
            </div>
        </div>
    </header>

    <div class="register-page-wrapper">
        <div class="register-card-box">

            <div class="reg-header">
                <div class="reg-badge-tag">
                    <i class="fas fa-user-plus"></i> {{ __('عضوية طالب جديدة') }}
                </div>
                <h1>{{ __('إنشاء حسابك الأكاديمي') }}</h1>
                <p>{{ __('أدخل بياناتك للانضمام فورياً إلى المنصة ومتابعة دروسك') }}</p>
            </div>

            @if(session('error'))
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px; color: #dc2626; font-size: 12.5px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px; color: #1d4ed8; font-size: 12.5px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <form id="registerForm" onsubmit="handleRegisterSubmit(event)" enctype="multipart/form-data">
                @csrf
                <!-- الاسم الكامل ورقم الهوية -->
                <div class="grid-2-cols">
                    <div class="input-group">
                        <label for="name_ar">
                            <span>{{ __('الاسم الرباعي (بالعربية)') }} <span class="req">*</span></span>
                        </label>
                        <div class="input-control-wrap">
                            <i class="fas fa-user lead-icon"></i>
                            <input type="text" name="name_ar" id="name_ar" class="form-input has-icon" value="{{ old('name_ar') }}" placeholder="{{ __('مثال: أحمد محمد خليل علي') }}" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="nid"><span>{{ __('رقم الهوية الفلسطينية') }} <span class="req">*</span></span></label>
                        <div class="input-control-wrap">
                            <i class="fas fa-id-card lead-icon"></i>
                            <input type="text" name="nid" id="nid" maxlength="9" class="form-input has-icon" placeholder="{{ __('9 أرقام (مثال: 401234567)') }}" required>
                        </div>
                    </div>
                </div>

                <!-- البريد الرسمي بنطاق tawjihi.ps ورقم جوال الطالب -->
                <div class="grid-2-cols">
                    <div class="input-group">
                        <label for="email">
                            <span>{{ __('اسم المستخدم للبريد الأكاديمي') }} <span class="req">*</span></span>
                            <span style="color: var(--ed-primary); font-size: 11px; font-weight: 700;">
                                {{ __('نطاق @tawjihi.ps ثابت معتمد') }}
                            </span>
                        </label>
                        <div style="display: flex; align-items: stretch; background: #ffffff; border: 1px solid var(--ed-border); border-radius: var(--radius-sm); overflow: hidden;" id="emailBoxWrapper">
                            <input type="text" name="email" id="email" class="form-input" style="border: none; background: transparent; flex: 1; padding: 9px 12px; outline: none; font-weight: 700; color: #0f172a; direction: ltr; text-align: left;" placeholder="{{ __('أدخل اسم المستخدم بالإنجليزية (مثال: ahmed2026)') }}" required oninput="sanitizeUsername(this)">
                            <div style="background: var(--ed-surface-alt); color: var(--ed-text-main); font-weight: 700; font-size: 12px; padding: 9px 12px; border-inline-start: 1px solid var(--ed-border); user-select: none; direction: ltr; display: flex; align-items: center;">
                                @tawjihi.ps
                            </div>
                        </div>
                        <small style="color: var(--ed-text-muted); font-size: 11px; margin-top: 2px; display: block;">
                            {{ __('* اكتب اسم المستخدم فقط، وسيتم اعتماد البريد الرسمي: :email', ['email' => 'username@tawjihi.ps']) }}
                        </small>
                    </div>

                    <div class="input-group">
                        <label for="phone"><span>{{ __('رقم جوال الطالب / واتساب') }} <span class="req">*</span></span></label>
                        <div class="input-control-wrap">
                            <i class="fas fa-mobile-screen-button lead-icon"></i>
                            <input type="tel" name="phone" id="phone" class="form-input has-icon" placeholder="059XXXXXXX / 056XXXXXXX" required>
                        </div>
                    </div>
                </div>

                <!-- هاتف ولي الأمر واسم المدرسة -->
                <div class="grid-2-cols">
                    <div class="input-group">
                        <label for="guardian_phone">{{ __('رقم جوال ولي الأمر (للمتابعة الأكاديمية)') }}</label>
                        <div class="input-control-wrap">
                            <i class="fas fa-user-shield lead-icon"></i>
                            <input type="tel" name="guardian_phone" id="guardian_phone" class="form-input has-icon" placeholder="059XXXXXXX / 056XXXXXXX">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="school_name">{{ __('اسم المدرسة الثانوية') }}</label>
                        <div class="input-control-wrap">
                            <i class="fas fa-school lead-icon"></i>
                            <input type="text" name="school_name" id="school_name" class="form-input has-icon" placeholder="{{ __('مثال: مدرسة الحسين بن علي الثانوية') }}">
                        </div>
                    </div>
                </div>

                <!-- المرحلة/الفرع والجنس -->
                <div class="grid-2-cols">
                    <div class="input-group">
                        <label for="stage_id"><span>{{ __('الفرع الأكاديمي (توجيهي فلسطين)') }} <span class="req">*</span></span></label>
                        <div class="input-control-wrap">
                            <i class="fas fa-graduation-cap lead-icon"></i>
                            <select name="stage_id" id="stage_id" class="form-input has-icon" required onchange="onStageChanged(this.value)">
                                @foreach($stages as $stg)
                                    @if($stg->grade_level >= 120)
                                        @php
                                            $stgDispName = (app()->getLocale() === 'en' && !empty($stg->name_en)) 
                                                ? $stg->name_en 
                                                : ($stg->label_ar ?? $stg->name_ar ?? $stg->name);
                                        @endphp
                                        <option value="{{ $stg->id }}" data-grade="{{ $stg->grade_level }}" {{ ($stg->grade_level == 122 || str_contains($stg->label_ar ?? '', 'علمي')) ? 'selected' : '' }}>
                                            {{ $stg->icon ?? '🎓' }} {{ $stgDispName }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="gender"><span>{{ __('الجنس') }} <span class="req">*</span></span></label>
                        <div class="input-control-wrap">
                            <i class="fas fa-venus-mars lead-icon"></i>
                            <select name="gender" id="gender" class="form-input has-icon" required>
                                <option value="ذكر" selected>{{ __('ذكر (طالب)') }}</option>
                                <option value="أنثى">{{ __('أنثى (طالبة)') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- المحافظة والعمر -->
                <div class="grid-2-cols">
                    <div class="input-group">
                        <label for="city"><span>{{ __('المحافظة / المدينة') }} <span class="req">*</span></span></label>
                        <div class="input-control-wrap">
                            <i class="fas fa-map-marker-alt lead-icon"></i>
                            <select name="city" id="city" class="form-input has-icon">
                                <option value="القدس">{{ __('القدس الشريف 🕌') }}</option>
                                <option value="رام الله والبيرة" selected>{{ __('رام الله والبيرة') }}</option>
                                <option value="غزة">{{ __('غزة العزة 🌿') }}</option>
                                <option value="نابلس">{{ __('نابلس (جبل النار)') }}</option>
                                <option value="الخليل">{{ __('الخليل') }}</option>
                                <option value="جنين">{{ __('جنين القسام') }}</option>
                                <option value="طولكرم">{{ __('طولكرم') }}</option>
                                <option value="قلقيلية">{{ __('قلقيلية') }}</option>
                                <option value="بيت لحم">{{ __('بيت لحم') }}</option>
                                <option value="سلفيت">{{ __('سلفيت') }}</option>
                                <option value="أريحا">{{ __('أريحا والأغوار') }}</option>
                                <option value="طوباس">{{ __('طوباس') }}</option>
                                <option value="خان يونس">{{ __('خان يونس') }}</option>
                                <option value="رفح">{{ __('رفح') }}</option>
                                <option value="شمال غزة">{{ __('شمال غزة (جباليا)') }}</option>
                                <option value="دير البلح">{{ __('دير البلح والوسطى') }}</option>
                                <option value="أخرى">{{ __('خارج فلسطين / أخرى') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="age">{{ __('العمر') }}</label>
                        <div class="input-control-wrap">
                            <i class="fas fa-calendar-check lead-icon"></i>
                            <input type="number" name="age" id="age" value="18" min="15" max="25" class="form-input has-icon">
                        </div>
                    </div>
                </div>

                <!-- المرفقات والوثائق: الصورة الشخصية وصورة الهوية -->
                <div style="margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <label style="font-size: 12.5px; font-weight: 700; color: var(--ed-text-main);">
                            <i class="fas fa-camera" style="color: var(--ed-primary);"></i> {{ __('الصورة الشخصية وصورة الهوية الفلسطينية:') }}
                        </label>
                        <span style="font-size: 11px; color: var(--ed-text-muted);">{{ __('(اختياري وموصى به للاعتماد الرسمي)') }}</span>
                    </div>

                    <div class="upload-grid">
                        <!-- 1. صندوق رفع الصورة الشخصية -->
                        <div class="upload-card-box" id="boxPhoto" onclick="document.getElementById('photoInput').click()">
                            <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;" onchange="previewStudentPhoto(this)">
                            <img id="previewPhotoImg" class="upload-thumb-preview" alt="{{ __('الصورة الشخصية للطالب') }}">
                            <div class="upload-icon-circle" id="iconPhotoCircle">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <span class="upload-card-title">{{ __('الصورة الشخصية للطالب') }}</span>
                            <span class="upload-card-sub" id="photoSubText">{{ __('انقر لاختيار صورة واضحة لوجه الطالب (JPG/PNG)') }}</span>
                            <div class="upload-file-status" id="photoStatusBadge">
                                <i class="fas fa-check-circle"></i> {{ __('تم إرفاق الصورة') }}
                            </div>
                        </div>

                        <!-- 2. صندوق رفع صورة الهوية الفلسطينية -->
                        <div class="upload-card-box" id="boxIdPhoto" onclick="document.getElementById('idPhotoInput').click()">
                            <input type="file" name="id_photo" id="idPhotoInput" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" style="display: none;" onchange="previewStudentIdPhoto(this)">
                            <img id="previewIdPhotoImg" class="upload-thumb-preview" alt="{{ __('صورة بطاقة الهوية الفلسطينية') }}">
                            <div class="upload-icon-circle" id="iconIdCircle">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <span class="upload-card-title">{{ __('صورة بطاقة الهوية الفلسطينية') }}</span>
                            <span class="upload-card-sub" id="idSubText">{{ __('صورة البطاقة أو شهادة الميلاد للمطابقة الرسمية') }}</span>
                            <div class="upload-file-status" id="idStatusBadge">
                                <i class="fas fa-check-circle"></i> {{ __('تم إرفاق الوثيقة') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قائمة المواد التابعة للفرع للاشتراك بها -->
                <div class="input-group" style="margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label style="font-weight: 700; color: var(--ed-text-main); font-size: 12.5px;">
                            <i class="fas fa-book-bookmark" style="color: var(--ed-primary);"></i> {{ __('المواد المقررة للاشتراك بها في الفرع:') }}
                        </label>
                        <span style="font-size: 11px; color: var(--ed-success); font-weight: 700;">{{ __('(جميع المواد مفعلة تلقائياً أو اختر ما يناسبك)') }}</span>
                    </div>
                    <div id="subjectsSelectionContainer" style="background: #ffffff; border: 1px solid var(--ed-border); border-radius: var(--radius-sm); padding: 10px 14px; max-height: 180px; overflow-y: auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px;">
                        <!-- يتم ملء المواد ديناميكياً بواسطة جافاسكريبت -->
                    </div>
                </div>

                <!-- كلمة المرور -->
                <div class="input-group">
                    <label for="password"><span>{{ __('كلمة المرور (6 خانات على الأقل)') }} <span class="req">*</span></span></label>
                    <div class="input-control-wrap">
                        <i class="fas fa-lock lead-icon"></i>
                        <input type="password" name="password" id="password" minlength="6" class="form-input has-icon" placeholder="••••••••" required oninput="checkPasswordStrength(this.value)">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye" id="pwdEye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="btnSubmitRegister" class="btn-register-submit">
                    <span>{{ __('إنشاء الحساب وبدء التعلم فورياً') }}</span>
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                </button>

                <div class="login-switch-footer">
                    {{ __('لديك حساب بالفعل؟') }}
                    <a href="{{ route('login') }}">{{ __('تسجيل الدخول هنا') }}</a>
                </div>
            </form>

        </div>
    </div>

<script>
    const stagesData = @json($stages);
    const isEn = {{ app()->getLocale() === 'en' ? 'true' : 'false' }};
    const regI18n = {
        allActivated: "{{ __('سيتم تفعيل كافة مواد المنهاج تلقائياً عند التسجيل.') }}",
        creating: "{{ __('جاري إنشاء الحساب الأكاديمي...') }}",
        createdTitle: "{{ __('تم إنشاء حسابك بنجاح! 🎉') }}",
        confirmBtn: "{{ __('تأكيد واستكمال') }}",
        errorTitle: "{{ __('تعذر إنشاء الحساب') }}",
        errorDefault: "{{ __('يرجى مراجعة الحقول وتصحيح الأخطاء.') }}",
        nidError: "{{ __('رقم الهوية الفلسطينية يجب أن يتكون من 9 أرقام بالضبط.') }}",
        pwdError: "{{ __('كلمة المرور يجب أن تكون 6 خانات على الأقل.') }}",
        photoAttached: "{{ __('تم إرفاق الصورة') }}",
        docAttached: "{{ __('تم إرفاق الوثيقة') }}",
        currency: "{{ app()->getLocale() === 'ar' ? '₪' : 'ILS' }}"
    };

    function onStageChanged(stageId) {
        const container = document.getElementById('subjectsSelectionContainer');
        if (!container) return;

        const currentStage = stagesData.find(s => s.id == stageId || s.grade_level == stageId);
        if (!currentStage || !currentStage.subjects || currentStage.subjects.length === 0) {
            container.innerHTML = `<div style="color: #64748b; font-size: 12px; padding: 6px;">${regI18n.allActivated}</div>`;
            return;
        }

        let html = '';
        currentStage.subjects.forEach(sub => {
            const basePrice = parseFloat(sub.price_ils) || 150;
            const discountPrice = (sub.discount_price_ils !== null && parseFloat(sub.discount_price_ils) > 0) ? parseFloat(sub.discount_price_ils) : null;
            const isFree = (sub.is_free == 1);
            const subTitle = (isEn && sub.name_en) ? sub.name_en : (sub.name_ar || sub.name);

            let priceHtml = '';
            if (isFree) {
                priceHtml = `<span style="color: #15803d; font-weight: 800;">{{ __('مجانية 🎁') }}</span>`;
            } else if (discountPrice && discountPrice < basePrice) {
                const pct = Math.round(((basePrice - discountPrice) / basePrice) * 100);
                priceHtml = `
                    <span style="text-decoration: line-through; color: #94a3b8; font-size: 10.5px; margin-inline-end: 4px;">${basePrice} ${regI18n.currency}</span>
                    <strong style="color: #ea580c; font-size: 12px;">${discountPrice} ${regI18n.currency}</strong>
                    <span style="background: #fef2f2; color: #dc2626; font-size: 9.5px; font-weight: 800; padding: 1px 5px; border-radius: 4px; margin-inline-start: 4px;">-${pct}%</span>
                `;
            } else {
                priceHtml = `<span style="font-size: 11px; color: #475569; font-weight: 700;">${basePrice} ${regI18n.currency}</span>`;
            }

            html += `
                <label style="display: flex; align-items: center; gap: 8px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 7px 10px; cursor: pointer; user-select: none;">
                    <input type="checkbox" name="subject_ids[]" value="${sub.id}" checked style="width: 15px; height: 15px; accent-color: var(--ed-primary); cursor: pointer;">
                    <span style="font-size: 1rem;">${sub.icon || '📘'}</span>
                    <div style="flex: 1;">
                        <div style="font-size: 12px; font-weight: 700; color: #0f172a;">${subTitle}</div>
                        <div style="display: flex; align-items: center; gap: 2px; margin-top: 2px;">${priceHtml}</div>
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

    function sanitizeUsername(input) {
        input.value = input.value.replace(/[^a-zA-Z0-9._-]/g, '').toLowerCase();
    }

    function togglePasswordVisibility() {
        const pwd = document.getElementById('password');
        const eye = document.getElementById('pwdEye');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            eye.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pwd.type = 'password';
            eye.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function checkPasswordStrength(val) {
        // strength indicator hook if needed
    }

    function handleRegisterSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('registerForm');
        const btn = document.getElementById('btnSubmitRegister');

        const nid = document.getElementById('nid').value.trim();
        if (nid.length !== 9 || !/^\d+$/.test(nid)) {
            Swal.fire({
                icon: 'warning',
                title: regI18n.errorTitle,
                text: regI18n.nidError,
                confirmButtonColor: '#1d4ed8'
            });
            return;
        }

        const pwd = document.getElementById('password').value;
        if (pwd.length < 6) {
            Swal.fire({
                icon: 'warning',
                title: regI18n.errorTitle,
                text: regI18n.pwdError,
                confirmButtonColor: '#1d4ed8'
            });
            return;
        }

        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${regI18n.creating}`;

        const formData = new FormData(form);

        axios.post('{{ route("students.store") }}', formData)
            .then(res => {
                Swal.fire({
                    icon: 'success',
                    title: regI18n.createdTitle,
                    text: res.data.message || '',
                    confirmButtonColor: '#1d4ed8',
                    confirmButtonText: regI18n.confirmBtn
                }).then(() => {
                    if (res.data.redirect) {
                        window.location.href = res.data.redirect;
                    } else {
                        window.location.href = '{{ route("dashboard") }}';
                    }
                });
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = `<span>{{ __('إنشاء الحساب وبدء التعلم فورياً') }}</span> <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>`;
                let msg = regI18n.errorDefault;
                if (err.response?.data?.errors) {
                    const first = Object.values(err.response.data.errors)[0];
                    if (Array.isArray(first)) msg = first[0];
                } else if (err.response?.data?.message) {
                    msg = err.response.data.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: regI18n.errorTitle,
                    text: msg,
                    confirmButtonColor: '#ef4444'
                });
            });
    }
</script>

</body>
</html>
