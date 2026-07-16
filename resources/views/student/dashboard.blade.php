@extends('layouts.app')
@section('content')
<div style="display: flex; flex-direction: column; gap: 40px; animation: slideUp 0.8s ease;">

    {{-- ترحيب الطالب --}}
    <div style="background: linear-gradient(135deg, #059669, #065f46); color: white; padding: 60px; border-radius: 40px; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -20px; top: -20px; font-size: 15rem; opacity: 0.1;">📖</div>
        <div style="position: relative; z-index: 1;">
            <h1 style="font-size: 2.5rem; font-weight: 900;">أهلاً بك يا بطل! 🌟</h1>
            <p style="font-size: 1.2rem; opacity: 0.8; margin-top: 10px; max-width: 500px;">استكمل رحلتك التعليمية اليوم، لديك اختبارات جديدة بانتظارك.</p>
            <div style="display: flex; gap: 20px; margin-top: 30px;">
                <div style="background: rgba(255,255,255,0.15); padding: 15px 25px; border-radius: 20px;">
                    <div style="font-size: 0.7rem; opacity: 0.8;">معدلك الحالي</div>
                    <div style="font-size: 1.8rem; font-weight: 900;">{{ number_format($my_stats['avg_grade'], 1) }}</div>
                </div>
                <div style="background: rgba(255,255,255,0.15); padding: 15px 25px; border-radius: 20px;">
                    <div style="font-size: 0.7rem; opacity: 0.8;">اختبارات أنجزتها</div>
                    <div style="font-size: 1.8rem; font-weight: 900;">{{ $my_stats['completed_exams'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        {{-- اختبارات مقترحة --}}
        <div>
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">📝 اختبارات بانتظارك</h3>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                @foreach($available_exams as $ex)
                <div class="glass-card" style="padding: 25px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 800; color: #1e293b;">{{ $ex->title }}</div>
                        <span style="font-size: 0.75rem; color: #64748b;">{{ $ex->subject->name_ar }}</span>
                    </div>
                    <a href="{{ route('student.exams.take', $ex->id) }}" class="btn btn-primary btn-sm">بدء الاختبار</a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- اختصار سريع --}}
        <div class="glass-card" style="padding: 40px; background: #f8fafc; border: 2px dashed #e2e8f0; display: grid; place-items: center; text-align: center;">
            <div>
                <div style="font-size: 3rem; margin-bottom: 15px;">🤖</div>
                <h3 style="margin-bottom: 10px;">هل تحتاج مساعدة في دروسك؟</h3>
                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 20px;">المساعد الذكي متاح 24/7 للإجابة على استفساراتك الدراسية.</p>
                <button class="btn btn-primary">تحدث مع المساعد الآن</button>
            </div>
        </div>
    </div>
</div>
@endsection
