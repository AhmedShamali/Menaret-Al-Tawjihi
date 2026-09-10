<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1d4ed8">
    @if(\App\Models\Setting::get('site_favicon'))
        <link rel="icon" href="{{ asset(\App\Models\Setting::get('site_favicon')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @endif
    <title>@yield('title', 'المنصة التعليمية') | {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸</title>

    <!-- Google Fonts: Alexandria & Tajawal (Clean Educational Typography) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            /* نظام ألوان هادئ مخصص للمنصات التعليمية (Calm EdTech Tokens) */
            --ed-bg: #f8fafc;
            --ed-surface: #ffffff;
            --ed-surface-alt: #f1f5f9;
            --ed-border: #e2e8f0;
            --ed-border-subtle: #edf2f7;
            --ed-border-focus: #3b82f6;

            --ed-primary: #1d4ed8;         /* أزرق أكاديمي رصين ومريح */
            --ed-primary-hover: #1e40af;
            --ed-primary-soft: #eff6ff;
            --ed-primary-border: #bfdbfe;

            --ed-accent: #0284c7;
            --ed-accent-soft: #f0f9ff;

            --ed-text-main: #0f172a;       /* كحلي داكن للنصوص الرئيسية وعالي المقروئية */
            --ed-text-body: #334155;       /* نصوص الشرح والقراءة المريحة */
            --ed-text-muted: #64748b;      /* نصوص مساعدة ثانوية */
            --ed-text-dim: #94a3b8;

            --ed-success: #059669;
            --ed-success-soft: #ecfdf5;
            --ed-warning: #d97706;
            --ed-warning-soft: #fffbeb;
            --ed-danger: #dc2626;
            --ed-danger-soft: #fef2f2;

            --ed-radius-sm: 8px;
            --ed-radius-md: 12px;
            --ed-radius-lg: 16px;
            --ed-radius-xl: 20px;

            --ed-shadow-sm: 0 1px 2px 0 rgba(15, 23, 42, 0.05);
            --ed-shadow-card: 0 1px 3px 0 rgba(15, 23, 42, 0.06), 0 1px 2px -1px rgba(15, 23, 42, 0.04);
            --ed-shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.05);
            --ed-shadow-lg: 0 10px 15px -3px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.03);

            --sidebar-width: 270px;
            --topbar-height: 68px;
            --transition-smooth: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* --- أنماط الوضع الليلي الهادئ المريح للعين (Night Mode) --- */
        body.dark-theme {
            --ed-bg: #0b1120;
            --ed-surface: #0f172a;
            --ed-surface-alt: #1e293b;
            --ed-border: #1e293b;
            --ed-border-subtle: #1e293b;
            --ed-border-focus: #60a5fa;

            --ed-primary: #3b82f6;
            --ed-primary-hover: #60a5fa;
            --ed-primary-soft: rgba(59, 130, 246, 0.12);
            --ed-primary-border: rgba(59, 130, 246, 0.25);

            --ed-text-main: #f8fafc;
            --ed-text-body: #cbd5e1;
            --ed-text-muted: #94a3b8;
            --ed-text-dim: #64748b;

            --ed-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
            --ed-shadow-card: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Alexandria', 'Tajawal', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--ed-bg);
            color: var(--ed-text-body);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            line-height: 1.6;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* --- تصميم الشريط الجانبي الأكاديمي (Sidebar) --- */
        aside.sidebar {
            width: var(--sidebar-width);
            background: var(--ed-surface);
            border-left: 1px solid var(--ed-border);
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: var(--transition-smooth);
            box-shadow: var(--ed-shadow-sm);
        }

        .side-brand {
            height: var(--topbar-height);
            padding: 0 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--ed-border);
            background: var(--ed-surface);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--ed-text-main);
            font-weight: 700;
            font-size: 1.05rem;
        }

        .logo-square {
            width: 38px;
            height: 38px;
            background: var(--ed-primary);
            color: #ffffff;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .menu-wrapper {
            flex: 1;
            overflow-y: auto;
            padding: 18px 12px;
            scrollbar-width: thin;
            scrollbar-color: var(--ed-border) transparent;
        }

        .menu-wrapper::-webkit-scrollbar {
            width: 4px;
        }
        .menu-wrapper::-webkit-scrollbar-thumb {
            background: var(--ed-border);
            border-radius: 4px;
        }

        .group-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--ed-text-dim);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 20px 12px 6px;
            display: block;
        }

        .nav-item {
            display: block;
            text-decoration: none;
            margin-bottom: 3px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 14px;
            border-radius: var(--ed-radius-sm);
            color: var(--ed-text-body);
            font-size: 0.86rem;
            font-weight: 500;
            transition: var(--transition-smooth);
            cursor: pointer;
        }

        .nav-link:hover {
            background: var(--ed-surface-alt);
            color: var(--ed-primary);
        }

        .nav-item.active .nav-link {
            background: var(--ed-primary-soft);
            color: var(--ed-primary);
            font-weight: 700;
        }

        .link-main {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .link-main i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            opacity: 0.9;
        }

        .nav-arrow {
            font-size: 0.68rem;
            transition: transform 0.2s ease;
            color: var(--ed-text-muted);
        }

        .has-sub.open .nav-arrow {
            transform: rotate(-90deg);
            color: var(--ed-primary);
        }

        .submenu {
            display: none;
            list-style: none;
            padding: 4px 0;
            margin: 2px 0 6px 0;
            border-right: 2px solid var(--ed-border);
            margin-right: 22px;
        }

        .has-sub.open .submenu {
            display: block;
        }

        .submenu-item {
            display: block;
            padding: 7px 16px;
            color: var(--ed-text-muted);
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 500;
            border-radius: var(--ed-radius-sm);
            transition: var(--transition-smooth);
        }

        .submenu-item:hover {
            color: var(--ed-primary);
            background: var(--ed-surface-alt);
        }

        .sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid var(--ed-border);
            background: var(--ed-surface);
        }

        /* --- المحتوى الرئيسي (Main Content) --- */
        main.main-content {
            flex: 1;
            margin-right: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition-smooth);
        }

        .top-bar {
            height: var(--topbar-height);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--ed-surface);
            padding: 0 32px;
            border-bottom: 1px solid var(--ed-border);
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        .content-body {
            flex: 1;
            padding: 28px 32px 60px;
        }

        /* زر الجوال */
        .mobile-toggle {
            display: none;
            background: var(--ed-surface-alt);
            color: var(--ed-text-body);
            border: 1px solid var(--ed-border);
            width: 38px;
            height: 38px;
            border-radius: var(--ed-radius-sm);
            cursor: pointer;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: var(--transition-smooth);
        }

        .mobile-toggle:hover {
            color: var(--ed-primary);
            border-color: var(--ed-primary-border);
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.4);
            z-index: 999;
            display: none;
            backdrop-filter: blur(2px);
        }

        .no-sidebar aside.sidebar,
        .no-sidebar .top-bar,
        .no-sidebar .sidebar-overlay {
            display: none !important;
        }

        .no-sidebar main.main-content {
            margin-right: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        /* --- عناصر التصميم الموحدة الهادئة (Universal EdTech UI Components) --- */
        .ed-card {
            background: var(--ed-surface);
            border: 1px solid var(--ed-border);
            border-radius: var(--ed-radius-lg);
            box-shadow: var(--ed-shadow-card);
            overflow: hidden;
            transition: var(--transition-smooth);
        }
        .ed-card:hover {
            box-shadow: var(--ed-shadow-md);
        }

        .ed-card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--ed-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .ed-card-header h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--ed-text-main);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ed-card-body {
            padding: 24px;
        }

        .ed-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--ed-radius-sm);
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: var(--transition-smooth);
        }

        .ed-btn-primary {
            background: var(--ed-primary);
            color: #ffffff;
            border-color: var(--ed-primary);
        }
        .ed-btn-primary:hover {
            background: var(--ed-primary-hover);
            border-color: var(--ed-primary-hover);
            color: #ffffff;
        }

        .ed-btn-outline {
            background: transparent;
            color: var(--ed-text-body);
            border-color: var(--ed-border);
        }
        .ed-btn-outline:hover {
            background: var(--ed-surface-alt);
            color: var(--ed-primary);
            border-color: var(--ed-primary-border);
        }

        .ed-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .ed-badge-blue { background: var(--ed-primary-soft); color: var(--ed-primary); }
        .ed-badge-green { background: var(--ed-success-soft); color: var(--ed-success); }
        .ed-badge-amber { background: var(--ed-warning-soft); color: var(--ed-warning); }
        .ed-badge-red { background: var(--ed-danger-soft); color: var(--ed-danger); }
        .ed-badge-slate { background: var(--ed-surface-alt); color: var(--ed-text-muted); }

        .ed-input, .ed-select {
            width: 100%;
            padding: 10px 14px;
            background: var(--ed-surface);
            border: 1px solid var(--ed-border);
            border-radius: var(--ed-radius-sm);
            color: var(--ed-text-main);
            font-size: 0.9rem;
            outline: none;
            transition: var(--transition-smooth);
        }
        .ed-input:focus, .ed-select:focus {
            border-color: var(--ed-border-focus);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        /* --- شريط التنقل السفلي للهواتف الذكية (Mobile Bottom Nav) --- */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 62px;
            background: var(--ed-surface);
            border-top: 1px solid var(--ed-border);
            z-index: 1000;
            justify-content: space-around;
            align-items: center;
            padding: 4px 8px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.03);
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--ed-text-muted);
            font-size: 0.72rem;
            font-weight: 600;
            gap: 4px;
            position: relative;
            flex: 1;
            padding: 6px 0;
            transition: var(--transition-smooth);
        }

        .bottom-nav-item i {
            font-size: 1.15rem;
        }

        .bottom-nav-item.active,
        .bottom-nav-item:hover {
            color: var(--ed-primary);
        }

        .bottom-nav-badge {
            position: absolute;
            top: 2px;
            right: 22%;
            background: var(--ed-danger);
            color: #ffffff;
            font-size: 0.62rem;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 999px;
            border: 1.5px solid var(--ed-surface);
        }

        /* --- التجاوب مع مختلف الشاشات (Responsive Breakpoints) --- */
        @media (max-width: 1024px) {
            aside.sidebar {
                transform: translateX(105%);
                box-shadow: -4px 0 25px rgba(0,0,0,0.08);
            }
            aside.sidebar.mobile-active {
                transform: translateX(0);
            }
            main.main-content {
                margin-right: 0;
                width: 100%;
            }
            .mobile-toggle {
                display: flex;
            }
            .sidebar-overlay.active {
                display: block;
            }
            .top-bar {
                padding: 0 18px;
            }
            .content-body {
                padding: 20px 18px 80px;
            }
        }

        @media (max-width: 768px) {
            .mobile-bottom-nav {
                display: flex;
            }
            body:not(.no-sidebar) {
                padding-bottom: 64px;
            }
            .date-info {
                display: none !important;
            }
        }

        @media (max-width: 480px) {
            .user-info-text {
                display: none;
            }
        }
    </style>
</head>
<body class="{{ request()->is('login') || request()->is('register') ? 'no-sidebar' : '' }}">

    <!-- طبقة التعتيم للجوال -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- الشريط الجانبي الأكاديمي -->
    <aside class="sidebar" id="sidebar">
        <div class="side-brand">
            <a href="/" class="brand-logo">
                @if(\App\Models\Setting::get('site_logo'))
                    <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}" style="max-height: 38px; max-width: 44px; object-fit: contain; border-radius: 6px;">
                @else
                    <div class="logo-square">{{ mb_substr(\App\Models\Setting::get('site_name', 'منارة التوجيهي'), 0, 1) }}</div>
                @endif
                <span>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</span>
            </a>
        </div>

        <div class="menu-wrapper">
            @if(auth()->check() && auth()->user()->role === 'admin')
                <span class="group-label">الإدارة العامة</span>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-chart-pie"></i> <span>لوحة الإحصائيات</span></div></div>
                </a>

                <div class="nav-item has-sub {{ Request::is('admin/students*') || Request::is('admin/teachers*') ? 'open' : '' }}">
                    <div class="nav-link" onclick="toggleSub(this)">
                        <div class="link-main"><i class="fa-solid fa-user-gear"></i> <span>القبول والكادر</span></div>
                        <i class="fa-solid fa-chevron-left nav-arrow"></i>
                    </div>
                    <ul class="submenu">
                        <li><a href="{{ route('admin.students.index') }}" class="submenu-item">إدارة الطلاب</a></li>
                        <li><a href="{{ route('admin.teachers.index') }}" class="submenu-item">إدارة المعلمين</a></li>
                        <li><a href="{{ route('admin.teachers.info') }}" class="submenu-item">إضافة معلم جديد</a></li>
                        <li><a href="{{ route('admin.students.profile_all') }}" class="submenu-item">سجل الطلاب الكامل</a></li>
                    </ul>
                </div>

                <a href="{{ route('admin.certificates.index') }}" class="nav-item {{ Request::is('admin/certificates*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-graduation-cap" style="color: #4f46e5;"></i> <span>الشهادات والنتائج</span></div></div>
                </a>

                <a href="{{ route('admin.subjects.pricing') }}" class="nav-item {{ Request::is('admin/subjects/pricing*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-tags" style="color: #059669;"></i> <span>تسعير المواد</span></div></div>
                </a>

                <a href="{{ route('admin.payments.index') }}" class="nav-item {{ Request::is('admin/payments*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-wallet" style="color: #d97706;"></i> <span>الاشتراكات والمدفوعات</span></div></div>
                </a>

                <span class="group-label">التواصل والدعم</span>
                <a href="{{ route('admin.messages.index') }}" class="nav-item {{ Request::is('admin/inbox*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-comments"></i> <span>رسائل الطلاب</span></div></div>
                </a>
                <a href="{{ route('admin.teachers.chat') }}" class="nav-item {{ Request::is('admin/teachers/chat*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-chalkboard-user"></i> <span>مراسلة المعلمين</span></div></div>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="nav-item {{ Request::is('admin/settings*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-gear"></i> <span>إعدادات النظام</span></div></div>
                </a>
            @endif

            @if(auth()->check() && auth()->user()->role === 'teacher')
                <span class="group-label">بوابة المعلم</span>
                <a href="{{ route('teacher.dashboard') }}" class="nav-item {{ Request::is('teacher/dashboard*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-house"></i> <span>لوحة التحكم</span></div></div>
                </a>

                <div class="nav-item has-sub {{ Request::is('teacher/exams*') || Request::is('teacher/submissions*') ? 'open' : '' }}">
                    <div class="nav-link" onclick="toggleSub(this)">
                        <div class="link-main"><i class="fa-solid fa-file-pen"></i> <span>إدارة الاختبارات</span></div>
                        <i class="fa-solid fa-chevron-left nav-arrow"></i>
                    </div>
                    <ul class="submenu">
                        <li><a href="{{ route('teacher.exams.index') }}" class="submenu-item">قائمة الاختبارات</a></li>
                        <li><a href="{{ route('teacher.exams.create') }}" class="submenu-item">بناء اختبار جديد</a></li>
                        <li><a href="{{ route('teacher.submissions.index') }}" class="submenu-item">رصد درجات الطلاب</a></li>
                    </ul>
                </div>

                <a href="{{ route('teacher.educational_contents.index') }}" class="nav-item {{ Request::is('teacher/educational_contents*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-photo-film"></i> <span>المحتوى والملفات</span></div></div>
                </a>

                <a href="{{ route('teacher.access.index') }}" class="nav-item {{ Request::is('teacher/access*') || Request::is('teacher/students*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-user-check"></i> <span>اشتراكات وصلاحيات الطلاب</span></div></div>
                </a>

                <a href="{{ route('teacher.messages.index') }}" class="nav-item {{ Request::is('teacher/inbox*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-comments"></i> <span>رسائل الطلاب</span></div></div>
                </a>

                <a href="{{ route('teacher.admin.chat') }}" class="nav-item {{ Request::is('teacher/admin/chat*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-shield-halved"></i> <span>مراسلة الإدارة</span></div></div>
                </a>
            @endif

            @if(auth('student')->check() || (auth()->check() && auth()->user()->role === 'student'))
                <span class="group-label">المساحة التعليمية</span>
                <a href="{{ route('student.dashboard') }}" class="nav-item {{ Request::is('student/dashboard*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-house-chimney"></i> <span>الرئيسية</span></div></div>
                </a>
                <a href="{{ route('student.subjects.index') }}" class="nav-item {{ request()->routeIs('student.subjects.*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-book-open"></i> <span>المواد والدروس</span></div></div>
                </a>
                <a href="{{ route('student.exams.index') }}" class="nav-item {{ Request::is('student/my-exams*') || Request::is('student/exams*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-pen-ruler"></i> <span>اختباراتي</span></div></div>
                </a>
                <a href="{{ route('student.courses.catalog') }}" class="nav-item {{ Request::is('student/courses/catalog*') || Request::is('student/checkout*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-layer-group"></i> <span>باقات المواد والاشتراك</span></div></div>
                </a>
                <a href="{{ route('student.notifications.index') }}" class="nav-item {{ Request::is('student/notifications*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-bell"></i> <span>مركز التنبيهات</span></div></div>
                </a>

                <span class="group-label">أدوات التفوق الدراسي</span>
                <a href="{{ route('student.planner.index') }}" class="nav-item {{ Request::is('student/study-planner*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-calendar-check" style="color: #0284c7;"></i> <span>جدول المراجعة</span></div></div>
                </a>
                <a href="{{ route('student.achievements') }}" class="nav-item {{ Request::is('student/achievements*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-award" style="color: #059669;"></i> <span>الشهادات والإنجازات</span></div></div>
                </a>
                <a href="{{ route('student.leaderboard') }}" class="nav-item {{ Request::is('student/leaderboard*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-fire" style="color: #ea580c;"></i> <span>مؤشر الالتزام اليومي</span></div></div>
                </a>
                <a href="{{ route('tawjihi.calculator') }}" target="_blank" class="nav-item">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-calculator" style="color: #1d4ed8;"></i> <span>حاسبة المعدل الجامعي</span></div></div>
                </a>
                <a href="{{ route('tawjihi.formulas') }}" target="_blank" class="nav-item">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-square-root-variable" style="color: #7c3aed;"></i> <span>دليل القوانين الوزارية</span></div></div>
                </a>

                <span class="group-label">الحساب والتواصل</span>
                <a href="{{ route('student.teachers.index') }}" class="nav-item {{ Request::is('student/teachers*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-chalkboard-user"></i> <span>معلمو مرحلتي</span></div></div>
                </a>
                <a href="{{ route('student.support') }}" class="nav-item {{ Request::is('student/support*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-headset"></i> <span>المساعدة والدعم</span></div></div>
                </a>
                <a href="{{ route('student.profile') }}" class="nav-item {{ Request::is('student/profile*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-user-gear"></i> <span>الملف الشخصي</span></div></div>
                </a>
            @endif
        </div>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" style="background:none; border:none; color: var(--ed-danger); cursor:pointer; font-weight:600; font-size:0.86rem; display:flex; align-items:center; gap:10px; width:100%; padding: 6px 8px; border-radius: var(--ed-radius-sm); transition: var(--transition-smooth);" onmouseover="this.style.background='var(--ed-danger-soft)'" onmouseout="this.style.background='none'">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    <!-- مساحة العمل والمحتوى -->
    <main class="main-content">
        <header class="top-bar">
            <div style="display: flex; align-items: center; gap: 14px;">
                <button class="mobile-toggle" id="btnToggleSidebar" aria-label="فتح القائمة">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="date-info" style="color: var(--ed-text-muted); font-weight: 500; font-size: 0.84rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-regular fa-calendar" style="color: var(--ed-primary);"></i> {{ date('Y/m/d') }}
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:12px;">
                @php
                    $unreadCount = 0; 
                    $unreadItems = collect();

                    try {
                        $isStudent = auth('student')->check();

                        if($isStudent) {
                            $sId = auth('student')->id();
                            $studentUser = auth('student')->user();
                            $dbNotifs = $studentUser ? $studentUser->unreadNotifications()->count() : 0;
                            $msgNotifs = \App\Models\Message::where('student_id', $sId)->where('sender_type', '!=', 'student')->where('is_read', false)->count();
                            $unreadCount = $dbNotifs + $msgNotifs;
                            $unreadItems = \App\Models\Message::where('student_id', $sId)->where('sender_type', '!=', 'student')->where('is_read', false)->latest()->take(5)->get();
                        } elseif(auth()->check() && auth()->user()->role === 'teacher') {
                            $tId = auth()->id();
                            $unreadCount = \App\Models\Message::where('teacher_id', $tId)->where('sender_type', 'student')->where('is_read', false)->count();
                            $unreadItems = \App\Models\Message::where('teacher_id', $tId)->where('sender_type', 'student')->where('is_read', false)->latest()->take(5)->get();
                        } elseif(auth()->check() && auth()->user()->role === 'admin') {
                            $unreadCount = \App\Models\Message::whereNull('teacher_id')->where('sender_type', 'student')->where('is_read', false)->count();
                            $unreadItems = \App\Models\Message::whereNull('teacher_id')->where('sender_type', 'student')->where('is_read', false)->latest()->take(5)->get();
                        }
                    } catch (\Throwable $e) {
                        $unreadCount = 0;
                        $unreadItems = collect();
                    }
                @endphp

                <!-- قائمة الإشعارات -->
                <div class="notifications-dropdown-container" style="position: relative;">
                    <button id="notificationsToggle" style="background: var(--ed-surface); border: 1px solid var(--ed-border); width: 40px; height: 40px; border-radius: 10px; cursor: pointer; position: relative; display: grid; place-items: center; transition: var(--transition-smooth); color: var(--ed-text-body);">
                        <i class="fa-regular fa-bell" style="font-size: 1.1rem;"></i>
                        <span id="navUnreadBadge" style="{{ $unreadCount > 0 ? '' : 'display: none;' }} position: absolute; top: -3px; right: -3px; background: var(--ed-danger); color: white; font-size: 0.62rem; padding: 2px 6px; border-radius: 99px; border: 2px solid var(--ed-surface); font-weight: 700;">{{ $unreadCount }}</span>
                    </button>
                    
                    <div id="notificationsMenu" style="display: none; position: absolute; left: 0; top: 48px; width: 320px; background: var(--ed-surface); border-radius: var(--ed-radius-md); box-shadow: var(--ed-shadow-lg); border: 1px solid var(--ed-border); z-index: 1000; overflow: hidden;">
                        <div style="padding: 12px 16px; background: var(--ed-surface-alt); border-bottom: 1px solid var(--ed-border); display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; font-size: 0.88rem; color: var(--ed-text-main); display: flex; align-items: center; gap: 8px;">
                                <i class="fa-regular fa-bell" style="color: var(--ed-primary);"></i> التنبيهات
                            </span>
                            @if(isset($isStudent) && $isStudent)
                                <button onclick="markAllReadFromNav()" style="background: none; border: none; font-size: 0.74rem; color: var(--ed-primary); font-weight: 600; cursor: pointer;">
                                    تحديد الكل كمقروء
                                </button>
                            @endif
                        </div>

                        <div style="max-height: 300px; overflow-y: auto;" id="navNotificationsList">
                            @forelse($unreadItems as $item)
                                @php
                                    $link = '#';
                                    if (isset($isStudent) && $isStudent) {
                                        $link = $item->sender_type === 'teacher' ? route('student.chat.teacher', $item->teacher_id ?? 1) : route('student.support');
                                    } elseif (auth()->check() && auth()->user()->role === 'teacher') {
                                        $link = route('teacher.messages.index');
                                    } else {
                                        $link = route('admin.messages.index');
                                    }
                                @endphp
                                <a href="{{ $link }}" style="display: flex; gap: 12px; padding: 12px 16px; border-bottom: 1px solid var(--ed-border-subtle); text-decoration: none; color: inherit; transition: var(--transition-smooth);" onmouseover="this.style.background='var(--ed-surface-alt)'" onmouseout="this.style.background='transparent'">
                                    <div style="width: 34px; height: 34px; border-radius: 8px; background: var(--ed-primary-soft); color: var(--ed-primary); display: grid; place-items: center; flex-shrink: 0; font-size: 0.85rem;">
                                        <i class="fa-regular fa-message"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-size: 0.82rem; font-weight: 600; color: var(--ed-text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $item->message }}
                                        </div>
                                        <span style="font-size: 0.72rem; color: var(--ed-text-dim);">{{ $item->created_at ? $item->created_at->diffForHumans() : 'الآن' }}</span>
                                    </div>
                                </a>
                            @empty
                                <div style="padding: 28px 16px; text-align: center; color: var(--ed-text-muted);">
                                    <i class="fa-regular fa-circle-check" style="font-size: 1.6rem; margin-bottom: 6px; display: block; color: var(--ed-success); opacity: 0.8;"></i>
                                    <span style="font-size: 0.84rem; font-weight: 500;">لا توجد إشعارات جديدة</span>
                                </div>
                            @endforelse
                        </div>

                        @if(isset($isStudent) && $isStudent)
                            <div style="padding: 10px; background: var(--ed-surface-alt); border-top: 1px solid var(--ed-border); text-align: center;">
                                <a href="{{ route('student.notifications.index') }}" style="font-size: 0.8rem; font-weight: 600; color: var(--ed-primary); text-decoration: none;">
                                    عرض كافة التنبيهات ←
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- زر الوضع الليلي / النهاري -->
                <button id="themeToggleBtn" onclick="toggleTheme()" title="تبديل المظهر" style="background: var(--ed-surface); border: 1px solid var(--ed-border); width: 40px; height: 40px; border-radius: 10px; cursor: pointer; display: grid; place-items: center; transition: var(--transition-smooth); color: var(--ed-text-body);">
                    <i class="fa-regular fa-moon" id="themeIcon"></i>
                </button>

                <!-- بطاقة المستخدم -->
                <div style="display:flex; align-items:center; gap:9px; background: var(--ed-surface); padding: 5px 12px; border-radius: 10px; border: 1px solid var(--ed-border);">
                    <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--ed-primary-soft); color: var(--ed-primary); display: grid; place-items: center; font-size: 0.85rem; font-weight: 700;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <span class="user-info-text" style="font-size: 0.84rem; font-weight: 600; color: var(--ed-text-main);">
                        {{ auth()->user()->name ?? auth('student')->user()->name_ar ?? auth('student')->user()->name ?? 'حسابي' }}
                    </span>
                </div>
            </div>
        </header>

        <div class="content-body">
            @yield('content')
        </div>
    </main>

    <!-- شريط التنقل السفلي للهواتف الذكية -->
    <nav class="mobile-bottom-nav">
        @if(auth('student')->check())
            <a href="{{ route('student.dashboard') }}" class="bottom-nav-item {{ Request::is('student/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>الرئيسية</span>
            </a>
            <a href="{{ route('student.subjects.index') }}" class="bottom-nav-item {{ Request::is('student/subjects*') ? 'active' : '' }}">
                <i class="fa-solid fa-book-open"></i>
                <span>المواد</span>
            </a>
            <a href="{{ route('student.exams.index') }}" class="bottom-nav-item {{ Request::is('student/my-exams*') || Request::is('student/exams*') ? 'active' : '' }}">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>اختباراتي</span>
            </a>
            <a href="{{ route('student.achievements') }}" class="bottom-nav-item {{ Request::is('student/achievements*') ? 'active' : '' }}">
                <i class="fa-solid fa-award"></i>
                <span>الشهادات</span>
            </a>
            <a href="{{ route('student.profile') }}" class="bottom-nav-item {{ Request::is('student/profile*') ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i>
                <span>حسابي</span>
            </a>
        @elseif(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="bottom-nav-item {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>اللوحة</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="bottom-nav-item {{ Request::is('admin/students*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>الطلاب</span>
            </a>
            <a href="{{ route('admin.certificates.index') }}" class="bottom-nav-item {{ Request::is('admin/certificates*') ? 'active' : '' }}">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>الشهادات</span>
            </a>
            <a href="{{ route('admin.payments.index') }}" class="bottom-nav-item {{ Request::is('admin/payments*') ? 'active' : '' }}">
                <i class="fa-solid fa-wallet"></i>
                <span>المدفوعات</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="bottom-nav-item {{ Request::is('admin/settings*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i>
                <span>الإعدادات</span>
            </a>
        @elseif(auth()->check() && auth()->user()->role === 'teacher')
            <a href="{{ route('teacher.dashboard') }}" class="bottom-nav-item {{ Request::is('teacher/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>اللوحة</span>
            </a>
            <a href="{{ route('teacher.exams.index') }}" class="bottom-nav-item {{ Request::is('teacher/exams*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-signature"></i>
                <span>الاختبارات</span>
            </a>
            <a href="{{ route('teacher.submissions.index') }}" class="bottom-nav-item {{ Request::is('teacher/submissions*') ? 'active' : '' }}">
                <i class="fa-solid fa-marker"></i>
                <span>التصحيح</span>
            </a>
            <a href="{{ route('teacher.students.index') }}" class="bottom-nav-item {{ Request::is('teacher/students*') || Request::is('teacher/access*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-check"></i>
                <span>الطلاب</span>
            </a>
            <a href="{{ route('teacher.admin.chat') }}" class="bottom-nav-item {{ Request::is('teacher/admin/chat*') ? 'active' : '' }}">
                <i class="fa-solid fa-shield-halved"></i>
                <span>الإدارة</span>
            </a>
        @else
            <a href="/" class="bottom-nav-item {{ Request::is('/') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>الرئيسية</span>
            </a>
            <a href="{{ route('stages.index') }}" class="bottom-nav-item {{ Request::is('stages*') ? 'active' : '' }}">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>الفروع</span>
            </a>
            <a href="{{ route('tawjihi.calculator') }}" class="bottom-nav-item {{ Request::is('tawjihi-calculator*') ? 'active' : '' }}">
                <i class="fa-solid fa-calculator"></i>
                <span>الحاسبة</span>
            </a>
            <a href="{{ route('contact') }}" class="bottom-nav-item {{ Request::is('contact*') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope"></i>
                <span>تواصل</span>
            </a>
            <a href="{{ route('login') }}" class="bottom-nav-item {{ Request::is('login*') ? 'active' : '' }}">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                <span>دخول</span>
            </a>
        @endif
    </nav>

    <script>
        function toggleSub(el) { 
            el.parentElement.classList.toggle('open'); 
        }

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const btnToggle = document.getElementById('btnToggleSidebar');

        if(btnToggle) {
            btnToggle.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-active');
                overlay.classList.toggle('active');
            });
        }

        if(overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-active');
                overlay.classList.remove('active');
            });
        }

        const notifToggle = document.getElementById('notificationsToggle');
        const notifMenu = document.getElementById('notificationsMenu');
        if(notifToggle && notifMenu) {
            notifToggle.onclick = (e) => { 
                e.stopPropagation(); 
                notifMenu.style.display = notifMenu.style.display === 'block' ? 'none' : 'block'; 
            };
            window.addEventListener('click', () => {
                notifMenu.style.display = 'none';
            });
        }

        function markAllReadFromNav() {
            axios.post('/student/notifications/mark-all-read', {
                _token: '{{ csrf_token() }}'
            }).then(() => {
                const badge = document.getElementById('navUnreadBadge');
                if (badge) badge.style.display = 'none';
                const list = document.getElementById('navNotificationsList');
                if (list) {
                    list.innerHTML = '<div style="padding: 24px 16px; text-align: center; color: var(--ed-text-muted);"><i class="fa-regular fa-circle-check" style="font-size: 1.6rem; margin-bottom: 6px; display: block; color: var(--ed-success);"></i><span style="font-size: 0.84rem; font-weight: 500;">تمت قراءة جميع الإشعارات بنجاح</span></div>';
                }
            });
        }

        function toggleTheme() {
            const isDark = document.body.classList.toggle('dark-theme');
            localStorage.setItem('tawjihi-theme', isDark ? 'dark' : 'light');
            updateThemeIcon(isDark);
        }

        function updateThemeIcon(isDark) {
            const icon = document.getElementById('themeIcon');
            if (icon) {
                icon.className = isDark ? 'fa-regular fa-sun' : 'fa-regular fa-moon';
            }
        }

        (function initTheme() {
            const savedTheme = localStorage.getItem('tawjihi-theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-theme');
                updateThemeIcon(true);
            }
        })();

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>