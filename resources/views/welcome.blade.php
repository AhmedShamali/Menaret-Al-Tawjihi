<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0284c7">
    @if(\App\Models\Setting::get('site_favicon'))
        <link rel="icon" href="{{ asset(\App\Models\Setting::get('site_favicon')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @endif
    <title>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸 | المنصة الوطنية الأولى لطلبة الثانوية العامة في فلسطين</title>

    <!-- Google Fonts: Alexandria -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #0284c7;
            --primary-dark: #0369a1;
            --primary-light: #e0f2fe;
            --emerald: #059669;
            --emerald-light: #ecfdf5;
            --amber: #d97706;
            --bg-page: #f8fafc;
            --bg-card: #ffffff;
            --text-title: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow-sm: 0 4px 10px rgba(0,0,0,0.03);
            --shadow-md: 0 12px 30px rgba(0,0,0,0.06);
            --shadow-lg: 0 20px 45px rgba(2, 132, 199, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Alexandria', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-body);
            overflow-x: hidden;
            line-height: 1.7;
        }

        /* Ambient Glow Background Orbs */
        .ambient-glow {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }
        .glow-1 {
            position: absolute;
            top: -10%; right: -5%;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.1) 0%, transparent 70%);
            filter: blur(80px);
        }
        .glow-2 {
            position: absolute;
            bottom: 10%; left: -5%;
            width: 550px; height: 550px;
            background: radial-gradient(circle, rgba(5, 150, 105, 0.08) 0%, transparent 70%);
            filter: blur(80px);
        }

        /* شريط الملاحة العلوي Navbar */
        nav.main-nav {
            position: fixed;
            top: 0; width: 100%;
            z-index: 1000;
            padding: 16px 7%;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-title);
            font-weight: 900;
            font-size: 1.35rem;
        }

        .brand-icon-box {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary) 0%, #0369a1 100%);
            color: white;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.25rem;
            font-weight: 900;
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.3);
        }

        .nav-menu-links {
            display: flex;
            gap: 24px;
            align-items: center;
            list-style: none;
        }

        .nav-menu-links a {
            text-decoration: none;
            color: var(--text-body);
            font-size: 0.9rem;
            font-weight: 700;
            transition: color 0.2s;
        }

        .nav-menu-links a:hover {
            color: var(--primary);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-nav-login {
            color: var(--text-title);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 9px 18px;
            border-radius: 12px;
            transition: 0.2s;
        }

        .btn-nav-login:hover {
            background: #f1f5f9;
        }

        .btn-nav-register {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            text-decoration: none;
            font-weight: 800;
            font-size: 0.9rem;
            padding: 10px 22px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.35);
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-nav-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(2, 132, 199, 0.45);
        }

        /* قسم الهيرو الرئيسي Hero Section */
        .hero-section {
            padding: 140px 7% 80px;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 50px;
            align-items: center;
            min-height: 90vh;
        }

        .hero-badge-pal {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: var(--primary);
            font-size: 0.82rem;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 50px;
            margin-bottom: 20px;
        }

        .hero-title-main {
            font-size: 3rem;
            font-weight: 900;
            color: var(--text-title);
            line-height: 1.35;
            margin-bottom: 18px;
            letter-spacing: -1px;
        }

        .hero-title-main span {
            color: var(--primary);
            position: relative;
        }

        .hero-description-main {
            font-size: 1.12rem;
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: 30px;
            max-width: 620px;
        }

        .hero-cta-group {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .btn-cta-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 15px 32px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 1.05rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(2, 132, 199, 0.4);
            transition: all 0.25s ease;
        }

        .btn-cta-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 35px rgba(2, 132, 199, 0.5);
        }

        .btn-cta-secondary {
            background: #ffffff;
            color: var(--text-title);
            border: 1.5px solid var(--border);
            padding: 15px 28px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 1.05rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.25s ease;
        }

        .btn-cta-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #f0f9ff;
            transform: translateY(-2px);
        }

        /* كرت المعاينة التفاعلي لليمين */
        .hero-preview-box {
            position: relative;
        }

        .glass-tutor-card {
            background: #ffffff;
            border-radius: 28px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
            padding: 32px;
            position: relative;
            overflow: hidden;
        }

        .tutor-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .top-flag-badge {
            background: #ecfdf5;
            color: var(--emerald);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .interactive-subject-chips {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 24px;
        }

        .subject-chip-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px 18px;
            transition: 0.2s;
        }

        .subject-chip-item:hover {
            border-color: var(--primary);
            transform: translateX(-4px);
        }

        .chip-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.1rem;
            color: white;
        }

        /* شريط الإحصائيات الحية */
        .stats-strip {
            background: #ffffff;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 40px 7%;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            text-align: center;
        }

        .stat-number {
            font-size: 2.8rem;
            font-weight: 900;
            color: var(--primary);
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 0.95rem;
            color: var(--text-muted);
            font-weight: 700;
        }

        /* أقسام الفروع الأكاديمية */
        .branches-section {
            padding: 80px 7%;
        }

        .section-header-center {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-tag {
            background: #eff6ff;
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--text-title);
            margin-bottom: 10px;
        }

        .section-subtitle {
            font-size: 1rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }

        .branches-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }

        .branch-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 28px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .branch-card:hover {
            border-color: var(--primary);
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
        }

        .branch-card-icon {
            width: 55px;
            height: 55px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 18px;
        }

        .branch-card-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-title);
            margin-bottom: 8px;
        }

        .branch-card-desc {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* بوابات الدفع الوطنية */
        .payments-feature-section {
            padding: 80px 7%;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            border-radius: 36px;
            margin: 0 5% 80px;
        }

        .payment-methods-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .payment-box {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 24px;
            text-align: center;
            backdrop-filter: blur(8px);
            transition: 0.2s;
        }

        .payment-box:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
        }

        .payment-icon-wrap {
            font-size: 2.2rem;
            margin-bottom: 12px;
            color: #38bdf8;
        }

        .payment-title {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .payment-detail {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        /* زر الواتساب العائم */
        .whatsapp-floating-fab {
            position: fixed;
            bottom: 28px;
            left: 28px;
            background: #25d366;
            color: white;
            width: 62px;
            height: 62px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 2rem;
            box-shadow: 0 10px 25px rgba(37, 211, 102, 0.45);
            z-index: 999;
            text-decoration: none;
            transition: all 0.3s;
            animation: pulseFab 2.5s infinite;
        }

        .whatsapp-floating-fab:hover {
            transform: scale(1.1);
            color: white;
        }

        @keyframes pulseFab {
            0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
            70% { box-shadow: 0 0 0 18px rgba(37, 211, 102, 0); }
            100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
        }

        /* الفوتر */
        footer.main-footer {
            background: #ffffff;
            border-top: 1px solid var(--border);
            padding: 50px 7% 30px;
            text-align: center;
        }

        .footer-brand-title {
            font-size: 1.4rem;
            font-weight: 900;
            color: var(--text-title);
            margin-bottom: 8px;
        }

        .footer-slogan {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        .footer-copy {
            font-size: 0.82rem;
            color: #94a3b8;
            border-top: 1px solid var(--border);
            padding-top: 20px;
        }

        @media (max-width: 900px) {
            .hero-section {
                grid-template-columns: 1fr;
                padding-top: 110px;
            }
            .hero-title-main {
                font-size: 2.2rem;
            }
            .nav-menu-links {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient orbs -->
    <div class="ambient-glow">
        <div class="glow-1"></div>
        <div class="glow-2"></div>
    </div>

    <!-- شريط الملاحة -->
    <nav class="main-nav">
        <a href="/" class="nav-brand">
            @if(\App\Models\Setting::get('site_logo'))
                <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}" style="max-height: 42px; max-width: 50px; object-fit: contain; border-radius: 8px;">
            @else
                <div class="brand-icon-box">
                    {{ mb_substr(\App\Models\Setting::get('site_name', 'منارة التوجيهي'), 0, 1) }}
                </div>
            @endif
            <span>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</span>
        </a>

        <ul class="nav-menu-links">
            <li><a href="#features">مميزات المنصة</a></li>
            <li><a href="#branches">فروع التوجيهي</a></li>
            <li><a href="#payments">طرق السداد الفلسطينية</a></li>
            <li><a href="{{ route('tawjihi.calculator') }}" target="_blank">حاسبة المعدل</a></li>
            <li><a href="{{ route('tawjihi.formulas') }}" target="_blank">دليل القوانين</a></li>
        </ul>

        <div class="nav-actions">
            @if(auth('student')->check())
                <a href="{{ route('student.dashboard') }}" class="btn-nav-register">
                    <span>لوحة التحكم الدراسية</span>
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            @elseif(auth()->check() && auth()->user()->role === 'teacher')
                <a href="{{ route('teacher.dashboard') }}" class="btn-nav-register">
                    <span>لوحة تحكم المعلم</span>
                    <i class="fa-solid fa-chalkboard-user"></i>
                </a>
            @elseif(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="btn-nav-register">
                    <span>لوحة تحكم الإدارة</span>
                    <i class="fa-solid fa-gear"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-nav-login">تسجيل الدخول</a>
                <a href="{{ route('students.create') }}" class="btn-nav-register">
                    <span>انضم إلينا مجاناً</span>
                    <i class="fa-solid fa-user-plus"></i>
                </a>
            @endif
        </div>
    </nav>

    <!-- الهيرو سكشن -->
    <section class="hero-section">
        <div>
            <div class="hero-badge-pal">
                <span>🇵🇸 المنصة الوطنية المعتمدة لطلبة الثانوية العامة في فلسطين</span>
            </div>
            <h1 class="hero-title-main">
                طريقك المؤكد نحو التفوق والـ <span>99%</span> في امتحانات التوجيهي
            </h1>
            <p class="hero-description-main">
                {{ \App\Models\Setting::get('site_slogan', 'المنصة التعليمية الرائدة التي تجمع نخبة أساتذة فلسطين، شروحات الفيديو التفاعلية، بنوك الأسئلة الذكية، والامتحانات الوزارية التجريبية لكافة الفروع.') }}
            </p>

            <div class="hero-cta-group">
                <a href="{{ route('students.create') }}" class="btn-cta-primary">
                    <span>ابدأ رحلة التفوق الآن</span>
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <a href="{{ route('student.courses.catalog') }}" class="btn-cta-secondary">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>استكشاف باقات المواد</span>
                </a>
            </div>

            <div style="display: flex; align-items: center; gap: 16px; color: var(--text-muted); font-size: 0.88rem; font-weight: 600;">
                <span style="color: var(--emerald);"><i class="fa-solid fa-circle-check"></i> شروحات المنهاج الفلسطيني الجديد</span>
                <span>•</span>
                <span style="color: var(--primary);"><i class="fa-solid fa-comments"></i> محادثة ومتابعة مباشرة مع المعلمين</span>
            </div>
        </div>

        <!-- كرت المعاينة التفاعلية -->
        <div class="hero-preview-box">
            <div class="glass-tutor-card">
                <div class="tutor-card-top">
                    <div>
                        <h4 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--text-title);">مساحتك التعليمية المتطورة</h4>
                        <small style="color: var(--text-muted);">نخبة المعلمين في القدس وغزة والضفة</small>
                    </div>
                    <span class="top-flag-badge">
                        <i class="fa-solid fa-star"></i> منهاج 2026/2027
                    </span>
                </div>

                <div class="interactive-subject-chips">
                    <div class="subject-chip-item">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="chip-icon" style="background: linear-gradient(135deg, #0284c7, #0369a1);">
                                <i class="fa-solid fa-calculator"></i>
                            </div>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-title);">الرياضيات (علمي وصناعي)</strong>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">شروحات التفاضل والتكامل والوزاري</div>
                            </div>
                        </div>
                        <span style="color: var(--emerald); font-weight: 800; font-size: 0.85rem;">متاح الآن ✅</span>
                    </div>

                    <div class="subject-chip-item">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="chip-icon" style="background: linear-gradient(135deg, #059669, #047857);">
                                <i class="fa-solid fa-atom"></i>
                            </div>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-title);">الفيزياء والكيمياء</strong>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">حلول أسئلة الكتاب وتوقعات الامتحان</div>
                            </div>
                        </div>
                        <span style="color: var(--emerald); font-weight: 800; font-size: 0.85rem;">متاح الآن ✅</span>
                    </div>

                    <div class="subject-chip-item">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="chip-icon" style="background: linear-gradient(135deg, #d97706, #b45309);">
                                <i class="fa-solid fa-book-bookmark"></i>
                            </div>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-title);">اللغة العربية والإنجليزية</strong>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">القواعد الوزارية والنصوص والشعر</div>
                            </div>
                        </div>
                        <span style="color: var(--emerald); font-weight: 800; font-size: 0.85rem;">متاح الآن ✅</span>
                    </div>
                </div>

                <div style="background: #eff6ff; border-radius: 14px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-size: 0.85rem; color: #1e40af; font-weight: 700;">
                        <i class="fa-solid fa-wallet"></i> سداد محلي سهل عبر جوال باي وبال باي وبنك فلسطين
                    </div>
                    <strong style="color: var(--primary); font-size: 0.95rem;">بالشيكل (₪)</strong>
                </div>
            </div>
        </div>
    </section>

    <!-- شريط الإحصائيات -->
    <section class="stats-strip">
        <div class="stats-grid">
            <div>
                <div class="stat-number">{{ number_format($stats['students'] ?? 1500) }}+</div>
                <div class="stat-label">طالب وطالبة في عموم فلسطين</div>
            </div>
            <div>
                <div class="stat-number">{{ $stats['subjects'] ?? 18 }}+</div>
                <div class="stat-label">مادة تخصصية شاملة كافة الفروع</div>
            </div>
            <div>
                <div class="stat-number">{{ number_format($stats['lessons'] ?? 350) }}+</div>
                <div class="stat-label">درس فيديو ودوسية وملخص PDF</div>
            </div>
            <div>
                <div class="stat-number">{{ number_format($stats['exams'] ?? 120) }}+</div>
                <div class="stat-label">اختبار إلكتروني وزاري وتجريبي</div>
            </div>
        </div>
    </section>

    <!-- فروع الثانوية العامة -->
    <section class="branches-section" id="branches">
        <div class="section-header-center">
            <span class="section-tag">الفروع المعتمدة</span>
            <h2 class="section-title">نوفر المنهاج لجميع فروع التوجيهي</h2>
            <p class="section-subtitle">شروحات تخصصية مصممة خصيصاً لكل فرع من فروع الثانوية العامة وفق معايير وزارة التربية والتعليم الفلسطينية.</p>
        </div>

        <div class="branches-cards-grid">
            <div class="branch-card">
                <div class="branch-card-icon" style="background: linear-gradient(135deg, #0284c7, #0369a1);">
                    <i class="fa-solid fa-microscope"></i>
                </div>
                <h3 class="branch-card-title">الفرع العلمي</h3>
                <p class="branch-card-desc">رياضيات، فيزياء، كيمياء، أحياء، لغة عربية، لغة إنجليزية، وتربية إسلامية مع حلول شاملة لبنوك الأسئلة الوزارية.</p>
            </div>

            <div class="branch-card">
                <div class="branch-card-icon" style="background: linear-gradient(135deg, #059669, #047857);">
                    <i class="fa-solid fa-feather-pointed"></i>
                </div>
                <h3 class="branch-card-title">الفرع الأدبي</h3>
                <p class="branch-card-desc">تاريخ، جغرافيا، دراسات أدبية وبلاغة، لغة إنجليزية، والعلوم اللغوية بأسلوب مبسط وخرائط ذهنية ذكية.</p>
            </div>

            <div class="branch-card">
                <div class="branch-card-icon" style="background: linear-gradient(135deg, #d97706, #b45309);">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <h3 class="branch-card-title">الريادة والأعمال</h3>
                <p class="branch-card-desc">محاسبة، إدارة واقتصاد، ومشاريع ريادية تؤهلك لأعلى المراتب الجامعية والتفوق المستحق.</p>
            </div>

            <div class="branch-card">
                <div class="branch-card-icon" style="background: linear-gradient(135deg, #6366f1, #4338ca);">
                    <i class="fa-solid fa-gears"></i>
                </div>
                <h3 class="branch-card-title">الفرع الصناعي والمهني</h3>
                <p class="branch-card-desc">الرسم الهندسي، العلوم الصناعية الخاصة، وتطبيقات الكهرباء والإلكترونيات والاتصالات.</p>
            </div>
        </div>
    </section>

    <!-- بوابات الدفع الفلسطينية -->
    <section class="payments-feature-section" id="payments">
        <div style="text-align: center; max-width: 700px; margin: 0 auto;">
            <span style="background: rgba(56, 189, 248, 0.2); color: #38bdf8; padding: 4px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 800; display: inline-block; margin-bottom: 10px;">
                سهولة السداد الوطنية 🇵🇸
            </span>
            <h2 style="font-size: 2.2rem; font-weight: 900; margin-bottom: 10px;">بوابات الدفع المعتمدة في فلسطين</h2>
            <p style="color: #94a3b8; font-size: 0.95rem;">
                اشترك في مادة واحدة أو أكثر وسدد بأمان وسهولة عبر أي من قنوات الدفع الفلسطينية الرسمية بالعملة المحلية (الشيكل ₪).
            </p>
        </div>

        <div class="payment-methods-row">
            <div class="payment-box">
                <div class="payment-icon-wrap"><i class="fa-solid fa-mobile-screen-button"></i></div>
                <h4 class="payment-title">جوال باي (Jawwal Pay)</h4>
                <p class="payment-detail">رقم الحساب: <strong>{{ \App\Models\Setting::get('payment_phone', '0567897212') }}</strong></p>
                <small style="color: #6ee7b7; font-size: 0.75rem;">المستفيد: {{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}</small>
            </div>

            <div class="payment-box">
                <div class="payment-icon-wrap"><i class="fa-solid fa-credit-card"></i></div>
                <h4 class="payment-title">بال باي (PalPay)</h4>
                <p class="payment-detail">كود الخدمة: <strong>{{ \App\Models\Setting::get('palpay_service_code', '99420') }}</strong></p>
                <small style="color: #6ee7b7; font-size: 0.75rem;">تطبيق محفظتي وكافة نقاط البيع</small>
            </div>

            <div class="payment-box">
                <div class="payment-icon-wrap"><i class="fa-solid fa-building-columns"></i></div>
                <h4 class="payment-title">بنك فلسطين (Bank of Palestine)</h4>
                <p class="payment-detail">تطبيق بنكي (Banki) أو التحويل المباشر</p>
                <small style="color: #6ee7b7; font-size: 0.75rem;">المستفيد: {{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}</small>
            </div>

            <div class="payment-box">
                <div class="payment-icon-wrap"><i class="fa-solid fa-ticket"></i></div>
                <h4 class="payment-title">بطاقات الشحن المسبقة</h4>
                <p class="payment-detail">شحن كود القسيمة من المكتبات المعتمدة</p>
                <small style="color: #6ee7b7; font-size: 0.75rem;">تفعيل فوري للاشتراك</small>
            </div>
        </div>
    </section>

    <!-- الفوتر -->
    <footer class="main-footer">
        @if(\App\Models\Setting::get('site_logo'))
            <div style="margin-bottom: 14px;">
                <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}" style="max-height: 55px; max-width: 120px; object-fit: contain;">
            </div>
        @endif
        <h3 class="footer-brand-title">🇵🇸 {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</h3>
        <p class="footer-slogan">{{ \App\Models\Setting::get('site_slogan', 'المنصة الوطنية الرائدة لطلبة الثانوية العامة في فلسطين') }}</p>

        <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 24px;">
            <a href="{{ route('public.faq') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 700;">الأسئلة الشائعة</a>
            <a href="{{ route('public.terms') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 700;">الشروط والأحكام</a>
            <a href="{{ route('public.privacy') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 700;">سياسة الخصوصية</a>
            <a href="{{ route('public.contact') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 700;">اتصل بنا</a>
        </div>

        <div class="footer-copy">
            جميع الحقوق محفوظة © {{ date('Y') }} • تم التطوير والتصميم بعناية فائقة لطلبة فلسطين
        </div>
    </footer>

    <!-- زر الواتساب العائم للتواصل المباشر -->
    @php
        $rawPhone = \App\Models\Setting::get('contact_whatsapp', \App\Models\Setting::get('payment_phone', '0567897212'));
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '970' . substr($cleanPhone, 1);
        }
    @endphp
    <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode('مرحباً، أود الاستفسار عن اشتراكات منارة التوجيهي الفلسطينية') }}" 
       class="whatsapp-floating-fab" 
       target="_blank" 
       title="تواصل معنا عبر واتساب">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

</body>
</html>
