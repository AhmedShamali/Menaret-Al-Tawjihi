@extends('layouts.app')
@section('content')
<div style="display: flex; flex-direction: column; gap: 20px; width: 100%; max-width: 100%;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--primary); margin: 0;">إدارة الطلاب 👥</h1>
        <a href="{{ route('students.create') }}" class="btn btn-primary" style="white-space: nowrap;">➕ إضافة طالب</a>
    </div>
    <div class="glass-card" style="padding: 0; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 12px; width: 100%; max-width: 100%;">
        <div class="table-responsive" style="overflow-x: auto; width: 100%;">
            <table style="width: 100%; min-width: 600px; border-collapse: collapse; text-align: right;">
                <thead>
                    <tr style="background: var(--primary); color: white;">
                        <th style="padding: 16px 20px;">{{ __('الطالب') }}</th>
                        <th style="padding: 16px 20px;">{{ __('المرحلة') }}</th>
                        <th style="padding: 16px 20px;">{{ __('الحالة') }}</th>
                        <th style="padding: 16px 20px; text-align: center;">{{ __('إجراءات') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $s)
                    <tr id="row_{{ $s->id }}" style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="{{ $s->photo_url }}" style="width: 40px; height: 40px; border-radius: 10px; object-fit: cover;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($s->name_ar ?? 'طالب') }}&background=0284c7&color=fff&size=50&bold=true';">
                                <div><div style="font-weight: 700;">{{ $s->name_ar }}</div><div style="font-size: 0.75rem; color: #94a3b8;">{{ $s->email }}</div></div>
                            </div>
                        </td>
                        <td style="padding: 14px 20px; font-weight: 600;">{{ $s->stage?->label_ar ?? __('توجيهي عام') }}</td>
                        <td style="padding: 14px 20px;"><span class="chip {{ $s->status == 'active' ? 'active' : '' }}">{{ $s->status == 'active' ? 'مفعل' : 'معلق' }}</span></td>
                        <td style="padding: 14px 20px; display: flex; justify-content: center; gap: 8px;">
                            <a href="{{ route('admin.students.edit', $s->id) }}" class="act-btn">✏️</a>
                            <button onclick="deleteStudent({{ $s->id }})" class="act-btn delete">🗑️</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
