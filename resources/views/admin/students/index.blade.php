@extends('layouts.app')

@section('title', 'سجل وإدارة الطلاب | ' . \App\Models\Setting::get('site_name', 'منارة التوجيهي'))

@section('content')
<div class="students-dashboard-container">

    {{-- 1. رأس الصفحة: العنوان والإحصائيات وزر الإضافة --}}
    <div class="dashboard-header-block">
        <div class="header-titles">
            <div class="title-emblem">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div>
                <h1 class="main-page-title">
                    سجل طلبة الثانوية العامة
                    <span class="count-badge" id="visibleStudentsCount">{{ count($students) }}</span>
                </h1>
                <p class="main-page-subtitle">
                    إدارة وتفعيل حسابات طلاب {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }} لدورة 2026، تخصيص المنح، وتعيين المواد الدراسية.
                </p>
            </div>
        </div>

        <div class="header-actions">
            <a href="{{ route('admin.students.create') }}" class="btn-add-student">
                <i class="fa-solid fa-user-plus"></i>
                <span>إضافة طالب جديد</span>
            </a>
        </div>
    </div>

    {{-- 2. بطاقات المؤشرات السريعة (Quick Stats KPI) --}}
    <div class="kpi-grid">
        <div class="kpi-card" onclick="setFilterTab('all')">
            <div class="kpi-icon kpi-blue"><i class="fa-solid fa-users"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">إجمالي الطلبة المسجلين</span>
                <strong class="kpi-num">{{ count($students) }}</strong>
            </div>
        </div>

        <div class="kpi-card" onclick="setFilterTab('pending')">
            <div class="kpi-icon kpi-amber"><i class="fa-solid fa-clock"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">بانتظار التفعيل والموافقة</span>
                <strong class="kpi-num" style="color: #d97706;">{{ $students->where('status', '!=', 'active')->count() }}</strong>
            </div>
        </div>

        <div class="kpi-card" onclick="setFilterTab('active')">
            <div class="kpi-icon kpi-emerald"><i class="fa-solid fa-circle-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">حسابات نشطة ومعتمدة</span>
                <strong class="kpi-num" style="color: #059669;">{{ $students->where('status', 'active')->count() }}</strong>
            </div>
        </div>

        <div class="kpi-card" onclick="setFilterTab('all')">
            <div class="kpi-icon kpi-purple"><i class="fa-solid fa-tags"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">منح وخصومات مخصصة</span>
                <strong class="kpi-num" style="color: #7c3aed;">{{ $students->filter(fn($s) => $s->hasDiscount())->count() }}</strong>
            </div>
        </div>
    </div>

    {{-- 3. شريط البحث التفاعلي والفلاتر السريعة --}}
    <div class="filter-search-card">
        <div class="search-input-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="studentSearchInput" placeholder="بحث فوري باسم الطالب، رقم الهوية الوطنية، أو البريد الإلكتروني..." oninput="filterStudents()">
            <button type="button" id="clearSearchBtn" onclick="clearSearch()" style="display: none;"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="filter-tabs-row">
            <button type="button" class="filter-tab-btn active" data-filter="all" onclick="setFilterTab('all')">الكل ({{ count($students) }})</button>
            <button type="button" class="filter-tab-btn" data-filter="pending" onclick="setFilterTab('pending')">
                <span class="dot-amber"></span> بانتظار الموافقة ({{ $students->where('status', '!=', 'active')->count() }})
            </button>
            <button type="button" class="filter-tab-btn" data-filter="active" onclick="setFilterTab('active')">
                <span class="dot-green"></span> المعتمدون ({{ $students->where('status', 'active')->count() }})
            </button>
            <button type="button" class="filter-tab-btn" data-filter="sci" onclick="setFilterTab('sci')">الفرع العلمي ⚛️</button>
            <button type="button" class="filter-tab-btn" data-filter="lit" onclick="setFilterTab('lit')">الفرع الأدبي 📜</button>
            <button type="button" class="filter-tab-btn" data-filter="bus" onclick="setFilterTab('bus')">ريادة وأعمال 💼</button>
        </div>
    </div>

    {{-- 4. بطاقة الجدول المتطورة مع التمرير الأفقي المحمي --}}
    <div class="table-outer-card">
        <div class="responsive-table-wrapper">
            <table class="students-data-table">
                <thead>
                    <tr>
                        <th style="min-width: 230px;">المعلومات الشخصية</th>
                        <th style="min-width: 170px;">الفرع والمرحلة</th>
                        <th style="min-width: 130px;">الهوية الوطنية</th>
                        <th style="min-width: 140px;">حالة الحساب</th>
                        <th style="min-width: 140px;">الخصم والمنحة 🏷️</th>
                        <th style="min-width: 240px; text-align: center;">إجراءات التحكم</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    @forelse($students as $student)
                    @php
                        $stageLabel = $student->stage->label_ar ?? 'توجيهي';
                        $branchTag = 'other';
                        $branchPill = 'توجيهي 2026';
                        $branchClass = 'badge-stage-blue';

                        if (str_contains($stageLabel, 'علمي')) {
                            $branchTag = 'sci';
                            $branchPill = 'الفرع العلمي ⚛️';
                            $branchClass = 'badge-stage-sci';
                        } elseif (str_contains($stageLabel, 'أدبي')) {
                            $branchTag = 'lit';
                            $branchPill = 'الفرع الأدبي 📜';
                            $branchClass = 'badge-stage-lit';
                        } elseif (str_contains($stageLabel, 'ريادة') || str_contains($stageLabel, 'أعمال') || str_contains($stageLabel, 'تجاري')) {
                            $branchTag = 'bus';
                            $branchPill = 'ريادة وأعمال 💼';
                            $branchClass = 'badge-stage-bus';
                        } elseif (str_contains($stageLabel, 'صناعي')) {
                            $branchTag = 'ind';
                            $branchPill = 'الفرع الصناعي ⚙️';
                            $branchClass = 'badge-stage-ind';
                        }
                    @endphp
                    <tr id="row_{{ $student->id }}" 
                        class="student-row"
                        data-name="{{ mb_strtolower($student->name_ar) }}"
                        data-email="{{ strtolower($student->email) }}"
                        data-nid="{{ $student->nid }}"
                        data-status="{{ $student->status }}"
                        data-branch="{{ $stageLabel }}">
                        
                        {{-- 1. المعلومات الشخصية --}}
                        <td>
                            <div class="student-profile-cell">
                                <div class="student-avatar-wrap">
                                    <img src="{{ $student->photo ? asset('storage/'.$student->photo) : 'https://ui-avatars.com/api/?name='.urlencode($student->name_ar).'&background=3b82f6&color=fff&bold=true&rounded=true' }}"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($student->name_ar) }}&background=3b82f6&color=fff&bold=true&rounded=true'"
                                         alt="{{ $student->name_ar }}">
                                    <span class="avatar-dot {{ $student->status == 'active' ? 'online' : 'offline' }}"></span>
                                </div>
                                <div class="student-meta-wrap">
                                    <a href="{{ route('admin.students.show', $student->id) }}" class="student-name-link" title="فتح الملف الشخصي والمواد الدراسية">
                                        {{ $student->name_ar }}
                                    </a>
                                    <span class="student-email-sub" dir="ltr">{{ $student->email }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- 2. الفرع والمرحلة --}}
                        <td>
                            <div class="stage-cell-wrap">
                                <span class="stage-badge-clean {{ $branchClass }}" title="{{ $stageLabel }}">
                                    {{ $branchPill }}
                                </span>
                            </div>
                        </td>

                        {{-- 3. الهوية الوطنية --}}
                        <td>
                            <span class="nid-clean-pill">
                                <i class="fa-regular fa-id-card"></i>
                                <code>{{ $student->nid }}</code>
                            </span>
                        </td>

                        {{-- 4. حالة الحساب --}}
                        <td>
                            @if($student->status === 'active')
                                <span class="status-clean-badge badge-active">
                                    <span class="live-dot-green"></span>
                                    <span>مفعّل ومعتمد</span>
                                </span>
                            @else
                                <span class="status-clean-badge badge-pending">
                                    <span class="live-dot-amber"></span>
                                    <span>بانتظار الموافقة ⏳</span>
                                </span>
                            @endif
                        </td>

                        {{-- 5. الخصم والمنحة --}}
                        <td>
                            <div id="discount_badge_{{ $student->id }}" 
                                 onclick="openDiscountModal({{ $student->id }}, '{{ addslashes($student->name_ar) }}', {{ (float)($student->custom_discount_percent ?? 0) }}, {{ (float)($student->custom_discount_fixed ?? 0) }}, '{{ addslashes($student->discount_notes ?? '') }}')" 
                                 class="discount-trigger-pill {{ $student->hasDiscount() ? ((float)($student->custom_discount_percent ?? 0) >= 100 ? 'discount-full' : 'discount-custom') : 'discount-none' }}"
                                 title="انقر لتعديل الخصم أو المنحة للطالب">
                                <span>{{ $student->hasDiscount() ? '🏷️' : '➕' }}</span>
                                <span id="badge_text_{{ $student->id }}">{{ $student->discount_label }}</span>
                            </div>
                        </td>

                        {{-- 6. أزرار التحكم --}}
                        <td>
                            <div class="actions-container-row">
                                @if($student->status !== 'active')
                                    <button type="button" 
                                            onclick="approveStudentDirect({{ $student->id }}, '{{ addslashes($student->name_ar) }}')"
                                            class="btn-action-approve"
                                            title="اعتماد وتفعيل حساب الطالب فورياً">
                                        <i class="fa-solid fa-check"></i>
                                        <span>تفعيل</span>
                                    </button>
                                @endif

                                <button type="button" 
                                        onclick="openDiscountModal({{ $student->id }}, '{{ addslashes($student->name_ar) }}', {{ (float)($student->custom_discount_percent ?? 0) }}, {{ (float)($student->custom_discount_fixed ?? 0) }}, '{{ addslashes($student->discount_notes ?? '') }}')"
                                        class="action-icon-btn btn-action-discount"
                                        title="تحديد خصم أو منحة 🏷️">
                                    <i class="fa-solid fa-tag"></i>
                                </button>

                                <a href="{{ route('admin.students.show', $student->id) }}"
                                   class="action-icon-btn btn-action-view"
                                   title="عرض وتخصيص المواد الدراسية 📚">
                                    <i class="fa-solid fa-book-open"></i>
                                </a>

                                <button type="button" 
                                        onclick="performToggle({{ $student->id }})"
                                        class="action-icon-btn {{ $student->status == 'active' ? 'btn-action-lock' : 'btn-action-unlock' }}"
                                        title="{{ $student->status == 'active' ? 'تجميد الحساب وإيقافه' : 'تفعيل الحساب' }}">
                                    @if($student->status == 'active')
                                        <i class="fa-solid fa-user-slash"></i>
                                    @else
                                        <i class="fa-solid fa-user-check"></i>
                                    @endif
                                </button>

                                <a href="{{ route((auth()->check() && auth()->user()->role === 'admin' ? 'admin' : 'teacher') . '.students.edit', $student->id) }}"
                                   class="action-icon-btn btn-action-edit"
                                   title="تعديل بيانات الطالب ✏️">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <button type="button" 
                                        onclick="deleteStudent({{ $student->id }})" 
                                        class="action-icon-btn btn-action-delete" 
                                        title="حذف الطالب 🗑️">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                            <i class="fa-solid fa-user-xmark" style="font-size: 2.2rem; color: #cbd5e1; display: block; margin-bottom: 12px;"></i>
                            <strong>لا يوجد طلاب مسجلون حالياً في المنظومة.</strong>
                        </td>
                    </tr>
                    @endforelse

                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 2.2rem; color: #cbd5e1; display: block; margin-bottom: 12px;"></i>
                            <strong>لا توجد نتائج مطابقة لبحثك أو الفلتر المحدد.</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* أنماط صفحة إدارة الطلاب الحديثة ومتناسقة مع نظام المنارة */
    .students-dashboard-container {
        max-width: 1440px;
        margin: 0 auto;
        padding: 24px 20px 60px;
    }

    /* 1. Header */
    .dashboard-header-block {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
    }

    .header-titles {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .title-emblem {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
        color: #ffffff;
        display: grid;
        place-items: center;
        font-size: 1.4rem;
        box-shadow: 0 4px 14px rgba(29, 78, 216, 0.25);
    }

    .main-page-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .count-badge {
        font-size: 0.85rem;
        font-weight: 700;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        padding: 2px 10px;
        border-radius: 20px;
    }

    .main-page-subtitle {
        font-size: 0.88rem;
        color: #64748b;
        margin: 4px 0 0;
    }

    .btn-add-student {
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
        color: #ffffff;
        text-decoration: none;
        padding: 11px 22px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.92rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(29, 78, 216, 0.28);
        transition: all 0.2s ease;
    }

    .btn-add-student:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(29, 78, 216, 0.38);
        color: #ffffff;
    }

    /* 2. KPI Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        border-color: #cbd5e1;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
    }

    .kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 1.25rem;
    }

    .kpi-blue { background: #eff6ff; color: #1d4ed8; }
    .kpi-amber { background: #fffbeb; color: #d97706; }
    .kpi-emerald { background: #ecfdf5; color: #059669; }
    .kpi-purple { background: #f5f3ff; color: #7c3aed; }

    .kpi-info {
        display: flex;
        flex-direction: column;
    }

    .kpi-label {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 600;
    }

    .kpi-num {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    /* 3. Search & Filter Bar */
    .filter-search-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .search-input-box {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .search-input-box i {
        position: absolute;
        right: 16px;
        color: #94a3b8;
        font-size: 0.95rem;
    }

    .search-input-box input {
        width: 100%;
        padding: 12px 42px 12px 36px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        font-size: 0.92rem;
        font-family: inherit;
        background: #f8fafc;
        outline: none;
        transition: all 0.2s;
    }

    .search-input-box input:focus {
        background: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
    }

    .search-input-box button {
        position: absolute;
        left: 12px;
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
    }

    .filter-tabs-row {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .filter-tab-btn {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid transparent;
        padding: 7px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .filter-tab-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .filter-tab-btn.active {
        background: #1d4ed8;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(29, 78, 216, 0.25);
    }

    .dot-amber { width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; }
    .dot-green { width: 7px; height: 7px; border-radius: 50%; background: #10b981; }

    /* 4. Table Layout */
    .table-outer-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .responsive-table-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .students-data-table {
        width: 100%;
        min-width: 1080px;
        border-collapse: collapse;
        text-align: right;
    }

    .students-data-table th {
        background: #f8fafc;
        color: #475569;
        padding: 16px 20px;
        font-size: 0.82rem;
        font-weight: 800;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }

    .students-data-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.88rem;
    }

    .students-data-table tr.student-row:hover {
        background: #f8faff;
    }

    /* Student Profile Cell */
    .student-profile-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-avatar-wrap {
        position: relative;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
    }

    .student-avatar-wrap img {
        width: 100%;
        height: 100%;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
    }

    .avatar-dot {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 11px;
        height: 11px;
        border-radius: 50%;
        border: 2px solid #ffffff;
    }

    .avatar-dot.online { background: #10b981; }
    .avatar-dot.offline { background: #94a3b8; }

    .student-meta-wrap {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .student-name-link {
        font-weight: 800;
        color: #0f172a;
        text-decoration: none;
        font-size: 0.94rem;
        transition: color 0.2s;
        white-space: nowrap;
    }

    .student-name-link:hover {
        color: #1d4ed8;
    }

    .student-email-sub {
        font-size: 0.78rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Stage Clean Pill */
    .stage-cell-wrap {
        white-space: nowrap;
    }

    .stage-badge-clean {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-stage-sci { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .badge-stage-lit { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; }
    .badge-stage-bus { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .badge-stage-ind { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .badge-stage-blue { background: #f8fafc; color: #334155; border: 1px solid #e2e8f0; }

    /* NID Badge */
    .nid-clean-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 5px 10px;
        border-radius: 8px;
        color: #475569;
        font-size: 0.82rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .nid-clean-pill code {
        font-family: 'Plus Jakarta Sans', monospace;
        letter-spacing: 0.5px;
    }

    /* Status Badge */
    .status-clean-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .badge-active { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .badge-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }

    .live-dot-green { width: 7px; height: 7px; border-radius: 50%; background: #10b981; }
    .live-dot-amber { width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; }

    /* Discount Trigger Pill */
    .discount-trigger-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .discount-trigger-pill:hover {
        transform: scale(1.04);
    }

    .discount-full { background: #ecfdf5; color: #059669; border: 1.5px solid #a7f3d0; }
    .discount-custom { background: #f5f3ff; color: #7c3aed; border: 1.5px solid #ddd6fe; }
    .discount-none { background: #f8fafc; color: #94a3b8; border: 1px dashed #cbd5e1; }

    /* Actions Row */
    .actions-container-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        white-space: nowrap;
    }

    .btn-action-approve {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        transition: all 0.2s;
    }

    .btn-action-approve:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .action-icon-btn {
        width: 35px;
        height: 35px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
        font-size: 0.88rem;
        transition: all 0.2s ease;
    }

    .action-icon-btn:hover {
        transform: translateY(-2px);
    }

    .btn-action-discount { background: #f5f3ff; color: #7c3aed; border-color: #ede9fe; }
    .btn-action-view { background: #eff6ff; color: #1d4ed8; border-color: #dbeafe; }
    .btn-action-lock { background: #ecfdf5; color: #059669; border-color: #d1fae5; }
    .btn-action-unlock { background: #fffbeb; color: #d97706; border-color: #fde68a; }
    .btn-action-edit { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
    .btn-action-delete { background: #fef2f2; color: #dc2626; border-color: #fee2e2; }
</style>

{{-- سكربتات التفاعل والاعتماد والحذف وإدارة الخصومات --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. فلترة وبحث فوري ذكي
    let currentFilter = 'all';

    function setFilterTab(filterKey) {
        currentFilter = filterKey;
        document.querySelectorAll('.filter-tab-btn').forEach(btn => {
            if (btn.getAttribute('data-filter') === filterKey) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        filterStudents();
    }

    function clearSearch() {
        document.getElementById('studentSearchInput').value = '';
        document.getElementById('clearSearchBtn').style.display = 'none';
        filterStudents();
    }

    function filterStudents() {
        const query = (document.getElementById('studentSearchInput').value || '').toLowerCase().trim();
        const clearBtn = document.getElementById('clearSearchBtn');
        if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';

        const rows = document.querySelectorAll('.student-row');
        let count = 0;

        rows.forEach(row => {
            const name = (row.getAttribute('data-name') || '').toLowerCase();
            const email = (row.getAttribute('data-email') || '').toLowerCase();
            const nid = (row.getAttribute('data-nid') || '').toLowerCase();
            const status = row.getAttribute('data-status') || '';
            const branch = (row.getAttribute('data-branch') || '').toLowerCase();

            const matchQuery = !query || name.includes(query) || email.includes(query) || nid.includes(query);
            let matchFilter = true;

            if (currentFilter === 'pending') {
                matchFilter = (status !== 'active');
            } else if (currentFilter === 'active') {
                matchFilter = (status === 'active');
            } else if (currentFilter === 'sci') {
                matchFilter = branch.includes('علمي');
            } else if (currentFilter === 'lit') {
                matchFilter = branch.includes('أدبي');
            } else if (currentFilter === 'bus') {
                matchFilter = branch.includes('ريادة') || branch.includes('أعمال') || branch.includes('تجاري');
            }

            if (matchQuery && matchFilter) {
                row.style.display = '';
                count++;
            } else {
                row.style.display = 'none';
            }
        });

        const counterEl = document.getElementById('visibleStudentsCount');
        if (counterEl) counterEl.innerText = count;

        const emptyRow = document.getElementById('noResultsRow');
        if (emptyRow) {
            emptyRow.style.display = count === 0 ? '' : 'none';
        }
    }

    // 2. اعتماد وتفعيل الطالب الفوري
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
                        text: res.data.message || 'تم اعتماد الطالب وتفعيل حسابه واشتراكه.',
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => location.reload());
                })
                .catch(err => Swal.fire('خطأ', 'فشلت عملية الاعتماد، يرجى المحاولة ثانية', 'error'));
            }
        });
    }

    // 3. تجميد أو إعادة تفعيل الحساب
    function performToggle(id) {
        axios.post(`{{ url('admin/students/toggle-status') }}/${id}`)
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: 'تم التحديث بنجاح',
                text: 'تم تعديل حالة حساب الطالب بنجاح',
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        })
        .catch(err => Swal.fire('خطأ', 'فشل تعديل حالة الحساب', 'error'));
    }

    // 4. حذف الطالب
    function deleteStudent(id) {
        Swal.fire({
            title: 'هل أنت متأكد من حذف الطالب؟',
            text: "سيتم حذف حساب الطالب وكافة بياناته نهائياً!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، احذف نهائياً',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`{{ url('admin/students') }}/${id}`)
                .then(res => {
                    const row = document.getElementById(`row_${id}`);
                    if (row) {
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 400);
                    }
                    Swal.fire('تم الحذف!', 'تمت إزالة حساب الطالب بنجاح', 'success');
                })
                .catch(err => Swal.fire('خطأ', 'فشلت عملية الحذف', 'error'));
            }
        });
    }

    // 5. إدارة المنح والخصومات (Discount Modal)
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

            // تحديث الشارة بالجدول فورياً
            const badge = document.getElementById(`discount_badge_${currentDiscountStudentId}`);
            const badgeText = document.getElementById(`badge_text_${currentDiscountStudentId}`);
            if (badge && badgeText) {
                badgeText.innerText = data.discount_label;
                badge.className = 'discount-trigger-pill ' + (data.has_discount ? (data.percent >= 100 ? 'discount-full' : 'discount-custom') : 'discount-none');
                badge.querySelector('span:first-child').innerText = data.has_discount ? (data.percent >= 100 ? '✨' : '🏷️') : '➕';
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

{{-- النافذة المنبثقة للخصومات (Modal) --}}
<div id="discountModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;" dir="rtl">
    <div style="background: #ffffff; width: 100%; max-width: 520px; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: modalIn 0.25s ease-out;">
        
        <div style="padding: 20px 24px; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: white; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(255,255,255,0.18); display: grid; place-items: center; font-size: 1.3rem;">
                    🏷️
                </div>
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0;">تحديد خصم أو منحة للطالب</h3>
                    <span id="discountStudentName" style="font-size: 0.85rem; color: #c7d2fe; font-weight: 700;">اسم الطالب</span>
                </div>
            </div>
            <button type="button" onclick="closeDiscountModal()" style="background: rgba(255,255,255,0.15); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 1rem; display: grid; place-items: center;">✕</button>
        </div>

        <form id="discountForm" onsubmit="handleDiscountSubmit(event)" style="padding: 24px;">
            <input type="hidden" id="discountStudentId" value="">
            <input type="hidden" id="discountType" value="percent">

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

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #64748b; margin-bottom: 6px;">خيارات سريعة بنقرة واحدة:</label>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="button" onclick="setQuickDiscount(15, 'خصم تشجيعي')" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">15% تشجيعي</button>
                    <button type="button" onclick="setQuickDiscount(25, 'منحة تفوق دراسي')" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">25% تفوق</button>
                    <button type="button" onclick="setQuickDiscount(50, 'نصف منحة دراسية')" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">50% نصف منحة</button>
                    <button type="button" onclick="setQuickDiscount(100, 'إعفاء كامل - منحة شاملة 100%')" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">✨ إعفاء كامل 100%</button>
                    <button type="button" onclick="setQuickDiscount(0, '')" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">❌ إلغاء الخصم</button>
                </div>
            </div>

            <div style="margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155;">قيمة الخصم:</label>
                    <span id="discountUnitLabel" style="font-size: 0.75rem; color: #7c3aed; font-weight: 700;">% (نسبة مئوية)</span>
                </div>
                <input type="number" id="discountValue" min="0" max="100" step="any" placeholder="مثال: 25 أو 50" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1.5px solid #cbd5e1; font-size: 1.05rem; font-weight: 800; font-family: monospace; outline: none; transition: 0.2s; box-sizing: border-box;" onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#cbd5e1'">
            </div>

            <div style="margin-bottom: 22px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">سبب الخصم أو ملاحظات المنحة (اختياري):</label>
                <input type="text" id="discountNotes" placeholder="مثال: منحة تفوق توجيهي / إعفاء خاص" style="width: 100%; padding: 10px 14px; border-radius: 12px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; outline: none; transition: 0.2s; box-sizing: border-box;" onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#cbd5e1'">
                <small style="color: #64748b; font-size: 0.73rem; display: block; margin-top: 4px;">سيظهر هذا السبب للطالب في إشعاراته وتفاصيل حسابه.</small>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" id="btnSaveDiscount" style="flex: 1; background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); color: white; border: none; padding: 13px; border-radius: 12px; font-weight: 800; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 15px rgba(29, 78, 216, 0.3);">
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
