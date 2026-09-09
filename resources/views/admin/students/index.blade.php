@extends('layouts.app')

@section('title', 'إدارة الطلاب | منصة جسر')

@section('content')
<!-- استدعاء خط Cairo من جوجل -->
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

<div class="students-dashboard">
    {{-- الجزء العلوي: الإحصائيات والبحث --}}
    <div class="dashboard-header">
        <div class="header-main">
            <div class="title-section">
                <div class="icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div>
                    <h1 class="page-title">سجل الطلاب <span class="count-pill">{{ count($students) }}</span></h1>
                    <p class="page-subtitle">إدارة وتفعيل حسابات طلاب منصة جسر والمراجعة الأكاديمية</p>
                </div>
            </div>
            <a href="{{ route('admin.students.create') }}" class="btn-primary-gradient">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                إضافة طالب جديد
            </a>
        </div>
    </div>

    {{-- بطاقة الجدول --}}
    <div class="main-card">
        <div class="table-wrapper">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>المعلومات الشخصية</th>
                        <th>المرحلة الدراسية</th>
                        <th>الهوية الوطنية</th>
                        <th>حالة الحساب</th>
                        <th class="text-center">التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr id="row_{{ $student->id }}">
                        <td>
                            <div class="student-profile">
                                <div class="avatar-container">
                                    <img src="{{ asset('storage/'.$student->photo) }}"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($student->name_ar) }}&background=6366f1&color=fff&bold=true'"
                                         alt="">
                                    <div class="status-dot {{ $student->status == 'active' ? 'online' : 'offline' }}"></div>
                                </div>
                                <div class="student-details">
                                    <span class="name">{{ $student->name_ar }}</span>
                                    <span class="email">{{ $student->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-grade">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                                {{ $student->stage->label_ar ?? 'غير محدد' }}
                            </span>
                        </td>
                        <td>
                            <code class="nid-badge">{{ $student->nid }}</code>
                        </td>
                        <td>
                            <div class="status-pill {{ $student->status == 'active' ? 'active' : 'pending' }}">
                                <span class="pulse"></span>
                                {{ $student->status == 'active' ? 'حساب مفعّل ومعتمد' : 'بانتظار موافقة المدير ⏳' }}
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                @if($student->status !== 'active')
                                    <button onclick="approveStudentDirect({{ $student->id }}, '{{ addslashes($student->name_ar) }}')"
                                            class="btn-approve-direct"
                                            title="الموافقة الفورية على تسجيل الدخول وتفعيل الاشتراك">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        <span>موافقة وتفعيل</span>
                                    </button>
                                @endif

                                <button onclick="performToggle({{ $student->id }})"
                                        class="btn-icon {{ $student->status == 'active' ? 'btn-active' : 'btn-inactive' }}"
                                        title="{{ $student->status == 'active' ? 'تجميد الحساب وإعادته لقيد الانتظار' : 'تفعيل الحساب' }}">
                                    @if($student->status == 'active')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    @else
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    @endif
                                </button>

                                <a href="{{ route((auth()->check() && auth()->user()->role === 'admin' ? 'admin' : 'teacher') . '.students.edit', $student->id) }}"
                                   class="btn-icon btn-edit" title="تعديل">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>

                                <button onclick="deleteStudent({{ $student->id }})" class="btn-icon btn-delete" title="حذف">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --secondary: #64748b;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --bg-body: #f8fafc;
        --card-bg: #ffffff;
        --text-dark: #1e293b;
        --text-light: #64748b;
        --radius-xl: 16px;
        --font-family: 'Cairo', sans-serif;
    }

    .students-dashboard {
        font-family: var(--font-family);
        color: var(--text-dark);
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
        animation: fadeIn 0.6s ease-out;
    }

    /* Header Styling */
    .dashboard-header {
        margin-bottom: 30px;
    }

    .header-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .title-section {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .icon-box {
        background: white;
        padding: 12px;
        border-radius: 14px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        color: var(--primary);
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .count-pill {
        background: var(--primary);
        color: white;
        font-size: 0.9rem;
        padding: 2px 12px;
        border-radius: 20px;
        font-weight: 600;
    }

    .page-subtitle {
        color: var(--text-light);
        margin: 5px 0 0 0;
        font-size: 0.95rem;
    }

    /* Buttons */
    .btn-primary-gradient {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
    }

    .btn-primary-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 20px -3px rgba(99, 102, 241, 0.4);
        color: white;
    }

    /* Main Card & Table */
    .main-card {
        background: var(--card-bg);
        border-radius: var(--radius-xl);
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: 1px solid rgba(226, 232, 240, 0.8);
        overflow: hidden;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }

    .modern-table th {
        background: #fcfdfe;
        padding: 20px 24px;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--secondary);
        text-transform: uppercase;
        border-bottom: 2px solid #f1f5f9;
    }

    .modern-table td {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .modern-table tr:hover {
        background-color: #f8faff;
    }

    /* Student Profile Cell */
    .student-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar-container {
        position: relative;
        width: 48px;
        height: 48px;
    }

    .avatar-container img {
        width: 100%;
        height: 100%;
        border-radius: 12px;
        object-fit: cover;
    }

    .status-dot {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
    }

    .status-dot.online { background: var(--success); }
    .status-dot.offline { background: #cbd5e1; }

    .student-details {
        display: flex;
        flex-direction: column;
    }

    .student-details .name {
        font-weight: 700;
        color: var(--text-dark);
        font-size: 0.95rem;
    }

    .student-details .email {
        font-size: 0.8rem;
        color: var(--text-light);
    }

    /* Badges */
    .badge-grade {
        background: #eff6ff;
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .nid-badge {
        font-family: 'Courier New', monospace;
        background: #f1f5f9;
        color: #475569;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
    }

    /* Status Pill */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .status-pill.active {
        background: #dcfce7;
        color: #15803d;
    }

    .status-pill.pending {
        background: #fef3c7;
        color: #b45309;
    }

    .pulse {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
        animation: pulse-animation 2s infinite;
    }

    /* Actions */
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .btn-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-active { background: #ecfdf5; color: var(--success); }
    .btn-inactive { background: #f1f5f9; color: var(--secondary); }
    .btn-edit { background: #eef2ff; color: var(--primary); }
    .btn-delete { background: #fef2f2; color: var(--danger); }

    .btn-icon:hover {
        transform: scale(1.1);
        filter: brightness(0.95);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse-animation {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }

    .btn-approve-direct {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        padding: 7px 14px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        transition: all 0.2s;
        white-space: nowrap;
    }
    .btn-approve-direct:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .modern-table thead { display: none; }
        .modern-table td {
            display: block;
            text-align: left;
            padding: 10px 24px;
            border: none;
        }
        .modern-table td::before {
            content: attr(data-label);
            float: right;
            font-weight: bold;
        }
        .modern-table tr {
            display: block;
            border-bottom: 5px solid #f1f5f9;
            padding: 10px 0;
        }
        .action-buttons { justify-content: flex-start; }
    }
</style>

{{-- نفس الـ Scripts التي كانت لديك مع تحسين خفيف --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function approveStudentDirect(id, name) {
        Swal.fire({
            title: 'اعتماد تسجيل ودخول الطالب؟',
            text: `هل تريد الموافقة على تسجيل دخول واشتراك الطالب (${name}) وفتح صلاحيات المنصة له؟`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، موافقة وتفعيل',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post(`{{ url('admin/students') }}/${id}/approve`)
                .then(res => {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التفعيل والاعتماد بنجاح! 🎉',
                        text: res.data.message || 'تم اعتماد الطالب وتفعيل دخوله واشتراكه.',
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => location.reload());
                })
                .catch(err => Swal.fire('خطأ', 'فشلت عملية الاعتماد', 'error'));
            }
        });
    }

    function performToggle(id) {
        axios.post(`{{ url('admin/students/toggle-status') }}/${id}`)
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: 'تم التحديث',
                text: 'تم تغيير حالة الحساب بنجاح',
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        })
        .catch(err => Swal.fire('خطأ', 'فشل تغيير الحالة', 'error'));
    }

    function deleteStudent(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا الإجراء!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`{{ url('admin/students') }}/${id}`)
                .then(res => {
                    document.getElementById(`row_${id}`).style.opacity = '0';
                    setTimeout(() => {
                        document.getElementById(`row_${id}`).remove();
                    }, 500);
                    Swal.fire('تم الحذف!', 'تمت إزالة الطالب بنجاح', 'success');
                });
            }
        });
    }
</script>
@endsection
