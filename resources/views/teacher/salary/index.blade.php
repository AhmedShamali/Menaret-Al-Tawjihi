@extends('layouts.app')

@section('title', __('كشف مسير الرواتب والمستحقات المالية') . ' - ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
@php
    $teacherDisplayName = (app()->getLocale() === 'en' && !empty($teacher->name_en)) ? $teacher->name_en : $teacher->name;
    $subjectDisplayName = $teacher->subject ? ((app()->getLocale() === 'en' && !empty($teacher->subject->name_en)) ? $teacher->subject->name_en : ($teacher->subject->name_ar ?? $teacher->subject->name)) : __('كادر التدريس');
@endphp

<div class="salary-dashboard-wrapper">
    {{-- 1. رأس الصفحة الأكاديمي الفاتح --}}
    <div class="salary-header-card">
        <div class="header-main-info">
            <div class="avatar-seal-box">
                <img src="{{ $teacher->photo ? asset('storage/' . $teacher->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($teacherDisplayName) . '&background=0284c7&color=fff&size=140&bold=true' }}" alt="{{ $teacherDisplayName }}" class="teacher-avatar-img">
                <span class="role-badge-gold"><i class="fa-solid fa-chalkboard-user"></i> {{ __('كادر التدريس') }}</span>
            </div>
            <div class="teacher-details">
                <div class="badge-tag">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>{{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }} | {{ __('الإدارة المالية والأكاديمية') }}</span>
                </div>
                <h1 class="page-title">{{ __('كشف ومسير الرواتب والمستحقات المالية') }}</h1>
                <p class="teacher-subtitle">
                    {{ __('المعلم الفاضل:') }} <strong>{{ $teacherDisplayName }}</strong>
                    @if($teacher->subject)
                        | {{ __('مادة:') }} <span class="text-primary">{{ $subjectDisplayName }}</span>
                    @endif
                    | {{ __('العام المالي:') }} <span class="text-emerald font-mono font-bold">{{ $year }}</span>
                </p>
            </div>
        </div>

        <div class="header-actions">
            <form method="GET" action="{{ route('teacher.salaries.index') }}" class="year-select-form">
                <label for="yearSelect" class="select-label"><i class="fa-regular fa-calendar"></i> {{ __('اختر السنة:') }}</label>
                <select name="year" id="yearSelect" class="year-dropdown font-mono" onchange="this.form.submit()">
                    @for($y = date('Y') + 1; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }} {{ app()->getLocale() === 'ar' ? 'م' : 'AD' }}</option>
                    @endfor
                </select>
            </form>
            <button type="button" onclick="window.print()" class="btn-print-page" title="{{ __('طباعة الكشف السنوي') }}">
                <i class="fa-solid fa-print"></i> {{ __('طباعة الكشف السنوي') }}
            </button>
        </div>
    </div>

    {{-- 2. بطاقات المؤشرات المالية الكلاسيكية الفاتحة --}}
    <div class="stats-row-clean">
        <div class="stat-card-clean" style="--card-accent: #059669;">
            <span class="stat-label">{{ __('إجمالي المستلم لعام :year', ['year' => $year]) }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-emerald font-mono">{{ number_format($totalPaid, 2) }} ₪</span>
                <i class="fa-solid fa-hand-holding-dollar stat-icon text-emerald"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">{{ __('مستحقات تم إيداعها وتأكيدها') }}</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #1e3a8a;">
            <span class="stat-label">{{ __('إجمالي المكافآت والحوافز') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-navy font-mono">{{ number_format($totalBonus, 2) }} ₪</span>
                <i class="fa-solid fa-gift stat-icon text-navy"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">{{ __('تقدير إنجازاتك وجهودك') }}</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #d97706;">
            <span class="stat-label">{{ __('مستحقات قيد الاعتماد') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-amber font-mono">{{ number_format($pendingAmount, 2) }} ₪</span>
                <i class="fa-solid fa-clock-rotate-left stat-icon text-amber"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">{{ __('جاري الصرف من الإدارة') }}</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #6366f1;">
            <span class="stat-label">{{ __('الأشهر المصروفة') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-indigo font-mono">{{ $paidMonthsCount }} <small style="font-size: 0.85rem; color: #64748b;">/ 12</small></span>
                <i class="fa-solid fa-calendar-check stat-icon text-indigo"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">{{ __('نسبة الالتزام والتسديد') }}</small>
        </div>
    </div>

    {{-- 3. جدول مسير الرواتب لجميع أشهر السنة (12 شهراً) --}}
    <div class="salary-table-card">
        <div class="table-header-row">
            <div>
                <h2 class="table-title"><i class="fa-solid fa-file-invoice-dollar text-primary"></i> {{ __('كشف مسير رواتب الشهور (1 - 12) لعام :year', ['year' => $year]) }}</h2>
                <p class="table-subtitle">{{ __('يتم تحديث وإصدار الرواتب شهرياً بواسطة الإدارة المالية لمنصة منارة التوجيهي') }}</p>
            </div>
            <div class="table-header-badge">
                <i class="fa-solid fa-shield-halved text-emerald"></i>
                <span>{{ __('كشف مالي رسمي معتمد وموثق') }}</span>
            </div>
        </div>

        <div class="table-responsive-box">
            <table class="salary-luxury-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th>{{ __('الشهر') }}</th>
                        <th>{{ __('الراتب الأساسي') }}</th>
                        <th>{{ __('المكافآت والحوافز') }}</th>
                        <th>{{ __('الخصومات والاستقطاع') }}</th>
                        <th>{{ __('صافي الراتب المستحق') }}</th>
                        <th>{{ __('طريقة وتاريخ الصرف') }}</th>
                        <th>{{ __('حالة الصرف') }}</th>
                        <th style="text-align: center; width: 170px;">{{ __('قسيمة الراتب والإجراء') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthsNames as $monthNum => $monthLabel)
                        @php
                            $sal = $salaries[$monthNum] ?? null;
                            $isPaid = $sal && $sal->status === 'paid';
                            $isPending = $sal && $sal->status === 'pending';
                        @endphp
                        <tr class="{{ $isPaid ? 'row-paid' : ($isPending ? 'row-pending' : 'row-unissued') }}">
                            <td class="col-num font-mono">{{ sprintf('%02d', $monthNum) }}</td>
                            <td class="col-month">
                                <div class="month-title-wrap">
                                    <span class="month-bullet {{ $isPaid ? 'bullet-paid' : ($isPending ? 'bullet-pending' : 'bullet-gray') }}"></span>
                                    <strong>{{ $monthLabel }}</strong>
                                </div>
                            </td>
                            <td class="font-mono">{{ $sal ? number_format($sal->basic_salary, 2) . ' ₪' : '-' }}</td>
                            <td class="font-mono text-emerald">
                                @if($sal && $sal->bonus > 0)
                                    +{{ number_format($sal->bonus, 2) }} ₪
                                @else
                                    <span class="text-gray-muted">-</span>
                                @endif
                            </td>
                            <td class="font-mono text-rose">
                                @if($sal && $sal->deductions > 0)
                                    -{{ number_format($sal->deductions, 2) }} ₪
                                @else
                                    <span class="text-gray-muted">-</span>
                                @endif
                            </td>
                            <td class="col-net font-mono">
                                @if($sal)
                                    <strong class="net-value {{ $isPaid ? 'text-paid' : 'text-pending' }}">
                                        {{ number_format($sal->net_salary, 2) }} ₪
                                    </strong>
                                @else
                                    <span class="text-gray-muted">{{ __('غير مدخل') }}</span>
                                @endif
                            </td>
                            <td class="col-method">
                                @if($sal && $isPaid)
                                    <div class="payment-meta">
                                        <span><i class="fa-solid fa-money-check-dollar"></i> {{ $sal->payment_method ? __($sal->payment_method) : __('تحويل بنكي') }}</span>
                                        @if($sal->payment_date)
                                            <small class="font-mono">{{ $sal->payment_date->format('Y-m-d') }}</small>
                                        @endif
                                    </div>
                                @elseif($sal && $isPending)
                                    <span class="text-amber text-xs"><i class="fa-solid fa-clock"></i> {{ __('في مرحلة الإعداد المالي') }}</span>
                                @else
                                    <span class="text-gray-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($isPaid)
                                    <span class="status-pill status-paid">
                                        <i class="fa-solid fa-circle-check"></i> {{ __('تم الصرف والاستلام') }}
                                    </span>
                                @elseif($isPending)
                                    <span class="status-pill status-pending">
                                        <i class="fa-solid fa-clock-rotate-left"></i> {{ __('قيد الاعتماد والصرف') }}
                                    </span>
                                @else
                                    <span class="status-pill status-unissued">
                                        <i class="fa-regular fa-circle"></i> {{ __('بانتظار إعداد الشهر') }}
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="row-actions-group">
                                    @if($sal)
                                        <button type="button" 
                                                class="btn-table-action btn-payslip" 
                                                onclick="openPayslipModal({{ json_encode($sal) }}, '{{ addslashes($monthLabel) }}')" 
                                                title="{{ __('قسيمة الراتب') }}">
                                            <i class="fa-solid fa-receipt"></i> {{ __('قسيمة الراتب') }}
                                        </button>
                                    @else
                                        <button type="button" 
                                                class="btn-table-action btn-claim" 
                                                onclick="openClaimModal({{ $monthNum }}, '{{ addslashes($monthLabel) }}')" 
                                                title="{{ __('استفسار مالي') }}">
                                            <i class="fa-regular fa-comment-dots"></i> {{ __('استفسار مالي') }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- عرض بطاقات الشهور للجوال والتابلت بدون سكرول نهائياً --}}
        <div class="teacher-salary-mobile-cards">
            @foreach($monthsNames as $monthNum => $monthLabel)
                @php
                    $sal = $salaries[$monthNum] ?? null;
                    $isPaid = $sal && $sal->status === 'paid';
                    $isPending = $sal && $sal->status === 'pending';
                @endphp
                <div class="teacher-mob-month-card {{ $isPaid ? 'card-paid' : ($isPending ? 'card-pending' : 'card-unissued') }}">
                    <div class="mob-m-header">
                        <div class="mob-m-title">
                            <span class="mob-m-num font-mono">{{ sprintf('%02d', $monthNum) }}</span>
                            <strong>{{ $monthLabel }}</strong>
                        </div>
                        @if($isPaid)
                            <span class="status-pill status-paid"><i class="fa-solid fa-circle-check"></i> {{ __('تم الصرف') }}</span>
                        @elseif($isPending)
                            <span class="status-pill status-pending"><i class="fa-solid fa-clock-rotate-left"></i> {{ __('قيد الاعتماد') }}</span>
                        @else
                            <span class="status-pill status-unissued"><i class="fa-regular fa-circle"></i> {{ __('بانتظار الإعداد') }}</span>
                        @endif
                    </div>

                    <div class="mob-m-body">
                        <div class="mob-net-box">
                            <span class="mob-net-label">{{ __('صافي الراتب المستحق:') }}</span>
                            @if($sal)
                                <strong class="mob-net-val font-mono {{ $isPaid ? 'text-paid' : 'text-pending' }}">
                                    {{ number_format($sal->net_salary, 2) }} ₪
                                </strong>
                            @else
                                <span class="text-gray-muted font-bold">-</span>
                            @endif
                        </div>

                        @if($sal)
                            <div class="mob-salary-pills">
                                <span class="m-pill-item">{{ __('الأساسي:') }} <strong class="font-mono">{{ number_format($sal->basic_salary, 0) }} ₪</strong></span>
                                @if($sal->bonus > 0)
                                    <span class="m-pill-item text-emerald">+{{ __('مكافأة:') }} <strong class="font-mono">{{ number_format($sal->bonus, 0) }} ₪</strong></span>
                                @endif
                                @if($sal->deductions > 0)
                                    <span class="m-pill-item text-rose">-{{ __('خصم:') }} <strong class="font-mono">{{ number_format($sal->deductions, 0) }} ₪</strong></span>
                                @endif
                            </div>
                            @if($sal->payment_method || $sal->payment_date)
                                <div class="mob-payment-note">
                                    <i class="fa-solid fa-money-check-dollar"></i>
                                    <span>{{ $sal->payment_method ? __($sal->payment_method) : __('تحويل بنكي') }} {{ $sal->payment_date ? '(' . $sal->payment_date->format('Y-m-d') . ')' : '' }}</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="mob-m-footer">
                        @if($sal)
                            <button type="button" class="btn-table-action btn-payslip w-full" onclick="openPayslipModal({{ json_encode($sal) }}, '{{ addslashes($monthLabel) }}')">
                                <i class="fa-solid fa-receipt"></i> {{ __('قسيمة الراتب الرسمية') }}
                            </button>
                        @else
                            <button type="button" class="btn-table-action btn-claim w-full" onclick="openClaimModal({{ $monthNum }}, '{{ addslashes($monthLabel) }}')">
                                <i class="fa-regular fa-comment-dots"></i> {{ __('إرسال استفسار مالي') }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- 4. مودال سند صرف مستحقات ورواتب المعلمين الطبيعي الكلاسيكي (قابل للطباعة الفورية في ورقة A4 واحدة) --}}
<div id="payslipModal" class="payslip-modal-overlay" style="display: none;">
    <div class="payslip-modal-container" id="printablePayslip">
        
        <!-- ورقة السند الكلاسيكية المدرسية -->
        <div class="voucher-double-border">

            <!-- 1. الترويسة الوزارية والمدرسية الرسمية -->
            <header class="voucher-gov-header">
                <div class="gov-header-col right-col">
                    <div class="gov-text-line"><strong>دولة فلسطين</strong></div>
                    <div class="gov-text-line">وزارة التربية والتعليم العالي</div>
                    <div class="gov-text-line">منصة منارة التوجيهي للثانوية العامة</div>
                    <div class="gov-text-sub">الإدارة المالية • شؤون الكادر التعليمي</div>
                </div>

                <div class="gov-header-col center-col">
                    <div class="voucher-official-emblem">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h2 class="voucher-headline">سَنَدُ صَرْفٍ مَالِيّ</h2>
                    <span class="voucher-headline-en">TEACHER SALARY DISBURSEMENT VOUCHER</span>
                    <div class="voucher-serial-tag">
                        <span>رقم السند:</span>
                        <strong class="font-mono" id="slipSerialNo">SLIP-{{ $year }}-0000</strong>
                    </div>
                </div>

                <div class="gov-header-col left-col">
                    <table class="voucher-meta-mini-table">
                        <tr>
                            <td class="lbl">{{ __('تاريخ الصرف:') }}</td>
                            <td class="val font-mono" id="slipPaymentDate">-</td>
                        </tr>
                        <tr>
                            <td class="lbl">{{ __('العام المالي:') }}</td>
                            <td class="val font-mono">{{ $year }} م</td>
                        </tr>
                        <tr>
                            <td class="lbl">{{ __('حالة السند:') }}</td>
                            <td class="val">
                                <span class="state-badge-paid"><i class="fa-solid fa-check"></i> {{ __('مصروف ومسدد') }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </header>

            <div class="voucher-hairline"></div>

            <!-- 2. بيان الإقرار المالي الكلاسيكي لسند الصرف -->
            <div class="voucher-statement-block">
                <div class="statement-row">
                    <div class="statement-field full-width">
                        <span class="field-label">صرفنـا للأستاذ/ـة المكرم/ـة:</span>
                        <span class="field-content student-name-highlight">{{ $teacherDisplayName }}</span>
                        <span class="field-label-inline">المبحث التدريسي:</span>
                        <span class="field-content">{{ $subjectDisplayName }}</span>
                        <span class="field-label-inline">عن مستحقات شهر:</span>
                        <span class="field-content text-primary font-bold" id="slipMonthYear">-</span>
                    </div>
                </div>

                <div class="statement-row">
                    <div class="statement-field flex-2">
                        <span class="field-label">مبلغاً وقدره (صافي الصرف):</span>
                        <span class="field-content font-mono bold-currency" id="slipNetSalary">0.00 ₪</span>
                        <span class="field-sub">(شيكل فلسطيني جديد)</span>
                    </div>
                    <div class="statement-field flex-3">
                        <span class="field-label">فقط وقدره تفقيطاً:</span>
                        <span class="field-content words-content" id="slipAmountInWords">-</span>
                    </div>
                </div>

                <div class="statement-row">
                    <div class="statement-field flex-1">
                        <span class="field-label">طريقة الصرف / التحويل:</span>
                        <span class="field-content" id="slipPaymentMethod">-</span>
                    </div>
                    <div class="statement-field flex-1">
                        <span class="field-label">رقم الحوالة / المرجع:</span>
                        <span class="field-content font-mono" id="slipRefNo">-</span>
                    </div>
                    <div class="statement-field flex-1">
                        <span class="field-label">ملاحظات الإدارة:</span>
                        <span class="field-content" id="slipNotes">{{ __('تم الصرف والتحويل') }}</span>
                    </div>
                </div>
            </div>

            <!-- 3. جدول تفاصيل الاستحقاقات والاستقطاعات المعتمد (Classical Natural Table) -->
            <div class="voucher-table-wrapper">
                <table class="voucher-natural-table">
                    <thead>
                        <tr>
                            <th colspan="2" style="width: 50%; text-align: center; background: #ecfdf5; color: #065f46;">
                                <i class="fa-solid fa-circle-plus"></i> {{ __('كشف الاستحقاقات والبدلات (Earnings)') }}
                            </th>
                            <th colspan="2" style="width: 50%; text-align: center; background: #fff1f2; color: #9f1239;">
                                <i class="fa-solid fa-circle-minus"></i> {{ __('كشف الخصومات والاستقطاع (Deductions)') }}
                            </th>
                        </tr>
                        <tr>
                            <th style="text-align: right;">{{ __('بيان البند') }}</th>
                            <th style="width: 100px; text-align: center;">{{ __('المبلغ (ILS)') }}</th>
                            <th style="text-align: right;">{{ __('بيان البند') }}</th>
                            <th style="width: 100px; text-align: center;">{{ __('المبلغ (ILS)') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ __('الراتب الأساسي الشهري المعتمد') }}</td>
                            <td class="text-center font-mono bold-text" id="slipBasicSalary">0.00 ₪</td>
                            <td>{{ __('استقطاع غياب أو تأخير إداري') }}</td>
                            <td class="text-center font-mono text-rose" id="slipDeductions">0.00 ₪</td>
                        </tr>
                        <tr>
                            <td>{{ __('مكافآت التميز وحوافز العطاء') }}</td>
                            <td class="text-center font-mono text-emerald" id="slipBonus">+0.00 ₪</td>
                            <td>{{ __('سلفيات أو أقساط مستردة مسبقة') }}</td>
                            <td class="text-center font-mono text-rose">0.00 ₪</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="voucher-total-summary-row">
                            <td class="total-label-cell"><strong>{{ __('إجمالي الاستحقاقات:') }}</strong></td>
                            <td class="text-center font-mono font-bold text-emerald" id="slipTotalEarnings">0.00 ₪</td>
                            <td class="total-label-cell"><strong>{{ __('إجمالي الخصومات:') }}</strong></td>
                            <td class="text-center font-mono font-bold text-rose" id="slipTotalDeductions">0.00 ₪</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- 4. صندوق صافي الراتب وإقرار الاستلام -->
            <div class="voucher-clearance-box clearance-paid">
                <div class="clearance-icon">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div class="clearance-text">
                    <strong>{{ __('إقرار استلام وإبراء ذمة:') }}</strong>
                    <span>{{ __('أقر أنا المعلم المكرم الموقع أدناه باستلامي كامل مستحقاتي ورواتبي المبينة أعلاه عن هذا الشهر دون أي قيد أو شرط.') }}</span>
                </div>
                <div class="clearance-remaining">
                    <span class="rem-lbl">{{ __('صافي المبلغ المقبوض:') }}</span>
                    <strong class="rem-val font-mono" id="slipNetSalaryBanner">0.00 ₪</strong>
                </div>
            </div>

            <!-- 5. الأختام والتواقيع الرسمية الثلاثية -->
            <footer class="voucher-signatures-section">
                <!-- 1. توقيع المعلم المستلم -->
                <div class="sig-column">
                    <div class="sig-header">{{ __('المعلم المستلم (المستفيد)') }}</div>
                    <div class="sig-space">
                        <div class="sig-handwritten-line">........................................</div>
                    </div>
                    <div class="sig-name">{{ $teacherDisplayName }}</div>
                </div>

                <!-- 2. خاتم المنصة والاعتماد المالي الرسمي -->
                <div class="sig-column stamp-center-col">
                    <div class="authentic-school-stamp">
                        <div class="stamp-outer-circle">
                            <div class="stamp-middle-circle">
                                <div class="stamp-text-arc-top">منارة التوجيهي • بوابة الثانوية العامة</div>
                                <div class="stamp-center-content">
                                    <i class="fa-solid fa-stamp stamp-inner-icon"></i>
                                    <div class="stamp-state-txt">معتمد ومصروف</div>
                                    <div class="stamp-gov-txt">دولة فلسطين</div>
                                </div>
                                <div class="stamp-text-arc-bottom">الدائرة المالية • {{ $year }} م</div>
                            </div>
                        </div>
                    </div>
                    <div class="stamp-caption">{{ __('خاتم الصرف والاعتماد المالي الرسمي') }}</div>
                </div>

                <!-- 3. المشرف العام وإدارة المنصة -->
                <div class="sig-column">
                    <div class="sig-header">{{ __('المشرف العام والمالي') }}</div>
                    <div class="sig-space">
                        <span class="official-signature-facsimile">م.أحمد شمالي</span>
                        <div class="sig-handwritten-line">........................................</div>
                    </div>
                    <div class="sig-name">{{ __('م.أحمد شمالي') }}</div>
                </div>
            </footer>

            <!-- شريط الملاحظة القانونية -->
            <div class="voucher-legal-footer">
                <span>{{ __('ملاحظة: هذا السند وثيقة مالية رسمية صادرة إلكترونياً عن منصة منارة التوجيهي وموثقة بالسجلات المصرفية. يعتبر السند لاغياً في حال الكشط أو التعديل اليدوي.') }}</span>
                <span class="footer-ref font-mono">{{ date('Y-m-d') }} • فلسطين</span>
            </div>

        </div>

        <!-- أزرار التحكم بالمودال (تختفي في الطباعة) -->
        <div class="modal-footer-actions no-print">
            <button type="button" class="btn-modal-print" onclick="window.print()">
                <i class="fa-solid fa-print"></i> {{ __('طباعة السند المدرسي الرسمي (ورقة A4)') }}
            </button>
            <button type="button" class="btn-modal-close" onclick="closePayslipModal()">
                {{ __('إغلاق النافذة') }}
            </button>
        </div>
    </div>
</div>

{{-- 5. مودال إرسال استفسار أو ملاحظة مالية للإدارة --}}
<div id="claimModal" class="payslip-modal-overlay" style="display: none;">
    <div class="claim-modal-container">
        <div class="claim-header">
            <i class="fa-solid fa-comments-dollar text-primary" style="font-size: 1.8rem;"></i>
            <div>
                <h3>{{ __('إرسال استفسار مالي للإدارة') }}</h3>
                <p id="claimMonthTitle">{{ __('بخصوص مستحقات وراتب شهر محدد') }}</p>
            </div>
        </div>

        <form id="claimForm" onsubmit="submitClaimForm(event)">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="month" id="claimMonthInput">

            <div class="form-group-block">
                <label class="input-label">{{ __('تفاصيل الاستفسار أو الملاحظة') }} <span class="required" style="color: #dc2626;">*</span></label>
                <textarea name="message" id="claimMessageInput" rows="4" class="claim-textarea" placeholder="{{ __('اكتب استفسارك أو ملاحظتك للإدارة بخصوص هذا الشهر...') }}" required></textarea>
            </div>

            <div class="claim-actions-row">
                <button type="submit" class="btn-submit-claim" id="claimSubmitBtn">
                    <i class="fa-solid fa-paper-plane"></i> {{ __('إرسال للإدارة العامة') }}
                </button>
                <button type="button" class="btn-cancel-claim" onclick="closeClaimModal()">
                    {{ __('إلغاء') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const teacherSalaryI18n = {
        adSuffix: "{{ app()->getLocale() === 'ar' ? 'م' : 'AD' }}",
        defaultMethod: "{{ __('تحويل بنكي / محفظة إلكترونية') }}",
        defaultNotes: "{{ __('تم اعتماد وصرف الراتب كاملاً وفقاً للائحة منصة منارة التوجيهي.') }}",
        claimTitlePrefix: "{{ __('بخصوص راتب ومستحقات') }}",
        sending: "{{ __('جاري الإرسال...') }}",
        claimSuccessTitle: "{{ __('تم إرسال استفسارك بنجاح') }}",
        errorTitle: "{{ __('خطأ') }}",
        errorClaimFailed: "{{ __('فشل إرسال الملاحظة') }}"
    };

    function tafqeetArabic(amount) {
        const ones = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة', 'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];
        const tens = ['', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
        const hundreds = ['', 'مائة', 'مائتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];

        function convert(num) {
            if (num === 0) return '';
            if (num < 20) return ones[num];
            if (num < 100) {
                const t = Math.floor(num / 10), r = num % 10;
                return r === 0 ? tens[t] : ones[r] + ' و ' + tens[t];
            }
            if (num < 1000) {
                const h = Math.floor(num / 100), r = num % 100;
                return r === 0 ? hundreds[h] : hundreds[h] + ' و ' + convert(r);
            }
            if (num < 1000000) {
                const th = Math.floor(num / 1000), r = num % 1000;
                let thTxt = (th === 1) ? 'ألف' : (th === 2 ? 'ألفان' : (th >= 3 && th <= 10 ? convert(th) + ' آلاف' : convert(th) + ' ألفاً'));
                return r === 0 ? thTxt : thTxt + ' و ' + convert(r);
            }
            return num.toString();
        }

        const intPart = Math.floor(amount);
        if (intPart <= 0) return 'صفر شيكل لا غير';
        const txt = convert(intPart);
        const curr = (intPart >= 3 && intPart <= 10) ? 'شواكل' : (intPart >= 11 ? 'شيكلاً' : 'شيكل');
        return 'فقط ' + txt + ' ' + curr + ' لا غير';
    }

    function openPayslipModal(salary, monthLabel) {
        document.getElementById('slipSerialNo').innerText = `SLIP-{{ $year }}-${String(salary.month).padStart(2, '0')}-${salary.id}`;
        document.getElementById('slipMonthYear').innerText = `${monthLabel} {{ $year }} ${teacherSalaryI18n.adSuffix}`;
        document.getElementById('slipPaymentDate').innerText = salary.payment_date || '{{ date("Y-m-d") }}';
        document.getElementById('slipPaymentMethod').innerText = salary.payment_method ? salary.payment_method : teacherSalaryI18n.defaultMethod;
        document.getElementById('slipRefNo').innerText = salary.reference_no || `TRX-${salary.id}8472`;
        
        const basic = parseFloat(salary.basic_salary || 0);
        const bonus = parseFloat(salary.bonus || 0);
        const deductions = parseFloat(salary.deductions || 0);
        const net = parseFloat(salary.net_salary || 0);

        document.getElementById('slipBasicSalary').innerText = `${basic.toFixed(2)} ₪`;
        document.getElementById('slipBonus').innerText = `+${bonus.toFixed(2)} ₪`;
        document.getElementById('slipTotalEarnings').innerText = `${(basic + bonus).toFixed(2)} ₪`;
        
        document.getElementById('slipDeductions').innerText = `-${deductions.toFixed(2)} ₪`;
        document.getElementById('slipTotalDeductions').innerText = `-${deductions.toFixed(2)} ₪`;
        
        document.getElementById('slipNetSalary').innerText = `${net.toFixed(2)} ₪`;
        document.getElementById('slipNetSalaryBanner').innerText = `${net.toFixed(2)} ₪`;
        document.getElementById('slipAmountInWords').innerText = tafqeetArabic(net);
        document.getElementById('slipNotes').innerText = salary.notes || teacherSalaryI18n.defaultNotes;

        document.getElementById('payslipModal').style.display = 'flex';
    }

    function closePayslipModal() {
        document.getElementById('payslipModal').style.display = 'none';
    }

    function openClaimModal(monthNum, monthLabel) {
        document.getElementById('claimMonthInput').value = monthNum;
        document.getElementById('claimMonthTitle').innerText = `${teacherSalaryI18n.claimTitlePrefix} (${monthLabel} {{ $year }})`;
        document.getElementById('claimMessageInput').value = '';
        document.getElementById('claimModal').style.display = 'flex';
    }

    function closeClaimModal() {
        document.getElementById('claimModal').style.display = 'none';
    }

    function submitClaimForm(e) {
        e.preventDefault();
        const btn = document.getElementById('claimSubmitBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + teacherSalaryI18n.sending;

        const form = document.getElementById('claimForm');
        const formData = new FormData(form);

        axios.post("{{ route('teacher.salaries.claim') }}", {
            year: formData.get('year'),
            month: formData.get('month'),
            message: formData.get('message')
        })
        .then(res => {
            closeClaimModal();
            Swal.fire({
                icon: 'success',
                title: teacherSalaryI18n.claimSuccessTitle,
                text: res.data.message,
                confirmButtonColor: '#059669'
            });
        })
        .catch(err => {
            Swal.fire(teacherSalaryI18n.errorTitle, err.response?.data?.message || teacherSalaryI18n.errorClaimFailed, 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    window.onclick = function(e) {
        const payslip = document.getElementById('payslipModal');
        const claim = document.getElementById('claimModal');
        if (e.target === payslip) closePayslipModal();
        if (e.target === claim) closeClaimModal();
    }
</script>

<style>
    .salary-dashboard-wrapper {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 10px 0 60px;
        font-family: inherit;
        box-sizing: border-box;
        overflow-x: hidden;
    }

    /* 1. Header Card - Clean Light Academic Style */
    .salary-header-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        color: #0f172a;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
        border-inline-start: 5px solid var(--ed-primary, #1d4ed8);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .header-main-info {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
    }
    .avatar-seal-box {
        position: relative;
    }
    .teacher-avatar-img {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #cbd5e1;
    }
    .role-badge-gold {
        position: absolute;
        bottom: -4px;
        left: 50%;
        transform: translateX(-50%);
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
        white-space: nowrap;
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
        font-size: 1.4rem;
        font-weight: 800;
        margin: 0 0 4px;
        color: #0f172a;
    }
    .teacher-subtitle {
        margin: 0;
        color: #64748b;
        font-size: 0.88rem;
    }
    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .year-select-form {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .select-label {
        font-size: 0.84rem;
        font-weight: 700;
        color: #475569;
    }
    .year-dropdown {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 7px 12px;
        font-size: 0.85rem;
        color: #0f172a;
        font-weight: 700;
        outline: none;
    }
    .btn-print-page {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #334155;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.84rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: 0.15s ease;
    }
    .btn-print-page:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
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
    .text-navy { color: #1e3a8a; }
    .text-amber { color: #d97706; }
    .text-indigo { color: #4f46e5; }
    .text-rose { color: #dc2626; }

    /* Table Card */
    .salary-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .table-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 18px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-title {
        font-size: 1.15rem;
        font-weight: 800;
        margin: 0 0 4px;
        color: #0f172a;
    }
    .table-subtitle {
        margin: 0;
        font-size: 0.82rem;
        color: #64748b;
    }
    .table-header-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .table-responsive-box {
        overflow-x: auto;
    }
    .salary-luxury-table {
        width: 100%;
        border-collapse: collapse;
        text-align: start;
        font-size: 0.88rem;
    }
    .salary-luxury-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.82rem;
        white-space: nowrap;
    }
    .salary-luxury-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        vertical-align: middle;
    }
    .salary-luxury-table tr:hover td {
        background: #f8fafc;
    }

    .month-title-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .month-bullet {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .bullet-paid { background: #059669; }
    .bullet-pending { background: #d97706; }
    .bullet-gray { background: #cbd5e1; }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .status-paid { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .status-unissued { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

    .btn-table-action {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: 0.15s ease;
        border: 1px solid transparent;
    }
    .btn-payslip {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }
    .btn-payslip:hover {
        background: #1d4ed8;
        color: white;
    }
    .btn-claim {
        background: #f8fafc;
        color: #475569;
        border-color: #cbd5e1;
    }
    .btn-claim:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Mobile cards */
    .teacher-salary-mobile-cards { display: none; }
    @media (max-width: 900px) {
        .table-responsive-box { display: none; }
        .teacher-salary-mobile-cards {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .teacher-mob-month-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .mob-m-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px;
        }
        .mob-m-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }
        .mob-m-num {
            background: #f1f5f9;
            color: #334155;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.78rem;
            font-weight: 700;
        }
        .mob-net-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 6px;
        }
        .mob-net-label { font-size: 0.82rem; color: #64748b; font-weight: 700; }
        .mob-net-val { font-size: 1.1rem; }
        .mob-salary-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
            font-size: 0.76rem;
        }
        .m-pill-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 2px 8px;
            border-radius: 4px;
            color: #475569;
        }
        .mob-payment-note {
            font-size: 0.76rem;
            color: #64748b;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .mob-m-footer {
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }
        .w-full { width: 100%; justify-content: center; }
    }

    /* Payslip Modal */
    .payslip-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(2px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .payslip-modal-container {
        background: #ffffff;
        border-radius: 12px;
        max-width: 680px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }
    .payslip-inner-card {
        padding: 24px;
    }
    .payslip-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }
    .payslip-branding {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .brand-logo-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #eff6ff;
        color: #1d4ed8;
        display: grid;
        place-items: center;
        font-size: 1.3rem;
        border: 1px solid #bfdbfe;
    }
    .brand-title {
        font-size: 1.15rem;
        font-weight: 800;
        margin: 0 0 2px;
        color: #0f172a;
    }
    .brand-sub {
        font-size: 0.75rem;
        color: #64748b;
    }
    .payslip-badge-box {
        text-align: end;
    }
    .payslip-type {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e40af;
    }
    .payslip-serial {
        font-size: 0.78rem;
        color: #64748b;
    }
    .payslip-divider {
        height: 2px;
        background: #e2e8f0;
        margin: 16px 0;
    }
    .payslip-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px;
        margin-bottom: 16px;
    }
    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .meta-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 700;
    }
    .meta-value {
        font-size: 0.88rem;
        color: #0f172a;
    }
    .breakdown-tables-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 16px;
    }
    .breakdown-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
    }
    .breakdown-title {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 10px;
        padding-bottom: 6px;
        border-bottom: 1px solid #f1f5f9;
    }
    .breakdown-table {
        width: 100%;
        font-size: 0.82rem;
    }
    .breakdown-table td {
        padding: 4px 0;
    }
    .breakdown-table td:last-child {
        text-align: end;
    }
    .total-row td {
        border-top: 1px solid #e2e8f0;
        padding-top: 8px;
    }
    .net-salary-banner {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }
    .net-title {
        display: block;
        font-size: 0.88rem;
        font-weight: 700;
        color: #166534;
    }
    .net-sub {
        display: block;
        font-size: 0.76rem;
        color: #4ade80;
        margin-top: 2px;
    }
    .net-banner-amount {
        font-size: 1.45rem;
        font-weight: 900;
        color: #15803d;
    }
    .payslip-signatures-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-top: 10px;
    }
    .sig-box {
        font-size: 0.78rem;
        color: #475569;
    }
    .sig-line {
        margin-top: 6px;
        font-weight: 700;
        color: #0f172a;
    }
    .stamp-seal-box {
        text-align: center;
    }
    .official-seal {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        border: 2px dashed #059669;
        border-radius: 50%;
        width: 76px;
        height: 76px;
        justify-content: center;
        color: #059669;
        font-size: 0.65rem;
        font-weight: 700;
    }
    .official-seal i { font-size: 1.1rem; margin-bottom: 2px; }
    .modal-footer-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #e2e8f0;
        padding-top: 16px;
    }
    .btn-modal-print {
        background: var(--ed-primary, #1d4ed8);
        color: white;
        border: none;
        padding: 8px 18px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.84rem;
        cursor: pointer;
    }
    .btn-modal-close {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #475569;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.84rem;
        cursor: pointer;
    }

    /* Claim Modal */
    .claim-modal-container {
        background: #ffffff;
        border-radius: 12px;
        max-width: 520px;
        width: 100%;
        padding: 24px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }
    .claim-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e2e8f0;
    }
    .claim-header h3 {
        margin: 0 0 2px;
        font-size: 1.15rem;
        color: #0f172a;
        font-weight: 800;
    }
    .claim-header p {
        margin: 0;
        font-size: 0.82rem;
        color: #64748b;
    }
    .input-label {
        font-size: 0.84rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    .claim-textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 0.86rem;
        font-family: inherit;
        outline: none;
        box-sizing: border-box;
    }
    .claim-textarea:focus {
        border-color: #1d4ed8;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
    }
    .claim-actions-row {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 16px;
    }
    .btn-submit-claim {
        background: var(--ed-primary, #1d4ed8);
        color: white;
        border: none;
        padding: 8px 18px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.84rem;
        cursor: pointer;
    }
    .btn-cancel-claim {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #475569;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.84rem;
        cursor: pointer;
    }

    /* ==========================================================================
       أنماط سند الصرف المدرسي الكلاسيكي للمعلمين (Teacher Salary Voucher)
       ========================================================================== */
    .payslip-modal-container {
        max-width: 860px;
        width: 100%;
        background: #ffffff;
        border-radius: 6px;
        padding: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        box-sizing: border-box;
    }

    .voucher-double-border {
        border: 2px solid #0f172a;
        outline: 1px solid #0f172a;
        outline-offset: -5px;
        padding: 20px 22px 14px;
        box-sizing: border-box;
        background: #ffffff;
    }

    .voucher-gov-header {
        display: grid;
        grid-template-columns: 1.2fr 1.4fr 1fr;
        align-items: center;
        gap: 12px;
        padding-bottom: 12px;
    }
    .gov-header-col.right-col {
        text-align: right;
        font-size: 0.82rem;
        line-height: 1.45;
        color: #1e293b;
    }
    .gov-text-line strong { font-size: 0.96rem; color: #0f172a; }
    .gov-text-sub { font-size: 0.76rem; color: #64748b; margin-top: 2px; }

    .gov-header-col.center-col { text-align: center; }
    .voucher-official-emblem {
        width: 38px;
        height: 38px;
        margin: 0 auto 4px;
        border-radius: 50%;
        border: 1.5px solid #1e3a8a;
        color: #1e3a8a;
        display: grid;
        place-items: center;
        font-size: 1.15rem;
    }
    .voucher-headline {
        font-size: 1.55rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
        letter-spacing: 0.5px;
        font-family: 'Amiri', 'Traditional Arabic', serif;
    }
    .voucher-headline-en {
        display: block;
        font-size: 0.65rem;
        font-weight: 700;
        color: #475569;
        letter-spacing: 1.5px;
        margin-top: 1px;
    }
    .voucher-serial-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 0.76rem;
        margin-top: 4px;
        color: #1e3a8a;
    }

    .gov-header-col.left-col {
        text-align: left;
        display: flex;
        justify-content: flex-end;
    }
    .voucher-meta-mini-table { font-size: 0.76rem; border-collapse: collapse; }
    .voucher-meta-mini-table td { padding: 2px 6px; }
    .voucher-meta-mini-table .lbl { color: #475569; font-weight: 600; text-align: right; }
    .voucher-meta-mini-table .val { font-weight: 700; color: #0f172a; text-align: left; }
    .state-badge-paid { color: #15803d; font-weight: 800; }

    .voucher-hairline {
        height: 1.5px;
        background: #0f172a;
        margin: 6px 0 14px;
    }

    .voucher-statement-block {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        margin-bottom: 12px;
        font-size: 0.84rem;
        line-height: 1.8;
    }
    .statement-row {
        display: flex;
        align-items: baseline;
        gap: 12px;
        margin-bottom: 4px;
        flex-wrap: wrap;
    }
    .statement-row:last-child { margin-bottom: 0; }
    .statement-field { display: flex; align-items: baseline; gap: 6px; flex-wrap: wrap; }
    .statement-field.full-width { width: 100%; }
    .statement-field.flex-1 { flex: 1; min-width: 180px; }
    .statement-field.flex-2 { flex: 2; min-width: 200px; }
    .statement-field.flex-3 { flex: 3; min-width: 250px; }

    .field-label { color: #334155; font-weight: 700; white-space: nowrap; }
    .field-label-inline { color: #334155; font-weight: 700; margin-right: 12px; white-space: nowrap; }
    .field-content { color: #0f172a; font-weight: 700; border-bottom: 1px dotted #94a3b8; padding: 0 4px; }
    .student-name-highlight { font-size: 0.95rem; color: #0f172a; font-weight: 800; }
    .bold-currency { font-size: 0.98rem; color: #0f172a; font-weight: 800; }
    .field-sub { font-size: 0.72rem; color: #64748b; }
    .words-content { color: #1e3a8a; font-weight: 700; }

    .voucher-table-wrapper { margin-bottom: 12px; }
    .voucher-natural-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
    .voucher-natural-table th, .voucher-natural-table td { border: 1px solid #334155; padding: 6px 8px; }
    .voucher-natural-table thead th { background: #f1f5f9; color: #0f172a; font-weight: 800; font-size: 0.78rem; }
    .voucher-total-summary-row td { background: #f8fafc; border-top: 2px solid #0f172a; border-bottom: 2px solid #0f172a; }
    .total-label-cell { text-align: right; font-size: 0.82rem; color: #0f172a; }

    .voucher-clearance-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        margin-bottom: 14px;
        gap: 12px;
        font-size: 0.78rem;
    }
    .clearance-paid { background: #f0fdf4; border-color: #86efac; }
    .clearance-icon { font-size: 1.25rem; color: #15803d; flex-shrink: 0; }
    .clearance-text { flex: 1; color: #1e293b; line-height: 1.45; }
    .clearance-remaining { text-align: left; white-space: nowrap; background: #ffffff; padding: 4px 10px; border: 1px solid #cbd5e1; border-radius: 4px; }
    .rem-lbl { display: block; font-size: 0.68rem; color: #64748b; }
    .rem-val { font-size: 0.82rem; color: #15803d; font-weight: 800; }

    .voucher-signatures-section {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
        align-items: center;
        padding: 8px 0 6px;
        text-align: center;
    }
    .sig-column { display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .sig-header { font-size: 0.78rem; font-weight: 800; color: #334155; margin-bottom: 4px; }
    .sig-space { height: 52px; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; width: 100%; position: relative; }
    .sig-handwritten-line { color: #94a3b8; font-size: 0.75rem; letter-spacing: 2px; }
    .official-signature-facsimile { font-family: 'Amiri', 'Traditional Arabic', serif; font-size: 1.15rem; font-weight: 700; color: #1e3a8a; margin-bottom: -4px; transform: rotate(-2deg); }
    .sig-name { font-size: 0.76rem; font-weight: 700; color: #0f172a; margin-top: 4px; }

    .authentic-school-stamp {
        width: 82px;
        height: 82px;
        border-radius: 50%;
        margin: 0 auto;
        display: grid;
        place-items: center;
        transform: rotate(-3deg);
        filter: drop-shadow(0 1px 2px rgba(30, 58, 138, 0.15));
    }
    .stamp-outer-circle { width: 80px; height: 80px; border-radius: 50%; border: 2px solid #1e3a8a; padding: 2px; display: grid; place-items: center; box-sizing: border-box; }
    .stamp-middle-circle { width: 100%; height: 100%; border-radius: 50%; border: 1px dashed #1e3a8a; display: flex; flex-direction: column; align-items: center; justify-content: space-between; padding: 3px 2px; box-sizing: border-box; text-align: center; }
    .stamp-text-arc-top { font-size: 0.52rem; font-weight: 800; color: #1e3a8a; line-height: 1; }
    .stamp-center-content { display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .stamp-inner-icon { font-size: 0.8rem; color: #1e3a8a; margin-bottom: 1px; }
    .stamp-state-txt { font-size: 0.62rem; font-weight: 900; color: #b91c1c; border: 1px solid #b91c1c; padding: 1px 4px; border-radius: 2px; line-height: 1; }
    .stamp-gov-txt { font-size: 0.5rem; color: #1e3a8a; font-weight: 700; margin-top: 1px; }
    .stamp-text-arc-bottom { font-size: 0.48rem; font-weight: 700; color: #1e3a8a; line-height: 1; }
    .stamp-caption { font-size: 0.68rem; font-weight: 700; color: #475569; margin-top: 3px; }

    .voucher-legal-footer {
        border-top: 1px solid #cbd5e1;
        margin-top: 10px;
        padding-top: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.66rem;
        color: #64748b;
        line-height: 1.4;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 8mm;
        }
        html, body {
            background: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .no-print, .sidebar, .navbar, .topbar, .footer, .salary-dashboard-wrapper, #claimModal {
            display: none !important;
        }
        #payslipModal {
            display: block !important;
            position: static !important;
            background: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            height: auto !important;
        }
        .payslip-modal-container {
            max-width: 100% !important;
            width: 100% !important;
            box-shadow: none !important;
            padding: 0 !important;
            border-radius: 0 !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .voucher-double-border {
            border: 2px solid #000000 !important;
            outline: 1px solid #000000 !important;
            padding: 12px 14px 10px !important;
        }
    }
</style>
@endsection
