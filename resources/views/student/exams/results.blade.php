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

            <div style="margin-top: 40px; display: flex; gap: 15px; justify-content: center;">
                <a href="{{ url('/student/exams') }}" style="background: #4f46e5; color: white; padding: 15px 40px; border-radius: 15px; text-decoration: none; font-weight: 800; transition: 0.3s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                    العودة لقاعة الاختبارات
                </a>
                <button onclick="window.print()" style="background: #f1f5f9; color: #475569; padding: 15px 40px; border-radius: 15px; border: none; font-weight: 800; cursor: pointer;">
                    <i class="fa-solid fa-print"></i> طباعة التقرير
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
