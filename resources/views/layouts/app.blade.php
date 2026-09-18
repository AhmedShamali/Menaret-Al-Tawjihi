<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
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
    <title>@yield('title', __('المنصة التعليمية')) | {{ \App\Models\Setting::get('site_name', __('منارة التوجيهي')) }} 🇵🇸</title>

    <!-- Google Fonts: Alexandria & Tajawal & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if (window.axios) {
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfMeta.getAttribute('content');
            }
        }
        try {
            localStorage.removeItem('tawjihi-theme');
            localStorage.removeItem('theme');
            document.documentElement.classList.remove('dark-theme');
        } catch(e) {}
    </script>
    
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
            --ed-primary-light: #3b82f6;
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

        /* --- دعم اللغة الإنجليزية واتجاه من اليسار لليمين (LTR Support) --- */
        html[dir="ltr"] aside.sidebar {
            right: auto;
            left: 0;
            border-left: none;
            border-right: 1px solid var(--ed-border);
        }
        html[dir="ltr"] main.main-content {
            margin-right: 0;
            margin-left: var(--sidebar-width);
        }
        @media (max-width: 1024px) {
            html[dir="ltr"] aside.sidebar {
                transform: translateX(-105%);
            }
            html[dir="ltr"] aside.sidebar.mobile-active {
                transform: translateX(0);
            }
            html[dir="ltr"] main.main-content {
                margin-left: 0;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Alexandria', 'Tajawal', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        html {
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
        }

        body {
            background-color: var(--ed-bg);
            color: var(--ed-text-body);
            min-height: 100vh;
            width: 100%;
            max-width: 100vw;
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
            padding: 0 16px;
            display: flex;
            align-items: center;
            border-bottom: 3px solid #f59e0b;
            background: linear-gradient(135deg, #172554 0%, #1e3a8a 100%);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 1.05rem;
        }

        .logo-square {
            width: 38px;
            height: 38px;
            background: #ffffff;
            color: #1e3a8a;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            display: grid;
            place-items: center;
            font-size: 1.15rem;
            font-weight: 800;
            flex-shrink: 0;
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
            max-width: calc(100% - var(--sidebar-width));
            min-width: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition-smooth);
            overflow-x: hidden;
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
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
            overflow-x: hidden;
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
                max-width: 100%;
                min-width: 0;
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

        @media (max-width: 640px) {
            .supervisor-top-tag {
                display: none !important;
            }
        }

        /* ====================================================================
           نظام التصميم الأكاديمي الملكي الكلاسيكي الموحد لجميع جداول وواجهات المنصة
           Master Classic Royal Academic Design System (Navy/Gold/Clean)
           ==================================================================== */
        
        /* 1. الجداول الكلاسيكية الموحدة (Universal Royal Classic Tables) */
        .data-table-clean, 
        .classic-table, 
        table.data-table, 
        table.table-custom, 
        table.payroll-table, 
        table.ed-custom-table,
        table.payments-table,
        table.clean-matrix-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
            box-sizing: border-box;
        }
        html[dir="ltr"] .data-table-clean,
        html[dir="ltr"] .classic-table,
        html[dir="ltr"] table.data-table,
        html[dir="ltr"] table.table-custom,
        html[dir="ltr"] table.payroll-table,
        html[dir="ltr"] table.ed-custom-table,
        html[dir="ltr"] table.payments-table,
        html[dir="ltr"] table.clean-matrix-table {
            text-align: left;
        }

        .data-table-clean th,
        .classic-table th,
        table.data-table th,
        table.table-custom th,
        table.payroll-table th,
        table.ed-custom-table th,
        table.payments-table th,
        table.clean-matrix-table th {
            background: linear-gradient(135deg, #172554 0%, #1e3a8a 100%) !important;
            color: #ffffff !important;
            font-size: 0.82rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.3px !important;
            padding: 12px 14px !important;
            border-bottom: 2.5px solid #d97706 !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
        }

        .data-table-clean th a,
        .classic-table th a,
        table.data-table th a,
        table.payroll-table th a,
        table.ed-custom-table th a {
            color: #ffffff !important;
            text-decoration: none;
        }

        .data-table-clean td,
        .classic-table td,
        table.data-table td,
        table.table-custom td,
        table.payroll-table td,
        table.ed-custom-table td,
        table.payments-table td,
        table.clean-matrix-table td {
            padding: 11px 14px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            vertical-align: middle !important;
            font-size: 0.86rem !important;
            color: #1e293b !important;
            background: transparent;
        }

        .data-table-clean tbody tr:hover,
        .classic-table tbody tr:hover,
        table.data-table tbody tr:hover,
        table.table-custom tbody tr:hover,
        table.payroll-table tbody tr:hover,
        table.ed-custom-table tbody tr:hover,
        table.payments-table tbody tr:hover,
        table.clean-matrix-table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        .table-card-clean {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            width: 100%;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            margin-bottom: 20px;
        }

        .table-container-clean {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* 2. مؤشرات الأرقام والبطاقات الكلاسيكية (Classic Metric KPI Cards) */
        .stats-row-clean {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
            width: 100%;
        }

        @media (max-width: 960px) {
            .stats-row-clean {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 480px) {
            .stats-row-clean {
                grid-template-columns: 1fr;
            }
        }

        .stat-card-clean {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-top: 3.5px solid var(--card-accent, #1e3a8a);
            border-radius: 10px;
            padding: 16px 18px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-card-clean:hover {
            border-color: #94a3b8;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
        }

        .stat-card-clean .stat-label {
            font-size: 0.8rem;
            color: #475569;
            font-weight: 700;
            display: block;
            margin-bottom: 6px;
        }

        .stat-card-clean .stat-value-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-card-clean .stat-number {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .stat-card-clean .stat-icon {
            font-size: 1.15rem;
        }

        /* فئات الألوان الكلاسيكية */
        .text-navy { color: #1e3a8a !important; }
        .text-emerald { color: #059669 !important; }
        .text-amber { color: #d97706 !important; }
        .text-indigo { color: #6366f1 !important; }
        .text-rose { color: #dc2626 !important; }

        /* 3. شريط البحث والفلاتر النظيف (Clean Search & Filter Toolbar) */
        .toolbar-clean {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .search-box-clean {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .search-box-clean .search-icon {
            position: absolute;
            right: 12px;
            color: #94a3b8;
            font-size: 0.85rem;
            pointer-events: none;
        }
        html[dir="ltr"] .search-box-clean .search-icon {
            right: auto;
            left: 12px;
        }

        .search-box-clean input {
            width: 100%;
            padding: 8px 36px 8px 32px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-size: 0.85rem;
            color: #0f172a;
            outline: none;
            transition: all 0.15s;
            box-sizing: border-box;
        }
        html[dir="ltr"] .search-box-clean input {
            padding: 8px 32px 8px 36px;
        }

        .search-box-clean input:focus {
            background: #ffffff;
            border-color: #94a3b8;
        }

        .search-box-clean .clear-search {
            position: absolute;
            left: 10px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 2px;
        }
        html[dir="ltr"] .search-box-clean .clear-search {
            left: auto;
            right: 10px;
        }

        .filter-pills-clean {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .filter-pill {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .filter-pill:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .filter-pill.active {
            background: #1e3a8a;
            color: #ffffff;
            border-color: #1e3a8a;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2);
        }

        /* 4. كبسولات الحالة النظيفة (Status Dot Pills) */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.74rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-pill .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-active, .status-completed, .status-approved {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .status-active .dot, .status-completed .dot, .status-approved .dot { background: #16a34a; }

        .status-pending, .status-waiting {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .status-pending .dot, .status-waiting .dot { background: #d97706; }

        .status-frozen, .status-rejected, .status-cancelled {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .status-frozen .dot, .status-rejected .dot, .status-cancelled .dot { background: #dc2626; }

        .status-info, .status-review {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .status-info .dot, .status-review .dot { background: #2563eb; }

        /* 5. الأزرار الموحدة (Unified Action Buttons) */
        .btn-clean {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            line-height: 1.4;
        }

        .btn-clean.btn-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #172554 100%);
            color: #ffffff !important;
            border: 1px solid #1e3a8a;
            box-shadow: 0 2px 6px rgba(30, 58, 138, 0.25);
        }
        .btn-clean.btn-primary:hover {
            background: #1e3a8a;
            transform: translateY(-1px);
            color: #ffffff !important;
        }

        .btn-clean.btn-outline {
            background: #ffffff;
            color: #334155 !important;
            border-color: #e2e8f0;
        }
        .btn-clean.btn-outline:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a !important;
        }

        .btn-clean.btn-danger-outline {
            background: #ffffff;
            color: #dc2626 !important;
            border-color: #fecaca;
        }
        .btn-clean.btn-danger-outline:hover {
            background: #fef2f2;
            border-color: #fca5a5;
        }

        .tbl-btn-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #64748b !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
        }

        .tbl-btn-icon:hover {
            background: #f8fafc;
            color: #0f172a !important;
            border-color: #cbd5e1;
        }

        .tbl-btn-icon.tbl-btn-del:hover {
            background: #fef2f2;
            color: #dc2626 !important;
            border-color: #fecaca;
        }

        .tbl-btn {
            background: #0f172a;
            color: #ffffff !important;
            border: none;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.74rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .tbl-btn:hover {
            background: #1e293b;
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
                    <div class="logo-square"><i class="fa-solid fa-graduation-cap"></i></div>
                @endif
                <div style="display: flex; flex-direction: column;">
                    <span style="font-weight: 800; font-size: 0.98rem; color: #ffffff; line-height: 1.2;">{{ \App\Models\Setting::get('site_name', __('منارة التوجيهي')) }} 🇵🇸</span>
                    <small style="font-size: 0.68rem; color: #fef08a; font-weight: 700;">{{ __('بوابة الثانوية العامة') }}</small>
                </div>
            </a>
        </div>

        <div class="menu-wrapper">
            @if(auth()->check() && auth()->user()->role === 'admin')
                <span class="group-label">{{ __('الإدارة العامة') }}</span>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-chart-pie"></i> <span>{{ __('لوحة الإحصائيات') }}</span></div></div>
                </a>

                <div class="nav-item has-sub {{ Request::is('admin/students*') || Request::is('admin/teachers*') ? 'open' : '' }}">
                    <div class="nav-link" onclick="toggleSub(this)">
                        <div class="link-main"><i class="fa-solid fa-user-gear"></i> <span>{{ __('القبول والكادر') }}</span></div>
                        <i class="fa-solid fa-chevron-left nav-arrow"></i>
                    </div>
                    <ul class="submenu">
                        <li><a href="{{ route('admin.students.index') }}" class="submenu-item">{{ __('إدارة الطلاب') }}</a></li>
                        <li><a href="{{ route('admin.teachers.index') }}" class="submenu-item">{{ __('إدارة المعلمين') }}</a></li>
                        <li><a href="{{ route('admin.teachers.salaries') }}" class="submenu-item"><i class="fa-solid fa-money-bill-wave" style="font-size: 0.8rem; margin-left: 6px; color: #059669;"></i> {{ __('رواتب ومستحقات المعلمين') }}</a></li>
                        <li><a href="{{ route('admin.teachers.create') }}" class="submenu-item">{{ __('إضافة معلم جديد') }}</a></li>
                        <li><a href="{{ route('admin.teachers.info') }}" class="submenu-item">{{ __('دليل المعلمين الموسع') }}</a></li>
                        <li><a href="{{ route('admin.students.profile_all') }}" class="submenu-item">{{ __('سجل الطلاب الكامل') }}</a></li>
                    </ul>
                </div>

                <a href="{{ route('admin.certificates.index') }}" class="nav-item {{ Request::is('admin/certificates*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-graduation-cap" style="color: #4f46e5;"></i> <span>{{ __('الشهادات والنتائج') }}</span></div></div>
                </a>

                <a href="{{ route('admin.subjects.pricing') }}" class="nav-item {{ Request::is('admin/subjects/pricing*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-tags" style="color: #059669;"></i> <span>{{ __('تسعير المواد') }}</span></div></div>
                </a>

                <div class="nav-item has-sub {{ Request::is('admin/payments*') || Request::is('admin/subscriptions*') ? 'open' : '' }}">
                    <div class="nav-link" onclick="toggleSub(this)">
                        <div class="link-main"><i class="fa-solid fa-wallet" style="color: #d97706;"></i> <span>{{ __('الاشتراكات والمدفوعات') }}</span></div>
                        <i class="fa-solid fa-chevron-left nav-arrow"></i>
                    </div>
                    <ul class="submenu">
                        <li><a href="{{ route('admin.subscriptions.monthly') }}" class="submenu-item"><i class="fa-solid fa-calendar-days" style="font-size: 0.8rem; margin-left: 6px; color: #0284c7;"></i> {{ __('مصفوفة اشتراكات الطلاب (12 شهراً)') }}</a></li>
                        <li><a href="{{ route('admin.payments.index') }}" class="submenu-item"><i class="fa-solid fa-receipt" style="font-size: 0.8rem; margin-left: 6px; color: #059669;"></i> {{ __('إشعارات الدفع والتحويلات') }}</a></li>
                    </ul>
                </div>

                <span class="group-label">{{ __('التواصل والدعم') }}</span>
                <a href="{{ route('admin.inquiries.index') }}" class="nav-item {{ Request::is('admin/academic-inquiries*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-clipboard-question" style="color: #2563eb;"></i> <span>{{ __('الاستفسار الأكاديمي والشكاوى') }}</span></div></div>
                </a>
                <a href="{{ route('admin.messages.index') }}" class="nav-item {{ Request::is('admin/inbox*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-comments"></i> <span>{{ __('رسائل الطلاب') }}</span></div></div>
                </a>
                <a href="{{ route('admin.teachers.chat') }}" class="nav-item {{ Request::is('admin/teachers/chat*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-chalkboard-user"></i> <span>{{ __('مراسلة المعلمين') }}</span></div></div>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="nav-item {{ Request::is('admin/settings*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-gear"></i> <span>{{ __('إعدادات النظام') }}</span></div></div>
                </a>
            @endif

            @if(auth()->check() && auth()->user()->role === 'teacher')
                <span class="group-label">{{ __('بوابة المعلم') }}</span>
                <a href="{{ route('teacher.dashboard') }}" class="nav-item {{ Request::is('teacher/dashboard*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-house"></i> <span>{{ __('لوحة التحكم') }}</span></div></div>
                </a>

                <div class="nav-item has-sub {{ Request::is('teacher/exams*') || Request::is('teacher/submissions*') ? 'open' : '' }}">
                    <div class="nav-link" onclick="toggleSub(this)">
                        <div class="link-main"><i class="fa-solid fa-file-pen"></i> <span>{{ __('إدارة الاختبارات') }}</span></div>
                        <i class="fa-solid fa-chevron-left nav-arrow"></i>
                    </div>
                    <ul class="submenu">
                        <li><a href="{{ route('teacher.exams.index') }}" class="submenu-item">{{ __('قائمة الاختبارات') }}</a></li>
                        <li><a href="{{ route('teacher.exams.create') }}" class="submenu-item">{{ __('بناء اختبار جديد') }}</a></li>
                        <li><a href="{{ route('teacher.submissions.index') }}" class="submenu-item">{{ __('رصد درجات الطلاب') }}</a></li>
                    </ul>
                </div>

                <div class="nav-item has-sub {{ Request::is('teacher/videos*') || Request::is('teacher/files*') || Request::is('teacher/visibility*') || Request::is('teacher/educational_contents*') ? 'open' : '' }}">
                    <div class="nav-link" onclick="toggleSub(this)">
                        <div class="link-main"><i class="fa-solid fa-photo-film"></i> <span>{{ __('إدارة المحتوى الأكاديمي') }}</span></div>
                        <i class="fa-solid fa-chevron-left nav-arrow"></i>
                    </div>
                    <ul class="submenu">
                        <li><a href="{{ route('teacher.videos') }}" class="submenu-item"><i class="fa-solid fa-video" style="font-size: 0.8rem; margin-left: 6px; color: #1d4ed8;"></i> {{ __('رفع وإدارة الفيديوهات') }}</a></li>
                        <li><a href="{{ route('teacher.files') }}" class="submenu-item"><i class="fa-solid fa-file-pdf" style="font-size: 0.8rem; margin-left: 6px; color: #dc2626;"></i> {{ __('رفع الملازم والملفات') }}</a></li>
                        <li><a href="{{ route('teacher.visibility') }}" class="submenu-item"><i class="fa-solid fa-toggle-on" style="font-size: 0.8rem; margin-left: 6px; color: #059669;"></i> {{ __('التحكم بظهور المحتوى') }}</a></li>
                    </ul>
                </div>

                <a href="{{ route('teacher.access.index') }}" class="nav-item {{ Request::is('teacher/access*') || Request::is('teacher/students*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-user-check"></i> <span>{{ __('اشتراكات وصلاحيات الطلاب') }}</span></div></div>
                </a>

                <a href="{{ route('teacher.messages.index') }}" class="nav-item {{ Request::is('teacher/inbox*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-comments"></i> <span>{{ __('رسائل الطلاب') }}</span></div></div>
                </a>

                <a href="{{ route('teacher.salaries.index') }}" class="nav-item {{ Request::is('teacher/salaries*') || Request::is('teacher/salary*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-file-invoice-dollar" style="color: #10b981;"></i> <span>{{ __('مسير الرواتب والمستحقات') }}</span></div></div>
                </a>

                <a href="{{ route('teacher.admin.chat') }}" class="nav-item {{ Request::is('teacher/admin/chat*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-shield-halved"></i> <span>{{ __('مراسلة الإدارة') }}</span></div></div>
                </a>
            @endif

            @if(auth('student')->check() || (auth()->check() && auth()->user()->role === 'student'))
                <span class="group-label">{{ __('المساحة التعليمية') }}</span>
                <a href="{{ route('student.dashboard') }}" class="nav-item {{ Request::is('student/dashboard*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-house-chimney"></i> <span>{{ __('الرئيسية') }}</span></div></div>
                </a>
                <a href="{{ route('student.subjects.index') }}" class="nav-item {{ request()->routeIs('student.subjects.*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-book-open"></i> <span>{{ __('المواد والدروس') }}</span></div></div>
                </a>
                <a href="{{ route('student.subscriptions.index') }}" class="nav-item {{ Request::is('student/subscriptions*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-calendar-days" style="color: #059669;"></i> <span>{{ __('سجل اشتراكاتي الشهرية (12 شهراً)') }}</span></div></div>
                </a>
                <a href="{{ route('student.exams.index') }}" class="nav-item {{ Request::is('student/my-exams*') || Request::is('student/exams*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-pen-ruler"></i> <span>{{ __('اختباراتي') }}</span></div></div>
                </a>
                <a href="{{ route('student.courses.catalog') }}" class="nav-item {{ Request::is('student/courses/catalog*') || Request::is('student/checkout*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-layer-group"></i> <span>{{ __('باقات المواد والاشتراك') }}</span></div></div>
                </a>
                <a href="{{ route('student.notifications.index') }}" class="nav-item {{ Request::is('student/notifications*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-bell"></i> <span>{{ __('مركز التنبيهات') }}</span></div></div>
                </a>

                <span class="group-label">{{ __('أدوات التفوق الدراسي') }}</span>
                <a href="{{ route('student.planner.index') }}" class="nav-item {{ Request::is('student/study-planner*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-calendar-check" style="color: #0284c7;"></i> <span>{{ __('جدول المراجعة') }}</span></div></div>
                </a>
                <a href="{{ route('student.achievements') }}" class="nav-item {{ Request::is('student/achievements*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-award" style="color: #059669;"></i> <span>{{ __('الشهادات والإنجازات') }}</span></div></div>
                </a>
                <a href="{{ route('student.flashcards.index') }}" class="nav-item {{ Request::is('student/flashcards*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-bolt" style="color: #a21caf;"></i> <span>{{ __('بطاقات الاستذكار السريع') }}</span></div></div>
                </a>
                <a href="{{ route('student.leaderboard') }}" class="nav-item {{ Request::is('student/leaderboard*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-trophy" style="color: #ea580c;"></i> <span>{{ __('لوحة الشرف وتحدي الأوائل') }}</span></div></div>
                </a>
                <a href="{{ route('tawjihi.calculator') }}" target="_blank" class="nav-item">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-calculator" style="color: #1d4ed8;"></i> <span>{{ __('حاسبة المعدل الجامعي') }}</span></div></div>
                </a>
                <a href="{{ route('tawjihi.formulas') }}" target="_blank" class="nav-item">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-square-root-variable" style="color: #7c3aed;"></i> <span>{{ __('دليل القوانين الوزارية') }}</span></div></div>
                </a>

                <span class="group-label">{{ __('الحساب والتواصل') }}</span>
                <a href="{{ route('student.teachers.index') }}" class="nav-item {{ Request::is('student/teachers*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-chalkboard-user"></i> <span>{{ __('معلمو مرحلتي') }}</span></div></div>
                </a>
                <a href="{{ route('student.support') }}" class="nav-item {{ Request::is('student/support*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-headset"></i> <span>{{ __('المساعدة والدعم') }}</span></div></div>
                </a>
                <a href="{{ route('student.profile') }}" class="nav-item {{ Request::is('student/profile*') ? 'active' : '' }}">
                    <div class="nav-link"><div class="link-main"><i class="fa-solid fa-user-gear"></i> <span>{{ __('الملف الشخصي') }}</span></div></div>
                </a>
            @endif
        </div>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" style="background:none; border:none; color: var(--ed-danger); cursor:pointer; font-weight:600; font-size:0.86rem; display:flex; align-items:center; gap:10px; width:100%; padding: 6px 8px; border-radius: var(--ed-radius-sm); transition: var(--transition-smooth);" onmouseover="this.style.background='var(--ed-danger-soft)'" onmouseout="this.style.background='none'">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> {{ __('تسجيل الخروج') }}
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
                <div class="date-info" style="color: var(--ed-text-muted); font-weight: 600; font-size: 0.84rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-regular fa-calendar-check" style="color: var(--ed-primary);"></i> {{ date('Y/m/d') }} م
                </div>
                <div class="supervisor-top-tag" style="display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; padding: 4px 12px; border-radius: 6px; font-size: 0.78rem; font-weight: 700;">
                    <i class="fa-solid fa-user-tie"></i> {{ __('المشرف العام: أ. أحمد حسين شمالي') }}
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:10px;">
                <!-- زر تبديل اللغة (عربي / English) -->
                @php $currentLocale = app()->getLocale(); @endphp
                <a href="{{ route('lang.switch', $currentLocale === 'ar' ? 'en' : 'ar') }}" 
                   title="{{ $currentLocale === 'ar' ? 'Switch to English' : 'التبديل إلى العربية' }}" 
                   style="background: var(--ed-surface); border: 1px solid var(--ed-border); color: var(--ed-text-main); height: 38px; padding: 0 12px; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; font-size: 0.82rem; font-weight: 700; transition: var(--transition-smooth);">
                    <i class="fa-solid fa-globe" style="color: var(--ed-primary); font-size: 0.95rem;"></i>
                    <span>{{ $currentLocale === 'ar' ? 'EN' : 'عربي' }}</span>
                </a>
                @php
                    $unreadCount = 0; 
                    $unreadItems = collect();

                    try {
                        $isStudent = auth('student')->check();
                        $isWeb = auth('web')->check();

                        if ($isStudent) {
                            $studentUser = auth('student')->user();
                            $sId = $studentUser->id;

                            // 1. إشعارات النظام وقاعدة البيانات للطالب
                            $dbNotifs = $studentUser->unreadNotifications()->latest()->take(6)->get()->map(function($n) {
                                $d = is_array($n->data) ? $n->data : json_decode($n->data, true) ?? [];
                                return (object)[
                                    'id'      => $n->id,
                                    'title'   => $d['title'] ?? 'تنبيه أكاديمي',
                                    'message' => $d['message'] ?? '',
                                    'icon'    => $d['icon'] ?? 'fa-bell',
                                    'url'     => $d['action_url'] ?? $d['url'] ?? route('student.dashboard'),
                                    'time'    => $n->created_at ? $n->created_at->diffForHumans() : 'الآن',
                                ];
                            });

                            // 2. رسائل المحادثة غير المقروءة للطالب
                            $msgNotifs = \App\Models\Message::where('student_id', $sId)
                                ->where('sender_type', '!=', 'student')
                                ->where('is_read', false)
                                ->latest()
                                ->take(4)
                                ->get()
                                ->map(function($m) {
                                    return (object)[
                                        'id'      => 'msg_' . $m->id,
                                        'title'   => ($m->sender_type === 'teacher') ? 'رسالة من معلم المادة 💬' : 'تنبيه من الدعم الفني 🎧',
                                        'message' => $m->message,
                                        'icon'    => 'fa-comments',
                                        'url'     => ($m->sender_type === 'teacher') ? route('student.teachers.chat', $m->teacher_id ?? 1) : route('student.support'),
                                        'time'    => $m->created_at ? $m->created_at->diffForHumans() : 'الآن',
                                    ];
                                });

                            $unreadCount = $studentUser->unreadNotifications()->count() + \App\Models\Message::where('student_id', $sId)->where('sender_type', '!=', 'student')->where('is_read', false)->count();
                            $unreadItems = $dbNotifs->concat($msgNotifs)->take(8);

                        } elseif ($isWeb && auth()->user()->role === 'admin') {
                            $adminUser = auth()->user();

                            // 1. إشعارات النظام والعمليات للمدير
                            $dbNotifs = $adminUser->unreadNotifications()->latest()->take(6)->get()->map(function($n) {
                                $d = is_array($n->data) ? $n->data : json_decode($n->data, true) ?? [];
                                return (object)[
                                    'id'      => $n->id,
                                    'title'   => $d['title'] ?? 'تنبيه إداري',
                                    'message' => $d['message'] ?? '',
                                    'icon'    => $d['icon'] ?? 'fa-shield-halved',
                                    'url'     => $d['action_url'] ?? $d['url'] ?? route('admin.dashboard'),
                                    'time'    => $n->created_at ? $n->created_at->diffForHumans() : 'الآن',
                                ];
                            });

                            // 2. رسائل الطلاب وتذاكر الدعم غير المقروءة للمدير
                            $msgNotifs = \App\Models\Message::whereNull('teacher_id')
                                ->where('sender_type', 'student')
                                ->where('is_read', false)
                                ->latest()
                                ->take(4)
                                ->get()
                                ->map(function($m) {
                                    return (object)[
                                        'id'      => 'msg_' . $m->id,
                                        'title'   => 'تذكرة / رسالة جديدة من طالب 💬',
                                        'message' => $m->message,
                                        'icon'    => 'fa-comment-dots',
                                        'url'     => route('admin.messages.index'),
                                        'time'    => $m->created_at ? $m->created_at->diffForHumans() : 'الآن',
                                    ];
                                });

                            $unreadCount = $adminUser->unreadNotifications()->count() + \App\Models\Message::whereNull('teacher_id')->where('sender_type', 'student')->where('is_read', false)->count();
                            $unreadItems = $dbNotifs->concat($msgNotifs)->take(8);

                        } elseif ($isWeb && auth()->user()->role === 'teacher') {
                            $teacherUser = auth()->user();

                            // 1. إشعارات النظام الأكاديمية للمعلم
                            $dbNotifs = $teacherUser->unreadNotifications()->latest()->take(6)->get()->map(function($n) {
                                $d = is_array($n->data) ? $n->data : json_decode($n->data, true) ?? [];
                                return (object)[
                                    'id'      => $n->id,
                                    'title'   => $d['title'] ?? 'تنبيه أكاديمي',
                                    'message' => $d['message'] ?? '',
                                    'icon'    => $d['icon'] ?? 'fa-chalkboard-teacher',
                                    'url'     => $d['action_url'] ?? $d['url'] ?? route('teacher.dashboard'),
                                    'time'    => $n->created_at ? $n->created_at->diffForHumans() : 'الآن',
                                ];
                            });

                            // 2. استفسارات الطلاب لمعلم المادة
                            $msgNotifs = \App\Models\Message::where('teacher_id', $teacherUser->id)
                                ->where('sender_type', 'student')
                                ->where('is_read', false)
                                ->latest()
                                ->take(4)
                                ->get()
                                ->map(function($m) {
                                    return (object)[
                                        'id'      => 'msg_' . $m->id,
                                        'title'   => 'استفسار دراسي من طالب 💬',
                                        'message' => $m->message,
                                        'icon'    => 'fa-comments',
                                        'url'     => route('teacher.messages.index'),
                                        'time'    => $m->created_at ? $m->created_at->diffForHumans() : 'الآن',
                                    ];
                                });

                            $unreadCount = $teacherUser->unreadNotifications()->count() + \App\Models\Message::where('teacher_id', $teacherUser->id)->where('sender_type', 'student')->where('is_read', false)->count();
                            $unreadItems = $dbNotifs->concat($msgNotifs)->take(8);
                        }
                    } catch (\Throwable $e) {
                        $unreadCount = 0;
                        $unreadItems = collect();
                    }
                @endphp

                <!-- قائمة الإشعارات والتنبيهات الشاملة -->
                <div class="notifications-dropdown-container" style="position: relative;">
                    <button id="notificationsToggle" style="background: var(--ed-surface); border: 1px solid var(--ed-border); width: 40px; height: 40px; border-radius: 10px; cursor: pointer; position: relative; display: grid; place-items: center; transition: var(--transition-smooth); color: var(--ed-text-body);">
                        <i class="fa-regular fa-bell" style="font-size: 1.1rem;"></i>
                        <span id="navUnreadBadge" style="{{ $unreadCount > 0 ? '' : 'display: none;' }} position: absolute; top: -3px; right: -3px; background: var(--ed-danger); color: white; font-size: 0.62rem; padding: 2px 6px; border-radius: 99px; border: 2px solid var(--ed-surface); font-weight: 700;">{{ $unreadCount }}</span>
                    </button>
                    
                    <div id="notificationsMenu" style="display: none; position: absolute; left: 0; top: 48px; width: 340px; background: var(--ed-surface); border-radius: var(--ed-radius-md); box-shadow: var(--ed-shadow-lg); border: 1px solid var(--ed-border); z-index: 1000; overflow: hidden;">
                        <div style="padding: 12px 16px; background: var(--ed-surface-alt); border-bottom: 1px solid var(--ed-border); display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; font-size: 0.88rem; color: var(--ed-text-main); display: flex; align-items: center; gap: 8px;">
                                <i class="fa-regular fa-bell" style="color: var(--ed-primary);"></i> {{ __('مركز التنبيهات') }}
                            </span>
                            @if(auth()->check() || auth('student')->check())
                                <button onclick="markAllReadFromNav()" style="background: none; border: none; font-size: 0.74rem; color: var(--ed-primary); font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-check-double"></i> {{ __('تحديد الكل كمقروء') }}
                                </button>
                            @endif
                        </div>

                        <div style="max-height: 320px; overflow-y: auto;" id="navNotificationsList">
                            @forelse($unreadItems as $item)
                                <a href="{{ route('notifications.open', $item->id) }}" class="notif-dropdown-item" style="display: flex; gap: 12px; padding: 12px 16px; border-bottom: 1px solid var(--ed-border-subtle); text-decoration: none; color: inherit; transition: var(--transition-smooth);" onmouseover="this.style.background='var(--ed-surface-alt)'" onmouseout="this.style.background='transparent'">
                                    <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--ed-primary-soft); color: var(--ed-primary); display: grid; place-items: center; flex-shrink: 0; font-size: 0.95rem;">
                                        <i class="fa-solid {{ $item->icon }}"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-size: 0.82rem; font-weight: 700; color: var(--ed-text-main); margin-bottom: 2px;">
                                            {{ $item->title }}
                                        </div>
                                        <div style="font-size: 0.78rem; font-weight: 500; color: var(--ed-text-body); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px;">
                                            {{ $item->message }}
                                        </div>
                                        <span style="font-size: 0.7rem; color: var(--ed-text-dim); display: flex; align-items: center; gap: 4px;">
                                            <i class="fa-regular fa-clock" style="font-size: 0.65rem;"></i> {{ $item->time }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <div style="padding: 28px 16px; text-align: center; color: var(--ed-text-muted);">
                                    <i class="fa-regular fa-circle-check" style="font-size: 1.6rem; margin-bottom: 6px; display: block; color: var(--ed-success); opacity: 0.8;"></i>
                                    <span style="font-size: 0.84rem; font-weight: 500;">{{ __('لا توجد تنبيهات جديدة') }}</span>
                                </div>
                            @endforelse
                        </div>

                        @if(isset($isStudent) && $isStudent)
                            <div style="padding: 10px; background: var(--ed-surface-alt); border-top: 1px solid var(--ed-border); text-align: center;">
                                <a href="{{ route('student.notifications.index') }}" style="font-size: 0.8rem; font-weight: 600; color: var(--ed-primary); text-decoration: none;">
                                    {{ __('عرض كافة التنبيهات ←') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>


                <!-- بطاقة المستخدم -->
                <div style="display:flex; align-items:center; gap:9px; background: var(--ed-surface); padding: 5px 12px; border-radius: 10px; border: 1px solid var(--ed-border);">
                    <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--ed-primary-soft); color: var(--ed-primary); display: grid; place-items: center; font-size: 0.85rem; font-weight: 700;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <span class="user-info-text" style="font-size: 0.84rem; font-weight: 600; color: var(--ed-text-main);">
                        {{ auth()->user()->name ?? auth('student')->user()->name_ar ?? auth('student')->user()->name ?? __('حسابي') }}
                    </span>
                </div>
            </div>
        </header>

        <div class="content-body">
            @if(auth('student')->check() && in_array(auth('student')->user()->status, ['suspended', 'frozen', 'inactive']))
                <div style="background: #fef2f2; border: 1.5px solid #f87171; border-radius: 12px; padding: 14px 20px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.08);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 38px; height: 38px; border-radius: 8px; background: #fee2e2; color: #dc2626; display: grid; place-items: center; font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <div>
                            <strong style="color: #991b1b; font-size: 0.92rem; display: block;">{{ __('تنبيه إداري: تم تجميد حسابك الدراسي مؤقتاً 🔒') }}</strong>
                            <span style="color: #b91c1c; font-size: 0.84rem;"><strong>{{ __('سبب التجميد:') }}</strong> {{ __(auth('student')->user()->freeze_reason ?: 'عدم سداد الرسوم الدراسية أو مراجعة النشاط الأكاديمي والالتزام.') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('student.pending-approval') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #dc2626; color: #ffffff; padding: 8px 16px; border-radius: 8px; font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: background 0.15s;">
                        <i class="fa-solid fa-shield-halved"></i> {{ __('عرض تفاصيل التجميد وإجراءات التفعيل') }}
                    </a>
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <!-- شريط التنقل السفلي للهواتف الذكية -->
    <nav class="mobile-bottom-nav">
        @if(auth('student')->check())
            <a href="{{ route('student.dashboard') }}" class="bottom-nav-item {{ Request::is('student/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>{{ __('الرئيسية') }}</span>
            </a>
            <a href="{{ route('student.subjects.index') }}" class="bottom-nav-item {{ Request::is('student/subjects*') ? 'active' : '' }}">
                <i class="fa-solid fa-book-open"></i>
                <span>{{ __('المواد') }}</span>
            </a>
            <a href="{{ route('student.exams.index') }}" class="bottom-nav-item {{ Request::is('student/my-exams*') || Request::is('student/exams*') ? 'active' : '' }}">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>{{ __('اختباراتي') }}</span>
            </a>
            <a href="{{ route('student.achievements') }}" class="bottom-nav-item {{ Request::is('student/achievements*') ? 'active' : '' }}">
                <i class="fa-solid fa-award"></i>
                <span>{{ __('الشهادات') }}</span>
            </a>
            <a href="{{ route('student.profile') }}" class="bottom-nav-item {{ Request::is('student/profile*') ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i>
                <span>{{ __('حسابي') }}</span>
            </a>
        @elseif(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="bottom-nav-item {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>{{ __('اللوحة') }}</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="bottom-nav-item {{ Request::is('admin/students*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>{{ __('الطلاب') }}</span>
            </a>
            <a href="{{ route('admin.certificates.index') }}" class="bottom-nav-item {{ Request::is('admin/certificates*') ? 'active' : '' }}">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>{{ __('الشهادات') }}</span>
            </a>
            <a href="{{ route('admin.payments.index') }}" class="bottom-nav-item {{ Request::is('admin/payments*') ? 'active' : '' }}">
                <i class="fa-solid fa-wallet"></i>
                <span>{{ __('المدفوعات') }}</span>
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
        if (notifToggle && notifMenu) {
            notifToggle.onclick = (e) => { 
                e.stopPropagation(); 
                notifMenu.style.display = notifMenu.style.display === 'block' ? 'none' : 'block'; 
            };
            notifMenu.onclick = (e) => {
                e.stopPropagation();
            };
            document.addEventListener('click', (e) => {
                if (!notifToggle.contains(e.target) && !notifMenu.contains(e.target)) {
                    notifMenu.style.display = 'none';
                }
            });
        }

        function markAllReadFromNav() {
            axios.post('{{ route('notifications.markAllReadUnified') }}', {
                _token: '{{ csrf_token() }}'
            }).then(() => {
                const badge = document.getElementById('navUnreadBadge');
                if (badge) badge.style.display = 'none';
                const list = document.getElementById('navNotificationsList');
                if (list) {
                    list.innerHTML = '<div style="padding: 24px 16px; text-align: center; color: var(--ed-text-muted);"><i class="fa-regular fa-circle-check" style="font-size: 1.6rem; margin-bottom: 6px; display: block; color: var(--ed-success);"></i><span style="font-size: 0.84rem; font-weight: 500;">تمت قراءة كافة التنبيهات بنجاح</span></div>';
                }
            }).catch(err => {
                console.error('Error marking all notifications read:', err);
            });
        }

        // الحفاظ التام على الواجهات الفاتحة الأصلية وإزالة أي أثر للوضع الداكن
        try {
            localStorage.removeItem('tawjihi-theme');
            localStorage.removeItem('theme');
            document.body.classList.remove('dark-theme');
            document.documentElement.classList.remove('dark-theme');
        } catch(e) {}

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>