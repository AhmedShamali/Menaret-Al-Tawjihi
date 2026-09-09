@extends('layouts.app')

@section('title', 'أوسمة الإنجاز والشهادات المعتمدة | منارة التوجيهي')

@section('content')
<div class="achievements-page-wrapper" dir="rtl">

    <!-- الترويسة الرئيسية -->
    <div class="achievements-hero-card">
        <div class="hero-content-flex">
            <div class="hero-avatar">
                <i class="fa-solid fa-medal"></i>
            </div>
            <div>
                <span class="hero-tag">سجل الفخر والتميز الأكاديمي</span>
                <h1 class="hero-title">أوسمة التفوق والشهادات المعتمدة 🏆</h1>
                <p class="hero-subtitle">
                    مبارك جهودك يا <strong>{{ $student->name_ar ?? $student->name ?? 'بطل التوجيهي' }}</strong>! كل خطوة تخطوها تقربك من فرحة التوجيهي الكبرى.
                </p>
            </div>
        </div>

        <div class="hero-stats-row">
            <div class="hero-stat-pill">
                <span class="num">{{ $isYearEndPublished ? $certificates->count() : '🔒' }}</span>
                <span class="lbl">{{ $isYearEndPublished ? 'شهادات معتمدة' : 'الشهادات (نهاية العام)' }}</span>
            </div>
            <div class="hero-stat-pill">
                <span class="num">{{ $completedExamsCount }}</span>
                <span class="lbl">امتحانات منجزة</span>
            </div>
            <div class="hero-stat-pill">
                <span class="num">{{ $student->streak_count ?? 1 }} 🔥</span>
                <span class="lbl">أيام التزام متتالية</span>
            </div>
        </div>
    </div>

    <!-- شبكة الشهادات الملكية ونتائج نهاية العام -->
    <div class="section-title-bar">
        <h3><i class="fa-solid fa-award text-warning"></i> الشهادات الأكاديمية ونتائج نهاية العام</h3>
        <span class="badge-count">
            @if($isYearEndPublished)
                {{ $certificates->count() }} شهادة معتمدة
            @else
                محجوبة حتى نهاية العام 🔒
            @endif
        </span>
    </div>

    @if(!$isYearEndPublished)
        <!-- حالة الحجب الأكاديمي الشرفي: الشهادات محجوبة حتى نهاية العام بقرار المدير -->
        <div class="year-end-locked-box" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 2px dashed #cbd5e1; border-radius: 24px; padding: 45px 30px; text-align: center; margin-bottom: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); position: relative; overflow: hidden;">
            <div style="width: 80px; height: 80px; margin: 0 auto 20px; border-radius: 24px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; box-shadow: 0 10px 25px rgba(217, 119, 6, 0.2);">
                <i class="fa-solid fa-school-flag"></i>
            </div>
            
            <span style="background: #eef2ff; color: #4f46e5; padding: 6px 16px; border-radius: 50px; font-weight: 800; font-size: 0.85rem; display: inline-block; margin-bottom: 15px;">
                <i class="fa-solid fa-lock"></i> نظام الاعتماد والشهادات المدرسية الرسمي
            </span>
            
            <h2 style="font-size: 1.5rem; font-weight: 900; color: #0f172a; margin-bottom: 12px;">
                الشهادات الأكاديمية والمعدلات تُعلن رسميّاً في نهاية العام الدراسي 🎓
            </h2>
            
            <p style="font-size: 0.96rem; color: #64748b; max-width: 650px; margin: 0 auto 25px auto; line-height: 1.8;">
                تنفيذاً للضوابط والمعايير المدرسية والأكاديمية المعتمدة، تخضع درجاتك وسجلك الدراسي للمتابعة والتقييم المستمر. لا تصدر الشهادات والمعدلات تلقائياً، بل تُعتمد وتُعلن رسميّاً من قبل <strong>إدارة المنصة والمشرف العام</strong> في نهاية العام الدراسي بعد استكمال متطلبات المنهاج والاختبارات الوزارية.
            </p>

            <div style="display: inline-flex; flex-wrap: wrap; gap: 15px; justify-content: center; align-items: center; background: #ffffff; padding: 14px 25px; border-radius: 16px; border: 1px solid #e2e8f0; font-size: 0.88rem; font-weight: 700;">
                <span style="color: #475569; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: #f59e0b;"></i> حالة الاعتماد: <span style="color: #d97706;">قيد المتابعة والتقييم الأكاديمي المستمر</span>
                </span>
                <span style="color: #cbd5e1;">•</span>
                <span style="color: #475569; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-calendar-check" style="color: #10b981;"></i> موعد إعلان النتائج: <span style="color: #059669;">نهاية العام الدراسي (بقرار الإدارة العامة)</span>
                </span>
            </div>
        </div>
    @elseif($certificates->count() > 0)
        <!-- عرض الشهادات المعتمدة الحقيقية فقط بعد إعلان الإدارة -->
        <div class="certificates-royal-grid">
            @foreach($certificates as $cert)
                <div class="cert-royal-card">
                    <div class="cert-gold-ribbon"><i class="fa-solid fa-star"></i> معتمد رسمياً</div>
                    <div class="cert-card-icon">🎓</div>
                    <h4 class="cert-subject-title">{{ $cert->subject->name_ar ?? $cert->subject->name ?? 'شهادة إتمام وتفوق عامة' }}</h4>
                    <p class="cert-student-sub">شهادة إتمام واجتياز أكاديمي صادرة باسم: <strong>{{ $cert->student->name_ar ?? $cert->student->name ?? $student->name_ar ?? $student->name }}</strong></p>
                    
                    <div class="cert-grade-tag">
                        المعدل المعتمد: <span>{{ $cert->final_grade }}%</span>
                    </div>

                    <div class="cert-code-box">
                        <small>كود الوثيقة المعتمد:</small>
                        <code>{{ $cert->certificate_code }}</code>
                    </div>

                    <div class="cert-actions">
                        <a href="{{ route('student.certificates.show', $cert->id) }}" class="btn-cert-view" target="_blank">
                            <i class="fa-solid fa-eye"></i> استعراض وطباعة الشهادة
                        </a>
                        <a href="{{ route('certificates.verify', $cert->certificate_code) }}" class="btn-cert-verify" target="_blank" title="التحقق المباشر من صحة الشهادة">
                            <i class="fa-solid fa-qrcode"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-certs-card">
            <i class="fa-solid fa-graduation-cap empty-icon"></i>
            <h4>تم إعلان نتائج العام الدراسي</h4>
            <p>شهادتك قيد المراجعة الإدارية النهائية وسيتم إدراجها فور اعتماد المشرف العام.</p>
        </div>
    @endif

    <!-- غرفة التحكم وتنظيم المذاكرة والتركيز الذهني (Smart Study Organizer & Focus Room) -->
    <div class="pomodoro-focus-card" id="focusRoomCard">
        <!-- ترويسة الغرفة وأزرار الوضع -->
        <div class="focus-card-header">
            <div class="focus-title-wrap">
                <div class="focus-badge-row">
                    <span class="focus-tag"><i class="fa-solid fa-graduation-cap"></i> غرفة تنظيم المذاكرة والتركيز الذهني ⏳</span>
                    <span class="session-mode-badge" id="sessionModeBadge"><i class="fa-solid fa-book-open"></i> وضع المذاكرة</span>
                </div>
                <h3>مُنظّم وقتك وهدفك الدراسي للتوجيهي 🎯</h3>
                <p>تحكّم بكامل تفاصيل جلستك: حدد المادة، اكتب هدفك المباشر، رتب مهامك، واضبط مدة الجلسة بدقة لتصل لأعلى درجات الاستيعاب والإنتاجية.</p>
            </div>
            <div class="header-actions-pills">
                <button type="button" class="btn-zen-mode" onclick="toggleZenMode()" title="وضع ملء الشاشة للتركيز الخالي من المشتتات">
                    <i class="fa-solid fa-expand" id="zenIcon"></i> <span id="zenText">وضع التركيز الكامل</span>
                </button>
            </div>
        </div>

        <div class="focus-main-grid">
            <!-- العمود الأيمن: أدوات تنظيم الدراسة (المادة، الهدف، المهام) -->
            <div class="study-planner-column">
                
                <!-- اختيار المادة والهدف -->
                <div class="planner-box subject-goal-box">
                    <div class="field-group">
                        <label class="field-label"><i class="fa-solid fa-book-bookmark text-primary"></i> المادة أو المساق الدراسي:</label>
                        <div class="subject-select-row">
                            <select id="studySubjectSelect" class="form-select-custom" onchange="onSubjectChange()">
                                <option value="مراجعة عامة">📚 مراجعة عامة / دراسة حرة</option>
                                @if(isset($subjects) && $subjects->count() > 0)
                                    @foreach($subjects as $sub)
                                        <option value="{{ $sub->name_ar }}">{{ $sub->name_ar }}</option>
                                    @endforeach
                                @endif
                                <option value="custom">✏️ مادة أخرى (اكتبها بنفسك)...</option>
                            </select>
                            <input type="text" id="customSubjectInput" class="form-input-custom d-none" placeholder="اكتب اسم المادة..." oninput="onCustomSubjectInput()">
                        </div>
                    </div>

                    <div class="field-group mt-3">
                        <label class="field-label"><i class="fa-solid fa-bullseye text-danger"></i> هدف الجلسة الحالي (ماذا تريد أن تنجز؟):</label>
                        <div class="goal-input-wrap">
                            <input type="text" id="sessionGoalInput" class="form-input-custom" placeholder="مثال: حل 10 مسائل وزارية، مراجعة الدرس الأول، حفظ المفاهيم..." oninput="updateActiveGoalDisplay()">
                        </div>
                        <!-- وسوم سريعة للأهداف الشائعة -->
                        <div class="quick-goal-tags">
                            <span class="goal-tag-pill" onclick="setQuickGoal('حل مسائل وزارية مكثفة')">✍️ مسائل وزارية</span>
                            <span class="goal-tag-pill" onclick="setQuickGoal('حفظ ومراجعة درس ومفاهيم')">📖 حفظ ومراجعة</span>
                            <span class="goal-tag-pill" onclick="setQuickGoal('تلخيص وكتابة ملاحظات هامة')">📝 تلخيص شامل</span>
                            <span class="goal-tag-pill" onclick="setQuickGoal('تدريب على نموذج امتحان تجريبي')">🎯 امتحان تجريبي</span>
                        </div>
                    </div>
                </div>

                <!-- قائمة مهام الجلسة السريعة (Study Checklist / Micro-Tasks) -->
                <div class="planner-box tasks-box">
                    <div class="box-header-flex">
                        <label class="field-label mb-0"><i class="fa-solid fa-list-check text-success"></i> قائمة مهام الجلسة (Checklist):</label>
                        <span class="tasks-counter-badge" id="tasksCounterBadge">0 / 0 مكتمل</span>
                    </div>

                    <div class="add-task-row mt-2">
                        <input type="text" id="newTaskInput" class="form-input-custom" placeholder="اكتب مهمة سريعة واضغط Enter..." onkeydown="handleTaskInputKey(event)">
                        <button type="button" class="btn-add-task" onclick="addNewTask()">
                            <i class="fa-solid fa-plus"></i> إضافة
                        </button>
                    </div>

                    <!-- قائمة المهام التفاعلية -->
                    <div class="tasks-checklist-list" id="tasksChecklist">
                        <!-- تُعرض المهام ديناميكياً هنا -->
                    </div>

                    <div class="tasks-footer-actions">
                        <button type="button" class="btn-clean-tasks" onclick="clearCompletedTasks()">
                            <i class="fa-solid fa-broom"></i> تنظيف المنجز
                        </button>
                        <button type="button" class="btn-clean-tasks text-danger" onclick="clearAllTasks()">
                            <i class="fa-solid fa-trash-can"></i> مسح الكل
                        </button>
                    </div>
                </div>

                <!-- إحصائيات جلسات اليوم المحققة -->
                <div class="today-stats-strip">
                    <div class="stat-cell">
                        <i class="fa-solid fa-stopwatch text-primary"></i>
                        <div>
                            <strong id="statTodayMinutes">0</strong> دقيقة
                            <small>دراسة اليوم</small>
                        </div>
                    </div>
                    <div class="stat-cell">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <div>
                            <strong id="statTodaySessions">0</strong> جلسات
                            <small>مكتملة اليوم</small>
                        </div>
                    </div>
                    <div class="stat-cell">
                        <i class="fa-solid fa-fire text-warning"></i>
                        <div>
                            <strong id="statStreakDays">{{ $student->streak_count ?? 1 }}</strong> أيام
                            <small>التزام متتالي</small>
                        </div>
                    </div>
                </div>

            </div>

            <!-- العمود الأيسر: المؤقت والتحكم الكامل بالوقت والأصوات -->
            <div class="timer-engine-column">

                <!-- بطاقة الهدف النشط أثناء الجلسة -->
                <div class="active-session-banner" id="activeSessionBanner">
                    <div class="banner-top-row">
                        <span class="banner-subject" id="bannerSubjectText"><i class="fa-solid fa-book"></i> مراجعة عامة</span>
                        <span class="banner-status-tag" id="bannerStatusTag">جاهز للانطلاق</span>
                    </div>
                    <div class="banner-goal" id="bannerGoalText">حدد هدفك واضغط بدء الجلسة للتركيز!</div>
                    <div class="session-progress-bar-wrap">
                        <div class="session-progress-bar" id="sessionProgressBar" style="width: 0%;"></div>
                    </div>
                </div>

                <!-- المؤقت الدائري SVG التفاعلي -->
                <div class="timer-dial-wrapper">
                    <div class="svg-dial-container">
                        <svg class="timer-svg" width="220" height="220" viewBox="0 0 220 220">
                            <!-- دائرة الخلفية -->
                            <circle class="timer-bg-circle" cx="110" cy="110" r="92" stroke-width="12"></circle>
                            <!-- دائرة التقدم النشطة المتناقصة -->
                            <circle class="timer-progress-circle" id="timerProgressCircle" cx="110" cy="110" r="92" stroke-width="12" stroke-dasharray="578" stroke-dashoffset="0"></circle>
                        </svg>
                        
                        <div class="dial-center-content">
                            <span class="timer-digits" id="timerDigits">25:00</span>
                            <span class="timer-state-label" id="timerStatusLabel">جاهز للبدء</span>
                            <span class="timer-percent-badge" id="timerPercentBadge">100% متبقي</span>
                        </div>
                    </div>

                    <!-- أزرار التعديل الفوري للوقت بنقرة واحدة (+5د، -5د، +15د) -->
                    <div class="time-adjusters-row">
                        <button type="button" class="btn-adjust-chip minus" onclick="adjustTimerSeconds(-300)" title="إنقاص 5 دقائق">
                            <i class="fa-solid fa-minus"></i> 5 دقائق
                        </button>
                        <button type="button" class="btn-adjust-chip plus" onclick="adjustTimerSeconds(300)" title="زيادة 5 دقائق">
                            <i class="fa-solid fa-plus"></i> 5 دقائق
                        </button>
                        <button type="button" class="btn-adjust-chip plus" onclick="adjustTimerSeconds(900)" title="زيادة 15 دقيقة">
                            <i class="fa-solid fa-plus"></i> 15 دقيقة
                        </button>
                    </div>
                </div>

                <!-- أزرار التحكم الرئيسية بالمؤقت -->
                <div class="main-timer-controls">
                    <button type="button" class="btn-timer-ctrl play" id="btnStartTimer" onclick="startFocusTimer()">
                        <i class="fa-solid fa-play"></i> بدء الجلسة
                    </button>
                    <button type="button" class="btn-timer-ctrl reset" onclick="resetFocusTimer()" title="إعادة ضبط المؤقت">
                        <i class="fa-solid fa-rotate-right"></i> إعادة ضبط
                    </button>
                    <button type="button" class="btn-timer-ctrl finish" onclick="finishSessionEarly()" title="إنهاء الجلسة وحفظ الإنجاز فوراً">
                        <i class="fa-solid fa-circle-check"></i> إنهاء وحفظ
                    </button>
                </div>

                <!-- خيارات الوقت المسبقة واليدوية -->
                <div class="time-presets-master">
                    <div class="preset-section-title"><i class="fa-solid fa-clock"></i> اختر مدة الجلسة أو أدخل ما يناسبك:</div>
                    
                    <div class="preset-buttons-lane">
                        <button type="button" class="btn-preset active" onclick="setTimerPreset(25, 'study', this)">
                            <i class="fa-solid fa-bolt text-warning"></i> 25 د (بومودورو)
                        </button>
                        <button type="button" class="btn-preset" onclick="setTimerPreset(45, 'study', this)">
                            <i class="fa-solid fa-brain text-primary"></i> 45 د (حل مسائل)
                        </button>
                        <button type="button" class="btn-preset" onclick="setTimerPreset(60, 'study', this)">
                            <i class="fa-solid fa-rocket text-danger"></i> 60 د (جلسة مكثفة)
                        </button>
                        <button type="button" class="btn-preset break" onclick="setTimerPreset(5, 'break', this)">
                            <i class="fa-solid fa-mug-hot text-success"></i> 5 د (استراحة قصيرة)
                        </button>
                        <button type="button" class="btn-preset break" onclick="setTimerPreset(15, 'break', this)">
                            <i class="fa-solid fa-couch text-info"></i> 15 د (استراحة طويلة)
                        </button>
                    </div>

                    <!-- إدخال يدوي حر لأي عدد من الدقائق يريده الطالب -->
                    <div class="custom-time-picker-row">
                        <span class="custom-time-label"><i class="fa-solid fa-sliders"></i> مدة مخصصة:</span>
                        <div class="custom-input-with-btn">
                            <input type="number" id="customMinutesInput" min="1" max="240" value="30" class="input-custom-mins" placeholder="مثال: 30">
                            <span class="mins-unit">دقيقة</span>
                            <button type="button" class="btn-apply-custom-time" onclick="applyCustomMinutes()">
                                <i class="fa-solid fa-check"></i> تطبيق
                            </button>
                        </div>
                    </div>
                </div>

                <!-- إعدادات الأجواء الصوتية للتركيز -->
                <div class="ambience-control-box">
                    <div class="ambience-label">
                        <i class="fa-solid fa-headphones text-primary"></i> بيئة التركيز الصوتي:
                    </div>
                    <div class="ambience-buttons">
                        <button type="button" class="btn-sound-mode active" id="btnSoundNone" onclick="setSoundMode('none')">
                            <i class="fa-solid fa-volume-xmark"></i> صامت
                        </button>
                        <button type="button" class="btn-sound-mode" id="btnSoundTick" onclick="setSoundMode('tick')">
                            <i class="fa-solid fa-clock"></i> دقات هادئة
                        </button>
                        <button type="button" class="btn-sound-mode" id="btnSoundRain" onclick="setSoundMode('rain')">
                            <i class="fa-solid fa-cloud-rain"></i> مطر لطيف
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    // ==========================================
    // محرك غرفة تنظيم المذاكرة والتركيز للتوجيهي
    // ==========================================
    let timerDurationMinutes = 25;
    let totalSeconds = 25 * 60;
    let remainingSeconds = totalSeconds;
    let timerInterval = null;
    let isRunning = false;
    let currentSessionType = 'study'; // 'study' | 'break'
    let currentSoundMode = 'none'; // 'none' | 'tick' | 'rain'
    let audioContext = null;
    let ambientSourceNode = null;
    let tickInterval = null;

    const CIRCLE_CIRCUMFERENCE = 578; // 2 * PI * 92

    // تحميل المهام والإحصائيات المحفوظة عند فتح الصفحة
    document.addEventListener('DOMContentLoaded', () => {
        loadSavedStudyData();
        renderTasks();
        updateDailyStatsDisplay();
        updateTimerDisplay();
    });

    // ------------------------------------------
    // التحكم بالمواد والأهداف الدراسية
    // ------------------------------------------
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
        if (select.value === 'custom') {
            const val = document.getElementById('customSubjectInput').value.trim();
            return val || 'مادة مخصصة';
        }
        return select.value || 'مراجعة عامة';
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

    // ------------------------------------------
    // إدارة قائمة المهام السريعة (Checklist)
    // ------------------------------------------
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
        tasks.push({
            id: Date.now(),
            text: text,
            completed: false
        });

        saveTasks(tasks);
        input.value = '';
        input.focus();
    }

    function toggleTask(id) {
        const tasks = getTasks();
        const task = tasks.find(t => t.id === id);
        if (task) {
            task.completed = !task.completed;
            saveTasks(tasks);
            if (task.completed) {
                playSubtleChime();
            }
        }
    }

    function deleteTask(id) {
        let tasks = getTasks();
        tasks = tasks.filter(t => t.id !== id);
        saveTasks(tasks);
    }

    function clearCompletedTasks() {
        let tasks = getTasks();
        tasks = tasks.filter(t => !t.completed);
        saveTasks(tasks);
    }

    function clearAllTasks() {
        if (confirm('هل أنت متأكد من مسح جميع مهام الجلسة؟')) {
            saveTasks([]);
        }
    }

    function renderTasks() {
        const tasks = getTasks();
        const container = document.getElementById('tasksChecklist');
        const badge = document.getElementById('tasksCounterBadge');

        const completedCount = tasks.filter(t => t.completed).length;
        badge.innerText = `${completedCount} / ${tasks.length} مكتمل`;

        if (tasks.length === 0) {
            container.innerHTML = `
                <div class="empty-tasks-hint">
                    <i class="fa-solid fa-feather"></i> لا توجد مهام محددة بعد. اكتب مهمة لأعلى لتقسيم وقتك وإنجازها خطوة بخطوة!
                </div>
            `;
            return;
        }

        container.innerHTML = tasks.map(t => `
            <div class="task-check-item ${t.completed ? 'completed' : ''}">
                <label class="task-checkbox-label">
                    <input type="checkbox" ${t.completed ? 'checked' : ''} onchange="toggleTask(${t.id})">
                    <span class="custom-chk"><i class="fa-solid fa-check"></i></span>
                    <span class="task-text">${escapeHtml(t.text)}</span>
                </label>
                <button type="button" class="btn-del-task" onclick="deleteTask(${t.id})" title="حذف المهمة">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        `).join('');
    }

    function escapeHtml(str) {
        return str.replace(/[&<>'"]/g, tag => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#39;',
            '"': '&quot;'
        }[tag] || tag));
    }

    // ------------------------------------------
    // التحكم بالوقت والأوضاع (Presets & Custom)
    // ------------------------------------------
    function setTimerPreset(mins, type, btn) {
        if (isRunning) {
            Swal.fire({
                icon: 'warning',
                title: 'المؤقت قيد التشغيل',
                text: 'يرجى إيقاف المؤقت مؤقتاً أو إعادة ضبطه قبل تغيير نمط الوقت.',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        document.querySelectorAll('.btn-preset').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        currentSessionType = type;
        updateSessionTypeUI(type);

        timerDurationMinutes = mins;
        totalSeconds = mins * 60;
        remainingSeconds = totalSeconds;
        updateTimerDisplay();
    }

    function applyCustomMinutes() {
        if (isRunning) {
            Swal.fire({
                icon: 'warning',
                title: 'المؤقت قيد التشغيل',
                text: 'يرجى إيقاف المؤقت مؤقتاً قبل تطبيق مدة جديدة.',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        const input = document.getElementById('customMinutesInput');
        const mins = parseInt(input.value);

        if (!mins || mins < 1 || mins > 240) {
            Swal.fire({
                icon: 'error',
                title: 'مدة غير صحيحة',
                text: 'يرجى إدخال عدد دقائق بين 1 و 240 دقيقة.',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        document.querySelectorAll('.btn-preset').forEach(b => b.classList.remove('active'));
        currentSessionType = 'study';
        updateSessionTypeUI('study');

        timerDurationMinutes = mins;
        totalSeconds = mins * 60;
        remainingSeconds = totalSeconds;
        updateTimerDisplay();

        Swal.fire({
            icon: 'success',
            title: 'تم ضبط المدة بنجاح ⏱️',
            text: `تم ضبط وقت الجلسة على ${mins} دقيقة. جاهز للبدء!`,
            timer: 1500,
            showConfirmButton: false
        });
    }

    function adjustTimerSeconds(secondsDelta) {
        if (remainingSeconds + secondsDelta < 60) {
            Swal.fire({
                icon: 'info',
                title: 'تنبيه',
                text: 'لا يمكن إنقاص الوقت لأقل من دقيقة واحدة.',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        remainingSeconds += secondsDelta;
        totalSeconds = Math.max(totalSeconds, remainingSeconds);
        timerDurationMinutes = Math.round(totalSeconds / 60);

        updateTimerDisplay();
    }

    function updateSessionTypeUI(type) {
        const modeBadge = document.getElementById('sessionModeBadge');
        const circle = document.getElementById('timerProgressCircle');

        if (type === 'break') {
            modeBadge.className = 'session-mode-badge break';
            modeBadge.innerHTML = '<i class="fa-solid fa-mug-hot"></i> وضع الاستراحة';
            circle.style.stroke = '#10b981'; // أخضر مريح للاستراحة
            document.getElementById('bannerStatusTag').innerText = 'استراحة مستحقة ☕';
        } else {
            modeBadge.className = 'session-mode-badge study';
            modeBadge.innerHTML = '<i class="fa-solid fa-book-open"></i> وضع المذاكرة والتركيز';
            circle.style.stroke = '#0284c7'; // أزرق تركيز ملكي
            document.getElementById('bannerStatusTag').innerText = isRunning ? 'جلسة نشطة 🎯' : 'جاهز للانطلاق';
        }
    }

    // ------------------------------------------
    // محرك تشغيل المؤقت والعد التنازلي
    // ------------------------------------------
    function updateTimerDisplay() {
        const m = Math.floor(remainingSeconds / 60);
        const s = remainingSeconds % 60;
        document.getElementById('timerDigits').innerText = 
            `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;

        // حساب النسبة المئوية المتبقية وشريط التقدم الدائري
        const fractionRemaining = totalSeconds > 0 ? (remainingSeconds / totalSeconds) : 0;
        const offset = CIRCLE_CIRCUMFERENCE * (1 - fractionRemaining);
        const progressCircle = document.getElementById('timerProgressCircle');
        if (progressCircle) {
            progressCircle.style.strokeDashoffset = offset;
        }

        // شريط تقدم الجلسة الأفقي
        const percentCompleted = Math.round((1 - fractionRemaining) * 100);
        const progressBar = document.getElementById('sessionProgressBar');
        if (progressBar) {
            progressBar.style.width = `${percentCompleted}%`;
        }

        const percentBadge = document.getElementById('timerPercentBadge');
        if (percentBadge) {
            percentBadge.innerText = `${Math.round(fractionRemaining * 100)}% متبقي`;
        }

        // تحديث عنوان تبويب المتصفح ليتابع الطالب وقته
        if (isRunning) {
            document.title = `(${m}:${s.toString().padStart(2, '0')}) ${getActiveSubjectName()} | منارة التوجيهي`;
        }
    }

    function startFocusTimer() {
        const btn = document.getElementById('btnStartTimer');
        const statusLbl = document.getElementById('timerStatusLabel');
        const bannerStatus = document.getElementById('bannerStatusTag');

        if (!isRunning) {
            isRunning = true;
            btn.innerHTML = '<i class="fa-solid fa-pause"></i> إيقاف مؤقت';
            btn.classList.add('paused');

            const activeLabel = currentSessionType === 'break' ? 'استراحة منعشة ☕' : 'تركيز تام مستمر 🎯';
            statusLbl.innerText = activeLabel;
            bannerStatus.innerText = activeLabel;

            startAmbienceSound();

            timerInterval = setInterval(() => {
                remainingSeconds--;
                updateTimerDisplay();

                if (remainingSeconds <= 0) {
                    completeSession();
                }
            }, 1000);
        } else {
            pauseFocusTimer();
        }
    }

    function pauseFocusTimer() {
        isRunning = false;
        clearInterval(timerInterval);
        stopAmbienceSound();

        const btn = document.getElementById('btnStartTimer');
        btn.innerHTML = '<i class="fa-solid fa-play"></i> استئناف';
        btn.classList.remove('paused');
        document.getElementById('timerStatusLabel').innerText = 'متوقف مؤقتاً ⏸';
        document.getElementById('bannerStatusTag').innerText = 'متوقف مؤقتاً';
        document.title = 'أوسمة الإنجاز والشهادات المعتمدة | منارة التوجيهي';
    }

    function resetFocusTimer() {
        isRunning = false;
        clearInterval(timerInterval);
        stopAmbienceSound();

        remainingSeconds = totalSeconds;
        updateTimerDisplay();

        const btn = document.getElementById('btnStartTimer');
        btn.innerHTML = '<i class="fa-solid fa-play"></i> بدء الجلسة';
        btn.classList.remove('paused');
        document.getElementById('timerStatusLabel').innerText = 'جاهز للبدء';
        document.getElementById('bannerStatusTag').innerText = 'جاهز للانطلاق';
        document.title = 'أوسمة الإنجاز والشهادات المعتمدة | منارة التوجيهي';
    }

    function finishSessionEarly() {
        if (remainingSeconds >= totalSeconds) {
            Swal.fire({
                icon: 'info',
                title: 'لم تبدأ الجلسة بعد',
                text: 'اضغط على زر "بدء الجلسة" أولاً لبدء وقت التركيز.',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        Swal.fire({
            title: 'هل تريد إنهاء الجلسة وحفظ إنجازك الآن؟',
            text: `أمضيت جزءاً مفيداً من وقتك وسيتم حفظ الدقائق المنجزة في رصيدك!`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، احفظ إنجازي',
            cancelButtonText: 'إلغاء والمتابعة'
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
        stopAmbienceSound();

        const btn = document.getElementById('btnStartTimer');
        btn.innerHTML = '<i class="fa-solid fa-play"></i> بدء الجلسة';
        btn.classList.remove('paused');
        document.getElementById('timerStatusLabel').innerText = 'اكتملت الجلسة! 🎉';
        document.getElementById('bannerStatusTag').innerText = 'تم الإنجاز بنجاح 🏆';
        document.title = 'اكتملت الجلسة! 🎉 | منارة التوجيهي';

        // نغمة نجاح احتفالية
        playSuccessFanfare();

        // حفظ الجلسة
        saveSessionRecord(timerDurationMinutes);
    }

    function saveSessionRecord(minutes) {
        const subject = getActiveSubjectName();
        const goal = getActiveGoalText();
        const tasks = getTasks();
        const completedTasksCount = tasks.filter(t => t.completed).length;

        // تسجيل في قاعدة البيانات عبر السيرفر
        axios.post('{{ route("student.pomodoro.save") }}', {
            minutes: minutes,
            subject_name: subject,
            task_goal: goal,
            tasks_completed: completedTasksCount,
            _token: '{{ csrf_token() }}'
        }).then(res => {
            // تحديث إحصائيات المتصفح لليوم
            recordLocalSessionStats(minutes);

            Swal.fire({
                icon: 'success',
                title: 'بطل التوجيهي المتميز! 👏',
                html: `
                    <div style="font-size: 1rem; line-height: 1.8; color: #334155; text-align: center;">
                        <p style="margin-bottom: 10px;">${res.data.message || 'أتممت جلسة دراسية بنجاح فائق!'}</p>
                        <div style="background: #f1f5f9; padding: 12px; border-radius: 12px; display: inline-block;">
                            📖 المادة: <strong>${subject}</strong><br>
                            ⏱️ المدة المسجلة: <strong>${minutes} دقيقة</strong><br>
                            🔥 رصيد أيام الالتزام: <strong>${res.data.streak || 1} أيام</strong>
                        </div>
                    </div>
                `,
                confirmButtonText: 'متابعة بطلة'
            });

            resetFocusTimer();
        }).catch(() => {
            // حفظ محلي في حال وجود انقطاع مؤقت
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

    // ------------------------------------------
    // إحصائيات اليوم المخزنة محلياً
    // ------------------------------------------
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

        document.getElementById('statTodayMinutes').innerText = stats.minutes;
        document.getElementById('statTodaySessions').innerText = stats.sessions;
    }

    function saveStudyData() {
        const subject = document.getElementById('studySubjectSelect').value;
        const customSubject = document.getElementById('customSubjectInput').value;
        const goal = document.getElementById('sessionGoalInput').value;

        localStorage.setItem('tawjihi_saved_subject', subject);
        localStorage.setItem('tawjihi_saved_custom_subject', customSubject);
        localStorage.setItem('tawjihi_saved_goal', goal);
    }

    function loadSavedStudyData() {
        const savedSubject = localStorage.getItem('tawjihi_saved_subject');
        const savedCustomSubject = localStorage.getItem('tawjihi_saved_custom_subject');
        const savedGoal = localStorage.getItem('tawjihi_saved_goal');

        if (savedSubject) {
            const select = document.getElementById('studySubjectSelect');
            if (select) {
                select.value = savedSubject;
                if (savedSubject === 'custom') {
                    const customInput = document.getElementById('customSubjectInput');
                    customInput.classList.remove('d-none');
                    if (savedCustomSubject) customInput.value = savedCustomSubject;
                }
            }
        }

        if (savedGoal) {
            const goalInput = document.getElementById('sessionGoalInput');
            if (goalInput) goalInput.value = savedGoal;
        }

        updateActiveGoalDisplay();
    }

    // ------------------------------------------
    // محاكي الأصوات الهادئة للتركيز (Web Audio API)
    // ------------------------------------------
    function setSoundMode(mode) {
        currentSoundMode = mode;
        document.querySelectorAll('.btn-sound-mode').forEach(b => b.classList.remove('active'));
        const activeBtn = document.getElementById('btnSound' + mode.charAt(0).toUpperCase() + mode.slice(1));
        if (activeBtn) activeBtn.classList.add('active');

        if (isRunning) {
            stopAmbienceSound();
            startAmbienceSound();
        }
    }

    function getAudioCtx() {
        if (!audioContext) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioContext.state === 'suspended') {
            audioContext.resume();
        }
        return audioContext;
    }

    function startAmbienceSound() {
        if (currentSoundMode === 'none') return;
        const ctx = getAudioCtx();

        if (currentSoundMode === 'tick') {
            // دقات ساعة هادئة كل ثانية
            tickInterval = setInterval(() => {
                try {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.frequency.setValueAtTime(800, ctx.currentTime);
                    gain.gain.setValueAtTime(0.04, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.05);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.05);
                } catch(e) {}
            }, 1000);
        } else if (currentSoundMode === 'rain') {
            // محاكاة صوت مطر هادئ بالضجيج الوردي
            try {
                const bufferSize = ctx.sampleRate * 2;
                const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
                const data = buffer.getChannelData(0);
                let b0 = 0, b1 = 0, b2 = 0;
                for (let i = 0; i < bufferSize; i++) {
                    const white = Math.random() * 2 - 1;
                    b0 = 0.99886 * b0 + white * 0.0555179;
                    b1 = 0.99332 * b1 + white * 0.0750759;
                    b2 = 0.96900 * b2 + white * 0.1538520;
                    data[i] = (b0 + b1 + b2) * 0.06;
                }
                const noise = ctx.createBufferSource();
                noise.buffer = buffer;
                noise.loop = true;

                const filter = ctx.createBiquadFilter();
                filter.type = 'lowpass';
                filter.frequency.value = 850;

                const gain = ctx.createGain();
                gain.gain.value = 0.08;

                noise.connect(filter);
                filter.connect(gain);
                gain.connect(ctx.destination);

                noise.start();
                ambientSourceNode = noise;
            } catch(e) {}
        }
    }

    function stopAmbienceSound() {
        if (tickInterval) {
            clearInterval(tickInterval);
            tickInterval = null;
        }
        if (ambientSourceNode) {
            try {
                ambientSourceNode.stop();
                ambientSourceNode.disconnect();
            } catch(e) {}
            ambientSourceNode = null;
        }
    }

    function playSubtleChime() {
        try {
            const ctx = getAudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.setValueAtTime(659.25, ctx.currentTime); // E5
            gain.gain.setValueAtTime(0.08, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.18);
            osc.start();
            osc.stop(ctx.currentTime + 0.18);
        } catch(e) {}
    }

    function playSuccessFanfare() {
        try {
            const ctx = getAudioCtx();
            const notes = [523.25, 659.25, 783.99, 1046.50]; // C5, E5, G5, C6
            notes.forEach((freq, idx) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                const startTime = ctx.currentTime + (idx * 0.14);
                osc.frequency.setValueAtTime(freq, startTime);
                gain.gain.setValueAtTime(0.2, startTime);
                gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.7);
                osc.start(startTime);
                osc.stop(startTime + 0.7);
            });
        } catch(e) {}
    }

    // ------------------------------------------
    // وضع ملء الشاشة للتركيز (Zen Mode)
    // ------------------------------------------
    function toggleZenMode() {
        const card = document.getElementById('focusRoomCard');
        const icon = document.getElementById('zenIcon');
        const text = document.getElementById('zenText');

        card.classList.toggle('zen-fullscreen-mode');

        if (card.classList.contains('zen-fullscreen-mode')) {
            icon.className = 'fa-solid fa-compress';
            text.innerText = 'خروج من وضع التركيز';
            document.body.style.overflow = 'hidden';
        } else {
            icon.className = 'fa-solid fa-expand';
            text.innerText = 'وضع التركيز الكامل';
            document.body.style.overflow = 'auto';
        }
    }

    // إمكانية الخروج من وضع التركيز بضغط Esc
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const card = document.getElementById('focusRoomCard');
            if (card && card.classList.contains('zen-fullscreen-mode')) {
                toggleZenMode();
            }
        }
    });
</script>

<style>
    .achievements-page-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 50px;
    }

    /* الترويسة */
    .achievements-hero-card {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: white;
        padding: 35px 30px;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 25px;
        margin-bottom: 35px;
    }
    .hero-content-flex {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .hero-avatar {
        width: 75px;
        height: 75px;
        border-radius: 20px;
        background: linear-gradient(135deg, #d4af37, #996515);
        display: grid;
        place-items: center;
        font-size: 2.2rem;
        color: white;
        box-shadow: 0 8px 20px rgba(212, 175, 55, 0.35);
    }
    .hero-tag {
        background: rgba(212, 175, 55, 0.2);
        color: #fde047;
        font-size: 0.78rem;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 50px;
        display: inline-block;
        margin-bottom: 8px;
    }
    .hero-title {
        font-size: 1.7rem;
        font-weight: 900;
        margin: 0 0 6px;
    }
    .hero-subtitle {
        color: #94a3b8;
        font-size: 0.92rem;
        margin: 0;
    }

    .hero-stats-row {
        display: flex;
        gap: 15px;
    }
    .hero-stat-pill {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.12);
        padding: 12px 20px;
        border-radius: 16px;
        text-align: center;
    }
    .hero-stat-pill .num {
        display: block;
        font-size: 1.5rem;
        font-weight: 900;
        color: #f8fafc;
    }
    .hero-stat-pill .lbl {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .section-title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .section-title-bar h3 {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
    }
    .badge-count {
        background: #e0f2fe;
        color: #0284c7;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 800;
    }

    /* شبكة الشهادات الملكية */
    .certificates-royal-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
        margin-bottom: 45px;
    }
    .cert-royal-card {
        background: #ffffff;
        border-radius: 20px;
        border: 2px solid #fef08a;
        padding: 28px 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        text-align: center;
    }
    .cert-royal-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(212, 175, 55, 0.15);
        border-color: #d4af37;
    }
    .cert-gold-ribbon {
        position: absolute;
        top: 14px;
        right: -32px;
        transform: rotate(45deg);
        background: linear-gradient(135deg, #d4af37, #b8860b);
        color: white;
        font-size: 0.7rem;
        font-weight: 800;
        padding: 4px 35px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .cert-card-icon {
        font-size: 3.2rem;
        margin-bottom: 12px;
    }
    .cert-subject-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .cert-student-sub {
        font-size: 0.82rem;
        color: #64748b;
        margin-bottom: 16px;
    }
    .cert-grade-tag {
        display: inline-block;
        background: #ecfdf5;
        color: #059669;
        font-size: 0.85rem;
        font-weight: 700;
        padding: 5px 16px;
        border-radius: 50px;
        margin-bottom: 14px;
        border: 1px solid #a7f3d0;
    }
    .cert-grade-tag span {
        font-weight: 900;
        font-size: 1rem;
    }
    .cert-code-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        padding: 8px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .cert-code-box small {
        display: block;
        font-size: 0.72rem;
        color: #94a3b8;
    }
    .cert-code-box code {
        color: #0284c7;
        font-weight: 800;
        font-size: 0.88rem;
    }
    .cert-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    .btn-cert-view {
        flex: 1;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: white;
        text-decoration: none;
        padding: 10px 18px;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: 0.2s;
    }
    .btn-cert-view:hover {
        background: #0284c7;
        color: white;
        transform: translateY(-2px);
    }
    .btn-cert-verify {
        width: 44px;
        height: 44px;
        background: #f1f5f9;
        color: #475569;
        border-radius: 12px;
        display: grid;
        place-items: center;
        text-decoration: none;
        transition: 0.2s;
    }
    .btn-cert-verify:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* ===================================================
       غرفة تنظيم المذاكرة والتركيز الذهني (Study Organizer)
       =================================================== */
    .pomodoro-focus-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 28px;
        padding: 35px 32px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.05);
        margin-top: 25px;
        margin-bottom: 40px;
        position: relative;
        transition: all 0.3s ease;
    }

    /* ترويسة الغرفة */
    .focus-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 28px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .focus-title-wrap {
        max-width: 820px;
    }
    .focus-badge-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    .focus-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #e0f2fe;
        color: #0369a1;
        font-size: 0.8rem;
        font-weight: 800;
        padding: 5px 14px;
        border-radius: 50px;
    }
    .session-mode-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 800;
        padding: 5px 14px;
        border-radius: 50px;
        transition: 0.3s;
    }
    .session-mode-badge.study {
        background: #dbeafe;
        color: #1e40af;
    }
    .session-mode-badge.break {
        background: #dcfce7;
        color: #166534;
    }
    .focus-title-wrap h3 {
        font-size: 1.55rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .focus-title-wrap p {
        color: #64748b;
        font-size: 0.94rem;
        line-height: 1.7;
        margin: 0;
    }

    .btn-zen-mode {
        background: #0f172a;
        color: #f8fafc;
        border: none;
        padding: 10px 18px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
    }
    .btn-zen-mode:hover {
        background: #1e293b;
        transform: translateY(-2px);
    }

    /* تقسيم الغرفة لعمودين */
    .focus-main-grid {
        display: grid;
        grid-template-columns: 1.15fr 1fr;
        gap: 32px;
        align-items: start;
    }
    @media (max-width: 950px) {
        .focus-main-grid {
            grid-template-columns: 1fr;
        }
    }

    /* صناديق التخطيط (العمود الأيمن) */
    .planner-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px;
        margin-bottom: 18px;
    }
    .box-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .field-label {
        font-size: 0.9rem;
        font-weight: 800;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 8px;
    }
    .subject-select-row {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .form-select-custom, .form-input-custom {
        width: 100%;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 0.9rem;
        color: #0f172a;
        font-weight: 600;
        outline: none;
        transition: 0.2s;
    }
    .form-select-custom:focus, .form-input-custom:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
    }

    .quick-goal-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
    }
    .goal-tag-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.76rem;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        transition: 0.2s;
    }
    .goal-tag-pill:hover {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #bae6fd;
    }

    /* قائمة المهام (Checklist) */
    .tasks-counter-badge {
        background: #e2e8f0;
        color: #334155;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 50px;
    }
    .add-task-row {
        display: flex;
        gap: 8px;
    }
    .btn-add-task {
        background: #0284c7;
        color: white;
        border: none;
        padding: 0 16px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: 0.2s;
    }
    .btn-add-task:hover {
        background: #0369a1;
    }
    .tasks-checklist-list {
        max-height: 180px;
        overflow-y: auto;
        margin-top: 12px;
        display: flex;
        flex-direction: column;
        gap: 7px;
        padding-left: 4px;
    }
    .task-check-item {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 9px 12px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: 0.2s;
    }
    .task-checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        margin: 0;
        width: 100%;
    }
    .task-checkbox-label input {
        display: none;
    }
    .custom-chk {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.7rem;
        transition: 0.2s;
        flex-shrink: 0;
    }
    .task-check-item.completed {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }
    .task-check-item.completed .custom-chk {
        background: #10b981;
        border-color: #10b981;
    }
    .task-check-item.completed .task-text {
        text-decoration: line-through;
        color: #94a3b8;
    }
    .task-text {
        font-size: 0.88rem;
        font-weight: 600;
        color: #1e293b;
        word-break: break-word;
    }
    .btn-del-task {
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px 6px;
        border-radius: 6px;
        transition: 0.2s;
    }
    .btn-del-task:hover {
        color: #ef4444;
        background: #fee2e2;
    }
    .empty-tasks-hint {
        text-align: center;
        padding: 20px 10px;
        color: #94a3b8;
        font-size: 0.84rem;
        font-weight: 600;
    }
    .tasks-footer-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        padding-top: 8px;
        border-top: 1px dashed #e2e8f0;
    }
    .btn-clean-tasks {
        background: none;
        border: none;
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-clean-tasks:hover {
        color: #0f172a;
    }

    /* شريط إحصائيات اليوم */
    .today-stats-strip {
        background: #f1f5f9;
        border-radius: 16px;
        padding: 12px 18px;
        display: flex;
        justify-content: space-around;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .stat-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .stat-cell i {
        font-size: 1.4rem;
    }
    .stat-cell strong {
        font-size: 1.05rem;
        font-weight: 900;
        color: #0f172a;
    }
    .stat-cell small {
        display: block;
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 700;
    }

    /* ===================================================
       العمود الأيسر: محرك المؤقت والأصوات
       =================================================== */
    .timer-engine-column {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        padding: 25px 22px;
    }

    /* بطاقة الهدف والمساق النشط */
    .active-session-banner {
        width: 100%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 14px 18px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .banner-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }
    .banner-subject {
        font-size: 0.85rem;
        font-weight: 800;
        color: #0284c7;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .banner-status-tag {
        font-size: 0.72rem;
        font-weight: 800;
        background: #f1f5f9;
        color: #475569;
        padding: 3px 8px;
        border-radius: 6px;
    }
    .banner-goal {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 10px;
        line-height: 1.4;
    }
    .session-progress-bar-wrap {
        width: 100%;
        height: 6px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .session-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #0284c7, #10b981);
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    /* المؤقت الدائري SVG */
    .timer-dial-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
        position: relative;
    }
    .svg-dial-container {
        position: relative;
        width: 220px;
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .timer-svg {
        transform: rotate(-90deg);
    }
    .timer-bg-circle {
        fill: none;
        stroke: #e2e8f0;
    }
    .timer-progress-circle {
        fill: none;
        stroke: #0284c7;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.8s ease, stroke 0.4s ease;
    }
    .dial-center-content {
        position: absolute;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .timer-digits {
        font-size: 3.1rem;
        font-weight: 900;
        color: #0f172a;
        font-family: 'Consolas', 'Courier New', monospace;
        letter-spacing: -1.5px;
        line-height: 1;
    }
    .timer-state-label {
        font-size: 0.82rem;
        color: #64748b;
        font-weight: 800;
        margin-top: 6px;
    }
    .timer-percent-badge {
        font-size: 0.72rem;
        background: #e2e8f0;
        color: #334155;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 50px;
        margin-top: 6px;
    }

    /* أزرار التعديل الفوري للوقت */
    .time-adjusters-row {
        display: flex;
        gap: 8px;
        margin-top: 14px;
    }
    .btn-adjust-chip {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 800;
        color: #334155;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: 0.2s;
    }
    .btn-adjust-chip:hover {
        background: #0284c7;
        color: white;
        border-color: #0284c7;
    }
    .btn-adjust-chip.minus:hover {
        background: #ef4444;
        border-color: #ef4444;
    }

    /* أزرار التحكم الرئيسية بالمؤقت */
    .main-timer-controls {
        display: flex;
        gap: 10px;
        margin-bottom: 22px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .btn-timer-ctrl {
        padding: 11px 22px;
        border-radius: 12px;
        font-size: 0.92rem;
        font-weight: 800;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-timer-ctrl.play {
        background: #10b981;
        color: white;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
    }
    .btn-timer-ctrl.play.paused {
        background: #f59e0b;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.25);
    }
    .btn-timer-ctrl.reset {
        background: #e2e8f0;
        color: #475569;
    }
    .btn-timer-ctrl.finish {
        background: #3b82f6;
        color: white;
    }
    .btn-timer-ctrl:hover {
        transform: translateY(-2px);
    }

    /* تبويبات وخيارات الوقت */
    .time-presets-master {
        width: 100%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 16px;
    }
    .preset-section-title {
        font-size: 0.82rem;
        font-weight: 800;
        color: #475569;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .preset-buttons-lane {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-bottom: 12px;
    }
    .btn-preset {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        color: #334155;
        padding: 7px 13px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 800;
        cursor: pointer;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-preset:hover, .btn-preset.active {
        background: #0284c7;
        color: white;
        border-color: #0284c7;
    }
    .btn-preset.break:hover, .btn-preset.break.active {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }

    /* حقل إدخال مدة مخصصة */
    .custom-time-picker-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 10px;
        border-top: 1px dashed #e2e8f0;
        flex-wrap: wrap;
    }
    .custom-time-label {
        font-size: 0.82rem;
        font-weight: 800;
        color: #334155;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .custom-input-with-btn {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .input-custom-mins {
        width: 75px;
        padding: 6px 10px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        font-size: 0.88rem;
        font-weight: 800;
        color: #0f172a;
        text-align: center;
        outline: none;
    }
    .input-custom-mins:focus {
        border-color: #0284c7;
    }
    .mins-unit {
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748b;
    }
    .btn-apply-custom-time {
        background: #0f172a;
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: 0.2s;
    }
    .btn-apply-custom-time:hover {
        background: #1e293b;
    }

    /* التحكم بأجواء الصوت */
    .ambience-control-box {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        padding: 8px 4px;
    }
    .ambience-label {
        font-size: 0.82rem;
        font-weight: 800;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ambience-buttons {
        display: flex;
        gap: 6px;
    }
    .btn-sound-mode {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        font-size: 0.76rem;
        font-weight: 700;
        padding: 5px 11px;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: 0.2s;
    }
    .btn-sound-mode:hover, .btn-sound-mode.active {
        background: #0f172a;
        color: white;
        border-color: #0f172a;
    }

    /* ===================================================
       وضع ملء الشاشة للتركيز الشديد (Zen Focus Mode)
       =================================================== */
    .pomodoro-focus-card.zen-fullscreen-mode {
        position: fixed;
        inset: 0;
        z-index: 99999;
        width: 100vw;
        height: 100vh;
        margin: 0;
        border-radius: 0;
        background: #090d16;
        color: #f8fafc;
        padding: 30px 40px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    .pomodoro-focus-card.zen-fullscreen-mode .focus-card-header {
        border-color: #1e293b;
    }
    .pomodoro-focus-card.zen-fullscreen-mode .focus-title-wrap h3 {
        color: #f8fafc;
    }
    .pomodoro-focus-card.zen-fullscreen-mode .focus-title-wrap p {
        color: #94a3b8;
    }
    .pomodoro-focus-card.zen-fullscreen-mode .planner-box,
    .pomodoro-focus-card.zen-fullscreen-mode .timer-engine-column {
        background: #111827;
        border-color: #1f2937;
    }
    .pomodoro-focus-card.zen-fullscreen-mode .field-label {
        color: #e2e8f0;
    }
    .pomodoro-focus-card.zen-fullscreen-mode .form-select-custom,
    .pomodoro-focus-card.zen-fullscreen-mode .form-input-custom,
    .pomodoro-focus-card.zen-fullscreen-mode .active-session-banner,
    .pomodoro-focus-card.zen-fullscreen-mode .time-presets-master,
    .pomodoro-focus-card.zen-fullscreen-mode .task-check-item {
        background: #1f2937;
        border-color: #374151;
        color: #f9fafb;
    }
    .pomodoro-focus-card.zen-fullscreen-mode .timer-digits {
        color: #ffffff;
    }
    .pomodoro-focus-card.zen-fullscreen-mode .banner-goal,
    .pomodoro-focus-card.zen-fullscreen-mode .task-text {
        color: #f3f4f6;
    }
    .pomodoro-focus-card.zen-fullscreen-mode .today-stats-strip {
        background: #1f2937;
    }
    .pomodoro-focus-card.zen-fullscreen-mode .stat-cell strong {
        color: #ffffff;
    }
    .pomodoro-focus-card.zen-fullscreen-mode .timer-bg-circle {
        stroke: #1f2937;
    }

    .empty-certs-card {
        background: white;
        border-radius: 20px;
        border: 2px dashed #cbd5e1;
        padding: 45px 20px;
        text-align: center;
        margin-bottom: 35px;
    }
    .empty-certs-card .empty-icon {
        font-size: 3.5rem;
        color: #cbd5e1;
        margin-bottom: 15px;
    }
    .empty-certs-card h4 {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .empty-certs-card p {
        color: #64748b;
        font-size: 0.88rem;
        margin-bottom: 20px;
    }
    .btn-primary-action {
        background: #0284c7;
        color: white;
        text-decoration: none;
        padding: 10px 24px;
        border-radius: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
</style>
@endsection
