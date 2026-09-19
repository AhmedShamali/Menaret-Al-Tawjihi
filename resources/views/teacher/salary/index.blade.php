@extends('layouts.app')

@section('title', 'كشف مسير الرواتب والمستحقات المالية - منصة منارة التوجيهي')

@section('content')
<div class="salary-dashboard-wrapper">
    {{-- 1. رأس الصفحة الفاخر --}}
    <div class="salary-header-card">
        <div class="header-main-info">
            <div class="avatar-seal-box">
                <img src="{{ $teacher->photo ? asset('storage/' . $teacher->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($teacher->name) . '&background=0284c7&color=fff&size=140&bold=true' }}" alt="{{ $teacher->name }}" class="teacher-avatar-img">
                <span class="role-badge-gold"><i class="fa-solid fa-chalkboard-user"></i> كادر التدريس</span>
            </div>
            <div class="teacher-details">
                <div class="badge-tag">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>منارة التوجيهي | الإدارة المالية والأكاديمية</span>
                </div>
                <h1 class="page-title">كشف ومسير الرواتب والمستحقات المالية</h1>
                <p class="teacher-subtitle">
                    المعلم الفاضل: <strong>{{ $teacher->name }}</strong>
                    @if($teacher->subject)
                        | مادة: <span class="text-primary">{{ $teacher->subject->name_ar ?? $teacher->subject->name }}</span>
                    @endif
                    | العام المالي: <span class="text-emerald font-mono font-bold">{{ $year }}</span>
                </p>
            </div>
        </div>

        <div class="header-actions">
            <form method="GET" action="{{ route('teacher.salaries.index') }}" class="year-select-form">
                <label for="yearSelect" class="select-label"><i class="fa-regular fa-calendar"></i> اختر السنة:</label>
                <select name="year" id="yearSelect" class="year-dropdown" onchange="this.form.submit()">
                    @for($y = date('Y') + 1; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }} م</option>
                    @endfor
                </select>
            </form>
            <button type="button" onclick="window.print()" class="btn-print-page" title="طباعة الكشف السنوي">
                <i class="fa-solid fa-print"></i> طباعة الكشف السنوي
            </button>
        </div>
    </div>

    {{-- 2. بطاقات المؤشرات المالية الكلاسيكية --}}
    <div class="stats-row-clean">
        <div class="stat-card-clean" style="--card-accent: #059669;">
            <span class="stat-label">إجمالي المستلم لعام {{ $year }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-emerald">{{ number_format($totalPaid, 2) }} ₪</span>
                <i class="fa-solid fa-hand-holding-dollar stat-icon text-emerald"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">مستحقات تم إيداعها وتأكيدها</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #1e3a8a;">
            <span class="stat-label">إجمالي المكافآت والحوافز</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-navy">{{ number_format($totalBonus, 2) }} ₪</span>
                <i class="fa-solid fa-gift stat-icon text-navy"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">تقدير إنجازاتك وجهودك</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #d97706;">
            <span class="stat-label">مستحقات قيد الاعتماد</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-amber">{{ number_format($pendingAmount, 2) }} ₪</span>
                <i class="fa-solid fa-clock-rotate-left stat-icon text-amber"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">جاري الصرف من الإدارة</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #6366f1;">
            <span class="stat-label">الأشهر المصروفة</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-indigo">{{ $paidMonthsCount }} <small style="font-size: 0.85rem; color: #64748b;">/ 12</small></span>
                <i class="fa-solid fa-calendar-check stat-icon text-indigo"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">نسبة الالتزام والتسديد</small>
        </div>
    </div>

    {{-- 3. جدول مسير الرواتب لجميع أشهر السنة (12 شهراً) --}}
    <div class="salary-table-card">
        <div class="table-header-row">
            <div>
                <h2 class="table-title"><i class="fa-solid fa-file-invoice-dollar text-primary"></i> كشف مسير رواتب الشهور (1 - 12) لعام {{ $year }}</h2>
                <p class="table-subtitle">يتم تحديث وإصدار الرواتب شهرياً بواسطة الإدارة المالية لمنصة منارة التوجيهي</p>
            </div>
            <div class="table-header-badge">
                <i class="fa-solid fa-shield-halved text-emerald"></i>
                <span>كشف مالي رسمي معتمد وموثق</span>
            </div>
        </div>

        <div class="table-responsive-box">
            <table class="salary-luxury-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th>الشهر</th>
                        <th>الراتب الأساسي</th>
                        <th>المكافآت والحوافز</th>
                        <th>الخصومات والاستقطاع</th>
                        <th>صافي الراتب المستحق</th>
                        <th>طريقة وتاريخ الصرف</th>
                        <th>حالة الصرف</th>
                        <th style="text-align: center; width: 170px;">قسيمة الراتب والإجراء</th>
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
                                    <span class="text-gray-muted">غير مدخل</span>
                                @endif
                            </td>
                            <td class="col-method">
                                @if($sal && $isPaid)
                                    <div class="payment-meta">
                                        <span><i class="fa-solid fa-money-check-dollar"></i> {{ $sal->payment_method ?: 'تحويل بنكي' }}</span>
                                        @if($sal->payment_date)
                                            <small class="font-mono">{{ $sal->payment_date->format('Y-m-d') }}</small>
                                        @endif
                                    </div>
                                @elseif($sal && $isPending)
                                    <span class="text-amber text-xs"><i class="fa-solid fa-clock"></i> في مرحلة الإعداد المالي</span>
                                @else
                                    <span class="text-gray-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($isPaid)
                                    <span class="status-pill status-paid">
                                        <i class="fa-solid fa-circle-check"></i> تم الصرف والاستلام
                                    </span>
                                @elseif($isPending)
                                    <span class="status-pill status-pending">
                                        <i class="fa-solid fa-clock-rotate-left"></i> قيد الاعتماد والصرف
                                    </span>
                                @else
                                    <span class="status-pill status-unissued">
                                        <i class="fa-regular fa-circle"></i> بانتظار إعداد الشهر
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="row-actions-group">
                                    @if($sal)
                                        <button type="button" 
                                                class="btn-table-action btn-payslip" 
                                                onclick="openPayslipModal({{ json_encode($sal) }}, '{{ addslashes($monthLabel) }}')" 
                                                title="استعراض وطباعة قسيمة الراتب الرسمية">
                                            <i class="fa-solid fa-receipt"></i> قسيمة الراتب
                                        </button>
                                    @else
                                        <button type="button" 
                                                class="btn-table-action btn-claim" 
                                                onclick="openClaimModal({{ $monthNum }}, '{{ addslashes($monthLabel) }}')" 
                                                title="إرسال استفسار أو ملاحظة مالية للإدارة">
                                            <i class="fa-regular fa-comment-dots"></i> استفسار مالي
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
                            <span class="status-pill status-paid"><i class="fa-solid fa-circle-check"></i> تم الصرف</span>
                        @elseif($isPending)
                            <span class="status-pill status-pending"><i class="fa-solid fa-clock-rotate-left"></i> قيد الاعتماد</span>
                        @else
                            <span class="status-pill status-unissued"><i class="fa-regular fa-circle"></i> بانتظار الإعداد</span>
                        @endif
                    </div>

                    <div class="mob-m-body">
                        <div class="mob-net-box">
                            <span class="mob-net-label">صافي الراتب المستحق:</span>
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
                                <span class="m-pill-item">الأساسي: <strong class="font-mono">{{ number_format($sal->basic_salary, 0) }} ₪</strong></span>
                                @if($sal->bonus > 0)
                                    <span class="m-pill-item text-emerald">+مكافأة: <strong class="font-mono">{{ number_format($sal->bonus, 0) }} ₪</strong></span>
                                @endif
                                @if($sal->deductions > 0)
                                    <span class="m-pill-item text-rose">-خصم: <strong class="font-mono">{{ number_format($sal->deductions, 0) }} ₪</strong></span>
                                @endif
                            </div>
                            @if($sal->payment_method || $sal->payment_date)
                                <div class="mob-payment-note">
                                    <i class="fa-solid fa-money-check-dollar"></i>
                                    <span>{{ $sal->payment_method ?: 'تحويل بنكي' }} {{ $sal->payment_date ? '(' . $sal->payment_date->format('Y-m-d') . ')' : '' }}</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="mob-m-footer">
                        @if($sal)
                            <button type="button" class="btn-table-action btn-payslip w-full" onclick="openPayslipModal({{ json_encode($sal) }}, '{{ addslashes($monthLabel) }}')">
                                <i class="fa-solid fa-receipt"></i> قسيمة الراتب الرسمية
                            </button>
                        @else
                            <button type="button" class="btn-table-action btn-claim w-full" onclick="openClaimModal({{ $monthNum }}, '{{ addslashes($monthLabel) }}')">
                                <i class="fa-regular fa-comment-dots"></i> إرسال استفسار مالي
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- 4. مودال قسيمة الراتب الرقمية القابلة للطباعة (Print-Ready Payslip Modal) --}}
<div id="payslipModal" class="payslip-modal-overlay" style="display: none;">
    <div class="payslip-modal-container" id="printablePayslip">
        <div class="payslip-inner-card">
            {{-- رأس القسيمة الرسمي --}}
            <div class="payslip-header">
                <div class="payslip-branding">
                    <div class="brand-logo-circle">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h2 class="brand-title">منصة منارة التوجيهي</h2>
                        <span class="brand-sub">الإدارة المالية وشؤون الكادر التعليمي - دولة فلسطين 🇵🇸</span>
                    </div>
                </div>
                <div class="payslip-badge-box">
                    <div class="payslip-type">قسيمة راتب شهرية رسمية</div>
                    <div class="payslip-serial font-mono" id="slipSerialNo">SLIP-2026-0000</div>
                </div>
            </div>

            {{-- خط فاصل ذهبي --}}
            <div class="payslip-divider"></div>

            {{-- بيانات المعلم والشهر --}}
            <div class="payslip-meta-grid">
                <div class="meta-item">
                    <span class="meta-label">اسم المعلم المكرم:</span>
                    <strong class="meta-value">{{ $teacher->name }}</strong>
                </div>
                <div class="meta-item">
                    <span class="meta-label">الشهر والسنة:</span>
                    <strong class="meta-value" id="slipMonthYear">-</strong>
                </div>
                <div class="meta-item">
                    <span class="meta-label">المادة الدراسية:</span>
                    <strong class="meta-value">{{ $teacher->subject->name_ar ?? 'كادر التدريس' }}</strong>
                </div>
                <div class="meta-item">
                    <span class="meta-label">تاريخ الإصدار / الصرف:</span>
                    <strong class="meta-value font-mono" id="slipPaymentDate">-</strong>
                </div>
                <div class="meta-item">
                    <span class="meta-label">طريقة الصرف:</span>
                    <strong class="meta-value" id="slipPaymentMethod">-</strong>
                </div>
                <div class="meta-item">
                    <span class="meta-label">رقم السند / المرجع:</span>
                    <strong class="meta-value font-mono" id="slipRefNo">-</strong>
                </div>
            </div>

            {{-- جدول تفاصيل الاستحقاقات والاستقطاعات --}}
            <div class="breakdown-tables-grid">
                {{-- الاستحقاقات --}}
                <div class="breakdown-card earnings">
                    <div class="breakdown-title">
                        <i class="fa-solid fa-circle-plus text-emerald"></i>
                        <span>الاستحقاقات والإضافات (Earnings)</span>
                    </div>
                    <table class="breakdown-table">
                        <tr>
                            <td>الراتب الأساسي المعتمد:</td>
                            <td class="font-mono" id="slipBasicSalary">0.00 ₪</td>
                        </tr>
                        <tr>
                            <td>مكافآت وحوافز التميز:</td>
                            <td class="font-mono text-emerald" id="slipBonus">0.00 ₪</td>
                        </tr>
                        <tr class="total-row">
                            <td><strong>إجمالي الاستحقاقات:</strong></td>
                            <td class="font-mono font-bold" id="slipTotalEarnings">0.00 ₪</td>
                        </tr>
                    </table>
                </div>

                {{-- الاستقطاعات والخصومات --}}
                <div class="breakdown-card deductions">
                    <div class="breakdown-title">
                        <i class="fa-solid fa-circle-minus text-rose"></i>
                        <span>الاستقطاعات والخصومات (Deductions)</span>
                    </div>
                    <table class="breakdown-table">
                        <tr>
                            <td>غياب / استقطاع إداري:</td>
                            <td class="font-mono text-rose" id="slipDeductions">0.00 ₪</td>
                        </tr>
                        <tr>
                            <td>أقساط أو سلف مسبقة:</td>
                            <td class="font-mono text-rose">0.00 ₪</td>
                        </tr>
                        <tr class="total-row">
                            <td><strong>إجمالي الخصومات:</strong></td>
                            <td class="font-mono font-bold text-rose" id="slipTotalDeductions">0.00 ₪</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- صندوق صافي الراتب المستلم الإجمالي --}}
            <div class="net-salary-banner">
                <div class="net-banner-info">
                    <span class="net-title">صافي الراتب المستحق والمحول:</span>
                    <span class="net-sub" id="slipNotes">لا توجد ملاحظات إضافية</span>
                </div>
                <div class="net-banner-amount font-mono" id="slipNetSalary">
                    0.00 ₪
                </div>
            </div>

            {{-- التواقيع والأختام الرسمية --}}
            <div class="payslip-signatures-row">
                <div class="sig-box">
                    <span>توقيع المعلم المستلم:</span>
                    <div class="sig-line">.....................................</div>
                </div>
                <div class="stamp-seal-box">
                    <div class="official-seal">
                        <i class="fa-solid fa-stamp"></i>
                        <span>معتمد - الإدارة العامة</span>
                        <small>منارة التوجيهي</small>
                    </div>
                </div>
                <div class="sig-box">
                    <span>اعتماد المشرف العام والمالي:</span>
                    <div class="sig-line">أ. أحمد حسين شمالي</div>
                </div>
            </div>

            {{-- أزرار التحكم بالمودال --}}
            <div class="modal-footer-actions no-print">
                <button type="button" class="btn-modal-print" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> طباعة القسيمة فوراً
                </button>
                <button type="button" class="btn-modal-close" onclick="closePayslipModal()">
                    إغلاق
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 5. مودال إرسال استفسار أو ملاحظة مالية للإدارة --}}
<div id="claimModal" class="payslip-modal-overlay" style="display: none;">
    <div class="claim-modal-container">
        <div class="claim-header">
            <i class="fa-solid fa-comments-dollar text-primary" style="font-size: 1.8rem;"></i>
            <div>
                <h3>إرسال استفسار مالي للإدارة</h3>
                <p id="claimMonthTitle">بخصوص مستحقات وراتب شهر محدد</p>
            </div>
        </div>

        <form id="claimForm" onsubmit="submitClaimForm(event)">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="month" id="claimMonthInput">

            <div class="form-group-block">
                <label class="input-label">تفاصيل الاستفسار أو الملاحظة <span class="required">*</span></label>
                <textarea name="message" id="claimMessageInput" rows="4" class="claim-textarea" placeholder="اكتب استفسارك أو ملاحظتك للإدارة بخصوص هذا الشهر..." required></textarea>
            </div>

            <div class="claim-actions-row">
                <button type="submit" class="btn-submit-claim" id="claimSubmitBtn">
                    <i class="fa-solid fa-paper-plane"></i> إرسال للإدارة العامة
                </button>
                <button type="button" class="btn-cancel-claim" onclick="closeClaimModal()">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openPayslipModal(salary, monthLabel) {
        document.getElementById('slipSerialNo').innerText = `SLIP-{{ $year }}-${String(salary.month).padStart(2, '0')}-${salary.id}`;
        document.getElementById('slipMonthYear').innerText = `${monthLabel} {{ $year }} م`;
        document.getElementById('slipPaymentDate').innerText = salary.payment_date || '{{ date("Y-m-d") }}';
        document.getElementById('slipPaymentMethod').innerText = salary.payment_method || 'تحويل بنكي / محفظة إلكترونية';
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
        document.getElementById('slipNotes').innerText = salary.notes || 'تم اعتماد وصرف الراتب كاملاً وفقاً للائحة منصة منارة التوجيهي.';

        document.getElementById('payslipModal').style.display = 'flex';
    }

    function closePayslipModal() {
        document.getElementById('payslipModal').style.display = 'none';
    }

    function openClaimModal(monthNum, monthLabel) {
        document.getElementById('claimMonthInput').value = monthNum;
        document.getElementById('claimMonthTitle').innerText = `بخصوص راتب ومستحقات (${monthLabel} {{ $year }})`;
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
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الإرسال...';

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
                title: 'تم إرسال استفسارك بنجاح',
                text: res.data.message,
                confirmButtonColor: '#059669'
            });
        })
        .catch(err => {
            Swal.fire('خطأ', err.response?.data?.message || 'فشل إرسال الملاحظة', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // إغلاق المودال عند النقر خارج المحتوى
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

    /* 1. Header Card */
    .salary-header-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 24px;
        padding: 32px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
        color: #ffffff;
        box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.25);
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .salary-header-card::after {
        content: '';
        position: absolute;
        top: -60px;
        left: -60px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.2) 0%, transparent 70%);
        pointer-events: none;
    }
    .header-main-info {
        display: flex;
        align-items: center;
        gap: 22px;
        z-index: 1;
    }
    .avatar-seal-box {
        position: relative;
        width: 86px;
        height: 86px;
    }
    .teacher-avatar-img {
        width: 86px;
        height: 86px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #38bdf8;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
    .role-badge-gold {
        position: absolute;
        bottom: -6px;
        right: -6px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
        font-size: 0.72rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 12px;
        border: 2px solid #1e293b;
        white-space: nowrap;
    }
    .badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.12);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        color: #94a3b8;
        margin-bottom: 8px;
    }
    .page-title {
        font-size: 1.65rem;
        font-weight: 800;
        margin: 0 0 6px;
        color: #ffffff;
    }
    .teacher-subtitle {
        margin: 0;
        color: #cbd5e1;
        font-size: 0.95rem;
    }
    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 1;
    }
    .year-select-form {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,0.08);
        padding: 6px 14px;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.15);
    }
    .select-label {
        font-size: 0.85rem;
        color: #94a3b8;
        margin: 0;
    }
    .year-dropdown {
        background: transparent;
        border: none;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.95rem;
        outline: none;
        cursor: pointer;
    }
    .year-dropdown option {
        background: #1e293b;
        color: #fff;
    }
    .btn-print-page {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: #fff;
        border: none;
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-print-page:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(2, 132, 199, 0.4);
    }

    /* 2. KPI Cards */
    .kpi-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }
    .kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 22px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        transition: 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.06);
    }
    .kpi-icon-bubble {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }
    .card-emerald .kpi-icon-bubble { background: #ecfdf5; color: #059669; }
    .card-blue .kpi-icon-bubble { background: #f0f9ff; color: #0284c7; }
    .card-amber .kpi-icon-bubble { background: #fffbeb; color: #d97706; }
    .card-purple .kpi-icon-bubble { background: #faf5ff; color: #9333ea; }

    .kpi-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
        display: block;
        margin-bottom: 4px;
    }
    .kpi-value {
        font-size: 1.55rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .kpi-subtext {
        font-size: 0.76rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* 3. Table Card */
    .salary-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 28px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.03);
        width: 100%;
        box-sizing: border-box;
        overflow-x: hidden;
    }
    .table-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .table-subtitle {
        margin: 0;
        color: #64748b;
        font-size: 0.88rem;
    }
    .table-header-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
    }

    .table-responsive-box {
        width: 100%;
        overflow-x: hidden;
    }
    .salary-luxury-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }
    .salary-luxury-table th {
        background: #f8fafc !important;
        color: #0f172a !important;
        font-weight: 800;
        font-size: 0.82rem;
        padding: 12px 16px;
        border-bottom: 2px solid #cbd5e1 !important;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }
    .salary-luxury-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.92rem;
        vertical-align: middle;
    }
    .salary-luxury-table tbody tr:hover {
        background: #f8fafc;
    }
    .col-num {
        color: #94a3b8;
        font-weight: 800;
    }
    .month-title-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .month-bullet {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .bullet-paid { background: #10b981; box-shadow: 0 0 8px rgba(16, 185, 129, 0.5); }
    .bullet-pending { background: #f59e0b; box-shadow: 0 0 8px rgba(245, 158, 11, 0.5); }
    .bullet-gray { background: #cbd5e1; }

    .net-value {
        font-size: 1.05rem;
    }
    .text-paid { color: #059669; }
    .text-pending { color: #d97706; }
    .text-gray-muted { color: #94a3b8; }

    .payment-meta {
        display: flex;
        flex-direction: column;
        gap: 2px;
        font-size: 0.84rem;
        color: #334155;
    }
    .payment-meta small {
        color: #64748b;
        font-size: 0.76rem;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .status-paid { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .status-unissued { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

    .btn-table-action {
        border: none;
        padding: 7px 14px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: 0.2s;
    }
    .btn-payslip {
        background: #0284c7;
        color: #ffffff;
    }
    .btn-payslip:hover {
        background: #0369a1;
        transform: translateY(-1px);
    }
    .btn-claim {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    .btn-claim:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* 4. Payslip Modal Styles */
    .payslip-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .payslip-modal-container {
        background: #ffffff;
        border-radius: 24px;
        max-width: 800px;
        width: 100%;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        max-height: 90vh;
        overflow-y: auto;
    }
    .payslip-inner-card {
        padding: 36px 32px;
    }
    .payslip-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .payslip-branding {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .brand-logo-circle {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: #fff;
        display: grid;
        place-items: center;
        font-size: 1.6rem;
    }
    .brand-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .brand-sub {
        font-size: 0.8rem;
        color: #64748b;
    }
    .payslip-badge-box {
        text-align: left;
    }
    .payslip-type {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
    }
    .payslip-serial {
        font-size: 0.8rem;
        color: #64748b;
    }
    .payslip-divider {
        height: 3px;
        background: linear-gradient(90deg, #f59e0b, #0284c7);
        border-radius: 2px;
        margin: 20px 0 24px;
    }
    .payslip-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px 20px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        margin-bottom: 24px;
    }
    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .meta-label {
        font-size: 0.78rem;
        color: #64748b;
    }
    .meta-value {
        font-size: 0.95rem;
        color: #0f172a;
    }

    .breakdown-tables-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 24px;
    }
    .breakdown-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
    }
    .breakdown-title {
        background: #f8fafc;
        padding: 10px 16px;
        font-weight: 800;
        font-size: 0.85rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid #e2e8f0;
    }
    .breakdown-table {
        width: 100%;
        border-collapse: collapse;
    }
    .breakdown-table td {
        padding: 10px 16px;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .breakdown-table .total-row td {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        border-bottom: none;
    }

    .net-salary-banner {
        background: linear-gradient(135deg, #059669, #047857);
        color: #fff;
        border-radius: 18px;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        box-shadow: 0 10px 20px rgba(5, 150, 105, 0.2);
    }
    .net-title {
        font-size: 1.1rem;
        font-weight: 800;
        display: block;
        margin-bottom: 4px;
    }
    .net-sub {
        font-size: 0.82rem;
        opacity: 0.85;
    }
    .net-banner-amount {
        font-size: 2rem;
        font-weight: 900;
        letter-spacing: -0.5px;
    }

    .payslip-signatures-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px dashed #cbd5e1;
        padding-top: 24px;
        margin-bottom: 24px;
        text-align: center;
    }
    .sig-box {
        font-size: 0.85rem;
        color: #475569;
    }
    .sig-line {
        margin-top: 24px;
        font-weight: 800;
        color: #0f172a;
    }
    .official-seal {
        border: 2px dashed #0284c7;
        color: #0284c7;
        padding: 8px 18px;
        border-radius: 50px;
        display: flex;
        flex-direction: column;
        align-items: center;
        transform: rotate(-5deg);
        font-weight: 800;
        font-size: 0.78rem;
    }

    .modal-footer-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
    .btn-modal-print {
        background: #0284c7;
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-modal-close {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 10px 22px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
    }

    /* Claim Modal */
    .claim-modal-container {
        background: #ffffff;
        border-radius: 20px;
        max-width: 520px;
        width: 100%;
        padding: 28px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    .claim-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
    }
    .claim-header h3 {
        margin: 0 0 4px;
        font-size: 1.2rem;
        color: #0f172a;
    }
    .claim-header p {
        margin: 0;
        font-size: 0.85rem;
        color: #64748b;
    }
    .claim-textarea {
        width: 100%;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 0.92rem;
        outline: none;
        resize: vertical;
        font-family: inherit;
        box-sizing: border-box;
    }
    .claim-textarea:focus {
        border-color: #0284c7;
    }
    .claim-actions-row {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
    }
    .btn-submit-claim {
        background: #0284c7;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
    }
    .btn-cancel-claim {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
    /* بطاقات كشف الرواتب للجوال بدون سكرول أفقي نهائياً */
    .teacher-salary-mobile-cards {
        display: none;
    }

    @media (max-width: 992px) {
        .table-responsive-box { display: none; }
        .teacher-salary-mobile-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            width: 100%;
        }
        .teacher-mob-month-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .teacher-mob-month-card.card-paid { border-color: #10b981; background: #f0fdf4; }
        .teacher-mob-month-card.card-pending { border-color: #f59e0b; background: #fffbeb; }
        .teacher-mob-month-card.card-unissued { border-color: #e2e8f0; background: #ffffff; }

        .mob-m-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            padding-bottom: 10px;
        }
        .mob-m-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            color: #0f172a;
        }
        .mob-m-num {
            background: #0284c7;
            color: #fff;
            padding: 2px 8px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.85rem;
        }
        .mob-net-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255,255,255,0.85);
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 12px;
            padding: 10px 14px;
        }
        .mob-net-label { font-size: 0.84rem; color: #475569; font-weight: 700; }
        .mob-net-val { font-size: 1.25rem; font-weight: 900; }
        .mob-salary-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        .m-pill-item {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.78rem;
        }
        .mob-payment-note {
            font-size: 0.78rem;
            color: #64748b;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .mob-m-footer {
            border-top: 1px solid rgba(0,0,0,0.06);
            padding-top: 10px;
        }
        .w-full {
            width: 100%;
            justify-content: center;
        }
        .salary-header-card {
            padding: 20px 18px;
        }
        .header-main-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
        }
    }

    /* Print styles */
    @media print {
        body * {
            visibility: hidden;
        }
        #payslipModal, #printablePayslip, #printablePayslip * {
            visibility: visible;
        }
        #payslipModal {
            position: absolute;
            left: 0;
            top: 0;
            background: transparent;
            padding: 0;
            display: block !important;
        }
        .payslip-modal-container {
            box-shadow: none;
            max-width: 100%;
            border-radius: 0;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection
