@extends('layouts.app')
@section('content')
<div style="display: flex; flex-direction: column; gap: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--primary);">إدارة الطلاب 👥</h1>
        <a href="{{ route('students.create') }}" class="btn btn-primary">➕ إضافة طالب</a>
    </div>
    <div class="glass-card" style="padding: 0; overflow: hidden; border: none;">
        <table style="width: 100%; border-collapse: collapse; text-align: right;">
            <thead>
                <tr style="background: var(--primary); color: white;">
                    <th style="padding: 20px;">الطالب</th>
                    <th style="padding: 20px;">المرحلة</th>
                    <th style="padding: 20px;">الحالة</th>
                    <th style="padding: 20px; text-align: center;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $s)
                <tr id="row_{{ $s->id }}" style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="{{ asset('storage/'.$s->photo) }}" style="width: 45px; height: 45px; border-radius: 12px; object-fit: cover;">
                            <div><div style="font-weight: 700;">{{ $s->name_ar }}</div><div style="font-size: 0.7rem; color: #94a3b8;">{{ $s->email }}</div></div>
                        </div>
                    </td>
                    <td style="padding: 20px; font-weight: 600;">{{ $s->stage->label_ar }}</td>
                    <td style="padding: 20px;"><span class="chip {{ $s->status == 'active' ? 'active' : '' }}">{{ $s->status == 'active' ? 'مفعل' : 'معلق' }}</span></td>
                    <td style="padding: 20px; display: flex; justify-content: center; gap: 8px;">
                        <a href="{{ route('students.edit', $s->id) }}" class="act-btn">✏️</a>
                        <button onclick="deleteStudent({{ $s->id }})" class="act-btn delete">🗑️</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
