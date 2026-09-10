@extends('layouts.app')

@section('title', 'سجل الإنجاز والشهادات الأكاديمية | منارة التوجيهي')

@section('content')
<div class="achievements-page-wrapper" dir="rtl">

    <!-- 1. ترويسة سجل الإنجاز الأكاديمي -->
    <div class="calm-achievements-hero">
        <div class="hero-left-col">
            <div class="hero-icon-box">
                <i class="fa-solid fa-award"></i>
            </div>
            <div>
                <span class="hero-subtag">سجل التفوق والاعتماد الأكاديمي</span>
                <h1 class="hero-main-title">الأوسمة والشهادات الأكاديمية 🎓</h1>
                <p class="hero-subtext">
                    مبارك جهودك ومثابرتك يا <strong>{{ $student->name_ar ?? $student->name ?? 'طالبنا المتميز' }}</strong>. كل إنجاز تحققه يقربك خطوة نحو طموحك الوزاري.
                </p>
            </div>
        </div>

        <div class="hero-stats-pills">
            <div class="stat-pill-box">
                <span class="pill-num">{{ $isYearEndPublished ? $certificates->count() : '🔒' }}</span>
                <span class="pill-lbl">{{ $isYearEndPublished ? 'شهادات معتمدة' : 'الشهادات (نهاية العام)' }}</span>
            </div>
            <div class="stat-pill-box">
                <span class="pill-num">{{ $completedExamsCount }}</span>
                <span class="pill-lbl">اختبارات مكتملة</span>
            </div>
            <div class="stat-pill-box">
                <span class="pill-num">{{ $student->streak_count ?? 1 }} 🔥</span>
                <span class="pill-lbl">أيام الالتزام</span>
            </div>
        </div>
    </div>

    <!-- 2. قسم الشهادات المعتمدة ونتائج نهاية العام -->
    <div class="ed-card mb-4" style="margin-bottom: 28px;">
        <div class="ed-card-header">
            <h3>
                <i class="fa-solid fa-graduation-cap" style="color: var(--ed-primary);"></i>
                <span>الشهادات الأكاديمية المعتمدة ونتائج التخرج</span>
            </h3>
            <span class="ed-badge {{ $isYearEndPublished ? 'ed-badge-green' : 'ed-badge-amber' }}">
                @if($isYearEndPublished)
                    {{ $certificates->count() }} شهادة معتمدة
                @else
                    محجوبة حتى نهاية العام 🔒
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
                        <i class="fa-solid fa-lock"></i> نظام الاعتماد والشهادات المدرسية الرسمي
                    </span>
                    <h2 class="wait-title">الشهادات والنتائج النهائية تُعتمد وتُعلن في ختام العام الدراسي 🎓</h2>
                    <p class="wait-desc">
                        تنفيذاً للضوابط والمعايير المدرسية والأكاديمية المعتمدة، تخضع نتائجك وسجلك الدراسي للمتابعة والتقييم المستمر. تصدر وتُعتمد الشهادات الرسمية من قبل <strong>إدارة المنصة والمشرف العام</strong> في نهاية العام الدراسي بعد استكمال متطلبات المنهاج والاختبارات الوزارية.
                    </p>
                    <div class="wait-details-pills">
                        <span><i class="fa-regular fa-clock" style="color: var(--ed-warning);"></i> حالة التقييم: <strong style="color: var(--ed-warning);">قيد المتابعة والتدقيق الأكاديمي</strong></span>
                        <span>•</span>
                        <span><i class="fa-regular fa-calendar-check" style="color: var(--ed-success);"></i> موعد الإعلان: <strong style="color: var(--ed-success);">نهاية العام الدراسي</strong></span>
                    </div>
                </div>
            @elseif($certificates->count() > 0)
                <!-- عرض الشهادات المعتمدة الحقيقية بعد إعلان الإدارة -->
                <div class="certificates-clean-grid">
                    @foreach($certificates as $cert)
                        <div class="cert-item-card">
                            <div class="cert-header-status">
                                <span class="ed-badge ed-badge-green"><i class="fa-solid fa-circle-check"></i> معتمد رسميّاً</span>
                                <span style="font-size: 0.74rem; color: var(--ed-text-dim);">عام {{ $cert->created_at ? $cert->created_at->format('Y') : date('Y') }}</span>
                            </div>

                            <div class="cert-icon-center">🎓</div>
                            <h4 class="cert-subject-name">{{ $cert->subject->name_ar ?? $cert->subject->name ?? 'شهادة إتمام وتفوق عامة' }}</h4>
                            <p class="cert-issued-to">صادرة للطالبـ/ـة: <strong>{{ $cert->student->name_ar ?? $cert->student->name ?? $student->name_ar ?? $student->name }}</strong></p>

                            <div class="cert-grade-box">
                                المعدل المعتمد: <strong>{{ $cert->final_grade }}%</strong>
                            </div>

                            <div class="cert-code-tag">
                                <small>رمز الوثيقة:</small>
                                <code>{{ $cert->certificate_code }}</code>
                            </div>

                            <div class="cert-actions-row">
                                <a href="{{ route('student.certificates.show', $cert->id) }}" class="ed-btn ed-btn-primary" style="flex: 1; font-size: 0.82rem;" target="_blank">
                                    <i class="fa-solid fa-eye"></i> استعراض وطباعة
                                </a>
                                <a href="{{ route('certificates.verify', $cert->certificate_code) }}" class="ed-btn ed-btn-outline" style="padding: 8px 12px;" target="_blank" title="التحقق من صحة الوثيقة">
                                    <i class="fa-solid fa-qrcode"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-certs-box">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <h4>تم إعلان نتائج العام الدراسي</h4>
                    <p>شهادتك قيد التدقيق الإداري وسيتم اعتمادها لحسابك قريباً.</p>
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
                    <span>غرفة تنظيم المذاكرة والتركيز الذهني ⏳</span>
                </h3>
                <span style="font-size: 0.8rem; color: var(--ed-text-muted); margin-top: 2px; display: block;">
                    حدد مادتك، اكتب هدفك المباشر، واضبط وقتك لرفع مستوى الإنتاجية والاستيعاب.
                </span>
            </div>
            <button type="button" class="ed-btn ed-btn-outline" onclick="toggleZenMode()" style="font-size: 0.78rem; padding: 6px 12px;">
                <i class="fa-solid fa-expand" id="zenIcon"></i> <span id="zenText">وضع ملء الشاشة</span>
            </button>
        </div>

        <div class="ed-card-body">
            <div class="focus-layout-grid">
                <!-- الجانب الأيمن: أدوات تنظيم الدراسة (المادة، الهدف، المهام) -->
                <div class="focus-col-planner">
                    
                    <div class="planner-card-sub">
                        <label class="form-label-clean"><i class="fa-solid fa-book-bookmark" style="color: var(--ed-primary);"></i> المادة أو المساق الدراسي:</label>
                        <select id="studySubjectSelect" class="ed-select mb-2" onchange="onSubjectChange()">
                            <option value="مراجعة عامة">📚 مراجعة عامة / دراسة حرة</option>
                            @if(isset($subjects) && $subjects->count() > 0)
                                @foreach($subjects as $sub)
                                    <option value="{{ $sub->name_ar }}">{{ $sub->name_ar }}</option>
                                @endforeach
                            @endif
                            <option value="custom">✏️ مادة أخرى (اكتبها بنفسك)...</option>
                        </select>
                        <input type="text" id="customSubjectInput" class="ed-input d-none mt-2" placeholder="اكتب اسم المادة..." oninput="onCustomSubjectInput()">
                    </div>

                    <div class="planner-card-sub">
                        <label class="form-label-clean"><i class="fa-solid fa-bullseye" style="color: var(--ed-danger);"></i> هدف الجلسة الحالي (ماذا تريد أن تنجز؟):</label>
                        <input type="text" id="sessionGoalInput" class="ed-input" placeholder="مثال: حل 10 أسئلة وزارية، مراجعة الدرس الأول..." oninput="updateActiveGoalDisplay()">
                        
                        <div class="quick-tags-wrap">
                            <span class="tag-chip" onclick="setQuickGoal('حل مسائل وزارية مكثفة')">✍️ مسائل وزارية</span>
                            <span class="tag-chip" onclick="setQuickGoal('حفظ ومراجعة درس ومفاهيم')">📖 حفظ ومراجعة</span>
                            <span class="tag-chip" onclick="setQuickGoal('تلخيص وكتابة ملاحظات هامة')">📝 تلخيص شامل</span>
                            <span class="tag-chip" onclick="setQuickGoal('تدريب على نموذج امتحان تجريبي')">🎯 امتحان تجريبي</span>
                        </div>
                    </div>

                    <!-- قائمة مهام الجلسة (Checklist) -->
                    <div class="planner-card-sub">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <label class="form-label-clean mb-0"><i class="fa-solid fa-list-check" style="color: var(--ed-success);"></i> قائمة مهام الجلسة:</label>
                            <span class="ed-badge ed-badge-slate" id="tasksCounterBadge">0 / 0 مكتمل</span>
                        </div>

                        <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                            <input type="text" id="newTaskInput" class="ed-input" placeholder="اكتب مهمة سريعة واضغط Enter..." onkeydown="handleTaskInputKey(event)">
                            <button type="button" class="ed-btn ed-btn-primary" onclick="addNewTask()" style="padding: 0 16px;">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>

                        <div class="tasks-checklist-list" id="tasksChecklist"></div>

                        <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 0.78rem;">
                            <button type="button" onclick="clearCompletedTasks()" style="background: none; border: none; color: var(--ed-text-muted); cursor: pointer;">
                                <i class="fa-solid fa-broom"></i> تنظيف المكتمل
                            </button>
                            <button type="button" onclick="clearAllTasks()" style="background: none; border: none; color: var(--ed-danger); cursor: pointer;">
                                <i class="fa-solid fa-trash-can"></i> مسح الكل
                            </button>
                        </div>
                    </div>

                    <!-- إحصائيات الجلسات لليوم -->
                    <div class="today-stats-row">
                        <div class="stat-mini-box">
                            <i class="fa-regular fa-clock" style="color: var(--ed-primary);"></i>
                            <div>
                                <strong id="statTodayMinutes">0</strong> دقيقة
                                <small>دراسة اليوم</small>
                            </div>
                        </div>
                        <div class="stat-mini-box">
                            <i class="fa-regular fa-circle-check" style="color: var(--ed-success);"></i>
                            <div>
                                <strong id="statTodaySessions">0</strong> جلسات
                                <small>مكتملة</small>
                            </div>
                        </div>
                        <div class="stat-mini-box">
                            <i class="fa-solid fa-fire" style="color: #ea580c;"></i>
                            <div>
                                <strong id="statStreakDays">{{ $student->streak_count ?? 1 }}</strong> أيام
                                <small>التزام متتالي</small>
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
                                <i class="fa-solid fa-book"></i> مراجعة عامة
                            </span>
                            <span class="ed-badge ed-badge-blue" id="bannerStatusTag" style="font-size: 0.7rem;">جاهز للبدء</span>
                        </div>
                        <div id="bannerGoalText" style="font-size: 0.84rem; color: var(--ed-text-body); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            حدد هدفك واضغط بدء الجلسة للتركيز!
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
                                <span class="timer-status-hint" id="timerStatusLabel">جاهز للبدء</span>
                                <span class="timer-pct-hint" id="timerPercentBadge">100% متبقي</span>
                            </div>
                        </div>

                        <!-- أزرار التعديل السريع (+5د، -5د، +15د) -->
                        <div style="display: flex; justify-content: center; gap: 8px; margin-top: 14px;">
                            <button type="button" class="btn-time-chip" onclick="adjustTimerSeconds(-300)">-5 دقائق</button>
                            <button type="button" class="btn-time-chip" onclick="adjustTimerSeconds(300)">+5 دقائق</button>
                            <button type="button" class="btn-time-chip" onclick="adjustTimerSeconds(900)">+15 دقيقة</button>
                        </div>
                    </div>

                    <!-- أزرار التحكم بالمؤقت -->
                    <div class="timer-main-actions">
                        <button type="button" class="ed-btn ed-btn-primary" id="btnStartTimer" onclick="startFocusTimer()" style="flex: 1.5; padding: 12px 20px; font-size: 0.95rem;">
                            <i class="fa-solid fa-play"></i> بدء الجلسة
                        </button>
                        <button type="button" class="ed-btn ed-btn-outline" onclick="resetFocusTimer()" style="flex: 1; padding: 12px;" title="إعادة ضبط">
                            <i class="fa-solid fa-rotate-right"></i> ضبط
                        </button>
                        <button type="button" class="ed-btn ed-btn-outline" onclick="finishSessionEarly()" style="flex: 1; padding: 12px; color: var(--ed-success); border-color: #86efac;" title="إنهاء وحفظ">
                            <i class="fa-solid fa-check"></i> إنهاء
                        </button>
                    </div>

                    <!-- الأنماط المسبقة للمدة -->
                    <div class="timer-presets-wrap">
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--ed-text-muted); display: block; margin-bottom: 6px;">اختر مدة الجلسة:</span>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                            <button type="button" class="btn-preset-chip active" onclick="setTimerPreset(25, 'study', this)">25 د (بومودورو)</button>
                            <button type="button" class="btn-preset-chip" onclick="setTimerPreset(45, 'study', this)">45 د (حل مسائل)</button>
                            <button type="button" class="btn-preset-chip" onclick="setTimerPreset(60, 'study', this)">60 د (مكثفة)</button>
                            <button type="button" class="btn-preset-chip break" onclick="setTimerPreset(5, 'break', this)">5 د (استراحة)</button>
                            <button type="button" class="btn-preset-chip break" onclick="setTimerPreset(15, 'break', this)">15 د (طويلة)</button>
                        </div>
                    </div>

                    <!-- أصوات التركيز -->
                    <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--ed-border); display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.78rem; color: var(--ed-text-muted); font-weight: 600;">
                            <i class="fa-solid fa-headphones"></i> الأجواء الصوتية:
                        </span>
                        <div style="display: flex; gap: 6px;">
                            <button type="button" class="btn-sound-chip active" id="btnSoundNone" onclick="setSoundMode('none')">صامت</button>
                            <button type="button" class="btn-sound-chip" id="btnSoundTick" onclick="setSoundMode('tick')">دقات هادئة</button>
                            <button type="button" class="btn-sound-chip" id="btnSoundRain" onclick="setSoundMode('rain')">مطر</button>
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

    /* ترويسة هادئة */
    .calm-achievements-hero {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        border-radius: var(--ed-radius-lg);
        padding: 28px 32px;
        color: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: 0 10px 25px -5px rgba(29, 78, 216, 0.25);
    }

    .hero-left-col {
        display: flex;
        align-items: center;
        gap: 18px;
        max-width: 600px;
    }

    .hero-icon-box {
        width: 58px;
        height: 58px;
        border-radius: var(--ed-radius-md);
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        display: grid;
        place-items: center;
        font-size: 1.8rem;
        flex-shrink: 0;
    }

    .hero-subtag {
        font-size: 0.76rem;
        font-weight: 600;
        color: #bfdbfe;
        display: block;
        margin-bottom: 2px;
    }

    .hero-main-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0 0 4px;
    }

    .hero-subtext {
        font-size: 0.88rem;
        color: #dbeafe;
        margin: 0;
        line-height: 1.5;
    }

    .hero-stats-pills {
        display: flex;
        gap: 12px;
    }

    .stat-pill-box {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 10px 18px;
        border-radius: var(--ed-radius-md);
        text-align: center;
        min-width: 105px;
    }

    .pill-num {
        font-size: 1.4rem;
        font-weight: 800;
        color: #ffffff;
        display: block;
        line-height: 1.2;
    }

    .pill-lbl {
        font-size: 0.7rem;
        color: #bfdbfe;
        font-weight: 600;
    }

    /* حالة الانتظار الأكاديمية */
    .academic-wait-box {
        text-align: center;
        padding: 36px 20px;
        background: var(--ed-surface-alt);
        border: 2px dashed var(--ed-border);
        border-radius: var(--ed-radius-lg);
    }

    .wait-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 14px;
        border-radius: 50%;
        background: var(--ed-warning-soft);
        color: var(--ed-warning);
        display: grid;
        place-items: center;
        font-size: 1.8rem;
    }

    .wait-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--ed-text-main);
        margin-bottom: 8px;
    }

    .wait-desc {
        font-size: 0.9rem;
        color: var(--ed-text-muted);
        max-width: 640px;
        margin: 0 auto 20px;
        line-height: 1.7;
    }

    .wait-details-pills {
        display: inline-flex;
        gap: 12px;
        background: var(--ed-surface);
        border: 1px solid var(--ed-border);
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
        background: var(--ed-surface);
        border: 1px solid var(--ed-border);
        border-radius: var(--ed-radius-md);
        padding: 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 12px;
        box-shadow: var(--ed-shadow-card);
        transition: var(--transition-smooth);
    }

    .cert-item-card:hover {
        border-color: var(--ed-primary-border);
        transform: translateY(-2px);
        box-shadow: var(--ed-shadow-md);
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
        color: var(--ed-text-main);
        margin: 0;
    }

    .cert-issued-to {
        font-size: 0.82rem;
        color: var(--ed-text-muted);
        margin: 0;
    }

    .cert-grade-box {
        background: var(--ed-primary-soft);
        color: var(--ed-primary);
        padding: 6px 12px;
        border-radius: var(--ed-radius-sm);
        font-size: 0.88rem;
    }

    .cert-code-tag {
        font-size: 0.74rem;
        color: var(--ed-text-dim);
    }
    .cert-code-tag code {
        font-family: monospace;
        font-weight: 700;
        color: var(--ed-text-main);
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
        color: var(--ed-text-muted);
    }
    .empty-certs-box i {
        font-size: 3rem;
        color: var(--ed-text-dim);
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
        background: var(--ed-surface-alt);
        border: 1px solid var(--ed-border);
        border-radius: var(--ed-radius-md);
        padding: 14px 16px;
        margin-bottom: 14px;
    }

    .form-label-clean {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--ed-text-main);
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
        background: var(--ed-surface);
        border: 1px solid var(--ed-border);
        padding: 3px 9px;
        border-radius: 6px;
        font-size: 0.74rem;
        color: var(--ed-text-muted);
        cursor: pointer;
        transition: var(--transition-smooth);
    }
    .tag-chip:hover {
        color: var(--ed-primary);
        border-color: var(--ed-primary-border);
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
        background: var(--ed-surface);
        border: 1px solid var(--ed-border);
        border-radius: var(--ed-radius-sm);
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.82rem;
    }
    .stat-mini-box strong {
        display: block;
        font-size: 1.05rem;
        color: var(--ed-text-main);
        line-height: 1.1;
    }
    .stat-mini-box small {
        font-size: 0.68rem;
        color: var(--ed-text-muted);
    }

    /* عمود المؤقت */
    .focus-col-timer {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: var(--ed-surface-alt);
        border: 1px solid var(--ed-border);
        border-radius: var(--ed-radius-md);
        padding: 18px 20px;
    }

    .active-goal-strip {
        background: var(--ed-surface);
        border: 1px solid var(--ed-border);
        border-radius: var(--ed-radius-sm);
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
        color: var(--ed-text-main);
        font-family: monospace;
        letter-spacing: -1px;
    }

    .timer-status-hint {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--ed-primary);
        margin-top: 2px;
    }

    .timer-pct-hint {
        font-size: 0.68rem;
        color: var(--ed-text-muted);
    }

    .btn-time-chip {
        background: var(--ed-surface);
        border: 1px solid var(--ed-border);
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 0.74rem;
        color: var(--ed-text-muted);
        cursor: pointer;
        transition: var(--transition-smooth);
    }
    .btn-time-chip:hover {
        color: var(--ed-primary);
        border-color: var(--ed-primary-border);
    }

    .timer-main-actions {
        display: flex;
        gap: 8px;
        margin: 14px 0 10px;
    }

    .btn-preset-chip {
        background: var(--ed-surface);
        border: 1px solid var(--ed-border);
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--ed-text-muted);
        cursor: pointer;
        transition: var(--transition-smooth);
    }
    .btn-preset-chip:hover, .btn-preset-chip.active {
        background: var(--ed-primary);
        color: #ffffff;
        border-color: var(--ed-primary);
    }
    .btn-preset-chip.break.active {
        background: var(--ed-success);
        border-color: var(--ed-success);
    }

    .btn-sound-chip {
        background: var(--ed-surface);
        border: 1px solid var(--ed-border);
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 0.72rem;
        color: var(--ed-text-muted);
        cursor: pointer;
    }
    .btn-sound-chip.active {
        background: var(--ed-primary);
        color: white;
        border-color: var(--ed-primary);
    }

    @media (max-width: 900px) {
        .focus-layout-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    let timerDurationMinutes = 25;
    let totalSeconds = 25 * 60;
    let remainingSeconds = totalSeconds;
    let timerInterval = null;
    let isRunning = false;
    let currentSessionType = 'study';
    let currentSoundMode = 'none';
    let tickInterval = null;

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
            return document.getElementById('customSubjectInput').value.trim() || 'مادة مخصصة';
        }
        return select ? select.value : 'مراجعة عامة';
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
        document.getElementById('bannerGoalText').innerText = goal ? `الهدف: ${goal}` : 'حدد هدفك واضغط بدء الجلسة للتركيز!';
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
        if (counterEl) counterEl.innerText = `${completedCount} / ${tasks.length} مكتمل`;

        if (!listEl) return;
        if (tasks.length === 0) {
            listEl.innerHTML = '<div style="font-size: 0.78rem; color: var(--ed-text-muted); text-align: center; padding: 12px 0;">لا توجد مهام محددة للجلسة بعد.</div>';
            return;
        }

        let html = '';
        tasks.forEach(t => {
            html += `
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 6px 10px; background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: 6px; font-size: 0.8rem;">
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
        const btn = document.getElementById('btnStartTimer');
        btn.innerHTML = '<i class="fa-solid fa-pause"></i> إيقاف مؤقت';
        document.getElementById('timerStatusLabel').innerText = currentSessionType === 'study' ? 'جلسة مذاكرة جارية 📚' : 'استراحة مستحقة ☕';
        document.getElementById('bannerStatusTag').innerText = 'الجلسة نشطة';

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
        const btn = document.getElementById('btnStartTimer');
        btn.innerHTML = '<i class="fa-solid fa-play"></i> استئناف الجلسة';
        document.getElementById('timerStatusLabel').innerText = 'موقوف مؤقتاً ⏸️';
        document.getElementById('bannerStatusTag').innerText = 'موقوف مؤقتاً';
    }

    function resetFocusTimer() {
        clearInterval(timerInterval);
        isRunning = false;
        remainingSeconds = totalSeconds;
        const btn = document.getElementById('btnStartTimer');
        btn.innerHTML = '<i class="fa-solid fa-play"></i> بدء الجلسة';
        document.getElementById('timerStatusLabel').innerText = 'جاهز للبدء';
        document.getElementById('bannerStatusTag').innerText = 'جاهز للبدء';
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
        btn.innerHTML = '<i class="fa-solid fa-play"></i> بدء الجلسة';
        document.getElementById('timerStatusLabel').innerText = type === 'study' ? 'جاهز للمذاكرة' : 'جاهز للاستراحة';

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
        if (pctBadge) pctBadge.innerText = `${Math.round(100 - progressPercent)}% متبقي`;
    }

    function finishSessionEarly() {
        if (remainingSeconds === totalSeconds) {
            Swal.fire({ icon: 'info', title: 'لم تبدأ الجلسة بعد', text: 'اضغط على بدء الجلسة أولاً لتسجيل إنجازك.' });
            return;
        }

        Swal.fire({
            title: 'هل تريد إنهاء الجلسة وتسجيل الإنجاز؟',
            text: 'سيتم حفظ الدقائق التي درستها وحسابها ضمن إحصائيات التزامك اليومي.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، حفظ الإنجاز',
            cancelButtonText: 'متابعة الجلسة'
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
        btn.innerHTML = '<i class="fa-solid fa-play"></i> بدء الجلسة';
        document.getElementById('timerStatusLabel').innerText = 'اكتملت الجلسة! 🎉';
        document.getElementById('bannerStatusTag').innerText = 'تم الإنجاز بنجاح';

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
                title: 'مبارك إنجازك يا بطل! 👏',
                text: res.data.message || `أتممت ${minutes} دقيقة مذاكرة بنجاح.`,
                confirmButtonText: 'متابعة'
            });
            resetFocusTimer();
        }).catch(() => {
            recordLocalSessionStats(minutes);
            Swal.fire({
                icon: 'success',
                title: 'تم تسجيل الجلسة محلياً! 👏',
                text: `أنجزت ${minutes} دقيقة دراسة بنجاح. استمر يا بطل!`,
                confirmButtonText: 'ممتاز'
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

    function setSoundMode(mode) {
        currentSoundMode = mode;
        document.querySelectorAll('.btn-sound-chip').forEach(b => b.classList.remove('active'));
        if (mode === 'none') document.getElementById('btnSoundNone').classList.add('active');
        if (mode === 'tick') document.getElementById('btnSoundTick').classList.add('active');
        if (mode === 'rain') document.getElementById('btnSoundRain').classList.add('active');
    }

    function toggleZenMode() {
        const card = document.getElementById('focusRoomCard');
        if (card) {
            card.classList.toggle('zen-fullscreen-mode');
            const icon = document.getElementById('zenIcon');
            const txt = document.getElementById('zenText');
            if (card.classList.contains('zen-fullscreen-mode')) {
                icon.className = 'fa-solid fa-compress';
                txt.innerText = 'إنهاء ملء الشاشة';
            } else {
                icon.className = 'fa-solid fa-expand';
                txt.innerText = 'وضع ملء الشاشة';
            }
        }
    }
</script>
@endsection
