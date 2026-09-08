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

    <title>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} | المنصة التعليمية لطلبة الثانوية العامة في فلسطين</title>

    <!-- Google Fonts: Alexandria & Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-page: #f8fafc;
            --bg-surface: #ffffff;
            --border-color: #e2e8f0;
            --border-hover: #cbd5e1;
            --primary: #1e3a8a;
            --primary-light: #eff6ff;
            --primary-accent: #2563eb;
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --success: #059669;
            --success-light: #ecfdf5;
            --warning: #d97706;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --shadow-subtle: 0 1px 3px rgba(15, 23, 42, 0.05);
            --shadow-card: 0 4px 14px rgba(15, 23, 42, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Alexandria', 'Tajawal', sans-serif;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-body);
            line-height: 1.65;
            overflow-x: hidden;
        }

        /* Top Announcement Bar */
        .top-bar-alert {
            background: #0f172a;
            color: #f8fafc;
            padding: 9px 20px;
            text-align: center;
            font-size: 0.84rem;
            font-weight: 500;
        }

        .top-bar-alert a {
            color: #93c5fd;
            text-decoration: underline;
            margin-right: 8px;
            font-weight: 600;
        }

        /* Sticky Navigation Bar */
        nav.navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .nav-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-heading);
            font-size: 1.2rem;
            font-weight: 800;
        }

        .nav-logo-box {
            width: 42px;
            height: 42px;
            background: var(--primary);
            color: #ffffff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            overflow: hidden;
        }

        .nav-logo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 24px;
            list-style: none;
        }

        @media (max-width: 900px) {
            .nav-menu {
                display: none;
            }
        }

        .nav-menu a {
            text-decoration: none;
            color: var(--text-body);
            font-size: 0.9rem;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .nav-menu a:hover {
            color: var(--primary-accent);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-text {
            text-decoration: none;
            color: var(--text-body);
            font-size: 0.88rem;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            transition: background 0.2s;
        }

        .btn-text:hover {
            background: #f1f5f9;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            text-decoration: none;
            padding: 9px 20px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: #1e40af;
        }

        /* Hero Section */
        .hero-section {
            max-width: 1240px;
            margin: 0 auto;
            padding: 60px 24px 50px;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 48px;
            align-items: center;
        }

        @media (max-width: 960px) {
            .hero-section {
                grid-template-columns: 1fr;
                padding-top: 40px;
            }
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-light);
            color: var(--primary);
            border: 1px solid #bfdbfe;
            padding: 5px 14px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-heading);
            line-height: 1.35;
            margin-bottom: 18px;
            letter-spacing: -0.5px;
        }

        .hero-title span {
            color: var(--primary-accent);
        }

        .hero-desc {
            font-size: 1.05rem;
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: 30px;
            max-width: 580px;
        }

        .hero-cta {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 36px;
        }

        .btn-cta-main {
            background: var(--primary);
            color: #ffffff;
            text-decoration: none;
            padding: 13px 28px;
            border-radius: var(--radius-md);
            font-size: 0.98rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }

        .btn-cta-main:hover {
            background: #1e40af;
            transform: translateY(-1px);
        }

        .btn-cta-outline {
            background: #ffffff;
            color: var(--text-heading);
            border: 1px solid var(--border-color);
            text-decoration: none;
            padding: 13px 24px;
            border-radius: var(--radius-md);
            font-size: 0.98rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }

        .btn-cta-outline:hover {
            background: #f8fafc;
            border-color: var(--border-hover);
        }

        .trust-pills {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            padding-top: 10px;
            border-top: 1px solid var(--border-color);
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .trust-item i {
            color: var(--success);
        }

        /* Hero Preview Card */
        .hero-card-side {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 28px;
            box-shadow: var(--shadow-card);
        }

        .card-inner-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-inner-header h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-verified {
            background: var(--success-light);
            color: var(--success);
            font-size: 0.76rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
        }

        /* Grading System Quick Overview */
        .grading-summary-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .grading-item {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .grading-item-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .grading-item-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 0.95rem;
        }

        .grading-item-text h4 {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-heading);
        }

        .grading-item-text p {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .grade-pill {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--primary);
            background: var(--primary-light);
            padding: 4px 10px;
            border-radius: 6px;
        }

        .card-action-bar {
            margin-top: 16px;
        }

        .card-action-bar a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--primary-light);
            color: var(--primary);
            text-decoration: none;
            padding: 11px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 700;
            transition: background 0.2s;
        }

        .card-action-bar a:hover {
            background: #dbeafe;
        }

        /* Section Wrappers */
        section.content-section {
            max-width: 1240px;
            margin: 0 auto;
            padding: 48px 24px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .section-header .section-tag {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: block;
        }

        .section-header h2 {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 10px;
        }

        .section-header p {
            font-size: 0.98rem;
            color: var(--text-muted);
            max-width: 620px;
            margin: 0 auto;
        }

        /* Pillars 4-Card Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 22px;
        }

        .feature-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 24px;
            transition: border-color 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .feature-card:hover {
            border-color: var(--border-hover);
            box-shadow: var(--shadow-card);
        }

        .feature-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 18px;
        }

        .feature-card h3 {
            font-size: 1.08rem;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 8px;
        }

        .feature-card p {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 18px;
        }

        .feature-card a {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .feature-card a:hover {
            text-decoration: underline;
        }

        /* Curriculum Stages & Subjects Explorer */
        .curriculum-box {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 32px;
            box-shadow: var(--shadow-subtle);
        }

        .branch-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 24px;
        }

        .branch-card {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 22px;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .branch-card:hover {
            border-color: var(--primary-accent);
            box-shadow: var(--shadow-card);
        }

        .branch-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .branch-icon {
            font-size: 1.8rem;
        }

        .branch-badge {
            font-size: 0.75rem;
            font-weight: 700;
            background: #f1f5f9;
            color: var(--text-heading);
            padding: 4px 10px;
            border-radius: 6px;
        }

        .branch-card h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 6px;
        }

        .branch-card p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .subject-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 18px;
        }

        .subject-tag {
            font-size: 0.78rem;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            padding: 3px 8px;
            border-radius: 4px;
            color: var(--text-body);
        }

        .branch-link {
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Palestinian Payment Section */
        .payment-box {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 32px;
            margin-top: 30px;
        }

        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
            flex-wrap: wrap;
            gap: 12px;
        }

        .payment-header h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-recipient-badge {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            color: var(--text-body);
        }

        .payment-recipient-badge strong {
            color: var(--text-heading);
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }

        .payment-item-card {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 18px;
            background: #f8fafc;
        }

        .payment-item-top {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .payment-item-top i {
            font-size: 1.2rem;
            color: var(--primary);
        }

        .payment-item-top h4 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-heading);
        }

        .payment-number {
            font-family: monospace;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-heading);
            direction: ltr;
            text-align: right;
            margin-bottom: 6px;
        }

        .payment-owner {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Footer */
        footer.footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 50px 24px 30px;
            margin-top: 60px;
            border-top: 1px solid #1e293b;
        }

        .footer-inner {
            max-width: 1240px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        @media (max-width: 900px) {
            .footer-inner {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .footer-inner {
                grid-template-columns: 1fr;
            }
        }

        .footer-brand h3 {
            color: #ffffff;
            font-size: 1.2rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .footer-brand p {
            font-size: 0.88rem;
            line-height: 1.7;
            color: #94a3b8;
            max-width: 320px;
        }

        .footer-col h4 {
            color: #ffffff;
            font-size: 0.92rem;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-col a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }

        .footer-col a:hover {
            color: #ffffff;
        }

        .footer-bottom {
            max-width: 1240px;
            margin: 0 auto;
            padding-top: 24px;
            border-top: 1px solid #1e293b;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            flex-wrap: wrap;
            gap: 12px;
        }
    </style>
</head>
<body>

    <!-- Top Alert -->
    <div class="top-bar-alert">
        <span>🇵🇸 مرحباً بكم في منصة التوجيهي الوطنية. تم اعتماد سلم العلامات الرسمي 2026 وحساب المعدل الوزاري.</span>
        <a href="{{ route('tawjihi.calculator') }}">احسب معدلك الآن ←</a>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="/" class="nav-brand">
                <div class="nav-logo-box">
                    @if(\App\Models\Setting::get('site_logo'))
                        <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="شعار المنصة">
                    @else
                        <i class="fas fa-graduation-cap"></i>
                    @endif
                </div>
                <span>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</span>
            </a>

            <ul class="nav-menu">
                <li><a href="{{ Route::has('stages.index') ? route('stages.index') : url('/stages') }}">فروع التوجيهي</a></li>
                <li><a href="{{ Route::has('tawjihi.calculator') ? route('tawjihi.calculator') : url('/tawjihi-calculator') }}">حاسبة معدل التوجيهي</a></li>
                <li><a href="{{ Route::has('courses.catalog') ? route('courses.catalog') : url('/courses/catalog') }}">المواد والمقررات</a></li>
                <li><a href="{{ Route::has('contact') ? route('contact') : (Route::has('public.contact') ? route('public.contact') : url('/contact')) }}">تواصل معنا</a></li>
            </ul>

            <div class="nav-actions">
                @auth
                    <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/dashboard') }}" class="btn-primary">
                        <i class="fas fa-tachometer-alt"></i> لوحة التحكم
                    </a>
                @else
                    <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="btn-text">تسجيل الدخول</a>
                    <a href="{{ Route::has('student.create') ? route('student.create') : (Route::has('students.create') ? route('students.create') : url('/register')) }}" class="btn-primary">
                        <i class="fas fa-user-plus"></i> انضم كطالب
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-shield-alt"></i>
                <span>المنهاج الفلسطيني الرسمي المعتمد 2026</span>
            </div>
            <h1 class="hero-title">
                بوابتك المتكاملة للتفوق في <span>الثانوية العامة</span>
            </h1>
            <p class="hero-desc">
                منصة تعليمية فلسطينية متخصصة لطلبة الثانوية العامة توفر شروحات دراسية شاملة ومقررات مصورة، ومتابعة أكاديمية مستمرة، وحاسبة معدل رسمية دقيقة وفق سلم الدرجات الوزاري المعتمد.
            </p>

            <div class="hero-cta">
                <a href="{{ Route::has('student.create') ? route('student.create') : (Route::has('students.create') ? route('students.create') : url('/register')) }}" class="btn-cta-main">
                    <i class="fas fa-rocket"></i> انضم إلى منصتنا الآن
                </a>
                <a href="{{ Route::has('tawjihi.calculator') ? route('tawjihi.calculator') : url('/tawjihi-calculator') }}" class="btn-cta-outline">
                    <i class="fas fa-calculator"></i> احسب معدلك (من 700)
                </a>
            </div>

            <div class="trust-pills">
                <div class="trust-item"><i class="fas fa-check-circle"></i> كادر تعليمي معتمد</div>
                <div class="trust-item"><i class="fas fa-check-circle"></i> شروحات المنهاج الفلسطيني المعتمد</div>
                <div class="trust-item"><i class="fas fa-check-circle"></i> بوابات دفع فلسطينية آمنة</div>
            </div>
        </div>

        <!-- Side Card: Palestinian Grading Standards -->
        <div class="hero-card-side">
            <div class="card-inner-header">
                <h3><i class="fas fa-award" style="color: var(--primary);"></i> سلم درجات التوجيهي المعتمد</h3>
                <span class="badge-verified">المجموع 700 علامة</span>
            </div>

            <div class="grading-summary-list">
                <!-- الفرع العلمي -->
                <div class="grading-item">
                    <div class="grading-item-info">
                        <div class="grading-item-icon"><i class="fas fa-square-root-variable"></i></div>
                        <div class="grading-item-text">
                            <h4>الرياضيات - الفرع العلمي</h4>
                            <p>ورقتان وزاريتان (النجاح من 100)</p>
                        </div>
                    </div>
                    <span class="grade-pill">من 200</span>
                </div>

                <!-- الفرع الأدبي: عربي -->
                <div class="grading-item">
                    <div class="grading-item-info">
                        <div class="grading-item-icon"><i class="fas fa-feather-pointed"></i></div>
                        <div class="grading-item-text">
                            <h4>اللغة العربية - الفرع الأدبي</h4>
                            <p>ورقتان وزاريتان (النجاح من 75)</p>
                        </div>
                    </div>
                    <span class="grade-pill">من 150</span>
                </div>

                <!-- الفرع الأدبي: إنجليزي -->
                <div class="grading-item">
                    <div class="grading-item-info">
                        <div class="grading-item-icon"><i class="fas fa-language"></i></div>
                        <div class="grading-item-text">
                            <h4>اللغة الإنجليزية - الفرع الأدبي</h4>
                            <p>ورقتان وزاريتان (النجاح من 75)</p>
                        </div>
                    </div>
                    <span class="grade-pill">من 150</span>
                </div>

                <!-- باقي المباحث -->
                <div class="grading-item">
                    <div class="grading-item-info">
                        <div class="grading-item-icon"><i class="fas fa-book-open"></i></div>
                        <div class="grading-item-text">
                            <h4>باقي المباحث والاختياري</h4>
                            <p>الفيزياء، التاريخ، الجغرافيا، الإسلامية، إلخ</p>
                        </div>
                    </div>
                    <span class="grade-pill">من 100</span>
                </div>
            </div>

            <div class="card-action-bar">
                <a href="{{ route('tawjihi.calculator') }}">
                    <i class="fas fa-arrow-left"></i> تجربة حاسبة المعدل ودليل التنسيق الجامعي
                </a>
            </div>
        </div>
    </header>

    <!-- Key Services & Portals -->
    <section class="content-section">
        <div class="section-header">
            <span class="section-tag">أركان المنصة</span>
            <h2>كل ما يلزم طالب التوجيهي في مكان واحد</h2>
            <p>أدوات تعليمية ورقمية مصممة خصيصاً لمساعدة طلاب فلسطين في تنظيم دراستهم وتحقيق أعلى الدرجات.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div>
                    <div class="feature-icon-wrapper"><i class="fas fa-calculator"></i></div>
                    <h3>حاسبة معدل التوجيهي ودليل التنسيق</h3>
                    <p>احتساب دقيق وفق أعلى مادة اختيارية مع عرض مفاتيح القبول لجامعات بيرزيت، النجاح، القدس، وخضوري.</p>
                </div>
                <a href="{{ Route::has('tawjihi.calculator') ? route('tawjihi.calculator') : url('/tawjihi-calculator') }}">جرب الحاسبة الآن ←</a>
            </div>

            <div class="feature-card">
                <div>
                    <div class="feature-icon-wrapper"><i class="fas fa-book-open-reader"></i></div>
                    <h3>شروحات ومقررات التوجيهي</h3>
                    <p>شروحات مرئية وافية للمنهاج الفلسطيني لطلبة العلمي والأدبي والريادة مع ملخصات PDF معتمدة.</p>
                </div>
                <a href="{{ Route::has('stages.index') ? route('stages.index') : url('/stages') }}">استعراض فروع التوجيهي ←</a>
            </div>

            <div class="feature-card">
                <div>
                    <div class="feature-icon-wrapper"><i class="fas fa-shield-halved"></i></div>
                    <h3>بوابات الدفع الفلسطينية</h3>
                    <p>تفعيل مباشر وسريع عبر محفظة جوال باي (Jawwal Pay)، بال باي (PalPay)، وبنك فلسطين.</p>
                </div>
                <a href="{{ Route::has('courses.catalog') ? route('courses.catalog') : url('/courses/catalog') }}">كتالوج المواد والاشتراك ←</a>
            </div>

            <div class="feature-card">
                <div>
                    <div class="feature-icon-wrapper"><i class="fas fa-certificate"></i></div>
                    <h3>شهادات إنجاز معتمدة</h3>
                    <p>شهادات تفوق وإتمام للمقررات التعليمية قابلة للتحقق الفوري عبر رمز الاستجابة السريع (QR Code).</p>
                </div>
                <a href="{{ Route::has('login') ? route('login') : url('/login') }}">بوابة الطلاب ←</a>
            </div>
        </div>
    </section>

    <!-- Curriculum Explorer -->
    <section class="content-section">
        <div class="curriculum-box">
            <div class="section-header" style="margin-bottom: 24px;">
                <span class="section-tag">فروع الثانوية العامة</span>
                <h2>منهاج التوجيهي الفلسطيني المعتمد 2026</h2>
                <p>تصفح المقررات والمباحث الوزارية الخاصة بفرعك للوصول إلى الشروحات والملخصات.</p>
            </div>

            <div class="branch-cards-grid">
                <!-- الفرع العلمي -->
                <div class="branch-card">
                    <div class="branch-card-top">
                        <span class="branch-icon">⚛️</span>
                        <span class="branch-badge">الثانوية العامة</span>
                    </div>
                    <h3>الفرع العلمي</h3>
                    <p>الرياضيات (200 علامة)، الفيزياء (100)، الكيمياء، الأحياء، التكنولوجيا، واللغات.</p>
                    <div class="subject-tags">
                        <span class="subject-tag">رياضيات (200)</span>
                        <span class="subject-tag">فيزياء</span>
                        <span class="subject-tag">كيمياء</span>
                        <span class="subject-tag">أحياء</span>
                    </div>
                    <a href="{{ Route::has('stages.show') ? route('stages.show', 122) : url('/stages/122') }}" class="branch-link">تصفح مواد الفرع العلمي ←</a>
                </div>

                <!-- الفرع الأدبي -->
                <div class="branch-card">
                    <div class="branch-card-top">
                        <span class="branch-icon">📜</span>
                        <span class="branch-badge">الثانوية العامة</span>
                    </div>
                    <h3>الفرع الأدبي</h3>
                    <p>اللغة العربية (150 علامة)، اللغة الإنجليزية (150 علامة)، التاريخ، الجغرافيا، والمواد الاختيارية.</p>
                    <div class="subject-tags">
                        <span class="subject-tag">عربي (150)</span>
                        <span class="subject-tag">إنجليزي (150)</span>
                        <span class="subject-tag">تاريخ</span>
                        <span class="subject-tag">جغرافيا</span>
                    </div>
                    <a href="{{ Route::has('stages.show') ? route('stages.show', 121) : url('/stages/121') }}" class="branch-link">تصفح مواد الفرع الأدبي ←</a>
                </div>

                <!-- فرع الريادة والأعمال -->
                <div class="branch-card">
                    <div class="branch-card-top">
                        <span class="branch-icon">💼</span>
                        <span class="branch-badge">الثانوية العامة</span>
                    </div>
                    <h3>فرع الريادة والأعمال</h3>
                    <p>المشاريع الريادية، المحاسبة، الإدارة والاقتصاد، اللغات، والعلوم الإدارية التطبيقية.</p>
                    <div class="subject-tags">
                        <span class="subject-tag">مشاريع ريادية</span>
                        <span class="subject-tag">محاسبة</span>
                        <span class="subject-tag">إدارة واقتصاد</span>
                        <span class="subject-tag">رياضيات</span>
                    </div>
                    <a href="{{ Route::has('stages.show') ? route('stages.show', 123) : url('/stages/123') }}" class="branch-link">تصفح مواد فرع الريادة ←</a>
                </div>
            </div>

            <!-- Palestinian Pricing & Subscription Plans (Modern Educational Platform Standard) -->
            <div class="payment-box" style="margin-top: 36px;">
                <div class="payment-header">
                    <div>
                        <h3><i class="fas fa-gem" style="color: var(--primary);"></i> خطط وباقات الاشتراك لطلبة التوجيهي 🇵🇸</h3>
                        <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">اختر الباقة المناسبة وسدد بسهولة عبر وسائل الدفع الفلسطينية المعتمدة مع التفعيل الفوري.</p>
                    </div>
                    <div class="payment-recipient-badge">
                        صاحب الحساب المعتمد: <strong>{{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}</strong>
                    </div>
                </div>

                <!-- Plans Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 28px;">
                    <!-- Single Subject Plan -->
                    <div style="background: #ffffff; border: 1.5px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <span style="display: inline-block; background: #eff6ff; color: #1e40af; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 700; margin-bottom: 12px;">باقة مرنة</span>
                            <h4 style="font-size: 1.2rem; font-weight: 800; color: var(--text-heading); margin-bottom: 6px;">اشتراك المادة الواحدة</h4>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 16px;">اختر أي مبحث ترغب بتقوية نفسك فيه واحصل على شروحاته الكاملة وملخصاته واختباراته.</p>
                            <div style="font-size: 1.7rem; font-weight: 900; color: var(--primary); margin-bottom: 16px;">
                                تبدأ من 80 ₪ <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">/ للمادة</span>
                            </div>
                            <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 0.84rem; color: var(--text-body); margin-bottom: 20px;">
                                <li><i class="fas fa-check-circle" style="color: #10b981; margin-left: 6px;"></i> شروحات فيديو تفاعلية شاملة المنهاج</li>
                                <li><i class="fas fa-check-circle" style="color: #10b981; margin-left: 6px;"></i> ملخصات PDF وأوراق عمل وزارية</li>
                                <li><i class="fas fa-check-circle" style="color: #10b981; margin-left: 6px;"></i> اختبارات تدريبية مع تصحيح مباشر</li>
                            </ul>
                        </div>
                        <a href="{{ route('courses.catalog') }}" class="btn-primary" style="text-align: center; justify-content: center;">
                            اختيار المواد والاشتراك ←
                        </a>
                    </div>

                    <!-- Full Branch Bundle Plan (Featured) -->
                    <div style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border: 2px solid #10b981; border-radius: var(--radius-lg); padding: 24px; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                        <span style="position: absolute; top: -12px; left: 24px; background: #10b981; color: white; padding: 4px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 800; box-shadow: 0 2px 6px rgba(16,185,129,0.3);">
                            الأكثر طلباً وتوفيراً 🔥
                        </span>
                        <div>
                            <span style="display: inline-block; background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 700; margin-bottom: 12px;">باقة المنهاج الكامل</span>
                            <h4 style="font-size: 1.2rem; font-weight: 800; color: var(--text-heading); margin-bottom: 6px;">باقة الفرع الكامل للتوجيهي</h4>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 16px;">جميع مواد فرعك (علمي / أدبي / ريادة) مع خصم باقة خاص 25% ومتابعة أكاديمية مستمرة حتى ليلة الامتحان الوزاري.</p>
                            <div style="font-size: 1.7rem; font-weight: 900; color: #047857; margin-bottom: 16px;">
                                خصم إضافي 25% <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">عند اختيار 3 مواد أو أكثر</span>
                            </div>
                            <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 0.84rem; color: var(--text-body); margin-bottom: 20px;">
                                <li><i class="fas fa-check-circle" style="color: #10b981; margin-left: 6px;"></i> وصول كامل لكافة مباحث الفرع الـ 8</li>
                                <li><i class="fas fa-check-circle" style="color: #10b981; margin-left: 6px;"></i> بنك أسئلة شامل ومراجعات ليلة الامتحان</li>
                                <li><i class="fas fa-check-circle" style="color: #10b981; margin-left: 6px;"></i> تواصل مباشر واستفسارات مع مدرسي التوجيهي</li>
                            </ul>
                        </div>
                        <a href="{{ route('courses.catalog') }}" class="btn-primary" style="background: #059669; text-align: center; justify-content: center;">
                            اشترك في الباقة الكاملة ووفر الآن ←
                        </a>
                    </div>
                </div>

                <!-- Palestinian Payment Methods Available -->
                <div style="border-top: 1px solid var(--border-color); padding-top: 20px;">
                    <div style="font-size: 0.92rem; font-weight: 800; color: var(--text-heading); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-shield-check" style="color: #10b981;"></i> بوابات الدفع الوطنية المتاحة للسداد الفوري:
                    </div>
                    <div class="payment-grid">
                        <div class="payment-item-card">
                            <div class="payment-item-top">
                                <i class="fas fa-mobile-screen" style="color: #0284c7;"></i>
                                <h4>محفظة جوال باي (Jawwal Pay)</h4>
                            </div>
                            <div class="payment-number">{{ \App\Models\Setting::get('jawwal_pay_account', '0567897212') }}</div>
                            <div class="payment-owner">المستفيد: {{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}</div>
                        </div>

                        <div class="payment-item-card">
                            <div class="payment-item-top">
                                <i class="fas fa-credit-card" style="color: #047857;"></i>
                                <h4>محفظة بال باي (PalPay)</h4>
                            </div>
                            <div class="payment-number">{{ \App\Models\Setting::get('palpay_account', '0567897212') }}</div>
                            <div class="payment-owner">المستفيد: {{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}</div>
                        </div>

                        <div class="payment-item-card">
                            <div class="payment-item-top">
                                <i class="fas fa-building-columns" style="color: #b45309;"></i>
                                <h4>بنك فلسطين (Bank of Palestine)</h4>
                            </div>
                            <div class="payment-number">{{ \App\Models\Setting::get('bop_account', '0567897212') }}</div>
                            <div class="payment-owner">المستفيد: {{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <h3>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸</h3>
                <p>المنصة التعليمية الوطنية الرائدة لطلبة الثانوية العامة في فلسطين، لتقديم شروحات المنهاج وبنوك الأسئلة والامتحانات المعتمدة.</p>
            </div>

            <div class="footer-col">
                <h4>روابط سريعة</h4>
                <ul>
                    <li><a href="{{ Route::has('stages.index') ? route('stages.index') : url('/stages') }}">فروع التوجيهي</a></li>
                    <li><a href="{{ Route::has('tawjihi.calculator') ? route('tawjihi.calculator') : url('/tawjihi-calculator') }}">حاسبة معدل التوجيهي</a></li>
                    <li><a href="{{ Route::has('courses.catalog') ? route('courses.catalog') : url('/courses/catalog') }}">المواد والمقررات</a></li>
                    <li><a href="{{ Route::has('contact') ? route('contact') : (Route::has('public.contact') ? route('public.contact') : url('/contact')) }}">تواصل معنا</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>البوابات والخدمات</h4>
                <ul>
                    <li><a href="{{ Route::has('login') ? route('login') : url('/login') }}">دخول الطلاب والمعلمين</a></li>
                    <li><a href="{{ Route::has('student.create') ? route('student.create') : (Route::has('students.create') ? route('students.create') : url('/register')) }}">تسجيل طالب جديد</a></li>
                    <li><a href="{{ Route::has('courses.catalog') ? route('courses.catalog') : url('/courses/catalog') }}">كتالوج المواد</a></li>
                    <li><a href="{{ Route::has('privacy') ? route('privacy') : (Route::has('public.privacy') ? route('public.privacy') : url('/privacy')) }}">سياسة الخصوصية</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>معلومات التواصل</h4>
                <ul>
                    <li><i class="fab fa-whatsapp"></i> واتساب: {{ \App\Models\Setting::get('contact_whatsapp', '0567897212') }}</li>
                    <li><i class="fas fa-envelope"></i> البريد: {{ \App\Models\Setting::get('contact_email', 'ahmed.shamali@tawjihi.ps') }}</li>
                    <li><i class="fas fa-user"></i> الإدارة: {{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}</li>
                    <li><i class="fas fa-map-marker-alt"></i> فلسطين</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <span>جميع الحقوق محفوظة © {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}.</span>
            <span>بإشراف: {{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }} 🇵🇸</span>
        </div>
    </footer>

</body>
</html>
