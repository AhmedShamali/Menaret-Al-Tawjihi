@extends('layouts.app')
@section('content')
<div style="display: flex; flex-direction: column; gap: 35px;">
    <h1 style="font-weight: 800;">تحليل بيانات الاختبار: {{ $exam->title }} 📊</h1>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px;">
        <div class="glass-card" style="padding: 30px; border-right: 6px solid #3b82f6;">
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 700;">متوسط درجات الطلاب</span>
            <h2 style="font-size: 2.5rem; margin-top: 10px; color: #1e293b;">{{ number_format($stats['avg'], 1) }}</h2>
        </div>
        <div class="glass-card" style="padding: 30px; border-right: 6px solid #10b981;">
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 700;">أعلى درجة في المساق</span>
            <h2 style="font-size: 2.5rem; margin-top: 10px; color: #1e293b;">{{ $stats['max'] }}</h2>
        </div>
        <div class="glass-card" style="padding: 30px; border-right: 6px solid #f59e0b;">
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 700;">إجمالي التسليمات</span>
            <h2 style="font-size: 2.5rem; margin-top: 10px; color: #1e293b;">{{ $stats['count'] }}</h2>
        </div>
    </div>

    {{-- جدول ترتيب الطلاب --}}
    <div class="glass-card" style="padding: 0; overflow: hidden; border: none; border-radius: 30px;">
        <div style="padding: 20px 30px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-weight: 800;">ترتيب المتفوقين</div>
        <table style="width: 100%; border-collapse: collapse; text-align: right;">
            <thead>
                <tr style="color: #64748b; font-size: 0.85rem;">
                    <th style="padding: 20px;">الطالب</th>
                    <th style="padding: 20px;">الدرجة</th>
                    <th style="padding: 20px; text-align: center;">الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exam->submissions->sortByDesc('total_earned_grade') as $s)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 20px; font-weight: 700;">{{ $s->student->name_ar }}</td>
                    <td style="padding: 20px; font-weight: 900; color: #2563eb;">{{ $s->total_earned_grade }}</td>
                    <td style="padding: 20px; text-align: center;">
                        <span class="chip" style="background:#ecfdf5; color:#059669;">{{ $s->total_earned_grade > ($exam->questions->sum('points') / 2) ? 'ناجح' : 'راسب' }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
