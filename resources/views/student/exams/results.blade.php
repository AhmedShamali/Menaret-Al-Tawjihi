@extends('layouts.app')

@section('title', __('كشف درجات الاختبار الأكاديمي') . ' | ' . $submission->exam->title)

@section('content')
<div class="ed-results-container">

    @php
        $canView = $canViewResult ?? $submission->canStudentViewResult();
    @endphp

    {{-- 1. في حال كانت النتيجة محجوبة وقيد مراجعة وتصحيح المعلم --}}
    @if(!$canView)
    <div class="ed-pending-result-card">
        <div class="ed-pending-icon-wrap">
            <i class="fa-solid fa-hourglass-half fa-spin-pulse"></i>
        </div>

        <div class="ed-academic-tag" style="margin: 0 auto 12px; width: fit-content;">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>{{ __('الثانوية العامة - فلسطين') }}</span>
            <span class="dot">•</span>
            <span>{{ optional($submission->exam?->subject)->name_ar ?? optional($submission->exam?->subject)->name ?? __('مادة دراسية') }}</span>
        </div>

        <h1 class="ed-pending-title">{{ $submission->exam?->title ?? __('اختبار إلكتروني') }}</h1>
        <div class="ed-pending-badge">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ __('تم استلام وتوثيق إجاباتك بنجاح') }}</span>
        </div>

        <div class="ed-pending-msg-box">
            <h3 class="ed-pending-msg-title">
                <i class="fa-solid fa-clock-rotate-left text-navy"></i>
                {{ __('النتيجة قيد المراجعة والتدقيق الأكاديمي ⏳') }}
            </h3>
            <p class="ed-pending-msg-text">
                {{ __('تم استلام كافة إجاباتك وحفظها بأمان في سجل درجاتك. بناءً على تعليمات أستاذ المساق، سيتم إعلان كشف الدرجات ونموذج الإجابة فور مراجعة واعتماد النتائج.') }}
            </p>
            <div class="ed-pending-notice">
                <i class="fa-solid fa-bell text-amber"></i>
                <span>{{ __('سيصلك إشعار تلقائي في حسابك فور قيام المعلم باعتماد وإعلان النتيجة.') }}</span>
            </div>
        </div>

        <div class="ed-pending-meta-grid">
            <div class="meta-item">
                <span class="meta-lbl">{{ __('تاريخ وتوقيت التسليم') }}</span>
                <strong class="meta-val">{{ $submission->updated_at ? $submission->updated_at->format('Y/m/d - h:i A') : now()->format('Y/m/d') }}</strong>
            </div>
            <div class="meta-item">
                <span class="meta-lbl">{{ __('إجمالي الأسئلة المنجزة') }}</span>
                <strong class="meta-val">{{ count($submission->answers) }} {{ __('من أصل') }} {{ count($submission->exam->questions) }}</strong>
            </div>
            <div class="meta-item">
                <span class="meta-lbl">{{ __('حالة الاعتماد') }}</span>
                <strong class="meta-val text-amber">{{ __('بانتظار تصحيح المعلم') }}</strong>
            </div>
        </div>

        <div class="ed-report-actions" style="margin-top: 28px;">
            <a href="{{ route('student.exams.index') }}" class="ed-btn-back">
                <i class="fa-solid fa-arrow-right arrow-icon"></i>
                <span>{{ __('العودة إلى سجل الاختبارات') }}</span>
            </a>
            @if($submission->allow_retake)
                <a href="{{ route('student.exams.take', $submission->exam_id) }}" class="ed-btn-retake">
                    <i class="fa-solid fa-rotate-right"></i>
                    <span>{{ __('إعادة تأدية الاختبار (مسموح لك)') }}</span>
                </a>
            @endif
        </div>
    </div>

    {{-- 2. في حال تم اعتماد النتيجة والسماح بظهورها للطالب --}}
    @else
    @php
        $totalMax = (float) ($submission->exam?->questions?->sum('points') ?? $submission->exam?->total_grade ?? 100);
        $earned = (float) $submission->total_earned_grade;
        $pct = $totalMax > 0 ? round(($earned / $totalMax) * 100, 1) : 0;

        $durationSeconds = ($submission->created_at && $submission->updated_at) 
            ? $submission->created_at->diffInSeconds($submission->updated_at) 
            : null;
        $mins = $durationSeconds ? floor($durationSeconds / 60) : 0;
        $secs = $durationSeconds ? ($durationSeconds % 60) : 0;
        $timeTakenStr = $durationSeconds 
            ? ($mins > 0 ? "{$mins} " . __('دقيقة') . ($secs > 0 ? " و {$secs} " . __('ثانية') : '') : "{$secs} " . __('ثانية')) 
            : '—';
    @endphp

    <div class="moodle-review-wrapper">
        
        <!-- عنوان المراجعة الجامعي الهادئ -->
        <div class="moodle-page-header">
            <h1 class="moodle-quiz-title">{{ $submission->exam?->title ?? __('اختبار') }}</h1>
            <div class="moodle-quiz-sub">
                <span>{{ optional($submission->exam?->subject)->name_ar ?? optional($submission->exam?->subject)->name ?? __('المادة الدراسية') }}</span>
                <span>•</span>
                <span>{{ __('مراجعة الإجابات ونموذج التصحيح') }}</span>
            </div>
        </div>

        <!-- جدول ملخص المحاولة الكلاسيكي المتطابق مع مودل الجامعي (Summary Table) -->
        <div class="moodle-summary-table-box">
            <table class="generaltable quizreviewsummary">
                <tbody>
                    <tr>
                        <th scope="row">{{ __('بدأ في') }}</th>
                        <td>{{ $submission->created_at ? $submission->created_at->locale(app()->getLocale())->translatedFormat('l، d F Y، h:i A') : '—' }}</td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('الحالة') }}</th>
                        <td>{{ ($submission->status === 'graded' || $submission->is_published) ? __('مكتمل') : __('قيد التصحيح والاعتماد') }}</td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('اكتمل في') }}</th>
                        <td>{{ $submission->updated_at ? $submission->updated_at->locale(app()->getLocale())->translatedFormat('l، d F Y، h:i A') : '—' }}</td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('الوقت المستغرق') }}</th>
                        <td>{{ $timeTakenStr }}</td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('العلامات') }}</th>
                        <td>{{ number_format($earned, 2) }} / {{ number_format($totalMax, 2) }}</td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('الدرجة') }}</th>
                        <td><strong>{{ number_format($earned, 2) }}</strong> {{ __('من') }} {{ number_format($totalMax, 2) }} (<strong>{{ $pct }}%</strong>)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if(!empty($submission->deduction_amount) && $submission->deduction_amount > 0)
        <div class="moodle-deduction-alert">
            <div class="alert-title">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ __('تم تطبيق خصم درجات:') }} {{ $submission->deduction_amount }} {{ __('علامات') }}</span>
            </div>
            @if($submission->deduction_reason)
                <p class="alert-desc"><strong>{{ __('السبب:') }}</strong> {{ $submission->deduction_reason }}</p>
            @endif
            @if($submission->teacher_notes)
                <p class="alert-desc"><strong>{{ __('توجيهات المعلم:') }}</strong> {{ $submission->teacher_notes }}</p>
            @endif
        </div>
        @elseif(!empty($submission->teacher_notes))
        <div class="moodle-teacher-alert">
            <i class="fa-solid fa-comment-dots"></i>
            <span><strong>{{ __('ملاحظة أستاذ المادة:') }}</strong> {{ $submission->teacher_notes }}</span>
        </div>
        @endif

        <!-- مسار تدفق الأسئلة بنمط مودل الجامعي الأصيل -->
        <div class="moodle-questions-stream">
            @foreach($submission->answers as $idx => $ans)
                @php
                    $awarded = (float)$ans->points_awarded;
                    $max = (float)($ans->question ? $ans->question->points : 0);

                    if($ans->question && $ans->question->type == 'mcq') {
                        $isCorrect = ($awarded >= $max && $max > 0);
                        $statusClass = $isCorrect ? 'correct' : 'wrong';
                        $statusText = $isCorrect ? __('صحيح') : __('غير صحيح');
                    } else {
                        $isCorrect = ($awarded >= $max && $max > 0);
                        $statusClass = $awarded > 0 ? 'correct' : 'pending';
                        $statusText = $awarded > 0 ? __('تم التصحيح') : __('بانتظار تصحيح المعلم');
                    }

                    $qCandidates = $ans->question ? $ans->question->getImageCandidates() : [];
                    $firstQImg = !empty($qCandidates) ? $qCandidates[0] : null;
                @endphp

                <div class="que {{ $statusClass }}" id="q{{ $idx + 1 }}">
                    
                    <!-- صندوق معلومات وحالة السؤال الجانبي (Side Info Box) -->
                    <div class="info">
                        <h3 class="no">{{ __('السؤال') }} <span class="qno">{{ $idx + 1 }}</span></h3>
                        <div class="state state-{{ $statusClass }}">
                            <span class="state-badge {{ $isCorrect ? 'text-success' : ($statusClass == 'wrong' ? 'text-danger' : 'text-amber') }}">
                                {{ $statusText }}
                            </span>
                        </div>
                        <div class="grade">
                            {{ __('الدرجة') }} {{ number_format($awarded, 2) }} {{ __('من') }} {{ number_format($max, 2) }}
                        </div>
                        <div class="questionflag">
                            <span class="flag-text"><i class="fa-regular fa-flag"></i> {{ __('علم السؤال') }}</span>
                        </div>
                    </div>

                    <!-- صندوق محتوى السؤال والخيارات والإجابة النموذجية -->
                    <div class="content">
                        <div class="formulation">
                            <div class="qtext">
                                {!! nl2br(e($ans->question->question_text ?? '')) !!}
                            </div>

                            @if(!empty($firstQImg))
                            <div class="moodle-qimage-box">
                                <img src="{{ $firstQImg }}" alt="{{ __('مرفق السؤال') }}" 
                                     onclick="window.open(this.src, '_blank')" 
                                     title="{{ __('انقر لفتح الصورة بالحجم الكامل') }}"
                                     data-candidates="{{ implode('|', $qCandidates) }}"
                                     data-candidate-idx="0"
                                     onerror="handleResultImageFallback(this)">
                            </div>
                            @endif

                            @if($ans->question && $ans->question->type == 'mcq')
                                <div class="ablock">
                                    @foreach(['a', 'b', 'c', 'd'] as $opt)
                                        @php
                                            $optCandidates = $ans->question ? $ans->question->getOptionImageCandidates($opt) : [];
                                            $firstOptImg = !empty($optCandidates) ? $optCandidates[0] : null;
                                            $hasOptImg = !empty($firstOptImg);
                                            $hasOptText = !empty($ans->question->$opt);
                                        @endphp
                                        @if($hasOptText || $hasOptImg)
                                            @php
                                                $isCorrectOpt = (strtolower(trim((string)($ans->question->correct_answer ?? ''))) == $opt);
                                                $isStudentOpt = (strtolower(trim((string)($ans->answer_text ?? ''))) == $opt);
                                            @endphp
                                            <div class="moodle-choice-line {{ $isStudentOpt ? ($isCorrectOpt ? 'choice-student-correct' : 'choice-student-wrong') : '' }}">
                                                <div class="moodle-radio-indicator {{ $isStudentOpt ? 'checked' : '' }}"></div>
                                                <div class="moodle-choice-body">
                                                    @if($hasOptText)
                                                        <span class="choice-text-span">{{ $ans->question->$opt }}</span>
                                                    @endif
                                                    @if($hasOptImg)
                                                        <div class="choice-img-thumb" onclick="window.open(this.querySelector('img').src, '_blank')">
                                                            <img src="{{ $firstOptImg }}" alt="Option {{ strtoupper($opt) }}"
                                                                 data-candidates="{{ implode('|', $optCandidates) }}"
                                                                 data-candidate-idx="0"
                                                                 onerror="handleResultImageFallback(this)">
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="moodle-choice-grade-icon">
                                                    @if($isCorrectOpt)
                                                        <i class="fa-solid fa-check text-success" title="{{ __('الإجابة الصحيحة') }}"></i>
                                                    @elseif($isStudentOpt && !$isCorrectOpt)
                                                        <i class="fa-solid fa-xmark text-danger" title="{{ __('إجابتك المسجلة خاطئة') }}"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="moodle-essay-zone">
                                    <div class="essay-label-heading">{{ __('الإجابة المسجلة في الاختبار:') }}</div>
                                    <div class="moodle-essay-answer-box">
                                        <p class="essay-val">{{ $ans->answer_text ?? __('لم يتم تقديم إجابة نصية.') }}</p>
                                        @if($awarded >= $max && $max > 0)
                                            <i class="fa-solid fa-check text-success essay-check"></i>
                                        @endif
                                    </div>
                                    @if($ans->file_path)
                                        <div class="moodle-essay-attachment">
                                            <a href="{{ \App\Support\MediaHelper::url($ans->file_path) }}" target="_blank" class="moodle-attachment-link">
                                                <i class="fa-solid fa-paperclip"></i>
                                                <span>{{ __('عرض الملف أو الرسم المرفق مع الحل') }}</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- صندوق الإجابة النموذجية المعتمد باللون الخوخي/البيج الكلاسيكي لمودل (Moodle Peach Outcome Box) -->
                        @if($ans->question && $ans->question->type == 'mcq')
                            @php
                                $correctOptLetter = strtolower(trim((string)($ans->question->correct_answer ?? '')));
                                $correctOptText = $ans->question->$correctOptLetter ?? '';
                            @endphp
                            <div class="outcome">
                                <div class="feedback">
                                    <span class="outcome-label">{{ __('الإجابة الصحيحة هي:') }}</span>
                                    <span class="outcome-ans-text">
                                        @if(!empty($correctOptText))
                                            {{ $correctOptText }}
                                        @else
                                            {{ strtoupper($correctOptLetter) }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @elseif(!empty($submission->teacher_notes))
                            <div class="outcome">
                                <div class="feedback">
                                    <span class="outcome-label">{{ __('توجيهات أستاذ المساق:') }}</span>
                                    <span class="outcome-ans-text">{{ $submission->teacher_notes }}</span>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>

        {{-- قسم طلب إعادة الاختبار الجامعي --}}
        <div class="moodle-retake-section">
            @if($submission->allow_retake)
                <div class="moodle-retake-notice granted">
                    <div class="retake-text-group">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <span>{{ __('مسموح لك بإعادة الاختبار الآن: منحك أستاذ المادة فرصة لتحسين درجتك.') }}</span>
                    </div>
                    <a href="{{ route('student.exams.take', $submission->exam_id) }}" class="moodle-btn-primary">
                        {{ __('بدء المحاولة الجديدة') }}
                    </a>
                </div>
            @elseif($submission->retake_requested)
                <div class="moodle-retake-notice pending">
                    <i class="fa-solid fa-clock-rotate-left text-amber"></i>
                    <span>{{ __('طلب إذن الإعادة قيد مراجعة أستاذ المادة وسيتم إشعارك فور البت فيه.') }}</span>
                </div>
            @else
                <div class="moodle-retake-notice normal">
                    <div class="retake-text-group">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>{{ __('هل واجهت عذراً وترغب في طلب إعادة المحاولة؟') }}</span>
                    </div>
                    <button type="button" onclick="requestRetakePrompt()" class="moodle-btn-outline">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>{{ __('طلب إذن إعادة الاختبار') }}</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="moodle-actions-footer">
            <a href="{{ route('student.exams.index') }}" class="moodle-btn-back">
                <i class="fa-solid fa-arrow-right"></i>
                <span>{{ __('العودة إلى سجل الاختبارات') }}</span>
            </a>
            <button type="button" onclick="window.print()" class="moodle-btn-print">
                <i class="fa-solid fa-print"></i>
                <span>{{ __('طباعة المراجعة') }}</span>
            </button>
        </div>

    </div>
    @endif
</div>

<style>
    .ed-results-container {
        max-width: 960px;
        margin: 0 auto;
        padding: 16px 16px 60px;
        box-sizing: border-box;
        width: 100%;
    }

    /* Pending Result Card */
    .ed-pending-result-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 40px 28px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
    }

    .ed-pending-icon-wrap {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: #eff6ff;
        color: #1e3a8a;
        display: grid;
        place-items: center;
        font-size: 2.2rem;
        margin: 0 auto 18px;
        border: 1.5px solid #bfdbfe;
    }

    .ed-pending-title {
        font-size: 1.5rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 10px;
    }

    .ed-pending-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 6px 16px;
        border-radius: 30px;
        font-size: 0.84rem;
        font-weight: 700;
        margin-bottom: 24px;
    }

    .ed-pending-msg-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 22px;
        max-width: 680px;
        margin: 0 auto 24px;
        text-align: right;
    }

    .ed-pending-msg-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ed-pending-msg-text {
        font-size: 0.9rem;
        color: #334155;
        line-height: 1.8;
        margin: 0 0 14px;
    }

    .ed-pending-notice {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: #b45309;
        background: #fffbeb;
        border: 1px solid #fde68a;
        padding: 10px 14px;
        border-radius: 8px;
        font-weight: 600;
    }

    .ed-pending-meta-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        max-width: 680px;
        margin: 0 auto;
    }

    .meta-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 12px;
        border-radius: 10px;
        text-align: center;
    }

    .meta-lbl {
        display: block;
        font-size: 0.72rem;
        color: #64748b;
        margin-bottom: 4px;
    }

    .meta-val {
        display: block;
        font-size: 0.88rem;
        color: #0f172a;
        font-weight: 800;
    }

    /* =========================================================
       أنماط المراجعة الأكاديمية بنمط نظام مودل الجامعي (Moodle Review Styles)
       ========================================================= */
    .moodle-review-wrapper {
        max-width: 960px;
        margin: 0 auto;
        padding: 0 0 40px;
    }

    .moodle-page-header {
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #dee2e6;
    }

    .moodle-quiz-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: #212529;
        margin: 0 0 4px;
    }

    .moodle-quiz-sub {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 500;
    }

    /* جدول ملخص المحاولة الجامعي (Moodle Summary Table) */
    .moodle-summary-table-box {
        margin-bottom: 24px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .quizreviewsummary {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #dee2e6;
        background: #ffffff;
        font-size: 0.9rem;
    }

    .quizreviewsummary th {
        background-color: #f8f9fa;
        color: #333333;
        font-weight: 700;
        padding: 9px 14px;
        border: 1px solid #dee2e6;
        width: 25%;
        min-width: 130px;
        text-align: right;
    }

    html[dir="ltr"] .quizreviewsummary th {
        text-align: left;
    }

    .quizreviewsummary td {
        padding: 9px 14px;
        border: 1px solid #dee2e6;
        color: #212529;
    }

    /* تنبيهات الخصم أو ملاحظات المعلم */
    .moodle-deduction-alert {
        background: #fdf2f2;
        border: 1px solid #f8b4b4;
        border-radius: 4px;
        padding: 12px 16px;
        margin-bottom: 20px;
        color: #9b1c1c;
        font-size: 0.88rem;
    }

    .moodle-deduction-alert .alert-title {
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }

    .moodle-deduction-alert .alert-desc {
        margin: 4px 0 0;
        line-height: 1.6;
    }

    .moodle-teacher-alert {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 4px;
        padding: 12px 16px;
        margin-bottom: 20px;
        color: #166534;
        font-size: 0.88rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* هيكل الأسئلة بنمط مودل (Moodle .que block) */
    .moodle-questions-stream {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .que {
        display: flex;
        gap: 14px;
        align-items: stretch;
    }

    /* الصندوق الجانبي لمعلومات السؤال (Info Block) */
    .que .info {
        width: 125px;
        flex-shrink: 0;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 10px 12px;
        font-size: 0.84rem;
        color: #495057;
        text-align: right;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    html[dir="ltr"] .que .info {
        text-align: left;
    }

    .que .info .no {
        font-size: 0.92rem;
        font-weight: 800;
        color: #212529;
        margin: 0;
    }

    .que .info .state {
        font-size: 0.85rem;
        font-weight: 700;
        margin: 2px 0;
    }

    .que .info .state .text-success {
        color: #198754 !important;
    }

    .que .info .state .text-danger {
        color: #dc3545 !important;
    }

    .que .info .grade {
        font-size: 0.8rem;
        color: #6c757d;
        line-height: 1.4;
    }

    .que .info .questionflag {
        margin-top: auto;
        padding-top: 8px;
        font-size: 0.78rem;
        color: #8c98a4;
    }

    /* المحتوى الرئيسي للسؤال (Content Block) */
    .que .content {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .formulation {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 16px 20px;
    }

    .formulation .qtext {
        font-size: 0.98rem;
        font-weight: 500;
        line-height: 1.7;
        color: #212529;
        margin-bottom: 16px;
    }

    .moodle-qimage-box {
        margin: 12px 0 16px;
        text-align: center;
    }

    .moodle-qimage-box img {
        max-width: 100%;
        max-height: 320px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        cursor: zoom-in;
    }

    /* الخيارات (Moodle Ablock) */
    .ablock {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .moodle-choice-line {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        border-radius: 4px;
        background: #ffffff;
        transition: background 0.15s ease;
    }

    .moodle-choice-line:hover {
        background: #f8f9fa;
    }

    .choice-student-correct {
        background: rgba(25, 135, 84, 0.07) !important;
    }

    .choice-student-wrong {
        background: rgba(220, 53, 69, 0.07) !important;
    }

    .moodle-radio-indicator {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 1.5px solid #adb5bd;
        flex-shrink: 0;
        position: relative;
    }

    .moodle-radio-indicator.checked {
        border-color: #0d6efd;
    }

    .moodle-radio-indicator.checked::after {
        content: '';
        position: absolute;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #0d6efd;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .moodle-choice-body {
        flex: 1;
        font-size: 0.92rem;
        color: #212529;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .choice-img-thumb img {
        max-height: 70px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        cursor: zoom-in;
    }

    .moodle-choice-grade-icon {
        width: 24px;
        text-align: center;
        font-size: 1.15rem;
        flex-shrink: 0;
        margin-inline-start: auto;
    }

    .moodle-choice-grade-icon .text-success {
        color: #198754 !important;
        font-weight: 800;
    }

    .moodle-choice-grade-icon .text-danger {
        color: #dc3545 !important;
        font-weight: 800;
    }

    /* الأسئلة المقالية */
    .moodle-essay-zone {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        padding: 14px;
    }

    .essay-label-heading {
        font-size: 0.8rem;
        font-weight: 700;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .moodle-essay-answer-box {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .essay-val {
        margin: 0;
        font-size: 0.92rem;
        color: #212529;
        line-height: 1.7;
    }

    .essay-check {
        font-size: 1.2rem;
        color: #198754;
    }

    .moodle-essay-attachment {
        margin-top: 10px;
    }

    .moodle-attachment-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #0d6efd;
        text-decoration: none;
    }

    .moodle-attachment-link:hover {
        text-decoration: underline;
    }

    /* صندوق الإجابة النموذجية المعتمد باللون الخوخي/البيج الكلاسيكي لمودل (Moodle Peach Outcome Box) */
    .outcome {
        background-color: #fcefdc;
        border: 1px solid #f8e5c8;
        border-radius: 4px;
        padding: 10px 16px;
        color: #795548;
        font-size: 0.88rem;
        line-height: 1.6;
    }

    .outcome .feedback {
        display: flex;
        align-items: baseline;
        gap: 6px;
        flex-wrap: wrap;
    }

    .outcome-label {
        font-weight: 700;
    }

    .outcome-ans-text {
        font-weight: 600;
    }

    /* قسم إعادة الاختبار */
    .moodle-retake-section {
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #dee2e6;
    }

    .moodle-retake-notice {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 18px;
        border-radius: 4px;
        font-size: 0.88rem;
        gap: 14px;
        flex-wrap: wrap;
    }

    .moodle-retake-notice.granted {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .moodle-retake-notice.pending {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
    }

    .moodle-retake-notice.normal {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #495057;
    }

    .retake-text-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .moodle-btn-primary {
        background: #0d6efd;
        color: #ffffff;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .moodle-btn-outline {
        background: #ffffff;
        color: #495057;
        border: 1px solid #ced4da;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .moodle-btn-outline:hover {
        background: #f8f9fa;
        border-color: #adb5bd;
    }

    /* أزرار الإجراءات السفلية */
    .moodle-actions-footer {
        margin-top: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .moodle-btn-back {
        background: #f8f9fa;
        color: #495057;
        border: 1px solid #ced4da;
        padding: 9px 18px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .moodle-btn-back:hover {
        background: #e9ecef;
    }

    .moodle-btn-print {
        background: #ffffff;
        color: #495057;
        border: 1px solid #ced4da;
        padding: 9px 18px;
        border-radius: 4px;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .moodle-btn-print:hover {
        background: #f8f9fa;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .ed-results-container {
            padding: 8px 6px 40px !important;
        }
        .que {
            gap: 8px;
        }
        .que .info {
            width: 95px;
            padding: 8px 6px;
            font-size: 0.75rem;
        }
        .que .info .no {
            font-size: 0.82rem;
        }
        .formulation {
            padding: 12px 14px;
        }
        .quizreviewsummary th {
            width: 32%;
            padding: 7px 10px;
            font-size: 0.82rem;
        }
        .quizreviewsummary td {
            padding: 7px 10px;
            font-size: 0.82rem;
        }
    }

    @media (max-width: 480px) {
        .que {
            flex-direction: column;
        }
        .que .info {
            width: 100%;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
        }
        .que .info .questionflag {
            display: none;
        }
    }

    @media print {
        .moodle-actions-footer, .moodle-retake-section, nav, header.app-header, aside.sidebar, .top-bar, .mobile-bottom-nav, .sidebar-overlay {
            display: none !important;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
        .ed-results-container {
            padding: 0 !important;
            max-width: 100% !important;
        }
        .que {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    }
</style>

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
        preConfirm: async (notes) => {
            try {
                const response = await axios.post("{{ route('student.exams.requestRetake', $submission->exam_id) }}", {
                    notes: (notes || '').trim(),
                    _token: '{{ csrf_token() }}'
                });
                if (response.data && response.data.error) {
                    Swal.showValidationMessage(response.data.error);
                    return false;
                }
                return response.data;
            } catch (error) {
                const msg = error.response?.data?.error || error.response?.data?.message || '{{ __("تعذر إرسال الطلب، يرجى المحاولة لاحقاً") }}';
                Swal.showValidationMessage(msg);
                return false;
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result && result.isConfirmed && result.value) {
            Swal.fire({
                icon: 'success',
                title: '{{ __("نجاح") }}',
                text: result.value.title || result.value.message || '{{ __("تم إرسال طلبك للمعلم بنجاح") }}',
                confirmButtonColor: '#1e3a8a'
            }).then(() => location.reload());
        }
    });
}

function handleResultImageFallback(img) {
    const raw = img.getAttribute('data-candidates');
    if (!raw) {
        const box = img.closest('.ed-result-q-image') || img.closest('.ed-result-opt-img');
        if (box) box.style.display = 'none';
        return;
    }
    const candidates = raw.split('|').filter(Boolean);
    let idx = parseInt(img.getAttribute('data-candidate-idx') || '0', 10) + 1;
    if (idx < candidates.length) {
        img.setAttribute('data-candidate-idx', idx);
        img.src = candidates[idx];
    } else {
        const box = img.closest('.ed-result-q-image') || img.closest('.ed-result-opt-img');
        if (box) box.style.display = 'none';
    }
}
</script>
@endsection
