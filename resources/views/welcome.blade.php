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

    <title>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} | بوابة ومنظومة الثانوية العامة لدولة فلسطين 🇵🇸</title>
    <meta name="description" content="المنظومة التعليمية الرائدة لطلبة الثانوية العامة في فلسطين: شروحات المنهاج الوزاري، بنك اختبارات وزارية محلولة، ومتابعة دراسية بإشراف أ. أحمد حسين شمالي.">

    <!-- الخطوط الموحدة للمنظومة (Alexandria & Tajawal) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ==========================================================================
           نظام التصميم الموحد لكافة واجهات المنظومة (Universal Tawjihi EdTech Design)
           - كامل العرض على الشاشة (Full Width 100%)
           - ريسبنسيف كامل لجميع الأجهزة والشاشات
           - كلاسيكي، أنيق، عالي الوضوح، ومريح للعين
           ========================================================================== */
        :root {
            --ed-primary: #1d4ed8;
            --ed-primary-dark: #1e3a8a;
            --ed-primary-deep: #0f172a;
            --ed-primary-hover: #1e40af;
            --ed-primary-soft: #eff6ff;
            --ed-primary-border: #bfdbfe;

            --ed-accent-gold: #f59e0b;
            --ed-accent-gold-dark: #d97706;
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
            --ed-text-light: #94a3b8;

            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;

            --shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.05);
            --shadow-card: 0 2px 5px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02);
            --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.06);
            --transition: all 0.2s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Alexandria', 'Tajawal', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            width: 100%;
            min-height: 100vh;
            background-color: var(--ed-bg);
            color: var(--ed-text-body);
            font-size: 14.5px;
            line-height: 1.65;
            direction: rtl;
            text-align: right;
            overflow-x: hidden;
        }

        a {
            color: var(--ed-primary);
            text-decoration: none;
            transition: var(--transition);
        }
        a:hover {
            color: var(--ed-primary-hover);
        }

        /* 1. الشريط العلوي الرفيع على كامل عرض الشاشة (Top Info Bar) */
        .top-info-bar {
            width: 100%;
            background-color: var(--ed-primary-deep);
            color: #cbd5e1;
            padding: 7px 32px;
            font-size: 12.5px;
            border-bottom: 1px solid #1e293b;
        }
        .top-bar-inner {
            width: 100%;
            max-width: 1560px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .top-info-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .top-info-right span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .top-info-left {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .top-info-left a {
            color: #93c5fd;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .top-info-left a:hover {
            color: #ffffff;
            text-decoration: none;
        }

        /* 2. الترويسة الرئيسية الرسمية على كامل العرض (Main Header) */
        .main-header {
            width: 100%;
            background: linear-gradient(135deg, #172554 0%, #1e3a8a 50%, #1e40af 100%);
            color: #ffffff;
            padding: 22px 32px;
            border-bottom: 4px solid var(--ed-accent-gold);
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.12);
        }
        .header-inner {
            width: 100%;
            max-width: 1560px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        .header-brand {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .header-logo-icon {
            width: 62px;
            height: 62px;
            background: #ffffff;
            color: var(--ed-primary-dark);
            border-radius: var(--radius-md);
            display: grid;
            place-items: center;
            font-size: 32px;
            border: 2px solid var(--ed-accent-gold);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            flex-shrink: 0;
        }
        .header-titles h1 {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.3;
            letter-spacing: -0.3px;
        }
        .header-titles p {
            font-size: 13.5px;
            color: #bfdbfe;
            font-weight: 500;
            margin-top: 3px;
        }
        .header-supervisor-badge {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: var(--radius-md);
            padding: 10px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            backdrop-filter: blur(4px);
        }
        .supervisor-avatar {
            width: 44px;
            height: 44px;
            background: var(--ed-accent-gold);
            color: var(--ed-primary-deep);
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 20px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .supervisor-meta .sup-title {
            font-size: 11.5px;
            color: var(--ed-accent-gold);
            font-weight: 700;
            display: block;
        }
        .supervisor-meta .sup-name {
            font-size: 15px;
            font-weight: 800;
            color: #ffffff;
            display: block;
        }

        /* 3. شريط التنقل المتناسق على كامل العرض (Main Navbar) */
        .main-navbar {
            width: 100%;
            background-color: var(--ed-primary-deep);
            border-bottom: 1px solid #1e293b;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .navbar-inner {
            width: 100%;
            max-width: 1560px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 54px;
        }
        .nav-menu {
            display: flex;
            align-items: center;
            list-style: none;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .nav-item {
            height: 100%;
            display: flex;
            align-items: center;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0 16px;
            height: 100%;
            color: #f1f5f9;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            border-left: 1px solid #1e293b;
            transition: var(--transition);
        }
        .nav-item:first-child .nav-link {
            border-right: 1px solid #1e293b;
        }
        .nav-link:hover,
        .nav-link.active {
            background-color: var(--ed-primary);
            color: #ffffff;
            text-decoration: none;
        }
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* أزرار النافبار */
        .btn-nav-login {
            background-color: var(--ed-primary);
            color: #ffffff;
            padding: 7px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .btn-nav-login:hover {
            background-color: var(--ed-primary-hover);
            color: #ffffff;
            text-decoration: none;
        }
        .btn-nav-register {
            background-color: var(--ed-success);
            color: #ffffff;
            padding: 7px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
            border: 1px solid #15803d;
        }
        .btn-nav-register:hover {
            background-color: var(--ed-success-hover);
            color: #ffffff;
            text-decoration: none;
        }

        /* زر القائمة للشاشات الصغيرة */
        .mobile-menu-btn {
            display: none;
            background: #1e293b;
            color: #ffffff;
            border: 1px solid #334155;
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            font-size: 16px;
            cursor: pointer;
        }

        /* 4. شريط الإعلانات والتنبيهات المباشر (Notice Ticker) */
        .notice-ticker-bar {
            width: 100%;
            background-color: var(--ed-accent-gold-soft);
            border-bottom: 1px solid #fde68a;
            color: #92400e;
            padding: 8px 32px;
        }
        .notice-ticker-inner {
            width: 100%;
            max-width: 1560px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13.5px;
        }
        .notice-tag {
            background-color: var(--ed-accent-gold-dark);
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 4px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .notice-content {
            font-weight: 600;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* 5. الحاوية العامة على كامل الشاشة (Main Page Layout) */
        .page-container {
            width: 100%;
            max-width: 1560px;
            margin: 0 auto;
            padding: 24px 32px 50px;
        }
        .layout-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 24px;
            align-items: start;
        }
        .main-content-flow {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .sidebar-flow {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* 6. بطاقات المنظومة الكلاسيكية الموحدة (Universal Ed-Cards) */
        .ed-card {
            background-color: var(--ed-surface);
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            transition: var(--transition);
        }
        .ed-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--ed-border-hover);
        }

        .ed-card-header {
            padding: 12px 18px;
            background-color: var(--ed-primary-dark);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--ed-accent-gold);
        }
        .ed-card-header.emerald {
            background-color: #166534;
            border-bottom-color: #22c55e;
        }
        .ed-card-header.slate {
            background-color: #334155;
            border-bottom-color: #94a3b8;
        }
        .ed-card-header h2,
        .ed-card-header h3 {
            font-size: 15px;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ffffff;
        }
        .ed-card-body {
            padding: 18px;
        }

        /* الصندوق الترحيبي الرئيسي (Welcome Hero Box) */
        .welcome-hero-card {
            background: linear-gradient(to bottom, #ffffff, var(--ed-surface-alt));
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-md);
            padding: 24px;
            border-right: 5px solid var(--ed-primary);
        }
        .welcome-hero-card h2 {
            font-size: 20px;
            font-weight: 800;
            color: var(--ed-primary-dark);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .welcome-hero-card p {
            font-size: 14px;
            color: var(--ed-text-body);
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .hero-action-buttons {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-cta-green {
            background-color: var(--ed-success);
            color: #ffffff;
            padding: 10px 22px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #15803d;
            box-shadow: 0 2px 6px rgba(22, 163, 74, 0.2);
            transition: var(--transition);
        }
        .btn-cta-green:hover {
            background-color: var(--ed-success-hover);
            color: #ffffff;
            transform: translateY(-1px);
            text-decoration: none;
        }
        .btn-cta-blue {
            background-color: var(--ed-primary);
            color: #ffffff;
            padding: 10px 22px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--ed-primary-hover);
            box-shadow: 0 2px 6px rgba(29, 78, 216, 0.2);
            transition: var(--transition);
        }
        .btn-cta-blue:hover {
            background-color: var(--ed-primary-hover);
            color: #ffffff;
            transform: translateY(-1px);
            text-decoration: none;
        }

        /* شبكة الفروع الدراسية (Tawjihi Branches Grid) */
        .branches-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }
        .branch-box {
            background: #ffffff;
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-md);
            padding: 16px;
            border-right: 4px solid var(--ed-primary);
            transition: var(--transition);
        }
        .branch-box.sci { border-right-color: #2563eb; }
        .branch-box.lit { border-right-color: #059669; }
        .branch-box.bus { border-right-color: #d97706; }
        .branch-box.voc { border-right-color: #dc2626; }
        .branch-box:hover {
            border-color: var(--ed-border-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .branch-box-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .branch-box-head h4 {
            font-size: 15px;
            font-weight: 800;
            color: var(--ed-text-main);
        }
        .branch-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            background: var(--ed-surface-alt);
            color: var(--ed-text-muted);
        }
        .branch-box p {
            font-size: 13px;
            color: var(--ed-text-muted);
            line-height: 1.6;
            margin-bottom: 12px;
        }
        .branch-tags-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .branch-tag {
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
            background: var(--ed-primary-soft);
            color: var(--ed-primary);
            border: 1px solid var(--ed-primary-border);
        }

        /* شبكة الخدمات والمميزات (Features Grid) */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
        }
        .feature-card {
            background: var(--ed-surface);
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-sm);
            padding: 16px;
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }
        .feature-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: var(--radius-sm);
            background: var(--ed-primary-soft);
            color: var(--ed-primary);
            display: grid;
            place-items: center;
            font-size: 18px;
            flex-shrink: 0;
            border: 1px solid var(--ed-primary-border);
        }
        .feature-icon-wrap.green {
            background: var(--ed-success-soft);
            color: var(--ed-success);
            border-color: #a7f3d0;
        }
        .feature-icon-wrap.gold {
            background: var(--ed-accent-gold-soft);
            color: var(--ed-accent-gold-dark);
            border-color: #fde68a;
        }
        .feature-text h4 {
            font-size: 14px;
            font-weight: 700;
            color: var(--ed-text-main);
            margin-bottom: 4px;
        }
        .feature-text p {
            font-size: 12.5px;
            color: var(--ed-text-muted);
            line-height: 1.6;
        }

        /* جدول كلاسيكي كامل العرض (Classic Full-Width Table) */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        .classic-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .classic-table th,
        .classic-table td {
            padding: 10px 14px;
            border: 1px solid var(--ed-border);
            text-align: right;
        }
        .classic-table th {
            background-color: var(--ed-surface-alt);
            color: var(--ed-text-main);
            font-weight: 700;
        }
        .classic-table tr:nth-child(even) td {
            background-color: #fcfdfe;
        }
        .classic-table tr:hover td {
            background-color: #eff6ff;
        }

        /* مكونات الشريط الجانبي (Sidebar Components) */
        .sidebar-login-box {
            text-align: center;
            padding: 10px 0;
        }
        .sidebar-login-box p {
            font-size: 13px;
            color: var(--ed-text-muted);
            margin-bottom: 14px;
        }
        .sidebar-action-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            font-weight: 700;
            margin-bottom: 10px;
            transition: var(--transition);
        }
        .sidebar-action-btn.login {
            background-color: var(--ed-primary);
            color: #ffffff;
            border: 1px solid var(--ed-primary-hover);
        }
        .sidebar-action-btn.login:hover {
            background-color: var(--ed-primary-hover);
            color: #ffffff;
            text-decoration: none;
        }
        .sidebar-action-btn.reg {
            background-color: var(--ed-success);
            color: #ffffff;
            border: 1px solid #15803d;
        }
        .sidebar-action-btn.reg:hover {
            background-color: var(--ed-success-hover);
            color: #ffffff;
            text-decoration: none;
        }

        /* جدول الإحصائيات */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }
        .stats-table td {
            padding: 10px 14px;
            border-bottom: 1px solid var(--ed-border);
            font-size: 13px;
        }
        .stats-table tr:last-child td {
            border-bottom: none;
        }
        .stat-val {
            text-align: left;
            font-weight: 800;
            color: var(--ed-primary-dark);
            font-size: 14px;
        }

        /* بطاقة المشرف والواتساب */
        .supervisor-profile-card {
            text-align: center;
            padding: 6px 0;
        }
        .supervisor-avatar-lg {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--ed-primary-dark);
            color: #ffffff;
            display: inline-grid;
            place-items: center;
            font-size: 26px;
            margin-bottom: 10px;
            border: 3px solid var(--ed-accent-gold);
        }
        .supervisor-profile-card h4 {
            font-size: 15px;
            font-weight: 800;
            color: var(--ed-text-main);
            margin-bottom: 2px;
        }
        .supervisor-profile-card span {
            font-size: 12.5px;
            color: var(--ed-text-muted);
            display: block;
            margin-bottom: 14px;
        }
        .btn-whatsapp-full {
            background-color: #25d366;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            font-weight: 700;
            border: 1px solid #1eb956;
            transition: var(--transition);
        }
        .btn-whatsapp-full:hover {
            background-color: #1eb956;
            color: #ffffff;
            text-decoration: none;
        }

        /* قائمة الروابط الجانبية السريعة */
        .quick-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .quick-nav-list li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-bottom: 1px solid var(--ed-border);
            font-size: 13px;
            color: var(--ed-text-body);
            font-weight: 600;
            transition: var(--transition);
        }
        .quick-nav-list li:last-child a {
            border-bottom: none;
        }
        .quick-nav-list li a:hover {
            background-color: var(--ed-primary-soft);
            color: var(--ed-primary);
            text-decoration: none;
            padding-right: 18px;
        }

        /* 7. تذييل الصفحة الشامل على كامل العرض (Full-Width Footer) */
        .main-footer {
            width: 100%;
            background-color: var(--ed-primary-deep);
            color: #94a3b8;
            border-top: 4px solid var(--ed-primary);
            padding: 40px 32px 0;
            margin-top: 40px;
        }
        .footer-inner {
            width: 100%;
            max-width: 1560px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 36px;
            padding-bottom: 30px;
        }
        .footer-brand h3 {
            font-size: 18px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .footer-brand p {
            font-size: 13px;
            line-height: 1.8;
            color: #cbd5e1;
            max-width: 500px;
        }
        .footer-col h4 {
            font-size: 14.5px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 14px;
            border-bottom: 2px solid #334155;
            padding-bottom: 6px;
        }
        .footer-links-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-links-list li {
            margin-bottom: 8px;
        }
        .footer-links-list li a {
            color: #94a3b8;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .footer-links-list li a:hover {
            color: #ffffff;
            text-decoration: none;
        }
        .footer-bottom-bar {
            border-top: 1px solid #1e293b;
            padding: 18px 0;
            background-color: #020617;
            margin: 0 -32px;
            padding: 16px 32px;
        }
        .footer-bottom-inner {
            width: 100%;
            max-width: 1560px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 12.5px;
            color: #64748b;
        }

        /* التوافقية والريسبنسيف الكامل لجميع الشاشات والأجهزة */
        @media (max-width: 1080px) {
            .layout-grid {
                grid-template-columns: 1fr;
            }
            .sidebar-flow {
                order: 2;
            }
            .footer-inner {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 860px) {
            .top-info-bar,
            .main-header,
            .navbar-inner,
            .notice-ticker-bar,
            .page-container,
            .main-footer,
            .footer-bottom-bar {
                padding-left: 16px;
                padding-right: 16px;
            }
            .header-inner {
                flex-direction: column;
                text-align: center;
                gap: 14px;
            }
            .header-brand {
                flex-direction: column;
            }
            .header-supervisor-badge {
                width: 100%;
                justify-content: center;
            }
            .mobile-menu-btn {
                display: block;
            }
            .navbar-inner {
                position: relative;
            }
            .nav-menu {
                display: none;
                position: absolute;
                top: 54px;
                right: 0;
                width: 100%;
                background-color: var(--ed-primary-deep);
                flex-direction: column;
                height: auto;
                border-top: 1px solid #334155;
                box-shadow: 0 10px 15px rgba(0,0,0,0.3);
                z-index: 1100;
            }
            .nav-menu.active {
                display: flex;
            }
            .nav-item {
                width: 100%;
                height: auto;
            }
            .nav-link {
                width: 100%;
                padding: 12px 20px;
                border: none;
                border-bottom: 1px solid #1e293b;
            }
            .nav-actions {
                display: flex;
            }
            .btn-nav-login,
            .btn-nav-register {
                padding: 6px 12px;
                font-size: 12px;
            }
            .footer-inner {
                grid-template-columns: 1fr;
            }
            .footer-bottom-inner {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <!-- 1. الشريط العلوي الرفيع على كامل عرض الشاشة -->
    <div class="top-info-bar">
        <div class="top-bar-inner">
            <div class="top-info-right">
                <span><i class="fa-regular fa-calendar-check text-warning"></i> اليوم: {{ date('Y/m/d') }} م</span>
                <span>بِسْمِ اللَّـهِ الرَّحْمَـٰنِ الرَّحِيمِ</span>
            </div>
            <div class="top-info-left">
                <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> الرئيسية</a>
                <a href="{{ route('public.faq') }}"><i class="fa-solid fa-circle-question"></i> الأسئلة الشائعة</a>
                <a href="{{ route('public.contact') }}"><i class="fa-solid fa-headset"></i> الدعم والشكاوى</a>
                <a href="https://wa.me/970597694385" target="_blank" style="color: #4ade80;">
                    <i class="fa-brands fa-whatsapp"></i> واتساب: 0597694385
                </a>
            </div>
        </div>
    </div>

    <!-- 2. الترويسة الرسمية على كامل عرض الشاشة -->
    <header class="main-header">
        <div class="header-inner">
            <div class="header-brand">
                <div class="header-logo-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="header-titles">
                    <h1>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸</h1>
                    <p>بوابة ومنظومة الثانوية العامة لدولة فلسطين | المناهج الوزارية ونماذج الاختبارات المعتمدة</p>
                </div>
            </div>

            <div class="header-supervisor-badge">
                <div class="supervisor-avatar">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div class="supervisor-meta">
                    <span class="sup-title">المشرف العام على المنظومة التعليمية:</span>
                    <span class="sup-name">أ. أحمد حسين شمالي</span>
                </div>
            </div>
        </div>
    </header>

    <!-- 3. شريط القوائم الرئيسي على كامل عرض الشاشة مع استجابة الموبايل -->
    <nav class="main-navbar">
        <div class="navbar-inner">
            <button class="mobile-menu-btn" id="mobileMenuToggle" aria-label="القائمة">
                <i class="fa-solid fa-bars"></i> القائمة
            </button>

            <ul class="nav-menu" id="mainNavMenu">
                <li class="nav-item"><a href="{{ route('home') }}" class="nav-link active"><i class="fa-solid fa-house"></i> الرئيسية</a></li>
                <li class="nav-item"><a href="#branches" class="nav-link"><i class="fa-solid fa-book-bookmark"></i> فروع التوجيهي</a></li>
                <li class="nav-item"><a href="#features" class="nav-link"><i class="fa-solid fa-list-check"></i> خدمات المنصة</a></li>
                @if(Route::has('tawjihi.archive'))
                    <li class="nav-item"><a href="{{ route('tawjihi.archive') }}" class="nav-link"><i class="fa-solid fa-folder-open"></i> بنك الامتحانات الوزارية</a></li>
                @endif
                @if(Route::has('tawjihi.calculator'))
                    <li class="nav-item"><a href="{{ route('tawjihi.calculator') }}" class="nav-link"><i class="fa-solid fa-calculator"></i> حساب المعدل</a></li>
                @endif
                <li class="nav-item"><a href="{{ route('public.faq') }}" class="nav-link"><i class="fa-solid fa-circle-question"></i> المساعدة</a></li>
                <li class="nav-item"><a href="{{ route('public.contact') }}" class="nav-link"><i class="fa-solid fa-phone"></i> تواصل مع الإدارة</a></li>
            </ul>

            <div class="nav-actions">
                @if(Auth::guard('student')->check() || Auth::check())
                    <a href="{{ route('dashboard') }}" class="btn-nav-login">
                        <i class="fa-solid fa-gauge-high"></i> لوحة التحكم
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

    <!-- 4. شريط الإعلانات والتنبيهات المباشر على كامل العرض -->
    <div class="notice-ticker-bar">
        <div class="notice-ticker-inner">
            <span class="notice-tag"><i class="fa-solid fa-bullhorn"></i> إعلان هام</span>
            <span class="notice-content">
                أهلاً وسهلاً بكافة طلبة الثانوية العامة في فلسطين لدورة {{ date('Y') }}. تم فتح باب التسجيل وتفعيل الشروحات ونماذج الامتحانات بإشراف نخبة من المعلمين المعتمدين.
            </span>
        </div>
    </div>

    <!-- 5. الحاوية العامة على كامل الشاشة (توزيع متوازن وعالي الاستجابة) -->
    <div class="page-container">
        <div class="layout-grid">

            <!-- العمود الرئيسي الأيمن للمحتوى (Main Content Column) -->
            <main class="main-content-flow">

                <!-- الصندوق الترحيبي والبدء السريع -->
                <div class="welcome-hero-card">
                    <h2>
                        <i class="fa-solid fa-graduation-cap text-primary"></i>
                        مرحباً بكم في منصة منارة التوجيهي التعليمية
                    </h2>
                    <p>
                        المنظومة الأكاديمية الفلسطينية الرائدة والمتخصصة في مرافقة طلبة الثانوية العامة (التوجيهي) في كافة محافظات فلسطين (القدس، الضفة الغربية، وقطاع غزة). تقدم المنصة شروحات منهجية مفصلة، بنك نماذج اختبارات وزارية محلولة، ومتابعة دراسية دقيقة بقيادة وإشراف الأستاذ <strong>أحمد حسين شمالي</strong> لضمان نيل أعلى المراتب والتفوق الأكاديمي.
                    </p>

                    <div class="hero-action-buttons">
                        @if(Auth::guard('student')->check() || Auth::check())
                            <a href="{{ route('dashboard') }}" class="btn-cta-blue">
                                <i class="fa-solid fa-arrow-left"></i> الدخول للوحة التحكم التعليمية الخاصة بك
                            </a>
                        @else
                            <a href="{{ route('students.create') }}" class="btn-cta-green">
                                <i class="fa-solid fa-user-pen"></i> ابدأ الآن - تسجيل حساب طالب جديد
                            </a>
                            <a href="{{ route('login') }}" class="btn-cta-blue">
                                <i class="fa-solid fa-key"></i> تسجيل الدخول للنظام
                            </a>
                        @endif
                    </div>
                </div>

                <!-- صندوق فروع الثانوية العامة -->
                <div class="ed-card" id="branches">
                    <div class="ed-card-header">
                        <h2>
                            <i class="fa-solid fa-book-open"></i>
                            فروع ومسارات الثانوية العامة المعتمدة (المنهاج الفلسطيني)
                        </h2>
                        <span style="font-size: 12px; color: #fef08a;">تغطية شاملة 100%</span>
                    </div>
                    <div class="ed-card-body">
                        <div class="branches-grid">
                            <!-- العلمي -->
                            <div class="branch-box sci">
                                <div class="branch-box-head">
                                    <h4>الفرع العلمي</h4>
                                    <span class="branch-badge">مسار علمي ⚛️</span>
                                </div>
                                <p>شروحات عميقة وتمارين تفصيلية لقوانين المساقات العلمية ونماذج التوجيهي الوزارية.</p>
                                <div class="branch-tags-list">
                                    <span class="branch-tag">الرياضيات</span>
                                    <span class="branch-tag">الفيزياء</span>
                                    <span class="branch-tag">الكيمياء</span>
                                    <span class="branch-tag">العلوم الحياتية</span>
                                </div>
                            </div>

                            <!-- الأدبي -->
                            <div class="branch-box lit">
                                <div class="branch-box-head">
                                    <h4>الفرع الأدبي</h4>
                                    <span class="branch-badge">مسار أدبي 📜</span>
                                </div>
                                <p>تبسيط شامل لقواعد وقصائد اللغة العربية والإنجليزية، وتلخيص التاريخ والجغرافيا.</p>
                                <div class="branch-tags-list">
                                    <span class="branch-tag">اللغة العربية</span>
                                    <span class="branch-tag">اللغة الإنجليزية</span>
                                    <span class="branch-tag">التاريخ</span>
                                    <span class="branch-tag">الجغرافيا</span>
                                </div>
                            </div>

                            <!-- الريادة والأعمال -->
                            <div class="branch-box bus">
                                <div class="branch-box-head">
                                    <h4>فرع الريادة والأعمال</h4>
                                    <span class="branch-badge">ريادة واقتصاد 💼</span>
                                </div>
                                <p>مسائل تطبيقية في المحاسبة المالية، دراسات الجدوى، والإدارة والاقتصاد والمشاريع.</p>
                                <div class="branch-tags-list">
                                    <span class="branch-tag">المحاسبة</span>
                                    <span class="branch-tag">الإدارة والاقتصاد</span>
                                    <span class="branch-tag">المشاريع الصغيرة</span>
                                </div>
                            </div>

                            <!-- الشرعي والصناعي -->
                            <div class="branch-box voc">
                                <div class="branch-box-head">
                                    <h4>الفرع الشرعي والصناعي</h4>
                                    <span class="branch-badge">مسارات مهنية وشرعية ⚙️</span>
                                </div>
                                <p>تغطية مساقات العلوم الشرعية والحديث والفقه، بجانب الرياضيات والفيزياء التطبيقية الصناعية.</p>
                                <div class="branch-tags-list">
                                    <span class="branch-tag">العلوم الإسلامية</span>
                                    <span class="branch-tag">الرياضيات الصناعية</span>
                                    <span class="branch-tag">الفيزياء التطبيقية</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- صندوق خدمات ومميزات المنظومة -->
                <div class="ed-card" id="features">
                    <div class="ed-card-header emerald">
                        <h3>
                            <i class="fa-solid fa-star"></i>
                            مميزات المنظومة التعليمية للطالب الفلسطيني
                        </h3>
                        <span style="font-size: 12px; color: #bbf7d0;">بيئة دراسية متكاملة</span>
                    </div>
                    <div class="ed-card-body">
                        <div class="features-grid">
                            <div class="feature-card">
                                <div class="feature-icon-wrap"><i class="fa-solid fa-play"></i></div>
                                <div class="feature-text">
                                    <h4>شروحات مرئية منظمة</h4>
                                    <p>دروس مصورة عالية الجودة مرتبة ترتيباً دقيقاً حسب فهرس ووحدات الكتاب الوزاري الفلسطيني.</p>
                                </div>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon-wrap green"><i class="fa-solid fa-clipboard-check"></i></div>
                                <div class="feature-text">
                                    <h4>بنك نماذج الامتحانات الوزارية</h4>
                                    <p>اختبارات السنوات السابقة لجميع الفروع مع نماذج الإجابات الرسمية المعتمدة وتوزيع الدرجات.</p>
                                </div>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon-wrap gold"><i class="fa-solid fa-file-pdf"></i></div>
                                <div class="feature-text">
                                    <h4>ملازم وتلاخيص PDF</h4>
                                    <p>ملفات دراسية وتلاخيص مكثفة جاهزة للتحميل والطباعة المنزلية لسرعة مراجعة القوانين والقواعد.</p>
                                </div>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon-wrap"><i class="fa-solid fa-chalkboard-user"></i></div>
                                <div class="feature-text">
                                    <h4>إشراف ومتابعة مستمرة</h4>
                                    <p>تواصل أكاديمي ومتابعة مباشرة من المشرف أ. أحمد شمالي لدعم مسيرة تفوق الطلاب خطوة بخطوة.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- صندوق خطوات البدء والدراسة -->
                <div class="ed-card">
                    <div class="ed-card-header slate">
                        <h3>
                            <i class="fa-solid fa-shoe-prints"></i>
                            كيف تبدأ رحلة التفوق في المنصة؟ (٣ خطوات ميسرة)
                        </h3>
                    </div>
                    <div class="ed-card-body" style="padding: 0;">
                        <div class="table-responsive">
                            <table class="classic-table">
                                <thead>
                                    <tr>
                                        <th style="width: 90px; text-align: center;">المرحلة</th>
                                        <th>الإجراء المطلوب من الطالب</th>
                                        <th style="width: 150px; text-align: center;">الرابط المباشر</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: center;"><strong>الخطوة 1</strong></td>
                                        <td>إنشاء حساب طالب جديد وإدخال بيانات الفرع الدراسي والاسم ورقم الهاتف للتواصل.</td>
                                        <td style="text-align: center;"><a href="{{ route('students.create') }}" class="btn-cta-green" style="padding: 5px 12px; font-size: 12px;">تسجيل جديد</a></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;"><strong>الخطوة 2</strong></td>
                                        <td>تسجيل الدخول إلى حسابك الخاص واختيار المساقات والمواد الدراسية المقررة لفرعك.</td>
                                        <td style="text-align: center;"><a href="{{ route('login') }}" class="btn-cta-blue" style="padding: 5px 12px; font-size: 12px;">تسجيل الدخول</a></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;"><strong>الخطوة 3</strong></td>
                                        <td>مشاهدة الدروس والشروحات، تحميل التلاخيص والملازم، وحل نماذج الامتحانات الوزارية بانتظام.</td>
                                        <td style="text-align: center;"><a href="{{ route('dashboard') }}" style="font-weight: 700;">الانتقال للمنظومة ←</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main>

            <!-- العمود الجانبي الأيسر (Sidebar Flow) -->
            <aside class="sidebar-flow">

                <!-- 1. صندوق بوابة الحساب والدخول السريع -->
                <div class="ed-card">
                    <div class="ed-card-header">
                        <h3><i class="fa-solid fa-user-lock"></i> بوابة الحساب والدخول</h3>
                    </div>
                    <div class="ed-card-body">
                        @if(Auth::guard('student')->check() || Auth::check())
                            <div style="text-align: center; margin-bottom: 14px;">
                                <div style="font-size: 12px; color: var(--ed-text-muted);">مرحباً بك مجدداً</div>
                                <div style="font-size: 15px; font-weight: 800; color: var(--ed-primary-dark); margin-top: 2px;">
                                    @if(Auth::guard('student')->check())
                                        {{ Auth::guard('student')->user()->name }}
                                    @else
                                        {{ Auth::user()->name }}
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('dashboard') }}" class="sidebar-action-btn login">
                                <i class="fa-solid fa-gauge-high"></i> الانتقال للوحة التحكم التعليمية
                            </a>
                        @else
                            <div class="sidebar-login-box">
                                <p>سجل دخولك لمتابعة دروسك واختباراتك أو أنشئ حسابك الجديد خلال لحظات:</p>
                                <a href="{{ route('login') }}" class="sidebar-action-btn login">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i> تسجيل الدخول للنظام
                                </a>
                                <a href="{{ route('students.create') }}" class="sidebar-action-btn reg">
                                    <i class="fa-solid fa-user-plus"></i> تسجيل حساب طالب جديد
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. صندوق إحصائيات المنصة الموحدة -->
                <div class="ed-card">
                    <div class="ed-card-header slate">
                        <h3><i class="fa-solid fa-chart-column"></i> إحصائيات المنظومة</h3>
                    </div>
                    <div class="ed-card-body" style="padding: 0;">
                        <table class="stats-table">
                            <tbody>
                                <tr>
                                    <td><i class="fa-solid fa-users text-primary me-2"></i> الطلبة المسجلين</td>
                                    <td class="stat-val">{{ number_format($stats['students'] ?? 1200) }} طالب</td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid fa-book-bookmark text-success me-2"></i> المساقات المعتمدة</td>
                                    <td class="stat-val" style="color: var(--ed-success);">{{ number_format($stats['subjects'] ?? 18) }} مساق</td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid fa-video text-warning me-2"></i> الدروس والشروحات</td>
                                    <td class="stat-val" style="color: var(--ed-accent-gold-dark);">{{ number_format($stats['lessons'] ?? 350) }} شرح</td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid fa-file-signature text-danger me-2"></i> النماذج والاختبارات</td>
                                    <td class="stat-val" style="color: #dc2626;">{{ number_format($stats['exams'] ?? 150) }} اختبار</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. بطاقة المشرف العام والتواصل المباشر -->
                <div class="ed-card">
                    <div class="ed-card-header emerald">
                        <h3><i class="fa-solid fa-headset"></i> الإشراف والتواصل المباشر</h3>
                    </div>
                    <div class="ed-card-body">
                        <div class="supervisor-profile-card">
                            <div class="supervisor-avatar-lg">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <h4>أ. أحمد حسين شمالي</h4>
                            <span>المشرف العام على المنظومة التعليمية</span>

                            @php
                                $waDigits = '970597694385';
                                $waMsg = urlencode("السلام عليكم أستاذ أحمد شمالي، أود الاستفسار والتسجيل في منصة منارة التوجيهي.");
                            @endphp

                            <a href="https://wa.me/{{ $waDigits }}?text={{ $waMsg }}" target="_blank" class="btn-whatsapp-full">
                                <i class="fa-brands fa-whatsapp fa-lg"></i> تواصل عبر الواتساب (0597694385)
                            </a>

                            <div style="font-size: 12px; color: var(--ed-text-muted); margin-top: 10px;">
                                رقم بديل وجوال باي: <strong>0567897212</strong> • فلسطين 🇵🇸
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. روابط سريعة ومفيدة للطالب -->
                <div class="ed-card">
                    <div class="ed-card-header">
                        <h3><i class="fa-solid fa-link"></i> روابط سريعة ومفيدة</h3>
                    </div>
                    <div class="ed-card-body" style="padding: 0;">
                        <ul class="quick-nav-list">
                            @if(Route::has('tawjihi.calculator'))
                                <li>
                                    <a href="{{ route('tawjihi.calculator') }}">
                                        <i class="fa-solid fa-calculator text-success"></i> حاسبة معدل التوجيهي التفاعلية
                                    </a>
                                </li>
                            @endif
                            @if(Route::has('tawjihi.archive'))
                                <li>
                                    <a href="{{ route('tawjihi.archive') }}">
                                        <i class="fa-solid fa-folder-tree text-primary"></i> أرشيف الامتحانات الوزارية السابقة
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('public.faq') }}">
                                    <i class="fa-solid fa-circle-question text-warning"></i> الأسئلة الشائعة وإجاباتها
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('public.terms') }}">
                                    <i class="fa-solid fa-file-contract text-secondary"></i> شروط الاستخدام والاشتراك
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('public.contact') }}">
                                    <i class="fa-solid fa-envelope-open-text text-danger"></i> تقديم شكوى أو استفسار
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

            </aside>

        </div>
    </div>

    <!-- 6. تذييل الصفحة الشامل على كامل العرض (Full-Width Footer) -->
    <footer class="main-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <h3>🇵🇸 {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</h3>
                <p>
                    المنظومة الأكاديمية الفلسطينية المعتمدة لطلبة الثانوية العامة (التوجيهي). نسعى إلى تيسير وصول العلم والشروحات النموذجية المتوافقة مع تحديثات وزارة التربية والتعليم لكافة بيوت فلسطين.
                </p>
                <div style="margin-top: 12px; color: #93c5fd; font-weight: 700; font-size: 13px;">
                    إشراف ومتابعة: الأستاذ أحمد حسين شمالي
                </div>
            </div>

            <div class="footer-col">
                <h4>روابط سريعة</h4>
                <ul class="footer-links-list">
                    <li><a href="{{ route('home') }}"><i class="fa-solid fa-angle-left"></i> الصفحة الرئيسية</a></li>
                    <li><a href="{{ route('login') }}"><i class="fa-solid fa-angle-left"></i> تسجيل الدخول للنظام</a></li>
                    <li><a href="{{ route('students.create') }}"><i class="fa-solid fa-angle-left"></i> تسجيل طالب جديد</a></li>
                    <li><a href="{{ route('public.terms') }}"><i class="fa-solid fa-angle-left"></i> الشروط والأحكام</a></li>
                    <li><a href="{{ route('public.privacy') }}"><i class="fa-solid fa-angle-left"></i> سياسة الخصوصية</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>التواصل والدعم الفني</h4>
                <ul class="footer-links-list">
                    <li>
                        <a href="https://wa.me/970597694385" target="_blank" style="color: #4ade80;">
                            <i class="fa-brands fa-whatsapp"></i> واتساب المشرف: 0597694385
                        </a>
                    </li>
                    <li>
                        <span style="font-size: 13px; color: #94a3b8;">
                            <i class="fa-solid fa-phone me-1"></i> هاتف بديل: 0567897212
                        </span>
                    </li>
                    <li>
                        <a href="{{ route('public.contact') }}">
                            <i class="fa-regular fa-envelope me-1"></i> مركز الشكاوى والاستفسارات
                        </a>
                    </li>
                    <li>
                        <span style="font-size: 13px; color: #94a3b8;">
                            <i class="fa-solid fa-location-dot me-1"></i> دولة فلسطين 🇵🇸
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom-bar">
            <div class="footer-bottom-inner">
                <span>جميع الحقوق محفوظة © {{ date('Y') }} - {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸</span>
                <span>متوافق تماماً مع المنهاج الرسمي لوزارة التربية والتعليم الفلسطينية - دورة {{ date('Y') }}</span>
            </div>
        </div>
    </footer>

    <!-- سكربت بسيط لفتح وإغلاق قائمة الموبايل -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mobileMenuToggle');
            const navMenu = document.getElementById('mainNavMenu');

            if (toggleBtn && navMenu) {
                toggleBtn.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                });
            }
        });
    </script>

</body>
</html>
