@extends('layouts.app')

@section('title', __('قاعة الاختبار الرقمية') . ' | ' . $exam->title)
@section('no-sidebar', 'true')

@section('content')
<div class="ed-exam-take-wrapper">

    <!-- شاشة تأكيد البدء الأكاديمية (University Pre-flight Modal) -->
    <div id="examPreflightModal" class="ed-preflight-overlay">
        <div class="ed-preflight-card">
            <div class="ed-preflight-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <span class="ed-preflight-sub-tag">
                {{ $exam->subject->name_ar ?? __('مادة دراسية') }}
            </span>
            <h2 class="ed-preflight-title">{{ $exam->title }}</h2>
            <p class="ed-preflight-desc">{{ __('يرجى مراجعة تفاصيل وتعليمات الاختبار الأكاديمي قبل بدء الوقت الرسمي.') }}</p>

            <div class="ed-preflight-stats">
                <div class="ed-stat-box">
                    <i class="fa-regular fa-clock text-amber"></i>
                    <strong>{{ $exam->duration_minutes }} {{ __('دقيقة') }}</strong>
                    <span>{{ __('المدة الزمنية') }}</span>
                </div>
                <div class="ed-stat-box">
                    <i class="fa-solid fa-list-check text-navy"></i>
                    <strong>{{ count($exam->questions) }} {{ __('أسئلة') }}</strong>
                    <span>{{ __('إجمالي الأسئلة') }}</span>
                </div>
                <div class="ed-stat-box">
                    <i class="fa-solid fa-star text-emerald"></i>
                    <strong>{{ $exam->total_grade ?? $exam->questions->sum('points') }} {{ __('درجة') }}</strong>
                    <span>{{ __('الدرجة الكلية') }}</span>
                </div>
            </div>

            <div class="ed-preflight-rules">
                <h4>
                    <i class="fa-solid fa-shield-halved text-navy"></i> {{ __('الإرشادات الأكاديمية وضوابط النزاهة:') }}
                </h4>
                <ul>
                    <li>{{ __('يبدأ العداد التنازلي فور النقر على زر "أوافق وأبدأ الاختبار الآن".') }}</li>
                    <li><strong class="text-danger">{{ __('ممنوع مغادرة الصفحة أو تبديل التبويب:') }}</strong> {{ __('النظام يرصد حركات الشاشة ويخطر المعلم بأي نشاط مريب.') }}</li>
                    <li><strong class="text-danger">{{ __('ممنوع أخذ لقطات شاشة (Screenshot):') }}</strong> {{ __('محاولات التصوير محظورة وتُسجل كمخالفة نزاهة أكاديمية.') }}</li>
                    <li>{{ __('عند انتهاء الوقت سيتم تسليم إجاباتك المحفوظة تلقائياً وبشكل فوري.') }}</li>
                </ul>
            </div>

            <div class="ed-preflight-actions">
                <a href="{{ route('student.exams.index') }}" class="ed-btn-cancel">
                    {{ __('العودة لاحقاً') }}
                </a>
                <button type="button" onclick="startExamOfficially()" class="ed-btn-start">
                    <i class="fa-solid fa-play"></i>
                    <span>{{ __('أوافق وأبدأ الاختبار الآن') }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- شريط الاختبار العلوي الثابت مع التجاوب التام -->
    <header class="ed-take-topbar">
        <div class="ed-take-bar-content">
            <div class="ed-exam-meta-block">
                <div class="ed-exam-badge-tag">
                    <i class="fas fa-book-open"></i>
                    <span>{{ $exam->subject->name_ar ?? $exam->subject->name ?? __('مادة دراسية') }}</span>
                </div>
                <h1 class="ed-exam-title">{{ $exam->title }}</h1>
            </div>

            <!-- شارة المراقبة ومكافحة الغش الحية -->
            <div class="ed-proctoring-status" id="proctoringBadge" title="{{ __('نظام الرصد الذكي لمكافحة الغش نشط') }}">
                <span class="proctoring-dot"></span>
                <i class="fa-solid fa-shield-halved"></i>
                <span id="proctoringText">{{ __('المراقبة الأكاديمية: نشطة') }}</span>
            </div>

            <div class="ed-timer-block">
                <span class="timer-label"><i class="far fa-clock"></i> {{ __('الوقت المتبقي') }}</span>
                <div id="countdown_timer">00:00</div>
            </div>
        </div>

        <div class="ed-take-progress-track">
            <div class="ed-take-progress-fill" id="examProgressBar"></div>
        </div>
    </header>

    <!-- نموذج الاختبار والأسئلة -->
    <form id="fullExamForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="tab_switches_count" id="tabSwitchesCount" value="0">
        <input type="hidden" name="screenshots_count" id="screenshotsCount" value="0">
        <input type="hidden" name="cheating_flags" id="cheatingFlagsInput" value="[]">

        <div class="ed-take-layout">

            <!-- قائمة الأسئلة -->
            <main class="ed-questions-flow">
                @foreach($exam->questions as $index => $q)
                <article class="ed-question-card" id="q_card_{{ $q->id }}">
                    
                    <div class="ed-q-header">
                        <div class="ed-q-info">
                            <span class="ed-q-number">سؤال {{ $index + 1 }} من {{ count($exam->questions) }}</span>
                            <span class="ed-q-score"><i class="far fa-star"></i> {{ $q->points }} درجات</span>
                        </div>
                        <button type="button" class="ed-flag-btn" onclick="toggleFlag({{ $q->id }}, {{ $index }})" title="{{ __('تمييز للمراجعة') }}">
                            <i class="far fa-bookmark" id="flag_icon_{{ $q->id }}"></i>
                            <span>{{ __('مراجعة') }}</span>
                        </button>
                    </div>

                    <div class="ed-q-body">
                        <h2 class="ed-q-text">{!! nl2br(e($q->question_text)) !!}</h2>

                        @php
                            $qImg = $q->image_url;
                            $localFallback = $q->local_image_url;
                        @endphp

                        @if(!empty($qImg) || !empty($q->image))
                        <div class="ed-q-image-box">
                            <div class="ed-q-image-header">
                                <span class="ed-q-image-tag"><i class="fa-solid fa-image"></i> {{ __('مرفق السؤال (رسم توضيحي / مسألة)') }}</span>
                                <button type="button" class="ed-zoom-btn" onclick="openImageModal(this.dataset.src || '{{ $qImg }}')" data-src="{{ $qImg }}">
                                    <i class="fas fa-search-plus"></i> {{ __('تكبير الصورة بدقة عالية') }}
                                </button>
                            </div>
                            <div class="ed-img-canvas">
                                <img src="{{ $qImg }}" 
                                     alt="{{ __('مرفق السؤال') }}" 
                                     class="ed-q-img" 
                                     onclick="openImageModal(this.src)" 
                                     loading="lazy"
                                     onerror="if(!this.dataset.triedFallback && '{{ $localFallback }}'){ this.dataset.triedFallback='1'; this.src='{{ $localFallback }}'; const btn = this.closest('.ed-q-image-box').querySelector('.ed-zoom-btn'); if(btn) btn.dataset.src='{{ $localFallback }}'; } else { this.closest('.ed-q-image-box').classList.add('load-failed'); }">
                                <div class="ed-img-fallback-notice">
                                    <i class="fa-solid fa-circle-exclamation" style="color: #d97706; font-size: 1.1rem;"></i>
                                    <span>{{ __('تعذر جلب المرفق السحابي مؤقتاً') }}</span>
                                    <button type="button" class="ed-btn-retry" onclick="const img = this.closest('.ed-q-image-box').querySelector('img'); img.src = '{{ $qImg }}?r=' + Date.now(); this.closest('.ed-q-image-box').classList.remove('load-failed');">
                                        <i class="fas fa-rotate-right"></i> {{ __('إعادة المحاولة') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($q->type == 'mcq')
                            <div class="ed-options-grid">
                                @foreach(['a', 'b', 'c', 'd'] as $option)
                                @php
                                    $optImg = $q->getOptionImageUrl($option);
                                    $hasOptImg = !empty($optImg);
                                    $hasOptText = !empty($q->$option);
                                @endphp
                                @if($hasOptText || $hasOptImg)
                                <label class="ed-option-item {{ $hasOptImg ? 'has-opt-img' : '' }}">
                                    <input 
                                        type="radio" 
                                        name="answers[{{ $q->id }}]" 
                                        value="{{ $option }}" 
                                        hidden 
                                        onchange="markAsAnswered({{ $index }}, {{ $q->id }})"
                                    >
                                    <div class="ed-option-box">
                                        <span class="ed-opt-letter">{{ strtoupper($option) }}</span>
                                        <div class="ed-opt-content">
                                            @if($hasOptText)
                                                <span class="ed-opt-text">{{ $q->$option }}</span>
                                            @endif
                                            @if($hasOptImg)
                                                <div class="ed-opt-img-wrapper" onclick="event.stopPropagation(); openImageModal('{{ $optImg }}')" title="{{ __('انقر لتكبير صورة الخيار') }}">
                                                    <img src="{{ $optImg }}" alt="خيار {{ strtoupper($option) }}" class="ed-opt-thumb" loading="lazy">
                                                    <span class="ed-opt-zoom-tag"><i class="fas fa-search-plus"></i> {{ __('تكبير') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                                @endif
                                @endforeach
                            </div>
                        @else
                            <div class="ed-written-answer-box">
                                <label class="ed-input-label">{{ __('اكتب إجابتك النموذجية:') }}</label>
                                <textarea 
                                    name="answers[{{ $q->id }}]" 
                                    rows="4" 
                                    placeholder="{{ __('دون إجابتك هنا بوضوح وبشكل كامل...') }}" 
                                    class="ed-textarea" 
                                    oninput="markAsAnswered({{ $index }}, {{ $q->id }})"
                                ></textarea>

                                @if($q->require_file)
                                <div class="ed-file-upload-zone">
                                    <label class="ed-file-upload-btn">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>{{ __('إرفاق ملف الحل أو صورة (PDF, JPG, PNG)') }}</span>
                                        <input 
                                            type="file" 
                                            name="files[{{ $q->id }}]" 
                                            accept="image/*,application/pdf" 
                                            hidden 
                                            onchange="updateFileName(this, {{ $q->id }}, {{ $index }})"
                                        >
                                    </label>
                                    <span class="ed-selected-file-name" id="file_name_{{ $q->id }}"></span>
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>

                </article>
                @endforeach

                <div class="ed-exam-footer-submit">
                    <button type="button" onclick="confirmSubmission()" class="ed-btn ed-btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i>
                        <span>{{ __('تسليم الاختبار النهائي') }}</span>
                    </button>
                    <p class="ed-submit-hint">{{ __('تأكد من مراجعة كافة الأسئلة وإجاباتك قبل الضغط على تسليم الاختبار.') }}</p>
                </div>
            </main>

            <!-- خريطة الأسئلة الجانبية (سطح المكتب والتابلت) -->
            <aside class="ed-take-sidebar" id="examNavSidebar">
                <div class="ed-nav-card">
                    <div class="ed-nav-header">
                        <h4><i class="fas fa-map-marked-alt"></i> {{ __('خريطة الأسئلة') }}</h4>
                        <span class="ed-progress-counter"><strong id="answeredCounter">0</strong> / {{ count($exam->questions) }}</span>
                    </div>

                    <div class="ed-nav-legend">
                        <span class="legend-item"><span class="dot answered"></span>{{ __('تم الحل') }}</span>
                        <span class="legend-item"><span class="dot flagged"></span>{{ __('للمراجعة') }}</span>
                        <span class="legend-item"><span class="dot unvisited"></span>{{ __('متبقي') }}</span>
                    </div>

                    <div class="ed-nav-grid">
                        @foreach($exam->questions as $index => $q)
                        <a href="#q_card_{{ $q->id }}" class="ed-nav-cell" id="nav_btn_{{ $index }}">
                            {{ $index + 1 }}
                        </a>
                        @endforeach
                    </div>

                    <div class="ed-sidebar-submit">
                        <button type="button" onclick="confirmSubmission()" class="ed-btn ed-btn-outline" style="width: 100%; justify-content: center;">
                            <i class="fas fa-check-circle"></i> {{ __('تسليم الاختبار') }}
                        </button>
                    </div>
                </div>
            </aside>

        </div>
    </form>

</div>

<!-- زر عائم لخريطة الأسئلة على الهواتف -->
<button type="button" class="ed-floating-map-toggle" onclick="toggleMobileNavDrawer()" title="{{ __('خريطة الأسئلة') }}">
    <i class="fas fa-list-ol"></i>
    <span class="floating-counter" id="floatingAnsweredCounter">0/{{ count($exam->questions) }}</span>
</button>

<!-- نافذة تكبير الصورة -->
<div class="ed-image-modal" id="imageModal" onclick="closeImageModal()">
    <span class="ed-image-modal-close">&times;</span>
    <img id="modalImageTarget" src="" alt="{{ __('صورة مكبرة') }}">
</div>

<style>
    /* Reset and Layout Base */
    .ed-exam-take-wrapper {
        max-width: 1240px;
        margin: 0 auto;
        padding: 16px 16px 80px;
        box-sizing: border-box;
        width: 100%;
    }

    /* Fixed Topbar */
    .ed-take-topbar {
        position: sticky;
        top: 10px;
        z-index: 950;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border-radius: 16px;
        padding: 14px 20px 0;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .ed-take-bar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .ed-exam-meta-block .ed-exam-title {
        font-size: 1.2rem;
        font-weight: 800;
        margin: 3px 0 0;
        color: #ffffff;
        line-height: 1.3;
    }

    .ed-exam-badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.74rem;
        color: #93c5fd;
        font-weight: 700;
    }

    /* شارة المراقبة الحية لمكافحة الغش */
    .ed-proctoring-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.35);
        color: #34d399;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.78rem;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .ed-proctoring-status .proctoring-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseProctor 1.8s infinite;
    }

    @keyframes pulseProctor {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .ed-proctoring-status.warning {
        background: rgba(220, 38, 38, 0.2);
        border-color: rgba(239, 68, 68, 0.5);
        color: #f87171;
    }

    .ed-proctoring-status.warning .proctoring-dot {
        background: #ef4444;
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    }

    .ed-timer-block {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 6px 16px;
        text-align: center;
        flex-shrink: 0;
    }

    .timer-label {
        display: block;
        font-size: 0.68rem;
        color: #cbd5e1;
        font-weight: 600;
    }

    #countdown_timer {
        font-family: 'Inter', monospace;
        font-size: 1.45rem;
        font-weight: 900;
        color: #38bdf8;
        direction: ltr;
        line-height: 1.1;
    }

    .ed-take-progress-track {
        height: 5px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 999px;
        overflow: hidden;
    }

    .ed-take-progress-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #3b82f6 0%, #10b981 100%);
        transition: width 0.3s ease;
    }

    /* Layout Grid */
    .ed-take-layout {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 20px;
        align-items: start;
    }

    /* Question Cards */
    .ed-question-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        box-sizing: border-box;
    }

    .ed-q-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 14px;
        margin-bottom: 18px;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-q-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ed-q-number {
        font-size: 0.82rem;
        font-weight: 800;
        color: #1d4ed8;
        background: #eff6ff;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .ed-q-score {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 700;
    }

    .ed-flag-btn {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
    }

    .ed-flag-btn:hover, .ed-flag-btn.flagged {
        background: #fffbeb;
        color: #d97706;
        border-color: #fde68a;
    }

    .ed-q-text {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.8;
        margin: 0 0 18px;
        word-break: break-word;
    }

    /* Classic Question Image Display */
    .ed-q-image-box {
        position: relative;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 22px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        transition: all 0.2s ease;
    }

    .ed-q-image-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ed-q-image-tag {
        font-size: 0.8rem;
        font-weight: 800;
        color: #1e3a8a;
        background: #eff6ff;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .ed-img-canvas {
        position: relative;
        text-align: center;
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px;
        border: 1px dashed #e2e8f0;
    }

    .ed-q-img {
        max-height: 420px;
        max-width: 100%;
        border-radius: 6px;
        object-fit: contain;
        cursor: zoom-in;
        transition: transform 0.2s ease;
    }

    .ed-q-img:hover {
        transform: scale(1.01);
    }

    .ed-zoom-btn {
        background: #1e3a8a;
        color: #ffffff;
        border: 1px solid #172554;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }

    .ed-zoom-btn:hover {
        background: #1d4ed8;
    }

    .ed-img-fallback-notice {
        display: none;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 6px;
        margin-top: 10px;
        font-size: 0.82rem;
        color: #92400e;
        font-weight: 600;
    }

    .ed-q-image-box.load-failed .ed-img-fallback-notice {
        display: flex;
    }

    .ed-btn-retry {
        background: #d97706;
        color: #ffffff;
        border: none;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* MCQ Options Grid */
    .ed-options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .ed-option-item {
        cursor: pointer;
        display: block;
        margin: 0;
    }

    .ed-option-box {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .ed-option-item:hover .ed-option-box {
        border-color: #93c5fd;
        background: #f8fafc;
    }

    .ed-option-item input:checked + .ed-option-box {
        border-color: #1d4ed8;
        background: #eff6ff;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
    }

    .ed-opt-letter {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        font-weight: 800;
        font-size: 0.88rem;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .ed-option-item input:checked + .ed-option-box .ed-opt-letter {
        background: #1d4ed8;
        color: #ffffff;
    }

    .ed-opt-text {
        font-size: 0.92rem;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.5;
        flex: 1;
        word-break: break-word;
    }

    .ed-opt-content {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
        min-width: 0;
    }

    .ed-opt-img-wrapper {
        position: relative;
        display: inline-block;
        max-width: 100%;
        cursor: zoom-in;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        padding: 4px;
        transition: all 0.2s ease;
    }

    .ed-opt-img-wrapper:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }

    .ed-opt-thumb {
        max-height: 120px;
        max-width: 100%;
        display: block;
        border-radius: 6px;
        object-fit: contain;
    }

    .ed-opt-zoom-tag {
        position: absolute;
        bottom: 4px;
        left: 4px;
        background: rgba(15, 23, 42, 0.85);
        color: #ffffff;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
        pointer-events: none;
    }

    /* Written / Essay */
    .ed-textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-family: inherit;
        font-size: 0.92rem;
        color: #0f172a;
        background: #f8fafc;
        resize: vertical;
        box-sizing: border-box;
        outline: none;
    }

    .ed-textarea:focus {
        border-color: #1d4ed8;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
    }

    .ed-file-upload-zone {
        margin-top: 12px;
    }

    .ed-file-upload-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #f1f5f9;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
    }

    .ed-file-upload-btn:hover {
        background: #e0e7ff;
        color: #1d4ed8;
        border-color: #1d4ed8;
    }

    /* Submit Button Footer */
    .ed-exam-footer-submit {
        text-align: center;
        padding: 24px 0 30px;
    }

    .ed-btn-submit {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: #ffffff;
        font-size: 1.05rem;
        font-weight: 800;
        padding: 14px 44px;
        border-radius: 30px;
        border: none;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(5, 150, 105, 0.25);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .ed-btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(5, 150, 105, 0.35);
    }

    .ed-submit-hint {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 10px;
    }

    /* Sidebar Navigation Card */
    .ed-nav-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        position: sticky;
        top: 90px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }

    .ed-nav-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-nav-header h4 {
        margin: 0;
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ed-progress-counter {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 700;
    }

    .ed-progress-counter strong {
        color: #1d4ed8;
    }

    .ed-nav-legend {
        display: flex;
        justify-content: space-between;
        font-size: 0.72rem;
        color: #64748b;
        margin-bottom: 14px;
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .dot.answered { background: #10b981; }
    .dot.flagged { background: #f59e0b; }
    .dot.unvisited { background: #cbd5e1; }

    .ed-nav-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-bottom: 16px;
    }

    .ed-nav-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #334155;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .ed-nav-cell:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1d4ed8;
    }

    .ed-nav-cell.answered {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #059669;
    }

    .ed-nav-cell.flagged {
        background: #fffbeb;
        border-color: #fde68a;
        color: #d97706;
    }

    /* Preflight Modal */
    .ed-preflight-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(8px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .ed-preflight-card {
        background: #ffffff;
        border-radius: 20px;
        max-width: 580px;
        width: 100%;
        padding: 28px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        border: 1px solid #e2e8f0;
        text-align: center;
        box-sizing: border-box;
    }

    .ed-preflight-icon {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        background: #eff6ff;
        color: #1e3a8a;
        display: grid;
        place-items: center;
        font-size: 1.6rem;
        margin: 0 auto 12px;
    }

    .ed-preflight-sub-tag {
        background: #eff6ff;
        color: #1e3a8a;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        display: inline-block;
        margin-bottom: 6px;
    }

    .ed-preflight-title {
        font-size: 1.25rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 8px;
    }

    .ed-preflight-desc {
        color: #64748b;
        font-size: 0.84rem;
        margin: 0 0 16px;
    }

    .ed-preflight-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 18px;
    }

    .ed-stat-box {
        background: #f8fafc;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    .ed-stat-box strong {
        font-size: 0.95rem;
        color: #0f172a;
        display: block;
        margin-top: 4px;
    }

    .ed-stat-box span {
        font-size: 0.7rem;
        color: #64748b;
    }

    .ed-preflight-rules {
        background: #f8fafc;
        border-radius: 10px;
        padding: 14px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
        text-align: right;
    }

    .ed-preflight-rules h4 {
        margin: 0 0 8px;
        font-size: 0.82rem;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ed-preflight-rules ul {
        margin: 0;
        padding-inline-start: 18px;
        font-size: 0.78rem;
        color: #334155;
        line-height: 1.7;
    }

    .ed-preflight-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .ed-btn-cancel {
        padding: 10px 18px;
        border-radius: 8px;
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .ed-btn-start {
        padding: 10px 24px;
        border-radius: 8px;
        background: #1e3a8a;
        color: #ffffff;
        border: none;
        font-weight: 800;
        font-size: 0.88rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* Image Modal */
    .ed-image-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(6px);
        z-index: 99999;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .ed-image-modal img {
        max-width: 92vw;
        max-height: 88vh;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
    }

    .ed-image-modal-close {
        position: absolute;
        top: 18px;
        right: 22px;
        color: #ffffff;
        font-size: 2rem;
        cursor: pointer;
    }

    /* Floating map button on mobile */
    .ed-floating-map-toggle {
        display: none;
        position: fixed;
        bottom: 20px;
        left: 20px;
        z-index: 900;
        background: #1e3a8a;
        color: #ffffff;
        border: none;
        border-radius: 30px;
        padding: 10px 18px;
        box-shadow: 0 8px 20px rgba(30, 58, 138, 0.4);
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        align-items: center;
        gap: 8px;
    }

    .floating-counter {
        background: rgba(255, 255, 255, 0.2);
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.74rem;
    }

    /* ====================================================================
       قواعد التجاوب الكامل للشاشات (Mobile, Tablet, Desktop)
       ==================================================================== */
    @media (max-width: 1024px) {
        .ed-take-layout {
            grid-template-columns: 1fr;
        }
        .ed-take-sidebar {
            display: none; /* يتم إتاحتها عبر الزر العائم أو نقلها للأسفل */
        }
        .ed-floating-map-toggle {
            display: inline-flex;
        }
        .ed-take-sidebar.mobile-open {
            display: block;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            z-index: 9990;
            padding: 20px;
            overflow-y: auto;
        }
        .ed-take-sidebar.mobile-open .ed-nav-card {
            max-width: 450px;
            margin: 40px auto;
            position: relative;
            top: 0;
        }
    }

    @media (max-width: 768px) {
        .ed-exam-take-wrapper {
            padding: 10px 8px 60px;
        }
        .ed-take-topbar {
            padding: 12px 14px 0;
            margin-bottom: 16px;
            border-radius: 12px;
        }
        .ed-take-bar-content {
            gap: 8px;
        }
        .ed-exam-meta-block .ed-exam-title {
            font-size: 1.05rem;
        }
        .ed-proctoring-status {
            padding: 4px 10px;
            font-size: 0.72rem;
        }
        #countdown_timer {
            font-size: 1.25rem;
        }
        .ed-question-card {
            padding: 16px 14px;
            border-radius: 12px;
            margin-bottom: 14px;
        }
        .ed-q-text {
            font-size: 0.98rem;
            line-height: 1.7;
        }
        /* على الشاشات الأصغر من 768px، الخيارات تصبح عموداً واحداً مريحاً */
        .ed-options-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        .ed-option-box {
            padding: 10px 14px;
        }
        .ed-btn-submit {
            width: 100%;
            padding: 12px;
            font-size: 0.95rem;
            justify-content: center;
        }
        .ed-preflight-card {
            padding: 20px 16px;
        }
        .ed-preflight-stats {
            grid-template-columns: 1fr;
            gap: 6px;
        }
        .ed-preflight-actions {
            flex-direction: column;
        }
        .ed-btn-cancel, .ed-btn-start {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };

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

    // حالة المراقبة ومكافحة الغش
    let isExamRunning = false;
    let isSubmittingExam = false;
    let tabSwitches = 0;
    let screenshots = 0;
    let cheatingFlags = [];
    let lastTabSwitchTime = 0;
    let lastScreenshotTime = 0;

    function startExamOfficially() {
        const modal = document.getElementById('examPreflightModal');
        if (modal) modal.style.display = 'none';

        isExamRunning = true;

        if (!sessionStorage.getItem('exam_start_time_{{ $exam->id }}')) {
            sessionStorage.setItem('exam_start_time_{{ $exam->id }}', Date.now());
        }

        if (timerInterval) clearInterval(timerInterval);

        timerInterval = setInterval(function() {
            let mins = Math.floor(timeLeft / 60);
            let secs = timeLeft % 60;
            timerBox.textContent = (mins < 10 ? '0' : '') + mins + " : " + (secs < 10 ? '0' : '') + secs;

            if (timeLeft <= 60) {
                timerBox.style.color = '#ef4444';
            } else if (timeLeft <= 300) {
                timerBox.style.color = '#f97316';
            }

            if (--timeLeft < 0) {
                clearInterval(timerInterval);
                timerBox.textContent = "00 : 00";
                autoSubmitExam();
            }
        }, 1000);
    }

    // استعادة وقت الجلسة تلقائياً في حال إعادة تحميل الصفحة
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
    // محرك مكافحة الغش الأكاديمي والرصد الذكي
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

        // تحديث الشارة اللحظية بهدوء في الشريط العلوي
        const badge = document.getElementById('proctoringBadge');
        const text = document.getElementById('proctoringText');
        if (badge && text) {
            badge.classList.add('warning');
            text.textContent = `تنبيه رصد (${tabSwitches + screenshots})`;
        }

        // إشعار الخادم فورياً في الخلفية بدون تعطيل الطالب
        try {
            axios.post("{{ route('student.exams.cheatingIncident', $exam->id) }}", {
                violation_type: type,
                details: details
            }).catch(() => {});
        } catch (e) {}

        // إشعار علوي هادئ وغير معطّل لتدفق الاختبار (Non-blocking discreet toast)
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            title: `تم رصد نشاط خارج صفحة الاختبار (${details}) وتوثيقه للمعلم.`,
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true
        });
    }

    // 1. رصد مغادرة التبويب (Visibility Change)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden' && isExamRunning && !isSubmittingExam) {
            recordCheatingIncident('tab_switch', 'مغادرة تبويب الاختبار أو تصغير المتصفح');
        }
    });

    // 2. رصد فقدان تركيز النافذة (Window Blur) - مع تجاهل نافذة تكبير الصور
    window.addEventListener('blur', function() {
        const modal = document.getElementById('imageModal');
        const isZoomOpen = modal && modal.style.display === 'flex';
        if (isExamRunning && !isSubmittingExam && !isZoomOpen) {
            recordCheatingIncident('tab_switch', 'الخروج من نافذة الاختبار إلى تطبيق آخر');
        }
    });

    // 3. اعتراض أزرار واختصارات لقطات الشاشة والطباعة
    window.addEventListener('keydown', function(e) {
        if (!isExamRunning || isSubmittingExam) return;

        if (e.key === 'PrintScreen' || e.code === 'PrintScreen') {
            try { navigator.clipboard?.writeText(''); } catch(ex){}
            recordCheatingIncident('screenshot', 'الضغط على زر تصوير الشاشة (PrintScreen)');
        } else if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
            recordCheatingIncident('screenshot', 'محاولة استخدام أداة قص الشاشة (Win+Shift+S)');
        } else if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 'P')) {
            e.preventDefault();
            recordCheatingIncident('screenshot', 'محاولة طباعة أو تصدير صفحة الاختبار PDF');
        } else if (e.metaKey && e.shiftKey && ['3', '4', '5'].includes(e.key)) {
            e.preventDefault();
            recordCheatingIncident('screenshot', 'محاولة تصوير الشاشة في نظام Mac');
        }
    });

    // 4. منع النسخ والقائمة المنسدلة
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

    // إدارة الإجابات والتقدم
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
        const navBtn = document.getElementById('nav_btn_' + index);
        const btn = navBtn ? navBtn : null;

        icon.classList.toggle('far');
        icon.classList.toggle('fas');
        if (btn) btn.classList.toggle('flagged');
    }

    function updateFileName(input, qId, index) {
        if (input.files && input.files[0]) {
            document.getElementById('file_name_' + qId).innerHTML = "<i class='fas fa-check-circle text-emerald'></i> " + input.files[0].name;
            markAsAnswered(index, qId);
        }
    }

    function openImageModal(src) {
        document.getElementById('modalImageTarget').src = src;
        document.getElementById('imageModal').style.display = 'flex';
    }

    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }

    function toggleMobileNavDrawer() {
        const sidebar = document.getElementById('examNavSidebar');
        if (sidebar) {
            sidebar.classList.toggle('mobile-open');
        }
    }

    function confirmSubmission() {
        Swal.fire({
            title: 'هل ترغب في تسليم الاختبار الآن؟',
            text: `لقد قمت بالإجابة على ${answeredSet.size} من أصل ${totalQuestions} سؤال.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1d4ed8',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، قم بالتسليم النهائي',
            cancelButtonText: 'متابعة المراجعة'
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

        btn.disabled = true;
        btn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> جاري حفظ وإرسال الإجابات...";

        axios.post("{{ route('student.exams.submit', $exam->id) }}", formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(res => {
            if (res.data.success) {
                sessionStorage.removeItem('exam_start_time_{{ $exam->id }}');
                Swal.fire({
                    title: 'تم التسليم بنجاح!',
                    text: res.data.message || 'تم توثيق إجاباتك بنجاح.',
                    icon: 'success',
                    confirmButtonColor: '#059669',
                    confirmButtonText: 'عرض سجل الاختبارات'
                }).then(() => {
                    window.location.href = res.data.redirect || "{{ route('student.exams.index') }}";
                });
            } else {
                Swal.fire('تنبيه', res.data.message || 'حدث خطأ أثناء المعالجة.', 'warning');
                btn.disabled = false;
                btn.innerHTML = "<i class='fas fa-paper-plane'></i> إعادة محاولة التسليم";
                isSubmittingExam = false;
            }
        })
        .catch(err => {
            let errorMsg = 'حدثت مشكلة في الاتصال أثناء إرسال الإجابات، يرجى المحاولة ثانية.';
            if (err.response && err.response.data && err.response.data.message) {
                errorMsg = err.response.data.message;
            }
            Swal.fire('خطأ في الاتصال', errorMsg, 'error');
            btn.disabled = false;
            btn.innerHTML = "<i class='fas fa-paper-plane'></i> إعادة محاولة التسليم";
            isSubmittingExam = false;
        });
    }

    function autoSubmitExam() {
        Swal.fire({
            title: 'انتهى الوقت المحدد للاختبار!',
            text: 'جاري تسليم وحفظ إجاباتك تلقائياً الآن...',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2500
        }).then(() => finalizeExamSubmission());
    }
</script>
@endsection