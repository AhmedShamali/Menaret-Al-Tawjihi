@extends('layouts.app')

@section('title', 'سجل درجاتي الأكاديمي')

@section('content')
<div style="display: flex; flex-direction: column; gap: 40px; animation: fadeIn 0.8s ease;">

    {{-- هيدر ملخص الأداء --}}
    <div style="background: linear-gradient(135deg, var(--primary), #1e293b); color: white; padding: 50px; border-radius: 35px; display: flex; justify-content: space-between; align-items: center; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.1);">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1px;">سجل الدرجات العام 🎓</h1>
            <p style="opacity: 0.7; font-size: 1.1rem; margin-top: 10px;">أهلاً بك، إليك تفاصيل أدائك ونتائجك في الاختبارات المكتملة.</p>
        </div>
        <div style="text-align: center; background: rgba(255,255,255,0.1); padding: 20px 40px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
            <div style="font-size: 0.8rem; opacity: 0.8; text-transform: uppercase; margin-bottom: 5px;">المعدل التراكمي</div>
            <div style="font-size: 3rem; font-weight: 900; color: #10b981;">{{ number_format($submissions->avg('total_earned_grade'), 1) }}</div>
        </div>
    </div>

    {{-- جدول النتائج --}}
    <div class="glass-card" style="padding: 0; overflow: hidden; border: none; border-radius: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.02);">
        <div style="padding: 25px 35px; background: #f8fafc; border-bottom: 1px solid #f1f5f9; font-weight: 800; color: var(--primary);">قائمة النتائج التفصيلية</div>
        <table style="width: 100%; border-collapse: collapse; text-align: right;">
            <thead>
                <tr style="background: #fff; color: #64748b; font-size: 0.85rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="padding: 20px 35px;">المادة الدراسية</th>
                    <th style="padding: 20px;">عنوان الاختبار</th>
                    <th style="padding: 20px;">الدرجة النهائية</th>
                    <th style="padding: 20px;">الحالة الأكاديمية</th>
                    <th style="padding: 20px; text-align: center;">التقرير</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $s)
                <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.3s;" onmouseover="this.style.background='#fcfcfd'">
                    <td style="padding: 20px 35px;">
                        <span class="chip" style="background: {{ $s->exam->subject->color }}15; color: {{ $s->exam->subject->color }}; font-weight: 800; padding: 8px 15px; border-radius: 10px;">
                            {{ $s->exam->subject->name_ar }}
                        </span>
                    </td>
                    <td style="padding: 20px; font-weight: 700; color: var(--primary);">{{ $s->exam->title }}</td>
                    <td style="padding: 20px;">
                        <div style="font-size: 1.2rem; font-weight: 900; color: #2563eb;">
                            {{ $s->total_earned_grade }} <span style="font-size: 0.8rem; color: #cbd5e1;">/ {{ $s->exam->questions->sum('points') }}</span>
                        </div>
                    </td>
                    <td style="padding: 20px;">
                        <span class="chip" style="{{ $s->status == 'graded' ? 'background:#ecfdf5;color:#059669' : 'background:#fff7ed;color:#c2410c' }}; font-weight: 700;">
                            {{ $s->status == 'graded' ? 'مكتمل التصحيح' : 'قيد المراجعة' }}
                        </span>
                    </td>
                    <td style="padding: 20px; text-align: center;">
                        <a href="{{ route('student.exams.result', $s->id) }}" class="btn btn-sm" style="background: #f1f5f9; color: var(--primary); border-radius: 10px; font-weight: 700; padding: 10px 20px; text-decoration: none;">
                            عرض النتيجة ←
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 100px; text-align: center; color: #94a3b8;">
                        <div style="font-size: 3rem; margin-bottom: 15px;">📂</div>
                        <p>لا يوجد اختبارات مسجلة في سجلك حتى الآن.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
