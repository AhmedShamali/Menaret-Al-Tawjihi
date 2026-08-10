@extends('layouts.app')

@section('title', 'تسليمات الطلاب')

@section('content')
<div class="luxury-submissions-wrapper">

    <!-- هيدر سينمائي تدرجي -->
    <div class="hero-banner">
        <div class="banner-content">
            <div class="tag-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> لوحة التصحيح الذكية</div>
            <h1 class="hero-title">تسليمات الطلاب والتقييمات</h1>
            <p class="hero-sub">استعرض الإجابات، قارن الأداء، وقم برصد الدرجات بلمسة واحدة.</p>
        </div>
        <div class="banner-glow"></div>
    </div>

    @php
        $totalSubmissions = isset($submissions) ? $submissions->count() : 0;
        $gradedCount = isset($submissions) ? $submissions->where('status', 'graded')->count() : 0;
        $pendingCount = $totalSubmissions - $gradedCount;
    @endphp

    <!-- مؤشرات الأداء المتطورة -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon total-icon"><i class="fa-solid fa-layer-group"></i></div>
            <div class="metric-body">
                <span class="metric-label">إجمالي التسليمات</span>
                <span class="metric-num">{{ $totalSubmissions }}</span>
            </div>
            <div class="metric-bar-bg"><div class="metric-bar-fill" style="width: 100%;"></div></div>
        </div>

        <div class="metric-card">
            <div class="metric-icon pending-icon"><i class="fa-solid fa-hourglass-start"></i></div>
            <div class="metric-body">
                <span class="metric-label">بانتظار التقييم</span>
                <span class="metric-num">{{ $pendingCount }}</span>
            </div>
            <div class="metric-bar-bg"><div class="metric-bar-fill pending-fill" style="width: {{ $totalSubmissions > 0 ? ($pendingCount / $totalSubmissions) * 100 : 0 }}%;"></div></div>
        </div>

        <div class="metric-card">
            <div class="metric-icon success-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div class="metric-body">
                <span class="metric-label">تم تصحيحها</span>
                <span class="metric-num">{{ $gradedCount }}</span>
            </div>
            <div class="metric-bar-bg"><div class="metric-bar-fill success-fill" style="width: {{ $totalSubmissions > 0 ? ($gradedCount / $totalSubmissions) * 100 : 0 }}%;"></div></div>
        </div>
    </div>

    <!-- شريط البحث السريع والفلترة -->
    <div class="control-panel">
        <div class="search-field-wrap">
            <i class="fa-solid fa-magnifying-glass search-ico"></i>
            <input type="text" id="submissionsSearch" placeholder="ابحث باسم الطالب، البريد، أو عنوان الاختبار..." onkeyup="liveSearch()">
        </div>

        <div class="pills-filter">
            <button class="pill-btn active" onclick="filterCards('all', this)">
                <span>الكل</span>
                <span class="pill-count">{{ $totalSubmissions }}</span>
            </button>
            <button class="pill-btn" onclick="filterCards('pending', this)">
                <span>قيد المراجعة</span>
                <span class="pill-count pending-c">{{ $pendingCount }}</span>
            </button>
            <button class="pill-btn" onclick="filterCards('graded', this)">
                <span>تم التصحيح</span>
                <span class="pill-count graded-c">{{ $gradedCount }}</span>
            </button>
        </div>
    </div>

    <!-- بطاقات التسليمات الاحترافية -->
    @if(isset($submissions) && $submissions->count() > 0)
        <div class="cards-layout" id="cardsContainer">
            @foreach($submissions as $s)
                @php
                    $studentName = $s->student->name_ar ?? $s->student->name ?? 'طالب';
                    $examTitle = $s->exam->title ?? 'اختبار بدون عنوان';

                    // الاعتماد الحصري والكامل على مادة المعلم المسجل بها
                    $teacherSubject = auth()->user()->subject;
                    $subjectName = $teacherSubject?->name_ar ?? $teacherSubject?->name ?? 'مادة عامة';
                    $subjectIcon = $teacherSubject?->icon ?? '📚';

                    $totalPoints = $s->exam->questions_sum_points ?? ($s->exam->questions ? $s->exam->questions->sum('points') : 0);
                    $percentage = ($totalPoints > 0 && $s->status == 'graded') ? round(($s->total_earned_grade / $totalPoints) * 100) : 0;
                @endphp

                <div class="submission-item-card" data-status="{{ $s->status }}" data-search="{{ strtolower($studentName . ' ' . $s->student->email . ' ' . $examTitle . ' ' . $subjectName) }}">
                    <div class="item-header">
                        <div class="user-avatar-badge">
                            {{ mb_substr($studentName, 0, 1) }}
                        </div>
                        <div class="user-meta">
                            <h4 class="user-name">{{ $studentName }}</h4>
                            <span class="user-email">{{ $s->student->email ?? 'لا يوجد بريد' }}</span>
                        </div>
                        <div class="status-indicator {{ $s->status == 'graded' ? 'status-graded' : 'status-pending' }}">
                            @if($s->status == 'graded')
                                <i class="fa-solid fa-check"></i> تم
                            @else
                                <i class="fa-solid fa-clock"></i> معلق
                            @endif
                        </div>
                    </div>

                    <div class="item-body">
                        <!-- عرض مادة المعلم المباشرة بشكل صريح -->
                        <div class="exam-tag">
                            <span class="subject-badge-pill">
                                {{ $subjectIcon }} {{ $subjectName }}
                            </span>
                            <span class="exam-title-text"><i class="fa-solid fa-file-signature"></i> {{ $examTitle }}</span>
                        </div>

                        <div class="grade-result-box">
                            @if($s->status == 'graded')
                                <div class="score-main">
                                    <span class="score-earned">{{ $s->total_earned_grade }}</span>
                                    <span class="score-max">/ {{ $totalPoints }}</span>
                                </div>
                                <div class="score-pill {{ $percentage >= 50 ? 'pass-pill' : 'fail-pill' }}">
                                    {{ $percentage }}%
                                </div>
                            @else
                                <div class="pending-score-notice">
                                    <i class="fa-solid fa-pen-nib"></i> بانتظار إدخال الدرجات
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="item-footer">
                        <a href="{{ route(auth()->user()->role . '.submissions.grade', $s->id) }}" class="action-btn-main">
                            <span>{{ $s->status == 'graded' ? 'مراجعة وتعديل التصحيح' : 'تصحيح الإجابة الآن' }}</span>
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state-luxury">
            <div class="empty-glow-icon">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <h3>لا توجد أي تسليمات حتى الآن</h3>
            <p>عند إرسال الطلاب لإجاباتهم الخاصة بالمادة، ستقوم المنصة برصدها وإظهارها هنا فوراً.</p>
        </div>
    @endif

</div>

<script>
    function liveSearch() {
        const query = document.getElementById('submissionsSearch').value.toLowerCase();
        const cards = document.querySelectorAll('.submission-item-card');

        cards.forEach(card => {
            const searchContext = card.getAttribute('data-search');
            if (searchContext.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterCards(status, element) {
        document.querySelectorAll('.pill-btn').forEach(btn => btn.classList.remove('active'));
        element.classList.add('active');

        const cards = document.querySelectorAll('.submission-item-card');
        cards.forEach(card => {
            if (status === 'all' || card.getAttribute('data-status') === status) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap');

    .luxury-submissions-wrapper {
        font-family: 'Tajawal', sans-serif;
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    /* الهيدر الرئيسي السينمائي */
    .hero-banner {
        position: relative;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
        border-radius: 24px;
        padding: 40px 36px;
        color: #ffffff;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(30, 27, 75, 0.4);
    }

    .banner-content {
        position: relative;
        z-index: 2;
    }

    .tag-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #a5b4fc;
        border: 1px solid rgba(255, 255, 255, 0.15);
        margin-bottom: 14px;
    }

    .hero-title {
        font-size: 2.2rem;
        font-weight: 900;
        margin: 0 0 10px 0;
        letter-spacing: -0.5px;
    }

    .hero-sub {
        color: #cbd5e1;
        font-size: 1.05rem;
        margin: 0;
    }

    .banner-glow {
        position: absolute;
        top: -100px;
        left: -100px;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, rgba(0,0,0,0) 70%);
        pointer-events: none;
    }

    /* مؤشرات الأداء */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .metric-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }

    .total-icon { background: #e0e7ff; color: #4338ca; }
    .pending-icon { background: #fef3c7; color: #d97706; }
    .success-icon { background: #dcfce7; color: #15803d; }

    .metric-body {
        display: flex;
        flex-direction: column;
    }

    .metric-label {
        font-size: 0.88rem;
        color: #64748b;
        font-weight: 700;
    }

    .metric-num {
        font-size: 2rem;
        font-weight: 900;
        color: #0f172a;
    }

    .metric-bar-bg {
        height: 6px;
        background: #f1f5f9;
        border-radius: 10px;
        overflow: hidden;
    }

    .metric-bar-fill {
        height: 100%;
        background: #6366f1;
        border-radius: 10px;
        transition: width 0.4s ease;
    }

    .pending-fill { background: #f59e0b; }
    .success-fill { background: #10b981; }

    /* لوحة التحكم والفلترة */
    .control-panel {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .search-field-wrap {
        position: relative;
        flex: 1;
        min-width: 300px;
    }

    .search-ico {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1rem;
    }

    .search-field-wrap input {
        width: 100%;
        padding: 14px 50px 14px 20px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        font-size: 0.95rem;
        font-weight: 600;
        outline: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
    }

    .search-field-wrap input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
    }

    .pills-filter {
        display: flex;
        background: #f1f5f9;
        padding: 6px;
        border-radius: 16px;
        gap: 6px;
    }

    .pill-btn {
        border: none;
        background: transparent;
        padding: 8px 18px;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .pill-btn.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .pill-count {
        background: #e2e8f0;
        color: #475569;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.75rem;
    }

    .pill-btn.active .pill-count { background: #6366f1; color: #ffffff; }

    /* كروت التسليمات */
    .cards-layout {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 24px;
    }

    .submission-item-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        padding: 24px;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 20px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .submission-item-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px -10px rgba(99, 102, 241, 0.12);
        border-color: #c7d2fe;
    }

    .item-header {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .user-avatar-badge {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #ffffff;
        font-size: 1.2rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 16px -4px rgba(79, 70, 229, 0.3);
    }

    .user-meta {
        flex: 1;
        overflow: hidden;
    }

    .user-name {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-email {
        font-size: 0.8rem;
        color: #64748b;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .status-indicator {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 0.78rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .status-graded { background: #dcfce7; color: #166534; }
    .status-pending { background: #fef3c7; color: #92400e; }

    .item-body {
        background: #f8fafc;
        border-radius: 18px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        border: 1px solid #f1f5f9;
    }

    .exam-tag {
        display: flex;
        flex-direction: column;
        gap: 6px;
        color: #334155;
        font-weight: 700;
        font-size: 0.92rem;
    }

    /* تنسيق باج المادة داخل الكارت */
    .subject-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #e0e7ff;
        color: #3730a3;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 800;
        width: fit-content;
    }

    .exam-title-text {
        color: #0f172a;
        font-size: 0.98rem;
    }

    .exam-title-text i { color: #6366f1; }

    .grade-result-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 10px;
        border-top: 1px dashed #e2e8f0;
    }

    .score-main {
        font-weight: 900;
        font-size: 1.3rem;
    }

    .score-earned { color: #4f46e5; }
    .score-max { color: #94a3b8; font-size: 0.95rem; }

    .score-pill {
        padding: 4px 12px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.82rem;
    }

    .pass-pill { background: #d1fae5; color: #065f46; }
    .fail-pill { background: #fee2e2; color: #991b1b; }

    .pending-score-notice {
        color: #b45309;
        font-size: 0.88rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .item-footer {
        padding-top: 4px;
    }

    .action-btn-main {
        width: 100%;
        background: #0f172a;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 14px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
    }

    .action-btn-main:hover {
        background: #4f46e5;
        color: #ffffff;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
    }

    /* حالة الفراغ */
    .empty-state-luxury {
        text-align: center;
        background: #ffffff;
        padding: 70px 20px;
        border-radius: 24px;
        border: 2px dashed #e2e8f0;
    }

    .empty-glow-icon {
        width: 90px;
        height: 90px;
        background: #e0e7ff;
        color: #4338ca;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 20px auto;
    }

    .empty-state-luxury h3 {
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .empty-state-luxury p {
        color: #64748b;
        margin: 0;
    }
</style>
@endsection
