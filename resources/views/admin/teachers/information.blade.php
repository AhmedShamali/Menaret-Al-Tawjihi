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

<div class="dashboard-container">

    <!-- رأس الصفحة -->
    <div class="page-header">
        <div class="page-title">
            <h2>{{ __('سجل وبيانات المعلمين') }}</h2>
            <small style="color: #64748b;">{{ __('إدارة بيانات الكادر التعليمي، كلمات المرور، والتصدير والاستيراد') }}</small>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <button type="button" onclick="confirmPurgeAllTeachers()" style="background: #dc2626; color: white; border: none; padding: 9px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);" title="{{ __('حذف وتصفير جميع المعلمين دفعة واحدة') }}">
                <i class="fas fa-trash-can"></i>{{ __('حذف جميع المعلمين') }}</button>
            <a href="{{ route('admin.teachers.export') }}" class="btn-action" style="background: #059669; color: white; padding: 9px 16px; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);" title="تنزيل جدول المعلمين كاملاً إلى ملف Excel/CSV">
                <i class="fas fa-file-excel"></i> تصدير إكسل (CSV)
            </a>
            <a href="{{ route('admin.teachers.create') }}" class="btn-add-new">
                <i class="fas fa-plus-circle me-1"></i>{{ __('إضافة معلم جديد') }}</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-check-circle text-success"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif


    {{-- شريط التحكم الجماعي بالمعلمين المحددين --}}
    <div id="teacherBulkBar" style="display: none; background: #1e1b4b; color: white; border-radius: 12px; padding: 12px 20px; margin-bottom: 20px; align-items: center; justify-content: space-between; gap: 15px; box-shadow: 0 4px 14px rgba(30, 27, 75, 0.25);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="background: #4f46e5; width: 32px; height: 32px; border-radius: 8px; display: grid; place-items: center; font-weight: 900; font-size: 0.9rem;" id="selectedTeachersCount">0</span>
            <span style="font-weight: 700; font-size: 0.95rem;">{{ __('معلم تم تحديدهم') }}</span>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <button type="button" onclick="deselectAllTeachers()" style="background: rgba(255,255,255,0.15); color: white; border: none; padding: 7px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">{{ __('إلغاء التحديد') }}</button>
            <button type="button" onclick="deleteSelectedTeachers()" style="background: #dc2626; color: white; border: none; padding: 7px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-trash-can"></i>
                <span>{{ __('حذف المعلمين المحددين') }}</span>
            </button>
        </div>
    </div>

    <!-- جدول البيانات -->
    <div class="content-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">
                            <input type="checkbox" id="selectAllTeachersCheckbox" onchange="toggleSelectAllTeachers(this)" title="تحديد / إلغاء تحديد الكل" style="width: 17px; height: 17px; cursor: pointer; accent-color: #4f46e5;">
                        </th>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('المعلم') }}</th>
                        <th>{{ __('البريد وكلمة المرور') }}</th>
                        <th>{{ __('التخصص') }}</th>
                        <th>{{ __('المادة المسندة') }}</th>
                        <th>{{ __('تاريخ الانضمام') }}</th>
                        <th style="text-align: center;">{{ __('التحكم') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $index => $teacher)
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="teacher-row-checkbox" value="{{ $teacher->id }}" onchange="updateTeacherBulkBar()" style="width: 17px; height: 17px; cursor: pointer; accent-color: #4f46e5;">
                        </td>
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
                                    @if($teacher->phone)
                                        <small style="color: #64748b; font-size: 11px;">{{ $teacher->phone }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div dir="ltr" style="color: #1e293b; font-size: 13px; font-weight: 600;">{{ $teacher->email }}</div>
                            <div style="margin-top: 5px; display: flex; align-items: center; gap: 6px;">
                                @if(!empty($teacher->plain_password))
                                    <span class="password-badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 6px; padding: 2px 7px; font-size: 11px; font-weight: 700; font-family: monospace;" dir="ltr">
                                        <i class="fas fa-key" style="color: #d97706; font-size: 10px;"></i>
                                        <span>{{ $teacher->plain_password }}</span>
                                    </span>
                                    <button type="button" onclick="copyTeacherTablePass('{{ $teacher->plain_password }}')" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 5px; padding: 2px 6px; font-size: 10px; cursor: pointer; color: #475569;" title="{{ __('نسخ كلمة المرور') }}">
                                        <i class="far fa-copy"></i>
                                    </button>
                                @else
                                    <span style="background: #f1f5f9; color: #64748b; border: 1px dashed #cbd5e1; border-radius: 5px; padding: 2px 6px; font-size: 11px; font-weight: 700;" title="{{ __('مشفرة بأمان في النظام') }}">
                                        <i class="fa-solid fa-shield-halved" style="font-size: 10px;"></i> {{ __('مشفرة') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge-major">{{ $teacher->major ?? 'غير محدد' }}</span>
                        </td>
                        <td>
                            @php
                                $sub = \App\Models\Subject::find($teacher->subject_id);
                            @endphp
                            @if($sub)
                                <span style="background: #f0fdf4; color: #166534; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                                    {{ $sub->name_ar }}
                                </span>
                            @else
                                <span style="color: #94a3b8; font-size: 12px;">{{ __('غير مسند') }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 500;">{{ $teacher->created_at ? $teacher->created_at->format('Y-m-d') : '' }}</div>
                            <small class="text-muted" style="font-size: 11px;">{{ $teacher->created_at ? $teacher->created_at->diffForHumans() : '' }}</small>
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="action-btn btn-view" title="{{ __('عرض') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="action-btn btn-edit" title="{{ __('تعديل') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟');" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete" title="{{ __('حذف') }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 50px; color: #94a3b8;">
                            <i class="fas fa-users-slash fa-3x mb-3"></i>
                            <p>{{ __('لا يوجد معلمين مسجلين في النظام حتى الآن.') }}</p>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function revealTeacherPassword(name, pwd) {
        if (!pwd) {
            Swal.fire({
                icon: 'info',
                title: `كلمة مرور المعلم (${name})`,
                text: 'كلمة المرور مشفرة بالنظام لأمان الحساب. يمكنك تغييرها مباشرة بالنقر على تعديل المعلم.'
            });
            return;
        }
        Swal.fire({
            title: `كلمة مرور المعلم (${name}) 🔑`,
            html: `
                <div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 16px; margin-top: 10px;">
                    <span style="font-size: 0.8rem; color: #64748b; display: block; margin-bottom: 6px;">{{ __('كلمة المرور الحالية المعتمدة:') }}</span>
                    <code style="font-size: 1.4rem; font-weight: 900; color: #1e1b4b; letter-spacing: 2px; font-family: monospace;">${pwd}</code>
                </div>
            `,
            confirmButtonText: 'تمت المشاهدة'
        });
    }

    // إدارة التحديد الجماعي وحذف المعلمين
    function toggleSelectAllTeachers(master) {
        const checkboxes = document.querySelectorAll('.teacher-row-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateTeacherBulkBar();
    }

    function deselectAllTeachers() {
        const master = document.getElementById('selectAllTeachersCheckbox');
        if (master) master.checked = false;
        document.querySelectorAll('.teacher-row-checkbox').forEach(cb => cb.checked = false);
        updateTeacherBulkBar();
    }

    function updateTeacherBulkBar() {
        const checked = document.querySelectorAll('.teacher-row-checkbox:checked');
        const bar = document.getElementById('teacherBulkBar');
        const countSpan = document.getElementById('selectedTeachersCount');
        if (!bar) return;

        if (checked.length > 0) {
            bar.style.display = 'flex';
            if (countSpan) countSpan.innerText = checked.length;
        } else {
            bar.style.display = 'none';
        }
    }

    function deleteSelectedTeachers() {
        const checked = document.querySelectorAll('.teacher-row-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.value);

        if (ids.length === 0) {
            Swal.fire('تنبيه', 'لم يتم تحديد أي معلم للحذف', 'warning');
            return;
        }

        Swal.fire({
            title: `حذف (${ids.length}) معلم محدد؟ ⚠️`,
            text: 'سيتم حذف حسابات هؤلاء المعلمين وإخلاء إسناد المواد التابعة لهم نهائياً!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `نعم، احذف (${ids.length}) معلم`,
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#dc2626'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'جاري الحذف...',
                    text: 'يرجى الانتظار لحين معالجة البيانات',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                axios.post("{{ route('admin.teachers.bulkDelete') }}", { ids: ids })
                .then(res => {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحذف بنجاح ✅',
                        text: res.data.message
                    }).then(() => location.reload());
                })
                .catch(err => {
                    Swal.fire('خطأ', err.response?.data?.message || 'حدث خطأ أثناء الحذف', 'error');
                });
            }
        });
    }

    function confirmPurgeAllTeachers() {
        Swal.fire({
            title: 'حذف جميع المعلمين دفعة واحدة ⚠️',
            html: `
                <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 14px; text-align: right; margin-bottom: 12px; font-size: 0.88rem; color: #991b1b; line-height: 1.6;">
                    <strong>{{ __('تحذير أمني:') }}</strong><br>{{ __('أنت على وشك حذف') }}<strong>{{ __('كافة المعلمين المسجلين في المنصة دفعة واحدة') }}</strong>{{ __('وإخلاء إسناد المواد الدراسية.') }}<br>{{ __('حسابات الإدارة لن تتأثر، ولكن لا يمكن التراجع عن هذه الخطوة.') }}</div>
                <p style="font-size: 0.85rem; color: #475569; margin-bottom: 8px;">{{ __('للتأكيد، يرجى كتابة العبارة الآتية بدقة:') }}<br><strong style="color: #dc2626; font-size: 1rem;">{{ __('تأكيد الحذف') }}</strong></p>
            `,
            input: 'text',
            inputPlaceholder: 'اكتب هنا: تأكيد الحذف',
            showCancelButton: true,
            confirmButtonText: 'تأكيد وحذف جميع المعلمين الآن 🗑️',
            cancelButtonText: 'تراجع وإلغاء',
            confirmButtonColor: '#dc2626',
            preConfirm: (inputVal) => {
                if (inputVal !== 'تأكيد الحذف' && inputVal !== 'DELETE') {
                    Swal.showValidationMessage('العبارة غير متطابقة! يرجى كتابة (تأكيد الحذف)');
                    return false;
                }
                return inputVal;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'جاري تصفير وحذف جميع المعلمين...',
                    text: 'يرجى الانتظار بضع ثوانٍ',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                axios.post("{{ route('admin.teachers.purgeAll') }}", { confirm_text: result.value })
                .then(res => {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم حذف جميع المعلمين بنجاح 🎉',
                        text: res.data.message
                    }).then(() => location.reload());
                })
                .catch(err => {
                    Swal.fire('خطأ', err.response?.data?.message || 'حدث خطأ أثناء حذف المعلمين', 'error');
                });
            }
        });
    }

    function copyTeacherTablePass(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            textArea.remove();
        }
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ __("تم نسخ كلمة المرور") }}',
                showConfirmButton: false,
                timer: 1500
            });
        } else {
            alert('{{ __("تم نسخ كلمة المرور بنجاح") }}');
        }
    }
</script>
@endsection
