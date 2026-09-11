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

    <title>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} | المنظومة الأكاديمية الذكية الأولى في فلسطين 🇵🇸</title>
    <meta name="description" content="منصة منارة التوجيهي لدورة 2026: بنك الامتحانات الوزارية المحلولة، بطاقات الاستذكار الذكية، شروحات نخبة مدرسي فلسطين، وحاسبة التنسيق الجامعي الدقيقة.">

    <!-- خطوط عربية وعالمية فائقة الفخامة -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Readex+Pro:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 Pro -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            /* درجات ألوان نيون وميتافيرس ليلية ملكية فاخرة */
            --bg-deep: #060913;
            --bg-canvas: #090e1c;
            --bg-card: rgba(15, 23, 42, 0.75);
            --bg-card-hover: rgba(24, 34, 61, 0.85);
            --bg-glass: rgba(255, 255, 255, 0.04);
            --bg-glass-hover: rgba(255, 255, 255, 0.08);

            --border-glass: rgba(255, 255, 255, 0.1);
            --border-glow: rgba(59, 130, 246, 0.35);

            --primary: #3b82f6;
            --primary-glow: #2563eb;
            --cyan: #06b6d4;
            --cyan-glow: rgba(6, 182, 212, 0.4);
            --indigo: #6366f1;
            --emerald: #10b981;
            --amber: #f59e0b;
            --rose: #f43f5e;

            --text-heading: #f8fafc;
            --text-body: #94a3b8;
            --text-muted: #64748b;
            --text-light: #ffffff;

            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 28px;
            --radius-pill: 9999px;

            --glow-blue: 0 0 35px -5px rgba(59, 130, 246, 0.5);
            --glow-cyan: 0 0 35px -5px rgba(6, 182, 212, 0.4);
            --glow-amber: 0 0 30px -5px rgba(245, 158, 11, 0.4);
            --shadow-card: 0 20px 40px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.08);

            --transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Readex Pro', system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .num-font {
            font-family: 'Plus Jakarta Sans', 'Readex Pro', sans-serif !important;
            direction: ltr;
            display: inline-block;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-deep);
            color: var(--text-body);
            line-height: 1.7;
            overflow-x: hidden;
            position: relative;
        }

        /* توهجات الخلفية الكونية الفاخرة (Cosmic Mesh Glows) */
        .ambient-glow-orb-1 {
            position: absolute;
            top: -150px;
            right: -100px;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.22) 0%, rgba(99, 102, 241, 0.12) 45%, transparent 70%);
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
            border-radius: 50%;
        }

        .ambient-glow-orb-2 {
            position: absolute;
            top: 250px;
            left: -150px;
            width: 650px;
            height: 650px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.18) 0%, rgba(16, 185, 129, 0.08) 50%, transparent 70%);
            filter: blur(90px);
            z-index: 0;
            pointer-events: none;
            border-radius: 50%;
        }

        .ambient-glow-orb-3 {
            position: absolute;
            top: 1400px;
            right: 10%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(245, 158, 11, 0.06) 50%, transparent 70%);
            filter: blur(100px);
            z-index: 0;
            pointer-events: none;
        }

        /* شبكة الخلفية التفاعلية الفضائية */
        .cyber-grid-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1200px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 80% 70% at 50% 25%, #000 35%, transparent 85%);
            -webkit-mask-image: radial-gradient(ellipse 80% 70% at 50% 25%, #000 35%, transparent 85%);
            pointer-events: none;
            z-index: 0;
        }

        /* --- 1. شريط التنقل الزجاجي العائم فائق الأناقة (Glass Island Navbar) --- */
        .nav-wrapper {
            position: sticky;
            top: 18px;
            z-index: 1000;
            padding: 0 24px;
            max-width: 1280px;
            margin: 0 auto;
        }

        nav.island-nav {
            background: rgba(15, 23, 42, 0.82);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: var(--radius-pill);
            padding: 12px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 16px 35px -10px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.05);
            transition: var(--transition);
        }

        nav.island-nav:hover {
            border-color: rgba(59, 130, 246, 0.35);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.7), 0 0 20px rgba(59, 130, 246, 0.15);
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
        }

        .brand-emblem {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 50%, #06b6d4 100%);
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 1.25rem;
            box-shadow: 0 0 18px rgba(59, 130, 246, 0.5);
            transition: var(--transition);
        }

        .brand-link:hover .brand-emblem {
            transform: scale(1.08) rotate(-4deg);
            box-shadow: 0 0 26px rgba(6, 182, 212, 0.7);
        }

        .brand-titles strong {
            font-size: 1.22rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-badge {
            font-size: 0.68rem;
            font-weight: 700;
            background: rgba(59, 130, 246, 0.18);
            color: #60a5fa;
            padding: 3px 10px;
            border-radius: var(--radius-pill);
            border: 1px solid rgba(59, 130, 246, 0.35);
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.2);
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
        }

        @media (max-width: 1024px) {
            .nav-menu { display: none; }
        }

        .nav-menu a {
            text-decoration: none;
            color: #cbd5e1;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-menu a:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.2);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-ghost-login {
            text-decoration: none;
            color: #e2e8f0;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 9px 18px;
            border-radius: var(--radius-pill);
            transition: var(--transition);
            border: 1px solid transparent;
        }

        .btn-ghost-login:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .btn-primary-pill {
            text-decoration: none;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
            color: #ffffff;
            font-size: 0.88rem;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: var(--radius-pill);
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.45);
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            position: relative;
            overflow: hidden;
        }

        .btn-primary-pill::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: 0.5s;
        }

        .btn-primary-pill:hover::before {
            left: 100%;
        }

        .btn-primary-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(59, 130, 246, 0.65);
            color: #ffffff;
        }

        /* --- 2. قسم الهيرو السينمائي الخارق (Cinematic Split Hero) --- */
        .hero-section {
            position: relative;
            padding: 70px 24px 80px;
            max-width: 1280px;
            margin: 0 auto;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.15fr 0.95fr;
            gap: 48px;
            align-items: center;
        }

        @media (max-width: 980px) {
            .hero-section {
                grid-template-columns: 1fr;
                text-align: center;
                padding-top: 40px;
                gap: 40px;
            }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 20px;
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #93c5fd;
            border-radius: var(--radius-pill);
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 24px;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
            backdrop-filter: blur(12px);
        }

        .pulse-live {
            width: 9px;
            height: 9px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 12px #10b981, 0 0 20px #10b981;
            animation: pulseGlow 1.8s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.4); opacity: 1; }
        }

        .hero-title {
            font-size: clamp(2.4rem, 4.8vw, 4.1rem);
            font-weight: 900;
            line-height: 1.22;
            color: #ffffff;
            letter-spacing: -0.025em;
            margin-bottom: 22px;
        }

        .hero-title-highlight {
            background: linear-gradient(135deg, #60a5fa 0%, #38bdf8 40%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            text-shadow: 0 0 40px rgba(96, 165, 250, 0.35);
        }

        .hero-desc {
            font-size: clamp(1.02rem, 1.8vw, 1.18rem);
            color: #94a3b8;
            max-width: 620px;
            margin-bottom: 36px;
            line-height: 1.85;
            font-weight: 400;
        }

        @media (max-width: 980px) {
            .hero-desc {
                margin-left: auto;
                margin-right: auto;
            }
        }

        .hero-cta-group {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        @media (max-width: 980px) {
            .hero-cta-group {
                justify-content: center;
            }
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: var(--radius-pill);
            font-size: 1.05rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px -4px rgba(59, 130, 246, 0.6), 0 0 20px rgba(59, 130, 246, 0.4);
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-hero-primary::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(60deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transform: rotate(30deg);
            animation: btnShine 4s infinite;
        }

        @keyframes btnShine {
            0% { transform: translate(-100%, -100%) rotate(30deg); }
            25%, 100% { transform: translate(100%, 100%) rotate(30deg); }
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 16px 40px -4px rgba(59, 130, 246, 0.8), 0 0 30px rgba(6, 182, 212, 0.5);
            color: #ffffff;
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
            text-decoration: none;
            padding: 16px 30px;
            border-radius: var(--radius-pill);
            font-size: 1rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(16px);
            transition: var(--transition);
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(59, 130, 246, 0.5);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4), 0 0 15px rgba(59, 130, 246, 0.25);
        }

        /* شريط الثقة والمصداقية (Social Proof Strip) */
        .hero-trust-bar {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        @media (max-width: 980px) {
            .hero-trust-bar {
                justify-content: center;
            }
        }

        .trust-stat-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-cluster {
            display: flex;
            align-items: center;
            margin-left: 4px;
        }

        .avatar-cluster img, .avatar-cluster .avatar-pill {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid var(--bg-deep);
            margin-right: -10px;
            background: #1e293b;
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 0.8rem;
            font-weight: 800;
        }

        .avatar-cluster .avatar-pill:nth-child(1) { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .avatar-cluster .avatar-pill:nth-child(2) { background: linear-gradient(135deg, #10b981, #059669); }
        .avatar-cluster .avatar-pill:nth-child(3) { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .avatar-cluster .avatar-pill:nth-child(4) { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }

        .trust-text strong {
            display: block;
            font-size: 0.92rem;
            font-weight: 800;
            color: #ffffff;
        }

        .trust-text span {
            font-size: 0.78rem;
            color: #94a3b8;
        }

        .trust-stars {
            color: #fbbf24;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

        /* --- 3. نافذة العرض التفاعلية الحية بالهيرو (Hero Live Cockpit Mockup) --- */
        .hero-visual-wrapper {
            position: relative;
            z-index: 2;
        }

        .cockpit-container {
            background: rgba(15, 23, 42, 0.88);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: var(--radius-xl);
            padding: 24px;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.8), 0 0 40px rgba(59, 130, 246, 0.25);
            backdrop-filter: blur(24px);
            position: relative;
            transform: perspective(1000px) rotateY(-3deg) rotateX(2deg);
            transition: var(--transition);
        }

        .cockpit-container:hover {
            transform: perspective(1000px) rotateY(0deg) rotateX(0deg) translateY(-6px);
            box-shadow: 0 35px 70px -12px rgba(0, 0, 0, 0.9), 0 0 50px rgba(6, 182, 212, 0.35);
            border-color: rgba(59, 130, 246, 0.4);
        }

        .cockpit-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 18px;
        }

        .window-dots {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .w-dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
        }

        .w-dot.red { background: #ef4444; }
        .w-dot.yellow { background: #f59e0b; }
        .w-dot.green { background: #10b981; }

        .cockpit-title-badge {
            font-size: 0.8rem;
            font-weight: 700;
            color: #93c5fd;
            background: rgba(59, 130, 246, 0.15);
            padding: 4px 14px;
            border-radius: var(--radius-pill);
            border: 1px solid rgba(59, 130, 246, 0.3);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cockpit-timer {
            font-size: 0.8rem;
            font-weight: 800;
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.12);
            padding: 4px 12px;
            border-radius: var(--radius-pill);
            border: 1px solid rgba(245, 158, 11, 0.25);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cockpit-question-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-bottom: 18px;
        }

        .c-q-tag {
            font-size: 0.75rem;
            font-weight: 700;
            color: #38bdf8;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }

        .c-q-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .c-q-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .c-q-opt {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: 12px 16px;
            font-size: 0.9rem;
            color: #cbd5e1;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: var(--transition);
        }

        .c-q-opt:hover {
            background: rgba(59, 130, 246, 0.15);
            border-color: rgba(59, 130, 246, 0.4);
            color: #ffffff;
            transform: translateX(-4px);
        }

        .c-q-opt.correct {
            background: rgba(16, 185, 129, 0.2) !important;
            border-color: #10b981 !important;
            color: #34d399 !important;
            box-shadow: 0 0 18px rgba(16, 185, 129, 0.35);
        }

        .c-q-feedback {
            display: none;
            margin-top: 14px;
            padding: 12px 16px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: var(--radius-md);
            color: #34d399;
            font-size: 0.85rem;
            font-weight: 700;
            align-items: center;
            gap: 10px;
            animation: fadeIn 0.3s ease;
        }

        /* البطاقات العائمة التفاعلية حول المحاكي (Floating Accent Badges) */
        .float-badge-card {
            position: absolute;
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: var(--radius-lg);
            padding: 14px 18px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6), 0 0 20px rgba(59, 130, 246, 0.2);
            z-index: 3;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: floatUpDown 4s ease-in-out infinite alternate;
        }

        @keyframes floatUpDown {
            0% { transform: translateY(0); }
            100% { transform: translateY(-12px); }
        }

        .float-badge-1 {
            top: -24px;
            left: -20px;
            animation-delay: 0s;
        }

        .float-badge-2 {
            bottom: -22px;
            right: -20px;
            animation-delay: 1.5s;
        }

        @media (max-width: 640px) {
            .float-badge-card { display: none; }
        }

        .float-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.15rem;
        }

        .float-badge-1 .float-icon {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #ffffff;
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.4);
        }

        .float-badge-2 .float-icon {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
        }

        .float-text strong {
            display: block;
            font-size: 0.88rem;
            color: #ffffff;
            font-weight: 800;
        }

        .float-text span {
            font-size: 0.74rem;
            color: #94a3b8;
        }

        /* --- 4. شريط العد التنازلي الحي لامتحانات التوجيهي 2026 (Live Ticker Strip) --- */
        .countdown-strip-wrapper {
            max-width: 1280px;
            margin: -20px auto 80px;
            padding: 0 24px;
            position: relative;
            z-index: 5;
        }

        .countdown-strip-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.85) 0%, rgba(15, 23, 42, 0.95) 100%);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: var(--radius-xl);
            padding: 20px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.6), 0 0 30px rgba(59, 130, 246, 0.15);
            backdrop-filter: blur(20px);
        }

        .strip-lead {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .strip-lead-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 1.35rem;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
            animation: pulseGlow 2s infinite;
        }

        .strip-lead-text strong {
            display: block;
            font-size: 1.08rem;
            color: #ffffff;
            font-weight: 800;
        }

        .strip-lead-text span {
            font-size: 0.82rem;
            color: #94a3b8;
        }

        .countdown-clocks {
            display: flex;
            align-items: center;
            gap: 12px;
            direction: ltr;
        }

        .clock-box {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: 8px 14px;
            text-align: center;
            min-width: 65px;
        }

        .clock-num {
            font-size: 1.45rem;
            font-weight: 900;
            color: #60a5fa;
            line-height: 1.2;
            display: block;
        }

        .clock-lbl {
            font-size: 0.68rem;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* --- 5. العناوين المشتركة للأقسام (Section Headers) --- */
        .section-header-epic {
            text-align: center;
            max-width: 780px;
            margin: 0 auto 55px;
            position: relative;
            z-index: 2;
        }

        .section-tag-glow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 18px;
            border-radius: var(--radius-pill);
            font-size: 0.82rem;
            font-weight: 800;
            color: #38bdf8;
            background: rgba(6, 182, 212, 0.12);
            border: 1px solid rgba(6, 182, 212, 0.3);
            margin-bottom: 16px;
            box-shadow: 0 0 18px rgba(6, 182, 212, 0.2);
        }

        .section-title-epic {
            font-size: clamp(2rem, 3.8vw, 3rem);
            font-weight: 900;
            color: #ffffff;
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin-bottom: 16px;
        }

        .section-title-epic span {
            background: linear-gradient(135deg, #60a5fa 0%, #38bdf8 50%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-subtitle-epic {
            font-size: 1.05rem;
            color: #94a3b8;
            line-height: 1.8;
        }

        /* --- 6. قاعة الاختبارات والتدريب الحي السريع (Live Interactive Training Lab) --- */
        .training-lab-section {
            max-width: 1240px;
            margin: 0 auto 110px;
            padding: 0 24px;
            position: relative;
            z-index: 2;
        }

        .training-lab-card {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: var(--radius-xl);
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.8), 0 0 40px rgba(59, 130, 246, 0.2);
            backdrop-filter: blur(24px);
            overflow: hidden;
        }

        .lab-nav-tabs {
            display: flex;
            align-items: center;
            background: rgba(10, 15, 30, 0.7);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 12px 20px;
            gap: 10px;
            overflow-x: auto;
        }

        .lab-tab-btn {
            background: transparent;
            border: 1px solid transparent;
            padding: 10px 22px;
            border-radius: var(--radius-pill);
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        .lab-tab-btn:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
        }

        .lab-tab-btn.active {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.25) 0%, rgba(37, 99, 235, 0.35) 100%);
            border-color: rgba(59, 130, 246, 0.5);
            color: #ffffff;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.35);
        }

        .lab-body-container {
            padding: 36px;
        }

        @media (max-width: 640px) {
            .lab-body-container {
                padding: 24px 16px;
            }
        }

        .lab-quiz-pane {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 36px;
            align-items: center;
        }

        @media (max-width: 900px) {
            .lab-quiz-pane {
                grid-template-columns: 1fr;
            }
        }

        .lab-q-box {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-lg);
            padding: 26px;
        }

        .lab-q-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .lab-q-badge {
            font-size: 0.78rem;
            font-weight: 800;
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.15);
            padding: 4px 12px;
            border-radius: var(--radius-pill);
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .lab-q-points {
            font-size: 0.8rem;
            font-weight: 700;
            color: #a78bfa;
        }

        .lab-q-statement {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .lab-choices-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .lab-choice-btn {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: var(--radius-md);
            padding: 14px 20px;
            color: #e2e8f0;
            font-size: 0.95rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: var(--transition);
        }

        .lab-choice-btn:hover {
            background: rgba(59, 130, 246, 0.12);
            border-color: rgba(59, 130, 246, 0.4);
            transform: translateX(-4px);
            color: #ffffff;
        }

        .lab-choice-btn.correct-pick {
            background: rgba(16, 185, 129, 0.25) !important;
            border-color: #10b981 !important;
            color: #34d399 !important;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        }

        .lab-choice-btn.wrong-pick {
            background: rgba(239, 68, 68, 0.25) !important;
            border-color: #ef4444 !important;
            color: #f87171 !important;
        }

        .lab-explanation-box {
            display: none;
            margin-top: 20px;
            padding: 16px 20px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: var(--radius-md);
            color: #34d399;
            font-size: 0.92rem;
            line-height: 1.6;
            animation: fadeIn 0.3s ease;
        }

        .lab-info-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .lab-stat-highlight {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.05) 100%);
            border: 1px solid rgba(59, 130, 246, 0.25);
            border-radius: var(--radius-lg);
            padding: 24px;
        }

        .lab-stat-highlight h4 {
            font-size: 1.18rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 10px;
        }

        .lab-stat-highlight p {
            font-size: 0.88rem;
            color: #94a3b8;
            margin-bottom: 18px;
            line-height: 1.7;
        }

        .lab-feature-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .lab-f-pill {
            font-size: 0.76rem;
            font-weight: 700;
            color: #93c5fd;
            background: rgba(59, 130, 246, 0.15);
            padding: 5px 12px;
            border-radius: var(--radius-pill);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        /* --- 7. فروع الثانوية العامة ومناهجها (Curriculum Tracks) --- */
        .tracks-section {
            max-width: 1280px;
            margin: 0 auto 120px;
            padding: 0 24px;
            position: relative;
            z-index: 2;
        }

        .tracks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 28px;
        }

        .track-card-epic {
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            padding: 32px 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .track-card-epic::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #06b6d4);
            opacity: 0;
            transition: var(--transition);
        }

        .track-card-epic:hover {
            transform: translateY(-8px);
            background: rgba(24, 34, 61, 0.85);
            border-color: rgba(59, 130, 246, 0.4);
            box-shadow: 0 25px 50px -10px rgba(0, 0, 0, 0.7), 0 0 30px rgba(59, 130, 246, 0.25);
        }

        .track-card-epic:hover::before {
            opacity: 1;
        }

        .track-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .track-icon-emblem {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-size: 1.45rem;
            color: #ffffff;
        }

        .track-sci .track-icon-emblem {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
        }

        .track-lit .track-icon-emblem {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.4);
        }

        .track-bus .track-icon-emblem {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.4);
        }

        .track-ind .track-icon-emblem {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        }

        .track-badge {
            font-size: 0.74rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: var(--radius-pill);
            background: rgba(255, 255, 255, 0.06);
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .track-card-epic h3 {
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
        }

        .track-card-epic p {
            font-size: 0.88rem;
            color: #94a3b8;
            margin-bottom: 22px;
            line-height: 1.75;
        }

        .track-chips-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 30px;
        }

        .t-chip {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            font-size: 0.78rem;
            font-weight: 700;
            color: #e2e8f0;
        }

        .btn-track-join {
            width: 100%;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            text-decoration: none;
            padding: 13px;
            border-radius: var(--radius-pill);
            font-size: 0.9rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: var(--transition);
        }

        .btn-track-join:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-color: #3b82f6;
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
            color: #ffffff;
        }

        /* --- 8. شبكة البينتو الفائقة (Modern Cosmic Bento Grid) --- */
        .bento-section {
            max-width: 1280px;
            margin: 0 auto 120px;
            padding: 0 24px;
            position: relative;
            z-index: 2;
        }

        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
        }

        .bento-card-epic {
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            padding: 34px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(20px);
        }

        .bento-card-epic:hover {
            background: rgba(24, 34, 61, 0.85);
            border-color: rgba(59, 130, 246, 0.4);
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -10px rgba(0, 0, 0, 0.8), 0 0 35px rgba(59, 130, 246, 0.2);
        }

        .b-span-7 { grid-column: span 7; }
        .b-span-5 { grid-column: span 5; }
        .b-span-4 { grid-column: span 4; }

        @media (max-width: 980px) {
            .b-span-7, .b-span-5, .b-span-4 {
                grid-column: span 12;
            }
        }

        .bento-icon-glow {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.35rem;
            margin-bottom: 22px;
        }

        .bento-card-epic h3 {
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .bento-card-epic p {
            font-size: 0.92rem;
            color: #94a3b8;
            line-height: 1.8;
            margin-bottom: 24px;
        }

        .bento-visual-snippet {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* --- 9. حاسبة المعدل الوزارية والتنسيق الجامعي التفاعلية --- */
        .calculator-section {
            max-width: 1240px;
            margin: 0 auto 120px;
            padding: 0 24px;
            position: relative;
            z-index: 2;
        }

        .calc-panel-epic {
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(59, 130, 246, 0.35);
            border-radius: var(--radius-xl);
            padding: 44px;
            box-shadow: 0 30px 70px -15px rgba(0, 0, 0, 0.8), 0 0 45px rgba(59, 130, 246, 0.25);
            backdrop-filter: blur(24px);
        }

        @media (max-width: 640px) {
            .calc-panel-epic {
                padding: 24px 16px;
            }
        }

        .calc-sliders-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 28px;
            margin-bottom: 36px;
        }

        @media (max-width: 768px) {
            .calc-sliders-grid {
                grid-template-columns: 1fr;
            }
        }

        .calc-slider-box {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-lg);
            padding: 20px 24px;
        }

        .calc-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .calc-label-row label {
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
        }

        .calc-score-badge {
            font-size: 1.15rem;
            font-weight: 800;
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.15);
            padding: 2px 14px;
            border-radius: var(--radius-pill);
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .range-slider-input {
            -webkit-appearance: none;
            width: 100%;
            height: 7px;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.12);
            outline: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .range-slider-input::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #3b82f6;
            cursor: pointer;
            box-shadow: 0 0 15px #3b82f6;
            border: 2px solid #ffffff;
            transition: var(--transition);
        }

        .range-slider-input::-webkit-slider-thumb:hover {
            transform: scale(1.2);
            box-shadow: 0 0 25px #06b6d4;
        }

        .calc-result-verdict {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.5) 0%, rgba(15, 23, 42, 0.8) 100%);
            border: 1px solid rgba(59, 130, 246, 0.4);
            border-radius: var(--radius-lg);
            padding: 28px 34px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.2);
        }

        .verdict-info h4 {
            font-size: 1.3rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .verdict-info p {
            font-size: 0.92rem;
            color: #93c5fd;
            max-width: 600px;
        }

        .verdict-gpa-display {
            font-size: clamp(2.6rem, 4.5vw, 3.8rem);
            font-weight: 900;
            background: linear-gradient(135deg, #60a5fa 0%, #38bdf8 50%, #4ade80 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 30px rgba(96, 165, 250, 0.4);
        }

        /* --- 10. لوحة شرف أوائل فلسطين (Hall of Fame & Testimonials) --- */
        .testimonials-section {
            max-width: 1280px;
            margin: 0 auto 120px;
            padding: 0 24px;
            position: relative;
            z-index: 2;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 28px;
        }

        .testimonial-card-epic {
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            padding: 34px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: var(--transition);
            backdrop-filter: blur(20px);
            position: relative;
        }

        .testimonial-card-epic:hover {
            transform: translateY(-6px);
            border-color: rgba(245, 158, 11, 0.4);
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.8), 0 0 30px rgba(245, 158, 11, 0.15);
        }

        .t-stars {
            color: #fbbf24;
            font-size: 0.95rem;
            margin-bottom: 18px;
            letter-spacing: 2px;
        }

        .t-quote {
            font-size: 1.02rem;
            color: #e2e8f0;
            line-height: 1.85;
            font-style: italic;
            margin-bottom: 26px;
        }

        .t-author-row {
            display: flex;
            align-items: center;
            gap: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 18px;
        }

        .t-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 1.2rem;
            font-weight: 800;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.4);
        }

        .t-meta strong {
            display: block;
            font-size: 0.95rem;
            font-weight: 800;
            color: #ffffff;
        }

        .t-meta span {
            font-size: 0.78rem;
            color: #94a3b8;
        }

        /* --- 11. مركز الأسئلة الأكثر شيوعاً (FAQ Accordion) --- */
        .faq-section {
            max-width: 900px;
            margin: 0 auto 120px;
            padding: 0 24px;
            position: relative;
            z-index: 2;
        }

        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .faq-item-epic {
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: var(--transition);
        }

        .faq-item-epic.open {
            border-color: rgba(59, 130, 246, 0.45);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 25px rgba(59, 130, 246, 0.18);
        }

        .faq-header {
            padding: 22px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-size: 1.05rem;
            font-weight: 700;
            color: #ffffff;
            user-select: none;
        }

        .faq-header i {
            color: #60a5fa;
            transition: transform 0.3s ease;
        }

        .faq-item-epic.open .faq-header i {
            transform: rotate(180deg);
        }

        .faq-content {
            display: none;
            padding: 0 28px 24px;
            font-size: 0.95rem;
            color: #94a3b8;
            line-height: 1.85;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 16px;
        }

        .faq-item-epic.open .faq-content {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        /* --- 12. الراية الختامية الكبرى (Final High-Converting CTA) --- */
        .final-cta-section {
            max-width: 1240px;
            margin: 0 auto 100px;
            padding: 0 24px;
            position: relative;
            z-index: 2;
        }

        .final-cta-card {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e1b4b 50%, #0f172a 100%);
            border: 1px solid rgba(59, 130, 246, 0.5);
            border-radius: var(--radius-xl);
            padding: 65px 40px;
            text-align: center;
            box-shadow: 0 35px 80px -15px rgba(0, 0, 0, 0.8), 0 0 50px rgba(59, 130, 246, 0.35);
            position: relative;
            overflow: hidden;
        }

        .final-cta-card::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.4), transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .final-cta-card h2 {
            font-size: clamp(2.2rem, 4vw, 3.4rem);
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 18px;
            line-height: 1.25;
        }

        .final-cta-card p {
            font-size: 1.12rem;
            color: #cbd5e1;
            max-width: 720px;
            margin: 0 auto 40px;
            line-height: 1.85;
        }

        .btn-cta-radiant {
            background: #ffffff;
            color: #0f172a;
            text-decoration: none;
            padding: 18px 44px;
            border-radius: var(--radius-pill);
            font-size: 1.1rem;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3), 0 0 20px rgba(255, 255, 255, 0.2);
            transition: var(--transition);
        }

        .btn-cta-radiant:hover {
            transform: translateY(-3px) scale(1.03);
            background: #f8fafc;
            box-shadow: 0 16px 40px rgba(255, 255, 255, 0.5), 0 0 35px rgba(59, 130, 246, 0.5);
            color: #1e3a8a;
        }

        /* --- 13. التذييل الرسمي الفخم (World-Class Cosmic Footer) --- */
        .global-footer {
            background: rgba(8, 12, 22, 0.95);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 80px 24px 40px;
            position: relative;
            z-index: 2;
        }

        .footer-grid {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.3fr;
            gap: 48px;
            margin-bottom: 60px;
        }

        @media (max-width: 980px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }

        .footer-brand p {
            font-size: 0.9rem;
            color: #94a3b8;
            margin: 18px 0 24px;
            line-height: 1.8;
            max-width: 360px;
        }

        .footer-palestine-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: #cbd5e1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 6px 14px;
            border-radius: var(--radius-pill);
        }

        .footer-col h5 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 22px;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-col a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .footer-col a:hover {
            color: #60a5fa;
            padding-right: 6px;
        }

        .footer-bottom {
            max-width: 1280px;
            margin: 0 auto;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 0.85rem;
            color: #64748b;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <!-- التوهجات المحيطية الكونية الفاخرة (Ambient Glow Orbs) -->
    <div class="ambient-glow-orb-1"></div>
    <div class="ambient-glow-orb-2"></div>
    <div class="ambient-glow-orb-3"></div>
    <div class="cyber-grid-overlay"></div>

    <!-- 1. شريط التنقل الزجاجي العائم (Glass Island Navbar) -->
    <header class="nav-wrapper">
        <nav class="island-nav">
            <a href="/" class="brand-link">
                <div class="brand-emblem">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="brand-titles">
                    <strong>
                        {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}
                        <span class="brand-badge">دورة 2026 🇵🇸</span>
                    </strong>
                </div>
            </a>

            <ul class="nav-menu">
                <li><a href="#countdown"><i class="fas fa-stopwatch" style="color: #ef4444;"></i> العد التنازلي</a></li>
                <li><a href="#training-lab"><i class="fas fa-bolt" style="color: #38bdf8;"></i> قاعة التدريب</a></li>
                <li><a href="#tracks"><i class="fas fa-layer-group" style="color: #818cf8;"></i> الفروع والمناهج</a></li>
                <li><a href="#bento"><i class="fas fa-cubes" style="color: #34d399;"></i> منظومة التفوق</a></li>
                <li><a href="#calculator"><i class="fas fa-calculator" style="color: #fbbf24;"></i> حاسبة التنسيق</a></li>
                <li><a href="#faq"><i class="fas fa-circle-question" style="color: #94a3b8;"></i> الأسئلة الشائعة</a></li>
            </ul>

            <div class="nav-actions">
                @auth
                    @if(auth()->user()->role === 'student' || auth('student')->check())
                        <a href="{{ route('student.dashboard') }}" class="btn-primary-pill">
                            <i class="fas fa-columns"></i>
                            <span>لوحة دراستي</span>
                        </a>
                    @elseif(auth()->user()->role === 'teacher')
                        <a href="{{ route('teacher.dashboard') }}" class="btn-primary-pill">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>لوحة المعلم</span>
                        </a>
                    @elseif(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn-primary-pill">
                            <i class="fas fa-shield-halved"></i>
                            <span>لوحة الإدارة</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-ghost-login">دخول الحساب</a>
                    <a href="{{ route('students.create') }}" class="btn-primary-pill">
                        <span>انضم مجاناً</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    <!-- 2. قسم الهيرو السينمائي الخارق (Cinematic Split Hero) -->
    <section class="hero-section">
        
        <!-- الجانب الأيمن: العناوين المغرية وأزرار الانطلاق الفوري -->
        <div class="hero-content">
            <div class="hero-badge-pill">
                <span class="pulse-live"></span>
                <span>المنظومة الأكاديمية الأقوى لشهادة الثانوية العامة في فلسطين لدورة 2026 🇵🇸</span>
            </div>

            <h1 class="hero-title">
                طريقك المضمون نحو <br>
                <span class="hero-title-highlight">الـ 99% والدرجة الكاملة</span> <br>
                في امتحانات التوجيهي
            </h1>

            <p class="hero-desc">
                المنصة التعليمية الفلسطينية الأولى التي توفر لطلبة التوجيهي بنك الامتحانات الوزارية المحلولة خطوة بخطوة، بطاقات الاستذكار الذكية، شروحات نخبة مدرسي فلسطين، ومحاكي القبول والتنسيق الجامعي.
            </p>

            <div class="hero-cta-group">
                <a href="{{ route('students.create') }}" class="btn-hero-primary">
                    <span>ابدأ دراستك الآن مجاناً</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
                <a href="#training-lab" class="btn-hero-secondary">
                    <i class="fas fa-play-circle" style="color: #38bdf8; font-size: 1.2rem;"></i>
                    <span>جرب محاكي الامتحانات فوراً</span>
                </a>
            </div>

            <div class="hero-trust-bar">
                <div class="trust-stat-group">
                    <div class="avatar-cluster">
                        <div class="avatar-pill">أ</div>
                        <div class="avatar-pill">س</div>
                        <div class="avatar-pill">م</div>
                        <div class="avatar-pill">+15k</div>
                    </div>
                    <div class="trust-text">
                        <div class="trust-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <span>انضم لأكثر من <strong>15,200</strong> طالب وطالبة متفوق في فلسطين</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- الجانب الأيسر: مقصورة المنظومة الحية والمحاكي التفاعلي 3D -->
        <div class="hero-visual-wrapper">
            
            <!-- بطاقة عائمة 1: الأوائل والتفوق -->
            <div class="float-badge-card float-badge-1">
                <div class="float-icon"><i class="fas fa-crown"></i></div>
                <div class="float-text">
                    <strong>رغد البرغوثي • 99.4%</strong>
                    <span>الأولى على الفرع العلمي - القدس 🇵🇸</span>
                </div>
            </div>

            <!-- لوحة التحكم التفاعلية للمحاكي الوزاري -->
            <div class="cockpit-container">
                <div class="cockpit-header">
                    <div class="window-dots">
                        <div class="w-dot red"></div>
                        <div class="w-dot yellow"></div>
                        <div class="w-dot green"></div>
                    </div>
                    <div class="cockpit-title-badge">
                        <i class="fas fa-laptop-code"></i>
                        <span>محاكي الامتحان الوزاري التجريبي 2026</span>
                    </div>
                    <div class="cockpit-timer">
                        <i class="fas fa-clock"></i>
                        <span class="num-font" id="heroTimer">44:18</span>
                    </div>
                </div>

                <div class="cockpit-question-card">
                    <div class="c-q-tag">
                        <span>مبحث الرياضيات (الفرع العلمي) • الوحدة الثالثة</span>
                        <span class="num-font">(علامتان)</span>
                    </div>
                    <div class="c-q-title">
                        إذا كان الاقتران: <span class="num-font" style="color: #60a5fa;">ق(س) = س³ - 3س + 5</span>، فإن القيم القصوى المحلية للاقتران تحدث عند:
                    </div>

                    <div class="c-q-options">
                        <div class="c-q-opt" onclick="pickHeroOption(this, true)">
                            <span>أ) س = 1 (صغرى محلية) و س = -1 (عظمى محلية)</span>
                            <i class="far fa-circle"></i>
                        </div>
                        <div class="c-q-opt" onclick="pickHeroOption(this, false)">
                            <span>ب) س = 3 و س = -3</span>
                            <i class="far fa-circle"></i>
                        </div>
                        <div class="c-q-opt" onclick="pickHeroOption(this, false)">
                            <span>ج) س = 0 فقط</span>
                            <i class="far fa-circle"></i>
                        </div>
                    </div>

                    <div class="c-q-feedback" id="heroFeedback">
                        <i class="fas fa-circle-check" style="font-size: 1.3rem;"></i>
                        <span>إجابة صحيحة وعبقرية! 🎉 المشتقة ق'(س) = 3س² - 3 = 0 ومنها س² = 1 إذن س = ± 1.</span>
                    </div>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.82rem; color: #94a3b8;">
                    <span><i class="fas fa-bolt" style="color: #10b981;"></i> تصحيح ذكي فوري بالذكاء الاصطناعي</span>
                    <span style="color: #38bdf8; font-weight: 700;">+{{ $stats['exams'] ?? 18 }} نموذج وزاري معتمد</span>
                </div>
            </div>

            <!-- بطاقة عائمة 2: الشهادات المعتمدة -->
            <div class="float-badge-card float-badge-2">
                <div class="float-icon"><i class="fas fa-certificate"></i></div>
                <div class="float-text">
                    <strong>شهادة إنجاز معتمدة QR</strong>
                    <span>موثقة رقمياً لدورة 2026</span>
                </div>
            </div>

        </div>

    </section>

    <!-- 3. شريط العد التنازلي التفاعلي لامتحانات التوجيهي (Live Countdown Strip) -->
    <div class="countdown-strip-wrapper" id="countdown">
        <div class="countdown-strip-card">
            <div class="strip-lead">
                <div class="strip-lead-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="strip-lead-text">
                    <strong>العد التنازلي لانطلاق امتحانات الثانوية العامة في فلسطين (دورة 2026)</strong>
                    <span>كل دقيقة دراسة واستعداد تصنع فارقاً حاسماً في معدلك النهائي ومستقبلك الجامعي!</span>
                </div>
            </div>

            <div class="countdown-clocks">
                <div class="clock-box">
                    <span class="clock-num num-font" id="cdDays">274</span>
                    <span class="clock-lbl">يوم</span>
                </div>
                <div class="clock-box">
                    <span class="clock-num num-font" id="cdHours">14</span>
                    <span class="clock-lbl">ساعة</span>
                </div>
                <div class="clock-box">
                    <span class="clock-num num-font" id="cdMins">32</span>
                    <span class="clock-lbl">دقيقة</span>
                </div>
                <div class="clock-box">
                    <span class="clock-num num-font" id="cdSecs" style="color: #f43f5e;">50</span>
                    <span class="clock-lbl">ثانية</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. قاعة التدريب والاختبار التفاعلي المباشر (Interactive Training Lab) -->
    <section class="training-lab-section" id="training-lab">
        <div class="section-header-epic">
            <div class="section-tag-glow"><i class="fas fa-vial"></i> قاعة المحاكاة الفورية</div>
            <h2 class="section-title-epic">اختبر مستواك الآن في <span>ثوانٍ معدودة</span></h2>
            <p class="section-subtitle-epic">
                اختر المبحث وجرب حل سؤال وزاري حقيقي لتكتشف أسلوب الشرح التفاعلي الذكي والتقييم الفوري قبل التسجيل.
            </p>
        </div>

        <div class="training-lab-card">
            <div class="lab-nav-tabs">
                <button type="button" class="lab-tab-btn active" onclick="switchLabSubject('math', this)">
                    <i class="fas fa-square-root-variable"></i>
                    <span>الرياضيات (علمي)</span>
                </button>
                <button type="button" class="lab-tab-btn" onclick="switchLabSubject('physics', this)">
                    <i class="fas fa-atom"></i>
                    <span>الفيزياء (علمي)</span>
                </button>
                <button type="button" class="lab-tab-btn" onclick="switchLabSubject('arabic', this)">
                    <i class="fas fa-book"></i>
                    <span>اللغة العربية (مشترك)</span>
                </button>
                <button type="button" class="lab-tab-btn" onclick="switchLabSubject('chemistry', this)">
                    <i class="fas fa-flask"></i>
                    <span>الكيمياء (علمي)</span>
                </button>
            </div>

            <div class="lab-body-container">
                
                <!-- تبويب الرياضيات -->
                <div id="lab-math" class="lab-pane-item">
                    <div class="lab-quiz-pane">
                        <div class="lab-q-box">
                            <div class="lab-q-header">
                                <span class="lab-q-badge">سؤال وزاري استرشادي • دورة 2026</span>
                                <span class="lab-q-points num-font">3 علامات</span>
                            </div>
                            <div class="lab-q-statement">
                                ما هو ميل المماس لمنحنى الاقتران: <span class="num-font" style="color: #38bdf8;">ص = هـ^(2س) + جتا(س)</span> عند النقطة التي إحداثيها السيني <span class="num-font" style="color: #f59e0b;">س = 0</span>؟
                            </div>
                            <div class="lab-choices-list">
                                <button type="button" class="lab-choice-btn" onclick="handleLabAnswer(this, false, 'lab-math-exp')">
                                    <span>أ) 1</span>
                                    <i class="far fa-circle"></i>
                                </button>
                                <button type="button" class="lab-choice-btn" onclick="handleLabAnswer(this, true, 'lab-math-exp')">
                                    <span>ب) 2</span>
                                    <i class="far fa-circle"></i>
                                </button>
                                <button type="button" class="lab-choice-btn" onclick="handleLabAnswer(this, false, 'lab-math-exp')">
                                    <span>ج) 3</span>
                                    <i class="far fa-circle"></i>
                                </button>
                            </div>
                            <div class="lab-explanation-box" id="lab-math-exp">
                                <i class="fas fa-circle-check"></i>
                                <strong>تفسير الحل الوزاري النموذجي:</strong><br>
                                ص' = 2 هـ^(2س) - جا(س). بالتعويض عن س = 0: ص'(0) = 2(هـ^0) - جا(0) = 2(1) - 0 = 2. أحسنت!
                            </div>
                        </div>

                        <div class="lab-info-sidebar">
                            <div class="lab-stat-highlight">
                                <h4>بنك أسئلة الرياضيات الذهبي</h4>
                                <p>أكثر من 450 مسألة وزارية وتجريبية مشروحة بالفيديو التفاعلي والحل المكتوب خطوة بخطوة.</p>
                                <div class="lab-feature-pills">
                                    <span class="lab-f-pill">قواعد الاشتقاق وتطبيقاته</span>
                                    <span class="lab-f-pill">التكامل وتطبيقات المساحة</span>
                                    <span class="lab-f-pill">المصفوفات والمحددات</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب الفيزياء -->
                <div id="lab-physics" class="lab-pane-item" style="display: none;">
                    <div class="lab-quiz-pane">
                        <div class="lab-q-box">
                            <div class="lab-q-header">
                                <span class="lab-q-badge">سؤال وزاري • الدفع والزخم الخطي</span>
                                <span class="lab-q-points num-font">علامتان</span>
                            </div>
                            <div class="lab-q-statement">
                                جسم كتلته <span class="num-font" style="color: #38bdf8;">2 كغم</span> يتحرك بسرعة <span class="num-font" style="color: #38bdf8;">4 م/ث</span>. ما مقدار طاقته الحركية؟
                            </div>
                            <div class="lab-choices-list">
                                <button type="button" class="lab-choice-btn" onclick="handleLabAnswer(this, false, 'lab-phys-exp')">
                                    <span>أ) 8 جول</span>
                                    <i class="far fa-circle"></i>
                                </button>
                                <button type="button" class="lab-choice-btn" onclick="handleLabAnswer(this, true, 'lab-phys-exp')">
                                    <span>ب) 16 جول</span>
                                    <i class="far fa-circle"></i>
                                </button>
                                <button type="button" class="lab-choice-btn" onclick="handleLabAnswer(this, false, 'lab-phys-exp')">
                                    <span>ج) 32 جول</span>
                                    <i class="far fa-circle"></i>
                                </button>
                            </div>
                            <div class="lab-explanation-box" id="lab-phys-exp">
                                <i class="fas fa-circle-check"></i>
                                <strong>تفسير الحل النموذجي:</strong><br>
                                ط_ح = 0.5 × ك × ع² = 0.5 × 2 × (4)² = 1 × 16 = 16 جول. إجابة متفوقة!
                            </div>
                        </div>

                        <div class="lab-info-sidebar">
                            <div class="lab-stat-highlight">
                                <h4>مختبر الفيزياء التوجيهي المتكامل</h4>
                                <p>شرح مبسط للقوانين المعقدة مع تطبيقات رسومية وبنك امتحانات لجميع مديريات الوطن.</p>
                                <div class="lab-feature-pills">
                                    <span class="lab-f-pill">الزخم الخطي والتصادمات</span>
                                    <span class="lab-f-pill">الكهرباء المتحركة وكيرشوف</span>
                                    <span class="lab-f-pill">المجال المغناطيسي والحث</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب اللغة العربية -->
                <div id="lab-arabic" class="lab-pane-item" style="display: none;">
                    <div class="lab-quiz-pane">
                        <div class="lab-q-box">
                            <div class="lab-q-header">
                                <span class="lab-q-badge">قواعد اللغة العربية • النحو والصرف</span>
                                <span class="lab-q-points num-font">علامتان</span>
                            </div>
                            <div class="lab-q-statement">
                                ما الموقع الإعرابي لكلمة <span style="color: #38bdf8;">"صبراً"</span> في قول الشاعر: "فصبراً في مجال الموتِ صبراً"؟
                            </div>
                            <div class="lab-choices-list">
                                <button type="button" class="lab-choice-btn" onclick="handleLabAnswer(this, true, 'lab-arab-exp')">
                                    <span>أ) مفعول مطلق لفعل محذوف تقديره (اصبر)</span>
                                    <i class="far fa-circle"></i>
                                </button>
                                <button type="button" class="lab-choice-btn" onclick="handleLabAnswer(this, false, 'lab-arab-exp')">
                                    <span>ب) مفعول لأجله منصوب</span>
                                    <i class="far fa-circle"></i>
                                </button>
                                <button type="button" class="lab-choice-btn" onclick="handleLabAnswer(this, false, 'lab-arab-exp')">
                                    <span>ج) حال منصوبة</span>
                                    <i class="far fa-circle"></i>
                                </button>
                            </div>
                            <div class="lab-explanation-box" id="lab-arab-exp">
                                <i class="fas fa-circle-check"></i>
                                <strong>تفسير الحل الوزاري:</strong><br>
                                "صبراً" مفعول مطلق لفعل محذوف وجوباً تقديره (اصبر)، وهو مصدر نائب عن فعله يفيد الطلب.
                            </div>
                        </div>

                        <div class="lab-info-sidebar">
                            <div class="lab-stat-highlight">
                                <h4>حقيبة اللغة العربية الشاملة</h4>
                                <p>تحليل النصوص الأدبية، القواعد النحوية، البلاغة والعروض، ونماذج التعبير المقترحة للوزاري.</p>
                                <div class="lab-feature-pills">
                                    <span class="lab-f-pill">الممنوع من الصرف</span>
                                    <span class="lab-f-pill">إعراب الفعل المضارع</span>
                                    <span class="lab-f-pill">مراجعة نصوص الكتاب المقررة</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب الكيمياء -->
                <div id="lab-chemistry" class="lab-pane-item" style="display: none;">
                    <div class="lab-quiz-pane">
                        <div class="lab-q-box">
                            <div class="lab-q-header">
                                <span class="lab-q-badge">الكيمياء • البناء الإلكتروني ونظرية رابطة التكافؤ</span>
                                <span class="lab-q-points num-font">علامتان</span>
                            </div>
                            <div class="lab-q-statement">
                                ما هو نوع التهجين للذرة المركزية في جزيء الميثان <span class="num-font" style="color: #38bdf8;">(CH4)</span>؟
                            </div>
                            <div class="lab-choices-list">
                                <button type="button" class="lab-choice-btn" onclick="handleLabAnswer(this, false, 'lab-chem-exp')">
                                    <span>أ) sp²</span>
                                    <i class="far fa-circle"></i>
                                </button>
                                <button type="button" class="lab-choice-btn" onclick="handleLabAnswer(this, true, 'lab-chem-exp')">
                                    <span>ب) sp³</span>
                                    <i class="far fa-circle"></i>
                                </button>
                                <button type="button" class="lab-choice-btn" onclick="handleLabAnswer(this, false, 'lab-chem-exp')">
                                    <span>ج) sp</span>
                                    <i class="far fa-circle"></i>
                                </button>
                            </div>
                            <div class="lab-explanation-box" id="lab-chem-exp">
                                <i class="fas fa-circle-check"></i>
                                <strong>تفسير الحل النموذجي:</strong><br>
                                ذرة الكربون المركزية محاطة بأربعة أزواج إلكترونية رابطة ولا يوجد أزواج غير رابطة، لذا التهجين من نوع sp³ وشكل الجزيء هرم رباعي الأوجه منتظم.
                            </div>
                        </div>

                        <div class="lab-info-sidebar">
                            <div class="lab-stat-highlight">
                                <h4>دليل الكيمياء الوزاري المعتمد</h4>
                                <p>تبسيط معادلات الكيمياء العضوية ومسائل الاتزان والحموض والقواعد بدقة بالغة.</p>
                                <div class="lab-feature-pills">
                                    <span class="lab-f-pill">سرعة التفاعل والاتزان</span>
                                    <span class="lab-f-pill">الحموض والقواعد والمحاليل المنظمة</span>
                                    <span class="lab-f-pill">الكيمياء العضوية وتفاعلاتها</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 5. فروع الثانوية العامة ومناهجها (Curriculum Tracks) -->
    <section class="tracks-section" id="tracks">
        <div class="section-header-epic">
            <div class="section-tag-glow"><i class="fas fa-graduation-cap"></i> فروع الثانوية العامة</div>
            <h2 class="section-title-epic">اختر فرعك وانطلق في <span>المسار المتخصص</span></h2>
            <p class="section-subtitle-epic">
                محتوى دراسي منظم ومعد خصيصاً لكل تخصص وفق توزيع علامات وزارة التربية والتعليم الفلسطينية.
            </p>
        </div>

        <div class="tracks-grid">
            
            <!-- العلمي -->
            <div class="track-card-epic track-sci">
                <div>
                    <div class="track-top-row">
                        <div class="track-icon-emblem"><i class="fas fa-atom"></i></div>
                        <span class="track-badge">المسار الأوسع</span>
                    </div>
                    <h3>الثانوية العامة - الفرع العلمي</h3>
                    <p>مخصص للطلبة الطامحين في دراسة الطب البشري، طب الأسنان، الهندسة، الصيدلة، وتكنولوجيا الذكاء الاصطناعي.</p>
                    
                    <div class="track-chips-grid">
                        <span class="t-chip">📐 الرياضيات (200)</span>
                        <span class="t-chip">⚛️ الفيزياء (100)</span>
                        <span class="t-chip">🧪 الكيمياء (100)</span>
                        <span class="t-chip">🧬 العلوم الحياتية</span>
                        <span class="t-chip">📜 اللغة العربية</span>
                        <span class="t-chip">📖 اللغة الإنجليزية</span>
                    </div>
                </div>
                <a href="{{ route('students.create') }}" class="btn-track-join">
                    <span>انضم لمواد الفرع العلمي</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <!-- الأدبي -->
            <div class="track-card-epic track-lit">
                <div>
                    <div class="track-top-row">
                        <div class="track-icon-emblem"><i class="fas fa-book-open-reader"></i></div>
                        <span class="track-badge">العلوم الإنسانية</span>
                    </div>
                    <h3>الثانوية العامة - الفرع الأدبي</h3>
                    <p>موجه للراغبين في دراسة القانون، العلوم السياسية، الإعلام والصحافة، الترجمة، اللغات، والعلوم الإنسانية.</p>
                    
                    <div class="track-chips-grid">
                        <span class="t-chip">📜 اللغة العربية (200)</span>
                        <span class="t-chip">📖 اللغة الإنجليزية (150)</span>
                        <span class="t-chip">🏛️ الدراسات التاريخية</span>
                        <span class="t-chip">🌍 الدراسات الجغرافية</span>
                        <span class="t-chip">📐 الرياضيات الأدبية</span>
                        <span class="t-chip">🌙 التربية الإسلامية</span>
                    </div>
                </div>
                <a href="{{ route('students.create') }}" class="btn-track-join">
                    <span>انضم لمواد الفرع الأدبي</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <!-- الريادة والأعمال -->
            <div class="track-card-epic track-bus">
                <div>
                    <div class="track-top-row">
                        <div class="track-icon-emblem"><i class="fas fa-chart-pie"></i></div>
                        <span class="track-badge">إدارة وأعمال</span>
                    </div>
                    <h3>فرع الريادة والأعمال (التجاري)</h3>
                    <p>للطموحين في تخصصات إدارة الأعمال، المحاسبة، التمويل والمصارف، التسويق الرقمي، وريادة المشاريع.</p>
                    
                    <div class="track-chips-grid">
                        <span class="t-chip">💼 المشاريع الصغيرة</span>
                        <span class="t-chip">📊 المحاسبة المالية</span>
                        <span class="t-chip">🏢 الإدارة والاقتصاد</span>
                        <span class="t-chip">📐 الرياضيات</span>
                        <span class="t-chip">📜 اللغة العربية</span>
                        <span class="t-chip">💻 التكنولوجيا</span>
                    </div>
                </div>
                <a href="{{ route('students.create') }}" class="btn-track-join">
                    <span>انضم لمواد الريادة والأعمال</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <!-- الصناعي والتكنولوجي -->
            <div class="track-card-epic track-ind">
                <div>
                    <div class="track-top-row">
                        <div class="track-icon-emblem"><i class="fas fa-microchip"></i></div>
                        <span class="track-badge">التطبيقي والتكنولوجي</span>
                    </div>
                    <h3>الفرع الصناعي والتكنولوجي</h3>
                    <p>للطلبة الراغبين في مسارات الهندسة التطبيقية، ميكاترونكس، هندسة الطاقة المتجددة، والبرمجيات الذكية.</p>
                    
                    <div class="track-chips-grid">
                        <span class="t-chip">⚡ الكهرباء والإلكترونيات</span>
                        <span class="t-chip">⚙️ الميكانيكا والتشغيل</span>
                        <span class="t-chip">📐 الرياضيات الصناعية</span>
                        <span class="t-chip">⚛️ الفيزياء التطبيقية</span>
                        <span class="t-chip">📜 اللغة العربية</span>
                        <span class="t-chip">📖 اللغة الإنجليزية</span>
                    </div>
                </div>
                <a href="{{ route('students.create') }}" class="btn-track-join">
                    <span>انضم للمسار الصناعي</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

        </div>
    </section>

    <!-- 6. شبكة البينتو الفائقة (Cosmic Bento Grid Showcase) -->
    <section class="bento-section" id="bento">
        <div class="section-header-epic">
            <div class="section-tag-glow"><i class="fas fa-cubes"></i> منظومة التفوق المتكاملة</div>
            <h2 class="section-title-epic">أدوات ذكية مصممة لنيل <span>المراتب الأولى</span></h2>
            <p class="section-subtitle-epic">
                كل ما يحتاجه طالب التوجيهي لكسر حاجز الخوف والوصول إلى يوم الامتحان الوزاري بأقصى جاهزية وثقة.
            </p>
        </div>

        <div class="bento-grid">
            
            <!-- بطاقة 1 (كبيرة 7 أعمدة): بنك الامتحانات -->
            <div class="bento-card-epic b-span-7">
                <div>
                    <div class="bento-icon-glow" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h3>بنك الامتحانات الوزارية والتجريبية المحلولة 2026</h3>
                    <p>
                        أضخم مستودع رقمي لأسئلة التوجيهي الوزارية السابقة والامتحانات التجريبية المعتمدة لكافة مديريات الوطن (القدس، رام الله، غزة، نابلس، الخليل، جنين، طولكرم، بيت لحم)، مع نموذج الإجابة وتوزيع الدرجات الدقيق.
                    </p>
                </div>
                <div class="bento-visual-snippet">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-check-circle" style="color: #10b981; font-size: 1.3rem;"></i>
                        <div>
                            <strong style="color: #ffffff; font-size: 0.9rem; display: block;">الامتحان التجريبي الموحد 2025/2026</strong>
                            <span style="color: #94a3b8; font-size: 0.76rem;">تصحيح فوري ذكي مع شرح الخطوات</span>
                        </div>
                    </div>
                    <span style="background: rgba(16, 185, 129, 0.18); color: #34d399; font-size: 0.78rem; font-weight: 800; padding: 4px 12px; border-radius: var(--radius-pill);">
                        محلول 100%
                    </span>
                </div>
            </div>

            <!-- بطاقة 2 (5 أعمدة): بطاقات الاستذكار السريع -->
            <div class="bento-card-epic b-span-5">
                <div>
                    <div class="bento-icon-glow" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; box-shadow: 0 0 20px rgba(245, 158, 11, 0.3);">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>بطاقات الاستذكار السريع 3D Flashcards</h3>
                    <p>
                        ثبّت أصعب المتطابقات الرياضية والقوانين الفيزيائية ليلة الامتحان خلال 30 دقيقة فقط بتقنية التكرار المتباعد بدون تشتت.
                    </p>
                </div>
                <div style="background: rgba(245, 158, 11, 0.1); border: 1px dashed rgba(245, 158, 11, 0.35); border-radius: var(--radius-md); padding: 14px; text-align: center;">
                    <span style="font-size: 0.85rem; font-weight: 800; color: #fbbf24;">
                        ⚡ متوفر لأكثر من 450+ قانون ومعادلة وزارية
                    </span>
                </div>
            </div>

            <!-- بطاقة 3 (4 أعمدة): الشهادات المعتمدة -->
            <div class="bento-card-epic b-span-4">
                <div>
                    <div class="bento-icon-glow" style="background: rgba(16, 185, 129, 0.2); color: #34d399; box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>شهادات تفوق وإنجاز QR</h3>
                    <p>
                        احصل على شهادات رسمية موثقة برمز استجابة سريع (QR) عند إتمام الاختبارات التجريبية والمساقات المقررة بنجاح.
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: #34d399; font-weight: 700;">
                    <i class="fas fa-shield-halved"></i>
                    <span>نظام التوثيق الأكاديمي المعتمد</span>
                </div>
            </div>

            <!-- بطاقة 4 (4 أعمدة): تواصل المعلمين -->
            <div class="bento-card-epic b-span-4">
                <div>
                    <div class="bento-icon-glow" style="background: rgba(168, 85, 247, 0.2); color: #c084fc; box-shadow: 0 0 20px rgba(168, 85, 247, 0.3);">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>تواصل مباشر مع معلمي فلسطين</h3>
                    <p>
                        لا تترك أي فكرة غامضة! اطرح استفساراتك وأسئلتك الصعبة واحصل على شروحات وتوجيهات خاصة من معلمي التوجيهي المعتمدين.
                    </p>
                </div>
                <div style="font-size: 0.82rem; color: #c084fc; font-weight: 700;">
                    💬 دعم دراسي مستمر واستشارات مباشرة
                </div>
            </div>

            <!-- بطاقة 5 (4 أعمدة): الجدول الدراسي -->
            <div class="bento-card-epic b-span-4">
                <div>
                    <div class="bento-icon-glow" style="background: rgba(6, 182, 212, 0.2); color: #38bdf8; box-shadow: 0 0 20px rgba(6, 182, 212, 0.3);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>مخطط المراجعة ومؤقت التركيز</h3>
                    <p>
                        توليد جداول دراسية ذكية تحسب الوقت المتبقي للامتحان وتوزع حصص المراجعة حسب درجة صعوبة كل مبحث بتقنية بومودورو.
                    </p>
                </div>
                <div style="font-size: 0.82rem; color: #38bdf8; font-weight: 700;">
                    ⏱️ تعظيم التركيز ومنع التراكم
                </div>
            </div>

        </div>
    </section>

    <!-- 7. حاسبة المعدل الوزارية والتنسيق الجامعي المباشر -->
    <section class="calculator-section" id="calculator">
        <div class="section-header-epic">
            <div class="section-tag-glow"><i class="fas fa-calculator"></i> حاسبة التنسيق والقبول الموحد</div>
            <h2 class="section-title-epic">حاسبة معدل التوجيهي <span>وتنسيق الجامعات الفلسطينية</span></h2>
            <p class="section-subtitle-epic">
                حرّك المؤشرات لتكتشف معدلك التقديري والكليات المتاحة لك فورياً في جامعات (بيرزيت، النجاح، القدس، الإسلامية، البوليتكنك).
            </p>
        </div>

        <div class="calc-panel-epic">
            <div class="calc-sliders-grid">
                
                <div class="calc-slider-box">
                    <div class="calc-label-row">
                        <label>📐 الرياضيات (علمي - من 200)</label>
                        <span class="calc-score-badge num-font" id="mathScoreVal">194</span>
                    </div>
                    <input type="range" class="range-slider-input" id="mathSlider" min="100" max="200" value="194" oninput="calculateLandingGpa()">
                </div>

                <div class="calc-slider-box">
                    <div class="calc-label-row">
                        <label>⚛️ الفيزياء (من 100)</label>
                        <span class="calc-score-badge num-font" id="physScoreVal">97</span>
                    </div>
                    <input type="range" class="range-slider-input" id="physSlider" min="50" max="100" value="97" oninput="calculateLandingGpa()">
                </div>

                <div class="calc-slider-box">
                    <div class="calc-label-row">
                        <label>🧪 الكيمياء / الأحياء (من 100)</label>
                        <span class="calc-score-badge num-font" id="chemScoreVal">96</span>
                    </div>
                    <input type="range" class="range-slider-input" id="chemSlider" min="50" max="100" value="96" oninput="calculateLandingGpa()">
                </div>

                <div class="calc-slider-box">
                    <div class="calc-label-row">
                        <label>📜 اللغة العربية (من 100)</label>
                        <span class="calc-score-badge num-font" id="arabScoreVal">95</span>
                    </div>
                    <input type="range" class="range-slider-input" id="arabSlider" min="50" max="100" value="95" oninput="calculateLandingGpa()">
                </div>

            </div>

            <div class="calc-result-verdict">
                <div class="verdict-info">
                    <h4>المعدل التقديري المحسوب لدورة 2026:</h4>
                    <p id="landingAdmissionMajor">
                        🎓 يؤهلك مباشرة للمنافسة على مقاعد: <strong>الطب البشري، طب وجراحة الأسنان، الصيدلة السريرية، وهندسة الذكاء الاصطناعي والحاسوب</strong> في كافة الجامعات الفلسطينية الرسمية.
                    </p>
                </div>
                <div class="verdict-gpa-display num-font" id="landingGpaResult">96.4%</div>
            </div>

            <div style="text-align: center; margin-top: 28px;">
                <a href="{{ route('tawjihi.calculator') }}" target="_blank" style="color: #60a5fa; text-decoration: none; font-weight: 700; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 8px;">
                    <span>فتح دليل التنسيق الشامل لكافة التخصصات والجامعات الفلسطينية</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- 8. لوحة شرف وتجارب متفوقي فلسطين (Hall of Fame) -->
    <section class="testimonials-section">
        <div class="section-header-epic">
            <div class="section-tag-glow"><i class="fas fa-star"></i> قصص النجاح الحقيقية</div>
            <h2 class="section-title-epic">ماذا يقول أوائل فلسطين عن <span>منارة التوجيهي</span>؟</h2>
            <p class="section-subtitle-epic">
                تجارب حقيقية لطلبة متفوقين حققوا أعلى المراتب بفضل المتابعة اليومية عبر المنصة.
            </p>
        </div>

        <div class="testimonials-grid">
            
            <div class="testimonial-card-epic">
                <div>
                    <div class="t-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="t-quote">
                        "منارة التوجيهي كانت سر تفوقي في مادة الرياضيات والفيزياء. نماذج الامتحانات الوزارية المحلولة مع توضيح خطوات توزيع العلامات أزالت الرهبة تماماً من يوم الامتحان الوزاري!"
                    </p>
                </div>
                <div class="t-author-row">
                    <div class="t-avatar">س</div>
                    <div class="t-meta">
                        <strong>سارة النجار</strong>
                        <span>معدل 98.7% • الفرع العلمي • القدس 🇵🇸 (كلية الطب البشري)</span>
                    </div>
                </div>
            </div>

            <div class="testimonial-card-epic">
                <div>
                    <div class="t-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="t-quote">
                        "أعظم ميزة بالمنصة هي بطاقات الاستذكار السريع وقاعة التدريب. كنت أراجع جميع القوانين والمتطابقات قبل الامتحانات التجريبية بنصف ساعة فقط وأدخل بكل هدوء وثقة."
                    </p>
                </div>
                <div class="t-author-row">
                    <div class="t-avatar" style="background: linear-gradient(135deg, #10b981, #059669);">ع</div>
                    <div class="t-meta">
                        <strong>عمر البرغوثي</strong>
                        <span>معدل 97.8% • الفرع العلمي • رام الله 🇵🇸 (هندسة الحاسوب)</span>
                    </div>
                </div>
            </div>

            <div class="testimonial-card-epic">
                <div>
                    <div class="t-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="t-quote">
                        "حاسبة المعدل والتنسيق بالمنصة خلت عندي وضوح تام لهدفي من أول يوم في السنة، والمدرسين المعتمدين كانوا يجاوبوا على استفساراتنا ويوجهونا بطريقة راقية جداً."
                    </p>
                </div>
                <div class="t-author-row">
                    <div class="t-avatar" style="background: linear-gradient(135deg, #f59e0b, #d97706);">ن</div>
                    <div class="t-meta">
                        <strong>نور الدين المصري</strong>
                        <span>معدل 96.9% • فرع الريادة والأعمال • غزة 🇵🇸</span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 9. مركز الأسئلة الأكثر شيوعاً (FAQ Accordion) -->
    <section class="faq-section" id="faq">
        <div class="section-header-epic">
            <div class="section-tag-glow"><i class="fas fa-circle-question"></i> إجابات سريعة وواضحة</div>
            <h2 class="section-title-epic">الأسئلة الأكثر <span>شيوعاً وتكراراً</span></h2>
            <p class="section-subtitle-epic">
                كل ما ترغب بمعرفته حول التسجيل، المواد، والامتحانات الوزارية.
            </p>
        </div>

        <div class="faq-list">
            
            <div class="faq-item-epic open">
                <div class="faq-header" onclick="toggleEpicFaq(this)">
                    <span>هل التسجيل في المنصة مجاني للطلبة؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-content">
                    نعم، يمكنك التسجيل وإنشاء حساب مجاني تماماً والوصول الفوري إلى نماذج الامتحانات التجريبية، وحاسبة التنسيق والمعدل، وقاعة التدريب، وبطاقات القوانين السريعة دون أي رسوم.
                </div>
            </div>

            <div class="faq-item-epic">
                <div class="faq-header" onclick="toggleEpicFaq(this)">
                    <span>هل المناهج والامتحانات مطابقة لمواصفات وزارة التربية والتعليم لدورة 2026؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-content">
                    بكل تأكيد، جميع الشروحات ونماذج الامتحانات المتاحة بالمنصة مبنية ومحدثة بالكامل طبقاً للمنهاج الفلسطيني الرسمي المعتمد وكتب وزارة التربية والتعليم الصادرة لدورة 2026.
                </div>
            </div>

            <div class="faq-item-epic">
                <div class="faq-header" onclick="toggleEpicFaq(this)">
                    <span>كيف تحتسب حاسبة التنسيق بالمنصة درجات الفروع المختلفة؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-content">
                    تعتمد الحاسبة على النظام الوزاري الدقيق لأوزان المباحث (مثل احتساب الرياضيات من 200 علامة للفرع العلمي واللغة العربية للفرع الأدبي)، مع ميزة استخراج التخصصات الجامعية المتاحة لمعدلك مباشرة في الجامعات الفلسطينية.
                </div>
            </div>

            <div class="faq-item-epic">
                <div class="faq-header" onclick="toggleEpicFaq(this)">
                    <span>هل تتوفر مساقات لكافة فروع الثانوية العامة؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-content">
                    نعم، تغطي المنصة الفرع العلمي، الفرع الأدبي، فرع الريادة والأعمال، والفرع الصناعي والتكنولوجي، مع تحديث أسبوعي مستمر لبنك الأسئلة والمواد الإثرائية.
                </div>
            </div>

        </div>
    </section>

    <!-- 10. الراية الختامية الكبرى (Grand Final CTA) -->
    <section class="final-cta-section">
        <div class="final-cta-card">
            <h2>ابدأ رحلة تفوقك وصناعة مستقبلك اليوم</h2>
            <p>
                لا تدع الوقت يمر، انضم الآن لآلاف الطلبة المتميزين في فلسطين واستعد لامتحاناتك الوزارية بكل طمأنينة واضمن مقعدك في الكلية التي تحلم بها.
            </p>
            <a href="{{ route('students.create') }}" class="btn-cta-radiant">
                <span>سجّل حسابك مجاناً الآن وابدأ دراستك</span>
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </section>

    <!-- 11. التذييل الرسمي الفخم (World-Class Footer) -->
    <footer class="global-footer">
        <div class="footer-grid">
            
            <div class="footer-brand">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="brand-emblem" style="width: 38px; height: 38px; font-size: 1.15rem;"><i class="fas fa-graduation-cap"></i></div>
                    <strong style="font-size: 1.2rem; font-weight: 900; color: #ffffff;">{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</strong>
                </div>
                <p>
                    المنظومة الأكاديمية الفلسطينية الرائدة، صُممت لمساندة طلبة الثانوية العامة وتوفير بيئة تعليمية ذكية وشاملة لتحقيق أعلى مراتب التميز والنجاح.
                </p>
                <div class="footer-palestine-badge">
                    <span>🇵🇸 صُنع بإتقان لدعم مسيرة التعليم في فلسطين</span>
                </div>
            </div>

            <div class="footer-col">
                <h5>الفروع والمناهج</h5>
                <ul>
                    <li><a href="#tracks">الفرع العلمي</a></li>
                    <li><a href="#tracks">الفرع الأدبي</a></li>
                    <li><a href="#tracks">فرع الريادة والأعمال</a></li>
                    <li><a href="#tracks">الفرع الصناعي</a></li>
                    <li><a href="{{ route('tawjihi.calculator') }}">حاسبة التنسيق الجامعي</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>أدوات المنظومة</h5>
                <ul>
                    <li><a href="#training-lab">قاعة الاختبارات التفاعلية</a></li>
                    <li><a href="#bento">بنك الامتحانات المحلولة</a></li>
                    <li><a href="#bento">بطاقات الاستذكار السريع</a></li>
                    <li><a href="{{ route('login') }}">مولّد جداول المراجعة</a></li>
                    <li><a href="{{ route('login') }}">لوحة الشرف وتحدي الأوائل</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>المساعدة والدعم</h5>
                <ul>
                    <li><a href="https://wa.me/970597694385" target="_blank" style="color: #4ade80;"><i class="fab fa-whatsapp"></i> واتساب الدعم (+970597694385)</a></li>
                    <li><a href="mailto:{{ \App\Models\Setting::get('contact_email', 'support@tawjihi.ps') }}">البريد الإلكتروني للشكاوى</a></li>
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

    <!-- سكربتات التفاعل الحية والذكية -->
    <script>
        // تفاعل خيارات الهيرو
        function pickHeroOption(element, isCorrect) {
            const feedback = document.getElementById('heroFeedback');
            const parent = element.parentElement;
            parent.querySelectorAll('.c-q-opt').forEach(opt => {
                opt.classList.remove('correct');
                const icon = opt.querySelector('i');
                if (icon) icon.className = 'far fa-circle';
            });

            if (isCorrect) {
                element.classList.add('correct');
                const icon = element.querySelector('i');
                if (icon) icon.className = 'fas fa-circle-check';
                if (feedback) feedback.style.display = 'flex';
            } else {
                alert('إجابة غير دقيقة! حاول مرة أخرى بالاشتقاق والمساواة بالصفر ق\'(س) = 0.');
                if (feedback) feedback.style.display = 'none';
            }
        }

        // تبديل مواد قاعة التدريب
        function switchLabSubject(subjectKey, btnElement) {
            document.querySelectorAll('.lab-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.lab-pane-item').forEach(pane => pane.style.display = 'none');

            btnElement.classList.add('active');
            const targetPane = document.getElementById('lab-' + subjectKey);
            if (targetPane) {
                targetPane.style.display = 'block';
                targetPane.style.animation = 'fadeIn 0.3s ease';
            }
        }

        // تفاعل إجابات قاعة التدريب
        function handleLabAnswer(btn, isCorrect, expId) {
            const expBox = document.getElementById(expId);
            const parent = btn.parentElement;
            
            parent.querySelectorAll('.lab-choice-btn').forEach(b => {
                b.classList.remove('correct-pick', 'wrong-pick');
                const icon = b.querySelector('i');
                if (icon) icon.className = 'far fa-circle';
            });

            if (isCorrect) {
                btn.classList.add('correct-pick');
                const icon = btn.querySelector('i');
                if (icon) icon.className = 'fas fa-circle-check';
                if (expBox) expBox.style.display = 'block';
            } else {
                btn.classList.add('wrong-pick');
                const icon = btn.querySelector('i');
                if (icon) icon.className = 'fas fa-circle-xmark';
                if (expBox) expBox.style.display = 'none';
            }
        }

        // فتح وإغلاق أسئلة الـ FAQ
        function toggleEpicFaq(headerElement) {
            const item = headerElement.parentElement;
            item.classList.toggle('open');
        }

        // حاسبة المعدل والتنسيق بالصفحة الرئيسية
        function calculateLandingGpa() {
            const math = parseFloat(document.getElementById('mathSlider').value) || 194;
            const phys = parseFloat(document.getElementById('physSlider').value) || 97;
            const chem = parseFloat(document.getElementById('chemSlider').value) || 96;
            const arab = parseFloat(document.getElementById('arabSlider').value) || 95;

            document.getElementById('mathScoreVal').textContent = math;
            document.getElementById('physScoreVal').textContent = phys;
            document.getElementById('chemScoreVal').textContent = chem;
            document.getElementById('arabScoreVal').textContent = arab;

            const total = math + phys + chem + arab;
            const max = 500;
            const gpa = ((total / max) * 100).toFixed(1);

            document.getElementById('landingGpaResult').textContent = gpa + '%';

            const majorBox = document.getElementById('landingAdmissionMajor');
            if (gpa >= 95) {
                majorBox.innerHTML = '🎓 يؤهلك مباشرة للمنافسة على: <strong>الطب البشري، طب وجراحة الأسنان، الصيدلة السريرية، هندسة الذكاء الاصطناعي والحاسوب</strong> في الجامعات الفلسطينية الرسمية.';
            } else if (gpa >= 85) {
                majorBox.innerHTML = '🎓 يؤهلك للمنافسة على: <strong>الهندسة المعمارية والمدنية، العلوم الطبية المخبرية والتمريض، تكنولوجيا المعلومات والبرمجيات</strong>.';
            } else if (gpa >= 75) {
                majorBox.innerHTML = '🎓 يؤهلك للمنافسة على: <strong>القانون والعلوم السياسية، إدارة الأعمال والمحاسبة، الإعلام، واللغات والترجمة</strong>.';
            } else {
                majorBox.innerHTML = '🎓 يؤهلك للمنافسة على: <strong>برامج الدبلوم المهني والتقني المتطور، العلوم الإدارية والتطبيقية، والفنون</strong>.';
            }
        }

        // العد التنازلي التفاعلي للثانوية العامة 2026
        function initCountdown() {
            // موعد امتحانات الثانوية العامة 2026 في فلسطين (تقريباً منتصف يونيو 2026)
            const targetDate = new Date('June 15, 2026 09:00:00').getTime();

            function updateTime() {
                const now = new Date().getTime();
                const diff = targetDate - now;

                if (diff > 0) {
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    const elDays = document.getElementById('cdDays');
                    const elHours = document.getElementById('cdHours');
                    const elMins = document.getElementById('cdMins');
                    const elSecs = document.getElementById('cdSecs');

                    if (elDays) elDays.textContent = days;
                    if (elHours) elHours.textContent = hours < 10 ? '0' + hours : hours;
                    if (elMins) elMins.textContent = minutes < 10 ? '0' + minutes : minutes;
                    if (elSecs) elSecs.textContent = seconds < 10 ? '0' + seconds : seconds;
                }
            }

            updateTime();
            setInterval(updateTime, 1000);
        }

        // عداد مؤقت الهيرو الافتراضي
        function initHeroTimer() {
            let totalSeconds = 44 * 60 + 18;
            const timerEl = document.getElementById('heroTimer');
            if (!timerEl) return;

            setInterval(() => {
                if (totalSeconds > 0) {
                    totalSeconds--;
                    const m = Math.floor(totalSeconds / 60);
                    const s = totalSeconds % 60;
                    timerEl.textContent = (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
                }
            }, 1000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            initCountdown();
            initHeroTimer();
            calculateLandingGpa();
        });
    </script>
</body>
</html>
