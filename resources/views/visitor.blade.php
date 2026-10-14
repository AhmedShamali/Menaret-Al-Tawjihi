<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <!-- إعدادات تطبيق الويب وتطبيقات الهواتف (PWA / Mobile App Support) -->
    <link rel="manifest" href="/manifest.json?v=20261002-v33">
    <meta name="theme-color" content="#0b3b6f">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Step by Step">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=20261002-v33">
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>بوابة الزوار والطلبة | {{ \App\Models\Setting::get('site_name', 'Step by Step') }}</title>

    @php
        $siteName = \App\Models\Setting::get('site_name', 'Step by Step');
        $siteDesc = \App\Models\Setting::get('seo_description', 'Step by Step - المنصة التعليمية الرقمية الشاملة لطلبة الثانوية العامة (التوجيهي) في فلسطين: شروحات المنهاج الوزاري، حاسبة معدل التوجيهي الدقيقة، بنك الامتحانات الوزارية، دوسيات وملخصات وبطاقات استذكار ذكية لجميع الفروع بإشراف م.أحمد شمالي.');
        $siteKeywords = \App\Models\Setting::get('seo_keywords', 'Step by Step, منصة ستيب, منصة ستيب التعليمية, ستيب, منصة تعليمية, منصات تعليمية فلسطين, موقع تعليمي, تعليمي, شروحات تعليمية, دروس تعليمية, دورات أونلاين فلسطين, توجيهي فلسطين, توجيهي 2026, توجيهي 2025, الثانوية العامة فلسطين, المنهاج الفلسطيني, وزارة التربية والتعليم فلسطين, إنجاز توجيهي, حاسبة معدل التوجيهي, حساب معدل التوجيهي فلسطين, طريقة حساب معدل التوجيهي, امتحانات توجيهي وزارية, اسئلة سنوات سابقة توجيهي, امتحانات تجريبية توجيهي فلسطين, اجابات امتحانات التوجيهي, حلول اسئلة الكتب المدرسية فلسطين, دوسيات توجيهي, ملخصات توجيهي فلسطين, مكثفات توجيهي, بطاقات استذكار توجيهي, دليل القوانين الذهبية توجيهي, توجيهي علمي, توجيهي ادبي, توجيهي صناعي, توجيهي تجاري ريادة وأعمال, توجيهي شرعي, رياضيات توجيهي علمي, فيزياء توجيهي فلسطين, كيمياء توجيهي, احياء توجيهي, عربي توجيهي, لغة انجليزية توجيهي, تاريخ توجيهي, جغرافيا توجيهي, تكنولوجيا توجيهي, م. أحمد شمالي');
        $canonicalUrl = url('/students');
        $siteLogo = \App\Models\Setting::get('site_logo') ? asset(\App\Models\Setting::get('site_logo')) : asset('images/logo.png');
        $gaId = \App\Models\Setting::get('google_analytics_id');
    @endphp

    <meta name="description" content="{{ $siteDesc }}">
    <meta name="keywords" content="{{ $siteKeywords }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="بوابة الزوار والطلبة | {{ $siteName }}">
    <meta property="og:description" content="{{ $siteDesc }}">
    <meta property="og:image" content="{{ $siteLogo }}">
    <meta property="og:site_name" content="{{ $siteName }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="بوابة الزوار والطلبة | {{ $siteName }}">
    <meta name="twitter:description" content="{{ $siteDesc }}">
    <meta name="twitter:image" content="{{ $siteLogo }}">

    @if($gaId)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif

    <!-- الخطوط الأكاديمية الكلاسيكية المعتمدة للمنظومة (Tajawal & Alexandria) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800;900&family=Alexandria:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ==========================================================================
           التصميم الأكاديمي الكلاسيكي الملكي لبوابة الزوار والطلبة
           (Royal Academic Classic Design System - Visitor Portal)
           - ألوان أكاديمية كلاسيكية: الكحلي الملكي (#0b3b6f / #072344)، الذهب الأكاديمي (#d97706)، والعاجي الفاتح (#f8fafc).
           - خطوط متوازنة وواضحة (Tajawal + Alexandria) بدون عناصر نيون أو فوضى بصرية.
           - توافق تام وشامل مع جميع قياسات الشاشات (Mobile 320px -> 4K Desktop).
           ========================================================================== */
        :root {
            --royal-navy: #0b3b6f;
            --royal-navy-dark: #072344;
            --royal-navy-hover: #092c55;
            --royal-navy-soft: #eff6ff;
            --royal-navy-border: #bfdbfe;

            --royal-gold: #d97706;
            --royal-gold-hover: #b45309;
            --royal-gold-soft: #fffbeb;
            --royal-gold-border: #fde68a;

            --royal-emerald: #059669;
            --royal-emerald-soft: #ecfdf5;
            --royal-emerald-border: #a7f3d0;

            --academic-bg: #f8fafc;
            --academic-surface: #ffffff;
            --academic-surface-alt: #f1f5f9;
            --academic-border: #e2e8f0;
            --academic-border-dark: #cbd5e1;

            --academic-text-title: #0f172a;
            --academic-text-body: #334155;
            --academic-text-muted: #64748b;

            --radius-xs: 6px;
            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 18px;

            --shadow-subtle: 0 1px 3px rgba(11, 59, 111, 0.04), 0 1px 2px rgba(11, 59, 111, 0.02);
            --shadow-card: 0 2px 10px rgba(11, 59, 111, 0.05), 0 1px 3px rgba(11, 59, 111, 0.03);
            --shadow-hover: 0 10px 24px rgba(11, 59, 111, 0.08), 0 3px 8px rgba(11, 59, 111, 0.04);
            --shadow-gold: 0 4px 14px rgba(217, 119, 6, 0.22);

            --transition: all 0.22s ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', 'Alexandria', serif, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            width: 100%;
            max-width: 100%;
            min-height: 100vh;
            background-color: var(--academic-bg);
            color: var(--academic-text-body);
            font-size: 14px;
            line-height: 1.65;
            overflow-x: hidden;
        }

        body {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        html[dir="rtl"] body { direction: rtl; text-align: right; }
        html[dir="ltr"] body { direction: ltr; text-align: left; }

        a {
            color: var(--royal-navy);
            text-decoration: none;
            transition: var(--transition);
        }
        a:hover {
            color: var(--royal-gold);
        }

        /* 1. الشريط السيادي العلوي الأكاديمي */
        .royal-top-ribbon {
            width: 100%;
            background: #ffffff;
            color: #1e293b;
            border-bottom: 2px solid var(--royal-gold);
            font-size: 12px;
            padding: 7px 5%;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
        }

        .royal-ribbon-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .ribbon-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            color: var(--academic-text-muted);
        }

        .ribbon-meta .meta-flag {
            color: #dc2626;
            font-size: 0.95rem;
        }

        .ribbon-curriculum-pill {
            background: var(--royal-gold-soft);
            color: var(--royal-gold-hover);
            border: 1px solid var(--royal-gold-border);
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .ribbon-supervisor {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--royal-navy);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* 2. الهيدر الأكاديمي الكلاسيكي */
        header.visitor-header {
            position: sticky;
            top: 0;
            z-index: 990;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--academic-border);
            padding: 12px 5%;
            box-shadow: 0 2px 10px rgba(11, 59, 111, 0.03);
        }

        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .brand-logo {
            font-size: 1.18rem;
            font-weight: 800;
            color: var(--royal-navy-dark);
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-logo:hover {
            color: var(--royal-navy);
        }

        .brand-logo img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: contain;
            background: #ffffff;
            padding: 2px;
            border: 1.5px solid var(--royal-navy-border);
            box-shadow: 0 2px 6px rgba(11, 59, 111, 0.08);
        }

        .brand-text-col {
            display: flex;
            flex-direction: column;
            line-height: 1.25;
        }

        .brand-title {
            font-size: 1.15rem;
            font-weight: 900;
            color: var(--royal-navy-dark);
        }

        .brand-subtitle {
            font-size: 0.72rem;
            color: var(--academic-text-muted);
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-header-link {
            text-decoration: none;
            color: var(--academic-text-body);
            font-size: 0.85rem;
            font-weight: 700;
            padding: 8px 14px;
            border-radius: var(--radius-xs);
            border: 1px solid transparent;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
        }

        .btn-header-link:hover {
            color: var(--royal-navy);
            background: var(--royal-navy-soft);
            border-color: var(--royal-navy-border);
        }

        .btn-visitor-app-download {
            background: var(--royal-emerald-soft);
            color: var(--royal-emerald);
            border: 1px solid var(--royal-emerald-border);
            cursor: pointer;
            font-family: inherit;
        }

        .btn-visitor-app-download:hover {
            background: #dcfce7 !important;
            color: #166534 !important;
            border-color: #86efac;
        }

        .btn-register-header {
            background: var(--royal-navy);
            color: #ffffff !important;
            padding: 8px 18px;
            border-radius: var(--radius-xs);
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            border: 1px solid var(--royal-navy-dark);
            box-shadow: 0 2px 6px rgba(11, 59, 111, 0.15);
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .btn-register-header:hover {
            background: var(--royal-navy-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(11, 59, 111, 0.25);
        }

        /* 3. المحتوى الرئيسي الأكاديمي الكلاسيكي */
        main.visitor-main {
            position: relative;
            z-index: 10;
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 40px 20px 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* وسم الترويسة الأكاديمي الكلاسيكي */
        .hero-crest-badge {
            background: #ffffff;
            color: var(--royal-navy-dark);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.84rem;
            font-weight: 800;
            margin-bottom: 24px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1.5px solid var(--royal-navy-border);
            box-shadow: var(--shadow-subtle);
        }

        .hero-crest-badge i {
            color: var(--royal-gold);
            font-size: 0.95rem;
        }

        /* عنوان البوابة الأكاديمي الفخم */
        h1.hero-title {
            font-size: clamp(1.75rem, 3.8vw, 2.75rem);
            font-weight: 900;
            color: var(--royal-navy-dark);
            margin-bottom: 16px;
            line-height: 1.35;
            text-align: center;
            max-width: 900px;
        }

        h1.hero-title .title-accent {
            color: var(--royal-navy);
            position: relative;
            display: inline-block;
        }

        h1.hero-title .title-accent::after {
            content: '';
            position: absolute;
            bottom: 2px;
            right: 0;
            left: 0;
            height: 4px;
            background: var(--royal-gold);
            border-radius: 2px;
            opacity: 0.85;
        }

        p.hero-desc {
            color: var(--academic-text-muted);
            font-size: clamp(0.92rem, 1.8vw, 1.05rem);
            max-width: 760px;
            margin-bottom: 35px;
            line-height: 1.85;
            text-align: center;
        }

        /* أزرار الإجراءات الرئيسية */
        .cta-buttons-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 14px;
            width: 100%;
            max-width: 900px;
            margin-bottom: 50px;
        }

        .btn-cta-primary {
            background: linear-gradient(135deg, var(--royal-navy) 0%, #1d4ed8 100%);
            color: #ffffff !important;
            padding: 13px 28px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-weight: 800;
            font-size: 0.96rem;
            border: 1px solid var(--royal-navy-dark);
            box-shadow: 0 4px 16px rgba(11, 59, 111, 0.22);
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(11, 59, 111, 0.32);
            color: #ffffff;
        }

        .btn-cta-secondary {
            background: #ffffff;
            color: var(--royal-navy-dark);
            padding: 13px 24px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-weight: 800;
            font-size: 0.94rem;
            border: 1px solid var(--academic-border-dark);
            box-shadow: var(--shadow-card);
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-cta-secondary:hover {
            border-color: var(--royal-navy);
            color: var(--royal-navy);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-cta-secondary i {
            color: var(--royal-gold);
        }

        .btn-cta-green {
            background: var(--royal-emerald-soft);
            color: var(--royal-emerald);
            padding: 13px 24px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-weight: 800;
            font-size: 0.94rem;
            border: 1px solid var(--royal-emerald-border);
            box-shadow: var(--shadow-subtle);
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-cta-green:hover {
            background: var(--royal-emerald);
            color: #ffffff;
            border-color: var(--royal-emerald);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(5, 150, 105, 0.25);
        }

        /* 4. قسم الفروع الأكاديمية الرسمية للثانوية العامة */
        .section-branches-wrap {
            width: 100%;
            margin-bottom: 50px;
        }

        .section-header-classic {
            text-align: center;
            margin-bottom: 28px;
            position: relative;
        }

        .section-header-classic h2 {
            font-size: clamp(1.25rem, 2.5vw, 1.65rem);
            font-weight: 900;
            color: var(--royal-navy-dark);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .section-header-classic h2 i {
            color: var(--royal-gold);
            font-size: 1.15rem;
        }

        .section-header-classic p {
            font-size: 0.88rem;
            color: var(--academic-text-muted);
            font-weight: 500;
        }

        .branches-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            width: 100%;
        }

        .branch-card {
            background: #ffffff;
            border: 1px solid var(--academic-border);
            border-radius: var(--radius-md);
            padding: 22px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            text-decoration: none;
            box-shadow: var(--shadow-card);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .branch-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--royal-navy);
            transition: var(--transition);
        }

        .branch-card:hover {
            border-color: var(--royal-navy-border);
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .branch-card:hover::before {
            height: 4px;
            background: var(--royal-gold);
        }

        .branch-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: var(--royal-navy-soft);
            color: var(--royal-navy);
            display: grid;
            place-items: center;
            font-size: 1.35rem;
            margin-bottom: 14px;
            border: 1px solid var(--royal-navy-border);
            transition: var(--transition);
        }

        .branch-card:hover .branch-icon {
            background: var(--royal-gold-soft);
            color: var(--royal-gold);
            border-color: var(--royal-gold-border);
        }

        .branch-name {
            font-size: 1rem;
            font-weight: 800;
            color: var(--royal-navy-dark);
            margin-bottom: 6px;
        }

        .branch-meta {
            font-size: 0.76rem;
            color: var(--academic-text-muted);
            line-height: 1.5;
            margin-bottom: 14px;
            flex: 1;
        }

        .branch-action-link {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--royal-navy);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: var(--radius-xs);
            background: var(--academic-surface-alt);
            transition: var(--transition);
        }

        .branch-card:hover .branch-action-link {
            background: var(--royal-navy);
            color: #ffffff;
        }

        /* 5. بطاقات المزايا الأكاديمية الكلاسيكية (4 Pillars) */
        .section-features-wrap {
            width: 100%;
            margin-bottom: 45px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            width: 100%;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid var(--academic-border);
            padding: 24px 20px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-card);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            text-align: right;
            position: relative;
        }

        .feature-card:hover {
            border-color: var(--royal-navy-border);
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .feature-icon-crest {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 1.25rem;
            margin-bottom: 16px;
            border: 1px solid transparent;
        }

        .icon-crest-blue {
            background: var(--royal-navy-soft);
            color: var(--royal-navy);
            border-color: var(--royal-navy-border);
        }

        .icon-crest-gold {
            background: var(--royal-gold-soft);
            color: var(--royal-gold);
            border-color: var(--royal-gold-border);
        }

        .icon-crest-emerald {
            background: var(--royal-emerald-soft);
            color: var(--royal-emerald);
            border-color: var(--royal-emerald-border);
        }

        .icon-crest-dark {
            background: #f1f5f9;
            color: var(--royal-navy-dark);
            border-color: var(--academic-border-dark);
        }

        .feature-card h3 {
            font-size: 0.98rem;
            font-weight: 800;
            color: var(--royal-navy-dark);
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .feature-card p {
            font-size: 0.84rem;
            color: var(--academic-text-muted);
            line-height: 1.7;
            margin: 0;
        }

        /* 6. الشريط الإحصائي الأكاديمي الكلاسيكي */
        .academic-stats-banner {
            width: 100%;
            background: #ffffff;
            border: 1px solid var(--academic-border);
            border-radius: var(--radius-md);
            padding: 20px 24px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            box-shadow: var(--shadow-card);
            margin-bottom: 30px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: right;
        }

        .stat-icon {
            font-size: 1.45rem;
            color: var(--royal-gold);
            flex-shrink: 0;
        }

        .stat-text h4 {
            font-size: 0.88rem;
            font-weight: 800;
            color: var(--royal-navy-dark);
            margin: 0 0 2px;
        }

        .stat-text p {
            font-size: 0.76rem;
            color: var(--academic-text-muted);
            margin: 0;
            font-weight: 500;
        }

        /* 7. الفوتر الأكاديمي الكلاسيكي */
        footer.visitor-footer {
            position: relative;
            z-index: 10;
            background: #ffffff;
            border-top: 1px solid var(--academic-border);
            padding: 24px 20px;
            text-align: center;
            color: var(--academic-text-muted);
            font-size: 0.84rem;
            font-weight: 500;
            margin-top: auto;
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .footer-links a {
            color: var(--academic-text-body);
        }

        .footer-links a:hover {
            color: var(--royal-navy);
            text-decoration: underline;
        }

        /* زر العودة إلى أعلى الصفحة الكلاسيكي الأنيق */
        .ed-scroll-top-btn {
            position: fixed;
            bottom: 24px;
            left: 24px;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--royal-navy-dark) 0%, var(--royal-navy) 100%);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 16px rgba(11, 59, 111, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            cursor: pointer;
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(14px) scale(0.92);
            transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        visibility 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        background 0.2s ease,
                        box-shadow 0.2s ease;
        }
        .ed-scroll-top-btn.visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }
        .ed-scroll-top-btn:hover {
            background: linear-gradient(135deg, var(--royal-navy) 0%, #1d4ed8 100%);
            box-shadow: 0 6px 20px rgba(11, 59, 111, 0.35);
            transform: translateY(-3px) scale(1.05);
            color: #ffffff;
        }
        html[dir="ltr"] .ed-scroll-top-btn {
            left: auto;
            right: 24px;
        }

        /* ==========================================================================
           التوافقية التامة والشاملة مع جميع مقاسات الشاشات والأجهزة
           (Comprehensive Responsive Engine)
           ========================================================================== */

        /* أجهزة التابلت والأجهزة المتوسطة (769px - 1024px) */
        @media (max-width: 1024px) {
            .branches-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .academic-stats-banner {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }

        /* الهواتف الذكية والأجهزة الصغيرة (<= 768px) */
        @media (max-width: 768px) {
            .royal-top-ribbon {
                display: none; /* إخفاء الشريط لتوفير المساحة الرأسية على الشاشات الصغيرة */
            }

            header.visitor-header {
                padding: 10px 14px;
            }

            .header-inner {
                gap: 10px;
            }

            .brand-logo img {
                width: 36px;
                height: 36px;
            }

            .brand-title {
                font-size: 1.02rem;
            }

            .brand-subtitle {
                font-size: 0.68rem;
            }

            .header-actions {
                gap: 6px;
            }

            .btn-header-link {
                padding: 6px 9px;
                font-size: 0.78rem;
            }

            .btn-register-header {
                padding: 6px 12px;
                font-size: 0.8rem;
            }

            main.visitor-main {
                padding: 24px 14px calc(76px + env(safe-area-inset-bottom, 0px));
            }

            .hero-crest-badge {
                font-size: 0.76rem;
                padding: 6px 14px;
                margin-bottom: 18px;
            }

            .cta-buttons-container {
                flex-direction: column;
                width: 100%;
                gap: 10px;
                margin-bottom: 36px;
            }

            .btn-cta-primary,
            .btn-cta-secondary,
            .btn-cta-green {
                width: 100%;
                justify-content: center;
                text-align: center;
                padding: 12px 18px;
                font-size: 0.92rem;
            }

            .branches-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .branch-card {
                padding: 16px 12px;
            }

            .branch-icon {
                width: 44px;
                height: 44px;
                font-size: 1.15rem;
                margin-bottom: 10px;
            }

            .branch-name {
                font-size: 0.9rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .academic-stats-banner {
                grid-template-columns: 1fr;
                gap: 14px;
                padding: 16px 18px;
            }

            .ed-scroll-top-btn {
                bottom: calc(76px + env(safe-area-inset-bottom, 0px)) !important;
                left: 14px;
                width: 38px;
                height: 38px;
                font-size: 0.92rem;
                z-index: 990;
            }

            html[dir="ltr"] .ed-scroll-top-btn {
                left: auto;
                right: 14px;
            }

            footer.visitor-footer {
                padding-bottom: calc(78px + env(safe-area-inset-bottom, 0px));
            }
        }

        /* الهواتف فائقة الصغر (<= 480px) */
        @media (max-width: 480px) {
            header.visitor-header .btn-header-link:not([href*="login"]):not(.btn-visitor-app-download) {
                display: none;
            }

            .btn-visitor-app-download span:last-child {
                display: none;
            }

            .brand-subtitle {
                display: none;
            }

            .branches-grid {
                grid-template-columns: 1fr;
            }

            .hero-crest-badge {
                text-align: center;
                line-height: 1.4;
            }
        }

        /* الشاشات الصغيرة جداً (<= 360px مثل iPhone SE القديم) */
        @media (max-width: 360px) {
            .brand-title {
                font-size: 0.92rem;
            }

            .btn-register-header span {
                font-size: 0.74rem;
            }

            .btn-header-link {
                padding: 5px 7px;
            }
        }
    </style>
</head>
<body>

    <!-- 1. الشريط السيادي العلوي الأكاديمي -->
    <div class="royal-top-ribbon">
        <div class="royal-ribbon-inner">
            <div class="ribbon-meta">
                <span><i class="fa-solid fa-flag meta-flag"></i> دولة فلسطين</span>
                <span>•</span>
                <span class="ribbon-curriculum-pill"><i class="fa-solid fa-book-bookmark"></i> المنهاج الوزاري المعتمد</span>
                <span>•</span>
                <span>{{ date('Y/m/d') }}{{ app()->getLocale() === 'ar' ? ' م' : ' AD' }}</span>
            </div>
            <div class="ribbon-supervisor">
                <i class="fa-solid fa-user-tie" style="color: var(--royal-gold);"></i>
                <span>الإشراف العام: <strong>م. أحمد شمالي</strong></span>
            </div>
        </div>
    </div>

    <!-- 2. الهيدر الأكاديمي الكلاسيكي -->
    <header class="visitor-header">
        <div class="header-inner">
            <a href="/" class="brand-logo" title="{{ $siteName }}">
                <img src="{{ $siteLogo }}" alt="{{ $siteName }}" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                <div class="brand-text-col">
                    <span class="brand-title">{{ $siteName }}</span>
                    <span class="brand-subtitle">{{ __('بوابة ومنظومة الثانوية العامة') }}</span>
                </div>
            </a>

            <div class="header-actions">
                <button type="button" 
                        class="btn-header-link btn-visitor-app-download" 
                        onclick="triggerPwaInstall()" 
                        title="{{ __('تحميل وتثبيت تطبيق Step by Step على هاتفك أو حاسوبك') }}"
                        aria-label="{{ __('تحميل التطبيق') }}">
                    <span style="position: relative; display: inline-flex; align-items: center;">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </span>
                    <span>{{ __('تطبيق المنصة') }}</span>
                </button>

                <a href="{{ route('tawjihi.calculator') }}" class="btn-header-link" title="{{ __('حاسبة معدل التوجيهي والقبول الجامعي') }}">
                    <i class="fa-solid fa-calculator" style="color: var(--royal-navy);"></i>
                    <span>{{ __('حاسبة المعدل') }}</span>
                </a>

                <a href="{{ route('courses.catalog') }}" class="btn-header-link" title="{{ __('دليل المقررات وبنك الامتحانات') }}">
                    <i class="fa-solid fa-book-open-reader" style="color: var(--royal-gold);"></i>
                    <span>{{ __('دليل المقررات') }}</span>
                </a>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-register-header">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span>{{ __('لوحة التحكم') }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-header-link">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>{{ __('دخول') }}</span>
                        </a>
                        <a href="{{ route('students.create') }}" class="btn-register-header">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>{{ __('تسجيل طالب') }}</span>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- 3. المحتوى الرئيسي الأكاديمي الكلاسيكي -->
    <main class="visitor-main">
        
        <!-- الوسم الأكاديمي الرسمي -->
        <div class="hero-crest-badge">
            <i class="fa-solid fa-award"></i>
            <span>{{ __('المنظومة الأكاديمية الأولى لطلبة الثانوية العامة (التوجيهي) في فلسطين') }}</span>
        </div>

        <!-- العنوان الفخم -->
        <h1 class="hero-title">
            <span>{{ __('طريقك الراسخ نحو التفوق والتميز في') }}</span><br>
            <span class="title-accent">{{ __('امتحانات الثانوية العامة (التوجيهي)') }}</span>
        </h1>

        <!-- الوصف الأكاديمي الرصين -->
        <p class="hero-desc">
            {{ __('بيئة تعليمية كلاسيكية متكاملة تغطي المنهاج الوزاري الفلسطيني المعتمد لكافة الفروع (العلمي، الأدبي، الريادة والأعمال، الصناعي، والشرعي)، بإشراف هندسي وأكاديمي مباشر، تدعمك ببنك امتحانات الإنجاز الوزارية، بطاقات الاستذكار السريع، وحاسبة التنسيق والقبول الجامعي الدقيقة.') }}
        </p>

        <!-- أزرار الإجراءات الرئيسية -->
        <div class="cta-buttons-container">
            <a href="{{ route('students.create') }}" class="btn-cta-primary">
                <i class="fa-solid fa-user-plus"></i>
                <span>{{ __('إنشاء حساب طالب جديد') }}</span>
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <a href="{{ route('tawjihi.calculator') }}" class="btn-cta-secondary">
                <i class="fa-solid fa-calculator"></i>
                <span>{{ __('حاسبة معدل التوجيهي والتنسيق الجامعي') }}</span>
            </a>

            <a href="{{ route('courses.catalog') }}" class="btn-cta-green">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>{{ __('دليل المقررات والمباحث الدراسية') }}</span>
            </a>
        </div>

        <!-- 4. قسم الفروع الأكاديمية الرسمية للثانوية العامة -->
        <section class="section-branches-wrap" aria-label="{{ __('الفروع الأكاديمية الرسمية') }}">
            <div class="section-header-classic">
                <h2><i class="fa-solid fa-layer-group"></i> {{ __('الفروع الأكاديمية المعتمدة للثانوية العامة') }}</h2>
                <p>{{ __('اختر فرعك الدراسي لاستعراض المباحث، الشروحات، ونماذج الامتحانات الوزارية') }}</p>
            </div>

            <div class="branches-grid">
                <!-- 1. الفرع العلمي -->
                <a href="{{ route('courses.catalog', ['branch' => 'العلمي']) }}" class="branch-card">
                    <div class="branch-icon"><i class="fa-solid fa-atom"></i></div>
                    <div class="branch-name">{{ __('الفرع العلمي') }}</div>
                    <div class="branch-meta">{{ __('الرياضيات، الفيزياء، الكيمياء، الأحياء، واللغات') }}</div>
                    <span class="branch-action-link"><span>{{ __('استعراض المباحث') }}</span> <i class="fa-solid fa-arrow-left"></i></span>
                </a>

                <!-- 2. الفرع الأدبي -->
                <a href="{{ route('courses.catalog', ['branch' => 'الأدبي']) }}" class="branch-card">
                    <div class="branch-icon"><i class="fa-solid fa-feather-pointed"></i></div>
                    <div class="branch-name">{{ __('الفرع الأدبي') }}</div>
                    <div class="branch-meta">{{ __('اللغة العربية، التاريخ، الجغرافيا، والدراسات الأدبية') }}</div>
                    <span class="branch-action-link"><span>{{ __('استعراض المباحث') }}</span> <i class="fa-solid fa-arrow-left"></i></span>
                </a>

                <!-- 3. فرع الريادة والأعمال -->
                <a href="{{ route('courses.catalog', ['branch' => 'الريادة والأعمال']) }}" class="branch-card">
                    <div class="branch-icon"><i class="fa-solid fa-chart-line"></i></div>
                    <div class="branch-name">{{ __('الريادة والأعمال') }}</div>
                    <div class="branch-meta">{{ __('المحاسبة، إدارة المشاريع، الاقتصاد، والعلوم المالية') }}</div>
                    <span class="branch-action-link"><span>{{ __('استعراض المباحث') }}</span> <i class="fa-solid fa-arrow-left"></i></span>
                </a>

                <!-- 4. الفرع الصناعي -->
                <a href="{{ route('courses.catalog', ['branch' => 'الصناعي']) }}" class="branch-card">
                    <div class="branch-icon"><i class="fa-solid fa-gears"></i></div>
                    <div class="branch-name">{{ __('الفرع الصناعي') }}</div>
                    <div class="branch-meta">{{ __('الكهرباء، الإلكترونيات، الرسم الهندسي، والميكانيكا') }}</div>
                    <span class="branch-action-link"><span>{{ __('استعراض المباحث') }}</span> <i class="fa-solid fa-arrow-left"></i></span>
                </a>

                <!-- 5. الفرع الشرعي -->
                <a href="{{ route('courses.catalog', ['branch' => 'الشرعي']) }}" class="branch-card">
                    <div class="branch-icon"><i class="fa-solid fa-mosque"></i></div>
                    <div class="branch-name">{{ __('الفرع الشرعي') }}</div>
                    <div class="branch-meta">{{ __('القرآن وعلومه، الفقه وأصوله، والحديث الشريف') }}</div>
                    <span class="branch-action-link"><span>{{ __('استعراض المباحث') }}</span> <i class="fa-solid fa-arrow-left"></i></span>
                </a>
            </div>
        </section>

        <!-- 5. أركان المنظومة الأكاديمية الكلاسيكية -->
        <section class="section-features-wrap" aria-label="{{ __('أركان التميز الأكاديمي') }}">
            <div class="section-header-classic">
                <h2><i class="fa-solid fa-shield-halved"></i> {{ __('أركان المنظومة الأكاديمية المتكاملة') }}</h2>
                <p>{{ __('أدوات تعليمية مصممة بأعلى المعايير الأكاديمية لمرافقتك نحو قمة التوجيهي') }}</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon-crest icon-crest-blue"><i class="fa-solid fa-book-open-reader"></i></div>
                    <h3>{{ __('المنهاج الفلسطيني المعتمد') }}</h3>
                    <p>{{ __('شروحات مفصلة، ملازم وزارية، وتلخيصات تغطي كافة الوحدات والمباحث بدقة علمية راسخة.') }}</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-crest icon-crest-gold"><i class="fa-solid fa-video"></i></div>
                    <h3>{{ __('دروس مرئية ومتابعة مباشرة') }}</h3>
                    <p>{{ __('شروحات فيديو تفاعلية وملخصات مركزة مع نخبة من أفضل المعلمين والمشرفين الأكاديميين.') }}</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-crest icon-crest-emerald"><i class="fa-solid fa-file-circle-check"></i></div>
                    <h3>{{ __('بنك الامتحانات الوزارية') }}</h3>
                    <p>{{ __('أسئلة وزارية وتجريبية سابقة مع نماذج الإجابات الرسمية لترسيخ الفهم واكتساب مهارة الحل السريع.') }}</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-crest icon-crest-dark"><i class="fa-solid fa-scale-balanced"></i></div>
                    <h3>{{ __('حاسبة التنسيق والقبول') }}</h3>
                    <p>{{ __('احسب معدلك بدقة متناهية وفق معايير وزارة التربية وتعرف على التخصصات الجامعية المتاحة لك.') }}</p>
                </div>
            </div>
        </section>

        <!-- 6. الشريط الإحصائي الأكاديمي الكلاسيكي -->
        <div class="academic-stats-banner">
            <div class="stat-item">
                <i class="fa-solid fa-graduation-cap stat-icon"></i>
                <div class="stat-text">
                    <h4>{{ __('جميع الفروع المعتمدة') }}</h4>
                    <p>{{ __('العلمي، الأدبي، الريادة، والمزيد') }}</p>
                </div>
            </div>
            <div class="stat-item">
                <i class="fa-solid fa-file-lines stat-icon"></i>
                <div class="stat-text">
                    <h4>{{ __('امتحانات وزارية موثقة') }}</h4>
                    <p>{{ __('سنوات سابقة ونماذج تجريبية') }}</p>
                </div>
            </div>
            <div class="stat-item">
                <i class="fa-solid fa-calculator stat-icon"></i>
                <div class="stat-text">
                    <h4>{{ __('حاسبة معدل دقيقة 100%') }}</h4>
                    <p>{{ __('وفق القواعد الرسمية للوزارة') }}</p>
                </div>
            </div>
            <div class="stat-item">
                <i class="fa-solid fa-mobile-screen-button stat-icon"></i>
                <div class="stat-text">
                    <h4>{{ __('تطبيق ويب وتصفح أوفلاين') }}</h4>
                    <p>{{ __('دراسة في أي وقت ومن أي جهاز') }}</p>
                </div>
            </div>
        </div>

    </main>

    <!-- 7. الفوتر الأكاديمي الكلاسيكي -->
    <footer class="visitor-footer">
        <div class="footer-inner">
            <div class="footer-links">
                <a href="{{ route('home') }}">{{ __('الرئيسية') }}</a>
                <span>•</span>
                <a href="{{ route('courses.catalog') }}">{{ __('دليل المقررات') }}</a>
                <span>•</span>
                <a href="{{ route('tawjihi.calculator') }}">{{ __('حاسبة المعدل') }}</a>
                <span>•</span>
                <a href="{{ route('public.contact') }}">{{ __('الدعم والشكاوى') }}</a>
                <span>•</span>
                <a href="{{ route('public.terms') }}">{{ __('الشروط والأحكام') }}</a>
                <span>•</span>
                <a href="{{ route('public.privacy') }}">{{ __('سياسة الخصوصية') }}</a>
            </div>
            <p>{{ __('جميع الحقوق الأكاديمية والفكرية محفوظة') }} © {{ date('Y') }} — {{ $siteName }}</p>
        </div>
    </footer>

    <!-- زر العودة إلى بداية الصفحة الكلاسيكي الأنيق (Scroll to Top Button) -->
    <button type="button" class="ed-scroll-top-btn" id="edScrollTopBtn" aria-label="{{ __('العودة إلى بداية الصفحة') }}" title="{{ __('العودة للأعلى') }}" onclick="scrollToPageTop()">
        <i class="fa-solid fa-chevron-up"></i>
    </button>

    <script>
        function scrollToPageTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        window.addEventListener('scroll', function() {
            const btn = document.getElementById('edScrollTopBtn');
            if (btn) {
                if (window.scrollY > 280) {
                    btn.classList.add('visible');
                } else {
                    btn.classList.remove('visible');
                }
            }
        }, { passive: true });
    </script>

    <!-- المعلم الأكاديمي الذكي العائم -->
    @include('partials.ai_widget')

    <!-- شريط التنقل السفلي وبانر التثبيت لتطبيق الجوال (PWA) -->
    @include('partials.mobile_app_pwa')

</body>
</html>
