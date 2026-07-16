@extends('layouts.app')
@section('content')
<div style="display: flex; flex-direction: column; gap: 30px;">
    <h1 style="font-size: 2.2rem; font-weight: 800; color: #0f172a;">تسليمات الطلاب 📄</h1>

    <div class="glass-card" style="padding: 0; overflow: hidden; border-radius: 25px;">
        <table style="width: 100%; border-collapse: collapse; text-align: right;">
            <thead>
                <tr style="background: #0f172a; color: white;">
                    <th style="padding: 20px;">الطالب</th>
                    <th style="padding: 20px;">الاختبار</th>
                    <th style="padding: 20px;">الدرجة</th>
                    <th style="padding: 20px;">الحالة</th>
                    <th style="padding: 20px; text-align: center;">إجراء</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $s)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 20px; font-weight: 700;">{{ $s->student->name_ar }}</td>
                    <td style="padding: 20px;">{{ $s->exam->title }}</td>
                    <td style="padding: 20px; font-weight: 800; color: #10b981;">{{ $s->total_earned_grade ?? '--' }}</td>
                    <td style="padding: 20px;">
                        <span class="chip" style="{{ $s->status == 'graded' ? 'background:#ecfdf5;color:#059669' : 'background:#fff7ed;color:#c2410c' }}">
                            {{ $s->status == 'graded' ? 'مكتمل' : 'بانتظار التصحيح' }}
                        </span>
                    </td>
                    <td style="padding: 20px; text-align: center;">
                        <a href="{{ route('admin.submissions.grade', $s->id) }}" class="btn btn-sm" style="background: #0f172a; color: white;">🔍 تصحيح</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
