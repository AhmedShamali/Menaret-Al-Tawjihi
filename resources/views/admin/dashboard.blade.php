@extends('layouts.app')
@section('content')
<div style="display: flex; flex-direction: column; gap: 40px; animation: fadeIn 0.8s ease;">

    <div>
        <h1 style="font-size: 2.2rem; font-weight: 800; color: #0f172a;">مرحباً بك، سيادة المدير 👋</h1>
        <p style="color: #64748b;">إليك ملخص أداء منصة "جسر" التعليمية لهذا اليوم.</p>
    </div>

    {{-- بطاقات الإحصائيات (KPIs) --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <div class="glass-card" style="padding: 30px; border-bottom: 5px solid #3b82f6;">
            <div style="font-size: 2rem; margin-bottom: 10px;">👥</div>
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 700;">إجمالي الطلاب</span>
            <h2 style="font-size: 2.2rem; margin-top: 5px;">{{ $stats['students_count'] }}</h2>
        </div>
        <div class="glass-card" style="padding: 30px; border-bottom: 5px solid #10b981;">
            <div style="font-size: 2rem; margin-bottom: 10px;">📚</div>
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 700;">المواد الدراسية</span>
            <h2 style="font-size: 2.2rem; margin-top: 5px;">{{ $stats['subjects_count'] }}</h2>
        </div>
        <div class="glass-card" style="padding: 30px; border-bottom: 5px solid #f59e0b;">
            <div style="font-size: 2rem; margin-bottom: 10px;">📝</div>
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 700;">الاختبارات المنشورة</span>
            <h2 style="font-size: 2.2rem; margin-top: 5px;">{{ $stats['exams_count'] }}</h2>
        </div>
        <div class="glass-card" style="padding: 30px; border-bottom: 5px solid #ef4444;">
            <div style="font-size: 2rem; margin-bottom: 10px;">⏳</div>
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 700;">تسليمات لم تصحح</span>
            <h2 style="font-size: 2.2rem; margin-top: 5px;">{{ $stats['pending_grades'] }}</h2>
        </div>
    </div>

    {{-- الجداول السريعة --}}
    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px;">
        <div class="glass-card" style="padding: 0; overflow: hidden;">
            <div style="padding: 20px 30px; background: #f8fafc; font-weight: 800; border-bottom: 1px solid #e2e8f0;">أحدث تسليمات الاختبارات</div>
            <table style="width: 100%; border-collapse: collapse; text-align: right;">
                @foreach($recent_submissions as $sub)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px 30px;">
                        <div style="font-weight: 700;">{{ $sub->student->name_ar }}</div>
                        <div style="font-size: 0.7rem; color: #94a3b8;">{{ $sub->exam->title }}</div>
                    </td>
                    <td style="padding: 15px 30px; text-align: left;">
                        <a href="{{ route('admin.submissions.grade', $sub->id) }}" class="btn btn-sm" style="background: #0f172a; color: white;">تصحيح</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>

        <div class="glass-card" style="padding: 30px;">
            <h3 style="margin-bottom: 20px;">طلاب انضموا حديثاً</h3>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                @foreach($recent_students as $st)
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="{{ asset('storage/'.$st->photo) }}" style="width: 40px; height: 40px; border-radius: 10px; object-fit: cover;">
                    <div>
                        <div style="font-weight: 700; font-size: 0.9rem;">{{ $st->name_ar }}</div>
                        <div style="font-size: 0.7rem; color: #94a3b8;">{{ $st->stage->label_ar }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
