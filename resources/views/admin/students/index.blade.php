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
                        <th>الخصم والمنحة 🏷️</th>
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
                                    <a href="{{ route('admin.students.show', $student->id) }}" style="text-decoration: none; color: inherit;" title="عرض الملف وتخصيص المواد">
                                        <span class="name" style="transition: color 0.2s; cursor: pointer;">{{ $student->name_ar }}</span>
                                    </a>
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
                            <div id="discount_badge_{{ $student->id }}" 
                                 onclick="openDiscountModal({{ $student->id }}, '{{ addslashes($student->name_ar) }}', {{ (float)($student->custom_discount_percent ?? 0) }}, {{ (float)($student->custom_discount_fixed ?? 0) }}, '{{ addslashes($student->discount_notes ?? '') }}')" 
                                 style="cursor: pointer; display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; transition: transform 0.2s; {{ $student->hasDiscount() ? ((float)($student->custom_discount_percent ?? 0) >= 100 ? 'background: #ecfdf5; color: #059669; border: 1.5px solid #a7f3d0;' : 'background: #f5f3ff; color: #7c3aed; border: 1.5px solid #ddd6fe;') : 'background: #f8fafc; color: #94a3b8; border: 1px dashed #cbd5e1;' }}"
                                 onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"
                                 title="انقر لتعديل الخصم أو المنحة للطالب">
                                <span>{{ $student->hasDiscount() ? '🏷️' : '➕' }}</span>
                                <span id="badge_text_{{ $student->id }}">{{ $student->discount_label }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" onclick="openDiscountModal({{ $student->id }}, '{{ addslashes($student->name_ar) }}', {{ (float)($student->custom_discount_percent ?? 0) }}, {{ (float)($student->custom_discount_fixed ?? 0) }}, '{{ addslashes($student->discount_notes ?? '') }}')"
                                        class="btn-icon" style="color: #7c3aed; background: #f5f3ff;" title="تحديد / تعديل خصم الطالب 🏷️">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                </button>

                                <a href="{{ route('admin.students.show', $student->id) }}"
                                   class="btn-icon" style="color: #4f46e5; background: #eef2ff;" title="عرض ملف ومواد الطالب 📚">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                                </a>

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

    // --- وظائف إدارة وتحديد خصومات الطلاب المخصصة ---
    let currentDiscountStudentId = null;

    function openDiscountModal(id, name, percent, fixed, notes) {
        currentDiscountStudentId = id;
        document.getElementById('discountStudentId').value = id;
        document.getElementById('discountStudentName').innerText = name;
        document.getElementById('discountNotes').value = notes || '';

        if (fixed > 0) {
            setDiscountType('fixed');
            document.getElementById('discountValue').value = fixed;
        } else {
            setDiscountType('percent');
            document.getElementById('discountValue').value = percent > 0 ? percent : '';
        }

        const overlay = document.getElementById('discountModalOverlay');
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeDiscountModal() {
        const overlay = document.getElementById('discountModalOverlay');
        overlay.style.display = 'none';
        document.body.style.overflow = '';
        currentDiscountStudentId = null;
    }

    function setDiscountType(type) {
        document.getElementById('discountType').value = type;
        const btnPercent = document.getElementById('typeBtnPercent');
        const btnFixed = document.getElementById('typeBtnFixed');
        const unitLabel = document.getElementById('discountUnitLabel');

        if (type === 'percent') {
            btnPercent.style.background = '#7c3aed';
            btnPercent.style.color = '#ffffff';
            btnPercent.style.borderColor = '#7c3aed';
            btnFixed.style.background = '#ffffff';
            btnFixed.style.color = '#64748b';
            btnFixed.style.borderColor = '#cbd5e1';
            unitLabel.innerText = '% (نسبة مئوية)';
            document.getElementById('discountValue').placeholder = 'مثال: 25 أو 50 أو 100';
            document.getElementById('discountValue').max = '100';
        } else {
            btnFixed.style.background = '#7c3aed';
            btnFixed.style.color = '#ffffff';
            btnFixed.style.borderColor = '#7c3aed';
            btnPercent.style.background = '#ffffff';
            btnPercent.style.color = '#64748b';
            btnPercent.style.borderColor = '#cbd5e1';
            unitLabel.innerText = '₪ (شيكل فلسطيني)';
            document.getElementById('discountValue').placeholder = 'مثال: 50 أو 100';
            document.getElementById('discountValue').removeAttribute('max');
        }
    }

    function setQuickDiscount(percent, notes) {
        setDiscountType('percent');
        document.getElementById('discountValue').value = percent;
        if (notes) {
            document.getElementById('discountNotes').value = notes;
        } else if (percent === 0) {
            document.getElementById('discountNotes').value = '';
        }
    }

    function handleDiscountSubmit(event) {
        event.preventDefault();
        if (!currentDiscountStudentId) return;

        const type = document.getElementById('discountType').value;
        const val = parseFloat(document.getElementById('discountValue').value) || 0;
        const notes = document.getElementById('discountNotes').value.trim();

        const btn = document.getElementById('btnSaveDiscount');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...';

        axios.post(`{{ url('admin/students') }}/${currentDiscountStudentId}/discount`, {
            discount_type: val > 0 ? type : 'none',
            discount_value: val,
            discount_notes: notes
        })
        .then(res => {
            const data = res.data;
            Swal.fire({
                icon: data.icon || 'success',
                title: data.title || 'تم تحديث الخصم',
                text: data.message || '',
                timer: 1800,
                showConfirmButton: false
            });

            // تحديث الشارة في الجدول فوراً
            const badge = document.getElementById(`discount_badge_${currentDiscountStudentId}`);
            const badgeText = document.getElementById(`badge_text_${currentDiscountStudentId}`);
            if (badge && badgeText) {
                badgeText.innerText = data.discount_label;
                if (data.has_discount) {
                    if (data.percent >= 100) {
                        badge.style.background = '#ecfdf5';
                        badge.style.color = '#059669';
                        badge.style.borderColor = '#a7f3d0';
                        badge.querySelector('span:first-child').innerText = '✨';
                    } else {
                        badge.style.background = '#f5f3ff';
                        badge.style.color = '#7c3aed';
                        badge.style.borderColor = '#ddd6fe';
                        badge.querySelector('span:first-child').innerText = '🏷️';
                    }
                } else {
                    badge.style.background = '#f8fafc';
                    badge.style.color = '#94a3b8';
                    badge.style.borderColor = '#cbd5e1';
                    badge.querySelector('span:first-child').innerText = '➕';
                }

                // تحديث الـ onclick parameters
                badge.setAttribute('onclick', `openDiscountModal(${currentDiscountStudentId}, '${document.getElementById('discountStudentName').innerText}', ${data.percent || 0}, ${data.fixed || 0}, '${(data.notes || '').replace(/'/g, "\\'")}')`);
            }

            closeDiscountModal();
        })
        .catch(err => {
            const msg = (err.response && err.response.data && err.response.data.title) ? err.response.data.title : 'فشل حفظ الخصم، يرجى المحاولة ثانية';
            Swal.fire('خطأ', msg, 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
</script>

<!-- النافذة المنبثقة لتخصيص خصم الطالب (Modal) -->
<div id="discountModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;" dir="rtl">
    <div style="background: #ffffff; width: 100%; max-width: 520px; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: modalIn 0.25s ease-out;">
        
        <!-- هيدر المودال -->
        <div style="padding: 20px 24px; background: linear-gradient(135deg, #4c1d95 0%, #6d28d9 100%); color: white; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(255,255,255,0.18); display: grid; place-items: center; font-size: 1.3rem;">
                    🏷️
                </div>
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0;">تحديد خصم أو منحة للطالب</h3>
                    <span id="discountStudentName" style="font-size: 0.85rem; color: #e9d5ff; font-weight: 700;">اسم الطالب</span>
                </div>
            </div>
            <button type="button" onclick="closeDiscountModal()" style="background: rgba(255,255,255,0.15); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 1rem; display: grid; place-items: center;">✕</button>
        </div>

        <form id="discountForm" onsubmit="handleDiscountSubmit(event)" style="padding: 24px;">
            <input type="hidden" id="discountStudentId" value="">
            <input type="hidden" id="discountType" value="percent">

            <!-- نوع الخصم -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 8px;">نوع الخصم المعتمد:</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <button type="button" id="typeBtnPercent" onclick="setDiscountType('percent')" style="padding: 10px; border-radius: 12px; border: 2px solid #7c3aed; background: #7c3aed; color: white; font-weight: 800; font-size: 0.85rem; cursor: pointer; transition: 0.2s;">
                        نسبة مئوية (%)
                    </button>
                    <button type="button" id="typeBtnFixed" onclick="setDiscountType('fixed')" style="padding: 10px; border-radius: 12px; border: 2px solid #cbd5e1; background: white; color: #64748b; font-weight: 800; font-size: 0.85rem; cursor: pointer; transition: 0.2s;">
                        مبلغ نقدي ثابت (₪)
                    </button>
                </div>
            </div>

            <!-- خيارات سريعة بنقرة واحدة -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #64748b; margin-bottom: 6px;">خيارات سريعة ومقترحة:</label>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="button" onclick="setQuickDiscount(15, 'خصم تشجيعي')" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">15% تشجيعي</button>
                    <button type="button" onclick="setQuickDiscount(25, 'منحة تفوق دراسي')" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">25% تفوق</button>
                    <button type="button" onclick="setQuickDiscount(50, 'نصف منحة دراسية')" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">50% نصف منحة</button>
                    <button type="button" onclick="setQuickDiscount(100, 'إعفاء كامل - منحة شاملة 100%')" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">✨ إعفاء كامل 100%</button>
                    <button type="button" onclick="setQuickDiscount(0, '')" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">❌ إلغاء الخصم</button>
                </div>
            </div>

            <!-- قيمة الخصم -->
            <div style="margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155;">قيمة الخصم:</label>
                    <span id="discountUnitLabel" style="font-size: 0.75rem; color: #7c3aed; font-weight: 700;">% (نسبة مئوية)</span>
                </div>
                <input type="number" id="discountValue" min="0" max="100" step="any" placeholder="مثال: 25 أو 50" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1.5px solid #cbd5e1; font-size: 1.05rem; font-weight: 800; font-family: monospace; outline: none; transition: 0.2s; box-sizing: border-box;" onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#cbd5e1'">
            </div>

            <!-- سبب الخصم / ملاحظات المنحة -->
            <div style="margin-bottom: 22px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">سبب الخصم أو ملاحظات المنحة (اختياري):</label>
                <input type="text" id="discountNotes" placeholder="مثال: منحة تفوق توجيهي / إعفاء خاص" style="width: 100%; padding: 10px 14px; border-radius: 12px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; outline: none; transition: 0.2s; box-sizing: border-box;" onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#cbd5e1'">
                <small style="color: #64748b; font-size: 0.73rem; display: block; margin-top: 4px;">سيظهر هذا السبب للطالب في إشعاراته وسلة اشتراكه.</small>
            </div>

            <!-- أزرار الحفظ والإلغاء -->
            <div style="display: flex; gap: 10px;">
                <button type="submit" id="btnSaveDiscount" style="flex: 1; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: white; border: none; padding: 13px; border-radius: 12px; font-weight: 800; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);">
                    <span>اعتماد وتطبيق الخصم</span>
                    <i class="fa-solid fa-check"></i>
                </button>
                <button type="button" onclick="closeDiscountModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 13px 20px; border-radius: 12px; font-weight: 700; font-size: 0.9rem; cursor: pointer;">
                    إلغاء
                </button>
            </div>
        </form>

    </div>
</div>

@endsection
