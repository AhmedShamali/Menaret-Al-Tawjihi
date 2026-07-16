<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصّة جسر | بوابتك نحو التفوق</title>

    <!-- الخطوط -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- مكتبة الأيقونات FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- مكتبة الأنميشن Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --primary: #1e40af;
            --secondary: #3b82f6;
            --accent: #10b981;
            --dark: #0f172a;
            --light: #f8fafc;
            --glass-bg: rgba(255, 255, 255, 0.8);
            --card-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: #f3f7ff;
            color: var(--dark);
            overflow-x: hidden;
        }

        /* تحسين الخلفية المتحركة */
        .background-blobs {
            position: fixed;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            overflow: hidden;
            filter: blur(100px);
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            opacity: 0.4;
            animation: float 20s infinite alternate;
        }

        .blob-1 { width: 600px; height: 600px; background: #bfdbfe; top: -10%; left: -10%; }
        .blob-2 { width: 500px; height: 500px; background: #d1fae5; bottom: -10%; right: -10%; animation-delay: -5s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 100px) scale(1.2); }
        }

        /* Navbar مطور */
        nav {
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 15px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            transition: 0.3s;
        }

        nav.scrolled {
            padding: 10px 8%;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }

        .logo {
            font-size: 2rem;
            font-weight: 900;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Hero Section */
        .hero {
            padding: 120px 8% 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 40px;
        }

        .hero-content h1 {
            font-size: 4rem;
            line-height: 1.2;
            font-weight: 900;
            margin-bottom: 25px;
            color: var(--dark);
        }

        .hero-content h1 mark {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content p {
            font-size: 1.4rem;
            color: #4b5563;
            margin-bottom: 35px;
            line-height: 1.8;
        }

        .hero-image {
            position: relative;
            display: flex;
            justify-content: center;
        }

        .hero-image img {
            width: 80%;
            border-radius: 40px;
            box-shadow: var(--card-shadow);
            animation: upDown 4s ease-in-out infinite;
        }

        @keyframes upDown {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* الإحصائيات بشكل جديد */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 40px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.02);
        }

        .stat-card i {
            font-size: 1.5rem;
            color: var(--secondary);
            margin-bottom: 10px;
        }

        .stat-card span {
            display: block;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
        }

        /* قسم المراحل الدراسية */
        .section-title {
            text-align: center;
            margin: 100px 0 50px;
        }

        .section-title h2 {
            font-size: 2.8rem;
            font-weight: 800;
        }

        .stages-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 0 8% 100px;
        }

        .stage-card {
            background: white;
            padding: 40px;
            border-radius: 35px;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .stage-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; width: 100%; height: 6px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transition: 0.4s;
        }

        .stage-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 30px 60px rgba(30, 58, 138, 0.1);
        }

        .stage-card:hover::after { transform: scaleX(1); }

        .stage-card .icon-box {
            width: 80px;
            height: 80px;
            background: #f0f7ff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 20px;
            transition: 0.4s;
        }

        .stage-card:hover .icon-box {
            background: var(--primary);
            color: white;
            transform: rotate(10deg);
        }

        .stage-card h3 {
            font-size: 1.6rem;
            color: var(--dark);
            margin-bottom: 10px;
        }

        /* الأزرار */
        .btn-main {
            padding: 15px 40px;
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }

        .btn-main:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(59, 130, 246, 0.4);
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 80px 8% 40px;
            text-align: center;
            border-radius: 60px 60px 0 0;
        }

        .footer-logo { font-size: 2.5rem; font-weight: 900; margin-bottom: 20px; }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 30px 0;
        }

        .social-links a {
            color: white;
            font-size: 1.5rem;
            width: 50px;
            height: 50px;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }

        .social-links a:hover {
            background: var(--secondary);
            border-color: var(--secondary);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .hero { grid-template-columns: 1fr; text-align: center; padding-top: 60px; }
            .hero-content h1 { font-size: 2.8rem; }
            .hero-image { order: -1; }
            .stats-grid { justify-content: center; }
        }
    </style>
</head>
<body>

    <div class="background-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <nav id="navbar">
        <a href="#" class="logo">
            <i class="fas fa-bridge"></i> جسر
        </a>
        <div class="nav-btns">
            <a href="login.html" style="text-decoration: none; color: var(--dark); margin-left: 20px; font-weight: 700;">دخول</a>
            <a href="register.html" class="btn-main">ابدأ الآن مجاناً</a>
        </div>
    </nav>

    <main>
        <section class="hero">
            <div class="hero-content animate__animated animate__fadeInRight">
                <span style="background: #dbeafe; color: #1e40af; padding: 5px 15px; border-radius: 50px; font-weight: 700; font-size: 0.9rem;">
                    🚀 ثورة في التعليم الإلكتروني بفلسطين
                </span><br>
                <br><h1>نصنعُ لك <br><mark>جسر التميز</mark></h1>
                <p>
                    أول منصة تعليمية تفاعلية تدعم الطالب الفلسطيني بمحتوى ذكي، شروحات مبسطة، واختبارات تقيس مستوى تقدمك لحظة بلحظة.
                </p>
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-user-graduate"></i>
                        <span class="counter" data-target="15000">0</span>
                        <small>طالب وطالبة</small>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-video"></i>
                        <span class="counter" data-target="1200">0</span>
                        <small>درس مصور</small>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-star"></i>
                        <span class="counter" data-target="98">0</span>
                        <small>نسبة الرضا</small>
                    </div>
                </div>
            </div>
            <div class="hero-image animate__animated animate__fadeInLeft">
                <img src="https://img.freepik.com/free-vector/learning-concept-illustration_114360-6186.jpg" alt="Learning illustration">
            </div>
        </section>

        <section id="stages">
            <div class="section-title">
                <h2>اختر مرحلتك الدراسية</h2>
                <p style="color: #64748b; margin-top: 10px;">نرافقك من الصف السابع وحتى عتبة الجامعة</p>
            </div>

            <div class="stages-container" id="stagesWrapper">
                <!-- ستتم التعبئة بواسطة JavaScript -->
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-logo">جِســرـ</div>
        <p style="max-width: 600px; margin: 0 auto; opacity: 0.8;">
            نحن نؤمن أن العلم هو السلاح الأقوى، ومن هنا انطلقت منصة جسر لتمكين كل طالب فلسطيني من الوصول إلى أفضل جودة تعليمية مجاناً.
        </p>
        <div class="social-links">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
            <a href="#"><i class="fab fa-whatsapp"></i></a>
        </div>
        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 30px 0;">
        <p>© 2026 جميع الحقوق محفوظة لمنصة جسر التعليمية 🇵🇸</p>
    </footer>

    <script>
        // بيانات المراحل الدراسية مع أيقونات احترافية
        const stages = [
            { id: 7, name: 'السابع', icon: 'fa-seedling', desc: 'بداية الرحلة التعليمية الممتعة' },
            { id: 8, name: 'الثامن', icon: 'fa-book-open', desc: 'تعميق المعارف والمهارات الأساسية' },
            { id: 9, name: 'التاسع', icon: 'fa-brain', desc: 'تحضير متميز للمرحلة الثانوية' },
            { id: 10, name: 'العاشر', icon: 'fa-microscope', desc: 'استكشاف التخصصات العلمية والأدبية' },
            { id: 11, name: 'الحادي عشر', icon: 'fa-rocket', desc: 'الاستعداد للانطلاق نحو القمة' },
            { id: 12, name: 'التوجيهي', icon: 'fa-trophy', desc: 'سنة الحصاد وبناء المستقبل الجامعي' }
        ];

        const wrapper = document.getElementById('stagesWrapper');

        wrapper.innerHTML = stages.map(s => `
            <a href="stage.html?grade=${s.id}" class="stage-card">
                <div class="icon-box">
                    <i class="fas ${s.icon}"></i>
                </div>
                <h3>الصف ${s.name}</h3>
                <p style="color: #64748b; font-size: 0.95rem;">${s.desc}</p>
                <span style="margin-top: 20px; color: var(--primary); font-weight: 700;">
                    تصفح المواد <i class="fas fa-chevron-left" style="font-size: 0.8rem; margin-right: 5px;"></i>
                </span>
            </a>
        `).join('');

        // تأثير الـ Navbar عند التمرير
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // عداد الأرقام الذكي
        const counters = document.querySelectorAll('.counter');
        const speed = 200;

        const startCounters = () => {
            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText;
                    const inc = target / speed;

                    if (count < target) {
                        counter.innerText = Math.ceil(count + inc);
                        setTimeout(updateCount, 15);
                    } else {
                        counter.innerText = target + (target > 100 ? "+" : "%");
                    }
                };
                updateCount();
            });
        };

        // تشغيل العداد عند التمرير إليه فقط
        const observer = new IntersectionObserver((entries) => {
            if(entries[0].isIntersecting) startCounters();
        }, { threshold: 0.5 });

        observer.observe(document.querySelector('.stats-grid'));
    </script>
</body>
</html>
