<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- العنوان ديناميكي حسب إعدادات المنصة -->
    <title>بوابة الزوار | {{ \App\Models\Setting::get('site_name', 'منصة جسر') }}</title>

    <!-- الخطوط والأيقونات -->
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-body: #f8fafc;
            --primary-color: #0284c7;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Alexandria', sans-serif; }

        body {
            background: var(--bg-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--text-main);
        }

        /* --- الهيدر العلوي --- */
        header.visitor-header {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        }

        .brand-logo {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-square {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: #fff;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
        }

        .btn-login {
            background: #e0f2fe;
            color: var(--primary-color);
            padding: 10px 22px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #bae6fd;
        }

        .btn-login:hover {
            background: var(--primary-color);
            color: #fff;
        }

        /* --- المحتوى الرئيسي --- */
        main.visitor-main {
            flex: 1;
            max-width: 1000px;
            width: 100%;
            margin: 0 auto;
            padding: 50px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .hero-badge {
            background: #e0f2fe;
            color: var(--primary-color);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        h1.hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 15px;
            line-height: 1.3;
        }

        h1.hero-title span {
            color: var(--primary-color);
        }

        p.hero-desc {
            color: var(--text-muted);
            font-size: 1rem;
            max-width: 600px;
            margin-bottom: 40px;
            line-height: 1.7;
        }

        .hero-action {
            margin-bottom: 60px;
        }

        .btn-main-action {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: white;
            padding: 14px 35px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.25);
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-main-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(2, 132, 199, 0.35);
        }

        /* --- بطاقات المزايا --- */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            width: 100%;
            text-align: right;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.01);
            transition: 0.3s;
        }

        .feature-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        }

        .feature-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: #f0f9ff;
            color: var(--primary-color);
            display: grid;
            place-items: center;
            font-size: 1.2rem;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .feature-card p {
            font-size: 0.83rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* --- الفوتر --- */
        footer.visitor-footer {
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 20px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.83rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            header.visitor-header { padding: 15px 20px; }
            h1.hero-title { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <!-- الهيدر -->
    <header class="visitor-header">
        <a href="/" class="brand-logo">
            <div class="logo-square">{{ mb_substr(\App\Models\Setting::get('site_name', 'منصة جسر'), 0, 1) }}</div>
            <span>{{ \App\Models\Setting::get('site_name', 'منصة جسر') }}</span>
        </a>

        @if (Route::has('login'))
            <div>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-login">
                        <i class="fa-solid fa-gauge-high"></i> لوحة التحكم
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-login">
                        <i class="fa-solid fa-right-to-bracket"></i> تسجيل الدخول
                    </a>
                @endauth
            </div>
        @endif
    </header>

    <!-- المحتوى الرئيسي -->
    <main class="visitor-main">
        <div class="hero-badge">
            <i class="fa-solid fa-sparkles"></i> البوابة التعليمية الرقمية المتكاملة
        </div>

        <h1 class="hero-title">
            أهلاً بك في <span>{{ \App\Models\Setting::get('site_name', 'منصة جسر') }}</span>
        </h1>

        <p class="hero-desc">
            بيئة تعليمية تفاعلية متطورة تتيح للمدرسين إدارة المحتوى والاختبارات، وتمكن الطلاب من متابعة مسارهم الأكاديمي بكل سهولة واحترافية.
        </p>

        <div class="hero-action">
            <a href="{{ route('login') }}" class="btn-main-action">
                <span>ابدأ الآن وتسجيل الدخول</span>
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>

        <!-- بطاقات المزايا -->
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-book-open-reader"></i></div>
                <h3>المحتوى الأكاديمي</h3>
                <p>استعراض الدروس، الملفات التعليمية، والمراجع المتاحة في المسارات بكل يسر.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-file-pen"></i></div>
                <h3>الاختبارات الذكية</h3>
                <p>تقديم الاختبارات التفاعلية ورصد النتائج والدرجات بشكل فوري ودقيق.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-chart-line"></i></div>
                <h3>متابعة التقدم</h3>
                <p>تقارير وإحصائيات مفصلة لمتابعة الأداء التعليمي ومستوى الإنجاز.</p>
            </div>
        </div>
    </main>

    <!-- الفوتر -->
    <footer class="visitor-footer">
        <p>جميع الحقوق محفوظة © {{ date('Y') }} — {{ \App\Models\Setting::get('site_name', 'منصة جسر') }}</p>
    </footer>

</body>
</html>
