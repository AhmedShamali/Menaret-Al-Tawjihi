@extends('layouts.app')

@section('title', 'قاعة الاختبار الرقمية | ' . $exam->title)

@section('content')
<div class="ed-exam-take-wrapper">

    <!-- شريط الاختبار العلوي الثابت -->
    <div class="ed-take-topbar">
        <div class="ed-take-bar-content">
            <div class="ed-exam-meta-block">
                <div class="ed-exam-badge-tag">
                    <i class="fas fa-file-signature"></i>
                    <span>{{ $exam->subject->name_ar ?? $exam->subject->name ?? 'مادة دراسية' }}</span>
                </div>
                <h2>{{ $exam->title }}</h2>
            </div>

            <div class="ed-timer-block">
                <span class="timer-label"><i class="far fa-clock"></i> الوقت المتبقي</span>
                <div id="countdown_timer">00:00</div>
            </div>
        </div>

        <div class="ed-take-progress-track">
            <div class="ed-take-progress-fill" id="examProgressBar"></div>
        </div>
    </div>

    <!-- نموذج الاختبار والأسئلة -->
    <form id="fullExamForm" enctype="multipart/form-data">
        @csrf
        <div class="ed-take-layout">

            <!-- قائمة الأسئلة -->
            <div class="ed-questions-flow">
                @foreach($exam->questions as $index => $q)
                <div class="ed-question-card" id="q_card_{{ $q->id }}">
                    
                    <div class="ed-q-header">
                        <div class="ed-q-info">
                            <span class="ed-q-number">سؤال {{ $index + 1 }} من {{ count($exam->questions) }}</span>
                            <span class="ed-q-score"><i class="far fa-star"></i> {{ $q->points }} درجات</span>
                        </div>
                        <button type="button" class="ed-flag-btn" onclick="toggleFlag({{ $q->id }}, {{ $index }})" title="تمييز للمراجعة">
                            <i class="far fa-bookmark" id="flag_icon_{{ $q->id }}"></i>
                            <span>مراجعة</span>
                        </button>
                    </div>

                    <div class="ed-q-body">
                        <h3 class="ed-q-text">{!! nl2br(e($q->question_text)) !!}</h3>

                        @php
                            $questionImage = $q->image ?? ($q->image_path ?? null);
                        @endphp

                        @if(!empty($questionImage))
                        <div class="ed-q-image-box">
                            <button type="button" class="ed-zoom-btn" onclick="openImageModal('{{ asset('storage/' . $questionImage) }}')">
                                <i class="fas fa-search-plus"></i> تكبير الصورة
                            </button>
                            <img src="{{ asset('storage/' . $questionImage) }}" alt="مرفق السؤال" class="ed-q-img" onclick="openImageModal('{{ asset('storage/' . $questionImage) }}')">
                        </div>
                        @endif

                        @if($q->type == 'mcq')
                            <div class="ed-options-grid">
                                @foreach(['a', 'b', 'c', 'd'] as $option)
                                @if(!empty($q->$option))
                                <label class="ed-option-item">
                                    <input 
                                        type="radio" 
                                        name="answers[{{ $q->id }}]" 
                                        value="{{ $option }}" 
                                        hidden 
                                        onchange="markAsAnswered({{ $index }}, {{ $q->id }})"
                                    >
                                    <div class="ed-option-box">
                                        <span class="ed-opt-letter">{{ strtoupper($option) }}</span>
                                        <span class="ed-opt-text">{{ $q->$option }}</span>
                                    </div>
                                </label>
                                @endif
                                @endforeach
                            </div>
                        @else
                            <div class="ed-written-answer-box">
                                <label class="ed-input-label">اكتب إجابتك النموذجية:</label>
                                <textarea 
                                    name="answers[{{ $q->id }}]" 
                                    rows="4" 
                                    placeholder="دون إجابتك هنا بوضوح وبشكل كامل..." 
                                    class="ed-textarea" 
                                    oninput="markAsAnswered({{ $index }}, {{ $q->id }})"
                                ></textarea>

                                @if(!empty($q->require_file))
                                <div class="ed-file-upload-zone">
                                    <input 
                                        type="file" 
                                        name="files[{{ $q->id }}]" 
                                        id="file_{{ $q->id }}" 
                                        hidden 
                                        onchange="updateFileName(this, {{ $q->id }}, {{ $index }})"
                                    >
                                    <label for="file_{{ $q->id }}" class="ed-upload-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <strong>إرفاق ملف الحل (PDF أو صورة توضيحية)</strong>
                                        <span id="file_name_{{ $q->id }}" class="ed-uploaded-name"></span>
                                    </label>
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>

                </div>
                @endforeach

                <!-- زر التسليم النهائي -->
                <div class="ed-final-submit-wrap">
                    <button type="button" onclick="confirmSubmission()" id="submitBtn" class="ed-btn ed-btn-primary" style="padding: 14px 44px; font-size: 1.05rem;">
                        <i class="fas fa-paper-plane"></i>
                        <span>تسليم الاختبار النهائي</span>
                    </button>
                    <p class="ed-submit-hint">تأكد من مراجعة كافة الأسئلة وإجاباتك قبل الضغط على تسليم الاختبار.</p>
                </div>
            </div>

            <!-- خريطة الأسئلة الجانبية -->
            <aside class="ed-take-sidebar">
                <div class="ed-nav-card">
                    <div class="ed-nav-header">
                        <h4><i class="fas fa-map-marked-alt"></i> خريطة الأسئلة</h4>
                        <span class="ed-progress-counter"><strong id="answeredCounter">0</strong> / {{ count($exam->questions) }}</span>
                    </div>

                    <div class="ed-nav-legend">
                        <span class="legend-item"><span class="dot answered"></span> تم الحل</span>
                        <span class="legend-item"><span class="dot flagged"></span> للمراجعة</span>
                        <span class="legend-item"><span class="dot unvisited"></span> متبقي</span>
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
                            تسليم الاختبار
                        </button>
                    </div>
                </div>
            </aside>

        </div>
    </form>

</div>

<!-- نافذة تكبير الصورة -->
<div class="ed-image-modal" id="imageModal" onclick="closeImageModal()">
    <span class="ed-image-modal-close">&times;</span>
    <img id="modalImageTarget" src="" alt="صورة مكبرة">
</div>

<style>
    .ed-exam-take-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px 20px 80px;
        direction: rtl;
        font-family: 'Alexandria', 'Tajawal', sans-serif;
    }

    /* Fixed Topbar */
    .ed-take-topbar {
        position: sticky;
        top: 20px;
        z-index: 100;
        background: #0f172a;
        color: #ffffff;
        border-radius: 18px;
        padding: 16px 24px 0;
        margin-bottom: 28px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
    }

    .ed-take-bar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 14px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .ed-exam-meta-block h2 {
        font-size: 1.25rem;
        font-weight: 800;
        margin: 4px 0 0;
        color: #ffffff;
    }

    .ed-exam-badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        color: #93c5fd;
    }

    .ed-timer-block {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 8px 18px;
        text-align: center;
    }

    .timer-label {
        display: block;
        font-size: 0.72rem;
        color: #cbd5e1;
        font-weight: 600;
        margin-bottom: 2px;
    }

    #countdown_timer {
        font-family: monospace;
        font-size: 1.6rem;
        font-weight: 800;
        color: #38bdf8;
        direction: ltr;
        line-height: 1;
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
        background: #10b981;
        transition: width 0.3s ease;
    }

    /* Layout */
    .ed-take-layout {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 24px;
        align-items: start;
    }

    /* Question Cards */
    .ed-question-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .ed-q-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 16px;
        margin-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-q-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ed-q-number {
        font-size: 0.88rem;
        font-weight: 800;
        color: #1d4ed8;
        background: #eff6ff;
        padding: 4px 12px;
        border-radius: 8px;
    }

    .ed-q-score {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
    }

    .ed-flag-btn {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        padding: 6px 12px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .ed-flag-btn:hover {
        background: #fef3c7;
        color: #d97706;
        border-color: #fde68a;
    }

    .ed-flag-btn.active {
        background: #fef3c7;
        color: #d97706;
        border-color: #fde68a;
    }

    .ed-q-text {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.8;
        margin: 0 0 20px;
    }

    /* Question Image */
    .ed-q-image-box {
        position: relative;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 24px;
        text-align: center;
    }

    .ed-q-img {
        max-height: 360px;
        max-width: 100%;
        border-radius: 10px;
        object-fit: contain;
        cursor: pointer;
    }

    .ed-zoom-btn {
        position: absolute;
        top: 18px;
        left: 18px;
        background: rgba(15, 23, 42, 0.75);
        color: #ffffff;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
    }

    /* MCQ */
    .ed-options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .ed-option-item {
        cursor: pointer;
        display: block;
    }

    .ed-option-box {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        transition: all 0.2s ease;
    }

    .ed-option-item:hover .ed-option-box {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .ed-option-item input:checked + .ed-option-box {
        border-color: #1d4ed8;
        background: #eff6ff;
    }

    .ed-opt-letter {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        color: #475569;
        flex-shrink: 0;
    }

    .ed-option-item input:checked + .ed-option-box .ed-opt-letter {
        background: #1d4ed8;
        color: #ffffff;
    }

    .ed-opt-text {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.5;
    }

    /* Written Answer */
    .ed-written-answer-box {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .ed-input-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
    }

    .ed-textarea {
        width: 100%;
        padding: 14px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-family: inherit;
        font-size: 0.92rem;
        color: #0f172a;
        line-height: 1.6;
        outline: none;
        resize: vertical;
    }

    .ed-textarea:focus {
        border-color: #1d4ed8;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
    }

    .ed-file-upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        background: #f8fafc;
    }

    .ed-upload-label {
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        margin: 0;
    }

    .ed-upload-label i {
        font-size: 1.8rem;
        color: #1d4ed8;
    }

    .ed-upload-label strong {
        font-size: 0.85rem;
        color: #334155;
    }

    .ed-uploaded-name {
        font-size: 0.82rem;
        color: #059669;
        font-weight: 700;
    }

    /* Final Submit */
    .ed-final-submit-wrap {
        text-align: center;
        margin: 40px 0 20px;
    }

    .ed-submit-hint {
        margin-top: 10px;
        font-size: 0.82rem;
        color: #94a3b8;
    }

    /* Sidebar Navigator */
    .ed-nav-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px;
        position: sticky;
        top: 120px;
    }

    .ed-nav-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-nav-header h4 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-progress-counter {
        font-size: 0.85rem;
        color: #64748b;
    }

    .ed-progress-counter strong {
        color: #1d4ed8;
    }

    .ed-nav-legend {
        display: flex;
        gap: 10px;
        font-size: 0.72rem;
        color: #64748b;
        margin-bottom: 16px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .legend-item .dot {
        width: 8px;
        height: 8px;
        border-radius: 2px;
    }

    .legend-item .dot.answered { background: #10b981; }
    .legend-item .dot.flagged { background: #f59e0b; }
    .legend-item .dot.unvisited { background: #e2e8f0; }

    .ed-nav-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        margin-bottom: 20px;
    }

    .ed-nav-cell {
        aspect-ratio: 1;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 0.85rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s;
    }

    .ed-nav-cell:hover {
        border-color: #1d4ed8;
        color: #1d4ed8;
    }

    .ed-nav-cell.answered {
        background: #10b981;
        border-color: #10b981;
        color: #ffffff;
    }

    .ed-nav-cell.flagged {
        background: #fef3c7;
        border-color: #f59e0b;
        color: #b45309;
    }

    /* Image Modal */
    .ed-image-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.85);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .ed-image-modal img {
        max-width: 92%;
        max-height: 88vh;
        border-radius: 12px;
    }

    .ed-image-modal-close {
        position: absolute;
        top: 20px;
        right: 24px;
        color: #ffffff;
        font-size: 2rem;
        cursor: pointer;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .ed-take-layout {
            grid-template-columns: 1fr;
        }
        .ed-take-sidebar {
            order: 2;
        }
        .ed-nav-card {
            position: static;
        }
        .ed-options-grid {
            grid-template-columns: 1fr;
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
        e.preventDefault();
        e.returnValue = 'هل أنت متأكد من مغادرة قاعة الاختبار؟ سيتم فقدان تقدمك الحالي!';
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

        if (timeLeft <= 300) {
            timerBox.style.color = '#ef4444';
        }

        if (--timeLeft < 0) {
            clearInterval(timerInterval);
            timerBox.textContent = "00 : 00";
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
        document.getElementById('answeredCounter').innerText = answeredSet.size;
    }

    function toggleFlag(qId, index) {
        const icon = document.getElementById('flag_icon_' + qId);
        const navBtn = document.getElementById('nav_btn_' + index);

        icon.classList.toggle('far');
        icon.classList.toggle('fas');
        if (navBtn) navBtn.classList.toggle('flagged');
    }

    function updateFileName(input, qId, index) {
        if (input.files && input.files[0]) {
            document.getElementById('file_name_' + qId).innerHTML = "<i class='fas fa-check-circle'></i> " + input.files[0].name;
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
                Swal.fire({
                    title: 'تم التسليم بنجاح!',
                    text: res.data.message || 'تم حفظ إجاباتك بنجاح في سجل درجاتك.',
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