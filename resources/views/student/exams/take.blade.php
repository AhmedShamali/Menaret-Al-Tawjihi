@extends('layouts.app')

@section('title', 'قاعة الاختبار الرقمية')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding-bottom: 100px; position: relative;">

    {{-- هيدر الاختبار الثابت --}}
    <div class="exam-header-fixed">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 1.4rem; font-weight: 800; margin: 0; color: white;">{{ $exam->title }}</h2>
                <p style="font-size: 0.8rem; opacity: 0.7; margin: 5px 0 0; color: white;">المساق: {{ $exam->subject->name_ar }} • نظام الاختبارات</p>
            </div>
            <div class="timer-card">
                <span style="font-size: 0.7rem; display: block; opacity: 0.8; text-transform: uppercase; color: white;">المتبقي</span>
                {{-- تم إضافة الـ ID الصحيح وضبط الاتجاه لمنع الانعكاس --}}
                <div id="countdown_timer" style="font-size: 2.2rem; font-weight: 900; font-family: monospace; color: #10b981; direction: ltr !important;">
                    00:00
                </div>
            </div>
        </div>
    </div>

    {{-- محتوى الأسئلة --}}
    <form id="fullExamForm" enctype="multipart/form-data">
        @csrf
        <div style="display: flex; flex-direction: column; gap: 35px; margin-top: 50px;">
            @foreach($exam->questions as $index => $q)
            <div class="question-premium-card">
                <div style="display: flex; justify-content: space-between; margin-bottom: 25px;">
                    <span style="font-weight: 900; color: var(--accent); font-size: 1.1rem;">سؤال #{{ $index + 1 }}</span>
                    <span style="font-weight: 700; color: #94a3b8; font-size: 0.8rem; background: #f8fafc; padding: 5px 12px; border-radius: 10px;">{{ $q->points }} نقاط</span>
                </div>

                <h3 style="font-size: 1.6rem; color: #1e293b; line-height: 1.6; margin-bottom: 40px; font-weight: 700;">{{ $q->question_text }}</h3>

                @if($q->type == 'mcq')
                    {{-- خيارات الموضوعي --}}
                    <div class="mcq-options-grid">
                        @foreach(['a', 'b', 'c', 'd'] as $option)
                        <label class="mcq-item">
                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $option }}" required hidden>
                            <div class="mcq-item-design">
                                <span class="opt-letter">{{ strtoupper($option) }}</span>
                                <span class="opt-text">{{ $q->$option }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                @else
                    {{-- الأسئلة المقالية ورفع الملفات --}}
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <textarea name="answers[{{ $q->id }}]" rows="6" placeholder="اكتب إجابتك هنا بوضوح..." class="essay-textarea-modern"></textarea>

                        @if($q->require_file)
                        <div class="upload-zone-modern">
                            <input type="file" name="files[{{ $q->id }}]" id="f_{{ $q->id }}" hidden onchange="updateFileName(this, {{ $q->id }})">
                            <label for="f_{{ $q->id }}" style="cursor: pointer; display: block; padding: 40px;">
                                <div style="font-size: 2.5rem; margin-bottom: 10px;">📤</div>
                                <strong style="display: block; color: #1e293b;">اضغط لرفع ملف الحل (PDF/صور)</strong>
                                <span id="file_name_{{ $q->id }}" style="font-size: 0.85rem; color: #059669; font-weight: 700; margin-top: 10px; display: block;"></span>
                            </label>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
            @endforeach
        </div>

        <div style="margin-top: 60px; text-align: center;">
            <button type="button" onclick="finalizeExamSubmission()" id="submitBtn" class="final-submit-btn">إنهاء وتسليم الاختبار 🚩</button>
        </div>
    </form>
</div>

<style>
    /* التنسيقات الفخمة */
    .exam-header-fixed { position: sticky; top: 15px; background: #0f172a; padding: 25px 45px; border-radius: 30px; z-index: 1000; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
    .timer-card { background: rgba(255,255,255,0.05); padding: 10px 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); text-align: center; min-width: 150px; }

    .question-premium-card { background: white; padding: 50px; border-radius: 40px; border: 1px solid #f1f5f9; box-shadow: 0 10px 40px rgba(0,0,0,0.02); }

    .mcq-options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .mcq-item-design { display: flex; align-items: center; gap: 15px; padding: 22px; border: 2px solid #f1f5f9; border-radius: 22px; cursor: pointer; transition: 0.3s; background: #fff; }
    .mcq-item input:checked + .mcq-item-design { border-color: #10b981; background: #f0fdf4; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.1); }
    .opt-letter { width: 35px; height: 35px; border-radius: 50%; background: #f1f5f9; display: grid; place-items: center; font-weight: 800; font-size: 0.8rem; color: #475569; }
    .mcq-item input:checked + .mcq-item-design .opt-letter { background: #10b981; color: white; }

    .essay-textarea-modern { width: 100%; padding: 25px; border-radius: 25px; border: 2px solid #f1f5f9; background: #f8fafc; font-family: inherit; font-size: 1.1rem; outline: none; transition: 0.3s; }
    .essay-textarea-modern:focus { border-color: var(--accent); background: white; }

    .upload-zone-modern { border: 3px dashed #cbd5e1; border-radius: 30px; text-align: center; background: #fdfdfd; transition: 0.3s; }
    .upload-zone-modern:hover { border-color: #10b981; background: #f0fdf4; }

    .final-submit-btn { padding: 25px 120px; font-size: 1.5rem; font-weight: 800; border-radius: 30px; border: none; background: var(--primary); color: white; cursor: pointer; transition: 0.4s; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.2); }
    .final-submit-btn:hover { transform: translateY(-7px); box-shadow: 0 30px 60px rgba(15, 23, 42, 0.3); filter: brightness(1.2); }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. تشغيل المؤقت وإصلاح انعكاس الساعة
    let timeLeft = {{ $exam->duration_minutes * 60 }};
    const timerBox = document.getElementById('countdown_timer');

    const timerInterval = setInterval(function() {
        let mins = Math.floor(timeLeft / 60);
        let secs = timeLeft % 60;
        // عرض الدقائق ثم الثواني بشكل LTR
        timerBox.textContent = (mins < 10 ? '0' : '') + mins + " : " + (secs < 10 ? '0' : '') + secs;

        if (--timeLeft < 0) {
            clearInterval(timerInterval);
            autoSubmitExam();
        }
    }, 1000);

    function updateFileName(input, id) {
        document.getElementById('file_name_' + id).textContent = "📂 تم اختيار: " + input.files[0].name;
    }

    function autoSubmitExam() {
        Swal.fire({ title: 'انتهى الوقت!', text: 'سيتم تسليم إجاباتك تلقائياً الآن.', icon: 'info', showConfirmButton: false, timer: 2000 })
        .then(() => finalizeExamSubmission());
    }

    function finalizeExamSubmission() {
        const btn = document.getElementById('submitBtn');
        const form = document.getElementById('fullExamForm');
        const formData = new FormData(form);

        btn.disabled = true;

        Swal.fire({
            title: 'جاري المعالجة...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        axios.post("{{ route('student.exams.submit', $exam->id) }}", formData)
        .then(res => {
            if (res.data.success) {
                Swal.fire('تم التسليم!', 'تم حفظ إجاباتك بنجاح ✅', 'success')
                .then(() => location.href = "/student/results/" + res.data.submission_id);
            } else {
                Swal.fire('تنبيه', res.data.error, 'warning');
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            // إظهار الخطأ الحقيقي القادم من لارافيل
            let msg = err.response.data.error || 'خطأ غير معروف في السيرفر';
            Swal.fire('فشل الإرسال', 'السبب: ' + msg, 'error');
            btn.disabled = false;
        });
}
</script>
@endsection
