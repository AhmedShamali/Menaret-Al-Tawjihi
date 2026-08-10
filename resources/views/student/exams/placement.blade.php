@extends('layouts.app')

@section('content')
<!-- خطوط احترافية -->
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        --accent-gold: #fbbf24;
        --glass-bg: rgba(255, 255, 255, 0.95);
        --text-main: #1e293b;
        --success: #10b981;
        --danger: #ef4444;
        --radius: 20px;
    }

    body {
        font-family: 'Tajawal', sans-serif;
        background: #f1f5f9;
        background-image: radial-gradient(at 0% 0%, rgba(30, 58, 138, 0.05) 0px, transparent 50%),
                          radial-gradient(at 100% 100%, rgba(30, 58, 138, 0.05) 0px, transparent 50%);
        min-height: 100vh;
        color: var(--text-main);
        direction: rtl;
    }

    /* Container */
    .premium-wrapper {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    /* Header Styling */
    .main-header {
        background: var(--primary-gradient);
        border-radius: var(--radius);
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }

    .main-header::after {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    /* Course Grid */
    .course-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }

    .premium-card {
        background: white;
        border-radius: var(--radius);
        padding: 30px;
        border: 1px solid rgba(255,255,255,0.3);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        position: relative;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }

    .premium-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
    }

    .premium-card i {
        font-size: 2.5rem;
        margin-bottom: 20px;
        display: block;
    }

    /* Quiz Layout */
    .quiz-container {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 30px;
    }

    @media (max-width: 992px) {
        .quiz-container { grid-template-columns: 1fr; }
    }

    .question-box {
        background: white;
        border-radius: var(--radius);
        padding: 40px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }

    /* Options Styling */
    .option-item {
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 15px;
        padding: 20px 25px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 15px;
        font-weight: 600;
    }

    .option-item:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .option-item.active {
        background: #eff6ff;
        border-color: #3b82f6;
        color: #1e3a8a;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
    }

    .option-badge {
        width: 35px;
        height: 35px;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        transition: 0.3s;
    }

    .option-item.active .option-badge {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    /* Sidebar Palette */
    .sidebar-panel {
        background: white;
        border-radius: var(--radius);
        padding: 25px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
        height: fit-content;
        position: sticky;
        top: 20px;
    }

    .q-dot {
        width: 100%;
        aspect-ratio: 1;
        border-radius: 12px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
    }

    .q-dot.done { background: #10b981; color: white; border-color: #10b981; }
    .q-dot.current { border: 3px solid #3b82f6; color: #3b82f6; }
    .q-dot.flagged { background: #fbbf24; color: white; border-color: #fbbf24; }

    /* Timer & Progress */
    .stat-card {
        background: #fff1f2;
        padding: 15px;
        border-radius: 15px;
        text-align: center;
        margin-bottom: 20px;
    }

    .timer-text {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        color: #be123c;
    }

    /* Buttons */
    .btn-premium {
        padding: 15px 35px;
        border-radius: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-main { background: var(--primary-gradient); color: white; }
    .btn-main:hover { opacity: 0.9; transform: scale(1.02); }

    .btn-finish {
        background: #10b981;
        color: white;
        width: 100%;
        margin-top: 20px;
        font-size: 1.1rem;
    }

    .progress-bar-wrap {
        height: 8px;
        background: #e2e8f0;
        border-radius: 10px;
        margin-top: 10px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background: #10b981;
        width: 0%;
        transition: 0.4s;
    }
</style>

<div class="premium-wrapper">

    <!-- View 1: Portal -->
    <div id="selection_view">
        <header class="main-header">
            <h1 style="font-weight: 900; font-size: 2.5rem; margin-bottom: 10px;">المنصة الأكاديمية الذكية ✨</h1>
            <p style="opacity: 0.8; font-size: 1.1rem;">أهلاً بك في نظام التقييم المطور. يرجى اختيار المادة لبدء الاختبار التخصصي.</p>
        </header>

        <div class="course-grid">
            @php
            $courses = [
                ['id'=>'arabic', 'name'=>'لغة الضاد وعلوم العرب', 'icon'=>'📜', 'code'=>'ARA-101'],
                ['id'=>'math', 'name'=>'المنطق الرياضي المتقدم', 'icon'=>'📐', 'code'=>'MTH-202'],
                ['id'=>'english', 'name'=>'Academic English Elite', 'icon'=>'🌍', 'code'=>'ENG-101'],
                ['id'=>'science', 'name'=>'العلوم والفيزياء الكونية', 'icon'=>'⚛️', 'code'=>'SCI-300'],
                ['id'=>'islamic', 'name'=>'الفكر والحضارة الإسلامية', 'icon'=>'🌙', 'code'=>'ISL-101'],
            ];
            @endphp

            @foreach($courses as $c)
            <div class="premium-card" onclick="startFlow('{{ $c['id'] }}', '{{ $c['name'] }}', '{{ $c['code'] }}')">
                <span style="font-size: 3rem;">{{ $c['icon'] }}</span>
                <div style="margin-top: 20px;">
                    <span style="color: #64748b; font-weight: 800; font-size: 0.8rem;">{{ $c['code'] }}</span>
                    <h3 style="margin: 5px 0; font-weight: 800;">{{ $c['name'] }}</h3>
                    <div style="margin-top: 15px; font-size: 0.9rem; color: #94a3b8;">
                        ⏱️ 60 دقيقة | 📝 50 سؤال
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- View 2: Quiz -->
    <div id="quiz_view" style="display: none;">
        <div class="quiz-container">
            <main>
                <div class="question-box">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px;">
                        <div>
                            <span id="course_code_display" style="background: #eff6ff; color: #3b82f6; padding: 5px 12px; border-radius: 8px; font-weight: 800; font-size: 0.8rem;"></span>
                            <h2 id="quiz_title_display" style="margin: 5px 0; font-weight: 900;"></h2>
                        </div>
                        <button onclick="toggleFlag()" class="btn-premium" style="background: #fef3c7; color: #d97706;">🚩 مراجعة لاحقاً</button>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <span style="font-weight: 800; color: #64748b;">السؤال <span id="q_num" style="color: #1e3a8a;">1</span> / 50</span>
                    </div>

                    <h3 id="q_text" style="font-size: 1.6rem; line-height: 1.5; margin-bottom: 40px; color: #0f172a;"></h3>

                    <div id="options_wrap">
                        <!-- Options via JS -->
                    </div>

                    <div style="margin-top: 50px; display: flex; justify-content: space-between;">
                        <button onclick="move(-1)" class="btn-premium" style="background: #f1f5f9; color: #475569;">السابق</button>
                        <button onclick="move(1)" class="btn-premium btn-main">السؤال التالي ⬅️</button>
                    </div>
                </div>
            </main>

            <aside>
                <div class="sidebar-panel">
                    <div class="stat-card">
                        <div style="font-size: 0.8rem; font-weight: 800; color: #991b1b; margin-bottom: 5px;">الوقت المتبقي</div>
                        <div id="timer" class="timer-text">60:00</div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 0.85rem; margin-bottom: 5px;">
                            <span>الإنجاز الكلي</span>
                            <span id="progress_pct">0%</span>
                        </div>
                        <div class="progress-bar-wrap">
                            <div id="progress_fill" class="progress-bar-fill"></div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px;" id="palette">
                        <!-- Dots via JS -->
                    </div>

                    <button onclick="checkAndFinish()" class="btn-premium btn-finish">إرسال الورقة النهائية ✅</button>
                </div>
            </aside>
        </div>
    </div>

    <!-- View 3: Result -->
    <div id="result_view" style="display: none;">
        <div class="question-box" style="text-align: center; max-width: 700px; margin: 0 auto;">
            <div style="font-size: 5rem;">🏆</div>
            <h1 style="font-weight: 900; color: #1e3a8a;">اكتمل التقييم بنجاح</h1>
            <p style="color: #64748b; margin-bottom: 30px;">لقد تم تحليل أدائك الأكاديمي، إليك النتيجة الرسمية:</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f8fafc; padding: 30px; border-radius: 20px; border: 1px dashed #cbd5e1;">
                <div>
                    <div style="color: #94a3b8; font-weight: 700;">النسبة المئوية</div>
                    <div id="final_pct" style="font-size: 3rem; font-weight: 900; color: #1e3a8a;">0%</div>
                </div>
                <div>
                    <div style="color: #94a3b8; font-weight: 700;">التقدير</div>
                    <div id="final_grade" style="font-size: 3rem; font-weight: 900; color: #10b981;">-</div>
                </div>
            </div>

            <button onclick="location.reload()" class="btn-premium btn-main" style="margin-top: 30px;">العودة للبوابة الأكاديمية</button>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    let currentQuestions = [];
    let currentIdx = 0;
    let answers = [];
    let flags = [];
    let timeLeft = 3600;
    let timerInt;
    let meta = { sub: '', code: '' };

    // رسالة البدء الاحترافية
    function startFlow(id, name, code) {
        Swal.fire({
            title: `تأكيد الدخول: ${name}`,
            html: `
                <div style="text-align: right; padding: 10px;">
                    <p>أنت على وشك بدء اختبار <b>${code}</b>. يرجى الانتباه للتعليمات التالية:</p>
                    <ul style="line-height: 2;">
                        <li>مدة الاختبار <b>60 دقيقة</b> ولن يُسمح بالتجاوز.</li>
                        <li><b>ممنوع التسليم</b> إلا بعد الإجابة على كافة الأسئلة.</li>
                        <li>سيتم تسجيل عنوان IP الخاص بك لدواعي الأمان الأكاديمي.</li>
                    </ul>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'ابدأ الرحلة الأكاديمية',
            cancelButtonText: 'تراجع',
            confirmButtonColor: '#1e3a8a',
            cancelButtonColor: '#94a3b8',
            borderRadius: '20px'
        }).then((res) => {
            if(res.isConfirmed) initQuiz(id, name, code);
        });
    }

    function initQuiz(id, name, code) {
        meta.sub = name; meta.code = code;
        currentQuestions = BANK[id];
        answers = new Array(currentQuestions.length).fill(null);
        flags = new Array(currentQuestions.length).fill(false);

        document.getElementById('selection_view').style.display = 'none';
        document.getElementById('quiz_view').style.display = 'block';
        document.getElementById('quiz_title_display').innerText = name;
        document.getElementById('course_code_display').innerText = code;

        render();
        startTimer();
    }

    function startTimer() {
        timerInt = setInterval(() => {
            let m = Math.floor(timeLeft / 60);
            let s = timeLeft % 60;
            document.getElementById('timer').innerText = `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
            if (timeLeft <= 0) { clearInterval(timerInt); forceSubmit(); }
            timeLeft--;
        }, 1000);
    }

    function render() {
        const q = currentQuestions[currentIdx];
        document.getElementById('q_num').innerText = currentIdx + 1;
        document.getElementById('q_text').innerText = q.q;

        // Progress
        const answeredCount = answers.filter(a => a !== null).length;
        const p = Math.round((answeredCount / currentQuestions.length) * 100);
        document.getElementById('progress_pct').innerText = p + '%';
        document.getElementById('progress_fill').style.width = p + '%';

        // Options
        const wrap = document.getElementById('options_wrap');
        wrap.innerHTML = '';
        const labels = ['أ', 'ب', 'جـ', 'د'];
        q.o.forEach((opt, i) => {
            const div = document.createElement('div');
            div.className = `option-item ${answers[currentIdx] === i ? 'active' : ''}`;
            div.innerHTML = `<div class="option-badge">${labels[i]}</div> <span>${opt}</span>`;
            div.onclick = () => { answers[currentIdx] = i; render(); };
            wrap.appendChild(div);
        });

        // Palette
        const pal = document.getElementById('palette');
        pal.innerHTML = '';
        currentQuestions.forEach((_, i) => {
            const dot = document.createElement('div');
            dot.className = `q-dot ${answers[i] !== null ? 'done' : ''} ${i === currentIdx ? 'current' : ''} ${flags[i] ? 'flagged' : ''}`;
            dot.innerText = i + 1;
            dot.onclick = () => { currentIdx = i; render(); };
            pal.appendChild(dot);
        });
    }

    function move(step) {
        if(currentIdx + step >= 0 && currentIdx + step < currentQuestions.length) {
            currentIdx += step;
            render();
        }
    }

    function toggleFlag() {
        flags[currentIdx] = !flags[currentIdx];
        render();
    }

    // المنع من التسليم إلا بالحل الكامل
    function checkAndFinish() {
        const missing = answers.filter(a => a === null).length;

        if(missing > 0) {
            Swal.fire({
                title: 'عذراً، إجابات ناقصة!',
                text: `لديك ${missing} سؤالاً لم يتم حلها بعد. النظام الأكاديمي يمنع تسليم الورقة ناقصة.`,
                icon: 'error',
                confirmButtonText: 'الرجوع للحل',
                confirmButtonColor: '#ef4444'
            });
        } else {
            Swal.fire({
                title: 'هل أنت متأكد من التسليم؟',
                text: "سيتم إغلاق الاختبار فوراً وحساب النتيجة.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، سلم الآن',
                cancelButtonText: 'مراجعة الأسئلة',
                confirmButtonColor: '#10b981'
            }).then(r => { if(r.isConfirmed) processResult(); });
        }
    }

    function forceSubmit() {
        Swal.fire('انتهى الوقت!', 'سيتم تسليم ما قمت بحله تلقائياً.', 'warning').then(processResult);
    }

    function processResult() {
        clearInterval(timerInt);
        let score = 0;
        currentQuestions.forEach((q, i) => { if(answers[i] === q.c) score++; });
        const pct = Math.round((score / currentQuestions.length) * 100);

        // إرسال البيانات (مثال)
        axios.post("{{ route('placement.store') }}", {
            subject: meta.sub,
            score: score,
            percentage: pct
        }).then(() => {
            document.getElementById('quiz_view').style.display = 'none';
            document.getElementById('result_view').style.display = 'block';
            document.getElementById('final_pct').innerText = pct + '%';

            let grade = pct >= 90 ? 'A+' : (pct >= 80 ? 'B' : (pct >= 70 ? 'C' : 'D'));
            document.getElementById('final_grade').innerText = grade;
        });
    }

    // ملاحظة: BANK يجب أن يكون بنفس التنسيق السابق
    const BANK = {
        arabic: [
            {q:"الكلمة التي تدل على حدث مقترن بزمن هي:", o:["الاسم","الفعل","الحرف","الصفة"], c:1},
            {q:"مبتدأ الجملة الاسمية يكون دائماً:", o:["منصوباً","مرفوعاً","مجروراً","مجزوماً"], c:1},
            {q:"'كان' وأخواتها تدخل على الجملة الاسمية فـ:", o:["ترفع المبتدأ وتنصب الخبر","تنصب المبتدأ وترفع الخبر","تنصب الاثنين","ترفع الاثنين"], c:0},
            {q:"جمع كلمة 'قاضي' هو:", o:["قاضون","قضاة","قاضيين","قواضي"], c:1},
            {q:"الفعل 'يأكلون' من الأفعال الخمسة ويرفع بـ:", o:["الضمة","ثبوت النون","الواو","الألف"], c:1},
            {q:"كلمة 'استغفار' همزتها همزة:", o:["قطع","وصل","مد","متطرفة"], c:1},
            {q:"الأسماء الخمسة ترفع بـ:", o:["الضمة","الألف","الواو","الياء"], c:2},
            {q:"إعراب 'المسلمان' في 'نجح المسلمان':", o:["فاعل مرفوع بالضمة","فاعل مرفوع بالألف","مبتدأ","مفعول به"], c:1},
            {q:"نوع الخبر في 'العلم نوره ساطع':", o:["مفرد","جملة فعلية","جملة اسمية","شبه جملة"], c:2},
            {q:"أداة الجزم 'لم' تجزم الفعل:", o:["الماضي","المضارع","الأمر","المبني"], c:1},
            {q:"كلمة 'سماء' تنتهي بـ:", o:["همزة متوسطة","همزة متطرفة","تاء مفتوحة","ألف مقصورة"], c:1},
            {q:"المثنى يجر بـ:", o:["الكسرة","الياء","الفتحة","الألف"], c:1},
            {q:"'إن' وأخواتها تسمى حروفاً:", o:["جارة","جازمة","ناسخة","عطف"], c:2},
            {q:"الاسم الذي يأتي بعد حرف الجر يكون:", o:["مرفوعاً","منصوباً","مجروراً","مبنياً"], c:2},
            {q:"جمع المذكر السالم ينتهي بـ:", o:["ات","ان","ون أو ين","ى"], c:2},
            {q:"الفعل 'سعى' ينتهي بـ:", o:["ألف ممدودة","ألف مقصورة","ياء","واو"], c:1},
            {q:"صاحب معلقة 'قفا نبكِ' هو:", o:["عنترة","امرؤ القيس","زهير","لبيد"], c:1},
            {q:"'لعل' تفيد:", o:["التمني","الترجي","التوكيد","التشبيه"], c:1},
            {q:"الظرف الذي يدل على المكان:", o:["فوق","صباحاً","أمس","غداً"], c:0},
            {q:"الفعل الأمر يبنى دائماً على:", o:["السكون","الفتح","الضم","الكسر"], c:0},
            {q:"كلمة 'أحمد' ممنوعة من الصرف لأنها:", o:["اسم علم على وزن الفعل","صفة","اسم أعجمي","جمع تكسير"], c:0},
            {q:"'ما' في 'ما أجمل السماء!' هي:", o:["استفهامية","تعجبية","نافية","موصلة"], c:1},
            {q:"نائب الفاعل يكون دائماً:", o:["مرفوعاً","منصوباً","مجروراً","ساكناً"], c:0},
            {q:"المفعول لأجله جواب لسؤال يبدأ بـ:", o:["كيف","متى","لماذا","أين"], c:2},
            {q:"حرف العطف الذي يفيد الترتيب والتعقيب:", o:["و","ثم","فـ","أو"], c:2},
            {q:"الوزن الصرفي لكلمة 'استخرج':", o:["افعل","استفعل","انفعل","تفاعل"], c:1},
            {q:"'يا محمدُ' منادى نوعه:", o:["مضاف","شبيه بالمضاف","علم مفرد","نكرة مقصودة"], c:2},
            {q:"الفعل 'وعد' هو فعل:", o:["مثال","أجوف","ناقص","لفيف"], c:0},
            {q:"الجملة التي لها محل من الإعراب:", o:["الابتدائية","صلة الموصول","خبر المبتدأ","التفسيرية"], c:2},
            {q:"'كم' الخبرية تفيد:", o:["الاستفهام","الكثرة","التعجب","النفي"], c:1},
            {q:"المصدر من الفعل 'انطلق':", o:["نطاق","انطلاق","ناطق","منطلق"], c:1},
            {q:"نوع التوابع في 'جاء القائدُ نفسُه':", o:["نعت","بدل","توكيد","عطف"], c:2},
            {q:"كلمة 'مساجد' تمنع من الصرف لأنها:", o:["اسم علم","صيغة منتهى الجموع","وصف","اسم مؤنث"], c:1},
            {q:"الفعل الماضي يبنى على الفتح إذا:", o:["اتصلت به واو الجماعة","اتصلت به نون النسوة","لم يتصل به شيء","اتصلت به تاء الفاعل"], c:2},
            {q:"أدوات النصب للفعل المضارع منها:", o:["لم","أن","لا الناهية","إن"], c:1},
            {q:"الهمزة في كلمة 'لؤلؤ' كتبت على واو لأنها:", o:["مفتوحة","ساكنة وما قبلها مضموم","مكسورة","مضمومة"], c:1},
            {q:"العدد 'ثلاثة' يخالف المعدود في:", o:["التذكير والتأنيث","الإعراب","التعريف","الجمع"], c:0},
            {q:"اسم الفاعل من الفعل 'كتب':", o:["مكتوب","كاتب","كتابة","يُكتب"], c:1},
            {q:"اسم المكان من 'لعب':", o:["لعيب","ملعب","لاعب","ملعوب"], c:1},
            {q:"الجملة الفعلية تتكون من ركنين هما:", o:["مبتدأ وخبر","فعل وفاعل","جار ومجرور","مضاف ومضاف إليه"], c:1},
            {q:"الاسم الذي يقع بعد 'غير' و 'سوى' يعرب:", o:["فاعل","مفعول به","مضاف إليه","نعت"], c:2},
            {q:"علامة الجزم في 'لا تنهَ عن خلق':", o:["السكون","حذف النون","حذف حرف العلة","الكسرة"], c:2},
            {q:"التصغير في كلمة 'جبل':", o:["جبيل","جبال","جبيلات","جبلون"], c:0},
            {q:"'هيهات' اسم فعل:", o:["ماضٍ","مضارع","أمر","مبني للمجهول"], c:0},
            {q:"المفعول المطلق في 'ضربت ضرباً' نوعه:", o:["مؤكد للفعل","مبين للنوع","مبين للعدد","نائب فاعل"], c:0},
            {q:"الجملة 'ليت الشباب يعود' تفيد:", o:["الترجي","التمني","التشبيه","الاستدراك"], c:1},
            {q:"إعراب 'خمسة' في 'اشتريت خمسة كتب':", o:["فاعل","مفعول به","مبتدأ","خبر"], c:1},
            {q:"الحرف 'بل' يفيد:", o:["الإضراب","التخيير","الشك","الاستدراك"], c:0},
            {q:"كلمة 'صحراء' همزتها:", o:["أصلية","منقلبة عن ياء","منقلبة عن واو","مزيدة للتأنيث"], c:3},
            {q:"نوع 'لا' في 'لا تكذب':", o:["نافية","ناهية جازمة","عاطفة","زائدة"], c:1}
        ],
        math: [
            {q:"قيمة س في المعادلة 2س + 4 = 12 هي:", o:["2","4","6","8"], c:1},
            {q:"مساحة المربع الذي طول ضلعه 6 سم:", o:["12","24","36","42"], c:2},
            {q:"مجموع زوايا المثلث يساوي:", o:["90","180","270","360"], c:1},
            {q:"العدد الأولي الزوجي الوحيد هو:", o:["0","2","4","6"], c:1},
            {q:"جذر العدد 144 هو:", o:["10","12","14","16"], c:1},
            {q:"محيط الدائرة يساوي:", o:["ط نق","2 ط نق","ط نق^2","ط ق^2"], c:1},
            {q:"3/4 تحول إلى نسبة مئوية هي:", o:["25%","50%","75%","100%"], c:2},
            {q:"حجم المكعب الذي طول ضلعه 3 سم:", o:["9","18","27","36"], c:2},
            {q:"العدد 17 يعتبر عدداً:", o:["زوجياً","أولياً","مؤلفاً","كسرياً"], c:1},
            {q:"المثلث القائم الزاوية يحتوي على زاوية قياسها:", o:["45","90","120","180"], c:1},
            {q:"ناتج 10 + 5 * 2 هو:", o:["30","20","15","25"], c:1},
            {q:"عدد أضلاع الشكل السداسي:", o:["4","5","6","8"], c:2},
            {q:"القيمة المطلقة للعدد -15 هي:", o:["15","-15","0","1"], c:0},
            {q:"الزاوية الحادة قياسها:", o:["90","أكبر من 90","أقل من 90","180"], c:2},
            {q:"مليار يساوي كم مليون؟", o:["10","100","1000","10000"], c:2},
            {q:"ناتج جمع 1/2 + 1/4 هو:", o:["2/6","3/4","1/4","1"], c:1},
            {q:"مجموع زوايا الشكل الرباعي:", o:["180","360","540","720"], c:1},
            {q:"العدد الذي يقبل القسمة على 5 دائماً ينتهي بـ:", o:["1","2","0 أو 5","3"], c:2},
            {q:"مساحة المستطيل تساوي:", o:["الطول+العرض","الطول*العرض","2*(ط+ع)","الطول/العرض"], c:1},
            {q:"نصف القطر يساوي:", o:["القطر/2","القطر*2","القطر+2","القطر^2"], c:0},
            {q:"المنوال هو القيمة الأكثر:", o:["كبراً","صغراً","تكراراً","توسطاً"], c:2},
            {q:"الوسط الحسابي للأعداد 2، 4، 6 هو:", o:["2","4","6","12"], c:1},
            {q:"قيمة 5^0 (خمسة أس صفر) تساوي:", o:["0","5","1","10"], c:2},
            {q:"الميل للمستقيم الموازي لمحور السينات هو:", o:["0","1","غير معرف","-1"], c:0},
            {q:"عدد الثواني في الساعة الواحدة:", o:["60","600","3600","360"], c:2},
            {q:"المعكوس الضربي للعدد 5 هو:", o:["-5","1/5","0.5","5"], c:1},
            {q:"ناتج -8 + 3 يساوي:", o:["11","5","-5","-11"], c:2},
            {q:"أصغر عدد طبيعي هو:", o:["0","1","-1","لا يوجد"], c:0},
            {q:"الكسر العشري للعدد 1/5 هو:", o:["0.1","0.2","0.5","0.25"], c:1},
            {q:"العدد 1000000 يسمى:", o:["مليون","مليار","تريليون","مئة ألف"], c:0},
            {q:"مساحة المثلث تساوي:", o:["ق*ع","1/2 ق*ع","2*ق*ع","ق+ع"], c:1},
            {q:"مجموع قياس الزاويتين المتكاملتين:", o:["90","180","270","360"], c:1},
            {q:"ما هو العدد الذي 20% منه تساوي 40؟", o:["100","200","300","400"], c:1},
            {q:"إذا كان طول قطر الدائرة 10 سم، فإن نصف قطرها:", o:["5","10","20","100"], c:0},
            {q:"المعكوس الجمعي للعدد 7 هو:", o:["7","-7","1/7","0"], c:1},
            {q:"عدد الأقطار في المثلث هو:", o:["0","1","2","3"], c:0},
            {q:"ناتج 2^3 يساوي:", o:["5","6","8","9"], c:2},
            {q:"التربيع للعدد 9 هو:", o:["18","27","81","3"], c:2},
            {q:"العدد الدوري 0.333... يساوي الكسر:", o:["1/2","1/3","1/4","3/10"], c:1},
            {q:"كم وجهاً للمكعب؟", o:["4","6","8","12"], c:1},
            {q:"مجموع الأعداد الفردية بين 1 و 5 (شاملة):", o:["6","9","15","4"], c:1},
            {q:"قيمة π التقريبية هي:", o:["3.14","2.14","4.14","1.14"], c:0},
            {q:"إذا كانت س=3، فإن 2س^2 تساوي:", o:["12","18","36","9"], c:1},
            {q:"أي مما يلي يمثل تناسباً؟", o:["1/2 = 2/4","1/2 = 1/3","2/3 = 4/5","1/4 = 2/6"], c:0},
            {q:"ناتج 100 / 0.1 هو:", o:["10","100","1000","1"], c:2},
            {q:"مجموع زوايا الشكل الخماسي:", o:["360","540","720","900"], c:1},
            {q:"العدد المحايد الجمعي هو:", o:["1","0","-1","10"], c:1},
            {q:"العدد المحايد الضربي هو:", o:["0","1","2","10"], c:1},
            {q:"العدد الذي مربعه يساوي مكعبه هو:", o:["1","2","3","4"], c:0},
            {q:"مجموع 0.75 + 0.25 يساوي:", o:["0.5","1","1.25","0.9"], c:1}
        ],
        english: [
            {q:"Which of these is a verb?", o:["Apple","Run","Beautiful","Slowly"], c:1},
            {q:"He ____ to school every day.", o:["go","goes","going","gone"], c:1},
            {q:"The plural of 'Child' is:", o:["Childs","Children","Childrens","Childes"], c:1},
            {q:"I have ____ orange.", o:["a","an","the","some"], c:1},
            {q:"They ____ football now.", o:["is playing","am playing","are playing","played"], c:2},
            {q:"Past tense of 'Speak' is:", o:["Speaked","Spoke","Spoken","Speaks"], c:1},
            {q:"Opposite of 'Fast' is:", o:["Quick","Slow","Happy","Big"], c:1},
            {q:"She is the ____ girl in class.", o:["tall","taller","tallest","more tall"], c:2},
            {q:"____ are you from?", o:["What","Where","When","Who"], c:1},
            {q:"We ____ friends.", o:["is","am","are","be"], c:2},
            {q:"Look! The birds ____.", o:["fly","is flying","are flying","flies"], c:2},
            {q:"Capital of Palestine is ____.", o:["Gaza","Jerusalem","Ramallah","Haifa"], c:1},
            {q:"I ____ my lunch an hour ago.", o:["eat","ate","eaten","eating"], c:1},
            {q:"A person who helps sick people is a ____.", o:["Teacher","Farmer","Doctor","Pilot"], c:2},
            {q:"Monday comes after ____.", o:["Tuesday","Sunday","Friday","Saturday"], c:1},
            {q:"The cat is ____ the table.", o:["under","between","at","to"], c:0},
            {q:"I like apples ____ oranges.", o:["but","and","or","so"], c:1},
            {q:"Can you ____ me?", o:["help","helps","helping","helped"], c:0},
            {q:"It is ____ today. Bring your umbrella.", o:["sunny","raining","hot","dry"], c:1},
            {q:"I ____ 15 years old.", o:["is","am","are","be"], c:1},
            {q:"Which month has 28 or 29 days?", o:["January","February","March","April"], c:1},
            {q:"The synonym of 'Happy' is:", o:["Sad","Joyful","Angry","Brave"], c:1},
            {q:"They ____ their homework yet.", o:["haven't done","didn't do","not done","hasn't done"], c:0},
            {q:"If I ____ rich, I would buy a car.", o:["am","was","were","be"], c:2},
            {q:"She ____ her room every morning.", o:["clean","cleans","cleaning","cleaned"], c:1},
            {q:"This is the man ____ helped me.", o:["which","who","whom","whose"], c:1},
            {q:"The car is ____ than the bike.", o:["expensive","more expensive","expensivest","most expensive"], c:1},
            {q:"I'm interested ____ learning English.", o:["in","at","on","with"], c:0},
            {q:"Please turn ____ the light.", o:["off","of","down","at"], c:0},
            {q:"I ____ seen that movie before.", o:["am","have","has","had"], c:1},
            {q:"The sun ____ in the east.", o:["rise","rises","rising","rose"], c:1},
            {q:"Would you like ____ water?", o:["some","any","many","few"], c:0},
            {q:"He is good ____ Math.", o:["at","in","on","with"], c:0},
            {q:"What is the past participle of 'Write'?", o:["Wrote","Writing","Written","Writes"], c:2},
            {q:"I have been studying ____ 3 hours.", o:["since","for","at","from"], c:1},
            {q:"____ you ever been to London?", o:["Do","Have","Did","Are"], c:1},
            {q:"Don't forget to take ____ coat.", o:["you","your","yours","you're"], c:1},
            {q:"A ____ of bread.", o:["loaf","piece","bottle","cup"], c:0},
            {q:"She is afraid ____ dogs.", o:["from","of","with","by"], c:1},
            {q:"The plural of 'Mouse' is:", o:["Mouses","Mice","Mices","Mouse"], c:1},
            {q:"Wait ____ a minute, please.", o:["for","at","in","to"], c:0},
            {q:"He ____ speak three languages.", o:["can","cans","canning","could"], c:0},
            {q:"I usually get up ____ 7 o'clock.", o:["at","on","in","to"], c:0},
            {q:"Which one is a fruit?", o:["Carrot","Banana","Potato","Onion"], c:1},
            {q:"The baby is ____. Please be quiet.", o:["sleep","sleeping","sleeps","slept"], c:1},
            {q:"This book belongs to me. It's ____.", o:["my","mine","me","I"], c:1},
            {q:"How ____ is this shirt?", o:["many","much","long","old"], c:1},
            {q:"Listen! Someone ____ the piano.", o:["play","plays","is playing","played"], c:2},
            {q:"It's ____ to go out at night.", o:["danger","dangerous","dangerously","dangered"], c:1},
            {q:"I don't have ____ money.", o:["some","any","many","few"], c:1}
        ],
        science: [
            {q:"الرمز الكيميائي للماء هو:", o:["CO2","H2O","O2","NaCl"], c:1},
            {q:"الكوكب الأحمر هو:", o:["الأرض","المريخ","الزهرة","زحل"], c:1},
            {q:"مركز الخلية الحيوانية هو:", o:["النواة","السيتوبلازم","الغشاء","الميتوكندريا"], c:0},
            {q:"وحدة قياس القوة هي:", o:["الجول","الواط","النيوتن","الفولت"], c:2},
            {q:"الغاز الذي يحتاجه النبات في البناء الضوئي:", o:["الأكسجين","ثاني أكسيد الكربون","النيتروجين","الهيدروجين"], c:1},
            {q:"تتحول المادة من صلبة إلى سائلة بـ:", o:["التجمد","الانصهار","التبخر","التكاثف"], c:1},
            {q:"أقرب كوكب للشمس هو:", o:["الأرض","عطارد","المريخ","الزهرة"], c:1},
            {q:"سرعة الضوء أسرع من سرعة الصوت.", o:["صح","خطأ","متساويان","لا يوجد صوت"], c:0},
            {q:"الجهاز المسؤول عن ضخ الدم:", o:["الرئتان","القلب","المعدة","الدماغ"], c:1},
            {q:"المادة التي لها شكل ثابت وحجم ثابت:", o:["صلبة","سائلة","غازية","بلازما"], c:0},
            {q:"عنصر الحديد رمزه:", o:["H","Fe","O","Au"], c:1},
            {q:"يغلي الماء عند درجة حرارة:", o:["50","100","150","200"], c:1},
            {q:"المعدن السائل الوحيد هو:", o:["الحديد","الذهب","الزئبق","النحاس"], c:2},
            {q:"القوة التي تجذب الأجسام نحو الأرض:", o:["مغناطيسية","كهربائية","جاذبية","احتكاك"], c:2},
            {q:"عدد كواكب المجموعة الشمسية:", o:["7","8","9","10"], c:1},
            {q:"النسيج الذي ينقل الماء في النبات:", o:["الخشب","اللحاء","الجذور","الأوراق"], c:0},
            {q:"القمر يعتبر:", o:["كوكب","نجم","تابع للأرض","مجرة"], c:2},
            {q:"الحيوانات التي تأكل النباتات فقط تسمى:", o:["آكلة لحوم","عاشبة","قارتة","طفيليات"], c:1},
            {q:"وحدة بناء الكائن الحي هي:", o:["العضو","الجهاز","الخلية","النسيج"], c:2},
            {q:"طبقة الغلاف الجوي التي تحمينا من الأشعة الضارة:", o:["الأكسجين","الأوزون","الهيدروجين","النيتروجين"], c:1},
            {q:"عملية تحويل الطعام إلى طاقة تسمى:", o:["التنفس","الهضم","التمثيل الغذائي","الإخراج"], c:1},
            {q:"يتكون العمود الفقري من عظام تسمى:", o:["أضلاع","فقرات","مفاصل","غضاريف"], c:1},
            {q:"ما هو الغاز الأكثر توافراً في الهواء الجوي؟", o:["الأكسجين","النيتروجين","الهيدروجين","الأرجون"], c:1},
            {q:"العضو المسؤول عن حاسة الشم:", o:["الأذن","العين","الأنف","اللسان"], c:2},
            {q:"تنتقل الحرارة في المعادن عن طريق:", o:["التوصيل","الحمل","الإشعاع","التبخر"], c:0},
            {q:"أكبر عضو في جسم الإنسان هو:", o:["الكبد","الجلد","الدماغ","القلب"], c:1},
            {q:"الدم الذي يحمل الأكسجين يكون لونه:", o:["أحمر فاتح","أحمر غامق","أزرق","أسود"], c:0},
            {q:"من أمثلة الكائنات وحيدة الخلية:", o:["الإنسان","الأميبا","السمك","النحل"], c:1},
            {q:"وحدة قياس المقاومة الكهربائية:", o:["الأمبير","الفولت","الأوم","الواط"], c:2},
            {q:"العالم الذي اكتشف الجاذبية هو:", o:["أينشتاين","نيوتن","باستور","أديسون"], c:1},
            {q:"الصوت ينتقل بشكل أسرع في المواد:", o:["الصلبة","السائلة","الغازية","الفراغ"], c:0},
            {q:"المرآة التي تفرق الأشعة هي:", o:["مقعرة","محدبة","مستوية","كروية"], c:1},
            {q:"المصدر الرئيسي للطاقة على الأرض:", o:["الرياح","الكهرباء","الشمس","البترول"], c:2},
            {q:"تسمى الكائنات التي تصنع غذاءها بنفسها:", o:["مستهلكة","محللة","منتجة","متطفلة"], c:2},
            {q:"البروتينات ضرورية لـ:", o:["بناء العضلات","توفير الطاقة","تخزين الدهون","الهضم"], c:0},
            {q:"يتكون الماء من هيدروجين و:", o:["نيتروجين","أكسجين","كربون","كبريت"], c:1},
            {q:"مرض السكري يتعلق بخلل في هرمون:", o:["الأدرينالين","الأنسولين","الثايروكسين","النمو"], c:1},
            {q:"أطول عظمة في جسم الإنسان هي:", o:["الجمجمة","الساعد","الفخذ","العمود الفقري"], c:2},
            {q:"يتم امتصاص الماء في الجهاز الهضمي في:", o:["المعدة","الأمعاء الدقيقة","الأمعاء الغليظة","البلعوم"], c:2},
            {q:"تحول بخار الماء إلى قطرات سائلة يسمى:", o:["تبخر","تكاثف","تجمد","انصهار"], c:1},
            {q:"عدد أسنان الإنسان البالغ:", o:["20","28","32","36"], c:2},
            {q:"الجهاز الذي يستخدم لقياس الزلازل:", o:["الترمومتر","البارومتر","السيزموجراف","التلسكوب"], c:2},
            {q:"المادة التي تعطي النبات لونه الأخضر:", o:["الكلوروفيل","الهيموجلوبين","الميلانين","النشا"], c:0},
            {q:"الفيتامين الذي نحصل عليه من الشمس:", o:["فيتامين A","فيتامين C","فيتامين D","فيتامين B"], c:2},
            {q:"تعتبر البكتيريا من:", o:["الفطريات","النباتات","الكائنات الدقيقة","الفيروسات"], c:2},
            {q:"أي مما يلي مصدر طاقة متجدد؟", o:["الفحم","الرياح","الغاز الطبيعي","النفط"], c:1},
            {q:"تستخدم الصيدلية الميزان لقياس:", o:["الحجم","الطول","الكتلة","الكثافة"], c:2},
            {q:"عنصر الملح الكيميائي هو:", o:["NaCl","HCl","NaOH","H2O"], c:0},
            {q:"العالم الذي اخترع المصباح الكهربائي:", o:["بيل","أديسون","تسلا","فاراداي"], c:1},
            {q:"نسبة الماء في جسم الإنسان تقريباً:", o:["30%","50%","70%","90%"], c:2}
        ],
        islamic: [
            {q:"أول ركن من أركان الإسلام:", o:["الصلاة","الشهادتان","الزكاة","الحج"], c:1},
            {q:"عدد ركعات صلاة المغرب:", o:["2","3","4","1"], c:1},
            {q:"أطول سورة في القرآن الكريم:", o:["يس","البقرة","النساء","آل عمران"], c:1},
            {q:"من هو خاتم الأنبياء؟", o:["عيسى عليه السلام","موسى عليه السلام","محمد ﷺ","إبراهيم عليه السلام"], c:2},
            {q:"في أي شهر أنزل القرآن؟", o:["رجب","شعبان","رمضان","ذو الحجة"], c:2},
            {q:"عدد أركان الإيمان:", o:["5","6","7","4"], c:1},
            {q:"ما هي قبلة المسلمين الأولى؟", o:["الكعبة","المسجد الأقصى","المسجد النبوي","مكة"], c:1},
            {q:"كم عدد السجدات في الركعة الواحدة؟", o:["1","2","3","4"], c:1},
            {q:"اسم أم النبي محمد ﷺ:", o:["خديجة","عائشة","آمنة","فاطمة"], c:2},
            {q:"ما هو الكتاب المنزل على سيدنا عيسى؟", o:["التوراة","الزبور","الإنجيل","القرآن"], c:2},
            {q:"من هو أول المؤذنين في الإسلام؟", o:["عمر","علي","بلال بن رباح","أبو بكر"], c:2},
            {q:"الغار الذي كان يتعبد فيه النبي ﷺ:", o:["ثور","حراء","أحد","تبوك"], c:1},
            {q:"صلاة العيدين حكمها:", o:["فرض عين","سنة مؤكدة","مباحة","مكروهة"], c:1},
            {q:"عدد السور في القرآن الكريم:", o:["110","114","120","100"], c:1},
            {q:"من هو الملقب بالصديق؟", o:["عمر بن الخطاب","عثمان بن عفان","أبو بكر الصديق","علي بن أبي طالب"], c:2},
            {q:"ما اسم ناقة النبي ﷺ؟", o:["القصواء","العضباء","الغمامة","الخضراء"], c:0},
            {q:"بماذا يلقب عمر بن الخطاب؟", o:["الصديق","الفاروق","ذو النورين","أسد الله"], c:1},
            {q:"كم عدد الصلوات المفروضة في اليوم؟", o:["3","5","7","10"], c:1},
            {q:"ما هو الركن الخامس من أركان الإسلام؟", o:["الزكاة","الصوم","الحج","الصلاة"], c:2},
            {q:"اسم السورة التي تسمى عروس القرآن:", o:["يس","الرحمن","الملك","الواقعة"], c:1},
            {q:"كم دام نزول القرآن الكريم؟", o:["10 سنوات","13 سنة","23 سنة","30 سنة"], c:2},
            {q:"ما هي عاصمة الدولة الإسلامية في عهد الخلفاء الراشدين؟", o:["مكة","المدينة المنورة","دمشق","الكوفة"], c:1},
            {q:"من هو الصحابي الذي لُقب بـ 'سيف الله المسلول'؟", o:["خالد بن الوليد","حمزة بن عبد المطلب","علي بن أبي طالب","عمر بن الخطاب"], c:0},
            {q:"ما هي السورة التي تعدل ثلث القرآن؟", o:["الفاتحة","الإخلاص","الكرسي","يس"], c:1},
            {q:"ما اسم المرضعة التي أرضعت النبي ﷺ؟", o:["ثويبة","حليمة السعدية","آمنة بنت وهب","فاطمة بنت أسد"], c:1},
            {q:"ما هي المعركة التي استشهد فيها حمزة بن عبد المطلب؟", o:["بدر","أحد","الخندق","خيبر"], c:1},
            {q:"كم عدد أجزاء القرآن الكريم؟", o:["30","60","114","20"], c:0},
            {q:"ما هو الركن الثالث من أركان الإسلام؟", o:["الصلاة","الزكاة","الصوم","الحج"], c:1},
            {q:"ما هي أقصر سورة في القرآن الكريم؟", o:["الناس","الفلق","الكوثر","الإخلاص"], c:2},
            {q:"من هو الملك المكلف بالوحي؟", o:["إسرافيل","ميكائيل","جبريل عليه السلام","مالك"], c:2},
            {q:"في أي عام ولد النبي محمد ﷺ؟", o:["عام الفيل","عام الحزن","عام الوفود","عام الرمادة"], c:0},
            {q:"ما هي السورة التي بدأت بـ 'المر'؟", o:["البقرة","الرعد","يس","الرحمن"], c:1},
            {q:"كم عدد سنوات دعوة النبي ﷺ في مكة؟", o:["10","13","23","5"], c:1},
            {q:"من هو أول من آمن من الرجال؟", o:["علي بن أبي طالب","أبو بكر الصديق","زيد بن حارثة","عمر بن الخطاب"], c:1},
            {q:"ما هي السورة التي تسمى قلب القرآن؟", o:["الفاتحة","الرحمن","يس","الملك"], c:2},
            {q:"بماذا لُقب عثمان بن عفان؟", o:["الفاروق","ذو النورين","الصديق","أسد الله"], c:1},
            {q:"أين توفي النبي محمد ﷺ؟", o:["مكة","المدينة المنورة","الطائف","القدس"], c:1},
            {q:"ما هو أول ما يحاسب عليه العبد يوم القيامة؟", o:["الصدقة","الصوم","الصلاة","بر الوالدين"], c:2},
            {q:"كم عدد أبناء النبي محمد ﷺ من الذكور؟", o:["1","2","3","4"], c:2},
            {q:"ما هي السورة التي تنفر الشياطين من البيت؟", o:["الفاتحة","يس","البقرة","الكهف"], c:2},
            {q:"من هو النبي الذي ابتلعه الحوت؟", o:["يوسف","يونس عليه السلام","أيوب","موسى"], c:1},
            {q:"ما هي أطول آية في القرآن الكريم؟", o:["آية الكرسي","آية الدين","آية البر","آية المداينة"], c:1},
            {q:"في أي يوم تقوم الساعة؟", o:["السبت","الأحد","الجمعة","الخميس"], c:2},
            {q:"ما هي مهنة النبي محمد ﷺ قبل البعثة؟", o:["التجارة والعي","النجارة","الحدادة","الزراعة"], c:0},
            {q:"من هي أول زوجات النبي ﷺ؟", o:["عائشة","خديجة بنت خويلد","حفصة","زينب"], c:1},
            {q:"ما هو اسم خازن الجنة؟", o:["مالك","رضوان","منكر","جبريل"], c:1},
            {q:"كم عدد المصارف التي توزع عليها الزكاة؟", o:["5","7","8","10"], c:2},
            {q:"ما هي المعجزة الكبرى للنبي محمد ﷺ؟", o:["انشقاق القمر","القرآن الكريم","الإسراء والمعراج","نبع الماء"], c:1},
            {q:"ما هي كنية النبي محمد ﷺ؟", o:["أبو إبراهيم","أبو القاسم","أبو محمد","أبو عبد الله"], c:1},
            {q:"من هو النبي الذي كلم الله تعالى؟", o:["إبراهيم","موسى عليه السلام","عيسى","نوح"], c:1}
        ]
    };
</script>
@endsection
