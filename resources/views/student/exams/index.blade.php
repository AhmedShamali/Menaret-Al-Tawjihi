@extends('layouts.app')

@section('title', __('قاعة الاختبارات والتقييم الأكاديمي') . ' | ' . __('منارة التوجيهي'))

@section('content')
<div class="ed-exams-container">

    <!-- هيدر البوابة الأكاديمية -->
    <header class="ed-exams-header">
        <div class="ed-exams-title-box">
            <div class="ed-exams-breadcrumbs">
                <i class="fas fa-home"></i>
                <a href="{{ route('student.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('لوحة الطالب') }}</a>
                <i class="fas fa-chevron-left divider"></i>
                <span class="active">{{ __('الاختبارات الأكاديمية') }}</span>
            </div>
            <h1>{{ __('قاعة الاختبارات والتقييم الأكاديمي') }}</h1>
            <p>
                {{ __('مرحباً بك') }} <strong style="color: #0f172a;">{{ optional($student)->name_ar ?? auth()->user()->name }}</strong>، {{ __('إليك جدول الاختبارات والتقييمات المعتمدة لمسيرتك الدراسية.') }}
            </p>
        </div>

        <div class="ed-exams-header-meta">
            <div class="ed-stage-tag">
                <i class="fas fa-layer-group"></i>
                <span>{{ __('المرحلة:') }} <strong>{{ $currentStageName ?? __('غير محددة') }}</strong></span>
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
                <strong>{{ __('تنبيه أكاديمي:') }}</strong>
                <span>{{ __('حسابك غير مرتبط بفرع دراسي محدد في قاعدة البيانات، يرجى مراجعة إدارة المنصة لضبط مرحلتك الدراسية.') }}</span>
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
            <span>{{ __('الاختبارات والتقييمات المتاحة لمرحلتك') }}</span>
        </div>
        <span class="ed-count-badge">
            {{ __('إجمالي الاختبارات:') }} <strong>{{ isset($exams) ? $exams->count() : 0 }}</strong>
        </span>
    </div>

    <!-- شبكة الاختبارات -->
    <div class="ed-exams-grid">
        @forelse($exams as $exam)
            @php
                $submissionRecord = ($student && $exam->submissions) ? $exam->submissions->where('student_id', $student->id)->first() : null;
                $hasCompletedSubmission = $submissionRecord && $submissionRecord->answers()->exists();
                $canRetake = $submissionRecord && (bool)$submissionRecord->allow_retake;
            @endphp
            <div class="ed-exam-card {{ $hasCompletedSubmission ? 'completed' : '' }}">
                <div class="ed-exam-card-body">
                    <div class="ed-exam-tags">
                        <span class="ed-badge ed-badge-blue">
                            {{ optional($exam->subject)->name_ar ?? (optional($exam->subject)->name ?? __('مادة دراسية')) }}
                        </span>
                        <span class="ed-badge ed-badge-gray">
                            {{ optional($exam->stage)->name_ar ?? (optional($exam->stage)->name ?? __('عام')) }}
                        </span>
                    </div>

                    <h3 class="ed-exam-title">{{ $exam->title }}</h3>

                    <p class="ed-exam-desc">
                        {{ $exam->description ?? __('اختبار معتمد ضمن خطتك الدراسية لهذا الفصل، يرجى الالتزام بالوقت المخصص وقراءة الأسئلة بعناية.') }}
                    </p>

                    <div class="ed-exam-meta-grid">
                        <div class="ed-meta-item">
                            <span class="meta-label">{{ __('المدة المخصصة') }}</span>
                            <span class="meta-val"><i class="far fa-clock"></i> {{ $exam->duration_minutes }} {{ __('دقيقة') }}</span>
                        </div>
                        <div class="ed-meta-item">
                            <span class="meta-label">{{ __('عدد الأسئلة') }}</span>
                            <span class="meta-val"><i class="far fa-question-circle"></i> {{ $exam->questions_count ?? ($exam->questions ? $exam->questions->count() : 0) }} {{ __('سؤال') }}</span>
                        </div>
                    </div>
                </div>

                <div class="ed-exam-card-footer">
                    @if($canRetake)
                        <div style="width: 100%; display: flex; flex-direction: column; gap: 8px;">
                            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-size: 0.8rem; font-weight: 800; padding: 6px 10px; border-radius: 8px; text-align: center;">
                                <i class="fa-solid fa-rotate-right"></i> {{ __('المعلّم أتاح لك إعادة تقديم الاختبار') }}
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button type="button" onclick="confirmStartExam('{{ route('student.exams.take', $exam->id) }}')" class="ed-btn ed-btn-primary" style="flex: 1; justify-content: center; background: #059669;">
                                    <i class="fa-solid fa-play"></i>
                                    <span>{{ __('إعادة الاختبار الآن') }}</span>
                                </button>
                                <a href="{{ route('student.exams.result', $submissionRecord->id) }}" class="ed-btn ed-btn-outline" style="font-size: 0.82rem; padding: 8px 12px;">
                                    {{ __('النتيجة السابقة') }}
                                </a>
                            </div>
                        </div>
                    @elseif($hasCompletedSubmission)
                        <div class="ed-submitted-actions">
                            <span class="ed-btn ed-btn-completed">
                                <i class="fas fa-check"></i> {{ __('تم التقديم') }}
                            </span>
                            @if($submissionRecord)
                                <a href="{{ route('student.exams.result', $submissionRecord->id) }}" class="ed-btn ed-btn-outline" style="font-size: 0.85rem; padding: 10px 16px;">
                                    {{ __('عرض النتيجة') }}
                                </a>
                            @endif
                        </div>
                    @else
                        <button type="button" onclick="confirmStartExam('{{ route('student.exams.take', $exam->id) }}')" class="ed-btn ed-btn-primary" style="width: 100%; justify-content: center;">
                            <span>{{ __('بدء الاختبار الآن') }}</span>
                            <i class="fas fa-arrow-left arrow-icon"></i>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="ed-empty-card">
                <div class="ed-empty-icon"><i class="fas fa-folder-open"></i></div>
                <h3>{{ __('لا توجد اختبارات متاحة حالياً') }}</h3>
                <p>{{ __('لم يتم طرح أي اختبارات جديدة لمرحلتك الدراسية في الوقت الحالي، تابع لوحتك دورياً للاطلاع على أي تحديثات.') }}</p>
            </div>
        @endforelse
    </div>

    <!-- جدول الاختبارات الكلاسيكي الرسمي -->
    <div class="table-card-clean" style="margin-top: 24px;">
        <div class="table-container-clean">
            <table class="data-table-clean">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th>{{ __('عنوان الاختبار الأكاديمي') }}</th>
                        <th style="width: 170px;">{{ __('المادة الدراسية') }}</th>
                        <th style="width: 120px;">{{ __('المدة') }}</th>
                        <th style="width: 110px;">{{ __('الأسئلة') }}</th>
                        <th style="width: 140px; text-align: center;">{{ __('الحالة والنتيجة') }}</th>
                        <th style="width: 130px; text-align: center;">{{ __('الإجراء') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        @php
                            $submissionRecord = ($student && $exam->submissions) ? $exam->submissions->where('student_id', $student->id)->first() : null;
                            $hasCompletedSubmission = $submissionRecord && $submissionRecord->answers()->exists();
                            $canRetake = $submissionRecord && (bool)$submissionRecord->allow_retake;
                        @endphp
                        <tr>
                            <td style="text-align: center; color: #94a3b8; font-family: monospace; font-size: 0.8rem; font-weight: 700;">
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                <strong style="color: #0f172a; font-size: 0.9rem;">{{ $exam->title }}</strong>
                            </td>
                            <td>
                                <span class="status-pill status-info">
                                    <span class="dot"></span>
                                    {{ optional($exam->subject)->name_ar ?? (optional($exam->subject)->name ?? __('مادة عامة')) }}
                                </span>
                            </td>
                            <td style="color: #475569; font-size: 0.84rem;">
                                <i class="fa-regular fa-clock" style="color: #94a3b8; margin-inline-end: 4px;"></i>
                                {{ $exam->duration_minutes }} {{ __('دقيقة') }}
                            </td>
                            <td style="color: #475569; font-size: 0.84rem;">
                                {{ $exam->questions_count ?? ($exam->questions ? $exam->questions->count() : 0) }} {{ __('سؤال') }}
                            </td>
                            <td style="text-align: center;">
                                @if($canRetake)
                                    <span class="status-pill" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-weight: 800;">
                                        <span class="dot" style="background: #10b981;"></span>
                                        {{ __('متاح للإعادة 🔄') }}
                                    </span>
                                @elseif($hasCompletedSubmission)
                                    <span class="status-pill status-active">
                                        <span class="dot"></span>
                                        {{ __('تم التقديم') }}
                                        @if($submissionRecord && $submissionRecord->status === 'graded')
                                            ({{ $submissionRecord->total_earned_grade }} علامة)
                                        @endif
                                    </span>
                                @else
                                    <span class="status-pill status-pending">
                                        <span class="dot"></span>
                                        {{ __('متاح للبدء') }}
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($canRetake)
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                        <button type="button" onclick="confirmStartExam('{{ route('student.exams.take', $exam->id) }}')" class="tbl-btn" style="background: #059669;">
                                            <i class="fa-solid fa-rotate-right"></i> {{ __('إعادة') }}
                                        </button>
                                        <a href="{{ route('student.exams.result', $submissionRecord->id) }}" class="tbl-btn" style="background: #64748b; padding: 6px 10px;" title="{{ __('النتيجة السابقة') }}">
                                            <i class="fa-solid fa-chart-simple"></i>
                                        </a>
                                    </div>
                                @elseif($hasCompletedSubmission)
                                    @if($submissionRecord)
                                        <a href="{{ route('student.exams.result', $submissionRecord->id) }}" class="tbl-btn" style="background: #059669;">
                                            <i class="fa-solid fa-square-poll-vertical"></i> {{ __('النتيجة') }}
                                        </a>
                                    @else
                                        <span style="color: #16a34a; font-size: 0.8rem; font-weight: 700;">
                                            <i class="fa-solid fa-check"></i> {{ __('مكتمل') }}
                                        </span>
                                    @endif
                                @else
                                    <button type="button" onclick="confirmStartExam('{{ route('student.exams.take', $exam->id) }}')" class="tbl-btn" style="background: #1e3a8a;">
                                        <i class="fa-solid fa-play"></i> {{ __('بدء الاختبار') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                {{ __('لا توجد اختبارات متاحة حالياً لمرحلتك الدراسية.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .ed-exams-container {
        padding: 0 0 60px;
        width: 100%;
        box-sizing: border-box;
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
