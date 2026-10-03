@extends('layouts.app')
@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; width: 100%; max-width: 100%;">
    <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--primary, #0f172a); margin: 0;">تحليل بيانات الاختبار: {{ $exam->title }} 📊</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; width: 100%;">
        <div class="glass-card" style="padding: 20px 24px; border-inline-start: 5px solid #3b82f6; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 700;">{{ __('متوسط درجات الطلاب') }}</span>
            <h2 style="font-size: 2rem; margin-top: 8px; color: #1e293b; font-weight: 900;">{{ number_format($stats['avg'], 1) }}</h2>
        </div>
        <div class="glass-card" style="padding: 20px 24px; border-inline-start: 5px solid #10b981; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 700;">{{ __('أعلى درجة في المساق') }}</span>
            <h2 style="font-size: 2rem; margin-top: 8px; color: #1e293b; font-weight: 900;">{{ $stats['max'] }}</h2>
        </div>
        <div class="glass-card" style="padding: 20px 24px; border-inline-start: 5px solid #f59e0b; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 700;">{{ __('إجمالي التسليمات') }}</span>
            <h2 style="font-size: 2rem; margin-top: 8px; color: #1e293b; font-weight: 900;">{{ $stats['count'] }}</h2>
        </div>
    </div>

    {{-- جدول ترتيب الطلاب --}}
    <div class="glass-card" style="padding: 0; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 14px; background: #fff; width: 100%; max-width: 100%;">
        <div style="padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-weight: 800; color: #0f172a;">{{ __('ترتيب المتفوقين') }}</div>
        <div class="table-responsive" style="overflow-x: auto; width: 100%;">
            <table style="width: 100%; min-width: 520px; border-collapse: collapse; text-align: right;">
                <thead>
                    <tr style="color: #64748b; font-size: 0.82rem; background: #ffffff; border-bottom: 2px solid #f1f5f9;">
                        <th style="padding: 14px 18px;">{{ __('الطالب') }}</th>
                        <th style="padding: 14px 18px;">{{ __('الدرجة') }}</th>
                        <th style="padding: 14px 18px; text-align: center;">{{ __('الحالة') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exam->submissions->sortByDesc('total_earned_grade') as $s)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 18px; font-weight: 700; color: #1e293b;">{{ $s->student?->name_ar ?? $s->student?->name ?? __('طالب') }}</td>
                        <td style="padding: 14px 18px; font-weight: 900; color: #2563eb;">{{ $s->total_earned_grade }}</td>
                        <td style="padding: 14px 18px; text-align: center;">
                            <span class="chip" style="background:#ecfdf5; color:#059669; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.78rem;">{{ $s->total_earned_grade > ($exam->questions->sum('points') / 2) ? 'ناجح' : 'راسب' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
