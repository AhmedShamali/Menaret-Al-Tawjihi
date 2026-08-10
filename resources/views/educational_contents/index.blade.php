@extends('layouts.app')

@section('title', 'المكتبة التعليمية | إدارة الدروس')

@section('content')
<div class="educational-library-container">

    <!-- الهيدر الرئيسي -->
    <header class="dashboard-header">
        <div class="header-main-info">
            <div class="header-icon-avatar">
                📂
            </div>
            <div class="header-text-details">
                <nav class="breadcrumb-nav">
                    <span>لوحة التحكم</span>
                    <i class="fa-solid fa-chevron-left sep"></i>
                    <span class="current">المكتبة التعليمية</span>
                </nav>
                <h1 class="dashboard-title">
                    المكتبة التعليمية والدروس
                </h1>
                <p class="dashboard-subtitle">
                    تحكم كامل في دروس ومصادر المنصة التعليمية للطلاب.
                </p>
            </div>
        </div>

        <div class="header-actions">
            <!-- زر الإضافة الديناميكي -->
            <a href="{{ route(auth()->user()->role . '.educational_contents.create') }}" class="btn-primary-create">
                <i class="fa-solid fa-plus-circle"></i>
                <span>إضافة محتوى جديد</span>
            </a>
        </div>
    </header>

    <!-- شريط معلومات المحتوى -->
    <main class="dashboard-content">
        <div class="content-card">
            <div class="card-top-bar">
                <h3><i class="fa-solid fa-folder-tree"></i> قائمة المحتويات والدروس</h3>
                <span class="badge-count">{{ isset($contents) ? $contents->count() : 0 }} درس / ملف</span>
            </div>

            @if(isset($contents) && $contents->count() > 0)
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>نوع المحتوى</th>
                                <th>عنوان الدرس</th>
                                <th>المادة / المساق</th>
                                <th>الترتيب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contents as $content)
                                <tr id="row_{{ $content->id }}" class="table-row">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="content-badge {{ $content->type == 'video' ? 'video' : 'file' }}">
                                            <i class="fa-solid {{ $content->type == 'video' ? 'fa-video' : 'fa-file-lines' }}"></i>
                                            {{ $content->type == 'video' ? 'فيديو' : 'ملف / مستند' }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="content-title-text">{{ $content->title }}</strong>
                                    </td>
                                    <td>
                                        <span class="subject-tag">
                                            {{ $content->subject?->icon ?? '📚' }} {{ $content->subject?->name_ar ?? 'عام' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="order-badge">#{{ $content->order }}</span>
                                    </td>
                                    <td>
                                        <div class="actions-group">
                                            <!-- زر عرض المساق -->
                                            <a href="{{ route('subject.show', $content->subject_id) }}" class="btn-action view" title="عرض المساق">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <!-- زر التعديل -->
                                            <a href="{{ route('teacher.educational_contents.edit', $content->id) }}" class="btn-action edit" title="تعديل">                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <!-- زر الحذف التفاعلي -->
                                            <button type="button" onclick="deleteContent({{ $content->id }})" class="btn-action delete" title="حذف">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- الحالة الفارغة (Empty State) -->
                <div class="empty-dashboard-state">
                    <div class="empty-icon"><i class="fa-solid fa-folder-open"></i></div>
                    <h3>لا يوجد محتوى تعليمي مضاف حتى الآن</h3>
                    <p>يمكنك البدء بنشر أُولى الفيديوهات والملفات التعليمية لطلابك بضغطة زر.</p>
                    <a href="{{ route(auth()->user()->role . '.educational_contents.create') }}" class="btn-secondary-create">
                        <i class="fa-solid fa-plus"></i> إضافة محتوى الآن
                    </a>
                </div>
            @endif
        </div>
    </main>

</div>

{{-- المكتبات الخارجية --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const userRole = "{{ auth()->user()->role }}";

    function deleteContent(id) {
        Swal.fire({
            title: 'هل أنت متأكد من حذف هذا المحتوى؟',
            text: "لن تتمكن من استرجاع هذا الملف أو الفيديو بعد الحذف!",
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
                axios.delete(`/${userRole}/educational_contents/${id}`, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => {
                    if(res.data.success || res.status === 200) {
                        Swal.fire({ icon: 'success', title: 'تم الحذف بنجاح!', showConfirmButton: false, timer: 1200 });

                        const row = document.getElementById(`row_${id}`);
                        if (row) {
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(20px)';
                            setTimeout(() => row.remove(), 400);
                        }
                    }
                })
                .catch(err => {
                    Swal.fire('خطأ!', 'حدثت مشكلة أثناء الحذف، يرجى المحاولة لاحقاً', 'error');
                });
            }
        });
    }
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

    .educational-library-container { max-width: 1440px; margin: 0 auto; padding: 24px; }

    /* Header Styles */
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; background: var(--bg-card); padding: 24px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); margin-bottom: 24px; }
    .header-main-info { display: flex; align-items: center; gap: 20px; }
    .header-icon-avatar { width: 70px; height: 70px; border-radius: 20px; display: grid; place-items: center; font-size: 2.2rem; flex-shrink: 0; background: #eef2ff; border: 1px solid #c7d2fe; }
    .breadcrumb-nav { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 6px; font-weight: 600; }
    .breadcrumb-nav .sep { font-size: 0.7rem; color: #cbd5e1; }
    .breadcrumb-nav .current { color: var(--primary-color); }
    .dashboard-title { font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin: 0 0 6px 0; }
    .dashboard-subtitle { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
    .btn-primary-create { background: var(--primary-color); color: #ffffff; padding: 12px 24px; border-radius: var(--radius-md); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; transition: all 0.2s ease; }
    .btn-primary-create:hover { background: var(--primary-hover); transform: translateY(-2px); }

    /* Content Card & Table */
    .content-card { background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); overflow: hidden; }
    .card-top-bar { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .card-top-bar h3 { font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px; }
    .badge-count { background: #f1f5f9; color: var(--text-muted); padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; }

    .table-responsive { width: 100%; overflow-x: auto; }
    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
    .custom-table th, .custom-table td { padding: 18px 24px; border-bottom: 1px solid var(--border-color); font-size: 0.95rem; vertical-align: middle; }
    .custom-table th { background: #f8fafc; color: var(--text-muted); font-weight: 700; }

    .content-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.82rem; }
    .content-badge.video { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .content-badge.file { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }

    .content-title-text { color: var(--text-main); font-size: 1rem; }
    .subject-tag { background: #f1f5f9; padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; color: #334155; display: inline-block; }
    .order-badge { background: #f8fafc; border: 1px solid #e2e8f0; color: var(--text-muted); padding: 2px 8px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; }

    /* Actions */
    .actions-group { display: flex; gap: 8px; align-items: center; }
    .btn-action { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.95rem; }
    .btn-action.view { background: #e0e7ff; color: var(--primary-color); }
    .btn-action.view:hover { background: var(--primary-color); color: #fff; }
    .btn-action.edit { background: #f1f5f9; color: var(--text-muted); }
    .btn-action.edit:hover { background: #475569; color: #fff; }
    .btn-action.delete { background: #fef2f2; color: #ef4444; }
    .btn-action.delete:hover { background: #ef4444; color: #fff; }

    /* Empty State */
    .empty-dashboard-state { text-align: center; padding: 70px 20px; }
    .empty-dashboard-state .empty-icon { width: 80px; height: 80px; background: #f1f5f9; color: var(--text-muted); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 18px auto; }
    .empty-dashboard-state h3 { font-size: 1.3rem; font-weight: 800; color: var(--text-main); margin-bottom: 8px; }
    .empty-dashboard-state p { color: var(--text-muted); margin-bottom: 22px; font-size: 0.95rem; }
    .btn-secondary-create { display: inline-flex; align-items: center; gap: 8px; background: var(--primary-color); color: #fff; padding: 12px 24px; border-radius: var(--radius-md); text-decoration: none; font-weight: 700; transition: 0.2s; }
    .btn-secondary-create:hover { background: var(--primary-hover); }

    /* SweetAlert Custom */
    .swal2-custom-popup { font-family: inherit !important; border-radius: 16px !important; }
</style>
@endsection
