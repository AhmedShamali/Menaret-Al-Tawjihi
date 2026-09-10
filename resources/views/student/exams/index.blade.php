@extends('layouts.app')

@section('title', 'قاعة الاختبارات والتقييم الأكاديمي | منارة التوجيهي')

@section('content')
<div class="ed-exams-container">

    <!-- هيدر البوابة الأكاديمية -->
    <header class="ed-exams-header">
        <div class="ed-exams-title-box">
            <div class="ed-exams-breadcrumbs">
                <i class="fas fa-home"></i>
                <a href="{{ route('student.dashboard') }}" style="color: inherit; text-decoration: none;">لوحة الطالب</a>
                <i class="fas fa-chevron-left divider"></i>
                <span class="active">الاختبارات الأكاديمية</span>
            </div>
            <h1>قاعة الاختبارات والتقييم الأكاديمي</h1>
            <p>
                مرحباً بك <strong style="color: #0f172a;">{{ optional($student)->name_ar ?? auth()->user()->name }}</strong>، إليك جدول الاختبارات والتقييمات المعتمدة لمسيرتك الدراسية.
            </p>
        </div>

        <div class="ed-exams-header-meta">
            <div class="ed-stage-tag">
                <i class="fas fa-layer-group"></i>
                <span>المرحلة: <strong>{{ $currentStageName ?? 'غير محددة' }}</strong></span>
            </div>
            <div class="ed-date-tag">
                <i class="far fa-calendar-alt"></i>
                <span>{{ date('Y/m/d') }}</span>
            </div>
        </div>
    </header>

    <!-- تنبيه إذا كان الحساب غير مرتبط بمرحلة -->
    @if(!$student || !$student->stage_id)
        <div class="ed-alert warning">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>تنبيه أكاديمي:</strong>
                <span>حسابك غير مرتبط بفرع دراسي محدد في قاعدة البيانات، يرجى مراجعة إدارة المنصة لضبط مرحلتك الدراسية.</span>
            </div>
        </div>
    @endif

    <!-- تنبيهات النجاح والخطأ -->
    @if(session('success'))
        <div class="ed-alert success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="ed-alert error">
            <i class="fas fa-times-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- شريط معلومات القسم -->
    <div class="ed-section-bar">
        <div class="ed-section-title">
            <i class="fas fa-clipboard-check"></i>
            <span>الاختبارات والتقييمات المتاحة لمرحلتك</span>
        </div>
        <span class="ed-count-badge">
            إجمالي الاختبارات: <strong>{{ isset($exams) ? $exams->count() : 0 }}</strong>
        </span>
    </div>

    <!-- شبكة الاختبارات -->
    <div class="ed-exams-grid">
        @forelse($exams as $exam)
            @php
                $hasSubmitted = $student && $exam->submissions && $exam->submissions->where('student_id', $student->id)->isNotEmpty();
                $submissionRecord = $hasSubmitted ? $exam->submissions->where('student_id', $student->id)->first() : null;
            @endphp
            <div class="ed-exam-card {{ $hasSubmitted ? 'completed' : '' }}">
                <div class="ed-exam-card-body">
                    <div class="ed-exam-tags">
                        <span class="ed-badge ed-badge-blue">
                            {{ optional($exam->subject)->name_ar ?? (optional($exam->subject)->name ?? 'مادة دراسية') }}
                        </span>
                        <span class="ed-badge ed-badge-gray">
                            {{ optional($exam->stage)->name_ar ?? (optional($exam->stage)->name ?? 'عام') }}
                        </span>
                    </div>

                    <h3 class="ed-exam-title">{{ $exam->title }}</h3>

                    <p class="ed-exam-desc">
                        {{ $exam->description ?? 'اختبار معتمد ضمن خطتك الدراسية لهذا الفصل، يرجى الالتزام بالوقت المخصص وقراءة الأسئلة بعناية.' }}
                    </p>

                    <div class="ed-exam-meta-grid">
                        <div class="ed-meta-item">
                            <span class="meta-label">المدة المخصصة</span>
                            <span class="meta-val"><i class="far fa-clock"></i> {{ $exam->duration_minutes }} دقيقة</span>
                        </div>
                        <div class="ed-meta-item">
                            <span class="meta-label">عدد الأسئلة</span>
                            <span class="meta-val"><i class="far fa-question-circle"></i> {{ $exam->questions_count ?? ($exam->questions ? $exam->questions->count() : 0) }} سؤال</span>
                        </div>
                    </div>
                </div>

                <div class="ed-exam-card-footer">
                    @if($hasSubmitted)
                        <div class="ed-submitted-actions">
                            <span class="ed-btn ed-btn-completed">
                                <i class="fas fa-check"></i> تم التقديم
                            </span>
                            @if($submissionRecord)
                                <a href="{{ route('student.exams.result', $submissionRecord->id) }}" class="ed-btn ed-btn-outline" style="font-size: 0.85rem; padding: 10px 16px;">
                                    عرض النتيجة
                                </a>
                            @endif
                        </div>
                    @else
                        <button type="button" onclick="confirmStartExam('{{ route('student.exams.take', $exam->id) }}')" class="ed-btn ed-btn-primary" style="width: 100%; justify-content: center;">
                            <span>بدء الاختبار الآن</span>
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="ed-empty-card">
                <div class="ed-empty-icon"><i class="fas fa-folder-open"></i></div>
                <h3>لا توجد اختبارات متاحة حالياً</h3>
                <p>لم يتم طرح أي اختبارات جديدة لمرحلتك الدراسية في الوقت الحالي، تابع لوحتك دورياً للاطلاع على أي تحديثات.</p>
            </div>
        @endforelse
    </div>

</div>

<style>
    .ed-exams-container {
        padding: 24px 32px 60px;
        direction: rtl;
        font-family: 'Alexandria', 'Tajawal', sans-serif;
    }

    /* Header */
    .ed-exams-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .ed-exams-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: #64748b;
        margin-bottom: 8px;
    }

    .ed-exams-breadcrumbs .divider {
        font-size: 0.65rem;
        color: #cbd5e1;
    }

    .ed-exams-breadcrumbs .active {
        color: #1d4ed8;
        font-weight: 600;
    }

    .ed-exams-title-box h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .ed-exams-title-box p {
        font-size: 0.9rem;
        color: #64748b;
        margin: 0;
    }

    .ed-exams-header-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .ed-stage-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        color: #1e40af;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .ed-stage-tag strong {
        color: #1d4ed8;
    }

    .ed-date-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* Alerts */
    .ed-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 14px;
        font-size: 0.88rem;
        margin-bottom: 20px;
    }

    .ed-alert.warning {
        background: #fffbeb;
        border: 1px solid #fef3c7;
        color: #92400e;
    }

    .ed-alert.success {
        background: #ecfdf5;
        border: 1px solid #d1fae5;
        color: #065f46;
    }

    .ed-alert.error {
        background: #fef2f2;
        border: 1px solid #fee2e2;
        color: #991b1b;
    }

    /* Section Bar */
    .ed-section-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .ed-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-section-title i {
        color: #1d4ed8;
    }

    .ed-count-badge {
        font-size: 0.82rem;
        color: #64748b;
        background: #f1f5f9;
        padding: 4px 12px;
        border-radius: 999px;
    }

    .ed-count-badge strong {
        color: #1d4ed8;
    }

    /* Grid */
    .ed-exams-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    .ed-exam-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .ed-exam-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }

    .ed-exam-card.completed {
        border-color: #d1fae5;
        background: #fafdfc;
    }

    .ed-exam-tags {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .ed-badge-gray {
        background: #f1f5f9;
        color: #475569;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .ed-exam-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 10px;
        line-height: 1.4;
    }

    .ed-exam-desc {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.6;
        margin: 0 0 20px;
    }

    .ed-exam-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 20px;
    }

    .ed-meta-item {
        text-align: center;
    }

    .ed-meta-item:first-child {
        border-left: 1px solid #e2e8f0;
    }

    .meta-label {
        font-size: 0.72rem;
        color: #94a3b8;
        font-weight: 600;
        display: block;
        margin-bottom: 2px;
    }

    .meta-val {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
    }

    .ed-meta-item i {
        color: #64748b;
        margin-left: 4px;
    }

    .ed-submitted-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .ed-btn-completed {
        flex: 1;
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 700;
        text-align: center;
        cursor: default;
    }

    .ed-empty-card {
        grid-column: 1 / -1;
        background: #ffffff;
        border: 2px dashed #cbd5e1;
        border-radius: 18px;
        padding: 60px 20px;
        text-align: center;
    }

    .ed-empty-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 16px;
    }

    .ed-empty-card h3 {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .ed-empty-card p {
        font-size: 0.88rem;
        color: #64748b;
        max-width: 480px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .ed-exams-container {
            padding: 18px 16px 60px;
        }
        .ed-exams-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmStartExam(takeUrl) {
        Swal.fire({
            title: 'هل أنت مستعد لبدء الاختبار؟',
            text: 'ملاحظة أكاديمية: يُسمح بتقديم الاختبار لمرة واحدة فقط، وسيبدأ توقيت الاختبار بالعد التنازلي مباشرة بمجرد دخول القاعة.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1d4ed8',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، ابدأ الاختبار الآن',
            cancelButtonText: 'العودة لاحقاً'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = takeUrl;
            }
        });
    }
</script>
@endsection
