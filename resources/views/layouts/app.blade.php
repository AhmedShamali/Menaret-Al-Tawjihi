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
    <title>@yield('title') | {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸</title>

    <!-- الخطوط والأيقونات -->
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --side-bg: #ffffff;
            --side-active: #eff6ff;
            --side-hover: #f8fafc;
            --text-active: #1e40af;
            --bg-body: #f8fafc;
            --sidebar-width: 285px;
            --primary-color: #1e40af;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* --- أنماط الوضع الليلي (Dark Theme) --- */
        body.dark-theme {
            --side-bg: #0b1120;
            --side-active: #1e293b;
            --side-hover: #1e293b;
            --text-active: #38bdf8;
            --bg-body: #060913;
            --primary-color: #38bdf8;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border-color: #1e293b;
        }

        body.dark-theme .top-bar,
        body.dark-theme .side-brand,
        body.dark-theme aside.sidebar,
        body.dark-theme .user-info-text {
            background-color: #0b1120 !important;
            color: #f1f5f9 !important;
            border-color: #1e293b !important;
        }

        body.dark-theme h1, 
        body.dark-theme h2, 
        body.dark-theme h3, 
        body.dark-theme h4,
        body.dark-theme .brand-logo,
        body.dark-theme .st-exam-title,
        body.dark-theme .st-completed-title {
            color: #f1f5f9 !important;
        }

        body.dark-theme #notificationsMenu {
            background: #0b1120 !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        body.dark-theme #themeToggleBtn {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #fbbf24 !important;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Alexandria', sans-serif; }
        body { background: var(--bg-body); min-height: 100vh; overflow-x: hidden; display: flex; color: var(--text-main); transition: background 0.3s ease, color 0.3s ease; }

        /* --- السايدبار --- */
        aside.sidebar {
            width: var(--sidebar-width);
            background: var(--side-bg);
            color: var(--text-main);
            position: fixed;
            top: 0; right: 0; bottom: 0;
            display: flex; flex-direction: column;
            z-index: 2000;
            border-left: 1px solid var(--border-color);
            box-shadow: 4px 0 20px rgba(0,0,0,0.02);
            transition: var(--transition);
        }

        .side-brand { padding: 25px 20px; background: #ffffff; text-align: center; border-bottom: 1px solid var(--border-color); }
        .brand-logo { font-size: 1.25rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; justify-content: center; gap: 12px; text-decoration: none; }
        .logo-square { width: 42px; height: 42px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #fff; border-radius: 12px; display: grid; place-items: center; font-size: 1.25rem; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2); }

        .menu-wrapper { flex: 1; overflow-y: auto; padding: 20px 12px; }
        .menu-wrapper::-webkit-scrollbar { width: 5px; }
        .menu-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .group-label { font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.2px; margin: 25px 15px 10px; display: block; border-bottom: 1px solid var(--border-color); padding-bottom: 5px; }

        .nav-item { display: block; text-decoration: none; margin-bottom: 4px; }
        .nav-link { display: flex; align-items: center; justify-content: space-between; padding: 11px 16px; border-radius: 10px; color: #475569; cursor: pointer; transition: var(--transition); font-size: 0.88rem; font-weight: 500; }
        .nav-link:hover { background: var(--side-hover); color: var(--primary-color); transform: translateX(-3px); }
        .nav-item.active .nav-link { background: var(--side-active); color: var(--text-active); font-weight: 700; border-right: 3px solid var(--primary-color); }

        .link-main { display: flex; align-items: center; gap: 12px; }
        .link-main i { width: 22px; text-align: center; font-size: 1.05rem; }

        .nav-arrow { font-size: 0.7rem; transition: 0.3s; color: var(--text-muted); }
        .has-sub.open .nav-arrow { transform: rotate(-90deg); color: var(--text-active); }

        .submenu { display: none; list-style: none; padding: 6px 0; background: #f8fafc; border-radius: 8px; margin: 6px 10px; border: 1px solid var(--border-color); }
        .has-sub.open .submenu { display: block; }
        .submenu-item { display: block; padding: 9px 40px 9px 15px; color: var(--text-muted); text-decoration: none; font-size: 0.83rem; border-radius: 6px; transition: 0.2s; }
        .submenu-item:hover { color: var(--primary-color); background: #f1f5f9; }

        /* --- المحتوى الرئيسي --- */
        main.main-content { flex: 1; margin-right: var(--sidebar-width); padding: 30px 40px; width: calc(100% - var(--sidebar-width)); transition: var(--transition); min-height: 100vh; }
        
        .top-bar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            background: white; 
            padding: 12px 25px; 
            border-radius: 16px; 
            margin-bottom: 30px; 
            box-shadow: 0 2px 12px rgba(0,0,0,0.02); 
            border: 1px solid var(--border-color);
            position: sticky;
            top: 15px;
            z-index: 1000;
        }

        /* زر الجوال */
        .mobile-toggle {
            display: none;
            background: var(--primary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        /* خلفية التعتيم للجوال */
        .sidebar-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1999;
            display: none;
            backdrop-filter: blur(4px);
        }

        /* الإخفاء عند صفحة الدخول */
        .no-sidebar aside.sidebar, .no-sidebar .top-bar, .no-sidebar .sidebar-overlay { display: none !important; }
        .no-sidebar main.main-content { margin-right: 0 !important; padding: 0 !important; width: 100% !important; }

        /* --- التحسين للموبايل والتابلت --- */
        @media (max-width: 1024px) {
            aside.sidebar { 
                transform: translateX(105%); 
                box-shadow: -10px 0 30px rgba(0,0,0,0.1);
            }
            aside.sidebar.mobile-active { 
                transform: translateX(0); 
            }
            main.main-content { 
                margin-right: 0; 
                width: 100%; 
                padding: 15px; 
            }
            .top-bar {
                margin-bottom: 20px;
                padding: 10px 15px;
            }
            .mobile-toggle { 
                display: flex; 
            }
            .sidebar-overlay.active { 
                display: block; 
            }
            .date-info {
                display: none !important; /* إخفاء التاريخ في الشاشات الصغيرة لتوفير مساحة */
            }
        }

        @media (max-width: 480px) {
            .user-info-text { display: none; }
            .brand-logo span { font-size: 1rem; }
            .logo-square { width: 35px; height: 35px; font-size: 1rem; }
        }

        /* شريط التنقل السفلي الفاخر للهواتف الذكية (Mobile Bottom Navigation) */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 65px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-top: 1px solid var(--border-color);
            z-index: 1000;
            justify-content: space-around;
            align-items: center;
            padding: 4px 10px;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
        }
        .dark-theme .mobile-bottom-nav {
            background: rgba(15, 23, 42, 0.95);
            border-top-color: #1e293b;
        }
        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 700;
            gap: 3px;
            position: relative;
            flex: 1;
            padding: 6px 0;
            transition: all 0.2s ease;
        }
        .bottom-nav-item i {
            font-size: 1.18rem;
            transition: transform 0.2s ease;
        }
        .bottom-nav-item.active, .bottom-nav-item:hover {
            color: var(--primary-color);
        }
        .bottom-nav-item.active i {
            transform: translateY(-2px);
        }
        .bottom-nav-badge {
            position: absolute;
            top: 2px;
            right: 20%;
            background: #ef4444;
            color: #ffffff;
            font-size: 0.6rem;
            font-weight: 800;
            padding: 1px 5px;
            border-radius: 999px;
            border: 1.5px solid #ffffff;
        }
        @media (max-width: 768px) {
            .mobile-bottom-nav {
                display: flex;
            }
            body:not(.no-sidebar) {
                padding-bottom: 70px;
            }
        }
    </style>
</head>
<body class="{{ request()->is('login') || request()->is('register') ? 'no-sidebar' : '' }}">

    <!-- طبقة التظليل للجوال -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="side-brand">
            <a href="/" class="brand-logo">
                @if(\App\Models\Setting::get('site_logo'))
                    <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}" style="max-height: 40px; max-width: 48px; object-fit: contain; border-radius: 8px;">
                @else
                    <div class="logo-square">{{ mb_substr(\App\Models\Setting::get('site_name', 'منارة التوجيهي'), 0, 1) }}</div>
                @endif
                <span>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</span>
            </a>
        </div>

        <div class="menu-wrapper">
            @if(auth()->check() && auth()->user()->role === 'admin')
                <span class="group-label">الإدارة والرقابة</span>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-chart-line"></i> <span>إحصائيات المنصة</span></div></div>
                </a>

                <div class="nav-item has-sub {{ Request::is('admin/students*') || Request::is('admin/teachers*') ? 'open' : '' }}">
                    <div class="nav-link" onclick="toggleSub(this)">
                        <div class="link-main"><i class="fa-solid fa-user-plus"></i> <span>القبول والتسجيل</span></div>
                        <i class="fa-solid fa-chevron-left nav-arrow"></i>
                    </div>
                    <ul class="submenu">
                        <li><a href="{{ route('admin.students.index') }}" class="submenu-item">إدارة الطلاب</a></li>
                        <li><a href="{{ route('admin.teachers.info') }}" class="submenu-item">إضافة معلم</a></li>
                    </ul>
                </div>

                <a href="{{ route('admin.teachers.index') }}" class="nav-item {{ Request::is('admin/teachers*') && !Request::is('admin/teachers/create') ? 'active' : '' }}">
                    <div class="nav-link">
                        <div class="link-main"><i class="fa-solid fa-chalkboard-user"></i> <span>المعلمين</span></div>
                    </div>
                </a>

                <a href="{{ route('admin.students.profile_all') }}" class="nav-item {{ Request::is('admin/students/records/all*') ? 'active' : '' }}">
                    <div class="nav-link">
                        <div class="link-main"><i class="fa-solid fa-user-graduate"></i> <span>سجل الطلاب</span></div>
                    </div>
                </a>

                <a href="{{ route('admin.settings.index') }}" class="nav-item {{ Request::is('admin/settings*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-gears"></i> <span>إعدادات الهوية</span></div></div>
                </a>

                <a href="{{ route('admin.teachers.chat') }}" class="nav-item {{ Request::is('admin/teachers/chat*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-message"></i> <span>مراسلة المعلمين</span></div></div>
                </a>

                <a href="{{ route('admin.messages.index') }}" class="nav-item {{ Request::is('admin/inbox*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-comments"></i> <span>مراسلة الطلاب</span></div></div>
                </a>

                <a href="{{ route('admin.subjects.pricing') }}" class="nav-item {{ Request::is('admin/subjects/pricing*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-tags" style="color: #10b981;"></i> <span>تسعير المواد والخصومات 🏷️</span></div></div>
                </a>

                <a href="{{ route('admin.payments.index') }}" class="nav-item {{ Request::is('admin/payments*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-wallet" style="color: #f59e0b;"></i> <span>الاشتراكات والمدفوعات 💳</span></div></div>
                </a>

                <a href="{{ route('admin.certificates.index') }}" class="nav-item {{ Request::is('admin/certificates*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-award" style="color: #6366f1;"></i> <span>شهادات ونتائج التخرج 🎓</span></div></div>
                </a>
            @endif

            @if(auth()->check() && auth()->user()->role === 'teacher')
                <span class="group-label">بوابة المحاضر</span>
                <a href="{{ route('teacher.dashboard') }}" class="nav-item {{ Request::is('teacher/dashboard') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-house"></i> <span>لوحة التحكم</span></div></div>
                </a>

                <a href="{{ route('teacher.admin.chat') }}" class="nav-item {{ Request::is('teacher/admin/chat') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-user-shield"></i> <span>مراسلة الإدارة</span></div></div>
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
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-photo-film"></i> <span>دروسي وملفاتي</span></div></div>
                </a>
                <a href="{{ route('teacher.access.index') }}" class="nav-item {{ Request::is('teacher/access*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-user-shield"></i> <span>اشتراكات وصلاحيات الطلاب</span></div></div>
                </a>
                <a href="{{ route('teacher.messages.index') }}" class="nav-item {{ Request::is('teacher/inbox*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-comments"></i> <span>رسائل الطلاب</span></div></div>
                </a>
            @endif

            @if(auth('student')->check() || (auth()->check() && auth()->user()->role === 'student'))
                <span class="group-label">مساحتي التعليمية</span>
                <a href="{{ route('student.dashboard') }}" class="nav-item {{ Request::is('student/dashboard') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-house-user"></i> <span>الرئيسية</span></div></div>
                </a>
                <a href="{{ route('student.exams.index') }}" class="nav-item {{ Request::is('student/my-exams*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-graduation-cap"></i> <span>اختباراتي</span></div></div>
                </a>
                <a href="{{ route('student.profile') }}" class="nav-item {{ Request::is('student/profile*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-user-gear"></i> <span>ملفي الشخصي</span></div></div>
                </a>
                <a href="{{ route('student.subjects.index') }}" class="nav-item {{ request()->routeIs('student.subjects.*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-book-open-reader"></i> <span>المواد الدراسية</span></div></div>
                </a>
                <a href="{{ route('student.courses.catalog') }}" class="nav-item {{ Request::is('student/courses/catalog*') || Request::is('student/checkout*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-cart-shopping" style="color: #0284c7;"></i> <span>باقات المواد والاشتراك 💳</span></div></div>
                </a>
                <a href="{{ route('student.notifications.index') }}" class="nav-item {{ Request::is('student/notifications*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-bell" style="color: #eab308;"></i> <span>مركز التنبيهات 🔔</span></div></div>
                </a>
                <a href="{{ route('student.teachers.index') }}" class="nav-item {{ Request::is('student/teachers*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-chalkboard-teacher"></i> <span>معلّمو مرحلتي</span></div></div>
                </a>
                <a href="{{ route('student.support') }}" class="nav-item {{ Request::is('student/support*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-headset"></i> <span>الدعم الفني</span></div></div>
                </a>

                <span class="group-label">أدوات التفوق الوزاري 🇵🇸</span>
                <a href="{{ route('student.planner.index') }}" class="nav-item {{ Request::is('student/study-planner*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-calendar-check" style="color: #0ea5e9;"></i> <span>جدول تنظيم المراجعة</span></div></div>
                </a>
                <a href="{{ route('student.leaderboard') }}" class="nav-item {{ Request::is('student/leaderboard*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-fire" style="color: #f97316;"></i> <span>لوحة شرف الأيام المتتالية</span></div></div>
                </a>
                <a href="{{ route('student.achievements') }}" class="nav-item {{ Request::is('student/achievements*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-medal" style="color: #d4af37;"></i> <span>أوسمتي والشهادات الملكية 🏆</span></div></div>
                </a>
                <a href="{{ route('tawjihi.calculator') }}" target="_blank" class="nav-item">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-calculator" style="color: #10b981;"></i> <span>حاسبة المعدل والقبول</span></div></div>
                </a>
                <a href="{{ route('tawjihi.formulas') }}" target="_blank" class="nav-item">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-square-root-variable" style="color: #8b5cf6;"></i> <span>دليل القوانين الذهبية</span></div></div>
                </a>
            @endif
        </div>

        <div style="padding: 18px 20px; border-top: 1px solid var(--border-color); background: #f8fafc;">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; font-weight:600; font-size:0.88rem; display:flex; align-items:center; gap:10px; width:100%;">
                    <i class="fa-solid fa-power-off"></i> تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-bar">
            <div style="display: flex; align-items: center; gap: 15px;">
                <button class="mobile-toggle" id="btnToggleSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="date-info" style="color: var(--text-muted); font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-calendar-day" style="color: var(--primary-color);"></i> {{ date('Y/m/d') }}
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:15px;">
                @php
                    $unreadCount = 0; 
                    $unreadItems = collect();

                    try {
                        $isStudent = auth('student')->check();

                        if($isStudent) {
                            $sId = auth('student')->id();
                            $studentUser = auth('student')->user();
                            $dbNotifs = $studentUser ? $studentUser->unreadNotifications()->count() : 0;
                            $msgNotifs = \App\Models\Message::where('student_id', $sId)->where('sender_type', '!=', 'student')->where('is_read', \Illuminate\Support\Facades\DB::raw('false'))->count();
                            $unreadCount = $dbNotifs + $msgNotifs;
                            $unreadItems = \App\Models\Message::where('student_id', $sId)->where('sender_type', '!=', 'student')->where('is_read', \Illuminate\Support\Facades\DB::raw('false'))->latest()->take(5)->get();
                        } elseif(auth()->check() && auth()->user()->role === 'teacher') {
                            $tId = auth()->id();
                            $unreadCount = \App\Models\Message::where('teacher_id', $tId)->where('sender_type', 'student')->where('is_read', \Illuminate\Support\Facades\DB::raw('false'))->count();
                            $unreadItems = \App\Models\Message::where('teacher_id', $tId)->where('sender_type', 'student')->where('is_read', \Illuminate\Support\Facades\DB::raw('false'))->latest()->take(5)->get();
                        } elseif(auth()->check() && auth()->user()->role === 'admin') {
                            $unreadCount = \App\Models\Message::whereNull('teacher_id')->where('sender_type', 'student')->where('is_read', \Illuminate\Support\Facades\DB::raw('false'))->count();
                            $unreadItems = \App\Models\Message::whereNull('teacher_id')->where('sender_type', 'student')->where('is_read', \Illuminate\Support\Facades\DB::raw('false'))->latest()->take(5)->get();
                        }
                    } catch (\Throwable $e) {
                        $unreadCount = 0;
                        $unreadItems = collect();
                    }
                @endphp

                <div class="notifications-dropdown-container" style="position: relative;">
                    <button id="notificationsToggle" style="background: #f8fafc; border: 1px solid var(--border-color); width: 42px; height: 42px; border-radius: 12px; cursor: pointer; position: relative; display: grid; place-items: center; transition: 0.2s;">
                        <i class="fa-solid fa-bell" style="font-size: 1.15rem; color: #475569;"></i>
                        <span id="navUnreadBadge" style="{{ $unreadCount > 0 ? '' : 'display: none;' }} position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; font-size: 0.65rem; padding: 2px 7px; border-radius: 10px; border: 2px solid white; font-weight: 800; animation: pulse 2s infinite;">{{ $unreadCount }}</span>
                    </button>
                    
                    <div id="notificationsMenu" style="display: none; position: absolute; left: 0; top: 52px; width: 340px; background: white; border-radius: 18px; box-shadow: 0 15px 35px rgba(0,0,0,0.12); border: 1px solid var(--border-color); z-index: 1000; overflow: hidden; animation: fadeIn 0.2s ease;">
                        <div style="padding: 14px 18px; background: #f8fafc; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-bell" style="color: var(--primary-color);"></i> التنبيهات
                            </span>
                            @if($isStudent)
                                <button onclick="markAllReadFromNav()" style="background: none; border: none; font-size: 0.75rem; color: var(--primary-color); font-weight: 700; cursor: pointer;">
                                    تحديد الكل كمقروء
                                </button>
                            @endif
                        </div>

                        <div style="max-height: 320px; overflow-y: auto;" id="navNotificationsList">
                            @forelse($unreadItems as $item)
                                @php
                                    $link = '#';
                                    if ($isStudent) {
                                        $link = $item->sender_type === 'teacher' ? route('student.chat.teacher', $item->teacher_id ?? 1) : route('student.support');
                                    } elseif (auth()->check() && auth()->user()->role === 'teacher') {
                                        $link = route('teacher.messages.index');
                                    } else {
                                        $link = route('admin.messages.index');
                                    }
                                @endphp
                                <a href="{{ $link }}" style="display: flex; gap: 12px; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; text-decoration: none; color: inherit; transition: 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: grid; place-items: center; flex-shrink: 0; font-size: 0.9rem;">
                                        <i class="fa-solid fa-comment-dots"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-size: 0.82rem; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $item->message }}
                                        </div>
                                        <span style="font-size: 0.72rem; color: #94a3b8;">{{ $item->created_at ? $item->created_at->diffForHumans() : 'الآن' }}</span>
                                    </div>
                                </a>
                            @empty
                                <div style="padding: 30px 20px; text-align: center; color: #94a3b8;">
                                    <i class="fa-solid fa-check-circle" style="font-size: 1.8rem; margin-bottom: 6px; display: block; opacity: 0.4;"></i>
                                    <span style="font-size: 0.85rem; font-weight: 600;">لا توجد إشعارات غير مقروءة</span>
                                </div>
                            @endforelse
                        </div>

                        @if($isStudent)
                            <div style="padding: 10px; background: #f8fafc; border-top: 1px solid var(--border-color); text-align: center;">
                                <a href="{{ route('student.notifications.index') }}" style="font-size: 0.8rem; font-weight: 700; color: var(--primary-color); text-decoration: none;">
                                    عرض كافة التنبيهات والأرشيف ←
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <button id="themeToggleBtn" onclick="toggleTheme()" title="تبديل الوضع الليلي / النهاري" style="background: #f8fafc; border: 1px solid var(--border-color); width: 42px; height: 42px; border-radius: 12px; cursor: pointer; display: grid; place-items: center; transition: 0.2s; color: #475569;">
                    <i class="fa-solid fa-moon" id="themeIcon"></i>
                </button>

                <div style="display:flex; align-items:center; gap:10px; background: #f8fafc; padding: 5px 12px; border-radius: 12px; border: 1px solid var(--border-color);">
                    <span class="user-info-text" style="font-size: 0.85rem; font-weight: 600;">{{ auth()->user()->name ?? auth('student')->user()->name ?? 'مستخدم' }}</span>
                    <i class="fa-solid fa-user-circle" style="font-size: 1.8rem; color: var(--primary-color);"></i>
                </div>
            </div>
        </header>

        <div class="content-body">
            @yield('content')
        </div>
    </main>

    <!-- شريط التنقل السفلي الفاخر للهواتف الذكية (Mobile Bottom Navigation) -->
    <nav class="mobile-bottom-nav">
        @if(auth('student')->check())
            <a href="{{ route('student.dashboard') }}" class="bottom-nav-item {{ Request::is('student/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>الرئيسية</span>
            </a>
            <a href="{{ route('student.subjects.index') }}" class="bottom-nav-item {{ Request::is('student/subjects*') ? 'active' : '' }}">
                <i class="fa-solid fa-book-open"></i>
                <span>موادي</span>
            </a>
            <a href="{{ route('tawjihi.calculator') }}" class="bottom-nav-item {{ Request::is('tawjihi-calculator*') ? 'active' : '' }}">
                <i class="fa-solid fa-calculator"></i>
                <span>الحاسبة</span>
            </a>
            <a href="{{ route('student.notifications.index') }}" class="bottom-nav-item {{ Request::is('student/notifications*') ? 'active' : '' }}">
                <i class="fa-solid fa-bell"></i>
                <span>التنبيهات</span>
                @if(isset($unreadCount) && $unreadCount > 0)
                    <span class="bottom-nav-badge">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('student.profile') }}" class="bottom-nav-item {{ Request::is('student/profile*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-graduate"></i>
                <span>حسابي</span>
            </a>
        @elseif(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="bottom-nav-item {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>اللوحة</span>
            </a>
            <a href="{{ route('admin.subjects.pricing') }}" class="bottom-nav-item {{ Request::is('admin/subjects/pricing*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i>
                <span>الأسعار</span>
            </a>
            <a href="{{ route('admin.payments.index') }}" class="bottom-nav-item {{ Request::is('admin/payments*') ? 'active' : '' }}">
                <i class="fa-solid fa-wallet"></i>
                <span>المدفوعات</span>
            </a>
            <a href="{{ route('admin.teachers.chat') }}" class="bottom-nav-item {{ Request::is('admin/teachers/chat*') ? 'active' : '' }}">
                <i class="fa-solid fa-comments"></i>
                <span>المحادثات</span>
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
            <a href="{{ route('teacher.students.index') }}" class="bottom-nav-item {{ Request::is('teacher/students*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-check"></i>
                <span>طلابي</span>
            </a>
            <a href="{{ route('admin.teachers.chat') }}" class="bottom-nav-item {{ Request::is('admin/teachers/chat*') ? 'active' : '' }}">
                <i class="fa-solid fa-comments"></i>
                <span>الرسائل</span>
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
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>دخول</span>
            </a>
        @endif
    </nav>

    <script>
        // دالة التبديل للقوائم الفرعية
        function toggleSub(el) { 
            el.parentElement.classList.toggle('open'); 
        }

        // التعامل مع السايدبار في الجوال
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

        // التعامل مع الإشعارات
        const notifToggle = document.getElementById('notificationsToggle');
        const notifMenu = document.getElementById('notificationsMenu');
        if(notifToggle) {
            notifToggle.onclick = (e) => { 
                e.stopPropagation(); 
                notifMenu.style.display = notifMenu.style.display === 'block' ? 'none' : 'block'; 
            };
            window.addEventListener('click', () => {
                if(notifMenu) notifMenu.style.display = 'none';
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
                    list.innerHTML = '<div style="padding: 30px 20px; text-align: center; color: #94a3b8;"><i class="fa-solid fa-check-circle" style="font-size: 1.8rem; margin-bottom: 6px; display: block; color: #10b981;"></i><span style="font-size: 0.85rem; font-weight: 600;">تمت قراءة جميع الإشعارات بنجاح</span></div>';
                }
            });
        }

        // إدارة الوضع الليلي
        function toggleTheme() {
            const isDark = document.body.classList.toggle('dark-theme');
            localStorage.setItem('tawjihi-theme', isDark ? 'dark' : 'light');
            updateThemeIcon(isDark);
        }

        function updateThemeIcon(isDark) {
            const icon = document.getElementById('themeIcon');
            if (icon) {
                icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
        }

        (function initTheme() {
            const savedTheme = localStorage.getItem('tawjihi-theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-theme');
                updateThemeIcon(true);
            }
        })();

        // تسجيل Service Worker للعمل أوفلاين كتطبيق سطح مكتب PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(reg => {
                    console.log('تم تفعيل مشغل الأوفلاين PWA بنجاح:', reg.scope);
                }).catch(err => {
                    console.log('تعذر تفعيل Service Worker:', err);
                });
            });
        }
    </script>
</body>
</html>