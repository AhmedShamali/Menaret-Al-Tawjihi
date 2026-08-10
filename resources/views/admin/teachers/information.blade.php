@extends('layouts.app')

@section('content')
<!-- تنسيقات CSS مخصصة بالكامل لضبط التصميم -->
<style>
    /* الحاوية الرئيسية */
    .dashboard-container {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #334155;
        padding: 25px;
        background-color: #f8fafc;
        min-height: 100vh;
    }

    /* رأس الصفحة */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .page-title h2 {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .btn-add-new {
        background-color: #4f46e5;
        color: white !important;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none !important;
        font-weight: 600;
        transition: 0.3s;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
    }

    .btn-add-new:hover {
        background-color: #4338ca;
        transform: translateY(-2px);
    }

    /* تصميم البطاقة والجدول */
    .content-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .custom-table thead {
        background-color: #f1f5f9;
    }

    .custom-table th {
        padding: 16px;
        text-align: right; /* المحاذاة لليمين */
        font-size: 13px;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
    }

    .custom-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 14px;
    }

    .custom-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* تنسيق بيانات المعلم */
    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        object-fit: cover;
        background-color: #e2e8f0;
        border: 1px solid #cbd5e1;
    }

    .user-name {
        font-weight: 600;
        color: #1e293b;
        display: block;
    }

    .user-status {
        font-size: 11px;
        color: #10b981;
        display: block;
    }

    /* الأوسمة (Badges) */
    .badge-major {
        background-color: #eff6ff;
        color: #2563eb;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
    }

    /* أزرار التحكم */
    .action-group {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .action-btn {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        text-decoration: none !important;
        transition: 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-view { background-color: #f1f5f9; color: #64748b; }
    .btn-view:hover { background-color: #e2e8f0; color: #1e293b; }

    .btn-edit { background-color: #ecfdf5; color: #059669; }
    .btn-edit:hover { background-color: #d1fae5; }

    .btn-delete { background-color: #fff1f2; color: #e11d48; }
    .btn-delete:hover { background-color: #ffe4e6; }

    /* تحسين البحث */
    .filter-section {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
    }

    .search-input {
        padding: 8px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        width: 250px;
        outline: none;
    }

    .search-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
    }
</style>

<div class="dashboard-container" dir="rtl">

    <!-- رأس الصفحة -->
    <div class="page-header">
        <div class="page-title">
            <h2>سجل المعلمين</h2>
            <small style="color: #64748b;">إدارة بيانات المعلمين وصلاحياتهم في المنصة</small>
        </div>
        <a href="{{ route('admin.teachers.create') }}" class="btn-add-new">
            <i class="fas fa-plus-circle me-1"></i> إضافة معلم جديد
        </a>
    </div>


    <!-- جدول البيانات -->
    <div class="content-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>المعلم</th>
                        <th>البريد الإلكتروني</th>
                        <th>التخصص</th>
                        <th>تاريخ الانضمام</th>
                        <th style="text-align: center;">التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $index => $teacher)
                    <tr>
                        <td>{{ $teachers->firstItem() + $index }}</td>
                        <td>
                            <div class="user-profile">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" class="user-avatar">
                                @else
                                    <div class="user-avatar d-flex align-items-center justify-content-center bg-light text-muted">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                                <div>
                                    <span class="user-name">{{ $teacher->name }}</span>
                                </div>
                            </div>
                        </td>
                        <td dir="ltr" style="color: #64748b;">{{ $teacher->email }}</td>
                        <td>
                            <span class="badge-major">{{ $teacher->major ?? 'غير محدد' }}</span>
                        </td>
                        <td>
                            <div style="font-weight: 500;">{{ $teacher->created_at->format('Y-m-d') }}</div>
                            <small class="text-muted" style="font-size: 11px;">{{ $teacher->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="action-btn btn-view" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="action-btn btn-edit" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟');" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete" title="حذف">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 50px; color: #94a3b8;">
                            <i class="fas fa-users-slash fa-3x mb-3"></i>
                            <p>لا يوجد معلمين مسجلين في النظام حتى الآن.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- الترقيم -->
    <div class="d-flex justify-content-center mt-4">
        {{ $teachers->links() }}
    </div>
</div>
@endsection
