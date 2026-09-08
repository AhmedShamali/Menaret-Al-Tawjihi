<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>بوابة الزوار | {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸</title>

    <!-- الخطوط والأيقونات -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-body: #f8fafc;
            --primary: #0284c7;
            --primary-dark: #0369a1;
            --primary-light: #e0f2fe;
            --indigo: #6366f1;
            --emerald: #10b981;
            --amber: #f59e0b;
            --rose: #f43f5e;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Alexandria', sans-serif;
        }

        body {
            background: var(--bg-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--text-main);
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Glow Background */
        .ambient-glow {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .glow-1 {
            position: absolute;
            top: -10%; right: -5%;
            width: 550px; height: 550px;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.1) 0%, transparent 70%);
            filter: blur(80px);
        }
        .glow-2 {
            position: absolute;
            bottom: 5%; left: -5%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
            filter: blur(70px);
        }

        /* --- الهيدر العلوي --- */
        header.visitor-header {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-logo {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-square {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #0284c7, #6366f1);
            color: #fff;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-header-link {
            text-decoration: none;
            color: var(--text-main);
            font-size: 0.88rem;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 10px;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-header-link:hover {
            color: var(--primary);
            background: var(--primary-light);
        }

        .btn-register-header {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: #fff !important;
            padding: 9px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.88rem;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-register-header:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.35);
        }

        /* --- المحتوى الرئيسي --- */
        main.visitor-main {
            position: relative;
            z-index: 10;
            flex: 1;
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
            padding: 60px 20px 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .hero-badge {
            background: #e0f2fe;
            color: #0369a1;
            padding: 7px 18px;
            border-radius: 30px;
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 24px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #bae6fd;
        }

        h1.hero-title {
            font-size: 2.8rem;
            font-weight: 900;
            color: var(--text-main);
            margin-bottom: 18px;
            line-height: 1.35;
            letter-spacing: -0.5px;
        }

        h1.hero-title span {
            background: linear-gradient(135deg, #0284c7, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.hero-desc {
            color: var(--text-muted);
            font-size: 1.05rem;
            max-width: 720px;
            margin-bottom: 35px;
            line-height: 1.8;
        }

        /* Action Buttons Grid */
        .cta-buttons-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-bottom: 65px;
        }

        .btn-cta-primary {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: white;
            padding: 15px 32px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 8px 24px rgba(2, 132, 199, 0.3);
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-cta-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(2, 132, 199, 0.4);
            color: white;
        }

        .btn-cta-secondary {
            background: #ffffff;
            color: var(--text-main);
            padding: 15px 26px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-cta-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .btn-cta-green {
            background: #ecfdf5;
            color: #065f46;
            padding: 15px 26px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            border: 1px solid #a7f3d0;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-cta-green:hover {
            background: #10b981;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
        }

        /* --- بطاقات المزايا --- */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            width: 100%;
            text-align: right;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            padding: 28px 24px;
            border-radius: 18px;
            box-shadow: var(--card-shadow);
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .feature-card:hover {
            border-color: #93c5fd;
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(2, 132, 199, 0.08);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.3rem;
            margin-bottom: 18px;
        }
        .icon-blue { background: #e0f2fe; color: #0284c7; }
        .icon-purple { background: #ede9fe; color: #7c3aed; }
        .icon-emerald { background: #d1fae5; color: #059669; }
        .icon-amber { background: #fef3c7; color: #d97706; }

        .feature-card h3 {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .feature-card p {
            font-size: 0.86rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* --- الفوتر --- */
        footer.visitor-footer {
            position: relative;
            z-index: 10;
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 25px 20px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            header.visitor-header { padding: 15px 20px; }
            h1.hero-title { font-size: 2.1rem; }
            .cta-buttons-container { flex-direction: column; width: 100%; }
            .btn-cta-primary, .btn-cta-secondary, .btn-cta-green { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

    <!-- خلفية مضيئة -->
    <div class="ambient-glow">
        <div class="glow-1"></div>
        <div class="glow-2"></div>
    </div>

    <!-- الهيدر -->
    <header class="visitor-header">
        <a href="/" class="brand-logo">
            <div class="logo-square">{{ mb_substr(\App\Models\Setting::get('site_name', 'منارة التوجيهي'), 0, 1) }}</div>
            <span>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</span>
        </a>

        <div class="header-actions">
            <a href="{{ route('tawjihi.calculator') }}" class="btn-header-link">
                <i class="fa-solid fa-calculator" style="color: #0284c7;"></i> حاسبة المعدل
            </a>
            <a href="{{ route('tawjihi.archive') }}" class="btn-header-link">
                <i class="fa-solid fa-file-lines" style="color: #10b981;"></i> بنك الامتحانات
            </a>
            <a href="{{ route('tawjihi.formulas') }}" class="btn-header-link">
                <i class="fa-solid fa-square-root-variable" style="color: #8b5cf6;"></i> دليل القوانين
            </a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-register-header">
                        <i class="fa-solid fa-gauge-high"></i> لوحة التحكم
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-header-link">
                        <i class="fa-solid fa-right-to-bracket"></i> دخول
                    </a>
                    <a href="{{ route('students.create') }}" class="btn-register-header">
                        <i class="fa-solid fa-user-plus"></i> تسجيل طالب
                    </a>
                @endauth
            @endif
        </div>
    </header>

    <!-- المحتوى الرئيسي -->
    <main class="visitor-main">
        <div class="hero-badge">
            <i class="fa-solid fa-graduation-cap"></i> المنصة التفاعلية الأولى لطلبة التوجيهي في فلسطين 🇵🇸
        </div>

        <h1 class="hero-title">
            طريقك نحو التفوق والتميز في <br><span>امتحانات الثانوية العامة (التوجيهي)</span>
        </h1>

        <p class="hero-desc">
            بيئة تعليمية ذكية متكاملة مصممة خصيصاً للمنهاج الفلسطيني المعتمد (العلمي، الأدبي، الريادة والأعمال)، تدعمك ببنك امتحانات الإنجاز الوزارية، بطاقات الاستذكار السريع، وحاسبة التنسيق الجامعي الدقيقة.
        </p>

        <!-- أزرار الإجراءات الرئيسية -->
        <div class="cta-buttons-container">
            <a href="{{ route('students.create') }}" class="btn-cta-primary">
                <span>إنشاء حساب طالب جديد</span>
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <a href="{{ route('tawjihi.calculator') }}" class="btn-cta-secondary">
                <i class="fa-solid fa-calculator" style="color: #0284c7;"></i>
                <span>حاسبة معدل التوجيهي والقبول الجامعي</span>
            </a>
            <a href="{{ route('tawjihi.archive') }}" class="btn-cta-green">
                <i class="fa-solid fa-book-bookmark"></i>
                <span>أرشيف الامتحانات الوزارية النموذجية</span>
            </a>
            <a href="{{ route('tawjihi.formulas') }}" class="btn-cta-secondary" style="border-color: #c7d2fe; background: #f5f3ff; color: #5b21b6;">
                <i class="fa-solid fa-square-root-variable" style="color: #7c3aed;"></i>
                <span>دليل القوانين والقواعد الذهبية</span>
            </a>
        </div>

        <!-- بطاقات المزايا -->
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon icon-blue"><i class="fa-solid fa-book-open-reader"></i></div>
                <h3>المنهاج الفلسطيني الرسمي</h3>
                <p>شروحات مفصلة، ملفات PDF، وملازم تلخيص تغطي فروع العلمي والأدبي والريادة بدقة واحترافية.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon icon-purple"><i class="fa-solid fa-play"></i></div>
                <h3>شروحات فيديو تفاعلية وملازم</h3>
                <p>دروس فيديو متخصصة مع ملخصات شاملة ومتابعة مباشرة مع نخبة من أفضل معلمي فلسطين.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon icon-emerald"><i class="fa-solid fa-file-shield"></i></div>
                <h3>بنك امتحانات الإنجاز الوزارية</h3>
                <p>نماذج الامتحانات الوزارية من 2020 إلى 2024 مع نماذج الإجابة الرسمية المعتمدة للتحميل المباشر.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon icon-amber"><i class="fa-solid fa-scale-balanced"></i></div>
                <h3>حاسبة القبول الجامعي الموحد</h3>
                <p>احسب معدلك وفق قواعد وزارة التربية والتعليم وتعرف على التخصصات المتاحة لك في جامعات الوطن.</p>
            </div>
        </div>
    </main>

    <!-- الفوتر -->
    <footer class="visitor-footer">
        <p>جميع الحقوق محفوظة © {{ date('Y') }} — {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} 🇵🇸</p>
    </footer>

</body>
</html>
