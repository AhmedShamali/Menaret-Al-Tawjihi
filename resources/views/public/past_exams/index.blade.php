<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أرشيف الامتحانات الوزارية ونماذج الإجابات الرسمية | {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</title>

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

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Alexandria', sans-serif; }
        body { background-color: var(--bg-main); color: var(--text-body); line-height: 1.7; min-height: 100vh; }

        nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px 8%; background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px); border-bottom: 1px solid var(--border-card);
            position: sticky; top: 0; z-index: 50;
        }
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; font-weight: 800; font-size: 1.25rem; color: var(--text-title); }
        .logo-badge {
            width: 42px; height: 42px; border-radius: 12px;
            background: linear-gradient(135deg, #2563eb, #6366f1);
            color: white; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        .nav-actions { display: flex; align-items: center; gap: 12px; }
        .btn-nav { text-decoration: none; padding: 8px 18px; border-radius: 10px; font-size: 0.9rem; font-weight: 600; color: var(--text-muted); transition: 0.2s ease; }
        .btn-nav:hover { color: var(--primary); background: var(--primary-light); }

        .hero-banner {
            text-align: center; padding: 50px 20px 30px; max-width: 900px; margin: 0 auto;
        }
        .badge-pill {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 16px; background: #ecfdf5; border: 1px solid #a7f3d0;
            color: var(--emerald); border-radius: 50px; font-size: 0.85rem;
            font-weight: 600; margin-bottom: 16px;
        }
        .badge-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--emerald); }
        .hero-banner h1 { font-size: 2.2rem; font-weight: 800; color: var(--text-title); margin-bottom: 12px; }
        .hero-banner p { color: var(--text-muted); font-size: 1.05rem; }

        /* Filter Box */
        .filter-container {
            max-width: 1200px; margin: 0 auto 40px; padding: 24px;
            background: white; border: 1px solid var(--border-card);
            border-radius: 20px; box-shadow: var(--shadow-sm);
        }
        .filter-form {
            display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 16px; align-items: end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 6px; }
        .filter-group label { font-size: 0.85rem; font-weight: 700; color: var(--text-title); }
        .filter-control {
            width: 100%; padding: 10px 14px; border: 1.5px solid var(--border-card);
            border-radius: 10px; font-size: 0.9rem; font-family: inherit;
            color: var(--text-body); background: #f8fafc; outline: none; transition: 0.2s;
        }
        .filter-control:focus { border-color: var(--primary); background: white; }
        .btn-filter-submit {
            padding: 11px 24px; border: none; border-radius: 10px;
            background: var(--primary); color: white; font-weight: 700;
            cursor: pointer; transition: 0.2s ease; display: flex; align-items: center; gap: 8px;
        }
        .btn-filter-submit:hover { background: var(--primary-dark); }

        /* Grid Cards */
        .exams-grid {
            max-width: 1200px; margin: 0 auto 60px; padding: 0 20px;
            display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 24px;
        }
        .exam-card {
            background: white; border: 1px solid var(--border-card);
            border-radius: 20px; padding: 24px; box-shadow: var(--shadow-sm);
            transition: 0.3s ease; display: flex; flex-direction: column; justify-content: space-between;
        }
        .exam-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); border-color: #93c5fd; }
        .exam-card-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px; }
        .exam-badges { display: flex; flex-wrap: wrap; gap: 6px; }
        .exam-badge {
            font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;
        }
        .badge-year { background: #dbeafe; color: #1e40af; }
        .badge-branch { background: #fef3c7; color: #92400e; }
        .badge-session { background: #f1f5f9; color: #475569; }

        .exam-card h3 { font-size: 1.2rem; font-weight: 800; color: var(--text-title); margin-bottom: 8px; }
        .exam-card p { font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px; }

        .exam-actions-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
            padding-top: 16px; border-top: 1px solid #f1f5f9;
        }
        .btn-download-paper {
            padding: 10px 12px; border-radius: 10px; text-decoration: none;
            font-size: 0.85rem; font-weight: 700; text-align: center;
            background: var(--primary-light); color: var(--primary);
            display: flex; align-items: center; justify-content: center; gap: 6px;
            transition: 0.2s; border: 1px solid #bfdbfe;
        }
        .btn-download-paper:hover { background: var(--primary); color: white; }

        .btn-download-key {
            padding: 10px 12px; border-radius: 10px; text-decoration: none;
            font-size: 0.85rem; font-weight: 700; text-align: center;
            background: #ecfdf5; color: #059669;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            transition: 0.2s; border: 1px solid #a7f3d0;
        }
        .btn-download-key:hover { background: #059669; color: white; }

        .empty-state {
            grid-column: 1 / -1; text-align: center; padding: 60px 20px;
            background: white; border-radius: 20px; border: 1px solid var(--border-card);
        }
        .empty-state i { font-size: 3rem; color: var(--text-muted); margin-bottom: 16px; }

        @media (max-width: 850px) {
            .filter-form { grid-template-columns: 1fr; }
            .btn-filter-submit { width: 100%; justify-content: center; }
            .exams-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <nav>
        <a href="/" class="nav-logo">
            <div class="logo-badge">🇵🇸</div>
            <span>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</span>
        </a>
        <div class="nav-actions">
            <a href="/" class="btn-nav"><i class="fas fa-home"></i> الرئيسية</a>
            <a href="{{ route('tawjihi.calculator') }}" class="btn-nav"><i class="fas fa-calculator"></i> حاسبة التوجيهي</a>
            <a href="{{ route('login') }}" class="btn-nav" style="background: var(--primary); color: white;"><i class="fas fa-sign-in-alt"></i> دخول</a>
        </div>
    </nav>

    <!-- Header Banner -->
    <div class="hero-banner">
        <div class="badge-pill">
            <span class="badge-dot"></span>
            <span>أرشيف امتحانات الثانوية العامة في فلسطين (إنجاز / توجيهي)</span>
        </div>
        <h1>بنك الامتحانات الوزارية ونماذج الإجابة المعتمدة</h1>
        <p>تصفح وحمّل أوراق الامتحانات الوزارية لجميع السنوات السابقة مع نماذج الحل وسلالم توزيع الدرجات الرسمية للتدرب عليها.</p>
    </div>

    <!-- Filter Form Container -->
    <div class="filter-container">
        <form action="{{ route('tawjihi.archive') }}" method="GET" class="filter-form">
            <div class="filter-group">
                <label for="search">بحث باسم المادة أو الكلمة الدلالية</label>
                <input type="text" name="search" id="search" class="filter-control" placeholder="مثال: رياضيات، فيزياء، لغة عربية..." value="{{ request('search') }}">
            </div>

            <div class="filter-group">
                <label for="branch">الفرع الدراسي</label>
                <select name="branch" id="branch" class="filter-control">
                    <option value="all">كافة الفروع</option>
                    <option value="scientific" {{ request('branch') == 'scientific' ? 'selected' : '' }}>الفرع العلمي</option>
                    <option value="literary" {{ request('branch') == 'literary' ? 'selected' : '' }}>الفرع الأدبي</option>
                    <option value="business" {{ request('branch') == 'business' ? 'selected' : '' }}>الريادة والأعمال</option>
                    <option value="industrial" {{ request('branch') == 'industrial' ? 'selected' : '' }}>الفرع الصناعي</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="year">سنة الامتحان</label>
                <select name="year" id="year" class="filter-control">
                    <option value="">جميع السنوات</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="session">الدورة الوزارية</label>
                <select name="session" id="session" class="filter-control">
                    <option value="">كافة الدورات</option>
                    <option value="first" {{ request('session') == 'first' ? 'selected' : '' }}>الدورة الأولى (يونيو)</option>
                    <option value="second" {{ request('session') == 'second' ? 'selected' : '' }}>الدورة الثانية (أغسطس)</option>
                    <option value="completion" {{ request('session') == 'completion' ? 'selected' : '' }}>الاستكمالية</option>
                </select>
            </div>

            <button type="submit" class="btn-filter-submit">
                <i class="fas fa-filter"></i> تصفية
            </button>
        </form>
    </div>

    <!-- Exams Cards Grid -->
    <div class="exams-grid">
        @forelse($exams as $exam)
            <div class="exam-card">
                <div>
                    <div class="exam-card-header">
                        <div class="exam-badges">
                            <span class="exam-badge badge-year">{{ $exam->year }} م</span>
                            <span class="exam-badge badge-branch">{{ $exam->branch_label }}</span>
                            <span class="exam-badge badge-session">{{ $exam->session_label }}</span>
                        </div>
                    </div>
                    <h3>امتحان {{ $exam->subject_name }}</h3>
                    <p>{{ $exam->notes ?? 'امتحان شهادة الدراسة الثانوية العامة الرسمية الصادر عن وزارة التربية والتعليم الفلسطينية.' }}</p>
                </div>

                <div class="exam-actions-grid">
                    <a href="{{ route('tawjihi.download.paper', $exam->id) }}" target="_blank" class="btn-download-paper">
                        <i class="fas fa-file-pdf"></i> ورقة الأسئلة
                    </a>
                    <a href="{{ route('tawjihi.download.key', $exam->id) }}" target="_blank" class="btn-download-key">
                        <i class="fas fa-check-circle"></i> نموذج الإجابة
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3 style="color: var(--text-title); margin-bottom: 8px;">لا توجد امتحانات مطابقة للبحث حالياً</h3>
                <p style="color: var(--text-muted);">جرب تغيير خيارات التصفية أو البحث عن مادة أخرى.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="max-width: 1200px; margin: 0 auto 60px; padding: 0 20px;">
        {{ $exams->links() }}
    </div>

</body>
</html>
