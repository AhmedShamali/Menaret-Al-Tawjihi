@extends('layouts.app')

@section('title', $exam->title . ' | ' . __('تقديم الاختبار'))
@section('no-sidebar', 'true')

@section('content')
<div class="classic-exam-page">

    <!-- الشريط الأكاديمي العلوي الفاخر (Classic Academic Topbar) -->
    <header class="classic-exam-topbar">
        <div class="topbar-inner">
            
            <!-- عنوان الاختبار والمادة -->
            <div class="topbar-brand">
                <div class="topbar-subject-badge">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>{{ $exam->subject?->name_ar ?? $exam->subject?->name ?? __('مادة دراسية') }}</span>
                </div>
                <h1 class="topbar-exam-title">{{ $exam->title }}</h1>
                <div class="topbar-schedule-pill" style="display:inline-flex; align-items:center; gap:6px; font-size:0.75rem; color:#64748b; margin-top:2px;">
                    <i class="fa-regular fa-calendar-check" style="color:#0284c7;"></i>
                    <span>{{ __('ساعات وموعد فتح الاختبار الأكاديمي:') }}</span>
                    <strong style="color:#1e293b;">{{ $exam->formatted_timing_text }}</strong>
                </div>
            </div>

            <!-- شريط النزاهة والمراقبة الأكاديمية الذكي -->
            <div class="topbar-proctoring-pod" id="proctoringPod" title="{{ __('المراقبة الأكاديمية ومكافحة الغش نشطة') }}">
                <span class="proctoring-pulse-dot" id="proctoringPulseDot"></span>
                <i class="fa-solid fa-shield-halved"></i>
                <div class="proctoring-info">
                    <span class="proctoring-caption">{{ __('نظام النزاهة') }}</span>
                    <span class="proctoring-digits" id="proctoringViolationsBadge">0 / 5</span>
                </div>
            </div>

            <!-- كبسولة التوقيت الأكاديمي الرقمية (Academic Chronometer) -->
            <div class="topbar-timer-pod" id="examTimerPod">
                <div class="timer-icon-wrap">
                    <i class="fa-regular fa-clock" id="timerClockIcon"></i>
                </div>
                <div class="timer-details">
                    <span class="timer-caption">{{ __('الوقت المتبقي') }}</span>
                    <span class="timer-digits" id="countdown_timer">00:00:00</span>
                </div>
            </div>

            <!-- أزرار التحكم والإجراءات -->
            <div class="topbar-actions">
                <button type="button" class="btn-topbar-calc" onclick="toggleExamCalculator()" id="btnCalculator" title="{{ __('فتح الحاسبة العلمية') }}">
                    <i class="fa-solid fa-calculator"></i>
                    <span>{{ __('حاسبة') }}</span>
                </button>
                <button type="button" class="btn-topbar-fs" onclick="toggleExamFullscreen()" id="btnFullscreen" title="{{ __('التبديل إلى وضع ملء الشاشة') }}">
                    <i class="fa-solid fa-expand" id="fullscreenIcon"></i>
                    <span id="fullscreenText">{{ __('ملء الشاشة') }}</span>
                </button>
                <button type="button" class="btn-topbar-submit" onclick="confirmSubmission()" title="{{ __('إنهاء المحاولة وتسليم الاختبار') }}">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>{{ __('تسليم الاختبار') }}</span>
                </button>
            </div>

        </div>
    </header>

    <!-- نموذج تقديم الاختبار الرئيسي -->
    <form id="fullExamForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="tab_switches_count" id="tabSwitchesCount" value="0">
        <input type="hidden" name="screenshots_count" id="screenshotsCount" value="0">
        <input type="hidden" name="cheating_flags" id="cheatingFlagsInput" value="[]">

        <div class="classic-exam-layout">

            <!-- مسار الأسئلة الرئيسي -->
            <main class="classic-questions-column">

                @foreach($exam->questions as $index => $q)
                @php
                    $qCandidates = $q->getImageCandidates();
                    $firstQImg = !empty($qCandidates) ? $qCandidates[0] : null;
                @endphp
                <article class="classic-exam-card" id="q_card_{{ $q->id }}" data-question-id="{{ $q->id }}" data-index="{{ $index }}">
                    
                    <!-- الشريط العلوي لمعلومات السؤال وحالته -->
                    <div class="card-meta-ribbon">
                        <div class="meta-right">
                            <span class="q-badge-num">
                                <i class="fa-solid fa-bookmark"></i>
                                <span>{{ __('السؤال') }} {{ $index + 1 }}</span>
                            </span>
                            <span class="q-badge-grade">
                                <i class="fa-solid fa-award"></i>
                                <span>{{ number_format($q->points, 2) }} {{ __('درجة') }}</span>
                            </span>
                        </div>

                        <div class="meta-left">
                            <span class="q-status-chip unanswered" id="state_q_{{ $q->id }}">
                                <i class="fa-regular fa-circle-dot"></i>
                                <span class="status-txt">{{ __('لم تتم الإجابة بعد') }}</span>
                            </span>
                            <button type="button" class="btn-classic-flag" onclick="toggleFlag({{ $q->id }}, {{ $index }})" id="flag_btn_{{ $q->id }}" title="{{ __('علم السؤال لمراجعته لاحقاً') }}">
                                <i class="fa-regular fa-flag" id="flag_icon_{{ $q->id }}"></i>
                                <span id="flag_text_{{ $q->id }}">{{ __('علم السؤال') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- محتوى وصياغة السؤال -->
                    <div class="card-q-body">
                        <div class="card-q-text">
                            {!! nl2br(e($q->question_text)) !!}
                        </div>

                        @if(!empty($firstQImg))
                        <div class="classic-qimg-container">
                            <div class="classic-qimg-wrapper" onclick="openImageModal(this.querySelector('img').src)">
                                <img id="q_img_{{ $q->id }}"
                                     src="{{ $firstQImg }}" 
                                     alt="{{ __('مرفق السؤال') }}" 
                                     loading="lazy"
                                     data-candidates="{{ implode('|', $qCandidates) }}"
                                     data-candidate-idx="0"
                                     onerror="handleExamImageFallback(this)">
                                <div class="qimg-zoom-tag">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    <span>{{ __('انقر للتكبير بدقة عالية') }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($q->type == 'true_false')
                            {{-- 1. سؤال صح أو خطأ الأكاديمي الكلاسيكي (True / False) --}}
                            <div class="classic-tf-grid">
                                <label for="tf_{{ $q->id }}_true" class="classic-tf-card true-card" id="choice_box_{{ $q->id }}_true">
                                    <input 
                                        type="radio" 
                                        name="answers[{{ $q->id }}]" 
                                        id="tf_{{ $q->id }}_true"
                                        value="true" 
                                        class="classic-radio-input"
                                        onchange="markAsAnswered({{ $index }}, {{ $q->id }}, 'true')"
                                    >
                                    <div class="tf-icon-wrap true-icon">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <div class="tf-content-wrap">
                                        <span class="tf-main-text">{{ __('صحيح / صواب (True)') }}</span>
                                        <span class="tf-sub-text">{{ __('العبارة صحيحة ومطابقة علمياً') }}</span>
                                    </div>
                                    <div class="choice-check-indicator"><div class="indicator-inner"></div></div>
                                </label>

                                <label for="tf_{{ $q->id }}_false" class="classic-tf-card false-card" id="choice_box_{{ $q->id }}_false">
                                    <input 
                                        type="radio" 
                                        name="answers[{{ $q->id }}]" 
                                        id="tf_{{ $q->id }}_false"
                                        value="false" 
                                        class="classic-radio-input"
                                        onchange="markAsAnswered({{ $index }}, {{ $q->id }}, 'false')"
                                    >
                                    <div class="tf-icon-wrap false-icon">
                                        <i class="fa-solid fa-xmark"></i>
                                    </div>
                                    <div class="tf-content-wrap">
                                        <span class="tf-main-text">{{ __('خطأ (False)') }}</span>
                                        <span class="tf-sub-text">{{ __('العبارة غير صحيحة علمياً') }}</span>
                                    </div>
                                    <div class="choice-check-indicator"><div class="indicator-inner"></div></div>
                                </label>
                            </div>

                        @elseif($q->type == 'mcq')
                            {{-- 2. سؤال اختيار من متعدد (MCQ) --}}
                            @php
                                $arLetters = ['a' => 'أ', 'b' => 'ب', 'c' => 'ج', 'd' => 'د'];
                                $isMulti = !empty($q->is_multiple);
                            @endphp
                            <div class="classic-choices-grid">
                                @foreach(['a', 'b', 'c', 'd'] as $option)
                                @php
                                    $optCandidates = $q->getOptionImageCandidates($option);
                                    $firstOptImg = !empty($optCandidates) ? $optCandidates[0] : null;
                                    $hasOptImg = !empty($firstOptImg);
                                    $hasOptText = !empty($q->$option);
                                @endphp
                                @if($hasOptText || $hasOptImg)
                                <label for="opt_{{ $q->id }}_{{ $option }}" class="classic-choice-card" id="choice_box_{{ $q->id }}_{{ $option }}">
                                    @if($isMulti)
                                    <input 
                                        type="checkbox" 
                                        name="answers[{{ $q->id }}][]" 
                                        id="opt_{{ $q->id }}_{{ $option }}"
                                        value="{{ $option }}" 
                                        class="classic-radio-input"
                                        onchange="markAsMultiAnswered({{ $index }}, {{ $q->id }})"
                                    >
                                    @else
                                    <input 
                                        type="radio" 
                                        name="answers[{{ $q->id }}]" 
                                        id="opt_{{ $q->id }}_{{ $option }}"
                                        value="{{ $option }}" 
                                        class="classic-radio-input"
                                        onchange="markAsAnswered({{ $index }}, {{ $q->id }}, '{{ $option }}')"
                                    >
                                    @endif
                                    
                                    <!-- الحرف الدائري الكلاسيكي -->
                                    <div class="choice-letter-badge">
                                        <span>{{ $arLetters[$option] ?? strtoupper($option) }}</span>
                                    </div>

                                    <!-- متن الخيار والصورة التوضيحية -->
                                    <div class="choice-text-wrap">
                                        @if($hasOptText)
                                            <span class="choice-text-content">{{ $q->$option }}</span>
                                        @endif
                                        @if($hasOptImg)
                                            <div class="choice-thumb-wrap ed-opt-img-wrapper" onclick="event.stopPropagation(); openImageModal('{{ $firstOptImg }}')">
                                                <img src="{{ $firstOptImg }}" alt="خيار {{ $arLetters[$option] ?? $option }}" class="choice-thumb-img" onerror="this.closest('.choice-thumb-wrap').style.display='none'">
                                                <span class="thumb-zoom-hint"><i class="fa-solid fa-expand"></i></span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- دائرة المؤشر المخصصة -->
                                    <div class="choice-check-indicator">
                                        <div class="indicator-inner"></div>
                                    </div>
                                </label>
                                @endif
                                @endforeach
                            </div>

                        @elseif($q->type == 'short_answer')
                            {{-- 3. سؤال إجابة قصيرة / إكمال الفراغ الأكاديمي (Short Answer) --}}
                            <div class="classic-short-answer-box">
                                <div class="short-answer-prompt-row">
                                    <label for="short_{{ $q->id }}" class="short-answer-lbl">
                                        <i class="fa-solid fa-i-cursor"></i>
                                        <span>{{ __('اكتب الإجابة المباشرة أو المصطلح المطلوب بدقة:') }}</span>
                                    </label>
                                    <span class="short-answer-hint">
                                        <i class="fa-solid fa-circle-info"></i> {{ __('يراعي التصحيح الذكي الفروق الطفيفة في الهمزات والمسافات.') }}
                                    </span>
                                </div>
                                <div class="short-answer-input-wrap">
                                    <input 
                                        type="text" 
                                        name="answers[{{ $q->id }}]" 
                                        id="short_{{ $q->id }}"
                                        class="classic-short-input" 
                                        placeholder="{{ __('أدخل الكلمة أو الرقم أو الإجابة هنا...') }}"
                                        autocomplete="off"
                                        oninput="markAsTextAnswered(this, {{ $index }}, {{ $q->id }})"
                                    >
                                    <span class="short-input-badge"><i class="fa-solid fa-pen-clip"></i></span>
                                </div>
                            </div>

                        @elseif($q->type == 'matching')
                            {{-- 4. سؤال توصيل وسحب وإفلات المربعات (Matching & Drag-and-Drop) --}}
                            @php
                                $pairs = $q->getParsedMatchingPairs();
                                // خلط أزواج الطرف الأول (المفاهيم/الأسئلة) عشوائياً للطالب لمنع التخمين والغش
                                $shuffledPairs = collect($pairs)->shuffle()->values();
                                // خلط بنك الخيارات المتاحة للمطابقة في الطرف الثاني عشوائياً
                                $rightOptions = collect($pairs)->pluck('right')->filter()->shuffle()->values();
                            @endphp
                            <div class="classic-matching-container" id="matching_container_{{ $q->id }}" data-qid="{{ $q->id }}" data-idx="{{ $index }}">
                                <div class="matching-guide-bar">
                                    <i class="fa-solid fa-diagram-project"></i>
                                    <span>{{ __('قم بمطابقة كل عنصر مع ما يناسبه بالسحب والإفلات أو النقر المباشر أو القائمة المنسدلة:') }}</span>
                                </div>

                                <input type="hidden" name="answers[{{ $q->id }}]" id="matching_answer_{{ $q->id }}" value="">

                                <div class="matching-workbench-grid">
                                    <!-- عمود الأسئلة أو المفاهيم مع مناطق الإفلات (مخلوطة عشوائياً للطالب) -->
                                    <div class="matching-prompts-col">
                                        @foreach($shuffledPairs as $pIdx => $pair)
                                        @php
                                            $leftItem = $pair['left'] ?? $pair['term'] ?? '';
                                            $pairScore = isset($pair['points']) && is_numeric($pair['points']) ? (float)$pair['points'] : null;
                                        @endphp
                                        <div class="matching-target-row" data-prompt="{{ $leftItem }}">
                                            <div class="prompt-text-pill">
                                                <span class="prompt-num">{{ $pIdx + 1 }}</span>
                                                <span class="prompt-val">{{ $leftItem }}</span>
                                                @if($pairScore !== null && $pairScore > 0)
                                                    <span class="prompt-pair-pts" style="font-size: 0.72rem; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-weight: 800; margin-inline-start: 6px;" title="{{ __('درجة هذا الخيار') }}">
                                                        {{ $pairScore }} {{ __('علامة') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="prompt-connector-arrow">
                                                <i class="fa-solid fa-arrow-left"></i>
                                            </div>
                                            <div class="matching-dropzone" 
                                                 id="dropzone_{{ $q->id }}_{{ $pIdx }}" 
                                                 data-target="{{ $leftItem }}" 
                                                 ondragover="allowMatchingDrop(event)" 
                                                 ondrop="handleMatchingDrop(event, {{ $q->id }}, '{{ addslashes($leftItem) }}', {{ $index }})"
                                                 onclick="handleDropzoneClick(this, {{ $q->id }}, '{{ addslashes($leftItem) }}', {{ $index }})">
                                                <span class="dropzone-placeholder">{{ __('اسحب الخيار أو انقر للربط...') }}</span>
                                                <div class="dropped-chip" style="display: none;">
                                                    <span class="chip-text"></span>
                                                    <button type="button" class="btn-clear-match" onclick="clearMatchPair(event, {{ $q->id }}, '{{ addslashes($leftItem) }}', {{ $index }})">&times;</button>
                                                </div>
                                            </div>
                                            <!-- بديل القائمة المنسدلة للتوافق مع شاشات اللمس القديمة -->
                                            <select class="matching-fallback-select" onchange="handleMatchingSelect(this, {{ $q->id }}, '{{ addslashes($leftItem) }}', {{ $index }})">
                                                <option value="">{{ __('-- اختر المقابل --') }}</option>
                                                @foreach($rightOptions as $rOpt)
                                                    <option value="{{ $rOpt }}">{{ $rOpt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endforeach
                                    </div>

                                    <!-- بنك الخيارات القابلة للسحب والنقر -->
                                    <div class="matching-pool-col">
                                        <div class="pool-title">
                                            <i class="fa-solid fa-boxes-stacked"></i>
                                            <span>{{ __('بنك الخيارات المتاحة للربط:') }}</span>
                                        </div>
                                        <div class="matching-pool-chips" id="pool_{{ $q->id }}">
                                            @foreach($rightOptions as $rIdx => $rOpt)
                                            <div class="draggable-match-tile" 
                                                 id="tile_{{ $q->id }}_{{ $rIdx }}"
                                                 draggable="true" 
                                                 data-val="{{ $rOpt }}" 
                                                 ondragstart="handleMatchingDragStart(event, {{ $q->id }}, '{{ addslashes($rOpt) }}')"
                                                 onclick="handleMatchingTileClick(this, {{ $q->id }})">
                                                <i class="fa-solid fa-grip-vertical drag-handle"></i>
                                                <span class="tile-label">{{ $rOpt }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @else
                            {{-- 5. الأسئلة المقالية والإنشائية (Essay / Text) --}}
                            <div class="classic-essay-box">
                                <label for="essay_{{ $q->id }}" class="essay-title-lbl">
                                    <i class="fa-solid fa-pen-fancy"></i>
                                    <span>{{ __('الإجابة النموذجية أو الشرح المطلوب:') }}</span>
                                </label>
                                <textarea 
                                    name="answers[{{ $q->id }}]" 
                                    id="essay_{{ $q->id }}"
                                    rows="5" 
                                    placeholder="{{ __('اكتب إجابتك هنا بدقة ووضوح...') }}" 
                                    class="classic-essay-textarea" 
                                    oninput="markAsTextAnswered(this, {{ $index }}, {{ $q->id }})"
                                ></textarea>
                                <div class="essay-counter-pill" id="counter_wrap_{{ $q->id }}">
                                    <span><i class="fa-solid fa-align-left"></i> <span id="word_count_{{ $q->id }}">0 {{ __('كلمة') }}</span></span>
                                    <span class="counter-sep">•</span>
                                    <span><span id="char_count_{{ $q->id }}">0 {{ __('حرف') }}</span></span>
                                </div>

                                @if($q->require_file)
                                <div class="classic-file-dropzone">
                                    <label class="dropzone-trigger">
                                        <i class="fa-solid fa-cloud-arrow-up dropzone-icon"></i>
                                        <span class="dropzone-main">{{ __('إرفاق مستند أو صورة الحل للمراجعة') }}</span>
                                        <span class="dropzone-sub">{{ __('يدعم الصور بصيغة PNG, JPG أو ملفات PDF (بحد أقصى 10MB)') }}</span>
                                        <input 
                                            type="file" 
                                            name="files[{{ $q->id }}]" 
                                            accept="image/*,application/pdf" 
                                            hidden 
                                            onchange="updateFileName(this, {{ $q->id }}, {{ $index }})"
                                        >
                                    </label>
                                    <div class="dropzone-filename-chip" id="file_name_{{ $q->id }}" style="display: none;"></div>
                                </div>
                                @endif
                            </div>
                        @endif

                    </div>

                </article>
                @endforeach

                <!-- شريط الإنهاء السفلي بعد كافة الأسئلة -->
                <div class="classic-bottom-finish-card">
                    <div class="finish-info">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <div>
                            <h4>{{ __('هل انتهيت من الإجابة على جميع الأسئلة؟') }}</h4>
                            <p>{{ __('تأكد من مراجعة إجاباتك جيداً قبل اعتماد تسليم ورقة الاختبار النهائية.') }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="confirmSubmission()" class="btn-classic-grand-submit">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>{{ __('تسليم ورقة الاختبار واعتماد النتيجة...') }}</span>
                    </button>
                </div>

            </main>

            <!-- العمود الجانبي: خريطة تنقل الاختبار الأكاديمية (Quiz Navigator) -->
            <aside class="classic-nav-column" id="examNavSidebar">
                <div class="classic-nav-card">
                    
                    <div class="nav-card-header">
                        <div class="nav-title-group">
                            <i class="fa-solid fa-compass"></i>
                            <h3>{{ __('تنقل الاختبار') }}</h3>
                        </div>
                        <span class="nav-counter-badge" id="navCounterRatio">0 / {{ count($exam->questions) }}</span>
                    </div>

                    <!-- شريط نسبة الإنجاز والتقدم -->
                    <div class="nav-progress-block">
                        <div class="progress-track">
                            <div class="progress-fill" id="examProgressBar" style="width: 0%;"></div>
                        </div>
                        <div class="progress-labels">
                            <span id="examProgressPercent">0% مكتمل</span>
                            <span>{{ count($exam->questions) }} أسئلة إجمالاً</span>
                        </div>
                    </div>

                    <div class="nav-card-body">
                        <!-- شبكة الأسئلة التفاعلية -->
                        <div class="classic-qn-grid">
                            @foreach($exam->questions as $index => $q)
                            <a href="#q_card_{{ $q->id }}" class="qn-tile" id="nav_btn_{{ $index }}" title="{{ __('الانتقال إلى السؤال رقم') }} {{ $index + 1 }}">
                                <span class="qn-num">{{ $index + 1 }}</span>
                                <span class="qn-check-icon" id="nav_check_{{ $index }}"><i class="fa-solid fa-check"></i></span>
                                <span class="qn-flag-badge" id="nav_flag_{{ $index }}"><i class="fa-solid fa-flag"></i></span>
                            </a>
                            @endforeach
                        </div>

                        <!-- مفتاح الخريطة ودليل الحالات (Legend) -->
                        <div class="nav-legend-list">
                            <div class="legend-entry">
                                <span class="legend-swatch answered"></span>
                                <span>{{ __('تمت الإجابة') }}</span>
                            </div>
                            <div class="legend-entry">
                                <span class="legend-swatch pending"></span>
                                <span>{{ __('بانتظار الإجابة') }}</span>
                            </div>
                            <div class="legend-entry">
                                <span class="legend-swatch flagged"></span>
                                <span>{{ __('معلم للمراجعة') }}</span>
                            </div>
                        </div>

                        <!-- رابط الإجراء النهائي -->
                        <div class="nav-actions-block">
                            <button type="button" onclick="confirmSubmission()" class="btn-nav-direct-finish">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>{{ __('إنهاء المحاولة وتسليم الحل...') }}</span>
                            </button>
                        </div>

                    </div>
                </div>
            </aside>

        </div>
    </form>

</div>

<!-- الزر العائم للهواتف الذكية -->
<button type="button" class="classic-mobile-fab" onclick="toggleMobileNavDrawer()" title="{{ __('خريطة الأسئلة') }}">
    <i class="fa-solid fa-list-check"></i>
    <span class="mobile-fab-label">{{ __('الأسئلة:') }}</span>
    <span class="mobile-fab-ratio" id="mobileFabRatio">0/{{ count($exam->questions) }}</span>
</button>

<!-- نافذة تكبير الصورة عالية الدقة الفاخرة (Image Lightbox) -->
<div class="classic-lightbox" id="imageModal" onclick="closeImageModal()">
    <div class="lightbox-dialog" onclick="event.stopPropagation()">
        <button type="button" class="lightbox-close-btn" onclick="closeImageModal()" aria-label="{{ __('إغلاق') }}">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="lightbox-image-viewport">
            <img id="modalImageTarget" src="" alt="{{ __('رسم توضيحي مكبر') }}">
        </div>
        <div class="lightbox-caption-bar">
            <span><i class="fa-solid fa-magnifying-glass"></i> {{ __('عرض كامل الشاشة بدقة عالية. انقر في أي مكان أو اضغط Esc للإغلاق.') }}</span>
        </div>
    </div>
</div>

<!-- نافذة تأكيد الدخول للاختبار الكلاسيكية (Moodle Pre-Exam Entry Confirmation Modal) -->
<div class="moodle-modal-backdrop" id="moodleEntryModalBackdrop" style="display: flex;">
    <div class="moodle-entry-dialog">
        <div class="moodle-entry-header">
            <div class="entry-header-branding">
                <div class="entry-logo-badge"><i class="fa-solid fa-graduation-cap"></i></div>
                <div>
                    <h2 class="entry-exam-title">{{ $exam->title }}</h2>
                    <span class="entry-exam-course">{{ $exam->course->title ?? __('المقرر الأكاديمي المعتمد') }}</span>
                </div>
            </div>
            <div class="entry-header-pill">
                <i class="fa-solid fa-shield-halved"></i>
                <span>{{ __('نظام الاختبارات الجامعي المعتمد') }}</span>
            </div>
        </div>

        <div class="moodle-entry-body">
            <!-- شبكة تفاصيل الاختبار -->
            <div class="entry-stats-grid">
                <div class="entry-stat-card">
                    <span class="stat-lbl"><i class="fa-solid fa-list-ol"></i> {{ __('عدد الأسئلة') }}</span>
                    <strong class="stat-num">{{ count($exam->questions) }}</strong>
                    <span class="stat-sub">{{ __('سؤالاً مقرراً') }}</span>
                </div>
                <div class="entry-stat-card">
                    <span class="stat-lbl"><i class="fa-regular fa-clock"></i> {{ __('المدة الزمنية') }}</span>
                    <strong class="stat-num">{{ $exam->duration_minutes }}</strong>
                    <span class="stat-sub">{{ __('دقيقة') }}</span>
                </div>
                <div class="entry-stat-card">
                    <span class="stat-lbl"><i class="fa-solid fa-award"></i> {{ __('إجمالي الدرجات') }}</span>
                    <strong class="stat-num">{{ number_format($exam->questions->sum('points'), 1) }}</strong>
                    <span class="stat-sub">{{ __('درجة') }}</span>
                </div>
                <div class="entry-stat-card">
                    <span class="stat-lbl"><i class="fa-solid fa-percent"></i> {{ __('نسبة النجاح') }}</span>
                    <strong class="stat-num">{{ $exam->pass_percentage }}%</strong>
                    <span class="stat-sub">{{ __('علامة الاجتياز') }}</span>
                </div>
            </div>

            <!-- ضوابط وتعليمات الاختبار والنزاهة الأكاديمية (Moodle Honor Code) -->
            <div class="entry-rules-box">
                <h4><i class="fa-solid fa-triangle-exclamation"></i> {{ __('تعليمات وضوابط النزاهة الأكاديمية:') }}</h4>
                <ul class="entry-rules-list">
                    <li>
                        <i class="fa-solid fa-circle-check text-green"></i>
                        <span><strong>{{ __('تفعيل الرقابة اللحظية:') }}</strong> {{ __('يُمنع منعاً باتاً مغادرة نافذة أو تبويب الاختبار؛ يتم توثيق أي خروج فوراً لدى المعلم وإدارة المنصة.') }}</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check text-green"></i>
                        <span><strong>{{ __('منع لقطات الشاشة والنسخ:') }}</strong> {{ __('محاولات قص الشاشة (Snipping Tool) أو الضغط على زر PrintScreen أو النسخ مسجلة ومحظورة أمنياً.') }}</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check text-green"></i>
                        <span><strong>{{ __('الحفظ التلقائي المستمر:') }}</strong> {{ __('يتم حفظ إجاباتك لحظياً في المتصفح تلقائياً عند اختيار أي إجابة لحماية تقدمك عند تذبذب الاتصال.') }}</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check text-green"></i>
                        <span><strong>{{ __('التسليم الآلي عند انتهاء الوقت:') }}</strong> {{ __('سيتم سحب واعتماد إجاباتك إلكترونياً بشكل فوري عند نفاد الوقت المحدد أو استنفاد المخالفات.') }}</span>
                    </li>
                </ul>
            </div>

            <!-- إقرار وتعهد الطالب -->
            <div class="entry-pledge-box">
                <label class="pledge-label">
                    <input type="checkbox" id="academicPledgeCheckbox" onchange="toggleStartAttemptButton(this.checked)">
                    <span class="pledge-text">
                        <strong>{{ __('أقر بأنني اطلعت على كافة التعليمات أعلاه، وأتعهد بالالتزام بقواعد النزاهة الأكاديمية وعدم مغادرة الامتحان.') }}</strong>
                    </span>
                </label>
            </div>
        </div>

        <div class="moodle-entry-footer">
            <a href="{{ route('student.exams.index') }}" class="btn-entry-cancel">
                <i class="fa-solid fa-arrow-right"></i>
                <span>{{ __('العودة لقائمة الاختبارات') }}</span>
            </a>
            <button type="button" id="btnStartAttempt" onclick="proceedToStartExam()" class="btn-entry-start" disabled>
                <i class="fa-solid fa-play"></i>
                <span>{{ __('بدء المحاولة الآن (Start Attempt)') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- نافذة ملخص المحاولة والمراجعة الكلاسيكية (Moodle Summary of Attempt Modal) -->
<div class="moodle-modal-backdrop" id="moodleSummaryModalBackdrop" style="display: none;">
    <div class="moodle-summary-dialog">
        <div class="moodle-summary-header">
            <div>
                <h3><i class="fa-solid fa-clipboard-list"></i> {{ __('ملخص محاولة الاختبار (Summary of Attempt)') }}</h3>
                <span class="summary-subtitle">{{ __('يرجى مراجعة حالة كافة الأسئلة بدقة قبل تأكيد التسليم النهائي:') }}</span>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeSummaryModal()">&times;</button>
        </div>
        <div class="moodle-summary-body">
            <div class="summary-table-wrap">
                <table class="moodle-summary-table">
                    <thead>
                        <tr>
                            <th>{{ __('رقم السؤال') }}</th>
                            <th>{{ __('الدرجة') }}</th>
                            <th>{{ __('حالة الإجابة') }}</th>
                            <th>{{ __('إشارة المراجعة') }}</th>
                            <th>{{ __('إجراء') }}</th>
                        </tr>
                    </thead>
                    <tbody id="summaryTableBody">
                        <!-- يتم ملؤها ديناميكياً بالجافاسكريبت -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="moodle-summary-footer">
            <button type="button" onclick="closeSummaryModal()" class="btn-summary-return">
                <i class="fa-solid fa-arrow-turn-down-right"></i>
                <span>{{ __('العودة إلى الاختبار لمراجعة الإجابات') }}</span>
            </button>
            <button type="button" onclick="finalizeExamSubmission()" class="btn-summary-finish">
                <i class="fa-solid fa-paper-plane"></i>
                <span>{{ __('تسليم الكل وإنهاء الاختبار نهائياً (Submit all and finish)') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- الآلة الحاسبة العلمية الأكاديمية (Academic Scientific Calculator) -->
<div class="exam-calc-popup" id="examCalculatorPopup" style="display: none;">
    <div class="calc-header">
        <div class="calc-title">
            <i class="fa-solid fa-calculator"></i>
            <span>{{ __('الآلة الحاسبة العلمية') }}</span>
        </div>
        <button type="button" class="btn-calc-close" onclick="toggleExamCalculator()">&times;</button>
    </div>
    <div class="calc-screen-wrap">
        <input type="text" id="calcScreen" class="calc-screen" readonly value="0">
    </div>
    <div class="calc-keypad">
        <button type="button" onclick="calcAction('clear')" class="calc-btn btn-func">AC</button>
        <button type="button" onclick="calcAction('back')" class="calc-btn btn-func"><i class="fa-solid fa-delete-left"></i></button>
        <button type="button" onclick="calcAction('(')" class="calc-btn btn-op">(</button>
        <button type="button" onclick="calcAction(')')" class="calc-btn btn-op">)</button>
        <button type="button" onclick="calcAction('pi')" class="calc-btn btn-sci">π</button>

        <button type="button" onclick="calcAction('sin')" class="calc-btn btn-sci">sin</button>
        <button type="button" onclick="calcAction('cos')" class="calc-btn btn-sci">cos</button>
        <button type="button" onclick="calcAction('tan')" class="calc-btn btn-sci">tan</button>
        <button type="button" onclick="calcAction('sqrt')" class="calc-btn btn-sci">√</button>
        <button type="button" onclick="calcAction('/')" class="calc-btn btn-op">÷</button>

        <button type="button" onclick="calcAction('7')" class="calc-btn btn-num">7</button>
        <button type="button" onclick="calcAction('8')" class="calc-btn btn-num">8</button>
        <button type="button" onclick="calcAction('9')" class="calc-btn btn-num">9</button>
        <button type="button" onclick="calcAction('*')" class="calc-btn btn-op">×</button>
        <button type="button" onclick="calcAction('pow')" class="calc-btn btn-sci">x²</button>

        <button type="button" onclick="calcAction('4')" class="calc-btn btn-num">4</button>
        <button type="button" onclick="calcAction('5')" class="calc-btn btn-num">5</button>
        <button type="button" onclick="calcAction('6')" class="calc-btn btn-num">6</button>
        <button type="button" onclick="calcAction('-')" class="calc-btn btn-op">−</button>
        <button type="button" onclick="calcAction('log')" class="calc-btn btn-sci">log</button>

        <button type="button" onclick="calcAction('1')" class="calc-btn btn-num">1</button>
        <button type="button" onclick="calcAction('2')" class="calc-btn btn-num">2</button>
        <button type="button" onclick="calcAction('3')" class="calc-btn btn-num">3</button>
        <button type="button" onclick="calcAction('+')" class="calc-btn btn-op">+</button>
        <button type="button" onclick="calcAction('percent')" class="calc-btn btn-sci">%</button>

        <button type="button" onclick="calcAction('0')" class="calc-btn btn-num btn-zero">0</button>
        <button type="button" onclick="calcAction('.')" class="calc-btn btn-num">.</button>
        <button type="button" onclick="calcAction('equals')" class="calc-btn btn-equals">=</button>
    </div>
</div>

<style>
    /* =========================================================
       التصميم الأكاديمي الكلاسيكي الفاخر لصفحة تقديم الاختبار
       (Classic Academic Luxury Exam Suite)
       ========================================================= */

    :root {
        --ac-navy: #1e3a8a;
        --ac-navy-dark: #0f172a;
        --ac-navy-light: #2563eb;
        --ac-gold: #d97706;
        --ac-gold-light: #fef3c7;
        --ac-emerald: #059669;
        --ac-emerald-light: #d1fae5;
        --ac-rose: #dc2626;
        --ac-rose-light: #fee2e2;
        --ac-bg: #f8fafc;
        --ac-surface: #ffffff;
        --ac-border: #e2e8f0;
        --ac-border-strong: #cbd5e1;
        --ac-text-primary: #1e293b;
        --ac-text-secondary: #64748b;
        --ac-text-muted: #94a3b8;
        --ac-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.06);
        --ac-shadow-md: 0 4px 14px rgba(15, 23, 42, 0.08);
        --ac-shadow-lg: 0 10px 25px rgba(15, 23, 42, 0.1);
        --ac-radius: 10px;
    }

    body {
        background-color: var(--ac-bg) !important;
        color: var(--ac-text-primary);
        font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif !important;
    }

    body.exam-pseudo-fullscreen {
        position: fixed !important;
        inset: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 99999 !important;
        overflow-y: auto !important;
        background: var(--ac-bg) !important;
    }

    .classic-exam-page {
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 20px 80px;
        box-sizing: border-box;
        width: 100%;
        direction: rtl;
    }

    /* الشريط الأكاديمي العلوي الفاخر */
    .classic-exam-topbar {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(8px);
        border-bottom: 2px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
        margin-bottom: 28px;
        padding: 14px 24px;
        border-radius: 0 0 14px 14px;
    }

    .topbar-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .topbar-brand {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 220px;
    }

    .topbar-subject-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--ac-navy);
        background: #eff6ff;
        padding: 2px 10px;
        border-radius: 50px;
        width: fit-content;
        border: 1px solid #bfdbfe;
    }

    .topbar-exam-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--ac-navy-dark);
        margin: 0;
        line-height: 1.3;
    }

    /* كبسولة العداد التنازلي الكلاسيكي */
    .topbar-timer-pod {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        padding: 8px 20px;
        border-radius: 50px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
    }

    .timer-icon-wrap {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #eff6ff;
        color: var(--ac-navy);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .timer-details {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.1;
    }

    .timer-caption {
        font-size: 0.72rem;
        color: var(--ac-text-secondary);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .timer-digits {
        font-family: 'Courier New', Courier, monospace;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--ac-navy-dark);
        letter-spacing: 1px;
        direction: ltr;
        display: inline-block;
    }

    .timer-digits.timer-warning {
        color: var(--ac-rose) !important;
    }

    .topbar-timer-pod.warning-state {
        border-color: #fca5a5 !important;
        background: #fff5f5 !important;
        animation: classicTimerPulse 1.2s infinite alternate;
    }

    .topbar-timer-pod.warning-state .timer-icon-wrap {
        background: #fee2e2;
        color: var(--ac-rose);
    }

    @keyframes classicTimerPulse {
        from { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.3); }
        to { box-shadow: 0 0 0 8px rgba(220, 38, 38, 0); }
    }

    /* أزرار الإجراءات في الشريط */
    .topbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-topbar-fs {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        color: var(--ac-text-primary);
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-topbar-fs:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .btn-topbar-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 22px;
        background: linear-gradient(135deg, var(--ac-navy) 0%, #1e40af 100%);
        border: none;
        border-radius: 8px;
        color: #ffffff;
        font-size: 0.92rem;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
        transition: all 0.2s ease;
    }

    .btn-topbar-submit:hover {
        background: linear-gradient(135deg, #172554 0%, #1e3a8a 100%);
        box-shadow: 0 6px 16px rgba(30, 58, 138, 0.35);
        transform: translateY(-1px);
    }

    /* تخطيط الصفحة العام */
    .classic-exam-layout {
        display: flex;
        gap: 28px;
        align-items: flex-start;
    }

    .classic-questions-column {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 26px;
    }

    /* بطاقة السؤال الأكاديمية الكلاسيكية الموحدة */
    .classic-exam-card {
        background: var(--ac-surface);
        border: 1.5px solid var(--ac-border);
        border-radius: var(--ac-radius);
        box-shadow: var(--ac-shadow-sm);
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .classic-exam-card:hover {
        border-color: #cbd5e1;
        box-shadow: var(--ac-shadow-md);
    }

    /* شريط معلومات رأس السؤال */
    .card-meta-ribbon {
        background: #f8fafc;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
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
        background: var(--ac-navy);
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
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
        font-size: 0.84rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 6px;
    }

    .meta-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .q-status-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 50px;
        transition: all 0.2s ease;
    }

    .q-status-chip.unanswered {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    .q-status-chip.answered {
        background: #d1fae5 !important;
        color: #065f46 !important;
        border: 1px solid #a7f3d0 !important;
    }

    .btn-classic-flag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 4px 12px;
        border-radius: 6px;
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-classic-flag:hover {
        background: #fff1f2;
        border-color: #fda4af;
        color: var(--ac-rose);
    }

    .btn-classic-flag.flagged {
        background: #fee2e2;
        border-color: #fca5a5;
        color: var(--ac-rose);
    }

    .btn-classic-flag.flagged i {
        color: var(--ac-rose);
    }

    /* متن السؤال */
    .card-q-body {
        padding: 22px 24px;
    }

    .card-q-text {
        font-size: 1.08rem;
        font-weight: 600;
        line-height: 1.85;
        color: var(--ac-text-primary);
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .classic-qimg-wrapper:hover {
        border-color: var(--ac-navy-light);
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.15);
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
        backdrop-filter: blur(4px);
    }

    /* خيارات الاختيار من متعدد الكلاسيكية الملكية */
    .classic-choices-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .classic-choice-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px 18px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        background: #ffffff;
        cursor: pointer;
        position: relative;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
    }

    .classic-choice-card:hover {
        border-color: #93c5fd;
        background: #f8fbff;
        transform: translateX(-2px);
    }

    .classic-radio-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    /* الحرف الدائري الكلاسيكي [ أ ، ب ، ج ، د ] */
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
        color: var(--ac-text-primary);
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .choice-text-wrap {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .choice-text-content {
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.6;
        color: var(--ac-text-primary);
    }

    .choice-thumb-wrap {
        position: relative;
        display: inline-flex;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        overflow: hidden;
        background: #fff;
        cursor: zoom-in;
    }

    .choice-thumb-img {
        max-height: 70px;
        max-width: 160px;
        object-fit: contain;
        display: block;
    }

    .thumb-zoom-hint {
        position: absolute;
        top: 4px;
        left: 4px;
        background: rgba(0,0,0,0.6);
        color: #fff;
        border-radius: 3px;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
    }

    /* دائرة المؤشر المخصصة */
    .choice-check-indicator {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .indicator-inner {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: transparent;
        transition: all 0.2s ease;
    }

    /* حالة التحديد النشطة للخيار */
    .classic-choice-card.is-selected {
        border-color: var(--ac-navy-light) !important;
        background: #eff6ff !important;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.12);
    }

    .classic-choice-card.is-selected .choice-letter-badge {
        background: var(--ac-navy);
        border-color: var(--ac-navy);
        color: #ffffff;
    }

    .classic-choice-card.is-selected .choice-check-indicator {
        border-color: var(--ac-navy);
        background: #ffffff;
    }

    .classic-choice-card.is-selected .indicator-inner {
        background: var(--ac-navy);
    }

    /* حقل السؤال المقالي */
    .classic-essay-box {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .essay-title-lbl {
        font-size: 0.94rem;
        font-weight: 700;
        color: var(--ac-navy-dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .classic-essay-textarea {
        width: 100%;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 14px 16px;
        font-size: 0.98rem;
        line-height: 1.7;
        font-family: inherit;
        resize: vertical;
        box-sizing: border-box;
        outline: none;
        background: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .classic-essay-textarea:focus {
        border-color: var(--ac-navy-light);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }

    .essay-counter-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.78rem;
        color: var(--ac-text-secondary);
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 4px 12px;
        border-radius: 6px;
        margin-top: 6px;
        font-weight: 700;
        align-self: flex-start;
    }

    .counter-sep {
        color: #cbd5e1;
    }

    /* منطقة إرفاق الملفات */
    .classic-file-dropzone {
        margin-top: 6px;
    }

    .dropzone-trigger {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 18px 20px;
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }

    .dropzone-trigger:hover {
        background: #eff6ff;
        border-color: var(--ac-navy-light);
    }

    .dropzone-icon {
        font-size: 1.6rem;
        color: var(--ac-navy-light);
    }

    .dropzone-main {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--ac-text-primary);
    }

    .dropzone-sub {
        font-size: 0.78rem;
        color: var(--ac-text-secondary);
    }

    .dropzone-filename-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
        font-size: 0.88rem;
        font-weight: 700;
        color: #065f46;
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        padding: 6px 14px;
        border-radius: 6px;
    }

    /* بطاقة الإنهاء السفلية */
    .classic-bottom-finish-card {
        background: #ffffff;
        border: 1.5px solid var(--ac-border);
        border-radius: var(--ac-radius);
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: var(--ac-shadow-sm);
        flex-wrap: wrap;
    }

    .finish-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .finish-info i {
        font-size: 2.2rem;
        color: var(--ac-navy);
    }

    .finish-info h4 {
        margin: 0 0 4px;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--ac-navy-dark);
    }

    .finish-info p {
        margin: 0;
        font-size: 0.88rem;
        color: var(--ac-text-secondary);
    }

    .btn-classic-grand-submit {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 28px;
        background: linear-gradient(135deg, var(--ac-navy) 0%, #1e40af 100%);
        border: none;
        border-radius: 8px;
        color: #ffffff;
        font-size: 1rem;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(30, 58, 138, 0.25);
        transition: all 0.2s ease;
    }

    .btn-classic-grand-submit:hover {
        background: linear-gradient(135deg, #172554 0%, #1e3a8a 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(30, 58, 138, 0.35);
    }

    /* العمود الجانبي لخريطة الأسئلة */
    .classic-nav-column {
        width: 290px;
        flex-shrink: 0;
        position: sticky;
        top: 96px;
    }

    .classic-nav-card {
        background: #ffffff;
        border: 1.5px solid var(--ac-border);
        border-radius: var(--ac-radius);
        box-shadow: var(--ac-shadow-md);
        overflow: hidden;
    }

    .nav-card-header {
        background: #f8fafc;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .nav-title-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-title-group i {
        color: var(--ac-navy);
        font-size: 1.1rem;
    }

    .nav-title-group h3 {
        margin: 0;
        font-size: 0.98rem;
        font-weight: 800;
        color: var(--ac-navy-dark);
    }

    .nav-counter-badge {
        font-size: 0.84rem;
        font-weight: 800;
        color: var(--ac-navy);
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 2px 10px;
        border-radius: 50px;
    }

    /* شريط نسبة الإنجاز والتقدم */
    .nav-progress-block {
        padding: 14px 18px 10px;
        border-bottom: 1px solid #f1f5f9;
    }

    .progress-track {
        height: 7px;
        background: #e2e8f0;
        border-radius: 50px;
        overflow: hidden;
        margin-bottom: 6px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        border-radius: 50px;
        transition: width 0.3s ease;
    }

    .progress-labels {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.76rem;
        font-weight: 700;
        color: var(--ac-text-secondary);
    }

    .nav-card-body {
        padding: 16px 18px;
    }

    /* شبكة الأسئلة التفاعلية */
    .classic-qn-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        margin-bottom: 18px;
    }

    .qn-tile {
        height: 44px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        position: relative;
        font-weight: 800;
        font-size: 0.92rem;
        color: var(--ac-text-primary);
        transition: all 0.2s ease;
    }

    .qn-tile:hover {
        border-color: var(--ac-navy-light);
        background: #eff6ff;
        transform: translateY(-2px);
    }

    .qn-tile .qn-check-icon {
        position: absolute;
        bottom: 2px;
        left: 2px;
        font-size: 0.65rem;
        color: #ffffff;
        display: none;
    }

    .qn-tile .qn-flag-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: var(--ac-rose);
        color: #ffffff;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.3);
    }

    /* حالة السؤال المحلول */
    .qn-tile.is-answered {
        background: #10b981 !important;
        border-color: #059669 !important;
        color: #ffffff !important;
    }

    .qn-tile.is-answered .qn-check-icon {
        display: block;
    }

    /* حالة السؤال المعلم */
    .qn-tile.is-flagged .qn-flag-badge {
        display: flex;
    }

    /* دليل الحالات (Legend) */
    .nav-legend-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
        margin-bottom: 16px;
    }

    .legend-entry {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        color: var(--ac-text-secondary);
        font-weight: 600;
    }

    .legend-swatch {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    .legend-swatch.answered {
        background: #10b981;
    }

    .legend-swatch.pending {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
    }

    .legend-swatch.flagged {
        background: var(--ac-rose);
    }

    .btn-nav-direct-finish {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 14px;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        color: var(--ac-navy);
        font-size: 0.88rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-nav-direct-finish:hover {
        background: #eff6ff;
        border-color: var(--ac-navy-light);
    }

    /* زر الهاتف العائم */
    .classic-mobile-fab {
        display: none;
        position: fixed;
        bottom: 24px;
        left: 24px;
        z-index: 1050;
        background: var(--ac-navy);
        color: #ffffff;
        border: none;
        border-radius: 50px;
        padding: 12px 22px;
        box-shadow: 0 6px 20px rgba(30, 58, 138, 0.4);
        cursor: pointer;
        align-items: center;
        gap: 8px;
        font-size: 0.92rem;
        font-weight: 800;
        transition: transform 0.2s;
    }

    .classic-mobile-fab:hover {
        transform: scale(1.04);
    }

    /* نافذة المعاينة المكبرة للصور */
    .classic-lightbox {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.88);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .lightbox-dialog {
        position: relative;
        max-width: 92vw;
        max-height: 90vh;
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
    }

    .lightbox-close-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(15, 23, 42, 0.7);
        color: #ffffff;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: background 0.2s;
    }

    .lightbox-close-btn:hover {
        background: var(--ac-rose);
    }

    .lightbox-image-viewport {
        padding: 16px;
        overflow: auto;
        text-align: center;
        background: #0f172a;
    }

    .lightbox-image-viewport img {
        max-width: 100%;
        max-height: 75vh;
        object-fit: contain;
    }

    .lightbox-caption-bar {
        background: #f8fafc;
        padding: 10px 18px;
        font-size: 0.84rem;
        color: var(--ac-text-secondary);
        border-top: 1px solid #e2e8f0;
        text-align: center;
    }

    /* التجاوب مع شاشات الجوال والأجهزة اللوحية */
    @media (max-width: 992px) {
        .classic-exam-layout {
            flex-direction: column;
        }

        .classic-nav-column {
            position: fixed;
            top: 0;
            bottom: 0;
            right: -340px;
            width: 300px;
            z-index: 2000;
            background: #ffffff;
            box-shadow: -6px 0 25px rgba(0, 0, 0, 0.2);
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            padding: 0;
        }

        .classic-nav-column.mobile-open {
            right: 0;
        }

        .classic-mobile-fab {
            display: inline-flex;
        }
    }

    @media (max-width: 640px) {
        .classic-exam-page {
            padding: 0 12px 60px;
        }

        .classic-exam-topbar {
            padding: 12px 14px;
            margin-bottom: 18px;
        }

        .topbar-inner {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .topbar-brand {
            text-align: center;
            align-items: center;
        }

        .topbar-timer-pod {
            align-self: center;
        }

        .topbar-actions {
            justify-content: center;
        }

        .card-meta-ribbon {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .meta-left {
            width: 100%;
            justify-content: space-between;
        }

        .card-q-body {
            padding: 16px 14px;
        }

        .classic-bottom-finish-card {
            flex-direction: column;
            text-align: center;
        }

        .finish-info {
            flex-direction: column;
        }
    }

    /* زر الآلة الحاسبة بالشريط العلوي */
    .btn-topbar-calc {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        color: #334155;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-topbar-calc:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* 1. صح أو خطأ الأكاديمي (True / False) */
    .classic-tf-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
        margin-top: 14px;
    }
    .classic-tf-card {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    .classic-tf-card:hover {
        border-color: #94a3b8;
        background: #f8fafc;
    }
    .classic-tf-card.true-card.is-selected {
        border-color: #059669 !important;
        background: #ecfdf5 !important;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.12);
    }
    .classic-tf-card.false-card.is-selected {
        border-color: #dc2626 !important;
        background: #fef2f2 !important;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.12);
    }
    .tf-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .true-icon {
        background: #d1fae5;
        color: #059669;
    }
    .false-icon {
        background: #fee2e2;
        color: #dc2626;
    }
    .tf-content-wrap {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        gap: 2px;
    }
    .tf-main-text {
        font-size: 1.05rem;
        font-weight: 800;
        color: #1e293b;
    }
    .tf-sub-text {
        font-size: 0.8rem;
        color: #64748b;
    }

    /* 2. إجابة قصيرة (Short Answer) */
    .classic-short-answer-box {
        margin-top: 14px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
    }
    .short-answer-prompt-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }
    .short-answer-lbl {
        font-weight: 800;
        font-size: 0.95rem;
        color: #1e3a8a;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .short-answer-hint {
        font-size: 0.8rem;
        color: #64748b;
    }
    .short-answer-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .classic-short-input {
        width: 100%;
        padding: 14px 18px 14px 45px;
        font-size: 1.05rem;
        font-family: inherit;
        border: 2px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        color: #1e293b;
        outline: none;
        transition: all 0.2s ease;
    }
    .classic-short-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }
    .short-input-badge {
        position: absolute;
        left: 16px;
        color: #94a3b8;
        font-size: 1.1rem;
        pointer-events: none;
    }

    /* 3. توصيل وسحب وإفلات (Matching) */
    .classic-matching-container {
        margin-top: 16px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
    }
    .matching-guide-bar {
        font-size: 0.92rem;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .matching-workbench-grid {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 24px;
    }
    @media (max-width: 860px) {
        .matching-workbench-grid {
            grid-template-columns: 1fr;
        }
    }
    .matching-prompts-col {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .matching-target-row {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #ffffff;
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        flex-wrap: wrap;
    }
    .prompt-text-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        min-width: 140px;
        font-weight: 700;
        color: #1e293b;
        font-size: 0.92rem;
    }
    .prompt-num {
        width: 24px;
        height: 24px;
        background: #eff6ff;
        color: #1e40af;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 800;
    }
    .prompt-connector-arrow {
        color: #94a3b8;
        font-size: 0.9rem;
    }
    .matching-dropzone {
        flex: 1.2;
        min-width: 180px;
        min-height: 42px;
        border: 2px dashed #94a3b8;
        border-radius: 8px;
        background: #fafafa;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .matching-dropzone.drag-over {
        border-color: #2563eb;
        background: #eff6ff;
    }
    .matching-dropzone.is-occupied {
        border-style: solid;
        border-color: #059669;
        background: #ecfdf5;
    }
    .dropzone-placeholder {
        font-size: 0.82rem;
        color: #94a3b8;
        pointer-events: none;
    }
    .dropped-chip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        width: 100%;
        font-weight: 700;
        color: #065f46;
        font-size: 0.9rem;
    }
    .btn-clear-match {
        background: none;
        border: none;
        color: #dc2626;
        font-size: 1.2rem;
        font-weight: bold;
        cursor: pointer;
        padding: 0 4px;
    }
    .matching-fallback-select {
        display: none;
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        font-family: inherit;
        font-size: 0.86rem;
    }
    @media (pointer: coarse) {
        .matching-fallback-select {
            display: inline-block;
        }
    }
    .matching-pool-col {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px;
    }
    .pool-title {
        font-size: 0.86rem;
        font-weight: 800;
        color: #475569;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .matching-pool-chips {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .draggable-match-tile {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        cursor: grab;
        font-weight: 700;
        font-size: 0.92rem;
        color: #1e293b;
        transition: all 0.2s ease;
        user-select: none;
    }
    .draggable-match-tile:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        transform: translateY(-1px);
    }
    .draggable-match-tile.is-selected-tile {
        background: #dbeafe !important;
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    }
    .draggable-match-tile.is-used {
        opacity: 0.45;
        text-decoration: line-through;
        cursor: not-allowed;
    }
    .drag-handle {
        color: #94a3b8;
        font-size: 0.85rem;
    }

    /* 4. النوافذ المنبثقة الكلاسيكية لمودل (Moodle Modals) */
    .moodle-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 10000;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        direction: rtl;
    }
    .moodle-entry-dialog, .moodle-summary-dialog {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 760px;
        max-height: 90vh;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        border: 2px solid #e2e8f0;
    }
    .moodle-entry-header, .moodle-summary-header {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .entry-header-branding {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .entry-logo-badge {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #eff6ff;
        color: #1e40af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .entry-exam-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .entry-exam-course {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
    }
    .entry-header-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .moodle-entry-body, .moodle-summary-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .entry-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }
    @media (max-width: 600px) {
        .entry-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .entry-stat-card {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 4px;
    }
    .stat-lbl {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 700;
    }
    .stat-num {
        font-size: 1.5rem;
        font-weight: 900;
        color: #1e3a8a;
    }
    .stat-sub {
        font-size: 0.75rem;
        color: #94a3b8;
    }
    .entry-rules-box {
        background: #fffbeb;
        border: 1.5px solid #fde68a;
        border-radius: 12px;
        padding: 16px 20px;
    }
    .entry-rules-box h4 {
        margin: 0 0 12px;
        color: #92400e;
        font-size: 0.98rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .entry-rules-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
        font-size: 0.88rem;
        color: #451a03;
        line-height: 1.6;
    }
    .entry-rules-list li {
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }
    .entry-rules-list li i {
        color: #16a34a;
        margin-top: 4px;
    }
    .entry-pledge-box {
        background: #eff6ff;
        border: 1.5px solid #bfdbfe;
        border-radius: 12px;
        padding: 16px 20px;
    }
    .pledge-label {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
    }
    .pledge-label input[type="checkbox"] {
        width: 22px;
        height: 22px;
        cursor: pointer;
        accent-color: #1e3a8a;
    }
    .pledge-text {
        font-size: 0.94rem;
        color: #1e3a8a;
        line-height: 1.5;
    }
    .moodle-entry-footer, .moodle-summary-footer {
        background: #f8fafc;
        border-top: 2px solid #e2e8f0;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }
    .btn-entry-cancel, .btn-summary-return {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        color: #475569;
        font-weight: 700;
        font-size: 0.94rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-entry-cancel:hover, .btn-summary-return:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .btn-entry-start, .btn-summary-finish {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        background: #1e3a8a;
        border: none;
        border-radius: 8px;
        color: #ffffff;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-entry-start:hover:not(:disabled), .btn-summary-finish:hover {
        background: #1e40af;
        transform: translateY(-1px);
    }
    .btn-entry-start:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* جدول ملخص المحاولة */
    .summary-table-wrap {
        overflow-x: auto;
    }
    .moodle-summary-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    .moodle-summary-table th {
        background: #f1f5f9;
        color: #334155;
        font-weight: 800;
        padding: 10px 14px;
        text-align: right;
        border-bottom: 2px solid #cbd5e1;
    }
    .moodle-summary-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
    }
    .summary-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .summary-status-badge.answered {
        background: #d1fae5;
        color: #065f46;
    }
    .summary-status-badge.unanswered {
        background: #fef3c7;
        color: #92400e;
    }
    .btn-goto-q {
        padding: 4px 10px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
    }
    .btn-close-modal {
        background: none;
        border: none;
        font-size: 1.6rem;
        color: #94a3b8;
        cursor: pointer;
    }

    /* 5. الآلة الحاسبة العلمية (Scientific Calculator) */
    .exam-calc-popup {
        position: fixed;
        bottom: 30px;
        left: 30px;
        z-index: 9999;
        width: 320px;
        background: #0f172a;
        border: 2px solid #334155;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.4);
        direction: ltr;
    }
    .calc-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #94a3b8;
        font-size: 0.86rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .btn-calc-close {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 1.2rem;
        cursor: pointer;
    }
    .calc-screen-wrap {
        margin-bottom: 12px;
    }
    .calc-screen {
        width: 100%;
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 8px;
        padding: 10px 14px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 1.4rem;
        font-weight: 800;
        color: #38bdf8;
        text-align: right;
        outline: none;
        box-sizing: border-box;
    }
    .calc-keypad {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
    }
    .calc-btn {
        padding: 10px 4px;
        border: none;
        border-radius: 6px;
        font-size: 0.92rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: center;
    }
    .calc-btn:active {
        transform: scale(0.95);
    }
    .btn-num {
        background: #334155;
        color: #ffffff;
    }
    .btn-num:hover {
        background: #475569;
    }
    .btn-op {
        background: #f59e0b;
        color: #ffffff;
    }
    .btn-sci {
        background: #1e3a8a;
        color: #93c5fd;
        font-size: 0.8rem;
    }
    .btn-func {
        background: #ef4444;
        color: #ffffff;
    }
    .btn-equals {
        background: #10b981;
        color: #ffffff;
        grid-column: span 3;
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
    const timerPod = document.getElementById('examTimerPod');
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
    // نغمة التنبيه الصوتي الأمني المباشر (Web Audio API)
    // ==========================================
    function playWarningSound() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            if (ctx.state === 'suspended') {
                ctx.resume();
            }

            const now = ctx.currentTime;
            
            // نغمة أولى تحذيرية
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'triangle';
            osc1.frequency.setValueAtTime(880, now); // A5
            gain1.gain.setValueAtTime(0.35, now);
            gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.25);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.25);

            // نغمة ثانية حادة
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sawtooth';
            osc2.frequency.setValueAtTime(587.33, now + 0.15); // D5
            gain2.gain.setValueAtTime(0.35, now + 0.15);
            gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.15);
            osc2.stop(now + 0.5);
        } catch(e) {}
    }

    // ==========================================
    // إدارة ملء الشاشة الكلاسيكية
    // ==========================================
    function toggleExamFullscreen() {
        const doc = document.documentElement;

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
    // معالج الصور الذكي والبدائل
    // ==========================================
    function handleExamImageFallback(img) {
        const raw = img.getAttribute('data-candidates');
        if (!raw) {
            const box = img.closest('.classic-qimg-container');
            if (box) box.style.display = 'none';
            return;
        }

        const candidates = raw.split('|').filter(Boolean);
        let idx = parseInt(img.getAttribute('data-candidate-idx') || '0', 10) + 1;

        if (idx < candidates.length) {
            img.setAttribute('data-candidate-idx', idx);
            img.src = candidates[idx];
        } else {
            const box = img.closest('.classic-qimg-container');
            if (box) box.style.display = 'none';
        }
    }

    // ==========================================
    // تشغيل العداد التنازلي للاختبار
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
                    if (timerPod) timerPod.classList.add('warning-state');
                } else {
                    timerBox.classList.remove('timer-warning');
                    if (timerPod) timerPod.classList.remove('warning-state');
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

    // ==========================================
    // إدارة نافذة تأكيد الدخول وبدء المحاولة الأكاديمية
    // ==========================================
    function toggleStartAttemptButton(isPledged) {
        const btn = document.getElementById('btnStartAttempt');
        if (btn) btn.disabled = !isPledged;
    }

    function proceedToStartExam() {
        const modal = document.getElementById('moodleEntryModalBackdrop');
        if (modal) modal.style.display = 'none';
        sessionStorage.setItem('exam_confirmed_{{ $exam->id }}', '1');
        startExamOfficially();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const isConfirmed = sessionStorage.getItem('exam_confirmed_{{ $exam->id }}');
        const modal = document.getElementById('moodleEntryModalBackdrop');
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

        restoreExamProgressFromStorage();

        if (isConfirmed) {
            if (modal) modal.style.display = 'none';
            startExamOfficially();
        } else {
            if (modal) modal.style.display = 'flex';
        }
    });

    // ==========================================
    // رصد وتوثيق النزاهة الأكاديمية وإشعارات الغش الفورية المرئية
    // ==========================================
    let pendingCheatingWarning = null;

    function updateViolationsUI(total, max = 5) {
        const badge = document.getElementById('proctoringViolationsBadge');
        if (badge) {
            badge.textContent = `${total} / ${max}`;
            if (total > 0) {
                badge.style.color = '#dc2626';
                badge.style.fontWeight = '900';
                const pod = document.getElementById('proctoringPod');
                if (pod) pod.style.borderColor = '#f87171';
            }
        }
    }

    function showCheatingAlertModal(alertData) {
        playWarningSound();
        const total = alertData.total || (tabSwitches + screenshots);
        const max = alertData.max || 5;
        const details = alertData.details || 'مغادرة نافذة/تبويب الاختبار';

        if (alertData.shouldForceSubmit || total >= max) {
            Swal.fire({
                icon: 'error',
                title: '🚨 تم إنهاء وسحب ورقة الاختبار!',
                html: `
                    <div style="font-size: 0.98rem; line-height: 1.8; color: #7f1d1d; direction: rtl; text-align: right;">
                        لقد استنفدت الحد الأقصى للمخالفات الأمنية المسموحة (<b>${total} من ${max}</b>).<br>
                        تم توثيق محاولات الاشتباه بالغش لدى إدارة المنصة والمعلم، وسيتم تسليم الامتحان إلكترونياً الآن.
                    </div>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'حسناً، الانتقال لكشف النتيجة',
                confirmButtonColor: '#dc2626'
            }).then(() => {
                finalizeExamSubmission();
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: '⚠️ تحذير أمني: رصد مخالفة لقواعد الاختبار!',
                html: `
                    <div style="font-size: 0.95rem; line-height: 1.8; color: #1e293b; text-align: right; direction: rtl;">
                        <div style="margin-bottom: 8px;">
                            <b>نوع المخالفة:</b> <span style="color: #b91c1c; font-weight: 700;">${details}</span>
                        </div>
                        <div style="background: #fff1f2; border: 1.5px solid #fecdd3; border-radius: 8px; padding: 10px 14px; margin-bottom: 10px;">
                            <div style="font-weight: 800; color: #9f1239; margin-bottom: 4px;">
                                المخالفة رقم <b>${total}</b> من أصل <b>${max}</b> مخالفات مسموحة.
                            </div>
                            <div style="font-size: 0.86rem; color: #881337;">
                                يُمنع منعاً باتاً مغادرة نافذة الامتحان أو فتح أي برامج أخرى أو أخذ لقطات للشاشة.
                            </div>
                        </div>
                        <div style="font-size: 0.86rem; color: #64748b;">
                            ⚠️ ملاحظة: تم إشعار المعلم والإدارة بلحظة خروجك، وأي تكرار إضافي سيؤدي لسحب الامتحان نهائياً.
                        </div>
                    </div>
                `,
                confirmButtonText: 'فهمت، متابعة الاختبار بحذر',
                confirmButtonColor: '#d97706',
                timer: 9000,
                timerProgressBar: true
            });
        }
    }

    function recordCheatingIncident(type, details, showImmediately = true) {
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

        const localTotal = tabSwitches + screenshots;
        const timeStr = new Date().toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        cheatingFlags.push({ type: type, details: details, time: timeStr, count: localTotal });

        const swInput = document.getElementById('tabSwitchesCount');
        const scInput = document.getElementById('screenshotsCount');
        const flagsInput = document.getElementById('cheatingFlagsInput');
        if (swInput) swInput.value = tabSwitches;
        if (scInput) scInput.value = screenshots;
        if (flagsInput) flagsInput.value = JSON.stringify(cheatingFlags);

        updateViolationsUI(localTotal, 5);
        playWarningSound();

        const pendingObj = {
            total: localTotal,
            max: 5,
            details: details,
            shouldForceSubmit: localTotal >= 5
        };

        if (showImmediately && document.visibilityState === 'visible') {
            showCheatingAlertModal(pendingObj);
        } else {
            pendingCheatingWarning = pendingObj;
        }

        // إرسال بلاغ فوري للخادم
        axios.post("{{ route('student.exams.cheatingIncident', $exam->id) }}", {
            violation_type: type,
            details: details
        })
        .then(res => {
            const data = res.data || {};
            const total = data.total_violations || localTotal;
            const max = data.max_allowed || 5;
            updateViolationsUI(total, max);

            const updatedObj = {
                total: total,
                max: max,
                details: data.action_text || details,
                shouldForceSubmit: !!(data.should_force_submit || total >= max)
            };

            if (document.visibilityState === 'visible') {
                showCheatingAlertModal(updatedObj);
                pendingCheatingWarning = null;
            } else {
                pendingCheatingWarning = updatedObj;
            }
        })
        .catch(() => {});
    }

    // رصد مغادرة وعودة التبويب
    document.addEventListener('visibilitychange', function() {
        if (!isExamRunning || isSubmittingExam) return;

        if (document.visibilityState === 'hidden') {
            recordCheatingIncident('tab_switch', 'مغادرة نافذة/تبويب الاختبار', false);
        } else if (document.visibilityState === 'visible') {
            if (pendingCheatingWarning) {
                showCheatingAlertModal(pendingCheatingWarning);
                pendingCheatingWarning = null;
            }
        }
    });

    window.addEventListener('blur', function() {
        const modal = document.getElementById('imageModal');
        const isZoomOpen = modal && modal.style.display === 'flex';
        if (isExamRunning && !isSubmittingExam && !isZoomOpen) {
            recordCheatingIncident('tab_switch', 'الخروج من إطار نافذة الاختبار', false);
        }
    });

    window.addEventListener('focus', function() {
        if (pendingCheatingWarning && isExamRunning && !isSubmittingExam) {
            showCheatingAlertModal(pendingCheatingWarning);
            pendingCheatingWarning = null;
        }
    });

    // اعتراض محاولات تصوير الشاشة
    window.addEventListener('keydown', function(e) {
        if (!isExamRunning || isSubmittingExam) return;

        if (e.key === 'PrintScreen' || e.code === 'PrintScreen') {
            try { navigator.clipboard?.writeText(''); } catch(ex){}
            recordCheatingIncident('screenshot', 'الضغط على زر تصوير الشاشة (PrintScreen)', true);
        } else if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
            recordCheatingIncident('screenshot', 'استخدام أداة قص الشاشة (Snipping Tool)', true);
        } else if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 'P')) {
            e.preventDefault();
            recordCheatingIncident('screenshot', 'محاولة طباعة الصفحة أو حفظها كـ PDF', true);
        } else if (e.metaKey && e.shiftKey && ['3', '4', '5'].includes(e.key)) {
            e.preventDefault();
            recordCheatingIncident('screenshot', 'محاولة تصوير الشاشة في نظام Mac', true);
        }
    });

    // منع النسخ والقائمة المنسدلة
    document.addEventListener('copy', function(e) {
        if (isExamRunning && !isSubmittingExam) e.preventDefault();
    });

    document.addEventListener('contextmenu', function(e) {
        if (isExamRunning && !isSubmittingExam) e.preventDefault();
    });

    // ==========================================
    // إدارة الإجابات والتنقل وشريط التقدم
    // ==========================================
    function markAsAnswered(index, qId, selectedOption = null) {
        answeredSet.add(qId);
        
        if (selectedOption) {
            const parentCard = document.getElementById('q_card_' + qId);
            if (parentCard) {
                parentCard.querySelectorAll('.classic-choice-card, .classic-tf-card').forEach(card => card.classList.remove('is-selected'));
            }
            const activeChoice = document.getElementById('choice_box_' + qId + '_' + selectedOption);
            if (activeChoice) activeChoice.classList.add('is-selected');
        }

        const navBtn = document.getElementById('nav_btn_' + index);
        if (navBtn) navBtn.classList.add('is-answered');

        const stateLbl = document.getElementById('state_q_' + qId);
        if (stateLbl) {
            stateLbl.innerHTML = '<i class="fa-solid fa-check"></i> <span class="status-txt">{{ __("تمت الإجابة") }}</span>';
            stateLbl.classList.remove('unanswered');
            stateLbl.classList.add('answered');
        }

        updateProgressMetrics();
        saveExamProgressToStorage();
    }

    function markAsMultiAnswered(index, qId) {
        const parentCard = document.getElementById('q_card_' + qId);
        if (!parentCard) return;
        const checked = parentCard.querySelectorAll('input[type="checkbox"]:checked');
        
        parentCard.querySelectorAll('.classic-choice-card').forEach(card => {
            const input = card.querySelector('input[type="checkbox"]');
            if (input && input.checked) {
                card.classList.add('is-selected');
            } else {
                card.classList.remove('is-selected');
            }
        });

        if (checked.length > 0) {
            answeredSet.add(qId);
            const navBtn = document.getElementById('nav_btn_' + index);
            if (navBtn) navBtn.classList.add('is-answered');
            const stateLbl = document.getElementById('state_q_' + qId);
            if (stateLbl) {
                stateLbl.innerHTML = '<i class="fa-solid fa-check"></i> <span class="status-txt">{{ __("تمت الإجابة") }}</span>';
                stateLbl.classList.remove('unanswered');
                stateLbl.classList.add('answered');
            }
        } else {
            answeredSet.delete(qId);
            const navBtn = document.getElementById('nav_btn_' + index);
            if (navBtn) navBtn.classList.remove('is-answered');
            const stateLbl = document.getElementById('state_q_' + qId);
            if (stateLbl) {
                stateLbl.innerHTML = '<i class="fa-regular fa-circle-dot"></i> <span class="status-txt">{{ __("لم تتم الإجابة بعد") }}</span>';
                stateLbl.classList.remove('answered');
                stateLbl.classList.add('unanswered');
            }
        }
        updateProgressMetrics();
        saveExamProgressToStorage();
    }

    function markAsTextAnswered(inputOrTextarea, index, qId) {
        const val = inputOrTextarea ? inputOrTextarea.value.trim() : '';
        const wordCountEl = document.getElementById('word_count_' + qId);
        const charCountEl = document.getElementById('char_count_' + qId);
        if (wordCountEl) {
            const words = val.length > 0 ? val.split(/\s+/).filter(Boolean).length : 0;
            wordCountEl.textContent = `${words} {{ __('كلمة') }}`;
        }
        if (charCountEl) {
            charCountEl.textContent = `${val.length} {{ __('حرف') }}`;
        }

        const fileInput = document.querySelector(`input[name="files[${qId}]"]`);
        const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

        if (val.length > 0 || hasFile) {
            answeredSet.add(qId);
            const navBtn = document.getElementById('nav_btn_' + index);
            if (navBtn) navBtn.classList.add('is-answered');
            const stateLbl = document.getElementById('state_q_' + qId);
            if (stateLbl) {
                stateLbl.innerHTML = '<i class="fa-solid fa-check"></i> <span class="status-txt">{{ __("تمت الإجابة") }}</span>';
                stateLbl.classList.remove('unanswered');
                stateLbl.classList.add('answered');
            }
        } else {
            answeredSet.delete(qId);
            const navBtn = document.getElementById('nav_btn_' + index);
            if (navBtn) navBtn.classList.remove('is-answered');
            const stateLbl = document.getElementById('state_q_' + qId);
            if (stateLbl) {
                stateLbl.innerHTML = '<i class="fa-regular fa-circle-dot"></i> <span class="status-txt">{{ __("لم تتم الإجابة بعد") }}</span>';
                stateLbl.classList.remove('answered');
                stateLbl.classList.add('unanswered');
            }
        }
        updateProgressMetrics();
        saveExamProgressToStorage();
    }

    function updateProgressMetrics() {
        const answeredCount = answeredSet.size;
        const percent = Math.round((answeredCount / totalQuestions) * 100);

        const ratioBadge = document.getElementById('navCounterRatio');
        if (ratioBadge) ratioBadge.innerText = `${answeredCount} / ${totalQuestions}`;

        const progressBar = document.getElementById('examProgressBar');
        if (progressBar) progressBar.style.width = `${percent}%`;

        const progressPercent = document.getElementById('examProgressPercent');
        if (progressPercent) progressPercent.innerText = `${percent}% مكتمل`;

        const fabRatio = document.getElementById('mobileFabRatio');
        if (fabRatio) fabRatio.innerText = `${answeredCount}/${totalQuestions}`;
    }

    function toggleFlag(qId, index) {
        const icon = document.getElementById('flag_icon_' + qId);
        const btn = document.getElementById('flag_btn_' + qId);
        const text = document.getElementById('flag_text_' + qId);
        const navBtn = document.getElementById('nav_btn_' + index);

        if (btn) btn.classList.toggle('flagged');
        if (navBtn) navBtn.classList.toggle('is-flagged');

        if (icon) {
            if (icon.classList.contains('fa-regular')) {
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
                if (text) text.textContent = '{{ __("إلغاء التمييز") }}';
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
                pill.innerHTML = `<i class="fa-solid fa-file-circle-check"></i> <span>${input.files[0].name}</span>`;
            }
            markAsAnswered(index, qId);
        }
    }

    // ==========================================
    // منطق التوصيل وسحب وإفلات المربعات (Matching)
    // ==========================================
    let currentDraggedMatchTile = null;
    let currentSelectedMatchTile = null;

    function handleMatchingDragStart(e, qId, val) {
        currentDraggedMatchTile = { qId, val };
        e.dataTransfer.setData('text/plain', JSON.stringify({ qId, val }));
        e.dataTransfer.effectAllowed = 'copyMove';
    }

    function allowMatchingDrop(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        const dz = e.currentTarget;
        if (dz) dz.classList.add('drag-over');
    }

    function handleMatchingDrop(e, qId, leftItem, index) {
        e.preventDefault();
        const dz = e.currentTarget;
        if (dz) dz.classList.remove('drag-over');

        let val = '';
        try {
            const data = JSON.parse(e.dataTransfer.getData('text/plain'));
            if (data.qId === qId) val = data.val;
        } catch(err) {
            if (currentDraggedMatchTile && currentDraggedMatchTile.qId === qId) {
                val = currentDraggedMatchTile.val;
            }
        }

        if (val) assignMatchPair(qId, leftItem, val, index, dz);
    }

    function handleMatchingTileClick(tile, qId) {
        if (tile.classList.contains('is-used')) return;
        const pool = document.getElementById('pool_' + qId);
        if (!pool) return;

        if (tile.classList.contains('is-selected-tile')) {
            tile.classList.remove('is-selected-tile');
            currentSelectedMatchTile = null;
        } else {
            pool.querySelectorAll('.draggable-match-tile').forEach(t => t.classList.remove('is-selected-tile'));
            tile.classList.add('is-selected-tile');
            currentSelectedMatchTile = { qId, val: tile.getAttribute('data-val') };
        }
    }

    function handleDropzoneClick(dz, qId, leftItem, index) {
        if (currentSelectedMatchTile && currentSelectedMatchTile.qId === qId) {
            assignMatchPair(qId, leftItem, currentSelectedMatchTile.val, index, dz);
            const pool = document.getElementById('pool_' + qId);
            if (pool) pool.querySelectorAll('.draggable-match-tile').forEach(t => t.classList.remove('is-selected-tile'));
            currentSelectedMatchTile = null;
        }
    }

    function handleMatchingSelect(selectElem, qId, leftItem, index) {
        const val = selectElem.value;
        const row = selectElem.closest('.matching-target-row');
        const dz = row ? row.querySelector('.matching-dropzone') : null;
        if (val) {
            assignMatchPair(qId, leftItem, val, index, dz);
        } else {
            clearMatchPair(null, qId, leftItem, index);
        }
    }

    function assignMatchPair(qId, leftItem, val, index, dz) {
        let map = {};
        const input = document.getElementById('matching_answer_' + qId);
        if (input && input.value) {
            try { map = JSON.parse(input.value) || {}; } catch(e){}
        }

        map[leftItem] = val;
        if (input) input.value = JSON.stringify(map);

        if (dz) {
            dz.classList.add('is-occupied');
            const ph = dz.querySelector('.dropzone-placeholder');
            if (ph) ph.style.display = 'none';
            const chip = dz.querySelector('.dropped-chip');
            if (chip) {
                chip.style.display = 'flex';
                const chipTxt = chip.querySelector('.chip-text');
                if (chipTxt) chipTxt.textContent = val;
            }
        }

        const row = dz ? dz.closest('.matching-target-row') : null;
        if (row) {
            const sel = row.querySelector('.matching-fallback-select');
            if (sel) sel.value = val;
        }

        markAsAnswered(index, qId);
    }

    function clearMatchPair(e, qId, leftItem, index) {
        if (e) e.stopPropagation();
        const input = document.getElementById('matching_answer_' + qId);
        let map = {};
        if (input && input.value) {
            try { map = JSON.parse(input.value) || {}; } catch(e){}
        }
        delete map[leftItem];
        if (input) input.value = Object.keys(map).length > 0 ? JSON.stringify(map) : '';

        const container = document.getElementById('matching_container_' + qId);
        if (container) {
            const row = container.querySelector(`[data-prompt="${CSS.escape(leftItem)}"]`);
            if (row) {
                const dz = row.querySelector('.matching-dropzone');
                if (dz) {
                    dz.classList.remove('is-occupied');
                    const ph = dz.querySelector('.dropzone-placeholder');
                    if (ph) ph.style.display = 'block';
                    const chip = dz.querySelector('.dropped-chip');
                    if (chip) chip.style.display = 'none';
                }
                const sel = row.querySelector('.matching-fallback-select');
                if (sel) sel.value = '';
            }
        }

        if (Object.keys(map).length === 0) {
            answeredSet.delete(qId);
            const navBtn = document.getElementById('nav_btn_' + index);
            if (navBtn) navBtn.classList.remove('is-answered');
            const stateLbl = document.getElementById('state_q_' + qId);
            if (stateLbl) {
                stateLbl.innerHTML = '<i class="fa-regular fa-circle-dot"></i> <span class="status-txt">{{ __("لم تتم الإجابة بعد") }}</span>';
                stateLbl.classList.remove('answered');
                stateLbl.classList.add('unanswered');
            }
            updateProgressMetrics();
        }
        saveExamProgressToStorage();
    }

    // ==========================================
    // الآلة الحاسبة العلمية (Scientific Calculator)
    // ==========================================
    let calcExpression = '0';
    function toggleExamCalculator() {
        const calc = document.getElementById('examCalculatorPopup');
        if (!calc) return;
        calc.style.display = (calc.style.display === 'none' || !calc.style.display) ? 'block' : 'none';
    }

    function calcAction(val) {
        const screen = document.getElementById('calcScreen');
        if (!screen) return;

        if (val === 'clear') {
            calcExpression = '0';
        } else if (val === 'back') {
            calcExpression = calcExpression.length > 1 ? calcExpression.slice(0, -1) : '0';
        } else if (val === 'equals') {
            try {
                let expr = calcExpression
                    .replace(/×/g, '*')
                    .replace(/÷/g, '/')
                    .replace(/π/g, 'Math.PI')
                    .replace(/sin\(/g, 'Math.sin(')
                    .replace(/cos\(/g, 'Math.cos(')
                    .replace(/tan\(/g, 'Math.tan(')
                    .replace(/log\(/g, 'Math.log10(')
                    .replace(/sqrt\(/g, 'Math.sqrt(');
                let res = Function('"use strict";return (' + expr + ')')();
                calcExpression = String(Math.round(res * 1000000) / 1000000);
            } catch(e) {
                calcExpression = 'Error';
            }
        } else if (val === 'sin' || val === 'cos' || val === 'tan' || val === 'log' || val === 'sqrt') {
            if (calcExpression === '0' || calcExpression === 'Error') calcExpression = '';
            calcExpression += val + '(';
        } else if (val === 'pow') {
            calcExpression += '**2';
        } else if (val === 'percent') {
            calcExpression += '/100';
        } else if (val === 'pi') {
            if (calcExpression === '0' || calcExpression === 'Error') calcExpression = '';
            calcExpression += 'π';
        } else {
            if (calcExpression === '0' || calcExpression === 'Error') calcExpression = '';
            calcExpression += val;
        }

        screen.value = calcExpression;
    }

    // ==========================================
    // نافذة ملخص المحاولة والمراجعة (Moodle Summary Modal)
    // ==========================================
    function confirmSubmission() {
        openSummaryModal();
    }

    function openSummaryModal() {
        const modal = document.getElementById('moodleSummaryModalBackdrop');
        const tbody = document.getElementById('summaryTableBody');
        if (!modal || !tbody) return;

        let rowsHtml = '';
        const cards = document.querySelectorAll('.classic-exam-card');
        cards.forEach((card, idx) => {
            const qId = parseInt(card.getAttribute('data-question-id'), 10);
            const isAnswered = answeredSet.has(qId);
            const flagBtn = document.getElementById('flag_btn_' + qId);
            const isFlagged = flagBtn && flagBtn.classList.contains('flagged');
            const pointsBadge = card.querySelector('.q-badge-grade span');
            const pointsText = pointsBadge ? pointsBadge.textContent : '';

            rowsHtml += `
                <tr>
                    <td><strong>${idx + 1}</strong></td>
                    <td><span style="color:#64748b; font-size:0.85rem;">${pointsText}</span></td>
                    <td>
                        ${isAnswered 
                            ? '<span class="summary-status-badge answered"><i class="fa-solid fa-check"></i> تم حفظ الإجابة</span>' 
                            : '<span class="summary-status-badge unanswered"><i class="fa-regular fa-clock"></i> لم تتم الإجابة بعد</span>'
                        }
                    </td>
                    <td>
                        ${isFlagged ? '<span style="color:#dc2626; font-weight:700;"><i class="fa-solid fa-flag"></i> معلّم</span>' : '<span style="color:#94a3b8;">-</span>'}
                    </td>
                    <td>
                        <button type="button" class="btn-goto-q" onclick="scrollToQuestionFromSummary(${qId})">
                            <i class="fa-solid fa-arrow-left"></i> الذهاب للسؤال
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = rowsHtml;
        modal.style.display = 'flex';
    }

    function closeSummaryModal() {
        const modal = document.getElementById('moodleSummaryModalBackdrop');
        if (modal) modal.style.display = 'none';
    }

    function scrollToQuestionFromSummary(qId) {
        closeSummaryModal();
        const target = document.getElementById('q_card_' + qId);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            target.style.transition = 'box-shadow 0.4s ease';
            target.style.boxShadow = '0 0 0 4px #2563eb';
            setTimeout(() => { target.style.boxShadow = ''; }, 2000);
        }
    }

    // ==========================================
    // الحفظ والاسترجاع التلقائي من التخزين المحلي
    // ==========================================
    function saveExamProgressToStorage() {
        try {
            const form = document.getElementById('fullExamForm');
            if (!form) return;
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                if (key !== '_token' && !key.startsWith('files[')) {
                    if (key.endsWith('[]')) {
                        if (!data[key]) data[key] = [];
                        data[key].push(value);
                    } else {
                        data[key] = value;
                    }
                }
            });
            localStorage.setItem('exam_draft_{{ $exam->id }}', JSON.stringify(data));
        } catch(e){}
    }

    function restoreExamProgressFromStorage() {
        try {
            const saved = localStorage.getItem('exam_draft_{{ $exam->id }}');
            if (!saved) return;
            const data = JSON.parse(saved);

            Object.keys(data).forEach(key => {
                const val = data[key];
                if (key.startsWith('answers[')) {
                    const match = key.match(/answers\[(\d+)\]/);
                    if (!match) return;
                    const qId = parseInt(match[1], 10);
                    const card = document.getElementById('q_card_' + qId);
                    if (!card) return;
                    const index = parseInt(card.getAttribute('data-index'), 10);

                    if (Array.isArray(val)) {
                        val.forEach(v => {
                            const cb = card.querySelector(`input[type="checkbox"][value="${v}"]`);
                            if (cb) cb.checked = true;
                        });
                        markAsMultiAnswered(index, qId);
                    } else {
                        const radio = card.querySelector(`input[type="radio"][value="${val}"]`);
                        if (radio) {
                            radio.checked = true;
                            markAsAnswered(index, qId, val);
                        }
                        const txt = card.querySelector('input[type="text"], textarea');
                        if (txt && !card.querySelector('.classic-matching-container')) {
                            txt.value = val;
                            markAsTextAnswered(txt, index, qId);
                        }
                        const matchInput = document.getElementById('matching_answer_' + qId);
                        if (matchInput && val) {
                            matchInput.value = val;
                            try {
                                const parsedMap = JSON.parse(val);
                                Object.keys(parsedMap).forEach(leftItem => {
                                    const rightVal = parsedMap[leftItem];
                                    const row = card.querySelector(`[data-prompt="${CSS.escape(leftItem)}"]`);
                                    if (row) {
                                        const dz = row.querySelector('.matching-dropzone');
                                        if (dz) {
                                            dz.classList.add('is-occupied');
                                            const ph = dz.querySelector('.dropzone-placeholder');
                                            if (ph) ph.style.display = 'none';
                                            const chip = dz.querySelector('.dropped-chip');
                                            if (chip) {
                                                chip.style.display = 'flex';
                                                const chipTxt = chip.querySelector('.chip-text');
                                                if (chipTxt) chipTxt.textContent = rightVal;
                                            }
                                        }
                                        const sel = row.querySelector('.matching-fallback-select');
                                        if (sel) sel.value = rightVal;
                                    }
                                });
                                markAsAnswered(index, qId);
                            } catch(err){}
                        }
                    }
                }
            });
        } catch(e){}
    }

    // إدارة تكبير الصور والتنقل بالجوال
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

    function toggleMobileNavDrawer() {
        const sidebar = document.getElementById('examNavSidebar');
        if (sidebar) sidebar.classList.toggle('mobile-open');
    }

    document.querySelectorAll('.qn-tile').forEach(cell => {
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
    function finalizeExamSubmission() {
        isSubmittingExam = true;
        closeSummaryModal();
        window.removeEventListener('beforeunload', window.onbeforeunload);
        window.onbeforeunload = null;

        const form = document.getElementById('fullExamForm');
        const formData = new FormData(form);

        Swal.fire({
            title: 'جاري تسليم وتوثيق الإجابات...',
            text: 'يرجى الانتظار لحين اكتمال إرسال إجاباتك إلكترونياً.',
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
                sessionStorage.removeItem('exam_confirmed_{{ $exam->id }}');
                localStorage.removeItem('exam_draft_{{ $exam->id }}');
                Swal.fire({
                    title: 'تم تسليم الاختبار بنجاح!',
                    text: res.data.message || 'تم توثيق إجاباتك وإصدار تقرير النتائج بنجاح.',
                    icon: 'success',
                    confirmButtonColor: '#059669',
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