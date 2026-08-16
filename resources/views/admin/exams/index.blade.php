@extends('layouts.app')

@section('title', 'بوابة المساقات | إدارة التقييمات والاختبارات')

<!-- استدعاء مكتبة SweetAlert2 للتنبيهات الفاخرة -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
<div class="exams-dashboard-container">

    <!-- الهيدر الرئيسي -->
    <header class="dashboard-header">
        <div class="header-main-info">
            <div class="subject-icon-avatar {{ auth()->user()->subject_id ? 'has-subject' : 'no-subject' }}">
                {{ auth()->user()->subject?->icon ?? '📚' }}
            </div>
            <div class="header-text-details">
                <nav class="breadcrumb-nav">
                    <span>لوحة التحكم</span>
                    <i class="fa-solid fa-chevron-left sep"></i>
                    <span class="current">إدارة التقييمات</span>
                </nav>
                <h1 class="dashboard-title">
                    بوابة مساق: {{ auth()->user()->subject?->name_ar ?? auth()->user()->subject?->name ?? 'جميع المساقات الأكاديمية' }}
                </h1>
                <p class="dashboard-subtitle">
                    أهلاً بك يا <strong>{{ auth()->user()->name }}</strong>، يمكنك من هنا إدارة كافة الاختبارات وتتبع نتائج الطلاب.
                </p>
            </div>
        </div>

        <div class="header-actions">
            <!-- رابط الإنشاء الديناميكي بحسب دور المستخدم -->
            <a href="{{ route(auth()->user()->role . '.exams.create') }}" class="btn-primary-create">
                <i class="fa-solid fa-plus-circle"></i>
                <span>إعداد اختبار جديد</span>
            </a>
        </div>
    </header>

    <!-- شريط تنبيه يربط مباشرة مع المجلد الإداري Management -->
    @if(!auth()->user()->subject_id)
        <div class="alert-info-banner">
            <div class="alert-content">
                <i class="fa-solid fa-triangle-exclamation icon"></i>
                <div>
                    <strong>تنبيه إداري:</strong> هذا الحساب غير مرتبط بمادة تعليمية محددة حالياً.
                    <span class="sub-text">يمكنك إسناد مادة علمية لجميع المعلمين من خلال قسم إعدادات الإدارة.</span>
                </div>
            </div>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.management.index') }}" class="btn-link-fix">الانتقال للإدارة وتحديد المادة</a>
            @endif
        </div>
    @endif

    <!-- جدول عرض الاختبارات -->
    <main class="dashboard-content">
        <div class="content-card">
            <div class="card-top-bar">
                <h3><i class="fa-solid fa-list-check"></i> قائمة التقييمات المنشورة</h3>
                <span class="badge-count">{{ isset($exams) ? $exams->count() : 0 }} اختبار</span>
            </div>

            @if(isset($exams) && $exams->count() > 0)
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>عنوان الاختبار</th>
                                <th>المادة / المساق</th>
                                <th>المدة الزمنية</th>
                                <th>عدد الأسئلة</th>
                                <th>تاريخ النشر</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exams as $exam)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $exam->title }}</strong></td>
                                    <td>
                                        <span class="subject-tag">
                                            {{ $exam->subject?->icon ?? auth()->user()->subject?->icon ?? '📚' }}
                                            {{ $exam->subject?->name_ar ?? $exam->subject?->name ?? auth()->user()->subject?->name_ar ?? auth()->user()->subject?->name ?? 'عام' }}
                                        </span>
                                    </td>
                                    <td><i class="fa-regular fa-clock"></i> {{ $exam->duration_minutes }} دقيقة</td>
                                    <td>{{ $exam->questions_count ?? 0 }} سؤال</td>
                                    <td>{{ $exam->created_at ? $exam->created_at->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        <div class="actions-group">
                                            <!-- روابط الإجراءات الديناميكية بحسب الدور (معلم أو مدير) -->
                                            <a href="{{ route(auth()->user()->role . '.exams.submissions', $exam->id) }}" class="btn-action view" title="إجابات الطلاب">
                                                <i class="fa-solid fa-users"></i>
                                            </a>
                                            <a href="{{ route(auth()->user()->role . '.exams.edit', $exam->id) }}" class="btn-action edit" title="تعديل الاختبار">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>

                                            <!-- فورم الحذف التفاعلي مع SweetAlert2 -->
                                            <form id="delete-form-{{ $exam->id }}" action="{{ route(auth()->user()->role . '.exams.destroy', $exam->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn-action delete" onclick="confirmDelete({{ $exam->id }})" title="حذف الاختبار">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-dashboard-state">
                    <div class="empty-icon"><i class="fa-solid fa-folder-open"></i></div>
                    <h3>لا توجد اختبارات منشورة حتى الآن</h3>
                    <p>يمكنك البدء بإنشاء أول اختبار للطلاب من خلال الضغط على زر "إعداد اختبار جديد".</p>
                    <a href="{{ route(auth()->user()->role . '.exams.create') }}" class="btn-secondary-create">
                        <i class="fa-solid fa-plus"></i> إنشاء اختبار الآن
                    </a>
                </div>
            @endif
        </div>
    </main>

</div>

<!-- JavaScript لتأكيد الحذف وإظهار رسائل الجلسة (Session Alerts) -->
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'هل أنت تأكد من رغبتك في حذف هذا الاختبار؟',
        text: "سيتم حذف جميع الأسئلة والتسليمات المرتبطة به نهائياً!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'نعم، قم بالحذف',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
        customClass: {
            popup: 'swal2-custom-popup'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// إظهار التنبيهات القادمة من الـ Controller تلقائياً
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'تم بنجاح!',
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false,
        customClass: { popup: 'swal2-custom-popup' }
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'عذراً!',
        text: "{{ session('error') }}",
        customClass: { popup: 'swal2-custom-popup' }
    });
@endif
</script>

<style>
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --bg-body: #f8fafc;
        --bg-card: #ffffff;
        --border-color: #e2e8f0;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --radius-lg: 20px;
        --radius-md: 12px;
        --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .exams-dashboard-container { max-width: 1440px; margin: 0 auto; padding: 24px; }
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; background: var(--bg-card); padding: 24px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); margin-bottom: 24px; }
    .header-main-info { display: flex; align-items: center; gap: 20px; }
    .subject-icon-avatar { width: 70px; height: 70px; border-radius: 20px; display: grid; place-items: center; font-size: 2.2rem; flex-shrink: 0; }
    .subject-icon-avatar.has-subject { background: #ecfdf5; border: 1px solid #a7f3d0; }
    .subject-icon-avatar.no-subject { background: #f1f5f9; border: 1px solid #cbd5e1; }
    .breadcrumb-nav { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 6px; font-weight: 600; }
    .breadcrumb-nav .sep { font-size: 0.7rem; color: #cbd5e1; }
    .breadcrumb-nav .current { color: var(--primary-color); }
    .dashboard-title { font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin: 0 0 6px 0; }
    .dashboard-subtitle { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
    .btn-primary-create { background: var(--primary-color); color: #ffffff; padding: 12px 24px; border-radius: var(--radius-md); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; transition: all 0.2s ease; }
    .btn-primary-create:hover { background: var(--primary-hover); transform: translateY(-2px); }

    .alert-info-banner { background: #fffbebfb; border: 1px solid #fde68a; color: #92400e; padding: 16px 20px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .alert-content { display: flex; align-items: center; gap: 12px; font-size: 0.95rem; }
    .alert-content .icon { font-size: 1.3rem; color: #d97706; }
    .sub-text { display: block; font-size: 0.8rem; color: #b45309; margin-top: 2px; }
    .btn-link-fix { background: #fef3c7; color: #92400e; padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 700; border: 1px solid #fcd34d; }

    .content-card { background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); overflow: hidden; }
    .card-top-bar { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .card-top-bar h3 { font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px; }
    .badge-count { background: #f1f5f9; color: var(--text-muted); padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; }

    .table-responsive { width: 100%; overflow-x: auto; }
    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
    .custom-table th, .custom-table td { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-size: 0.95rem; }
    .custom-table th { background: #f8fafc; color: var(--text-muted); font-weight: 700; }
    .subject-tag { background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; color: #334155; }
    .actions-group { display: flex; gap: 8px; align-items: center; }
    .btn-action { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; }
    .btn-action.view { background: #e0e7ff; color: var(--primary-color); }
    .btn-action.view:hover { background: #4338ca; color: #fff; }
    .btn-action.edit { background: #f1f5f9; color: var(--text-muted); }
    .btn-action.edit:hover { background: #475569; color: #fff; }
    .btn-action.delete { background: #fef2f2; color: #ef4444; }
    .btn-action.delete:hover { background: #ef4444; color: #fff; }

    .empty-dashboard-state { text-align: center; padding: 60px 20px; }
    .empty-dashboard-state .empty-icon { width: 70px; height: 70px; background: #f1f5f9; color: var(--text-muted); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 16px auto; }
    .empty-dashboard-state h3 { font-size: 1.2rem; font-weight: 800; color: var(--text-main); margin-bottom: 8px; }
    .empty-dashboard-state p { color: var(--text-muted); margin-bottom: 20px; }
    .btn-secondary-create { display: inline-flex; align-items: center; gap: 8px; background: var(--primary-color); color: #fff; padding: 10px 20px; border-radius: var(--radius-md); text-decoration: none; font-weight: 700; }

    /* تخصيص SweetAlert2 ليتماشى مع الصفحة */
    .swal2-custom-popup {
        font-family: inherit !important;
        border-radius: 16px !important;
        padding: 20px !important;
    }
    .swal2-title {
        font-size: 1.2rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .swal2-html-container {
        font-size: 0.9rem !important;
        color: #64748b !important;
    }
    .swal2-confirm, .swal2-cancel {
        border-radius: 10px !important;
        padding: 8px 20px !important;
        font-weight: 700 !important;
        font-size: 0.85rem !important;
    }
</style>
@endsection