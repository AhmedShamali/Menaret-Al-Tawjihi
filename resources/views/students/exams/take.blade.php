@extends('layouts.app')

@section('title', 'تقديم الاختبار الأكاديمي')

@section('content')
<style>
    :root {
        --primary: #4f46e5;
        --secondary: #3b82f6;
        --dark: #0f172a;
        --bg-light: #f8fafc;
        --success: #10b981;
    }

    .exam-container { max-width: 900px; margin: 0 auto; padding: 20px; }

    /* الهيدر الثابت */
    .sticky-header {
        position: sticky; top: 10px; z-index: 1000;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(10px);
        color: white; padding: 20px 30px;
        border-radius: 20px; display: flex;
        justify-content: space-between; align-items: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        margin-bottom: 30px; border: 1px solid rgba(255,255,255,0.1);
    }

    .timer-box {
        background: rgba(255,255,255,0.1);
        padding: 8px 20px; border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.2);
        text-align: center; min-width: 120px;
    }

    /* كرت السؤال */
    .question-card {
        background: #fff; border-radius: 24px;
        padding: 35px; margin-bottom: 25px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .question-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }

    /* شبكة الخيارات */
    .options-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px; margin-top: 20px;
    }

    .q-opt input { display: none; }
    .q-box {
        display: flex; align-items: center; gap: 15px;
        padding: 18px; border: 2px solid #f1f5f9;
        border-radius: 15px; cursor: pointer; transition: 0.2s;
        background: #f8fafc;
    }
    .q-opt input:checked + .q-box {
        border-color: var(--primary); background: #eef2ff;
    }
    .q-bullet {
        width: 30px; height: 30px; border-radius: 50%;
        background: #e2e8f0; display: grid; place-items: center;
        font-weight: bold; font-size: 0.8rem;
    }
    .q-opt input:checked + .q-box .q-bullet {
        background: var(--primary); color: white;
    }

    /* منطقة رفع الملفات */
    .file-upload-area {
        margin-top: 20px; border: 2px dashed #cbd5e1;
        padding: 20px; border-radius: 15px;
        text-align: center; cursor: pointer; transition: 0.3s;
    }
    .file-upload-area:hover { border-color: var(--primary); background: #f1f5f9; }

    .submit-btn {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white; border: none; padding: 18px 60px;
        border-radius: 15px; font-size: 1.1rem; font-weight: 800;
        cursor: pointer; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        display: inline-flex; align-items: center; gap: 10px; transition: 0.3s;
    }
    .submit-btn:hover { transform: scale(1.05); }

    @media (max-width: 600px) {
        .sticky-header { flex-direction: column; gap: 15px; text-align: center; }
        .options-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="exam-container">
    {{-- هيدر الاختبار --}}
    <div class="sticky-header">
        <div>
            <h2 style="font-size: 1.2rem; margin: 0;">{{ $exam->title }}</h2>
            <span style="font-size: 0.8rem; opacity: 0.8;">الطالب: {{ auth()->user()->name }}</span>
        </div>
        <div class="timer-box">
            <div style="font-size: 0.6rem; text-transform: uppercase;">الوقت المتبقي</div>
            <div id="timer" style="font-size: 1.5rem; font-weight: 800; color: var(--success);">{{ $exam->duration_minutes }}:00</div>
        </div>
    </div>

    {{-- نموذج الأسئلة --}}
    <form id="examForm" action="{{ route('exams.submit', $exam->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @foreach($exam->questions as $idx => $q)
        <div class="question-card">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <span style="color: var(--primary); font-weight: 800;">السؤال {{ $idx + 1 }}</span>
                <span style="font-size: 0.85rem; color: #64748b;">[{{ $q->points }} درجات]</span>
            </div>

            <h3 style="font-size: 1.25rem; color: var(--dark); line-height: 1.6; margin-bottom: 25px;">{{ $q->question_text }}</h3>

            @if($q->type == 'mcq')
                <div class="options-grid">
                    @foreach(['a','b','c','d'] as $o)
                    @if(!empty($q->$o))
                    <label class="q-opt">
                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $o }}" required>
                        <div class="q-box">
                            <span class="q-bullet">{{ strtoupper($o) }}</span>
                            <span>{{ $q->$o }}</span>
                        </div>
                    </label>
                    @endif
                    @endforeach
                </div>
            @else
                <textarea name="answers[{{ $q->id }}]" rows="5" class="form-control"
                    placeholder="اكتب إجابتك هنا..."
                    style="width: 100%; border-radius: 15px; padding: 15px; border: 2px solid #e2e8f0; outline: none; transition: 0.3s;"></textarea>

                @if($q->require_file)
                    <div class="file-upload-area" onclick="document.getElementById('f_{{ $q->id }}').click()">
                        <input type="file" name="files[{{ $q->id }}]" id="f_{{ $q->id }}" hidden onchange="updateFileName(this)">
                        <i class="fa-solid fa-file-arrow-up" style="font-size: 1.5rem; color: var(--primary);"></i>
                        <p id="name_{{ $q->id }}" style="margin: 10px 0 0; font-size: 0.9rem; color: #475569;">اضغط لرفع ملف الإجابة (PDF/Images)</p>
                    </div>
                @endif
            @endif
        </div>
        @endforeach

        <div style="text-align: center; margin-top: 40px; padding-bottom: 50px;">
            <button type="submit" class="submit-btn" onclick="return confirm('هل أنت متأكد من تسليم الإجابات؟ لا يمكنك التراجع بعد ذلك.')">
                <span>اعتماد وتسليم الاختبار</span>
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </form>
</div>

<script>
    // 1. منطق العداد الزمني
    let duration = {{ $exam->duration_minutes }};
    let totalSeconds = duration * 60;
    const timerElement = document.getElementById('timer');

    const countdown = setInterval(() => {
        let minutes = Math.floor(totalSeconds / 60);
        let seconds = totalSeconds % 60;

        // إضافة صفر حشوي
        seconds = seconds < 10 ? '0' + seconds : seconds;
        minutes = minutes < 10 ? '0' + minutes : minutes;

        timerElement.innerHTML = `${minutes}:${seconds}`;

        if (totalSeconds <= 60) {
            timerElement.style.color = '#ef4444'; // تغيير اللون للسكوت الأخير
        }

        if (totalSeconds <= 0) {
            clearInterval(countdown);
            alert('انتهى الوقت! سيتم تسليم إجاباتك تلقائياً.');
            document.getElementById('examForm').submit();
        }
        totalSeconds--;
    }, 1000);

    // 2. إظهار اسم الملف عند الاختيار
    function updateFileName(input) {
        const id = input.id.split('_')[1];
        const fileName = input.files[0] ? input.files[0].name : "اضغط لرفع ملف الإجابة";
        document.getElementById('name_' + id).innerText = "تم اختيار: " + fileName;
        document.getElementById('name_' + id).style.color = "#10b981";
    }

    // 3. منع إغلاق الصفحة بالخطأ
    window.onbeforeunload = function() {
        return "هل أنت متأكد؟ قد تفقد تقدمك في الاختبار.";
    };

    // إزالة المنع عند الضغط على زر الإرسال
    document.getElementById('examForm').onsubmit = function() {
        window.onbeforeunload = null;
    };
</script>
@endsection
