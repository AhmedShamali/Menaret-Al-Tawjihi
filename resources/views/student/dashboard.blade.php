@extends('layouts.app')

@section('title', 'لوحة تعلّم الطالب | منارة التوجيهي')

@section('content')
<div class="ed-student-dash-wrap">

    {{-- 1. بطاقة الترحيب والإنجاز اليومي الفخمة --}}
    <div class="ed-dash-hero">
        <div class="ed-hero-main">
            <div class="ed-hero-tags">
                <span class="ed-tag-pill">
                    <i class="fas fa-graduation-cap"></i>
                    <span>{{ $student->stage->name_ar ?? $student->stage->label_ar ?? 'الثانوية العامة فلسطين 🇵🇸' }}</span>
                </span>
                <span class="ed-tag-pill live">
                    <span class="live-dot"></span> الفصل الدراسي الحالي
                </span>
            </div>

            <h1 class="ed-hero-name">
                مرحباً بك، <span class="name-gradient">{{ $student->name_ar ?? 'طالبنا المتميز' }}</span> 👋
            </h1>

            <p class="ed-hero-quote" id="dailyTipText">
                💡 ركز على تنظيم ساعات دراستك اليومية وحل نماذج الامتحانات الاسترشادية باستمرار.
            </p>

            <div class="ed-hero-actions">
                <a href="{{ route('student.subjects.index') }}" class="ed-hero-btn primary">
                    <i class="fas fa-play-circle"></i>
                    <span>متابعة الدراسة الآن</span>
                </a>
                <a href="{{ route('student.planner.index') }}" class="ed-hero-btn secondary">
                    <i class="fas fa-calendar-check"></i>
                    <span>جدول المراجعة</span>
                </a>
            </div>
        </div>

        {{-- إحصائيات الطالب الحية --}}
        @php $currentStudent = Auth::guard('student')->user() ?? $student; @endphp
        <div class="ed-hero-stats">
            <div class="ed-hstat-card streak">
                <div class="ed-hstat-icon"><i class="fas fa-fire"></i></div>
                <div class="ed-hstat-info">
                    <span class="lbl">الالتزام المتتالي</span>
                    <strong class="val">{{ $currentStudent->streak_count ?? 1 }} أيام</strong>
                </div>
            </div>

            <div class="ed-hstat-card gpa">
                <div class="ed-hstat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="ed-hstat-info">
                    <span class="lbl">المعدل العام</span>
                    <strong class="val">{{ number_format($my_stats['avg_grade'] ?? 0, 1) }}%</strong>
                </div>
            </div>

            <div class="ed-hstat-card exams">
                <div class="ed-hstat-icon"><i class="fas fa-clipboard-check"></i></div>
                <div class="ed-hstat-info">
                    <span class="lbl">امتحانات منجزة</span>
                    <strong class="val">{{ $my_stats['completed_exams'] ?? 0 }} اختبار</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. تنبيه المدفوعات قيد المراجعة إن وجدت --}}
    @if(isset($pendingPayments) && $pendingPayments->isNotEmpty())
        @foreach($pendingPayments as $pendingPay)
            <div class="ed-alert-banner">
                <div class="ed-ab-content">
                    <div class="ed-ab-icon"><i class="fas fa-hourglass-half"></i></div>
                    <div>
                        <h4>إشعار سداد قيد المراجعة (معاملة #{{ $pendingPay->transaction_number }})</h4>
                        <p>
                            تم استلام إشعار الدفع بمبلغ <strong>{{ number_format($pendingPay->amount, 0) }} ₪</strong> عبر {{ $pendingPay->gateway_name_ar }}. سيقوم المشرف بمطابقته وتفعيل المواد لحسابك رسمياً.
                        </p>
                    </div>
                </div>
                <div class="ed-ab-actions">
                    <a href="{{ route('student.checkout.receipt', $pendingPay->id) }}" class="ed-btn ed-btn-outline" style="font-size: 0.8rem;">
                        <i class="far fa-file-alt"></i> الإيصال
                    </a>
                    @php $adminWhatsapp = \App\Models\Setting::get('contact_whatsapp', \App\Models\Setting::get('payment_phone', '0567897212')); @endphp
                    <a href="https://wa.me/972{{ ltrim($adminWhatsapp, '0') }}?text={{ urlencode('مرحباً إدارة منارة التوجيهي، قمت برفع إشعار دفع برقم: ' . $pendingPay->transaction_number . ' للاعتماد.') }}" target="_blank" class="ed-btn ed-btn-primary" style="background: #16a34a; border-color: #16a34a; font-size: 0.8rem;">
                        <i class="fab fa-whatsapp"></i> تواصل مع المشرف
                    </a>
                </div>
            </div>
        @endforeach
    @endif

    {{-- 3. شريط العد التنازلي للتوجيهي الفخم --}}
    <div class="ed-countdown-panel">
        <div class="ed-cd-info">
            <div class="ed-cd-icon">
                <i class="fas fa-hourglass-start"></i>
            </div>
            <div>
                <div class="ed-cd-badge">
                    <span>دورة امتحانات فلسطين الوزارية 🇵🇸</span>
                </div>
                <h3>العد التنازلي لانطلاق امتحانات التوجيهي</h3>
                <small>الوقت يمضي سريعاً، كل دقيقة تقضيها بتركيز تقربك من كليات القمة.</small>
            </div>
        </div>

        <div class="ed-cd-clock">
            <div class="ed-clock-unit">
                <span class="num" id="cntDays">--</span>
                <span class="txt">يوم</span>
            </div>
            <div class="ed-clock-sep">:</div>
            <div class="ed-clock-unit">
                <span class="num" id="cntHours">--</span>
                <span class="txt">ساعة</span>
            </div>
            <div class="ed-clock-sep">:</div>
            <div class="ed-clock-unit">
                <span class="num" id="cntMinutes">--</span>
                <span class="txt">دقيقة</span>
            </div>
            <div class="ed-clock-sep">:</div>
            <div class="ed-clock-unit sec">
                <span class="num" id="cntSeconds">--</span>
                <span class="txt">ثانية</span>
            </div>
        </div>
    </div>

    {{-- 4. أدوات التفوق الدراسي الذكية --}}
    <div class="ed-tools-section">
        <div class="ed-section-head">
            <div class="ed-sh-title">
                <i class="fas fa-shapes" style="color: #1d4ed8;"></i>
                <h2>أدوات التفوق الدراسي والخدمات الذكية</h2>
            </div>
            <span class="ed-sh-hint">وصول مباشر لأقسامك المفضلة</span>
        </div>

        <div class="ed-tools-grid">
            
            <a href="{{ route('student.subjects.index') }}" class="ed-tool-card">
                <div class="ed-t-icon" style="background: #eff6ff; color: #1d4ed8;">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="ed-t-info">
                    <h4>مناهجي ومقرراتي</h4>
                    <p>الدروس والشروحات المعتمدة</p>
                </div>
                <i class="fas fa-chevron-left ed-t-arrow"></i>
            </a>

            <a href="{{ route('student.flashcards.index') }}" class="ed-tool-card featured">
                <span class="ed-t-badge">تفاعلي 3D</span>
                <div class="ed-t-icon" style="background: #fdf4ff; color: #a21caf;">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="ed-t-info">
                    <h4>بطاقات القوانين</h4>
                    <p>استذكار سريع وتكرار متباعد</p>
                </div>
                <i class="fas fa-chevron-left ed-t-arrow"></i>
            </a>

            <a href="{{ route('student.planner.index') }}" class="ed-tool-card">
                <div class="ed-t-icon" style="background: #f0f9ff; color: #0284c7;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="ed-t-info">
                    <h4>جدول المراجعة الذكي</h4>
                    <p>خطة دراسية للأيام المتبقية</p>
                </div>
                <i class="fas fa-chevron-left ed-t-arrow"></i>
            </a>

            <a href="{{ route('student.exams.index') }}" class="ed-tool-card">
                <div class="ed-t-icon" style="background: #ecfdf5; color: #059669;">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div class="ed-t-info">
                    <h4>قاعة الاختبارات</h4>
                    <p>نماذج وزارية وامتحانات محاكية</p>
                </div>
                <i class="fas fa-chevron-left ed-t-arrow"></i>
            </a>

            <a href="{{ route('student.leaderboard') }}" class="ed-tool-card">
                <div class="ed-t-icon" style="background: #fffbeb; color: #b45309;">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="ed-t-info">
                    <h4>لوحة الشرف</h4>
                    <p>أوائل طلبة فلسطين ونقاطك</p>
                </div>
                <i class="fas fa-chevron-left ed-t-arrow"></i>
            </a>

            <a href="{{ route('student.achievements') }}" class="ed-tool-card">
                <div class="ed-t-icon" style="background: #fefce8; color: #ca8a04;">
                    <i class="fas fa-award"></i>
                </div>
                <div class="ed-t-info">
                    <h4>الشهادات وغرفة التركيز</h4>
                    <p>سجل الإنجاز ومؤقت بومودورو</p>
                </div>
                <i class="fas fa-chevron-left ed-t-arrow"></i>
            </a>

            <a href="{{ route('tawjihi.calculator') }}" target="_blank" class="ed-tool-card">
                <div class="ed-t-icon" style="background: #fdf2f8; color: #be185d;">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="ed-t-info">
                    <h4>حاسبة المعدل</h4>
                    <p>دليل التنسيق والقبول الجامعي</p>
                </div>
                <i class="fas fa-external-link-alt ed-t-arrow"></i>
            </a>

            <a href="{{ route('student.teachers.index') }}" class="ed-tool-card">
                <div class="ed-t-icon" style="background: #f5f3ff; color: #6d28d9;">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="ed-t-info">
                    <h4>معلمو مرحلتي</h4>
                    <p>مراسلة وإرشاد أكاديمي مباشر</p>
                </div>
                <i class="fas fa-chevron-left ed-t-arrow"></i>
            </a>

        </div>
    </div>

    {{-- 5. شبكة الاختبارات ومنطقة التركيز --}}
    <div class="ed-main-grid">

        {{-- الاختبارات بانتظارك --}}
        <div class="ed-card ed-exams-panel">
            <div class="ed-card-header">
                <div class="ed-card-title">
                    <i class="far fa-clock" style="color: #1d4ed8;"></i>
                    <h3>اختبارات مقترحة بانتظارك</h3>
                </div>
                <a href="{{ route('student.exams.index') }}" class="ed-link-btn">
                    <span>عرض كل الاختبارات</span>
                    <i class="fas fa-chevron-left"></i>
                </a>
            </div>

            <div class="ed-exams-list">
                @forelse($available_exams as $ex)
                    <div class="ed-exam-row">
                        <div class="ed-exam-left">
                            <span class="ed-exam-sub-tag">
                                {{ $ex->subject->name_ar ?? 'مبحث دراسي' }}
                            </span>
                            <h4>{{ $ex->title }}</h4>
                            <div class="ed-exam-specs">
                                <span><i class="far fa-question-circle"></i> {{ $ex->questions_count ?? ($ex->questions ? $ex->questions->count() : 10) }} سؤال</span>
                                <span><i class="far fa-clock"></i> {{ $ex->duration_minutes ?? '30' }} دقيقة</span>
                            </div>
                        </div>

                        <a href="{{ route('student.exams.take', $ex->id) }}" class="ed-btn ed-btn-primary" style="font-size: 0.85rem; padding: 9px 18px; white-space: nowrap;">
                            <span>بدء الاختبار</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                @empty
                    <div class="ed-empty-box">
                        <i class="fas fa-check-circle" style="color: #059669;"></i>
                        <h4>رائع جداً! لا توجد اختبارات معلقة</h4>
                        <p>أكملت كافة النماذج المتاحة لمرحلتك حالياً، تابع دراسة الوحدات الجديدة.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- الجانب الأيسر: الاختبارات المكتملة وهدف التوجيهي --}}
        <div class="ed-side-col">
            
            {{-- بطاقة هدفي في التوجيهي --}}
            <div class="ed-card ed-goal-card">
                <div class="ed-goal-header">
                    <div class="ed-goal-icon"><i class="fas fa-bullseye"></i></div>
                    <div>
                        <h4>هدفي الأكاديمي للثانوية</h4>
                        <span style="font-size: 0.78rem; color: #64748b;">طريقي نحو الجامعة</span>
                    </div>
                </div>
                <div class="ed-goal-body">
                    <div class="ed-goal-gpa">
                        <span class="lbl">معدلي المستهدف:</span>
                        <strong class="val">98.5% +</strong>
                    </div>
                    <p class="ed-goal-text">
                        "كل جهد تبذله في المراجعة وحل المسائل يرفع فرصك في الحصول على المقعد الجامعي الذي تحلم به."
                    </p>
                    <a href="{{ route('student.achievements') }}" class="ed-btn ed-btn-outline" style="width: 100%; justify-content: center; font-size: 0.82rem;">
                        <i class="fas fa-hourglass-start"></i> بدء جلسة تركيز (بومودورو)
                    </a>
                </div>
            </div>

            {{-- آخر الاختبارات المكتملة --}}
            <div class="ed-card">
                <div class="ed-card-header">
                    <div class="ed-card-title">
                        <i class="fas fa-history" style="color: #059669;"></i>
                        <h4 style="font-size: 0.95rem; font-weight: 800; margin: 0;">آخر درجاتك المحرزة</h4>
                    </div>
                </div>

                <div class="ed-completed-list">
                    @forelse($completed_exams ?? [] as $done_exam)
                        <div class="ed-done-item">
                            <div>
                                <strong class="title">{{ $done_exam->exam->title ?? $done_exam->title }}</strong>
                                <span class="date">{{ $done_exam->created_at ? $done_exam->created_at->format('Y/m/d') : 'مؤخراً' }}</span>
                            </div>
                            <div>
                                @if($done_exam->status == 'graded')
                                    <span class="ed-badge ed-badge-emerald">
                                        {{ number_format($done_exam->total_earned_grade, 1) }}%
                                    </span>
                                @else
                                    <span class="ed-badge ed-badge-amber">
                                        قيد المراجعة
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 24px 10px; color: #94a3b8; font-size: 0.84rem;">
                            <i class="far fa-folder-open" style="font-size: 1.6rem; display: block; margin-bottom: 6px; opacity: 0.6;"></i>
                            لم تقم بتسليم أي اختبارات بعد.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

<style>
    .ed-student-dash-wrap {
        display: flex;
        flex-direction: column;
        gap: 26px;
        font-family: 'Alexandria', 'Tajawal', sans-serif;
        direction: rtl;
    }

    /* 1. Hero Card */
    .ed-dash-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%);
        border-radius: 22px;
        padding: 34px 38px;
        color: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px -10px rgba(29, 78, 216, 0.35);
    }

    .ed-dash-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.2), transparent 50%),
                    radial-gradient(circle at 10% 80%, rgba(30, 64, 175, 0.3), transparent 50%);
        pointer-events: none;
    }

    .ed-hero-main {
        position: relative;
        z-index: 2;
        max-width: 580px;
    }

    .ed-hero-tags {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
    }

    .ed-tag-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #eff6ff;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .ed-tag-pill.live {
        background: rgba(16, 185, 129, 0.15);
        border-color: rgba(16, 185, 129, 0.3);
        color: #6ee7b7;
    }

    .live-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.4);
    }

    .ed-hero-name {
        font-size: 2rem;
        font-weight: 900;
        margin: 0 0 10px;
        line-height: 1.3;
    }

    .name-gradient {
        background: linear-gradient(135deg, #ffffff 0%, #93c5fd 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .ed-hero-quote {
        font-size: 0.95rem;
        color: #cbd5e1;
        line-height: 1.7;
        margin: 0 0 24px;
    }

    .ed-hero-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .ed-hero-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .ed-hero-btn.primary {
        background: #ffffff;
        color: #1d4ed8;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    }

    .ed-hero-btn.primary:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }

    .ed-hero-btn.secondary {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .ed-hero-btn.secondary:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .ed-hero-stats {
        display: flex;
        flex-direction: column;
        gap: 12px;
        position: relative;
        z-index: 2;
        min-width: 220px;
    }

    .ed-hstat-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 14px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform 0.2s;
    }

    .ed-hstat-card:hover {
        transform: translateX(-4px);
    }

    .ed-hstat-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .ed-hstat-card.streak .ed-hstat-icon { background: rgba(249, 115, 22, 0.2); color: #fb923c; }
    .ed-hstat-card.gpa .ed-hstat-icon { background: rgba(16, 185, 129, 0.2); color: #34d399; }
    .ed-hstat-card.exams .ed-hstat-icon { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }

    .ed-hstat-info .lbl {
        display: block;
        font-size: 0.72rem;
        color: #94a3b8;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .ed-hstat-info .val {
        font-size: 1.25rem;
        font-weight: 900;
        color: #ffffff;
    }

    /* 2. Alert Banner */
    .ed-alert-banner {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 16px;
        padding: 16px 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }

    .ed-ab-content {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .ed-ab-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #fef3c7;
        color: #b45309;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .ed-ab-content h4 {
        margin: 0 0 2px;
        font-size: 0.95rem;
        font-weight: 800;
        color: #78350f;
    }

    .ed-ab-content p {
        margin: 0;
        font-size: 0.82rem;
        color: #92400e;
    }

    .ed-ab-actions {
        display: flex;
        gap: 8px;
    }

    /* 3. Countdown Panel */
    .ed-countdown-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px 26px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 18px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    }

    .ed-cd-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .ed-cd-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #eff6ff;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .ed-cd-badge {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 700;
        color: #b91c1c;
        background: #fef2f2;
        padding: 2px 8px;
        border-radius: 6px;
        margin-bottom: 4px;
    }

    .ed-cd-info h3 {
        margin: 0 0 2px;
        font-size: 1.08rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-cd-info small {
        font-size: 0.8rem;
        color: #64748b;
    }

    .ed-cd-clock {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ed-clock-unit {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 14px;
        text-align: center;
        min-width: 58px;
    }

    .ed-clock-unit.sec .num {
        color: #1d4ed8;
    }

    .ed-clock-unit .num {
        font-size: 1.45rem;
        font-weight: 900;
        color: #0f172a;
        font-family: monospace;
        line-height: 1.1;
        display: block;
    }

    .ed-clock-unit .txt {
        font-size: 0.68rem;
        color: #64748b;
        font-weight: 700;
    }

    .ed-clock-sep {
        font-size: 1.2rem;
        font-weight: 800;
        color: #cbd5e1;
    }

    /* 4. Tools Grid */
    .ed-tools-section {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .ed-section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ed-sh-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ed-sh-title h2 {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .ed-sh-hint {
        font-size: 0.8rem;
        color: #64748b;
    }

    .ed-tools-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    @media (max-width: 1100px) { .ed-tools-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 580px) { .ed-tools-grid { grid-template-columns: 1fr; } }

    .ed-tool-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px 18px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 14px;
        position: relative;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }

    .ed-tool-card:hover {
        transform: translateY(-3px);
        border-color: #1d4ed8;
        box-shadow: 0 10px 20px -5px rgba(29, 78, 216, 0.1);
    }

    .ed-tool-card.featured {
        border-color: #f0abfc;
        background: linear-gradient(180deg, #fdf4ff 0%, #ffffff 100%);
    }

    .ed-t-badge {
        position: absolute;
        top: 8px;
        left: 10px;
        background: #a21caf;
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 800;
        padding: 1px 7px;
        border-radius: 6px;
    }

    .ed-t-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .ed-t-info {
        flex: 1;
    }

    .ed-t-info h4 {
        margin: 0 0 2px;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-t-info p {
        margin: 0;
        font-size: 0.76rem;
        color: #64748b;
        line-height: 1.4;
    }

    .ed-t-arrow {
        font-size: 0.75rem;
        color: #cbd5e1;
        transition: transform 0.2s;
    }

    .ed-tool-card:hover .ed-t-arrow {
        color: #1d4ed8;
        transform: translateX(-3px);
    }

    /* 5. Main Grid (Exams + Goal) */
    .ed-main-grid {
        display: grid;
        grid-template-columns: 1.9fr 1.1fr;
        gap: 22px;
        align-items: start;
    }

    @media (max-width: 992px) {
        .ed-main-grid {
            grid-template-columns: 1fr;
        }
    }

    .ed-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .ed-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 16px;
        margin-bottom: 18px;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ed-card-title h3 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-link-btn {
        color: #1d4ed8;
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .ed-link-btn:hover {
        text-decoration: underline;
    }

    /* Exams List */
    .ed-exams-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .ed-exam-row {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        transition: all 0.2s;
    }

    .ed-exam-row:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .ed-exam-sub-tag {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 700;
        color: #1d4ed8;
        background: #eff6ff;
        padding: 2px 8px;
        border-radius: 6px;
        margin-bottom: 6px;
    }

    .ed-exam-left h4 {
        margin: 0 0 6px;
        font-size: 0.98rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-exam-specs {
        display: flex;
        gap: 14px;
        font-size: 0.78rem;
        color: #64748b;
    }

    .ed-empty-box {
        text-align: center;
        padding: 40px 20px;
    }

    .ed-empty-box i {
        font-size: 2.4rem;
        margin-bottom: 10px;
    }

    .ed-empty-box h4 {
        margin: 0 0 4px;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-empty-box p {
        margin: 0;
        font-size: 0.85rem;
        color: #64748b;
    }

    /* Side Col */
    .ed-side-col {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .ed-goal-card {
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        border-color: #e2e8f0;
    }

    .ed-goal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .ed-goal-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #fee2e2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .ed-goal-header h4 {
        margin: 0 0 2px;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-goal-gpa {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .ed-goal-gpa .lbl {
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
    }

    .ed-goal-gpa .val {
        font-size: 1.3rem;
        font-weight: 900;
        color: #1d4ed8;
    }

    .ed-goal-text {
        font-size: 0.8rem;
        color: #64748b;
        line-height: 1.6;
        margin: 0 0 16px;
        font-style: italic;
    }

    /* Completed list */
    .ed-completed-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .ed-done-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-done-item:last-child {
        border-bottom: none;
    }

    .ed-done-item .title {
        display: block;
        font-size: 0.86rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .ed-done-item .date {
        font-size: 0.72rem;
        color: #94a3b8;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .ed-dash-hero {
            padding: 24px 20px;
        }
        .ed-hero-name {
            font-size: 1.6rem;
        }
        .ed-countdown-panel {
            padding: 18px 16px;
        }
        .ed-clock-unit {
            padding: 6px 10px;
            min-width: 48px;
        }
        .ed-clock-unit .num {
            font-size: 1.2rem;
        }
    }
</style>

<script>
    function initTawjihiCountdown() {
        const now = new Date();
        let currentYear = now.getFullYear();
        let targetExamDate = new Date(currentYear, 5, 7, 9, 0, 0); // 7 حزيران
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
