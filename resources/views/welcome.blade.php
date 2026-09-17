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

    <title>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} - بوابة الثانوية العامة الفلسطينية 🇵🇸</title>
    <meta name="description" content="منصة منارة التوجيهي التعليمية: شروحات مبسطة، بنك اختبارات وزارية محلولة، ومتابعة دراسية بإشراف أ. أحمد حسين شمالي.">

    <!-- خط كلاسيكي عربي مريح وواضح -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ==========================================================================
           تصميم كلاسيكي بسيط وهادئ (زي المواقع التعليمية التقليدية)
           ========================================================================== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', Tahoma, Arial, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: #1e293b;
            font-size: 14px;
            line-height: 1.6;
            direction: rtl;
            text-align: right;
        }

        a {
            color: #1d4ed8;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }

        /* الحاوية الرئيسية للموقع بتنسيق كلاسيكي متناسق */
        .site-wrapper {
            max-width: 1100px;
            margin: 15px auto;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        /* 1. الشريط العلوي الرفيع */
        .top-info-bar {
            background-color: #172554;
            color: #e2e8f0;
            padding: 6px 15px;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #1e3a8a;
        }
        .top-info-bar .date-info {
            display: flex;
            gap: 15px;
        }
        .top-info-bar .top-links a {
            color: #93c5fd;
            margin-right: 12px;
            font-size: 12px;
        }
        .top-info-bar .top-links a:hover {
            color: #ffffff;
        }

        /* 2. ترويسة الموقع (Header) الكلاسيكية */
        .main-header {
            background: linear-gradient(to bottom, #1e3a8a, #1e40af);
            color: #ffffff;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #f59e0b;
        }
        .header-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .header-logo-box {
            width: 58px;
            height: 58px;
            background-color: #ffffff;
            color: #1e3a8a;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            border: 2px solid #f59e0b;
        }
        .header-titles h1 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 2px;
            color: #ffffff;
        }
        .header-titles p {
            font-size: 13px;
            color: #bfdbfe;
            font-weight: 600;
        }
        .header-supervisor {
            text-align: left;
            background: rgba(0, 0, 0, 0.2);
            padding: 8px 14px;
            border-radius: 4px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .header-supervisor .label {
            font-size: 11px;
            color: #fef08a;
            display: block;
        }
        .header-supervisor .name {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
        }

        /* 3. شريط القوائم الرئيسي (Classic Navbar) */
        .main-navbar {
            background-color: #0f172a;
            border-bottom: 1px solid #334155;
        }
        .nav-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 10px;
            flex-wrap: wrap;
        }
        .nav-list {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .nav-list li a {
            display: block;
            padding: 10px 14px;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            border-left: 1px solid #1e293b;
            text-decoration: none;
            transition: background-color 0.15s;
        }
        .nav-list li:first-child a {
            border-right: 1px solid #1e293b;
        }
        .nav-list li a:hover,
        .nav-list li a.active {
            background-color: #2563eb;
            color: #ffffff;
        }
        .nav-actions {
            display: flex;
            gap: 8px;
            padding: 6px 0;
        }
        .btn-nav-login {
            background-color: #2563eb;
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-nav-login:hover {
            background-color: #1d4ed8;
            text-decoration: none;
        }
        .btn-nav-register {
            background-color: #16a34a;
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-nav-register:hover {
            background-color: #15803d;
            text-decoration: none;
        }

        /* 4. شريط الإعلانات والتنبيهات (Notice Bar) */
        .notice-bar {
            background-color: #fef3c7;
            border-bottom: 1px solid #fde68a;
            color: #92400e;
            padding: 7px 15px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .notice-badge {
            background-color: #d97706;
            color: #ffffff;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: 700;
            white-space: nowrap;
        }
        .notice-text {
            font-weight: 600;
        }

        /* 5. جسم الصفحة بتوزيع كلاسيكي (عمود رئيسي + عمود جانبي) */
        .site-body {
            padding: 16px;
            display: flex;
            gap: 16px;
        }
        .main-column {
            flex: 1;
            min-width: 0;
        }
        .side-column {
            width: 320px;
            flex-shrink: 0;
        }

        /* 6. الصناديق الكلاسيكية (Classic Panels) */
        .panel {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            margin-bottom: 16px;
            overflow: hidden;
        }
        .panel-header {
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #f59e0b;
        }
        .panel-header.green {
            background-color: #166534;
            border-bottom-color: #22c55e;
        }
        .panel-header.slate {
            background-color: #334155;
            border-bottom-color: #64748b;
        }
        .panel-header .title {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .panel-body {
            padding: 14px;
        }

        /* صندوق الترحيب الكلاسيكي */
        .welcome-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .welcome-box h2 {
            font-size: 18px;
            color: #1e3a8a;
            font-weight: 800;
            margin-bottom: 8px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 6px;
        }
        .welcome-box p {
            font-size: 13.5px;
            color: #334155;
            margin-bottom: 14px;
            line-height: 1.7;
        }
        .welcome-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-action-primary {
            background-color: #16a34a;
            color: #ffffff;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #15803d;
        }
        .btn-action-primary:hover {
            background-color: #15803d;
            text-decoration: none;
        }
        .btn-action-secondary {
            background-color: #1e40af;
            color: #ffffff;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #1e3a8a;
        }
        .btn-action-secondary:hover {
            background-color: #1e3a8a;
            text-decoration: none;
        }

        /* جدول كلاسيكي بسيط */
        .classic-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .classic-table th, 
        .classic-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: right;
        }
        .classic-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
        }
        .classic-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .classic-table tr:hover td {
            background-color: #eff6ff;
        }

        /* شبكة الفروع الدراسية الكلاسيكية */
        .branches-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .branch-item {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            background-color: #ffffff;
            padding: 10px 12px;
            border-right: 4px solid #1e40af;
        }
        .branch-item.lit { border-right-color: #059669; }
        .branch-item.bus { border-right-color: #d97706; }
        .branch-item.ind { border-right-color: #dc2626; }
        .branch-item.isl { border-right-color: #7c3aed; }
        .branch-item h4 {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .branch-item p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 6px;
        }
        .branch-subjects-tag {
            font-size: 11px;
            color: #1e40af;
            font-weight: 600;
            background: #eff6ff;
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
        }

        /* مميزات المنصة في قائمة واضحة */
        .features-bullet-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .features-bullet-list li {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .features-bullet-list li:last-child {
            border-bottom: none;
        }
        .features-bullet-list li i {
            color: #16a34a;
            margin-top: 3px;
        }

        /* القوائم الجانبية السريعة */
        .side-links-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .side-links-list li a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #334155;
            font-weight: 600;
        }
        .side-links-list li a:hover {
            background-color: #eff6ff;
            color: #1d4ed8;
            text-decoration: none;
        }
        .side-links-list li:last-child a {
            border-bottom: none;
        }

        /* صندوق المشرف والتواصل في الشريط الجانبي */
        .supervisor-card {
            text-align: center;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .supervisor-card .sup-avatar {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: #1e3a8a;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 8px;
            border: 2px solid #f59e0b;
        }
        .supervisor-card h4 {
            font-size: 14px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .supervisor-card span {
            font-size: 12px;
            color: #64748b;
            display: block;
            margin-bottom: 10px;
        }
        .btn-whatsapp-direct {
            background-color: #25d366;
            color: #ffffff;
            display: block;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            border: 1px solid #1eb956;
        }
        .btn-whatsapp-direct:hover {
            background-color: #1eb956;
            color: #ffffff;
            text-decoration: none;
        }

        /* 7. التذييل الكلاسيكي (Footer) */
        .site-footer {
            background-color: #0f172a;
            color: #94a3b8;
            font-size: 12px;
            border-top: 3px solid #1e40af;
        }
        .footer-inner {
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .footer-links a {
            color: #cbd5e1;
            margin-right: 12px;
        }
        .footer-links a:hover {
            color: #ffffff;
        }
        .footer-note {
            background-color: #020617;
            padding: 8px 20px;
            text-align: center;
            color: #64748b;
            font-size: 11px;
            border-top: 1px solid #1e293b;
        }

        /* استجابة الشاشات الصغيرة */
        @media (max-width: 860px) {
            .site-wrapper {
                margin: 0;
                border: none;
            }
            .main-header {
                flex-direction: column;
                text-align: center;
                gap: 12px;
            }
            .header-brand {
                flex-direction: column;
            }
            .header-supervisor {
                text-align: center;
            }
            .site-body {
                flex-direction: column;
            }
            .side-column {
                width: 100%;
            }
            .branches-list {
                grid-template-columns: 1fr;
            }
            .nav-container {
                flex-direction: column;
                gap: 6px;
                padding-bottom: 8px;
            }
            .nav-list {
                flex-wrap: wrap;
                justify-content: center;
            }
            .nav-list li a {
                border: none;
                padding: 7px 10px;
            }
            .footer-inner {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="site-wrapper">

    <!-- 1. شريط التاريخ والبسملة في الأعلى -->
    <div class="top-info-bar">
        <div class="date-info">
            <span><i class="fa-regular fa-calendar-days"></i> {{ date('Y/m/d') }} م</span>
            <span>بِسْمِ اللَّـهِ الرَّحْمَـٰنِ الرَّحِيمِ</span>
        </div>
        <div class="top-links">
            <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> الرئيسية</a>
            <a href="{{ route('public.faq') }}"><i class="fa-solid fa-circle-question"></i> الأسئلة الشائعة</a>
            <a href="{{ route('public.contact') }}"><i class="fa-solid fa-envelope"></i> اتصل بنا</a>
        </div>
    </div>

    <!-- 2. الترويسة الرسمية الكلاسيكية -->
    <header class="main-header">
        <div class="header-brand">
            <div class="header-logo-box">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="header-titles">
                <h1>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸</h1>
                <p>بوابة ومنظومة الثانوية العامة لطلبة فلسطين - المنهاج الوزاري المعتمد</p>
            </div>
        </div>

        <div class="header-supervisor">
            <span class="label">المشرف العام على المنصة:</span>
            <span class="name">أ. أحمد حسين شمالي</span>
        </div>
    </header>

    <!-- 3. شريط القوائم الكلاسيكي (Navbar) -->
    <nav class="main-navbar">
        <div class="nav-container">
            <ul class="nav-list">
                <li><a href="{{ route('home') }}" class="active"><i class="fa-solid fa-house"></i> الرئيسية</a></li>
                <li><a href="#branches"><i class="fa-solid fa-book-open"></i> فروع التوجيهي</a></li>
                <li><a href="#services"><i class="fa-solid fa-list-check"></i> خدمات المنصة</a></li>
                @if(Route::has('tawjihi.archive'))
                    <li><a href="{{ route('tawjihi.archive') }}"><i class="fa-solid fa-folder-open"></i> بنك الامتحانات الوزارية</a></li>
                @endif
                @if(Route::has('tawjihi.calculator'))
                    <li><a href="{{ route('tawjihi.calculator') }}"><i class="fa-solid fa-calculator"></i> حساب المعدل</a></li>
                @endif
                <li><a href="{{ route('public.contact') }}"><i class="fa-solid fa-phone"></i> تواصل مع الإدارة</a></li>
            </ul>

            <div class="nav-actions">
                @if(Auth::guard('student')->check() || Auth::check())
                    <a href="{{ route('dashboard') }}" class="btn-nav-login">
                        <i class="fa-solid fa-gauge"></i> لوحة التحكم
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-nav-login">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> تسجيل الدخول
                    </a>
                    <a href="{{ route('students.create') }}" class="btn-nav-register">
                        <i class="fa-solid fa-user-plus"></i> تسجيل طالب جديد
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- 4. شريط الإعلانات والتنبيهات المباشر -->
    <div class="notice-bar">
        <span class="notice-badge">📢 إعلان هام</span>
        <span class="notice-text">
            أهلاً وسهلاً بطلبة الثانوية العامة (التوجيهي) لدورة {{ date('Y') }}. تم تفعيل التسجيل الإلكتروني ومتابعة الحصص والنماذج الوزارية بإشراف نخبة المعلمين.
        </span>
    </div>

    <!-- 5. جسم الصفحة: تقسيم كلاسيكي (محتوى رئيسي + شريط جانبي) -->
    <div class="site-body">

        <!-- العمود الرئيسي (يمين) -->
        <main class="main-column">

            <!-- صندوق الترحيب والبدء السريع -->
            <div class="welcome-box">
                <h2>مرحباً بكم في منصة منارة التوجيهي التعليمية</h2>
                <p>
                    منصة فلسطينية متخصصة تأسست لمساندة طلبة الثانوية العامة (التوجيهي) في كافة محافظات الوطن (القدس، الضفة الغربية، وقطاع غزة). نوفر لكم شروحات منهجية مبسطة، ونماذج امتحانات وزارية سابقة مع نماذج الحل المعتمدة، لتمكينكم من نيل أعلى المعدلات والتفوق بإذن الله.
                </p>

                <div class="welcome-buttons">
                    @if(Auth::guard('student')->check() || Auth::check())
                        <a href="{{ route('dashboard') }}" class="btn-action-primary">
                            <i class="fa-solid fa-arrow-left"></i> الدخول إلى لوحة التحكم الخاصة بك
                        </a>
                    @else
                        <a href="{{ route('students.create') }}" class="btn-action-primary">
                            <i class="fa-solid fa-user-pen"></i> تسجيل حساب طالب جديد
                        </a>
                        <a href="{{ route('login') }}" class="btn-action-secondary">
                            <i class="fa-solid fa-key"></i> تسجيل الدخول للنظام
                        </a>
                    @endif
                </div>
            </div>

            <!-- صندوق فروع الثانوية العامة -->
            <div class="panel" id="branches">
                <div class="panel-header">
                    <div class="title">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>فروع الثانوية العامة المعتمدة (المنهاج الفلسطيني)</span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="branches-list">
                        <!-- الفرع العلمي -->
                        <div class="branch-item">
                            <h4>الفرع العلمي</h4>
                            <p>شروحات تفصيلية وحل مسائل وتمارين الكتاب الوزاري لمواد التخصص.</p>
                            <span class="branch-subjects-tag">الرياضيات • الفيزياء • الكيمياء • الأحياء</span>
                        </div>

                        <!-- الفرع الأدبي -->
                        <div class="branch-item lit">
                            <h4>الفرع الأدبي</h4>
                            <p>تبسيط مفاهيم المنهاج الأدبي وحل أسئلة السنوات السابقة والنصوص الوزارية.</p>
                            <span class="branch-subjects-tag">اللغة العربية • اللغة الإنجليزية • التاريخ • الجغرافيا</span>
                        </div>

                        <!-- فرع الريادة والأعمال -->
                        <div class="branch-item bus">
                            <h4>فرع الريادة والأعمال</h4>
                            <p>تغطية شاملة للمسائل المحاسبية، دراسات الجدوى، والإدارة والاقتصاد.</p>
                            <span class="branch-subjects-tag">المحاسبة • الإدارة والاقتصاد • المشاريع الصغيرة</span>
                        </div>

                        <!-- الفرع الشرعي والمهني -->
                        <div class="branch-item isl">
                            <h4>الفرع الشرعي والصناعي</h4>
                            <p>متابعة مخصصة للمساقات التخصصية والعلوم الشرعية والمواد المهنية.</p>
                            <span class="branch-subjects-tag">العلوم الإسلامية • الرياضيات الصناعية • الفيزياء التطبيقية</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- صندوق محتويات وخدمات المنصة -->
            <div class="panel" id="services">
                <div class="panel-header slate">
                    <div class="title">
                        <i class="fa-solid fa-list-check"></i>
                        <span>خدمات ومحتويات المنظومة للطلبة</span>
                    </div>
                </div>
                <div class="panel-body">
                    <ul class="features-bullet-list">
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <div>
                                <strong>شروحات مرئية منظمة:</strong> دروس مسجلة بجودة عالية مرتبة حسب فهرس الكتاب المدرسي المقرر من وزارة التربية والتعليم.
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <div>
                                <strong>بنك الامتحانات الوزارية:</strong> نماذج الامتحانات الوزارية الرسمية للأعوام السابقة مع مفاتيح الإجابة النموذجية وطرق توزيع العلامات.
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <div>
                                <strong>تلاخيص وملازم دراسية (PDF):</strong> تلخيص القوانين والقواعد وأهم الأسئلة المتكررة لسهولة المراجعة والطباعة المنزلية.
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <div>
                                <strong>متابعة وإشراف مباشر:</strong> إشراف الأستاذ أحمد حسين شمالي والتواصل لمعالجة أي صعوبة في المناهج أو تفعيل الاشتراكات.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- صندوق خطوات البدء البسيطة -->
            <div class="panel">
                <div class="panel-header green">
                    <div class="title">
                        <i class="fa-solid fa-shoe-prints"></i>
                        <span>كيف تبدأ الدراسة في المنصة؟ (٣ خطوات بسيطة)</span>
                    </div>
                </div>
                <div class="panel-body">
                    <table class="classic-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">الخطوة</th>
                                <th>الإجراء المطلوب</th>
                                <th style="width: 140px;">الرابط السريع</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>الأولى</strong></td>
                                <td>إنشاء حساب جديد كطالب وتعبئة بيانات الفرع الدراسي والاسم ورقم الهاتف.</td>
                                <td><a href="{{ route('students.create') }}">اضغط للتسجيل</a></td>
                            </tr>
                            <tr>
                                <td><strong>الثانية</strong></td>
                                <td>تسجيل الدخول إلى حسابك واختيار المساقات والمواد الدراسية المقررة لفرعك.</td>
                                <td><a href="{{ route('login') }}">تسجيل الدخول</a></td>
                            </tr>
                            <tr>
                                <td><strong>الثالثة</strong></td>
                                <td>مشاهدة الشروحات وتحميل الملفات وحل الاختبارات الوزارية بانتظام.</td>
                                <td><a href="{{ route('dashboard') }}">لوحة التحكم</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

        <!-- العمود الجانبي (يسار) -->
        <aside class="side-column">

            <!-- 1. صندوق الحساب والدخول السريع -->
            <div class="panel">
                <div class="panel-header">
                    <div class="title">
                        <i class="fa-solid fa-user-lock"></i>
                        <span>بوابة الحساب والدخول</span>
                    </div>
                </div>
                <div class="panel-body" style="text-align: center;">
                    @if(Auth::guard('student')->check() || Auth::check())
                        <div style="margin-bottom: 12px; font-weight: 700; color: #1e3a8a;">
                            أهلاً بك: 
                            @if(Auth::guard('student')->check())
                                {{ Auth::guard('student')->user()->name }}
                            @else
                                {{ Auth::user()->name }}
                            @endif
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn-action-primary" style="width: 100%; justify-content: center;">
                            <i class="fa-solid fa-gauge"></i> الانتقال للوحة التحكم
                        </a>
                    @else
                        <p style="font-size: 12.5px; color: #64748b; margin-bottom: 12px;">
                            سجل دخولك لمتابعة دروسك واختباراتك أو أنشئ حسابك خلال دقيقة واحدة.
                        </p>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <a href="{{ route('login') }}" class="btn-action-secondary" style="justify-content: center;">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i> تسجيل الدخول للنظام
                            </a>
                            <a href="{{ route('students.create') }}" class="btn-action-primary" style="justify-content: center;">
                                <i class="fa-solid fa-user-plus"></i> تسجيل حساب طالب جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. صندوق إحصائيات المنصة (جدول كلاسيكي مخطط) -->
            <div class="panel">
                <div class="panel-header slate">
                    <div class="title">
                        <i class="fa-solid fa-chart-simple"></i>
                        <span>إحصائيات المنصة</span>
                    </div>
                </div>
                <div class="panel-body" style="padding: 0;">
                    <table class="classic-table" style="border: none;">
                        <tbody>
                            <tr>
                                <td><i class="fa-solid fa-users" style="color: #1e40af;"></i> الطلبة المسجلين</td>
                                <td style="text-align: left; font-weight: 800; color: #1e3a8a;">
                                    {{ number_format($stats['students'] ?? 1200) }} طالب
                                </td>
                            </tr>
                            <tr>
                                <td><i class="fa-solid fa-book" style="color: #059669;"></i> المساقات الوزارية</td>
                                <td style="text-align: left; font-weight: 800; color: #059669;">
                                    {{ number_format($stats['subjects'] ?? 18) }} مساق
                                </td>
                            </tr>
                            <tr>
                                <td><i class="fa-solid fa-video" style="color: #d97706;"></i> الدروس والشروحات</td>
                                <td style="text-align: left; font-weight: 800; color: #d97706;">
                                    {{ number_format($stats['lessons'] ?? 350) }} درس
                                </td>
                            </tr>
                            <tr>
                                <td><i class="fa-solid fa-file-lines" style="color: #dc2626;"></i> النماذج والامتحانات</td>
                                <td style="text-align: left; font-weight: 800; color: #dc2626;">
                                    {{ number_format($stats['exams'] ?? 150) }} نموذج
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. بطاقة المشرف العام والتواصل -->
            <div class="panel">
                <div class="panel-header green">
                    <div class="title">
                        <i class="fa-solid fa-headset"></i>
                        <span>الإشراف والتواصل المباشر</span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="supervisor-card">
                        <div class="sup-avatar">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <h4>أ. أحمد حسين شمالي</h4>
                        <span>المشرف العام على المنظومة الأكاديمية</span>

                        @php
                            $waNumber = '970597694385';
                            $waText = urlencode("السلام عليكم أستاذ أحمد شمالي، أود الاستفسار بخصوص منصة منارة التوجيهي.");
                        @endphp

                        <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" class="btn-whatsapp-direct">
                            <i class="fa-brands fa-whatsapp"></i> تواصل عبر الواتساب (0597694385)
                        </a>

                        <div style="font-size: 11.5px; color: #64748b; margin-top: 8px;">
                            رقم بديل: 0567897212 • فلسطين 🇵🇸
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. روابط مساعدة وسريعة -->
            <div class="panel">
                <div class="panel-header">
                    <div class="title">
                        <i class="fa-solid fa-link"></i>
                        <span>روابط سريعة ومفيدة</span>
                    </div>
                </div>
                <div class="panel-body" style="padding: 0;">
                    <ul class="side-links-list">
                        <li>
                            <a href="{{ route('public.faq') }}">
                                <i class="fa-solid fa-circle-question" style="color: #1e40af;"></i> الأسئلة المتكررة وإجاباتها
                            </a>
                        </li>
                        @if(Route::has('tawjihi.calculator'))
                            <li>
                                <a href="{{ route('tawjihi.calculator') }}">
                                    <i class="fa-solid fa-calculator" style="color: #059669;"></i> حاسبة معدل التوجيهي التفاعلية
                                </a>
                            </li>
                        @endif
                        @if(Route::has('tawjihi.archive'))
                            <li>
                                <a href="{{ route('tawjihi.archive') }}">
                                    <i class="fa-solid fa-folder-tree" style="color: #d97706;"></i> أرشيف الامتحانات الوزارية السابقة
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('public.terms') }}">
                                <i class="fa-solid fa-file-contract" style="color: #64748b;"></i> شروط الاستخدام والاشتراك
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.contact') }}">
                                <i class="fa-solid fa-paper-plane" style="color: #dc2626;"></i> تقديم استفسار أو شكوى
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </aside>

    </div>

    <!-- 6. التذييل الكلاسيكي (Footer) -->
    <footer class="site-footer">
        <div class="footer-inner">
            <div>
                <strong>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸</strong>
                - بوابة ومنظومة الثانوية العامة في فلسطين
            </div>

            <div class="footer-links">
                <a href="{{ route('home') }}">الرئيسية</a>
                <a href="{{ route('login') }}">دخول النظام</a>
                <a href="{{ route('students.create') }}">تسجيل طالب</a>
                <a href="{{ route('public.terms') }}">الشروط والأحكام</a>
                <a href="{{ route('public.privacy') }}">الخصوصية</a>
                <a href="{{ route('public.contact') }}">اتصل بنا</a>
            </div>
        </div>

        <div class="footer-note">
            جميع الحقوق محفوظة © {{ date('Y') }} - إشراف الأستاذ: أحمد حسين شمالي | نسأل الله دوام التوفيق والنجاح لطلبتنا الأعزاء في الثانوية العامة.
        </div>
    </footer>

</div>

</body>
</html>
