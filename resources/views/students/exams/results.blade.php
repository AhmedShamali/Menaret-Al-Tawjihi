@extends('layouts.app')

@section('title', 'نتيجة الاختبار الأكاديمي')

@section('content')
<div style="max-width: 850px; margin: 0 auto; padding-bottom: 50px;">

    <div style="background: #ffffff; border-radius: 30px; padding: 50px; border: 1px solid #e2e8f0; box-shadow: 0 15px 35px rgba(0,0,0,0.03); text-align: center;">

        <div style="width: 90px; height: 90px; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; border-radius: 28px; display: grid; place-items: center; font-size: 2.5rem; margin: 0 auto 25px; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.25);">
            <i class="fa-solid fa-award"></i>
        </div>

        <h1 style="font-size: 2rem; font-weight: 900; color: #1e293b; margin-bottom: 8px;">سجل درجات الاختبار الأكاديمي</h1>
        <p style="color: #64748b; font-size: 1.05rem; font-weight: 600;">{{ $submission->exam->title ?? 'اختبار إلكتروني' }}</p>

        {{-- صندوق النتيجة الإجمالية --}}
        <div style="margin: 35px 0; padding: 25px 40px; background: #f8fafc; border-radius: 22px; display: inline-block; border: 1px solid #e2e8f0;">
            <div style="font-size: 3.5rem; font-weight: 900; color: #4f46e5; line-height: 1;">{{ $submission->total_earned_grade ?? 0 }}</div>
            <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 8px; letter-spacing: 1px;">المجموع النهائي المحصل</div>
        </div>

        {{-- تفاصيل الإجابات بدقة --}}
        <div style="display: flex; flex-direction: column; gap: 20px; text-align: right; margin-top: 30px;">
            @foreach($submission->answers as $idx => $ans)
            @php
                $awarded = floatval($ans->points_awarded ?? 0);
                $maxPoints = floatval($ans->question->points ?? 0);
                // الشرط الدقيق: تعتبر صحيحة فقط إذا كانت الدرجة الممنوحة أكبر من الصفر
                $isCorrect = ($awarded > 0);
            @endphp
            <div style="padding: 25px; border-radius: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-right: 6px solid {{ $isCorrect ? '#10b981' : '#ef4444' }};">

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <span style="font-weight: 800; color: #4f46e5; font-size: 1.1rem;">السؤال ({{ $idx + 1 }}):</span>
                    <span style="font-weight: 700; font-size: 0.85rem; padding: 4px 14px; border-radius: 8px; background: {{ $isCorrect ? '#ecfdf5' : '#fef2f2' }}; color: {{ $isCorrect ? '#10b981' : '#ef4444' }};">
                        {{ $isCorrect ? '✓ صحيح' : '✗ خاطئ' }} ({{ $awarded }} / {{ $maxPoints }})
                    </span>
                </div>

                <p style="font-weight: 700; color: #1e293b; margin-bottom: 15px; font-size: 1.1rem;">{{ $ans->question->question_text ?? '' }}</p>

                <div style="font-size: 0.9rem; color: #64748b; background: white; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        الإجابة المسجلة: <strong style="color: #0f172a;">{{ $ans->answer_text ?? 'تم رفع ملف مرفق لهذا السؤال' }}</strong>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div style="margin-top: 40px;">
            <a href="{{ route('student.exams.index') }}" style="background: #eef2ff; color: #4f46e5; padding: 12px 30px; border-radius: 14px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i> العودة لقائمة الاختبارات
            </a>
        </div>

    </div>
</div>
@endsection
