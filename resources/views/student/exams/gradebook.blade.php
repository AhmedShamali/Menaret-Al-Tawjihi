@extends('layouts.app')

@section('title', 'سجل درجاتي الأكاديمي')

@section('content')
<div class="gradebook-page-wrap">

    {{-- هيدر ملخص الأداء بتصميم أكاديمي متجاوب --}}
    <div class="gradebook-hero-card">
        <div class="gradebook-hero-info">
            <h1 class="gradebook-hero-title">سجل الدرجات العام 🎓</h1>
            <p class="gradebook-hero-desc">{{ __('أهلاً بك، إليك تفاصيل أدائك ونتائجك في الاختبارات المكتملة.') }}</p>
        </div>
        <div class="gradebook-gpa-badge">
            <div class="gpa-label">{{ __('المعدل التراكمي') }}</div>
            <div class="gpa-val">{{ number_format($submissions->avg('total_earned_grade'), 1) }}</div>
        </div>
    </div>

    {{-- جدول النتائج --}}
    <div class="glass-card gradebook-card">
        <div class="gradebook-card-header">{{ __('قائمة النتائج التفصيلية') }}</div>
        <div class="table-responsive">
            <table class="gradebook-table">
                <thead>
                    <tr>
                        <th>{{ __('المادة الدراسية') }}</th>
                        <th>{{ __('عنوان الاختبار') }}</th>
                        <th>{{ __('الدرجة النهائية') }}</th>
                        <th>{{ __('الحالة الأكاديمية') }}</th>
                        <th style="text-align: center;">{{ __('التقرير') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $s)
                    <tr class="gradebook-row">
                        <td>
                            <span class="chip" style="background: {{ $s->exam?->subject?->color ?? '#1e40af' }}15; color: {{ $s->exam?->subject?->color ?? '#1e40af' }}; font-weight: 800; padding: 6px 12px; border-radius: 8px;">
                                {{ $s->exam?->subject?->name_ar ?? $s->exam?->subject?->name ?? __('مادة دراسية') }}
                            </span>
                        </td>
                        <td style="font-weight: 700; color: var(--primary, #1e3a8a);">{{ $s->exam?->title ?? __('اختبار') }}</td>
                        <td>
                            <div style="font-size: 1.15rem; font-weight: 900; color: #2563eb;">
                                {{ $s->total_earned_grade }} <span style="font-size: 0.8rem; color: #94a3b8;">/ {{ $s->exam?->questions?->sum('points') ?? $s->exam?->total_grade ?? 100 }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="chip" style="{{ $s->status == 'graded' ? 'background:#ecfdf5;color:#059669' : 'background:#fff7ed;color:#c2410c' }}; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem;">
                                {{ $s->status == 'graded' ? 'مكتمل التصحيح' : 'قيد المراجعة' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('student.exams.result', $s->id) }}" class="btn btn-sm btn-grade-view">
                                عرض النتيجة ←
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="gradebook-empty-cell">
                            <div style="font-size: 2.5rem; margin-bottom: 12px;">📂</div>
                            <p>{{ __('لا يوجد اختبارات مسجلة في سجلك حتى الآن.') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .gradebook-page-wrap {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 100%;
        animation: fadeIn 0.4s ease;
    }
    .gradebook-hero-card {
        background: linear-gradient(135deg, var(--primary, #1e3a8a), #0f172a);
        color: #ffffff;
        padding: 28px 32px;
        border-radius: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }
    .gradebook-hero-title {
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin: 0 0 6px 0;
    }
    .gradebook-hero-desc {
        opacity: 0.85;
        font-size: 0.95rem;
        margin: 0;
    }
    .gradebook-gpa-badge {
        text-align: center;
        background: rgba(255, 255, 255, 0.12);
        padding: 14px 28px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        min-width: 120px;
    }
    .gpa-label {
        font-size: 0.76rem;
        opacity: 0.85;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .gpa-val {
        font-size: 2.2rem;
        font-weight: 900;
        color: #34d399;
        line-height: 1.1;
    }
    .gradebook-card {
        padding: 0;
        overflow: hidden;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
        width: 100%;
        max-width: 100%;
    }
    .gradebook-card-header {
        padding: 16px 22px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 800;
        font-size: 0.95rem;
        color: var(--primary, #1e3a8a);
    }
    .gradebook-table {
        width: 100%;
        min-width: 600px;
        border-collapse: collapse;
        text-align: right;
    }
    .gradebook-table thead tr {
        background: #ffffff;
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 700;
        border-bottom: 2px solid #f1f5f9;
    }
    .gradebook-table th {
        padding: 14px 18px;
        white-space: nowrap;
    }
    .gradebook-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.88rem;
    }
    .gradebook-row:hover {
        background: #f8fafc;
    }
    .btn-grade-view {
        background: #f1f5f9;
        color: var(--primary, #1e3a8a);
        border-radius: 8px;
        font-weight: 700;
        padding: 7px 14px;
        font-size: 0.82rem;
        text-decoration: none;
        display: inline-block;
        transition: 0.2s;
    }
    .btn-grade-view:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .gradebook-empty-cell {
        padding: 60px 20px;
        text-align: center;
        color: #94a3b8;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 768px) {
        .gradebook-hero-card {
            padding: 20px 18px;
            border-radius: 14px;
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }
        .gradebook-hero-title {
            font-size: 1.4rem;
        }
        .gradebook-gpa-badge {
            align-self: center;
            width: 100%;
            max-width: 220px;
            padding: 10px 18px;
        }
        .gpa-val {
            font-size: 1.8rem;
        }
    }
</style>
@endsection
