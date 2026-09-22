@extends('layouts.app')

@section('title', __('إدارة الطلاب') . ' | ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
<div class="students-dashboard-clean">

    {{-- 1. رأس الصفحة: العنوان والإحصائيات وزر الإضافة --}}
    <div class="page-header-clean">
        <div class="header-titles">
            <h1 class="page-title-text">
                {{ __('سجل الطلاب') }}
                <span class="count-pill" id="visibleStudentsCount">{{ count($students) }}</span>
            </h1>
            <p class="page-desc-text">
                {{ __('إدارة حسابات الطلبة، تفعيل الاشتراكات، وتخصيص المنح الأكاديمية.') }}
            </p>
        </div>

        <div class="header-actions-group">
            <a href="{{ route('admin.students.export') }}" class="btn-clean btn-outline" title="{{ __('تصدير ملف CSV') }}">
                <i class="fa-solid fa-arrow-up-from-bracket"></i>
                <span>{{ __('تصدير CSV') }}</span>
            </a>
            <button type="button" onclick="confirmPurgeAllStudents()" class="btn-clean btn-danger-outline" title="{{ __('حذف وتصفير جميع الطلاب') }}">
                <i class="fa-regular fa-trash-can"></i>
                <span>{{ __('تصفير الكل') }}</span>
            </button>
            <a href="{{ route('admin.students.create') }}" class="btn-clean btn-primary">
                <i class="fa-solid fa-plus"></i>
                <span>{{ __('إضافة طالب') }}</span>
            </a>
        </div>
    </div>

    {{-- 2. بطاقات المؤشرات الأكاديمية الكلاسيكية --}}
    <div class="stats-row-clean">
        <div class="stat-card-clean" style="--card-accent: #1e3a8a;" onclick="setFilterTab('all')">
            <span class="stat-label">{{ __('إجمالي الطلبة المسجلين') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-navy">{{ count($students) }}</span>
                <i class="fa-solid fa-users stat-icon text-navy"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #d97706;" onclick="setFilterTab('pending')">
            <span class="stat-label">{{ __('بانتظار الاعتماد الأكاديمي') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number {{ $students->where('status', '!=', 'active')->count() > 0 ? 'text-amber' : '' }}">
                    {{ $students->where('status', '!=', 'active')->count() }}
                </span>
                <i class="fa-solid fa-clock-rotate-left stat-icon text-amber"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #059669;" onclick="setFilterTab('active')">
            <span class="stat-label">{{ __('حسابات نشطة ومعتمدة') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-emerald">{{ $students->where('status', 'active')->count() }}</span>
                <i class="fa-solid fa-circle-check stat-icon text-emerald"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #6366f1;" onclick="setFilterTab('all')">
            <span class="stat-label">{{ __('المنح والخصومات المعتمدة') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-indigo">{{ $students->filter(fn($s) => $s->hasDiscount())->count() }}</span>
                <i class="fa-solid fa-award stat-icon text-indigo"></i>
            </div>
        </div>
    </div>

    {{-- 3. شريط البحث والفلاتر الموحد الأنيق --}}
    <div class="toolbar-clean">
        <div class="search-box-clean">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="studentSearchInput" placeholder="{{ __('بحث بالاسم، رقم الهوية، أو البريد الإلكتروني...') }}" oninput="filterStudents()">
            <button type="button" id="clearSearchBtn" onclick="clearSearch()" class="clear-search" style="display: none;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="filter-pills-clean">
            <button type="button" class="filter-pill active" data-filter="all" onclick="setFilterTab('all')">
                {{ __('الكل') }} ({{ count($students) }})
            </button>
            <button type="button" class="filter-pill" data-filter="pending" onclick="setFilterTab('pending')">
                {{ __('بانتظار الموافقة') }} ({{ $students->where('status', '!=', 'active')->count() }})
            </button>
            <button type="button" class="filter-pill" data-filter="active" onclick="setFilterTab('active')">
                {{ __('المعتمدون') }} ({{ $students->where('status', 'active')->count() }})
            </button>
            <button type="button" class="filter-pill" data-filter="sci" onclick="setFilterTab('sci')">
                {{ __('الفرع العلمي') }}
            </button>
            <button type="button" class="filter-pill" data-filter="lit" onclick="setFilterTab('lit')">
                {{ __('الفرع الأدبي') }}
            </button>
            <button type="button" class="filter-pill" data-filter="bus" onclick="setFilterTab('bus')">
                {{ __('ريادة وأعمال') }}
            </button>
        </div>
    </div>

    {{-- شريط التحكم الجماعي (Bulk Action Bar) --}}
    <div id="studentBulkBar" class="bulk-bar-clean" style="display: none;">
        <div class="bulk-info">
            <span class="bulk-count-badge" id="selectedStudentsCount">0</span>
            <span>{{ __('طالب محدد في الجدول') }}</span>
        </div>
        <div class="bulk-actions">
            <button type="button" onclick="deselectAllStudents()" class="btn-clean btn-ghost-white">
                {{ __('إلغاء التحديد') }}
            </button>
            <button type="button" onclick="deleteSelectedStudents()" class="btn-clean btn-danger-solid">
                <i class="fa-regular fa-trash-can"></i>
                <span>{{ __('حذف المحددين') }}</span>
            </button>
        </div>
    </div>

    {{-- 4. جدول الطلاب النظيف والبسيط بدون سكرول أفقي نهائياً --}}
    <div class="table-card-clean">
        <div class="table-container-clean">
            <table class="data-table-clean">
                <thead>
                    <tr>
                        <th style="width: 36px; text-align: center;">
                            <input type="checkbox" id="selectAllStudentsCheckbox" onchange="toggleSelectAllStudents(this)" title="{{ __('تحديد الكل') }}" class="custom-checkbox">
                        </th>
                        <th>{{ __('الطالب') }}</th>
                        <th style="width: 120px;">{{ __('الفرع') }}</th>
                        <th style="width: 110px;">{{ __('الهوية') }}</th>
                        <th style="width: 100px;">{{ __('الحالة') }}</th>
                        <th style="width: 100px;">{{ __('الخصم') }}</th>
                        <th style="width: 145px; text-align: center;">{{ __('الإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    @forelse($students as $student)
                    @php
                        $stageLabel = $student->stage->label_ar ?? ($student->stage->name_ar ?? 'توجيهي');
                        $branchShort = 'عام';
                        if (str_contains($stageLabel, 'علمي')) {
                            $branchShort = 'العلمي';
                        } elseif (str_contains($stageLabel, 'أدبي')) {
                            $branchShort = 'الأدبي';
                        } elseif (str_contains($stageLabel, 'ريادة') || str_contains($stageLabel, 'أعمال') || str_contains($stageLabel, 'تجاري')) {
                            $branchShort = 'ريادة وأعمال';
                        } elseif (str_contains($stageLabel, 'صناعي')) {
                            $branchShort = 'الصناعي';
                        }
                        $studentDispName = (app()->getLocale() === 'en' && !empty($student->name_en)) ? $student->name_en : $student->name_ar;
                    @endphp
                    <tr id="row_{{ $student->id }}" 
                        class="student-row"
                        data-name="{{ mb_strtolower($student->name_ar . ' ' . ($student->name_en ?? '')) }}"
                        data-email="{{ strtolower($student->email) }}"
                        data-nid="{{ $student->nid }}"
                        data-status="{{ $student->status }}"
                        data-branch="{{ $stageLabel }}">
                        
                        {{-- تحديد --}}
                        <td style="text-align: center;">
                            <input type="checkbox" class="student-row-checkbox custom-checkbox" value="{{ $student->id }}" onchange="updateStudentBulkBar()">
                        </td>

                        {{-- معلومات الطالب --}}
                        <td>
                            <div class="cell-student-info">
                                <div class="student-avatar-clean">
                                    @if(!empty($student->photo))
                                        <img src="{{ asset('storage/'.$student->photo) }}" alt="{{ $studentDispName }}" onerror="this.style.display='none';this.nextElementSibling.style.display='grid'">
                                        <span class="avatar-initials" style="display: none;">{{ mb_substr($studentDispName, 0, 2) }}</span>
                                    @else
                                        <span class="avatar-initials">{{ mb_substr($studentDispName, 0, 2) }}</span>
                                    @endif
                                </div>
                                <div class="student-details-clean">
                                    <div class="name-line">
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="student-name" title="{{ __('عرض ملف الطالب') }}">
                                            {{ $studentDispName }}
                                        </a>
                                    </div>
                                    <div class="meta-line">
                                        <span class="student-email" dir="ltr">{{ $student->email }}</span>
                                        @if($student->plain_password)
                                            <span class="pwd-snippet" title="{{ __('كلمة المرور للدخول (انقر للنسخ)') }}" onclick="copyToClipboard('{{ $student->plain_password }}', '{{ __('تم نسخ كلمة المرور') }}')">
                                                <i class="fa-solid fa-key"></i>
                                                <code>{{ $student->plain_password }}</code>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- الفرع --}}
                        <td>
                            <span class="branch-tag-clean">{{ __($branchShort) }}</span>
                        </td>

                        {{-- الهوية الوطنية --}}
                        <td>
                            <span class="nid-text-clean font-mono">{{ $student->nid ?: '-' }}</span>
                        </td>

                        {{-- الحالة --}}
                        <td>
                            @if($student->status === 'active')
                                <span class="status-pill status-active">
                                    <span class="dot"></span>
                                    <span>{{ __('نشط') }}</span>
                                </span>
                            @elseif($student->status === 'suspended' || $student->status === 'frozen' || $student->status === 'inactive')
                                <span class="status-pill status-frozen" title="{{ $student->freeze_reason ?: __('مجمد') }}">
                                    <span class="dot"></span>
                                    <span>{{ __('مجمد') }}</span>
                                </span>
                            @else
                                <span class="status-pill status-pending">
                                    <span class="dot"></span>
                                    <span>{{ __('بانتظار الموافقة') }}</span>
                                </span>
                            @endif
                        </td>

                        {{-- الخصم والمنحة --}}
                        <td>
                            <div id="discount_badge_{{ $student->id }}">
                                @if($student->hasDiscount())
                                    <button type="button" 
                                            onclick="openDiscountModal({{ $student->id }}, '{{ addslashes($studentDispName) }}', {{ (float)($student->custom_discount_percent ?? 0) }}, {{ (float)($student->custom_discount_fixed ?? 0) }}, '{{ addslashes($student->discount_notes ?? '') }}')" 
                                            class="btn-discount-badge"
                                            title="{{ __('تعديل الخصم') }}">
                                        <span id="badge_text_{{ $student->id }}">{{ $student->discount_label }}</span>
                                    </button>
                                @else
                                    <button type="button" 
                                            onclick="openDiscountModal({{ $student->id }}, '{{ addslashes($studentDispName) }}', 0, 0, '')" 
                                            class="btn-discount-none"
                                            title="{{ __('إضافة خصم أو منحة') }}">
                                        <span id="badge_text_{{ $student->id }}">{{ __('بدون خصم') }}</span>
                                    </button>
                                @endif
                            </div>
                        </td>

                        {{-- الإجراءات --}}
                        <td>
                            <div class="actions-cell-clean">
                                @if($student->status !== 'active')
                                    <button type="button" 
                                            onclick="approveStudentDirect({{ $student->id }}, '{{ addslashes($studentDispName) }}')"
                                            class="tbl-btn tbl-btn-approve"
                                            title="{{ __('تفعيل واعتماد الطالب') }}">
                                        {{ __('تفعيل') }}
                                    </button>
                                @endif

                                <a href="{{ route('admin.students.show', $student->id) }}"
                                   class="tbl-btn-icon"
                                   title="{{ __('عرض المواد والملف') }}">
                                    <i class="fa-regular fa-folder-open"></i>
                                </a>

                                <a href="{{ route('admin.subscriptions.monthly', ['search' => $student->nid ?: $student->name_ar]) }}"
                                   class="tbl-btn-icon text-navy"
                                   title="{{ __('كشف واشتراكات الشهور الـ 12 والذمم المالية') }}">
                                    <i class="fa-solid fa-receipt"></i>
                                </a>

                                <button type="button" 
                                        onclick="openDiscountModal({{ $student->id }}, '{{ addslashes($studentDispName) }}', {{ (float)($student->custom_discount_percent ?? 0) }}, {{ (float)($student->custom_discount_fixed ?? 0) }}, '{{ addslashes($student->discount_notes ?? '') }}')"
                                        class="tbl-btn-icon"
                                        title="{{ __('المنحة والخصم') }}">
                                    <i class="fa-solid fa-tag"></i>
                                </button>

                                <button type="button" 
                                        onclick="performToggle({{ $student->id }}, '{{ $student->status }}', '{{ addslashes($studentDispName) }}')"
                                        class="tbl-btn-icon {{ $student->status == 'active' ? '' : 'text-amber' }}"
                                        title="{{ $student->status == 'active' ? __('تجميد الحساب') : __('إلغاء التجميد') }}">
                                    @if($student->status == 'active')
                                        <i class="fa-solid fa-lock"></i>
                                    @else
                                        <i class="fa-solid fa-lock-open"></i>
                                    @endif
                                </button>

                                <a href="{{ route('admin.students.edit', $student->id) }}"
                                   class="tbl-btn-icon"
                                   title="{{ __('تعديل البيانات') }}">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>

                                <button type="button" 
                                        onclick="deleteStudent({{ $student->id }})" 
                                        class="tbl-btn-icon tbl-btn-del" 
                                        title="{{ __('حذف الطالب') }}">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state-cell">
                            <i class="fa-regular fa-user" style="font-size: 1.8rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                            <span>{{ __('لا يوجد طلاب مسجلون حالياً.') }}</span>
                        </td>
                    </tr>
                    @endforelse

                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="7" class="empty-state-cell">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 1.8rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                            <span>{{ __('لا توجد نتائج مطابقة لشروط البحث.') }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* =========================================================
       تصميم نظيف، احترافي، وبسيط 100% بدون أي سكرول أفقي
       Clean, Minimalist, Monochromatic SaaS Design (Zero Scroll)
       ========================================================= */
    .students-dashboard-clean {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    /* 1. رأس الصفحة */
    .page-header-clean {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .page-title-text {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .count-pill {
        font-size: 0.78rem;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .page-desc-text {
        font-size: 0.84rem;
        color: #64748b;
        margin: 3px 0 0;
    }

    .header-actions-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* الأزرار الموحدة */
    .btn-clean {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
        border: 1px solid transparent;
        line-height: 1.4;
    }

    .btn-primary {
        background: #1d4ed8;
        color: #ffffff;
        border: 1px solid #1d4ed8;
        box-shadow: 0 2px 6px rgba(29, 78, 216, 0.2);
    }
    .btn-primary:hover {
        background: #1e3a8a;
        transform: translateY(-1px);
        color: #ffffff;
    }

    .btn-outline {
        background: #ffffff;
        color: #334155;
        border-color: #e2e8f0;
    }
    .btn-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }

    .btn-danger-outline {
        background: #ffffff;
        color: #dc2626;
        border-color: #fecaca;
    }
    .btn-danger-outline:hover {
        background: #fef2f2;
        border-color: #fca5a5;
    }

    /* 2. مؤشرات الأرقام البسيطة (KPIs) */
    .stats-row-clean {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    @media (max-width: 900px) {
        .stats-row-clean {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .stat-card-clean {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-top: 3.5px solid var(--card-accent, #1e3a8a);
        border-radius: 10px;
        padding: 16px 18px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
    }

    .stat-card-clean:hover {
        border-color: #94a3b8;
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
    }

    .stat-label {
        font-size: 0.8rem;
        color: #475569;
        font-weight: 700;
        display: block;
        margin-bottom: 6px;
    }

    .stat-value-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-number {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .stat-icon {
        font-size: 1.15rem;
    }

    .text-navy { color: #1e3a8a !important; }
    .text-emerald { color: #059669 !important; }
    .text-amber { color: #d97706 !important; }
    .text-indigo { color: #6366f1 !important; }

    /* 3. شريط البحث والفلاتر */
    .toolbar-clean {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .search-box-clean {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .search-icon {
        position: absolute;
        right: 12px;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .search-box-clean input {
        width: 100%;
        padding: 8px 36px 8px 32px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-size: 0.85rem;
        color: #0f172a;
        outline: none;
        transition: all 0.15s;
        box-sizing: border-box;
    }

    .search-box-clean input:focus {
        background: #ffffff;
        border-color: #94a3b8;
    }

    .clear-search {
        position: absolute;
        left: 10px;
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 2px;
    }

    .filter-pills-clean {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .filter-pill {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
    }

    .filter-pill:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .filter-pill.active {
        background: #1e3a8a;
        color: #ffffff;
        border-color: #1e3a8a;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2);
    }

    /* شريط التحديد الجماعي */
    .bulk-bar-clean {
        background: #0f172a;
        color: #ffffff;
        border-radius: 8px;
        padding: 10px 16px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        font-size: 0.85rem;
    }

    .bulk-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .bulk-count-badge {
        background: #334155;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 700;
    }

    .bulk-actions {
        display: flex;
        gap: 8px;
    }

    .btn-ghost-white {
        background: rgba(255,255,255,0.12);
        color: #ffffff;
    }
    .btn-ghost-white:hover {
        background: rgba(255,255,255,0.2);
    }

    .btn-danger-solid {
        background: #dc2626;
        color: #ffffff;
    }
    .btn-danger-solid:hover {
        background: #b91c1c;
    }

    /* 4. الجدول النظيف (Zero Horizontal Scroll) */
    .table-card-clean {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        width: 100%;
    }

    .table-container-clean {
        width: 100%;
        overflow-x: auto;
    }

    .data-table-clean {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }

    .data-table-clean th {
        background: #f8fafc;
        color: #0f172a;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.3px;
        padding: 12px 14px;
        border-bottom: 2px solid #cbd5e1;
        white-space: nowrap;
    }

    .data-table-clean th input.custom-checkbox {
        accent-color: #f59e0b;
    }

    .data-table-clean td {
        padding: 11px 14px;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
        font-size: 0.86rem;
        color: #1e293b;
    }

    .data-table-clean tr.student-row:hover {
        background: #f8fafc;
    }

    .custom-checkbox {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #0f172a;
    }

    /* خلية الطالب */
    .cell-student-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .student-avatar-clean {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        overflow: hidden;
    }

    .student-avatar-clean img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-initials {
        font-size: 0.72rem;
        font-weight: 700;
        color: #475569;
    }

    .student-details-clean {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .student-name {
        font-weight: 600;
        color: #0f172a;
        text-decoration: none;
        font-size: 0.86rem;
        white-space: nowrap;
    }
    .student-name:hover {
        color: #2563eb;
    }

    .meta-line {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 1px;
    }

    .student-email {
        font-size: 0.73rem;
        color: #64748b;
        white-space: nowrap;
    }

    .pwd-snippet {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        color: #94a3b8;
        font-size: 0.7rem;
        cursor: pointer;
    }
    .pwd-snippet:hover {
        color: #475569;
    }
    .pwd-snippet code {
        font-family: monospace;
        font-size: 0.72rem;
    }

    /* شارة الفرع */
    .branch-tag-clean {
        display: inline-block;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .nid-text-clean {
        font-size: 0.8rem;
        color: #475569;
        letter-spacing: 0.3px;
    }

    /* مؤشر الحالة النظيف */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.74rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-pill .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-active {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .status-active .dot { background: #16a34a; }

    .status-pending {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .status-pending .dot { background: #d97706; }

    .status-frozen {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    .status-frozen .dot { background: #dc2626; }

    /* أزرار الخصم النظيفة */
    .btn-discount-badge {
        background: #f1f5f9;
        color: #0f172a;
        border: 1px solid #cbd5e1;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.74rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-discount-badge:hover {
        background: #e2e8f0;
    }

    .btn-discount-none {
        background: transparent;
        color: #94a3b8;
        border: none;
        padding: 0;
        font-size: 0.75rem;
        cursor: pointer;
        text-decoration: underline;
        text-underline-offset: 3px;
    }
    .btn-discount-none:hover {
        color: #475569;
    }

    /* أزرار الإجراءات */
    .actions-cell-clean {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        white-space: nowrap;
    }

    .tbl-btn {
        background: #0f172a;
        color: #ffffff;
        border: none;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.74rem;
        font-weight: 600;
        cursor: pointer;
    }
    .tbl-btn:hover {
        background: #1e293b;
    }

    .tbl-btn-icon {
        width: 26px;
        height: 26px;
        border-radius: 4px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.76rem;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s;
    }

    .tbl-btn-icon:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .tbl-btn-del:hover {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    .empty-state-cell {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
        font-size: 0.86rem;
    }
</style>

{{-- السكربتات التفاعلية --}}
<script>
    let currentFilter = 'all';

    function setFilterTab(filterKey) {
        currentFilter = filterKey;
        document.querySelectorAll('.filter-pill').forEach(btn => {
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

    function copyToClipboard(text, msg) {
        navigator.clipboard.writeText(text);
        if (window.Swal) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: msg || 'تم النسخ',
                showConfirmButton: false,
                timer: 1500
            });
        }
    }

    // اعتماد وتفعيل الطالب
    function approveStudentDirect(id, name) {
        Swal.fire({
            title: 'تفعيل حساب الطالب؟',
            text: `هل تريد اعتماد وتفعيل حساب الطالب (${name})؟`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، تفعيل',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post(`{{ url('admin/students') }}/${id}/approve`)
                .then(res => {
                    Swal.fire({
                        icon: 'success',
                        title: res.data?.message || 'تم التفعيل والاعتماد بنجاح',
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => location.reload());
                })
                .catch(err => {
                    const msg = err.response?.data?.message || err.response?.data?.error || 'فشلت عملية الاعتماد، يرجى المحاولة لاحقاً';
                    Swal.fire('خطأ', msg, 'error');
                });
            }
        });
    }

    // تجميد أو إلغاء تجميد الحساب
    function performToggle(id, currentStatus, studentName) {
        if (currentStatus === 'active') {
            Swal.fire({
                title: `تجميد حساب (${studentName})`,
                text: 'يرجى اختيار أو كتابة سبب التجميد:',
                input: 'select',
                inputOptions: {
                    'عدم سداد الرسوم الدراسية': 'عدم سداد الرسوم الدراسية',
                    'مخالفة الشروط والضوابط الأكاديمية': 'مخالفة الضوابط الأكاديمية',
                    'طلب ولي الأمر': 'طلب ولي الأمر',
                    'أخرى': 'سبب آخر (كتابة)'
                },
                inputPlaceholder: 'اختر السبب...',
                showCancelButton: true,
                confirmButtonText: 'تجميد الحساب',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#dc2626',
                preConfirm: (choice) => choice || false
            }).then((result) => {
                if (result.isConfirmed) {
                    let reason = result.value;
                    if (reason === 'أخرى') {
                        Swal.fire({
                            title: 'اكتب سبب التجميد:',
                            input: 'text',
                            showCancelButton: true,
                            confirmButtonText: 'تأكيد التجميد',
                            cancelButtonText: 'إلغاء',
                            confirmButtonColor: '#dc2626',
                            preConfirm: (val) => val ? val.trim() : 'إيقاف إداري'
                        }).then((sub) => {
                            if (sub.isConfirmed) executeToggleStatus(id, sub.value || 'إيقاف إداري');
                        });
                    } else {
                        executeToggleStatus(id, reason);
                    }
                }
            });
        } else {
            Swal.fire({
                title: 'تأكيد التفعيل',
                text: `هل تريد إلغاء التجميد وتفعيل حساب (${studentName})؟`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'تفعيل الحساب',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#0f172a'
            }).then((result) => {
                if (result.isConfirmed) executeToggleStatus(id, null);
            });
        }
    }

    function executeToggleStatus(id, reason) {
        axios.post(`{{ url('admin/students/toggle-status') }}/${id}`, { freeze_reason: reason })
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: 'تم التحديث بنجاح',
                timer: 1400,
                showConfirmButton: false
            }).then(() => location.reload());
        })
        .catch(err => Swal.fire('خطأ', 'فشل تعديل حالة الحساب', 'error'));
    }

    // حذف طالب فردي
    function deleteStudent(id) {
        Swal.fire({
            title: 'هل أنت متأكد من الحذف؟',
            text: "سيتم حذف حساب الطالب وبياناته نهائياً.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`{{ url('admin/students') }}/${id}`)
                .then(res => {
                    const row = document.getElementById(`row_${id}`);
                    if (row) {
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);
                    }
                    Swal.fire('تم الحذف', res.data?.message || 'تمت إزالة حساب الطالب بنجاح', 'success');
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'فشلت عملية الحذف، يرجى التحقق وإعادة المحاولة.';
                    Swal.fire('خطأ', msg, 'error');
                });
            }
        });
    }

    // تحديد جماعي وحذف
    function toggleSelectAllStudents(master) {
        const checkboxes = document.querySelectorAll('.student-row-checkbox');
        checkboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (row && row.style.display !== 'none') {
                cb.checked = master.checked;
            }
        });
        updateStudentBulkBar();
    }

    function deselectAllStudents() {
        const master = document.getElementById('selectAllStudentsCheckbox');
        if (master) master.checked = false;
        document.querySelectorAll('.student-row-checkbox').forEach(cb => cb.checked = false);
        updateStudentBulkBar();
    }

    function updateStudentBulkBar() {
        const checked = document.querySelectorAll('.student-row-checkbox:checked');
        const bar = document.getElementById('studentBulkBar');
        const countSpan = document.getElementById('selectedStudentsCount');
        if (!bar) return;

        if (checked.length > 0) {
            bar.style.display = 'flex';
            if (countSpan) countSpan.innerText = checked.length;
        } else {
            bar.style.display = 'none';
        }
    }

    function deleteSelectedStudents() {
        const checked = document.querySelectorAll('.student-row-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.value);

        if (ids.length === 0) return;

        Swal.fire({
            title: `حذف (${ids.length}) طالب محدد؟`,
            text: 'سيتم حذف حسابات الطلاب المحددين نهائياً ولا يمكن التراجع.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `نعم، احذف (${ids.length}) طالب`,
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#dc2626'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'جاري الحذف...', didOpen: () => Swal.showLoading() });
                axios.post("{{ route('admin.students.bulkDelete') }}", { ids: ids })
                .then(res => {
                    Swal.fire('تم الحذف', res.data.message, 'success')
                    .then(() => location.reload());
                })
                .catch(err => Swal.fire('خطأ', err.response?.data?.message || 'حدث خطأ أثناء الحذف', 'error'));
            }
        });
    }

    // تصفير جميع الطلاب
    function confirmPurgeAllStudents() {
        Swal.fire({
            title: 'تصفير وحذف جميع الطلاب؟',
            html: `
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px; font-size: 0.85rem; color: #991b1b; text-align: right; margin-bottom: 12px;">{{ __('أنت على وشك حذف') }}<strong>{{ __('كافة الطلاب وسجلاتهم بالكامل') }}</strong>{{ __('. للتأكيد اكتب:') }}<strong>{{ __('تأكيد الحذف') }}</strong>
                </div>
            `,
            input: 'text',
            inputPlaceholder: 'اكتب هنا: تأكيد الحذف',
            showCancelButton: true,
            confirmButtonText: 'تأكيد وحذف الكل',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#dc2626',
            preConfirm: (val) => {
                if (val !== 'تأكيد الحذف' && val !== 'DELETE') {
                    Swal.showValidationMessage('يرجى كتابة (تأكيد الحذف) بدقة');
                    return false;
                }
                return val;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'جاري المعالجة...', didOpen: () => Swal.showLoading() });
                axios.post("{{ route('admin.students.purgeAll') }}", { confirm_text: result.value })
                .then(res => {
                    Swal.fire('تم التصفير', res.data.message, 'success')
                    .then(() => location.reload());
                })
                .catch(err => Swal.fire('خطأ', err.response?.data?.message || 'فشلت عملية التصفير', 'error'));
            }
        });
    }

    // مودال المنح والخصومات
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

        document.getElementById('discountModalOverlay').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeDiscountModal() {
        document.getElementById('discountModalOverlay').style.display = 'none';
        document.body.style.overflow = '';
        currentDiscountStudentId = null;
    }

    function setDiscountType(type) {
        document.getElementById('discountType').value = type;
        const btnPercent = document.getElementById('typeBtnPercent');
        const btnFixed = document.getElementById('typeBtnFixed');
        const unitLabel = document.getElementById('discountUnitLabel');

        if (type === 'percent') {
            btnPercent.style.background = '#0f172a';
            btnPercent.style.color = '#ffffff';
            btnPercent.style.borderColor = '#0f172a';
            btnFixed.style.background = '#ffffff';
            btnFixed.style.color = '#475569';
            btnFixed.style.borderColor = '#e2e8f0';
            unitLabel.innerText = '% نسبة مئوية';
            document.getElementById('discountValue').placeholder = '25 أو 50 أو 100';
            document.getElementById('discountValue').max = '100';
        } else {
            btnFixed.style.background = '#0f172a';
            btnFixed.style.color = '#ffffff';
            btnFixed.style.borderColor = '#0f172a';
            btnPercent.style.background = '#ffffff';
            btnPercent.style.color = '#475569';
            btnPercent.style.borderColor = '#e2e8f0';
            unitLabel.innerText = '₪ شيكل';
            document.getElementById('discountValue').placeholder = '50 أو 100';
            document.getElementById('discountValue').removeAttribute('max');
        }
    }

    function setQuickDiscount(percent, notes) {
        setDiscountType('percent');
        document.getElementById('discountValue').value = percent;
        document.getElementById('discountNotes').value = notes || '';
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
            const container = document.getElementById(`discount_badge_${currentDiscountStudentId}`);
            if (container) {
                if (data.has_discount) {
                    container.innerHTML = `<button type="button" onclick="openDiscountModal(${currentDiscountStudentId}, '${document.getElementById('discountStudentName').innerText}', ${data.percent || 0}, ${data.fixed || 0}, '${(data.notes || '').replace(/'/g, "\\'")}')" class="btn-discount-badge"><span id="badge_text_${currentDiscountStudentId}">${data.discount_label}</span></button>`;
                } else {
                    container.innerHTML = `<button type="button" onclick="openDiscountModal(${currentDiscountStudentId}, '${document.getElementById('discountStudentName').innerText}', 0, 0, '')" class="btn-discount-none"><span id="badge_text_${currentDiscountStudentId}">{{ __('بدون خصم') }}</span></button>`;
                }
            }

            closeDiscountModal();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: data.message || 'تم تحديث الخصم بنجاح',
                showConfirmButton: false,
                timer: 1500
            });
        })
        .catch(err => {
            Swal.fire('خطأ', 'فشل حفظ الخصم', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
</script>

{{-- مودال الخصم النظيف البسيط --}}
<div id="discountModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); z-index: 9999; justify-content: center; align-items: center; padding: 16px;">
    <div style="background: #ffffff; width: 100%; max-width: 440px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="font-size: 0.98rem; font-weight: 700; margin: 0; color: #0f172a;">{{ __('الخصم أو المنحة') }}</h3>
                <span id="discountStudentName" style="font-size: 0.78rem; color: #64748b;">{{ __('اسم الطالب') }}</span>
            </div>
            <button type="button" onclick="closeDiscountModal()" style="background: none; border: none; font-size: 1.1rem; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form id="discountForm" onsubmit="handleDiscountSubmit(event)" style="padding: 18px;">
            <input type="hidden" id="discountStudentId" value="">
            <input type="hidden" id="discountType" value="percent">

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('نوع الخصم:') }}</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <button type="button" id="typeBtnPercent" onclick="setDiscountType('percent')" style="padding: 8px; border-radius: 6px; border: 1px solid #0f172a; background: #0f172a; color: #ffffff; font-weight: 600; font-size: 0.8rem; cursor: pointer;">
                        {{ __('نسبة مئوية (%)') }}
                    </button>
                    <button type="button" id="typeBtnFixed" onclick="setDiscountType('fixed')" style="padding: 8px; border-radius: 6px; border: 1px solid #e2e8f0; background: #ffffff; color: #475569; font-weight: 600; font-size: 0.8rem; cursor: pointer;">
                        {{ __('مبلغ ثابت (₪)') }}
                    </button>
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                    <button type="button" onclick="setQuickDiscount(25, 'منحة تفوق')" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; padding: 4px 8px; border-radius: 4px; font-size: 0.72rem; cursor: pointer;">{{ __('25% تفوق') }}</button>
                    <button type="button" onclick="setQuickDiscount(50, 'نصف منحة')" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; padding: 4px 8px; border-radius: 4px; font-size: 0.72rem; cursor: pointer;">{{ __('50% نصف منحة') }}</button>
                    <button type="button" onclick="setQuickDiscount(100, 'إعفاء كامل 100%')" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 600; cursor: pointer;">{{ __('100% إعفاء كامل') }}</button>
                    <button type="button" onclick="setQuickDiscount(0, '')" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 4px 8px; border-radius: 4px; font-size: 0.72rem; cursor: pointer;">{{ __('إلغاء الخصم') }}</button>
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <label style="font-size: 0.78rem; font-weight: 600; color: #334155;">{{ __('قيمة الخصم:') }}</label>
                    <span id="discountUnitLabel" style="font-size: 0.72rem; color: #64748b;">{{ __('% نسبة مئوية') }}</span>
                </div>
                <input type="number" id="discountValue" min="0" max="100" step="any" placeholder="{{ __('مثال: 25 أو 50') }}" style="width: 100%; padding: 8px 10px; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 0.95rem; font-family: monospace; outline: none; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #334155; margin-bottom: 4px;">{{ __('بيان الخصم (اختياري):') }}</label>
                <input type="text" id="discountNotes" placeholder="{{ __('مثال: منحة تفوق دراسي') }}" style="width: 100%; padding: 8px 10px; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 0.82rem; outline: none; box-sizing: border-box;">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" id="btnSaveDiscount" class="btn-clean btn-primary" style="flex: 1; justify-content: center;">
                    {{ __('حفظ التحديث') }}
                </button>
                <button type="button" onclick="closeDiscountModal()" class="btn-clean btn-outline">
                    {{ __('إلغاء') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
