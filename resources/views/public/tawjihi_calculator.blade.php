<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('حاسبة معدل التوجيهي ودليل التنسيق الجامعي') }} | {{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }}</title>

    @if(\App\Models\Setting::get('site_favicon'))
        <link rel="icon" href="{{ asset(\App\Models\Setting::get('site_favicon')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @endif

    <!-- الخطوط الموحدة للمنظومة (Tajawal & Alexandria) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800;900&family=Alexandria:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-page: #f8fafc;
            --bg-surface: #ffffff;
            --border-subtle: #e2e8f0;
            --border-focus: #1d4ed8;
            --primary: #1d4ed8;
            --primary-hover: #1e40af;
            --primary-soft: #eff6ff;
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --success: #16a34a;
            --success-soft: #ecfdf5;
            --warning: #d97706;
            --warning-soft: #fffbeb;
            --radius-md: 8px;
            --radius-lg: 12px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.06);
            --transition: all 0.2s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', 'Alexandria', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-body);
            font-size: 13.5px;
            line-height: 1.6;
            min-height: 100vh;
        }

        html[dir="rtl"] body { direction: rtl; text-align: right; }
        html[dir="ltr"] body { direction: ltr; text-align: left; }

        /* Top Navigation - فاتح وأنيق */
        .main-navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-subtle);
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text-heading);
            font-weight: 800;
            font-size: 16px;
        }

        .brand-logo-badge {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            background: var(--primary-soft);
            color: var(--primary);
            border: 1px solid #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link-btn {
            text-decoration: none;
            padding: 6px 14px;
            border-radius: var(--radius-md);
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-body);
            border: 1px solid var(--border-subtle);
            background: #ffffff;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link-btn:hover {
            background: var(--bg-page);
            color: var(--primary);
            border-color: #cbd5e1;
        }

        .nav-link-btn.primary {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
            font-weight: 700;
        }

        .nav-link-btn.primary:hover {
            background: var(--primary-hover);
        }

        /* Main Container */
        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 24px 60px;
        }

        /* Header Introduction */
        .calc-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .badge-academic {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: var(--primary-soft);
            color: var(--primary);
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .calc-header h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 6px;
        }

        .calc-header p {
            color: var(--text-muted);
            font-size: 13px;
            max-width: 650px;
            margin: 0 auto;
        }

        /* Branch Selector Tabs */
        .branch-nav {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin: 20px auto 26px;
            background: #ffffff;
            padding: 4px;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            max-width: 520px;
            box-shadow: var(--shadow-sm);
        }

        .branch-btn {
            flex: 1;
            padding: 8px 14px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 700;
            border-radius: var(--radius-md);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: var(--transition);
        }

        .branch-btn:hover {
            color: var(--primary);
            background: var(--primary-soft);
        }

        .branch-btn.active {
            background: var(--primary);
            color: #ffffff;
        }

        /* Calculator Grid */
        .calculator-grid {
            display: grid;
            grid-template-columns: 1.4fr 0.9fr;
            gap: 20px;
            align-items: start;
        }

        .card-panel {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 22px;
            box-shadow: var(--shadow-sm);
        }

        .panel-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .panel-title i {
            color: var(--primary);
            font-size: 18px;
        }

        .panel-title h2 {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-heading);
            margin: 0;
        }

        .section-label {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .inputs-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .input-box {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .input-box label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-body);
            display: flex;
            justify-content: space-between;
        }

        .max-tag {
            font-size: 11px;
            color: var(--primary);
            background: var(--primary-soft);
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 600;
        }

        .field-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .field-wrapper i {
            position: absolute;
            color: var(--text-muted);
            font-size: 12px;
            pointer-events: none;
        }
        html[dir="rtl"] .field-wrapper i { right: 12px; }
        html[dir="ltr"] .field-wrapper i { left: 12px; }

        .field-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            font-size: 13px;
            color: var(--text-heading);
            background: #ffffff;
            outline: none;
            transition: var(--transition);
        }
        html[dir="rtl"] .field-input { padding-right: 34px; }
        html[dir="ltr"] .field-input { padding-left: 34px; }

        .field-input:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }

        .field-input.input-error {
            border-color: #ef4444;
            background: #fff5f5;
        }

        .info-card {
            background: var(--bg-page);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 12px 14px;
            margin-top: 18px;
            display: flex;
            gap: 10px;
            font-size: 12px;
            color: var(--text-body);
            line-height: 1.55;
        }
        html[dir="rtl"] .info-card { border-right: 3px solid var(--primary); }
        html[dir="ltr"] .info-card { border-left: 3px solid var(--primary); }

        .info-card i {
            color: var(--primary);
            font-size: 14px;
            margin-top: 2px;
        }

        /* Result Panel Sticky Card */
        .result-box {
            position: sticky;
            top: 70px;
        }

        .result-badge {
            display: inline-block;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .score-display {
            font-size: 2.8rem;
            font-weight: 900;
            color: var(--text-heading);
            line-height: 1;
            margin: 4px 0 8px;
            letter-spacing: -1px;
            font-family: monospace;
        }

        .score-status {
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--text-muted);
        }

        .stats-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 12px 0;
            border-top: 1px solid var(--border-subtle);
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 16px;
        }

        .stat-cell h4 {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 3px;
        }

        .stat-cell p {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-heading);
            font-family: monospace;
        }

        .btn-view-majors {
            width: 100%;
            background: var(--primary);
            color: #ffffff;
            border: none;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: var(--transition);
            margin-bottom: 8px;
        }

        .btn-view-majors:hover {
            background: var(--primary-hover);
        }

        .btn-clear {
            width: 100%;
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-subtle);
            padding: 8px;
            border-radius: var(--radius-md);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-clear:hover {
            background: var(--bg-page);
            color: var(--text-heading);
        }

        /* Majors Section */
        .majors-panel {
            margin-top: 28px;
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 22px;
            box-shadow: var(--shadow-sm);
        }

        .majors-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-subtle);
            flex-wrap: wrap;
            gap: 10px;
        }

        .majors-panel-header h3 {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .count-pill {
            background: var(--primary-soft);
            color: var(--primary);
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            border: 1px solid #bfdbfe;
        }

        .majors-list-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 12px;
        }

        .major-item-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 14px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: var(--transition);
        }

        .major-item-card:hover {
            border-color: #cbd5e1;
            box-shadow: var(--shadow-sm);
        }

        .major-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .major-badge-status {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
        }

        .major-badge-status.guaranteed {
            background: var(--success-soft);
            color: var(--success);
            border: 1px solid #bbf7d0;
        }

        .major-badge-status.competitive {
            background: var(--warning-soft);
            color: var(--warning);
            border: 1px solid #fde68a;
        }

        .major-item-card h4 {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 2px;
        }

        .major-category {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .unis-row {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .uni-chip {
            background: var(--bg-page);
            color: var(--text-body);
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid var(--border-subtle);
        }

        @media (max-width: 900px) {
            .calculator-grid { grid-template-columns: 1fr; }
            .result-box { position: static; }
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <header class="main-navbar">
        <div class="nav-inner">
            <a href="/" class="nav-brand">
                <div class="brand-logo-badge">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span>{{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }} 🇵🇸</span>
            </a>
            <div class="nav-links">
                <!-- زر تبديل اللغة خالي من أي كلمة عربية في وضع الإنجليزية -->
                @php $currentLocale = app()->getLocale(); @endphp
                <a href="{{ route('lang.switch', $currentLocale === 'ar' ? 'en' : 'ar') }}" 
                   class="nav-link-btn"
                   title="{{ $currentLocale === 'ar' ? 'Switch to English' : 'Switch to Arabic' }}">
                    <i class="fa-solid fa-globe" style="color: var(--primary);"></i>
                    <span>{{ $currentLocale === 'ar' ? 'EN' : 'AR' }}</span>
                </a>

                <a href="/" class="nav-link-btn"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> {{ __('الرئيسية') }}</a>
                <a href="{{ route('login') }}" class="nav-link-btn primary"><i class="fas fa-arrow-right-to-bracket"></i> {{ __('تسجيل الدخول') }}</a>
            </div>
        </div>
    </header>

    <!-- Page Content Container -->
    <main class="page-container">

        <!-- Header Introduction -->
        <div class="calc-header">
            <div class="badge-academic">
                <i class="fas fa-check-circle"></i>
                <span>{{ __('المنهاج الفلسطيني الرسمي المعتمد') }} - {{ __('دورة') }} {{ \App\Models\Setting::tawjihiSession() }} ({{ \App\Models\Setting::academicYear() }})</span>
            </div>
            <h1>{{ __('حاسبة معدل التوجيهي ودليل التنسيق') }}</h1>
            <p>{{ __('احتساب دقيق لمعدل الثانوية العامة وفق السلم الوزاري الفلسطيني (أعلى مادة اختيارية تلقائياً)، مع استعراض التخصصات المتاحة فورياً.') }}</p>

            <!-- Branch Nav Selector -->
            <div class="branch-nav">
                <button type="button" class="branch-btn active" id="btn-scientific" onclick="changeBranch('scientific')">
                    <i class="fas fa-atom"></i> {{ __('الفرع العلمي') }}
                </button>
                <button type="button" class="branch-btn" id="btn-literary" onclick="changeBranch('literary')">
                    <i class="fas fa-book-open"></i> {{ __('الفرع الأدبي') }}
                </button>
                <button type="button" class="branch-btn" id="btn-business" onclick="changeBranch('business')">
                    <i class="fas fa-briefcase"></i> {{ __('فرع الريادة والأعمال') }}
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
                        <h2 id="panel-branch-title">{{ __('علامات الفرع العلمي (المجموع الكلي: 700)') }}</h2>
                    </div>
                </div>

                <form id="calculator-form" onsubmit="event.preventDefault(); calculate();">
                    <div id="dynamic-inputs-container">
                        <!-- Rendered via Javascript -->
                    </div>

                    <div class="info-card">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>{{ __('معايير الحساب الرسمية:') }}</strong>
                            {{ __('يتم احتساب المواد الإجبارية بالكامل، بينما تُقارن علامات المواد الاختيارية وتُحتسب أعلى مادة اختيارية فقط لمنح الطالب أفضل مجموع وزاري ممكن من 700.') }}
                        </div>
                    </div>
                </form>
            </div>

            <!-- Result Summary Card -->
            <div class="card-panel result-box">
                <span class="result-badge"><i class="fas fa-chart-line"></i> {{ __('النتيجة التقديرية') }}</span>
                <div class="score-display" id="display-percentage">0.0%</div>
                <div class="score-status" id="display-status">{{ __('في انتظار إدخال العلامات...') }}</div>

                <div class="stats-summary">
                    <div class="stat-cell">
                        <h4>{{ __('المجموع الكلي') }}</h4>
                        <p id="display-total">0 / 700</p>
                    </div>
                    <div class="stat-cell">
                        <h4>{{ __('المادة الاختيارية') }}</h4>
                        <p id="display-elective" style="font-size: 0.85rem;">-</p>
                    </div>
                </div>

                <button type="button" class="btn-view-majors" onclick="scrollToMajors()">
                    <i class="fas fa-university"></i> {{ __('عرض التخصصات المتوافقة') }}
                </button>
                <button type="button" class="btn-clear" onclick="clearInputs()">
                    <i class="fas fa-redo-alt"></i> {{ __('إعادة ضبط الحقول') }}
                </button>
            </div>

        </div>

        <!-- Majors Section -->
        <section class="majors-panel" id="majors-section">
            <div class="majors-panel-header">
                <div>
                    <h3><i class="fas fa-compass" style="color: var(--primary);"></i> {{ __('التخصصات والكليات المتاحة في الجامعات الفلسطينية') }}</h3>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">{{ __('مرتبة وفق الحد الأدنى لمفاتيح التنسيق المعتمدة في جامعات الوطن (بيرزيت، النجاح، القدس، خضوري، البوليتكنك، العربية الأمريكية، غزة)') }}</p>
                </div>
                <span class="count-pill" id="majors-count-badge">0 {{ __('تخصص متاح') }}</span>
            </div>

            <div class="majors-list-grid" id="majors-list-container">
                <!-- Dynamically Populated -->
            </div>
        </section>

    </main>

    <!-- Interactive Script -->
    <script>
        const isEn = {{ app()->getLocale() === 'en' ? 'true' : 'false' }};
        let currentBranch = 'scientific';

        const calcI18n = {
            mandatoryLabel: "{{ __('المواد الإجبارية الأساسية') }}",
            scorePlaceholder: "{{ __('أدخل علامة المادة') }}",
            outOf: isEn ? "out of " : "من ",
            passLabel: isEn ? "Pass: " : "النجاح: ",
            notDetermined: "{{ __('لم تُحدد') }}",
            statusExcellent: "{{ __('امتياز وتفوق عالي 🌟 فرص قبول لكافة الكليات') }}",
            statusVeryGood: "{{ __('جيد جداً مرتفع 🎓 فرص ممتازة في التخصصات الهندسية والصحية') }}",
            statusGood: "{{ __('جيد ✨ خيارات واسعة في تكنولوجيا المعلومات والعلوم الإدارية') }}",
            statusPass: "{{ __('ناجح ومؤهل للالتحاق بالبرامج الجامعية والدبلوم') }}",
            statusWaitingRest: "{{ __('في انتظار استكمال إدخال باقي المواد...') }}",
            statusWaitingStart: "{{ __('في انتظار إدخال العلامات...') }}",
            majorsUnit: "{{ __('تخصص متاح') }}",
            noMajorsHint: "{{ __('أدخل علاماتك للاطلاع على التخصصات المتوافقة مع معدلك المحسوب.') }}",
            guaranteed: "{{ __('مضمون القبول') }}",
            competitive: "{{ __('منافسة قوية / موازي') }}"
        };

        const configs = {
            scientific: {
                title: "{{ __('علامات الفرع العلمي (المجموع الكلي: 700)') }}",
                mandatory: [
                    { id: 'math', label: "{{ __('الرياضيات (علمي)') }}", max: 200, pass: 100, icon: 'fa-square-root-variable', note: calcI18n.outOf + '200 (' + calcI18n.passLabel + '100)' },
                    { id: 'physics', label: "{{ __('الفيزياء') }}", max: 100, pass: 50, icon: 'fa-atom', note: calcI18n.outOf + '100 (' + calcI18n.passLabel + '50)' },
                    { id: 'arabic', label: "{{ __('اللغة العربية') }}", max: 100, pass: 50, icon: 'fa-feather-pointed', note: calcI18n.outOf + '100 (' + calcI18n.passLabel + '50)' },
                    { id: 'english', label: "{{ __('اللغة الإنجليزية') }}", max: 100, pass: 50, icon: 'fa-language', note: calcI18n.outOf + '100 (' + calcI18n.passLabel + '50)' },
                    { id: 'islamic', label: "{{ __('التربية الإسلامية') }}", max: 100, pass: 50, icon: 'fa-moon', note: calcI18n.outOf + '100 (' + calcI18n.passLabel + '50)' }
                ],
                electiveTitle: "{{ __('المواد الاختيارية (أدخل العلامات وسيُحتسب الأعلى تلقائياً):') }}",
                electives: [
                    { id: 'chemistry', label: "{{ __('الكيمياء') }}", max: 100, pass: 50, icon: 'fa-flask' },
                    { id: 'biology', label: "{{ __('العلوم الحياتية (الأحياء)') }}", max: 100, pass: 50, icon: 'fa-dna' },
                    { id: 'tech', label: "{{ __('التكنولوجيا') }}", max: 100, pass: 50, icon: 'fa-laptop-code' }
                ]
            },
            literary: {
                title: "{{ __('علامات الفرع الأدبي (المجموع الكلي: 700)') }}",
                mandatory: [
                    { id: 'arabic', label: "{{ __('اللغة العربية') }}", max: 150, pass: 75, icon: 'fa-feather-pointed', note: calcI18n.outOf + '150 (' + calcI18n.passLabel + '75)' },
                    { id: 'english', label: "{{ __('اللغة الإنجليزية') }}", max: 150, pass: 75, icon: 'fa-language', note: calcI18n.outOf + '150 (' + calcI18n.passLabel + '75)' },
                    { id: 'history', label: "{{ __('الدراسات التاريخية') }}", max: 100, pass: 50, icon: 'fa-landmark', note: calcI18n.outOf + '100 (' + calcI18n.passLabel + '50)' },
                    { id: 'geography', label: "{{ __('الدراسات الجغرافية') }}", max: 100, pass: 50, icon: 'fa-earth-americas', note: calcI18n.outOf + '100 (' + calcI18n.passLabel + '50)' },
                    { id: 'islamic', label: "{{ __('التربية الإسلامية') }}", max: 100, pass: 50, icon: 'fa-moon', note: calcI18n.outOf + '100 (' + calcI18n.passLabel + '50)' }
                ],
                electiveTitle: "{{ __('المواد الاختيارية (يُحتسب المبحث الأعلى فقط من 100):') }}",
                electives: [
                    { id: 'math', label: "{{ __('الرياضيات (أدبي)') }}", max: 100, pass: 50, icon: 'fa-calculator' },
                    { id: 'sci_culture', label: "{{ __('الثقافة العلمية') }}", max: 100, pass: 50, icon: 'fa-lightbulb' },
                    { id: 'tech', label: "{{ __('التكنولوجيا') }}", max: 100, pass: 50, icon: 'fa-laptop-code' }
                ]
            },
            business: {
                title: "{{ __('علامات فرع الريادة والأعمال (المجموع الكلي: 700)') }}",
                mandatory: [
                    { id: 'projects', label: "{{ __('المشاريع الريادية') }}", max: 100, pass: 50, icon: 'fa-chart-pie', note: calcI18n.outOf + '100' },
                    { id: 'accounting', label: "{{ __('المحاسبة') }}", max: 100, pass: 50, icon: 'fa-coins', note: calcI18n.outOf + '100' },
                    { id: 'mgmt', label: "{{ __('الإدارة والاقتصاد') }}", max: 100, pass: 50, icon: 'fa-briefcase', note: calcI18n.outOf + '100' },
                    { id: 'arabic', label: "{{ __('اللغة العربية') }}", max: 100, pass: 50, icon: 'fa-feather-pointed', note: calcI18n.outOf + '100' },
                    { id: 'english', label: "{{ __('اللغة الإنجليزية') }}", max: 100, pass: 50, icon: 'fa-language', note: calcI18n.outOf + '100' },
                    { id: 'islamic', label: "{{ __('التربية الإسلامية') }}", max: 100, pass: 50, icon: 'fa-moon', note: calcI18n.outOf + '100' }
                ],
                electiveTitle: "{{ __('المواد الاختيارية (يُحتسب المبحث الأعلى فقط من 100):') }}",
                electives: [
                    { id: 'math', label: "{{ __('رياضيات الأعمال') }}", max: 100, pass: 50, icon: 'fa-calculator' },
                    { id: 'tech', label: "{{ __('التكنولوجيا') }}", max: 100, pass: 50, icon: 'fa-laptop-code' }
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
                <div class="section-label"><i class="fas fa-shield-halved" style="color: var(--primary);"></i> ${calcI18n.mandatoryLabel}</div>
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
                                   placeholder="${calcI18n.scorePlaceholder}" oninput="handleInput(this, ${sub.max})">
                        </div>
                    </div>
                `;
            });

            html += `
                </div>
                <div class="section-label" style="margin-top: 18px;"><i class="fas fa-star" style="color: var(--warning);"></i> ${conf.electiveTitle}</div>
                <div class="inputs-row">
            `;

            conf.electives.forEach(sub => {
                html += `
                    <div class="input-box">
                        <label for="${sub.id}">
                            <span>${sub.label}</span>
                            <span class="max-tag">${calcI18n.outOf}${sub.max}</span>
                        </label>
                        <div class="field-wrapper">
                            <i class="fas ${sub.icon}"></i>
                            <input type="number" id="${sub.id}" class="field-input" min="0" max="${sub.max}" step="0.5"
                                   placeholder="${calcI18n.scorePlaceholder}" oninput="handleInput(this, ${sub.max})">
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
            let bestName = calcI18n.notDetermined;

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
                statusEl.textContent = calcI18n.statusExcellent;
                statusEl.style.color = '#16a34a';
            } else if (percentage >= 80) {
                statusEl.textContent = calcI18n.statusVeryGood;
                statusEl.style.color = '#1d4ed8';
            } else if (percentage >= 70) {
                statusEl.textContent = calcI18n.statusGood;
                statusEl.style.color = '#d97706';
            } else if (percentage >= 50) {
                statusEl.textContent = calcI18n.statusPass;
                statusEl.style.color = '#475569';
            } else {
                statusEl.textContent = enteredAny ? calcI18n.statusWaitingRest : calcI18n.statusWaitingStart;
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

            countBadge.textContent = `${filtered.length} ${calcI18n.majorsUnit}`;

            if (filtered.length === 0) {
                listEl.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 30px; color: var(--text-muted);">
                        <i class="fas fa-search" style="font-size: 1.8rem; margin-bottom: 8px; display: block;"></i>
                        ${calcI18n.noMajorsHint}
                    </div>
                `;
                return;
            }

            let html = '';
            filtered.forEach(m => {
                const isGuaranteed = percentage >= m.min_rate;
                const statusClass = isGuaranteed ? 'guaranteed' : 'competitive';
                const statusText = isGuaranteed ? calcI18n.guaranteed : calcI18n.competitive;

                html += `
                    <div class="major-item-card">
                        <div>
                            <div class="major-top">
                                <span class="major-badge-status ${statusClass}">${statusText} (${m.min_rate}%)</span>
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
