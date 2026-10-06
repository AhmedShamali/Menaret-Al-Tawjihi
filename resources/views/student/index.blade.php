@extends('layouts.app')

@section('title', 'إدارة الطلاب')

@section('content')
<div class="ed-legacy-students-page">

    {{-- رأس الصفحة --}}
    <div class="page-header-wrap">
        <div>
            <h1 class="page-title">سجل الطلاب والطلبات 👥</h1>
            <p class="page-desc">{{ __('إدارة وتفعيل حسابات طلاب منصة Step by Step والمراجعة الأكاديمية.') }}</p>
        </div>
        <a href="{{ route('students.create') }}" class="btn-add-student">
            ➕ إضافة طالب جديد
        </a>
    </div>

    {{-- جدول الطلاب المصمم بأسلوب الجامعات --}}
    <div class="table-responsive" style="background: #ffffff; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04); overflow-x: auto;">
        <table style="width: 100%; min-width: 680px; border-collapse: collapse; text-align: right;">
            <thead>
                <tr style="background: #1e3a8a; color: white;">
                    <th style="padding: 25px;">{{ __('الطالب') }}</th>
                    <th style="padding: 25px;">{{ __('المرحلة الدراسية') }}</th>
                    <th style="padding: 25px;">{{ __('رقم الهوية') }}</th>
                    <th style="padding: 25px;">{{ __('الحالة') }}</th>
                    <th style="padding: 25px; text-align: center;">{{ __('الإجراءات') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr id="row_{{ $student->id }}" class="student-row" style="border-bottom: 1px solid #f1f5f9; transition: 0.3s;">
                    <td style="padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <img src="{{ $student->photo_url }}" style="width: 50px; height: 50px; border-radius: 14px; object-fit: cover; border: 2px solid #f1f5f9;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($student->name_ar ?? 'طالب') }}&background=0284c7&color=fff&size=100&bold=true';">
                            <div>
                                <div style="font-weight: 700; color: var(--primary);">{{ $student->name_ar }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-light);">{{ $student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 20px; font-weight: 600; color: var(--primary);">
                        {{ $student->stage->label_ar ?? 'غير محدد' }}
                    </td>
                    <td style="padding: 20px; font-family: monospace; font-weight: 700; color: var(--text-light);">
                        {{ $student->nid }}
                    </td>
                    <td style="padding: 20px;">
                        <span class="status-chip {{ $student->status == 'active' ? 'active' : 'pending' }}">
                            {{ $student->status == 'active' ? 'مفعّل' : 'قيد المراجعة' }}
                        </span>
                    </td>
                    <td style="padding: 20px;">
                        <div style="display: flex; justify-content: center; gap: 10px;">

                            {{-- زر التفعيل والتعطيل الذكي بالأيقونات الملونة --}}
                            <button onclick="performToggle({{ $student->id }})"
                                    class="act-icon {{ $student->status == 'active' ? 'status-active' : 'status-inactive' }}"
                                    title="{{ $student->status == 'active' ? 'تعطيل الحساب' : 'تفعيل الحساب' }}">
                                @if($student->status == 'active')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                @else
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                @endif
                            </button>

                            {{-- زر التعديل --}}
                            <a href="{{ route('admin.students.edit', $student->id) }}" class="act-icon edit-btn" title="{{ __('تعديل البيانات') }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </a>

                            {{-- زر الحذف --}}
                            <button onclick="deleteStudent({{ $student->id }})" class="act-icon delete-btn" title="{{ __('حذف الطالب') }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .ed-legacy-students-page {
        display: flex;
        flex-direction: column;
        gap: 25px;
        animation: fadeIn 0.6s ease;
    }
    .page-header-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .page-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--primary, #1e3a8a);
        margin: 0 0 6px 0;
    }
    .page-desc {
        color: var(--text-light, #64748b);
        margin: 0;
        font-size: 0.95rem;
    }
    .btn-add-student {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #0284c7;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
    }
    .btn-add-student:hover {
        background: #0369a1;
        transform: translateY(-2px);
        color: #fff;
    }

    /* التنسيقات الفخمة للحالات */
    .status-chip { padding: 6px 15px; border-radius: 10px; font-size: 0.75rem; font-weight: 700; }
    .status-chip.active { background: #ecfdf5; color: #059669; }
    .status-chip.pending { background: #fff7ed; color: #c2410c; }

    /* تنسيق الأيقونات */
    .act-icon { width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; display: grid; place-items: center; transition: 0.3s; cursor: pointer; text-decoration: none; }

    .status-active { color: #059669; background: #ecfdf5; border-color: #10b981; }
    .status-active:hover { background: #d1fae5; transform: scale(1.1); }

    .status-inactive { color: #64748b; background: #f1f5f9; }
    .status-inactive:hover { color: #ef4444; background: #fef2f2; transform: scale(1.1) rotate(10deg); }

    .edit-btn { color: var(--accent); }
    .edit-btn:hover { background: #ecfdf5; border-color: var(--accent); transform: translateY(-3px); }

    .delete-btn { color: #94a3b8; }
    .delete-btn:hover { color: #ef4444; background: #fef2f2; border-color: #fca5a5; transform: translateY(-3px); }

    .student-row:hover { background: #fcfcfd; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 768px) {
        .page-header-wrap {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .btn-add-student {
            width: 100%;
            text-align: center;
            padding: 12px 18px;
        }
        .page-title {
            font-size: 1.4rem;
        }
    }
</style>

<script>
    // 1. وظيفة التفعيل والتعطيل (Toggle)
    function performToggle(id) {
        axios.post(`/admin/students/toggle-status/${id}`)
        .then(function (res) {
            Swal.fire({
                icon: res.data.icon,
                title: res.data.title,
                showConfirmButton: false,
                timer: 1500
            }).then(() => location.reload());
        })
        .catch(function (err) {
            console.error(err);
            Swal.fire('خطأ في العملية', 'تعذر تحديث حالة الطالب', 'error');
        });
    }

    // 2. وظيفة الحذف (Delete)
    function deleteStudent(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم حذف بيانات الطالب نهائياً من النظام",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'نعم، احذف الآن',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/admin/students/${id}`)
                .then(res => {
                    if(res.data.success) {
                        document.getElementById(`row_${id}`).remove();
                        Swal.fire('تم الحذف!', '', 'success');
                    }
                });
            }
        });
    }
</script>
@endsection
