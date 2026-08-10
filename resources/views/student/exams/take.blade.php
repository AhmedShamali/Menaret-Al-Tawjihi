@extends('layouts.app')

@section('title', 'قاعة الاختبار الرقمية | ' . $exam->title)

@section('content')
<style>
    :root {
        --university-primary: #0f172a;
        --university-accent: #0284c7;
        --success-green: #10b981;
        --warning-amber: #f59e0b;
        --danger-red: #ef4444;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --bg-body: #f8fafc;
        --card-bg: #ffffff;
    }

    body {
        background-color: var(--bg-body);
        font-family: 'Tajawal', sans-serif;
        color: var(--text-main);
    }

    .exam-wrapper {
        max-width: 1300px;
        margin: 0 auto;
        padding: 20px 15px 100px;
    }

    .exam-header-fixed {
        position: sticky;
        top: 15px;
        background: var(--university-primary);
        padding: 18px 30px;
        border-radius: 18px;
        z-index: 100;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15);
        color: white;
        margin-bottom: 30px;
    }

    .progress-bar-container {
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        margin-top: 15px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: var(--success-green);
        width: 0%;
        transition: width 0.3s ease;
    }

    .timer-card {
        background: rgba(255, 255, 255, 0.08);
        padding: 8px 20px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        text-align: center;
    }

    #countdown_timer {
        font-size: 2rem;
        font-weight: 800;
        font-family: 'Courier New', Courier, monospace;
        color: #38bdf8;
        direction: ltr !important;
        line-height: 1;
        margin-top: 4px;
    }

    .exam-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 30px;
        align-items: start;
    }

    @media (max-width: 992px) {
        .exam-layout { grid-template-columns: 1fr; }
    }

    .question-premium-card {
        background: var(--card-bg);
        padding: 35px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        margin-bottom: 35px;
        position: relative;
    }

    .q-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 15px;
    }

    .q-badge {
        font-weight: 800;
        color: var(--university-accent);
        font-size: 1.05rem;
        background: #f0f9ff;
        padding: 6px 16px;
        border-radius: 10px;
    }

    .q-points {
        font-weight: 700;
        color: var(--university-primary);
        font-size: 0.85rem;
        background: #f1f5f9;
        padding: 6px 14px;
        border-radius: 8px;
    }

    .question-text {
        font-size: 1.35rem;
        color: var(--university-primary);
        line-height: 1.6;
        margin-bottom: 25px;
        font-weight: 700;
    }

    .question-image-box {
        margin: 0 0 30px 0;
        background-color: #f8fafc;
        padding: 15px;
        border-radius: 16px;
        border: 2px dashed #cbd5e1;
        text-align: center;
        position: relative;
    }

    .question-img-managed {
        max-height: 400px;
        max-width: 100%;
        border-radius: 12px;
        object-fit: contain;
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        cursor: pointer;
    }

    .img-zoom-btn {
        position: absolute;
        top: 25px;
        left: 25px;
        background: rgba(15, 23, 42, 0.75);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        cursor: pointer;
        z-index: 10;
    }

    .mcq-options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    @media (max-width: 640px) {
        .mcq-options-grid { grid-template-columns: 1fr; }
    }

    .mcq-item {
        position: relative;
        cursor: pointer;
        display: block;
    }

    .mcq-item-design {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s ease;
        background: #fff;
    }

    .mcq-item:hover .mcq-item-design {
        border-color: var(--university-accent);
        background-color: #f0f9ff;
    }

    .mcq-item input:checked + .mcq-item-design {
        border-color: var(--success-green);
        background: #ecfdf5;
    }

    .opt-letter {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #f1f5f9;
        display: grid;
        place-items: center;
        font-weight: 800;
        font-size: 0.85rem;
        color: #475569;
    }

    .mcq-item input:checked + .mcq-item-design .opt-letter {
        background: var(--success-green);
        color: white;
    }

    .exam-nav-card {
        background: white;
        padding: 20px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        position: sticky;
        top: 130px;
    }

    .nav-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin-top: 15px;
    }

    .nav-btn {
        aspect-ratio: 1;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        font-weight: 700;
        color: var(--text-main);
        cursor: pointer;
        display: grid;
        place-items: center;
        text-decoration: none;
    }

    .nav-btn.answered {
        background: var(--success-green);
        color: white;
        border-color: var(--success-green);
    }

    .nav-btn.flagged {
        border-color: var(--warning-amber);
        background: #fef3c7;
        color: #b45309;
    }

    .image-modal {
        display: none;
        position: fixed;
        z-index: 2000;
        inset: 0;
        background: rgba(15, 23, 42, 0.85);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .image-modal img {
        max-width: 95%;
        max-height: 90vh;
        border-radius: 12px;
    }

    .image-modal-close {
        position: absolute;
        top: 20px;
        right: 20px;
        color: white;
        font-size: 2rem;
        cursor: pointer;
    }
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="exam-wrapper">

    <div class="exam-header-fixed">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 1.4rem; font-weight: 800; margin: 0;">{{ $exam->title }}</h2>
                <p style="font-size: 0.85rem; opacity: 0.8; margin: 4px 0 0;">المساق: {{ $exam->subject->name_ar ?? $exam->subject->name ?? 'عام' }}</p>
            </div>
            <div class="timer-card">
                <span style="font-size: 0.7rem; display: block; opacity: 0.8; font-weight: 700;">الوقت المتبقي</span>
                <div id="countdown_timer">00:00</div>
            </div>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" id="examProgressBar"></div>
        </div>
    </div>

    <form id="fullExamForm" enctype="multipart/form-data">
        @csrf
        <div class="exam-layout">

            <div class="questions-list">
                @foreach($exam->questions as $index => $q)
                <div class="question-premium-card" id="q_card_{{ $q->id }}">
                    <div class="q-header">
                        <div>
                            <span class="q-badge">سؤال #{{ $index + 1 }}</span>
                            <span class="q-points">{{ $q->points }} نقاط</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="toggleFlag({{ $q->id }}, {{ $index }})" title="علم السؤال لمراجعته لاحقاً">
                            <i class="fa-regular fa-bookmark" id="flag_icon_{{ $q->id }}"></i>
                        </button>
                    </div>

                    <h3 class="question-text">{!! nl2br(e($q->question_text)) !!}</h3>

                    {{-- ✅ صندوق عرض صورة السؤال بشكل صحيح ومحدث ليدعم حقل image المضاف حديثاً --}}
                    @php
                        $questionImage = $q->image ?? ($q->image_path ?? null);
                    @endphp

                    @if(!empty($questionImage))
                    <div class="question-image-box">
                        <button type="button" class="img-zoom-btn" onclick="openImageModal('{{ asset('storage/' . $questionImage) }}')">
                            <i class="fa-solid fa-magnifying-glass-plus"></i> تكبير
                        </button>
                        <img src="{{ asset('storage/' . $questionImage) }}" alt="صورة السؤال" class="question-img-managed" onclick="openImageModal('{{ asset('storage/' . $questionImage) }}')">
                    </div>
                    @endif

                    @if($q->type == 'mcq')
                        <div class="mcq-options-grid">
                            @foreach(['a', 'b', 'c', 'd'] as $option)
                            @if(!empty($q->$option))
                            <label class="mcq-item">
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $option }}" hidden onchange="markAsAnswered({{ $index }}, {{ $q->id }})">
                                <div class="mcq-item-design">
                                    <span class="opt-letter">{{ strtoupper($option) }}</span>
                                    <span style="font-weight: 600;">{{ $q->$option }}</span>
                                </div>
                            </label>
                            @endif
                            @endforeach
                        </div>
                    @else
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <textarea name="answers[{{ $q->id }}]" rows="5" placeholder="اكتب إجابتك هنا..." style="width: 100%; padding: 15px; border-radius: 12px; border: 2px solid #e2e8f0; font-family: inherit;" oninput="markAsAnswered({{ $index }}, {{ $q->id }})"></textarea>

                            @if(!empty($q->require_file))
                            <div style="border: 2px dashed #cbd5e1; padding: 20px; border-radius: 12px; text-align: center; background: #f8fafc;">
                                <input type="file" name="files[{{ $q->id }}]" id="file_{{ $q->id }}" hidden onchange="updateFileName(this, {{ $q->id }}, {{ $index }})">
                                <label for="file_{{ $q->id }}" style="cursor: pointer; margin: 0;">
                                    <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2rem; color: var(--university-accent);"></i>
                                    <span style="display: block; margin-top: 5px; font-weight: 700;">إرفاق ملف إجابة (PDF أو صورة)</span>
                                    <span id="file_name_{{ $q->id }}" style="display: block; font-size: 0.85rem; color: var(--success-green); font-weight: 700; margin-top: 5px;"></span>
                                </label>
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
                @endforeach

                <div style="text-align: center; margin-top: 40px;">
                    <button type="button" onclick="confirmSubmission()" id="submitBtn" style="padding: 18px 60px; font-size: 1.25rem; font-weight: 800; border-radius: 50px; border: none; background: var(--university-primary); color: white; cursor: pointer; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.2);">
                        تقديم الإجابات نهائياً 🚩
                    </button>
                </div>
            </div>

            <div class="exam-nav-card">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 10px;">خريطة الأسئلة</h4>
                <div style="display: flex; gap: 10px; font-size: 0.75rem; margin-bottom: 15px;">
                    <span style="display: flex; align-items: center; gap: 4px;"><span style="width: 10px; height: 10px; background: var(--success-green); border-radius: 2px;"></span> مجاب</span>
                    <span style="display: flex; align-items: center; gap: 4px;"><span style="width: 10px; height: 10px; background: #fef3c7; border-radius: 2px;"></span> مراجعة</span>
                </div>
                <div class="nav-grid">
                    @foreach($exam->questions as $index => $q)
                    <a href="#q_card_{{ $q->id }}" class="nav-btn" id="nav_btn_{{ $index }}">
                        {{ $index + 1 }}
                    </a>
                    @endforeach
                </div>
            </div>

        </div>
    </form>
</div>

<div class="image-modal" id="imageModal" onclick="closeImageModal()">
    <span class="image-modal-close">&times;</span>
    <img id="modalImageTarget" src="" alt="صورة مكبرة">
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };

    window.addEventListener('beforeunload', function (e) {
        e.preventDefault();
        e.returnValue = 'هل أنت متأكد من مغادرة الاختبار؟ سيتم فقدان تقدمك الحالي!';
        return e.returnValue;
    });

    const totalQuestions = {{ count($exam->questions) }};
    let answeredSet = new Set();

    let timeLeft = {{ $exam->duration_minutes * 60 }};
    const timerBox = document.getElementById('countdown_timer');

    const timerInterval = setInterval(function() {
        let mins = Math.floor(timeLeft / 60);
        let secs = timeLeft % 60;
        timerBox.textContent = (mins < 10 ? '0' : '') + mins + " : " + (secs < 10 ? '0' : '') + secs;

        if (--timeLeft < 0) {
            clearInterval(timerInterval);
            timerBox.textContent = "00 : 00";
            timerBox.style.color = 'var(--danger-red)';
            autoSubmitExam();
        }
    }, 1000);

    function markAsAnswered(index, qId) {
        answeredSet.add(qId);
        const navBtn = document.getElementById('nav_btn_' + index);
        if (navBtn) navBtn.classList.add('answered');
        updateProgress();
    }

    function updateProgress() {
        const percent = (answeredSet.size / totalQuestions) * 100;
        document.getElementById('examProgressBar').style.width = percent + '%';
    }

    function toggleFlag(qId, index) {
        const icon = document.getElementById('flag_icon_' + qId);
        const navBtn = document.getElementById('nav_btn_' + index);

        icon.classList.toggle('fa-regular');
        icon.classList.toggle('fa-solid');
        icon.classList.toggle('text-warning');
        if (navBtn) navBtn.classList.toggle('flagged');
    }

    function updateFileName(input, qId, index) {
        if(input.files && input.files[0]) {
            document.getElementById('file_name_' + qId).innerHTML = "<i class='fa-solid fa-circle-check'></i> " + input.files[0].name;
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

    function confirmSubmission() {
        Swal.fire({
            title: 'تسليم الاختبار النهائي؟',
            text: `لقد أجبت على ${answeredSet.size} من أصل ${totalQuestions} سؤال.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: 'var(--success-green)',
            cancelButtonColor: 'var(--text-muted)',
            confirmButtonText: 'نعم، قم بالتسليم',
            cancelButtonText: 'مراجعة الأسئلة'
        }).then((result) => {
            if (result.isConfirmed) finalizeExamSubmission();
        });
    }

    function finalizeExamSubmission() {
        window.removeEventListener('beforeunload', window.onbeforeunload);
        window.onbeforeunload = null;

        const btn = document.getElementById('submitBtn');
        const formData = new FormData(document.getElementById('fullExamForm'));

        btn.disabled = true;
        btn.innerHTML = "<i class='fa-solid fa-spinner fa-spin'></i> جاري الحفظ والتسليم...";

        axios.post("{{ route('student.exams.submit', $exam->id) }}", formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(res => {
            if (res.data.success) {
                Swal.fire({
                    title: 'تم التسليم بنجاح!',
                    text: res.data.message || 'تم حفظ إجاباتك بنجاح.',
                    icon: 'success',
                    confirmButtonText: 'حسناً'
                }).then(() => {
                    window.location.href = res.data.redirect || "{{ route('student.exams.index') }}";
                });
            } else {
                Swal.fire('تنبيه', res.data.message || 'حدث خطأ ما.', 'warning');
                btn.disabled = false;
                btn.innerHTML = "إعادة محاولة التسليم 🚩";
            }
        })
        .catch(err => {
            let errorMsg = 'حدثت مشكلة أثناء إرسال الإجابات، حاول مجدداً.';
            if (err.response && err.response.data && err.response.data.message) {
                errorMsg = err.response.data.message;
            }
            Swal.fire('خطأ في الاتصال', errorMsg, 'error');
            btn.disabled = false;
            btn.innerHTML = "إعادة محاولة التسليم 🚩";
        });
    }

    function autoSubmitExam() {
        Swal.fire({
            title: 'انتهى الوقت المخصص!',
            text: 'جاري تسليم إجاباتك تلقائياً...',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2500
        }).then(() => finalizeExamSubmission());
    }
</script>
@endsection
