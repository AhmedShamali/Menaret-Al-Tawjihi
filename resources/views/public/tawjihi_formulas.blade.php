<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل القوانين والقواعد الذهبية للتوجيهي | {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</title>

    <!-- Google Fonts: Alexandria -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #eff6ff;
            --emerald: #10b981;
            --amber: #f59e0b;
            --rose: #f43f5e;
            --purple: #8b5cf6;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --border-card: #e2e8f0;
            --text-title: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 30px -10px rgba(37, 99, 235, 0.12);
        }

        body.dark-theme {
            --bg-main: #060913;
            --bg-card: #0f172a;
            --border-card: #1e293b;
            --text-title: #f8fafc;
            --text-body: #cbd5e1;
            --text-muted: #94a3b8;
            --primary-light: #1e293b;
        }

        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Alexandria', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-body);
            line-height: 1.7;
            min-height: 100vh;
            transition: background 0.3s ease, color 0.3s ease;
        }

        /* Top Navbar */
        nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 8%; background: var(--bg-card);
            backdrop-filter: blur(12px); border-bottom: 1px solid var(--border-card);
            position: sticky; top: 0; z-index: 50;
        }
        .nav-logo {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none; font-weight: 800; font-size: 1.25rem; color: var(--text-title);
        }
        .logo-badge {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg, #2563eb, #6366f1);
            color: white; display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .btn-nav-action {
            text-decoration: none; padding: 8px 16px; border-radius: 10px;
            font-size: 0.88rem; font-weight: 700; color: var(--text-title);
            background: var(--bg-main); border: 1px solid var(--border-card);
            transition: 0.2s ease; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-nav-action:hover { color: var(--primary); border-color: var(--primary); }

        /* Hero Header */
        .header-section {
            text-align: center; padding: 45px 20px 20px; max-width: 900px; margin: 0 auto;
        }
        .badge-pill {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 16px; background: var(--primary-light); border: 1px solid rgba(37, 99, 235, 0.2);
            color: var(--primary); border-radius: 50px; font-size: 0.85rem;
            font-weight: 700; margin-bottom: 16px;
        }
        .header-section h1 {
            font-size: 2.4rem; font-weight: 900; color: var(--text-title);
            margin-bottom: 12px; line-height: 1.3;
        }
        .header-section p {
            color: var(--text-muted); font-size: 1rem; max-width: 720px; margin: 0 auto 30px;
        }

        /* Search and Filter Controls */
        .controls-wrapper {
            max-width: 900px; margin: 0 auto 35px; padding: 0 20px;
            display: flex; flex-direction: column; gap: 16px;
        }
        .search-box {
            position: relative; width: 100%;
        }
        .search-box i {
            position: absolute; right: 18px; top: 50%; transform: translateY(-50%);
            color: var(--text-muted); font-size: 1.1rem;
        }
        .search-input {
            width: 100%; padding: 15px 48px 15px 20px; border-radius: 16px;
            border: 1px solid var(--border-card); background: var(--bg-card);
            color: var(--text-title); font-size: 0.95rem; outline: none;
            transition: 0.2s ease; box-shadow: var(--shadow-sm);
        }
        .search-input:focus {
            border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* Subject Tabs */
        .subject-tabs {
            display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;
        }
        .tab-btn {
            padding: 10px 22px; border-radius: 14px; border: 1px solid var(--border-card);
            background: var(--bg-card); color: var(--text-title); font-weight: 700;
            font-size: 0.88rem; cursor: pointer; transition: 0.2s ease;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .tab-btn:hover {
            border-color: var(--primary); color: var(--primary);
        }
        .tab-btn.active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white !important; border-color: transparent;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        /* Main Container */
        .content-container {
            max-width: 1100px; margin: 0 auto 60px; padding: 0 20px;
        }

        .section-block {
            margin-bottom: 40px;
        }
        .section-header-title {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--border-card);
        }
        .section-header-title h2 {
            font-size: 1.25rem; font-weight: 800; color: var(--text-title);
            display: flex; align-items: center; gap: 10px;
        }

        /* Formulas Grid */
        .formulas-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 18px;
        }

        .formula-card {
            background: var(--bg-card); border: 1px solid var(--border-card);
            border-radius: 18px; padding: 22px; transition: 0.25s ease;
            box-shadow: var(--shadow-sm); display: flex; flex-direction: column;
            justify-content: space-between; position: relative;
        }
        .formula-card:hover {
            transform: translateY(-3px); box-shadow: var(--shadow-md);
            border-color: rgba(37, 99, 235, 0.3);
        }

        .card-top {
            display: flex; justify-content: space-between; align-items: flex-start;
            margin-bottom: 12px; gap: 10px;
        }
        .card-top h3 {
            font-size: 0.98rem; font-weight: 800; color: var(--text-title);
        }

        .btn-copy {
            background: var(--bg-main); border: 1px solid var(--border-card);
            color: var(--text-muted); width: 32px; height: 32px; border-radius: 8px;
            cursor: pointer; display: grid; place-items: center; font-size: 0.85rem;
            transition: 0.2s; flex-shrink: 0;
        }
        .btn-copy:hover {
            background: var(--primary); color: white; border-color: var(--primary);
        }

        .formula-display {
            background: var(--bg-main); border: 1px solid var(--border-card);
            border-radius: 12px; padding: 14px 16px; margin-bottom: 12px;
            font-family: 'JetBrains Mono', monospace; font-size: 0.98rem; font-weight: 700;
            color: var(--primary); direction: ltr; text-align: center;
            overflow-x: auto; white-space: nowrap;
        }

        .formula-desc {
            font-size: 0.82rem; color: var(--text-muted); line-height: 1.6;
        }

        /* Print Media Styles */
        @media print {
            nav, .controls-wrapper, .badge-pill, .btn-copy { display: none !important; }
            body { background: white !important; color: black !important; }
            .formula-card { border: 1px solid #ccc !important; box-shadow: none !important; page-break-inside: avoid; }
            .formula-display { border: 1px solid #ddd !important; color: black !important; background: #f9f9f9 !important; }
        }

        @media (max-width: 768px) {
            .header-section h1 { font-size: 1.8rem; }
            .formulas-grid { grid-template-columns: 1fr; }
        }

        /* زر العودة للأعلى */
        .ed-scroll-top-btn {
            position: fixed;
            bottom: 24px;
            left: 24px;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0f243d 0%, #1e3a8a 100%);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.25);
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
                        box-shadow 0.2s ease;
        }
        .ed-scroll-top-btn.visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }
        .ed-scroll-top-btn:hover {
            background: linear-gradient(135deg, #173252 0%, #2563eb 100%);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
            transform: translateY(-3px) scale(1.05);
            color: #ffffff;
        }
        html[dir="ltr"] .ed-scroll-top-btn {
            left: auto;
            right: 24px;
        }
        @media (max-width: 768px) {
            .ed-scroll-top-btn {
                bottom: 20px;
                left: 16px;
                width: 38px;
                height: 38px;
                font-size: 0.92rem;
            }
            html[dir="ltr"] .ed-scroll-top-btn {
                left: auto;
                right: 16px;
            }
        }
        @media print {
            .ed-scroll-top-btn { display: none !important; }
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <nav>
        <a href="/" class="nav-logo">
            <div class="logo-badge"><i class="fa-solid fa-graduation-cap"></i></div>
            <span>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</span>
        </a>

        <div class="nav-actions">
            <button onclick="window.print()" class="btn-nav-action" title="{{ __('طباعة ملخص القوانين') }}">
                <i class="fa-solid fa-print"></i> <span>{{ __('طباعة الملخص') }}</span>
            </button>
            <a href="{{ route('tawjihi.calculator') }}" class="btn-nav-action">
                <i class="fa-solid fa-calculator"></i> <span>{{ __('حاسبة المعدل') }}</span>
            </a>
            <a href="{{ route('tawjihi.archive') }}" class="btn-nav-action">
                <i class="fa-solid fa-file-pdf"></i> <span>{{ __('بنك الامتحانات') }}</span>
            </a>
            <button id="themeToggleBtn" onclick="toggleTheme()" class="btn-nav-action" style="padding: 8px 12px;">
                <i class="fa-solid fa-moon" id="themeIcon"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Header -->
    <section class="header-section">
        <div class="badge-pill">
            <i class="fa-solid fa-bolt"></i>
            <span>الملخص الشامل للقوانين والقواعد الوزارية المعتمدة</span>
        </div>
        <h1>{{ __('دليل القوانين الذهبية لامتحانات الثانوية العامة') }}</h1>
        <p>{{ __('مرجعك السريع والدقيق لكافة قوانين الرياضيات، الفيزياء، الكيمياء، وقواعد اللغة الإنجليزية المقررة في المنهاج الفلسطيني للتوجيهي.') }}</p>
    </section>

    <!-- Controls: Search & Tabs -->
    <div class="controls-wrapper">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="formulaSearchInput" class="search-input" placeholder="ابحث عن قانون، قاعدة، أو رمز (مثال: اشتقاق، زخم، pH، Conditionals)..." oninput="filterFormulas()">
        </div>

        <div class="subject-tabs">
            <button class="tab-btn active" data-filter="all" onclick="filterBySubject('all', this)">
                <i class="fa-solid fa-layer-group"></i> <span>{{ __('جميع المواد') }}</span>
            </button>
            <button class="tab-btn" data-filter="math" onclick="filterBySubject('math', this)">
                <i class="fa-solid fa-calculator"></i> <span>{{ __('الرياضيات') }}</span>
            </button>
            <button class="tab-btn" data-filter="physics" onclick="filterBySubject('physics', this)">
                <i class="fa-solid fa-atom"></i> <span>{{ __('الفيزياء') }}</span>
            </button>
            <button class="tab-btn" data-filter="chemistry" onclick="filterBySubject('chemistry', this)">
                <i class="fa-solid fa-flask-vial"></i> <span>{{ __('الكيمياء') }}</span>
            </button>
            <button class="tab-btn" data-filter="english" onclick="filterBySubject('english', this)">
                <i class="fa-solid fa-language"></i> <span>{{ __('اللغة الإنجليزية') }}</span>
            </button>
        </div>
    </div>

    <!-- Content Sections -->
    <main class="content-container">
        @foreach($categories as $catKey => $cat)
            <div class="section-block" data-subject="{{ $catKey }}">
                <div class="section-header-title">
                    <h2>
                        <i class="fa-solid {{ $cat['icon'] }}" style="color: {{ $cat['color'] }};"></i>
                        <span>{{ $cat['title'] }}</span>
                    </h2>
                    <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); background: var(--bg-card); padding: 4px 12px; border-radius: 20px; border: 1px solid var(--border-card);">{{ __('منهاج فلسطين الرسمي') }}</span>
                </div>

                @foreach($cat['sections'] as $sec)
                    <div style="margin-bottom: 25px;">
                        <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-muted); margin-bottom: 14px; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-chevron-left" style="font-size: 0.75rem; color: {{ $cat['color'] }};"></i>
                            {{ $sec['name'] }}
                        </h3>

                        <div class="formulas-grid">
                            @foreach($sec['items'] as $item)
                                <div class="formula-card" data-keywords="{{ strtolower($item['name'] . ' ' . $item['formula'] . ' ' . $item['desc']) }}">
                                    <div>
                                        <div class="card-top">
                                            <h3>{{ $item['name'] }}</h3>
                                            <button class="btn-copy" onclick="copyFormula('{{ addslashes($item['formula']) }}', this)" title="{{ __('نسخ القانون') }}">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        </div>

                                        <div class="formula-display">
                                            {{ $item['formula'] }}
                                        </div>
                                    </div>

                                    <p class="formula-desc">{{ $item['desc'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

        <div id="noResultsBox" style="display: none; text-align: center; padding: 60px 20px; background: var(--bg-card); border-radius: 20px; border: 1px solid var(--border-card);">
            <i class="fa-solid fa-search" style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 12px; opacity: 0.4;"></i>
            <h3 style="color: var(--text-title); font-size: 1.1rem; margin-bottom: 6px;">{{ __('لم يتم العثور على قوانين مطابقة') }}</h3>
            <p style="color: var(--text-muted); font-size: 0.88rem;">جرب كتابة مصطلح آخر مثل "مشتقة" أو "زخم" أو "pH" أو "شرطية".</p>
        </div>
    </main>

    <!-- Scripts -->
    <script>
        let currentFilter = 'all';

        function filterBySubject(subject, btn) {
            currentFilter = subject;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            const sections = document.querySelectorAll('.section-block');
            sections.forEach(sec => {
                if (subject === 'all' || sec.getAttribute('data-subject') === subject) {
                    sec.style.display = 'block';
                } else {
                    sec.style.display = 'none';
                }
            });

            filterFormulas();
        }

        function filterFormulas() {
            const query = document.getElementById('formulaSearchInput').value.trim().toLowerCase();
            const cards = document.querySelectorAll('.formula-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const parentSection = card.closest('.section-block');
                const subjectMatch = (currentFilter === 'all' || parentSection.getAttribute('data-subject') === currentFilter);
                const keywords = card.getAttribute('data-keywords');
                const textMatch = !query || keywords.includes(query);

                if (subjectMatch && textMatch) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // إخفاء العناوين الفارغة
            document.querySelectorAll('.section-block').forEach(sec => {
                if (currentFilter !== 'all' && sec.getAttribute('data-subject') !== currentFilter) {
                    sec.style.display = 'none';
                    return;
                }
                const visibleInSec = sec.querySelectorAll('.formula-card[style="display: flex;"]').length;
                sec.style.display = (visibleInSec > 0) ? 'block' : 'none';
            });

            const noRes = document.getElementById('noResultsBox');
            if (noRes) {
                noRes.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        }

        function copyFormula(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const origHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check" style="color: #10b981;"></i>';
                setTimeout(() => { btn.innerHTML = origHtml; }, 1500);

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'تم نسخ القانون إلى الحافظة',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }

        // إعداد الوضع الليلي
        function toggleTheme() {
            const isDark = document.body.classList.toggle('dark-theme');
            localStorage.setItem('tawjihi-theme', isDark ? 'dark' : 'light');
            updateThemeIcon(isDark);
        }

        function updateThemeIcon(isDark) {
            const icon = document.getElementById('themeIcon');
            if (icon) {
                icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
        }

        (function initTheme() {
            const savedTheme = localStorage.getItem('tawjihi-theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-theme');
                updateThemeIcon(true);
            }
        })();

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

    <!-- زر العودة إلى بداية الصفحة الكلاسيكي الأنيق (Scroll to Top Button) -->
    <button type="button" class="ed-scroll-top-btn" id="edScrollTopBtn" aria-label="{{ __('العودة إلى بداية الصفحة') }}" title="{{ __('العودة للأعلى') }}" onclick="scrollToPageTop()">
        <i class="fa-solid fa-chevron-up"></i>
    </button>
</body>
</html>
