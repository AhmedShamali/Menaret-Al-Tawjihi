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

    <title>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} | المنصة التعليمية الأولى لطلبة الثانوية العامة في فلسطين</title>
    <meta name="description" content="منصة تعليمية متكاملة لطلبة الثانوية العامة (توجيهي فلسطين)، شروحات معتمدة، بنك أسئلة وامتحانات وزارية، بطاقات استذكار ذكية، وحاسبة معدل دقيقة.">

    <!-- خطوط عربية حديثة: Alexandria & Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #1d4ed8;
            --primary-dark: #1e3a8a;
            --primary-light: #eff6ff;
            --accent: #0284c7;
            --accent-light: #e0f2fe;
            --emerald: #059669;
            --emerald-light: #ecfdf5;
            --amber: #d97706;
            --amber-light: #fffbeb;
            --dark-surface: #0f172a;
            --dark-card: #1e293b;
            --bg-page: #f8fafc;
            --bg-surface: #ffffff;
            --border-subtle: #e2e8f0;
            --border-hover: #cbd5e1;
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 28px;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.02);
            --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            --shadow-lg: 0 20px 35px -10px rgba(29, 78, 216, 0.12), 0 10px 15px -5px rgba(0,0,0,0.04);
            --shadow-glow: 0 0 25px rgba(29, 78, 216, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Alexandria', 'Tajawal', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-body);
            line-height: 1.7;
            overflow-x: hidden;
        }

        /* --- شريط الإعلان العلوي --- */
        .top-announcement {
            background: linear-gradient(90deg, #0f172a 0%, #1e3a8a 50%, #0f172a 100%);
            color: #ffffff;
            padding: 9px 20px;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .top-announcement .badge-pal {
            background: rgba(255,255,255,0.15);
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 0.76rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .top-announcement a {
            color: #93c5fd;
            text-decoration: none;
            margin-right: 6px;
            font-weight: 700;
        }

        .top-announcement a:hover {
            text-decoration: underline;
        }

        /* --- شريط التنقل الرئيسي (Navbar) --- */
        nav.main-nav {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border-subtle);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .nav-inner {
            max-width: 1260px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-heading);
        }

        .logo-emblem {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 4px 14px rgba(29, 78, 216, 0.3);
        }

        .brand-name {
            display: flex;
            flex-direction: column;
        }

        .brand-name strong {
            font-size: 1.2rem;
            font-weight: 900;
            color: var(--text-heading);
            line-height: 1.2;
        }

        .brand-name span {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .nav-links-list {
            display: flex;
            align-items: center;
            gap: 26px;
            list-style: none;
        }

        @media (max-width: 960px) {
            .nav-links-list { display: none; }
        }

        .nav-links-list a {
            text-decoration: none;
            color: #334155;
            font-size: 0.92rem;
            font-weight: 600;
            transition: color 0.2s ease;
            position: relative;
        }

        .nav-links-list a:hover {
            color: var(--primary);
        }

        .nav-actions-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-nav-login {
            text-decoration: none;
            color: var(--text-heading);
            font-size: 0.9rem;
            font-weight: 700;
            padding: 9px 18px;
            border-radius: var(--radius-md);
            transition: background 0.2s ease;
        }

        .btn-nav-login:hover {
            background: #f1f5f9;
        }

        .btn-nav-register {
            text-decoration: none;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 700;
            padding: 10px 22px;
            border-radius: var(--radius-md);
            box-shadow: 0 4px 14px rgba(29, 78, 216, 0.25);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-nav-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(29, 78, 216, 0.35);
        }

        /* --- قسم الهيرو المبتكر (Modern Hero Section) --- */
        .hero-wrap {
            position: relative;
            padding: 70px 24px 80px;
            background: radial-gradient(circle at 10% 20%, rgba(29, 78, 216, 0.04) 0%, transparent 45%),
                        radial-gradient(circle at 90% 80%, rgba(2, 132, 199, 0.04) 0%, transparent 50%),
                        var(--bg-page);
            overflow: hidden;
        }

        .hero-container {
            max-width: 1260px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.15fr 0.9fr;
            gap: 50px;
            align-items: center;
        }

        @media (max-width: 992px) {
            .hero-container {
                grid-template-columns: 1fr;
                gap: 40px;
                padding-top: 20px;
            }
        }

        .hero-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: var(--primary);
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 22px;
        }

        .hero-headline {
            font-size: 3rem;
            font-weight: 900;
            line-height: 1.25;
            color: var(--text-heading);
            margin-bottom: 20px;
            letter-spacing: -0.02em;
        }

        .hero-headline .text-highlight {
            background: linear-gradient(135deg, #1d4ed8 0%, #0284c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtext {
            font-size: 1.12rem;
            color: var(--text-muted);
            line-height: 1.85;
            margin-bottom: 34px;
            max-width: 580px;
        }

        .hero-cta-buttons {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .btn-cta-primary {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: var(--radius-md);
            font-size: 1.05rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 25px -5px rgba(29, 78, 216, 0.35);
            transition: all 0.25s ease;
        }

        .btn-cta-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(29, 78, 216, 0.45);
        }

        .btn-cta-secondary {
            background: #ffffff;
            color: var(--text-heading);
            border: 1.5px solid var(--border-subtle);
            text-decoration: none;
            padding: 14px 28px;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }

        .btn-cta-secondary:hover {
            background: #f8fafc;
            border-color: var(--primary);
            color: var(--primary);
        }

        .hero-social-proof {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .avatar-group {
            display: flex;
            align-items: center;
        }

        .avatar-group img,
        .avatar-group span {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2.5px solid #ffffff;
            margin-right: -10px;
            object-fit: cover;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--primary);
        }

        .proof-text strong {
            display: block;
            font-size: 0.95rem;
            color: var(--text-heading);
        }

        .proof-text small {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Hero Right Visual Card (Interactive Preview) */
        .hero-visual-card {
            position: relative;
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            padding: 30px;
            box-shadow: var(--shadow-lg);
        }

        .vc-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 20px;
        }

        .vc-student-badge {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .vc-avatar {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #eff6ff;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: 800;
        }

        .vc-student-badge strong {
            display: block;
            font-size: 1.05rem;
            color: var(--text-heading);
        }

        .vc-student-badge span {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .vc-gpa-stat {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 8px 16px;
            border-radius: 12px;
            text-align: center;
        }

        .vc-gpa-stat span {
            font-size: 0.72rem;
            color: #15803d;
            font-weight: 700;
            display: block;
        }

        .vc-gpa-stat strong {
            font-size: 1.4rem;
            font-weight: 900;
            color: #166534;
        }

        .vc-metrics-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 22px;
        }

        .vc-metric-box {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 14px;
            padding: 12px;
            text-align: center;
        }

        .vc-metric-box i {
            font-size: 1.1rem;
            margin-bottom: 4px;
            display: block;
        }

        .vc-metric-box span {
            font-size: 0.72rem;
            color: var(--text-muted);
            display: block;
            margin-bottom: 2px;
        }

        .vc-metric-box strong {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-heading);
        }

        .vc-floating-pill {
            position: absolute;
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            padding: 10px 18px;
            border-radius: 999px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            animation: floatSlow 4s ease-in-out infinite alternate;
        }

        .vc-floating-pill.top-left {
            top: -16px;
            left: -20px;
        }

        .vc-floating-pill.bottom-right {
            bottom: -16px;
            right: -20px;
        }

        @keyframes floatSlow {
            0% { transform: translateY(0px); }
            100% { transform: translateY(-8px); }
        }

        /* --- شريط الإحصائيات السريعة (Stats Strip) --- */
        .stats-strip {
            background: #ffffff;
            border-top: 1px solid var(--border-subtle);
            border-bottom: 1px solid var(--border-subtle);
            padding: 36px 24px;
        }

        .stats-grid {
            max-width: 1260px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }

        .stat-item .num {
            font-size: 2.3rem;
            font-weight: 900;
            color: var(--primary);
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .stat-item .lbl {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        /* --- قسم الفروع والمناهج الأكاديمية (Curriculum Branches) --- */
        .section-padding {
            padding: 80px 24px;
        }

        .section-header {
            text-align: center;
            max-width: 680px;
            margin: 0 auto 50px;
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 14px;
            background: #eff6ff;
            color: var(--primary);
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--text-heading);
            margin-bottom: 14px;
            letter-spacing: -0.01em;
        }

        .section-desc {
            font-size: 1.05rem;
            color: var(--text-muted);
            line-height: 1.8;
        }

        .branches-grid {
            max-width: 1260px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        @media (max-width: 960px) {
            .branches-grid {
                grid-template-columns: 1fr;
            }
        }

        .branch-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .branch-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary);
            box-shadow: var(--shadow-lg);
        }

        .branch-icon-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .branch-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
        }

        .branch-card.sci .branch-icon { background: #eff6ff; color: #1d4ed8; }
        .branch-card.lit .branch-icon { background: #fefce8; color: #ca8a04; }
        .branch-card.bus .branch-icon { background: #ecfdf5; color: #059669; }

        .branch-tag {
            font-size: 0.78rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #475569;
        }

        .branch-card h3 {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 10px;
        }

        .branch-card p {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 22px;
        }

        .subject-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 26px;
        }

        .sub-pill {
            font-size: 0.8rem;
            font-weight: 600;
            background: #f8fafc;
            border: 1px solid var(--border-subtle);
            padding: 4px 12px;
            border-radius: 8px;
            color: var(--text-body);
        }

        .branch-btn {
            text-decoration: none;
            padding: 12px;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1.5px solid var(--border-subtle);
            background: #ffffff;
            color: var(--text-heading);
            transition: all 0.2s ease;
        }

        .branch-card:hover .branch-btn {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        /* --- حاسبة المعدل التفاعلية المباشرة (Live Interactive Calculator) --- */
        .calculator-preview-section {
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            border-top: 1px solid var(--border-subtle);
            border-bottom: 1px solid var(--border-subtle);
        }

        .calc-preview-card {
            max-width: 980px;
            margin: 0 auto;
            background: #ffffff;
            border: 1.5px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            padding: 40px;
            box-shadow: var(--shadow-lg);
        }

        @media (max-width: 768px) {
            .calc-preview-card { padding: 24px 18px; }
        }

        .calc-sliders-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 30px;
        }

        @media (max-width: 700px) {
            .calc-sliders-grid { grid-template-columns: 1fr; }
        }

        .calc-slider-item {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 14px;
            padding: 16px;
        }

        .calc-slider-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .calc-slider-header label {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-heading);
        }

        .calc-slider-header .score-val {
            font-size: 1.1rem;
            font-weight: 900;
            color: var(--primary);
        }

        .calc-range-input {
            width: 100%;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .calc-result-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            border-radius: 18px;
            padding: 24px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .calc-result-text h4 {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .calc-result-text p {
            font-size: 0.85rem;
            color: #cbd5e1;
            margin: 0;
        }

        .calc-live-gpa {
            font-size: 2.8rem;
            font-weight: 900;
            color: #38bdf8;
            font-family: monospace;
            line-height: 1;
        }

        /* --- بطاقات مزايا المنصة الذكية (Platform Superpowers) --- */
        .features-grid {
            max-width: 1260px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        @media (max-width: 1024px) { .features-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .features-grid { grid-template-columns: 1fr; } }

        .feat-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 28px 22px;
            transition: all 0.25s ease;
        }

        .feat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: #cbd5e1;
        }

        .feat-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 18px;
        }

        .feat-card h4 {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 8px;
        }

        .feat-card p {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.65;
        }

        /* --- آراء الطلبة المتفوقين (Testimonials) --- */
        .testimonials-grid {
            max-width: 1260px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        @media (max-width: 960px) { .testimonials-grid { grid-template-columns: 1fr; } }

        .testi-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .testi-stars {
            color: #f59e0b;
            font-size: 0.9rem;
            margin-bottom: 14px;
        }

        .testi-text {
            font-size: 0.94rem;
            color: var(--text-body);
            line-height: 1.75;
            margin-bottom: 22px;
            font-style: italic;
        }

        .testi-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .testi-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #eff6ff;
            color: var(--primary);
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .testi-info strong {
            display: block;
            font-size: 0.92rem;
            color: var(--text-heading);
        }

        .testi-info span {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        /* --- الأسئلة الشائعة (FAQ) --- */
        .faq-accordion {
            max-width: 820px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .faq-item {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            overflow: hidden;
            transition: all 0.2s;
        }

        .faq-question {
            padding: 18px 22px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-heading);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-question i {
            font-size: 0.85rem;
            color: var(--text-muted);
            transition: transform 0.3s;
        }

        .faq-answer {
            display: none;
            padding: 0 22px 18px;
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .faq-item.active {
            border-color: var(--primary);
        }

        .faq-item.active .faq-question {
            color: var(--primary);
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
            color: var(--primary);
        }

        .faq-item.active .faq-answer {
            display: block;
        }

        /* --- راية الانضمام الختامية (Final CTA Banner) --- */
        .final-cta-wrap {
            max-width: 1260px;
            margin: 0 auto 60px;
            padding: 0 24px;
        }

        .final-cta-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%);
            border-radius: var(--radius-xl);
            padding: 60px 40px;
            text-align: center;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.25);
        }

        .final-cta-card h2 {
            font-size: 2.4rem;
            font-weight: 900;
            margin-bottom: 14px;
        }

        .final-cta-card p {
            font-size: 1.1rem;
            color: #cbd5e1;
            max-width: 600px;
            margin: 0 auto 30px;
        }

        /* --- تذييل الصفحة (Footer) --- */
        footer.main-footer {
            background: #0f172a;
            color: #ffffff;
            padding: 60px 24px 30px;
            border-top: 1px solid #1e293b;
        }

        .footer-grid {
            max-width: 1260px;
            margin: 0 auto 40px;
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 40px;
        }

        @media (max-width: 900px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }

        .footer-brand p {
            color: #94a3b8;
            font-size: 0.88rem;
            line-height: 1.8;
            margin: 16px 0 20px;
        }

        .footer-col h5 {
            font-size: 1.05rem;
            font-weight: 800;
            margin-bottom: 18px;
            color: #ffffff;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-col ul a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.86rem;
            transition: color 0.2s;
        }

        .footer-col ul a:hover {
            color: #ffffff;
        }

        .footer-bottom {
            max-width: 1260px;
            margin: 0 auto;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            color: #64748b;
            flex-wrap: wrap;
            gap: 12px;
        }
    </style>
</head>
<body>

    <!-- 1. شريط الإعلان العلوي الرسمي -->
    <div class="top-announcement">
        <span class="badge-pal">🇵🇸 توجيهي فلسطين</span>
        <span>أهلاً بكم في منارة التوجيهي - تم اعتماد مناهج وامتحانات الثانوية العامة المحدثة لدورة 2026.</span>
        <a href="{{ route('tawjihi.calculator') }}">احسب معدلك المتوقع الآن ←</a>
    </div>

    <!-- 2. شريط التنقل الرئيسي (Navbar) -->
    <nav class="main-nav">
        <div class="nav-inner">
            <a href="/" class="brand-logo-area">
                <div class="logo-emblem">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="brand-name">
                    <strong>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</strong>
                    <span>بوابة التفوق الأكاديمي للثانوية العامة</span>
                </div>
            </a>

            <ul class="nav-links-list">
                <li><a href="#branches">الفروع والمناهج</a></li>
                <li><a href="#calculator">حاسبة المعدل</a></li>
                <li><a href="#features">مزايا المنصة</a></li>
                <li><a href="#testimonials">قصص النجاح</a></li>
                <li><a href="#faq">الأسئلة الشائعة</a></li>
            </ul>

            <div class="nav-actions-area">
                @auth
                    @if(auth()->user()->role === 'student' || auth('student')->check())
                        <a href="{{ route('student.dashboard') }}" class="btn-nav-register">
                            <i class="fas fa-columns"></i>
                            <span>لوحة دراستي</span>
                        </a>
                    @elseif(auth()->user()->role === 'teacher')
                        <a href="{{ route('teacher.dashboard') }}" class="btn-nav-register">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>لوحة المعلم</span>
                        </a>
                    @elseif(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn-nav-register">
                            <i class="fas fa-shield-alt"></i>
                            <span>لوحة الإدارة</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-nav-login">تسجيل الدخول</a>
                    <a href="{{ route('students.create') }}" class="btn-nav-register">
                        <i class="fas fa-user-plus"></i>
                        <span>إنشاء حساب مجاني</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- 3. قسم الهيرو الرئيسي الفخم (Hero Section) -->
    <section class="hero-wrap">
        <div class="hero-container">
            
            <!-- النصوص والإجراءات الرئيسية -->
            <div class="hero-content-col">
                <div class="hero-badge-pill">
                    <i class="fas fa-award"></i>
                    <span>المنصة الأولى المتخصصة لطلبة التوجيهي في فلسطين</span>
                </div>

                <h1 class="hero-headline">
                    طريقك نحو التفوق والـ <span class="text-highlight">99%</span> في توجيهي فلسطين يبدأ هنا.
                </h1>

                <p class="hero-subtext">
                    منصة تعليمية متكاملة توفر لك شروحات المباحث الوزارية المعتمدة، بنك الامتحانات المحلولة، بطاقات القوانين السريعة، ومتابعة دقيقة لكل فروع الثانوية العامة.
                </p>

                <div class="hero-cta-buttons">
                    <a href="{{ route('students.create') }}" class="btn-cta-primary">
                        <span>ابدأ دراستك الآن مجاناً</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <a href="#calculator" class="btn-cta-secondary">
                        <i class="fas fa-calculator" style="color: var(--primary);"></i>
                        <span>جرب حاسبة المعدل</span>
                    </a>
                </div>

                <div class="hero-social-proof">
                    <div class="avatar-group">
                        <span>أ</span>
                        <span>م</span>
                        <span>س</span>
                        <span>+</span>
                    </div>
                    <div class="proof-text">
                        <strong>أكثر من 15,000+ طالب ومعلم</strong>
                        <small>يستعدون للامتحانات الوزارية بثقة وتفوق</small>
                    </div>
                </div>
            </div>

            <!-- بطاقة العرض البصري والتفاعل الحي -->
            <div class="hero-visual-col">
                <div class="hero-visual-card">
                    
                    <div class="vc-floating-pill top-left">
                        <i class="fas fa-check-circle" style="color: #059669;"></i>
                        <span>امتحانات وزارية محلولة 2026</span>
                    </div>

                    <div class="vc-header">
                        <div class="vc-student-badge">
                            <div class="vc-avatar">ط</div>
                            <div>
                                <strong>محمد أحمد خليل</strong>
                                <span>الفرع العلمي • نابلس / غزة</span>
                            </div>
                        </div>
                        <div class="vc-gpa-stat">
                            <span>المعدل التراكمي</span>
                            <strong>98.6%</strong>
                        </div>
                    </div>

                    <div class="vc-metrics-row">
                        <div class="vc-metric-box">
                            <i class="fas fa-bolt" style="color: #ea580c;"></i>
                            <span>التزام متواصل</span>
                            <strong>14 يوم</strong>
                        </div>
                        <div class="vc-metric-box">
                            <i class="fas fa-file-signature" style="color: #1d4ed8;"></i>
                            <span>اختبارات منجزة</span>
                            <strong>{{ $stats['exams'] ?? 18 }} اختبار</strong>
                        </div>
                        <div class="vc-metric-box">
                            <i class="fas fa-award" style="color: #d97706;"></i>
                            <span>أوسمة التميز</span>
                            <strong>5 أوسمة</strong>
                        </div>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 14px; padding: 14px;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 700; margin-bottom: 6px;">
                            <span>التقدم في مبحث الرياضيات (علمي)</span>
                            <span style="color: var(--primary);">88%</span>
                        </div>
                        <div style="height: 6px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                            <div style="width: 88%; height: 100%; background: var(--primary); border-radius: 999px;"></div>
                        </div>
                    </div>

                    <div class="vc-floating-pill bottom-right">
                        <i class="fas fa-shield-alt" style="color: #1d4ed8;"></i>
                        <span>شهادات رسمية موثقة برمز QR</span>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- 4. شريط الأرقام والإحصائيات الحية (Stats Strip) -->
    <section class="stats-strip">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="num">{{ is_numeric($stats['students'] ?? null) ? number_format($stats['students']) : '12,500+' }}</div>
                <div class="lbl">طالب وطالبة مسجلين</div>
            </div>
            <div class="stat-item">
                <div class="num">{{ $stats['subjects'] ?? '24' }}</div>
                <div class="lbl">مبحث دراسي معتمد</div>
            </div>
            <div class="stat-item">
                <div class="num">{{ is_numeric($stats['lessons'] ?? null) ? number_format($stats['lessons']) : '450+' }}</div>
                <div class="lbl">شرح وملف وملخص دراسي</div>
            </div>
            <div class="stat-item">
                <div class="num">99.2%</div>
                <div class="lbl">نسبة نجاح وتفوق طلبتنا</div>
            </div>
        </div>
    </section>

    <!-- 5. فروع الثانوية العامة ومناهجها (Curriculum Branches) -->
    <section class="section-padding" id="branches">
        <div class="section-header">
            <div class="section-badge"><i class="fas fa-layer-group"></i> فروع التوجيهي المعتمدة</div>
            <h2 class="section-title">اختر فرعك وتصفح المواد المقررة</h2>
            <p class="section-desc">مناهج فلسطينية شاملة ومحدثة مع أفضل الأساتذة المتميزين في كل تخصص.</p>
        </div>

        <div class="branches-grid">
            
            <!-- الفرع العلمي -->
            <div class="branch-card sci">
                <div>
                    <div class="branch-icon-header">
                        <div class="branch-icon"><i class="fas fa-atom"></i></div>
                        <span class="branch-tag">الفرع العلمي</span>
                    </div>
                    <h3>الثانوية العامة - الفرع العلمي</h3>
                    <p>مخصص للطلبة الراغبين في دراسة الطب، الهندسة، الصيدلة، وتكنولوجيا المعلومات وعلوم الحاسوب.</p>
                    
                    <div class="subject-pills">
                        <span class="sub-pill">📐 الرياضيات (200)</span>
                        <span class="sub-pill">⚛️ الفيزياء (100)</span>
                        <span class="sub-pill">🧪 الكيمياء (100)</span>
                        <span class="sub-pill">🧬 العلوم الحياتية</span>
                        <span class="sub-pill">📜 اللغة العربية</span>
                        <span class="sub-pill">📖 اللغة الإنجليزية</span>
                    </div>
                </div>

                <a href="{{ route('students.create') }}" class="branch-btn">
                    <span>انضم لمواد الفرع العلمي</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <!-- الفرع الأدبي -->
            <div class="branch-card lit">
                <div>
                    <div class="branch-icon-header">
                        <div class="branch-icon"><i class="fas fa-book-reader"></i></div>
                        <span class="branch-tag">الفرع الأدبي</span>
                    </div>
                    <h3>الثانوية العامة - الفرع الأدبي</h3>
                    <p>موجه للراغبين في دراسة القانون والعلوم السياسية، اللغات والترجمة، الصحافة، والعلوم الإنسانية.</p>
                    
                    <div class="subject-pills">
                        <span class="sub-pill">📜 اللغة العربية (200)</span>
                        <span class="sub-pill">📖 اللغة الإنجليزية (150)</span>
                        <span class="sub-pill">🏛️ الدراسات التاريخية</span>
                        <span class="sub-pill">🌍 الدراسات الجغرافية</span>
                        <span class="sub-pill">📐 الرياضيات الأدبية</span>
                        <span class="sub-pill">🌙 التربية الإسلامية</span>
                    </div>
                </div>

                <a href="{{ route('students.create') }}" class="branch-btn">
                    <span>انضم لمواد الفرع الأدبي</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <!-- فرع الريادة والأعمال -->
            <div class="branch-card bus">
                <div>
                    <div class="branch-icon-header">
                        <div class="branch-icon"><i class="fas fa-chart-line"></i></div>
                        <span class="branch-tag">الريادة والأعمال</span>
                    </div>
                    <h3>فرع الريادة والأعمال (التجاري)</h3>
                    <p>للطموحين في تخصصات إدارة الأعمال، المحاسبة، التمويل والمصارف، والتجارة الإلكترونية الحديثة.</p>
                    
                    <div class="subject-pills">
                        <span class="sub-pill">💼 المشاريع الصغيرة</span>
                        <span class="sub-pill">📊 المحاسبة المالية</span>
                        <span class="sub-pill">🏢 الإدارة والاقتصاد</span>
                        <span class="sub-pill">📐 الرياضيات</span>
                        <span class="sub-pill">📜 اللغة العربية</span>
                        <span class="sub-pill">💻 التكنولوجيا</span>
                    </div>
                </div>

                <a href="{{ route('students.create') }}" class="branch-btn">
                    <span>انضم لمواد الريادة والأعمال</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

        </div>
    </section>

    <!-- 6. تجربة حاسبة المعدل التفاعلية الحية (Interactive Live Calculator) -->
    <section class="section-padding calculator-preview-section" id="calculator">
        <div class="section-header">
            <div class="section-badge"><i class="fas fa-calculator"></i> حاسبة تفاعلية حية</div>
            <h2 class="section-title">جرّب حاسبة معدل التوجيهي فورياً</h2>
            <p class="section-desc">حرّك المؤشرات وشاهد المعدل المحتسب بدقة وفق المعايير الوزارية الرسمية ونظام أوزان المواد في فلسطين.</p>
        </div>

        <div class="calc-preview-card">
            <div class="calc-sliders-grid">
                
                <div class="calc-slider-item">
                    <div class="calc-slider-header">
                        <label>الرياضيات (علمي - من 200)</label>
                        <span class="score-val" id="mathVal">190</span>
                    </div>
                    <input type="range" class="calc-range-input" id="mathRange" min="100" max="200" value="190" oninput="updateLiveCalc()">
                </div>

                <div class="calc-slider-item">
                    <div class="calc-slider-header">
                        <label>الفيزياء (من 100)</label>
                        <span class="score-val" id="physVal">96</span>
                    </div>
                    <input type="range" class="calc-range-input" id="physRange" min="50" max="100" value="96" oninput="updateLiveCalc()">
                </div>

                <div class="calc-slider-item">
                    <div class="calc-slider-header">
                        <label>الكيمياء / الأحياء (من 100)</label>
                        <span class="score-val" id="chemVal">95</span>
                    </div>
                    <input type="range" class="calc-range-input" id="chemRange" min="50" max="100" value="95" oninput="updateLiveCalc()">
                </div>

                <div class="calc-slider-item">
                    <div class="calc-slider-header">
                        <label>اللغة العربية (من 100)</label>
                        <span class="score-val" id="arabVal">94</span>
                    </div>
                    <input type="range" class="calc-range-input" id="arabRange" min="50" max="100" value="94" oninput="updateLiveCalc()">
                </div>

            </div>

            <div class="calc-result-banner">
                <div class="calc-result-text">
                    <h4>المعدل التقديري المحتسب:</h4>
                    <p>يؤهلك لدراسة تخصصات الطب البشري، الهندسة، والصيدلة في الجامعات الفلسطينية الرسمية.</p>
                </div>
                <div class="calc-live-gpa" id="liveGpaResult">95.4%</div>
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ route('tawjihi.calculator') }}" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 700; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 8px;">
                    <span>فتح حاسبة المعدل الشاملة ودليل التنسيق الجامعي لجميع الفروع</span>
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- 7. مزايا وأدوات المنصة الذكية (Platform Superpowers) -->
    <section class="section-padding" id="features">
        <div class="section-header">
            <div class="section-badge"><i class="fas fa-sparkles"></i> أدوات التفوق الحصرية</div>
            <h2 class="section-title">لماذا يختار أوائل التوجيهي منصتنا؟</h2>
            <p class="section-desc">مجموعة أدوات مبتكرة صُممت لتجعل دراستك اليومية أكثر سهولة وتركيزاً وفعالية.</p>
        </div>

        <div class="features-grid">
            
            <div class="feat-card">
                <div class="feat-icon-box" style="background: #eff6ff; color: #1d4ed8;">
                    <i class="fas fa-file-signature"></i>
                </div>
                <h4>امتحانات وزارية ذكية</h4>
                <p>بنك أسئلة شامل يحاكي نمط الامتحانات الرسمية مع تصحيح فوري وتحليل لنقاط القوة والضعف لديك.</p>
            </div>

            <div class="feat-card">
                <div class="feat-icon-box" style="background: #fdf4ff; color: #a21caf;">
                    <i class="fas fa-bolt"></i>
                </div>
                <h4>بطاقات الاستذكار السريع</h4>
                <p>مراجعة القوانين الفيزيائية والمتطابقات بأسلوب البطاقات التفاعلية 3D لترسيخ المفاهيم قبل الامتحان.</p>
            </div>

            <div class="feat-card">
                <div class="feat-icon-box" style="background: #f0fdf4; color: #15803d;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h4>الجدول الدراسي الذكي</h4>
                <p>توليد خطة مراجعة مخصصة للأيام المتبقية قبل الامتحانات الوزارية وتوزيع الساعات حسب صعوبة المادة.</p>
            </div>

            <div class="feat-card">
                <div class="feat-icon-box" style="background: #fffbeb; color: #d97706;">
                    <i class="fas fa-award"></i>
                </div>
                <h4>شهادات تميز معتمدة</h4>
                <p>توثيق رسمي لدرجاتك وإنجازاتك الدراسية بشهادات قابلة للطباعة مع رمز تحقق رقمي QR.</p>
            </div>

        </div>
    </section>

    <!-- 8. آراء وقصص نجاح طلبتنا (Testimonials) -->
    <section class="section-padding" style="background: #ffffff; border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);" id="testimonials">
        <div class="section-header">
            <div class="section-badge"><i class="fas fa-comment-dots"></i> تجارب الطلبة</div>
            <h2 class="section-title">ماذا يقول طلبة الثانوية العامة عنا؟</h2>
            <p class="section-desc">قصص واقعية لطلبة حققوا أعلى الدرجات بفضل الالتزام والمتابعة عبر المنصة.</p>
        </div>

        <div class="testimonials-grid">
            
            <div class="testi-card">
                <div class="testi-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testi-text">
                    "منارة التوجيهي كانت مرجعي الأول في مبحث الرياضيات والفيزياء. بنك الأسئلة والامتحانات التجريبية ساعدني على التخلص تماماً من رهبة قاعة الامتحان."
                </p>
                <div class="testi-user">
                    <div class="testi-avatar">س</div>
                    <div class="testi-info">
                        <strong>سارة النجار</strong>
                        <span>معدل 98.7% • الفرع العلمي • القدس</span>
                    </div>
                </div>
            </div>

            <div class="testi-card">
                <div class="testi-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testi-text">
                    "أعظم ميزة بالمنصة هي وضوح الشرح وتوفر بطاقات الاستذكار السريع. حفظت كل القوانين وراجعتها ليلة الامتحان بدون أي تشتت."
                </p>
                <div class="testi-user">
                    <div class="testi-avatar">ع</div>
                    <div class="testi-info">
                        <strong>عمر البرغوثي</strong>
                        <span>معدل 97.4% • الفرع الأدبي • رام الله</span>
                    </div>
                </div>
            </div>

            <div class="testi-card">
                <div class="testi-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testi-text">
                    "حاسبة المعدل والتنسيق أعطتني حافزاً يومياً للدراسة، ومؤقت التركيز خلاني التزم بجدول دراسي منتظم طول السنة."
                </p>
                <div class="testi-user">
                    <div class="testi-avatar">ن</div>
                    <div class="testi-info">
                        <strong>نور الدين المصري</strong>
                        <span>معدل 96.9% • فرع الريادة والأعمال • غزة</span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 9. الأسئلة الشائعة (FAQ Accordion) -->
    <section class="section-padding" id="faq">
        <div class="section-header">
            <div class="section-badge"><i class="fas fa-question-circle"></i> إجابات سريعة</div>
            <h2 class="section-title">الأسئلة الأكثر شيوعاً</h2>
            <p class="section-desc">كل ما تحتاج لمعرفته حول التسجيل، المواد، والامتحانات في المنصة.</p>
        </div>

        <div class="faq-accordion">
            
            <div class="faq-item active">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>هل التسجيل في المنصة مجاني؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    نعم، يمكنك التسجيل وإنشاء حساب مجاني تماماً والوصول إلى الاختبارات التجريبية، وحاسبة المعدل، وبطاقات القوانين السريعة بدون أي رسوم.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>هل المناهج مطابقة لمواصفات وزارة التربية والتعليم الفلسطينية؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    بكل تأكيد، جميع شروحاتنا وأسئلتنا ونماذج الامتحانات مبنية بالكامل على المنهاج الفلسطيني المعتمد وتحديثات وزارة التربية والتعليم لدورة 2026.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>كيف تعمل حاسبة المعدل الأكاديمي في المنصة؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    تعتمد الحاسبة على النظام الوزاري الدقيق لأوزان المباحث (مثل احتساب مبحث الرياضيات من 200 علامة للعلمي واللغة العربية للأدبي)، مع ميزة استخراج التخصصات الجامعية المتاحة لمعدلك مباشرة.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>هل تتوفر شروحات واختبارات لجميع فروع التوجيهي؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    نعم، تشمل المنصة حالياً الفرع العلمي، الفرع الأدبي، وفرع الريادة والأعمال، مع إضافة مستمرة للمواد الإثرائية ونماذج السنوات السابقة.
                </div>
            </div>

        </div>
    </section>

    <!-- 10. راية الانضمام الختامية (Final CTA Banner) -->
    <div class="final-cta-wrap">
        <div class="final-cta-card">
            <h2>ابدأ رحلة تفوقك في التوجيهي اليوم</h2>
            <p>لا تنتظر حتى اللحظات الأخيرة، انضم لآلاف الطلبة المتفوقين في فلسطين واستعد لامتحاناتك بكل ثقة وهدوء.</p>
            <a href="{{ route('students.create') }}" class="btn-cta-primary" style="background: #ffffff; color: #1d4ed8; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                <span>سجل حسابك مجاناً الآن</span>
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <!-- 11. تذييل الصفحة الرسمي (Footer) -->
    <footer class="main-footer">
        <div class="footer-grid">
            
            <div class="footer-brand">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="logo-emblem" style="width: 36px; height: 36px; font-size: 1.1rem;"><i class="fas fa-graduation-cap"></i></div>
                    <strong style="font-size: 1.15rem;">{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</strong>
                </div>
                <p>
                    المنصة الأكاديمية الفلسطينية الرائدة، صُممت لمساندة طلبة الثانوية العامة وتوفير بيئة تعليمية هادئة وشاملة لتحقيق أعلى المراتب.
                </p>
                <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.82rem; color: #cbd5e1; background: rgba(255,255,255,0.06); padding: 6px 12px; border-radius: 8px;">
                    <span>🇵🇸 صُنع بإتقان لدعم مسيرة التعليم في فلسطين</span>
                </div>
            </div>

            <div class="footer-col">
                <h5>الفروع والمناهج</h5>
                <ul>
                    <li><a href="#branches">الفرع العلمي</a></li>
                    <li><a href="#branches">الفرع الأدبي</a></li>
                    <li><a href="#branches">فرع الريادة والأعمال</a></li>
                    <li><a href="{{ route('tawjihi.calculator') }}">حاسبة المعدل الجامعي</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>أدوات المنصة</h5>
                <ul>
                    <li><a href="{{ route('login') }}">قاعة الامتحانات المحلولة</a></li>
                    <li><a href="{{ route('login') }}">بطاقات الاستذكار السريع</a></li>
                    <li><a href="{{ route('login') }}">مولّد جدول المراجعة</a></li>
                    <li><a href="{{ route('login') }}">لوحة الشرف وتحدي الأوائل</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>المساعدة والدعم</h5>
                <ul>
                    <li><a href="https://wa.me/{{ \App\Models\Setting::get('contact_whatsapp', '970567897212') }}" target="_blank"><i class="fab fa-whatsapp" style="color: #22c55e;"></i> تواصل عبر واتساب</a></li>
                    <li><a href="mailto:{{ \App\Models\Setting::get('contact_email', 'support@tawjihi.ps') }}">الدعم الفني والشكاوى</a></li>
                    <li><a href="#faq">مركز الأسئلة الشائعة</a></li>
                    <li><a href="{{ route('login') }}">بوابة المعلمين والإدارة</a></li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom">
            <span>جميع الحقوق محفوظة © {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</span>
            <span>نظام تعليمي متكامل لطلبة فلسطين</span>
        </div>
    </footer>

    <!-- سكربتات التفاعل للحاسبة الحية والأسئلة الشائعة -->
    <script>
        function toggleFaq(element) {
            const item = element.parentElement;
            item.classList.toggle('active');
        }

        function updateLiveCalc() {
            const math = parseFloat(document.getElementById('mathRange').value) || 190;
            const phys = parseFloat(document.getElementById('physRange').value) || 96;
            const chem = parseFloat(document.getElementById('chemRange').value) || 95;
            const arab = parseFloat(document.getElementById('arabRange').value) || 94;

            document.getElementById('mathVal').textContent = math;
            document.getElementById('physVal').textContent = phys;
            document.getElementById('chemVal').textContent = chem;
            document.getElementById('arabVal').textContent = arab;

            // حساب النسبة التقديرية بناءً على 200 للرياضيات و100 للمواد الأخرى
            const totalSum = math + phys + chem + arab;
            const maxScore = 200 + 100 + 100 + 100; // 500
            const gpa = ((totalSum / maxScore) * 100).toFixed(1);

            document.getElementById('liveGpaResult').textContent = gpa + '%';
        }

        document.addEventListener('DOMContentLoaded', updateLiveCalc);
    </script>
</body>
</html>
