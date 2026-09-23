@extends('layouts.app')

@section('title', __('قاعة الاختبار الأكاديمية') . ' | ' . $exam->title)
@section('no-sidebar', 'true')

@section('content')
<div class="ed-exam-take-wrapper" id="examTakeWrapper">

    <!-- شاشة تأكيد البدء والتعليمات الأكاديمية الرسمية (Classical Academic Pre-flight Modal) -->
    <div id="examPreflightModal" class="ed-preflight-overlay">
        <div class="ed-preflight-card">
            <div class="ed-preflight-crest">
                <div class="ed-crest-seal">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="ed-crest-titles">
                    <span class="ed-crest-gov">{{ __('دولة فلسطين • وزارة التربية والتعليم') }}</span>
                    <span class="ed-crest-sub">{{ $exam->subject->name_ar ?? $exam->subject->name ?? __('مادة دراسية') }}</span>
                </div>
            </div>

            <h2 class="ed-preflight-title">{{ $exam->title }}</h2>
            <p class="ed-preflight-desc">{{ __('جلسة اختبار رسمي إلكتروني خاضعة لنظام المراقبة والرصد الأكاديمي المباشر.') }}</p>

            <div class="ed-preflight-stats">
                <div class="ed-stat-box">
                    <i class="fa-regular fa-clock text-amber"></i>
                    <strong>{{ $exam->duration_minutes }} {{ __('دقيقة') }}</strong>
                    <span>{{ __('المدة الزمنية المقررة') }}</span>
                </div>
                <div class="ed-stat-box">
                    <i class="fa-solid fa-list-check text-navy"></i>
                    <strong>{{ count($exam->questions) }} {{ __('سؤالاً') }}</strong>
                    <span>{{ __('عدد أسئلة الاختبار') }}</span>
                </div>
                <div class="ed-stat-box">
                    <i class="fa-solid fa-award text-emerald"></i>
                    <strong>{{ $exam->total_grade ?? $exam->questions->sum('points') }} {{ __('درجة') }}</strong>
                    <span>{{ __('الدرجة الكلية القصوى') }}</span>
                </div>
            </div>

            @php
                $timingBadge = $exam->timing_badge_data;
            @endphp
            <div class="ed-preflight-schedule-banner">
                <div class="schedule-info-group">
                    <div class="schedule-icon-wrap">
                        <i class="fa-regular fa-calendar-check"></i>
                    </div>
                    <div>
                        <span class="schedule-lbl">{{ __('ساعات وموعد فتح الاختبار الأكاديمي:') }}</span>
                        <strong class="schedule-val">{{ $exam->formatted_timing_text }}</strong>
                    </div>
                </div>
                <span class="badge-timing-schedule {{ $timingBadge['status'] === 'upcoming' ? 'upcoming' : ($timingBadge['status'] === 'expired' ? 'expired' : ($timingBadge['status'] === 'active_limited' ? 'active-limited' : 'always-open')) }}">
                    <i class="{{ $timingBadge['icon'] }}"></i> {{ $timingBadge['label'] }}
                </span>
            </div>

            <div class="ed-preflight-rules">
                <h4>
                    <i class="fa-solid fa-shield-halved text-navy"></i> {{ __('ضوابط النزاهة الأكاديمية وتعليمات الجلسة:') }}
                </h4>
                <div class="ed-rules-grid">
                    <div class="ed-rule-item">
                        <i class="fa-solid fa-stopwatch text-amber"></i>
                        <span>{{ __('يبدأ احتساب وقت الاختبار تلقائياً فور النقر على زر "بدء الاختبار الآن".') }}</span>
                    </div>
                    <div class="ed-rule-item">
                        <i class="fa-solid fa-expand text-blue"></i>
                        <span>{{ __('يُستحسن تفعيل وضع "ملء الشاشة" لضمان أعلى مستويات التركيز وعدم التشتت.') }}</span>
                    </div>
                    <div class="ed-rule-item danger">
                        <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                        <span><strong>{{ __('حظر مغادرة التبويب:') }}</strong> {{ __('رصد التنقل بين النوافذ أو التطبيقات يُسجل فورياً في سجل المخالفات الأكاديمية.') }}</span>
                    </div>
                    <div class="ed-rule-item danger">
                        <i class="fa-solid fa-camera-slash text-danger"></i>
                        <span><strong>{{ __('حظر تصوير الشاشة:') }}</strong> {{ __('محاولات لقطات الشاشة أو النسخ محظورة وتخطر لجنة التدقيق.') }}</span>
                    </div>
                    <div class="ed-rule-item">
                        <i class="fa-solid fa-cloud-arrow-up text-emerald"></i>
                        <span>{{ __('عند نفاد الوقت المتبقي، سيتم حفظ واعتماد إجاباتك إلكترونياً بشكل تلقائي.') }}</span>
                    </div>
                </div>
            </div>

            <div class="ed-preflight-actions">
                <a href="{{ route('student.exams.index') }}" class="ed-btn-cancel">
                    <i class="fa-solid fa-arrow-right"></i>
                    <span>{{ __('العودة لقائمة الاختبارات') }}</span>
                </a>
                <button type="button" onclick="startExamOfficially()" class="ed-btn-start">
                    <i class="fa-solid fa-circle-play"></i>
                    <span>{{ __('أوافق على الضوابط وأبدأ الاختبار الآن') }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- الشريط الأكاديمي العلوي الثابت مع ملء الشاشة والعداد -->
    <header class="ed-exam-topbar" id="examTopbar">
        <div class="ed-topbar-inner">
            
            <!-- معلومات المادة والاختبار -->
            <div class="ed-topbar-meta">
                <div class="ed-subject-tag">
                    <i class="fa-solid fa-book-bookmark"></i>
                    <span>{{ $exam->subject->name_ar ?? $exam->subject->name ?? __('مادة دراسية') }}</span>
                </div>
                <h1 class="ed-topbar-title">{{ $exam->title }}</h1>
            </div>

            <!-- العداد التنازلي الكلاسيكي الدقيق -->
            <div class="ed-topbar-center">
                <div class="ed-timer-pod" id="examTimerPod">
                    <div class="ed-timer-header">
                        <i class="fa-regular fa-clock"></i>
                        <span>{{ __('الوقت المتبقي للجلسة') }}</span>
                    </div>
                    <div class="ed-timer-display" id="countdown_timer">00:00:00</div>
                </div>
            </div>

            <!-- أزرار التحكم وشارة المراقبة وزر ملء الشاشة -->
            <div class="ed-topbar-controls">
                
                <!-- شارة المراقبة الحية -->
                <div class="ed-proctor-pill" id="proctoringBadge" title="{{ __('نظام الرصد والنزاهة الأكاديمية يعمل بنجاح') }}">
                    <span class="ed-pulse-dot"></span>
                    <i class="fa-solid fa-shield-halved"></i>
                    <span id="proctoringText">{{ __('المراقبة: نشطة') }}</span>
                </div>

                <!-- زر ملء الشاشة الكلاسيكي الحقيقي -->
                <button type="button" class="ed-ctrl-btn ed-fullscreen-btn" id="btnFullscreen" onclick="toggleExamFullscreen()" title="{{ __('التبديل إلى وضع ملء الشاشة للتركيز الكامل') }}">
                    <i class="fa-solid fa-expand" id="fullscreenIcon"></i>
                    <span id="fullscreenText">{{ __('ملء الشاشة') }}</span>
                </button>

                <!-- زر التسليم السريع من الشريط العلوي -->
                <button type="button" class="ed-ctrl-btn ed-quick-submit-btn" onclick="confirmSubmission()" title="{{ __('تسليم ورقة الاختبار واعتماد الإجابات') }}">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>{{ __('تسليم الاختبار') }}</span>
                </button>
            </div>

        </div>

        <!-- شريط تقدم الإنجاز الأكاديمي الدقيق -->
        <div class="ed-progress-track">
            <div class="ed-progress-fill" id="examProgressBar" style="width: 0%;"></div>
        </div>
    </header>

    <!-- الهيكل الرئيسي للاختبار (الأسئلة + الفهرس الجانبي) -->
    <form id="fullExamForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="tab_switches_count" id="tabSwitchesCount" value="0">
        <input type="hidden" name="screenshots_count" id="screenshotsCount" value="0">
        <input type="hidden" name="cheating_flags" id="cheatingFlagsInput" value="[]">

        <div class="ed-exam-container">

            <!-- مسار تدفق الأسئلة (العمود الرئيسي 75%) -->
            <main class="ed-questions-arena" id="questionsArena">
                
                <!-- ورقة الامتحان الوزارية الكلاسيكية الرسمية (Palestinian Classical Official Exam Sheet Header) -->
                <div class="ed-arena-header-card ed-palestine-exam-sheet">
                    <div class="ed-sheet-crest-section">
                        <div class="ed-sheet-gov-side">
                            <div class="ed-state-title">{{ __('دولة فلسطين') }}</div>
                            <div class="ed-ministry-title">{{ __('وزارة التربية والتعليم العالي') }}</div>
                            <div class="ed-dept-title">{{ __('الإدارة العامة للامتحانات والتقويم والقياس') }}</div>
                        </div>

                        <div class="ed-sheet-emblem-center">
                            <div class="ed-emblem-seal">
                                <i class="fa-solid fa-feather-pointed"></i>
                            </div>
                            <span class="ed-emblem-label">{{ __('ورقة امتحان رسمية') }}</span>
                            <span class="ed-exam-cycle">{{ __('جلسة الاختبار الأكاديمي الرقمي') }}</span>
                        </div>

                        <div class="ed-sheet-meta-side">
                            <div class="ed-meta-item">
                                <span class="lbl">{{ __('المبحث:') }}</span>
                                <strong class="val">{{ $exam->subject->name_ar ?? $exam->subject->name ?? __('مادة دراسية') }}</strong>
                            </div>
                            <div class="ed-meta-item">
                                <span class="lbl">{{ __('المرحلة / الصف:') }}</span>
                                <strong class="val">{{ $exam->academicYear->name_ar ?? $exam->academicYear->name ?? __('المرحلة الثانوية العامة') }}</strong>
                            </div>
                            <div class="ed-meta-item">
                                <span class="lbl">{{ __('زمن الإجابة:') }}</span>
                                <strong class="val">{{ $exam->duration_minutes }} {{ __('دقيقة') }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- بطاقة معلومات الجلسة والدرجات في جدول امتحاني كلاسيكي مذهب -->
                    <div class="ed-sheet-info-table">
                        <div class="ed-info-col">
                            <span class="ed-col-lbl"><i class="fa-solid fa-user-graduate"></i> {{ __('اسم الطالب الممتحن') }}</span>
                            <strong class="ed-col-val">{{ auth('student')->user()?->name_ar ?? auth()->user()?->name ?? __('طالب نظامي') }}</strong>
                        </div>
                        <div class="ed-info-col">
                            <span class="ed-col-lbl"><i class="fa-solid fa-fingerprint"></i> {{ __('رقم الجلسة الأكاديمية') }}</span>
                            <strong class="ed-col-val ltr-code">EX-{{ $exam->id }}-{{ auth()->id() }}</strong>
                        </div>
                        <div class="ed-info-col">
                            <span class="ed-col-lbl"><i class="fa-solid fa-file-circle-question"></i> {{ __('مجموع الأسئلة') }}</span>
                            <strong class="ed-col-val">{{ count($exam->questions) }} {{ __('أسئلة') }}</strong>
                        </div>
                        <div class="ed-info-col highlight">
                            <span class="ed-col-lbl"><i class="fa-solid fa-award"></i> {{ __('مجموع العلامات الكلية') }}</span>
                            <strong class="ed-col-val">{{ $exam->total_grade ?? $exam->questions->sum('points') }} {{ __('علامة') }}</strong>
                        </div>
                    </div>

                    <div class="ed-sheet-instructions-strip">
                        <div class="ed-strip-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
                        <div class="ed-strip-text">
                            <strong>{{ __('توجيهات وإرشادات هامة للممتحن:') }}</strong>
                            <span>{{ __('أجب عن جميع الأسئلة الواردة في هذه الورقة بعناية. تأكد من تظليل خيار الإجابة المطلوب أو كتابة الشرح المفصل، ويتم حفظ إجاباتك تلقائياً.') }}</span>
                        </div>
                    </div>
                </div>

                @foreach($exam->questions as $index => $q)
                @php
                    $qCandidates = $q->getImageCandidates();
                    $firstQImg = !empty($qCandidates) ? $qCandidates[0] : null;
                @endphp
                <article class="ed-q-card" id="q_card_{{ $q->id }}" data-question-id="{{ $q->id }}" data-index="{{ $index }}">
                    
                    <!-- رأس بطاقة السؤال الكلاسيكي -->
                    <div class="ed-q-card-head">
                        <div class="ed-q-meta">
                            <div class="ed-q-number-badge">
                                <span class="ed-num-prefix">{{ __('السؤال') }}</span>
                                <strong class="ed-num-val">{{ $index + 1 }}</strong>
                                <span class="ed-num-total">/ {{ count($exam->questions) }}</span>
                            </div>
                            <div class="ed-q-points-badge">
                                <i class="fa-solid fa-award"></i>
                                <span>{{ $q->points }} {{ __('درجات') }}</span>
                            </div>
                            <span class="ed-q-type-pill {{ $q->type == 'mcq' ? 'mcq' : 'essay' }}">
                                {{ $q->type == 'mcq' ? __('اختيار من متعدد') : __('سؤال مقالي / تحليلي') }}
                            </span>
                        </div>

                        <button type="button" class="ed-flag-action" onclick="toggleFlag({{ $q->id }}, {{ $index }})" id="flag_btn_{{ $q->id }}" title="{{ __('تمييز السؤال لمراجعته لاحقاً') }}">
                            <i class="fa-regular fa-bookmark" id="flag_icon_{{ $q->id }}"></i>
                            <span id="flag_text_{{ $q->id }}">{{ __('مراجعة') }}</span>
                        </button>
                    </div>

                    <!-- متن نص السؤال والمرفق -->
                    <div class="ed-q-card-body">
                        <div class="ed-q-text-box">
                            <h2 class="ed-q-main-text">{!! nl2br(e($q->question_text)) !!}</h2>
                        </div>

                        <!-- عرض صورة السؤال التوضيحية (بدون أي رسائل خطأ، مع التحقق السلس) -->
                        @if(!empty($firstQImg))
                        <div class="ed-q-figure-box" id="q_fig_box_{{ $q->id }}">
                            <div class="ed-q-figure-topbar">
                                <span class="ed-q-figure-title">
                                    <i class="fa-solid fa-file-image"></i>
                                    {{ __('الرسم الهندسي / المرفق التوضيحي للسؤال:') }}
                                </span>
                                <button type="button" class="ed-zoom-action-btn" onclick="openImageModal(document.getElementById('q_img_{{ $q->id }}').src)">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    <span>{{ __('تكبير الصورة بدقة كاملة') }}</span>
                                </button>
                            </div>
                            <div class="ed-q-figure-frame">
                                <img id="q_img_{{ $q->id }}"
                                     src="{{ $firstQImg }}" 
                                     alt="{{ __('مرفق السؤال الأكاديمي') }}" 
                                     class="ed-q-figure-img"
                                     onclick="openImageModal(this.src)" 
                                     loading="lazy"
                                     data-candidates="{{ implode('|', $qCandidates) }}"
                                     data-candidate-idx="0"
                                     onerror="handleExamImageFallback(this)">
                            </div>
                        </div>
                        @endif

                        <!-- خيارات الإجابة للسؤال (MCQ) -->
                        @if($q->type == 'mcq')
                            <div class="ed-mcq-container">
                                <div class="ed-mcq-grid">
                                    @php
                                        $lettersAr = ['a' => 'أ', 'b' => 'ب', 'c' => 'ج', 'd' => 'د'];
                                    @endphp
                                    @foreach(['a', 'b', 'c', 'd'] as $option)
                                    @php
                                        $optCandidates = $q->getOptionImageCandidates($option);
                                        $firstOptImg = !empty($optCandidates) ? $optCandidates[0] : null;
                                        $hasOptImg = !empty($firstOptImg);
                                        $hasOptText = !empty($q->$option);
                                    @endphp
                                    @if($hasOptText || $hasOptImg)
                                    <label class="ed-mcq-label {{ $hasOptImg ? 'with-image' : '' }}">
                                        <input 
                                            type="radio" 
                                            name="answers[{{ $q->id }}]" 
                                            value="{{ $option }}" 
                                            class="ed-mcq-input"
                                            onchange="markAsAnswered({{ $index }}, {{ $q->id }})"
                                        >
                                        <div class="ed-mcq-card">
                                            <div class="ed-opt-letter-disc">
                                                <span class="letter-ar">{{ $lettersAr[$option] ?? strtoupper($option) }}</span>
                                                <span class="letter-en">{{ strtoupper($option) }}</span>
                                            </div>
                                            <div class="ed-opt-payload">
                                                @if($hasOptText)
                                                    <div class="ed-opt-text">{{ $q->$option }}</div>
                                                @endif
                                                @if($hasOptImg)
                                                    <div class="ed-opt-img-wrapper ed-opt-visual-wrapper" onclick="event.stopPropagation(); openImageModal(this.querySelector('img').src)" title="{{ __('انقر لتكبير صورة الخيار') }}">
                                                        <img src="{{ $firstOptImg }}" 
                                                             alt="خيار {{ strtoupper($option) }}" 
                                                             class="ed-opt-thumb ed-opt-visual-thumb" 
                                                             loading="lazy"
                                                             data-candidates="{{ implode('|', $optCandidates) }}"
                                                             data-candidate-idx="0"
                                                             onerror="handleExamImageFallback(this)">
                                                        <span class="ed-opt-zoom-chip">
                                                            <i class="fa-solid fa-magnifying-glass-plus"></i> {{ __('تكبير') }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ed-opt-radio-marker">
                                                <i class="fa-solid fa-check"></i>
                                            </div>
                                        </div>
                                    </label>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <!-- إجابة مقالية تحريرية / كتابية -->
                            <div class="ed-written-arena">
                                <div class="ed-written-header">
                                    <label class="ed-written-title">
                                        <i class="fa-solid fa-pen-fancy"></i>
                                        <span>{{ __('الإجابة النموذجية والشرح الأكاديمي:') }}</span>
                                    </label>
                                    <span class="ed-written-hint">{{ __('اكتب حلك المنظم بدقة ووضوح في المساحة المخصصة أدناه.') }}</span>
                                </div>

                                <textarea 
                                    name="answers[{{ $q->id }}]" 
                                    rows="5" 
                                    placeholder="{{ __('اكتب صياغة الحل هنا بدقة مع ذكر الخطوات الحسابية أو التعليل الأكاديمي...') }}" 
                                    class="ed-written-textarea" 
                                    oninput="markAsAnswered({{ $index }}, {{ $q->id }})"
                                ></textarea>

                                @if($q->require_file)
                                <div class="ed-upload-dossier">
                                    <div class="ed-upload-notice">
                                        <i class="fa-solid fa-paperclip text-navy"></i>
                                        <div>
                                            <strong>{{ __('مطلوب إرفاق مسودة الحل اليدوي أو رسم بياني (PDF أو صورة):') }}</strong>
                                            <span>{{ __('يمكنك التقاط صورة واضحة لورقة الحل وإرفاقها هنا مباشرة.') }}</span>
                                        </div>
                                    </div>
                                    <label class="ed-upload-dropzone">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                        <span class="ed-dropzone-prompt">{{ __('انقر لاختيار الملف من جهازك (JPG, PNG, PDF)') }}</span>
                                        <input 
                                            type="file" 
                                            name="files[{{ $q->id }}]" 
                                            accept="image/*,application/pdf" 
                                            hidden 
                                            onchange="updateFileName(this, {{ $q->id }}, {{ $index }})"
                                        >
                                    </label>
                                    <div class="ed-selected-file-pill" id="file_name_{{ $q->id }}" style="display: none;"></div>
                                </div>
                                @endif
                            </div>
                        @endif

                    </div>

                    <!-- شريط التنقل السفلي السريع للبطاقة -->
                    <div class="ed-q-card-foot">
                        <div class="ed-q-foot-nav">
                            @if($index > 0)
                                <a href="#q_card_{{ $exam->questions[$index - 1]->id }}" class="ed-btn-step prev">
                                    <i class="fa-solid fa-arrow-right"></i>
                                    <span>{{ __('السابق') }}</span>
                                </a>
                            @else
                                <span></span>
                            @endif

                            @if($index < count($exam->questions) - 1)
                                <a href="#q_card_{{ $exam->questions[$index + 1]->id }}" class="ed-btn-step next">
                                    <span>{{ __('التالي') }}</span>
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                            @else
                                <button type="button" onclick="confirmSubmission()" class="ed-btn-step submit">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    <span>{{ __('إنهاء وتسليم الاختبار') }}</span>
                                </button>
                            @endif
                        </div>
                    </div>

                </article>
                @endforeach

                <!-- كرت الختام والتسليم النهائي الكلاسيكي الوزاري (Palestinian Ministerial Exam Closing Sheet) -->
                <div class="ed-arena-footer-card ed-palestine-footer-sheet">
                    <div class="ed-palestine-closing-banner">
                        <div class="ed-closing-crest"><i class="fa-solid fa-feather-pointed"></i></div>
                        <div class="ed-closing-text">
                            <h4>{{ __('انتـهـت الأسـئـلـة بـحـمـد الـلـه وتـوفـيـقـه') }}</h4>
                            <p>{{ __('مع أطيب التمنيات لجميع طلبتنا الأعزاء بالتوفيق والنجاح الباهر') }}</p>
                        </div>
                        <div class="ed-closing-crest"><i class="fa-solid fa-feather-pointed"></i></div>
                    </div>

                    <!-- جدول التواقيع والاعتماد الأكاديمي الكلاسيكي -->
                    <div class="ed-signature-grid">
                        <div class="ed-sig-box">
                            <span class="ed-sig-title">{{ __('إقرار الطالب الممتحن') }}</span>
                            <div class="ed-sig-line">
                                <i class="fa-solid fa-signature text-navy"></i>
                                <span>{{ auth('student')->user()?->name_ar ?? auth()->user()?->name ?? __('طالب نظامي') }}</span>
                            </div>
                            <span class="ed-sig-note">{{ __('أقر بأنني أجبت عن الأسئلة بنزاهة تامة') }}</span>
                        </div>
                        <div class="ed-sig-box">
                            <span class="ed-sig-title">{{ __('لجنة المراقبة والنزاهة الرقمية') }}</span>
                            <div class="ed-sig-line">
                                <i class="fa-solid fa-shield-check text-emerald"></i>
                                <span class="text-emerald">{{ __('نظام الرصد الذكي مؤكد') }}</span>
                            </div>
                            <span class="ed-sig-note">{{ __('الجلسة موثقة ومحمية برمجياً') }}</span>
                        </div>
                        <div class="ed-sig-box">
                            <span class="ed-sig-title">{{ __('لجنة التصحيح والاعتماد') }}</span>
                            <div class="ed-sig-line">
                                <i class="fa-solid fa-stamp text-amber"></i>
                                <span>{{ __('معتمد رقمياً') }}</span>
                            </div>
                            <span class="ed-sig-note">{{ __('الدرجة الكلية: ') }} {{ $exam->total_grade ?? $exam->questions->sum('points') }} {{ __('علامة') }}</span>
                        </div>
                    </div>

                    <div class="ed-footer-action-wrap">
                        <div class="ed-footer-notice">
                            <i class="fa-solid fa-circle-info text-navy"></i>
                            <span>{{ __('عند الضغط على الزر أدناه، سيتم تسليم ورقة الامتحان نهائياً وحفظ جميع الإجابات وإصدار تقرير نتيجتك.') }}</span>
                        </div>
                        <button type="button" onclick="confirmSubmission()" class="ed-btn-grand-submit" id="submitBtn">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>{{ __('تسليم ورقة الامتحان والاعتماد النهائي') }}</span>
                        </button>
                    </div>
                </div>

            </main>

            <!-- اللوحة الجانبية الكلاسيكية: خريطة الأسئلة ومتابعة التقدم (25%) -->
            <aside class="ed-exam-navigator" id="examNavSidebar">
                <div class="ed-nav-dossier">
                    
                    <div class="ed-dossier-header">
                        <div class="ed-dossier-title">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <h4>{{ __('فهرس الأسئلة') }}</h4>
                        </div>
                        <div class="ed-dossier-count">
                            <strong id="answeredCounter">0</strong>
                            <span>/ {{ count($exam->questions) }}</span>
                        </div>
                    </div>

                    <!-- مفتاح ألوان الأسئلة -->
                    <div class="ed-nav-legend-bar">
                        <div class="ed-legend-item">
                            <span class="legend-swatch answered"></span>
                            <span>{{ __('تم الحل') }}</span>
                        </div>
                        <div class="ed-legend-item">
                            <span class="legend-swatch flagged"></span>
                            <span>{{ __('للمراجعة') }}</span>
                        </div>
                        <div class="ed-legend-item">
                            <span class="legend-swatch unvisited"></span>
                            <span>{{ __('متبقي') }}</span>
                        </div>
                    </div>

                    <!-- شبكة الأسئلة الرقمية الكلاسيكية -->
                    <div class="ed-nav-questions-grid">
                        @foreach($exam->questions as $index => $q)
                        <a href="#q_card_{{ $q->id }}" class="ed-nav-cell" id="nav_btn_{{ $index }}" title="{{ __('الانتقال إلى السؤال رقم') }} {{ $index + 1 }}">
                            <span class="cell-num">{{ $index + 1 }}</span>
                            <span class="cell-flag-dot" id="nav_flag_{{ $index }}"></span>
                        </a>
                        @endforeach
                    </div>

                    <!-- بطاقة معلومات الطالب والأمان -->
                    <div class="ed-student-proctor-card">
                        <div class="ed-proctor-row">
                            <span class="ed-proctor-lbl">{{ __('اسم الطالب:') }}</span>
                            <strong class="ed-proctor-val">{{ auth('student')->user()?->name_ar ?? auth()->user()?->name ?? __('طالب أكاديمي') }}</strong>
                        </div>
                        <div class="ed-proctor-row">
                            <span class="ed-proctor-lbl">{{ __('رقم الجلسة:') }}</span>
                            <span class="ed-proctor-val">EX-{{ $exam->id }}-{{ auth()->id() }}</span>
                        </div>
                        <div class="ed-proctor-row">
                            <span class="ed-proctor-lbl">{{ __('حالة النزاهة:') }}</span>
                            <span class="ed-proctor-val text-emerald" id="proctorStateText">
                                <i class="fa-solid fa-shield-check"></i> {{ __('مطابق للضوابط') }}
                            </span>
                        </div>
                    </div>

                    <!-- زر التسليم الرئيسي من الشريط الجانبي -->
                    <div class="ed-nav-submit-box">
                        <button type="button" onclick="confirmSubmission()" class="ed-btn-side-submit">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>{{ __('اعتماد وتسليم الإجابات') }}</span>
                        </button>
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
       أنماط التصميم الكلاسيكي الملكي لقاعة الاختبارات الأكاديمية
       (Royal Palestinian Classic Academic Examination Arena)
       ========================================================= */

    :root {
        --ed-navy-950: #091a2e;
        --ed-navy-900: #0f243d;
        --ed-navy-800: #173252;
        --ed-navy-700: #1e3a8a;
        --ed-navy-600: #2563eb;
        --ed-amber-700: #b45309;
        --ed-amber-600: #d97706;
        --ed-amber-500: #f59e0b;
        --ed-amber-100: #fef3c7;
        --ed-emerald-700: #047857;
        --ed-emerald-600: #059669;
        --ed-emerald-50: #ecfdf5;
        --ed-red-600: #dc2626;
        --ed-red-50: #fef2f2;
        --ed-slate-900: #0f172a;
        --ed-slate-800: #1e293b;
        --ed-slate-700: #334155;
        --ed-slate-500: #64748b;
        --ed-slate-200: #e2e8f0;
        --ed-slate-100: #f1f5f9;
        --ed-bg: #f8fafc;
    }

    body {
        background-color: var(--ed-bg) !important;
        margin: 0 !important;
        padding: 0 !important;
        font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    .ed-exam-take-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 0 80px 0 !important;
        box-sizing: border-box;
        background-color: var(--ed-bg);
        min-height: 100vh;
    }

    /* =========================================================
       1. الشريط الأكاديمي العلوي الثابت (Sticky Academic Topbar)
       ========================================================= */
    .ed-exam-topbar {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: linear-gradient(135deg, var(--ed-navy-950) 0%, var(--ed-navy-900) 50%, var(--ed-navy-800) 100%);
        color: #ffffff;
        border-bottom: 2px solid var(--ed-amber-600);
        box-shadow: 0 8px 24px rgba(9, 26, 46, 0.25);
        padding: 0;
    }

    .ed-topbar-inner {
        max-width: 1600px;
        margin: 0 auto;
        padding: 12px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .ed-topbar-meta {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .ed-subject-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.76rem;
        font-weight: 700;
        color: #93c5fd;
        letter-spacing: 0.2px;
    }

    .ed-topbar-title {
        font-size: 1.18rem;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
        line-height: 1.3;
        letter-spacing: -0.2px;
    }

    /* عداد الوقت الرقمي الكلاسيكي */
    .ed-topbar-center {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ed-timer-pod {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 12px;
        padding: 6px 20px;
        text-align: center;
        backdrop-filter: blur(8px);
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .ed-timer-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.68rem;
        color: #cbd5e1;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .ed-timer-display {
        font-family: 'Consolas', 'Courier New', Courier, monospace;
        font-size: 1.5rem;
        font-weight: 900;
        color: #38bdf8;
        direction: ltr;
        letter-spacing: 1.5px;
        line-height: 1.1;
        transition: color 0.3s ease;
    }

    /* شارات وأزرار التحكم بالرأس */
    .ed-topbar-controls {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ed-proctor-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.4);
        color: #34d399;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.78rem;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .ed-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseProctorClassic 2s infinite;
    }

    @keyframes pulseProctorClassic {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .ed-proctor-pill.warning {
        background: rgba(220, 38, 38, 0.25);
        border-color: rgba(239, 68, 68, 0.6);
        color: #fca5a5;
    }

    .ed-proctor-pill.warning .ed-pulse-dot {
        background: #ef4444;
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    }

    .ed-ctrl-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }

    /* زر ملء الشاشة */
    .ed-fullscreen-btn {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .ed-fullscreen-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.4);
        transform: translateY(-1px);
    }

    /* زر التسليم السريع */
    .ed-quick-submit-btn {
        background: linear-gradient(135deg, var(--ed-emerald-600) 0%, var(--ed-emerald-700) 100%);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
    }

    .ed-quick-submit-btn:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
    }

    /* شريط تقدم الإنجاز */
    .ed-progress-track {
        height: 4px;
        background: rgba(255, 255, 255, 0.12);
        width: 100%;
        overflow: hidden;
    }

    .ed-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #38bdf8 0%, var(--ed-amber-500) 50%, var(--ed-emerald-600) 100%);
        transition: width 0.35s ease;
    }

    /* =========================================================
       2. تخطيط ساحة الاختبار الكلاسيكية (Exam Arena Layout)
       ========================================================= */
    .ed-exam-container {
        max-width: 1600px;
        margin: 24px auto 0;
        padding: 0 24px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 24px;
        align-items: start;
        box-sizing: border-box;
    }

    /* =========================================================
       2. ورقة الامتحان الوزارية الكلاسيكية (Palestinian Ministerial Exam Sheet)
       ========================================================= */
    .ed-palestine-exam-sheet {
        background: #ffffff;
        border: 2px solid var(--ed-navy-950);
        outline: 3px double #d97706;
        outline-offset: -4px;
        border-radius: 14px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(9, 26, 46, 0.06);
        position: relative;
    }

    .ed-sheet-crest-section {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e2e8f0;
    }

    .ed-sheet-gov-side {
        text-align: right;
    }

    .ed-state-title {
        font-size: 1.15rem;
        font-weight: 900;
        color: var(--ed-navy-950);
        letter-spacing: -0.2px;
    }

    .ed-ministry-title {
        font-size: 0.96rem;
        font-weight: 800;
        color: var(--ed-amber-700);
        margin: 2px 0;
    }

    .ed-dept-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--ed-slate-500);
    }

    .ed-sheet-emblem-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 4px;
    }

    .ed-emblem-seal {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--ed-navy-950) 0%, var(--ed-navy-800) 100%);
        border: 2px solid var(--ed-amber-500);
        color: #fef08a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.15);
    }

    .ed-emblem-label {
        font-size: 0.86rem;
        font-weight: 800;
        color: var(--ed-navy-950);
    }

    .ed-exam-cycle {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--ed-slate-500);
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .ed-sheet-meta-side {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 4px;
        align-items: flex-end;
    }

    .ed-meta-item {
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .ed-meta-item .lbl {
        color: var(--ed-slate-500);
        font-weight: 600;
    }

    .ed-meta-item .val {
        color: var(--ed-navy-950);
        font-weight: 800;
    }

    /* جدول معلومات الجلسة والدرجات الكلاسيكي المذهب */
    .ed-sheet-info-table {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin: 18px 0;
        background: #fbfbf9;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
    }

    .ed-info-col {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .ed-info-col .ed-col-lbl {
        font-size: 0.74rem;
        font-weight: 700;
        color: var(--ed-slate-500);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .ed-info-col .ed-col-val {
        font-size: 0.92rem;
        font-weight: 800;
        color: var(--ed-navy-950);
    }

    .ed-info-col.highlight .ed-col-val {
        color: var(--ed-amber-700);
        font-size: 1.02rem;
    }

    .ltr-code {
        direction: ltr;
        display: inline-block;
        font-family: 'Consolas', monospace;
    }

    /* شريط التوجيهات الوزارية */
    .ed-sheet-instructions-strip {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-right: 4px solid var(--ed-emerald-600);
        border-radius: 8px;
        padding: 10px 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 0.82rem;
        color: #166534;
        line-height: 1.5;
    }

    .ed-strip-icon {
        color: var(--ed-emerald-600);
        font-size: 1.05rem;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .ed-strip-text strong {
        color: #14532d;
        margin-left: 4px;
    }

    /* =========================================================
       3. بطاقات الأسئلة الأكاديمية (Question Cards)
       ========================================================= */
    .ed-q-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        margin-bottom: 22px;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        scroll-margin-top: 100px;
    }

    .ed-q-card:hover {
        border-color: #94a3b8;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }

    .ed-q-card-head {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .ed-q-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .ed-q-number-badge {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: var(--ed-navy-700);
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .ed-num-val {
        font-size: 0.95rem;
        font-weight: 900;
    }

    .ed-num-total {
        color: #64748b;
        font-size: 0.78rem;
    }

    .ed-q-points-badge {
        background: var(--ed-amber-100);
        border: 1px solid #fde68a;
        color: var(--ed-amber-700);
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .ed-q-type-pill {
        font-size: 0.74rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .ed-q-type-pill.mcq {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .ed-q-type-pill.essay {
        background: #f0fdf4;
        color: var(--ed-emerald-700);
        border: 1px solid #bbf7d0;
    }

    /* زر التمييز للمراجعة */
    .ed-flag-action {
        background: #ffffff;
        border: 1px solid var(--ed-slate-200);
        color: var(--ed-slate-500);
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .ed-flag-action:hover, .ed-flag-action.flagged {
        background: var(--ed-amber-100);
        border-color: var(--ed-amber-500);
        color: var(--ed-amber-700);
    }

    .ed-flag-action.flagged i {
        color: var(--ed-amber-600);
    }

    /* جسم السؤال */
    .ed-q-card-body {
        padding: 24px;
    }

    .ed-q-text-box {
        margin-bottom: 20px;
    }

    .ed-q-main-text {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--ed-slate-900);
        line-height: 1.85;
        margin: 0;
        word-break: break-word;
    }

    /* إطار صور السؤال الكلاسيكي المتقن */
    .ed-q-figure-box {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 22px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .ed-q-figure-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 10px;
        margin-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ed-q-figure-title {
        font-size: 0.82rem;
        font-weight: 800;
        color: var(--ed-navy-700);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .ed-zoom-action-btn {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: var(--ed-navy-700);
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .ed-zoom-action-btn:hover {
        background: var(--ed-navy-700);
        color: #ffffff;
        border-color: var(--ed-navy-700);
    }

    .ed-q-figure-frame {
        text-align: center;
        background: #f8fafc;
        border-radius: 8px;
        padding: 16px;
        border: 1px dashed #cbd5e1;
    }

    .ed-q-figure-img {
        max-height: 440px;
        max-width: 100%;
        border-radius: 6px;
        object-fit: contain;
        cursor: zoom-in;
        transition: transform 0.2s ease;
    }

    .ed-q-figure-img:hover {
        transform: scale(1.015);
    }

    /* شبكة خيارات السؤال (MCQ) الكلاسيكية */
    .ed-mcq-container {
        margin-top: 16px;
    }

    .ed-mcq-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .ed-mcq-label {
        cursor: pointer;
        display: block;
        margin: 0;
        user-select: none;
    }

    .ed-mcq-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .ed-mcq-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        min-height: 60px;
        box-sizing: border-box;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }

    .ed-mcq-label:hover .ed-mcq-card {
        border-color: #94a3b8;
        background: #fafaf8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    }

    /* حالة التحديد */
    .ed-mcq-input:checked + .ed-mcq-card {
        border-color: var(--ed-navy-950);
        background: #f0f7ff;
        box-shadow: 0 0 0 3px rgba(15, 36, 61, 0.12), 0 4px 14px rgba(15, 23, 42, 0.06);
    }

    /* ميدالية الحرف الكلاسيكية الوزارية */
    .ed-opt-letter-disc {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #f8fafc;
        border: 2px solid #cbd5e1;
        color: var(--ed-navy-950);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        flex-shrink: 0;
        transition: all 0.22s ease;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .ed-opt-letter-disc .letter-ar {
        font-family: 'Amiri', 'Traditional Arabic', serif, system-ui;
        font-size: 1.25rem;
        font-weight: 900;
        line-height: 1;
        color: var(--ed-navy-950);
    }

    .ed-opt-letter-disc .letter-en {
        font-size: 0.58rem;
        color: #94a3b8;
        line-height: 1;
        margin-top: 1px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .ed-mcq-input:checked + .ed-mcq-card .ed-opt-letter-disc {
        background: linear-gradient(135deg, var(--ed-navy-950) 0%, var(--ed-navy-800) 100%);
        border-color: var(--ed-amber-500);
        box-shadow: 0 2px 8px rgba(15, 36, 61, 0.25);
    }

    .ed-mcq-input:checked + .ed-mcq-card .ed-opt-letter-disc .letter-ar {
        color: #ffffff;
    }

    .ed-mcq-input:checked + .ed-mcq-card .ed-opt-letter-disc .letter-en {
        color: #fef08a;
    }

    .ed-opt-payload {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 0;
    }

    .ed-opt-text {
        font-size: 0.96rem;
        font-weight: 600;
        color: var(--ed-slate-800);
        line-height: 1.55;
        word-break: break-word;
    }

    .ed-mcq-input:checked + .ed-mcq-card .ed-opt-text {
        color: var(--ed-navy-950);
        font-weight: 700;
    }

    /* عرض صورة الخيار */
    .ed-opt-visual-wrapper {
        position: relative;
        display: inline-block;
        max-width: 100%;
        cursor: zoom-in;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        padding: 4px;
        transition: all 0.2s ease;
    }

    .ed-opt-visual-wrapper:hover {
        border-color: var(--ed-navy-600);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }

    .ed-opt-visual-thumb {
        max-height: 120px;
        max-width: 100%;
        display: block;
        border-radius: 6px;
        object-fit: contain;
    }

    .ed-opt-zoom-chip {
        position: absolute;
        bottom: 4px;
        left: 4px;
        background: rgba(15, 23, 42, 0.85);
        color: #ffffff;
        font-size: 0.66rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        pointer-events: none;
    }

    /* علامة الراديو */
    .ed-opt-radio-marker {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        color: transparent;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .ed-mcq-input:checked + .ed-mcq-card .ed-opt-radio-marker {
        border-color: var(--ed-navy-700);
        background: var(--ed-navy-700);
        color: #ffffff;
    }

    /* الأسئلة المقالية والتحريرية */
    .ed-written-arena {
        margin-top: 14px;
    }

    .ed-written-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        flex-wrap: wrap;
        gap: 6px;
    }

    .ed-written-title {
        font-size: 0.88rem;
        font-weight: 800;
        color: var(--ed-slate-800);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .ed-written-hint {
        font-size: 0.78rem;
        color: var(--ed-slate-500);
    }

    .ed-written-textarea {
        width: 100%;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 14px 16px;
        font-size: 0.95rem;
        color: var(--ed-slate-900);
        line-height: 1.7;
        font-family: inherit;
        background: #ffffff;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
        resize: vertical;
    }

    .ed-written-textarea:focus {
        outline: none;
        border-color: var(--ed-navy-700);
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.12);
    }

    /* منطقة رفع ملفات الحل */
    .ed-upload-dossier {
        margin-top: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px;
    }

    .ed-upload-notice {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 0.82rem;
        color: var(--ed-slate-700);
        margin-bottom: 12px;
    }

    .ed-upload-notice strong {
        color: var(--ed-navy-950);
        display: block;
        margin-bottom: 2px;
    }

    .ed-upload-dropzone {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px;
        border: 2px dashed #94a3b8;
        border-radius: 8px;
        background: #ffffff;
        cursor: pointer;
        color: var(--ed-navy-700);
        font-weight: 700;
        font-size: 0.86rem;
        transition: all 0.2s ease;
    }

    .ed-upload-dropzone:hover {
        border-color: var(--ed-navy-700);
        background: #eff6ff;
    }

    .ed-selected-file-pill {
        margin-top: 10px;
        background: var(--ed-emerald-50);
        border: 1px solid #a7f3d0;
        color: var(--ed-emerald-700);
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 0.84rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* شريط أسفل بطاقة السؤال للتنقل السريع */
    .ed-q-card-foot {
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        padding: 12px 24px;
    }

    .ed-q-foot-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ed-btn-step {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 18px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .ed-btn-step.prev {
        background: #ffffff;
        border-color: #cbd5e1;
        color: var(--ed-slate-700);
    }

    .ed-btn-step.prev:hover {
        background: var(--ed-slate-100);
        border-color: #94a3b8;
    }

    .ed-btn-step.next {
        background: var(--ed-navy-700);
        color: #ffffff;
        border-color: var(--ed-navy-700);
    }

    .ed-btn-step.next:hover {
        background: var(--ed-navy-600);
        box-shadow: 0 3px 10px rgba(30, 58, 138, 0.25);
    }

    .ed-btn-step.submit {
        background: var(--ed-emerald-600);
        color: #ffffff;
        border-color: var(--ed-emerald-600);
    }

    .ed-btn-step.submit:hover {
        background: var(--ed-emerald-700);
    }

    /* كرت الختام والتسليم الوزاري الكلاسيكي */
    .ed-palestine-footer-sheet {
        background: #ffffff;
        border: 2px solid var(--ed-navy-950);
        outline: 3px double #d97706;
        outline-offset: -4px;
        border-radius: 14px;
        padding: 24px 28px;
        margin-top: 30px;
        box-shadow: 0 4px 20px rgba(9, 26, 46, 0.06);
    }

    .ed-palestine-closing-banner {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e2e8f0;
        text-align: center;
    }

    .ed-closing-crest {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #eff6ff;
        border: 1.5px solid var(--ed-navy-800);
        color: var(--ed-navy-900);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .ed-closing-text h4 {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--ed-navy-950);
        margin: 0 0 4px;
        letter-spacing: -0.2px;
    }

    .ed-closing-text p {
        font-size: 0.88rem;
        color: var(--ed-amber-700);
        font-weight: 700;
        margin: 0;
    }

    /* جدول التواقيع الأكاديمي الكلاسيكي */
    .ed-signature-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin: 22px 0;
    }

    .ed-sig-box {
        background: #fbfbf9;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 14px;
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .ed-sig-title {
        font-size: 0.76rem;
        font-weight: 800;
        color: var(--ed-slate-500);
    }

    .ed-sig-line {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--ed-navy-950);
        min-height: 28px;
    }

    .ed-sig-note {
        font-size: 0.7rem;
        color: #94a3b8;
    }

    .ed-footer-action-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
    }

    .ed-footer-notice {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: var(--ed-slate-600);
        font-weight: 600;
        max-width: 600px;
    }

    .ed-btn-grand-submit {
        background: linear-gradient(135deg, var(--ed-navy-950) 0%, var(--ed-navy-800) 100%);
        color: #fef08a;
        border: 2px solid var(--ed-amber-500);
        padding: 14px 34px;
        border-radius: 12px;
        font-size: 1.05rem;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 4px 18px rgba(15, 36, 61, 0.35);
        transition: all 0.22s ease;
    }

    .ed-btn-grand-submit:hover {
        background: linear-gradient(135deg, #091a2e 0%, #1e3a8a 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(15, 36, 61, 0.45);
        color: #ffffff;
    }

    /* =========================================================
       4. اللوحة الجانبية الكلاسيكية (Exam Navigator Panel)
       ========================================================= */
    .ed-exam-navigator {
        position: sticky;
        top: 88px;
        z-index: 900;
    }

    .ed-nav-dossier {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
    }

    .ed-dossier-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 1px solid #e2e8f0;
    }

    .ed-dossier-title {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--ed-navy-950);
    }

    .ed-dossier-title h4 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
    }

    .ed-dossier-count {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: var(--ed-navy-700);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .ed-nav-legend-bar {
        display: flex;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        padding: 8px 12px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 0.72rem;
        color: #475569;
        font-weight: 700;
    }

    .ed-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .legend-swatch {
        width: 10px;
        height: 10px;
        border-radius: 3px;
    }

    .legend-swatch.answered {
        background: var(--ed-emerald-600);
    }

    .legend-swatch.flagged {
        background: var(--ed-amber-500);
    }

    .legend-swatch.unvisited {
        background: #e2e8f0;
        border: 1px solid #cbd5e1;
    }

    /* شبكة أزرار أرقام الأسئلة */
    .ed-nav-questions-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        margin-bottom: 20px;
        max-height: 380px;
        overflow-y: auto;
        padding: 2px;
    }

    .ed-nav-cell {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        color: #475569;
        font-size: 0.88rem;
        font-weight: 800;
        text-decoration: none;
        position: relative;
        transition: all 0.15s ease;
    }

    .ed-nav-cell:hover {
        border-color: var(--ed-navy-600);
        color: var(--ed-navy-700);
        background: #eff6ff;
        transform: translateY(-1px);
    }

    .ed-nav-cell.answered {
        background: var(--ed-emerald-600);
        border-color: var(--ed-emerald-700);
        color: #ffffff;
    }

    .ed-nav-cell.flagged {
        border-color: var(--ed-amber-500);
        background: var(--ed-amber-100);
        color: var(--ed-amber-700);
    }

    .ed-nav-cell.answered.flagged {
        background: linear-gradient(135deg, var(--ed-emerald-600) 50%, var(--ed-amber-500) 50%);
        color: #ffffff;
    }

    .cell-flag-dot {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: transparent;
    }

    .ed-nav-cell.flagged .cell-flag-dot {
        background: var(--ed-amber-600);
    }

    /* بطاقة الأمان والمراقبة بالطالب */
    .ed-student-proctor-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 0.76rem;
    }

    .ed-proctor-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ed-proctor-lbl {
        color: var(--ed-slate-500);
    }

    .ed-proctor-val {
        font-weight: 700;
        color: var(--ed-slate-900);
    }

    .ed-btn-side-submit {
        width: 100%;
        background: #ffffff;
        border: 1.5px solid var(--ed-emerald-600);
        color: var(--ed-emerald-700);
        padding: 12px;
        border-radius: 8px;
        font-size: 0.92rem;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .ed-btn-side-submit:hover {
        background: var(--ed-emerald-600);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
    }

    /* زر عائم للجوال */
    .ed-mobile-fab-map {
        display: none;
        position: fixed;
        bottom: 24px;
        left: 24px;
        z-index: 990;
        background: var(--ed-navy-800);
        color: #ffffff;
        border: none;
        border-radius: 50px;
        padding: 12px 18px;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.4);
        font-size: 1rem;
        font-weight: 800;
        cursor: pointer;
        align-items: center;
        gap: 8px;
    }

    .ed-fab-count {
        background: var(--ed-amber-500);
        color: var(--ed-slate-900);
        font-size: 0.72rem;
        padding: 2px 8px;
        border-radius: 12px;
    }

    /* =========================================================
       5. نافذة التعليمات الكلاسيكية (Pre-flight Modal)
       ========================================================= */
    .ed-preflight-overlay {
        position: fixed;
        inset: 0;
        background: rgba(9, 26, 46, 0.85);
        backdrop-filter: blur(6px);
        z-index: 2000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .ed-preflight-card {
        background: #ffffff;
        border: 2px solid var(--ed-amber-600);
        border-radius: 18px;
        max-width: 680px;
        width: 100%;
        padding: 32px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        text-align: right;
        box-sizing: border-box;
        animation: modalClassicIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes modalClassicIn {
        from { opacity: 0; transform: scale(0.96) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .ed-preflight-crest {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid #e2e8f0;
    }

    .ed-crest-seal {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--ed-navy-700);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        box-shadow: 0 4px 10px rgba(30, 58, 138, 0.3);
    }

    .ed-crest-titles {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .ed-crest-gov {
        font-size: 0.78rem;
        color: var(--ed-amber-700);
        font-weight: 800;
    }

    .ed-crest-sub {
        font-size: 0.95rem;
        color: var(--ed-slate-700);
        font-weight: 700;
    }

    .ed-preflight-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--ed-slate-900);
        margin: 0 0 6px;
        line-height: 1.35;
    }

    .ed-preflight-desc {
        font-size: 0.88rem;
        color: var(--ed-slate-500);
        margin: 0 0 20px;
    }

    .ed-preflight-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 22px;
    }

    .ed-stat-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .ed-stat-box i {
        font-size: 1.25rem;
        margin-bottom: 2px;
    }

    .ed-stat-box strong {
        font-size: 1.15rem;
        color: var(--ed-slate-900);
    }

    .ed-stat-box span {
        font-size: 0.72rem;
        color: var(--ed-slate-500);
        font-weight: 700;
    }

    .ed-preflight-schedule-banner {
        background: #f0f7ff;
        border: 1.5px solid #bfdbfe;
        border-radius: 12px;
        padding: 12px 18px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .schedule-info-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .schedule-icon-wrap {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--ed-navy-700);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2);
    }

    .schedule-lbl {
        font-size: 0.75rem;
        color: #475569;
        font-weight: 700;
        display: block;
        margin-bottom: 2px;
    }

    .schedule-val {
        font-size: 0.96rem;
        color: var(--ed-slate-900);
        font-weight: 800;
    }

    .badge-timing-schedule {
        font-size: 0.78rem;
        font-weight: 800;
        padding: 6px 14px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-timing-schedule.upcoming {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-timing-schedule.active-limited {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .badge-timing-schedule.always-open {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
    }
    .badge-timing-schedule.expired {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .ed-preflight-rules {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .ed-preflight-rules h4 {
        margin: 0 0 12px;
        font-size: 0.92rem;
        font-weight: 800;
        color: var(--ed-slate-900);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ed-rules-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .ed-rule-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 0.82rem;
        color: var(--ed-slate-700);
        line-height: 1.55;
    }

    .ed-rule-item i {
        margin-top: 3px;
        flex-shrink: 0;
    }

    .ed-rule-item.danger {
        color: #991b1b;
        background: #fef2f2;
        padding: 6px 10px;
        border-radius: 6px;
        border-right: 3px solid #dc2626;
    }

    .ed-preflight-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .ed-btn-cancel {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: var(--ed-slate-700);
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .ed-btn-cancel:hover {
        background: var(--ed-slate-100);
    }

    .ed-btn-start {
        background: linear-gradient(135deg, var(--ed-emerald-600) 0%, var(--ed-emerald-700) 100%);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        padding: 12px 26px;
        border-radius: 10px;
        font-size: 0.96rem;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3);
        transition: all 0.2s;
    }

    .ed-btn-start:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(5, 150, 105, 0.4);
    }

    /* =========================================================
       6. نافذة تكبير الصورة (Lightbox Modal)
       ========================================================= */
    .ed-modal-lightbox {
        position: fixed;
        inset: 0;
        background: rgba(9, 26, 46, 0.92);
        backdrop-filter: blur(8px);
        z-index: 3000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .ed-lightbox-container {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
    }

    .ed-lightbox-close {
        position: absolute;
        top: 14px;
        left: 14px;
        background: rgba(0, 0, 0, 0.6);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        width: 38px;
        height: 38px;
        border-radius: 50%;
        font-size: 1.1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        transition: background 0.2s;
    }

    .ed-lightbox-close:hover {
        background: #dc2626;
    }

    .ed-lightbox-img-wrap {
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: auto;
    }

    .ed-lightbox-img-wrap img {
        max-width: 85vw;
        max-height: 75vh;
        object-fit: contain;
        border-radius: 8px;
    }

    .ed-lightbox-footer {
        background: #1e293b;
        padding: 10px 20px;
        text-align: center;
        font-size: 0.78rem;
        color: #94a3b8;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* =========================================================
       7. التجاوب مع مختلف الشاشات والأجهزة
       ========================================================= */
    @media (max-width: 1100px) {
        .ed-exam-container {
            grid-template-columns: 1fr;
            padding: 0 16px;
        }

        .ed-exam-navigator {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 2500;
            background: rgba(9, 26, 46, 0.7);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .ed-exam-navigator.mobile-open {
            display: flex;
        }

        .ed-nav-dossier {
            max-width: 440px;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
        }

        .ed-mobile-fab-map {
            display: inline-flex;
        }
    }

    @media (max-width: 768px) {
        .ed-topbar-inner {
            padding: 10px 14px;
        }

        .ed-topbar-title {
            font-size: 1rem;
        }

        .ed-timer-display {
            font-size: 1.25rem;
        }

        .ed-ctrl-btn span {
            display: none;
        }

        .ed-mcq-grid {
            grid-template-columns: 1fr;
        }

        .ed-q-main-text {
            font-size: 1.02rem;
            line-height: 1.7;
        }

        .ed-preflight-stats {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .ed-preflight-actions {
            flex-direction: column;
        }

        .ed-btn-cancel, .ed-btn-start {
            width: 100%;
            justify-content: center;
        }

        .ed-palestine-exam-sheet {
            padding: 16px 14px;
        }

        .ed-sheet-crest-section {
            grid-template-columns: 1fr;
            text-align: center;
            gap: 14px;
        }

        .ed-sheet-gov-side, .ed-sheet-meta-side {
            text-align: center;
            align-items: center;
        }

        .ed-sheet-info-table {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            padding: 10px;
        }

        .ed-palestine-footer-sheet {
            padding: 18px 14px;
        }

        .ed-palestine-closing-banner {
            flex-direction: column;
            gap: 10px;
        }

        .ed-signature-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .ed-footer-action-wrap {
            flex-direction: column;
            text-align: center;
        }

        .ed-footer-notice {
            justify-content: center;
        }

        .ed-btn-grand-submit {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- مكتبات الاتصال والتنبيهات -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // منع الرجوع العشوائي بالمتصفح أثناء الاختبار
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };

    // تأكيد قبل مغادرة الصفحة أثناء سير الاختبار
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

    // حالة المراقبة والأمان
    let isExamRunning = false;
    let isSubmittingExam = false;
    let tabSwitches = 0;
    let screenshots = 0;
    let cheatingFlags = [];
    let lastTabSwitchTime = 0;
    let lastScreenshotTime = 0;

    // ==========================================
    // 1. إدارة ملء الشاشة الكلاسيكية الحقيقية
    // ==========================================
    function toggleExamFullscreen() {
        const doc = document.documentElement;
        const icon = document.getElementById('fullscreenIcon');
        const text = document.getElementById('fullscreenText');

        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.mozFullScreenElement && !document.msFullscreenElement) {
            if (doc.requestFullscreen) {
                doc.requestFullscreen().catch(err => {});
            } else if (doc.webkitRequestFullscreen) {
                doc.webkitRequestFullscreen();
            } else if (doc.mozRequestFullScreen) {
                doc.mozRequestFullScreen();
            } else if (doc.msRequestFullscreen) {
                doc.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen().catch(err => {});
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    }

    document.addEventListener('fullscreenchange', updateFullscreenButtonState);
    document.addEventListener('webkitfullscreenchange', updateFullscreenButtonState);
    document.addEventListener('mozfullscreenchange', updateFullscreenButtonState);
    document.addEventListener('MSFullscreenChange', updateFullscreenButtonState);

    function updateFullscreenButtonState() {
        const isFull = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
        const icon = document.getElementById('fullscreenIcon');
        const text = document.getElementById('fullscreenText');
        if (icon) {
            icon.className = isFull ? 'fa-solid fa-compress' : 'fa-solid fa-expand';
        }
        if (text) {
            text.textContent = isFull ? 'تصغير الشاشة' : 'ملء الشاشة';
        }
    }

    // ==========================================
    // 2. معالج الصور الهادئ بدون أي رسائل تنبيهية مزعجة
    // ==========================================
    function handleExamImageFallback(img) {
        const raw = img.getAttribute('data-candidates');
        if (!raw) {
            const box = img.closest('.ed-q-figure-box') || img.closest('.ed-opt-visual-wrapper');
            if (box) box.style.display = 'none';
            return;
        }

        const candidates = raw.split('|').filter(Boolean);
        let idx = parseInt(img.getAttribute('data-candidate-idx') || '0', 10) + 1;

        if (idx < candidates.length) {
            img.setAttribute('data-candidate-idx', idx);
            img.src = candidates[idx];
        } else {
            // استنفاد جميع البدائل بهدوء ودون إظهار أي رسالة خطأ مزعجة للطالب
            const box = img.closest('.ed-q-figure-box') || img.closest('.ed-opt-visual-wrapper');
            if (box) box.style.display = 'none';
        }
    }

    // ==========================================
    // 3. بدء الاختبار والعداد الزمني
    // ==========================================
    function startExamOfficially() {
        const modal = document.getElementById('examPreflightModal');
        if (modal) modal.style.display = 'none';

        isExamRunning = true;

        // محاولة الانتقال لملء الشاشة بتجربة مستخدم ممتازة
        try {
            toggleExamFullscreen();
        } catch(e) {}

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

            timerBox.textContent = hStr + mStr + " : " + sStr;

            if (timeLeft <= 60) {
                timerBox.style.color = '#ef4444';
            } else if (timeLeft <= 300) {
                timerBox.style.color = '#f59e0b';
            } else {
                timerBox.style.color = '#38bdf8';
            }

            if (--timeLeft < 0) {
                clearInterval(timerInterval);
                timerBox.textContent = "00 : 00 : 00";
                autoSubmitExam();
            }
        }

        renderTimer();
        timerInterval = setInterval(renderTimer, 1000);
    }

    // استعادة وقت الجلسة تلقائياً في حال تحديث الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        const storedStart = sessionStorage.getItem('exam_start_time_{{ $exam->id }}');
        if (storedStart) {
            const elapsed = Math.floor((Date.now() - parseInt(storedStart, 10)) / 1000);
            const totalDuration = {{ $exam->duration_minutes * 60 }};
            if (elapsed < totalDuration) {
                timeLeft = totalDuration - elapsed;
                startExamOfficially();
            }
        }
    });

    // ==========================================
    // 4. محرك النزاهة الأكاديمية والرصد الذكي
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

        document.getElementById('tabSwitchesCount').value = tabSwitches;
        document.getElementById('screenshotsCount').value = screenshots;
        document.getElementById('cheatingFlagsInput').value = JSON.stringify(cheatingFlags);

        // تحديث الشارة اللحظية
        const badge = document.getElementById('proctoringBadge');
        const text = document.getElementById('proctoringText');
        const stateText = document.getElementById('proctorStateText');
        if (badge && text) {
            badge.classList.add('warning');
            text.textContent = `تنبيه رصد (${tabSwitches + screenshots})`;
        }
        if (stateText) {
            stateText.className = 'ed-proctor-val text-danger';
            stateText.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> تم تسجيل نشاط (${tabSwitches + screenshots})`;
        }

        // إشعار الخادم في الخلفية
        try {
            axios.post("{{ route('student.exams.cheatingIncident', $exam->id) }}", {
                violation_type: type,
                details: details
            }).catch(() => {});
        } catch (e) {}

        // تنبيه علوي هادئ وغير معطّل لتدفق الاختبار
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            title: `تم توثيق حركة خارج نطاق الاختبار (${details}) للأستاذ المشرف.`,
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true
        });
    }

    // رصد مغادرة التبويب (Visibility Change)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden' && isExamRunning && !isSubmittingExam) {
            recordCheatingIncident('tab_switch', 'مغادرة نافذة الاختبار');
        }
    });

    // رصد فقدان تركيز النافذة (Window Blur)
    window.addEventListener('blur', function() {
        const modal = document.getElementById('imageModal');
        const isZoomOpen = modal && modal.style.display === 'flex';
        if (isExamRunning && !isSubmittingExam && !isZoomOpen) {
            recordCheatingIncident('tab_switch', 'الخروج من نافذة الاختبار');
        }
    });

    // اعتراض محاولات تصوير الشاشة
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

    // منع النسخ والقائمة المنسدلة
    document.addEventListener('copy', function(e) {
        if (isExamRunning && !isSubmittingExam) {
            e.preventDefault();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'نسخ محتوى الاختبار محظور حفاظاً على النزاهة الأكاديمية',
                showConfirmButton: false,
                timer: 3000
            });
        }
    });

    document.addEventListener('contextmenu', function(e) {
        if (isExamRunning && !isSubmittingExam) {
            e.preventDefault();
        }
    });

    // ==========================================
    // 5. إدارة الإجابات والتقدم والتنقل
    // ==========================================
    function markAsAnswered(index, qId) {
        answeredSet.add(qId);
        const navBtn = document.getElementById('nav_btn_' + index);
        if (navBtn) navBtn.classList.add('answered');
        updateProgress();
    }

    function updateProgress() {
        const count = answeredSet.size;
        const percent = (count / totalQuestions) * 100;
        document.getElementById('examProgressBar').style.width = percent + '%';
        document.getElementById('answeredCounter').innerText = count;
        const floatingCounter = document.getElementById('floatingAnsweredCounter');
        if (floatingCounter) floatingCounter.innerText = `${count}/${totalQuestions}`;
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
                if (text) text.textContent = 'مميّز';
            } else {
                icon.classList.remove('fa-solid');
                icon.classList.add('fa-regular');
                if (text) text.textContent = 'مراجعة';
            }
        }
    }

    function updateFileName(input, qId, index) {
        if (input.files && input.files[0]) {
            const pill = document.getElementById('file_name_' + qId);
            pill.style.display = 'flex';
            pill.innerHTML = `<i class="fa-solid fa-circle-check text-emerald"></i> <span>${input.files[0].name}</span>`;
            markAsAnswered(index, qId);
        }
    }

    // إدارة تكبير الصور (Image Modal)
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

    // خريطة الأسئلة للهواتف
    function toggleMobileNavDrawer() {
        const sidebar = document.getElementById('examNavSidebar');
        if (sidebar) {
            sidebar.classList.toggle('mobile-open');
        }
    }

    // إغلاق الدرج عند النقر على أي سؤال بالجوال
    document.querySelectorAll('.ed-nav-cell').forEach(cell => {
        cell.addEventListener('click', function() {
            const sidebar = document.getElementById('examNavSidebar');
            if (sidebar && sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
            }
        });
    });

    // ==========================================
    // 6. تسليم الاختبار النهائي
    // ==========================================
    function confirmSubmission() {
        const unCount = totalQuestions - answeredSet.size;
        let warningText = `لقد قمت بحل ${answeredSet.size} من أصل ${totalQuestions} سؤالاً.`;
        if (unCount > 0) {
            warningText += `\nهنالك ${unCount} سؤالاً لم تقم بالإجابة عليها بعد.`;
        }

        Swal.fire({
            title: 'هل ترغب في تسليم ورقة الاختبار رسمياً؟',
            text: warningText,
            icon: unCount > 0 ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، اعتمد التسليم النهائي',
            cancelButtonText: 'العودة للمراجعة'
        }).then((result) => {
            if (result.isConfirmed) finalizeExamSubmission();
        });
    }

    function finalizeExamSubmission() {
        isSubmittingExam = true;
        window.removeEventListener('beforeunload', window.onbeforeunload);
        window.onbeforeunload = null;

        const btn = document.getElementById('submitBtn');
        const formData = new FormData(document.getElementById('fullExamForm'));

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = "<i class='fa-solid fa-spinner fa-spin'></i> جاري توثيق واعتماد الإجابات...";
        }

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
                    confirmButtonColor: '#059669',
                    confirmButtonText: 'عرض النتائج والتقرير الأكاديمي'
                }).then(() => {
                    window.location.href = res.data.redirect || "{{ route('student.exams.index') }}";
                });
            } else {
                Swal.fire('تنبيه أكاديمي', res.data.message || 'حدث خطأ أثناء معالجة الإجابات.', 'warning');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = "<i class='fa-solid fa-paper-plane'></i> إعادة محاولة التسليم";
                }
                isSubmittingExam = false;
            }
        })
        .catch(err => {
            let errorMsg = 'تعذر الاتصال بالخادم لإرسال الإجابات، يرجى المحاولة فوراً.';
            if (err.response && err.response.data && err.response.data.message) {
                errorMsg = err.response.data.message;
            }
            Swal.fire('خطأ في الاتصال', errorMsg, 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = "<i class='fa-solid fa-paper-plane'></i> إعادة محاولة التسليم";
            }
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