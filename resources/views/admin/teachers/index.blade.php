@extends('layouts.app')

@section('title', 'قائمة المعلمين')

@section('content')
<div style="max-width: 1300px; margin: 0 auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; letter-spacing: -0.5px; margin-bottom: 5px;">قائمة المعلمين</h1>
        </div>
    </div>

    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.02); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; white-space: nowrap;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 16px 24px;">#</th>
                        <th style="padding: 16px 24px;">اسم المعلم</th>
                        <th style="padding: 16px 24px;">البريد الإلكتروني</th>
                        <th style="padding: 16px 24px;">تاريخ الانضمام</th>
                        <th style="padding: 16px 24px; text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">

                        <td style="padding: 16px 24px; font-size: 0.88rem; font-weight: 600; color: #94a3b8;">
                            #{{ $teacher->id }}
                        </td>

                        <td style="padding: 16px 24px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @if(!empty($teacher->photo))
                                    <!-- عرض الصورة من مسار التخزين العام بناءً على قاعدة البيانات -->
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->name }}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0;">
                                @else
                                    <!-- عرض الحرف الأول في حال كانت الصورة فارغة NULL -->
                                    <div style="width: 38px; height: 38px; background: #fdf4ff; color: #c026d3; border-radius: 50%; display: grid; place-items: center; font-weight: 700; font-size: 0.9rem; border: 2px solid #f3e8ff;">
                                        {{ mb_substr($teacher->name, 0, 1) }}
                                    </div>
                                @endif
                                <span style="font-size: 0.9rem; font-weight: 600; color: #1e293b;">{{ $teacher->name }}</span>
                            </div>
                        </td>

                        <td style="padding: 16px 24px; font-size: 0.88rem; color: #64748b;">
                            {{ $teacher->email }}
                        </td>

                        <td style="padding: 16px 24px; font-size: 0.85rem; color: #64748b;">
                            <div style="display: inline-flex; align-items: center; gap: 6px; background: #f1f5f9; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                <i class="fa-regular fa-calendar" style="color: #4f46e5;"></i>
                                {{ $teacher->created_at->format('Y-m-d') }}
                            </div>
                        </td>

                        <td style="padding: 16px 24px; text-align: center;">
                            <a href="{{ route('admin.teachers.show', $teacher->id) }}" style="background: #eef2ff; color: #4f46e5; padding: 7px 14px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#4f46e5';this.style.color='#fff'" onmouseout="this.style.background='#eef2ff';this.style.color='#4f46e5'">
                                <i class="fa-solid fa-eye"></i> الملف الشخصي
                            </a>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8; font-size: 0.9rem;">
                            <i class="fa-solid fa-chalkboard-user" style="font-size: 2.5rem; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
                            لا يوجد معلمون مسجلون حتى الآن.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
