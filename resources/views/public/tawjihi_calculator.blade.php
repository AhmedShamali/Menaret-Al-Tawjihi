<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حاسبة معدل توجيهي فلسطين ودليل التنسيق الجامعي | {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</title>

    <!-- Google Fonts: Alexandria -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
        }

        /* Ambient Glow */
        .ambient-glow {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            pointer-events: none; z-index: -1; overflow: hidden;
        }
        .glow-1 {
            position: absolute; top: -15%; right: 10%; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, rgba(255,255,255,0) 70%);
            filter: blur(50px);
        }
        .glow-2 {
            position: absolute; bottom: 10%; left: 5%; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.06) 0%, rgba(255,255,255,0) 70%);
            filter: blur(60px);
        }

        /* Top Navbar */
        nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px 8%; background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px); border-bottom: 1px solid var(--border-card);
            position: sticky; top: 0; z-index: 50;
        }
        .nav-logo {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none; font-weight: 800; font-size: 1.25rem; color: var(--text-title);
        }
        .logo-badge {
            width: 42px; height: 42px; border-radius: 12px;
            background: linear-gradient(135deg, #2563eb, #6366f1);
            color: white; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        .nav-actions { display: flex; align-items: center; gap: 12px; }
        .btn-nav-home {
            text-decoration: none; padding: 8px 18px; border-radius: 10px;
            font-size: 0.9rem; font-weight: 600; color: var(--text-muted);
            transition: 0.2s ease;
        }
        .btn-nav-home:hover { color: var(--primary); background: var(--primary-light); }

        /* Hero Header */
        .header-section {
            text-align: center; padding: 48px 20px 24px; max-width: 900px; margin: 0 auto;
        }
        .badge-pill {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 16px; background: #dbeafe; border: 1px solid #bfdbfe;
            color: var(--primary); border-radius: 50px; font-size: 0.85rem;
            font-weight: 600; margin-bottom: 16px;
        }
        .badge-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--primary); }
        .header-section h1 {
            font-size: 2.2rem; font-weight: 800; color: var(--text-title); margin-bottom: 12px;
        }
        .header-section p {
            color: var(--text-muted); font-size: 1.05rem; max-width: 680px; margin: 0 auto;
        }

        /* Branch Selector Tabs */
        .branch-tabs {
            display: flex; justify-content: center; gap: 12px; margin: 30px auto;
            max-width: 600px; padding: 6px; background: white;
            border: 1px solid var(--border-card); border-radius: 16px;
            box-shadow: var(--shadow-sm);
        }
        .branch-tab-btn {
            flex: 1; padding: 12px 18px; border: none; background: transparent;
            font-weight: 700; font-size: 0.95rem; color: var(--text-muted);
            border-radius: 12px; cursor: pointer; transition: all 0.3s ease;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .branch-tab-btn.active {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        /* Main Container */
        .container {
            max-width: 1200px; margin: 0 auto 60px; padding: 0 20px;
            display: grid; grid-template-columns: 1.2fr 0.9fr; gap: 32px;
            align-items: start;
        }

        /* Calculator Card */
        .calc-card {
            background: white; border-radius: 24px; border: 1px solid var(--border-card);
            padding: 32px; box-shadow: var(--shadow-md);
        }
        .card-header-title {
            display: flex; align-items: center; gap: 12px; margin-bottom: 24px;
            padding-bottom: 16px; border-bottom: 1px solid var(--border-card);
        }
        .card-header-title i {
            font-size: 1.4rem; color: var(--primary);
            background: var(--primary-light); width: 44px; height: 44px;
            border-radius: 12px; display: flex; align-items: center; justify-content: center;
        }
        .card-header-title h2 { font-size: 1.25rem; font-weight: 800; color: var(--text-title); }

        .form-section-title {
            font-size: 0.95rem; font-weight: 700; color: var(--text-muted);
            margin: 20px 0 14px; display: flex; align-items: center; gap: 8px;
        }
        .form-section-title span {
            background: #f1f5f9; padding: 2px 8px; border-radius: 6px; font-size: 0.8rem;
        }

        .inputs-grid {
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;
        }

        .input-group {
            display: flex; flex-direction: column; gap: 6px;
        }
        .input-group label {
            font-size: 0.88rem; font-weight: 600; color: var(--text-title);
            display: flex; justify-content: space-between;
        }
        .input-group label span { font-size: 0.78rem; color: var(--text-muted); }
        .input-control-wrapper {
            position: relative; display: flex; align-items: center;
        }
        .input-control-wrapper i {
            position: absolute; right: 14px; color: var(--text-muted); font-size: 0.9rem;
        }
        .score-input {
            width: 100%; padding: 12px 38px 12px 14px;
            border: 1.5px solid var(--border-card); border-radius: 12px;
            font-size: 1rem; font-weight: 700; color: var(--text-title);
            transition: 0.2s ease; outline: none; background: #fafafa;
        }
        .score-input:focus {
            border-color: var(--primary); background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        .score-input.error { border-color: var(--rose); background: #fff1f2; }

        /* Summary & Results Card (Sticky Sidebar) */
        .result-panel {
            position: sticky; top: 96px;
            display: flex; flex-direction: column; gap: 24px;
        }
        .result-card {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: white; border-radius: 24px; padding: 32px;
            box-shadow: var(--shadow-lg); text-align: center; position: relative;
            overflow: hidden;
        }
        .result-card::before {
            content: ''; position: absolute; top: -50px; right: -50px;
            width: 150px; height: 150px; border-radius: 50%;
            background: rgba(37, 99, 235, 0.2); filter: blur(30px);
        }
        .result-pill {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255, 255, 255, 0.1); padding: 4px 14px;
            border-radius: 50px; font-size: 0.82rem; font-weight: 600; margin-bottom: 16px;
        }
        .result-score {
            font-size: 3.6rem; font-weight: 900; line-height: 1.1; margin-bottom: 6px;
            background: linear-gradient(135deg, #60a5fa, #34d399);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .result-status {
            font-size: 1.1rem; font-weight: 700; color: #94a3b8; margin-bottom: 20px;
        }
        .result-details-grid {
            display: grid; grid-template-columns: repeat(2, 1fr);
            gap: 12px; background: rgba(255, 255, 255, 0.06);
            border-radius: 14px; padding: 14px; margin-bottom: 20px;
        }
        .res-stat-item h4 { font-size: 0.78rem; color: #94a3b8; margin-bottom: 4px; }
        .res-stat-item p { font-size: 1.1rem; font-weight: 700; color: white; }

        .btn-action-calc {
            width: 100%; padding: 14px; border: none; border-radius: 12px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white; font-weight: 700; font-size: 0.95rem; cursor: pointer;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: 0.2s ease;
        }
        .btn-action-calc:hover { filter: brightness(1.1); transform: translateY(-2px); }

        .btn-reset {
            margin-top: 8px; background: transparent; border: none;
            color: #94a3b8; font-size: 0.85rem; cursor: pointer; text-decoration: underline;
        }
        .btn-reset:hover { color: white; }

        /* Majors & University Recommendations */
        .majors-container {
            grid-column: span 2; margin-top: 20px;
        }
        .majors-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 24px;
        }
        .majors-header h3 {
            font-size: 1.4rem; font-weight: 800; color: var(--text-title);
            display: flex; align-items: center; gap: 10px;
        }
        .majors-badge-count {
            background: #dbeafe; color: var(--primary); font-size: 0.85rem;
            padding: 4px 12px; border-radius: 50px; font-weight: 700;
        }

        .majors-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        .major-card {
            background: white; border: 1px solid var(--border-card);
            border-radius: 18px; padding: 22px; box-shadow: var(--shadow-sm);
            transition: 0.3s ease; position: relative; overflow: hidden;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .major-card:hover {
            transform: translateY(-4px); box-shadow: var(--shadow-md);
            border-color: #93c5fd;
        }
        .major-card-top {
            display: flex; justify-content: space-between; align-items: start; margin-bottom: 14px;
        }
        .major-icon-box {
            width: 48px; height: 48px; border-radius: 14px;
            background: #eff6ff; color: var(--primary);
            display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
        }
        .major-rate-badge {
            padding: 4px 12px; border-radius: 50px; font-size: 0.82rem; font-weight: 700;
        }
        .rate-guaranteed { background: #dcfce7; color: #15803d; }
        .rate-competitive { background: #fef3c7; color: #b45309; }

        .major-card h4 {
            font-size: 1.05rem; font-weight: 700; color: var(--text-title); margin-bottom: 6px;
        }
        .major-category {
            font-size: 0.82rem; color: var(--text-muted); margin-bottom: 14px; font-weight: 500;
        }
        .major-universities-list {
            border-top: 1px solid #f1f5f9; padding-top: 12px;
            display: flex; flex-wrap: wrap; gap: 6px;
        }
        .uni-tag {
            font-size: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0;
            padding: 3px 8px; border-radius: 6px; color: var(--text-body); font-weight: 600;
        }

        /* Notice Box */
        .notice-box {
            margin-top: 24px; padding: 16px; border-radius: 12px;
            background: #fffbeb; border: 1px solid #fef3c7;
            display: flex; align-items: start; gap: 12px; font-size: 0.88rem; color: #92400e;
        }
        .notice-box i { font-size: 1.1rem; margin-top: 2px; }

        /* Responsive */
        @media (max-width: 900px) {
            .container { grid-template-columns: 1fr; }
            .result-panel { position: static; order: -1; }
            .majors-container { grid-column: span 1; }
            .inputs-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Ambient Glow Background -->
    <div class="ambient-glow">
        <div class="glow-1"></div>
        <div class="glow-2"></div>
    </div>

    <!-- Navigation Bar -->
    <nav>
        <a href="/" class="nav-logo">
            <div class="logo-badge">🇵🇸</div>
            <span>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</span>
        </a>
        <div class="nav-actions">
            <a href="/" class="btn-nav-home"><i class="fas fa-home"></i> الرئيسية</a>
            <a href="{{ route('login') }}" class="btn-nav-home" style="background: var(--primary); color: white;"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="header-section">
        <div class="badge-pill">
            <span class="badge-dot"></span>
            <span>المنهاج الفلسطيني الرسمي المعتمد 2026</span>
        </div>
        <h1>حاسبة معدل التوجيهي ودليل التنسيق الجامعي</h1>
        <p>احسب معدلك الوزاري بدقة متناهية وفق أعلى المواد الاختيارية وتعرّف على التخصصات والجامعات الفلسطينية المتاحة لك فورياً.</p>

        <!-- Branch Tabs -->
        <div class="branch-tabs">
            <button class="branch-tab-btn active" onclick="switchBranch('scientific')" id="tab-scientific">
                <i class="fas fa-atom"></i> الفرع العلمي
            </button>
            <button class="branch-tab-btn" onclick="switchBranch('literary')" id="tab-literary">
                <i class="fas fa-book-open"></i> الفرع الأدبي
            </button>
            <button class="branch-tab-btn" onclick="switchBranch('business')" id="tab-business">
                <i class="fas fa-briefcase"></i> الريادة والأعمال
            </button>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="container">

        <!-- Calculator Form Card -->
        <div class="calc-card">
            <div class="card-header-title">
                <i class="fas fa-calculator"></i>
                <div>
                    <h2 id="branch-title">علامات الفرع العلمي (مجموع 700 علامة)</h2>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">أدخل علاماتك المتوقعة أو الفعلية وسنقوم باحتساب أعلى اختياري تلقائياً</p>
                </div>
            </div>

            <!-- Dynamic Inputs Container -->
            <form id="calculator-form" onsubmit="event.preventDefault(); calculateScore();">
                <div id="branch-inputs-wrapper">
                    <!-- Loaded via JavaScript based on chosen branch -->
                </div>

                <div class="notice-box">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>ملاحظة وزارية هامة:</strong> يتم احتساب المواد الإجبارية بالكامل، بينما تُفحص المواد الاختيارية تلقائياً ويُحتسب فقط <strong>أعلى مادة اختيارية علامةً</strong> لضمان حصول الطالب على أعلى معدل ممكن.
                    </div>
                </div>
            </form>
        </div>

        <!-- Result Sidebar Panel -->
        <div class="result-panel">
            <div class="result-card">
                <div class="result-pill">
                    <i class="fas fa-award"></i> النتيجة التقديرية
                </div>
                <div class="result-score" id="result-percentage">0.0%</div>
                <div class="result-status" id="result-status">في انتظار إدخال العلامات...</div>

                <div class="result-details-grid">
                    <div class="res-stat-item">
                        <h4>المجموع المحسوب</h4>
                        <p id="result-total">0 / 700</p>
                    </div>
                    <div class="res-stat-item">
                        <h4>المادة الاختيارية</h4>
                        <p id="result-elective" style="font-size: 0.9rem;">-</p>
                    </div>
                </div>

                <button type="button" class="btn-action-calc" onclick="scrollToMajors()">
                    <i class="fas fa-university"></i> عرض التخصصات المتاحة
                </button>
                <button type="button" class="btn-reset" onclick="resetCalculator()">إعادة ضبط وتصفير الحقول</button>
            </div>
        </div>

        <!-- University Recommendations Section -->
        <div class="majors-container" id="majors-section">
            <div class="majors-header">
                <div>
                    <h3><i class="fas fa-graduation-cap" style="color: var(--primary);"></i> دليل القبول والتخصصات المتاحة لمعدلك</h3>
                    <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: 4px;">بناءً على مفاتيح تنسيق الجامعات الفلسطينية (بيرزيت، النجاح، القدس، خضوري، البوليتكنك، العربية الأمريكية، غزة)</p>
                </div>
                <span class="majors-badge-count" id="majors-count">15 تخصص متاح</span>
            </div>

            <div class="majors-grid" id="majors-list">
                <!-- Dynamically Populated with Cards -->
            </div>
        </div>

    </div>

    <!-- JavaScript Calculation Engine -->
    <script>
        let currentBranch = 'scientific';

        const branchConfigs = {
            scientific: {
                title: 'علامات الفرع العلمي (المجموع الكلي: 700)',
                mandatory: [
                    { id: 'math', label: 'الرياضيات (علمي)', max: 200, icon: 'fa-square-root-variable', note: 'من 200 علامة' },
                    { id: 'physics', label: 'الفيزياء', max: 100, icon: 'fa-atom', note: 'من 100 علامة' },
                    { id: 'arabic', label: 'اللغة العربية', max: 100, icon: 'fa-feather', note: 'من 100 علامة' },
                    { id: 'english', label: 'اللغة الإنجليزية', max: 100, icon: 'fa-language', note: 'من 100 علامة' },
                    { id: 'islamic', label: 'التربية الإسلامية', max: 100, icon: 'fa-mosque', note: 'من 100 علامة' }
                ],
                electiveTitle: 'المواد الاختيارية (أدخل ما قدمته وسنحتسب المادة الأعلى فقط):',
                electives: [
                    { id: 'chemistry', label: 'الكيمياء', max: 100, icon: 'fa-flask' },
                    { id: 'biology', label: 'العلوم الحياتية (الأحياء)', max: 100, icon: 'fa-dna' },
                    { id: 'tech', label: 'التكنولوجيا', max: 100, icon: 'fa-laptop-code' }
                ]
            },
            literary: {
                title: 'علامات الفرع الأدبي (المجموع الكلي: 700)',
                mandatory: [
                    { id: 'arabic', label: 'اللغة العربية (أدبي)', max: 200, icon: 'fa-feather', note: 'من 200 علامة' },
                    { id: 'history', label: 'الدراسات التاريخية', max: 100, icon: 'fa-landmark', note: 'من 100 علامة' },
                    { id: 'geography', label: 'الدراسات الجغرافية', max: 100, icon: 'fa-earth-asia', note: 'من 100 علامة' },
                    { id: 'english', label: 'اللغة الإنجليزية', max: 100, icon: 'fa-language', note: 'من 100 علامة' },
                    { id: 'islamic', label: 'التربية الإسلامية', max: 100, icon: 'fa-mosque', note: 'من 100 علامة' }
                ],
                electiveTitle: 'المواد الاختيارية (يُحتسب الأعلى من بينها تلقائياً):',
                electives: [
                    { id: 'math', label: 'الرياضيات (أدبي)', max: 100, icon: 'fa-calculator' },
                    { id: 'sci_culture', label: 'الثقافة العلمية', max: 100, icon: 'fa-lightbulb' },
                    { id: 'tech', label: 'التكنولوجيا', max: 100, icon: 'fa-laptop-code' }
                ]
            },
            business: {
                title: 'علامات فرع الريادة والأعمال (المجموع الكلي: 700)',
                mandatory: [
                    { id: 'projects', label: 'المشاريع الريادية', max: 100, icon: 'fa-diagram-project', note: 'من 100 علامة' },
                    { id: 'accounting', label: 'المحاسبة', max: 100, icon: 'fa-coins', note: 'من 100 علامة' },
                    { id: 'arabic', label: 'اللغة العربية', max: 100, icon: 'fa-feather', note: 'من 100 علامة' },
                    { id: 'english', label: 'اللغة الإنجليزية', max: 100, icon: 'fa-language', note: 'من 100 علامة' },
                    { id: 'islamic', label: 'التربية الإسلامية', max: 100, icon: 'fa-mosque', note: 'من 100 علامة' }
                ],
                electiveTitle: 'المواد الاختيارية (يُحتسب الأعلى):',
                electives: [
                    { id: 'mgmt', label: 'الإدارة والاقتصاد', max: 100, icon: 'fa-chart-pie' },
                    { id: 'math', label: 'الرياضيات', max: 100, icon: 'fa-calculator' },
                    { id: 'tech', label: 'التكنولوجيا', max: 100, icon: 'fa-laptop-code' }
                ]
            }
        };

        const majorsDatabase = @json($universities ? (new \App\Http\Controllers\TawjihiCalculatorController())->getEligibleMajors('scientific', 100) : []);

        function switchBranch(branch) {
            currentBranch = branch;
            document.querySelectorAll('.branch-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`tab-${branch}`).classList.add('active');
            renderFormInputs();
            calculateScore();
        }

        function renderFormInputs() {
            const config = branchConfigs[currentBranch];
            document.getElementById('branch-title').textContent = config.title;
            const container = document.getElementById('branch-inputs-wrapper');

            let html = `
                <div class="form-section-title">
                    <i class="fas fa-lock" style="color: var(--primary);"></i>
                    <span>المواد الإجبارية</span>
                </div>
                <div class="inputs-grid">
            `;

            config.mandatory.forEach(sub => {
                html += `
                    <div class="input-group">
                        <label for="${sub.id}">${sub.label} <span>${sub.note || 'من ' + sub.max}</span></label>
                        <div class="input-control-wrapper">
                            <i class="fas ${sub.icon}"></i>
                            <input type="number" id="${sub.id}" class="score-input" min="0" max="${sub.max}"
                                   placeholder="العلامة من ${sub.max}" oninput="validateAndCalculate(this, ${sub.max})">
                        </div>
                    </div>
                `;
            });

            html += `</div>
                <div class="form-section-title" style="margin-top: 26px;">
                    <i class="fas fa-check-double" style="color: var(--emerald);"></i>
                    <span>${config.electiveTitle}</span>
                </div>
                <div class="inputs-grid">
            `;

            config.electives.forEach(sub => {
                html += `
                    <div class="input-group">
                        <label for="${sub.id}">${sub.label} <span>من ${sub.max}</span></label>
                        <div class="input-control-wrapper">
                            <i class="fas ${sub.icon}"></i>
                            <input type="number" id="${sub.id}" class="score-input" min="0" max="${sub.max}"
                                   placeholder="العلامة من ${sub.max}" oninput="validateAndCalculate(this, ${sub.max})">
                        </div>
                    </div>
                `;
            });

            html += `</div>`;
            container.innerHTML = html;
        }

        function validateAndCalculate(input, max) {
            let val = parseFloat(input.value);
            if (val > max) {
                input.value = max;
                input.classList.add('error');
            } else if (val < 0) {
                input.value = 0;
            } else {
                input.classList.remove('error');
            }
            calculateScore();
        }

        function calculateScore() {
            const config = branchConfigs[currentBranch];
            let total = 0;

            // حساب الإجباري
            config.mandatory.forEach(sub => {
                const el = document.getElementById(sub.id);
                if (el && el.value) {
                    total += parseFloat(el.value) || 0;
                }
            });

            // حساب أعلى اختياري
            let bestElectiveScore = 0;
            let bestElectiveName = 'لم تُحدد بعد';

            config.electives.forEach(sub => {
                const el = document.getElementById(sub.id);
                if (el && el.value) {
                    const score = parseFloat(el.value) || 0;
                    if (score > bestElectiveScore) {
                        bestElectiveScore = score;
                        bestElectiveName = sub.label + ` (${score})`;
                    }
                }
            });

            total += bestElectiveScore;
            const percentage = (total / 700) * 100;
            const formatted = percentage.toFixed(1);

            document.getElementById('result-percentage').textContent = formatted + '%';
            document.getElementById('result-total').textContent = `${Math.round(total)} / 700`;
            document.getElementById('result-elective').textContent = bestElectiveName;

            const statusEl = document.getElementById('result-status');
            if (percentage >= 90) {
                statusEl.textContent = 'امتياز وتفوق عالي! 🌟 مبارك مقدماً';
                statusEl.style.color = '#34d399';
            } else if (percentage >= 80) {
                statusEl.textContent = 'معدل جيد جداً مرتفع 🎓 فرص قبول واسعة';
                statusEl.style.color = '#60a5fa';
            } else if (percentage >= 70) {
                statusEl.textContent = 'معدل جيد ✨ يتيح لك العديد من الكليات';
                statusEl.style.color = '#fbbf24';
            } else if (percentage >= 50) {
                statusEl.textContent = 'ناجح ومؤهل للقبول الجامعي والدبلوم';
                statusEl.style.color = '#a78bfa';
            } else {
                statusEl.textContent = total > 0 ? 'في انتظار استكمال إدخال باقي المواد' : 'في انتظار إدخال العلامات...';
                statusEl.style.color = '#94a3b8';
            }

            renderEligibleMajors(percentage);
        }

        function renderEligibleMajors(percentage) {
            const listEl = document.getElementById('majors-list');
            const countEl = document.getElementById('majors-count');

            const filtered = majorsDatabase.filter(m => {
                return m.allowed_branches.includes(currentBranch) && (percentage >= (m.min_rate - 1.5) || percentage === 0);
            });

            countEl.textContent = `${filtered.length} تخصص متاح`;

            if (filtered.length === 0) {
                listEl.innerHTML = `
                    <div style="grid-column: span 2; text-align: center; padding: 40px; background: white; border-radius: 16px;">
                        <i class="fas fa-search" style="font-size: 2rem; color: var(--text-muted); margin-bottom: 10px;"></i>
                        <p style="color: var(--text-muted);">أدخل علاماتك للحصول على قائمة التخصصات المتوافقة بدقة مع معدلك.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            filtered.forEach(m => {
                const isGuaranteed = percentage >= m.min_rate;
                const statusClass = isGuaranteed ? 'rate-guaranteed' : 'rate-competitive';
                const statusText = isGuaranteed ? 'مضمون القبول ✅' : 'منافسة قوية / موازي ⚠️';

                html += `
                    <div class="major-card">
                        <div>
                            <div class="major-card-top">
                                <div class="major-icon-box"><i class="${m.icon || 'fas fa-graduation-cap'}"></i></div>
                                <span class="major-rate-badge ${statusClass}">${statusText} (الحد الأدنى ${m.min_rate}%)</span>
                            </div>
                            <h4>${m.title}</h4>
                            <div class="major-category">${m.category}</div>
                        </div>
                        <div>
                            <div class="major-universities-list">
                                ${m.universities.map(u => `<span class="uni-tag">${u}</span>`).join('')}
                            </div>
                        </div>
                    </div>
                `;
            });

            listEl.innerHTML = html;
        }

        function resetCalculator() {
            document.querySelectorAll('.score-input').forEach(i => i.value = '');
            calculateScore();
        }

        function scrollToMajors() {
            document.getElementById('majors-section').scrollIntoView({ behavior: 'smooth' });
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            renderFormInputs();
            renderEligibleMajors(0);
        });
    </script>
</body>
</html>
