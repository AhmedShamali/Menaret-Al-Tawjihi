<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حاسبة معدل التوجيهي ودليل التنسيق الجامعي | {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</title>

    @if(\App\Models\Setting::get('site_favicon'))
        <link rel="icon" href="{{ asset(\App\Models\Setting::get('site_favicon')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @endif

    <!-- Google Fonts: Alexandria & Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-page: #f8fafc;
            --bg-surface: #ffffff;
            --border-subtle: #e2e8f0;
            --border-focus: #3b82f6;
            --primary: #1e40af;
            --primary-soft: #eff6ff;
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --success: #059669;
            --success-soft: #ecfdf5;
            --warning: #d97706;
            --warning-soft: #fffbeb;
            --radius-md: 12px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.05);
            --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.06);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Alexandria', 'Tajawal', sans-serif;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-body);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Top Navigation */
        header.nav-header {
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-subtle);
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-heading);
            font-weight: 700;
            font-size: 1.15rem;
        }

        .brand-logo-badge {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link-btn {
            text-decoration: none;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-body);
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .nav-link-btn:hover {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .nav-link-btn.primary {
            background: var(--primary);
            color: #ffffff;
        }

        .nav-link-btn.primary:hover {
            background: #1d4ed8;
        }

        /* Main Container */
        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 24px 80px;
        }

        /* Hero / Introduction Header */
        .calc-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .badge-academic {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 14px;
            background: var(--primary-soft);
            color: var(--primary);
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .calc-header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .calc-header p {
            color: var(--text-muted);
            font-size: 1rem;
            max-width: 650px;
            margin: 0 auto;
        }

        /* Branch Selector Tabs */
        .branch-nav {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 28px auto 36px;
            background: #ffffff;
            padding: 6px;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            max-width: 580px;
            box-shadow: var(--shadow-sm);
        }

        .branch-btn {
            flex: 1;
            padding: 11px 16px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 0.92rem;
            font-weight: 600;
            border-radius: var(--radius-md);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .branch-btn.active {
            background: var(--primary);
            color: #ffffff;
            box-shadow: var(--shadow-sm);
        }

        /* Two Columns Layout */
        .calculator-grid {
            display: grid;
            grid-template-columns: 1.25fr 0.85fr;
            gap: 32px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .calculator-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Card Panels */
        .card-panel {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-sm);
        }

        .panel-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .panel-title i {
            color: var(--primary);
            font-size: 1.2rem;
        }

        .panel-title h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-heading);
        }

        .section-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 20px 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-label:first-of-type {
            margin-top: 0;
        }

        /* Input Grid */
        .inputs-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        @media (max-width: 600px) {
            .inputs-row {
                grid-template-columns: 1fr;
            }
        }

        .input-box {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .input-box label {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-heading);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .input-box label span.max-tag {
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--primary);
            background: var(--primary-soft);
            padding: 2px 8px;
            border-radius: 6px;
        }

        .field-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .field-wrapper i {
            position: absolute;
            right: 14px;
            color: var(--text-muted);
            font-size: 0.95rem;
            pointer-events: none;
        }

        .field-input {
            width: 100%;
            padding: 10px 42px 10px 14px;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            color: var(--text-heading);
            background: #ffffff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field-input:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .field-input.input-error {
            border-color: #ef4444;
            background: #fff5f5;
        }

        /* Notice / Explanatory Box */
        .info-card {
            background: #f8fafc;
            border: 1px solid var(--border-subtle);
            border-right: 4px solid var(--primary);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-top: 24px;
            display: flex;
            gap: 12px;
            font-size: 0.88rem;
            color: var(--text-body);
            line-height: 1.6;
        }

        .info-card i {
            color: var(--primary);
            font-size: 1.1rem;
            margin-top: 2px;
        }

        /* Result Panel Sticky Card */
        .result-box {
            position: sticky;
            top: 90px;
        }

        .result-badge {
            display: inline-block;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .score-display {
            font-size: 3.2rem;
            font-weight: 800;
            color: var(--text-heading);
            line-height: 1;
            margin: 6px 0 10px;
            letter-spacing: -1px;
        }

        .score-status {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 22px;
            color: var(--text-muted);
        }

        .stats-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 16px 0;
            border-top: 1px solid var(--border-subtle);
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 20px;
        }

        .stat-cell h4 {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 4px;
        }

        .stat-cell p {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-heading);
        }

        .btn-view-majors {
            width: 100%;
            background: var(--primary);
            color: #ffffff;
            border: none;
            padding: 12px 18px;
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s;
            margin-bottom: 10px;
        }

        .btn-view-majors:hover {
            background: #1d4ed8;
        }

        .btn-clear {
            width: 100%;
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-subtle);
            padding: 10px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-clear:hover {
            background: #f1f5f9;
            color: var(--text-heading);
        }

        /* Majors Recommendations Section */
        .majors-panel {
            margin-top: 40px;
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 32px;
            box-shadow: var(--shadow-sm);
        }

        .majors-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .majors-panel-header h3 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .count-pill {
            background: var(--primary-soft);
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .majors-list-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 18px;
        }

        .major-item-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .major-item-card:hover {
            border-color: #cbd5e1;
            box-shadow: var(--shadow-sm);
        }

        .major-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .major-badge-status {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .major-badge-status.guaranteed {
            background: var(--success-soft);
            color: var(--success);
        }

        .major-badge-status.competitive {
            background: var(--warning-soft);
            color: var(--warning);
        }

        .major-item-card h4 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 4px;
        }

        .major-category {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 14px;
        }

        .unis-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .uni-chip {
            background: #f1f5f9;
            color: var(--text-body);
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <header class="nav-header">
        <div class="nav-inner">
            <a href="/" class="nav-brand">
                <div class="brand-logo-badge">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</span>
            </a>
            <div class="nav-links">
                <a href="/" class="nav-link-btn"><i class="fas fa-arrow-right"></i> الرئيسية</a>
                <a href="{{ route('login') }}" class="nav-link-btn primary"><i class="fas fa-user-check"></i> تسجيل الدخول</a>
            </div>
        </div>
    </header>

    <!-- Page Content Container -->
    <main class="page-container">

        <!-- Header Introduction -->
        <div class="calc-header">
            <div class="badge-academic">
                <i class="fas fa-check-circle"></i>
                <span>المنهاج الفلسطيني الرسمي المعتمد 2026</span>
            </div>
            <h1>حاسبة معدل التوجيهي ودليل التنسيق</h1>
            <p>احتساب دقيق لمعدل الثانوية العامة وفق السلم الوزاري الفلسطيني (أعلى مادة اختيارية تلقائياً)، مع استعراض التخصصات المتاحة فورياً.</p>

            <!-- Branch Nav Selector -->
            <div class="branch-nav">
                <button type="button" class="branch-btn active" id="btn-scientific" onclick="changeBranch('scientific')">
                    <i class="fas fa-atom"></i> الفرع العلمي
                </button>
                <button type="button" class="branch-btn" id="btn-literary" onclick="changeBranch('literary')">
                    <i class="fas fa-book-open"></i> الفرع الأدبي
                </button>
                <button type="button" class="branch-btn" id="btn-business" onclick="changeBranch('business')">
                    <i class="fas fa-briefcase"></i> الريادة والأعمال
                </button>
            </div>
        </div>

        <!-- Calculator Main Area -->
        <div class="calculator-grid">

            <!-- Input Fields Panel -->
            <div class="card-panel">
                <div class="panel-title">
                    <i class="fas fa-calculator"></i>
                    <div>
                        <h2 id="panel-branch-title">علامات الفرع العلمي (المجموع الكلي: 700)</h2>
                    </div>
                </div>

                <form id="calculator-form" onsubmit="event.preventDefault(); calculate();">
                    <div id="dynamic-inputs-container">
                        <!-- Rendered via Javascript -->
                    </div>

                    <div class="info-card">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>معايير الحساب الرسمية:</strong>
                            يتم احتساب المواد الإجبارية بالكامل، بينما تُقارن علامات المواد الاختيارية وتُحتسب <strong>أعلى مادة اختيارية فقط</strong> لمنح الطالب أفضل مجموع وزاري ممكن من 700.
                        </div>
                    </div>
                </form>
            </div>

            <!-- Result Summary Card -->
            <div class="card-panel result-box">
                <span class="result-badge"><i class="fas fa-chart-line"></i> النتيجة التقديرية</span>
                <div class="score-display" id="display-percentage">0.0%</div>
                <div class="score-status" id="display-status">في انتظار إدخال العلامات...</div>

                <div class="stats-summary">
                    <div class="stat-cell">
                        <h4>المجموع الكلي</h4>
                        <p id="display-total">0 / 700</p>
                    </div>
                    <div class="stat-cell">
                        <h4>المادة الاختيارية</h4>
                        <p id="display-elective" style="font-size: 0.9rem;">-</p>
                    </div>
                </div>

                <button type="button" class="btn-view-majors" onclick="scrollToMajors()">
                    <i class="fas fa-university"></i> عرض التخصصات المتوافقة
                </button>
                <button type="button" class="btn-clear" onclick="clearInputs()">
                    <i class="fas fa-redo-alt"></i> إعادة ضبط الحقول
                </button>
            </div>

        </div>

        <!-- Majors Section -->
        <section class="majors-panel" id="majors-section">
            <div class="majors-panel-header">
                <div>
                    <h3><i class="fas fa-compass" style="color: var(--primary);"></i> التخصصات والكليات المتاحة في الجامعات الفلسطينية</h3>
                    <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: 4px;">مرتبة وفق الحد الأدنى لمفاتيح التنسيق المعتمدة في جامعات الوطن (بيرزيت، النجاح، القدس، خضوري، البوليتكنك، العربية الأمريكية، غزة)</p>
                </div>
                <span class="count-pill" id="majors-count-badge">15 تخصص</span>
            </div>

            <div class="majors-list-grid" id="majors-list-container">
                <!-- Dynamically Populated -->
            </div>
        </section>

    </main>

    <!-- Interactive Script -->
    <script>
        let currentBranch = 'scientific';

        // المنهاج الفلسطيني المعتمد
        // الأدبي: عربي 150، إنجليزي 150، تاريخ 100، جغرافيا 100، إسلامية 100، اختياري 100 -> مجموع 700
        // العلمي: رياضيات 200، فيزياء 100، عربي 100، إنجليزي 100، إسلامية 100، اختياري 100 -> مجموع 700
        const configs = {
            scientific: {
                title: 'علامات الفرع العلمي (المجموع الكلي: 700)',
                mandatory: [
                    { id: 'math', label: 'الرياضيات (علمي)', max: 200, pass: 100, icon: 'fa-square-root-variable', note: 'من 200 (النجاح: 100)' },
                    { id: 'physics', label: 'الفيزياء', max: 100, pass: 50, icon: 'fa-atom', note: 'من 100 (النجاح: 50)' },
                    { id: 'arabic', label: 'اللغة العربية', max: 100, pass: 50, icon: 'fa-feather-pointed', note: 'من 100 (النجاح: 50)' },
                    { id: 'english', label: 'اللغة الإنجليزية', max: 100, pass: 50, icon: 'fa-language', note: 'من 100 (النجاح: 50)' },
                    { id: 'islamic', label: 'التربية الإسلامية', max: 100, pass: 50, icon: 'fa-moon', note: 'من 100 (النجاح: 50)' }
                ],
                electiveTitle: 'المواد الاختيارية (أدخل العلامات وسيُحتسب الأعلى تلقائياً):',
                electives: [
                    { id: 'chemistry', label: 'الكيمياء', max: 100, pass: 50, icon: 'fa-flask' },
                    { id: 'biology', label: 'العلوم الحياتية (الأحياء)', max: 100, pass: 50, icon: 'fa-dna' },
                    { id: 'tech', label: 'التكنولوجيا', max: 100, pass: 50, icon: 'fa-laptop-code' }
                ]
            },
            literary: {
                title: 'علامات الفرع الأدبي (المجموع الكلي: 700)',
                mandatory: [
                    { id: 'arabic', label: 'اللغة العربية (أدبي)', max: 150, pass: 75, icon: 'fa-feather-pointed', note: 'من 150 (النجاح: 75)' },
                    { id: 'english', label: 'اللغة الإنجليزية (أدبي)', max: 150, pass: 75, icon: 'fa-language', note: 'من 150 (النجاح: 75)' },
                    { id: 'history', label: 'الدراسات التاريخية', max: 100, pass: 50, icon: 'fa-landmark', note: 'من 100 (النجاح: 50)' },
                    { id: 'geography', label: 'الدراسات الجغرافية', max: 100, pass: 50, icon: 'fa-earth-americas', note: 'من 100 (النجاح: 50)' },
                    { id: 'islamic', label: 'التربية الإسلامية', max: 100, pass: 50, icon: 'fa-moon', note: 'من 100 (النجاح: 50)' }
                ],
                electiveTitle: 'المواد الاختيارية (يُحتسب المبحث الأعلى فقط من 100):',
                electives: [
                    { id: 'math', label: 'الرياضيات (أدبي)', max: 100, pass: 50, icon: 'fa-calculator' },
                    { id: 'sci_culture', label: 'الثقافة العلمية', max: 100, pass: 50, icon: 'fa-lightbulb' },
                    { id: 'tech', label: 'التكنولوجيا', max: 100, pass: 50, icon: 'fa-laptop-code' }
                ]
            },
            business: {
                title: 'علامات فرع الريادة والأعمال (المجموع الكلي: 700)',
                mandatory: [
                    { id: 'projects', label: 'المشاريع الريادية', max: 100, pass: 50, icon: 'fa-chart-pie', note: 'من 100' },
                    { id: 'accounting', label: 'المحاسبة', max: 100, pass: 50, icon: 'fa-coins', note: 'من 100' },
                    { id: 'mgmt', label: 'الإدارة والاقتصاد', max: 100, pass: 50, icon: 'fa-briefcase', note: 'من 100' },
                    { id: 'arabic', label: 'اللغة العربية', max: 100, pass: 50, icon: 'fa-feather-pointed', note: 'من 100' },
                    { id: 'english', label: 'اللغة الإنجليزية', max: 100, pass: 50, icon: 'fa-language', note: 'من 100' },
                    { id: 'islamic', label: 'التربية الإسلامية', max: 100, pass: 50, icon: 'fa-moon', note: 'من 100' }
                ],
                electiveTitle: 'المواد الاختيارية (يُحتسب المبحث الأعلى فقط من 100):',
                electives: [
                    { id: 'math', label: 'رياضيات الأعمال', max: 100, pass: 50, icon: 'fa-calculator' },
                    { id: 'tech', label: 'التكنولوجيا', max: 100, pass: 50, icon: 'fa-laptop-code' }
                ]
            }
        };

        const allMajors = @json((new \App\Http\Controllers\TawjihiCalculatorController())->getEligibleMajors('scientific', 100));

        function changeBranch(branch) {
            currentBranch = branch;
            document.querySelectorAll('.branch-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(`btn-${branch}`).classList.add('active');
            renderForm();
            calculate();
        }

        function renderForm() {
            const conf = configs[currentBranch];
            document.getElementById('panel-branch-title').textContent = conf.title;
            const container = document.getElementById('dynamic-inputs-container');

            let html = `
                <div class="section-label"><i class="fas fa-shield-halved" style="color: var(--primary);"></i> المواد الإجبارية الأساسية</div>
                <div class="inputs-row">
            `;

            conf.mandatory.forEach(sub => {
                html += `
                    <div class="input-box">
                        <label for="${sub.id}">
                            <span>${sub.label}</span>
                            <span class="max-tag">${sub.note}</span>
                        </label>
                        <div class="field-wrapper">
                            <i class="fas ${sub.icon}"></i>
                            <input type="number" id="${sub.id}" class="field-input" min="0" max="${sub.max}" step="0.5"
                                   placeholder="أدخل علامة المادة" oninput="handleInput(this, ${sub.max})">
                        </div>
                    </div>
                `;
            });

            html += `
                </div>
                <div class="section-label" style="margin-top: 24px;"><i class="fas fa-star" style="color: var(--warning);"></i> ${conf.electiveTitle}</div>
                <div class="inputs-row">
            `;

            conf.electives.forEach(sub => {
                html += `
                    <div class="input-box">
                        <label for="${sub.id}">
                            <span>${sub.label}</span>
                            <span class="max-tag">من ${sub.max}</span>
                        </label>
                        <div class="field-wrapper">
                            <i class="fas ${sub.icon}"></i>
                            <input type="number" id="${sub.id}" class="field-input" min="0" max="${sub.max}" step="0.5"
                                   placeholder="أدخل علامة المادة" oninput="handleInput(this, ${sub.max})">
                        </div>
                    </div>
                `;
            });

            html += `</div>`;
            container.innerHTML = html;
        }

        function handleInput(elem, max) {
            let val = parseFloat(elem.value);
            if (val > max) {
                elem.value = max;
                elem.classList.add('input-error');
            } else if (val < 0) {
                elem.value = 0;
                elem.classList.remove('input-error');
            } else {
                elem.classList.remove('input-error');
            }
            calculate();
        }

        function calculate() {
            const conf = configs[currentBranch];
            let total = 0;
            let enteredAny = false;

            conf.mandatory.forEach(sub => {
                const el = document.getElementById(sub.id);
                if (el && el.value !== '') {
                    total += parseFloat(el.value) || 0;
                    enteredAny = true;
                }
            });

            let bestScore = 0;
            let bestName = 'لم تُحدد';

            conf.electives.forEach(sub => {
                const el = document.getElementById(sub.id);
                if (el && el.value !== '') {
                    enteredAny = true;
                    const score = parseFloat(el.value) || 0;
                    if (score > bestScore) {
                        bestScore = score;
                        bestName = `${sub.label} (${score})`;
                    }
                }
            });

            total += bestScore;
            const percentage = (total / 700) * 100;
            const formattedPercentage = percentage.toFixed(1);

            document.getElementById('display-percentage').textContent = formattedPercentage + '%';
            document.getElementById('display-total').textContent = `${Math.round(total * 10) / 10} / 700`;
            document.getElementById('display-elective').textContent = bestName;

            const statusEl = document.getElementById('display-status');
            if (percentage >= 90) {
                statusEl.textContent = 'امتياز وتفوق عالي 🌟 فرص قبول لكافة الكليات';
                statusEl.style.color = '#059669';
            } else if (percentage >= 80) {
                statusEl.textContent = 'جيد جداً مرتفع 🎓 فرص ممتازة في التخصصات الهندسية والصحية';
                statusEl.style.color = '#2563eb';
            } else if (percentage >= 70) {
                statusEl.textContent = 'جيد ✨ خيارات واسعة في تكنولوجيا المعلومات والعلوم الإدارية';
                statusEl.style.color = '#d97706';
            } else if (percentage >= 50) {
                statusEl.textContent = 'ناجح ومؤهل للالتحاق بالبرامج الجامعية والدبلوم';
                statusEl.style.color = '#475569';
            } else {
                statusEl.textContent = enteredAny ? 'في انتظار استكمال إدخال باقي المواد...' : 'في انتظار إدخال العلامات...';
                statusEl.style.color = '#94a3b8';
            }

            renderMajors(percentage);
        }

        function renderMajors(percentage) {
            const listEl = document.getElementById('majors-list-container');
            const countBadge = document.getElementById('majors-count-badge');

            const filtered = allMajors.filter(m => {
                return m.allowed_branches.includes(currentBranch) && (percentage >= (m.min_rate - 1.5) || percentage === 0);
            });

            countBadge.textContent = `${filtered.length} تخصص متاح`;

            if (filtered.length === 0) {
                listEl.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 12px; display: block;"></i>
                        أدخل علاماتك للاطلاع على التخصصات المتوافقة مع معدلك المحسوب.
                    </div>
                `;
                return;
            }

            let html = '';
            filtered.forEach(m => {
                const isGuaranteed = percentage >= m.min_rate;
                const statusClass = isGuaranteed ? 'guaranteed' : 'competitive';
                const statusText = isGuaranteed ? 'مضمون القبول' : 'منافسة قوية / موازي';

                html += `
                    <div class="major-item-card">
                        <div>
                            <div class="major-top">
                                <span class="major-badge-status ${statusClass}">${statusText} (مفتاح ${m.min_rate}%)</span>
                            </div>
                            <h4>${m.title}</h4>
                            <div class="major-category">${m.category}</div>
                        </div>
                        <div class="unis-row">
                            ${m.universities.map(u => `<span class="uni-chip">${u}</span>`).join('')}
                        </div>
                    </div>
                `;
            });

            listEl.innerHTML = html;
        }

        function clearInputs() {
            document.querySelectorAll('.field-input').forEach(input => input.value = '');
            calculate();
        }

        function scrollToMajors() {
            document.getElementById('majors-section').scrollIntoView({ behavior: 'smooth' });
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderForm();
            calculate();
        });
    </script>
</body>
</html>
