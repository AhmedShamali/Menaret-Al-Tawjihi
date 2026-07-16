@extends('layouts.app')
@section('content')
<div style="max-width: 850px; margin: 0 auto; animation: slideUp 0.6s ease;">
    <div class="glass-card" style="padding: 60px; text-align: center; border: none; border-radius: 40px;">
        <div style="font-size: 5rem; margin-bottom: 20px;">🎓</div>
        <h1 style="font-size: 2.5rem; font-weight: 800;">نتيجة الاختبار النهائي</h1>
        <p style="color: #64748b; font-size: 1.2rem;">{{ $submission->exam->title }} • {{ $submission->exam->subject->name_ar }}</p>

        <div style="margin: 40px 0; padding: 30px; background: #f8fafc; border-radius: 25px; display: inline-block; min-width: 250px;">
            <div style="font-size: 4rem; font-weight: 900; color: var(--accent);">{{ $submission->total_earned_grade }}</div>
            <div style="font-size: 1rem; color: #94a3b8; font-weight: 700;">الدرجة المستحقة</div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 20px; text-align: right; margin-top: 40px;">
            @foreach($submission->answers as $ans)
            <div style="padding: 25px; border-radius: 20px; background: white; border: 1px solid #f1f5f9; border-right: 6px solid {{ $ans->points_awarded > 0 ? '#10b981' : '#ef4444' }};">
                <p style="font-weight: 700; margin-bottom: 10px;">{{ $ans->question->question_text }}</p>
                <div style="font-size: 0.85rem; color: #64748b;">
                    الدرجة المرصودة: <strong>{{ $ans->points_awarded ?? '--' }}</strong> من {{ $ans->question->points }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
