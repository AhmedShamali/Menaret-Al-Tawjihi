@extends('layouts.app')

@section('title', __('كشف درجات الاختبار الأكاديمي') . ' | ' . $submission->exam->title)

@section('content')
<div class="ed-results-container">

    {{-- كشف العلامات الأكاديمي الكلاسيكي --}}
    <div class="ed-report-card">
        <header class="ed-report-header">
            <div class="ed-report-header-info">
                <div class="ed-academic-tag">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>{{ __('الثانوية العامة - فلسطين') }}</span>
                    <span class="dot">•</span>
                    <span>{{ optional($submission->exam->subject)->name_ar ?? optional($submission->exam->subject)->name ?? __('مادة دراسية') }}</span>
                </div>
                <h1 class="ed-report-title">{{ $submission->exam->title }}</h1>
                <p class="ed-report-subtitle">
                    {{ __('مراجعة الإجابات المفصلة') }} — {{ optional($submission->student)->name_ar ?? auth()->user()->name }}
                </p>
            </div>

            @php
                $totalMax = (float) $submission->exam->questions->sum('points');
                $earned = (float) $submission->total_earned_grade;
                $pct = $totalMax > 0 ? round(($earned / $totalMax) * 100, 1) : 0;
            @endphp
            <div class="ed-report-score-box">
                <span class="ed-score-label">{{ __('الدرجة المحققة') }}</span>
                <div class="ed-score-values">
                    <span class="ed-score-earned">{{ $earned }}</span>
                    <span class="ed-score-total">/ {{ $totalMax }}</span>
                </div>
                <div class="ed-score-percentage">
                    <span>{{ $pct }}%</span>
                </div>
            </div>
        </header>

        <div class="ed-report-body">
            <div class="ed-report-section-title">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>{{ __('مراجعة الإجابات:') }}</span>
            </div>

            <div class="ed-questions-list">
                @foreach($submission->answers as $idx => $ans)
                    @php
                        $awarded = (float)$ans->points_awarded;
                        $max = (float)$ans->question->points;

                        if($ans->question->type == 'mcq') {
                            $isCorrect = ($awarded >= $max && $max > 0);
                            $statusClass = $isCorrect ? 'correct' : 'wrong';
                            $statusText = $isCorrect ? __('إجابة صحيحة') : __('إجابة خاطئة');
                            $icon = $isCorrect ? 'fa-check' : 'fa-xmark';
                        } else {
                            $statusClass = $awarded > 0 ? 'correct' : 'pending';
                            $statusText = $awarded > 0 ? __('تم التصحيح') : __('بانتظار تصحيح المعلم');
                            $icon = 'fa-clock';
                        }
                    @endphp

                    <div class="ed-question-item {{ $statusClass }}">
                        <div class="ed-q-topbar">
                            <span class="ed-q-badge-num">{{ __('السؤال') }} {{ $idx + 1 }}</span>
                            <div class="ed-status-badge">
                                <i class="fa-solid {{ $icon }}"></i>
                                <span>{{ $statusText }} ({{ $awarded }} / {{ $max }})</span>
                            </div>
                        </div>

                        <div class="ed-q-content">
                            <h3 class="ed-q-text">{!! nl2br(e($ans->question->question_text)) !!}</h3>

                            @if($ans->question->type == 'mcq')
                                <div class="ed-mcq-options">
                                    @foreach(['a', 'b', 'c', 'd'] as $opt)
                                        @if($ans->question->$opt)
                                            @php
                                                $isCorrectOpt = (strtolower($ans->question->correct_answer) == $opt);
                                                $isStudentOpt = (strtolower($ans->answer_text) == $opt);
                                            @endphp
                                            <div class="ed-option-cell {{ $isCorrectOpt ? 'option-correct' : ($isStudentOpt && !$isCorrectOpt ? 'option-wrong' : '') }}">
                                                <strong class="opt-letter">{{ strtoupper($opt) }}:</strong>
                                                <span class="opt-text">{{ $ans->question->$opt }}</span>
                                                @if($isStudentOpt)
                                                    <span class="opt-student-tag">{{ __('إجابتك') }}</span>
                                                @endif
                                                @if($isCorrectOpt)
                                                    <span class="opt-correct-tag"><i class="fa-solid fa-check"></i></span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="ed-essay-answer">
                                    <span class="essay-label">{{ __('الإجابة المسجلة:') }}</span>
                                    <div class="essay-text">{{ $ans->answer_text ?? __('تم رفع ملف مرفق لهذا السؤال') }}</div>
                                </div>
                                @if($ans->file_path)
                                    <a href="{{ asset('storage/'.$ans->file_path) }}" target="_blank" class="ed-btn-file">
                                        <i class="fa-solid fa-paperclip"></i>
                                        <span>{{ __('عرض الملف المرفق') }}</span>
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- حالة وقرار إعادة الاختبار --}}
            <div class="ed-retake-section">
                @if($submission->allow_retake)
                    <div class="ed-retake-approved">
                        <i class="fa-solid fa-unlock-keyhole"></i>
                        <span>{{ __('وافق أستاذ المادة على إعادة هذا الاختبار لك! يمكنك البدء بمحاولة جديدة.') }}</span>
                    </div>
                    <a href="{{ route('student.exams.take', $submission->exam_id) }}" class="ed-btn-retake-start">
                        <i class="fa-solid fa-play"></i>
                        <span>{{ __('بدء محاولة الاختبار الآن') }}</span>
                    </a>
                @elseif($submission->retake_requested)
                    <div class="ed-retake-waiting">
                        <i class="fa-solid fa-hourglass-half fa-spin"></i>
                        <span>{{ __('تم إرسال طلب إعادة الاختبار لأستاذ المادة، وهو قيد المراجعة حالياً.') }}</span>
                    </div>
                @else
                    <div class="ed-retake-prompt">
                        <div class="ed-retake-text">
                            <strong>{{ __('هل واجهت مشكلة أو ترغب في تحسين أدائك؟') }}</strong>
                            <span>{{ __('يمكنك إرسال طلب إلكتروني لمعلم المادة للسماح لك بفرصة إعادة الاختبار.') }}</span>
                        </div>
                        <button type="button" onclick="requestRetakePrompt()" class="ed-btn-retake-req">
                            <i class="fa-solid fa-rotate-right"></i>
                            <span>{{ __('طلب إذن إعادة الاختبار') }}</span>
                        </button>
                    </div>
                @endif
            </div>

            <div class="ed-report-actions">
                <a href="{{ route('student.exams.index') }}" class="ed-btn-back">
                    <i class="fa-solid fa-arrow-right arrow-icon"></i>
                    <span>{{ __('العودة لقاعة الاختبارات') }}</span>
                </a>
                <button onclick="window.print()" class="ed-btn-print">
                    <i class="fa-solid fa-print"></i>
                    <span>{{ __('طباعة التقرير') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ==========================================================
   CLASSIC ACADEMIC EXAM RESULTS REPORT (100% RESPONSIVE)
   ========================================================== */
.ed-results-container {
    width: 100%;
    margin: 0;
    padding: 0 0 60px;
    box-sizing: border-box;
}

.ed-report-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.ed-report-header {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    border-top: 4px solid #1e3a8a;
    padding: 24px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.ed-academic-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.ed-academic-tag .dot { color: #93c5fd; }

.ed-report-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}

.ed-report-subtitle {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0;
}

.ed-report-score-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 20px;
    text-align: center;
    min-width: 140px;
}

.ed-score-label {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 700;
    display: block;
    margin-bottom: 2px;
}

.ed-score-values {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 4px;
    font-family: monospace;
}

.ed-score-earned {
    font-size: 1.8rem;
    font-weight: 900;
    color: #1e3a8a;
    line-height: 1;
}

.ed-score-total {
    font-size: 1rem;
    color: #64748b;
    font-weight: 600;
}

.ed-score-percentage {
    font-size: 0.78rem;
    font-weight: 800;
    color: #16a34a;
    margin-top: 4px;
    font-family: monospace;
}

.ed-report-body {
    padding: 24px 30px;
}

.ed-report-section-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.ed-report-section-title i { color: #1e3a8a; }

.ed-questions-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.ed-question-item {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 20px;
    background: #ffffff;
    transition: border-color 0.15s ease;
}

.ed-question-item.correct {
    border-right: 4px solid #16a34a;
    background: #fcfdfc;
}

.ed-question-item.wrong {
    border-right: 4px solid #dc2626;
    background: #fefdfd;
}

.ed-question-item.pending {
    border-right: 4px solid #d97706;
    background: #fffdfa;
}

.ed-q-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.ed-q-badge-num {
    font-size: 0.8rem;
    font-weight: 800;
    color: #1e3a8a;
    background: #eff6ff;
    padding: 3px 10px;
    border-radius: 4px;
}

.ed-status-badge {
    font-size: 0.78rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: 6px;
}

.correct .ed-status-badge { background: #dcfce7; color: #15803d; }
.wrong .ed-status-badge { background: #fee2e2; color: #b91c1c; }
.pending .ed-status-badge { background: #fef3c7; color: #b45309; }

.ed-q-text {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.6;
    margin: 0 0 16px;
}

.ed-mcq-options {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}

.ed-option-cell {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.88rem;
    background: #ffffff;
}

.ed-option-cell.option-correct {
    background: #f0fdf4;
    border-color: #86efac;
    color: #15803d;
    font-weight: 700;
}

.ed-option-cell.option-wrong {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #b91c1c;
}

.opt-letter {
    font-family: monospace;
    font-size: 0.85rem;
}

.opt-student-tag {
    font-size: 0.7rem;
    background: #1e3a8a;
    color: #ffffff;
    padding: 2px 8px;
    border-radius: 4px;
    margin-right: auto;
}

html[dir="ltr"] .opt-student-tag {
    margin-right: 0;
    margin-left: auto;
}

.opt-correct-tag {
    color: #16a34a;
    font-size: 0.85rem;
}

.ed-essay-answer {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 16px;
    margin-top: 10px;
}

.essay-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
    display: block;
    margin-bottom: 4px;
}

.essay-text {
    font-size: 0.9rem;
    color: #0f172a;
    line-height: 1.6;
}

.ed-btn-file {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    color: #1e3a8a;
    font-weight: 700;
    font-size: 0.85rem;
    text-decoration: none;
}

.ed-retake-section {
    margin-top: 24px;
    padding: 16px 20px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
}

.ed-retake-prompt {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.ed-retake-text strong {
    display: block;
    font-size: 0.9rem;
    color: #0f172a;
}

.ed-retake-text span {
    font-size: 0.8rem;
    color: #64748b;
}

.ed-btn-retake-req {
    background: #fff7ed;
    color: #ea580c;
    border: 1px solid #fed7aa;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.84rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.ed-btn-retake-start {
    background: #16a34a;
    color: #ffffff;
    padding: 10px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 800;
    font-size: 0.88rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.ed-retake-approved {
    color: #16a34a;
    font-weight: 800;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.ed-retake-waiting {
    color: #ea580c;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 8px;
}

.ed-report-actions {
    margin-top: 24px;
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.ed-btn-back {
    background: #1e3a8a;
    color: #ffffff;
    padding: 10px 22px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.88rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.ed-btn-print {
    background: #ffffff;
    color: #334155;
    border: 1px solid #cbd5e1;
    padding: 10px 22px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

html[dir="ltr"] .arrow-icon {
    transform: rotate(180deg);
}

@media (max-width: 768px) {
    .ed-report-header, .ed-report-body {
        padding: 16px 18px;
    }
    .ed-report-score-box {
        width: 100%;
    }
}

@media print {
    .ed-report-actions, .ed-retake-section, nav, header.app-header, aside.app-sidebar {
        display: none !important;
    }
    .ed-report-card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function requestRetakePrompt() {
    Swal.fire({
        title: '{{ __("طلب إذن إعادة الاختبار") }}',
        text: '{{ __("اكتب سبباً مختصراً أو عذراً لتوضيحه لأستاذ المادة:") }}',
        input: 'textarea',
        inputPlaceholder: '...',
        showCancelButton: true,
        confirmButtonText: '{{ __("إرسال") }}',
        cancelButtonText: '{{ __("إلغاء") }}',
        confirmButtonColor: '#1e3a8a',
        cancelButtonColor: '#64748b',
        showLoaderOnConfirm: true,
        preConfirm: (notes) => {
            return axios.post("{{ route('student.exams.requestRetake', $submission->exam_id) }}", {
                notes: notes,
                _token: '{{ csrf_token() }}'
            }).then(response => {
                return response.data;
            }).catch(error => {
                Swal.showValidationMessage(
                    error.response?.data?.error || 'تعذر إرسال الطلب، يرجى المحاولة لاحقاً'
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value?.success) {
            Swal.fire({
                icon: 'success',
                title: '{{ __("نجاح") }}',
                text: result.value.title || 'تم إرسال طلبك للمعلم بنجاح',
                confirmButtonColor: '#1e3a8a'
            }).then(() => location.reload());
        }
    });
}
</script>
@endsection
