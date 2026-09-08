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
                    مبارك جهودك يا <strong>{{ $student->name }}</strong>! كل خطوة تخطوها تقربك من فرحة التوجيهي الكبرى.
                </p>
            </div>
        </div>

        <div class="hero-stats-row">
            <div class="hero-stat-pill">
                <span class="num">{{ $certificates->count() }}</span>
                <span class="lbl">شهادات معتمدة</span>
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

    <!-- شبكة الشهادات الملكية -->
    <div class="section-title-bar">
        <h3><i class="fa-solid fa-award text-warning"></i> الشهادات الأكاديمية الصادرة</h3>
        <span class="badge-count">{{ $certificates->count() }} شهادة</span>
    </div>

    @if($certificates->count() > 0)
        <div class="certificates-royal-grid">
            @foreach($certificates as $cert)
                <div class="cert-royal-card">
                    <div class="cert-gold-ribbon"><i class="fa-solid fa-star"></i> تفوق</div>
                    <div class="cert-card-icon">
                        🎓
                    </div>
                    <h4 class="cert-subject-title">{{ $cert->subject->name_ar ?? $cert->subject->name ?? 'مساق أكاديمي' }}</h4>
                    <p class="cert-student-sub">شهادة إتمام وتفوق صادرة باسم: <strong>{{ $student->name }}</strong></p>
                    
                    <div class="cert-grade-tag">
                        المعدل: <span>{{ $cert->final_grade }}%</span>
                    </div>

                    <div class="cert-code-box">
                        <small>كود الشهادة:</small>
                        <code>{{ $cert->certificate_code }}</code>
                    </div>

                    <div class="cert-actions">
                        <a href="{{ route('student.certificates.show', $cert->id) }}" class="btn-cert-view" target="_blank">
                            <i class="fa-solid fa-eye"></i> استعراض وطباعة
                        </a>
                        <a href="{{ route('certificates.verify', $cert->certificate_code) }}" class="btn-cert-verify" target="_blank" title="التحقق المباشر">
                            <i class="fa-solid fa-qrcode"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-certs-card">
            <i class="fa-solid fa-graduation-cap empty-icon"></i>
            <h4>لا توجد شهادات صادرة بعد</h4>
            <p>أكمل دراسة دروسك واجتز اختبارات المادة بمعدل 85% فأكثر ليتم توليد شهادتك تلقائياً!</p>
            <a href="{{ route('student.exams.index') }}" class="btn-primary-action">
                <i class="fa-solid fa-pen-nib"></i> الانتقال للاختبارات
            </a>
        </div>
    @endif

    <!-- غرفة مؤقت التركيز (بومودورو التوجيهي) Focus Room -->
    <div class="pomodoro-focus-card">
        <div class="focus-info-block">
            <span class="focus-tag"><i class="fa-solid fa-stopwatch"></i> تقنية بومودورو العالمية</span>
            <h3>غرفة التركيز الذهني لطلبة الثانوية العامة ⏳</h3>
            <p>
                اختر مدة الجلسة، ابدأ المؤقت وركز في دراستك بدون أي مشتتات. عند انتهاء الجلسة سيتم تسجيل إنجازك وزيادة رصيد التزامك!
            </p>

            <div class="preset-buttons-lane">
                <button type="button" class="btn-preset active" onclick="setTimerDuration(25, this)">
                    <i class="fa-solid fa-bolt"></i> 25 دقيقة (حفظ وتركيز)
                </button>
                <button type="button" class="btn-preset" onclick="setTimerDuration(45, this)">
                    <i class="fa-solid fa-book-open"></i> 45 دقيقة (حل مسائل وزارية)
                </button>
                <button type="button" class="btn-preset" onclick="setTimerDuration(15, this)">
                    <i class="fa-solid fa-rotate-left"></i> 15 دقيقة (مراجعة سريعة)
                </button>
            </div>
        </div>

        <div class="timer-display-block">
            <div class="circular-timer-circle" id="timerCircle">
                <span class="timer-digits" id="timerDigits">25:00</span>
                <span class="timer-state-label" id="timerStatusLabel">جاهز للبدء</span>
            </div>

            <div class="timer-control-btns">
                <button type="button" class="btn-timer-ctrl play" id="btnStartTimer" onclick="startFocusTimer()">
                    <i class="fa-solid fa-play"></i> بدء الجلسة
                </button>
                <button type="button" class="btn-timer-ctrl reset" onclick="resetFocusTimer()">
                    <i class="fa-solid fa-rotate-right"></i> إعادة ضبط
                </button>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    let timerDurationMinutes = 25;
    let totalSeconds = 25 * 60;
    let remainingSeconds = totalSeconds;
    let timerInterval = null;
    let isRunning = false;

    function setTimerDuration(mins, btn) {
        if (isRunning) {
            alert('يرجى إيقاف المؤقت أولاً لتغيير المدة.');
            return;
        }
        document.querySelectorAll('.btn-preset').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        timerDurationMinutes = mins;
        totalSeconds = mins * 60;
        remainingSeconds = totalSeconds;
        updateTimerDisplay();
    }

    function updateTimerDisplay() {
        const m = Math.floor(remainingSeconds / 60);
        const s = remainingSeconds % 60;
        document.getElementById('timerDigits').innerText = 
            `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    }

    function startFocusTimer() {
        const btn = document.getElementById('btnStartTimer');
        const statusLbl = document.getElementById('timerStatusLabel');

        if (!isRunning) {
            isRunning = true;
            btn.innerHTML = '<i class="fa-solid fa-pause"></i> إيقاف مؤقت';
            btn.classList.add('paused');
            statusLbl.innerText = 'جلسة تركيز جارية 🎯';

            timerInterval = setInterval(() => {
                remainingSeconds--;
                updateTimerDisplay();

                if (remainingSeconds <= 0) {
                    clearInterval(timerInterval);
                    isRunning = false;
                    btn.innerHTML = '<i class="fa-solid fa-play"></i> بدء الجلسة';
                    btn.classList.remove('paused');
                    statusLbl.innerText = 'اكتملت الجلسة! 🎉';

                    // نغمة صوتية لإشعار الطالب
                    playChime();

                    // حفظ الجلسة عبر السيرفر
                    axios.post('{{ route("student.pomodoro.save") }}', {
                        minutes: timerDurationMinutes,
                        _token: '{{ csrf_token() }}'
                    }).then(res => {
                        Swal.fire({
                            icon: 'success',
                            title: 'بطل التوجيهي! 👏',
                            text: res.data.message || 'أتممت جلسة المذاكرة بنجاح!',
                            confirmButtonText: 'ممتاز'
                        });
                    });
                }
            }, 1000);
        } else {
            isRunning = false;
            clearInterval(timerInterval);
            btn.innerHTML = '<i class="fa-solid fa-play"></i> استئناف';
            btn.classList.remove('paused');
            statusLbl.innerText = 'متوقف مؤقتاً';
        }
    }

    function resetFocusTimer() {
        isRunning = false;
        clearInterval(timerInterval);
        remainingSeconds = totalSeconds;
        updateTimerDisplay();
        const btn = document.getElementById('btnStartTimer');
        btn.innerHTML = '<i class="fa-solid fa-play"></i> بدء الجلسة';
        btn.classList.remove('paused');
        document.getElementById('timerStatusLabel').innerText = 'جاهز للبدء';
    }

    function playChime() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
            osc.frequency.setValueAtTime(880, ctx.currentTime + 0.15); // A5
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.8);
            osc.start();
            osc.stop(ctx.currentTime + 0.8);
        } catch(e) {}
    }
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

    /* كرت بومودورو والتركيز */
    .pomodoro-focus-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 35px 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 30px;
        align-items: center;
    }
    @media (max-width: 850px) {
        .pomodoro-focus-card {
            grid-template-columns: 1fr;
            text-align: center;
        }
    }
    .focus-tag {
        display: inline-block;
        background: #e0f2fe;
        color: #0369a1;
        font-size: 0.78rem;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 50px;
        margin-bottom: 10px;
    }
    .focus-info-block h3 {
        font-size: 1.4rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 10px;
    }
    .focus-info-block p {
        color: #64748b;
        font-size: 0.92rem;
        line-height: 1.7;
        margin-bottom: 22px;
    }
    .preset-buttons-lane {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .btn-preset {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #334155;
        padding: 10px 16px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 700;
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

    /* العداد الدائري */
    .timer-display-block {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .circular-timer-circle {
        width: 190px;
        height: 190px;
        border-radius: 50%;
        border: 6px solid #e0f2fe;
        border-top-color: #0284c7;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        margin-bottom: 20px;
        box-shadow: 0 10px 25px rgba(2, 132, 199, 0.08);
    }
    .timer-digits {
        font-size: 2.8rem;
        font-weight: 900;
        color: #0f172a;
        font-family: monospace;
        letter-spacing: -1px;
    }
    .timer-state-label {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 700;
    }
    .timer-control-btns {
        display: flex;
        gap: 10px;
    }
    .btn-timer-ctrl {
        padding: 10px 22px;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 800;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
    }
    .btn-timer-ctrl.play {
        background: #10b981;
        color: white;
    }
    .btn-timer-ctrl.play.paused {
        background: #f59e0b;
    }
    .btn-timer-ctrl.reset {
        background: #e2e8f0;
        color: #475569;
    }
    .btn-timer-ctrl:hover {
        transform: translateY(-2px);
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
