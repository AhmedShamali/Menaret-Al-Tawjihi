<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="manifest" href="/manifest.json?v=20261002-v33">
    <meta name="theme-color" content="#ffffff">

    <!-- Apple iOS Mobile App Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Step by Step">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=20261002-v33">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/step-by-step-icon-192.png?v=20261002-v33">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=20261002-v33">
    <link rel="apple-touch-icon" sizes="167x167" href="/icons/step-by-step-icon-192.png?v=20261002-v33">

    @if(\App\Models\Setting::get('site_favicon'))
        <link rel="icon" href="{{ asset(\App\Models\Setting::get('site_favicon')) }}?v=20261002-v33">
    @else
        <link rel="icon" type="image/png" sizes="64x64" href="/favicon.png?v=20261002-v33">
        <link rel="icon" type="image/x-icon" href="/favicon.ico?v=20261002-v33">
    @endif

    <title>{{ __(\App\Models\Setting::get('site_name', 'Step by Step')) }} | {{ __('بوابة ومنظومة الثانوية العامة لدولة فلسطين | المنهاج الوزاري المعتمد') }}</title>

    @php
        $siteName = \App\Models\Setting::get('site_name', 'Step by Step');
        $siteDesc = \App\Models\Setting::get('seo_description', 'Step by Step - المنصة التعليمية الرقمية الشاملة لطلبة الثانوية العامة (التوجيهي) في فلسطين: شروحات المنهاج الوزاري، حاسبة معدل التوجيهي الدقيقة، بنك الامتحانات الوزارية، دوسيات وملخصات وبطاقات استذكار ذكية لجميع الفروع بإشراف م.أحمد شمالي.');
        $siteKeywords = \App\Models\Setting::get('seo_keywords', 'stepvoro, stepvoro.com, منصة stepvoro, ستيبفورو, منصة ستيبفورو, ستيب, منصة ستيب, منصة ستيب التعليمية, ستيب توجيهي, Step by Step, منصة تعليمية, منصات تعليمية فلسطين, موقع تعليمي, تعليمي, شروحات تعليمية, دروس تعليمية, دورات أونلاين فلسطين, توجيهي فلسطين, توجيهي 2026, توجيهي 2025, الثانوية العامة فلسطين, المنهاج الفلسطيني, وزارة التربية والتعليم فلسطين, إنجاز توجيهي, حاسبة معدل التوجيهي, حساب معدل التوجيهي فلسطين, طريقة حساب معدل التوجيهي, امتحانات توجيهي وزارية, اسئلة سنوات سابقة توجيهي, امتحانات تجريبية توجيهي فلسطين, اجابات امتحانات التوجيهي, حلول اسئلة الكتب المدرسية فلسطين, دوسيات توجيهي, ملخصات توجيهي فلسطين, مكثفات توجيهي, بطاقات استذكار توجيهي, دليل القوانين الذهبية توجيهي, توجيهي علمي, توجيهي ادبي, توجيهي صناعي, توجيهي تجاري ريادة وأعمال, توجيهي شرعي, رياضيات توجيهي علمي, فيزياء توجيهي فلسطين, كيمياء توجيهي, احياء توجيهي, عربي توجيهي, لغة انجليزية توجيهي, تاريخ توجيهي, جغرافيا توجيهي, تكنولوجيا توجيهي, منصة ابواب, جو اكاديمي, منصة الاوائل فلسطين, روافد التعليمية, منصة درسك, اساس التعليمية, م. أحمد شمالي');
        $canonicalUrl = url('/');
        $siteLogo = (\App\Models\Setting::get('site_logo') ? asset(\App\Models\Setting::get('site_logo')) : asset('images/logo.png')) . '?v=20261001';
        $googleVerify = \App\Models\Setting::get('google_site_verification');
        $gaId = \App\Models\Setting::get('google_analytics_id');
    @endphp

    <meta name="description" content="{{ $siteDesc }}">
    <meta name="keywords" content="{{ $siteKeywords }}">
    <meta name="author" content="م. أحمد شمالي - Step by Step">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    {{-- Google Site Verification --}}
    @if($googleVerify)
        <meta name="google-site-verification" content="{{ $googleVerify }}">
    @endif

    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $siteName }} | بوابة ومنظومة الثانوية العامة لدولة فلسطين">
    <meta property="og:description" content="{{ $siteDesc }}">
    <meta property="og:image" content="{{ $siteLogo }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : 'ar_AR' }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $canonicalUrl }}">
    <meta name="twitter:title" content="{{ $siteName }} | بوابة ومنظومة الثانوية العامة لدولة فلسطين">
    <meta name="twitter:description" content="{{ $siteDesc }}">
    <meta name="twitter:image" content="{{ $siteLogo }}">

    {{-- JSON-LD Structured Data Schema --}}
    @php
        $welcomeSchema = [
            chr(64) . 'context' => 'https://schema.org',
            chr(64) . 'graph' => [
                [
                    '@type' => 'EducationalOrganization',
                    '@id' => url('/') . '#organization',
                    'name' => $siteName,
                    'alternateName' => ['Stepvoro', 'stepvoro.com', 'منصة ستيبفورو', 'منصة ستيب', 'منصة ستيب التعليمية', 'Step by Step'],
                    'url' => url('/'),
                    'logo' => $siteLogo,
                    'description' => $siteDesc,
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressCountry' => 'PS',
                        'addressRegion' => 'Palestine'
                    ],
                    'founder' => [
                        '@type' => 'Person',
                        'name' => 'م. أحمد شمالي'
                    ]
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => url('/') . '#website',
                    'url' => url('/'),
                    'name' => $siteName,
                    'description' => $siteDesc,
                    'publisher' => [
                        '@id' => url('/') . '#organization'
                    ],
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => url('/catalog') . '?search={search_term_string}',
                        'query-input' => 'required name=search_term_string'
                    ],
                    'inLanguage' => app()->getLocale()
                ]
            ]
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($welcomeSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- Google Analytics 4 (GA4) --}}
    @if($gaId)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif

    <!-- الخطوط الرسمية المعتمدة للمنظومة (Tajawal & Alexandria) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800;900&family=Alexandria:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ==========================================================================
           النظام الأكاديمي الملكي الكلاسيكي (Royal Academic Classic Design System)
           - ألوان سيادية كلاسيكية: الكحلي الملكي العميق (#061329 / #0c2340)، الذهب العتيق (#c28e2b)، والعاجي الفاخر (#f9f8f5).
           - ترويسة ملكية رفيعة موحدة تمنع التكدس وتبرز الهوية الفلسطينية والإشراف الأكاديمي.
           - بطاقات وسجلات أكاديمية أصيلة لكافة فروع الثانوية العامة وخدمات المنصة.
           ========================================================================== */
        :root {
            --royal-navy-dark: #072344;
            --royal-navy: #0b3b6f;
            --royal-navy-hover: #092c55;
            --royal-navy-soft: #eff6ff;
            --royal-navy-border: #bfdbfe;

            --royal-gold: #d97706;
            --royal-gold-hover: #b45309;
            --royal-gold-light: #fef3c7;
            --royal-gold-soft: #fffbeb;
            --royal-gold-border: #fde68a;

            --royal-crimson: #dc2626;
            --royal-emerald: #059669;
            --royal-bronze: #d97706;
            --royal-sapphire: #0284c7;

            --academic-bg: #f8fafc;
            --academic-surface: #ffffff;
            --academic-surface-alt: #f1f5f9;
            --academic-border: #e2e8f0;
            --academic-border-subtle: #f1f5f9;
            --academic-border-dark: #cbd5e1;

            --academic-text-title: #0f172a;
            --academic-text-body: #334155;
            --academic-text-muted: #64748b;

            /* التوافقية العكسية للمتغيرات القديمة */
            --ed-primary: var(--royal-navy);
            --ed-primary-hover: var(--royal-navy-hover);
            --ed-primary-soft: var(--royal-navy-soft);
            --ed-primary-border: var(--royal-navy-border);
            --ed-accent-gold: var(--royal-gold);
            --ed-accent-gold-soft: var(--royal-gold-soft);
            --ed-accent-gold-border: var(--royal-gold-border);
            --ed-success: #16a34a;
            --ed-success-hover: #15803d;
            --ed-success-soft: #ecfdf5;
            --ed-success-border: #a7f3d0;
            --ed-bg: var(--academic-bg);
            --ed-surface: var(--academic-surface);
            --ed-surface-alt: var(--academic-surface-alt);
            --ed-border: var(--academic-border);
            --ed-border-hover: var(--academic-border-dark);
            --ed-text-main: var(--academic-text-title);
            --ed-text-body: var(--academic-text-body);
            --ed-text-muted: var(--academic-text-muted);

            --radius-xs: 6px;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 18px;

            --shadow-subtle: 0 1px 3px rgba(11, 59, 111, 0.04), 0 1px 2px rgba(11, 59, 111, 0.02);
            --shadow-card: 0 2px 10px rgba(11, 59, 111, 0.05), 0 1px 3px rgba(11, 59, 111, 0.03);
            --shadow-hover: 0 10px 28px rgba(11, 59, 111, 0.09), 0 3px 8px rgba(11, 59, 111, 0.04);
            --shadow-gold: 0 4px 15px rgba(217, 119, 6, 0.25);

            --transition: all 0.22s ease-in-out;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', 'Alexandria', serif, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            width: 100%;
            min-height: 100vh;
            background-color: var(--academic-bg);
            color: var(--academic-text-body);
            font-size: 14.5px;
            line-height: 1.65;
            overflow-x: hidden;
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

        /* 1. الشريط السيادي العلوي الأكاديمي (Luminous Academic Top Ribbon - فواتح كلاسيكية فاخرة) */
        .royal-top-ribbon {
            width: 100%;
            background: #ffffff;
            color: #1e293b;
            border-bottom: 2px solid #d97706;
            font-size: 12.5px;
            padding: 8px 32px;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.05);
            position: relative;
            z-index: 1001;
        }
        .royal-ribbon-inner {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .royal-ribbon-right {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .royal-ribbon-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-weight: 800;
            color: #0b3b6f;
            letter-spacing: -0.1px;
            font-size: 12.5px;
        }
        .royal-ribbon-bismillah {
            color: #b45309;
            font-weight: 800;
            font-size: 12.5px;
            padding: 0 10px;
            border-inline-start: 1.5px solid #e2e8f0;
        }
        .royal-ribbon-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .royal-ribbon-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 3px 9px;
            border-radius: var(--radius-sm);
        }
        .royal-supervisor-ribbon-badge {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: 3px 10px;
            border-radius: var(--radius-sm);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 800;
        }
        .royal-supervisor-ribbon-badge i {
            color: #d97706;
        }
        .royal-ribbon-app-btn {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            padding: 3px 10px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: var(--transition);
        }
        .royal-ribbon-app-btn:hover {
            background: #1d4ed8;
            color: #ffffff;
            border-color: #1d4ed8;
            box-shadow: 0 2px 8px rgba(29, 78, 216, 0.25);
        }
        .royal-ribbon-lang-btn {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 3px 9px;
            border-radius: var(--radius-sm);
            font-size: 11.5px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: var(--transition);
        }
        .royal-ribbon-lang-btn:hover {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
        }

        /* 2. شريط القوائم الأكاديمي الملكي (Grand Academic Navbar) */
        .royal-main-navbar {
            width: 100%;
            background-color: var(--academic-surface);
            border-bottom: 1px solid var(--academic-border);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(6, 19, 41, 0.05);
        }
        .royal-navbar-inner {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            padding: 0 32px;
            height: 72px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        .royal-brand-group {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            flex-shrink: 0;
        }
        .royal-crest-seal {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid var(--royal-gold);
            box-shadow: 0 0 0 2px rgba(194, 142, 43, 0.2), 0 3px 8px rgba(6, 19, 41, 0.08);
            display: grid;
            place-items: center;
            overflow: hidden;
            flex-shrink: 0;
            padding: 2px;
        }
        .royal-crest-seal img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        .royal-brand-meta h1 {
            font-size: 20px;
            font-weight: 900;
            color: var(--royal-navy);
            line-height: 1.25;
            letter-spacing: -0.2px;
            margin: 0;
        }
        .royal-brand-meta p {
            font-size: 12px;
            color: var(--academic-text-muted);
            font-weight: 600;
            margin-top: 2px;
            margin-bottom: 0;
        }

        .royal-nav-menu {
            display: flex;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 4px;
            height: 100%;
        }
        .royal-nav-item {
            height: 100%;
            display: flex;
            align-items: center;
        }
        .royal-nav-link {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 13px;
            color: var(--academic-text-body);
            font-size: 13.5px;
            font-weight: 700;
            border-radius: var(--radius-sm);
            transition: var(--transition);
            position: relative;
        }
        .royal-nav-link i {
            color: var(--royal-gold);
            font-size: 13px;
            transition: var(--transition);
        }
        .royal-nav-link:hover {
            color: var(--royal-navy);
            background-color: var(--academic-surface-alt);
        }
        .royal-nav-link:hover i {
            color: var(--royal-navy);
            transform: scale(1.1);
        }
        .royal-nav-link.active {
            color: var(--royal-navy);
            background-color: var(--royal-navy-soft);
            border-bottom: 2px solid var(--royal-navy);
            border-radius: var(--radius-sm) var(--radius-sm) 0 0;
        }
        .royal-nav-link.active i {
            color: var(--royal-navy);
        }

        .royal-nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .btn-royal-login {
            background-color: #ffffff;
            color: var(--royal-navy);
            border: 1.5px solid var(--royal-navy);
            padding: 7px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: var(--transition);
        }
        .btn-royal-login:hover {
            background-color: var(--royal-navy-soft);
            color: var(--royal-navy);
            border-color: var(--royal-navy);
            transform: translateY(-1px);
        }
        .btn-royal-gold {
            background: linear-gradient(135deg, #c28e2b 0%, #e6be65 50%, #c28e2b 100%);
            color: #061329;
            border: 1px solid #a8781d;
            padding: 7px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: var(--shadow-gold);
            transition: var(--transition);
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.4);
        }
        .btn-royal-gold:hover {
            background: linear-gradient(135deg, #a8781d 0%, #d4a742 50%, #a8781d 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(194, 142, 43, 0.38);
        }
        .btn-royal-gold:active {
            transform: translateY(0);
        }

        .royal-mobile-toggle {
            display: none;
            background: #ffffff;
            color: var(--royal-navy);
            border: 1.5px solid var(--academic-border);
            padding: 7px 12px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
        }

        /* 3. شريط آخر الأخبار والتنبيهات المباشرة (Dynamic Royal Academic News Ticker) */
        .royal-ticker-bar {
            width: 100%;
            background: linear-gradient(90deg, #072344 0%, #0b3b6f 50%, #072344 100%);
            border-bottom: 2px solid #d97706;
            color: #ffffff;
            padding: 6px 20px;
            box-shadow: 0 3px 12px rgba(7, 35, 68, 0.15);
            position: relative;
            z-index: 50;
        }
        .royal-ticker-inner {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13.5px;
        }
        .royal-ticker-tag {
            background: linear-gradient(135deg, #e11d48, #be123c);
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 6px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(225, 29, 72, 0.35);
            letter-spacing: 0.2px;
        }
        .pulse-dot {
            width: 7px;
            height: 7px;
            background: #ffffff;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.5);
            animation: pulseDotAnim 1.4s infinite ease-in-out;
        }
        @keyframes pulseDotAnim {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.6; }
        }
        .royal-ticker-viewport {
            flex: 1;
            overflow: hidden;
            position: relative;
            height: 28px;
            display: flex;
            align-items: center;
        }
        .royal-ticker-track {
            display: flex;
            flex-direction: column;
            width: 100%;
            transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .royal-ticker-item {
            height: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #f8fafc;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s;
        }
        .royal-ticker-item:hover {
            color: #fbbf24;
        }
        .royal-ticker-item-badge {
            font-size: 10.5px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 4px;
            flex-shrink: 0;
            letter-spacing: 0.3px;
        }
        .ticker-badge-urgent {
            background: rgba(225, 29, 72, 0.28);
            color: #fda4af;
            border: 1px solid rgba(244, 63, 94, 0.45);
        }
        .ticker-badge-warning {
            background: rgba(217, 119, 6, 0.3);
            color: #fde68a;
            border: 1px solid rgba(251, 191, 36, 0.45);
        }
        .ticker-badge-info {
            background: rgba(2, 132, 199, 0.3);
            color: #bae6fd;
            border: 1px solid rgba(56, 189, 248, 0.45);
        }
        .ticker-badge-success {
            background: rgba(16, 185, 129, 0.3);
            color: #a7f3d0;
            border: 1px solid rgba(52, 211, 153, 0.45);
        }
        .royal-ticker-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .royal-ticker-controls {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }
        .ticker-nav-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e2e8f0;
            width: 26px;
            height: 26px;
            border-radius: 5px;
            display: grid;
            place-items: center;
            cursor: pointer;
            font-size: 10px;
            transition: all 0.15s;
        }
        .ticker-nav-btn:hover {
            background: #d97706;
            color: #ffffff;
            border-color: #d97706;
        }
        .ticker-admin-btn {
            background: rgba(217, 119, 6, 0.25);
            border: 1px solid rgba(217, 119, 6, 0.5);
            color: #fbbf24;
            padding: 3px 10px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
            margin-right: 4px;
        }
        .ticker-admin-btn:hover {
            background: #d97706;
            color: #ffffff;
        }
        @media (max-width: 768px) {
            .royal-ticker-bar {
                padding: 6px 12px;
            }
            .royal-ticker-tag span:last-child {
                display: none;
            }
            .ticker-admin-btn span {
                display: none;
            }
        }

        /* 4. الحاوية والتخطيط العام */
        .page-container {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            padding: 24px 32px 50px;
        }
        .layout-grid {
            display: grid;
            grid-template-columns: 1fr 330px;
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

        /* 5. بطاقات السجل الأكاديمي الملكي (Royal Academic Ledger Cards) */
        .royal-card {
            background-color: var(--academic-surface);
            border: 1px solid var(--academic-border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            transition: var(--transition);
        }
        .royal-card:hover {
            box-shadow: var(--shadow-hover);
            border-color: var(--academic-border-dark);
        }
        .royal-card-header {
            padding: 12px 20px;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--academic-border);
            position: relative;
        }
        .royal-card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--royal-navy) 0%, var(--royal-gold) 50%, var(--royal-navy) 100%);
        }
        .royal-card-header h2,
        .royal-card-header h3 {
            font-size: 15px;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--academic-text-title);
        }
        .royal-card-header-badge {
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: var(--radius-xs);
            background: var(--royal-gold-soft);
            color: #7c570b;
            border: 1px solid var(--royal-gold-border);
        }
        .royal-card-body {
            padding: 20px;
        }

        /* 6. صرح الترحيب الأكاديمي وبوابات الوصول الذكية (Luminous Hub & Gateways Grid) */
        .royal-hero-arch {
            background: #ffffff;
            border: 1px solid var(--academic-border);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
            position: relative;
            overflow: hidden;
            padding: 28px 28px 22px;
            background-image: radial-gradient(circle at 100% 0%, rgba(219, 234, 254, 0.4) 0%, transparent 45%),
                              radial-gradient(circle at 0% 100%, rgba(254, 243, 199, 0.3) 0%, transparent 40%);
        }
        .royal-hero-arch::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #1e40af 0%, #d97706 50%, #059669 100%);
        }
        .royal-hero-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .royal-hero-title-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .royal-hero-crest-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: #eff6ff;
            color: #1e40af;
            border: 1.5px solid #bfdbfe;
            display: grid;
            place-items: center;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(30, 64, 175, 0.12);
        }
        .royal-hero-title-wrap h2 {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            margin: 0;
            line-height: 1.35;
        }
        .royal-hero-badge-tag {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .royal-hero-desc {
            font-size: 13.5px;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 22px;
            max-width: 950px;
        }

        /* شبكة بوابات الدخول السريعة الذكية (4 Interactive Gateways) */
        .royal-hero-gateways-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }
        .royal-gateway-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px 18px;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.24s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }
        .royal-gateway-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.1);
        }
        .gateway-card-student {
            background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
            border-color: #bfdbfe;
        }
        .gateway-card-student:hover {
            border-color: #2563eb;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.16);
        }
        .gateway-card-register {
            background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);
            border-color: #fde68a;
        }
        .gateway-card-register:hover {
            border-color: #d97706;
            box-shadow: 0 10px 24px rgba(217, 119, 6, 0.18);
        }
        .gateway-card-calc {
            background: linear-gradient(135deg, #ffffff 0%, #ecfdf5 100%);
            border-color: #a7f3d0;
        }
        .gateway-card-calc:hover {
            border-color: #059669;
            box-shadow: 0 10px 24px rgba(5, 150, 105, 0.16);
        }
        .gateway-card-offline {
            background: linear-gradient(135deg, #ffffff 0%, #faf5ff 100%);
            border-color: #e9d5ff;
        }
        .gateway-card-offline:hover {
            border-color: #7c3aed;
            box-shadow: 0 10px 24px rgba(124, 58, 237, 0.16);
        }

        .gateway-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }
        .gateway-icon-pod {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 19px;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }
        .royal-gateway-card:hover .gateway-icon-pod {
            transform: scale(1.08);
        }
        .gateway-card-student .gateway-icon-pod {
            background: #1e40af;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.25);
        }
        .gateway-card-register .gateway-icon-pod {
            background: linear-gradient(135deg, #d97706, #f59e0b);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
        }
        .gateway-card-calc .gateway-icon-pod {
            background: #059669;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
        }
        .gateway-card-offline .gateway-icon-pod {
            background: #7c3aed;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);
        }

        .gateway-pill {
            font-size: 11px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
        }
        .gateway-card-student .gateway-pill {
            background: #dbeafe;
            color: #1e40af;
        }
        .gateway-card-register .gateway-pill {
            background: #fef3c7;
            color: #92400e;
        }
        .gateway-card-calc .gateway-pill {
            background: #d1fae5;
            color: #065f46;
        }
        .gateway-card-offline .gateway-pill {
            background: #ede9fe;
            color: #5b21b6;
        }

        .gateway-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 4px;
        }
        .gateway-desc {
            font-size: 12px;
            color: #64748b;
            margin: 0 0 12px;
            line-height: 1.5;
        }
        .gateway-cta-btn {
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: auto;
            transition: gap 0.2s ease;
        }
        .royal-gateway-card:hover .gateway-cta-btn {
            gap: 9px;
        }
        .btn-primary-pulse { color: #1e40af; }
        .btn-gold-pulse { color: #d97706; }
        .btn-emerald-pulse { color: #059669; }
        .btn-sapphire-pulse { color: #7c3aed; }

        /* أشرطة المؤشرات الأكاديمية السريعة أسفل البوابات */
        .royal-hero-quick-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding-top: 14px;
            border-top: 1px dashed #e2e8f0;
        }
        .quick-badge-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        .quick-badge-item i {
            color: #1e40af;
        }

        /* 7. شبكة فروع التوجيهي الملكية (Royal Branches Ledger Grid) */
        .royal-branches-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        .royal-branch-card {
            background: #ffffff;
            border: 1px solid var(--academic-border);
            border-radius: var(--radius-md);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            box-shadow: var(--shadow-subtle);
        }
        .royal-branch-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }
        .royal-branch-card-header {
            padding: 14px 18px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .branch-sci .royal-branch-card-header {
            background: linear-gradient(135deg, #0c2340 0%, #1b4385 100%);
        }
        .branch-lit .royal-branch-card-header {
            background: linear-gradient(135deg, #5b1212 0%, #8c1d1d 100%);
        }
        .branch-bus .royal-branch-card-header {
            background: linear-gradient(135deg, #05412b 0%, #0b6644 100%);
        }
        .branch-voc .royal-branch-card-header {
            background: linear-gradient(135deg, #643507 0%, #9a550d 100%);
        }
        .royal-branch-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .royal-branch-card-title h3 {
            font-size: 15px;
            font-weight: 800;
            margin: 0;
            color: #ffffff;
        }
        .royal-branch-crest-tag {
            font-size: 16px;
        }
        .royal-branch-badge-pill {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: var(--radius-xs);
        }
        .royal-branch-card-body {
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            flex: 1;
            justify-content: space-between;
            gap: 14px;
        }
        .royal-branch-desc {
            font-size: 12.5px;
            color: var(--academic-text-muted);
            line-height: 1.6;
        }
        .royal-branch-pills-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .royal-branch-pill {
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: var(--radius-xs);
            background: var(--academic-surface-alt);
            color: var(--academic-text-body);
            border: 1px solid var(--academic-border);
        }
        .branch-sci .royal-branch-pill {
            background: #f0f5ff;
            color: #1e40af;
            border-color: #dbeafe;
        }
        .branch-lit .royal-branch-pill {
            background: #fef2f2;
            color: #991b1b;
            border-color: #fee2e2;
        }
        .branch-bus .royal-branch-pill {
            background: #ecfdf5;
            color: #065f46;
            border-color: #d1fae5;
        }
        .branch-voc .royal-branch-pill {
            background: #fefce8;
            color: #854d0e;
            border-color: #fef08a;
        }
        .royal-branch-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 10px;
            border-top: 1px dashed var(--academic-border);
        }
        .royal-branch-cta-btn {
            font-size: 12.5px;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }
        .branch-sci .royal-branch-cta-btn {
            background: #1b4385;
            color: #ffffff;
        }
        .branch-sci .royal-branch-cta-btn:hover { background: #0c2340; }
        .branch-lit .royal-branch-cta-btn {
            background: #8c1d1d;
            color: #ffffff;
        }
        .branch-lit .royal-branch-cta-btn:hover { background: #5b1212; }
        .branch-bus .royal-branch-cta-btn {
            background: #0b6644;
            color: #ffffff;
        }
        .branch-bus .royal-branch-cta-btn:hover { background: #05412b; }
        .branch-voc .royal-branch-cta-btn {
            background: #9a550d;
            color: #ffffff;
        }
        .branch-voc .royal-branch-cta-btn:hover { background: #643507; }

        /* 8. شبكة مميزات المنظومة الأكاديمية (Royal Features Grid) */
        .royal-features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        .royal-feature-item {
            background: #ffffff;
            border: 1px solid var(--academic-border);
            border-radius: var(--radius-md);
            padding: 16px 18px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: var(--transition);
        }
        .royal-feature-item:hover {
            border-color: var(--royal-gold);
            transform: translateY(-2px);
            box-shadow: var(--shadow-subtle);
        }
        .royal-feature-icon-pod {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sm);
            background: var(--royal-gold-soft);
            border: 1px solid var(--royal-gold-border);
            color: var(--royal-gold);
            display: grid;
            place-items: center;
            font-size: 18px;
            flex-shrink: 0;
            transition: var(--transition);
        }
        .royal-feature-item:hover .royal-feature-icon-pod {
            background: var(--royal-navy);
            border-color: var(--royal-navy);
            color: var(--royal-gold-light);
        }
        .royal-feature-text h4 {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--academic-text-title);
            margin: 0 0 4px 0;
        }
        .royal-feature-text p {
            font-size: 12.5px;
            color: var(--academic-text-muted);
            margin: 0;
            line-height: 1.6;
        }

        /* 9. مراحل مسار التفوق في ٣ خطوات (Milestone Journey Steps) */
        .royal-steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .royal-step-pod {
            background: #ffffff;
            border: 1px solid var(--academic-border);
            border-radius: var(--radius-md);
            padding: 18px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 12px;
            transition: var(--transition);
            position: relative;
        }
        .royal-step-pod:hover {
            border-color: var(--royal-gold);
            transform: translateY(-2px);
            box-shadow: var(--shadow-card);
        }
        .royal-step-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .royal-step-num {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--royal-navy);
            color: var(--royal-gold-light);
            border: 1px solid var(--royal-gold);
            display: grid;
            place-items: center;
            font-size: 13px;
            font-weight: 900;
        }
        .royal-step-label {
            font-size: 12px;
            font-weight: 800;
            color: var(--royal-gold);
        }
        .royal-step-pod h4 {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--academic-text-title);
            margin: 0 0 4px 0;
        }
        .royal-step-pod p {
            font-size: 12px;
            color: var(--academic-text-muted);
            margin: 0;
            line-height: 1.55;
        }
        .royal-step-btn {
            font-size: 12px;
            font-weight: 800;
            color: var(--royal-navy);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
        }
        .royal-step-btn:hover {
            color: var(--royal-gold);
        }

        /* 10. بطاقات الشريط الجانبي (Sidebar Prestige Cards) */
        .royal-supervisor-card {
            background: #ffffff;
            border: 1px solid var(--academic-border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-card);
            padding: 22px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .royal-supervisor-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--royal-navy) 0%, var(--royal-gold) 50%, var(--royal-navy) 100%);
        }
        .royal-supervisor-avatar-frame {
            width: 68px;
            height: 68px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: var(--royal-gold-soft);
            border: 2px solid var(--royal-gold);
            box-shadow: 0 0 0 3px rgba(194, 142, 43, 0.18);
            display: grid;
            place-items: center;
            font-size: 26px;
            color: var(--royal-navy);
        }
        .royal-supervisor-card h3 {
            font-size: 16px;
            font-weight: 900;
            color: var(--academic-text-title);
            margin-bottom: 2px;
        }
        .royal-supervisor-card .royal-sup-sub {
            font-size: 12px;
            font-weight: 700;
            color: var(--royal-gold);
            display: block;
            margin-bottom: 14px;
        }
        .btn-whatsapp-royal {
            background: linear-gradient(135deg, #15803d 0%, #16a34a 100%);
            color: #ffffff;
            border: 1px solid #166534;
            padding: 9px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
            transition: var(--transition);
        }
        .btn-whatsapp-royal:hover {
            background: linear-gradient(135deg, #166534 0%, #15803d 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(22, 163, 74, 0.35);
        }
        .royal-sup-phones {
            margin-top: 12px;
            font-size: 11.5px;
            color: var(--academic-text-muted);
            line-height: 1.6;
        }

        /* سجل إحصائيات المنظومة */
        .royal-stats-list {
            display: flex;
            flex-direction: column;
        }
        .royal-stat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 16px;
            border-bottom: 1px solid var(--academic-border-subtle);
            font-size: 13px;
        }
        .royal-stat-row:last-child {
            border-bottom: none;
        }
        .royal-stat-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--academic-text-body);
            font-weight: 600;
        }
        .royal-stat-icon-pod {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            font-size: 13px;
            flex-shrink: 0;
            background: var(--royal-navy-soft);
            color: var(--royal-navy);
            border: 1px solid var(--royal-navy-border);
        }
        .royal-stat-icon-pod.gold {
            background: var(--royal-gold-soft);
            color: var(--royal-gold);
            border-color: var(--royal-gold-border);
        }
        .royal-stat-icon-pod.emerald {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }
        .royal-stat-icon-pod.crimson {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }
        .royal-stat-number {
            font-weight: 800;
            font-size: 13.5px;
            color: var(--academic-text-title);
        }

        /* إضاءات التفوق الكلاسيكية */
        .royal-counsel-card {
            background: var(--royal-gold-soft);
            border: 1px solid var(--royal-gold-border);
            border-radius: var(--radius-sm);
            padding: 16px;
            color: #634305;
        }
        .royal-counsel-card h4 {
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 7px;
            color: #452e04;
        }
        .royal-counsel-card p {
            font-size: 12px;
            line-height: 1.65;
            margin: 0;
            color: #5c3e07;
        }

        /* 11. تذييل الصفحة الأكاديمي الملكي */
        .main-footer {
            width: 100%;
            background-color: #ffffff;
            color: var(--academic-text-muted);
            border-top: 2px solid var(--royal-gold-border);
            padding: 36px 32px 0;
            margin-top: 40px;
        }
        .footer-inner {
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 32px;
            padding-bottom: 28px;
        }
        .footer-brand h3 {
            font-size: 17px;
            font-weight: 800;
            color: var(--royal-navy);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .footer-brand p {
            font-size: 13px;
            line-height: 1.7;
            color: var(--academic-text-body);
            max-width: 480px;
        }
        .footer-col h4 {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--royal-navy);
            margin-bottom: 12px;
            border-bottom: 1px solid var(--academic-border);
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
            color: var(--academic-text-muted);
            font-size: 12.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }
        .footer-links-list li a:hover {
            color: var(--royal-gold);
            transform: translateX(-3px);
        }
        .footer-bottom-bar {
            border-top: 1px solid var(--academic-border);
            padding: 16px 32px;
            background-color: var(--academic-surface-alt);
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
            gap: 12px;
            font-size: 12px;
            color: var(--academic-text-muted);
        }

        /* 12. ريسبنسيف التصميم الملكي الشامل لكافة الشاشات */
        @media (max-width: 1080px) {
            .layout-grid { grid-template-columns: 1fr; }
            .sidebar-flow { order: 2; }
            .royal-branches-grid { grid-template-columns: 1fr; }
            .royal-features-grid { grid-template-columns: 1fr; }
            .royal-steps-grid { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 860px) {
            .royal-top-ribbon,
            .royal-navbar-inner,
            .royal-ticker-bar,
            .page-container,
            .main-footer,
            .footer-bottom-bar {
                padding-left: 16px;
                padding-right: 16px;
            }
            .footer-bottom-bar {
                margin: 0 -16px;
            }
            .royal-navbar-inner {
                height: 66px;
                position: relative;
            }
            .royal-mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .royal-nav-menu {
                display: none;
                position: absolute;
                top: 66px;
                left: 0;
                right: 0;
                width: 100%;
                background-color: #ffffff;
                flex-direction: column;
                height: auto;
                border-top: 1px solid var(--academic-border);
                box-shadow: 0 12px 28px rgba(11, 59, 111, 0.12);
                z-index: 1100;
                padding: 10px 0;
            }
            .royal-nav-menu.active {
                display: flex;
            }
            .royal-nav-item {
                width: 100%;
                height: auto;
            }
            .royal-nav-link {
                width: 100%;
                padding: 12px 20px;
                border-radius: 0;
                border-bottom: 1px solid var(--academic-border-subtle);
            }
            .btn-royal-login,
            .btn-royal-gold {
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

        /* ==========================================================
           ROYAL ACADEMIC MOBILE OPTIMIZATIONS (100% UNIFIED & CLEAN)
           تنسيقات الجوال النظيفة والأنيقة لمنع التكدس والتداخل البصري
           ========================================================== */
        @media (max-width: 768px) {
            /* 1. الشريط العلوي الملكي: سطر واحد مدمج وأنيق */
            .royal-top-ribbon {
                padding: 4px 12px !important;
                min-height: 32px !important;
                display: flex !important;
                align-items: center !important;
            }
            .royal-ribbon-inner {
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                justify-content: space-between !important;
                align-items: center !important;
                gap: 8px !important;
            }
            .royal-ribbon-right {
                flex: 1 !important;
                min-width: 0 !important;
                gap: 6px !important;
            }
            .royal-ribbon-badge {
                font-size: 11px !important;
                font-weight: 800 !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                display: inline-flex !important;
                align-items: center !important;
                gap: 5px !important;
                color: #0b3b6f !important;
            }
            .royal-ribbon-bismillah,
            .royal-ribbon-item,
            .royal-supervisor-ribbon-badge,
            .royal-ribbon-app-btn {
                display: none !important;
            }
            .royal-ribbon-left {
                flex-shrink: 0 !important;
                gap: 6px !important;
            }
            .royal-ribbon-lang-btn {
                font-size: 10.5px !important;
                padding: 2px 7px !important;
                border-radius: 6px !important;
                font-weight: 800 !important;
            }

            /* 2. شريط القوائم الرئيسي: مساحة مريحة وزر واحد أنيق بدون مزاحمة */
            .royal-main-navbar {
                position: sticky !important;
                top: 0 !important;
                z-index: 1000 !important;
                box-shadow: 0 2px 10px rgba(6, 19, 41, 0.06) !important;
            }
            .royal-navbar-inner {
                padding: 0 12px !important;
                height: 56px !important;
                gap: 8px !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            .royal-mobile-toggle {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 36px !important;
                height: 36px !important;
                border-radius: 8px !important;
                font-size: 15px !important;
                flex-shrink: 0 !important;
                background: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                color: #0b3b6f !important;
            }
            .royal-brand-group {
                gap: 8px !important;
                flex: 1 !important;
                min-width: 0 !important;
                align-items: center !important;
                text-decoration: none !important;
            }
            .royal-crest-seal {
                width: 36px !important;
                height: 36px !important;
                border-width: 1.5px !important;
                padding: 1.5px !important;
                flex-shrink: 0 !important;
            }
            .royal-brand-meta {
                min-width: 0 !important;
            }
            .royal-brand-meta h1 {
                font-size: 15px !important;
                font-weight: 800 !important;
                color: #0b3b6f !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                margin: 0 !important;
                line-height: 1.2 !important;
            }
            .royal-brand-meta p {
                display: none !important;
            }
            .royal-nav-actions {
                display: flex !important;
                align-items: center !important;
                gap: 6px !important;
                flex-shrink: 0 !important;
            }
            /* إخفاء زر حساب جديد من الشريط العلوي على الجوال لمنع التكدس مع إتاحته في القائمة والهيرو */
            .royal-nav-actions .btn-royal-gold:not([href*="dashboard"]) {
                display: none !important;
            }
            .royal-nav-actions .btn-royal-login,
            .royal-nav-actions .btn-royal-gold[href*="dashboard"] {
                display: inline-flex !important;
                height: 34px !important;
                padding: 0 12px !important;
                font-size: 12px !important;
                font-weight: 800 !important;
                border-radius: 8px !important;
                background: #0b3b6f !important;
                color: #ffffff !important;
                border: 1px solid #0b3b6f !important;
                box-shadow: 0 2px 6px rgba(11, 59, 111, 0.2) !important;
                white-space: nowrap !important;
                gap: 5px !important;
                align-items: center !important;
                text-decoration: none !important;
            }
            .btn-royal-login .label-full,
            .btn-royal-gold .label-full {
                display: none !important;
            }
            .btn-royal-login .label-short,
            .btn-royal-gold .label-short {
                display: inline !important;
            }

            /* 3. شريط الإعلانات والتعاميم: مدمج ومنظم على سطرين */
            .royal-ticker-bar {
                padding: 8px 12px !important;
                background: #fffbeb !important;
                border-bottom: 1px solid #fde68a !important;
            }
            .royal-ticker-inner {
                flex-direction: row !important;
                align-items: center !important;
                gap: 8px !important;
            }
            .royal-ticker-tag {
                font-size: 10.5px !important;
                padding: 2px 8px !important;
                border-radius: 6px !important;
                flex-shrink: 0 !important;
                background: #b45309 !important;
                color: #ffffff !important;
                box-shadow: none !important;
            }
            .royal-ticker-content {
                font-size: 11.5px !important;
                line-height: 1.45 !important;
                color: #92400e !important;
                font-weight: 700 !important;
                display: -webkit-box !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
            }

            /* 4. الحاوية العامة والصرح الترحيبي */
            .page-container {
                padding: 12px 10px 30px !important;
            }
            .layout-grid {
                grid-template-columns: 1fr !important;
                gap: 16px !important;
            }
            .royal-hero-arch {
                padding: 16px 14px !important;
                border-radius: 16px !important;
            }
            .royal-hero-top {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 6px !important;
                margin-bottom: 8px !important;
            }
            .royal-hero-title-wrap {
                gap: 8px !important;
                width: 100% !important;
                align-items: center !important;
            }
            .royal-hero-crest-icon {
                width: 36px !important;
                height: 36px !important;
                font-size: 15px !important;
                border-radius: 10px !important;
                flex-shrink: 0 !important;
            }
            .royal-hero-title-wrap h2 {
                font-size: 15.5px !important;
                line-height: 1.35 !important;
                font-weight: 900 !important;
                color: #0f172a !important;
            }
            .royal-hero-badge-tag {
                font-size: 10.5px !important;
                padding: 2px 9px !important;
                border-radius: 6px !important;
                margin-top: 2px !important;
            }
            .royal-hero-desc {
                font-size: 12.5px !important;
                line-height: 1.6 !important;
                margin-bottom: 14px !important;
                color: #475569 !important;
            }

            /* 5. بوابات الوصول السريعة: شبكة 2x2 أنيقة كالتطبيقات الذكية */
            .royal-hero-gateways-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 8px !important;
                margin-bottom: 12px !important;
            }
            .royal-gateway-card {
                padding: 10px 9px !important;
                border-radius: 12px !important;
                min-height: 82px !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
            }
            .gateway-card-header {
                margin-bottom: 4px !important;
                gap: 4px !important;
            }
            .gateway-icon-pod {
                width: 32px !important;
                height: 32px !important;
                font-size: 14px !important;
                border-radius: 8px !important;
            }
            .gateway-pill {
                font-size: 9.5px !important;
                padding: 1px 5px !important;
                border-radius: 4px !important;
                letter-spacing: -0.2px !important;
            }
            .gateway-title {
                font-size: 12px !important;
                font-weight: 800 !important;
                margin-bottom: 2px !important;
                line-height: 1.3 !important;
            }
            .gateway-desc {
                display: none !important;
            }
            .gateway-cta-btn {
                margin-top: 4px !important;
                padding: 2px 0 !important;
                font-size: 10.5px !important;
                gap: 4px !important;
                font-weight: 800 !important;
            }
            .gateway-cta-btn i {
                font-size: 9px !important;
            }

            .royal-hero-quick-badges {
                flex-direction: column !important;
                gap: 5px !important;
                padding-top: 10px !important;
                margin-top: 8px !important;
                border-top: 1px solid #f1f5f9 !important;
            }
            .quick-badge-item {
                font-size: 11px !important;
                gap: 6px !important;
            }

            /* بطاقات الفروع الأكاديمية */
            .royal-branches-grid {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }
            .royal-card-body {
                padding: 12px !important;
            }
            .royal-branch-card {
                padding: 14px 12px !important;
                border-radius: 12px !important;
            }
            .royal-branch-desc {
                font-size: 12px !important;
                margin-bottom: 10px !important;
            }
            .royal-branch-pills-list {
                gap: 5px !important;
                margin-bottom: 10px !important;
            }
            .royal-branch-pill {
                font-size: 10.5px !important;
                padding: 2px 7px !important;
            }
        }

        .mobile-auth-drawer-item {
            display: none;
        }

        @media (max-width: 992px) {
            .mobile-auth-drawer-item {
                display: block;
                padding: 12px 16px;
                background: #f8fafc;
                border-bottom: 1.5px solid #e2e8f0;
            }
            .drawer-auth-buttons {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
            .drawer-btn-login {
                background: #1e40af;
                color: #ffffff !important;
                padding: 10px 12px;
                border-radius: 8px;
                font-weight: 800;
                font-size: 13px;
                text-align: center;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 7px;
                box-shadow: 0 2px 6px rgba(30, 64, 175, 0.25);
            }
            .drawer-btn-reg {
                background: linear-gradient(135deg, #d97706, #f59e0b);
                color: #ffffff !important;
                padding: 10px 12px;
                border-radius: 8px;
                font-weight: 800;
                font-size: 13px;
                text-align: center;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 7px;
                box-shadow: 0 2px 6px rgba(217, 119, 6, 0.25);
            }
            .drawer-user-pill {
                background: #eff6ff;
                border: 1px solid #bfdbfe;
                color: #1e40af !important;
                padding: 10px 14px;
                border-radius: 8px;
                font-weight: 800;
                display: flex;
                align-items: center;
                gap: 10px;
            }
        }

        /* زر العودة إلى بداية الصفحة الكلاسيكي الفاتح */
        .ed-scroll-top-btn {
            position: fixed;
            bottom: 24px;
            left: 24px;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #ffffff;
            color: #1e3a8a;
            border: 1.5px solid #bfdbfe;
            box-shadow: 0 4px 14px rgba(30, 58, 138, 0.12);
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
            transition: opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                        visibility 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                        background 0.2s ease,
                        box-shadow 0.2s ease,
                        color 0.2s ease;
        }

        .ed-scroll-top-btn.visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }

        .ed-scroll-top-btn:hover {
            background: #1e3a8a;
            color: #ffffff;
            border-color: #1e3a8a;
            box-shadow: 0 6px 20px rgba(30, 58, 138, 0.28);
            transform: translateY(-3px) scale(1.05);
        }

        .ed-scroll-top-btn:active {
            transform: translateY(0) scale(0.96);
        }

        html[dir="ltr"] .ed-scroll-top-btn {
            left: auto;
            right: 24px;
        }

        @media (max-width: 768px) {
            body {
                padding-bottom: calc(85px + env(safe-area-inset-bottom, 0px)) !important;
            }
            .ed-scroll-top-btn {
                bottom: calc(78px + env(safe-area-inset-bottom, 0px)) !important;
                left: 16px;
                width: 38px;
                height: 38px;
                font-size: 0.92rem;
                z-index: 990;
            }
            html[dir="ltr"] .ed-scroll-top-btn {
                left: auto;
                right: 16px;
            }
        }
    </style>
</head>
<body>

    <!-- 1. الشريط السيادي العلوي الأكاديمي (Sovereign Top Ribbon) -->
    <div class="royal-top-ribbon">
        <div class="royal-ribbon-inner">
            <div class="royal-ribbon-right">
                <span class="royal-ribbon-badge">
                    <i class="fa-solid fa-flag" style="color: #ef4444;"></i>
                    {{ __('المنهاج الفلسطيني المعتمد لطلبة الثانوية العامة') }}
                </span>
                <span class="royal-ribbon-bismillah">{{ __('بِسْمِ اللَّـهِ الرَّحْمَـٰنِ الرَّحِيمِ') }}</span>
            </div>
            <div class="royal-ribbon-left">
                <span class="royal-ribbon-item">
                    <i class="fa-regular fa-calendar-check" style="color: var(--royal-gold-light);"></i>
                    {{ date('Y/m/d') }}{{ app()->getLocale() === 'ar' ? ' م' : ' AD' }}
                </span>
                <div class="royal-supervisor-ribbon-badge">
                    <i class="fa-solid fa-award"></i>
                    <span>{{ __('المشرف العام: م.أحمد شمالي') }}</span>
                </div>
                <button type="button" 
                        class="royal-ribbon-app-btn" 
                        onclick="triggerPwaInstall()" 
                        title="{{ __('تثبيت تطبيق Step by Step على هاتفك') }}">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                    <span>{{ __('تطبيق الجوال') }}</span>
                </button>
                @php $currentLocale = app()->getLocale(); @endphp
                <a href="{{ route('lang.switch', $currentLocale === 'ar' ? 'en' : 'ar') }}" 
                   class="royal-ribbon-lang-btn"
                   title="{{ $currentLocale === 'ar' ? 'Switch to English' : 'Switch to Arabic' }}">
                    <i class="fa-solid fa-globe"></i>
                    <span>{{ $currentLocale === 'ar' ? 'EN' : 'AR' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. شريط القوائم الأكاديمي الملكي (Grand Academic Navbar) -->
    <nav class="royal-main-navbar">
        <div class="royal-navbar-inner">
            <button type="button" class="royal-mobile-toggle" id="mobileMenuToggle" aria-label="{{ __('القائمة') }}">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a href="{{ route('home') }}" class="royal-brand-group">
                <div class="royal-crest-seal">
                    <img src="{{ $siteLogo }}" alt="{{ __(\App\Models\Setting::get('site_name', 'Step by Step')) }}">
                </div>
                <div class="royal-brand-meta">
                    <h1>{{ __(\App\Models\Setting::get('site_name', 'Step by Step')) }}</h1>
                    <p>{{ __('بوابة ومنظومة الثانوية العامة لدولة فلسطين') }}</p>
                </div>
            </a>

            <ul class="royal-nav-menu" id="mainNavMenu">
                <li class="royal-nav-item mobile-auth-drawer-item">
                    @if(Auth::guard('student')->check() || Auth::check())
                        <a href="{{ route('dashboard') }}" class="drawer-user-pill">
                            <i class="fa-solid fa-user-circle"></i>
                            <span>{{ __('الانتقال إلى لوحة تحكم حسابي') }}</span>
                            <i class="fa-solid fa-arrow-left" style="margin-right: auto;"></i>
                        </a>
                    @else
                        <div class="drawer-auth-buttons">
                            <a href="{{ route('login') }}" class="drawer-btn-login">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i> {{ __('تسجيل الدخول') }}
                            </a>
                            <a href="{{ route('students.create') }}" class="drawer-btn-reg">
                                <i class="fa-solid fa-user-plus"></i> {{ __('حساب جديد') }}
                            </a>
                        </div>
                    @endif
                </li>
                <li class="royal-nav-item"><a href="{{ route('home') }}" class="royal-nav-link active"><i class="fa-solid fa-house-chimney"></i> {{ __('الرئيسية') }}</a></li>
                <li class="royal-nav-item"><a href="#branches" class="royal-nav-link"><i class="fa-solid fa-book-bookmark"></i> {{ __('فروع التوجيهي') }}</a></li>
                <li class="royal-nav-item"><a href="#features" class="royal-nav-link"><i class="fa-solid fa-award"></i> {{ __('خدمات المنصة') }}</a></li>
                <li class="royal-nav-item"><a href="{{ route('courses.catalog') }}" class="royal-nav-link"><i class="fa-solid fa-graduation-cap"></i> {{ __('دليل المقررات') }}</a></li>
                @if(Route::has('tawjihi.calculator'))
                    <li class="royal-nav-item"><a href="{{ route('tawjihi.calculator') }}" class="royal-nav-link"><i class="fa-solid fa-calculator"></i> {{ __('حساب المعدل') }}</a></li>
                @endif
                <li class="royal-nav-item"><a href="{{ route('public.faq') }}" class="royal-nav-link"><i class="fa-solid fa-circle-question"></i> {{ __('الأسئلة الشائعة') }}</a></li>
                <li class="royal-nav-item"><a href="{{ route('public.contact') }}" class="royal-nav-link"><i class="fa-solid fa-phone"></i> {{ __('تواصل مع الإدارة') }}</a></li>
            </ul>

            <div class="royal-nav-actions">
                @if(Auth::guard('student')->check() || Auth::check())
                    <a href="{{ route('dashboard') }}" class="btn-royal-gold" title="{{ __('لوحة التحكم') }}">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span class="label-full">{{ __('لوحة التحكم') }}</span>
                        <span class="label-short" style="display: none;">{{ __('لوحتي') }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-royal-login" title="{{ __('تسجيل الدخول إلى حسابك') }}">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span class="label-full">{{ __('تسجيل الدخول') }}</span>
                        <span class="label-short" style="display: none;">{{ __('دخول') }}</span>
                    </a>
                    <a href="{{ route('students.create') }}" class="btn-royal-gold" title="{{ __('تسجيل طالب جديد') }}">
                        <i class="fa-solid fa-user-plus"></i>
                        <span class="label-full">{{ __('تسجيل طالب جديد') }}</span>
                        <span class="label-short" style="display: none;">{{ __('حساب جديد') }}</span>
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- 3. شريط آخر الأخبار والتنبيهات المباشرة (Dynamic Royal Academic News Ticker) -->
    @php
        $activeNewsItems = \App\Services\NewsTickerService::getActive();
    @endphp
    @if(!empty($activeNewsItems))
    <div class="royal-ticker-bar" id="royalTickerBar">
        <div class="royal-ticker-inner">
            <div class="royal-ticker-tag">
                <span class="pulse-dot"></span>
                <i class="fa-solid fa-bullhorn" style="font-size: 11px;"></i>
                <span>{{ __('آخر الأخبار') }}</span>
            </div>

            <div class="royal-ticker-viewport" id="tickerViewport">
                <div class="royal-ticker-track" id="tickerTrack">
                    @foreach($activeNewsItems as $nIdx => $nItem)
                        @php
                            $nType = $nItem['type'] ?? 'urgent';
                            $badgeClass = 'ticker-badge-' . (in_array($nType, ['urgent', 'warning', 'info', 'success']) ? $nType : 'urgent');
                            $hasUrl = !empty($nItem['url']);
                        @endphp
                        @if($hasUrl)
                            <a href="{{ $nItem['url'] }}" target="_blank" class="royal-ticker-item" title="{{ $nItem['text'] }}">
                                <span class="royal-ticker-item-badge {{ $badgeClass }}">{{ $nItem['badge'] ?? 'عاجل' }}</span>
                                <span class="royal-ticker-text">{{ $nItem['text'] }}</span>
                                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px; opacity: 0.65; margin-right: 4px;"></i>
                            </a>
                        @else
                            <div class="royal-ticker-item" title="{{ $nItem['text'] }}">
                                <span class="royal-ticker-item-badge {{ $badgeClass }}">{{ $nItem['badge'] ?? 'عاجل' }}</span>
                                <span class="royal-ticker-text">{{ $nItem['text'] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="royal-ticker-controls">
                @if(count($activeNewsItems) > 1)
                    <button type="button" class="ticker-nav-btn" onclick="prevTickerItem()" title="{{ __('الخبر السابق') }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                    <button type="button" class="ticker-nav-btn" onclick="nextTickerItem()" title="{{ __('الخبر التالي') }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                @endif

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('admin.news.index') }}" class="ticker-admin-btn" title="{{ __('إدارة شريط الأخبار') }}">
                        <i class="fa-solid fa-gear"></i>
                        <span>{{ __('إدارة الأخبار') }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- 4. الحاوية العامة على كامل الشاشة -->
    <div class="page-container">
        <div class="layout-grid">

            <!-- العمود الرئيسي للمحتوى الأكاديمي -->
            <main class="main-content-flow">

                <!-- صرح الترحيب الأكاديمي وبوابات الوصول الذكية -->
                <div class="royal-hero-arch">
                    <div class="royal-hero-top">
                        <div class="royal-hero-title-wrap">
                            <div class="royal-hero-crest-icon">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <div>
                                <h2>{{ __('منظومة Step by Step | المنهاج الفلسطيني المعتمد') }}</h2>
                            </div>
                        </div>
                        <span class="royal-hero-badge-tag">
                            <i class="fa-solid fa-award"></i>
                            {{ __('الثانوية العامة (التوجيهي) 2026 م') }}
                        </span>
                    </div>

                    <p class="royal-hero-desc">
                        {{ __('بوابتك المتخصصة للتفوق والدرجات العالية في الثانوية العامة في كافة محافظات فلسطين (القدس، الضفة الغربية، وقطاع غزة). شروحات مصورة نموذجية، بنك الامتحانات الوزارية المحلولة، دوسيات وتلاخيص PDF، وحاسبة المعدل الوزارية بإشراف م.أحمد شمالي.') }}
                    </p>

                    <!-- بوابات الدخول السريعة الذكية (4 Interactive Gateways) -->
                    <div class="royal-hero-gateways-grid">

                        <!-- 1. بوابة تسجيل الدخول / حسابي -->
                        @if(Auth::guard('student')->check() || Auth::check())
                            <a href="{{ route('dashboard') }}" class="royal-gateway-card gateway-card-student">
                                <div class="gateway-card-header">
                                    <div class="gateway-icon-pod">
                                        <i class="fa-solid fa-gauge-high"></i>
                                    </div>
                                    <span class="gateway-pill">{{ __('متصل الآن 🟢') }}</span>
                                </div>
                                <h3 class="gateway-title">{{ __('لوحة تحكم حسابي') }}</h3>
                                <p class="gateway-desc">{{ __('متابعة تقدمك الدراسي، الدروس المتبقية، والتقييمات الذاتية.') }}</p>
                                <span class="gateway-cta-btn btn-primary-pulse">
                                    <span>{{ __('الدخول للوحة التحكم') }}</span>
                                    <i class="fa-solid fa-arrow-left"></i>
                                </span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="royal-gateway-card gateway-card-student">
                                <div class="gateway-card-header">
                                    <div class="gateway-icon-pod">
                                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                    </div>
                                    <span class="gateway-pill">{{ __('دخول فوري ⚡') }}</span>
                                </div>
                                <h3 class="gateway-title">{{ __('بوابة تسجيل الدخول') }}</h3>
                                <p class="gateway-desc">{{ __('ادخل إلى حسابك لمتابعة حصصك، اختباراتك، والمواد المسجلة.') }}</p>
                                <span class="gateway-cta-btn btn-primary-pulse">
                                    <span>{{ __('تسجيل الدخول لحسابك') }}</span>
                                    <i class="fa-solid fa-arrow-left"></i>
                                </span>
                            </a>
                        @endif

                        <!-- 2. إنشاء حساب طالب جديد -->
                        <a href="{{ route('students.create') }}" class="royal-gateway-card gateway-card-register">
                            <div class="gateway-card-header">
                                <div class="gateway-icon-pod">
                                    <i class="fa-solid fa-user-plus"></i>
                                </div>
                                <span class="gateway-pill">{{ __('مجاناً ✨') }}</span>
                            </div>
                            <h3 class="gateway-title">{{ __('تسجيل طالب جديد') }}</h3>
                            <p class="gateway-desc">{{ __('انضم لآلاف طلبة التوجيهي واستفد من الشروحات والملخصات المعتمدة.') }}</p>
                            <span class="gateway-cta-btn btn-gold-pulse">
                                <span>{{ __('فتح حساب طالب جديد') }}</span>
                                <i class="fa-solid fa-arrow-left"></i>
                            </span>
                        </a>

                        <!-- 3. حاسبة معدل التوجيهي الوزارية -->
                        <a href="{{ route('tawjihi.calculator') }}" class="royal-gateway-card gateway-card-calc">
                            <div class="gateway-card-header">
                                <div class="gateway-icon-pod">
                                    <i class="fa-solid fa-calculator"></i>
                                </div>
                                <span class="gateway-pill">{{ __('نظام 2026 🎯') }}</span>
                            </div>
                            <h3 class="gateway-title">{{ __('حاسبة المعدل الوزارية') }}</h3>
                            <p class="gateway-desc">{{ __('احتساب دقيق ومعتمد لمعدلك وفق معايير وزارة التربية والتعليم لكافة الفروع.') }}</p>
                            <span class="gateway-cta-btn btn-emerald-pulse">
                                <span>{{ __('احسب معدلك الآن') }}</span>
                                <i class="fa-solid fa-arrow-left"></i>
                            </span>
                        </a>

                        <!-- 4. المكتبة الأوفلاين والدروس المحفوظة بدون إنترنت -->
                        <a href="{{ route('offline.videos') }}" class="royal-gateway-card gateway-card-offline">
                            <div class="gateway-card-header">
                                <div class="gateway-icon-pod">
                                    <i class="fa-solid fa-cloud-arrow-down"></i>
                                </div>
                                <span class="gateway-pill">{{ __('بدون نت 🚀') }}</span>
                            </div>
                            <h3 class="gateway-title">{{ __('دروسي المحفوظة أوفلاين') }}</h3>
                            <p class="gateway-desc">{{ __('شاهد الحصص والشروحات المحملة على هاتفك حتى عند انقطاع الإنترنت.') }}</p>
                            <span class="gateway-cta-btn btn-sapphire-pulse">
                                <span>{{ __('عرض الحصص المحملة') }}</span>
                                <i class="fa-solid fa-arrow-left"></i>
                            </span>
                        </a>

                    </div>

                    <!-- أشرطة المؤشرات الأكاديمية السريعة أسفل البوابات -->
                    <div class="royal-hero-quick-badges">
                        <div class="quick-badge-item">
                            <i class="fa-solid fa-book-bookmark"></i>
                            <span>{{ __('المنهاج الفلسطيني المعتمد (القدس، الضفة، غزة)') }}</span>
                        </div>
                        <div class="quick-badge-item">
                            <i class="fa-solid fa-chalkboard-user"></i>
                            <span>{{ __('إشراف وتدريس نخبة معلمين متميزين') }}</span>
                        </div>
                        <div class="quick-badge-item">
                            <i class="fa-solid fa-file-pdf"></i>
                            <span>{{ __('دوسيات وامتحانات وزارية محلولة 100%') }}</span>
                        </div>
                    </div>
                </div>

                <!-- فروع ومسارات الثانوية العامة المعتمدة (بطاقات الفروع الملكية) -->
                <div class="royal-card" id="branches">
                    <div class="royal-card-header">
                        <h2>
                            <i class="fa-solid fa-book-open" style="color: var(--royal-navy);"></i>
                            {{ __('فروع ومسارات الثانوية العامة المعتمدة (المنهاج الفلسطيني)') }}
                        </h2>
                        <span class="royal-card-header-badge">{{ __('تغطية وزارية شاملة 100%') }}</span>
                    </div>
                    <div class="royal-card-body">
                        <div class="royal-branches-grid">

                            <!-- 1. الفرع العلمي -->
                            <div class="royal-branch-card branch-sci">
                                <div class="royal-branch-card-header">
                                    <div class="royal-branch-card-title">
                                        <span class="royal-branch-crest-tag">⚛️</span>
                                        <h3>{{ __('الفرع العلمي') }}</h3>
                                    </div>
                                    <span class="royal-branch-badge-pill">{{ __('شامل 100%') }}</span>
                                </div>
                                <div class="royal-branch-card-body">
                                    <p class="royal-branch-desc">
                                        {{ __('المسار العلمي والهندسي والطبي، يركز على الفهم العميق للعلوم التجريبية والتفكير الرياضي المتقدم.') }}
                                    </p>
                                    <div class="royal-branch-pills-list">
                                        <span class="royal-branch-pill">{{ __('الرياضيات (علمي)') }}</span>
                                        <span class="royal-branch-pill">{{ __('الفيزياء') }}</span>
                                        <span class="royal-branch-pill">{{ __('الكيمياء') }}</span>
                                        <span class="royal-branch-pill">{{ __('العلوم الحياتية') }}</span>
                                        <span class="royal-branch-pill">{{ __('اللغة العربية') }}</span>
                                        <span class="royal-branch-pill">{{ __('اللغة الإنجليزية') }}</span>
                                    </div>
                                    <div class="royal-branch-card-footer">
                                        <span style="font-size: 11.5px; font-weight: 700; color: #1e40af;">{{ __('المنهاج الفلسطيني المعتمد') }}</span>
                                        <a href="{{ route('courses.catalog', ['branch' => 'scientific']) }}" class="royal-branch-cta-btn">
                                            <span>{{ __('استعراض المواد') }}</span>
                                            <i class="fa-solid fa-arrow-left"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. الفرع الأدبي -->
                            <div class="royal-branch-card branch-lit">
                                <div class="royal-branch-card-header">
                                    <div class="royal-branch-card-title">
                                        <span class="royal-branch-crest-tag">📜</span>
                                        <h3>{{ __('الفرع الأدبي') }}</h3>
                                    </div>
                                    <span class="royal-branch-badge-pill">{{ __('شامل 100%') }}</span>
                                </div>
                                <div class="royal-branch-card-body">
                                    <p class="royal-branch-desc">
                                        {{ __('مسار العلوم الإنسانية واللغات، يركز على الأدب والتاريخ والجغرافيا والعلوم الاجتماعية.') }}
                                    </p>
                                    <div class="royal-branch-pills-list">
                                        <span class="royal-branch-pill">{{ __('اللغة العربية') }}</span>
                                        <span class="royal-branch-pill">{{ __('اللغة الإنجليزية') }}</span>
                                        <span class="royal-branch-pill">{{ __('التاريخ') }}</span>
                                        <span class="royal-branch-pill">{{ __('الجغرافيا') }}</span>
                                        <span class="royal-branch-pill">{{ __('الدراسات الإسلامية') }}</span>
                                        <span class="royal-branch-pill">{{ __('الرياضيات الأدبية') }}</span>
                                    </div>
                                    <div class="royal-branch-card-footer">
                                        <span style="font-size: 11.5px; font-weight: 700; color: #991b1b;">{{ __('المنهاج الفلسطيني المعتمد') }}</span>
                                        <a href="{{ route('courses.catalog', ['branch' => 'literary']) }}" class="royal-branch-cta-btn">
                                            <span>{{ __('استعراض المواد') }}</span>
                                            <i class="fa-solid fa-arrow-left"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. فرع الريادة والأعمال -->
                            <div class="royal-branch-card branch-bus">
                                <div class="royal-branch-card-header">
                                    <div class="royal-branch-card-title">
                                        <span class="royal-branch-crest-tag">💼</span>
                                        <h3>{{ __('فرع الريادة والأعمال') }}</h3>
                                    </div>
                                    <span class="royal-branch-badge-pill">{{ __('شامل 100%') }}</span>
                                </div>
                                <div class="royal-branch-card-body">
                                    <p class="royal-branch-desc">
                                        {{ __('مسار العلوم الإدارية والاقتصادية والمشاريع الريادية الحديثة وتطبيقات التجارة.') }}
                                    </p>
                                    <div class="royal-branch-pills-list">
                                        <span class="royal-branch-pill">{{ __('المحاسبة المالية') }}</span>
                                        <span class="royal-branch-pill">{{ __('الإدارة والاقتصاد') }}</span>
                                        <span class="royal-branch-pill">{{ __('المشاريع الصغيرة') }}</span>
                                        <span class="royal-branch-pill">{{ __('الرياضيات التطبيقية') }}</span>
                                        <span class="royal-branch-pill">{{ __('التكنولوجيا') }}</span>
                                    </div>
                                    <div class="royal-branch-card-footer">
                                        <span style="font-size: 11.5px; font-weight: 700; color: #065f46;">{{ __('المنهاج الفلسطيني المعتمد') }}</span>
                                        <a href="{{ route('courses.catalog', ['branch' => 'business']) }}" class="royal-branch-cta-btn">
                                            <span>{{ __('استعراض المواد') }}</span>
                                            <i class="fa-solid fa-arrow-left"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. الفرع الشرعي والصناعي والمهني -->
                            <div class="royal-branch-card branch-voc">
                                <div class="royal-branch-card-header">
                                    <div class="royal-branch-card-title">
                                        <span class="royal-branch-crest-tag">⚙️</span>
                                        <h3>{{ __('الفرع الشرعي والصناعي') }}</h3>
                                    </div>
                                    <span class="royal-branch-badge-pill">{{ __('معتمد') }}</span>
                                </div>
                                <div class="royal-branch-card-body">
                                    <p class="royal-branch-desc">
                                        {{ __('المسارات الشرعية المتخصصة والمساقات التطبيقية والمهنية المعتمدة وزارياً.') }}
                                    </p>
                                    <div class="royal-branch-pills-list">
                                        <span class="royal-branch-pill">{{ __('العلوم الإسلامية والفقه') }}</span>
                                        <span class="royal-branch-pill">{{ __('الحديث الشريف') }}</span>
                                        <span class="royal-branch-pill">{{ __('الرياضيات التطبيقية') }}</span>
                                        <span class="royal-branch-pill">{{ __('الفيزياء المهنية') }}</span>
                                    </div>
                                    <div class="royal-branch-card-footer">
                                        <span style="font-size: 11.5px; font-weight: 700; color: #854d0e;">{{ __('المنهاج الفلسطيني المعتمد') }}</span>
                                        <a href="{{ route('courses.catalog', ['branch' => 'vocational']) }}" class="royal-branch-cta-btn">
                                            <span>{{ __('استعراض المواد') }}</span>
                                            <i class="fa-solid fa-arrow-left"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- صندوق مميزات وخدمات المنظومة التعليمية -->
                <div class="royal-card" id="features">
                    <div class="royal-card-header">
                        <h3>
                            <i class="fa-solid fa-star" style="color: var(--royal-gold);"></i>
                            {{ __('مميزات المنظومة التعليمية للطالب الفلسطيني') }}
                        </h3>
                        <span class="royal-card-header-badge">{{ __('بيئة دراسية متكاملة') }}</span>
                    </div>
                    <div class="royal-card-body">
                        <div class="royal-features-grid">
                            <div class="royal-feature-item">
                                <div class="royal-feature-icon-pod">
                                    <i class="fa-solid fa-circle-play"></i>
                                </div>
                                <div class="royal-feature-text">
                                    <h4>{{ __('شروحات مرئية نموذجية') }}</h4>
                                    <p>{{ __('دروس مصورة عالية الجودة مرتبة ترتيباً دقيقاً حسب فهرس ووحدات الكتاب الوزاري الفلسطيني.') }}</p>
                                </div>
                            </div>

                            <div class="royal-feature-item">
                                <div class="royal-feature-icon-pod">
                                    <i class="fa-solid fa-file-signature"></i>
                                </div>
                                <div class="royal-feature-text">
                                    <h4>{{ __('بنك التدريبات والتقييمات الذاتية') }}</h4>
                                    <p>{{ __('أسئلة وتدريبات تفاعلية لكل درس ووحدة دراسية لترسيخ القوانين والمفاهيم الوزارية.') }}</p>
                                </div>
                            </div>

                            <div class="royal-feature-item">
                                <div class="royal-feature-icon-pod">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </div>
                                <div class="royal-feature-text">
                                    <h4>{{ __('ملازم وتلاخيص PDF معتمدة') }}</h4>
                                    <p>{{ __('ملفات دراسية وتلاخيص مكثفة جاهزة للتحميل والطباعة المنزلية لسرعة مراجعة القوانين.') }}</p>
                                </div>
                            </div>

                            <div class="royal-feature-item">
                                <div class="royal-feature-icon-pod">
                                    <i class="fa-solid fa-calculator"></i>
                                </div>
                                <div class="royal-feature-text">
                                    <h4>{{ __('حاسبة معدل التوجيهي الوزارية') }}</h4>
                                    <p>{{ __('احتساب دقيق ومعتمد لمعدل شهادة الثانوية العامة لكافة الفروع وفق أحدث الأسس الوزارية.') }}</p>
                                </div>
                            </div>

                            <div class="royal-feature-item">
                                <div class="royal-feature-icon-pod">
                                    <i class="fa-solid fa-mobile-screen-button"></i>
                                </div>
                                <div class="royal-feature-text">
                                    <h4>{{ __('تطبيق جوال ومشاهدة بدون إنترنت') }}</h4>
                                    <p>{{ __('إمكانية تحميل الفيديوهات التعليمية ومشاهدتها في أي وقت وأي مكان دون الحاجة للاتصال بالإنترنت.') }}</p>
                                </div>
                            </div>

                            <div class="royal-feature-item">
                                <div class="royal-feature-icon-pod">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                                <div class="royal-feature-text">
                                    <h4>{{ __('إشراف ومتابعة مستمرة') }}</h4>
                                    <p>{{ __('تواصل أكاديمي ومتابعة مباشرة من المشرف م.أحمد شمالي لدعم مسيرة تفوق الطلاب خطوة بخطوة.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- مسار التفوق الأكاديمي في ٣ مراحل -->
                <div class="royal-card">
                    <div class="royal-card-header">
                        <h3>
                            <i class="fa-solid fa-shoe-prints" style="color: var(--royal-navy);"></i>
                            {{ __('كيف تبدأ رحلة التفوق في المنصة؟ (٣ خطوات ميسرة)') }}
                        </h3>
                    </div>
                    <div class="royal-card-body">
                        <div class="royal-steps-grid">
                            <div class="royal-step-pod">
                                <div class="royal-step-head">
                                    <div class="royal-step-num">١</div>
                                    <span class="royal-step-label">{{ __('المرحلة الأولى') }}</span>
                                </div>
                                <h4>{{ __('إنشاء حساب طالب') }}</h4>
                                <p>{{ __('إنشاء حساب طالب جديد وإدخال بيانات الفرع الدراسي والاسم ورقم الهاتف.') }}</p>
                                <a href="{{ route('students.create') }}" class="royal-step-btn">
                                    <span>{{ __('فتح الحساب الآن') }}</span>
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                            </div>

                            <div class="royal-step-pod">
                                <div class="royal-step-head">
                                    <div class="royal-step-num">٢</div>
                                    <span class="royal-step-label">{{ __('المرحلة الثانية') }}</span>
                                </div>
                                <h4>{{ __('اختيار المواد والمقررات') }}</h4>
                                <p>{{ __('تسجيل الدخول إلى حسابك واختيار المساقات والمواد الدراسية المقررة لفرعك.') }}</p>
                                <a href="{{ route('login') }}" class="royal-step-btn">
                                    <span>{{ __('دخول المنظومة') }}</span>
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                            </div>

                            <div class="royal-step-pod">
                                <div class="royal-step-head">
                                    <div class="royal-step-num">٣</div>
                                    <span class="royal-step-label">{{ __('المرحلة الثالثة') }}</span>
                                </div>
                                <h4>{{ __('الدراسة والتفوق') }}</h4>
                                <p>{{ __('مشاهدة الدروس والشروحات، تحميل التلاخيص والملازم، وحل التدريبات والأنشطة بانتظام.') }}</p>
                                <a href="{{ route('dashboard') }}" class="royal-step-btn">
                                    <span>{{ __('لوحة التحكم') }}</span>
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </main>

            <!-- العمود الجانبي -->
            <aside class="sidebar-flow">

                <!-- 1. بطاقة المشرف العام والتواصل المباشر عبر واتساب -->
                <div class="royal-supervisor-card">
                    <div class="royal-supervisor-avatar-frame">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <h3>{{ __('م.أحمد شمالي') }}</h3>
                    <span class="royal-sup-sub">{{ __('المشرف العام على المنظومة الأكاديمية') }}</span>

                    @php
                        $waDigits = '970597694385';
                        $waMsg = urlencode(app()->getLocale() === 'ar' 
                            ? "السلام عليكم بشمهندس أحمد شمالي، أود الاستفسار والتسجيل في منصة Step by Step." 
                            : "Hello Eng. Ahmed Shamali, I would like to inquire and register in Step by Step platform.");
                    @endphp

                    <a href="https://wa.me/{{ $waDigits }}?text={{ $waMsg }}" target="_blank" class="btn-whatsapp-royal">
                        <i class="fa-brands fa-whatsapp fa-lg"></i>
                        <span>{{ __('مراسلة الواتساب (0597694385)') }}</span>
                    </a>

                    <div class="royal-sup-phones">
                        <div>{{ __('هاتف بديل: 0567897212') }}</div>
                        <div>{{ __('فلسطين - قطاع غزة والضفة الغربية والقدس') }}</div>
                    </div>
                </div>

                <!-- 2. سجل إحصائيات المنظومة التعليمية -->
                <div class="royal-card">
                    <div class="royal-card-header">
                        <h3><i class="fa-solid fa-chart-column" style="color: var(--royal-navy);"></i> {{ __('إحصائيات المنظومة') }}</h3>
                    </div>
                    <div class="royal-card-body" style="padding: 0;">
                        <div class="royal-stats-list">
                            <div class="royal-stat-row">
                                <div class="royal-stat-title">
                                    <span class="royal-stat-icon-pod"><i class="fa-solid fa-users"></i></span>
                                    <span>{{ __('الطلبة المسجلين') }}</span>
                                </div>
                                <span class="royal-stat-number">{{ number_format($stats['students'] ?? 1200) }} {{ __('طالب') }}</span>
                            </div>
                            <div class="royal-stat-row">
                                <div class="royal-stat-title">
                                    <span class="royal-stat-icon-pod emerald"><i class="fa-solid fa-book-bookmark"></i></span>
                                    <span>{{ __('المساقات المعتمدة') }}</span>
                                </div>
                                <span class="royal-stat-number" style="color: #059669;">{{ number_format($stats['subjects'] ?? 18) }} {{ __('مساق') }}</span>
                            </div>
                            <div class="royal-stat-row">
                                <div class="royal-stat-title">
                                    <span class="royal-stat-icon-pod gold"><i class="fa-solid fa-video"></i></span>
                                    <span>{{ __('الدروس والشروحات') }}</span>
                                </div>
                                <span class="royal-stat-number" style="color: var(--royal-gold);">{{ number_format($stats['lessons'] ?? 350) }} {{ __('شرح') }}</span>
                            </div>
                            <div class="royal-stat-row">
                                <div class="royal-stat-title">
                                    <span class="royal-stat-icon-pod crimson"><i class="fa-solid fa-file-signature"></i></span>
                                    <span>{{ __('النماذج والاختبارات') }}</span>
                                </div>
                                <span class="royal-stat-number" style="color: #dc2626;">{{ number_format($stats['exams'] ?? 150) }} {{ __('اختبار') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. صندوق إرشادات دراسية للتفوق -->
                <div class="royal-card">
                    <div class="royal-card-header">
                        <h3><i class="fa-solid fa-lightbulb" style="color: var(--royal-gold);"></i> {{ __('إضاءات نحو التفوق') }}</h3>
                    </div>
                    <div class="royal-card-body">
                        <div class="royal-counsel-card">
                            <h4><i class="fa-solid fa-quote-right"></i> {{ __('سر النجاح والتفوق في التوجيهي:') }}</h4>
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
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <img src="{{ $siteLogo }}" alt="{{ __(\App\Models\Setting::get('site_name', 'Step by Step')) }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: contain; background: #ffffff; padding: 1px; border: 1.5px solid rgba(14,61,111,0.12); box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                    <h3 style="margin: 0;">{{ __(\App\Models\Setting::get('site_name', 'Step by Step')) }}</h3>
                </div>
                <p>
                    {{ __('المنظومة الأكاديمية الفلسطينية المعتمدة لطلبة الثانوية العامة (التوجيهي). منصة تعليمية متكاملة تقدم شروحات تعليمية، دروس أونلاين، دوسيات، بنك أسئلة، وحاسبة معدل التوجيهي متوافقة مع منهاج وزارة التربية والتعليم الفلسطينية.') }}
                </p>
                <div style="margin-top: 8px; color: var(--ed-primary); font-weight: 700; font-size: 12.5px;">
                    {{ __('إشراف ومتابعة: المهندس أحمد شمالي') }}
                </div>
            </div>

            <div class="footer-col">
                <h4>{{ __('روابط سريعة') }}</h4>
                <ul class="footer-links-list">
                    <li><a href="{{ route('home') }}"><i class="fa-solid fa-angle-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i> {{ __('الرئيسية') }}</a></li>
                    <li><a href="{{ route('tawjihi.calculator') }}"><i class="fa-solid fa-angle-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i> {{ __('حاسبة معدل التوجيهي') }}</a></li>
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
                            <i class="fa-solid fa-location-dot" style="margin-inline-end: 4px;"></i> {{ __('دولة فلسطين') }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom-bar">
            <div class="footer-bottom-inner">
                <span>{{ __('جميع الحقوق محفوظة © :year - :site_name • العام الأكاديمي :academic م', ['year' => date('Y'), 'site_name' => __(\App\Models\Setting::get('site_name', 'Step by Step')), 'academic' => \App\Models\Setting::academicYear()]) }}</span>
                <span>{{ __('متوافق تماماً مع المنهاج الرسمي لوزارة التربية والتعليم الفلسطينية') }}</span>
            </div>
        </div>
    </footer>

    <!-- سكربت قائمة الموبايل وزر العودة للأعلى -->
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

        // تفعيل زر العودة إلى بداية الصفحة بسلاسة
        function scrollToPageTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
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

        // تدوير شريط آخر الأخبار والتنبيهات المباشرة بالصفحة الرئيسية
        (function() {
            const track = document.getElementById('tickerTrack');
            const viewport = document.getElementById('tickerViewport');
            if (!track) return;

            const items = track.querySelectorAll('.royal-ticker-item');
            if (items.length <= 1) return;

            let currentIndex = 0;
            const itemHeight = 28;
            let tickerInterval = null;
            const duration = 5000;

            function goToIndex(idx) {
                currentIndex = (idx + items.length) % items.length;
                track.style.transform = 'translateY(-' + (currentIndex * itemHeight) + 'px)';
            }

            window.nextTickerItem = function() {
                goToIndex(currentIndex + 1);
            };

            window.prevTickerItem = function() {
                goToIndex(currentIndex - 1);
            };

            function startAutoTicker() {
                if (tickerInterval) clearInterval(tickerInterval);
                tickerInterval = setInterval(function() {
                    goToIndex(currentIndex + 1);
                }, duration);
            }

            function stopAutoTicker() {
                if (tickerInterval) {
                    clearInterval(tickerInterval);
                    tickerInterval = null;
                }
            }

            startAutoTicker();

            if (viewport) {
                viewport.addEventListener('mouseenter', stopAutoTicker);
                viewport.addEventListener('mouseleave', startAutoTicker);
            }
        })();
    </script>

    <!-- زر العودة إلى بداية الصفحة الكلاسيكي الأنيق (Scroll to Top Button) -->
    <button type="button" class="ed-scroll-top-btn" id="edScrollTopBtn" aria-label="{{ __('العودة إلى بداية الصفحة') }}" title="{{ __('العودة للأعلى') }}" onclick="scrollToPageTop()">
        <i class="fa-solid fa-chevron-up"></i>
    </button>

    <!-- شريط التنقل السفلي وبانر التثبيت لتطبيق الجوال (PWA) -->
    @include('partials.mobile_app_pwa')

</body>
</html>
