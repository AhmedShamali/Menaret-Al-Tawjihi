@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .custom-container {
        font-family: 'Cairo', sans-serif;
        padding: 30px;
        background-color: #fdfeff;
        direction: rtl;
        text-align: right;
    }
    .header-section {
        margin-bottom: 30px;
    }
    .title-wrapper h2 {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 5px;
    }
    .btn-add-new {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        padding: 10px 22px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        transition: 0.2s;
    }
    .btn-add-new:hover {
        opacity: 0.95;
        color: white;
        transform: translateY(-1px);
    }
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }
    .modern-table thead th {
        background: transparent;
        border: none;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 10px 20px;
        text-transform: uppercase;
    }
    .modern-table tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }
    .modern-table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.05);
    }
    .modern-table td {
        padding: 18px 20px;
        border: none;
        vertical-align: middle;
    }
    .modern-table td:first-child { border-radius: 0 12px 12px 0; }
    .modern-table td:last-child { border-radius: 12px 0 0 12px; }

    .student-row-box {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .initial-avatar {
        width: 40px;
        height: 40px;
        background: #f1f5f9;
        color: #6366f1;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
    }
    .profile-link-btn {
        background: #eef2ff;
        color: #4f46e5;
        border: 1px solid #e0e7ff;
        padding: 7px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: 0.2s;
    }
    .profile-link-btn:hover {
        background: #4f46e5;
        color: white;
    }
    .date-box {
        color: #64748b;
        font-size: 0.9rem;
    }
</style>

<div class="custom-container">
    <div class="header-section d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="title-wrapper">
            <h2>سجل الطلاب</h2>
        </div>

    </div>

    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم الطالب</th>
                    <th>البريد الإلكتروني</th>
                    <th>تاريخ الانضمام</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td style="font-weight: 700; color: #64748b;">#{{ $student->id }}</td>
                    <td>
                        <div class="student-row-box">
                            <div class="initial-avatar">
                                {{ mb_substr($student->name_ar ?? $student->name ?? 'ط', 0, 1) }}
                            </div>
                            <div>
                                <span style="font-weight: 700; color: #1e293b; display: block;">{{ $student->name_ar ?? $student->name }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="color: #64748b; font-size: 0.9rem;">{{ $student->email }}</span>
                    </td>
                    <td>
                        <div class="date-box" dir="ltr">
                            <i class="fa-regular fa-calendar-days me-1" style="color: #94a3b8;"></i>
                            {{ $student->created_at ? $student->created_at->format('Y-m-d') : '—' }}
                        </div>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.students.show', $student->id) }}" class="profile-link-btn">
                            <i class="fa-solid fa-id-card"></i> الملف الشخصي
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="py-4">
                            <i class="fa-solid fa-users-slash fa-3x text-muted mb-3" style="opacity: 0.4;"></i>
                            <h5 class="text-muted">لا توجد سجلات طلاب حالياً</h5>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $students->links() }}
    </div>
</div>
@endsection
