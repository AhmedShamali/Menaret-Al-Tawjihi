@extends('layouts.app')

@section('title', __('سجل الإنجاز والشهادات الأكاديمية') . ' | ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
<div class="achievements-page-wrapper">

    <!-- 1. ترويسة سجل الإنجاز الأكاديمي -->
    <div class="calm-achievements-hero">
        <div class="hero-left-col">
            <div class="hero-icon-box">
                <i class="fa-solid fa-award"></i>
            </div>
            <div>
                <span class="hero-subtag">{{ __('سجل التفوق والاعتماد الأكاديمي') }}</span>
                <h1 class="hero-main-title">{{ __('الأوسمة والشهادات الأكاديمية') }} 🎓</h1>
                <p class="hero-subtext">
                    {{ __('مبارك جهودك ومثابرتك يا') }} <strong>{{ (app()->getLocale() === 'en' && !empty($student->name_en)) ? $student->name_en : ($student->name_ar ?? $student->name ?? __('طالبنا المتميز')) }}</strong>. {{ __('كل إنجاز تحققه يقربك خطوة نحو طموحك الوزاري.') }}
                </p>
            </div>
        </div>

        <div class="hero-stats-pills">
            <div class="stat-pill-box">
                <span class="pill-num">{{ $isYearEndPublished ? $certificates->count() : '🔒' }}</span>
                <span class="pill-lbl">{{ $isYearEndPublished ? __('شهادات معتمدة') : __('الشهادات (نهاية العام)') }}</span>
            </div>
            <div class="stat-pill-box">
                <span class="pill-num">{{ $completedExamsCount }}</span>
                <span class="pill-lbl">{{ __('اختبارات مكتملة') }}</span>
            </div>
            <div class="stat-pill-box">
                <span class="pill-num">{{ $student->streak_count ?? 1 }} 🔥</span>
                <span class="pill-lbl">{{ __('أيام الالتزام') }}</span>
            </div>
        </div>
    </div>

    <!-- 2. قسم الشهادات المعتمدة ونتائج نهاية العام -->
    <div class="ed-card mb-4" style="margin-bottom: 28px;">
        <div class="ed-card-header">
            <h3>
                <i class="fa-solid fa-graduation-cap" style="color: var(--ed-primary);"></i>
                <span>{{ __('الشهادات الأكاديمية المعتمدة ونتائج التخرج') }}</span>
            </h3>
            <span class="ed-badge {{ $isYearEndPublished ? 'ed-badge-green' : 'ed-badge-amber' }}">
                @if($isYearEndPublished)
                    {{ $certificates->count() }} {{ __('شهادة معتمدة') }}
                @else
                    {{ __('محجوبة حتى نهاية العام') }} 🔒
                @endif
            </span>
        </div>

        <div class="ed-card-body">
            @if(!$isYearEndPublished)
                <!-- حالة الحجب الأكاديمي الرسمي بقرار الإدارة -->
                <div class="academic-wait-box">
                    <div class="wait-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <span class="ed-badge ed-badge-blue" style="margin-bottom: 12px;">
                        <i class="fa-solid fa-lock"></i> {{ __('نظام الاعتماد والشهادات المدرسية الرسمي') }}
                    </span>
                    <h2 class="wait-title">{{ __('الشهادات والنتائج النهائية تُعتمد وتُعلن في ختام العام الدراسي') }} 🎓</h2>
                    <p class="wait-desc">
                        {{ __('تنفيذاً للضوابط والمعايير المدرسية والأكاديمية المعتمدة، تخضع نتائجك وسجلك الدراسي للمتابعة والتقييم المستمر. تصدر وتُعتمد الشهادات الرسمية من قبل إدارة المنصة والمشرف العام في نهاية العام الدراسي بعد استكمال متطلبات المنهاج والتقييمات الأكاديمية المعتمدة.') }}
                    </p>
                    <div class="wait-details-pills">
                        <span><i class="fa-regular fa-clock" style="color: var(--ed-warning);"></i> {{ __('حالة التقييم:') }} <strong style="color: var(--ed-warning);">{{ __('قيد المتابعة والتدقيق الأكاديمي') }}</strong></span>
                        <span>•</span>
                        <span><i class="fa-regular fa-calendar-check" style="color: var(--ed-success);"></i> {{ __('موعد الإعلان:') }} <strong style="color: var(--ed-success);">{{ __('نهاية العام الدراسي') }}</strong></span>
                    </div>
                </div>
            @elseif($certificates->count() > 0)
                <!-- عرض الشهادات المعتمدة الحقيقية بعد إعلان الإدارة -->
                <div class="certificates-clean-grid">
                    @foreach($certificates as $cert)
                        <div class="cert-item-card">
                            <div class="cert-header-status">
                                <span class="ed-badge ed-badge-green"><i class="fa-solid fa-circle-check"></i> {{ __('معتمد رسميّاً') }}</span>
                                <span style="font-size: 0.74rem; color: var(--ed-text-dim);">{{ __('عام') }} {{ $cert->created_at ? $cert->created_at->format('Y') : date('Y') }}</span>
                            </div>

                            <div class="cert-icon-center">🎓</div>
                            <h4 class="cert-subject-name">{{ (app()->getLocale() === 'en' && !empty($cert->subject->name_en)) ? $cert->subject->name_en : ($cert->subject->name_ar ?? $cert->subject->name ?? __('شهادة إتمام وتفوق عامة')) }}</h4>
                            <p class="cert-issued-to">{{ __('صادرة للطالبـ/ـة:') }} <strong>{{ (app()->getLocale() === 'en' && !empty($cert->student->name_en)) ? $cert->student->name_en : ($cert->student->name_ar ?? $cert->student->name ?? ($student->name_ar ?? $student->name)) }}</strong></p>

                            <div class="cert-grade-box">
                                {{ __('المعدل المعتمد:') }} <strong>{{ $cert->final_grade }}%</strong>
                            </div>

                            <div class="cert-code-tag">
                                <small>{{ __('رمز الوثيقة:') }}</small>
                                <code>{{ $cert->certificate_code }}</code>
                            </div>

                            <div class="cert-actions-row">
                                <a href="{{ route('student.certificates.show', $cert->id) }}" class="ed-btn ed-btn-primary" style="flex: 1; font-size: 0.82rem;" target="_blank">
                                    <i class="fa-solid fa-eye"></i> {{ __('استعراض وطباعة') }}
                                </a>
                                <a href="{{ route('certificates.verify', $cert->certificate_code) }}" class="ed-btn ed-btn-outline" style="padding: 8px 12px;" target="_blank" title="{{ __('التحقق من صحة الوثيقة') }}">
                                    <i class="fa-solid fa-qrcode"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-certs-box">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <h4>{{ __('تم إعلان نتائج العام الدراسي') }}</h4>
                    <p>{{ __('شهادتك قيد التدقيق الإداري وسيتم اعتمادها لحسابك قريباً.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- 3. غرفة تنظيم المذاكرة والتركيز الذهني (Focus Room / Pomodoro) -->
    <div class="ed-card" id="focusRoomCard">
        <div class="ed-card-header">
            <div>
                <h3>
                    <i class="fa-solid fa-brain" style="color: var(--ed-primary);"></i>
                    <span>{{ __('غرفة تنظيم المذاكرة والتركيز الذهني') }} ⏳</span>
                </h3>
                <span style="font-size: 0.8rem; color: var(--ed-text-muted); margin-top: 2px; display: block;">
                    {{ __('حدد مادتك، اكتب هدفك المباشر، واضبط وقتك لرفع مستوى الإنتاجية والاستيعاب.') }}
                </span>
            </div>
            <button type="button" class="ed-btn ed-btn-outline" onclick="toggleZenMode()" style="font-size: 0.78rem; padding: 6px 12px;">
                <i class="fa-solid fa-expand" id="zenIcon"></i> <span id="zenText">{{ __('وضع ملء الشاشة') }}</span>
            </button>
        </div>

        <div class="ed-card-body">
            <div class="focus-layout-grid">
                <!-- الجانب الأيمن: أدوات تنظيم الدراسة (المادة، الهدف، المهام) -->
                <div class="focus-col-planner">
                    
                    <div class="planner-card-sub">
                        <label class="form-label-clean"><i class="fa-solid fa-book-bookmark" style="color: var(--ed-primary);"></i> {{ __('المادة أو المساق الدراسي:') }}</label>
                        <select id="studySubjectSelect" class="ed-select mb-2" onchange="onSubjectChange()">
                            <option value="{{ __('مراجعة عامة') }}">📚 {{ __('مراجعة عامة / دراسة حرة') }}</option>
                            @if(isset($subjects) && $subjects->count() > 0)
                                @foreach($subjects as $sub)
                                    <option value="{{ (app()->getLocale() === 'en' && !empty($sub->name_en)) ? $sub->name_en : __($sub->name_ar) }}">
                                        {{ (app()->getLocale() === 'en' && !empty($sub->name_en)) ? $sub->name_en : __($sub->name_ar) }}
                                    </option>
                                @endforeach
                            @endif
                            <option value="custom">✏️ {{ __('مادة أخرى (اكتبها بنفسك)...') }}</option>
                        </select>
                        <input type="text" id="customSubjectInput" class="ed-input d-none mt-2" placeholder="{{ __('اكتب اسم المادة...') }}" oninput="onCustomSubjectInput()">
                    </div>

                    <div class="planner-card-sub">
                        <label class="form-label-clean"><i class="fa-solid fa-bullseye" style="color: var(--ed-danger);"></i> {{ __('هدف الجلسة الحالي (ماذا تريد أن تنجز؟):') }}</label>
                        <input type="text" id="sessionGoalInput" class="ed-input" placeholder="{{ __('مثال: حل تمارين الكتاب المدرسي، مراجعة الدرس الأول...') }}" oninput="updateActiveGoalDisplay()">
                        
                        <div class="quick-tags-wrap">
                            <span class="tag-chip" onclick="setQuickGoal(@json(__('حل تمارين وأسئلة الكتاب')))">✍️ {{ __('تمارين الكتاب') }}</span>
                            <span class="tag-chip" onclick="setQuickGoal(@json(__('حفظ ومراجعة درس ومفاهيم')))">📖 {{ __('حفظ ومراجعة') }}</span>
                            <span class="tag-chip" onclick="setQuickGoal(@json(__('تلخيص وكتابة ملاحظات هامة')))">📝 {{ __('تلخيص شامل') }}</span>
                            <span class="tag-chip" onclick="setQuickGoal(@json(__('مراجعة المفاهيم والقوانين')))">🎯 {{ __('مراجعة المفاهيم') }}</span>
                        </div>
                    </div>

                    <!-- قائمة مهام الجلسة (Checklist) -->
                    <div class="planner-card-sub">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <label class="form-label-clean mb-0"><i class="fa-solid fa-list-check" style="color: var(--ed-success);"></i> {{ __('قائمة مهام الجلسة:') }}</label>
                            <span class="ed-badge ed-badge-slate" id="tasksCounterBadge">0 / 0 {{ __('مكتمل') }}</span>
                        </div>

                        <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                            <input type="text" id="newTaskInput" class="ed-input" placeholder="{{ __('اكتب مهمة سريعة واضغط Enter...') }}" onkeydown="handleTaskInputKey(event)">
                            <button type="button" class="ed-btn ed-btn-primary" onclick="addNewTask()" style="padding: 0 16px;">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>

                        <div class="tasks-checklist-list" id="tasksChecklist"></div>

                        <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 0.78rem;">
                            <button type="button" onclick="clearCompletedTasks()" style="background: none; border: none; color: var(--ed-text-muted); cursor: pointer;">
                                <i class="fa-solid fa-broom"></i> {{ __('تنظيف المكتمل') }}
                            </button>
                            <button type="button" onclick="clearAllTasks()" style="background: none; border: none; color: var(--ed-danger); cursor: pointer;">
                                <i class="fa-solid fa-trash-can"></i> {{ __('مسح الكل') }}
                            </button>
                        </div>
                    </div>

                    <!-- إحصائيات الجلسات لليوم -->
                    <div class="today-stats-row">
                        <div class="stat-mini-box">
                            <i class="fa-regular fa-clock" style="color: var(--ed-primary);"></i>
                            <div>
                                <strong id="statTodayMinutes">0</strong> {{ __('دقيقة') }}
                                <small>{{ __('دراسة اليوم') }}</small>
                            </div>
                        </div>
                        <div class="stat-mini-box">
                            <i class="fa-regular fa-circle-check" style="color: var(--ed-success);"></i>
                            <div>
                                <strong id="statTodaySessions">0</strong> {{ __('جلسات') }}
                                <small>{{ __('مكتملة') }}</small>
                            </div>
                        </div>
                        <div class="stat-mini-box">
                            <i class="fa-solid fa-fire" style="color: #ea580c;"></i>
                            <div>
                                <strong id="statStreakDays">{{ $student->streak_count ?? 1 }}</strong> {{ __('أيام') }}
                                <small>{{ __('التزام متتالي') }}</small>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- الجانب الأيسر: المؤقت الدائري والتحكم بالوقت -->
                <div class="focus-col-timer">
                    <!-- لافتة الهدف النشط -->
                    <div class="active-goal-strip">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span id="bannerSubjectText" style="font-weight: 700; color: var(--ed-primary); font-size: 0.88rem;">
                                <i class="fa-solid fa-book"></i> {{ __('مراجعة عامة') }}
                            </span>
                            <span class="ed-badge ed-badge-blue" id="bannerStatusTag" style="font-size: 0.7rem;">{{ __('جاهز للبدء') }}</span>
                        </div>
                        <div id="bannerGoalText" style="font-size: 0.84rem; color: var(--ed-text-body); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ __('حدد هدفك واضغط بدء الجلسة للتركيز!') }}
                        </div>
                        <div style="background: var(--ed-border); height: 4px; border-radius: 4px; margin-top: 8px; overflow: hidden;">
                            <div id="sessionProgressBar" style="background: var(--ed-primary); height: 100%; width: 0%; transition: width 0.3s ease;"></div>
                        </div>
                    </div>

                    <!-- المؤقت الدائري الأنيق -->
                    <div class="timer-dial-center">
                        <div class="timer-svg-wrap">
                            <svg width="210" height="210" viewBox="0 0 220 220">
                                <circle cx="110" cy="110" r="92" stroke="var(--ed-border)" stroke-width="10" fill="none"></circle>
                                <circle id="timerProgressCircle" cx="110" cy="110" r="92" stroke="var(--ed-primary)" stroke-width="10" fill="none" stroke-dasharray="578" stroke-dashoffset="0" stroke-linecap="round" style="transform: rotate(-90deg); transform-origin: 50% 50%; transition: stroke-dashoffset 0.5s ease;"></circle>
                            </svg>
                            <div class="timer-digits-box">
                                <span class="timer-time-digits" id="timerDigits">25:00</span>
                                <span class="timer-status-hint" id="timerStatusLabel">{{ __('جاهز للبدء') }}</span>
                                <span class="timer-pct-hint" id="timerPercentBadge">100% {{ __('متبقي') }}</span>
                            </div>
                        </div>

                        <!-- أزرار التعديل السريع (+5د، -5د، +15د) -->
                        <div style="display: flex; justify-content: center; gap: 8px; margin-top: 14px;">
                            <button type="button" class="btn-time-chip" onclick="adjustTimerSeconds(-300)">-5 {{ __('دقائق') }}</button>
                            <button type="button" class="btn-time-chip" onclick="adjustTimerSeconds(300)">+5 {{ __('دقائق') }}</button>
                            <button type="button" class="btn-time-chip" onclick="adjustTimerSeconds(900)">+15 {{ __('دقيقة') }}</button>
                        </div>
                    </div>

                    <!-- أزرار التحكم بالمؤقت -->
                    <div class="timer-main-actions">
                        <button type="button" class="ed-btn ed-btn-primary" id="btnStartTimer" onclick="startFocusTimer()" style="flex: 1.5; padding: 12px 20px; font-size: 0.95rem;">
                            <i class="fa-solid fa-play"></i> {{ __('بدء الجلسة') }}
                        </button>
                        <button type="button" class="ed-btn ed-btn-outline" onclick="resetFocusTimer()" style="flex: 1; padding: 12px;" title="{{ __('إعادة ضبط') }}">
                            <i class="fa-solid fa-rotate-right"></i> {{ __('ضبط') }}
                        </button>
                        <button type="button" class="ed-btn ed-btn-outline" onclick="finishSessionEarly()" style="flex: 1; padding: 12px; color: var(--ed-success); border-color: #86efac;" title="{{ __('إنهاء وحفظ') }}">
                            <i class="fa-solid fa-check"></i> {{ __('إنهاء') }}
                        </button>
                    </div>

                    <!-- الأنماط المسبقة للمدة -->
                    <div class="timer-presets-wrap">
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--ed-text-muted); display: block; margin-bottom: 6px;">{{ __('اختر مدة الجلسة:') }}</span>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                            <button type="button" class="btn-preset-chip active" onclick="setTimerPreset(25, 'study', this)">25 {{ __('د (بومودورو)') }}</button>
                            <button type="button" class="btn-preset-chip" onclick="setTimerPreset(45, 'study', this)">45 {{ __('د (حل مسائل)') }}</button>
                            <button type="button" class="btn-preset-chip" onclick="setTimerPreset(60, 'study', this)">60 {{ __('د (مكثفة)') }}</button>
                            <button type="button" class="btn-preset-chip break" onclick="setTimerPreset(5, 'break', this)">5 {{ __('د (استراحة)') }}</button>
                            <button type="button" class="btn-preset-chip break" onclick="setTimerPreset(15, 'break', this)">15 {{ __('د (طويلة)') }}</button>
                        </div>
                    </div>

                    <!-- أصوات التركيز -->
                    <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--ed-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                        <span style="font-size: 0.78rem; color: var(--ed-text-muted); font-weight: 600;">
                            <i class="fa-solid fa-headphones"></i> {{ __('الأجواء الصوتية المركّزة:') }}
                        </span>
                        <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                            <button type="button" class="btn-sound-chip active" id="btnSoundNone" onclick="setSoundMode('none')">{{ __('صامت') }}</button>
                            <button type="button" class="btn-sound-chip" id="btnSoundTick" onclick="setSoundMode('tick')">{{ __('بندول (دقات)') }}</button>
                            <button type="button" class="btn-sound-chip" id="btnSoundRain" onclick="setSoundMode('rain')">{{ __('صوت مطر') }}</button>
                            <button type="button" class="btn-sound-chip" id="btnSoundNoise" onclick="setSoundMode('noise')">{{ __('ضوضاء بيضاء') }}</button>
                            <div style="display: inline-flex; align-items: center; gap: 4px; margin-inline-start: 6px; background: rgba(0,0,0,0.03); padding: 3px 8px; border-radius: 8px;" title="{{ __('مستوى الصوت') }}">
                                <i class="fa-solid fa-volume-high" style="font-size: 0.75rem; color: var(--ed-text-muted);"></i>
                                <input type="range" min="0" max="1" step="0.05" value="0.5" oninput="setAudioVolume(this.value)" style="width: 60px; height: 4px; accent-color: #1d4ed8; cursor: pointer;">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .achievements-page-wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* ترويسة أكاديمية فاتحة وأنيقة */
    .calm-achievements-hero {
        background: #ffffff;
        border: 1px solid var(--ed-border, #e2e8f0);
        border-radius: var(--ed-radius-lg, 12px);
        padding: 22px 28px;
        color: var(--ed-text-main, #0f172a);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: var(--ed-shadow-card, 0 1px 3px rgba(0,0,0,0.05));
    }

    .hero-left-col {
        display: flex;
        align-items: center;
        gap: 16px;
        max-width: 600px;
    }

    .hero-icon-box {
        width: 52px;
        height: 52px;
        border-radius: var(--ed-radius-md, 8px);
        background: #eff6ff;
        color: var(--ed-primary, #1d4ed8);
        border: 1px solid #bfdbfe;
        display: grid;
        place-items: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .hero-subtag {
        font-size: 0.76rem;
        font-weight: 700;
        color: var(--ed-primary, #1d4ed8);
        display: block;
        margin-bottom: 2px;
    }

    .hero-main-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--ed-text-main, #0f172a);
        margin: 0 0 4px;
    }

    .hero-subtext {
        font-size: 0.86rem;
        color: var(--ed-text-muted, #64748b);
        margin: 0;
        line-height: 1.5;
    }

    .hero-stats-pills {
        display: flex;
        gap: 12px;
    }

    .stat-pill-box {
        background: #f8fafc;
        border: 1px solid var(--ed-border, #e2e8f0);
        padding: 8px 16px;
        border-radius: var(--ed-radius-md, 8px);
        text-align: center;
        min-width: 105px;
    }

    .pill-num {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--ed-primary, #1d4ed8);
        display: block;
        line-height: 1.2;
    }

    .pill-lbl {
        font-size: 0.72rem;
        color: var(--ed-text-muted, #64748b);
        font-weight: 600;
    }

    /* حالة الانتظار الأكاديمية */
    .academic-wait-box {
        text-align: center;
        padding: 36px 20px;
        background: var(--ed-surface-alt, #f8fafc);
        border: 2px dashed var(--ed-border, #e2e8f0);
        border-radius: var(--ed-radius-lg, 12px);
    }

    .wait-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 14px;
        border-radius: 50%;
        background: #fef3c7;
        color: #d97706;
        display: grid;
        place-items: center;
        font-size: 1.8rem;
    }

    .wait-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--ed-text-main, #0f172a);
        margin-bottom: 8px;
    }

    .wait-desc {
        font-size: 0.9rem;
        color: var(--ed-text-muted, #64748b);
        max-width: 640px;
        margin: 0 auto 20px;
        line-height: 1.7;
    }

    .wait-details-pills {
        display: inline-flex;
        gap: 12px;
        background: #ffffff;
        border: 1px solid var(--ed-border, #e2e8f0);
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 600;
    }

    /* شبكة الشهادات المعتمدة */
    .certificates-clean-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 18px;
    }

    .cert-item-card {
        background: #ffffff;
        border: 1px solid var(--ed-border, #e2e8f0);
        border-radius: var(--ed-radius-md, 8px);
        padding: 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 12px;
        box-shadow: var(--ed-shadow-card, 0 1px 3px rgba(0,0,0,0.05));
        transition: all 0.2s ease;
    }

    .cert-item-card:hover {
        border-color: #93c5fd;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .cert-header-status {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cert-icon-center {
        font-size: 2.2rem;
        margin: 4px 0;
    }

    .cert-subject-name {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--ed-text-main, #0f172a);
        margin: 0;
    }

    .cert-issued-to {
        font-size: 0.82rem;
        color: var(--ed-text-muted, #64748b);
        margin: 0;
    }

    .cert-grade-box {
        background: #eff6ff;
        color: var(--ed-primary, #1d4ed8);
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.88rem;
    }

    .cert-code-tag {
        font-size: 0.74rem;
        color: var(--ed-text-muted, #64748b);
    }
    .cert-code-tag code {
        font-family: monospace;
        font-weight: 700;
        color: var(--ed-text-main, #0f172a);
    }

    .cert-actions-row {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-top: 4px;
    }

    .empty-certs-box {
        text-align: center;
        padding: 40px 16px;
        color: var(--ed-text-muted, #64748b);
    }
    .empty-certs-box i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 10px;
        display: block;
    }

    /* تخطيط غرفة التركيز */
    .focus-layout-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .planner-card-sub {
        background: var(--ed-surface-alt, #f8fafc);
        border: 1px solid var(--ed-border, #e2e8f0);
        border-radius: var(--ed-radius-md, 8px);
        padding: 14px 16px;
        margin-bottom: 14px;
    }

    .form-label-clean {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--ed-text-main, #0f172a);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .quick-tags-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }

    .tag-chip {
        background: #ffffff;
        border: 1px solid var(--ed-border, #e2e8f0);
        padding: 3px 9px;
        border-radius: 6px;
        font-size: 0.74rem;
        color: var(--ed-text-muted, #64748b);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .tag-chip:hover {
        color: var(--ed-primary, #1d4ed8);
        border-color: #93c5fd;
    }

    .tasks-checklist-list {
        max-height: 140px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .today-stats-row {
        display: flex;
        gap: 8px;
    }

    .stat-mini-box {
        flex: 1;
        background: #ffffff;
        border: 1px solid var(--ed-border, #e2e8f0);
        border-radius: 6px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.82rem;
    }
    .stat-mini-box strong {
        display: block;
        font-size: 1.05rem;
        color: var(--ed-text-main, #0f172a);
        line-height: 1.1;
    }
    .stat-mini-box small {
        font-size: 0.68rem;
        color: var(--ed-text-muted, #64748b);
    }

    /* عمود المؤقت */
    .focus-col-timer {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: var(--ed-surface-alt, #f8fafc);
        border: 1px solid var(--ed-border, #e2e8f0);
        border-radius: var(--ed-radius-md, 8px);
        padding: 18px 20px;
    }

    .active-goal-strip {
        background: #ffffff;
        border: 1px solid var(--ed-border, #e2e8f0);
        border-radius: 6px;
        padding: 10px 14px;
        margin-bottom: 12px;
    }

    .timer-dial-center {
        text-align: center;
        padding: 8px 0;
    }

    .timer-svg-wrap {
        position: relative;
        width: 210px;
        height: 210px;
        margin: 0 auto;
    }

    .timer-digits-box {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .timer-time-digits {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--ed-text-main, #0f172a);
        font-family: monospace;
        letter-spacing: -1px;
    }

    .timer-status-hint {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--ed-primary, #1d4ed8);
        margin-top: 2px;
    }

    .timer-pct-hint {
        font-size: 0.68rem;
        color: var(--ed-text-muted, #64748b);
    }

    .btn-time-chip {
        background: #ffffff;
        border: 1px solid var(--ed-border, #e2e8f0);
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 0.74rem;
        color: var(--ed-text-muted, #64748b);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-time-chip:hover {
        color: var(--ed-primary, #1d4ed8);
        border-color: #93c5fd;
    }

    .timer-main-actions {
        display: flex;
        gap: 8px;
        margin: 14px 0 10px;
    }

    .btn-preset-chip {
        background: #ffffff;
        border: 1px solid var(--ed-border, #e2e8f0);
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--ed-text-muted, #64748b);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-preset-chip:hover, .btn-preset-chip.active {
        background: var(--ed-primary, #1d4ed8);
        color: #ffffff;
        border-color: var(--ed-primary, #1d4ed8);
    }
    .btn-preset-chip.break.active {
        background: #059669;
        border-color: #059669;
    }

    .btn-sound-chip {
        background: #ffffff;
        border: 1px solid var(--ed-border, #e2e8f0);
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 0.72rem;
        color: var(--ed-text-muted, #64748b);
        cursor: pointer;
    }
    .btn-sound-chip.active {
        background: var(--ed-primary, #1d4ed8);
        color: white;
        border-color: var(--ed-primary, #1d4ed8);
    }

    @media (max-width: 900px) {
        .focus-layout-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    const achievementsI18n = {
        customSubject: @json(__('مادة مخصصة')),
        generalReview: @json(__('مراجعة عامة')),
        goalPrefix: @json(__('الهدف: ')),
        noGoalPrompt: @json(__('حدد هدفك واضغط بدء الجلسة للتركيز!')),
        noTasks: @json(__('لا توجد مهام محددة للجلسة بعد.')),
        completed: @json(__('مكتمل')),
        remaining: @json(__('متبقي')),
        pause: @json(__('إيقاف مؤقت')),
        resume: @json(__('استئناف الجلسة')),
        start: @json(__('بدء الجلسة')),
        studySessionActive: @json(__('جلسة مذاكرة جارية 📚')),
        breakActive: @json(__('استراحة مستحقة ☕')),
        sessionActive: @json(__('الجلسة نشطة')),
        paused: @json(__('موقوف مؤقتاً')),
        ready: @json(__('جاهز للبدء')),
        readyStudy: @json(__('جاهز للمذاكرة')),
        readyBreak: @json(__('جاهز للاستراحة')),
        sessionNotStarted: @json(__('لم تبدأ الجلسة بعد')),
        startFirst: @json(__('اضغط على بدء الجلسة أولاً لتسجيل إنجازك.')),
        confirmFinishTitle: @json(__('هل تريد إنهاء الجلسة وتسجيل الإنجاز؟')),
        confirmFinishText: @json(__('سيتم حفظ الدقائق التي درستها وحسابها ضمن إحصائيات التزامك اليومي.')),
        confirmFinishBtn: @json(__('نعم، حفظ الإنجاز')),
        cancelFinishBtn: @json(__('متابعة الجلسة')),
        sessionDoneTitle: @json(__('اكتملت الجلسة! 🎉')),
        sessionDoneTag: @json(__('تم الإنجاز بنجاح')),
        congratsTitle: @json(__('مبارك إنجازك يا بطل! 👏')),
        continueBtn: @json(__('متابعة')),
        savedLocallyTitle: @json(__('تم تسجيل الجلسة محلياً! 👏')),
        savedLocallyText: @json(__('أنجزت :mins دقيقة دراسة بنجاح. استمر يا بطل!')),
        excellentBtn: @json(__('ممتاز')),
        enterZen: @json(__('وضع ملء الشاشة')),
        exitZen: @json(__('إنهاء ملء الشاشة'))
    };

    let timerDurationMinutes = 25;
    let totalSeconds = 25 * 60;
    let remainingSeconds = totalSeconds;
    let timerInterval = null;
    let isRunning = false;
    let currentSessionType = 'study';
    let currentSoundMode = 'none';

    const CIRCLE_CIRCUMFERENCE = 578;

    document.addEventListener('DOMContentLoaded', () => {
        loadSavedStudyData();
        renderTasks();
        updateDailyStatsDisplay();
        updateTimerDisplay();
    });

    function onSubjectChange() {
        const select = document.getElementById('studySubjectSelect');
        const customInput = document.getElementById('customSubjectInput');
        if (select.value === 'custom') {
            customInput.classList.remove('d-none');
            customInput.focus();
        } else {
            customInput.classList.add('d-none');
        }
        updateActiveGoalDisplay();
        saveStudyData();
    }

    function onCustomSubjectInput() {
        updateActiveGoalDisplay();
        saveStudyData();
    }

    function getActiveSubjectName() {
        const select = document.getElementById('studySubjectSelect');
        if (select && select.value === 'custom') {
            return document.getElementById('customSubjectInput').value.trim() || achievementsI18n.customSubject;
        }
        return select ? select.value : achievementsI18n.generalReview;
    }

    function getActiveGoalText() {
        return document.getElementById('sessionGoalInput').value.trim();
    }

    function setQuickGoal(goalText) {
        document.getElementById('sessionGoalInput').value = goalText;
        updateActiveGoalDisplay();
        saveStudyData();
    }

    function updateActiveGoalDisplay() {
        const subject = getActiveSubjectName();
        const goal = getActiveGoalText();
        document.getElementById('bannerSubjectText').innerHTML = `<i class="fa-solid fa-book"></i> ${subject}`;
        document.getElementById('bannerGoalText').innerText = goal ? `${achievementsI18n.goalPrefix}${goal}` : achievementsI18n.noGoalPrompt;
    }

    function getTasks() {
        try {
            return JSON.parse(localStorage.getItem('tawjihi_study_tasks') || '[]');
        } catch (e) {
            return [];
        }
    }

    function saveTasks(tasks) {
        localStorage.setItem('tawjihi_study_tasks', JSON.stringify(tasks));
        renderTasks();
    }

    function handleTaskInputKey(e) {
        if (e.key === 'Enter') {
            addNewTask();
        }
    }

    function addNewTask() {
        const input = document.getElementById('newTaskInput');
        const text = input.value.trim();
        if (!text) return;

        const tasks = getTasks();
        tasks.push({ id: Date.now(), text: text, completed: false });
        saveTasks(tasks);
        input.value = '';
    }

    function toggleTask(id) {
        const tasks = getTasks().map(t => {
            if (t.id === id) t.completed = !t.completed;
            return t;
        });
        saveTasks(tasks);
    }

    function deleteTask(id) {
        const tasks = getTasks().filter(t => t.id !== id);
        saveTasks(tasks);
    }

    function clearCompletedTasks() {
        const tasks = getTasks().filter(t => !t.completed);
        saveTasks(tasks);
    }

    function clearAllTasks() {
        saveTasks([]);
    }

    function renderTasks() {
        const tasks = getTasks();
        const listEl = document.getElementById('tasksChecklist');
        const counterEl = document.getElementById('tasksCounterBadge');

        const completedCount = tasks.filter(t => t.completed).length;
        if (counterEl) counterEl.innerText = `${completedCount} / ${tasks.length} ${achievementsI18n.completed}`;

        if (!listEl) return;
        if (tasks.length === 0) {
            listEl.innerHTML = `<div style="font-size: 0.78rem; color: var(--ed-text-muted); text-align: center; padding: 12px 0;">${achievementsI18n.noTasks}</div>`;
            return;
        }

        let html = '';
        tasks.forEach(t => {
            html += `
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 6px 10px; background: #ffffff; border: 1px solid var(--ed-border); border-radius: 6px; font-size: 0.8rem;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0; text-decoration: ${t.completed ? 'line-through' : 'none'}; opacity: ${t.completed ? '0.6' : '1'};">
                        <input type="checkbox" ${t.completed ? 'checked' : ''} onchange="toggleTask(${t.id})">
                        <span>${t.text}</span>
                    </label>
                    <button type="button" onclick="deleteTask(${t.id})" style="background: none; border: none; color: var(--ed-danger); cursor: pointer;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            `;
        });
        listEl.innerHTML = html;
    }

    function saveStudyData() {
        const data = {
            subject: document.getElementById('studySubjectSelect').value,
            customSubject: document.getElementById('customSubjectInput').value,
            goal: document.getElementById('sessionGoalInput').value
        };
        localStorage.setItem('tawjihi_active_session_data', JSON.stringify(data));
    }

    function loadSavedStudyData() {
        try {
            const raw = localStorage.getItem('tawjihi_active_session_data');
            if (raw) {
                const data = JSON.parse(raw);
                if (data.subject) document.getElementById('studySubjectSelect').value = data.subject;
                if (data.customSubject) document.getElementById('customSubjectInput').value = data.customSubject;
                if (data.goal) document.getElementById('sessionGoalInput').value = data.goal;
                if (data.subject === 'custom') document.getElementById('customSubjectInput').classList.remove('d-none');
            }
        } catch (e) {}
        updateActiveGoalDisplay();
    }

    function startFocusTimer() {
        if (isRunning) {
            pauseFocusTimer();
            return;
        }

        isRunning = true;
        applySoundPlayback();
        const btn = document.getElementById('btnStartTimer');
        btn.innerHTML = `<i class="fa-solid fa-pause"></i> ${achievementsI18n.pause}`;
        document.getElementById('timerStatusLabel').innerText = currentSessionType === 'study' ? achievementsI18n.studySessionActive : achievementsI18n.breakActive;
        document.getElementById('bannerStatusTag').innerText = achievementsI18n.sessionActive;

        timerInterval = setInterval(() => {
            if (remainingSeconds > 0) {
                remainingSeconds--;
                updateTimerDisplay();
            } else {
                completeSession();
            }
        }, 1000);
    }

    function pauseFocusTimer() {
        clearInterval(timerInterval);
        isRunning = false;
        stopCurrentAudio();
        const btn = document.getElementById('btnStartTimer');
        btn.innerHTML = `<i class="fa-solid fa-play"></i> ${achievementsI18n.resume}`;
        document.getElementById('timerStatusLabel').innerText = achievementsI18n.paused;
        document.getElementById('bannerStatusTag').innerText = achievementsI18n.paused;
    }

    function resetFocusTimer() {
        clearInterval(timerInterval);
        isRunning = false;
        stopCurrentAudio();
        remainingSeconds = totalSeconds;
        const btn = document.getElementById('btnStartTimer');
        btn.innerHTML = `<i class="fa-solid fa-play"></i> ${achievementsI18n.start}`;
        document.getElementById('timerStatusLabel').innerText = achievementsI18n.ready;
        document.getElementById('bannerStatusTag').innerText = achievementsI18n.ready;
        updateTimerDisplay();
    }

    function adjustTimerSeconds(sec) {
        remainingSeconds = Math.max(60, remainingSeconds + sec);
        totalSeconds = Math.max(totalSeconds, remainingSeconds);
        updateTimerDisplay();
    }

    function setTimerPreset(minutes, type, btnEl) {
        clearInterval(timerInterval);
        isRunning = false;
        timerDurationMinutes = minutes;
        totalSeconds = minutes * 60;
        remainingSeconds = totalSeconds;
        currentSessionType = type;

        document.querySelectorAll('.btn-preset-chip').forEach(b => b.classList.remove('active'));
        if (btnEl) btnEl.classList.add('active');

        const btn = document.getElementById('btnStartTimer');
        btn.innerHTML = `<i class="fa-solid fa-play"></i> ${achievementsI18n.start}`;
        document.getElementById('timerStatusLabel').innerText = type === 'study' ? achievementsI18n.readyStudy : achievementsI18n.readyBreak;

        updateTimerDisplay();
    }

    function updateTimerDisplay() {
        const m = Math.floor(remainingSeconds / 60);
        const s = remainingSeconds % 60;
        const formatted = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        document.getElementById('timerDigits').innerText = formatted;

        const progressPercent = totalSeconds > 0 ? ((totalSeconds - remainingSeconds) / totalSeconds) * 100 : 0;
        const circle = document.getElementById('timerProgressCircle');
        if (circle) {
            const offset = CIRCLE_CIRCUMFERENCE - (progressPercent / 100 * CIRCLE_CIRCUMFERENCE);
            circle.style.strokeDashoffset = offset;
        }

        const bar = document.getElementById('sessionProgressBar');
        if (bar) bar.style.width = `${progressPercent}%`;

        const pctBadge = document.getElementById('timerPercentBadge');
        if (pctBadge) pctBadge.innerText = `${Math.round(100 - progressPercent)}% ${achievementsI18n.remaining}`;
    }

    function finishSessionEarly() {
        if (remainingSeconds === totalSeconds) {
            Swal.fire({ icon: 'info', title: achievementsI18n.sessionNotStarted, text: achievementsI18n.startFirst });
            return;
        }

        Swal.fire({
            title: achievementsI18n.confirmFinishTitle,
            text: achievementsI18n.confirmFinishText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: achievementsI18n.confirmFinishBtn,
            cancelButtonText: achievementsI18n.cancelFinishBtn
        }).then((result) => {
            if (result.isConfirmed) {
                const minutesCompleted = Math.max(1, Math.round((totalSeconds - remainingSeconds) / 60));
                saveSessionRecord(minutesCompleted);
            }
        });
    }

    function completeSession() {
        clearInterval(timerInterval);
        isRunning = false;

        const btn = document.getElementById('btnStartTimer');
        btn.innerHTML = `<i class="fa-solid fa-play"></i> ${achievementsI18n.start}`;
        document.getElementById('timerStatusLabel').innerText = achievementsI18n.sessionDoneTitle;
        document.getElementById('bannerStatusTag').innerText = achievementsI18n.sessionDoneTag;

        saveSessionRecord(timerDurationMinutes);
    }

    function saveSessionRecord(minutes) {
        const subject = getActiveSubjectName();
        const goal = getActiveGoalText();
        const tasks = getTasks();
        const completedTasksCount = tasks.filter(t => t.completed).length;

        axios.post('{{ route("student.pomodoro.save") }}', {
            minutes: minutes,
            subject_name: subject,
            task_goal: goal,
            tasks_completed: completedTasksCount,
            _token: '{{ csrf_token() }}'
        }).then(res => {
            recordLocalSessionStats(minutes);
            Swal.fire({
                icon: 'success',
                title: achievementsI18n.congratsTitle,
                text: res.data.message || achievementsI18n.savedLocallyText.replace(':mins', minutes),
                confirmButtonText: achievementsI18n.continueBtn
            });
            resetFocusTimer();
        }).catch(() => {
            recordLocalSessionStats(minutes);
            Swal.fire({
                icon: 'success',
                title: achievementsI18n.savedLocallyTitle,
                text: achievementsI18n.savedLocallyText.replace(':mins', minutes),
                confirmButtonText: achievementsI18n.excellentBtn
            });
            resetFocusTimer();
        });
    }

    function getTodayKey() {
        const d = new Date();
        return `tawjihi_stats_${d.getFullYear()}_${d.getMonth()+1}_${d.getDate()}`;
    }

    function recordLocalSessionStats(mins) {
        const key = getTodayKey();
        let stats = { minutes: 0, sessions: 0 };
        try {
            stats = JSON.parse(localStorage.getItem(key) || JSON.stringify(stats));
        } catch (e) {}

        stats.minutes += mins;
        stats.sessions += 1;
        localStorage.setItem(key, JSON.stringify(stats));
        updateDailyStatsDisplay();
    }

    function updateDailyStatsDisplay() {
        const key = getTodayKey();
        let stats = { minutes: 0, sessions: 0 };
        try {
            stats = JSON.parse(localStorage.getItem(key) || JSON.stringify(stats));
        } catch (e) {}

        const minEl = document.getElementById('statTodayMinutes');
        const sessEl = document.getElementById('statTodaySessions');
        if (minEl) minEl.innerText = stats.minutes;
        if (sessEl) sessEl.innerText = stats.sessions;
    }

    // --- مشغل ومولد الأصوات المحيطية Web Audio API ---
    let audioCtx = null;
    let noiseNode = null;
    let noiseGain = null;
    let metronomeInterval = null;
    let currentVolume = 0.5;

    function initAudioContext() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
    }

    function stopCurrentAudio() {
        if (metronomeInterval) {
            clearInterval(metronomeInterval);
            metronomeInterval = null;
        }
        if (noiseNode) {
            try { noiseNode.stop(); } catch(e){}
            try { noiseNode.disconnect(); } catch(e){}
            noiseNode = null;
        }
        if (noiseGain) {
            try { noiseGain.disconnect(); } catch(e){}
            noiseGain = null;
        }
    }

    function playTickSound() {
        if (!audioCtx || currentSoundMode !== 'tick') return;
        try {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.18 * currentVolume, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.04);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.05);
        } catch (e) {}
    }

    function startRainOrNoiseSound(type) {
        initAudioContext();
        stopCurrentAudio();

        const bufferSize = audioCtx.sampleRate * 2;
        const buffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
        const data = buffer.getChannelData(0);

        let lastOut = 0.0;
        for (let i = 0; i < bufferSize; i++) {
            const white = Math.random() * 2 - 1;
            if (type === 'rain') {
                lastOut = (lastOut * 0.94) + (white * 0.06);
                data[i] = lastOut * 3.5;
            } else {
                lastOut = (lastOut * 0.85) + (white * 0.15);
                data[i] = lastOut * 2.0;
            }
        }

        noiseNode = audioCtx.createBufferSource();
        noiseNode.buffer = buffer;
        noiseNode.loop = true;

        const filter = audioCtx.createBiquadFilter();
        filter.type = 'lowpass';
        filter.frequency.value = (type === 'rain') ? 650 : 1200;

        noiseGain = audioCtx.createGain();
        noiseGain.gain.setValueAtTime(0.15 * currentVolume, audioCtx.currentTime);

        noiseNode.connect(filter);
        filter.connect(noiseGain);
        noiseGain.connect(audioCtx.destination);

        noiseNode.start();
    }

    function setAudioVolume(val) {
        currentVolume = parseFloat(val);
        if (noiseGain && audioCtx) {
            noiseGain.gain.setValueAtTime(0.15 * currentVolume, audioCtx.currentTime);
        }
    }

    function applySoundPlayback() {
        stopCurrentAudio();
        if (currentSoundMode === 'none' || !isRunning) return;

        initAudioContext();

        if (currentSoundMode === 'tick') {
            playTickSound();
            metronomeInterval = setInterval(playTickSound, 1000);
        } else if (currentSoundMode === 'rain' || currentSoundMode === 'noise') {
            startRainOrNoiseSound(currentSoundMode);
        }
    }

    function setSoundMode(mode) {
        currentSoundMode = mode;
        document.querySelectorAll('.btn-sound-chip').forEach(b => b.classList.remove('active'));
        if (mode === 'none') document.getElementById('btnSoundNone')?.classList.add('active');
        if (mode === 'tick') document.getElementById('btnSoundTick')?.classList.add('active');
        if (mode === 'rain') document.getElementById('btnSoundRain')?.classList.add('active');
        if (mode === 'noise') document.getElementById('btnSoundNoise')?.classList.add('active');

        if (isRunning) {
            applySoundPlayback();
        } else {
            stopCurrentAudio();
        }
    }

    function toggleZenMode() {
        const card = document.getElementById('focusRoomCard');
        if (card) {
            card.classList.toggle('zen-fullscreen-mode');
            const icon = document.getElementById('zenIcon');
            const txt = document.getElementById('zenText');
            if (card.classList.contains('zen-fullscreen-mode')) {
                icon.className = 'fa-solid fa-compress';
                txt.innerText = achievementsI18n.exitZen;
            } else {
                icon.className = 'fa-solid fa-expand';
                txt.innerText = achievementsI18n.enterZen;
            }
        }
    }
</script>
@endsection
