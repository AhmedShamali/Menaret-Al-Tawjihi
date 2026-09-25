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
            <span>{{ optional($submission->exam->subject)->name_ar ?? optional($submission->exam->subject)->name ?? __('مادة دراسية') }}</span>
        </div>

        <h1 class="ed-pending-title">{{ $submission->exam->title }}</h1>
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
                    {{ __('مراجعة الإجابات المفصلة') }} — {{ optional($submission->student)->name_ar ?? auth('student')->user()?->name_ar ?? auth()->user()?->name ?? __('طالب') }}
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
                <div class="ed-score-percentage {{ $pct >= 50 ? 'pass' : 'fail' }}">
                    <span>{{ $pct }}%</span>
                </div>
            </div>
        </header>

        @if(!empty($submission->deduction_amount) && $submission->deduction_amount > 0)
        <div style="margin: 20px 24px 0; background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 14px; padding: 18px 22px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 8px;">
                <div style="display: flex; align-items: center; gap: 10px; color: #991b1b; font-weight: 800; font-size: 1rem;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.2rem;"></i>
                    <span>{{ __('تنبيه أكاديمي: تم تطبيق خصم درجات على هذا الاختبار') }}</span>
                </div>
                <span style="background: #dc2626; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 800; font-size: 0.85rem;">
                    - {{ $submission->deduction_amount }} علامات مخصومة
                </span>
            </div>
            @if($submission->deduction_reason)
                <div style="color: #b91c1c; font-size: 0.92rem; line-height: 1.6; margin-top: 6px;">
                    <strong>سبب خصم العلامة:</strong> {{ $submission->deduction_reason }}
                </div>
            @endif
            @if($submission->teacher_notes)
                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #fca5a5; color: #7f1d1d; font-size: 0.88rem; line-height: 1.6;">
                    <strong>توجيهات المعلم:</strong> {{ $submission->teacher_notes }}
                </div>
            @endif
        </div>
        @elseif(!empty($submission->teacher_notes))
        <div style="margin: 20px 24px 0; background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 14px; padding: 16px 20px;">
            <div style="color: #166534; font-weight: 800; font-size: 0.95rem; margin-bottom: 6px;">
                <i class="fa-solid fa-comment-dots"></i> ملاحظات وتوجيهات أستاذ المساق:
            </div>
            <div style="color: #15803d; font-size: 0.9rem; line-height: 1.6;">
                {{ $submission->teacher_notes }}
            </div>
        </div>
        @endif

        <div class="ed-report-body">
            <div class="ed-report-section-title">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>{{ __('مراجعة الإجابات ونموذج التصحيح:') }}</span>
            </div>

            <div class="ed-questions-list">
                @foreach($submission->answers as $idx => $ans)
                    @php
                        $awarded = (float)$ans->points_awarded;
                        $max = (float)($ans->question ? $ans->question->points : 0);

                        if($ans->question && $ans->question->type == 'mcq') {
                            $isCorrect = ($awarded >= $max && $max > 0);
                            $statusClass = $isCorrect ? 'correct' : 'wrong';
                            $statusText = $isCorrect ? __('إجابة صحيحة') : __('إجابة خاطئة');
                            $icon = $isCorrect ? 'fa-check' : 'fa-xmark';
                        } else {
                            $statusClass = $awarded > 0 ? 'correct' : 'pending';
                            $statusText = $awarded > 0 ? __('تم التصحيح') : __('بانتظار تصحيح المعلم');
                            $icon = 'fa-clock';
                        }

                        $qCandidates = $ans->question ? $ans->question->getImageCandidates() : [];
                        $firstQImg = !empty($qCandidates) ? $qCandidates[0] : null;
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
                            <h3 class="ed-q-text">{!! nl2br(e($ans->question->question_text ?? '')) !!}</h3>

                            @if(!empty($firstQImg))
                            <div class="ed-result-q-image">
                                <img src="{{ $firstQImg }}" alt="{{ __('مرفق السؤال') }}" 
                                     onclick="window.open(this.src, '_blank')" 
                                     style="cursor: zoom-in;"
                                     title="{{ __('انقر لفتح الصورة بالحجم الكامل') }}"
                                     data-candidates="{{ implode('|', $qCandidates) }}"
                                     data-candidate-idx="0"
                                     onerror="handleResultImageFallback(this)">
                            </div>
                            @endif

                            @if($ans->question && $ans->question->type == 'mcq')
                                <div class="ed-mcq-options">
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
                                            <div class="ed-option-cell {{ $isCorrectOpt ? 'option-correct' : ($isStudentOpt && !$isCorrectOpt ? 'option-wrong' : '') }}">
                                                <strong class="opt-letter">{{ strtoupper($opt) }}:</strong>
                                                <div class="ed-result-opt-body">
                                                    @if($hasOptText)
                                                        <span class="opt-text">{{ $ans->question->$opt }}</span>
                                                    @endif
                                                    @if($hasOptImg)
                                                        <div class="ed-result-opt-img" onclick="window.open(this.querySelector('img').src, '_blank')" title="{{ __('انقر لفتح صورة الخيار بالحجم الكامل') }}">
                                                            <img src="{{ $firstOptImg }}" alt="صورة الخيار {{ strtoupper($opt) }}"
                                                                 data-candidates="{{ implode('|', $optCandidates) }}"
                                                                 data-candidate-idx="0"
                                                                 onerror="handleResultImageFallback(this)">
                                                        </div>
                                                    @endif
                                                </div>
                                                @if($isStudentOpt)
                                                    <span class="opt-student-tag">{{ __('إجابتك') }}</span>
                                                @endif
                                                @if($isCorrectOpt)
                                                    <span class="opt-correct-tag"><i class="fa-solid fa-check"></i> {{ __('الإجابة النموذجية') }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="ed-essay-answer">
                                    <span class="essay-label">{{ __('الإجابة المسجلة:') }}</span>
                                    <p class="essay-text">{{ $ans->answer_text ?? __('لم يتم تقديم إجابة نصية.') }}</p>
                                    @if($ans->file_path)
                                        <div class="essay-file-link">
                                            <a href="{{ asset('storage/' . $ans->file_path) }}" target="_blank" class="ed-btn-file">
                                                <i class="fa-solid fa-paperclip"></i>
                                                <span>{{ __('عرض الملف/الصورة المرفقة مع الحل') }}</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- قسم طلب إعادة الاختبار --}}
        <div class="ed-retake-section">
            @if($submission->allow_retake)
                <div class="retake-box granted">
                    <i class="fa-solid fa-unlock-keyhole retake-icon"></i>
                    <div>
                        <h4>{{ __('مسموح لك بإعادة الاختبار الآن') }}</h4>
                        <p>{{ __('منحك المعلم فرصة جديدة لتحسين درجتك.') }}</p>
                    </div>
                    <a href="{{ route('student.exams.take', $submission->exam_id) }}" class="ed-btn-retake-action">
                        {{ __('بدء المحاولة الجديدة') }}
                    </a>
                </div>
            @elseif($submission->retake_requested)
                <div class="retake-box pending">
                    <i class="fa-solid fa-clock-rotate-left retake-icon"></i>
                    <div>
                        <h4>{{ __('طلب الإعادة قيد المراجعة') }}</h4>
                        <p>{{ __('تم إرسال طلبك إلى أستاذ المادة، وسيتم إشعارك فور البت فيه.') }}</p>
                    </div>
                </div>
            @else
                <div class="retake-box normal">
                    <i class="fa-solid fa-rotate-right retake-icon"></i>
                    <div>
                        <h4>{{ __('هل واجهت عذراً أو ترغب في فرصة إعادة؟') }}</h4>
                        <p>{{ __('يمكنك تقديم طلب رسمي لأستاذ المساق لمنحك صلاحية إعادة التقييم.') }}</p>
                    </div>
                    <button type="button" onclick="requestRetakePrompt()" class="ed-btn-request-retake">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>{{ __('طلب إذن إعادة الاختبار') }}</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="ed-report-actions">
            <a href="{{ route('student.exams.index') }}" class="ed-btn-back">
                <i class="fa-solid fa-arrow-right arrow-icon"></i>
                <span>{{ __('العودة إلى سجل الاختبارات') }}</span>
            </a>
            <button type="button" onclick="window.print()" class="ed-btn-print">
                <i class="fa-solid fa-print"></i>
                <span>{{ __('طباعة كشف الدرجات') }}</span>
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

    /* Report Card */
    .ed-report-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .ed-report-header {
        padding: 24px 30px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .ed-academic-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.12);
        color: #93c5fd;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .ed-report-title {
        font-size: 1.4rem;
        font-weight: 800;
        margin: 6px 0 4px;
        color: #ffffff;
    }

    .ed-report-subtitle {
        color: #cbd5e1;
        font-size: 0.85rem;
        margin: 0;
    }

    .ed-report-score-box {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 14px;
        padding: 14px 22px;
        text-align: center;
        min-width: 140px;
    }

    .ed-score-label {
        display: block;
        font-size: 0.72rem;
        color: #94a3b8;
        margin-bottom: 2px;
    }

    .ed-score-values {
        display: flex;
        align-items: baseline;
        justify-content: center;
        gap: 4px;
    }

    .ed-score-earned {
        font-size: 1.8rem;
        font-weight: 900;
        color: #38bdf8;
        font-family: 'Inter', monospace;
    }

    .ed-score-total {
        font-size: 0.95rem;
        color: #cbd5e1;
        font-family: 'Inter', monospace;
    }

    .ed-score-percentage {
        font-size: 0.8rem;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 12px;
        display: inline-block;
        margin-top: 4px;
    }

    .ed-score-percentage.pass { background: rgba(16, 185, 129, 0.2); color: #34d399; }
    .ed-score-percentage.fail { background: rgba(239, 68, 68, 0.2); color: #f87171; }

    .ed-report-body {
        padding: 26px 30px;
    }

    .ed-report-section-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 10px;
    }

    .ed-questions-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .ed-question-item {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        background: #ffffff;
        transition: all 0.2s;
    }

    .ed-question-item.correct { border-right: 4px solid #10b981; }
    .ed-question-item.wrong { border-right: 4px solid #ef4444; }
    .ed-question-item.pending { border-right: 4px solid #f59e0b; }

    html[dir="ltr"] .ed-question-item.correct { border-right: 1px solid #e2e8f0; border-left: 4px solid #10b981; }
    html[dir="ltr"] .ed-question-item.wrong { border-right: 1px solid #e2e8f0; border-left: 4px solid #ef4444; }
    html[dir="ltr"] .ed-question-item.pending { border-right: 1px solid #e2e8f0; border-left: 4px solid #f59e0b; }
    html[dir="ltr"] .arrow-icon { transform: rotate(180deg); display: inline-block; }

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
        border-radius: 6px;
    }

    .ed-status-badge {
        font-size: 0.78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .correct .ed-status-badge { color: #059669; }
    .wrong .ed-status-badge { color: #dc2626; }
    .pending .ed-status-badge { color: #d97706; }

    .ed-q-text {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.7;
        margin: 0 0 14px;
    }

    .ed-result-q-image {
        margin-bottom: 14px;
        text-align: center;
    }

    .ed-result-q-image img {
        max-height: 260px;
        max-width: 100%;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .ed-mcq-options {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .ed-option-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.9rem;
    }

    .ed-result-opt-body {
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
    }

    .ed-result-opt-img {
        max-width: 130px;
        cursor: zoom-in;
    }

    .ed-result-opt-img img {
        max-height: 80px;
        max-width: 100%;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        object-fit: contain;
        background: #ffffff;
        padding: 2px;
    }

    .option-correct {
        background: #ecfdf5 !important;
        border-color: #a7f3d0 !important;
        color: #065f46;
        font-weight: 700;
    }

    .option-wrong {
        background: #fef2f2 !important;
        border-color: #fecaca !important;
        color: #991b1b;
    }

    .opt-student-tag {
        background: #fee2e2;
        color: #991b1b;
        font-size: 0.7rem;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 4px;
        margin-inline-start: auto;
    }

    .opt-correct-tag {
        background: #d1fae5;
        color: #065f46;
        font-size: 0.7rem;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 4px;
        margin-inline-start: auto;
    }

    .ed-essay-answer {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px;
    }

    .essay-label {
        display: block;
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .essay-text {
        font-size: 0.92rem;
        color: #1e293b;
        line-height: 1.7;
        margin: 0;
    }

    .ed-btn-file {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 6px 14px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 700;
        border: 1px solid #bfdbfe;
    }

    /* Retake Section */
    .ed-retake-section {
        margin: 20px 30px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    .retake-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-radius: 12px;
        gap: 14px;
        flex-wrap: wrap;
    }

    .retake-box.granted { background: #ecfdf5; border: 1px solid #a7f3d0; }
    .retake-box.pending { background: #fffbeb; border: 1px solid #fde68a; }
    .retake-box.normal { background: #f8fafc; border: 1px solid #e2e8f0; }

    .retake-box h4 { margin: 0 0 4px; font-size: 0.92rem; color: #0f172a; font-weight: 800; }
    .retake-box p { margin: 0; font-size: 0.8rem; color: #64748b; }

    .ed-btn-retake-action {
        background: #059669;
        color: #ffffff;
        padding: 8px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 800;
        font-size: 0.85rem;
    }

    .ed-btn-request-retake {
        background: #1e3a8a;
        color: #ffffff;
        padding: 8px 18px;
        border-radius: 8px;
        border: none;
        font-weight: 800;
        font-size: 0.85rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Actions */
    .ed-report-actions {
        padding: 20px 30px 30px;
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

    .ed-btn-retake {
        background: #059669;
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

    @media (max-width: 768px) {
        .ed-results-container {
            padding: 10px 8px 60px;
        }
        .ed-report-header, .ed-report-body, .ed-pending-result-card {
            padding: 16px 14px;
        }
        .ed-report-score-box {
            width: 100%;
        }
        .ed-pending-meta-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        .ed-retake-section {
            margin: 16px 14px;
        }
        .retake-box {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media print {
        .ed-report-actions, .ed-retake-section, nav, header.app-header, aside.sidebar, .top-bar {
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
