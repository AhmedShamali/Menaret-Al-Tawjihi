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

    <!-- خطوط عربية وعالمية عصرية فائقة النقاء: Readex Pro & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Readex+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Pro-style icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-deep: #0f172a;
            --primary-soft: rgba(37, 99, 235, 0.08);
            --primary-glow: rgba(37, 99, 235, 0.25);
            --accent: #0284c7;
            --accent-soft: rgba(2, 132, 199, 0.08);
            --emerald: #10b981;
            --emerald-soft: rgba(16, 185, 129, 0.09);
            --amber: #f59e0b;
            --amber-soft: rgba(245, 158, 11, 0.09);
            
            --bg-body: #fafcff;
            --surface-card: rgba(255, 255, 255, 0.88);
            --surface-card-solid: #ffffff;
            --border-subtle: rgba(226, 232, 240, 0.8);
            --border-card: rgba(226, 232, 240, 0.9);
            --border-focus: #93c5fd;

            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --text-dim: #94a3b8;

            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 26px;
            --radius-full: 9999px;

            --shadow-subtle: 0 4px 20px -4px rgba(15, 23, 42, 0.05);
            --shadow-card: 0 20px 40px -15px rgba(15, 23, 42, 0.07), 0 0 0 1px rgba(226, 232, 240, 0.7);
            --shadow-float: 0 30px 60px -15px rgba(37, 99, 235, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.9);
            --shadow-btn: 0 10px 22px -6px rgba(37, 99, 235, 0.38);

            --transition-smooth: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Readex Pro', system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .font-num {
            font-family: 'Plus Jakarta Sans', 'Readex Pro', sans-serif !important;
            direction: ltr;
            display: inline-block;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-body);
            line-height: 1.7;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 15%, rgba(37, 99, 235, 0.04) 0%, transparent 45%),
                radial-gradient(circle at 90% 45%, rgba(14, 165, 233, 0.04) 0%, transparent 50%),
                radial-gradient(circle at 50% 85%, rgba(99, 102, 241, 0.03) 0%, transparent 45%);
            background-attachment: fixed;
        }

        /* --- 1. شريط الإعلان العلوي فائق الأناقة --- */
        .top-announcement {
            background: linear-gradient(90deg, #090e17 0%, #0f172a 45%, #1e293b 70%, #090e17 100%);
            color: #ffffff;
            padding: 8px 20px;
            font-size: 0.82rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            position: relative;
            z-index: 1001;
        }

        .top-announcement .badge-pal {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            padding: 2px 10px;
            border-radius: var(--radius-full);
            font-size: 0.74rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255,255,255,0.15);
        }

        .top-announcement .pulse-dot {
            width: 7px;
            height: 7px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #22c55e;
            animation: pulseGlow 2s infinite;
        }

        @keyframes pulseGlow {
            0% { transform: scale(0.9); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.8; }
        }

        .top-announcement a {
            color: #60a5fa;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: var(--transition-smooth);
        }

        .top-announcement a:hover {
            color: #93c5fd;
            transform: translateX(-3px);
        }

        @media (max-width: 768px) {
            .top-announcement {
                font-size: 0.76rem;
                padding: 7px 12px;
                flex-wrap: wrap;
                gap: 6px;
            }
        }

        /* --- 2. شريط التنقل الزجاجي العصري (Modern Glass Navbar) --- */
        nav.main-nav {
            background: rgba(255, 255, 255, 0.84);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: var(--transition-smooth);
        }

        .nav-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 13px 28px;
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
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 1.25rem;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition-smooth);
        }

        .brand-logo-area:hover .logo-emblem {
            transform: rotate(-5deg) scale(1.04);
        }

        .brand-name {
            display: flex;
            flex-direction: column;
        }

        .brand-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-name strong {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .brand-tag-year {
            font-size: 0.65rem;
            font-weight: 700;
            background: var(--primary-soft);
            color: var(--primary);
            padding: 1px 7px;
            border-radius: 6px;
            border: 1px solid rgba(37, 99, 235, 0.2);
        }

        .brand-name span {
            font-size: 0.74rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .nav-links-list {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
        }

        @media (max-width: 992px) {
            .nav-links-list { display: none; }
        }

        .nav-links-list a {
            text-decoration: none;
            color: #475569;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links-list a:hover {
            color: var(--primary);
            background: var(--primary-soft);
        }

        .nav-actions-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-nav-login {
            text-decoration: none;
            color: var(--text-heading);
            font-size: 0.88rem;
            font-weight: 600;
            padding: 9px 18px;
            border-radius: var(--radius-md);
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-nav-login:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .btn-nav-register {
            text-decoration: none;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 9px 20px;
            border-radius: var(--radius-md);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .btn-nav-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
            color: #ffffff;
        }

        .btn-nav-register i {
            font-size: 0.85rem;
            transition: transform 0.2s ease;
        }

        .btn-nav-register:hover i {
            transform: translateX(-3px);
        }

        /* --- 3. قسم الهيرو فائق الجمال والرقي (Ultra-Premium Hero Section) --- */
        .hero-wrap {
            position: relative;
            padding: 70px 24px 85px;
            overflow: hidden;
        }

        .hero-ambient-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.12) 0%, transparent 70%);
            top: -100px;
            right: 5%;
            pointer-events: none;
            filter: blur(60px);
            z-index: 0;
        }

        .hero-ambient-glow-left {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.1) 0%, transparent 70%);
            bottom: -50px;
            left: 5%;
            pointer-events: none;
            filter: blur(60px);
            z-index: 0;
        }

        .hero-container {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.15fr 0.9fr;
            gap: 56px;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 992px) {
            .hero-container {
                grid-template-columns: 1fr;
                gap: 48px;
                padding-top: 10px;
            }
        }

        .hero-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 7px 18px;
            background: rgba(37, 99, 235, 0.06);
            border: 1px solid rgba(37, 99, 235, 0.18);
            color: var(--primary);
            border-radius: var(--radius-full);
            font-size: 0.84rem;
            font-weight: 600;
            margin-bottom: 24px;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(37, 99, 235, 0.05);
        }

        .hero-badge-pill i {
            font-size: 0.9rem;
            color: var(--primary);
        }

        .hero-headline {
            font-size: clamp(2.3rem, 4vw, 3.4rem);
            font-weight: 800;
            line-height: 1.35;
            color: var(--text-heading);
            margin-bottom: 22px;
            letter-spacing: -0.02em;
        }

        .gradient-text {
            background: linear-gradient(135deg, #1d4ed8 0%, #0284c7 50%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            white-space: nowrap;
        }

        .hero-subtext {
            font-size: 1.08rem;
            color: #475569;
            line-height: 1.9;
            margin-bottom: 34px;
            max-width: 580px;
            font-weight: 400;
        }

        .hero-cta-buttons {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 38px;
        }

        .btn-cta-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-btn);
            transition: var(--transition-smooth);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-cta-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 30px -6px rgba(37, 99, 235, 0.45);
            color: #ffffff;
        }

        .btn-cta-primary i {
            transition: transform 0.2s ease;
        }

        .btn-cta-primary:hover i {
            transform: translateX(-4px);
        }

        .btn-cta-secondary {
            background: rgba(255, 255, 255, 0.85);
            color: var(--text-heading);
            border: 1px solid var(--border-card);
            text-decoration: none;
            padding: 14px 26px;
            border-radius: var(--radius-md);
            font-size: 0.98rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-subtle);
            transition: var(--transition-smooth);
        }

        .btn-cta-secondary:hover {
            background: #ffffff;
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-card);
        }

        .hero-social-proof {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-top: 6px;
        }

        .avatar-group {
            display: flex;
            align-items: center;
        }

        .avatar-pill {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2.5px solid #ffffff;
            margin-right: -10px;
            display: grid;
            place-items: center;
            font-size: 0.8rem;
            font-weight: 700;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        .proof-text {
            display: flex;
            flex-direction: column;
        }

        .proof-stars {
            color: #f59e0b;
            font-size: 0.78rem;
            display: flex;
            align-items: center;
            gap: 3px;
            margin-bottom: 2px;
        }

        .proof-text strong {
            font-size: 0.92rem;
            color: var(--text-heading);
            font-weight: 700;
        }

        .proof-text small {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        /* --- 4. بطاقة العرض البصري التفاعلية الخارقة (Showcase Glass Card) --- */
        .hero-visual-card {
            position: relative;
            background: var(--surface-card);
            border: 1px solid rgba(255, 255, 255, 0.95);
            border-radius: var(--radius-xl);
            padding: 30px;
            box-shadow: var(--shadow-float);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            transition: var(--transition-smooth);
        }

        .hero-visual-card:hover {
            box-shadow: 0 35px 70px -15px rgba(37, 99, 235, 0.2), 0 0 0 1px rgba(255, 255, 255, 1);
        }

        .vc-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.7);
            margin-bottom: 20px;
        }

        .vc-student-badge {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .vc-avatar-wrap {
            position: relative;
        }

        .vc-avatar {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-size: 1.25rem;
            font-weight: 800;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
        }

        .vc-verified-badge {
            position: absolute;
            bottom: -3px;
            right: -3px;
            background: #10b981;
            color: white;
            font-size: 0.65rem;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            border: 2px solid #ffffff;
        }

        .vc-student-badge strong {
            display: block;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 2px;
        }

        .vc-student-badge span {
            font-size: 0.76rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .vc-gpa-stat {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1px solid #a7f3d0;
            padding: 8px 16px;
            border-radius: 14px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
        }

        .vc-gpa-stat span {
            font-size: 0.7rem;
            color: #065f46;
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
        }

        .vc-gpa-stat strong {
            font-size: 1.35rem;
            font-weight: 800;
            color: #047857;
            line-height: 1;
        }

        .vc-metrics-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .vc-metric-box {
            background: rgba(248, 250, 252, 0.8);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: var(--radius-md);
            padding: 12px 10px;
            text-align: center;
            transition: var(--transition-smooth);
        }

        .vc-metric-box:hover {
            background: #ffffff;
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-subtle);
        }

        .vc-metric-box i {
            font-size: 1.15rem;
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
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text-heading);
        }

        .vc-progress-block {
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }

        .vc-progress-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-heading);
        }

        .vc-progress-track {
            height: 7px;
            background: #e2e8f0;
            border-radius: var(--radius-full);
            overflow: hidden;
            position: relative;
        }

        .vc-progress-fill {
            width: 88%;
            height: 100%;
            background: linear-gradient(90deg, #2563eb, #0284c7);
            border-radius: var(--radius-full);
            position: relative;
            animation: progressShimmer 3s infinite linear;
        }

        /* اختبار استرشادي سريع وتفاعلي داخل البطاقة */
        .vc-interactive-teaser {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }

        .vc-it-title {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
        }

        .vc-it-question {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-heading);
            margin-bottom: 10px;
        }

        .vc-it-options {
            display: flex;
            gap: 8px;
        }

        .vc-it-btn {
            flex: 1;
            padding: 6px 10px;
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: var(--text-body);
            cursor: pointer;
            transition: var(--transition-smooth);
            text-align: center;
        }

        .vc-it-btn:hover {
            border-color: var(--primary);
            background: var(--primary-soft);
            color: var(--primary);
        }

        .vc-it-btn.correct {
            border-color: #10b981;
            background: #ecfdf5;
            color: #065f46;
            font-weight: 700;
        }

        /* الأقراص الطافية العصرية (Floating Chips) */
        .vc-floating-pill {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(226, 232, 240, 0.9);
            padding: 9px 16px;
            border-radius: var(--radius-full);
            box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.12);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-heading);
            backdrop-filter: blur(12px);
            z-index: 2;
            transition: var(--transition-smooth);
        }

        .vc-floating-pill.top-left {
            top: -14px;
            left: -16px;
            animation: floatSlow 4s ease-in-out infinite alternate;
        }

        .vc-floating-pill.bottom-right {
            bottom: -14px;
            right: -16px;
            animation: floatSlow 4.5s ease-in-out 1s infinite alternate-reverse;
        }

        @keyframes floatSlow {
            0% { transform: translateY(0px); }
            100% { transform: translateY(-8px); }
        }

        @media (max-width: 600px) {
            .vc-floating-pill.top-left { left: 0; top: -10px; font-size: 0.74rem; padding: 6px 12px; }
            .vc-floating-pill.bottom-right { right: 0; bottom: -10px; font-size: 0.74rem; padding: 6px 12px; }
        }

        /* --- 5. شريط الإحصائيات والأرقام الحية (Stats Strip) --- */
        .stats-strip {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            border-top: 1px solid var(--border-subtle);
            border-bottom: 1px solid var(--border-subtle);
            padding: 38px 24px;
            position: relative;
        }

        .stats-grid {
            max-width: 1280px;
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

        .stat-item {
            padding: 10px;
            border-radius: var(--radius-md);
            transition: var(--transition-smooth);
        }

        .stat-item .num {
            font-size: 2.3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1d4ed8 0%, #0284c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .stat-item .lbl {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        /* --- 6. أقسام المحتوى الموحدة (Consistent Sections) --- */
        .section-padding {
            padding: 85px 24px;
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
            padding: 5px 16px;
            background: var(--primary-soft);
            color: var(--primary);
            border-radius: var(--radius-full);
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 14px;
            border: 1px solid rgba(37, 99, 235, 0.15);
        }

        .section-title {
            font-size: clamp(1.8rem, 3vw, 2.4rem);
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 14px;
            letter-spacing: -0.015em;
            line-height: 1.35;
        }

        .section-desc {
            font-size: 1.02rem;
            color: var(--text-muted);
            line-height: 1.8;
            font-weight: 400;
        }

        /* الفروع الأكاديمية */
        .branches-grid {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 26px;
        }

        @media (max-width: 960px) {
            .branches-grid {
                grid-template-columns: 1fr;
            }
        }

        .branch-card {
            background: #ffffff;
            border: 1px solid var(--border-card);
            border-radius: var(--radius-xl);
            padding: 32px 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-subtle);
            position: relative;
            overflow: hidden;
        }

        .branch-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-accent, var(--primary));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .branch-card:hover {
            transform: translateY(-6px);
            border-color: rgba(37, 99, 235, 0.3);
            box-shadow: 0 24px 45px -12px rgba(15, 23, 42, 0.09);
        }

        .branch-card:hover::before {
            opacity: 1;
        }

        .branch-card.sci { --card-accent: #2563eb; }
        .branch-card.lit { --card-accent: #d97706; }
        .branch-card.bus { --card-accent: #059669; }

        .branch-icon-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .branch-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.5rem;
        }

        .branch-card.sci .branch-icon { background: #eff6ff; color: #1d4ed8; }
        .branch-card.lit .branch-icon { background: #fefce8; color: #ca8a04; }
        .branch-card.bus .branch-icon { background: #ecfdf5; color: #059669; }

        .branch-tag {
            font-size: 0.76rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: var(--radius-full);
            background: #f1f5f9;
            color: #475569;
        }

        .branch-card h3 {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 10px;
        }

        .branch-card p {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 22px;
        }

        .subject-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-bottom: 28px;
        }

        .sub-pill {
            font-size: 0.78rem;
            font-weight: 600;
            background: #f8fafc;
            border: 1px solid rgba(226, 232, 240, 0.9);
            padding: 4px 11px;
            border-radius: 8px;
            color: var(--text-body);
        }

        .branch-btn {
            text-decoration: none;
            padding: 12px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1.5px solid var(--border-subtle);
            background: #ffffff;
            color: var(--text-heading);
            transition: var(--transition-smooth);
        }

        .branch-card:hover .branch-btn {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        /* --- 7. حاسبة المعدل التفاعلية الحية (Interactive Live Calculator) --- */
        .calculator-preview-section {
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            border-top: 1px solid var(--border-subtle);
            border-bottom: 1px solid var(--border-subtle);
        }

        .calc-preview-card {
            max-width: 980px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid var(--border-card);
            border-radius: var(--radius-xl);
            padding: 40px;
            box-shadow: var(--shadow-card);
        }

        @media (max-width: 768px) {
            .calc-preview-card { padding: 24px 18px; }
        }

        .calc-sliders-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }

        @media (max-width: 700px) {
            .calc-sliders-grid { grid-template-columns: 1fr; }
        }

        .calc-slider-item {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 14px;
            padding: 16px 18px;
            transition: var(--transition-smooth);
        }

        .calc-slider-item:hover {
            border-color: #cbd5e1;
            background: #ffffff;
        }

        .calc-slider-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .calc-slider-header label {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text-heading);
        }

        .calc-slider-header .score-val {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--primary);
        }

        .calc-range-input {
            width: 100%;
            accent-color: var(--primary);
            cursor: pointer;
            height: 6px;
        }

        .calc-result-banner {
            background: linear-gradient(135deg, #090e17 0%, #1e3a8a 100%);
            color: #ffffff;
            border-radius: 18px;
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
        }

        .calc-result-text h4 {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .calc-result-text p {
            font-size: 0.84rem;
            color: #cbd5e1;
            margin: 0;
        }

        .calc-live-gpa {
            font-size: 2.7rem;
            font-weight: 800;
            color: #38bdf8;
            line-height: 1;
        }

        /* --- 8. بطاقات مزايا المنصة الذكية (Features) --- */
        .features-grid {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
        }

        @media (max-width: 1024px) { .features-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .features-grid { grid-template-columns: 1fr; } }

        .feat-card {
            background: #ffffff;
            border: 1px solid var(--border-card);
            border-radius: var(--radius-lg);
            padding: 28px 22px;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-subtle);
        }

        .feat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-card);
            border-color: rgba(37, 99, 235, 0.3);
        }

        .feat-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.3rem;
            margin-bottom: 18px;
        }

        .feat-card h4 {
            font-size: 1.08rem;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 8px;
        }

        .feat-card p {
            font-size: 0.86rem;
            color: var(--text-muted);
            line-height: 1.68;
        }

        /* --- 9. آراء وقصص نجاح طلبتنا (Testimonials) --- */
        .testimonials-grid {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        @media (max-width: 960px) { .testimonials-grid { grid-template-columns: 1fr; } }

        .testi-card {
            background: #ffffff;
            border: 1px solid var(--border-card);
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-subtle);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: var(--transition-smooth);
        }

        .testi-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-card);
        }

        .testi-stars {
            color: #f59e0b;
            font-size: 0.85rem;
            margin-bottom: 14px;
            display: flex;
            gap: 3px;
        }

        .testi-text {
            font-size: 0.92rem;
            color: var(--text-body);
            line-height: 1.8;
            margin-bottom: 22px;
            font-style: italic;
        }

        .testi-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .testi-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--primary-soft);
            color: var(--primary);
            font-weight: 800;
            display: grid;
            place-items: center;
            font-size: 0.95rem;
            border: 1.5px solid rgba(37, 99, 235, 0.2);
        }

        .testi-info strong {
            display: block;
            font-size: 0.9rem;
            color: var(--text-heading);
            font-weight: 700;
        }

        .testi-info span {
            font-size: 0.76rem;
            color: var(--text-muted);
        }

        /* --- 10. الأسئلة الشائعة (FAQ) --- */
        .faq-accordion {
            max-width: 820px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .faq-item {
            background: #ffffff;
            border: 1px solid var(--border-card);
            border-radius: 14px;
            overflow: hidden;
            transition: var(--transition-smooth);
        }

        .faq-question {
            padding: 18px 22px;
            font-size: 0.98rem;
            font-weight: 700;
            color: var(--text-heading);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            user-select: none;
        }

        .faq-question i {
            font-size: 0.85rem;
            color: var(--text-muted);
            transition: transform 0.3s ease;
        }

        .faq-answer {
            display: none;
            padding: 0 22px 18px;
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.75;
        }

        .faq-item.active {
            border-color: var(--primary);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.08);
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

        /* --- 11. راية الانضمام الختامية (Final CTA) --- */
        .final-cta-wrap {
            max-width: 1280px;
            margin: 0 auto 60px;
            padding: 0 24px;
        }

        .final-cta-card {
            background: linear-gradient(135deg, #090e17 0%, #1e3a8a 60%, #1d4ed8 100%);
            border-radius: var(--radius-xl);
            padding: 65px 40px;
            text-align: center;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 24px 50px -10px rgba(15, 23, 42, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .final-cta-card h2 {
            font-size: clamp(1.8rem, 3.5vw, 2.5rem);
            font-weight: 800;
            margin-bottom: 14px;
            letter-spacing: -0.01em;
        }

        .final-cta-card p {
            font-size: 1.05rem;
            color: #cbd5e1;
            max-width: 620px;
            margin: 0 auto 32px;
            line-height: 1.8;
            font-weight: 400;
        }

        .btn-cta-white {
            background: #ffffff;
            color: var(--primary);
            text-decoration: none;
            padding: 14px 34px;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            transition: var(--transition-smooth);
        }

        .btn-cta-white:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.25);
            background: #f8fafc;
        }

        /* --- 12. تذييل الصفحة الرسمي (Footer) --- */
        footer.main-footer {
            background: #090e17;
            color: #ffffff;
            padding: 60px 24px 28px;
            border-top: 1px solid #1e293b;
        }

        .footer-grid {
            max-width: 1280px;
            margin: 0 auto 40px;
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 40px;
        }

        @media (max-width: 900px) { .footer-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 600px) { .footer-grid { grid-template-columns: 1fr; } }

        .footer-brand p {
            color: #94a3b8;
            font-size: 0.86rem;
            line-height: 1.8;
            margin: 16px 0 20px;
        }

        .footer-col h5 {
            font-size: 1rem;
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
            font-size: 0.85rem;
            transition: var(--transition-smooth);
        }

        .footer-col ul a:hover {
            color: #ffffff;
            transform: translateX(-3px);
            display: inline-block;
        }

        .footer-bottom {
            max-width: 1280px;
            margin: 0 auto;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: #64748b;
            flex-wrap: wrap;
            gap: 12px;
        }
    </style>
</head>
<body>

    <!-- 1. شريط الإعلان العلوي الأنيق -->
    <div class="top-announcement">
        <span class="badge-pal">
            <span class="pulse-dot"></span>
            <span>🇵🇸 توجيهي فلسطين 2026</span>
        </span>
        <span>أهلاً بكم في منارة التوجيهي — تم اعتماد مناهج ونماذج امتحانات الثانوية العامة المحدثة رسمياً.</span>
        <a href="{{ route('tawjihi.calculator') }}">
            <span>احسب معدلك المتوقع الآن</span>
            <i class="fas fa-arrow-left" style="font-size: 0.72rem;"></i>
        </a>
    </div>

    <!-- 2. شريط التنقل الرئيسي الزجاجي (Navbar) -->
    <nav class="main-nav">
        <div class="nav-inner">
            <a href="/" class="brand-logo-area">
                <div class="logo-emblem">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="brand-name">
                    <div class="brand-title-row">
                        <strong>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</strong>
                        <span class="brand-tag-year">2026 🇵🇸</span>
                    </div>
                    <span>بوابة التفوق الأكاديمي للثانوية العامة</span>
                </div>
            </a>

            <ul class="nav-links-list">
                <li><a href="#branches"><i class="fas fa-layer-group" style="font-size: 0.8rem; color: var(--primary);"></i> الفروع والمناهج</a></li>
                <li><a href="#calculator"><i class="fas fa-calculator" style="font-size: 0.8rem; color: var(--primary);"></i> حاسبة المعدل</a></li>
                <li><a href="#features"><i class="fas fa-sparkles" style="font-size: 0.8rem; color: var(--primary);"></i> مزايا المنصة</a></li>
                <li><a href="#testimonials"><i class="fas fa-award" style="font-size: 0.8rem; color: var(--primary);"></i> قصص النجاح</a></li>
                <li><a href="#faq"><i class="fas fa-circle-question" style="font-size: 0.8rem; color: var(--primary);"></i> الأسئلة الشائعة</a></li>
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
                    <a href="{{ route('login') }}" class="btn-nav-login">
                        <i class="fas fa-arrow-right-to-bracket" style="font-size: 0.85rem;"></i>
                        <span>تسجيل الدخول</span>
                    </a>
                    <a href="{{ route('students.create') }}" class="btn-nav-register">
                        <span>إنشاء حساب مجاني</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- 3. قسم الهيرو فائق الجمال (Hero Section) -->
    <section class="hero-wrap">
        <div class="hero-ambient-glow"></div>
        <div class="hero-ambient-glow-left"></div>

        <div class="hero-container">
            
            <!-- النصوص والإجراءات الرئيسية -->
            <div class="hero-content-col">
                <div class="hero-badge-pill">
                    <i class="fas fa-sparkles"></i>
                    <span>المنصة الأولى المتخصصة لطلبة التوجيهي في فلسطين 🇵🇸</span>
                </div>

                <h1 class="hero-headline">
                    طريقك الأكيد نحو التفوق <br>
                    والـ <span class="gradient-text">99% في توجيهي فلسطين</span>
                </h1>

                <p class="hero-subtext">
                    منصة تعليمية متكاملة توفر لك شروحات المباحث الوزارية المعتمدة، بنك الامتحانات المحلولة، بطاقات القوانين السريعة، ومتابعة دقيقة لكل فروع الثانوية العامة نحو حلمك الجامعي.
                </p>

                <div class="hero-cta-buttons">
                    <a href="{{ route('students.create') }}" class="btn-cta-primary">
                        <span>ابدأ دراستك الآن مجاناً</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <a href="#calculator" class="btn-cta-secondary">
                        <i class="fas fa-calculator" style="color: var(--primary);"></i>
                        <span>حاسبة المعدل التفاعلية</span>
                    </a>
                </div>

                <div class="hero-social-proof">
                    <div class="avatar-group">
                        <span class="avatar-pill" style="background: #2563eb;">أ</span>
                        <span class="avatar-pill" style="background: #0284c7;">م</span>
                        <span class="avatar-pill" style="background: #10b981;">س</span>
                        <span class="avatar-pill" style="background: #f59e0b;">+</span>
                    </div>
                    <div class="proof-text">
                        <div class="proof-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <span style="font-size: 0.76rem; font-weight: 700; color: var(--text-heading); margin-right: 4px;" class="font-num">4.9 / 5</span>
                        </div>
                        <strong>أكثر من 15,000+ طالب ومعلم</strong>
                        <small>يستعدون للامتحانات الوزارية بثقة وتميز</small>
                    </div>
                </div>
            </div>

            <!-- بطاقة العرض البصري والتفاعل الحي الفخمة (Showcase Card) -->
            <div class="hero-visual-col">
                <div class="hero-visual-card">
                    
                    <!-- الأقراص الطافية بنعومة -->
                    <div class="vc-floating-pill top-left">
                        <i class="fas fa-circle-check" style="color: #10b981;"></i>
                        <span>امتحانات وزارية محلولة 2026</span>
                    </div>

                    <div class="vc-header">
                        <div class="vc-student-badge">
                            <div class="vc-avatar-wrap">
                                <div class="vc-avatar">ط</div>
                                <div class="vc-verified-badge"><i class="fas fa-check"></i></div>
                            </div>
                            <div>
                                <strong>محمد أحمد خليل</strong>
                                <span><i class="fas fa-graduation-cap" style="color: var(--primary);"></i> الفرع العلمي • نابلس / غزة 🇵🇸</span>
                            </div>
                        </div>
                        <div class="vc-gpa-stat">
                            <span>المعدل التراكمي</span>
                            <strong class="font-num">98.6%</strong>
                        </div>
                    </div>

                    <div class="vc-metrics-row">
                        <div class="vc-metric-box">
                            <i class="fas fa-fire" style="color: #ea580c;"></i>
                            <span>التزام متواصل</span>
                            <strong class="font-num">14 يوم</strong>
                        </div>
                        <div class="vc-metric-box">
                            <i class="fas fa-file-signature" style="color: #2563eb;"></i>
                            <span>اختبارات منجزة</span>
                            <strong class="font-num">{{ $stats['exams'] ?? 18 }} اختبار</strong>
                        </div>
                        <div class="vc-metric-box">
                            <i class="fas fa-award" style="color: #d97706;"></i>
                            <span>أوسمة التميز</span>
                            <strong class="font-num">5 أوسمة</strong>
                        </div>
                    </div>

                    <!-- شريط تقدم المادة -->
                    <div class="vc-progress-block">
                        <div class="vc-progress-header">
                            <span>التقدم في مبحث الرياضيات (علمي)</span>
                            <span style="color: var(--primary);" class="font-num">88%</span>
                        </div>
                        <div class="vc-progress-track">
                            <div class="vc-progress-fill"></div>
                        </div>
                    </div>

                    <!-- تجربة سؤال وزاري تفاعلي سريع -->
                    <div class="vc-interactive-teaser">
                        <div class="vc-it-title">
                            <i class="fas fa-bolt"></i>
                            <span>سؤال وزاري استرشادي سريع 2026</span>
                        </div>
                        <div class="vc-it-question">
                            ما هي المشتقة الأولى للاقتران: <span style="direction: ltr; display: inline-block;" class="font-num">ق(س) = س³</span> ؟
                        </div>
                        <div class="vc-it-options">
                            <button type="button" class="vc-it-btn correct" onclick="alert('إجابة صحيحة وممتازة! 👏 3س² هي المشتقة الأولى وفق قواعد الاشتقاق.')">
                                3س² <i class="fas fa-check" style="font-size: 0.7rem;"></i>
                            </button>
                            <button type="button" class="vc-it-btn" onclick="alert('حاول مجدداً! المشتقة هي نزل الأس واطرح 1 فتصبح 3س²')">
                                2س³
                            </button>
                        </div>
                    </div>

                    <div class="vc-floating-pill bottom-right">
                        <i class="fas fa-shield-halved" style="color: #2563eb;"></i>
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
                <div class="num font-num">{{ is_numeric($stats['students'] ?? null) ? number_format($stats['students']) : '12,500+' }}</div>
                <div class="lbl">طالب وطالبة مسجلين</div>
            </div>
            <div class="stat-item">
                <div class="num font-num">{{ $stats['subjects'] ?? '24' }}</div>
                <div class="lbl">مبحث دراسي معتمد</div>
            </div>
            <div class="stat-item">
                <div class="num font-num">{{ is_numeric($stats['lessons'] ?? null) ? number_format($stats['lessons']) : '450+' }}</div>
                <div class="lbl">شرح وملف وملخص دراسي</div>
            </div>
            <div class="stat-item">
                <div class="num font-num">99.2%</div>
                <div class="lbl">نسبة نجاح وتفوق طلبتنا</div>
            </div>
        </div>
    </section>

    <!-- 5. فروع الثانوية العامة ومناهجها (Curriculum Branches) -->
    <section class="section-padding" id="branches">
        <div class="section-header">
            <div class="section-badge"><i class="fas fa-layer-group"></i> فروع التوجيهي المعتمدة</div>
            <h2 class="section-title">اختر فرعك وتصفح المواد المقررة</h2>
            <p class="section-desc">مناهج فلسطينية شاملة ومحدثة مع نخبة من أفضل الأساتذة المتميزين في كل تخصص.</p>
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
                    <p>مخصص للطلبة الراغبين في دراسة الطب، الهندسة، الصيدلة، وتكنولوجيا المعلومات والذكاء الاصطناعي.</p>
                    
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
                    <p>موجه للراغبين في دراسة القانون والعلوم السياسية، اللغات والترجمة، الإعلام، والعلوم الإنسانية.</p>
                    
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
                    <p>للطموحين في تخصصات إدارة الأعمال، المحاسبة، التمويل والمصارف، والتجارة والتسويق الرقمي.</p>
                    
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
                        <span class="score-val font-num" id="mathVal">190</span>
                    </div>
                    <input type="range" class="calc-range-input" id="mathRange" min="100" max="200" value="190" oninput="updateLiveCalc()">
                </div>

                <div class="calc-slider-item">
                    <div class="calc-slider-header">
                        <label>الفيزياء (من 100)</label>
                        <span class="score-val font-num" id="physVal">96</span>
                    </div>
                    <input type="range" class="calc-range-input" id="physRange" min="50" max="100" value="96" oninput="updateLiveCalc()">
                </div>

                <div class="calc-slider-item">
                    <div class="calc-slider-header">
                        <label>الكيمياء / الأحياء (من 100)</label>
                        <span class="score-val font-num" id="chemVal">95</span>
                    </div>
                    <input type="range" class="calc-range-input" id="chemRange" min="50" max="100" value="95" oninput="updateLiveCalc()">
                </div>

                <div class="calc-slider-item">
                    <div class="calc-slider-header">
                        <label>اللغة العربية (من 100)</label>
                        <span class="score-val font-num" id="arabVal">94</span>
                    </div>
                    <input type="range" class="calc-range-input" id="arabRange" min="50" max="100" value="94" oninput="updateLiveCalc()">
                </div>

            </div>

            <div class="calc-result-banner">
                <div class="calc-result-text">
                    <h4>المعدل التقديري المحتسب:</h4>
                    <p>يؤهلك لدراسة تخصصات الطب البشري، الهندسة، والصيدلة في الجامعات الفلسطينية الرسمية.</p>
                </div>
                <div class="calc-live-gpa font-num" id="liveGpaResult">95.4%</div>
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ route('tawjihi.calculator') }}" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 700; font-size: 0.92rem; display: inline-flex; align-items: center; gap: 8px;">
                    <span>فتح حاسبة المعدل الشاملة ودليل التنسيق الجامعي لجميع الفروع</span>
                    <i class="fas fa-arrow-left" style="font-size: 0.8rem;"></i>
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
            <div class="section-badge"><i class="fas fa-circle-question"></i> إجابات سريعة</div>
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
            <p>لا تنتظر حتى اللحظات الأخيرة، انضم لآلاف الطلبة المتفوقين في فلسطين واستعد لامتحاناتك بكل ثقة وهدوء واضمن مكانك في الكلية التي تحلم بها.</p>
            <a href="{{ route('students.create') }}" class="btn-cta-white">
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
                    <div class="logo-emblem" style="width: 38px; height: 38px; font-size: 1.1rem;"><i class="fas fa-graduation-cap"></i></div>
                    <strong style="font-size: 1.15rem; font-weight: 800;">{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</strong>
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
            <span>نظام تعليمي متكامل لطلبة فلسطين 🇵🇸</span>
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
