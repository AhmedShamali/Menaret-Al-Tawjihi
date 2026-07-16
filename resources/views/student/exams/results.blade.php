@extends('layouts.app')

@section('title', 'حالة تسليم الاختبار')

@section('content')
<div style="max-width: 850px; margin: 0 auto; animation: slideUp 0.6s ease;">

    <div class="glass-card" style="padding: 60px; text-align: center; border: none; border-radius: 40px; background: white; box-shadow: 0 20px 60px rgba(0,0,0,0.02);">

        {{-- عرض حالة الاختبار بناءً على رصد المدرس --}}
        @if($submission->status == 'pending')
            {{-- حالة قيد المراجعة --}}
            <div style="font-size: 5rem; margin-bottom: 25px; animation: pulse 2s infinite;">⏳</div>
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--primary);">تم تسليم إجاباتك بنجاح!</h1>
            <p style="color: #64748b; font-size: 1.2rem; max-width: 500px; margin: 15px auto; line-height: 1.6;">
                شكراً لك يا بطل. إجاباتك الآن **قيد المراجعة والتدقيق** من قبل مدرس المساق. سيتم إخطارك فور رصد الدرجة النهائية.
            </p>

            <div style="margin: 40px 0; padding: 25px 40px; background: #f8fafc; border-radius: 20px; display: inline-flex; align-items: center; gap: 15px; border: 1px solid #e2e8f0;">
                <div style="width: 12px; height: 12px; background: #f59e0b; border-radius: 50%;"></div>
                <span style="font-weight: 700; color: #92400e;">حالة النتيجة: بانتظار تصحيح المدرس</span>
            </div>
        @else
            {{-- حالة تم الرصد (تظهر بعد تعديل المدرس) --}}
            <div style="font-size: 5rem; margin-bottom: 25px;">🎓</div>
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--primary);">النتيجة النهائية للاختبار</h1>
            <p style="color: #64748b; font-size: 1.2rem;">{{ $submission->exam->title }} • {{ $submission->exam->subject->name_ar }}</p>

            <div style="margin: 40px 0; padding: 30px; background: #ecfdf5; border-radius: 25px; display: inline-block; min-width: 250px; border: 2px solid #10b98120;">
                <div style="font-size: 4rem; font-weight: 900; color: #059669;">{{ $submission->total_earned_grade }}</div>
                <div style="font-size: 1rem; color: #065f46; font-weight: 700;">الدرجة النهائية المرصودة</div>
            </div>
        @endif

        {{-- مراجعة الأسئلة التي تم تسليمها --}}
        <div style="text-align: right; margin-top: 50px;">
            <h3 style="margin-bottom: 25px; color: var(--primary); border-right: 4px solid var(--accent); padding-right: 15px;">ملخص الإجابات المسلمة</h3>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                @foreach($submission->answers as $ans)
                <div style="padding: 25px; border-radius: 22px; background: white; border: 1px solid #f1f5f9; position: relative; overflow: hidden;">
                    <p style="font-weight: 700; margin-bottom: 12px; color: #1e293b;">{{ $ans->question->question_text }}</p>

                    <div style="font-size: 0.9rem; color: #64748b;">
                        @if($ans->question->type == 'mcq')
                            إجابتك المختارة: <strong>({{ strtoupper($ans->answer_text) }}) {{ $ans->question->{$ans->answer_text} }}</strong>
                        @else
                            نص الإجابة: <em style="color: #0f172a;">{{ $ans->answer_text ?? 'لقد قمت برفع ملف كحل لهذا السؤال' }}</em>
                        @endif
                    </div>

                    {{-- شريط الحالة بجانب السؤال --}}
                    <div style="position: absolute; left: 0; top: 0; width: 6px; height: 100%; background: {{ $submission->status == 'graded' ? ($ans->points_awarded > 0 ? '#10b981' : '#ef4444') : '#cbd5e1' }};"></div>
                </div>
                @endforeach
            </div>
        </div>

        <div style="margin-top: 50px; display: flex; gap: 15px; justify-content: center;">
            <a href="/student/my-exams" class="btn btn-primary" style="padding: 15px 40px; border-radius: 15px;">العودة لقاعة الاختبارات</a>
            <button onclick="window.print()" class="btn" style="background: #f1f5f9; padding: 15px 30px; border-radius: 15px; font-weight: 700;">📑 طباعة وصل التسليم</button>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
