@extends('layouts.app')
@section('content')
<div style="display: flex; flex-direction: column; gap: 35px;">
    <h1 style="font-size: 2.2rem; font-weight: 900;">تحليلات المحتوى الرقمي 📈</h1>

    <div class="glass-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 20px 30px; background: #f8fafc; font-weight: 800; border-bottom: 1px solid #e2e8f0;">أكثر الدروس مشاهدة</div>
        <table style="width: 100%; border-collapse: collapse; text-align: right;">
            <thead>
                <tr style="background: var(--primary); color: white;">
                    <th style="padding: 20px;">الدرس</th>
                    <th style="padding: 20px;">المادة</th>
                    <th style="padding: 20px;">عدد المشاهدات</th>
                    <th style="padding: 20px;">تفاعل الطلاب</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_contents as $c)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 20px; font-weight: 700;">{{ $c->title }}</td>
                    <td style="padding: 20px;">{{ $c->subject->name_ar }}</td>
                    <td style="padding: 20px; font-weight: 800; color: var(--accent);">{{ $c->views_count }}</td>
                    <td style="padding: 20px;">
                        <div style="width: 100px; height: 8px; background: #f1f5f9; border-radius: 10px;">
                            <div style="width: {{ ($c->views_count / 100) * 100 }}%; height: 100%; background: var(--accent); border-radius: 10px;"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
