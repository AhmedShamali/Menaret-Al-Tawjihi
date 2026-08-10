<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ \App\Models\Setting::get('site_name', 'منصة جسر') }}</title>

    <!-- الخطوط والأيقونات -->
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --side-bg: #ffffff;
            --side-active: #f0f9ff;
            --side-hover: #f8fafc;
            --text-active: #0284c7;
            --bg-body: #f8fafc;
            --sidebar-width: 285px;
            --primary-color: #0284c7;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Alexandria', sans-serif; }
        body { background: var(--bg-body); min-height: 100vh; overflow-x: hidden; display: flex; color: var(--text-main); }

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
    </style>
</head>
<body class="{{ request()->is('login') || request()->is('register') ? 'no-sidebar' : '' }}">

    <!-- طبقة التظليل للجوال -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="side-brand">
            <a href="/" class="brand-logo">
                <div class="logo-square">{{ mb_substr(\App\Models\Setting::get('site_name', 'ج'), 0, 1) }}</div>
                <span>{{ \App\Models\Setting::get('site_name', 'منصة جسر') }}</span>
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
                <a href="{{ route('student.teachers.index') }}" class="nav-item {{ Request::is('student/teachers*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-chalkboard-teacher"></i> <span>معلّمو مرحلتي</span></div></div>
                </a>
                <a href="{{ route('student.support') }}" class="nav-item {{ Request::is('student/support*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-headset"></i> <span>الدعم الفني</span></div></div>
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
                    $unreadCount = 0; $unreadItems = collect();
                    if(auth('student')->check()) {
                        $unreadCount = \App\Models\Message::where('student_id', auth('student')->id())->whereNull('teacher_id')->where('sender_type', 'admin')->count();
                        $unreadItems = \App\Models\Message::where('student_id', auth('student')->id())->whereNull('teacher_id')->where('sender_type', 'admin')->latest()->take(5)->get();
                    } elseif(auth()->check() && auth()->user()->role === 'teacher') {
                        $unreadCount = \App\Models\Message::where('teacher_id', auth()->id())->where('sender_type', 'student')->count();
                        $unreadItems = \App\Models\Message::where('teacher_id', auth()->id())->where('sender_type', 'student')->latest()->take(5)->get();
                    } elseif(auth()->check() && auth()->user()->role === 'admin') {
                        $unreadCount = \App\Models\Message::whereNull('teacher_id')->where('sender_type', 'student')->count();
                        $unreadItems = \App\Models\Message::whereNull('teacher_id')->where('sender_type', 'student')->latest()->take(5)->get();
                    }
                @endphp

                <div class="notifications-dropdown-container" style="position: relative;">
                    <button id="notificationsToggle" style="background: #f8fafc; border: 1px solid var(--border-color); width: 40px; height: 40px; border-radius: 12px; cursor: pointer; position: relative;">
                        <i class="fa-solid fa-bell"></i>
                        @if($unreadCount > 0)
                            <span style="position: absolute; top: -4px; right: -4px; background: #ef4444; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 10px; border: 2px solid white;">{{ $unreadCount }}</span>
                        @endif
                    </button>
                    <div id="notificationsMenu" style="display: none; position: absolute; left: 0; top: 50px; width: 280px; background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 1px solid var(--border-color); z-index: 1000;">
                        <div style="padding: 12px; background: #f8fafc; border-bottom: 1px solid var(--border-color); font-weight: bold; border-radius: 12px 12px 0 0;">التنبيهات</div>
                        <div style="max-height: 300px; overflow-y: auto;">
                            @forelse($unreadItems as $item)
                                <a href="#" style="display: block; padding: 12px; border-bottom: 1px solid #eee; text-decoration: none; color: #333; font-size: 0.8rem;">{{ Str::limit($item->message, 50) }}</a>
                            @empty
                                <div style="padding: 20px; text-align: center; color: #999;">لا توجد إشعارات</div>
                            @endforelse
                        </div>
                    </div>
                </div>

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
            }
            window.addEventListener('click', () => {
                if(notifMenu) notifMenu.style.display = 'none';
            });
        }
    </script>
</body>
</html>