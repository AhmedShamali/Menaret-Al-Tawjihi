@extends('layouts.app')

@section('title', __('لوحة تعلّم الطالب') . ' | ' . \App\Models\Setting::get('site_name', __('منارة التوجيهي')))

@section('content')
<div class="ed-student-dash-wrap">

    {{-- 1. الترويسة الأكاديمية الكلاسيكية الرسمية --}}
    <div class="ed-dash-header-classic">
        <div class="ed-header-main-info">
            <div class="ed-header-badges">
                <span class="ed-badge-item stage">
                    <i class="fas fa-graduation-cap"></i>
                    <span>{{ $student->stage->name_ar ?? $student->stage->label_ar ?? __('الثانوية العامة فلسطين 🇵🇸') }}</span>
                </span>
                <span class="ed-badge-item term">
                    <span class="pulse-dot"></span>
                    <span>{{ __('الفصل الدراسي الحالي') }}</span>
                </span>
                <span class="ed-badge-item sup">
                    <i class="fa-solid fa-user-tie"></i>
                    <span>{{ __('المشرف العام: أ. أحمد حسين شمالي') }}</span>
                </span>
            </div>

            <h1 class="ed-welcome-title">
                {{ __('مرحباً بك،') }} <span>{{ $student->name_ar ?? __('طالبنا المتميز') }}</span>
            </h1>

            <p class="ed-welcome-quote" id="dailyTipText">
                💡 ركز على تنظيم ساعات دراستك اليومية وحل نماذج الامتحانات الاسترشادية باستمرار لضمان التفوق.
            </p>

            <div class="ed-welcome-actions">
                <a href="{{ route('student.subjects.index') }}" class="ed-btn-classic primary">
                    <i class="fas fa-play-circle"></i>
                    <span>{{ __('متابعة الدراسة الآن') }}</span>
                </a>
                <a href="{{ route('student.planner.index') }}" class="ed-btn-classic secondary">
                    <i class="fas fa-calendar-check"></i>
                    <span>{{ __('جدول المراجعة') }}</span>
                </a>
            </div>
        </div>

        {{-- إحصائيات الطالب الحية الكلاسيكية --}}
        @php $currentStudent = Auth::guard('student')->user() ?? $student; @endphp
        <div class="stats-row-clean" style="grid-template-columns: repeat(3, 1fr); margin-top: 18px;">
            <div class="stat-card-clean" style="--card-accent: #d97706;">
                <span class="stat-label">{{ __('الالتزام المتتالي') }}</span>
                <div class="stat-value-wrap">
                    <span class="stat-number text-amber">{{ $currentStudent->streak_count ?? 1 }} <small style="font-size: 0.85rem; color: #64748b;">{{ __('أيام') }}</small></span>
                    <i class="fas fa-fire stat-icon text-amber"></i>
                </div>
                <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">{{ __('حضور وتفاعل يومي') }}</small>
            </div>

            <div class="stat-card-clean" style="--card-accent: #059669;">
                <span class="stat-label">{{ __('المعدل العام') }}</span>
                <div class="stat-value-wrap">
                    <span class="stat-number text-emerald">{{ number_format($my_stats['avg_grade'] ?? 0, 1) }}%</span>
                    <i class="fas fa-chart-line stat-icon text-emerald"></i>
                </div>
                <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">{{ __('متوسط الدرجات المحرزة') }}</small>
            </div>

            <div class="stat-card-clean" style="--card-accent: #1e3a8a;">
                <span class="stat-label">{{ __('امتحانات منجزة') }}</span>
                <div class="stat-value-wrap">
                    <span class="stat-number text-navy">{{ $my_stats['completed_exams'] ?? 0 }} <small style="font-size: 0.85rem; color: #64748b;">{{ __('منجز') }}</small></span>
                    <i class="fas fa-clipboard-check stat-icon text-navy"></i>
                </div>
                <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">
                    @if(($my_stats['available_exams_count'] ?? 0) > 0)
                        ({{ $my_stats['available_exams_count'] }} {{ __('اختبار بانتظارك') }})
                    @else
                        {{ __('جميع النماذج مكتملة') }}
                    @endif
                </small>
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
                    <a href="{{ route('student.checkout.receipt', $pendingPay->id) }}" class="ed-btn-classic secondary" style="font-size: 0.8rem; padding: 7px 14px;">
                        <i class="far fa-file-alt"></i> {{ __('الإيصال') }}
                    </a>
                    @php $waDirect = '970597694385'; @endphp
                    <a href="https://wa.me/{{ $waDirect }}?text={{ urlencode('مرحباً إدارة منارة التوجيهي، قمت برفع إشعار دفع برقم: ' . $pendingPay->transaction_number . ' للاعتماد.') }}" target="_blank" class="ed-btn-classic success" style="font-size: 0.8rem; padding: 7px 14px;">
                        <i class="fab fa-whatsapp"></i> {{ __('تواصل مع المشرف (واتساب)') }}
                    </a>
                </div>
            </div>
        @endforeach
    @endif

    {{-- 3. شريط العد التنازلي للتوجيهي الكلاسيكي --}}
    <div class="ed-countdown-panel-classic">
        <div class="ed-cd-info">
            <div class="ed-cd-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <div class="ed-cd-tag">
                    <i class="fa-solid fa-flag"></i> {{ __('دورة امتحانات فلسطين الوزارية 🇵🇸') }}
                </div>
                <h3>{{ __('العد التنازلي لانطلاق امتحانات التوجيهي') }}</h3>
                <small>{{ __('الوقت يمضي سريعاً، كل دقيقة تقضيها بتركيز تقربك من كليات القمة.') }}</small>
            </div>
        </div>

        <div class="ed-cd-clock">
            <div class="ed-clock-unit">
                <span class="num" id="cntDays">--</span>
                <span class="txt">{{ __('يوم') }}</span>
            </div>
            <div class="ed-clock-sep">:</div>
            <div class="ed-clock-unit">
                <span class="num" id="cntHours">--</span>
                <span class="txt">{{ __('ساعة') }}</span>
            </div>
            <div class="ed-clock-sep">:</div>
            <div class="ed-clock-unit">
                <span class="num" id="cntMinutes">--</span>
                <span class="txt">{{ __('دقيقة') }}</span>
            </div>
            <div class="ed-clock-sep">:</div>
            <div class="ed-clock-unit sec">
                <span class="num" id="cntSeconds">--</span>
                <span class="txt">{{ __('ثانية') }}</span>
            </div>
        </div>
    </div>

    {{-- 4. أدوات التفوق الدراسي والخدمات الأكاديمية الذكية --}}
    <div class="ed-section-box">
        <div class="ed-section-head">
            <div class="ed-sh-title">
                <i class="fas fa-th-large" style="color: #1e3a8a;"></i>
                <h2>{{ __('أدوات التفوق الدراسي والخدمات الذكية') }}</h2>
            </div>
            <span class="ed-sh-hint">{{ __('وصول مباشر لأقسامك المفضلة') }}</span>
        </div>

        <div class="ed-tools-grid-classic">
            <a href="{{ route('student.subjects.index') }}" class="ed-tool-card-classic">
                <div class="tool-icon-circle blue">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="tool-info">
                    <h4>{{ __('مناهجي ومقرراتي') }}</h4>
                    <p>{{ __('الدروس والشروحات المعتمدة') }}</p>
                </div>
                <i class="fas fa-arrow-left tool-arrow"></i>
            </a>

            <a href="{{ route('student.exams.index') }}" class="ed-tool-card-classic">
                <div class="tool-icon-circle green">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div class="tool-info">
                    <h4>{{ __('قاعة الاختبارات') }}</h4>
                    <p>{{ __('نماذج وزارية وامتحانات محاكية') }}</p>
                </div>
                <i class="fas fa-arrow-left tool-arrow"></i>
            </a>

            <a href="{{ route('student.planner.index') }}" class="ed-tool-card-classic">
                <div class="tool-icon-circle sky">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="tool-info">
                    <h4>{{ __('جدول المراجعة الذكي') }}</h4>
                    <p>{{ __('خطة دراسية للأيام المتبقية') }}</p>
                </div>
                <i class="fas fa-arrow-left tool-arrow"></i>
            </a>

            <a href="{{ route('student.flashcards.index') }}" class="ed-tool-card-classic">
                <div class="tool-icon-circle purple">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="tool-info">
                    <h4>{{ __('بطاقات القوانين') }}</h4>
                    <p>{{ __('استذكار سريع وتكرار متباعد') }}</p>
                </div>
                <i class="fas fa-arrow-left tool-arrow"></i>
            </a>

            <a href="{{ route('student.leaderboard') }}" class="ed-tool-card-classic">
                <div class="tool-icon-circle amber">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="tool-info">
                    <h4>{{ __('لوحة الشرف') }}</h4>
                    <p>{{ __('أوائل طلبة فلسطين ونقاطك') }}</p>
                </div>
                <i class="fas fa-arrow-left tool-arrow"></i>
            </a>

            <a href="{{ route('student.achievements') }}" class="ed-tool-card-classic">
                <div class="tool-icon-circle gold">
                    <i class="fas fa-award"></i>
                </div>
                <div class="tool-info">
                    <h4>{{ __('الشهادات وغرفة التركيز') }}</h4>
                    <p>{{ __('سجل الإنجاز ومؤقت بومودورو') }}</p>
                </div>
                <i class="fas fa-arrow-left tool-arrow"></i>
            </a>

            <a href="{{ route('tawjihi.calculator') }}" target="_blank" class="ed-tool-card-classic">
                <div class="tool-icon-circle rose">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="tool-info">
                    <h4>{{ __('حاسبة المعدل') }}</h4>
                    <p>{{ __('دليل التنسيق والقبول الجامعي') }}</p>
                </div>
                <i class="fas fa-arrow-up-right-from-square tool-arrow"></i>
            </a>

            <a href="{{ route('student.teachers.index') }}" class="ed-tool-card-classic">
                <div class="tool-icon-circle violet">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="tool-info">
                    <h4>{{ __('معلمو مرحلتي') }}</h4>
                    <p>{{ __('مراسلة وإرشاد أكاديمي مباشر') }}</p>
                </div>
                <i class="fas fa-arrow-left tool-arrow"></i>
            </a>
        </div>
    </div>

    {{-- 5. شبكة الاختبارات والهدف الأكاديمي --}}
    <div class="ed-main-grid-classic">

        {{-- الاختبارات المقترحة --}}
        <div class="ed-panel-classic exams-col">
            <div class="ed-panel-header">
                <div class="panel-title">
                    <i class="far fa-clock"></i>
                    <h3>{{ __('اختبارات مقترحة بانتظارك') }}</h3>
                </div>
                <a href="{{ route('student.exams.index') }}" class="ed-panel-link">
                    <span>{{ __('عرض كل الاختبارات') }}</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <div class="ed-panel-body">
                @forelse($available_exams as $ex)
                    <div class="ed-exam-item-row">
                        <div class="exam-info">
                            <span class="exam-subject-tag">
                                {{ $ex->subject->name_ar ?? 'مبحث دراسي' }}
                            </span>
                            <h4>{{ $ex->title }}</h4>
                            <div class="exam-meta">
                                <span><i class="far fa-question-circle"></i> {{ $ex->questions_count ?? ($ex->questions ? $ex->questions->count() : 10) }} {{ __('سؤال') }}</span>
                                <span><i class="far fa-clock"></i> {{ $ex->duration_minutes ?? '30' }} {{ __('دقيقة') }}</span>
                            </div>
                        </div>

                        <a href="{{ route('student.exams.take', $ex->id) }}" class="ed-btn-classic primary" style="font-size: 0.85rem; padding: 8px 16px; white-space: nowrap;">
                            <span>{{ __('بدء الاختبار') }}</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                @empty
                    <div class="ed-empty-state">
                        <i class="fas fa-check-circle" style="color: #16a34a; font-size: 2.2rem; margin-bottom: 8px;"></i>
                        <h4>رائع جداً! لا توجد اختبارات معلقة</h4>
                        <p>أكملت كافة النماذج المتاحة لمرحلتك حالياً، تابع دراسة الوحدات الجديدة.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- الجانب الأيسر: الهدف وآخر الدرجات --}}
        <div class="ed-panel-side">
            
            {{-- بطاقة الهدف الأكاديمي --}}
            <div class="ed-panel-classic goal-card">
                <div class="goal-header">
                    <div class="goal-icon"><i class="fas fa-bullseye"></i></div>
                    <div>
                        <h4>{{ __('هدفي الأكاديمي للثانوية') }}</h4>
                        <span>{{ __('طريقي نحو الجامعة') }}</span>
                    </div>
                </div>
                <div class="goal-body">
                    <div class="goal-stat-row">
                        <span class="lbl">{{ __('معدلي المستهدف:') }}</span>
                        <strong class="val">98.5% +</strong>
                    </div>
                    <p class="goal-quote">
                        "كل جهد تبذله في المراجعة وحل المسائل يرفع فرصك في الحصول على المقعد الجامعي الذي تحلم به."
                    </p>
                    <a href="{{ route('student.achievements') }}" class="ed-btn-classic secondary" style="width: 100%; justify-content: center; font-size: 0.84rem; padding: 9px;">
                        <i class="fas fa-hourglass-start"></i> {{ __('بدء جلسة تركيز (بومودورو)') }}
                    </a>
                </div>
            </div>

            {{-- آخر الاختبارات المكتملة --}}
            <div class="ed-panel-classic">
                <div class="ed-panel-header">
                    <div class="panel-title">
                        <i class="fas fa-history" style="color: #16a34a;"></i>
                        <h4 style="font-size: 0.95rem; font-weight: 800; margin: 0;">{{ __('آخر درجاتك المحرزة') }}</h4>
                    </div>
                </div>

                <div class="ed-panel-body" style="padding: 10px 18px;">
                    @forelse($completed_exams ?? [] as $done_exam)
                        <div class="ed-completed-row">
                            <div>
                                <strong class="title">{{ $done_exam->exam->title ?? $done_exam->title }}</strong>
                                <span class="date">{{ $done_exam->created_at ? $done_exam->created_at->format('Y/m/d') : 'مؤخراً' }}</span>
                            </div>
                            <div>
                                @if($done_exam->status == 'graded')
                                    <span class="grade-badge passed">
                                        {{ number_format($done_exam->total_earned_grade, 1) }}%
                                    </span>
                                @else
                                    <span class="grade-badge pending">
                                        {{ __('قيد المراجعة') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="ed-empty-state" style="padding: 20px 10px;">
                            <i class="far fa-folder-open" style="font-size: 1.6rem; opacity: 0.5; margin-bottom: 6px;"></i>
                            <p style="margin: 0; font-size: 0.84rem;">لم تقم بتسليم أي اختبارات بعد.</p>
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
        gap: 24px;
        width: 100%;
    }

    /* 1. الترويسة الأكاديمية الكلاسيكية */
    .ed-dash-header-classic {
        background: #172554;
        background: linear-gradient(135deg, #172554 0%, #1e3a8a 100%);
        border-radius: 12px;
        border-bottom: 4px solid #f59e0b;
        padding: 28px 32px;
        color: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
    }

    .ed-header-main-info {
        max-width: 620px;
    }

    .ed-header-badges {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .ed-badge-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #f8fafc;
    }

    .ed-badge-item.term {
        background: rgba(16, 185, 129, 0.18);
        border-color: rgba(16, 185, 129, 0.35);
        color: #a7f3d0;
    }

    .pulse-dot {
        width: 7px;
        height: 7px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
    }

    .ed-badge-item.sup {
        background: rgba(245, 158, 11, 0.15);
        border-color: rgba(245, 158, 11, 0.3);
        color: #fde68a;
    }

    .ed-welcome-title {
        font-size: 1.85rem;
        font-weight: 900;
        margin: 0 0 8px;
        color: #ffffff;
    }

    .ed-welcome-title span {
        color: #fde047;
    }

    .ed-welcome-quote {
        font-size: 0.92rem;
        color: #cbd5e1;
        line-height: 1.6;
        margin: 0 0 20px;
    }

    .ed-welcome-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .ed-btn-classic {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 20px;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
    }

    .ed-btn-classic.primary {
        background: #f59e0b;
        color: #0f172a;
    }
    .ed-btn-classic.primary:hover {
        background: #d97706;
        color: #ffffff;
        text-decoration: none;
    }

    .ed-btn-classic.secondary {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.25);
    }
    .ed-btn-classic.secondary:hover {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        text-decoration: none;
    }

    .ed-btn-classic.success {
        background: #16a34a;
        color: #ffffff;
    }
    .ed-btn-classic.success:hover {
        background: #15803d;
        color: #ffffff;
        text-decoration: none;
    }

    /* صناديق الإحصاءات الكلاسيكية */
    .ed-header-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        min-width: 380px;
    }

    .ed-stat-box {
        background: #ffffff;
        border-radius: 10px;
        padding: 16px;
        color: #0f172a;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        border-top: 3px solid #1e3a8a;
    }

    .ed-stat-box.streak { border-top-color: #f97316; }
    .ed-stat-box.gpa { border-top-color: #1e3a8a; }
    .ed-stat-box.exams { border-top-color: #16a34a; }

    .stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .stat-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
    }

    .stat-icon {
        font-size: 1rem;
        color: #94a3b8;
    }
    .streak .stat-icon { color: #ea580c; }
    .gpa .stat-icon { color: #1e40af; }
    .exams .stat-icon { color: #16a34a; }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.2;
    }

    .stat-number small {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
    }

    .stat-note {
        display: block;
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 4px;
    }

    .stat-note.active-note {
        color: #0284c7;
        font-weight: 700;
    }

    /* 2. تنبيه المدفوعات */
    .ed-alert-banner {
        background: #fffbeb;
        border: 1px solid #fef3c7;
        border-right: 4px solid #f59e0b;
        border-radius: 10px;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .ed-ab-content {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .ed-ab-icon {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background: #fef3c7;
        color: #d97706;
        display: grid;
        place-items: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .ed-ab-content h4 {
        margin: 0 0 4px;
        font-size: 0.95rem;
        font-weight: 800;
        color: #92400e;
    }

    .ed-ab-content p {
        margin: 0;
        font-size: 0.85rem;
        color: #78350f;
    }

    .ed-ab-actions {
        display: flex;
        gap: 10px;
    }

    /* 3. شريط العد التنازلي الكلاسيكي */
    .ed-countdown-panel-classic {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-top: 3px solid #1e3a8a;
        border-radius: 10px;
        padding: 18px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
    }

    .ed-cd-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .ed-cd-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: #eff6ff;
        color: #1e3a8a;
        display: grid;
        place-items: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .ed-cd-tag {
        font-size: 0.74rem;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ed-cd-info h3 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-cd-info small {
        color: #64748b;
        font-size: 0.8rem;
    }

    .ed-cd-clock {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ed-clock-unit {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 14px;
        text-align: center;
        min-width: 58px;
    }

    .ed-clock-unit .num {
        display: block;
        font-size: 1.45rem;
        font-weight: 900;
        color: #1e3a8a;
        line-height: 1.1;
        font-family: monospace;
    }

    .ed-clock-unit .txt {
        font-size: 0.7rem;
        font-weight: 700;
        color: #64748b;
    }

    .ed-clock-sep {
        font-size: 1.2rem;
        font-weight: 900;
        color: #94a3b8;
    }

    /* 4. أدوات التفوق الكلاسيكية */
    .ed-section-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 22px 24px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
    }

    .ed-section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-sh-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ed-sh-title h2 {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .ed-sh-hint {
        font-size: 0.8rem;
        color: #64748b;
    }

    .ed-tools-grid-classic {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .ed-tool-card-classic {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
        position: relative;
    }

    .ed-tool-card-classic:hover {
        background: #ffffff;
        border-color: #1e3a8a;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.08);
        transform: translateY(-2px);
        text-decoration: none;
    }

    .tool-icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .tool-icon-circle.blue { background: #eff6ff; color: #1d4ed8; }
    .tool-icon-circle.green { background: #ecfdf5; color: #059669; }
    .tool-icon-circle.sky { background: #f0f9ff; color: #0284c7; }
    .tool-icon-circle.purple { background: #faf5ff; color: #7e22ce; }
    .tool-icon-circle.amber { background: #fffbeb; color: #d97706; }
    .tool-icon-circle.gold { background: #fefce8; color: #ca8a04; }
    .tool-icon-circle.rose { background: #fff1f2; color: #e11d48; }
    .tool-icon-circle.violet { background: #f5f3ff; color: #6d28d9; }

    .tool-info {
        flex: 1;
        min-width: 0;
    }

    .tool-info h4 {
        margin: 0 0 3px;
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
    }

    .tool-info p {
        margin: 0;
        font-size: 0.76rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tool-arrow {
        font-size: 0.8rem;
        color: #94a3b8;
        transition: all 0.2s ease;
    }

    .ed-tool-card-classic:hover .tool-arrow {
        color: #1e3a8a;
        transform: translateX(-3px);
    }
    html[dir="ltr"] .ed-tool-card-classic:hover .tool-arrow {
        transform: translateX(3px);
    }

    /* 5. شبكة الاختبارات والهدف الأكاديمي */
    .ed-main-grid-classic {
        display: grid;
        grid-template-columns: 2fr 1.1fr;
        gap: 20px;
    }

    .ed-panel-classic {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
    }

    .ed-panel-header {
        padding: 16px 20px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .panel-title {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #1e3a8a;
    }

    .panel-title h3 {
        margin: 0;
        font-size: 1.02rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-panel-link {
        font-size: 0.82rem;
        font-weight: 700;
        color: #1e3a8a;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ed-panel-body {
        padding: 16px 20px;
    }

    .ed-exam-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-exam-item-row:last-child {
        border-bottom: none;
    }

    .exam-subject-tag {
        font-size: 0.74rem;
        font-weight: 700;
        color: #1e3a8a;
        background: #eff6ff;
        padding: 2px 8px;
        border-radius: 4px;
        display: inline-block;
        margin-bottom: 4px;
    }

    .exam-info h4 {
        margin: 0 0 6px;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
    }

    .exam-meta {
        display: flex;
        gap: 16px;
        font-size: 0.78rem;
        color: #64748b;
    }

    .ed-empty-state {
        text-align: center;
        padding: 36px 20px;
        color: #64748b;
    }

    .ed-panel-side {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* بطاقة الهدف */
    .goal-card {
        padding: 20px;
    }

    .goal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .goal-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #eff6ff;
        color: #1e3a8a;
        display: grid;
        place-items: center;
        font-size: 1.15rem;
    }

    .goal-header h4 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
    }

    .goal-header span {
        font-size: 0.76rem;
        color: #64748b;
    }

    .goal-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 12px;
    }

    .goal-stat-row .lbl {
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
    }

    .goal-stat-row .val {
        font-size: 1.25rem;
        font-weight: 900;
        color: #1e3a8a;
    }

    .goal-quote {
        font-size: 0.8rem;
        color: #64748b;
        line-height: 1.6;
        margin: 0 0 14px;
        font-style: italic;
    }

    /* الدرجات المكتملة */
    .ed-completed-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-completed-row:last-child {
        border-bottom: none;
    }

    .ed-completed-row .title {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .ed-completed-row .date {
        font-size: 0.72rem;
        color: #94a3b8;
    }

    .grade-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 800;
    }

    .grade-badge.passed {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .grade-badge.pending {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fef3c7;
    }

    /* استجابة الشاشات */
    @media (max-width: 1100px) {
        .ed-tools-grid-classic { grid-template-columns: repeat(2, 1fr); }
        .ed-header-stats-grid { min-width: 100%; }
        .ed-main-grid-classic { grid-template-columns: 1fr; }
    }

    @media (max-width: 650px) {
        .ed-dash-header-classic { padding: 20px 18px; }
        .ed-tools-grid-classic { grid-template-columns: 1fr; }
        .ed-header-stats-grid { grid-template-columns: 1fr; }
        .ed-countdown-panel-classic { padding: 16px; }
        .ed-clock-unit { min-width: 46px; padding: 6px 8px; }
        .ed-clock-unit .num { font-size: 1.15rem; }
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
