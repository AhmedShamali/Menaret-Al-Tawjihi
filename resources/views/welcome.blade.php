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

    <title>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} | المنظومة الأكاديمية الذكية للثانوية العامة في فلسطين</title>
    <meta name="description" content="المنصة العلمية الأولى لطلبة الثانوية العامة في فلسطين (توجيهي 2026). شروحات معتمدة، بنك امتحانات وزارية محلولة، بطاقات استذكار ذكية، وحاسبة معدل دقيقة.">

    <!-- الخطوط العالمية الراقية: Readex Pro للأصالة الهندسية + Plus Jakarta Sans للأرقام والمصطلحات -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Readex+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 Pro -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            /* درجات الألوان العالمية الحديثة (Linear / Stripe / Brilliant) */
            --brand-primary: #2563eb;
            --brand-primary-hover: #1d4ed8;
            --brand-accent: #0284c7;
            --brand-cyan: #06b6d4;
            --brand-emerald: #10b981;
            --brand-amber: #f59e0b;
            --brand-purple: #8b5cf6;
            
            --bg-canvas: #fafcff;
            --bg-dark-hero: #090e17;
            --bg-card: rgba(255, 255, 255, 0.9);
            --bg-card-hover: #ffffff;
            --border-glass: rgba(226, 232, 240, 0.85);
            --border-glow: rgba(37, 99, 235, 0.25);

            --text-heading: #090e17;
            --text-body: #334155;
            --text-muted: #64748b;
            --text-light: #f8fafc;

            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 28px;
            --radius-pill: 9999px;

            --shadow-subtle: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
            --shadow-card: 0 20px 40px -15px rgba(15, 23, 42, 0.06), 0 0 0 1px rgba(226, 232, 240, 0.7);
            --shadow-card-hover: 0 30px 60px -12px rgba(37, 99, 235, 0.12), 0 0 0 1px rgba(37, 99, 235, 0.25);
            --shadow-glow-blue: 0 10px 30px -4px rgba(37, 99, 235, 0.4);

            --transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
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
            background-color: var(--bg-canvas);
            color: var(--text-body);
            line-height: 1.7;
            overflow-x: hidden;
            background-image: 
                radial-gradient(at 15% 10%, rgba(37, 99, 235, 0.05) 0px, transparent 50%),
                radial-gradient(at 85% 30%, rgba(6, 182, 212, 0.04) 0px, transparent 50%),
                radial-gradient(at 50% 85%, rgba(139, 92, 246, 0.03) 0px, transparent 50%);
            background-attachment: fixed;
        }

        /* شبكة الخلفية العلمية الدقيقة (Subtle Scientific Grid) */
        .scientific-bg-grid {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 900px;
            background-image: 
                linear-gradient(to right, rgba(226, 232, 240, 0.4) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(226, 232, 240, 0.4) 1px, transparent 1px);
            background-size: 50px 50px;
            mask-image: radial-gradient(ellipse 70% 60% at 50% 20%, #000 30%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 20%, #000 30%, transparent 80%);
            pointer-events: none;
            z-index: 0;
        }

        /* --- 1. شريط التنقل العائم فائق النقاء (Floating Island Navbar) --- */
        .nav-wrapper {
            position: sticky;
            top: 14px;
            z-index: 1000;
            padding: 0 20px;
            max-width: 1260px;
            margin: 0 auto;
        }

        nav.island-nav {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-pill);
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.07), 0 1px 2px rgba(15, 23, 42, 0.03);
            transition: var(--transition);
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-heading);
        }

        .brand-emblem {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 1.15rem;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            transition: var(--transition);
        }

        .brand-link:hover .brand-emblem {
            transform: scale(1.05) rotate(-3deg);
        }

        .brand-titles strong {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-badge {
            font-size: 0.65rem;
            font-weight: 700;
            background: #eff6ff;
            color: #2563eb;
            padding: 2px 8px;
            border-radius: var(--radius-pill);
            border: 1px solid #bfdbfe;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
        }

        @media (max-width: 980px) {
            .nav-menu { display: none; }
        }

        .nav-menu a {
            text-decoration: none;
            color: #475569;
            font-size: 0.86rem;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: var(--radius-pill);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-menu a:hover {
            color: var(--brand-primary);
            background: rgba(37, 99, 235, 0.08);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-ghost-login {
            text-decoration: none;
            color: var(--text-heading);
            font-size: 0.86rem;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            transition: var(--transition);
        }

        .btn-ghost-login:hover {
            background: #f1f5f9;
            color: var(--brand-primary);
        }

        .btn-primary-pill {
            text-decoration: none;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            font-size: 0.86rem;
            font-weight: 700;
            padding: 9px 20px;
            border-radius: var(--radius-pill);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-primary-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(37, 99, 235, 0.4);
            color: #ffffff;
        }

        .btn-primary-pill i {
            font-size: 0.8rem;
            transition: transform 0.2s;
        }

        .btn-primary-pill:hover i {
            transform: translateX(-3px);
        }

        /* --- 2. قسم الهيرو الشاهق فائق الفخامة (Majestic Centered Hero) --- */
        .hero-section {
            position: relative;
            padding: 75px 24px 70px;
            text-align: center;
            z-index: 1;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 18px;
            background: rgba(37, 99, 235, 0.07);
            border: 1px solid rgba(37, 99, 235, 0.2);
            color: var(--brand-primary);
            border-radius: var(--radius-pill);
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 24px;
            box-shadow: 0 2px 10px rgba(37, 99, 235, 0.05);
            backdrop-filter: blur(8px);
        }

        .hero-chip .pulse-green {
            width: 7px;
            height: 7px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 8px #10b981;
            animation: pulseGlow 2s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.4); opacity: 1; }
        }

        .hero-title {
            font-size: clamp(2.4rem, 5vw, 4.2rem);
            font-weight: 800;
            line-height: 1.25;
            color: var(--text-heading);
            letter-spacing: -0.025em;
            max-width: 960px;
            margin: 0 auto 24px;
        }

        .hero-title-highlight {
            background: linear-gradient(135deg, #1d4ed8 0%, #0284c7 45%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        .hero-desc {
            font-size: clamp(1rem, 1.8vw, 1.18rem);
            color: #475569;
            max-width: 720px;
            margin: 0 auto 38px;
            line-height: 1.9;
            font-weight: 400;
        }

        .hero-cta-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 45px;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 15px 34px;
            border-radius: var(--radius-pill);
            font-size: 1.02rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--shadow-glow-blue);
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 36px -6px rgba(37, 99, 235, 0.5);
            color: #ffffff;
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.95);
            color: var(--text-heading);
            text-decoration: none;
            padding: 15px 28px;
            border-radius: var(--radius-pill);
            font-size: 1rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--border-glass);
            box-shadow: var(--shadow-subtle);
            transition: var(--transition);
        }

        .btn-hero-secondary:hover {
            background: #ffffff;
            border-color: var(--brand-primary);
            color: var(--brand-primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-card);
        }

        .hero-proof-strip {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 28px;
            flex-wrap: wrap;
            font-size: 0.84rem;
            color: var(--text-muted);
        }

        .proof-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .proof-item i {
            color: #10b981;
            font-size: 0.95rem;
        }

        /* --- 3. محرك التعلم التفاعلي المباشر (Interactive Learning Canvas) --- */
        .canvas-section {
            max-width: 1140px;
            margin: 0 auto 90px;
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }

        .interactive-canvas {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: var(--radius-xl);
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.09), 0 0 0 1px rgba(255, 255, 255, 0.9);
            overflow: hidden;
            position: relative;
        }

        .canvas-topbar {
            background: #f8fafc;
            border-bottom: 1px solid var(--border-glass);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .canvas-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .canvas-tab-btn {
            background: transparent;
            border: 1px solid transparent;
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            font-size: 0.84rem;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .canvas-tab-btn:hover {
            color: var(--brand-primary);
            background: rgba(37, 99, 235, 0.05);
        }

        .canvas-tab-btn.active {
            background: #ffffff;
            border-color: #cbd5e1;
            color: var(--brand-primary);
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            font-weight: 700;
        }

        .canvas-status {
            font-size: 0.78rem;
            color: #10b981;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .canvas-body {
            padding: 36px 32px;
            background: #ffffff;
            min-height: 380px;
        }

        @media (max-width: 768px) {
            .canvas-body { padding: 24px 18px; }
        }

        /* تبويب 1: محاكي الامتحانات الذكي */
        .exam-sim-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 32px;
            align-items: center;
        }

        @media (max-width: 900px) {
            .exam-sim-grid { grid-template-columns: 1fr; gap: 24px; }
        }

        .sim-question-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-lg);
            padding: 24px;
        }

        .sim-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .sim-tag {
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 0.74rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: var(--radius-pill);
            border: 1px solid #bfdbfe;
        }

        .sim-q-text {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .sim-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 18px;
        }

        .sim-opt-btn {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 12px 18px;
            text-align: right;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-heading);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sim-opt-btn:hover {
            border-color: var(--brand-primary);
            background: #f0f7ff;
        }

        .sim-opt-btn.correct-active {
            border-color: #10b981;
            background: #ecfdf5;
            color: #065f46;
        }

        .sim-feedback {
            display: none;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            font-size: 0.86rem;
            font-weight: 600;
            margin-top: 14px;
            animation: fadeIn 0.3s ease;
        }

        .sim-stats-card {
            background: linear-gradient(135deg, #090e17 0%, #1e3a8a 100%);
            border-radius: var(--radius-lg);
            padding: 28px;
            color: #ffffff;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.2);
            position: relative;
            overflow: hidden;
        }

        .sim-sc-title {
            font-size: 0.85rem;
            color: #93c5fd;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .sim-sc-stat {
            font-size: 2.8rem;
            font-weight: 800;
            color: #38bdf8;
            line-height: 1;
            margin-bottom: 12px;
        }

        .sim-sc-desc {
            font-size: 0.86rem;
            color: #cbd5e1;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .sim-sc-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .sc-pill {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            padding: 4px 12px;
            border-radius: var(--radius-pill);
            font-size: 0.74rem;
            font-weight: 600;
            color: #ffffff;
        }

        /* --- 4. شبكة البينتو العالمية الفاخرة (Modern Bento Grid) --- */
        .bento-section {
            max-width: 1260px;
            margin: 0 auto 100px;
            padding: 0 24px;
        }

        .section-header-center {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 50px;
        }

        .section-sub-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 16px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: var(--radius-pill);
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 14px;
            border: 1px solid #bfdbfe;
        }

        .section-title-bold {
            font-size: clamp(1.8rem, 3.5vw, 2.6rem);
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -0.02em;
            margin-bottom: 14px;
        }

        .section-lead-text {
            font-size: 1.05rem;
            color: #64748b;
            line-height: 1.8;
        }

        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
        }

        .bento-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-xl);
            padding: 34px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-card);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .bento-card:hover {
            transform: translateY(-5px);
            border-color: rgba(37, 99, 235, 0.4);
            box-shadow: var(--shadow-card-hover);
        }

        .bento-span-7 { grid-column: span 7; }
        .bento-span-5 { grid-column: span 5; }
        .bento-span-4 { grid-column: span 4; }
        .bento-span-8 { grid-column: span 8; }
        .bento-span-6 { grid-column: span 6; }

        @media (max-width: 980px) {
            .bento-span-7, .bento-span-5, .bento-span-4, .bento-span-8, .bento-span-6 {
                grid-column: span 12;
            }
        }

        .bento-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.4rem;
            margin-bottom: 22px;
        }

        .bento-card h3 {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 12px;
            letter-spacing: -0.01em;
        }

        .bento-card p {
            font-size: 0.92rem;
            color: #64748b;
            line-height: 1.75;
            margin-bottom: 24px;
        }

        .bento-mockup-graphic {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 16px;
            position: relative;
        }

        /* --- 5. فروع الثانوية العامة التفاعلية (Curriculum Tracks) --- */
        .branches-container {
            max-width: 1260px;
            margin: 0 auto 100px;
            padding: 0 24px;
        }

        .branches-3col {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 26px;
        }

        @media (max-width: 960px) {
            .branches-3col { grid-template-columns: 1fr; }
        }

        .track-card {
            background: #ffffff;
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-xl);
            padding: 34px 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: var(--transition);
            box-shadow: var(--shadow-card);
            position: relative;
            overflow: hidden;
        }

        .track-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--track-color, var(--brand-primary));
        }

        .track-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-card-hover);
        }

        .track-sci { --track-color: #2563eb; }
        .track-lit { --track-color: #d97706; }
        .track-bus { --track-color: #059669; }

        .track-icon-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .track-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.5rem;
        }

        .track-sci .track-icon { background: #eff6ff; color: #2563eb; }
        .track-lit .track-icon { background: #fefce8; color: #ca8a04; }
        .track-bus .track-icon { background: #ecfdf5; color: #059669; }

        .track-tag {
            font-size: 0.74rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: var(--radius-pill);
            background: #f1f5f9;
            color: #475569;
        }

        .track-card h3 {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 10px;
        }

        .track-card p {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 22px;
        }

        .subject-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-bottom: 26px;
        }

        .subject-chip {
            font-size: 0.76rem;
            font-weight: 600;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 4px 11px;
            border-radius: var(--radius-sm);
            color: var(--text-body);
        }

        .track-action-btn {
            text-decoration: none;
            padding: 12px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1.5px solid var(--border-glass);
            background: #ffffff;
            color: var(--text-heading);
            transition: var(--transition);
        }

        .track-card:hover .track-action-btn {
            background: var(--brand-primary);
            color: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        }

        /* --- 6. حاسبة المعدل التنبؤية والتنسيق الجامعي --- */
        .calculator-showcase {
            max-width: 1000px;
            margin: 0 auto 100px;
            padding: 0 24px;
        }

        .calc-panel {
            background: #ffffff;
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-xl);
            padding: 40px;
            box-shadow: var(--shadow-card);
        }

        @media (max-width: 768px) {
            .calc-panel { padding: 24px 18px; }
        }

        .calc-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        @media (max-width: 700px) {
            .calc-grid { grid-template-columns: 1fr; }
        }

        .calc-slider-card {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: var(--radius-md);
            padding: 16px 18px;
            transition: var(--transition);
        }

        .calc-slider-card:hover {
            border-color: #cbd5e1;
            background: #ffffff;
        }

        .calc-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .calc-header-row label {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text-heading);
        }

        .calc-header-row .val-badge {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--brand-primary);
        }

        .range-slider-input {
            width: 100%;
            accent-color: var(--brand-primary);
            cursor: pointer;
            height: 6px;
        }

        .calc-verdict-box {
            background: linear-gradient(135deg, #090e17 0%, #1e3a8a 100%);
            color: #ffffff;
            border-radius: var(--radius-lg);
            padding: 26px 34px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 12px 30px -5px rgba(15, 23, 42, 0.25);
        }

        .calc-verdict-info h4 {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .calc-verdict-info p {
            font-size: 0.85rem;
            color: #cbd5e1;
        }

        .calc-live-gpa {
            font-size: 2.9rem;
            font-weight: 800;
            color: #38bdf8;
            line-height: 1;
        }

        /* --- 7. آراء وتجارب أوائل التوجيهي (Testimonials) --- */
        .testimonials-section {
            max-width: 1260px;
            margin: 0 auto 100px;
            padding: 0 24px;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        @media (max-width: 960px) {
            .testimonials-grid { grid-template-columns: 1fr; }
        }

        .testimonial-card {
            background: #ffffff;
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-subtle);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: var(--transition);
        }

        .testimonial-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-card);
        }

        .stars-row {
            color: #f59e0b;
            font-size: 0.85rem;
            display: flex;
            gap: 3px;
            margin-bottom: 14px;
        }

        .testimonial-quote {
            font-size: 0.92rem;
            color: var(--text-body);
            line-height: 1.8;
            margin-bottom: 22px;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .author-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #eff6ff;
            color: #2563eb;
            font-weight: 800;
            display: grid;
            place-items: center;
            font-size: 0.95rem;
            border: 1.5px solid #bfdbfe;
        }

        .author-meta strong {
            display: block;
            font-size: 0.9rem;
            color: var(--text-heading);
            font-weight: 700;
        }

        .author-meta span {
            font-size: 0.76rem;
            color: var(--text-muted);
        }

        /* --- 8. الأسئلة الأكثر شيوعاً (FAQ Accordion) --- */
        .faq-container {
            max-width: 820px;
            margin: 0 auto 100px;
            padding: 0 24px;
        }

        .faq-accordion-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .faq-accordion-item {
            background: #ffffff;
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-md);
            overflow: hidden;
            transition: var(--transition);
        }

        .faq-trigger {
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

        .faq-trigger i {
            font-size: 0.82rem;
            color: var(--text-muted);
            transition: transform 0.3s;
        }

        .faq-body-content {
            display: none;
            padding: 0 22px 18px;
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.75;
        }

        .faq-accordion-item.open {
            border-color: var(--brand-primary);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.08);
        }

        .faq-accordion-item.open .faq-trigger {
            color: var(--brand-primary);
        }

        .faq-accordion-item.open .faq-trigger i {
            transform: rotate(180deg);
            color: var(--brand-primary);
        }

        .faq-accordion-item.open .faq-body-content {
            display: block;
        }

        /* --- 9. الراية الختامية الشاملة (Final Call-to-Action) --- */
        .cta-banner-wrapper {
            max-width: 1260px;
            margin: 0 auto 70px;
            padding: 0 24px;
        }

        .cta-banner-card {
            background: linear-gradient(135deg, #090e17 0%, #1e3a8a 60%, #1d4ed8 100%);
            border-radius: var(--radius-xl);
            padding: 70px 36px;
            text-align: center;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 24px 50px -10px rgba(15, 23, 42, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .cta-banner-card h2 {
            font-size: clamp(2rem, 4vw, 2.7rem);
            font-weight: 800;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }

        .cta-banner-card p {
            font-size: 1.08rem;
            color: #cbd5e1;
            max-width: 640px;
            margin: 0 auto 36px;
            line-height: 1.8;
            font-weight: 400;
        }

        .btn-cta-white {
            background: #ffffff;
            color: var(--brand-primary);
            text-decoration: none;
            padding: 15px 36px;
            border-radius: var(--radius-pill);
            font-size: 1.02rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            transition: var(--transition);
        }

        .btn-cta-white:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 36px rgba(0,0,0,0.3);
            background: #f8fafc;
        }

        /* --- 10. التذييل الرسمي الفخم (World-Class Footer) --- */
        footer.global-footer {
            background: #090e17;
            color: #ffffff;
            padding: 65px 24px 30px;
            border-top: 1px solid #1e293b;
        }

        .footer-content-grid {
            max-width: 1260px;
            margin: 0 auto 40px;
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 40px;
        }

        @media (max-width: 900px) { .footer-content-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 600px) { .footer-content-grid { grid-template-columns: 1fr; } }

        .footer-brand-bio p {
            color: #94a3b8;
            font-size: 0.86rem;
            line-height: 1.8;
            margin: 16px 0 20px;
        }

        .footer-col-nav h5 {
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: 18px;
            color: #ffffff;
        }

        .footer-col-nav ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-col-nav ul a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.85rem;
            transition: var(--transition);
        }

        .footer-col-nav ul a:hover {
            color: #ffffff;
            transform: translateX(-3px);
            display: inline-block;
        }

        .footer-bottom-bar {
            max-width: 1260px;
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

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <!-- شبكة الخطوط العلمية الخفيفة بالخلفية -->
    <div class="scientific-bg-grid"></div>

    <!-- 1. شريط التنقل العائم فائق الأناقة (Floating Island Navbar) -->
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
                <li><a href="#interactive-engine"><i class="fas fa-bolt" style="font-size: 0.78rem; color: #2563eb;"></i> المحاكي التفاعلي</a></li>
                <li><a href="#bento-features"><i class="fas fa-cubes" style="font-size: 0.78rem; color: #2563eb;"></i> منظومة التفوق</a></li>
                <li><a href="#tracks"><i class="fas fa-layer-group" style="font-size: 0.78rem; color: #2563eb;"></i> الفروع والمناهج</a></li>
                <li><a href="#calculator"><i class="fas fa-calculator" style="font-size: 0.78rem; color: #2563eb;"></i> حاسبة التنسيق</a></li>
                <li><a href="#faq"><i class="fas fa-circle-question" style="font-size: 0.78rem; color: #2563eb;"></i> الأسئلة الشائعة</a></li>
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
                    <a href="{{ route('login') }}" class="btn-ghost-login">تسجيل الدخول</a>
                    <a href="{{ route('students.create') }}" class="btn-primary-pill">
                        <span>ابدأ مجاناً</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    <!-- 2. قسم الهيرو الشاهق المبتكر (Centered Majestic Hero) -->
    <section class="hero-section">
        <div class="hero-chip">
            <span class="pulse-green"></span>
            <span>المنظومة الأكاديمية الأولى لشهادة الثانوية العامة في فلسطين لدورة 2026 🇵🇸</span>
        </div>

        <h1 class="hero-title">
            المسار العلمي المعتمد <br>
            <span class="hero-title-highlight">للتفوق في الثانوية العامة</span>
        </h1>

        <p class="hero-desc">
            منصة تعليمية متطورة توفر لطلبة التوجيهي بنك الامتحانات الوزارية المحلولة، بطاقات الاستذكار الذكية، حاسبة التنسيق الجامعي، وشروحات نخبة مدرسي فلسطين لتحقيق أعلى المراتب بكل ثقة.
        </p>

        <div class="hero-cta-group">
            <a href="{{ route('students.create') }}" class="btn-hero-primary">
                <span>ابدأ دراستك الآن مجاناً</span>
                <i class="fas fa-arrow-left"></i>
            </a>
            <a href="#interactive-engine" class="btn-hero-secondary">
                <i class="fas fa-play-circle" style="color: var(--brand-primary); font-size: 1.1rem;"></i>
                <span>تجربة المحاكي والامتحانات</span>
            </a>
        </div>

        <div class="hero-proof-strip">
            <div class="proof-item">
                <i class="fas fa-circle-check"></i>
                <span>أكثر من 15,000 طالب وطالبة</span>
            </div>
            <div class="proof-item">
                <i class="fas fa-circle-check"></i>
                <span>نسبة تفوق ونجاح 99.2%</span>
            </div>
            <div class="proof-item">
                <i class="fas fa-circle-check"></i>
                <span>مطابقة 100% لمنهاج وزارة التربية والتعليم</span>
            </div>
            <div class="proof-item">
                <i class="fas fa-circle-check"></i>
                <span>شهادات إنجاز موثقة برمز QR</span>
            </div>
        </div>
    </section>

    <!-- 3. محرك التعلم والمحاكاة التفاعلي المباشر (Interactive Canvas Engine) -->
    <section class="canvas-section" id="interactive-engine">
        <div class="interactive-canvas">
            
            <div class="canvas-topbar">
                <div class="canvas-tabs">
                    <button type="button" class="canvas-tab-btn active" onclick="switchCanvasTab('exam', this)">
                        <i class="fas fa-pen-to-square"></i>
                        <span>محاكي الامتحانات الذكي</span>
                    </button>
                    <button type="button" class="canvas-tab-btn" onclick="switchCanvasTab('gpa', this)">
                        <i class="fas fa-calculator"></i>
                        <span>حاسبة التنسيق الجامعي</span>
                    </button>
                    <button type="button" class="canvas-tab-btn" onclick="switchCanvasTab('flashcards', this)">
                        <i class="fas fa-bolt"></i>
                        <span>بطاقات القوانين السريعة</span>
                    </button>
                </div>
                <div class="canvas-status">
                    <i class="fas fa-circle" style="font-size: 0.55rem;"></i>
                    <span>جاهز للتفاعل الفوري</span>
                </div>
            </div>

            <div class="canvas-body">
                
                <!-- تبويب 1: محاكي الامتحانات -->
                <div id="tab-exam" class="canvas-content-pane">
                    <div class="exam-sim-grid">
                        <div class="sim-question-box">
                            <div class="sim-header">
                                <span class="sim-tag">سؤال وزاري استرشادي 2026 • الرياضيات (علمي)</span>
                                <span style="font-size: 0.78rem; font-weight: 700; color: #64748b;">(علامتان)</span>
                            </div>

                            <div class="sim-q-text">
                                إذا كان الاقتران: <span class="num-font" style="color: var(--brand-primary);">ق(س) = س³ - 3س + 5</span>، فما هي النقاط الحرجة للاقتران على مجموعة الأعداد الحقيقية؟
                            </div>

                            <div class="sim-options">
                                <button type="button" class="sim-opt-btn" onclick="checkExamAnswer(true, this)">
                                    <span>أ) س = ± 1</span>
                                    <i class="far fa-circle"></i>
                                </button>
                                <button type="button" class="sim-opt-btn" onclick="checkExamAnswer(false, this)">
                                    <span>ب) س = ± 3</span>
                                    <i class="far fa-circle"></i>
                                </button>
                                <button type="button" class="sim-opt-btn" onclick="checkExamAnswer(false, this)">
                                    <span>ج) س = 0 فقط</span>
                                    <i class="far fa-circle"></i>
                                </button>
                            </div>

                            <div class="sim-feedback" id="examFeedback">
                                <i class="fas fa-circle-check"></i>
                                <span>إجابة صحيحة وممتازة! 🎉 المشتقة ق'(س) = 3س² - 3 = 0 ومنها س² = 1 إذن س = ± 1.</span>
                            </div>
                        </div>

                        <div class="sim-stats-card">
                            <div class="sim-sc-title">بنك الامتحانات الوزارية المحلولة</div>
                            <div class="sim-sc-stat num-font">{{ $stats['exams'] ?? 18 }}+</div>
                            <p class="sim-sc-desc">
                                نماذج امتحانات رسمية وتجريبية تحاكي نظام القبول الوزاري تماماً مع حلول نموذجية خطوة بخطوة وتصنيف لمستوى الصعوبة.
                            </p>
                            <div class="sim-sc-pills">
                                <span class="sc-pill">تصحيح ذكي فوري</span>
                                <span class="sc-pill">تحليل نقاط الضعف</span>
                                <span class="sc-pill">توقيت حقيقي للاختبار</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب 2: حاسبة التنسيق -->
                <div id="tab-gpa" class="canvas-content-pane" style="display: none;">
                    <div style="text-align: center; max-width: 600px; margin: 0 auto;">
                        <h4 style="font-size: 1.25rem; font-weight: 800; color: var(--text-heading); margin-bottom: 8px;">
                            حاسبة المعدل التقديري وتنسيق الكليات الفلسطينية 2026
                        </h4>
                        <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 24px;">
                            حدد المعدل المتوقع لترى قائمة الكليات والجامعات المتاحة لك فورياً (جامعة النجاح، بيرزيت، القدس، الجامعة الإسلامية، البوليتكنك).
                        </p>
                        
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--radius-lg); padding: 24px; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <span style="font-weight: 700; color: var(--text-heading);">المعدل المتوقع:</span>
                                <span id="quickGpaVal" class="num-font" style="font-size: 1.6rem; font-weight: 800; color: var(--brand-primary);">96.5%</span>
                            </div>
                            <input type="range" min="65" max="100" value="96.5" step="0.5" class="range-slider-input" oninput="updateQuickGpa(this.value)">
                            <div style="display: flex; justify-content: space-between; font-size: 0.76rem; color: var(--text-muted); margin-top: 6px;" class="num-font">
                                <span>65%</span>
                                <span>80%</span>
                                <span>90%</span>
                                <span>100%</span>
                            </div>
                        </div>

                        <div id="quickMajorResult" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: var(--radius-md); padding: 14px 20px; color: #1e40af; font-size: 0.9rem; font-weight: 600;">
                            🎓 يؤهلك مباشرة للتسجيل في: <strong>الطب البشري، طب وجراحة الأسنان، الهندسة المعمارية وهندسة الحاسوب</strong>.
                        </div>
                    </div>
                </div>

                <!-- تبويب 3: بطاقات القوانين السريعة -->
                <div id="tab-flashcards" class="canvas-content-pane" style="display: none;">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--radius-md); padding: 20px; text-align: center;">
                            <span style="font-size: 0.76rem; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 2px 8px; border-radius: var(--radius-pill);">فيزياء • الزخم الخطي</span>
                            <div style="font-size: 1.25rem; font-weight: 800; margin: 14px 0; color: var(--text-heading);" class="num-font">خ = ك × ع</div>
                            <p style="font-size: 0.8rem; color: var(--text-muted);">الزخم كمية متجهة يكون دائماً بنفس اتجاه السرعة ووحدته كغم.م/ث.</p>
                        </div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--radius-md); padding: 20px; text-align: center;">
                            <span style="font-size: 0.76rem; font-weight: 700; color: #059669; background: #ecfdf5; padding: 2px 8px; border-radius: var(--radius-pill);">رياضيات • قاعدة لوبيتال</span>
                            <div style="font-size: 1.25rem; font-weight: 800; margin: 14px 0; color: var(--text-heading);" class="num-font">نهـ ق(س)/هـ(س) = ق'/هـ'</div>
                            <p style="font-size: 0.8rem; color: var(--text-muted);">تُطبق فقط عند الحصول على صيغة غير معينة مثل (0/0) أو (∞/∞).</p>
                        </div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--radius-md); padding: 20px; text-align: center;">
                            <span style="font-size: 0.76rem; font-weight: 700; color: #d97706; background: #fefce8; padding: 2px 8px; border-radius: var(--radius-pill);">كيمياء • قانون بويل</span>
                            <div style="font-size: 1.25rem; font-weight: 800; margin: 14px 0; color: var(--text-heading);" class="num-font">ض₁ × ح₁ = ض₂ × ح₂</div>
                            <p style="font-size: 0.8rem; color: var(--text-muted);">يتناسب حجم الغاز عكسياً مع ضغطه عند ثبوت درجة الحرارة.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 4. شبكة البينتو المتطورة (Modern Bento Grid Showcase) -->
    <section class="bento-section" id="bento-features">
        <div class="section-header-center">
            <div class="section-sub-badge"><i class="fas fa-sparkles"></i> المنظومة المتكاملة</div>
            <h2 class="section-title-bold">أدوات ذكية صُممت لنيل المرتبة الأولى</h2>
            <p class="section-lead-text">
                كل ما يحتاجه طالب الثانوية العامة للوصول للدرجة الكاملة تحت سقف منصة واحدة متناسقة وموثوقة.
            </p>
        </div>

        <div class="bento-grid">
            
            <!-- بطاقة 1 (كبيرة 7 أعمدة): بنك الامتحانات -->
            <div class="bento-card bento-span-7">
                <div>
                    <div class="bento-icon-wrapper" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h3>بنك الامتحانات الوزارية المحلولة لدورة 2026</h3>
                    <p>
                        أكبر مستودع رقمي لأسئلة التوجيهي الوزارية السابقة والامتحانات التجريبية لمديريات فلسطين كافة، مصنفة حسب الوحدات والمستويات، مع تصحيح ذكي فوري وتفسير دقيق للحلول النموذجية.
                    </p>
                </div>
                <div class="bento-mockup-graphic" style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #dbeafe; color: #1e40af; display: grid; place-items: center; font-weight: 800;">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <strong style="font-size: 0.88rem; display: block; color: var(--text-heading);">الامتحان النهائي 2025/2026</strong>
                            <span style="font-size: 0.76rem; color: #64748b;">محلول ومطابق بنسبة 100%</span>
                        </div>
                    </div>
                    <span style="font-size: 0.78rem; font-weight: 700; color: #10b981; background: #ecfdf5; padding: 4px 10px; border-radius: var(--radius-pill);">
                        نموذج وزاري معتمد
                    </span>
                </div>
            </div>

            <!-- بطاقة 2 (5 أعمدة): بطاقات الاستذكار السريع -->
            <div class="bento-card bento-span-5">
                <div>
                    <div class="bento-icon-wrapper" style="background: #fefce8; color: #ca8a04;">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>بطاقات الاستذكار السريع 3D</h3>
                    <p>
                        ثبّت القوانين والمتطابقات والتعريفات الصعبة بأسلوب البطاقات التفاعلية لتراجع كامل المادة ليلة الامتحان خلال 30 دقيقة فقط بدون أي تشتت.
                    </p>
                </div>
                <div style="background: #fffbeb; border: 1px dashed #fde68a; border-radius: var(--radius-md); padding: 14px; text-align: center;">
                    <span style="font-size: 0.82rem; font-weight: 700; color: #92400e;">
                        ⚡ متوفر لأكثر من 450+ قانون ومعادلة وزارية
                    </span>
                </div>
            </div>

            <!-- بطاقة 3 (4 أعمدة): الشهادات المعتمدة -->
            <div class="bento-card bento-span-4">
                <div>
                    <div class="bento-icon-wrapper" style="background: #ecfdf5; color: #059669;">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>شهادات تفوق موثقة برمز QR</h3>
                    <p>
                        احصل على شهادات إنجاز معتمدة بعد إتمام المساقات والامتحانات التجريبية مع كود رقمي للتحقق الفوري.
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: #059669; font-weight: 700;">
                    <i class="fas fa-shield-halved"></i>
                    <span>نظام التوثيق الأكاديمي الرسمي</span>
                </div>
            </div>

            <!-- بطاقة 4 (4 أعمدة): مجتمع وتواصل المعلمين -->
            <div class="bento-card bento-span-4">
                <div>
                    <div class="bento-icon-wrapper" style="background: #fdf4ff; color: #a21caf;">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>تواصل مباشر مع نخبة المعلمين</h3>
                    <p>
                        اطرح أسئلتك واستفساراتك حول أي مسألة أو فكرة صعبة واحصل على توضيح وشرح مخصص من مدرس المادة مباشرة.
                    </p>
                </div>
                <div style="font-size: 0.8rem; color: #a21caf; font-weight: 700;">
                    💬 دعم دراسي مستمر 24/7
                </div>
            </div>

            <!-- بطاقة 5 (4 أعمدة): الجدول الدراسي الذكي -->
            <div class="bento-card bento-span-4">
                <div>
                    <div class="bento-icon-wrapper" style="background: #eff6ff; color: #1d4ed8;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>مخطط المراجعة ومؤقت التركيز</h3>
                    <p>
                        توليد جداول دراسية مخصصة تحسب لك الساعات المتبقية للامتحان وتوزع حصص المراجعة بحسب درجة صعوبة كل مبحث.
                    </p>
                </div>
                <div style="font-size: 0.8rem; color: #1d4ed8; font-weight: 700;">
                    ⏱️ تقنية بومودورو للتركيز الأقصى
                </div>
            </div>

        </div>
    </section>

    <!-- 5. فروع الثانوية العامة ومناهجها (Curriculum Tracks) -->
    <section class="branches-container" id="tracks">
        <div class="section-header-center">
            <div class="section-sub-badge"><i class="fas fa-layer-group"></i> الفروع المعتمدة</div>
            <h2 class="section-title-bold">اختر فرعك وتصفح المناهج المقررة</h2>
            <p class="section-lead-text">شروحات ومواد متكاملة معدة خصيصاً لكل تخصص من فروع الثانوية العامة الفلسطينية.</p>
        </div>

        <div class="branches-3col">
            
            <!-- العلمي -->
            <div class="track-card track-sci">
                <div>
                    <div class="track-icon-row">
                        <div class="track-icon"><i class="fas fa-atom"></i></div>
                        <span class="track-tag">الفرع العلمي</span>
                    </div>
                    <h3>الثانوية العامة - الفرع العلمي</h3>
                    <p>مخصص للطلبة الطامحين في دراسة الطب، الهندسة، الصيدلة، وتكنولوجيا المعلومات والذكاء الاصطناعي.</p>
                    
                    <div class="subject-chip-list">
                        <span class="subject-chip">📐 الرياضيات (200)</span>
                        <span class="subject-chip">⚛️ الفيزياء (100)</span>
                        <span class="subject-chip">🧪 الكيمياء (100)</span>
                        <span class="subject-chip">🧬 العلوم الحياتية</span>
                        <span class="subject-chip">📜 اللغة العربية</span>
                        <span class="subject-chip">📖 اللغة الإنجليزية</span>
                    </div>
                </div>

                <a href="{{ route('students.create') }}" class="track-action-btn">
                    <span>انضم لمواد الفرع العلمي</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <!-- الأدبي -->
            <div class="track-card track-lit">
                <div>
                    <div class="track-icon-row">
                        <div class="track-icon"><i class="fas fa-book-open-reader"></i></div>
                        <span class="track-tag">الفرع الأدبي</span>
                    </div>
                    <h3>الثانوية العامة - الفرع الأدبي</h3>
                    <p>موجه للراغبين في دراسة القانون، العلوم السياسية، اللغات والترجمة، الإعلام، والعلوم الإنسانية.</p>
                    
                    <div class="subject-chip-list">
                        <span class="subject-chip">📜 اللغة العربية (200)</span>
                        <span class="subject-chip">📖 اللغة الإنجليزية (150)</span>
                        <span class="subject-chip">🏛️ الدراسات التاريخية</span>
                        <span class="subject-chip">🌍 الدراسات الجغرافية</span>
                        <span class="subject-chip">📐 الرياضيات الأدبية</span>
                        <span class="subject-chip">🌙 التربية الإسلامية</span>
                    </div>
                </div>

                <a href="{{ route('students.create') }}" class="track-action-btn">
                    <span>انضم لمواد الفرع الأدبي</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <!-- الريادة والأعمال -->
            <div class="track-card track-bus">
                <div>
                    <div class="track-icon-row">
                        <div class="track-icon"><i class="fas fa-chart-pie"></i></div>
                        <span class="track-tag">الريادة والأعمال</span>
                    </div>
                    <h3>فرع الريادة والأعمال (التجاري)</h3>
                    <p>للطموحين في تخصصات إدارة الأعمال، المحاسبة، التمويل والمصارف، والتجارة والتسويق الرقمي.</p>
                    
                    <div class="subject-chip-list">
                        <span class="subject-chip">💼 المشاريع الصغيرة</span>
                        <span class="subject-chip">📊 المحاسبة المالية</span>
                        <span class="subject-chip">🏢 الإدارة والاقتصاد</span>
                        <span class="subject-chip">📐 الرياضيات</span>
                        <span class="subject-chip">📜 اللغة العربية</span>
                        <span class="subject-chip">💻 التكنولوجيا</span>
                    </div>
                </div>

                <a href="{{ route('students.create') }}" class="track-action-btn">
                    <span>انضم لمواد الريادة والأعمال</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

        </div>
    </section>

    <!-- 6. حاسبة المعدل التنبؤية المتكاملة (Predictive GPA Calculator) -->
    <section class="calculator-showcase" id="calculator">
        <div class="section-header-center">
            <div class="section-sub-badge"><i class="fas fa-calculator"></i> حاسبة التنسيق</div>
            <h2 class="section-title-bold">حاسبة معدل التوجيهي الوزارية المعتمدة</h2>
            <p class="section-lead-text">حرّك المؤشرات وشاهد المعدل التقديري بدقة وفق معايير وزارة التربية والتعليم الرسمية.</p>
        </div>

        <div class="calc-panel">
            <div class="calc-grid">
                
                <div class="calc-slider-card">
                    <div class="calc-header-row">
                        <label>الرياضيات (علمي - من 200)</label>
                        <span class="val-badge num-font" id="mathVal">192</span>
                    </div>
                    <input type="range" class="range-slider-input" id="mathRange" min="100" max="200" value="192" oninput="updateLiveCalc()">
                </div>

                <div class="calc-slider-card">
                    <div class="calc-header-row">
                        <label>الفيزياء (من 100)</label>
                        <span class="val-badge num-font" id="physVal">96</span>
                    </div>
                    <input type="range" class="range-slider-input" id="physRange" min="50" max="100" value="96" oninput="updateLiveCalc()">
                </div>

                <div class="calc-slider-card">
                    <div class="calc-header-row">
                        <label>الكيمياء / الأحياء (من 100)</label>
                        <span class="val-badge num-font" id="chemVal">95</span>
                    </div>
                    <input type="range" class="range-slider-input" id="chemRange" min="50" max="100" value="95" oninput="updateLiveCalc()">
                </div>

                <div class="calc-slider-card">
                    <div class="calc-header-row">
                        <label>اللغة العربية (من 100)</label>
                        <span class="val-badge num-font" id="arabVal">95</span>
                    </div>
                    <input type="range" class="range-slider-input" id="arabRange" min="50" max="100" value="95" oninput="updateLiveCalc()">
                </div>

            </div>

            <div class="calc-verdict-box">
                <div class="calc-verdict-info">
                    <h4>المعدل التقديري المحتسب:</h4>
                    <p>يؤهلك لدراسة تخصصات الطب البشري، الهندسة، والصيدلة في كافة الجامعات الفلسطينية الرسمية.</p>
                </div>
                <div class="calc-live-gpa num-font" id="liveGpaResult">95.6%</div>
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ route('tawjihi.calculator') }}" target="_blank" style="color: var(--brand-primary); text-decoration: none; font-weight: 700; font-size: 0.92rem; display: inline-flex; align-items: center; gap: 8px;">
                    <span>فتح دليل القبول والتنسيق الموحد لكافة الجامعات الفلسطينية</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- 7. آراء وتجارب أوائل التوجيهي (Testimonials) -->
    <section class="testimonials-section">
        <div class="section-header-center">
            <div class="section-sub-badge"><i class="fas fa-star"></i> قصص النجاح</div>
            <h2 class="section-title-bold">ماذا يقول متفوقو فلسطين عن المنصة؟</h2>
            <p class="section-lead-text">تجارب حقيقية لطلبة حققوا أعلى المراتب بفضل المتابعة اليومية عبر المنصة.</p>
        </div>

        <div class="testimonials-grid">
            
            <div class="testimonial-card">
                <div>
                    <div class="stars-row">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-quote">
                        "منارة التوجيهي كانت مرجعي الأساسي في مبحث الرياضيات والفيزياء. بنك الامتحانات المحلولة كسر حاجز الخوف من أسئلة الوزارة تماماً."
                    </p>
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">س</div>
                    <div class="author-meta">
                        <strong>سارة النجار</strong>
                        <span>معدل 98.7% • الفرع العلمي • القدس 🇵🇸</span>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div>
                    <div class="stars-row">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-quote">
                        "أعظم ميزة بالمنصة هي وضوح الشرح وتوفر بطاقات الاستذكار السريع. راجعت جميع المتطابقات والقوانين ليلة الامتحان براحة تامة."
                    </p>
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">ع</div>
                    <div class="author-meta">
                        <strong>عمر البرغوثي</strong>
                        <span>معدل 97.4% • الفرع الأدبي • رام الله 🇵🇸</span>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div>
                    <div class="stars-row">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-quote">
                        "حاسبة المعدل والتنسيق أعطتني هدفاً واضحاً طول السنة، والجدول الدراسي خلاني ألتزم بدراسة يومية منتظمة بدون مراكمة."
                    </p>
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">ن</div>
                    <div class="author-meta">
                        <strong>نور الدين المصري</strong>
                        <span>معدل 96.9% • فرع الريادة والأعمال • غزة 🇵🇸</span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 8. الأسئلة الأكثر شيوعاً (FAQ Accordion) -->
    <section class="faq-container" id="faq">
        <div class="section-header-center">
            <div class="section-sub-badge"><i class="fas fa-circle-question"></i> إجابات سريعة</div>
            <h2 class="section-title-bold">الأسئلة الأكثر شيوعاً</h2>
            <p class="section-lead-text">كل ما يهمك معرفته حول التسجيل، المواد، والامتحانات في المنصة.</p>
        </div>

        <div class="faq-accordion-list">
            
            <div class="faq-accordion-item open">
                <div class="faq-trigger" onclick="toggleFaqAccordion(this)">
                    <span>هل التسجيل في المنصة مجاني للطلبة؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-body-content">
                    نعم، يمكنك التسجيل وإنشاء حساب مجاني تماماً والوصول الفوري إلى نماذج الامتحانات التجريبية، وحاسبة المعدل، وبطاقات القوانين السريعة بدون أي رسوم.
                </div>
            </div>

            <div class="faq-accordion-item">
                <div class="faq-trigger" onclick="toggleFaqAccordion(this)">
                    <span>هل المناهج مطابقة لمواصفات وزارة التربية والتعليم لدورة 2026؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-body-content">
                    بكل تأكيد، جميع شروحاتنا ونماذج الامتحانات مبنية بالكامل على المنهاج الفلسطيني الرسمي المعتمد وتحديثات وزارة التربية والتعليم لدورة 2026.
                </div>
            </div>

            <div class="faq-accordion-item">
                <div class="faq-trigger" onclick="toggleFaqAccordion(this)">
                    <span>كيف تحسب حاسبة المعدل في المنصة درجات الفروع المختلفة؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-body-content">
                    تعتمد الحاسبة على النظام الوزاري الدقيق لأوزان المباحث (مثل احتساب مبحث الرياضيات من 200 علامة للفرع العلمي واللغة العربية للفرع الأدبي)، مع ميزة استخراج التخصصات الجامعية المتاحة لمعدلك مباشرة.
                </div>
            </div>

            <div class="faq-accordion-item">
                <div class="faq-trigger" onclick="toggleFaqAccordion(this)">
                    <span>هل تتوفر مساقات لكافة فروع الثانوية العامة؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-body-content">
                    نعم، المنصة تغطي الفرع العلمي، الفرع الأدبي، وفرع الريادة والأعمال، مع تحديث مستمر للمواد الإثرائية ونماذج السنوات السابقة.
                </div>
            </div>

        </div>
    </section>

    <!-- 9. الراية الختامية الشاملة (Final Call to Action) -->
    <div class="cta-banner-wrapper">
        <div class="cta-banner-card">
            <h2>ابدأ رحلة تفوقك في التوجيهي اليوم</h2>
            <p>لا تنتظر حتى اللحظات الأخيرة، انضم لآلاف الطلبة المتفوقين في فلسطين واستعد لامتحاناتك الوزارية بكل هدوء وثقة واضمن مقعدك في الكلية التي تحلم بها.</p>
            <a href="{{ route('students.create') }}" class="btn-cta-white">
                <span>سجل حسابك مجاناً الآن</span>
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <!-- 10. التذييل الرسمي الفخم (World-Class Footer) -->
    <footer class="global-footer">
        <div class="footer-content-grid">
            
            <div class="footer-brand-bio">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="brand-emblem" style="width: 36px; height: 36px; font-size: 1.1rem;"><i class="fas fa-graduation-cap"></i></div>
                    <strong style="font-size: 1.15rem; font-weight: 800;">{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</strong>
                </div>
                <p>
                    المنظومة الأكاديمية الفلسطينية الرائدة، صُممت لمساندة طلبة الثانوية العامة وتوفير بيئة تعليمية علمية شاملة لتحقيق أعلى مراتب التميز.
                </p>
                <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.8rem; color: #cbd5e1; background: rgba(255,255,255,0.06); padding: 6px 14px; border-radius: var(--radius-pill);">
                    <span>🇵🇸 صُنع بإتقان لدعم مسيرة التعليم في فلسطين</span>
                </div>
            </div>

            <div class="footer-col-nav">
                <h5>الفروع والمناهج</h5>
                <ul>
                    <li><a href="#tracks">الفرع العلمي</a></li>
                    <li><a href="#tracks">الفرع الأدبي</a></li>
                    <li><a href="#tracks">فرع الريادة والأعمال</a></li>
                    <li><a href="{{ route('tawjihi.calculator') }}">حاسبة التنسيق الجامعي</a></li>
                </ul>
            </div>

            <div class="footer-col-nav">
                <h5>أدوات المنظومة</h5>
                <ul>
                    <li><a href="#interactive-engine">قاعة الامتحانات المحلولة</a></li>
                    <li><a href="#interactive-engine">بطاقات الاستذكار السريع</a></li>
                    <li><a href="{{ route('login') }}">مولّد جداول المراجعة</a></li>
                    <li><a href="{{ route('login') }}">لوحة الشرف وتحدي الأوائل</a></li>
                </ul>
            </div>

            <div class="footer-col-nav">
                <h5>المساعدة والتواصل</h5>
                <ul>
                    <li><a href="https://wa.me/{{ \App\Models\Setting::get('contact_whatsapp', '970567897212') }}" target="_blank"><i class="fab fa-whatsapp" style="color: #22c55e;"></i> تواصل عبر واتساب</a></li>
                    <li><a href="mailto:{{ \App\Models\Setting::get('contact_email', 'support@tawjihi.ps') }}">الدعم الفني والشكاوى</a></li>
                    <li><a href="#faq">مركز الأسئلة الشائعة</a></li>
                    <li><a href="{{ route('login') }}">بوابة المعلمين والإدارة</a></li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom-bar">
            <span>جميع الحقوق محفوظة © {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</span>
            <span>نظام تعليمي متكامل لطلبة فلسطين 🇵🇸</span>
        </div>
    </footer>

    <!-- سكربتات التفاعل للمحاكي والحاسبة الذكية -->
    <script>
        function toggleFaqAccordion(element) {
            const item = element.parentElement;
            item.classList.toggle('open');
        }

        function switchCanvasTab(tabName, btnElement) {
            document.querySelectorAll('.canvas-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.canvas-content-pane').forEach(pane => pane.style.display = 'none');

            btnElement.classList.add('active');
            const target = document.getElementById('tab-' + tabName);
            if (target) {
                target.style.display = 'block';
                target.style.animation = 'fadeIn 0.3s ease';
            }
        }

        function checkExamAnswer(isCorrect, btn) {
            const feedback = document.getElementById('examFeedback');
            document.querySelectorAll('.sim-opt-btn').forEach(b => {
                b.classList.remove('correct-active');
                const icon = b.querySelector('i');
                if (icon) icon.className = 'far fa-circle';
            });

            if (isCorrect) {
                btn.classList.add('correct-active');
                const icon = btn.querySelector('i');
                if (icon) icon.className = 'fas fa-circle-check';
                if (feedback) feedback.style.display = 'flex';
            } else {
                alert('إجابة غير دقيقة! حاول مرة أخرى بالاشتقاق والمساواة بالصفر ق\'(س) = 0.');
                if (feedback) feedback.style.display = 'none';
            }
        }

        function updateQuickGpa(val) {
            const num = parseFloat(val).toFixed(1);
            document.getElementById('quickGpaVal').textContent = num + '%';

            const resultBox = document.getElementById('quickMajorResult');
            if (num >= 95) {
                resultBox.innerHTML = '🎓 يؤهلك مباشرة للتسجيل في: <strong>الطب البشري، طب وجراحة الأسنان، الصيدلة السريرية، الهندسة المعمارية وهندسة الحاسوب</strong>.';
            } else if (num >= 85) {
                resultBox.innerHTML = '🎓 يؤهلك للتسجيل في: <strong>الهندسة المدنية والكهربائية، التمريض والعلوم الطبية، تكنولوجيا المعلومات والذكاء الاصطناعي</strong>.';
            } else if (num >= 75) {
                resultBox.innerHTML = '🎓 يؤهلك للتسجيل في: <strong>القانون والعلوم السياسية، إدارة الأعمال والمحاسبة، الآداب، واللغات والترجمة</strong>.';
            } else {
                resultBox.innerHTML = '🎓 يؤهلك للتسجيل في: <strong>كافة برامج الدبلوم المهني المتوسط، العلوم الإدارية والتطبيقية، والفنون</strong>.';
            }
        }

        function updateLiveCalc() {
            const math = parseFloat(document.getElementById('mathRange').value) || 192;
            const phys = parseFloat(document.getElementById('physRange').value) || 96;
            const chem = parseFloat(document.getElementById('chemRange').value) || 95;
            const arab = parseFloat(document.getElementById('arabRange').value) || 95;

            document.getElementById('mathVal').textContent = math;
            document.getElementById('physVal').textContent = phys;
            document.getElementById('chemVal').textContent = chem;
            document.getElementById('arabVal').textContent = arab;

            const totalSum = math + phys + chem + arab;
            const maxScore = 200 + 100 + 100 + 100; // 500
            const gpa = ((totalSum / maxScore) * 100).toFixed(1);

            document.getElementById('liveGpaResult').textContent = gpa + '%';
        }

        document.addEventListener('DOMContentLoaded', updateLiveCalc);
    </script>
</body>
</html>
