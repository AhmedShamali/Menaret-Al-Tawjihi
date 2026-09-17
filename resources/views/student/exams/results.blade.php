@extends('layouts.app')

@section('title', 'تفاصيل نتيجة الاختبار')

@section('content')
<style>
    .result-card { background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #eef2f6; overflow: hidden; margin-bottom: 30px; }
    .result-header { background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; padding: 40px; text-align: center; }
    .score-badge { background: rgba(255,255,255,0.2); display: inline-block; padding: 15px 30px; border-radius: 50px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); margin-top: 15px; }

    .question-box { border: 2px solid #f1f5f9; border-radius: 18px; padding: 25px; margin-bottom: 20px; position: relative; transition: 0.3s; }
    .status-badge { position: absolute; top: 20px; left: 20px; padding: 5px 15px; border-radius: 10px; font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; gap: 5px; }

    /* ألوان الحالات */
    .correct { border-color: #10b981; background-color: #f0fdf4; }
    .correct .status-badge { background: #10b981; color: white; }

    .wrong { border-color: #ef4444; background-color: #fef2f2; }
    .wrong .status-badge { background: #ef4444; color: white; }

    .pending { border-color: #f59e0b; background-color: #fffbeb; }
    .pending .status-badge { background: #f59e0b; color: white; }

    .answer-text { background: white; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0; margin-top: 15px; font-weight: 600; }
    .mcq-option { padding: 10px 15px; border-radius: 10px; margin-top: 5px; border: 1px solid #e2e8f0; font-size: 0.9rem; }
    .correct-option { background: #d1fae5; border-color: #10b981; color: #065f46; font-weight: bold; }
</style>

<div style="max-width: 1000px; margin: 0 auto; padding: 20px;">

    {{-- هيدر النتيجة --}}
    <div class="result-card">
        <div class="result-header">
            <h1 style="font-size: 2rem; font-weight: 900; margin: 0;">{{ $submission->exam->title }}</h1>
            <p style="opacity: 0.8; margin-top: 10px;">مراجعة الإجابات المفصلة</p>
            <div class="score-badge">
                <span style="font-size: 2.5rem; font-weight: 900;">{{ $submission->total_earned_grade }}</span>
                <span style="font-size: 1.2rem; opacity: 0.8;"> / {{ $submission->exam->questions->sum('points') }}</span>
            </div>
        </div>

        <div style="padding: 40px;">
            <h3 style="margin-bottom: 25px; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-clipboard-check" style="color: #4f46e5;"></i> مراجعة الإجابات:
            </h3>

            @foreach($submission->answers as $idx => $ans)
                @php
                    $awarded = (float)$ans->points_awarded;
                    $max = (float)$ans->question->points;

                    // تحديد الحالة
                    if($ans->question->type == 'mcq') {
                        $isCorrect = ($awarded >= $max && $max > 0);
                        $statusClass = $isCorrect ? 'correct' : 'wrong';
                        $statusText = $isCorrect ? 'إجابة صحيحة' : 'إجابة خاطئة';
                        $icon = $isCorrect ? 'fa-check' : 'fa-xmark';
                    } else {
                        // الأسئلة المقالية غالباً تحتاج مراجعة معلم
                        $statusClass = $awarded > 0 ? 'correct' : 'pending';
                        $statusText = $awarded > 0 ? 'تم التصحيح' : 'بانتظار تصحيح المعلم';
                        $icon = 'fa-clock';
                    }
                @endphp

                <div class="question-box {{ $statusClass }}">
                    <div class="status-badge">
                        <i class="fa-solid {{ $icon }}"></i> {{ $statusText }} ({{ $awarded }} / {{ $max }})
                    </div>

                    <div style="max-width: 80%;">
                        <span style="color: #4f46e5; font-weight: 800; font-size: 0.9rem;">السؤال {{ $idx + 1 }}:</span>
                        <h4 style="color: #1e293b; margin: 10px 0; line-height: 1.6;">{{ $ans->question->question_text }}</h4>

                        @if($ans->question->type == 'mcq')
                            {{-- عرض الخيارات مع تحديد الصحيح --}}
                            <div style="margin-top: 15px;">
                                @foreach(['a', 'b', 'c', 'd'] as $opt)
                                    @if($ans->question->$opt)
                                        <div class="mcq-option {{ strtolower($ans->question->correct_answer) == $opt ? 'correct-option' : '' }}">
                                            <strong>{{ strtoupper($opt) }}:</strong> {{ $ans->question->$opt }}
                                            @if(strtolower($ans->answer_text) == $opt)
                                                <span style="float: left; font-size: 0.7rem; background: #4f46e5; color: white; padding: 2px 8px; border-radius: 5px;">إجابتك</span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="answer-text">
                                <span style="font-size: 0.8rem; color: #64748b; display: block; margin-bottom: 5px;">الإجابة المسجلة:</span>
                                {{ $ans->answer_text ?? 'تم رفع ملف مرفق لهذا السؤال' }}
                            </div>
                            @if($ans->file_path)
                                <a href="{{ asset('storage/'.$ans->file_path) }}" target="_blank" style="margin-top: 10px; display: inline-block; color: #4f46e5; font-weight: 700; text-decoration: none; font-size: 0.9rem;">
                                    <i class="fa-solid fa-paperclip"></i> عرض الملف المرفق
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- حالة وقرار إعادة الاختبار --}}
            <div style="margin-top: 30px; padding: 20px; border-radius: 16px; background: var(--ed-surface-alt, #f8fafc); border: 1px solid var(--ed-border, #e2e8f0); text-align: center;">
                @if($submission->allow_retake)
                    <div style="color: var(--ed-success, #10b981); margin-bottom: 12px; font-weight: 800; font-size: 1.05rem;">
                        <i class="fa-solid fa-unlock-keyhole"></i> وافق أستاذ المادة على إعادة هذا الاختبار لك! يمكنك البدء بمحاولة جديدة.
                    </div>
                    <a href="{{ route('student.exams.take', $submission->exam_id) }}" style="background: var(--ed-success, #10b981); color: white; padding: 12px 32px; border-radius: 12px; text-decoration: none; font-weight: 800; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-play"></i> بدء محاولة الاختبار الآن
                    </a>
                @elseif($submission->retake_requested)
                    <div style="color: var(--ed-accent, #f97316); font-weight: 800; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-hourglass-half fa-spin"></i> تم إرسال طلب إعادة الاختبار لأستاذ المادة، وهو قيد المراجعة حالياً.
                    </div>
                @else
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                        <div style="text-align: right;">
                            <strong style="color: var(--ed-text-main, #0f172a); font-size: 0.92rem; display: block;">هل واجهت مشكلة أو ترغب في تحسين أدائك؟</strong>
                            <span style="color: var(--ed-text-muted, #64748b); font-size: 0.8rem;">يمكنك إرسال طلب إلكتروني لمعلم المادة للسماح لك بفرصة إعادة الاختبار.</span>
                        </div>
                        <button type="button" onclick="requestRetakePrompt()" style="background: var(--ed-accent-soft, #fff7ed); color: var(--ed-accent, #f97316); border: 1px solid var(--ed-accent-border, #fed7aa); padding: 10px 20px; border-radius: 10px; font-weight: 800; font-size: 0.86rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-rotate-right"></i> طلب إذن إعادة الاختبار
                        </button>
                    </div>
                @endif
            </div>

            <div style="margin-top: 30px; display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('student.exams.index') }}" style="background: var(--ed-primary, #0284c7); color: white; padding: 12px 30px; border-radius: 12px; text-decoration: none; font-weight: 800; transition: 0.2s;">
                    العودة لقاعة الاختبارات
                </a>
                <button onclick="window.print()" style="background: var(--ed-surface, #fff); color: var(--ed-text-body, #475569); border: 1px solid var(--ed-border, #cbd5e1); padding: 12px 28px; border-radius: 12px; font-weight: 800; cursor: pointer;">
                    <i class="fa-solid fa-print"></i> طباعة التقرير
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function requestRetakePrompt() {
    Swal.fire({
        title: 'طلب إذن إعادة الاختبار',
        text: 'اكتب سبباً مختصراً أو عذراً لتوضيحه لأستاذ المادة:',
        input: 'textarea',
        inputPlaceholder: 'مثال: انقطع الاتصال بالإنترنت، أو أرغب بفرصة ثانية لرفع التحصيل...',
        showCancelButton: true,
        confirmButtonText: 'إرسال الطلب للمعلم',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#0284c7',
        cancelButtonColor: '#64748b',
        showLoaderOnConfirm: true,
        preConfirm: (notes) => {
            return axios.post("{{ route('student.exams.requestRetake', $submission->exam_id) }}", {
                notes: notes,
                _token: '{{ csrf_token() }}'
            }).then(response => {
                return response.data;
            }).catch(error => {
                Swal.showValidationMessage(
                    error.response?.data?.error || 'تعذر إرسال الطلب، يرجى المحاولة لاحقاً'
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value?.success) {
            Swal.fire({
                icon: 'success',
                title: 'تم الإرسال!',
                text: result.value.title || 'تم إرسال طلبك للمعلم بنجاح',
                confirmButtonColor: '#0284c7'
            }).then(() => location.reload());
        }
    });
}
</script>
@endsection
