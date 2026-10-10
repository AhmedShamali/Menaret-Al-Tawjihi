@extends('layouts.app')

@section('title', __('كشف درجات ومراجعة الاختبار') . ' | ' . $submission->exam->title)

@section('content')
<div class="classic-results-page">

    @php
        $canView = $canViewResult ?? $submission->canStudentViewResult();
    @endphp

    {{-- 1. في حال كانت النتيجة محجوبة وقيد مراجعة وتصحيح المعلم --}}
    @if(!$canView)
    <div class="classic-pending-card">
        <div class="pending-icon-sphere">
            <i class="fa-solid fa-hourglass-half fa-spin-pulse"></i>
        </div>

        <div class="academic-context-pill">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>{{ __('الثانوية العامة - فلسطين') }}</span>
            <span class="pill-dot">•</span>
            <span>{{ optional($submission->exam?->subject)->name_ar ?? optional($submission->exam?->subject)->name ?? __('مادة دراسية') }}</span>
        </div>

        <h1 class="pending-main-title">{{ $submission->exam?->title ?? __('اختبار إلكتروني') }}</h1>
        <div class="pending-success-status">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ __('تم استلام وتوثيق إجاباتك بنجاح في النظام') }}</span>
        </div>

        <div class="pending-explanation-box">
            <h3 class="explanation-title">
                <i class="fa-solid fa-clock-rotate-left"></i>
                {{ __('النتيجة قيد المراجعة والتدقيق الأكاديمي ⏳') }}
            </h3>
            <p class="explanation-desc">
                {{ __('تم استلام كافة إجاباتك وحفظها بأمان في سجل درجاتك. بناءً على تعليمات أستاذ المساق، سيتم إعلان كشف الدرجات ونموذج الإجابة فور مراجعة واعتماد النتائج.') }}
            </p>
            <div class="explanation-hint">
                <i class="fa-solid fa-bell"></i>
                <span>{{ __('سيصلك إشعار فوري وتظهر الشارة على التطبيق فور قيام المعلم باعتماد وإعلان النتيجة.') }}</span>
            </div>
        </div>

        <div class="pending-meta-grid">
            <div class="meta-card">
                <span class="m-label">{{ __('تاريخ وتوقيت التسليم') }}</span>
                <strong class="m-val">{{ $submission->updated_at ? $submission->updated_at->format('Y/m/d - h:i A') : now()->format('Y/m/d') }}</strong>
            </div>
            <div class="meta-card">
                <span class="m-label">{{ __('الأسئلة المنجزة') }}</span>
                <strong class="m-val">{{ count($submission->answers) }} {{ __('من أصل') }} {{ count($submission->exam->questions) }}</strong>
            </div>
            <div class="meta-card">
                <span class="m-label">{{ __('حالة الاعتماد') }}</span>
                <strong class="m-val text-amber">{{ __('بانتظار تصحيح المعلم') }}</strong>
            </div>
        </div>

        <div class="pending-actions-bar">
            <a href="{{ route('student.exams.index') }}" class="btn-classic-return">
                <i class="fa-solid fa-arrow-right"></i>
                <span>{{ __('العودة إلى سجل الاختبارات') }}</span>
            </a>
            @if($submission->allow_retake)
                <a href="{{ route('student.exams.take', $submission->exam_id) }}" class="btn-classic-retake">
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
            
        $scoreColorClass = $pct >= 85 ? 'grade-excellent' : ($pct >= 70 ? 'grade-good' : ($pct >= 50 ? 'grade-pass' : 'grade-fail'));
    @endphp

    <div class="classic-review-container">
        
        <!-- الشريط الأكاديمي العلوي الفاخر لنتائج الاختبار -->
        <header class="review-page-topbar">
            <div class="topbar-right-info">
                <div class="subject-academic-pill">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>{{ optional($submission->exam?->subject)->name_ar ?? optional($submission->exam?->subject)->name ?? __('المادة الدراسية') }}</span>
                </div>
                <h1 class="review-exam-title">{{ $submission->exam?->title ?? __('اختبار') }}</h1>
                <span class="review-exam-subtitle">{{ __('كشف الدرجات الرسمي ونموذج تصحيح الإجابات المعتمد') }}</span>
            </div>

            <div class="topbar-left-actions no-print">
                <a href="{{ route('student.exams.index') }}" class="btn-review-back">
                    <i class="fa-solid fa-arrow-right"></i>
                    <span>{{ __('سجل الاختبارات') }}</span>
                </a>
                <button type="button" onclick="window.print()" class="btn-review-print" title="{{ __('طباعة أو تصدير كـ PDF') }}">
                    <i class="fa-solid fa-print"></i>
                    <span>{{ __('طباعة الكشف') }}</span>
                </button>
            </div>
        </header>

        <!-- بطاقة خلاصة الدرجات والأداء الأكاديمي (Scorecard) -->
        <section class="classic-scorecard-card">
            <div class="scorecard-main-display">
                <div class="score-number-badge {{ $scoreColorClass }}">
                    <span class="earned-digit">{{ number_format($earned, 2) }}</span>
                    <span class="max-digit">/ {{ number_format($totalMax, 2) }}</span>
                </div>
                <div class="score-percent-pod {{ $scoreColorClass }}">
                    <i class="fa-solid fa-percent"></i>
                    <span>{{ $pct }}%</span>
                </div>
            </div>

            <div class="scorecard-details-grid">
                <div class="sc-metric-item">
                    <span class="sc-lbl"><i class="fa-regular fa-clock"></i> {{ __('الزمن المستغرق') }}</span>
                    <strong class="sc-val">{{ $timeTakenStr }}</strong>
                </div>
                <div class="sc-metric-item">
                    <span class="sc-lbl"><i class="fa-regular fa-calendar-check"></i> {{ __('تاريخ وتوقيت التسليم') }}</span>
                    <strong class="sc-val">{{ $submission->updated_at ? $submission->updated_at->format('Y/m/d - h:i A') : '—' }}</strong>
                </div>
                <div class="sc-metric-item">
                    <span class="sc-lbl"><i class="fa-solid fa-list-check"></i> {{ __('حالة الاختبار') }}</span>
                    <strong class="sc-val text-success">{{ __('مكتمل وتم الاعتماد') }}</strong>
                </div>
                <div class="sc-metric-item">
                    <span class="sc-lbl"><i class="fa-solid fa-circle-question"></i> {{ __('الأسئلة المنجزة') }}</span>
                    <strong class="sc-val">{{ count($submission->answers) }} {{ __('من أصل') }} {{ count($submission->exam->questions) }}</strong>
                </div>
            </div>
        </section>

        <!-- تنبيهات الخصومات أو توجيهات أستاذ المساق -->
        @if(!empty($submission->deduction_amount) && $submission->deduction_amount > 0)
        <div class="classic-deduction-banner">
            <div class="banner-head">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <h4>{{ __('تم تطبيق خصم درجات:') }} {{ $submission->deduction_amount }} {{ __('علامات') }}</h4>
            </div>
            @if($submission->deduction_reason)
                <p class="banner-body"><strong>{{ __('السبب:') }}</strong> {{ $submission->deduction_reason }}</p>
            @endif
            @if($submission->teacher_notes)
                <p class="banner-body"><strong>{{ __('توجيهات المعلم:') }}</strong> {{ $submission->teacher_notes }}</p>
            @endif
        </div>
        @elseif(!empty($submission->teacher_notes))
        <div class="classic-teacher-banner">
            <i class="fa-solid fa-comment-dots"></i>
            <div>
                <strong>{{ __('ملاحظة وتوجيهات أستاذ المادة:') }}</strong>
                <p>{{ $submission->teacher_notes }}</p>
            </div>
        </div>
        @endif

        <!-- مسار مراجعة الأسئلة بتصميم أكاديمي ملكي فاخر -->
        <main class="classic-review-questions-stream">
            @foreach($submission->answers as $idx => $ans)
                @php
                    $awarded = (float)$ans->points_awarded;
                    $max = (float)($ans->question ? $ans->question->points : 0);

                    if($ans->question && $ans->question->type == 'mcq') {
                        $isCorrect = ($awarded >= $max && $max > 0);
                        $statusClass = $isCorrect ? 'is-correct' : 'is-wrong';
                        $statusText = $isCorrect ? __('صحيح') : __('غير صحيح');
                        $statusIcon = $isCorrect ? 'fa-circle-check' : 'fa-circle-xmark';
                    } else {
                        $isCorrect = ($awarded >= $max && $max > 0);
                        $statusClass = $awarded > 0 ? 'is-correct' : 'is-pending';
                        $statusText = $awarded > 0 ? __('تم الاعتماد') : __('بانتظار تدقيق المعلم');
                        $statusIcon = $awarded > 0 ? 'fa-circle-check' : 'fa-clock';
                    }

                    $qCandidates = $ans->question ? $ans->question->getImageCandidates() : [];
                    $firstQImg = !empty($qCandidates) ? $qCandidates[0] : null;
                @endphp

                <article class="classic-review-card {{ $statusClass }}" id="q{{ $idx + 1 }}">
                    
                    <!-- شريط رأس بطاقة المراجعة -->
                    <div class="card-meta-ribbon">
                        <div class="meta-right">
                            <span class="q-badge-num">
                                <i class="fa-solid fa-bookmark"></i>
                                <span>{{ __('السؤال') }} {{ $idx + 1 }}</span>
                            </span>
                            <span class="q-badge-grade">
                                <i class="fa-solid fa-award"></i>
                                <span>{{ __('الدرجة:') }} {{ number_format($awarded, 2) }} {{ __('من') }} {{ number_format($max, 2) }}</span>
                            </span>
                        </div>

                        <div class="meta-left">
                            <span class="review-status-chip {{ $statusClass }}">
                                <i class="fa-solid {{ $statusIcon }}"></i>
                                <span>{{ $statusText }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- متن ومحتوى وصياغة السؤال -->
                    <div class="card-q-body">
                        <div class="card-q-text">
                            {!! nl2br(e($ans->question->question_text ?? '')) !!}
                        </div>

                        @if(!empty($firstQImg))
                        <div class="classic-qimg-container">
                            <div class="classic-qimg-wrapper" onclick="window.open(this.querySelector('img').src, '_blank')">
                                <img src="{{ $firstQImg }}" alt="{{ __('مرفق السؤال') }}" 
                                     data-candidates="{{ implode('|', $qCandidates) }}"
                                     data-candidate-idx="0"
                                     onerror="handleResultImageFallback(this)">
                                <div class="qimg-zoom-tag">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    <span>{{ __('انقر للعرض بالحجم الكامل') }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($ans->question && $ans->question->type == 'mcq')
                            @php
                                $arLetters = ['a' => 'أ', 'b' => 'ب', 'c' => 'ج', 'd' => 'د'];
                                $correctOptLetter = strtolower(trim((string)($ans->question->correct_answer ?? '')));
                                $studentOptLetter = strtolower(trim((string)($ans->answer_text ?? '')));
                            @endphp
                            <div class="classic-review-choices-grid">
                                @foreach(['a', 'b', 'c', 'd'] as $opt)
                                    @php
                                        $optCandidates = $ans->question ? $ans->question->getOptionImageCandidates($opt) : [];
                                        $firstOptImg = !empty($optCandidates) ? $optCandidates[0] : null;
                                        $hasOptImg = !empty($firstOptImg);
                                        $hasOptText = !empty($ans->question->$opt);
                                    @endphp
                                    @if($hasOptText || $hasOptImg)
                                        @php
                                            $isThisCorrect = ($correctOptLetter === $opt);
                                            $isThisStudentChoice = ($studentOptLetter === $opt);
                                        @endphp
                                        <div class="review-choice-card {{ $isThisStudentChoice ? ($isThisCorrect ? 'choice-student-correct' : 'choice-student-wrong') : ($isThisCorrect ? 'choice-is-model-answer' : '') }}">
                                            
                                            <!-- الحرف العربي الدائري الكلاسيكي -->
                                            <div class="choice-letter-badge">
                                                <span>{{ $arLetters[$opt] ?? strtoupper($opt) }}</span>
                                            </div>

                                            <!-- متن الخيار والصورة -->
                                            <div class="choice-text-wrap">
                                                @if($hasOptText)
                                                    <span class="choice-text-content">{{ $ans->question->$opt }}</span>
                                                @endif
                                                @if($hasOptImg)
                                                    <div class="choice-thumb-wrap" onclick="window.open('{{ $firstOptImg }}', '_blank')">
                                                        <img src="{{ $firstOptImg }}" alt="Option {{ strtoupper($opt) }}" class="choice-thumb-img" onerror="this.closest('.choice-thumb-wrap').style.display='none'">
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- شارة توضيح الإجابة -->
                                            <div class="choice-result-flag">
                                                @if($isThisStudentChoice && $isThisCorrect)
                                                    <span class="badge-flag success">
                                                        <i class="fa-solid fa-check"></i> {{ __('إجابتك المختارة (صحيحة)') }}
                                                    </span>
                                                @elseif($isThisStudentChoice && !$isThisCorrect)
                                                    <span class="badge-flag danger">
                                                        <i class="fa-solid fa-xmark"></i> {{ __('إجابتك المختارة (خاطئة)') }}
                                                    </span>
                                                @elseif($isThisCorrect)
                                                    <span class="badge-flag model">
                                                        <i class="fa-solid fa-check-double"></i> {{ __('الإجابة النموذجية المعتمدة') }}
                                                    </span>
                                                @endif
                                            </div>

                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- صندوق الإجابة النموذجية المعتمدة -->
                            @php
                                $correctOptText = $ans->question->$correctOptLetter ?? '';
                            @endphp
                            <div class="classic-outcome-box">
                                <div class="outcome-icon"><i class="fa-solid fa-lightbulb"></i></div>
                                <div class="outcome-text">
                                    <span class="outcome-label">{{ __('الإجابة الصحيحة المعتمدة:') }}</span>
                                    <strong class="outcome-val">
                                        {{ $arLetters[$correctOptLetter] ?? strtoupper($correctOptLetter) }}
                                        @if(!empty($correctOptText)) - {{ $correctOptText }} @endif
                                    </strong>
                                </div>
                            </div>

                        @else
                            <!-- الأسئلة المقالية والإنشائية -->
                            <div class="classic-review-essay-zone">
                                <div class="essay-heading">
                                    <i class="fa-solid fa-pen-fancy"></i>
                                    <span>{{ __('إجابتك المسجلة للاختبار:') }}</span>
                                </div>
                                <div class="essay-answer-box">
                                    <p>{{ $ans->answer_text ?? __('لم يتم تقديم إجابة نصية.') }}</p>
                                </div>

                                @if($ans->file_path)
                                <div class="essay-attachment-row">
                                    <a href="{{ \App\Support\MediaHelper::url($ans->file_path) }}" target="_blank" class="btn-attachment-chip">
                                        <i class="fa-solid fa-paperclip"></i>
                                        <span>{{ __('معاينة الملف أو الرسم المرفق مع الحل') }}</span>
                                    </a>
                                </div>
                                @endif

                                @if(!empty($submission->teacher_notes))
                                <div class="classic-outcome-box">
                                    <div class="outcome-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                                    <div class="outcome-text">
                                        <span class="outcome-label">{{ __('توجيهات أستاذ المساق:') }}</span>
                                        <strong class="outcome-val">{{ $submission->teacher_notes }}</strong>
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endif

                    </div>

                </article>
            @endforeach
        </main>

        <!-- قسم خيارات إعادة الاختبار أو طلب الإذن -->
        <section class="classic-retake-section no-print">
            @if($submission->allow_retake)
                <div class="retake-card-granted">
                    <div class="retake-msg">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <div>
                            <h4>{{ __('مسموح لك بإعادة الاختبار الآن!') }}</h4>
                            <p>{{ __('منحك أستاذ المادة إذناً رسمياً لإعادة تأدية الاختبار لتحسين درجتك وتثبيتها.') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('student.exams.take', $submission->exam_id) }}" class="btn-start-retake">
                        <i class="fa-solid fa-rotate-right"></i>
                        <span>{{ __('بدء المحاولة الجديدة فوراً') }}</span>
                    </a>
                </div>
            @elseif($submission->retake_requested)
                <div class="retake-card-pending">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <div>
                        <h4>{{ __('طلب إذن الإعادة قيد التدقيق ⏳') }}</h4>
                        <p>{{ __('طلبك معروض حالياً لدى أستاذ المادة، وسيتم إشعارك فور اتخاذ القرار.') }}</p>
                    </div>
                </div>
            @else
                <div class="retake-card-normal">
                    <div class="retake-msg">
                        <i class="fa-solid fa-circle-question"></i>
                        <div>
                            <h4>{{ __('هل واجهت عذراً وترغب في طلب إعادة المحاولة؟') }}</h4>
                            <p>{{ __('يمكنك مراسلة أستاذ المادة وتوضيح السبب لطلب فتح محاولة إضافية للاختبار.') }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="requestRetakePrompt()" class="btn-request-retake">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>{{ __('إرسال طلب إذن إعادة') }}</span>
                    </button>
                </div>
            @endif
        </section>

        <!-- أزرار الإجراءات السفلية -->
        <div class="classic-footer-actions-bar no-print">
            <a href="{{ route('student.exams.index') }}" class="btn-footer-back">
                <i class="fa-solid fa-arrow-right"></i>
                <span>{{ __('العودة إلى سجل الاختبارات') }}</span>
            </a>
            <button type="button" onclick="window.print()" class="btn-footer-print">
                <i class="fa-solid fa-print"></i>
                <span>{{ __('طباعة المراجعة') }}</span>
            </button>
        </div>

    </div>
    @endif

</div>

<style>
    /* =========================================================
       التصميم الأكاديمي الكلاسيكي الملكي لمراجعة النتائج
       (Classic Academic Exam Review Suite - 100% Responsive)
       ========================================================= */

    :root {
        --rc-navy: #1e3a8a;
        --rc-navy-dark: #0f172a;
        --rc-navy-light: #2563eb;
        --rc-emerald: #059669;
        --rc-rose: #dc2626;
        --rc-amber: #d97706;
        --rc-border: #e2e8f0;
        --rc-surface: #ffffff;
        --rc-bg: #f8fafc;
        --rc-radius: 12px;
    }

    .classic-results-page {
        max-width: 1040px;
        margin: 0 auto;
        padding: 0 16px 80px;
        box-sizing: border-box;
        width: 100%;
        direction: rtl;
    }

    /* 1. الترويسة الأكاديمية العلوية */
    .review-page-topbar {
        background: #ffffff;
        border: 1.5px solid var(--rc-border);
        border-radius: var(--rc-radius);
        padding: 20px 24px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .topbar-right-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .subject-academic-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        color: var(--rc-navy);
        border: 1px solid #bfdbfe;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 2px 10px;
        border-radius: 50px;
        width: fit-content;
    }

    .review-exam-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--rc-navy-dark);
        margin: 0;
        line-height: 1.3;
    }

    .review-exam-subtitle {
        font-size: 0.86rem;
        color: #64748b;
        font-weight: 600;
    }

    .topbar-left-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-review-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        color: #334155;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-review-back:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .btn-review-print {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        color: var(--rc-navy);
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-review-print:hover {
        background: #eff6ff;
        border-color: var(--rc-navy-light);
    }

    /* 2. بطاقة خلاصة الدرجات والأداء (Scorecard) */
    .classic-scorecard-card {
        background: #ffffff;
        border: 1.5px solid var(--rc-border);
        border-radius: var(--rc-radius);
        padding: 22px 26px;
        margin-bottom: 24px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
    }

    .scorecard-main-display {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .score-number-badge {
        padding: 12px 24px;
        border-radius: 12px;
        display: inline-flex;
        align-items: baseline;
        gap: 6px;
    }

    .score-number-badge.grade-excellent {
        background: #ecfdf5;
        border: 2px solid #a7f3d0;
        color: #065f46;
    }

    .score-number-badge.grade-good {
        background: #eff6ff;
        border: 2px solid #bfdbfe;
        color: #1e40af;
    }

    .score-number-badge.grade-pass {
        background: #fffbeb;
        border: 2px solid #fde68a;
        color: #92400e;
    }

    .score-number-badge.grade-fail {
        background: #fef2f2;
        border: 2px solid #fecdd3;
        color: #991b1b;
    }

    .earned-digit {
        font-size: 2.1rem;
        font-weight: 900;
        letter-spacing: -0.5px;
    }

    .max-digit {
        font-size: 1.05rem;
        font-weight: 700;
        opacity: 0.8;
    }

    .score-percent-pod {
        padding: 12px 18px;
        border-radius: 12px;
        font-size: 1.35rem;
        font-weight: 900;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .score-percent-pod.grade-excellent { background: #10b981; color: #ffffff; }
    .score-percent-pod.grade-good { background: #2563eb; color: #ffffff; }
    .score-percent-pod.grade-pass { background: #d97706; color: #ffffff; }
    .score-percent-pod.grade-fail { background: #dc2626; color: #ffffff; }

    .scorecard-details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px 24px;
        flex: 1;
        min-width: 280px;
    }

    .sc-metric-item {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .sc-lbl {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sc-val {
        font-size: 0.94rem;
        color: var(--rc-navy-dark);
        font-weight: 800;
    }

    /* 3. شريط تنبيه الخصم أو توجيه المعلم */
    .classic-deduction-banner {
        background: #fef2f2;
        border: 1.5px solid #fecdd3;
        border-radius: var(--rc-radius);
        padding: 16px 20px;
        margin-bottom: 24px;
        color: #991b1b;
    }

    .banner-head {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }

    .banner-head h4 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
    }

    .banner-body {
        margin: 4px 0 0;
        font-size: 0.88rem;
        line-height: 1.6;
    }

    .classic-teacher-banner {
        background: #f0fdf4;
        border: 1.5px solid #bbf7d0;
        border-radius: var(--rc-radius);
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        color: #166534;
    }

    .classic-teacher-banner i {
        font-size: 1.3rem;
        margin-top: 2px;
    }

    .classic-teacher-banner p {
        margin: 4px 0 0;
        font-size: 0.9rem;
        line-height: 1.6;
    }

    /* 4. مسار مراجعة الأسئلة */
    .classic-review-questions-stream {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .classic-review-card {
        background: var(--rc-surface);
        border: 1.5px solid var(--rc-border);
        border-radius: var(--rc-radius);
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        transition: border-color 0.2s ease;
    }

    .classic-review-card.is-correct {
        border-color: #a7f3d0;
    }

    .classic-review-card.is-wrong {
        border-color: #fecdd3;
    }

    .classic-review-card.is-pending {
        border-color: #fde68a;
    }

    .card-meta-ribbon {
        background: #f8fafc;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .meta-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .q-badge-num {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--rc-navy);
        color: #ffffff;
        font-size: 0.88rem;
        font-weight: 800;
        padding: 5px 14px;
        border-radius: 6px;
    }

    .q-badge-grade {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        font-size: 0.84rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 6px;
    }

    .review-status-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.84rem;
        font-weight: 800;
        padding: 4px 14px;
        border-radius: 50px;
    }

    .review-status-chip.is-correct {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .review-status-chip.is-wrong {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .review-status-chip.is-pending {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .card-q-body {
        padding: 22px 24px;
    }

    .card-q-text {
        font-size: 1.05rem;
        font-weight: 600;
        line-height: 1.85;
        color: var(--rc-navy-dark);
        margin-bottom: 20px;
    }

    /* مرفق صورة السؤال */
    .classic-qimg-container {
        margin: 14px 0 24px;
        display: flex;
        justify-content: center;
    }

    .classic-qimg-wrapper {
        position: relative;
        display: inline-block;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        background: #f8fafc;
        max-width: 100%;
    }

    .classic-qimg-wrapper img {
        display: block;
        max-width: 100%;
        max-height: 380px;
        object-fit: contain;
    }

    .qimg-zoom-tag {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: rgba(15, 23, 42, 0.75);
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* خيارات مراجعة MCQ */
    .classic-review-choices-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 18px;
    }

    .review-choice-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px 18px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        background: #ffffff;
        position: relative;
        transition: all 0.2s ease;
    }

    .choice-letter-badge {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #f1f5f9;
        border: 1.5px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--rc-navy-dark);
        flex-shrink: 0;
    }

    .choice-text-wrap {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .choice-text-content {
        font-size: 0.98rem;
        font-weight: 600;
        line-height: 1.6;
        color: var(--rc-navy-dark);
    }

    .choice-thumb-wrap {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        overflow: hidden;
        background: #fff;
        cursor: zoom-in;
    }

    .choice-thumb-img {
        max-height: 65px;
        max-width: 140px;
        object-fit: contain;
        display: block;
    }

    .choice-result-flag {
        margin-right: auto;
        flex-shrink: 0;
    }

    .badge-flag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 800;
    }

    .badge-flag.success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .badge-flag.danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .badge-flag.model {
        background: #ecfdf5;
        color: #047857;
        border: 1px dashed #059669;
    }

    /* الحالات اللونية لاختيار الطالب */
    .review-choice-card.choice-student-correct {
        background: #f0fdf4 !important;
        border-color: #86efac !important;
    }

    .review-choice-card.choice-student-correct .choice-letter-badge {
        background: #10b981;
        border-color: #059669;
        color: #ffffff;
    }

    .review-choice-card.choice-student-wrong {
        background: #fef2f2 !important;
        border-color: #fca5a5 !important;
    }

    .review-choice-card.choice-student-wrong .choice-letter-badge {
        background: #ef4444;
        border-color: #dc2626;
        color: #ffffff;
    }

    .review-choice-card.choice-is-model-answer {
        background: #f8fafc;
        border-color: #a7f3d0;
    }

    /* صندوق الإجابة النموذجية المعتمدة */
    .classic-outcome-box {
        background: #fffbeb;
        border: 1.5px solid #fde68a;
        border-radius: 8px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #92400e;
        font-size: 0.92rem;
    }

    .outcome-icon {
        font-size: 1.25rem;
        color: #d97706;
    }

    .outcome-text {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .outcome-label {
        font-weight: 700;
    }

    .outcome-val {
        font-weight: 800;
        color: #78350f;
    }

    /* منطقة الأسئلة المقالية */
    .classic-review-essay-zone {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .essay-heading {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--rc-navy-dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .essay-answer-box {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 14px 18px;
        font-size: 0.96rem;
        line-height: 1.7;
        color: #1e293b;
    }

    .btn-attachment-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #eff6ff;
        border: 1.5px solid #bfdbfe;
        border-radius: 8px;
        color: var(--rc-navy);
        font-size: 0.86rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-attachment-chip:hover {
        background: #dbeafe;
        border-color: #93c5fd;
    }

    /* 5. قسم إعادة الاختبار والنتائج المعلقة */
    .classic-retake-section {
        margin-top: 28px;
    }

    .retake-card-granted,
    .retake-card-pending,
    .retake-card-normal {
        border-radius: var(--rc-radius);
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .retake-card-granted {
        background: #f0fdf4;
        border: 1.5px solid #86efac;
        color: #166534;
    }

    .retake-card-pending {
        background: #fffbeb;
        border: 1.5px solid #fde68a;
        color: #92400e;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .retake-card-pending i {
        font-size: 2rem;
    }

    .retake-card-pending h4 {
        margin: 0 0 4px;
        font-size: 1.05rem;
        font-weight: 800;
    }

    .retake-card-pending p {
        margin: 0;
        font-size: 0.86rem;
    }

    .retake-card-normal {
        background: #ffffff;
        border: 1.5px solid var(--rc-border);
        color: #334155;
    }

    .retake-msg {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .retake-msg i {
        font-size: 2rem;
        color: var(--rc-navy);
    }

    .retake-msg h4 {
        margin: 0 0 4px;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--rc-navy-dark);
    }

    .retake-msg p {
        margin: 0;
        font-size: 0.86rem;
        color: #64748b;
    }

    .btn-start-retake {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: #059669;
        border: none;
        border-radius: 8px;
        color: #ffffff;
        font-size: 0.94rem;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
        transition: all 0.2s ease;
    }

    .btn-start-retake:hover {
        background: #047857;
        transform: translateY(-1px);
    }

    .btn-request-retake {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 20px;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        color: var(--rc-navy);
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-request-retake:hover {
        background: #eff6ff;
        border-color: var(--rc-navy-light);
    }

    /* 6. شريط الإجراءات السفلي */
    .classic-footer-actions-bar {
        margin-top: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .btn-footer-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        color: #334155;
        font-size: 0.92rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-footer-back:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .btn-footer-print {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        color: var(--rc-navy);
        font-size: 0.92rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-footer-print:hover {
        background: #eff6ff;
        border-color: var(--rc-navy-light);
    }

    /* بطاقة النتيجة المعلقة */
    .classic-pending-card {
        background: #ffffff;
        border: 1.5px solid var(--rc-border);
        border-radius: var(--rc-radius);
        padding: 36px 24px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
    }

    .pending-icon-sphere {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #eff6ff;
        color: var(--rc-navy);
        font-size: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 18px;
    }

    .academic-context-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 3px 12px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 12px;
    }

    .pending-main-title {
        font-size: 1.4rem;
        font-weight: 900;
        color: var(--rc-navy-dark);
        margin: 0 0 10px;
    }

    .pending-success-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        padding: 4px 14px;
        border-radius: 50px;
        font-size: 0.84rem;
        font-weight: 800;
        margin-bottom: 24px;
    }

    .pending-explanation-box {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px 24px;
        max-width: 680px;
        margin: 0 auto 24px;
        text-align: right;
    }

    .explanation-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--rc-navy);
        margin: 0 0 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .explanation-desc {
        font-size: 0.92rem;
        line-height: 1.7;
        color: #334155;
        margin: 0 0 12px;
    }

    .explanation-hint {
        font-size: 0.84rem;
        color: #d97706;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
    }

    .pending-meta-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        max-width: 680px;
        margin: 0 auto 24px;
    }

    .meta-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .m-label {
        font-size: 0.78rem;
        color: #64748b;
    }

    .m-val {
        font-size: 0.92rem;
        color: var(--rc-navy-dark);
        font-weight: 800;
    }

    .pending-actions-bar {
        display: flex;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .btn-classic-return {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        color: #334155;
        font-size: 0.92rem;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-classic-return:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .btn-classic-retake {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        background: var(--rc-navy);
        border: none;
        border-radius: 8px;
        color: #ffffff;
        font-size: 0.92rem;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
    }

    /* التجاوب التام مع مختلف أحجام الشاشات */
    @media (max-width: 768px) {
        .review-page-topbar {
            flex-direction: column;
            align-items: stretch;
            padding: 16px;
            gap: 14px;
        }

        .topbar-left-actions {
            justify-content: flex-start;
            width: 100%;
        }

        .classic-scorecard-card {
            flex-direction: column;
            align-items: stretch;
            padding: 18px;
            gap: 18px;
        }

        .scorecard-main-display {
            justify-content: space-between;
        }

        .scorecard-details-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .card-meta-ribbon {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .meta-left {
            width: 100%;
            display: flex;
            justify-content: flex-end;
        }

        .card-q-body {
            padding: 16px;
        }

        .review-choice-card {
            flex-wrap: wrap;
            gap: 10px;
        }

        .choice-result-flag {
            width: 100%;
            margin-right: 0;
            padding-top: 6px;
            border-top: 1px dashed #e2e8f0;
        }

        .pending-meta-grid {
            grid-template-columns: 1fr;
        }

        .retake-card-granted,
        .retake-card-normal {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .retake-msg {
            flex-direction: column;
            text-align: center;
        }

        .btn-start-retake,
        .btn-request-retake {
            width: 100%;
            justify-content: center;
        }

        .classic-footer-actions-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-footer-back,
        .btn-footer-print {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .classic-results-page {
            padding: 0 10px 60px;
        }

        .earned-digit {
            font-size: 1.7rem;
        }

        .score-number-badge {
            padding: 8px 16px;
        }

        .score-percent-pod {
            padding: 8px 14px;
            font-size: 1.15rem;
        }

        .card-q-text {
            font-size: 0.98rem;
        }

        .review-exam-title {
            font-size: 1.15rem;
        }
    }

    /* الطباعة النظيفة */
    @media print {
        .no-print {
            display: none !important;
        }
        .classic-results-page {
            padding: 0 !important;
            max-width: 100% !important;
        }
        .classic-review-card {
            break-inside: avoid;
            page-break-inside: avoid;
            border: 1px solid #ccc !important;
            box-shadow: none !important;
        }
        .classic-scorecard-card {
            border: 1px solid #ccc !important;
            box-shadow: none !important;
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
        const box = img.closest('.classic-qimg-container') || img.closest('.choice-thumb-wrap');
        if (box) box.style.display = 'none';
        return;
    }
    const candidates = raw.split('|').filter(Boolean);
    let idx = parseInt(img.getAttribute('data-candidate-idx') || '0', 10) + 1;
    if (idx < candidates.length) {
        img.setAttribute('data-candidate-idx', idx);
        img.src = candidates[idx];
    } else {
        const box = img.closest('.classic-qimg-container') || img.closest('.choice-thumb-wrap');
        if (box) box.style.display = 'none';
    }
}
</script>
@endsection
