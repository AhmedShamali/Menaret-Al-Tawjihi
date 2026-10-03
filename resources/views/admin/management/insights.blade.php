@extends('layouts.app')
@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; width: 100%; max-width: 100%;">
    <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--primary, #0f172a); margin: 0;">تحليلات المحتوى الرقمي 📈</h1>

    <div class="glass-card" style="padding: 0; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 14px; background: #fff; width: 100%; max-width: 100%;">
        <div style="padding: 16px 20px; background: #f8fafc; font-weight: 800; border-bottom: 1px solid #e2e8f0; color: #0f172a;">{{ __('أكثر الدروس مشاهدة') }}</div>
        <div class="table-responsive" style="overflow-x: auto; width: 100%;">
            <table style="width: 100%; min-width: 600px; border-collapse: collapse; text-align: right;">
                <thead>
                    <tr style="background: var(--primary, #1e3a8a); color: white;">
                        <th style="padding: 14px 18px;">{{ __('الدرس') }}</th>
                        <th style="padding: 14px 18px;">{{ __('المادة') }}</th>
                        <th style="padding: 14px 18px;">{{ __('عدد المشاهدات') }}</th>
                        <th style="padding: 14px 18px;">{{ __('تفاعل الطلاب') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($top_contents as $c)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 18px; font-weight: 700; color: #1e293b;">{{ $c->title }}</td>
                        <td style="padding: 14px 18px; color: #475569;">{{ $c->subject?->name_ar ?? $c->subject?->name ?? __('مادة عامة') }}</td>
                        <td style="padding: 14px 18px; font-weight: 800; color: var(--accent, #0284c7);">{{ $c->views_count }}</td>
                        <td style="padding: 14px 18px;">
                            <div style="width: 100px; height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden;">
                                <div style="width: {{ min(100, max(5, ($c->views_count / 100) * 100)) }}%; height: 100%; background: var(--accent, #0284c7); border-radius: 10px;"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
