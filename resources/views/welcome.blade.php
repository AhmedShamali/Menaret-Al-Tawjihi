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

    <title>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} | المنظومة الأكاديمية لطلبة الثانوية العامة في فلسطين 🇵🇸</title>
    <meta name="description" content="منصة منارة التوجيهي التعليمية: شروحات مبسطة لنخبة معلمي فلسطين، بنك اختبارات وزارية شاملة، ومتابعة أكاديمية مستمرة لدورة التوجيهي.">

    <!-- الخطوط العربية الراقية -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&family=Readex+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #1e3a8a;
            --primary-dark: #172554;
            --primary-light: #2563eb;
            --accent: #059669;
            --accent-light: #10b981;
            --gold: #d97706;
            --gold-light: #f59e0b;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #475569;
            --text-light: #64748b;
            --border-color: #e2e8f0;
            --border-hover: #cbd5e1;
            --radius-md: 14px;
            --radius-lg: 22px;
            --shadow-sm: 0 2px 8px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 10px 30px -8px rgba(15, 23, 42, 0.08);
            --shadow-lg: 0 20px 40px -15px rgba(15, 23, 42, 0.12);
            --transition: all 0.25s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Alexandria', 'Readex Pro', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.8;
            overflow-x: hidden;
        }

        /* حاوية العرض المتناسقة */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* 1. شريط التنقل العلوي (Navbar) */
        .navbar-wrap {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .navbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 76px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            display: grid;
            place-items: center;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .brand-text h1 {
            font-size: 1.18rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin: 0;
            line-height: 1.2;
        }

        .brand-text span {
            font-size: 0.76rem;
            font-weight: 600;
            color: var(--accent);
            display: block;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .nav-links a:hover {
            color: var(--primary-light);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-nav-login {
            background: #f1f5f9;
            color: var(--text-main);
            border: 1px solid var(--border-color);
            padding: 9px 18px;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 700;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-nav-login:hover {
            background: #e2e8f0;
            color: var(--primary-dark);
        }

        .btn-nav-register {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 9px 20px;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 700;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.22);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-nav-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.32);
        }

        /* 2. قسم البطل (Hero Section) */
        .hero-section {
            padding: 70px 0 60px;
            background: radial-gradient(circle at top right, rgba(37, 99, 235, 0.05) 0%, rgba(248, 250, 252, 0.9) 60%);
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #eff6ff;
            color: var(--primary-light);
            border: 1px solid #bfdbfe;
            border-radius: 50px;
            padding: 6px 18px;
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 22px;
        }

        .hero-title {
            font-size: 2.6rem;
            font-weight: 900;
            color: var(--primary-dark);
            line-height: 1.35;
            max-width: 860px;
            margin: 0 auto 20px;
            letter-spacing: -0.5px;
        }

        .hero-title .highlight {
            color: var(--primary-light);
            position: relative;
            display: inline-block;
        }

        .hero-description {
            font-size: 1.12rem;
            color: var(--text-muted);
            max-width: 720px;
            margin: 0 auto 35px;
            line-height: 1.9;
        }

        .hero-cta-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 50px;
        }

        .btn-cta-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 1.02rem;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.28);
            transition: var(--transition);
        }

        .btn-cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(37, 99, 235, 0.38);
        }

        .btn-cta-secondary {
            background: var(--bg-card);
            color: var(--text-main);
            border: 1.5px solid var(--border-color);
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 1.02rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .btn-cta-secondary:hover {
            background: #f1f5f9;
            border-color: var(--border-hover);
        }

        /* إحصائيات سريعة في الهيرو */
        .hero-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            max-width: 980px;
            margin: 0 auto;
        }

        .hero-stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .hero-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: #bfdbfe;
        }

        .stat-icon {
            font-size: 1.6rem;
            margin-bottom: 8px;
            display: inline-block;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--primary-dark);
            display: block;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-light);
            margin-top: 4px;
            display: block;
        }

        /* 3. رسالة ورؤية المنظومة (Mission Section) */
        .section-block {
            padding: 85px 0;
        }

        .section-header {
            text-align: center;
            max-width: 680px;
            margin: 0 auto 50px;
        }

        .section-tag {
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--accent);
            background: #ecfdf5;
            padding: 4px 14px;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 12px;
            border: 1px solid #a7f3d0;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 14px;
            line-height: 1.35;
        }

        .section-desc {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.8;
        }

        .mission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(310px, 1fr));
            gap: 24px;
        }

        .mission-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 32px 28px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .mission-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: #cbd5e1;
        }

        .mission-icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: #eff6ff;
            color: var(--primary-light);
            display: grid;
            place-items: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .mission-icon-wrap.emerald { background: #ecfdf5; color: var(--accent); }
        .mission-icon-wrap.amber { background: #fffbeb; color: var(--gold); }

        .mission-card h3 {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 12px;
        }

        .mission-card p {
            font-size: 0.92rem;
            color: var(--text-muted);
            line-height: 1.8;
        }

        /* 4. الفروع والمراحل الدراسية (Academic Branches) */
        .branches-section {
            background: #f1f5f9;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }

        .branches-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .branch-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 26px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .branch-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary-light);
            box-shadow: var(--shadow-md);
        }

        .branch-pill {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 6px;
            margin-bottom: 14px;
        }

        .branch-pill.sci { background: #eff6ff; color: #1d4ed8; }
        .branch-pill.lit { background: #fdf2f8; color: #be185d; }
        .branch-pill.bus { background: #ecfdf5; color: #047857; }
        .branch-pill.ind { background: #fefce8; color: #a16207; }

        .branch-card h3 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 10px;
        }

        .branch-card p {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 16px;
        }

        .branch-subjects-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .branch-sub-tag {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 0.76rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
        }

        /* 5. مميزات المنظومة (Core Features) */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 18px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 24px;
            transition: var(--transition);
        }

        .feature-item:hover {
            border-color: #cbd5e1;
            box-shadow: var(--shadow-sm);
        }

        .feature-item-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #eff6ff;
            color: var(--primary-light);
            display: grid;
            place-items: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .feature-item h4 {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 6px;
        }

        .feature-item p {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* 6. بطاقة الدعوة للتسجيل (CTA Box) */
        .cta-banner-wrap {
            padding: 40px 0 80px;
        }

        .cta-banner-card {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            border-radius: 28px;
            padding: 55px 40px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(30, 58, 138, 0.25);
        }

        .cta-banner-card h2 {
            font-size: 2.2rem;
            font-weight: 900;
            margin-bottom: 14px;
            line-height: 1.35;
        }

        .cta-banner-card p {
            font-size: 1.08rem;
            color: #cbd5e1;
            max-width: 680px;
            margin: 0 auto 32px;
            line-height: 1.8;
        }

        .cta-banner-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-banner-main {
            background: #ffffff;
            color: var(--primary-dark);
            padding: 13px 30px;
            border-radius: 12px;
            font-size: 0.98rem;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
        }

        .btn-banner-main:hover {
            transform: translateY(-2px);
            background: #f8fafc;
        }

        .btn-banner-whatsapp {
            background: #25d366;
            color: white;
            padding: 13px 26px;
            border-radius: 12px;
            font-size: 0.98rem;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .btn-banner-whatsapp:hover {
            background: #1eb956;
            transform: translateY(-2px);
        }

        /* 7. تذييل الصفحة (Footer) */
        .footer-wrap {
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 50px 0 25px;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        @media (max-width: 768px) {
            .footer-top {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }

        .footer-brand h3 {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 10px;
        }

        .footer-brand p {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.8;
            max-width: 400px;
        }

        .footer-col h4 {
            font-size: 0.98rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 14px;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 8px;
        }

        .footer-col ul li a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.86rem;
            transition: var(--transition);
        }

        .footer-col ul li a:hover {
            color: var(--primary-light);
        }

        .footer-bottom {
            border-top: 1px solid var(--border-color);
            padding-top: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.82rem;
            color: var(--text-light);
            flex-wrap: wrap;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 1.95rem;
            }
            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- 1. شريط التنقل العلوي -->
    <header class="navbar-wrap">
        <div class="container navbar-inner">
            <a href="{{ route('home') }}" class="brand-logo">
                <div class="brand-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="brand-text">
                    <h1>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</h1>
                    <span>فلسطين 🇵🇸 | الثانوية العامة</span>
                </div>
            </a>

            <ul class="nav-links">
                <li><a href="#hero">الرئيسية</a></li>
                <li><a href="#mission">رسالتنا</a></li>
                <li><a href="#branches">الفروع الدراسية</a></li>
                <li><a href="#features">مميزات المنصة</a></li>
                <li><a href="#contact">تواصل معنا</a></li>
            </ul>

            <div class="nav-actions">
                @if(Auth::guard('student')->check() || Auth::check())
                    <a href="{{ route('dashboard') }}" class="btn-nav-register">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>لوحة التحكم</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-nav-login">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>تسجيل الدخول</span>
                    </a>
                    <a href="{{ route('students.create') }}" class="btn-nav-register">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>تسجيل طالب</span>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- 2. قسم البطل (Hero Section) -->
    <section class="hero-section" id="hero">
        <div class="container">
            <div class="hero-badge">
                <i class="fa-solid fa-certificate"></i>
                <span>المنظومة الأكاديمية الأولى المعتمدة لطلبة الثانوية العامة 2026</span>
            </div>

            <h1 class="hero-title">
                طريقك الواثق نحو <span class="highlight">التفوق في التوجيهي</span> برعاية نخبة المعلمين
            </h1>

            <p class="hero-description">
                منصة تعليمية متكاملة تواكب المنهاج الوزاري الفلسطيني خطوة بخطوة، تمنحك شروحات منهجية مبسطة، بنك اختبارات وزارية محلولة، ومتابعة أكاديمية مستمرة بإشراف <strong>أ. أحمد حسين شمالي</strong>.
            </p>

            <div class="hero-cta-group">
                @if(Auth::guard('student')->check() || Auth::check())
                    <a href="{{ route('dashboard') }}" class="btn-cta-primary">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>الانتقال للوحة التحكم التعليمية</span>
                    </a>
                @else
                    <a href="{{ route('students.create') }}" class="btn-cta-primary">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>ابدأ رحلة النجاح - تسجيل طالب جديد</span>
                    </a>
                    <a href="{{ route('login') }}" class="btn-cta-secondary">
                        <i class="fa-solid fa-key"></i>
                        <span>تسجيل الدخول للنظام</span>
                    </a>
                @endif
            </div>

            <!-- بطاقات الإحصائيات -->
            <div class="hero-stats-grid">
                <div class="hero-stat-card">
                    <span class="stat-icon">👨‍🎓</span>
                    <span class="stat-number">{{ number_format($stats['students'] ?? 1200) }}+</span>
                    <span class="stat-label">طالب وطالبة في المنظومة</span>
                </div>
                <div class="hero-stat-card">
                    <span class="stat-icon">📚</span>
                    <span class="stat-number">{{ number_format($stats['subjects'] ?? 18) }}</span>
                    <span class="stat-label">مساقاً تعليمياً وزارياً</span>
                </div>
                <div class="hero-stat-card">
                    <span class="stat-icon">🎥</span>
                    <span class="stat-number">{{ number_format($stats['lessons'] ?? 350) }}+</span>
                    <span class="stat-label">شرحاً مرئياً ودرس تفاعلي</span>
                </div>
                <div class="hero-stat-card">
                    <span class="stat-icon">📝</span>
                    <span class="stat-number">{{ number_format($stats['exams'] ?? 150) }}+</span>
                    <span class="stat-label">اختباراً ونموذجاً وزارياً محلولاً</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. رسالتنا الأكاديمية (Mission) -->
    <section class="section-block" id="mission">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">رؤيتنا ورسالتنا الأكاديمية</span>
                <h2 class="section-title">تعليم فلسطيني حديث، مبسط، وفي متناول كل طالب</h2>
                <p class="section-desc">
                    انطلقت منصة "منارة التوجيهي" لتكون السند الأكاديمي الحقيقي لكل طالب فلسطيني في القدس والضفة وقطاع غزة، لتذليل صعوبات المنهاج ومساعدته على تحصيل أعلى المعدلات.
                </p>
            </div>

            <div class="mission-grid">
                <div class="mission-card">
                    <div class="mission-icon-wrap">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <h3>نخبة المعلمين المعتمدين</h3>
                    <p>يقوم على المنصة كادر تعليمي متميز من ذوي الخبرة الطويلة في تدريس الثانوية العامة ووضع نماذج الاختبارات الوزارية وتصحيحها.</p>
                </div>

                <div class="mission-card">
                    <div class="mission-icon-wrap emerald">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h3>تركيز كامل على المنهاج الوزاري</h3>
                    <p>محتوى أكاديمي دقيق ومطابق 100% لتحديثات وزارة التربية والتعليم الفلسطينية مع التركيز على الأسئلة المتكررة وتوقعات الامتحانات.</p>
                </div>

                <div class="mission-card">
                    <div class="mission-icon-wrap amber">
                        <i class="fa-solid fa-handshake-angle"></i>
                    </div>
                    <h3>متابعة مستمرة ومباشرة</h3>
                    <p>إشراف مباشر من إدارة المنصة لحل الصعوبات، الرد على الاستفسارات الأكاديمية، وتقديم المنح والتسهيلات لطلبة فلسطين الكرام.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. الفروع الدراسية (Academic Branches) -->
    <section class="section-block branches-section" id="branches">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">فروع الثانوية العامة</span>
                <h2 class="section-title">تغطية شاملة لكافة الفروع والمسارات التعليمية</h2>
                <p class="section-desc">
                    اختر فرعك الدراسي لتجد الدروس المجدولة، التلاخيص الاحترافية، ونماذج الامتحانات الوزارية السابقة لكل مادة.
                </p>
            </div>

            <div class="branches-grid">
                <!-- العلمي -->
                <div class="branch-card">
                    <span class="branch-pill sci">علمي ⚛️</span>
                    <h3>الفرع العلمي</h3>
                    <p>تغطية عميقة ومسائل تفصيلية لقوانين الرياضيات والفيزياء والكيمياء والأحياء بأعلى معايير التفكير العلمي.</p>
                    <div class="branch-subjects-list">
                        <span class="branch-sub-tag">الرياضيات</span>
                        <span class="branch-sub-tag">الفيزياء</span>
                        <span class="branch-sub-tag">الكيمياء</span>
                        <span class="branch-sub-tag">العلوم الحياتية</span>
                    </div>
                </div>

                <!-- الأدبي -->
                <div class="branch-card">
                    <span class="branch-pill lit">أدبي 📜</span>
                    <h3>الفرع الأدبي</h3>
                    <p>شروحات وافية للغة العربية والإنجليزية، تبسيط التاريخ والجغرافيا، وتثبيت مفاهيم الدراسات الإسلامية.</p>
                    <div class="branch-subjects-list">
                        <span class="branch-sub-tag">اللغة العربية</span>
                        <span class="branch-sub-tag">التاريخ</span>
                        <span class="branch-sub-tag">الجغرافيا</span>
                        <span class="branch-sub-tag">اللغة الإنجليزية</span>
                    </div>
                </div>

                <!-- الريادة والأعمال -->
                <div class="branch-card">
                    <span class="branch-pill bus">ريادة وأعمال 💼</span>
                    <h3>فرع الريادة والأعمال</h3>
                    <p>فهم متكامل لقوانين المحاسبة، المشاريع الصغيرة، والإدارة والاقتصاد لضمان التفوق الأكاديمي.</p>
                    <div class="branch-subjects-list">
                        <span class="branch-sub-tag">المحاسبة</span>
                        <span class="branch-sub-tag">الإدارة والاقتصاد</span>
                        <span class="branch-sub-tag">المشاريع</span>
                        <span class="branch-sub-tag">الثقافة العلمية</span>
                    </div>
                </div>

                <!-- الصناعي والتكنولوجي -->
                <div class="branch-card">
                    <span class="branch-pill ind">مهني وصناعي ⚙️</span>
                    <h3>الفرع الصناعي</h3>
                    <p>دروس تطبيقية ومسائل عملية للفيزياء التطبيقية والرياضيات الصناعية والعلوم المهنية المتخصصة.</p>
                    <div class="branch-subjects-list">
                        <span class="branch-sub-tag">الرياضيات</span>
                        <span class="branch-sub-tag">الفيزياء الصناعية</span>
                        <span class="branch-sub-tag">الرسم الهندسي</span>
                        <span class="branch-sub-tag">العلوم الصناعية</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. مميزات المنصة (Core Features) -->
    <section class="section-block" id="features">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">لماذا منارة التوجيهي؟</span>
                <h2 class="section-title">بيئة تعليمية صُممت خصيصاً لتفوقك</h2>
                <p class="section-desc">
                    أدوات دراسية ذكية وهادئة تُمكّنك من استثمار وقتك والوصول للمعلومة بأسرع وأسهل طريقة.
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-item-icon"><i class="fa-solid fa-play"></i></div>
                    <div>
                        <h4>دروس مسجلة بجودة عالية</h4>
                        <p>شروحات تفصيلية منظمة ومرتبة حسب الكتاب الوزاري، يمكنك مشاهدتها بأي وقت ومن أي جهاز.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-item-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                    <div>
                        <h4>نماذج امتحانات وزارية محلولة</h4>
                        <p>بنك أسئلة للسنوات السابقة مع نماذج الإجابة المعتمدة لمساعدتك على فهم آلية توزيع العلامات.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-item-icon"><i class="fa-solid fa-layer-group"></i></div>
                    <div>
                        <h4>ملخصات وبطاقات استذكار</h4>
                        <p>ملخصات مكثفة للقوانين والتعاريف وأهم النقاط الوزارية لمراجعتها السريعة قبل الامتحانات.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-item-icon"><i class="fa-brands fa-whatsapp"></i></div>
                    <div>
                        <h4>تواصل مباشر مع المشرف</h4>
                        <p>إمكانية الاستفسار ومتابعة تفعيل الحساب وحل أي عقبة تقنية أو أكاديمية بكل سهولة عبر WhatsApp.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-item-icon"><i class="fa-solid fa-wallet"></i></div>
                    <div>
                        <h4>طرق دفع فلسطينية ميسرة</h4>
                        <p>دعم كامل لحسابات بنك فلسطين، بال باي (PalPay)، وجوال باي (Jawwal Pay) ومنح خاصة للمستحقين.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-item-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <div>
                        <h4>حساب محمي وخاص</h4>
                        <p>نظام تسجيل دخول سلس عبر اسم مستخدم بريدي موحد (@tawjihi.ps) يحفظ خصوصية دراستك وإنجازاتك.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. بطاقة الدعوة للتسجيل (CTA Banner) -->
    <div class="cta-banner-wrap" id="contact">
        <div class="container">
            <div class="cta-banner-card">
                <h2>جاهز لبدء رحلة التميز وحصد معدل أحلامك؟ 🎓</h2>
                <p>
                    انضم الآن إلى آلاف زملائك في منصة منارة التوجيهي، واستفد من الشروحات الشاملة والامتحانات الوزارية بإشراف المشرف العام أ. أحمد حسين شمالي.
                </p>

                <div class="cta-banner-actions">
                    @if(Auth::guard('student')->check() || Auth::check())
                        <a href="{{ route('dashboard') }}" class="btn-banner-main">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span>الانتقال للوحة التحكم الخاصة بك</span>
                        </a>
                    @else
                        <a href="{{ route('students.create') }}" class="btn-banner-main">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>تسجيل حساب طالب جديد</span>
                        </a>
                    @endif

                    @php
                        $waDigits = '970567897212';
                        $waMsg = urlencode("مرحباً أستاذ أحمد شمالي، أود الاستفسار عن التسجيل والاشتراك في منصة منارة التوجيهي.");
                    @endphp
                    <a href="https://wa.me/{{ $waDigits }}?text={{ $waMsg }}" target="_blank" class="btn-banner-whatsapp">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>تواصل معنا عبر واتساب (0567897212)</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 7. تذييل الصفحة (Footer) -->
    <footer class="footer-wrap">
        <div class="container">
            <div class="footer-top">
                <div class="footer-brand">
                    <h3>🇵🇸 {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</h3>
                    <p>
                        المنصة الأكاديمية الفلسطينية الرائدة لطلبة الثانوية العامة (التوجيهي). نسعى إلى توفير بيئة تعليمية هادئة وحديثة تضمن وصول العلم والتفوق لكل بيت فلسطيني.
                    </p>
                    <p style="margin-top: 10px; font-weight: 700; color: var(--primary-light);">
                        المشرف العام: أ. أحمد حسين شمالي
                    </p>
                </div>

                <div class="footer-col">
                    <h4>روابط سريعة</h4>
                    <ul>
                        <li><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                        <li><a href="{{ route('login') }}">تسجيل الدخول</a></li>
                        <li><a href="{{ route('students.create') }}">تسجيل حساب طالب جديد</a></li>
                        <li><a href="#branches">فروع الثانوية العامة</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>التواصل والدعم</h4>
                    <ul>
                        <li><a href="https://wa.me/970567897212" target="_blank"><i class="fa-brands fa-whatsapp text-success me-1"></i> واتساب: 0567897212</a></li>
                        <li><span style="font-size: 0.86rem; color: var(--text-muted);"><i class="fa-regular fa-envelope me-1"></i> info@tawjihi.ps</span></li>
                        <li><span style="font-size: 0.86rem; color: var(--text-muted);"><i class="fa-solid fa-location-dot me-1"></i> دولة فلسطين</span></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>جميع الحقوق محفوظة © {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸</span>
                <span>متوافق تماماً مع منهاج وزارة التربية والتعليم الفلسطينية لدورة 2026.</span>
            </div>
        </div>
    </footer>

</body>
</html>
