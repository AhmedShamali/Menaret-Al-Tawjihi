<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::get('site_name', 'جسر') }} | المنصة التعليمية الرقمية</title>

    <!-- Google Fonts: Alexandria & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --border-card: #e2e8f0;
            --border-hover: #3b82f6;

            --primary: #2563eb;
            --primary-light: #eff6ff;
            --cyan-accent: #0284c7;
            --emerald-accent: #10b981;

            --text-title: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;

            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
            --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 30px -10px rgba(37, 99, 235, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Alexandria', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-body);
            overflow-x: hidden;
            line-height: 1.7;
        }

        /* Ambient Glow Background - Light Version */
        .ambient-glow {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }

        .glow-1 {
            position: absolute;
            top: -15%; right: -10%;
            width: 650px; height: 650px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, rgba(255,255,255,0) 70%);
            filter: blur(80px);
        }

        .glow-2 {
            position: absolute;
            bottom: 10%; left: -10%;
            width: 550px; height: 550px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.08) 0%, rgba(255,255,255,0) 70%);
            filter: blur(80px);
        }

        /* Header / Navigation */
        nav {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            padding: 18px 8%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-card);
            display: flex; justify-content: space-between; align-items: center;
        }

        .logo {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none; color: var(--text-title);
            font-weight: 800; font-size: 1.35rem;
            letter-spacing: -0.5px;
        }

        .logo-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--cyan-accent));
            border-radius: 12px;
            display: grid; place-items: center; color: white;
            font-size: 1.2rem; font-weight: 900;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
        }

        .nav-actions { display: flex; gap: 16px; align-items: center; }

        .btn-link {
            text-decoration: none; color: var(--text-title);
            font-weight: 600; font-size: 0.92rem;
            padding: 10px 18px; border-radius: 10px;
            transition: 0.3s ease;
        }
        .btn-link:hover { color: var(--primary); background: var(--primary-light); }

        .btn-primary-sm {
            padding: 10px 22px;
            background: linear-gradient(135deg, var(--primary), #1d4ed8);
            color: white !important;
            border-radius: 10px; font-weight: 700; font-size: 0.9rem;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);
            transition: 0.3s ease;
        }
        .btn-primary-sm:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            padding: 160px 8% 80px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            align-items: center;
            gap: 60px;
        }

        .badge-pill {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 6px 16px;
            background: var(--primary-light);
            border: 1px solid rgba(37, 99, 235, 0.2);
            border-radius: 30px; color: var(--primary);
            font-weight: 600; font-size: 0.85rem;
            margin-bottom: 28px;
        }
        .badge-dot {
            width: 8px; height: 8px;
            background: var(--primary);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--primary);
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.25;
            color: var(--text-title);
            margin-bottom: 24px;
            letter-spacing: -1px;
        }

        .hero h1 .gradient-text {
            background: linear-gradient(135deg, var(--primary), var(--cyan-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.125rem;
            color: var(--text-muted);
            max-width: 580px;
            margin-bottom: 38px;
            font-weight: 400;
        }

        .hero-cta { display: flex; gap: 16px; align-items: center; }

        .btn-main {
            padding: 16px 36px;
            background: linear-gradient(135deg, var(--primary), #1d4ed8);
            color: white; border-radius: 14px;
            text-decoration: none; font-weight: 700; font-size: 1rem;
            box-shadow: var(--shadow-lg);
            transition: 0.3s ease;
            display: inline-flex; align-items: center; gap: 10px;
        }
        .btn-main:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(37, 99, 235, 0.3);
        }

        .btn-glass {
            padding: 16px 28px;
            background: white;
            border: 1px solid var(--border-card);
            color: var(--text-title);
            border-radius: 14px; text-decoration: none;
            font-weight: 600; font-size: 1rem;
            box-shadow: var(--shadow-sm);
            transition: 0.3s ease;
            display: inline-flex; align-items: center; gap: 10px;
        }
        .btn-glass:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }

        .btn-primary-elegant {
            text-decoration: none;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); /* تدرج لوني فخم */
            color: white;
            padding: 10px 25px;
            border-radius: 50px; /* زوايا دائرية بالكامل */
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            transition: all 0.3s ease;
        }

.btn-primary-elegant:hover {
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
    filter: brightness(1.1);
    transform: scale(1.05);
}
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px; margin-top: 55px; padding-top: 35px;
            border-top: 1px solid var(--border-card);
        }

        .stat-item h3 {
            font-size: 1.9rem; font-weight: 800;
            color: var(--text-title);
            font-family: 'Plus Jakarta Sans', 'Alexandria', sans-serif;
            margin-bottom: 4px;
        }
        .stat-item p { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; }

        /* Hero Showcase Visual */
        .showcase-wrapper {
            position: relative;
            display: flex; justify-content: center;
        }

        .showcase-card {
            width: 100%;
            background: white;
            border: 1px solid var(--border-card);
            border-radius: 28px;
            padding: 12px;
            box-shadow: var(--shadow-md);
        }

        .showcase-card img {
            width: 100%; height: 380px;
            border-radius: 20px; display: block;
            object-fit: cover;
        }

        .floating-pill {
            position: absolute;
            bottom: -20px; right: -20px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid var(--border-card);
            padding: 14px 22px; border-radius: 18px;
            backdrop-filter: blur(16px);
            box-shadow: var(--shadow-md);
            display: flex; align-items: center; gap: 14px;
            animation: floatAnim 4s infinite ease-in-out;
        }

        @keyframes floatAnim {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .pill-icon {
            width: 42px; height: 42px;
            background: #d1fae5;
            color: #059669;
            border-radius: 12px;
            display: grid; place-items: center;
            font-size: 1.1rem;
        }

        /* Features Section */
        .features {
            padding: 100px 8%;
            background: white;
            border-top: 1px solid var(--border-card);
            border-bottom: 1px solid var(--border-card);
            position: relative;
        }

        .section-header {
            text-align: center;
            max-width: 600px; margin: 0 auto 60px;
        }

        .section-header h2 {
            font-size: 2.2rem; font-weight: 800;
            color: var(--text-title); margin-bottom: 12px;
        }

        .section-header p {
            color: var(--text-muted); font-size: 1rem;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .card {
            background: var(--bg-main);
            border: 1px solid var(--border-card);
            border-radius: 20px;
            padding: 36px 30px;
            transition: 0.3s ease;
        }

        .card:hover {
            background: white;
            border-color: var(--border-hover);
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
        }

        .card-icon-box {
            width: 52px; height: 52px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 14px;
            display: grid; place-items: center;
            font-size: 1.35rem; margin-bottom: 24px;
        }

        .card h3 {
            font-size: 1.2rem; font-weight: 700;
            color: var(--text-title); margin-bottom: 10px;
        }

        .card p {
            color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;
        }

        /* Footer */
        footer {
            background: #ffffff;
            padding: 80px 8% 40px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.3fr;
            gap: 40px; margin-bottom: 60px;
        }

        .footer-about p {
            color: var(--text-muted); font-size: 0.92rem;
            margin-top: 16px; max-width: 320px;
        }

        .footer-title {
            color: var(--text-title); font-weight: 700;
            font-size: 1rem; margin-bottom: 20px;
        }

        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links a {
            color: var(--text-muted); text-decoration: none;
            font-size: 0.9rem; transition: 0.2s;
        }
        .footer-links a:hover { color: var(--primary); }

        .whatsapp-card {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 20px; border-radius: 16px;
        }

        .btn-wa {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: #10b981; color: white;
            padding: 10px; border-radius: 10px;
            text-decoration: none; font-weight: 700; font-size: 0.88rem;
            margin-top: 12px; transition: 0.3s;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        .btn-wa:hover { opacity: 0.9; transform: translateY(-2px); }

        .footer-bottom {
            display: flex; justify-content: space-between; align-items: center;
            padding-top: 30px; border-top: 1px solid var(--border-card);
            color: var(--text-muted); font-size: 0.85rem;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .hero { grid-template-columns: 1fr; text-align: center; padding-top: 140px; }
            .hero p { margin: 0 auto 30px; }
            .hero-cta { justify-content: center; }
            .stats-grid { max-width: 500px; margin: 40px auto 0; }
            .showcase-wrapper { order: -1; max-width: 500px; margin: 0 auto; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 30px; }
        }

        @media (max-width: 640px) {
            nav { padding: 16px 5%; }
            .hero h1 { font-size: 2.3rem; }
            .hero-cta { flex-direction: column; width: 100%; }
            .btn-main, .btn-glass { width: 100%; justify-content: center; }
            .stats-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .footer-bottom { flex-direction: column; gap: 12px; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Dynamic Light Background -->
    <div class="ambient-glow">
        <div class="glow-1"></div>
        <div class="glow-2"></div>
    </div>

    <!-- Top Navigation -->
    <nav>
        <a href="/" class="logo">
            <div class="logo-icon">{{ mb_substr(\App\Models\Setting::get('site_name', 'جسر'), 0, 1) }}</div>
            <span>{{ \App\Models\Setting::get('site_name', 'جسر') }}</span>
        </a>
        <div class="nav-actions">
            <a href="{{ route('login') }}" class="btn-primary-elegant">
                تسجيل الدخول
            </a>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-text">
                <div class="badge-pill">
                    <span class="badge-dot"></span>
                    <span>المنصة الأكاديمية الذكية في فلسطين</span>
                </div>

                <h1>طريقتك الأحدث نحو <br><span class="gradient-text">التميز الأكاديمي</span></h1>

                <p>{{ \App\Models\Setting::get('site_name', 'جسر') }} تقدم تجربة تعليمية سلسة ومصممة بعناية لمساعدة الطلاب على التفوق عبر أفضل الشروحات والأدوات التفاعلية.</p>

                <div class="hero-cta">
                    <a href="{{ route('students.create') }}" class="btn-main">
                        <span>انضم إلينا اليوم</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>

                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <h3><span class="counter" data-target="{{ $stats['students'] ?? 1500 }}">0</span>+</h3>
                        <p>طالب مسجّل</p>
                    </div>
                    <div class="stat-item">
                        <h3><span class="counter" data-target="{{ $stats['lessons'] ?? 450 }}">0</span>+</h3>
                        <p>درس تفاعلي</p>
                    </div>
                    <div class="stat-item">
                        <h3>99%</h3>
                        <p>نسبة الرضا</p>
                    </div>
                </div>
            </div>

            <!-- Showcase Card Visual - School Classroom Image -->
            <div class="showcase-wrapper">
                <div class="showcase-card">
                    <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="طلاب في الغرفة الصفية">
                </div>
                <div class="floating-pill">
                    <div class="pill-icon">
                        <i class="fas fa-shield-check"></i>
                    </div>
                    <div>
                        <div style="color: var(--text-title); font-weight: 700; font-size: 0.9rem;">منهاج معتمد</div>
                        <div style="color: var(--text-muted); font-size: 0.78rem;">مُطابق للمواصفات الوزارية</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <div class="section-header">
                <h2>مميزات المنصة التعليمية</h2>
                <p>صممنا أدواتنا لضمان أقصى درجات الاستيعاب والسهولة للطالب</p>
            </div>

            <div class="cards-grid">
                <div class="card">
                    <div class="card-icon-box"><i class="fas fa-play-circle"></i></div>
                    <h3>شروحات فيديو حديثة</h3>
                    <p>دروس مصورة بجودة عالية تغطي المفاهيم الأكاديمية خطوة بخطوة بطريقة ميسرة.</p>
                </div>

                <div class="card">
                    <div class="card-icon-box"><i class="fas fa-tasks"></i></div>
                    <h3>اختبارات تقييمية</h3>
                    <p>امتحانات تفاعلية قصيرة لتقييم مدى فهمك للدروس فور انتهائها.</p>
                </div>

                <div class="card">
                    <div class="card-icon-box"><i class="fas fa-headset"></i></div>
                    <h3>متابعة مستمرة</h3>
                    <p>دعم أكاديمي متواصل لمساعدتك في الإجابة عن أي استفسار أو صعوبة مع المواد.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-grid">
            <div class="footer-about">
                <div class="logo">
                    <div class="logo-icon">{{ mb_substr(\App\Models\Setting::get('site_name', 'جسر'), 0, 1) }}</div>
                    <span>{{ \App\Models\Setting::get('site_name', 'جسر') }}</span>
                </div>
                <p>منصة رقمية تم تطويرها خصيصاً لتيسير وتسريع عملية التعلم لطلاب فلسطين بكفاءة عالية.</p>
            </div>

            <div>
                <div class="footer-title">الروابط السريعة</div>
                <ul class="footer-links">
                    <li><a href="{{ route('placement.index') }}">اختبار المستوى</a></li>
                    <li><a href="{{ route('students.create') }}">حساب جديد</a></li>
                    <li><a href="{{ route('login') }}">تسجيل الدخول</a></li>
                </ul>
            </div>

            <div>
                <div class="footer-title">المساعدة</div>
                <ul class="footer-links">
                    <li><a href="{{ route('public.faq') }}">الأسئلة الشائعة</a></li>
                    <li><a href="{{ route('public.contact') }}">تواصل معنا</a></li>
                    <li><a href="{{ route('public.terms') }}">الشروط والأحكام</a></li>
                </ul>
            </div>

            <div class="whatsapp-card">
                <div style="color: #065f46; font-weight: 700; font-size: 0.95rem;">هل تحتاج إلى مساعدة؟</div>
                <div style="color: #047857; font-size: 0.82rem; margin-top: 4px;">فريق الدعم متاح عبر الواتساب.</div>
                <!-- تم ربط الرقم هنا -->
                <a href="https://wa.me/970597694385" class="btn-wa" target="_blank">
                    <i class="fab fa-whatsapp fa-lg"></i>
                    <span>تحدث معنا</span>
                </a>
            </div>
        </div>

        <div class="footer-bottom">
            <div>© 2026 جميع الحقوق محفوظة لطلبة فلسطين. إعداد: أحمد شمالي</div>
            <div style="display: flex; gap: 16px;">
                <a href="{{ route('public.privacy') }}" style="color: inherit; text-decoration: none;">الخصوصية</a>
            </div>
        </div>
    </footer>

    <script>
        // Number Counter Animation
        const counters = document.querySelectorAll('.counter');
        const speed = 150;

        const animateCounters = () => {
            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText;
                    const inc = Math.ceil(target / speed);

                    if (count < target) {
                        counter.innerText = count + inc;
                        setTimeout(updateCount, 20);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            });
        };

        let observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        const statsSection = document.querySelector('.stats-grid');
        if(statsSection) observer.observe(statsSection);
    </script>
</body>
</html>
