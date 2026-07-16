<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>جسر | @yield('title')</title>
    <script src="{{ asset('js/crud.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 280px;
            --primary: #0f172a;    /* كحلي ملكي عميق */
            --accent: #10b981;     /* زمردي حيوي */
            --bg: #f8fafc;         /* خلفية فاتحة هادئة */
            --card: #ffffff;
            --text-main: #1e293b;
            --text-light: #64748b;
            --tatreez: #ef4444;    /* أحمر فلسطيني للإشارات */
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Readex Pro', sans-serif; }

        body { background: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; }

        /* --- الشريط الجانبي (Sidebar) --- */
        aside {
            width: var(--sidebar-width);
            background: var(--primary);
            color: white;
            position: fixed;
            top: 0; right: 0; bottom: 0;
            display: flex; flex-direction: column;
            padding: 30px 20px;
            z-index: 1000;
            box-shadow: -5px 0 25px rgba(0,0,0,0.1);
        }

        .logo-section { display: flex; align-items: center; gap: 12px; margin-bottom: 50px; padding: 0 10px; }
        .logo-box {
            width: 45px; height: 45px; background: var(--accent); color: white;
            display: grid; place-items: center; border-radius: 14px; font-weight: 700; font-size: 1.4rem;
        }
        .logo-name { font-size: 1.5rem; font-weight: 700; letter-spacing: 1px; }

        .nav-links { flex: 1; }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px; border-radius: 16px; color: #94a3b8;
            text-decoration: none; margin-bottom: 8px; transition: 0.3s;
            font-size: 0.95rem; font-weight: 500;
        }
        .nav-item:hover { background: rgba(255,255,255,0.05); color: white; }
        .nav-item.active { background: var(--accent); color: white; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2); }

        .user-section {
            border-top: 1px solid rgba(255,255,255,0.1); padding-top: 25px;
            display: flex; flex-direction: column; gap: 15px;
        }

        /* --- المحتوى الرئيسي (Main Content) --- */
        main {
            flex: 1;
            margin-right: var(--sidebar-width); /* حجز مساحة السايدبار */
            padding: 40px 60px;
            position: relative;
        }

        .top-bar { display: flex; justify-content: flex-end; margin-bottom: 40px; }

        /* --- العناصر الجمالية --- */
        .glass-card { background: var(--card); border-radius: 30px; border: 1px solid rgba(0,0,0,0.02); box-shadow: 0 15px 40px rgba(0,0,0,0.02); transition: 0.4s; }
        .glass-card:hover { transform: translateY(-8px); box-shadow: 0 25px 50px rgba(0,0,0,0.06); }

        .chip { padding: 6px 16px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
        .chip-emerald { background: #ecfdf5; color: #059669; }
        .btn { padding: 12px 24px; border-radius: 14px; border: none; cursor: pointer; font-weight: 600; transition: 0.3s; }
        .btn-primary { background: var(--accent); color: white; }

        @media (max-width: 1000px) {
            aside { width: 80px; padding: 20px 10px; }
            .logo-name, .nav-text, .user-name { display: none; }
            main { margin-right: 80px; padding: 20px; }
        }
    </style>
</head>
<body>

    <aside>
        <div class="logo-section">
            <div class="logo-box">ج</div>
            <span class="logo-name">جسر</span>
        </div>

        <nav class="nav-links">
            <a href="/" class="nav-item {{ Request::is('/') ? 'active' : '' }}">
                <span>🏠</span> <span class="nav-text">الرئيسية</span>
            </a>
            <a href="/stages" class="nav-item {{ Request::is('stages*') ? 'active' : '' }}">
                <span>📚</span> <span class="nav-text">المراحل الدراسية</span>
            </a>
            <a href="#" class="nav-item">
                <span>🤖</span> <span class="nav-text">المساعد الذكي</span>
            </a>
            <a href="#" class="nav-item">
                <span>📝</span> <span class="nav-text">اختباراتي</span>
            </a>
        </nav>

        <div class="user-section">
            <a href="#" class="nav-item">
                <span>👤</span> <span class="nav-text user-name">الملف الشخصي</span>
            </a>
            <button class="btn btn-primary nav-text">تسجيل خروج</button>
        </div>
    </aside>

    <main>
        <div class="top-bar">
            <div class="flex gap-16">
                <span style="color: var(--text-light); font-size: 0.9rem;">مرحباً بك، <strong>أحمد شمالي</strong></span>
                <div style="width: 40px; height: 40px; border-radius: 12px; background: #e2e8f0; display: grid; place-items: center;">👨‍🎓</div>
            </div>
        </div>

        @yield('content')
    </main>
    <script src="https://unpkg.com/axios@<x.x.x>/dist/axios.min.js"></script>

        <!-- مكان وضع الكود: قبل إغلاق وسام الـ </body> مباشرة -->

{{-- نظام التنبيهات الذكي --}}
@if(session('success'))
    <div id="global_toast" class="toast-success active">
        <div class="toast-content">
            <div class="toast-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <div class="toast-text">
                <span class="toast-title">عملية ناجحة</span>
                <p class="toast-msg">{{ session('success') }}</p>
            </div>
        </div>
        <div class="toast-progress"></div>
    </div>
@endif

<style>
    /* تنسيقات التنبيه الفخم */
    .toast-success {
        position: fixed;
        bottom: 30px;
        left: 30px; /* تظهر من جهة اليسار لأن السايدبار في اليمين */
        background: white;
        padding: 15px 25px;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        border-right: 6px solid var(--accent); /* لون الزمردي */
        z-index: 9999;
        display: none;
        animation: slideIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }
    .toast-success.active { display: block; }

    .toast-content { display: flex; align-items: center; gap: 15px; }

    .toast-icon {
        width: 35px; height: 35px; background: #ecfdf5; color: var(--accent);
        border-radius: 50%; display: grid; place-items: center;
    }

    .toast-title { display: block; font-weight: 800; font-size: 0.9rem; color: var(--primary); }
    .toast-msg { font-size: 0.8rem; color: var(--text-light); margin: 0; }

    .toast-progress {
        position: absolute; bottom: 0; right: 0; height: 4px;
        background: #d1fae5; width: 100%; border-radius: 0 0 20px 20px;
    }
    .toast-progress::after {
        content: ''; position: absolute; top: 0; right: 0; height: 100%;
        background: var(--accent); width: 100%;
        animation: toastProgress 4s linear forwards;
    }

    @keyframes slideIn {
        from { transform: translateX(-120%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes toastProgress { from { width: 100%; } to { width: 0%; } }

    /* للاختفاء */
    .toast-fade-out { opacity: 0; transform: translateY(20px); transition: 0.5s; }
</style>

<script>
    // كود إخفاء الرسالة تلقائياً بعد 4 ثواني
    const toast = document.getElementById('global_toast');
    if (toast) {
        setTimeout(() => {
            toast.classList.add('toast-fade-out');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }
</script>

</body>
</html>
