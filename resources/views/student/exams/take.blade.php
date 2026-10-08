@extends('layouts.app')

@section('title', $exam->title . ' | ' . __('تقديم الاختبار'))
@section('no-sidebar', 'true')

@section('content')
<div class="moodle-attempt-page">

    <!-- شريط الاختبار العلوي الجامعي الهادئ بنمط مودل (Moodle Topbar) -->
    <header class="moodle-quiz-topbar">
        <div class="moodle-topbar-inner">
            
            <div class="moodle-topbar-title-block">
                <span class="moodle-sub-tag">{{ $exam->subject?->name_ar ?? $exam->subject?->name ?? __('مادة دراسية') }}</span>
                <h1 class="moodle-quiz-title">{{ $exam->title }}</h1>
            </div>

            <!-- عداد الوقت التنازلي الكلاسيكي الهادئ -->
            <div class="moodle-topbar-timer-block">
                <div class="moodle-timer-wrap" id="examTimerPod">
                    <i class="fa-regular fa-clock"></i>
                    <span class="timer-lbl">{{ __('الوقت المتبقي:') }}</span>
                    <span class="timer-val" id="countdown_timer">00:00:00</span>
                </div>
            </div>

            <!-- أزرار الإجراءات والتحكم الهادئة -->
            <div class="moodle-topbar-actions">
                <button type="button" class="btn-moodle-fs" onclick="toggleExamFullscreen()" id="btnFullscreen" title="{{ __('التبديل إلى وضع ملء الشاشة') }}">
                    <i class="fa-solid fa-expand" id="fullscreenIcon"></i>
                    <span id="fullscreenText">{{ __('ملء الشاشة') }}</span>
                </button>
                <button type="button" class="btn-moodle-top-finish" onclick="confirmSubmission()" title="{{ __('إنهاء المحاولة وتسليم الاختبار') }}">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>{{ __('تسليم الاختبار') }}</span>
                </button>
            </div>

        </div>
    </header>

    <!-- نموذج الاختبار الرئيسي -->
    <form id="fullExamForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="tab_switches_count" id="tabSwitchesCount" value="0">
        <input type="hidden" name="screenshots_count" id="screenshotsCount" value="0">
        <input type="hidden" name="cheating_flags" id="cheatingFlagsInput" value="[]">

        <div class="moodle-attempt-layout">

            <!-- العمود الرئيسي: مسار تدفق الأسئلة بنمط مودل الجامعي -->
            <main class="moodle-questions-column">

                @foreach($exam->questions as $index => $q)
                @php
                    $qCandidates = $q->getImageCandidates();
                    $firstQImg = !empty($qCandidates) ? $qCandidates[0] : null;
                @endphp
                <div class="que" id="q_card_{{ $q->id }}" data-question-id="{{ $q->id }}" data-index="{{ $index }}">
                    
                    <!-- الصندوق الجانبي لمعلومات وحالة السؤال (Info Block) -->
                    <div class="info">
                        <h3 class="no">{{ __('السؤال') }} <span class="qno">{{ $index + 1 }}</span></h3>
                        <div class="state">
                            <span class="state-label" id="state_q_{{ $q->id }}">{{ __('لم تتم الإجابة بعد') }}</span>
                        </div>
                        <div class="grade">
                            {{ __('الدرجة من') }} {{ number_format($q->points, 2) }}
                        </div>
                        <div class="questionflag">
                            <button type="button" class="moodle-btn-flag" onclick="toggleFlag({{ $q->id }}, {{ $index }})" id="flag_btn_{{ $q->id }}" title="{{ __('علم السؤال لمراجعته لاحقاً') }}">
                                <i class="fa-regular fa-flag" id="flag_icon_{{ $q->id }}"></i>
                                <span id="flag_text_{{ $q->id }}">{{ __('علم السؤال') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- صندوق محتوى وصياغة السؤال (Content Formulation Block) -->
                    <div class="content">
                        <div class="formulation">
                            <div class="qtext">
                                {!! nl2br(e($q->question_text)) !!}
                            </div>

                            @if(!empty($firstQImg))
                            <div class="moodle-qimage-box">
                                <img id="q_img_{{ $q->id }}"
                                     src="{{ $firstQImg }}" 
                                     alt="{{ __('مرفق السؤال') }}" 
                                     onclick="openImageModal(this.src)" 
                                     loading="lazy"
                                     data-candidates="{{ implode('|', $qCandidates) }}"
                                     data-candidate-idx="0"
                                     onerror="handleExamImageFallback(this)">
                            </div>
                            @endif

                            @if($q->type == 'mcq')
                                <div class="ablock">
                                    @foreach(['a', 'b', 'c', 'd'] as $option)
                                    @php
                                        $optCandidates = $q->getOptionImageCandidates($option);
                                        $firstOptImg = !empty($optCandidates) ? $optCandidates[0] : null;
                                        $hasOptImg = !empty($firstOptImg);
                                        $hasOptText = !empty($q->$option);
                                    @endphp
                                    @if($hasOptText || $hasOptImg)
                                    <div class="moodle-attempt-choice-row">
                                        <input 
                                            type="radio" 
                                            name="answers[{{ $q->id }}]" 
                                            id="opt_{{ $q->id }}_{{ $option }}"
                                            value="{{ $option }}" 
                                            class="moodle-attempt-radio"
                                            onchange="markAsAnswered({{ $index }}, {{ $q->id }})"
                                        >
                                        <label for="opt_{{ $q->id }}_{{ $option }}" class="moodle-attempt-choice-label">
                                            @if($hasOptText)
                                                <span class="opt-text">{{ $q->$option }}</span>
                                            @endif
                                            @if($hasOptImg)
                                                <img src="{{ $firstOptImg }}" alt="Option {{ strtoupper($option) }}" class="opt-img-thumb" onclick="event.stopPropagation(); openImageModal(this.src)">
                                            @endif
                                        </label>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="moodle-essay-zone">
                                    <label for="essay_{{ $q->id }}" class="essay-prompt-label">{{ __('الإجابة:') }}</label>
                                    <textarea 
                                        name="answers[{{ $q->id }}]" 
                                        id="essay_{{ $q->id }}"
                                        rows="5" 
                                        placeholder="{{ __('اكتب إجابتك هنا...') }}" 
                                        class="moodle-attempt-textarea" 
                                        oninput="markAsAnswered({{ $index }}, {{ $q->id }})"
                                    ></textarea>

                                    @if($q->require_file)
                                    <div class="moodle-essay-upload-box">
                                        <label class="moodle-upload-trigger">
                                            <i class="fa-solid fa-cloud-arrow-up"></i>
                                            <span>{{ __('إرفاق ملف أو صورة للحل') }}</span>
                                            <input 
                                                type="file" 
                                                name="files[{{ $q->id }}]" 
                                                accept="image/*,application/pdf" 
                                                hidden 
                                                onchange="updateFileName(this, {{ $q->id }}, {{ $index }})"
                                            >
                                        </label>
                                        <div class="moodle-selected-file-chip" id="file_name_{{ $q->id }}" style="display: none;"></div>
                                    </div>
                                    @endif
                                </div>
                            @endif

                        </div>
                    </div>

                </div>
                @endforeach

                <!-- زر إنهاء المحاولة وتسليم ورقة الاختبار بنمط مودل -->
                <div class="moodle-attempt-finish-bar">
                    <button type="button" onclick="confirmSubmission()" class="btn-moodle-grand-finish">
                        {{ __('إنهاء المحاولة وتسليم الاختبار...') }}
                    </button>
                </div>

            </main>

            <!-- العمود الجانبي: تنقل الاختبار بنمط مودل (Quiz Navigation Block) -->
            <aside class="moodle-nav-column" id="examNavSidebar">
                <div class="moodle-block-quiz-nav">
                    <h3 class="moodle-nav-header">{{ __('تنقل الاختبار') }}</h3>
                    <div class="moodle-nav-content">
                        
                        <div class="moodle-qn-grid">
                            @foreach($exam->questions as $index => $q)
                            <a href="#q_card_{{ $q->id }}" class="qnbutton" id="nav_btn_{{ $index }}" title="{{ __('الانتقال إلى السؤال رقم') }} {{ $index + 1 }}">
                                <span class="qn-top">{{ $index + 1 }}</span>
                                <span class="qn-bottom" id="nav_shade_{{ $index }}"></span>
                                <span class="qn-flag-triangle" id="nav_flag_{{ $index }}"></span>
                            </a>
                            @endforeach
                        </div>

                        <div class="moodle-nav-actions">
                            <a href="javascript:void(0)" onclick="confirmSubmission()" class="endtestlink">
                                {{ __('إنهاء المحاولة...') }}
                            </a>
                        </div>

                    </div>
                </div>
            </aside>

        </div>
    </form>

</div>

<!-- زر عائم لخريطة الأسئلة على الأجهزة المحمولة -->
<button type="button" class="ed-mobile-fab-map" onclick="toggleMobileNavDrawer()" title="{{ __('خريطة الأسئلة') }}">
    <i class="fa-solid fa-list-ol"></i>
    <span class="ed-fab-count" id="floatingAnsweredCounter">0/{{ count($exam->questions) }}</span>
</button>

<!-- نافذة تكبير الصورة عالية الدقة الكلاسيكية (High-Res Image Modal) -->
<div class="ed-modal-lightbox" id="imageModal" onclick="closeImageModal()">
    <div class="ed-lightbox-container" onclick="event.stopPropagation()">
        <button type="button" class="ed-lightbox-close" onclick="closeImageModal()" aria-label="{{ __('إغلاق') }}">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="ed-lightbox-img-wrap">
            <img id="modalImageTarget" src="" alt="{{ __('رسم توضيحي مكبر') }}">
        </div>
        <div class="ed-lightbox-footer">
            <span><i class="fa-solid fa-magnifying-glass"></i> {{ __('عرض كامل الشاشة بدقة عالية. انقر على زر الإغلاق أو اضغط Esc للخروج.') }}</span>
        </div>
    </div>
</div>

<style>
    /* =========================================================
       أنماط صفحة تقديم الاختبار بنظام مودل الجامعي الأصيل
       (Authentic Moodle Quiz Attempt Styles)
       ========================================================= */

    body.exam-pseudo-fullscreen {
        position: fixed !important;
        inset: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 99999 !important;
        overflow-y: auto !important;
        background: #f4f6f8 !important;
    }

    .moodle-attempt-page {
        max-width: 1140px;
        margin: 0 auto;
        padding: 0 16px 60px;
        box-sizing: border-box;
        width: 100%;
    }

    /* الشريط العلوي الكلاسيكي لمودل */
    .moodle-quiz-topbar {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: #ffffff;
        border-bottom: 1px solid #dee2e6;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
        padding: 12px 18px;
        border-radius: 0 0 6px 6px;
    }

    .moodle-topbar-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .moodle-topbar-title-block {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .moodle-sub-tag {
        font-size: 0.78rem;
        color: #6c757d;
        font-weight: 600;
    }

    .moodle-quiz-title {
        font-size: 1.18rem;
        font-weight: 800;
        color: #212529;
        margin: 0;
    }

    /* عداد الوقت التنازلي */
    .moodle-topbar-timer-block {
        display: flex;
        align-items: center;
    }

    .moodle-timer-wrap {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8f9fa;
        border: 1px solid #ced4da;
        padding: 7px 16px;
        border-radius: 4px;
        font-size: 0.9rem;
        color: #212529;
        font-weight: 600;
    }

    .moodle-timer-wrap i {
        color: #0f6cb0;
    }

    .timer-lbl {
        color: #495057;
    }

    .timer-val {
        font-family: 'Consolas', 'Monaco', monospace, sans-serif;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f6cb0;
        direction: ltr;
        display: inline-block;
    }

    .timer-val.timer-warning {
        color: #dc3545 !important;
        animation: moodleTimerPulse 1s infinite alternate;
    }

    @keyframes moodleTimerPulse {
        from { opacity: 1; }
        to { opacity: 0.55; }
    }

    /* أزرار الإجراءات في الشريط */
    .moodle-topbar-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-moodle-fs {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: #ffffff;
        border: 1px solid #ced4da;
        border-radius: 4px;
        color: #495057;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-moodle-fs:hover {
        background: #f8f9fa;
        border-color: #adb5bd;
    }

    .btn-moodle-top-finish {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 18px;
        background: #0f6cb0;
        border: 1px solid #0f6cb0;
        border-radius: 4px;
        color: #ffffff;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-moodle-top-finish:hover {
        background: #0b5184;
    }

    /* التخطيط العام لصفحة المحاولة */
    .moodle-attempt-layout {
        display: flex;
        gap: 24px;
        align-items: flex-start;
    }

    .moodle-questions-column {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    /* كتلة السؤال بنمط مودل (.que block) */
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
        font-size: 0.82rem;
        color: #6c757d;
        margin: 2px 0;
    }

    .que .info .state-label.answered {
        color: #198754 !important;
        font-weight: 700;
    }

    .que .info .grade {
        font-size: 0.8rem;
        color: #6c757d;
        line-height: 1.4;
    }

    .que .info .questionflag {
        margin-top: auto;
        padding-top: 8px;
    }

    .moodle-btn-flag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: transparent;
        border: none;
        padding: 0;
        color: #6c757d;
        font-size: 0.8rem;
        cursor: pointer;
        font-family: inherit;
        transition: color 0.15s ease;
    }

    .moodle-btn-flag:hover {
        color: #dc3545;
    }

    .moodle-btn-flag.flagged {
        color: #dc3545;
        font-weight: 700;
    }

    .moodle-btn-flag.flagged i {
        color: #dc3545;
    }

    /* المحتوى الرئيسي للسؤال (Content Formulation Block) */
    .que .content {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .que .content .formulation {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 18px 22px;
    }

    .que .content .formulation .qtext {
        font-size: 1rem;
        font-weight: 500;
        line-height: 1.75;
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

    /* خيارات الاختيار من متعدد بنمط مودل */
    .ablock {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .moodle-attempt-choice-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        border-radius: 4px;
        transition: background 0.15s ease;
    }

    .moodle-attempt-choice-row:hover {
        background: #f8f9fa;
    }

    .moodle-attempt-radio {
        width: 17px;
        height: 17px;
        cursor: pointer;
        accent-color: #0f6cb0;
        margin: 0;
        flex-shrink: 0;
    }

    .moodle-attempt-choice-label {
        flex: 1;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
        color: #212529;
        margin: 0;
        line-height: 1.5;
    }

    .opt-img-thumb {
        max-height: 70px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        cursor: zoom-in;
    }

    /* منطقة الأسئلة المقالية */
    .moodle-essay-zone {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .essay-prompt-label {
        font-size: 0.9rem;
        font-weight: 700;
        color: #495057;
    }

    .moodle-attempt-textarea {
        width: 100%;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 10px 14px;
        font-size: 0.95rem;
        line-height: 1.6;
        font-family: inherit;
        resize: vertical;
        box-sizing: border-box;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .moodle-attempt-textarea:focus {
        border-color: #0f6cb0;
        box-shadow: 0 0 0 3px rgba(15, 108, 176, 0.15);
    }

    .moodle-essay-upload-box {
        margin-top: 6px;
    }

    .moodle-upload-trigger {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #f8f9fa;
        border: 1px dashed #adb5bd;
        border-radius: 4px;
        font-size: 0.86rem;
        color: #495057;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .moodle-upload-trigger:hover {
        background: #e9ecef;
        border-color: #0f6cb0;
        color: #0f6cb0;
    }

    .moodle-selected-file-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        font-size: 0.85rem;
        color: #198754;
        background: #e8f5e9;
        padding: 4px 10px;
        border-radius: 4px;
    }

    /* زر إنهاء المحاولة وتسليم ورقة الاختبار */
    .moodle-attempt-finish-bar {
        margin-top: 10px;
        padding: 24px 0 10px;
        text-align: center;
    }

    .btn-moodle-grand-finish {
        padding: 10px 32px;
        background: #0f6cb0;
        border: 1px solid #0f6cb0;
        color: #ffffff;
        font-size: 1rem;
        font-weight: 700;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-moodle-grand-finish:hover {
        background: #0b5184;
    }

    /* العمود الجانبي: بلوك تنقل الاختبار الكلاسيكي لمودل (Moodle Quiz Navigation Block) */
    .moodle-nav-column {
        width: 250px;
        flex-shrink: 0;
        position: sticky;
        top: 80px;
    }

    .moodle-block-quiz-nav {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .moodle-nav-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 10px 14px;
        font-size: 0.92rem;
        font-weight: 700;
        color: #212529;
        margin: 0;
    }

    .moodle-nav-content {
        padding: 14px;
    }

    .moodle-qn-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
    }

    .qnbutton {
        height: 42px;
        border: 1px solid #adb5bd;
        border-radius: 3px;
        background: #ffffff;
        position: relative;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: border-color 0.15s ease;
    }

    .qnbutton:hover {
        border-color: #0f6cb0;
    }

    .qnbutton .qn-top {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        font-weight: 700;
        color: #212529;
    }

    .qnbutton .qn-bottom {
        height: 12px;
        background: #e9ecef;
        border-top: 1px solid #ced4da;
        transition: background 0.2s ease;
    }

    /* تظليل النصف السفلي عند حل السؤال كما في مودل تماماً */
    .qnbutton.answered .qn-bottom {
        background: #6c757d !important;
    }

    /* المثلث الأحمر الصغير في أعلى الزاوية عند تعليم السؤال */
    .qnbutton .qn-flag-triangle {
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-top: 9px solid #dc3545;
        border-left: 9px solid transparent;
        display: none;
    }

    html[dir="ltr"] .qnbutton .qn-flag-triangle {
        right: auto;
        left: 0;
        border-left: none;
        border-right: 9px solid transparent;
    }

    .qnbutton.flagged .qn-flag-triangle {
        display: block;
    }

    .moodle-nav-actions {
        margin-top: 14px;
        padding-top: 10px;
        border-top: 1px solid #e9ecef;
        text-align: center;
    }

    .endtestlink {
        font-size: 0.85rem;
        color: #0f6cb0;
        text-decoration: none;
        font-weight: 600;
    }

    .endtestlink:hover {
        text-decoration: underline;
    }

    /* الزر العائم للأجهزة المحمولة */
    .ed-mobile-fab-map {
        display: none;
        position: fixed;
        bottom: 24px;
        left: 24px;
        z-index: 1050;
        background: #0f6cb0;
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: 10px 18px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.25);
        cursor: pointer;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        font-weight: 700;
    }

    /* نافذة المعاينة المكبرة للصور */
    .ed-modal-lightbox {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.85);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .ed-lightbox-container {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .ed-lightbox-close {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.65);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
    }

    .ed-lightbox-img-wrap {
        padding: 12px;
        overflow: auto;
        text-align: center;
    }

    .ed-lightbox-img-wrap img {
        max-width: 100%;
        max-height: 75vh;
        object-fit: contain;
    }

    .ed-lightbox-footer {
        background: #f8f9fa;
        padding: 8px 16px;
        font-size: 0.8rem;
        color: #6c757d;
        border-top: 1px solid #dee2e6;
        text-align: center;
    }

    /* التجاوب مع الشاشات الصغيرة والجوال */
    @media (max-width: 768px) {
        .moodle-attempt-layout {
            flex-direction: column;
        }

        .moodle-nav-column {
            position: fixed;
            top: 0;
            bottom: 0;
            right: -320px;
            width: 280px;
            z-index: 2000;
            background: #fff;
            box-shadow: -4px 0 20px rgba(0,0,0,0.15);
            transition: right 0.3s ease;
            overflow-y: auto;
            padding: 16px;
        }

        .moodle-nav-column.mobile-open {
            right: 0;
        }

        .ed-mobile-fab-map {
            display: inline-flex;
        }

        .que {
            flex-direction: column;
            gap: 0;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            overflow: hidden;
        }

        .que .info {
            width: 100%;
            border: none;
            border-bottom: 1px solid #dee2e6;
            box-sizing: border-box;
        }

        .que .content .formulation {
            border: none;
            border-radius: 0;
        }
    }
</style>

<script>
    // 1. منع الرجوع العشوائي بالمتصفح أثناء سير الاختبار
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };

    // 2. تحذير تأكيدي قبل مغادرة الصفحة أثناء سير الاختبار
    window.addEventListener('beforeunload', function (e) {
        if (isExamRunning && !isSubmittingExam) {
            e.preventDefault();
            e.returnValue = 'هل أنت متأكد من مغادرة قاعة الاختبار؟ سيتم فقدان تقدمك الحالي!';
            return e.returnValue;
        }
    });

    const totalQuestions = {{ count($exam->questions) }};
    let answeredSet = new Set();
    let timeLeft = {{ $exam->duration_minutes * 60 }};
    const timerBox = document.getElementById('countdown_timer');
    let timerInterval = null;

    // حالة المراقبة والنزاهة الأكاديمية
    let isExamRunning = false;
    let isSubmittingExam = false;
    let tabSwitches = 0;
    let screenshots = 0;
    let cheatingFlags = [];
    let lastTabSwitchTime = 0;
    let lastScreenshotTime = 0;

    // ==========================================
    // إدارة ملء الشاشة الكلاسيكية
    // ==========================================
    function toggleExamFullscreen() {
        const doc = document.documentElement;
        const icon = document.getElementById('fullscreenIcon');
        const text = document.getElementById('fullscreenText');

        if (!doc.requestFullscreen && !doc.webkitRequestFullscreen && !doc.mozRequestFullScreen && !doc.msRequestFullscreen) {
            document.body.classList.toggle('exam-pseudo-fullscreen');
            updateFullscreenButtonState();
            return;
        }

        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.mozFullScreenElement && !document.msFullscreenElement) {
            if (doc.requestFullscreen) {
                doc.requestFullscreen().catch(() => {
                    document.body.classList.toggle('exam-pseudo-fullscreen');
                    updateFullscreenButtonState();
                });
            } else if (doc.webkitRequestFullscreen) {
                doc.webkitRequestFullscreen();
            } else if (doc.mozRequestFullScreen) {
                doc.mozRequestFullScreen();
            } else if (doc.msRequestFullscreen) {
                doc.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen().catch(() => {});
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            document.body.classList.remove('exam-pseudo-fullscreen');
        }
    }

    document.addEventListener('fullscreenchange', updateFullscreenButtonState);
    document.addEventListener('webkitfullscreenchange', updateFullscreenButtonState);
    document.addEventListener('mozfullscreenchange', updateFullscreenButtonState);
    document.addEventListener('MSFullscreenChange', updateFullscreenButtonState);

    function updateFullscreenButtonState() {
        const isFull = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement || document.body.classList.contains('exam-pseudo-fullscreen'));
        const icon = document.getElementById('fullscreenIcon');
        const text = document.getElementById('fullscreenText');
        if (icon) {
            icon.className = isFull ? 'fa-solid fa-compress' : 'fa-solid fa-expand';
        }
        if (text) {
            text.textContent = isFull ? '{{ __("تصغير الشاشة") }}' : '{{ __("ملء الشاشة") }}';
        }
    }

    // ==========================================
    // معالج الصور الهادئ بدون أي رسائل تنبيهية
    // ==========================================
    function handleExamImageFallback(img) {
        const raw = img.getAttribute('data-candidates');
        if (!raw) {
            const box = img.closest('.moodle-qimage-box') || img.closest('.opt-img-thumb');
            if (box) box.style.display = 'none';
            return;
        }

        const candidates = raw.split('|').filter(Boolean);
        let idx = parseInt(img.getAttribute('data-candidate-idx') || '0', 10) + 1;

        if (idx < candidates.length) {
            img.setAttribute('data-candidate-idx', idx);
            img.src = candidates[idx];
        } else {
            const box = img.closest('.moodle-qimage-box') || img.closest('.opt-img-thumb');
            if (box) box.style.display = 'none';
        }
    }

    // ==========================================
    // بدء الاختبار والعداد التنازلي
    // ==========================================
    function startExamOfficially() {
        if (isExamRunning) return;
        isExamRunning = true;

        if (!sessionStorage.getItem('exam_start_time_{{ $exam->id }}')) {
            sessionStorage.setItem('exam_start_time_{{ $exam->id }}', Date.now());
        }

        if (timerInterval) clearInterval(timerInterval);

        function renderTimer() {
            let hours = Math.floor(timeLeft / 3600);
            let mins = Math.floor((timeLeft % 3600) / 60);
            let secs = timeLeft % 60;

            let hStr = hours > 0 ? (hours < 10 ? '0' : '') + hours + ' : ' : '';
            let mStr = (mins < 10 ? '0' : '') + mins;
            let sStr = (secs < 10 ? '0' : '') + secs;

            if (timerBox) {
                timerBox.textContent = hStr + mStr + ":" + sStr;

                if (timeLeft <= 300) {
                    timerBox.classList.add('timer-warning');
                } else {
                    timerBox.classList.remove('timer-warning');
                }
            }

            if (--timeLeft < 0) {
                clearInterval(timerInterval);
                if (timerBox) timerBox.textContent = "00:00:00";
                autoSubmitExam();
            }
        }

        renderTimer();
        timerInterval = setInterval(renderTimer, 1000);
    }

    // استعادة الوقت المنقضي عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        const storedStart = sessionStorage.getItem('exam_start_time_{{ $exam->id }}');
        const totalDuration = {{ $exam->duration_minutes * 60 }};
        if (storedStart) {
            const elapsed = Math.floor((Date.now() - parseInt(storedStart, 10)) / 1000);
            if (elapsed < totalDuration) {
                timeLeft = Math.max(0, totalDuration - elapsed);
            } else {
                timeLeft = 0;
            }
        }
        startExamOfficially();
    });

    // ==========================================
    // توثيق النزاهة الأكاديمية الصامت وأنظمة كشف الغش
    // ==========================================
    function recordCheatingIncident(type, details) {
        if (!isExamRunning || isSubmittingExam) return;

        const now = Date.now();
        if (type === 'tab_switch' && (now - lastTabSwitchTime < 2500)) return;
        if (type === 'screenshot' && (now - lastScreenshotTime < 2000)) return;

        if (type === 'tab_switch') {
            lastTabSwitchTime = now;
            tabSwitches++;
        } else if (type === 'screenshot') {
            lastScreenshotTime = now;
            screenshots++;
        }

        const timeStr = new Date().toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        cheatingFlags.push({ type: type, details: details, time: timeStr });

        const swInput = document.getElementById('tabSwitchesCount');
        const scInput = document.getElementById('screenshotsCount');
        const flagsInput = document.getElementById('cheatingFlagsInput');
        if (swInput) swInput.value = tabSwitches;
        if (scInput) scInput.value = screenshots;
        if (flagsInput) flagsInput.value = JSON.stringify(cheatingFlags);

        // إشعار الخادم بصمت في الخلفية
        try {
            axios.post("{{ route('student.exams.cheatingIncident', $exam->id) }}", {
                violation_type: type,
                details: details
            }).catch(() => {});
        } catch (e) {}
    }

    // 1. رصد مغادرة التبويب (Visibility Change)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden' && isExamRunning && !isSubmittingExam) {
            recordCheatingIncident('tab_switch', 'مغادرة نافذة الاختبار');
        }
    });

    // 2. رصد فقدان تركيز النافذة (Window Blur)
    window.addEventListener('blur', function() {
        const modal = document.getElementById('imageModal');
        const isZoomOpen = modal && modal.style.display === 'flex';
        if (isExamRunning && !isSubmittingExam && !isZoomOpen) {
            recordCheatingIncident('tab_switch', 'الخروج من نافذة الاختبار');
        }
    });

    // 3. اعتراض محاولات تصوير الشاشة
    window.addEventListener('keydown', function(e) {
        if (!isExamRunning || isSubmittingExam) return;

        if (e.key === 'PrintScreen' || e.code === 'PrintScreen') {
            try { navigator.clipboard?.writeText(''); } catch(ex){}
            recordCheatingIncident('screenshot', 'الضغط على زر تصوير الشاشة');
        } else if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
            recordCheatingIncident('screenshot', 'أداة قص الشاشة');
        } else if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 'P')) {
            e.preventDefault();
            recordCheatingIncident('screenshot', 'محاولة طباعة الصفحة');
        } else if (e.metaKey && e.shiftKey && ['3', '4', '5'].includes(e.key)) {
            e.preventDefault();
            recordCheatingIncident('screenshot', 'محاولة تصوير الشاشة في نظام Mac');
        }
    });

    // 4. منع النسخ والقائمة المنسدلة
    document.addEventListener('copy', function(e) {
        if (isExamRunning && !isSubmittingExam) {
            e.preventDefault();
        }
    });

    document.addEventListener('contextmenu', function(e) {
        if (isExamRunning && !isSubmittingExam) {
            e.preventDefault();
        }
    });

    // ==========================================
    // إدارة الإجابات والتنقل والتعليم
    // ==========================================
    function markAsAnswered(index, qId) {
        answeredSet.add(qId);
        
        // تظليل النصف السفلي في قائمة تنقل مودل
        const navBtn = document.getElementById('nav_btn_' + index);
        if (navBtn) navBtn.classList.add('answered');

        // تحديث نص الحالة في صندوق معلومات السؤال
        const stateLbl = document.getElementById('state_q_' + qId);
        if (stateLbl) {
            stateLbl.textContent = '{{ __("تم حفظ الإجابة") }}';
            stateLbl.classList.add('answered');
        }

        const floatingCounter = document.getElementById('floatingAnsweredCounter');
        if (floatingCounter) floatingCounter.innerText = `${answeredSet.size}/${totalQuestions}`;
    }

    function toggleFlag(qId, index) {
        const icon = document.getElementById('flag_icon_' + qId);
        const btn = document.getElementById('flag_btn_' + qId);
        const text = document.getElementById('flag_text_' + qId);
        const navBtn = document.getElementById('nav_btn_' + index);

        if (btn) btn.classList.toggle('flagged');
        if (navBtn) navBtn.classList.toggle('flagged');

        if (icon) {
            if (icon.classList.contains('fa-regular')) {
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
                if (text) text.textContent = '{{ __("إلغاء التعليم") }}';
            } else {
                icon.classList.remove('fa-solid');
                icon.classList.add('fa-regular');
                if (text) text.textContent = '{{ __("علم السؤال") }}';
            }
        }
    }

    function updateFileName(input, qId, index) {
        if (input.files && input.files[0]) {
            const pill = document.getElementById('file_name_' + qId);
            if (pill) {
                pill.style.display = 'inline-flex';
                pill.innerHTML = `<i class="fa-solid fa-circle-check"></i> <span>${input.files[0].name}</span>`;
            }
            markAsAnswered(index, qId);
        }
    }

    // إدارة نافذة تكبير الصور
    function openImageModal(src) {
        if (!src) return;
        document.getElementById('modalImageTarget').src = src;
        document.getElementById('imageModal').style.display = 'flex';
    }

    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeImageModal();
    });

    // قائمة التنقل للهواتف
    function toggleMobileNavDrawer() {
        const sidebar = document.getElementById('examNavSidebar');
        if (sidebar) {
            sidebar.classList.toggle('mobile-open');
        }
    }

    // إغلاق الدرج عند النقر على أي سؤال بالجوال
    document.querySelectorAll('.qnbutton').forEach(cell => {
        cell.addEventListener('click', function() {
            const sidebar = document.getElementById('examNavSidebar');
            if (sidebar && sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
            }
        });
    });

    // ==========================================
    // تسليم الاختبار النهائي
    // ==========================================
    function confirmSubmission() {
        const unCount = totalQuestions - answeredSet.size;
        let warningText = `لقد قمت بالإجابة على ${answeredSet.size} من أصل ${totalQuestions} سؤالاً.`;
        if (unCount > 0) {
            warningText += `\nهنالك ${unCount} سؤالاً لم تقم بالإجابة عليها بعد.`;
        }

        Swal.fire({
            title: 'هل ترغب في إنهاء المحاولة وتسليم ورقة الاختبار؟',
            text: warningText,
            icon: unCount > 0 ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: '#0f6cb0',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، تسليم وإنهاء الاختبار',
            cancelButtonText: 'العودة للمراجعة'
        }).then((result) => {
            if (result.isConfirmed) finalizeExamSubmission();
        });
    }

    function finalizeExamSubmission() {
        isSubmittingExam = true;
        window.removeEventListener('beforeunload', window.onbeforeunload);
        window.onbeforeunload = null;

        const form = document.getElementById('fullExamForm');
        const formData = new FormData(form);

        Swal.fire({
            title: 'جاري تسليم وتوثيق الإجابات...',
            text: 'يرجى الانتظار لحين اكتمال إرسال إجاباتك.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        axios.post("{{ route('student.exams.submit', $exam->id) }}", formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(res => {
            if (res.data.success) {
                sessionStorage.removeItem('exam_start_time_{{ $exam->id }}');
                Swal.fire({
                    title: 'تم تسليم الاختبار بنجاح!',
                    text: res.data.message || 'تم توثيق إجاباتك وإصدار تقرير النتائج بنجاح.',
                    icon: 'success',
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'عرض كشف المراجعة والدرجات'
                }).then(() => {
                    window.location.href = res.data.redirect || "{{ route('student.exams.index') }}";
                });
            } else {
                Swal.fire('تنبيه', res.data.message || 'حدث خطأ أثناء معالجة الإجابات.', 'warning');
                isSubmittingExam = false;
            }
        })
        .catch(err => {
            let errorMsg = 'تعذر الاتصال بالخادم لإرسال الإجابات، يرجى المحاولة فوراً.';
            if (err.response && err.response.data && err.response.data.message) {
                errorMsg = err.response.data.message;
            }
            Swal.fire('خطأ في الاتصال', errorMsg, 'error');
            isSubmittingExam = false;
        });
    }

    function autoSubmitExam() {
        Swal.fire({
            title: 'انتهت المدة الزمنية المقررة للاختبار!',
            text: 'جاري تسليم واعتماد إجاباتك إلكترونياً وبشكل فوري...',
            icon: 'info',
            showConfirmButton: false,
            timer: 2500
        }).then(() => finalizeExamSubmission());
    }
</script>
@endsection