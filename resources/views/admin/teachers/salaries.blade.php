@extends('layouts.app')

@section('title', __('إدارة مسير وصرف رواتب المعلمين') . ' - ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
<div class="admin-payroll-wrapper">
    {{-- 1. الهيدر الأكاديمي الفاتح --}}
    <div class="payroll-header-box">
        <div>
            <div class="badge-tag">
                <i class="fa-solid fa-money-bill-transfer"></i>
                <span>{{ __('الإدارة المالية وشؤون الكادر التعليمي') }}</span>
            </div>
            <h1 class="page-title">{{ __('إدارة ومسير رواتب المعلمين لشهر وعام محدد') }}</h1>
            <p class="page-subtitle">{{ __('تسجيل مستحقات المعلمين، صرف الرواتب الشهرية، وإصدار قسائم الراتب الرسمية') }}</p>
        </div>
        <div class="header-btns">
            <button type="button" class="btn-create-salary" onclick="openCreateSalaryModal()">
                <i class="fa-solid fa-circle-plus"></i> {{ __('تسجيل / صرف راتب معلم جديد') }}
            </button>
        </div>
    </div>

    {{-- 2. إحصائيات الرواتب الكلاسيكية الفاتحة --}}
    <div class="stats-row-clean">
        <div class="stat-card-clean" style="--card-accent: #059669;">
            <span class="stat-label">{{ __('إجمالي الرواتب المصروفة') }} ({{ __('عام') }} {{ $year }})</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-emerald font-mono">{{ number_format($stats['total_disbursed'], 2) }} ₪</span>
                <i class="fa-solid fa-vault stat-icon text-emerald"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #d97706;">
            <span class="stat-label">{{ __('مستحقات قيد الاعتماد والصرف') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-amber font-mono">{{ number_format($stats['total_pending'], 2) }} ₪</span>
                <i class="fa-solid fa-hourglass-start stat-icon text-amber"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #1e3a8a;">
            <span class="stat-label">{{ __('إجمالي المكافآت والحوافز') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-navy font-mono">{{ number_format($stats['total_bonus'], 2) }} ₪</span>
                <i class="fa-solid fa-award stat-icon text-navy"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #6366f1;">
            <span class="stat-label">{{ __('عدد المعلمين المسجلين') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-indigo font-mono">{{ $stats['teachers_count'] }} {{ __('معلماً') }}</span>
                <i class="fa-solid fa-users-rectangle stat-icon text-indigo"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: {{ ($stats['pending_claims'] > 0 || ($stats['all_pending_claims'] ?? 0) > 0) ? '#dc2626' : '#64748b' }};">
            <span class="stat-label">{{ __('استفسارات المعلمين المالية') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number {{ ($stats['pending_claims'] > 0 || ($stats['all_pending_claims'] ?? 0) > 0) ? 'text-rose' : 'text-slate-500' }} font-mono">{{ $stats['pending_claims'] }}</span>
                <i class="fa-solid fa-comments-dollar stat-icon {{ ($stats['pending_claims'] > 0 || ($stats['all_pending_claims'] ?? 0) > 0) ? 'text-rose' : 'text-slate-400' }}"></i>
            </div>
            @if($stats['pending_claims'] > 0)
                <small style="color: #dc2626; font-size: 0.72rem; font-weight: 700;">{{ __('بانتظار رد الإدارة') }}</small>
            @elseif(($stats['all_pending_claims'] ?? 0) > 0)
                <small style="color: #d97706; font-size: 0.72rem; font-weight: 700;">{{ __('يوجد :count استفسار في فلاتر أخرى', ['count' => $stats['all_pending_claims']]) }}</small>
            @else
                <small style="color: #059669; font-size: 0.72rem; font-weight: 700;">{{ __('كافة الاستفسارات تمت الإجابة عليها ✅') }}</small>
            @endif
        </div>
    </div>

    {{-- 3. شريط الفلاتر والبحث --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.teachers.salaries') }}" class="filter-form">
            <div class="filter-group">
                <label>{{ __('السنة:') }}</label>
                <select name="year" class="form-select-sm" onchange="this.form.submit()">
                    @for($y = date('Y') + 1; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }} {{ app()->getLocale() === 'ar' ? 'م' : 'AD' }}</option>
                    @endfor
                </select>
            </div>

            <div class="filter-group">
                <label>{{ __('المعلم:') }}</label>
                <select name="teacher_id" class="form-select-sm" onchange="this.form.submit()">
                    <option value="">{{ __('كافة المعلمين') }}</option>
                    @foreach($teachers as $t)
                        @php
                            $tName = (app()->getLocale() === 'en' && !empty($t->name_en)) ? $t->name_en : $t->name;
                            $tSub = (app()->getLocale() === 'en' && !empty($t->subject->name_en)) ? $t->subject->name_en : ($t->subject->name_ar ?? __('عام'));
                        @endphp
                        <option value="{{ $t->id }}" {{ $teacherId == $t->id ? 'selected' : '' }}>{{ $tName }} ({{ $tSub }})</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>{{ __('الشهر:') }}</label>
                <select name="month" class="form-select-sm" onchange="this.form.submit()">
                    <option value="">{{ __('كافة أشهر السنة') }}</option>
                    @foreach($monthsNames as $mNum => $mLabel)
                        <option value="{{ $mNum }}" {{ $monthFilter == $mNum ? 'selected' : '' }}>{{ $mLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>{{ __('الحالة:') }}</label>
                <select name="status" class="form-select-sm" onchange="this.form.submit()">
                    <option value="">{{ __('الكل') }}</option>
                    <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>{{ __('تم الصرف ✅') }}</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>{{ __('قيد الاعتماد ⏳') }}</option>
                </select>
            </div>

            @if($teacherId || $monthFilter || $statusFilter)
                <a href="{{ route('admin.teachers.salaries', ['year' => $year]) }}" class="btn-clear-filter">{{ __('إلغاء الفلترة') }}</a>
            @endif
        </form>
    </div>

    {{-- استفسارات ومطالبات المعلمين المالية --}}
    @if(isset($financialClaims) && $financialClaims->count() > 0)
        <div class="claims-summary-card">
            <div class="claims-header">
                <div class="claims-header-title">
                    <i class="fa-solid fa-comments-dollar text-amber" style="font-size: 1.4rem;"></i>
                    <div>
                        <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0f172a;">{{ __('استفسارات وملاحظات المعلمين المالية') }} <span class="claims-count-badge">{{ $financialClaims->count() }}</span></h3>
                        <p style="margin: 3px 0 0; font-size: 0.8rem; color: #64748b;">{{ __('متابعة وتدقيق استفسارات المعلمين حول الرواتب والمستحقات، والرد عليها فورياً لتصل لحساب المعلم.') }}</p>
                    </div>
                </div>
                @if($stats['pending_claims'] > 0)
                    <span class="badge-pending-claims"><i class="fa-solid fa-clock-rotate-left"></i> {{ $stats['pending_claims'] }} {{ __('استفسار بانتظار الرد') }}</span>
                @else
                    <span class="badge-all-replied"><i class="fa-solid fa-circle-check"></i> {{ __('كافة الاستفسارات تمت الإجابة عليها') }}</span>
                @endif
            </div>

            <div class="claims-grid-list">
                @foreach($financialClaims as $claim)
                    <div class="claim-item-card {{ $claim->status === 'replied' ? 'claim-replied' : 'claim-pending' }}">
                        <div class="claim-item-top">
                            <div class="claim-teacher-info">
                                <i class="fa-solid fa-user-tie teacher-avatar-icon"></i>
                                <div>
                                    <strong>{{ $claim->teacher->name ?? __('معلم') }}</strong>
                                    <span class="claim-sub-tag">{{ $claim->teacher->subject->name_ar ?? '' }} • {{ $claim->month_name_ar }} {{ $claim->year }}</span>
                                </div>
                            </div>
                            <span class="status-pill {{ $claim->status === 'replied' ? 'status-paid' : 'status-pending' }}">
                                {{ $claim->status_badge['label'] }}
                            </span>
                        </div>
                        <div class="claim-message-body">
                            <i class="fa-solid fa-quote-right quote-icon"></i>
                            <p>{{ $claim->message }}</p>
                            <small class="claim-date">{{ $claim->created_at ? $claim->created_at->diffForHumans() : '' }}</small>
                        </div>
                        @if($claim->admin_reply)
                            <div class="claim-admin-reply-box">
                                <div class="reply-header">
                                    <i class="fa-solid fa-reply"></i>
                                    <strong>{{ __('رد الإدارة العامة المعتمد:') }}</strong>
                                    <small class="reply-date">({{ $claim->replied_at ? $claim->replied_at->format('Y-m-d h:i A') : '' }})</small>
                                </div>
                                <p class="reply-text">{{ $claim->admin_reply }}</p>
                            </div>
                        @endif
                        <div class="claim-actions">
                            <button type="button" class="btn-reply-claim" onclick="openReplyClaimModal({{ $claim->id }}, '{{ addslashes($claim->teacher->name ?? '') }}', '{{ addslashes($claim->month_name_ar) }}', '{{ addslashes(str_replace(["\r", "\n"], ' ', $claim->message)) }}', '{{ addslashes(str_replace(["\r", "\n"], ' ', $claim->admin_reply ?? '')) }}')">
                                <i class="fa-solid fa-reply"></i> {{ $claim->status === 'replied' ? __('تعديل الرد على المعلم') : __('الرد على استفسار المعلم') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="claims-summary-card" style="padding: 16px 20px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <strong style="font-size: 0.94rem; color: #064e3b; display: block;">{{ __('لا توجد استفسارات مالية للمعلمين في هذا الفلتر') }}</strong>
                    <small style="color: #64748b;">{{ __('أي استفسار مالي أو ملاحظة راتب يرسلها أي معلم ستظهر هنا فورياً للإدارة للتدقيق والرد عليها مباشرة.') }}</small>
                </div>
            </div>
        </div>
    @endif

    {{-- تنبيه لوجود استفسارات مالية معلقة خارج نطاق الفلتر الحالي --}}
    @if(($stats['all_pending_claims'] ?? 0) > 0 && (!isset($financialClaims) || $financialClaims->where('status', 'pending')->count() == 0))
        <div style="background: #fffbeb; border: 1.5px solid #f59e0b; border-radius: 10px; padding: 14px 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15);">
            <div style="display: flex; align-items: center; gap: 12px; color: #92400e;">
                <i class="fa-solid fa-bell text-amber" style="font-size: 1.3rem;"></i>
                <div>
                    <strong style="font-size: 0.94rem; display: block;">{{ __('تنبيه إداري: يوجد :count استفسار مالي معلق من المعلمين في سنوات أو فلاتر أخرى بانتظار ردك.', ['count' => $stats['all_pending_claims']]) }}</strong>
                    <span style="font-size: 0.8rem; color: #b45309;">{{ __('اضغط على الزر لإلغاء الفلترة واستعراض كافة الاستفسارات المعلقة للرد عليها.') }}</span>
                </div>
            </div>
            <a href="{{ route('admin.teachers.salaries') }}" class="btn-clear-filter" style="background: #d97706; color: #ffffff; padding: 8px 16px; border-radius: 8px; font-weight: 800; text-decoration: none; font-size: 0.84rem;">
                <i class="fa-solid fa-filter-circle-xmark"></i> {{ __('عرض كافة الاستفسارات') }}
            </a>
        </div>
    @endif

    {{-- 4. جدول وبطاقات الرواتب الفاتحة الأكاديمية --}}
    <div class="table-container-card">
        <div class="payroll-table-wrap">
            <table class="payroll-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">{{ __('المعلم والمادة') }}</th>
                        <th style="width: 15%;">{{ __('الشهر والعام') }}</th>
                        <th style="width: 22%;">{{ __('تفاصيل الراتب') }}</th>
                        <th style="width: 14%;">{{ __('صافي الراتب') }}</th>
                        <th style="width: 14%;">{{ __('حالة وطريقة الصرف') }}</th>
                        <th style="width: 10%; text-align: center;">{{ __('إجراءات') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaries as $sal)
                        @php
                            $isPaid = $sal->status === 'paid';
                            $monthLabel = $monthsNames[$sal->month] ?? (app()->getLocale() === 'en' ? "Month {$sal->month}" : "شهر {$sal->month}");
                            $teacherDisplayName = (app()->getLocale() === 'en' && !empty($sal->teacher->name_en)) ? $sal->teacher->name_en : $sal->teacher->name;
                            $subjectDisplayName = (app()->getLocale() === 'en' && !empty($sal->teacher->subject->name_en)) ? $sal->teacher->subject->name_en : ($sal->teacher->subject->name_ar ?? __('كادر التدريس'));
                        @endphp
                        <tr>
                            <td>
                                <div class="teacher-meta-cell">
                                    <img src="{{ $sal->teacher->photo ? asset('storage/' . $sal->teacher->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($teacherDisplayName) . '&background=0284c7&color=fff&size=80&bold=true' }}" class="teacher-thumb" alt="{{ $teacherDisplayName }}">
                                    <div class="teacher-meta-text">
                                        <strong class="teacher-name-text">{{ $teacherDisplayName }}</strong>
                                        <span class="teacher-subject-pill">{{ $subjectDisplayName }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="month-pill"><i class="fa-regular fa-calendar-check"></i> {{ $monthLabel }} {{ $sal->year }}</span>
                            </td>
                            <td>
                                <div class="salary-breakdown-compact">
                                    <span class="base-badge font-mono">{{ __('الأساسي:') }} {{ number_format($sal->basic_salary, 2) }} ₪</span>
                                    @if($sal->bonus > 0)
                                        <span class="bonus-badge font-mono">+{{ number_format($sal->bonus, 2) }} ₪ {{ __('إضافي') }}</span>
                                    @endif
                                    @if($sal->deductions > 0)
                                        <span class="deduct-badge font-mono">-{{ number_format($sal->deductions, 2) }} ₪ {{ __('خصم') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="font-mono font-bold text-base {{ $isPaid ? 'text-paid' : 'text-pending' }}">
                                    {{ number_format($sal->net_salary, 2) }} ₪
                                </span>
                            </td>
                            <td>
                                <div class="payment-col-wrap">
                                    <span class="status-pill {{ $isPaid ? 'status-paid' : 'status-pending' }}">
                                        {{ $isPaid ? __('تم الصرف ✅') : __('قيد الصرف ⏳') }}
                                    </span>
                                    <small class="payment-method-label">{{ $sal->payment_method ? __($sal->payment_method) : __('تحويل بنكي') }} {{ $sal->payment_date ? '(' . $sal->payment_date->format('m/d') . ')' : '' }}</small>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div class="actions-group">
                                    <button type="button" class="btn-action-edit" onclick='openEditSalaryModal(@json($sal))' title="{{ __('تعديل بيانات الراتب') }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn-action-delete" onclick="deleteSalaryRecord({{ $sal->id }})" title="{{ __('حذف السجل') }}">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                                <i class="fa-solid fa-receipt" style="font-size: 2.2rem; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
                                <strong>{{ __('لا توجد سجلات رواتب مدخلة تطابق معايير الفلترة المحددة.') }}</strong>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- عرض بطاقات الجوال الفاتحة --}}
        <div class="payroll-cards-mobile">
            @forelse($salaries as $sal)
                @php
                    $isPaid = $sal->status === 'paid';
                    $monthLabel = $monthsNames[$sal->month] ?? (app()->getLocale() === 'en' ? "Month {$sal->month}" : "شهر {$sal->month}");
                    $teacherDisplayName = (app()->getLocale() === 'en' && !empty($sal->teacher->name_en)) ? $sal->teacher->name_en : $sal->teacher->name;
                    $subjectDisplayName = (app()->getLocale() === 'en' && !empty($sal->teacher->subject->name_en)) ? $sal->teacher->subject->name_en : ($sal->teacher->subject->name_ar ?? __('كادر التدريس'));
                @endphp
                <div class="payroll-mob-card">
                    <div class="mob-card-header">
                        <div class="teacher-meta-cell">
                            <img src="{{ $sal->teacher->photo ? asset('storage/' . $sal->teacher->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($teacherDisplayName) . '&background=0284c7&color=fff&size=80&bold=true' }}" class="teacher-thumb" alt="{{ $teacherDisplayName }}">
                            <div>
                                <strong class="teacher-name-text">{{ $teacherDisplayName }}</strong>
                                <span class="teacher-subject-pill">{{ $subjectDisplayName }}</span>
                            </div>
                        </div>
                        <span class="status-pill {{ $isPaid ? 'status-paid' : 'status-pending' }}">
                            {{ $isPaid ? __('تم الصرف ✅') : __('قيد الصرف ⏳') }}
                        </span>
                    </div>

                    <div class="mob-card-body">
                        <div class="mob-stat-row">
                            <span class="mob-label">{{ __('الشهر والسنة:') }}</span>
                            <span class="month-pill font-mono">{{ $monthLabel }} {{ $sal->year }}</span>
                        </div>
                        <div class="mob-stat-row">
                            <span class="mob-label">{{ __('صافي الراتب:') }}</span>
                            <strong class="font-mono text-base {{ $isPaid ? 'text-paid' : 'text-pending' }}">{{ number_format($sal->net_salary, 2) }} ₪</strong>
                        </div>
                        <div class="mob-stat-row">
                            <span class="mob-label">{{ __('تفاصيل:') }}</span>
                            <div class="salary-breakdown-compact">
                                <span class="base-badge font-mono">{{ __('أساسي:') }} {{ number_format($sal->basic_salary, 0) }} ₪</span>
                                @if($sal->bonus > 0)
                                    <span class="bonus-badge font-mono">+{{ number_format($sal->bonus, 0) }} ₪</span>
                                @endif
                                @if($sal->deductions > 0)
                                    <span class="deduct-badge font-mono">-{{ number_format($sal->deductions, 0) }} ₪</span>
                                @endif
                            </div>
                        </div>
                        <div class="mob-stat-row">
                            <span class="mob-label">{{ __('الصرف:') }}</span>
                            <span style="font-size: 0.8rem; color: #64748b;">{{ $sal->payment_method ? __($sal->payment_method) : __('تحويل بنكي') }} {{ $sal->payment_date ? '(' . $sal->payment_date->format('Y-m-d') . ')' : '' }}</span>
                        </div>
                    </div>

                    <div class="mob-card-footer">
                        <button type="button" class="btn-mob-edit" onclick='openEditSalaryModal(@json($sal))'>
                            <i class="fa-solid fa-pen"></i> {{ __('تعديل') }}
                        </button>
                        <button type="button" class="btn-mob-delete" onclick="deleteSalaryRecord({{ $sal->id }})">
                            <i class="fa-solid fa-trash-can"></i> {{ __('حذف') }}
                        </button>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 30px; color: #64748b;">
                    <i class="fa-solid fa-receipt" style="font-size: 2.2rem; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                    <strong>{{ __('لا توجد سجلات رواتب مدخلة.') }}</strong>
                </div>
            @endforelse
        </div>

        <div style="margin-top: 20px;">
            {{ $salaries->links() }}
        </div>
    </div>
</div>

{{-- 5. مودال تسجيل أو تعديل راتب معلم الفاتح --}}
<div id="salaryFormModal" class="modal-overlay" style="display: none;">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalSalaryTitle">{{ __('تسجيل وصرف راتب معلم') }}</h3>
            <button type="button" class="btn-close-modal" onclick="closeSalaryModal()">&times;</button>
        </div>

        <form id="salarySaveForm" onsubmit="handleSaveSalary(event)">
            @csrf
            <div class="modal-body-form">
                <div class="form-row-2">
                    <div class="form-col">
                        <label class="form-label">{{ __('المعلم') }} <span class="required" style="color: #dc2626;">*</span></label>
                        <select name="teacher_id" id="modalTeacherId" class="modal-input" required>
                            <option value="">{{ __('اختر المعلم') }}</option>
                            @foreach($teachers as $t)
                                @php
                                    $tName = (app()->getLocale() === 'en' && !empty($t->name_en)) ? $t->name_en : $t->name;
                                    $tSub = (app()->getLocale() === 'en' && !empty($t->subject->name_en)) ? $t->subject->name_en : ($t->subject->name_ar ?? __('عام'));
                                @endphp
                                <option value="{{ $t->id }}">{{ $tName }} ({{ $tSub }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-col">
                        <label class="form-label">{{ __('السنة المالية') }} <span class="required" style="color: #dc2626;">*</span></label>
                        <input type="number" name="year" id="modalYear" class="modal-input font-mono" value="{{ $year }}" required min="2024" max="2030">
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-col">
                        <label class="form-label">{{ __('شهر الراتب') }} <span class="required" style="color: #dc2626;">*</span></label>
                        <select name="month" id="modalMonth" class="modal-input" required>
                            @foreach($monthsNames as $mNum => $mLabel)
                                <option value="{{ $mNum }}" {{ date('n') == $mNum ? 'selected' : '' }}>{{ $mLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-col">
                        <label class="form-label">{{ __('حالة الصرف') }} <span class="required" style="color: #dc2626;">*</span></label>
                        <select name="status" id="modalStatus" class="modal-input" required>
                            <option value="paid">{{ __('تم الصرف والاستلام ✅') }}</option>
                            <option value="pending">{{ __('قيد الاعتماد والإجراء ⏳') }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-col">
                        <label class="form-label">{{ __('الراتب الأساسي (₪)') }} <span class="required" style="color: #dc2626;">*</span></label>
                        <input type="number" step="0.01" name="basic_salary" id="modalBasicSalary" class="modal-input font-mono" placeholder="{{ __('0.00') }}" required oninput="calcNetSalary()">
                    </div>

                    <div class="form-col">
                        <label class="form-label">{{ __('المكافآت والحوافز (₪)') }}</label>
                        <input type="number" step="0.01" name="bonus" id="modalBonus" class="modal-input font-mono" value="0.00" oninput="calcNetSalary()">
                    </div>

                    <div class="form-col">
                        <label class="form-label">{{ __('الخصومات (₪)') }}</label>
                        <input type="number" step="0.01" name="deductions" id="modalDeductions" class="modal-input font-mono" value="0.00" oninput="calcNetSalary()">
                    </div>
                </div>

                <div class="net-calc-box">
                    <span>{{ __('صافي الراتب المستحق تلقائياً:') }}</span>
                    <strong id="modalNetDisplay" class="font-mono">0.00 ₪</strong>
                </div>

                <div class="form-row-2">
                    <div class="form-col">
                        <label class="form-label">{{ __('طريقة الصرف') }}</label>
                        <select name="payment_method" id="modalPaymentMethod" class="modal-input">
                            <option value="تحويل بنكي">{{ __('تحويل بنكي') }}</option>
                            <option value="جوال باي (Jawwal Pay)">{{ __('جوال باي (Jawwal Pay)') }}</option>
                            <option value="بال باي (PalPay)">{{ __('بال باي (PalPay)') }}</option>
                            <option value="سداد نقدي (كاش)">{{ __('سداد نقدي (كاش)') }}</option>
                            <option value="شيك بنكي">{{ __('شيك بنكي') }}</option>
                        </select>
                    </div>

                    <div class="form-col">
                        <label class="form-label">{{ __('تاريخ الصرف') }}</label>
                        <input type="date" name="payment_date" id="modalPaymentDate" class="modal-input font-mono" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="form-row-full">
                    <label class="form-label">{{ __('رقم السند / المرجع') }}</label>
                    <input type="text" name="reference_no" id="modalReferenceNo" class="modal-input" placeholder="{{ __('مثال: REF-9842') }}">
                </div>

                <div class="form-row-full">
                    <label class="form-label">{{ __('ملاحظات وبيان الصرف') }}</label>
                    <textarea name="notes" id="modalNotes" rows="2" class="modal-input" placeholder="{{ __('أي تفاصيل أو ملاحظات تخص الصرف...') }}"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-modal-save" id="btnSaveSalary">
                    <i class="fa-solid fa-check"></i> {{ __('حفظ واعتماد الراتب') }}
                </button>
                <button type="button" class="btn-modal-cancel" onclick="closeSalaryModal()">{{ __('إلغاء') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- مودال الرد على استفسار المعلم المالي --}}
<div id="replyClaimModal" class="modal-overlay" style="display: none;">
    <div class="modal-box" style="max-width: 580px;">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <i class="fa-solid fa-reply text-primary" style="font-size: 1.4rem;"></i>
                <div>
                    <h3 id="replyModalTitle">{{ __('الرد على استفسار المعلم المالي') }}</h3>
                    <p id="replyModalSubtitle" style="font-size: 0.8rem; color: #64748b; margin: 2px 0 0;"></p>
                </div>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeReplyClaimModal()">&times;</button>
        </div>

        <form id="replyClaimForm" onsubmit="handleReplyClaimSubmit(event)">
            @csrf
            <input type="hidden" id="replyClaimId">

            <div class="modal-body">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 16px;">
                    <div style="font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 4px;">
                        <i class="fa-solid fa-quote-right text-amber"></i> {{ __('نص استفسار المعلم:') }}
                    </div>
                    <p id="replyOriginalMessage" style="font-size: 0.88rem; color: #1e293b; margin: 0; line-height: 1.6; white-space: pre-wrap;"></p>
                </div>

                <div class="form-row-full">
                    <label class="form-label">{{ __('الرد الرسمي من الإدارة العامة') }} <span class="required" style="color: #dc2626;">*</span></label>
                    <textarea id="adminReplyText" rows="4" class="modal-input" placeholder="{{ __('اكتب ردك الواضح والشامل للمعلم هنا...') }}" required style="line-height: 1.6;"></textarea>
                    <small style="color: #64748b; font-size: 0.75rem;">{{ __('سيظهر هذا الرد فوراً في شاشة المعلم مع إرسال إشعار رسمي لحسابه.') }}</small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-modal-save" id="btnSubmitClaimReply">
                    <i class="fa-solid fa-paper-plane"></i> {{ __('اعتماد وإرسال الرد للمعلم') }}
                </button>
                <button type="button" class="btn-modal-cancel" onclick="closeReplyClaimModal()">{{ __('إلغاء') }}</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const payrollI18n = {
        titleCreate: "{{ __('تسجيل وصرف راتب معلم جديد') }}",
        titleEdit: "{{ __('تعديل مسير راتب المعلم') }}",
        saving: "{{ __('جاري الحفظ...') }}",
        saveBtn: "{{ __('حفظ واعتماد الراتب') }}",
        successTitle: "{{ __('تم الحفظ بنجاح') }}",
        errorTitle: "{{ __('خطأ') }}",
        deleteConfirmTitle: "{{ __('هل أنت متأكد من حذف السجل؟') }}",
        deleteConfirmText: "{{ __('سيتم حذف سجل مسير الراتب نهائياً!') }}",
        deleteConfirmBtn: "{{ __('نعم، احذف') }}",
        cancelBtn: "{{ __('إلغاء') }}",
        deletedSuccess: "{{ __('تم الحذف بنجاح') }}",
        deleteFailed: "{{ __('فشل حذف السجل') }}"
    };

    function calcNetSalary() {
        const basic = parseFloat(document.getElementById('modalBasicSalary').value || 0);
        const bonus = parseFloat(document.getElementById('modalBonus').value || 0);
        const deductions = parseFloat(document.getElementById('modalDeductions').value || 0);
        const net = Math.max(0, (basic + bonus) - deductions);
        document.getElementById('modalNetDisplay').innerText = net.toFixed(2) + ' ₪';
    }

    function openCreateSalaryModal() {
        document.getElementById('modalSalaryTitle').innerText = payrollI18n.titleCreate;
        document.getElementById('salarySaveForm').reset();
        document.getElementById('modalYear').value = '{{ $year }}';
        document.getElementById('modalPaymentDate').value = '{{ date("Y-m-d") }}';
        document.getElementById('modalBasicSalary').value = '';
        document.getElementById('modalBonus').value = '0.00';
        document.getElementById('modalDeductions').value = '0.00';
        calcNetSalary();
        document.getElementById('salaryFormModal').style.display = 'flex';
    }

    function openEditSalaryModal(salary) {
        document.getElementById('modalSalaryTitle').innerText = payrollI18n.titleEdit;
        document.getElementById('modalTeacherId').value = salary.teacher_id;
        document.getElementById('modalYear').value = salary.year;
        document.getElementById('modalMonth').value = salary.month;
        document.getElementById('modalStatus').value = salary.status;
        document.getElementById('modalBasicSalary').value = salary.basic_salary;
        document.getElementById('modalBonus').value = salary.bonus;
        document.getElementById('modalDeductions').value = salary.deductions;
        document.getElementById('modalPaymentMethod').value = salary.payment_method || 'تحويل بنكي';
        document.getElementById('modalPaymentDate').value = salary.payment_date ? salary.payment_date.split('T')[0] : '';
        document.getElementById('modalReferenceNo').value = salary.reference_no || '';
        document.getElementById('modalNotes').value = salary.notes || '';
        calcNetSalary();
        document.getElementById('salaryFormModal').style.display = 'flex';
    }

    function closeSalaryModal() {
        document.getElementById('salaryFormModal').style.display = 'none';
    }

    function handleSaveSalary(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveSalary');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + payrollI18n.saving;

        const form = document.getElementById('salarySaveForm');
        const formData = new FormData(form);

        axios.post("{{ route('admin.teachers.salaries.store') }}", Object.fromEntries(formData))
        .then(res => {
            closeSalaryModal();
            Swal.fire({
                icon: 'success',
                title: payrollI18n.successTitle,
                text: res.data.message,
                confirmButtonColor: '#059669'
            }).then(() => location.reload());
        })
        .catch(err => {
            Swal.fire(payrollI18n.errorTitle, err.response?.data?.message || 'Error occurred', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> ' + payrollI18n.saveBtn;
        });
    }

    function deleteSalaryRecord(id) {
        Swal.fire({
            title: payrollI18n.deleteConfirmTitle,
            text: payrollI18n.deleteConfirmText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: payrollI18n.deleteConfirmBtn,
            cancelButtonText: payrollI18n.cancelBtn
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`{{ url('admin/teachers/salaries') }}/${id}`)
                .then(res => {
                    Swal.fire(payrollI18n.deletedSuccess, res.data.message, 'success').then(() => location.reload());
                })
                .catch(err => Swal.fire(payrollI18n.errorTitle, payrollI18n.deleteFailed, 'error'));
            }
        });
    }

    function openReplyClaimModal(claimId, teacherName, monthLabel, message, currentReply) {
        document.getElementById('replyClaimId').value = claimId;
        document.getElementById('replyModalTitle').innerText = '{{ __("الرد على استفسار المعلم") }}: ' + teacherName;
        document.getElementById('replyModalSubtitle').innerText = '{{ __("بخصوص مستحقات") }} ' + monthLabel;
        document.getElementById('replyOriginalMessage').innerText = message;
        document.getElementById('adminReplyText').value = currentReply || '';
        document.getElementById('replyClaimModal').style.display = 'flex';
    }

    function closeReplyClaimModal() {
        document.getElementById('replyClaimModal').style.display = 'none';
    }

    function handleReplyClaimSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('replyClaimId').value;
        const replyText = document.getElementById('adminReplyText').value.trim();
        if (!replyText) return;

        const btn = document.getElementById('btnSubmitClaimReply');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __("جاري الاعتماد والإرسال...") }}';

        axios.post(`{{ url('admin/teachers/salaries/claims') }}/${id}/reply`, {
            reply: replyText,
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            closeReplyClaimModal();
            Swal.fire({
                icon: 'success',
                title: '{{ __("تم اعتماد الرد بنجاح ✅") }}',
                text: res.data.message,
                confirmButtonColor: '#059669'
            }).then(() => location.reload());
        })
        .catch(err => {
            Swal.fire('{{ __("خطأ") }}', err.response?.data?.message || '{{ __("فشل اعتماد الرد") }}', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
</script>

<style>
    /* Claims Section Styling */
    .claims-summary-card {
        background: #ffffff;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px -3px rgba(217, 119, 6, 0.08);
        border-inline-start: 5px solid #d97706;
    }
    .claims-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding-bottom: 14px;
        border-bottom: 1px solid #fef3c7;
        margin-bottom: 16px;
    }
    .claims-header-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .claims-count-badge {
        background: #fef3c7;
        color: #b45309;
        font-size: 0.8rem;
        padding: 2px 8px;
        border-radius: 999px;
        margin-inline-start: 6px;
    }
    .badge-pending-claims {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        color: #b45309;
        padding: 5px 12px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.82rem;
    }
    .badge-all-replied {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
        padding: 5px 12px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.82rem;
    }
    .claims-grid-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 16px;
    }
    .claim-item-card {
        background: #fafafa;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        transition: all 0.2s ease;
    }
    .claim-item-card.claim-pending {
        background: #fffdfa;
        border-color: #fde68a;
    }
    .claim-item-card.claim-replied {
        background: #fcfdfc;
        border-color: #d1fae5;
    }
    .claim-item-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
    }
    .claim-teacher-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .teacher-avatar-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #eff6ff;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .claim-sub-tag {
        display: block;
        font-size: 0.76rem;
        color: #64748b;
        margin-top: 2px;
    }
    .claim-message-body {
        position: relative;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.86rem;
        color: #1e293b;
        line-height: 1.5;
    }
    .quote-icon {
        position: absolute;
        top: 6px;
        left: 8px;
        color: #e2e8f0;
        font-size: 0.9rem;
    }
    .claim-date {
        display: block;
        margin-top: 6px;
        font-size: 0.72rem;
        color: #94a3b8;
    }
    .claim-admin-reply-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.84rem;
        color: #166534;
    }
    .reply-header {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
        font-size: 0.8rem;
    }
    .reply-date {
        color: #65a30d;
        font-size: 0.72rem;
    }
    .reply-text {
        margin: 0;
        line-height: 1.5;
        white-space: pre-wrap;
    }
    .btn-reply-claim {
        background: #0284c7;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 7px 14px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: 0.15s;
    }
    .btn-reply-claim:hover {
        background: #0369a1;
    }
    .admin-payroll-wrapper {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 10px 0 60px;
        box-sizing: border-box;
        overflow-x: hidden;
    }
    .payroll-header-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px 24px;
        color: #0f172a;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
        border-inline-start: 5px solid var(--ed-primary, #1d4ed8);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .page-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .page-subtitle {
        margin: 0;
        color: #64748b;
        font-size: 0.88rem;
    }
    .btn-create-salary {
        background: var(--ed-primary, #1d4ed8);
        color: #fff;
        border: 1px solid #1e40af;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.88rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s ease;
        box-shadow: 0 1px 3px rgba(29, 78, 216, 0.2);
    }
    .btn-create-salary:hover {
        background: #1e40af;
        transform: translateY(-1px);
    }

    /* Stats Cards */
    .stats-row-clean {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .stat-card-clean {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 20px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        border-top: 3px solid var(--card-accent, #1d4ed8);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .stat-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 8px;
    }
    .stat-value-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stat-number {
        font-size: 1.45rem;
        font-weight: 800;
    }
    .stat-icon {
        font-size: 1.5rem;
        opacity: 0.85;
    }
    .text-emerald { color: #059669; }
    .text-amber { color: #d97706; }
    .text-navy { color: #1e3a8a; }
    .text-indigo { color: #4f46e5; }

    /* Filter Card */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }
    .filter-form {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }
    .filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-group label {
        font-size: 0.84rem;
        font-weight: 700;
        color: #334155;
    }
    .form-select-sm {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 0.84rem;
        color: #0f172a;
        outline: none;
    }
    .form-select-sm:focus {
        border-color: #1d4ed8;
        background: #fff;
    }
    .btn-clear-filter {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #475569;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
    }

    /* Table Container */
    .table-container-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .payroll-table-wrap {
        overflow-x: auto;
    }
    .payroll-table {
        width: 100%;
        border-collapse: collapse;
        text-align: start;
        font-size: 0.88rem;
    }
    .payroll-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.82rem;
    }
    .payroll-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #1e293b;
    }
    .payroll-table tr:hover td {
        background: #f8fafc;
    }

    /* Teacher Cell */
    .teacher-meta-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .teacher-thumb {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #cbd5e1;
    }
    .teacher-name-text {
        display: block;
        font-size: 0.88rem;
        color: #0f172a;
    }
    .teacher-subject-pill {
        display: inline-block;
        font-size: 0.72rem;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 600;
        margin-top: 2px;
    }
    .month-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        color: #334155;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 600;
    }
    .salary-breakdown-compact {
        display: flex;
        flex-direction: column;
        gap: 3px;
        font-size: 0.76rem;
    }
    .base-badge { color: #334155; font-weight: 600; }
    .bonus-badge { color: #059669; font-weight: 700; }
    .deduct-badge { color: #dc2626; font-weight: 700; }
    .text-paid { color: #059669; }
    .text-pending { color: #d97706; }

    .status-pill {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .status-paid { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .status-pending { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

    .payment-col-wrap {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .payment-method-label {
        font-size: 0.75rem;
        color: #64748b;
    }

    .actions-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .btn-action-edit, .btn-action-delete {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        cursor: pointer;
        font-size: 0.85rem;
        transition: 0.15s ease;
    }
    .btn-action-edit { color: #0284c7; }
    .btn-action-edit:hover { background: #eff6ff; border-color: #bfdbfe; }
    .btn-action-delete { color: #dc2626; }
    .btn-action-delete:hover { background: #fef2f2; border-color: #fecaca; }

    /* Mobile Cards */
    .payroll-cards-mobile {
        display: none;
    }

    @media (max-width: 900px) {
        .payroll-table-wrap { display: none; }
        .payroll-cards-mobile {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .payroll-mob-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        }
        .mob-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px;
        }
        .mob-card-body {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .mob-stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
        }
        .mob-label {
            font-weight: 700;
            color: #64748b;
            font-size: 0.8rem;
        }
        .mob-card-footer {
            display: flex;
            gap: 8px;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }
        .btn-mob-edit, .btn-mob-delete {
            flex: 1;
            padding: 8px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.82rem;
            border: 1px solid #e2e8f0;
        }
        .btn-mob-edit { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
        .btn-mob-delete { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(2px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-box {
        background: #ffffff;
        border-radius: 12px;
        max-width: 640px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }
    .btn-close-modal {
        background: transparent;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #64748b;
    }
    .modal-body-form {
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    .form-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
    }
    .form-row-full {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .form-col {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .form-label {
        font-size: 0.84rem;
        font-weight: 700;
        color: #334155;
    }
    .modal-input {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 9px 12px;
        font-size: 0.88rem;
        outline: none;
        font-family: inherit;
        box-sizing: border-box;
    }
    .modal-input:focus {
        border-color: #1d4ed8;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
    }
    .net-calc-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        padding: 12px 16px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #166534;
        font-size: 0.88rem;
        font-weight: 700;
    }
    .net-calc-box strong {
        font-size: 1.25rem;
        color: #15803d;
    }
    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn-modal-save {
        background: var(--ed-primary, #1d4ed8);
        color: #fff;
        border: 1px solid #1e40af;
        padding: 9px 20px;
        border-radius: 6px;
        font-weight: 700;
        cursor: pointer;
        font-size: 0.88rem;
    }
    .btn-modal-cancel {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #475569;
        padding: 9px 16px;
        border-radius: 6px;
        font-weight: 700;
        cursor: pointer;
        font-size: 0.88rem;
    }
</style>
@endsection
