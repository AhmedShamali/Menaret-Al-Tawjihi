<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(\App\Models\Setting::get('site_favicon'))
        <link rel="icon" href="{{ asset(\App\Models\Setting::get('site_favicon')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @endif

    <title>{{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }} | {{ __('بوابة ومنظومة الثانوية العامة لدولة فلسطين | المنهاج الوزاري المعتمد') }} 🇵🇸</title>
    <meta name="description" content="{{ __('المنظومة التعليمية الرائدة لطلبة الثانوية العامة في فلسطين: شروحات المنهاج الوزاري، بنك اختبارات وزارية محلولة، ومتابعة دراسية بإشراف أ. أحمد حسين شمالي.') }}">

    <!-- الخطوط الموحدة للمنظومة (Tajawal & Alexandria) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800;900&family=Alexandria:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ==========================================================================
           نظام التصميم الأكاديمي الفاتح (Light Academic University Portal Design)
           - فواتح بالكامل: خلفيات بيضاء ورمادية ناعمة
           - حدود ناعمة #e2e8f0 وخطوط واضحة وصغيرة 13-14.5px
           - أسلوب بوابات الجامعات الكلاسيكية الأنيقة
           ========================================================================== */
        :root {
            --ed-primary: #1d4ed8;
            --ed-primary-hover: #1e40af;
            --ed-primary-soft: #eff6ff;
            --ed-primary-border: #bfdbfe;

            --ed-accent-gold: #d97706;
            --ed-accent-gold-soft: #fef3c7;
            --ed-accent-gold-border: #fde68a;

            --ed-success: #16a34a;
            --ed-success-hover: #15803d;
            --ed-success-soft: #ecfdf5;
            --ed-success-border: #a7f3d0;

            --ed-bg: #f8fafc;
            --ed-surface: #ffffff;
            --ed-surface-alt: #f1f5f9;
            --ed-border: #e2e8f0;
            --ed-border-hover: #cbd5e1;

            --ed-text-main: #0f172a;
            --ed-text-body: #334155;
            --ed-text-muted: #64748b;

            --radius-sm: 6px;
            --radius-md: 8px;

            --shadow-card: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.2s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', 'Alexandria', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            width: 100%;
            min-height: 100vh;
            background-color: var(--ed-bg);
            color: var(--ed-text-body);
            font-size: 13.5px;
            line-height: 1.6;
            overflow-x: hidden;
        }

        html[dir="rtl"] body { direction: rtl; text-align: right; }
        html[dir="ltr"] body { direction: ltr; text-align: left; }

        a {
            color: var(--ed-primary);
            text-decoration: none;
            transition: var(--transition);
        }
        a:hover {
            color: var(--ed-primary-hover);
        }

        /* 1. الشريط العلوي الرفيع - معلومات وتاريخ */
        .top-info-bar {
            width: 100%;
            background-color: #ffffff;
            color: var(--ed-text-muted);
            padding: 6px 32px;
            font-size: 12px;
            border-bottom: 1px solid var(--ed-border);
        }
        .top-bar-inner {
            width: 100%;
            max-width: 1440px;
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
            gap: 18px;
        }
        .top-info-right span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .top-info-left {
            display: flex;
            align-items: center;
            gap: 14px;
            color: var(--ed-text-body);
            font-size: 12px;
            font-weight: 600;
        }

        /* 2. الترويسة الرئيسية الرسمية - فاتحة ونظيفة */
        .main-header {
            width: 100%;
            background: #ffffff;
            color: var(--ed-text-main);
            padding: 18px 32px;
            border-bottom: 1px solid var(--ed-border);
        }
        .header-inner {
            width: 100%;
            max-width: 1440px;
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
            gap: 14px;
        }
        .header-logo-icon {
            width: 52px;
            height: 52px;
            background: var(--ed-primary-soft);
            color: var(--ed-primary);
            border-radius: var(--radius-md);
            display: grid;
            place-items: center;
            font-size: 26px;
            border: 1px solid var(--ed-primary-border);
            flex-shrink: 0;
        }
        .header-titles h1 {
            font-size: 20px;
            font-weight: 800;
            color: var(--ed-text-main);
            line-height: 1.3;
        }
        .header-titles p {
            font-size: 13px;
            color: var(--ed-text-muted);
            font-weight: 500;
            margin-top: 2px;
        }
        .header-supervisor-badge {
            background: #f8fafc;
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-md);
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .supervisor-avatar {
            width: 38px;
            height: 38px;
            background: #eff6ff;
            color: var(--ed-primary);
            border: 1px solid var(--ed-primary-border);
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 16px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .supervisor-meta .sup-title {
            font-size: 11px;
            color: var(--ed-text-muted);
            font-weight: 600;
            display: block;
        }
        .supervisor-meta .sup-name {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--ed-text-main);
            display: block;
        }

        /* 3. شريط التنقل المتناسق - فاتح وأنيق */
        .main-navbar {
            width: 100%;
            background-color: #ffffff;
            border-bottom: 1px solid var(--ed-border);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        }
        .navbar-inner {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 48px;
        }
        .nav-menu {
            display: flex;
            align-items: center;
            list-style: none;
            height: 100%;
            margin: 0;
            padding: 0;
            gap: 4px;
        }
        .nav-item {
            height: 100%;
            display: flex;
            align-items: center;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            color: var(--ed-text-body);
            font-size: 13px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }
        .nav-link:hover {
            background-color: var(--ed-surface-alt);
            color: var(--ed-primary);
        }
        .nav-link.active {
            background-color: var(--ed-primary-soft);
            color: var(--ed-primary);
            font-weight: 700;
        }
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* أزرار الحساب في النافبار */
        .btn-nav-login {
            background-color: #ffffff;
            color: var(--ed-text-main);
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
            border: 1px solid var(--ed-border);
        }
        .btn-nav-login:hover {
            background-color: var(--ed-surface-alt);
            border-color: var(--ed-border-hover);
            color: var(--ed-primary);
        }
        .btn-nav-register {
            background-color: var(--ed-primary);
            color: #ffffff;
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
            border: 1px solid var(--ed-primary);
        }
        .btn-nav-register:hover {
            background-color: var(--ed-primary-hover);
            color: #ffffff;
        }

        .btn-lang-toggle {
            background: #ffffff;
            border: 1px solid var(--ed-border);
            color: var(--ed-text-body);
            padding: 5px 10px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: var(--transition);
        }
        .btn-lang-toggle:hover {
            background: var(--ed-surface-alt);
            border-color: var(--ed-border-hover);
        }

        .mobile-menu-btn {
            display: none;
            background: #ffffff;
            color: var(--ed-text-main);
            border: 1px solid var(--ed-border);
            padding: 5px 10px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        /* 4. شريط الإعلانات والتنبيهات المباشر */
        .notice-ticker-bar {
            width: 100%;
            background-color: #eff6ff;
            border-bottom: 1px solid #bfdbfe;
            color: #1e40af;
            padding: 7px 32px;
        }
        .notice-ticker-inner {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12.5px;
        }
        .notice-tag {
            background-color: var(--ed-primary);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .notice-content {
            font-weight: 500;
            flex: 1;
        }

        /* 5. الحاوية العامة على كامل الشاشة */
        .page-container {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            padding: 20px 32px 40px;
        }
        .layout-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            align-items: start;
        }
        .main-content-flow {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .sidebar-flow {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* 6. البطاقات الموحدة (Ed-Cards) */
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
            padding: 10px 16px;
            background-color: #ffffff;
            color: var(--ed-text-main);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--ed-border);
        }
        .ed-card-header h2,
        .ed-card-header h3 {
            font-size: 14px;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--ed-text-main);
        }
        .ed-card-body {
            padding: 16px;
        }

        /* الصندوق الترحيبي الرئيسي */
        .welcome-hero-card {
            background: #ffffff;
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-md);
            padding: 18px 20px;
            border-top: 3px solid var(--ed-primary);
        }
        .welcome-hero-card h2 {
            font-size: 16.5px;
            font-weight: 800;
            color: var(--ed-text-main);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .welcome-hero-card p {
            font-size: 13px;
            color: var(--ed-text-body);
            line-height: 1.7;
            margin-bottom: 14px;
        }
        .hero-features-strip {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            padding-top: 10px;
            border-top: 1px dashed var(--ed-border);
            font-size: 12px;
            color: var(--ed-text-muted);
        }
        .hero-features-strip span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* شبكة الفروع الدراسية */
        .branches-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 14px;
        }
        .branch-box {
            background: #ffffff;
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-md);
            padding: 14px;
            border-top: 3px solid var(--ed-primary);
            transition: var(--transition);
        }
        .branch-box.sci { border-top-color: #2563eb; }
        .branch-box.lit { border-top-color: #059669; }
        .branch-box.bus { border-top-color: #d97706; }
        .branch-box.voc { border-top-color: #dc2626; }
        .branch-box:hover {
            border-color: var(--ed-border-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .branch-box-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .branch-box-head h4 {
            font-size: 14px;
            font-weight: 700;
            color: var(--ed-text-main);
        }
        .branch-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            background: var(--ed-surface-alt);
            color: var(--ed-text-muted);
        }
        .branch-box p {
            font-size: 12.5px;
            color: var(--ed-text-muted);
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .branch-tags-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .branch-tag {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 4px;
            background: var(--ed-primary-soft);
            color: var(--ed-primary);
            border: 1px solid var(--ed-primary-border);
        }

        /* شبكة الخدمات والمميزات */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 14px;
        }
        .feature-card {
            background: var(--ed-surface);
            border: 1px solid var(--ed-border);
            border-radius: var(--radius-sm);
            padding: 14px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .feature-icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            background: var(--ed-primary-soft);
            color: var(--ed-primary);
            display: grid;
            place-items: center;
            font-size: 15px;
            flex-shrink: 0;
            border: 1px solid var(--ed-primary-border);
        }
        .feature-icon-wrap.green {
            background: var(--ed-success-soft);
            color: var(--ed-success);
            border-color: var(--ed-success-border);
        }
        .feature-icon-wrap.gold {
            background: var(--ed-accent-gold-soft);
            color: var(--ed-accent-gold);
            border-color: var(--ed-accent-gold-border);
        }
        .feature-text h4 {
            font-size: 13px;
            font-weight: 700;
            color: var(--ed-text-main);
            margin-bottom: 3px;
        }
        .feature-text p {
            font-size: 12px;
            color: var(--ed-text-muted);
            line-height: 1.55;
        }

        /* جدول كلاسيكي كامل العرض */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        .classic-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .classic-table th,
        .classic-table td {
            padding: 9px 12px;
            border: 1px solid var(--ed-border);
        }
        html[dir="rtl"] .classic-table th,
        html[dir="rtl"] .classic-table td { text-align: right; }
        html[dir="ltr"] .classic-table th,
        html[dir="ltr"] .classic-table td { text-align: left; }

        .classic-table th {
            background-color: var(--ed-surface-alt);
            color: var(--ed-text-main);
            font-weight: 700;
        }
        .classic-table tr:nth-child(even) td {
            background-color: #fafbfc;
        }
        .classic-table tr:hover td {
            background-color: #eff6ff;
        }

        /* مكونات الشريط الجانبي */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }
        .stats-table td {
            padding: 9px 12px;
            border-bottom: 1px solid var(--ed-border);
            font-size: 12.5px;
        }
        .stats-table tr:last-child td {
            border-bottom: none;
        }
        .stat-val {
            font-weight: 700;
            color: var(--ed-primary);
            font-size: 13px;
        }
        html[dir="rtl"] .stat-val { text-align: left; }
        html[dir="ltr"] .stat-val { text-align: right; }

        /* بطاقة المشرف والتواصل بالواتساب */
        .supervisor-profile-card {
            text-align: center;
            padding: 4px 0;
        }
        .supervisor-avatar-lg {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--ed-primary-soft);
            color: var(--ed-primary);
            display: inline-grid;
            place-items: center;
            font-size: 20px;
            margin-bottom: 8px;
            border: 2px solid var(--ed-primary-border);
        }
        .supervisor-profile-card h4 {
            font-size: 14px;
            font-weight: 700;
            color: var(--ed-text-main);
            margin-bottom: 2px;
        }
        .supervisor-profile-card span {
            font-size: 12px;
            color: var(--ed-text-muted);
            display: block;
            margin-bottom: 12px;
        }
        .btn-whatsapp-full {
            background-color: #16a34a;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 700;
            border: 1px solid #15803d;
            transition: var(--transition);
        }
        .btn-whatsapp-full:hover {
            background-color: #15803d;
            color: #ffffff;
        }

        /* بطاقة نصائح دراسية */
        .study-tip-box {
            background: #fffbeb;
            border: 1px solid var(--ed-accent-gold-border);
            border-radius: var(--radius-sm);
            padding: 12px;
            color: #92400e;
        }
        .study-tip-box h5 {
            font-size: 12.5px;
            font-weight: 700;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .study-tip-box p {
            font-size: 12px;
            line-height: 1.55;
            color: #78350f;
            margin: 0;
        }

        /* 7. تذييل الصفحة الشامل - فاتح وأنيق */
        .main-footer {
            width: 100%;
            background-color: #ffffff;
            color: var(--ed-text-muted);
            border-top: 1px solid var(--ed-border);
            padding: 30px 32px 0;
            margin-top: 30px;
        }
        .footer-inner {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 30px;
            padding-bottom: 24px;
        }
        .footer-brand h3 {
            font-size: 16px;
            font-weight: 800;
            color: var(--ed-text-main);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .footer-brand p {
            font-size: 12.5px;
            line-height: 1.7;
            color: var(--ed-text-body);
            max-width: 480px;
        }
        .footer-col h4 {
            font-size: 13px;
            font-weight: 700;
            color: var(--ed-text-main);
            margin-bottom: 10px;
            border-bottom: 1px solid var(--ed-border);
            padding-bottom: 4px;
        }
        .footer-links-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-links-list li {
            margin-bottom: 6px;
        }
        .footer-links-list li a {
            color: var(--ed-text-muted);
            font-size: 12.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .footer-links-list li a:hover {
            color: var(--ed-primary);
        }
        .footer-bottom-bar {
            border-top: 1px solid var(--ed-border);
            padding: 14px 32px;
            background-color: var(--ed-surface-alt);
            margin: 0 -32px;
        }
        .footer-bottom-inner {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 12px;
            color: var(--ed-text-muted);
        }

        /* الريسبنسيف */
        @media (max-width: 1080px) {
            .layout-grid { grid-template-columns: 1fr; }
            .sidebar-flow { order: 2; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
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
                gap: 12px;
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
                top: 48px;
                left: 0;
                right: 0;
                width: 100%;
                background-color: #ffffff;
                flex-direction: column;
                height: auto;
                border-top: 1px solid var(--ed-border);
                box-shadow: 0 8px 16px rgba(0,0,0,0.08);
                z-index: 1100;
                padding: 10px 0;
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
                padding: 10px 20px;
                border-radius: 0;
            }
            .btn-nav-login,
            .btn-nav-register {
                padding: 5px 10px;
                font-size: 11.5px;
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

    <!-- 1. الشريط العلوي الرفيع: معلومات وتاريخ فقط -->
    <div class="top-info-bar">
        <div class="top-bar-inner">
            <div class="top-info-right">
                <span><i class="fa-regular fa-calendar-check" style="color: var(--ed-accent-gold);"></i> {{ date('Y/m/d') }}{{ app()->getLocale() === 'ar' ? ' م' : ' AD' }}</span>
                <span>{{ __('بِسْمِ اللَّـهِ الرَّحْمَـٰنِ الرَّحِيمِ') }}</span>
            </div>
            <div class="top-info-left">
                <span><i class="fa-solid fa-flag" style="color: #dc2626;"></i> {{ __('المنهاج الفلسطيني المعتمد - دورة') }} {{ \App\Models\Setting::tawjihiSession() }} ({{ \App\Models\Setting::academicYear() }}{{ app()->getLocale() === 'ar' ? ' م' : ' AD' }})</span>
            </div>
        </div>
    </div>

    <!-- 2. الترويسة الرسمية على كامل عرض الشاشة - فاتحة وأنيقة -->
    <header class="main-header">
        <div class="header-inner">
            <div class="header-brand">
                <div class="header-logo-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="header-titles">
                    <h1>{{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }} 🇵🇸</h1>
                    <p>{{ __('بوابة ومنظومة الثانوية العامة لدولة فلسطين | المناهج الوزارية ونماذج الاختبارات المعتمدة') }}</p>
                </div>
            </div>

            <div class="header-supervisor-badge">
                <div class="supervisor-avatar">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div class="supervisor-meta">
                    <span class="sup-title">{{ __('المشرف العام على المنظومة:') }}</span>
                    <span class="sup-name">{{ __('أ. أحمد حسين شمالي') }}</span>
                </div>
            </div>
        </div>
    </header>

    <!-- 3. شريط القوائم الرئيسي -->
    <nav class="main-navbar">
        <div class="navbar-inner">
            <button class="mobile-menu-btn" id="mobileMenuToggle" aria-label="{{ __('القائمة') }}">
                <i class="fa-solid fa-bars"></i> {{ __('القائمة') }}
            </button>

            <ul class="nav-menu" id="mainNavMenu">
                <li class="nav-item"><a href="{{ route('home') }}" class="nav-link active"><i class="fa-solid fa-house"></i> {{ __('الرئيسية') }}</a></li>
                <li class="nav-item"><a href="#branches" class="nav-link"><i class="fa-solid fa-book-bookmark"></i> {{ __('فروع التوجيهي') }}</a></li>
                <li class="nav-item"><a href="#features" class="nav-link"><i class="fa-solid fa-list-check"></i> {{ __('خدمات المنصة') }}</a></li>
                <li class="nav-item"><a href="{{ route('courses.catalog') }}" class="nav-link"><i class="fa-solid fa-graduation-cap"></i> {{ __('دليل المقررات') }}</a></li>
                @if(Route::has('tawjihi.calculator'))
                    <li class="nav-item"><a href="{{ route('tawjihi.calculator') }}" class="nav-link"><i class="fa-solid fa-calculator"></i> {{ __('حساب المعدل') }}</a></li>
                @endif
                <li class="nav-item"><a href="{{ route('public.faq') }}" class="nav-link"><i class="fa-solid fa-circle-question"></i> {{ __('الأسئلة الشائعة') }}</a></li>
                <li class="nav-item"><a href="{{ route('public.contact') }}" class="nav-link"><i class="fa-solid fa-phone"></i> {{ __('تواصل مع الإدارة') }}</a></li>
            </ul>

            <div class="nav-actions">
                <!-- زر تبديل اللغة (AR / EN) خالي تماماً من الكلمات العربية في وضع الإنجليزية -->
                @php $currentLocale = app()->getLocale(); @endphp
                <a href="{{ route('lang.switch', $currentLocale === 'ar' ? 'en' : 'ar') }}" 
                   class="btn-lang-toggle"
                   title="{{ $currentLocale === 'ar' ? 'Switch to English' : 'Switch to Arabic' }}">
                    <i class="fa-solid fa-globe" style="color: var(--ed-primary);"></i>
                    <span>{{ $currentLocale === 'ar' ? 'EN' : 'AR' }}</span>
                </a>

                @if(Auth::guard('student')->check() || Auth::check())
                    <a href="{{ route('dashboard') }}" class="btn-nav-register">
                        <i class="fa-solid fa-gauge-high"></i> {{ __('لوحة التحكم') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-nav-login">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> {{ __('تسجيل الدخول') }}
                    </a>
                    <a href="{{ route('students.create') }}" class="btn-nav-register">
                        <i class="fa-solid fa-user-plus"></i> {{ __('تسجيل طالب جديد') }}
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- 4. شريط الإعلانات والتنبيهات المباشر -->
    <div class="notice-ticker-bar">
        <div class="notice-ticker-inner">
            <span class="notice-tag"><i class="fa-solid fa-bullhorn"></i> {{ __('إعلان هام') }}</span>
            <span class="notice-content">
                {{ __('أهلاً وسهلاً بكافة طلبة الثانوية العامة في فلسطين لدورة :session (:year). تم فتح باب التسجيل وتفعيل الشروحات ونماذج الامتحانات بإشراف نخبة من المعلمين المعتمدين.', ['session' => \App\Models\Setting::tawjihiSession(), 'year' => \App\Models\Setting::academicYear()]) }}
            </span>
        </div>
    </div>

    <!-- 5. الحاوية العامة على كامل الشاشة -->
    <div class="page-container">
        <div class="layout-grid">

            <!-- العمود الرئيسي للمحتوى -->
            <main class="main-content-flow">

                <!-- الصندوق الترحيبي -->
                <div class="welcome-hero-card">
                    <h2>
                        <i class="fa-solid fa-graduation-cap" style="color: var(--ed-primary);"></i>
                        {{ __('مرحباً بكم في منصة منارة التوجيهي التعليمية') }}
                    </h2>
                    <p>
                        {{ __('المنظومة الأكاديمية الفلسطينية المتخصصة في مرافقة طلبة الثانوية العامة (التوجيهي) في كافة محافظات فلسطين (القدس، الضفة الغربية، وقطاع غزة). تقدم المنصة شروحات منهجية مبسطة، تدريبات تفاعلية شاملة، ومتابعة دراسية دقيقة لمساعدة كل طالب على نيل أعلى المراتب والتفوق بإذن الله.') }}
                    </p>

                    <div class="hero-features-strip">
                        <span><i class="fa-solid fa-check" style="color: var(--ed-success);"></i> {{ __('منهاج وزارة التربية والتعليم المعتمد') }}</span>
                        <span><i class="fa-solid fa-check" style="color: var(--ed-success);"></i> {{ __('بنك أسئلة وتدريبات تفاعلية شاملة') }}</span>
                        <span><i class="fa-solid fa-check" style="color: var(--ed-success);"></i> {{ __('ملازم وتلاخيص PDF للتحميل') }}</span>
                    </div>
                </div>

                <!-- جدول فروع ومسارات الثانوية العامة المعتمدة -->
                <div class="ed-card" id="branches">
                    <div class="ed-card-header">
                        <h2>
                            <i class="fa-solid fa-book-open" style="color: var(--ed-primary);"></i>
                            {{ __('فروع ومسارات الثانوية العامة المعتمدة (المنهاج الفلسطيني)') }}
                        </h2>
                        <span style="font-size: 11.5px; font-weight: 700; color: var(--ed-primary);">{{ __('تغطية شاملة 100%') }}</span>
                    </div>
                    <div class="ed-card-body" style="padding: 0;">
                        <div class="table-responsive">
                            <table class="academic-table" style="width: 100%; border-collapse: collapse; margin: 0;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                                        <th style="padding: 12px 18px; font-weight: 800; font-size: 0.82rem; color: #1e293b; width: 220px;">{{ __('الفرع والمسار الأكاديمي') }}</th>
                                        <th style="padding: 12px 18px; font-weight: 800; font-size: 0.82rem; color: #1e293b;">{{ __('أبرز المساقات والمباحث المقررة') }}</th>
                                        <th style="padding: 12px 18px; font-weight: 800; font-size: 0.82rem; color: #1e293b; width: 140px; text-align: center;">{{ __('التغطية') }}</th>
                                        <th style="padding: 12px 18px; font-weight: 800; font-size: 0.82rem; color: #1e293b; width: 140px; text-align: center;">{{ __('المنهاج') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 14px 18px;">
                                            <strong style="color: #1e3a8a; font-size: 0.95rem; display: block;">{{ __('الفرع العلمي') }}</strong>
                                            <small style="color: #64748b;">{{ __('مسار علمي ⚛️') }}</small>
                                        </td>
                                        <td style="padding: 14px 18px; font-size: 0.85rem; color: #334155;">
                                            {{ __('الرياضيات (علمي)، الفيزياء، الكيمياء، العلوم الحياتية (الأحياء)، اللغة العربية، والإنجليزية.') }}
                                        </td>
                                        <td style="padding: 14px 18px; text-align: center;">
                                            <span style="display: inline-block; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">{{ __('شامل 100%') }}</span>
                                        </td>
                                        <td style="padding: 14px 18px; text-align: center;">
                                            <a href="{{ route('courses.catalog') }}" class="tbl-btn" style="background: #1e3a8a; color: #fff; padding: 5px 12px; border-radius: 6px; font-size: 0.78rem; text-decoration: none; font-weight: 700;">{{ __('عرض المواد') }}</a>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 14px 18px;">
                                            <strong style="color: #991b1b; font-size: 0.95rem; display: block;">{{ __('الفرع الأدبي') }}</strong>
                                            <small style="color: #64748b;">{{ __('مسار أدبي 📜') }}</small>
                                        </td>
                                        <td style="padding: 14px 18px; font-size: 0.85rem; color: #334155;">
                                            {{ __('اللغة العربية، اللغة الإنجليزية، التاريخ، الجغرافيا، الدراسات الإسلامية، والرياضيات الأدبية.') }}
                                        </td>
                                        <td style="padding: 14px 18px; text-align: center;">
                                            <span style="display: inline-block; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">{{ __('شامل 100%') }}</span>
                                        </td>
                                        <td style="padding: 14px 18px; text-align: center;">
                                            <a href="{{ route('courses.catalog') }}" class="tbl-btn" style="background: #1e3a8a; color: #fff; padding: 5px 12px; border-radius: 6px; font-size: 0.78rem; text-decoration: none; font-weight: 700;">{{ __('عرض المواد') }}</a>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 14px 18px;">
                                            <strong style="color: #065f46; font-size: 0.95rem; display: block;">{{ __('فرع الريادة والأعمال') }}</strong>
                                            <small style="color: #64748b;">{{ __('ريادة واقتصاد 💼') }}</small>
                                        </td>
                                        <td style="padding: 14px 18px; font-size: 0.85rem; color: #334155;">
                                            {{ __('المحاسبة المالية، الإدارة والاقتصاد، المشاريع الصغيرة، الرياضيات التطبيقية، والتكنولوجيا.') }}
                                        </td>
                                        <td style="padding: 14px 18px; text-align: center;">
                                            <span style="display: inline-block; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">{{ __('شامل 100%') }}</span>
                                        </td>
                                        <td style="padding: 14px 18px; text-align: center;">
                                            <a href="{{ route('courses.catalog') }}" class="tbl-btn" style="background: #1e3a8a; color: #fff; padding: 5px 12px; border-radius: 6px; font-size: 0.78rem; text-decoration: none; font-weight: 700;">{{ __('عرض المواد') }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 14px 18px;">
                                            <strong style="color: #334155; font-size: 0.95rem; display: block;">{{ __('الفرع الشرعي والصناعي') }}</strong>
                                            <small style="color: #64748b;">{{ __('مسارات مهنية وشرعية ⚙️') }}</small>
                                        </td>
                                        <td style="padding: 14px 18px; font-size: 0.85rem; color: #334155;">
                                            {{ __('العلوم الإسلامية والحديث والفقه، بجانب الرياضيات والفيزياء التطبيقية والمهنية.') }}
                                        </td>
                                        <td style="padding: 14px 18px; text-align: center;">
                                            <span style="display: inline-block; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">{{ __('معتمد') }}</span>
                                        </td>
                                        <td style="padding: 14px 18px; text-align: center;">
                                            <a href="{{ route('courses.catalog') }}" class="tbl-btn" style="background: #1e3a8a; color: #fff; padding: 5px 12px; border-radius: 6px; font-size: 0.78rem; text-decoration: none; font-weight: 700;">{{ __('عرض المواد') }}</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- صندوق خدمات ومميزات المنظومة -->
                <div class="ed-card" id="features">
                    <div class="ed-card-header">
                        <h3>
                            <i class="fa-solid fa-star" style="color: var(--ed-accent-gold);"></i>
                            {{ __('مميزات المنظومة التعليمية للطالب الفلسطيني') }}
                        </h3>
                        <span style="font-size: 11.5px; font-weight: 700; color: var(--ed-success);">{{ __('بيئة دراسية متكاملة') }}</span>
                    </div>
                    <div class="ed-card-body" style="padding: 0;">
                        <div class="table-responsive">
                            <table class="academic-table" style="width: 100%; border-collapse: collapse; margin: 0;">
                                <tbody>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 14px 18px; width: 45px; text-align: center;">
                                            <i class="fa-solid fa-circle-play" style="color: #1e3a8a; font-size: 1.25rem;"></i>
                                        </td>
                                        <td style="padding: 14px 18px;">
                                            <strong style="color: #0f172a; font-size: 0.92rem; display: block; margin-bottom: 2px;">{{ __('شروحات مرئية منظمة') }}</strong>
                                            <span style="color: #64748b; font-size: 0.82rem;">{{ __('دروس مصورة عالية الجودة مرتبة ترتيباً دقيقاً حسب فهرس ووحدات الكتاب الوزاري الفلسطيني.') }}</span>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 14px 18px; width: 45px; text-align: center;">
                                            <i class="fa-solid fa-file-pen" style="color: #059669; font-size: 1.25rem;"></i>
                                        </td>
                                        <td style="padding: 14px 18px;">
                                            <strong style="color: #0f172a; font-size: 0.92rem; display: block; margin-bottom: 2px;">{{ __('بنك التدريبات والتقييمات الذاتية') }}</strong>
                                            <span style="color: #64748b; font-size: 0.82rem;">{{ __('أسئلة وتدريبات تفاعلية لكل درس ووحدة دراسية لترسيخ القوانين والمفاهيم.') }}</span>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 14px 18px; width: 45px; text-align: center;">
                                            <i class="fa-solid fa-file-pdf" style="color: #d97706; font-size: 1.25rem;"></i>
                                        </td>
                                        <td style="padding: 14px 18px;">
                                            <strong style="color: #0f172a; font-size: 0.92rem; display: block; margin-bottom: 2px;">{{ __('ملازم وتلاخيص PDF') }}</strong>
                                            <span style="color: #64748b; font-size: 0.82rem;">{{ __('ملفات دراسية وتلاخيص مكثفة جاهزة للتحميل والطباعة المنزلية لسرعة مراجعة القوانين والقواعد.') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 14px 18px; width: 45px; text-align: center;">
                                            <i class="fa-solid fa-chalkboard-user" style="color: #4f46e5; font-size: 1.25rem;"></i>
                                        </td>
                                        <td style="padding: 14px 18px;">
                                            <strong style="color: #0f172a; font-size: 0.92rem; display: block; margin-bottom: 2px;">{{ __('إشراف ومتابعة مستمرة') }}</strong>
                                            <span style="color: #64748b; font-size: 0.82rem;">{{ __('تواصل أكاديمي ومتابعة مباشرة من المشرف أ. أحمد شمالي لدعم مسيرة تفوق الطلاب خطوة بخطوة.') }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- صندوق خطوات البدء والدراسة -->
                <div class="ed-card">
                    <div class="ed-card-header">
                        <h3>
                            <i class="fa-solid fa-shoe-prints" style="color: var(--ed-primary);"></i>
                            {{ __('كيف تبدأ رحلة التفوق في المنصة؟ (٣ خطوات ميسرة)') }}
                        </h3>
                    </div>
                    <div class="ed-card-body" style="padding: 0;">
                        <div class="table-responsive">
                            <table class="classic-table">
                                <thead>
                                    <tr>
                                        <th style="width: 100px; text-align: center;">{{ __('المرحلة') }}</th>
                                        <th>{{ __('الإجراء المطلوب من الطالب') }}</th>
                                        <th style="width: 150px; text-align: center;">{{ __('الرابط المباشر') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: center;"><strong>{{ __('الخطوة 1') }}</strong></td>
                                        <td>{{ __('إنشاء حساب طالب جديد وإدخال بيانات الفرع الدراسي والاسم ورقم الهاتف.') }}</td>
                                        <td style="text-align: center;"><a href="{{ route('students.create') }}" style="font-weight: 700; color: var(--ed-success);">{{ __('إنشاء حساب طالب ←') }}</a></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;"><strong>{{ __('الخطوة 2') }}</strong></td>
                                        <td>{{ __('تسجيل الدخول إلى حسابك واختيار المساقات والمواد الدراسية المقررة لفرعك.') }}</td>
                                        <td style="text-align: center;"><a href="{{ route('login') }}" style="font-weight: 700; color: var(--ed-primary);">{{ __('دخول النظام ←') }}</a></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;"><strong>{{ __('الخطوة 3') }}</strong></td>
                                        <td>{{ __('مشاهدة الدروس والشروحات، تحميل التلاخيص والملازم، وحل التدريبات والأنشطة بانتظام.') }}</td>
                                        <td style="text-align: center;"><a href="{{ route('dashboard') }}" style="font-weight: 700;">{{ __('لوحة التحكم ←') }}</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main>

            <!-- العمود الجانبي -->
            <aside class="sidebar-flow">

                <!-- 1. صندوق إحصائيات المنصة الموحدة -->
                <div class="ed-card">
                    <div class="ed-card-header">
                        <h3><i class="fa-solid fa-chart-column" style="color: var(--ed-primary);"></i> {{ __('إحصائيات المنظومة') }}</h3>
                    </div>
                    <div class="ed-card-body" style="padding: 0;">
                        <table class="stats-table">
                            <tbody>
                                <tr>
                                    <td><i class="fa-solid fa-users" style="color: var(--ed-primary); margin-inline-end: 6px;"></i> {{ __('الطلبة المسجلين') }}</td>
                                    <td class="stat-val">{{ number_format($stats['students'] ?? 1200) }} {{ __('طالب') }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid fa-book-bookmark" style="color: var(--ed-success); margin-inline-end: 6px;"></i> {{ __('المساقات المعتمدة') }}</td>
                                    <td class="stat-val" style="color: var(--ed-success);">{{ number_format($stats['subjects'] ?? 18) }} {{ __('مساق') }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid fa-video" style="color: var(--ed-accent-gold); margin-inline-end: 6px;"></i> {{ __('الدروس والشروحات') }}</td>
                                    <td class="stat-val" style="color: var(--ed-accent-gold);">{{ number_format($stats['lessons'] ?? 350) }} {{ __('شرح') }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid fa-file-signature" style="color: #dc2626; margin-inline-end: 6px;"></i> {{ __('النماذج والاختبارات') }}</td>
                                    <td class="stat-val" style="color: #dc2626;">{{ number_format($stats['exams'] ?? 150) }} {{ __('اختبار') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. بطاقة المشرف العام والتواصل المباشر عبر واتساب -->
                <div class="ed-card">
                    <div class="ed-card-header">
                        <h3><i class="fa-solid fa-headset" style="color: var(--ed-success);"></i> {{ __('التواصل المباشر') }}</h3>
                    </div>
                    <div class="ed-card-body">
                        <div class="supervisor-profile-card">
                            <div class="supervisor-avatar-lg">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <h4>{{ __('أ. أحمد حسين شمالي') }}</h4>
                            <span>{{ __('المشرف العام على المنظومة') }}</span>

                            @php
                                $waDigits = '970597694385';
                                $waMsg = urlencode(app()->getLocale() === 'ar' 
                                    ? "السلام عليكم أستاذ أحمد شمالي، أود الاستفسار والتسجيل في منصة منارة التوجيهي." 
                                    : "Hello Mr. Ahmed Shamali, I would like to inquire and register in Menaret Al-Tawjihi platform.");
                            @endphp

                            <a href="https://wa.me/{{ $waDigits }}?text={{ $waMsg }}" target="_blank" class="btn-whatsapp-full">
                                <i class="fa-brands fa-whatsapp fa-lg"></i> {{ __('مراسلة عبر الواتساب (0597694385)') }}
                            </a>

                            <div style="font-size: 11.5px; color: var(--ed-text-muted); margin-top: 10px;">
                                {{ __('رقم بديل: 0567897212 • دولة فلسطين 🇵🇸') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. صندوق إرشادات دراسية للتفوق -->
                <div class="ed-card">
                    <div class="ed-card-header">
                        <h3><i class="fa-solid fa-lightbulb" style="color: var(--ed-accent-gold);"></i> {{ __('إضاءات نحو التفوق') }}</h3>
                    </div>
                    <div class="ed-card-body">
                        <div class="study-tip-box">
                            <h5><i class="fa-solid fa-star"></i> {{ __('سر النجاح في التوجيهي:') }}</h5>
                            <p>
                                {{ __('التركيز اليومي المتواصل، حل أسئلة وتمارين الكتاب المدرسي بدقة، والمتابعة المستمرة تضمن لك ثبات المعلومة والتفوق في نتائج الثانوية.') }}
                            </p>
                        </div>
                    </div>
                </div>

            </aside>

        </div>
    </div>

    <!-- 6. تذييل الصفحة الشامل على كامل العرض - فاتح وأنيق -->
    <footer class="main-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <h3>🇵🇸 {{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }}</h3>
                <p>
                    {{ __('المنظومة الأكاديمية الفلسطينية المعتمدة لطلبة الثانوية العامة (التوجيهي). نسعى إلى تيسير وصول العلم والشروحات النموذجية المتوافقة مع تحديثات وزارة التربية والتعليم لكافة بيوت فلسطين.') }}
                </p>
                <div style="margin-top: 8px; color: var(--ed-primary); font-weight: 700; font-size: 12.5px;">
                    {{ __('إشراف ومتابعة: الأستاذ أحمد حسين شمالي') }}
                </div>
            </div>

            <div class="footer-col">
                <h4>{{ __('روابط سريعة') }}</h4>
                <ul class="footer-links-list">
                    <li><a href="{{ route('home') }}"><i class="fa-solid fa-angle-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i> {{ __('الرئيسية') }}</a></li>
                    <li><a href="{{ route('public.terms') }}"><i class="fa-solid fa-angle-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i> {{ __('الشروط والأحكام') }}</a></li>
                    <li><a href="{{ route('public.privacy') }}"><i class="fa-solid fa-angle-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i> {{ __('سياسة الخصوصية') }}</a></li>
                    <li><a href="{{ route('public.faq') }}"><i class="fa-solid fa-angle-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i> {{ __('الأسئلة الشائعة') }}</a></li>
                    <li><a href="{{ route('public.contact') }}"><i class="fa-solid fa-angle-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i> {{ __('اتصل بنا') }}</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>{{ __('التواصل والدعم الفني') }}</h4>
                <ul class="footer-links-list">
                    <li>
                        <a href="https://wa.me/970597694385" target="_blank" style="color: var(--ed-success); font-weight: 600;">
                            <i class="fa-brands fa-whatsapp"></i> {{ __('واتساب: 0597694385') }}
                        </a>
                    </li>
                    <li>
                        <span style="font-size: 12.5px; color: var(--ed-text-muted);">
                            <i class="fa-solid fa-phone" style="margin-inline-end: 4px;"></i> {{ __('هاتف بديل: 0567897212') }}
                        </span>
                    </li>
                    <li>
                        <span style="font-size: 12.5px; color: var(--ed-text-muted);">
                            <i class="fa-solid fa-location-dot" style="margin-inline-end: 4px;"></i> {{ __('دولة فلسطين 🇵🇸') }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom-bar">
            <div class="footer-bottom-inner">
                <span>{{ __('جميع الحقوق محفوظة © :year - :site_name 🇵🇸 • العام الأكاديمي :academic م', ['year' => date('Y'), 'site_name' => __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')), 'academic' => \App\Models\Setting::academicYear()]) }}</span>
                <span>{{ __('متوافق تماماً مع المنهاج الرسمي لوزارة التربية والتعليم الفلسطينية') }}</span>
            </div>
        </div>
    </footer>

    <!-- سكربت قائمة الموبايل -->
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
