@extends('layouts.app')

@section('title', 'لوحة تعلّم الطالب')

@section('content')
<style>
    .student-dashboard-wrapper {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    /* 1. بطاقة الترحيب الأكاديمية الهادئة */
    .calm-hero-card {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        border-radius: var(--ed-radius-lg);
        padding: 32px 36px;
        color: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
        box-shadow: 0 10px 25px -5px rgba(29, 78, 216, 0.25);
        position: relative;
        overflow: hidden;
    }

    .calm-hero-card::after {
        content: '';
        position: absolute;
        top: -60px;
        left: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-text-content {
        max-width: 580px;
        position: relative;
        z-index: 1;
    }

    .hero-stage-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 12px;
        color: #eff6ff;
    }

    .hero-title {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .hero-desc {
        font-size: 0.94rem;
        color: #dbeafe;
        line-height: 1.6;
        margin: 0;
    }

    .hero-kpis {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    .hero-kpi-item {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 14px 20px;
        border-radius: var(--ed-radius-md);
        min-width: 125px;
        text-align: center;
    }

    .hero-kpi-label {
        font-size: 0.74rem;
        font-weight: 600;
        color: #bfdbfe;
        margin-bottom: 4px;
        display: block;
    }

    .hero-kpi-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
    }

    /* 2. شريط العد التنازلي للتوجيهي */
    .tawjihi-countdown-bar {
        background: var(--ed-surface);
        border: 1px solid var(--ed-border);
        border-radius: var(--ed-radius-lg);
        padding: 18px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: var(--ed-shadow-card);
    }

    .countdown-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .countdown-icon-box {
        width: 44px;
        height: 44px;
        border-radius: var(--ed-radius-md);
        background: var(--ed-primary-soft);
        color: var(--ed-primary);
        display: grid;
        place-items: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .countdown-timer-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .timer-unit-box {
        background: var(--ed-surface-alt);
        border: 1px solid var(--ed-border);
        border-radius: var(--ed-radius-sm);
        padding: 6px 12px;
        text-align: center;
        min-width: 52px;
    }

    .timer-unit-num {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--ed-text-main);
        font-family: monospace;
        line-height: 1.1;
        display: block;
    }

    .timer-unit-lbl {
        font-size: 0.65rem;
        color: var(--ed-text-muted);
        font-weight: 600;
    }

    /* 3. صندوق أدوات الطالب */
    .tools-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
    }

    .tool-card {
        background: var(--ed-surface);
        border: 1px solid var(--ed-border);
        border-radius: var(--ed-radius-md);
        padding: 18px 16px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: var(--transition-smooth);
        box-shadow: var(--ed-shadow-card);
    }

    .tool-card:hover {
        border-color: var(--ed-primary-border);
        transform: translateY(-2px);
        box-shadow: var(--ed-shadow-md);
    }

    .tool-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .tool-info h4 {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--ed-text-main);
        margin: 0 0 2px 0;
    }

    .tool-info span {
        font-size: 0.76rem;
        color: var(--ed-text-muted);
        display: block;
    }

    /* 4. قسم الاختبارات */
    .exams-layout-grid {
        display: grid;
        grid-template-columns: 2fr 1.2fr;
        gap: 24px;
    }

    .exam-card-item {
        background: var(--ed-surface);
        border: 1px solid var(--ed-border);
        border-radius: var(--ed-radius-md);
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 16px;
        transition: var(--transition-smooth);
    }

    .exam-card-item:hover {
        border-color: var(--ed-primary-border);
        box-shadow: var(--ed-shadow-md);
    }

    .exam-badge-tag {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--ed-primary);
        background: var(--ed-primary-soft);
        padding: 3px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .exam-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--ed-text-main);
        margin: 10px 0 8px;
    }

    .exam-meta-row {
        display: flex;
        gap: 16px;
        font-size: 0.8rem;
        color: var(--ed-text-muted);
        margin-bottom: 6px;
    }

    .exam-meta-row span {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    @media (max-width: 1024px) {
        .exams-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .calm-hero-card {
            padding: 24px 20px;
        }
        .hero-title {
            font-size: 1.4rem;
        }
        .hero-kpi-item {
            flex: 1;
            min-width: 90px;
            padding: 10px;
        }
        .hero-kpi-value {
            font-size: 1.25rem;
        }
    }
</style>

<div class="student-dashboard-wrapper">

    {{-- 1. بطاقة الترحيب والإنجاز اليومي --}}
    <div class="calm-hero-card">
        <div class="hero-text-content">
            <div class="hero-stage-badge">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>{{ $student->stage->name_ar ?? 'الثانوية العامة فلسطين 🇵🇸' }}</span>
            </div>
            <h1 class="hero-title">أهلاً بك يا {{ $student->name_ar ?? 'طالبنا المتميز' }} 👋</h1>
            <p class="hero-desc">
                مساحتك التعليمية المخصصة لمتابعة المنهاج، تقديم الاختبارات، وتنظيم المراجعة نحو الامتياز في التوجيهي.
            </p>
        </div>

        <div class="hero-kpis">
            @php $currentStudent = Auth::guard('student')->user() ?? $student; @endphp
            <div class="hero-kpi-item" style="border-color: rgba(251, 146, 60, 0.4); background: rgba(251, 146, 60, 0.15);">
                <span class="hero-kpi-label" style="color: #fed7aa;"><i class="fa-solid fa-fire"></i> الالتزام المتتالي</span>
                <span class="hero-kpi-value" style="color: #fff7ed;">{{ $currentStudent->streak_count ?? 1 }} أيام 🔥</span>
            </div>
            <div class="hero-kpi-item">
                <span class="hero-kpi-label">المعدل العام</span>
                <span class="hero-kpi-value">{{ number_format($my_stats['avg_grade'] ?? 0, 1) }}%</span>
            </div>
            <div class="hero-kpi-item">
                <span class="hero-kpi-label">الاختبارات المنجزة</span>
                <span class="hero-kpi-value">{{ $my_stats['completed_exams'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- 2. تنبيه المدفوعات قيد المراجعة إن وجدت --}}
    @if(isset($pendingPayments) && $pendingPayments->isNotEmpty())
        @foreach($pendingPayments as $pendingPay)
            <div class="ed-card" style="background: var(--ed-warning-soft); border-color: #fde68a;">
                <div class="ed-card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: #fef3c7; color: #b45309; display: grid; place-items: center; font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 3px 0; font-size: 0.98rem; font-weight: 700; color: #78350f;">
                                إشعار سداد قيد التدقيق (معاملة #{{ $pendingPay->transaction_number }})
                            </h4>
                            <p style="margin: 0; font-size: 0.84rem; color: #92400e;">
                                تم استلام إشعار الدفع بمبلغ <strong>{{ number_format($pendingPay->amount, 0) }} ₪</strong> عبر {{ $pendingPay->gateway_name_ar }}. يقوم المشرف بمطابقته وتفعيل المواد لحسابك رسمياً.
                            </p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <a href="{{ route('student.checkout.receipt', $pendingPay->id) }}" class="ed-btn ed-btn-outline" style="font-size: 0.82rem; padding: 7px 14px;">
                            <i class="fa-regular fa-file-lines"></i> عرض الإيصال
                        </a>
                        @php $adminWhatsapp = \App\Models\Setting::get('contact_whatsapp', \App\Models\Setting::get('payment_phone', '0567897212')); @endphp
                        <a href="https://wa.me/972{{ ltrim($adminWhatsapp, '0') }}?text={{ urlencode('مرحباً إدارة منارة التوجيهي، قمت برفع إشعار دفع برقم: ' . $pendingPay->transaction_number . ' للاعتماد.') }}" target="_blank" class="ed-btn ed-btn-primary" style="background: #16a34a; border-color: #16a34a; font-size: 0.82rem; padding: 7px 14px;">
                            <i class="fa-brands fa-whatsapp"></i> تواصل مع المشرف
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- 3. شريط العد التنازلي لامتحانات الثانوية العامة --}}
    <div class="tawjihi-countdown-bar">
        <div class="countdown-info">
            <div class="countdown-icon-box">
                <i class="fa-regular fa-calendar-check"></i>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 2px;">
                    <span class="ed-badge ed-badge-red" style="font-size: 0.68rem; padding: 1px 8px;">دورة فلسطين الوزارية 🇵🇸</span>
                    <strong style="font-size: 0.94rem; color: var(--ed-text-main);">العد التنازلي لامتحانات التوجيهي</strong>
                </div>
                <p style="margin: 0; font-size: 0.8rem; color: var(--ed-text-muted);" id="dailyTipText">
                    💡 ركز على تنظيم ساعات دراستك وحل نماذج الامتحانات الاسترشادية باستمرار.
                </p>
            </div>
        </div>

        <div class="countdown-timer-group" id="tawjihiCountdownBoxes">
            <div class="timer-unit-box">
                <span class="timer-unit-num" id="cntDays">--</span>
                <span class="timer-unit-lbl">يوم</span>
            </div>
            <div style="font-weight: 700; color: var(--ed-text-dim);">:</div>
            <div class="timer-unit-box">
                <span class="timer-unit-num" id="cntHours">--</span>
                <span class="timer-unit-lbl">ساعة</span>
            </div>
            <div style="font-weight: 700; color: var(--ed-text-dim);">:</div>
            <div class="timer-unit-box">
                <span class="timer-unit-num" id="cntMinutes">--</span>
                <span class="timer-unit-lbl">دقيقة</span>
            </div>
            <div style="font-weight: 700; color: var(--ed-text-dim);">:</div>
            <div class="timer-unit-box">
                <span class="timer-unit-num" id="cntSeconds" style="color: var(--ed-primary);">--</span>
                <span class="timer-unit-lbl">ثانية</span>
            </div>
        </div>
    </div>

    {{-- 4. أدوات التوجيهي السريعة والمواد --}}
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
            <h3 style="font-size: 1.08rem; font-weight: 700; color: var(--ed-text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-shapes" style="color: var(--ed-primary);"></i>
                <span>أدوات التفوق الدراسي والخدمات</span>
            </h3>
            <span style="font-size: 0.8rem; color: var(--ed-text-muted);">وصول مباشر لأقسامك المفضلة</span>
        </div>

        <div class="tools-grid">
            <a href="{{ route('student.subjects.index') }}" class="tool-card">
                <div class="tool-icon" style="background: #eff6ff; color: #1d4ed8;">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div class="tool-info">
                    <h4>مناهجي ومقرراتي</h4>
                    <span>الدروس والشروحات المعتمدة</span>
                </div>
            </a>

            <a href="{{ route('student.courses.catalog') }}" class="tool-card">
                <div class="tool-icon" style="background: #f0fdf4; color: #15803d;">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div class="tool-info">
                    <h4>باقات المواد</h4>
                    <span>الاشتراكات وبوابات الدفع</span>
                </div>
            </a>

            <a href="{{ route('student.planner.index') }}" class="tool-card">
                <div class="tool-icon" style="background: #f0f9ff; color: #0284c7;">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div class="tool-info">
                    <h4>جدول المراجعة</h4>
                    <span>تنظيم خطتك قبل الامتحانات</span>
                </div>
            </a>

            <a href="{{ route('student.achievements') }}" class="tool-card">
                <div class="tool-icon" style="background: #fefce8; color: #a16207;">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div class="tool-info">
                    <h4>الشهادات والأوسمة</h4>
                    <span>سجل الإنجاز ومؤقت التركيز</span>
                </div>
            </a>

            <a href="{{ route('tawjihi.calculator') }}" target="_blank" class="tool-card">
                <div class="tool-icon" style="background: #fdf2f8; color: #be185d;">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <div class="tool-info">
                    <h4>حاسبة المعدل</h4>
                    <span>دليل التنسيق والقبول الجامعي</span>
                </div>
            </a>

            <a href="{{ route('student.teachers.index') }}" class="tool-card">
                <div class="tool-icon" style="background: #f5f3ff; color: #6d28d9;">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <div class="tool-info">
                    <h4>معلمو مرحلتي</h4>
                    <span>مراسلة وتوجيه أكاديمي</span>
                </div>
            </a>
        </div>
    </div>

    {{-- 5. شبكة الاختبارات: المتاحة والمكتملة --}}
    <div class="exams-layout-grid">

        {{-- الاختبارات المتاحة --}}
        <div class="ed-card">
            <div class="ed-card-header">
                <h3><i class="fa-regular fa-clock" style="color: var(--ed-primary);"></i> اختبارات بانتظارك</h3>
                <a href="{{ route('student.exams.index') }}" class="ed-btn ed-btn-outline" style="font-size: 0.78rem; padding: 4px 10px;">
                    عرض الكل
                </a>
            </div>

            <div class="ed-card-body" style="display: flex; flex-direction: column; gap: 14px;">
                @forelse($available_exams as $ex)
                    <div class="exam-card-item">
                        <div>
                            <span class="exam-badge-tag">
                                <i class="fa-regular fa-bookmark"></i>
                                {{ $ex->subject->name_ar ?? 'المادة الدراسية' }}
                            </span>
                            <h4 class="exam-card-title">{{ $ex->title }}</h4>
                            <div class="exam-meta-row">
                                <span><i class="fa-regular fa-circle-question"></i> {{ $ex->questions_count ?? ($ex->questions ? $ex->questions->count() : 10) }} أسئلة</span>
                                <span><i class="fa-regular fa-clock"></i> {{ $ex->duration_minutes ?? '30' }} دقيقة</span>
                            </div>
                        </div>
                        <a href="{{ route('student.exams.take', $ex->id) }}" class="ed-btn ed-btn-primary" style="width: 100%;">
                            <span>بدء الاختبار الآن</span>
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    </div>
                @empty
                    <div style="text-align: center; padding: 32px 16px; color: var(--ed-text-muted);">
                        <i class="fa-regular fa-circle-check" style="font-size: 2rem; color: var(--ed-success); display: block; margin-bottom: 8px;"></i>
                        <h4 style="font-size: 0.96rem; font-weight: 700; color: var(--ed-text-main); margin-bottom: 4px;">رائع! لا توجد اختبارات معلقة</h4>
                        <p style="font-size: 0.82rem; margin: 0;">أكملت جميع الاختبارات المتاحة حالياً بنجاح.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- الاختبارات المكتملة --}}
        <div class="ed-card">
            <div class="ed-card-header">
                <h3><i class="fa-solid fa-check-double" style="color: var(--ed-success);"></i> آخر الاختبارات المكتملة</h3>
            </div>

            <div class="ed-card-body" style="padding: 12px 18px;">
                @forelse($completed_exams ?? [] as $done_exam)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--ed-border-subtle);">
                        <div>
                            <div style="font-size: 0.88rem; font-weight: 700; color: var(--ed-text-main); margin-bottom: 2px;">
                                {{ $done_exam->exam->title ?? $done_exam->title }}
                            </div>
                            <span style="font-size: 0.74rem; color: var(--ed-text-dim);">
                                {{ $done_exam->created_at ? $done_exam->created_at->format('Y/m/d') : 'مؤخراً' }}
                            </span>
                        </div>
                        <div>
                            @if($done_exam->status == 'graded')
                                <span class="ed-badge ed-badge-green">
                                    {{ number_format($done_exam->total_earned_grade, 1) }}%
                                </span>
                            @else
                                <span class="ed-badge ed-badge-amber">
                                    قيد التصحيح
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 28px 14px; color: var(--ed-text-muted);">
                        <i class="fa-regular fa-folder-open" style="font-size: 1.8rem; opacity: 0.5; margin-bottom: 6px; display: block;"></i>
                        <span style="font-size: 0.84rem;">لم تسلم أي اختبارات بعد</span>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<script>
    function initTawjihiCountdown() {
        const now = new Date();
        let currentYear = now.getFullYear();
        let targetExamDate = new Date(currentYear, 5, 7, 9, 0, 0); // 7 حزيران الساعة 9:00 صباحاً
        if (now > targetExamDate) {
            targetExamDate = new Date(currentYear + 1, 5, 7, 9, 0, 0);
        }

        const tips = [
            "💡 نصيحة اليوم: حل نماذج امتحانات الإنجاز الوزارية والأسئلة الشاملة لضبط إدارة الوقت في القاعة.",
            "💡 نصيحة اليوم: استخدم بطاقات الاستذكار لحفظ القوانين والمصطلحات الصعبة قبل النوم لتثبيتها في الذاكرة.",
            "💡 نصيحة اليوم: خصص استراحة 5 دقائق لكل 25 دقيقة دراسة (تقنية بومودورو) لتحافظ على تركيزك.",
            "💡 نصيحة اليوم: تأكد من مراجعة أسئلة نهاية كل وحدة في الكتب المدرسية فهي مصدر أساسي للأسئلة.",
            "💡 نصيحة اليوم: تواصل مع معلمك في المنصة لمساعدتك في أي مسألة أو قانون تجد فيه صعوبة."
        ];
        const dayOfYear = Math.floor((now - new Date(now.getFullYear(), 0, 0)) / 1000 / 60 / 60 / 24);
        const tipEl = document.getElementById('dailyTipText');
        if (tipEl) tipEl.innerText = tips[dayOfYear % tips.length];

        function updateTimer() {
            const diff = targetExamDate - new Date();
            if (diff <= 0) {
                document.getElementById('cntDays').innerText = '0';
                document.getElementById('cntHours').innerText = '0';
                document.getElementById('cntMinutes').innerText = '0';
                document.getElementById('cntSeconds').innerText = '0';
                return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
            const minutes = Math.floor((diff / 1000 / 60) % 60);
            const seconds = Math.floor((diff / 1000) % 60);

            const dEl = document.getElementById('cntDays');
            const hEl = document.getElementById('cntHours');
            const mEl = document.getElementById('cntMinutes');
            const sEl = document.getElementById('cntSeconds');

            if (dEl) dEl.innerText = days;
            if (hEl) hEl.innerText = String(hours).padStart(2, '0');
            if (mEl) mEl.innerText = String(minutes).padStart(2, '0');
            if (sEl) sEl.innerText = String(seconds).padStart(2, '0');
        }

        updateTimer();
        setInterval(updateTimer, 1000);
    }
    document.addEventListener('DOMContentLoaded', initTawjihiCountdown);
</script>
@endsection
