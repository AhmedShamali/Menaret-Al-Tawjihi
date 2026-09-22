@extends('layouts.app')

@php
    $studentDisplayName = (app()->getLocale() === 'en' && !empty($student->name_en)) ? $student->name_en : ($student->name_ar ?? $student->name);
    $stageDisplayName = (app()->getLocale() === 'en' && !empty($student->stage->name_en)) ? $student->stage->name_en : ($student->stage->label_ar ?? ($student->stage->name_ar ?? __('عام')));
@endphp

@section('title', __('الملف المالي وسجل اشتراكات الطالب') . ' | ' . $studentDisplayName . ' | ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
<div class="student-profile-finance-wrap">

    {{-- 1. شريط التنقل والترويسة الكلاسيكية العليا --}}
    <div class="top-nav-bar-classic">
        <div class="nav-breadcrumbs">
            <a href="{{ route('admin.dashboard') }}" class="crumb-link"><i class="fa-solid fa-house"></i> {{ __('الرئيسية') }}</a>
            <span class="crumb-sep">/</span>
            <a href="{{ route('admin.subscriptions.monthly', ['year' => $year]) }}" class="crumb-link">{{ __('مصفوفة وسجل الاشتراكات الشهرية') }}</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current">{{ $studentDisplayName }}</span>
        </div>

        <div class="nav-actions-group">
            <a href="{{ route('admin.subscriptions.monthly', ['year' => $year]) }}" class="btn-classic-outline">
                <i class="fa-solid fa-arrow-right"></i>
                <span>{{ __('العودة للمصفوفة العامة') }}</span>
            </a>

            <button type="button" class="btn-classic-print" onclick="openStatementModal()">
                <i class="fa-solid fa-print"></i>
                <span>{{ __('طباعة كشف الذمة المعتمد') }}</span>
            </button>
        </div>
    </div>

    {{-- 2. بطاقة هوية الطالب الأكاديمية والمالية الفاخرة --}}
    <div class="student-hero-classic-card">
        <div class="hero-main-details">
            <div class="avatar-holder">
                <img src="{{ $student->photo_url }}" alt="{{ $studentDisplayName }}" class="student-photo-royal">
                <span class="status-indicator-dot {{ $studentRemaining == 0 ? 'is-clear' : 'has-due' }}"></span>
            </div>

            <div class="student-identity-text">
                <div class="name-badge-row">
                    <h1 class="student-full-title">{{ $studentDisplayName }}</h1>
                    <span class="stage-tag-classic"><i class="fa-solid fa-graduation-cap"></i> {{ $stageDisplayName }}</span>
                    @if($studentRemaining == 0)
                        <span class="clearance-pill-royal"><i class="fa-solid fa-shield-check"></i> {{ __('ذمة مسددة ومبرأة بالكامل') }}</span>
                    @else
                        <span class="due-pill-royal"><i class="fa-solid fa-circle-exclamation"></i> {{ __('يوجد رصيد مستحق بذمة الطالب') }}</span>
                    @endif
                </div>

                <div class="student-meta-strip">
                    <span class="meta-item"><i class="fa-solid fa-id-card text-muted"></i> <strong>{{ __('رقم الهوية:') }}</strong> <span class="font-mono" dir="ltr">{{ $student->nid ?? '-' }}</span></span>
                    <span class="meta-item"><i class="fa-solid fa-key text-amber"></i> <strong>{{ __('كلمة المرور:') }}</strong> <code class="font-mono" style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; border: 1px solid #fde68a; font-weight: 800; cursor: pointer;" title="{{ __('انقر لنسخ كلمة المرور') }}" onclick="if(typeof Swal !== 'undefined'){ navigator.clipboard.writeText('{{ $student->plain_password ?: '123456' }}'); Swal.fire({toast:true,position:'top-end',icon:'success',title:'{{ __('تم نسخ كلمة المرور') }}',showConfirmButton:false,timer:1500}); } else { alert('{{ __('تم نسخ كلمة المرور') }}'); }">{{ $student->plain_password ?: '123456' }}</code></span>
                    <span class="meta-item"><i class="fa-solid fa-phone text-muted"></i> <strong>{{ __('هاتف الطالب:') }}</strong> <a href="tel:{{ $student->phone }}" class="phone-link font-mono" dir="ltr">{{ $student->phone ?? '-' }}</a></span>
                    @if($student->guardian_phone)
                        <span class="meta-item"><i class="fa-solid fa-user-shield text-muted"></i> <strong>{{ __('ولي الأمر:') }}</strong> <a href="tel:{{ $student->guardian_phone }}" class="phone-link font-mono" dir="ltr">{{ $student->guardian_phone }}</a></span>
                    @endif
                    <span class="meta-item"><i class="fa-solid fa-calendar-days text-muted"></i> <strong>{{ __('العام الدراسي:') }}</strong> <span class="font-mono">{{ $year }}</span></span>
                </div>
            </div>
        </div>

        {{-- تفاصيل خطة الرسوم والخصومات --}}
        <div class="fee-plan-box">
            <div class="fee-plan-header">
                <span class="fee-plan-lbl"><i class="fa-solid fa-coins text-amber"></i> {{ __('خطة الرسوم والخصم المعتمدة') }}</span>
                <button type="button" class="btn-edit-fee-mini" onclick="openStudentFeeModal()" title="{{ __('تعديل خطة رسوم الطالب') }}">
                    <i class="fa-solid fa-pen-to-square"></i> {{ __('تعديل') }}
                </button>
            </div>
            <div class="fee-plan-body">
                <div class="fee-val-row">
                    <span class="text-muted">{{ __('القسط الأساسي:') }}</span>
                    <strong class="font-mono">{{ number_format($student->monthly_fee ?: 150, 0) }} ₪/شهر</strong>
                </div>
                @if($student->hasDiscount())
                    <div class="fee-val-row text-rose">
                        <span>{{ __('الخصم:') }}</span>
                        <strong>
                            @if($student->custom_discount_percent > 0)
                                {{ $student->custom_discount_percent }}%
                            @endif
                            @if($student->custom_discount_fixed > 0)
                                ({{ number_format($student->custom_discount_fixed, 0) }} ₪)
                            @endif
                        </strong>
                    </div>
                @endif
                <div class="fee-val-row net-due-row">
                    <span>{{ __('المستحق الصافي:') }}</span>
                    <strong class="font-mono text-emerald" id="student_fee_label_hero">{{ number_format($student->monthlyAmountDue(), 0) }} ₪/شهر</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. بطاقات المؤشرات المالية الأربعة الكلاسيكية للطالب --}}
    <div class="financial-kpi-grid-classic">
        {{-- كرت المستحق المطلوب --}}
        <div class="kpi-card-classic kpi-due">
            <div class="kpi-icon-wrap">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">{{ __('إجمالي المستحق المطلوب للعام') }}</span>
                <div class="kpi-num-wrap font-mono" id="hero_total_due">{{ number_format($studentDue, 2) }} ₪</div>
                <small class="kpi-sub-text">{{ __('إجمالي الرسوم المقررة عن الشهور الـ 12') }}</small>
            </div>
        </div>

        {{-- كرت المسدد المعتمد --}}
        <div class="kpi-card-classic kpi-paid">
            <div class="kpi-icon-wrap">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">{{ __('إجمالي المبلغ المسدد المعتمد') }}</span>
                <div class="kpi-num-wrap font-mono text-emerald" id="hero_total_paid">{{ number_format($studentPaid, 2) }} ₪</div>
                <small class="kpi-sub-text">{{ __('المبالغ المقبوضة فعلياً في خزينة المنصة') }}</small>
            </div>
        </div>

        {{-- كرت الرصيد المتبقي --}}
        <div class="kpi-card-classic kpi-remaining {{ $studentRemaining > 0 ? 'is-alert' : 'is-safe' }}">
            <div class="kpi-icon-wrap">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">{{ __('الرصيد المتبقي بذمة الطالب') }}</span>
                <div class="kpi-num-wrap font-mono {{ $studentRemaining > 0 ? 'text-rose' : 'text-emerald' }}" id="hero_total_remaining">
                    {{ number_format($studentRemaining, 2) }} ₪
                </div>
                <small class="kpi-sub-text">
                    @if($studentRemaining > 0)
                        {{ __('مستحق للسداد بموجب أقساط الشهور') }} ⚠️
                    @else
                        {{ __('ذمة مالية بريئة ومسددة 100%') }} ✅
                    @endif
                </small>
            </div>
        </div>

        {{-- كرت نسبة الإنجاز وحالة الأقساط --}}
        <div class="kpi-card-classic kpi-rate">
            <div class="kpi-icon-wrap">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">{{ __('حالة سداد الأقساط الـ 12') }}</span>
                <div class="kpi-num-wrap font-mono" id="hero_installments_count">
                    {{ $paidCount + $waivedCount }} <span class="kpi-denom">/ 12</span>
                </div>
                <div class="progress-bar-classic">
                    <div class="progress-fill-classic" style="width: {{ $collectionRate }}%;"></div>
                </div>
                <small class="kpi-sub-text font-mono">{{ __('نسبة الإنجاز المالي:') }} {{ $collectionRate }}%</small>
            </div>
        </div>
    </div>

    {{-- 4. لوحة التحكم والتحكم الفردي بجميع الشهور الـ 12 --}}
    <div class="months-control-section-classic">
        <div class="section-classic-header">
            <div class="header-titles">
                <h2 class="sec-title"><i class="fa-solid fa-calendar-check text-primary"></i> {{ __('سجل استحقاقات وسداد الشهور الـ 12') }}</h2>
                <p class="sec-desc">{{ __('يمكنك النقر على أي شهر لمراجعة بياناته، تسجيل دفع كامل أو جزئي، أو منح إعفاء فوري.') }}</p>
            </div>

            {{-- التبديل السريع بين الطلاب --}}
            <div class="student-fast-navigator">
                <span class="nav-title-lbl">{{ __('التنقل بين الطلاب:') }}</span>
                @if($prevStudent)
                    <a href="{{ route('admin.subscriptions.student', ['student' => $prevStudent->id, 'year' => $year]) }}" class="btn-nav-step" title="{{ $prevStudent->name_ar ?? $prevStudent->name }}">
                        <i class="fa-solid fa-chevron-right"></i> {{ __('السابق') }}
                    </a>
                @endif

                <select class="select-fast-student" onchange="if(this.value) window.location.href=this.value;">
                    <option value="">-- {{ __('اختر طالباً آخر') }} --</option>
                    @foreach($allStageStudents as $st)
                        <option value="{{ route('admin.subscriptions.student', ['student' => $st->id, 'year' => $year]) }}" {{ $st->id == $student->id ? 'selected' : '' }}>
                            {{ $st->name_ar ?? $st->name_en }} ({{ $st->nid ?? '-' }})
                        </option>
                    @endforeach
                </select>

                @if($nextStudent)
                    <a href="{{ route('admin.subscriptions.student', ['student' => $nextStudent->id, 'year' => $year]) }}" class="btn-nav-step" title="{{ $nextStudent->name_ar ?? $nextStudent->name }}">
                        {{ __('التالي') }} <i class="fa-solid fa-chevron-left"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- شبكة بطاقات الشهور الـ 12 الكلاسيكية --}}
        <div class="months-cards-grid-classic">
            @for($m = 1; $m <= 12; $m++)
                @php
                    $sub = $subscriptions->firstWhere('month', $m);
                    $st = $sub ? $sub->status : 'unpaid';
                    $amt = $sub ? (float)$sub->amount : (float)$student->monthlyAmountDue();
                    $paidAmt = $sub ? (float)($sub->paid_amount ?? 0) : 0.00;
                    if ($st === 'paid' && $paidAmt <= 0) {
                        $paidAmt = $amt;
                    }
                    $remAmt = $sub ? (float)$sub->remaining_amount : ($st === 'waived' ? 0.00 : $amt);
                    $notes = $sub ? ($sub->notes ?? '') : '';
                    $mTitle = $monthsNames[$m] ?? (app()->getLocale() === 'en' ? "Month $m" : "شهر $m");
                    $paidAt = ($sub && $sub->paid_at) ? \Carbon\Carbon::parse($sub->paid_at)->format('Y-m-d') : null;
                @endphp

                <div class="month-card-classic status-border-{{ $st }}" id="month_card_{{ $m }}">
                    <div class="card-head-bar">
                        <div class="month-identity">
                            <span class="month-number-circle font-mono">{{ $m }}</span>
                            <strong class="month-name-text">{{ $mTitle }}</strong>
                        </div>

                        <span class="status-badge-classic badge-{{ $st }}" id="status_badge_{{ $m }}">
                            @if($st === 'paid')
                                <i class="fa-solid fa-check"></i> {{ __('مسدد بالكامل') }}
                            @elseif($st === 'partial')
                                <i class="fa-solid fa-circle-half-stroke"></i> {{ __('سداد جزئي') }}
                            @elseif($st === 'pending')
                                <i class="fa-solid fa-hourglass-half"></i> {{ __('قيد المراجعة') }}
                            @elseif($st === 'waived')
                                <i class="fa-solid fa-tag"></i> {{ __('إعفاء / منحة') }}
                            @else
                                <i class="fa-solid fa-xmark"></i> {{ __('غير مسدد') }}
                            @endif
                        </span>
                    </div>

                    <div class="card-financial-figures">
                        <div class="fig-item">
                            <span class="fig-lbl">{{ __('المستحق:') }}</span>
                            <span class="fig-val font-mono" id="card_amt_{{ $m }}">{{ number_format($amt, 2) }} ₪</span>
                        </div>
                        <div class="fig-item">
                            <span class="fig-lbl">{{ __('المسدد:') }}</span>
                            <span class="fig-val font-mono text-emerald" id="card_paid_{{ $m }}">{{ number_format($paidAmt, 2) }} ₪</span>
                        </div>
                        <div class="fig-item">
                            <span class="fig-lbl">{{ __('المتبقي:') }}</span>
                            <span class="fig-val font-mono {{ $remAmt > 0 ? 'text-rose font-bold' : 'text-emerald' }}" id="card_rem_{{ $m }}">
                                {{ number_format($remAmt, 2) }} ₪
                            </span>
                        </div>
                    </div>

                    <div class="card-notes-preview">
                        @if($paidAt)
                            <div class="date-stamp-row">
                                <i class="fa-regular fa-clock"></i> <span>{{ __('تاريخ السداد:') }} {{ $paidAt }}</span>
                            </div>
                        @endif
                        @if($notes)
                            <div class="note-snippet" title="{{ $notes }}">
                                <i class="fa-regular fa-comment-dots"></i> <span>{{ Str::limit($notes, 36) }}</span>
                            </div>
                        @else
                            <div class="note-snippet text-muted">
                                <span>- {{ __('لا توجد ملاحظات إضافية') }} -</span>
                            </div>
                        @endif
                    </div>

                    <div class="card-action-bar">
                        <button type="button" 
                                class="btn-manage-month-action"
                                data-student-id="{{ $student->id }}"
                                data-student-name="{{ $studentDisplayName }}"
                                data-month="{{ $m }}"
                                data-month-title="{{ $mTitle }}"
                                data-status="{{ $st }}"
                                data-amount="{{ $amt }}"
                                data-paid-amount="{{ $paidAmt }}"
                                data-remaining-amount="{{ $remAmt }}"
                                data-notes="{{ $notes }}"
                                data-student-fee="{{ (float)$student->monthlyAmountDue() }}"
                                onclick="openMonthModalFromEl(this)">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>{{ __('تسجيل وسداد القسط') }}</span>
                        </button>
                    </div>
                </div>
            @endfor
        </div>
    </div>

</div>

{{-- 5. نافذة تعديل وسداد الشهر الملكية المعتمدة (Edit Month Modal) --}}
<div id="editMonthModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box">
        <div class="modal-header-royal">
            <div class="modal-header-info">
                <div class="modal-avatar-badge font-mono" id="modalStudentInitials">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <h3 class="modal-student-name" id="modalStudentName">{{ $studentDisplayName }}</h3>
                    <p class="modal-month-subtitle" id="modalMonthTitle">{{ __('اشتراك الشهر') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close-x" onclick="closeMonthModal()">&times;</button>
        </div>

        <form id="editMonthForm" onsubmit="submitMonthForm(event)">
            @csrf
            <input type="hidden" name="student_id" id="formStudentId" value="{{ $student->id }}">
            <input type="hidden" name="month" id="formMonth">
            <input type="hidden" name="academic_year" value="{{ $year }}">

            <div class="form-body-wrap">
                {{-- أزرار الراديو الكلاسيكية المنسقة بحالات السداد --}}
                <label class="section-label-royal">{{ __('حالة الاشتراك والسداد لهذا الشهر *') }}</label>
                <div class="status-options-grid">
                    <label class="status-card-opt opt-paid">
                        <input type="radio" name="status" value="paid" id="st_paid" onchange="onStatusRadioChange('paid')">
                        <div class="opt-content">
                            <span class="opt-icon"><i class="fa-solid fa-circle-check"></i></span>
                            <div class="opt-text">
                                <strong>{{ __('مسدد بالكامل') }}</strong>
                                <small>{{ __('تم سداد كامل القسط') }}</small>
                            </div>
                        </div>
                    </label>

                    <label class="status-card-opt opt-partial">
                        <input type="radio" name="status" value="partial" id="st_partial" onchange="onStatusRadioChange('partial')">
                        <div class="opt-content">
                            <span class="opt-icon"><i class="fa-solid fa-circle-half-stroke"></i></span>
                            <div class="opt-text">
                                <strong>{{ __('سداد جزئي') }}</strong>
                                <small>{{ __('سداد جزئي مع بقاء رصيد') }}</small>
                            </div>
                        </div>
                    </label>

                    <label class="status-card-opt opt-unpaid">
                        <input type="radio" name="status" value="unpaid" id="st_unpaid" onchange="onStatusRadioChange('unpaid')">
                        <div class="opt-content">
                            <span class="opt-icon"><i class="fa-solid fa-circle-xmark"></i></span>
                            <div class="opt-text">
                                <strong>{{ __('غير مسدد') }}</strong>
                                <small>{{ __('قسط كامل متأخر') }}</small>
                            </div>
                        </div>
                    </label>

                    <label class="status-card-opt opt-pending">
                        <input type="radio" name="status" value="pending" id="st_pending" onchange="onStatusRadioChange('pending')">
                        <div class="opt-content">
                            <span class="opt-icon"><i class="fa-solid fa-hourglass-half"></i></span>
                            <div class="opt-text">
                                <strong>{{ __('قيد المراجعة') }}</strong>
                                <small>{{ __('أرسل إشعار تحويل') }}</small>
                            </div>
                        </div>
                    </label>

                    <label class="status-card-opt opt-waived">
                        <input type="radio" name="status" value="waived" id="st_waived" onchange="onStatusRadioChange('waived')">
                        <div class="opt-content">
                            <span class="opt-icon"><i class="fa-solid fa-tag"></i></span>
                            <div class="opt-text">
                                <strong>{{ __('إعفاء / منحة') }}</strong>
                                <small>{{ __('معفى رسمياً 100%') }}</small>
                            </div>
                        </div>
                    </label>
                </div>

                {{-- المبالغ والحساب اللحظي التفاعلي --}}
                <div class="amounts-calc-grid">
                    <div class="form-field-group">
                        <label class="field-label-royal" for="formAmount">{{ __('المبلغ المستحق للشهر (₪) *') }}</label>
                        <div class="input-with-currency">
                            <input type="number" step="0.5" min="0" name="amount" id="formAmount" class="clean-input" required oninput="calcRemainingRealtime()">
                            <span class="curr-tag">₪</span>
                        </div>
                        <small class="field-hint">{{ __('المبلغ المستحق لهذا الشهر') }}</small>
                    </div>

                    <div class="form-field-group">
                        <label class="field-label-royal" for="formPaidAmount">{{ __('المبلغ المسدد فعلياً (₪) *') }}</label>
                        <div class="input-with-currency">
                            <input type="number" step="0.5" min="0" name="paid_amount" id="formPaidAmount" class="clean-input" required oninput="calcRemainingRealtime()">
                            <span class="curr-tag">₪</span>
                        </div>
                        <small class="field-hint" id="paidHelpText">{{ __('المبلغ المقبوض من الطالب فعلياً') }}</small>
                    </div>
                </div>

                {{-- أزرار سريعة لتسريع الإدخال --}}
                <div class="presets-row">
                    <span class="presets-label">{{ __('خيارات سريعة:') }}</span>
                    <button type="button" class="btn-preset" onclick="setPresetPaid('full')">{{ __('سداد كامل 100%') }}</button>
                    <button type="button" class="btn-preset" onclick="setPresetPaid('half')">{{ __('سداد 50%') }}</button>
                    <button type="button" class="btn-preset" onclick="setPresetPaid('zero')">{{ __('غير مسدد (0 ₪)') }}</button>
                </div>

                {{-- شريط الحساب اللحظي المباشر للرصيد المتبقي --}}
                <div class="live-calc-box">
                    <div class="calc-label-row">
                        <span>{{ __('الرصيد المتبقي بذمة الطالب للشهر:') }}</span>
                        <strong class="font-mono remaining-display" id="formRemainingPreview">0.00 ₪</strong>
                    </div>
                    <div class="calc-status-indicator" id="formStatusNotice">
                        <i class="fa-solid fa-circle-check"></i> <span>{{ __('مسدد بالكامل رسمياً') }}</span>
                    </div>
                </div>

                {{-- الملاحظات ورقم السند --}}
                <div class="form-field-group">
                    <label class="field-label-royal" for="formNotes">{{ __('ملاحظات وبيان الدفعة (تظهر في السند)') }}</label>
                    <input type="text" name="notes" id="formNotes" class="clean-input" placeholder="{{ __('مثال: نقداً باليد، إشعار سداد بنكي رقم...') }}">
                </div>
            </div>

            <div class="modal-footer-royal">
                <button type="button" class="btn-cancel-sub" onclick="closeMonthModal()">{{ __('إلغاء') }}</button>
                <button type="submit" class="btn-save-sub" id="btnSaveSub">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('حفظ واعتماد التحديث') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 6. نافذة تعديل رسوم الطالب والخصومات المعتمدة --}}
<div id="studentFeeModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box modal-fee-box">
        <div class="modal-header-royal">
            <div class="modal-header-info">
                <div class="modal-avatar-badge text-amber">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div>
                    <h3 class="modal-student-name">{{ $studentDisplayName }}</h3>
                    <p class="modal-month-subtitle">{{ __('تعديل خطة القسط الشهري والخصومات المعتمدة') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close-x" onclick="closeStudentFeeModal()">&times;</button>
        </div>

        <form id="studentFeeForm" onsubmit="submitStudentFeeForm(event)">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <input type="hidden" name="academic_year" value="{{ $year }}">

            <div class="form-body-wrap">
                <div class="form-field-group">
                    <label class="field-label-royal">{{ __('القسط الشهري الأساسي للطالب (₪) *') }}</label>
                    <div class="input-with-currency">
                        <input type="number" step="1" min="0" name="monthly_fee" id="feeInputMonthly" class="clean-input" value="{{ (float)($student->monthly_fee ?: 150) }}" required oninput="calcNetFeePreview()">
                        <span class="curr-tag">₪</span>
                    </div>
                </div>

                <div class="amounts-calc-grid">
                    <div class="form-field-group">
                        <label class="field-label-royal">{{ __('نسبة الخصم المئوية (%)') }}</label>
                        <input type="number" step="0.5" min="0" max="100" name="custom_discount_percent" id="feeInputPercent" class="clean-input" value="{{ (float)($student->custom_discount_percent ?: 0) }}" oninput="calcNetFeePreview()">
                    </div>

                    <div class="form-field-group">
                        <label class="field-label-royal">{{ __('خصم مبلغ مقطوع (₪)') }}</label>
                        <input type="number" step="1" min="0" name="custom_discount_fixed" id="feeInputFixed" class="clean-input" value="{{ (float)($student->custom_discount_fixed ?: 0) }}" oninput="calcNetFeePreview()">
                    </div>
                </div>

                <div class="live-calc-box">
                    <div class="calc-label-row">
                        <span>{{ __('القسط الشهري الصافي بعد الخصم:') }}</span>
                        <strong class="font-mono remaining-display text-emerald" id="feeNetPreview">150 ₪/شهر</strong>
                    </div>
                </div>

                <div class="form-field-group">
                    <label class="field-label-royal">{{ __('سبب الخصم أو المنحة (ملاحظات إدارية)') }}</label>
                    <input type="text" name="discount_notes" id="feeInputNotes" class="clean-input" value="{{ $student->discount_notes ?? '' }}" placeholder="{{ __('مثال: منحة تفوق، إعفاء أبناء شهداء، خصم إخوة...') }}">
                </div>

                <div class="checkbox-box-royal">
                    <label class="custom-chk-label">
                        <input type="checkbox" name="apply_to_future_months" value="1" checked>
                        <span>{{ __('تحديث الشهور غير المسددة المتبقية تلقائياً بهذا القسط الجديد') }}</span>
                    </label>
                </div>
            </div>

            <div class="modal-footer-royal">
                <button type="button" class="btn-cancel-sub" onclick="closeStudentFeeModal()">{{ __('إلغاء') }}</button>
                <button type="submit" class="btn-save-sub" id="btnSaveFee">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('حفظ وتطبيق الخطة المالية') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 7. نافذة طباعة سند كشف الذمة المعتمد (Statement Modal) --}}
<div id="statementModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box modal-statement-sheet-wrap">
        <div class="statement-toolbar">
            <div class="tb-left">
                <button type="button" class="btn-print-action" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> {{ __('طباعة السند الرسمي') }}
                </button>
            </div>
            <button type="button" class="btn-close-x" onclick="closeStatementModal()">&times;</button>
        </div>

        <div id="statementPrintableArea" class="statement-document">
            {{-- الترويسة الوزارية الرسمية لسند كشف الحساب --}}
            <div class="doc-header">
                <div class="doc-header-col text-right">
                    <strong>{{ __('دولة فلسطين') }} 🇵🇸</strong>
                    <span>{{ __('منظومة التعليم الأكاديمي المعتمدة') }}</span>
                    <span>{{ __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')) }}</span>
                </div>
                <div class="doc-header-logo">
                    <img src="{{ asset('images/logo.png') }}" onerror="this.src='/images/logo.png'" alt="Logo" class="doc-logo-img">
                    <h2 class="doc-main-title">{{ __('سند كشف حساب وذمة مالية') }}</h2>
                    <span class="doc-badge-year">{{ __('العام الدراسي') }} {{ $year }}</span>
                </div>
                <div class="doc-header-col text-left font-mono">
                    <span><strong>{{ __('تاريخ الاستخراج:') }}</strong> {{ date('Y-m-d') }}</span>
                    <span><strong>{{ __('رقم السند:') }}</strong> PAL-STMT-{{ $student->id }}-{{ date('Ym') }}</span>
                    <span><strong>{{ __('المشرف العام:') }}</strong> م. أحمد شمالي</span>
                </div>
            </div>

            <hr class="doc-divider">

            {{-- بيانات الطالب --}}
            <div class="doc-student-info-grid">
                <div class="info-cell"><span>{{ __('اسم الطالب:') }}</span> <strong>{{ $studentDisplayName }}</strong></div>
                <div class="info-cell"><span>{{ __('رقم الهوية:') }}</span> <strong class="font-mono" dir="ltr">{{ $student->nid ?? '-' }}</strong></div>
                <div class="info-cell"><span>{{ __('المرحلة والفرع:') }}</span> <strong>{{ $stageDisplayName }}</strong></div>
                <div class="info-cell"><span>{{ __('رقم الهاتف:') }}</span> <strong class="font-mono" dir="ltr">{{ $student->phone ?? '-' }}</strong></div>
            </div>

            {{-- جدول الشهور الـ 12 المعتمد --}}
            <table class="doc-table">
                <thead>
                    <tr>
                        <th style="width: 45px;">#</th>
                        <th>{{ __('الشهر') }}</th>
                        <th>{{ __('المبلغ المستحق (₪)') }}</th>
                        <th>{{ __('المبلغ المسدد (₪)') }}</th>
                        <th>{{ __('الرصيد المتبقي (₪)') }}</th>
                        <th>{{ __('الحالة المعتمدة') }}</th>
                        <th>{{ __('البيان والملاحظات') }}</th>
                    </tr>
                </thead>
                <tbody id="statementTableBody">
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $sub = $subscriptions->firstWhere('month', $m);
                            $st = $sub ? $sub->status : 'unpaid';
                            $amt = $sub ? (float)$sub->amount : (float)$student->monthlyAmountDue();
                            $paidAmt = $sub ? (float)($sub->paid_amount ?? 0) : 0.00;
                            if ($st === 'paid' && $paidAmt <= 0) $paidAmt = $amt;
                            $remAmt = $sub ? (float)$sub->remaining_amount : ($st === 'waived' ? 0.00 : $amt);
                            $notes = $sub ? ($sub->notes ?? '-') : '-';
                            $mTitle = $monthsNames[$m] ?? "شهر $m";
                        @endphp
                        <tr>
                            <td class="font-mono text-center">{{ $m }}</td>
                            <td><strong>{{ $mTitle }}</strong></td>
                            <td class="font-mono text-center">{{ number_format($amt, 2) }} ₪</td>
                            <td class="font-mono text-center text-emerald"><strong>{{ number_format($paidAmt, 2) }} ₪</strong></td>
                            <td class="font-mono text-center {{ $remAmt > 0 ? 'text-rose font-bold' : 'text-emerald' }}">{{ number_format($remAmt, 2) }} ₪</td>
                            <td class="text-center">
                                @if($st === 'paid')
                                    <span class="sheet-status bg-p">{{ __('مسدد بالكامل') }} ✅</span>
                                @elseif($st === 'partial')
                                    <span class="sheet-status bg-part">{{ __('سداد جزئي (متبقي)') }} ⚠️</span>
                                @elseif($st === 'pending')
                                    <span class="sheet-status bg-pend">{{ __('قيد المراجعة') }} ⏳</span>
                                @elseif($st === 'waived')
                                    <span class="sheet-status bg-w">{{ __('إعفاء / منحة') }} 🏷️</span>
                                @else
                                    <span class="sheet-status bg-u">{{ __('غير مسدد') }} ❌</span>
                                @endif
                            </td>
                            <td style="font-size: 0.8rem; color: #475569;">{{ $notes }}</td>
                        </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr class="doc-totals-row">
                        <td colspan="2" class="text-left font-bold">{{ __('الإجماليات الرسمية:') }}</td>
                        <td class="font-mono text-center font-bold" id="docTotalDue">{{ number_format($studentDue, 2) }} ₪</td>
                        <td class="font-mono text-center font-bold text-emerald" id="docTotalPaid">{{ number_format($studentPaid, 2) }} ₪</td>
                        <td class="font-mono text-center font-bold {{ $studentRemaining > 0 ? 'text-rose' : 'text-emerald' }}" id="docTotalRem">{{ number_format($studentRemaining, 2) }} ₪</td>
                        <td colspan="2" class="text-center font-bold">
                            @if($studentRemaining == 0)
                                <span class="text-emerald">{{ __('مبرأة الذمة بالكامل 100%') }} ✅</span>
                            @else
                                <span class="text-rose">{{ __('متبقي بذمة الطالب') }} ⚠️</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>

            {{-- إقرار براءة الذمة وتوقيع الإدارة --}}
            <div class="doc-footer-clearance">
                <div class="clearance-notice-box">
                    <strong>{{ __('إشعار الاعتماد المالي:') }}</strong>
                    @if($studentRemaining == 0)
                        <span>{{ __('يشهد قسم الشؤون المالية والقبول في المنصة بأن الطالب المذكور أعلاه قد أوفى بكامل التزاماته المالية عن العام الدراسي (:year)، وتعتبر ذمته المالية مبرأة ومسددة بالكامل بنسبة 100% عن كافة الشهور المقررة.', ['year' => $year]) }}</span>
                    @else
                        <span>{{ __('يفيد هذا الكشف بوجود رصيد متبقي بذمة الطالب المذكور أعلاه وقدره (:rem ₪)، ويتوجب سداد الأقساط المتبقية وفقاً لتعليمات قسم الشؤون المالية والاشتراكات.', ['rem' => number_format($studentRemaining, 2)]) }}</span>
                    @endif
                </div>

                <div class="doc-signatures-row">
                    <div class="sig-block">
                        <span>{{ __('توقيع قسم الحسابات والمالية') }}</span>
                        <div class="sig-space"></div>
                    </div>
                    <div class="doc-stamp-box">
                        <div class="stamp-circle">
                            <span>{{ __('منارة التوجيهي') }}</span>
                            <small>{{ __('معتمد رسمياً') }}</small>
                            <i class="fa-solid fa-stamp"></i>
                        </div>
                    </div>
                    <div class="sig-block">
                        <span>{{ __('المشرف العام') }}</span>
                        <strong style="color: #0f172a; margin-top: 4px; display: block;">م. أحمد شمالي</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ==========================================================================
   التصميم الكلاسيكي الفاخر للواجهة المالية المستقلة للطالب
   ========================================================================== */
.student-profile-finance-wrap {
    padding: 24px 28px 60px;
    max-width: 1400px;
    margin: 0 auto;
    font-family: 'Outfit', 'Cairo', -apple-system, BlinkMacSystemFont, sans-serif;
    color: #1e293b;
}

/* 1. شريط التنقل الكلاسيكي */
.top-nav-bar-classic {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 14px 20px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}
.nav-breadcrumbs {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.88rem;
    font-weight: 600;
}
.crumb-link {
    color: #64748b;
    text-decoration: none;
    transition: color 0.2s;
}
.crumb-link:hover {
    color: #1e3a8a;
}
.crumb-sep {
    color: #cbd5e1;
}
.crumb-current {
    color: #0f172a;
    font-weight: 700;
}
.nav-actions-group {
    display: flex;
    align-items: center;
    gap: 10px;
}
.btn-classic-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    color: #334155;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-classic-outline:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.btn-classic-print {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #1e3a8a;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-classic-print:hover {
    background: #1e40af;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
}

/* 2. بطاقة الطالب الرئيسية الكلاسيكية */
.student-hero-classic-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 24px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.hero-main-details {
    display: flex;
    align-items: center;
    gap: 20px;
}
.avatar-holder {
    position: relative;
    flex-shrink: 0;
}
.student-photo-royal {
    width: 84px;
    height: 84px;
    border-radius: 20px;
    object-fit: cover;
    border: 3px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
.status-indicator-dot {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid #ffffff;
}
.status-indicator-dot.is-clear { background: #10b981; }
.status-indicator-dot.has-due { background: #f59e0b; }

.student-identity-text {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.name-badge-row {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.student-full-title {
    font-size: 1.45rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}
.stage-tag-classic {
    background: #eff6ff;
    color: #1e40af;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    border: 1px solid #dbeafe;
}
.clearance-pill-royal {
    background: #ecfdf5;
    color: #065f46;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    border: 1px solid #a7f3d0;
}
.due-pill-royal {
    background: #fffbeb;
    color: #92400e;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    border: 1px solid #fde68a;
}
.student-meta-strip {
    display: flex;
    align-items: center;
    gap: 18px;
    font-size: 0.84rem;
    color: #475569;
    flex-wrap: wrap;
}
.phone-link {
    color: #1e40af;
    text-decoration: none;
    font-weight: 600;
}

.fee-plan-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 18px;
    min-width: 250px;
}
.fee-plan-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.fee-plan-lbl {
    font-size: 0.82rem;
    font-weight: 800;
    color: #1e293b;
}
.btn-edit-fee-mini {
    background: none;
    border: none;
    color: #1e40af;
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
}
.fee-val-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.82rem;
    margin-bottom: 4px;
}
.net-due-row {
    margin-top: 8px;
    padding-top: 6px;
    border-top: 1px dashed #cbd5e1;
    font-weight: 800;
}

/* 3. شبكة المؤشرات المالية الأربعة */
.financial-kpi-grid-classic {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 18px;
    margin-bottom: 28px;
}
.kpi-card-classic {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 20px 22px;
    display: flex;
    gap: 16px;
    align-items: center;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    transition: transform 0.2s, box-shadow 0.2s;
}
.kpi-card-classic:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
}
.kpi-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-size: 1.45rem;
    flex-shrink: 0;
}
.kpi-due .kpi-icon-wrap { background: #eff6ff; color: #1e40af; }
.kpi-paid .kpi-icon-wrap { background: #ecfdf5; color: #059669; }
.kpi-remaining.is-alert .kpi-icon-wrap { background: #fff1f2; color: #e11d48; }
.kpi-remaining.is-safe .kpi-icon-wrap { background: #ecfdf5; color: #059669; }
.kpi-rate .kpi-icon-wrap { background: #f5f3ff; color: #7c3aed; }

.kpi-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 4px;
}
.kpi-num-wrap {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.kpi-denom {
    font-size: 0.95rem;
    color: #94a3b8;
}
.kpi-sub-text {
    display: block;
    font-size: 0.74rem;
    color: #64748b;
    margin-top: 4px;
}
.progress-bar-classic {
    height: 6px;
    background: #e2e8f0;
    border-radius: 6px;
    margin-top: 6px;
    overflow: hidden;
}
.progress-fill-classic {
    height: 100%;
    background: #7c3aed;
    border-radius: 6px;
    transition: width 0.4s ease;
}

/* 4. لوحة استعراض الشهور الـ 12 */
.months-control-section-classic {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 26px 28px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
}
.section-classic-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 18px;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.sec-title {
    font-size: 1.2rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
}
.sec-desc {
    font-size: 0.82rem;
    color: #64748b;
    margin: 0;
}
.student-fast-navigator {
    display: flex;
    align-items: center;
    gap: 8px;
}
.nav-title-lbl {
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
}
.btn-nav-step {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #334155;
    text-decoration: none;
}
.btn-nav-step:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.select-fast-student {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #1e293b;
    max-width: 220px;
}

/* شبكة بطاقات الشهور الـ 12 */
.months-cards-grid-classic {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
    gap: 20px;
}
.month-card-classic {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s, box-shadow 0.2s;
    position: relative;
    overflow: hidden;
}
.month-card-classic:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
}
.month-card-classic.status-border-paid { border-top: 4px solid #10b981; }
.month-card-classic.status-border-partial { border-top: 4px solid #f59e0b; }
.month-card-classic.status-border-pending { border-top: 4px solid #6366f1; }
.month-card-classic.status-border-waived { border-top: 4px solid #8b5cf6; }
.month-card-classic.status-border-unpaid { border-top: 4px solid #e2e8f0; }

.card-head-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
}
.month-identity {
    display: flex;
    align-items: center;
    gap: 8px;
}
.month-number-circle {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #1e3a8a;
    font-size: 0.84rem;
    font-weight: 800;
    display: grid;
    place-items: center;
}
.month-name-text {
    font-size: 0.96rem;
    font-weight: 800;
    color: #0f172a;
}
.status-badge-classic {
    font-size: 0.74rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
}
.status-badge-classic.badge-paid { background: #dcfce7; color: #166534; }
.status-badge-classic.badge-partial { background: #fef3c7; color: #92400e; }
.status-badge-classic.badge-pending { background: #e0e7ff; color: #3730a3; }
.status-badge-classic.badge-waived { background: #f3e8ff; color: #6b21a8; }
.status-badge-classic.badge-unpaid { background: #f1f5f9; color: #64748b; }

.card-financial-figures {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 10px;
    padding: 10px;
    margin-bottom: 12px;
    text-align: center;
}
.fig-lbl {
    display: block;
    font-size: 0.7rem;
    color: #64748b;
    margin-bottom: 2px;
}
.fig-val {
    font-size: 0.86rem;
    font-weight: 700;
    color: #1e293b;
}

.card-notes-preview {
    font-size: 0.76rem;
    color: #475569;
    margin-bottom: 14px;
    min-height: 38px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 3px;
}
.date-stamp-row {
    font-size: 0.72rem;
    color: #64748b;
}
.note-snippet {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.btn-manage-month-action {
    width: 100%;
    padding: 9px 12px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #1e293b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}
.btn-manage-month-action:hover {
    background: #1e3a8a;
    color: #ffffff;
    border-color: #1e3a8a;
}

/* ==========================================================================
   مودال وسند كشف الحساب المعتمد
   ========================================================================== */
.modal-fee-box {
    max-width: 500px;
}
.checkbox-box-royal {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    margin-top: 10px;
}
.custom-chk-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #1e293b;
    cursor: pointer;
}

.modal-statement-sheet-wrap {
    max-width: 880px;
    max-height: 92vh;
    padding: 0;
    background: #ffffff;
    display: flex;
    flex-direction: column;
}
.statement-toolbar {
    background: #1e293b;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 20px 20px 0 0;
}
.btn-print-action {
    background: #10b981;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 0.85rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.statement-document {
    padding: 32px 36px;
    overflow-y: auto;
}
.doc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}
.doc-header-col {
    display: flex;
    flex-direction: column;
    gap: 2px;
    font-size: 0.78rem;
    color: #475569;
}
.doc-logo-img {
    height: 48px;
    display: block;
    margin: 0 auto 6px;
}
.doc-main-title {
    font-size: 1.35rem;
    font-weight: 900;
    color: #0f172a;
    margin: 0;
    text-align: center;
}
.doc-badge-year {
    font-size: 0.78rem;
    background: #f1f5f9;
    padding: 2px 8px;
    border-radius: 6px;
    color: #475569;
    font-weight: 600;
    display: table;
    margin: 4px auto 0;
}
.doc-divider {
    border: none;
    border-top: 2px solid #0f172a;
    margin: 12px 0 16px;
}
.doc-student-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 0.82rem;
    margin-bottom: 18px;
}
.info-cell span {
    color: #64748b;
    margin-left: 6px;
}
.doc-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
    margin-bottom: 20px;
}
.doc-table th, .doc-table td {
    border: 1px solid #cbd5e1;
    padding: 7px 10px;
}
.doc-table th {
    background: #f1f5f9;
    font-weight: 800;
    color: #0f172a;
    text-align: center;
}
.doc-totals-row {
    background: #f8fafc;
    font-weight: 800;
}
.sheet-status {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    display: inline-block;
}
.bg-p { background: #dcfce7; color: #166534; }
.bg-part { background: #fef3c7; color: #92400e; }
.bg-pend { background: #e0e7ff; color: #3730a3; }
.bg-w { background: #f3e8ff; color: #6b21a8; }
.bg-u { background: #f1f5f9; color: #64748b; }

.clearance-notice-box {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 0.8rem;
    color: #334155;
    margin-bottom: 24px;
    line-height: 1.6;
}
.doc-signatures-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
}
.sig-block {
    text-align: center;
    font-size: 0.82rem;
    font-weight: 700;
    color: #475569;
}
.sig-space {
    height: 45px;
}
.stamp-circle {
    width: 76px;
    height: 76px;
    border: 2px dashed #059669;
    border-radius: 50%;
    color: #059669;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 0.68rem;
    font-weight: 800;
    transform: rotate(-8deg);
}

@media print {
    body * {
        visibility: hidden;
    }
    #statementModal, #statementModal * {
        visibility: visible;
    }
    #statementModal {
        position: absolute;
        inset: 0;
        display: block !important;
        background: transparent !important;
        padding: 0 !important;
    }
    .modal-statement-sheet-wrap {
        box-shadow: none !important;
        border: none !important;
        max-width: 100% !important;
    }
    .statement-toolbar {
        display: none !important;
    }
    .statement-document {
        padding: 0 !important;
    }
}
</style>

<script>
// فتح نافذة تعديل الشهر المحدد
function openMonthModalFromEl(btn) {
    const d = btn.dataset;
    document.getElementById('formStudentId').value = d.studentId;
    document.getElementById('formMonth').value = d.month;
    document.getElementById('modalStudentName').innerText = d.studentName;
    document.getElementById('modalMonthTitle').innerText = d.monthTitle + ' ({{ $year }})';

    const amt = parseFloat(d.amount) || parseFloat(d.studentFee) || 150;
    const paid = parseFloat(d.paidAmount) || 0;
    const st = d.status || 'unpaid';

    document.getElementById('formAmount').value = amt.toFixed(2);
    document.getElementById('formPaidAmount').value = paid.toFixed(2);
    document.getElementById('formNotes').value = d.notes || '';

    // اختيار زر الراديو المناسب
    const radio = document.getElementById('st_' + st);
    if (radio) radio.checked = true;

    calcRemainingRealtime();

    const modal = document.getElementById('editMonthModal');
    modal.style.display = 'flex';
}

function closeMonthModal() {
    document.getElementById('editMonthModal').style.display = 'none';
}

// تغيير الراديو وضبط المبالغ تلقائياً
function onStatusRadioChange(status) {
    const amtInput = document.getElementById('formAmount');
    const paidInput = document.getElementById('formPaidAmount');
    let amt = parseFloat(amtInput.value) || 0;

    if (status === 'paid') {
        if (amt === 0) amt = {{ (float)$student->monthlyAmountDue() }};
        amtInput.value = amt.toFixed(2);
        paidInput.value = amt.toFixed(2);
    } else if (status === 'unpaid') {
        paidInput.value = '0.00';
    } else if (status === 'waived') {
        amtInput.value = '0.00';
        paidInput.value = '0.00';
    } else if (status === 'partial') {
        if (parseFloat(paidInput.value) <= 0 || parseFloat(paidInput.value) >= amt) {
            paidInput.value = (amt / 2).toFixed(2);
        }
    }
    calcRemainingRealtime();
}

// حساب المتبقي التفاعلي
function calcRemainingRealtime() {
    const amtInput = document.getElementById('formAmount');
    const paidInput = document.getElementById('formPaidAmount');
    const remPreview = document.getElementById('formRemainingPreview');
    const statusNotice = document.getElementById('formStatusNotice');

    const amt = parseFloat(amtInput.value) || 0;
    const paid = parseFloat(paidInput.value) || 0;
    const remaining = Math.max(0, amt - paid);

    remPreview.innerText = remaining.toFixed(2) + ' ₪';

    if (paid >= amt && amt > 0) {
        statusNotice.className = 'calc-status-indicator is-paid';
        statusNotice.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>{{ __('مسدد بالكامل رسمياً ✅ (الرصيد المتبقي: 0.00 ₪)') }}</span>';
        const r = document.getElementById('st_paid');
        if (r) r.checked = true;
    } else if (paid > 0 && paid < amt) {
        statusNotice.className = 'calc-status-indicator is-partial';
        statusNotice.innerHTML = '<i class="fa-solid fa-circle-half-stroke"></i> <span>{{ __('سداد جزئي ⚠️ (الرصيد المتبقي: ') }}' + remaining.toFixed(2) + ' ₪)</span>';
        const r = document.getElementById('st_partial');
        if (r) r.checked = true;
    } else if (paid === 0 && amt > 0) {
        statusNotice.className = 'calc-status-indicator is-unpaid';
        statusNotice.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> <span>{{ __('غير مسدد ❌ (إجمالي المستحق: ') }}' + amt.toFixed(2) + ' ₪)</span>';
        const r = document.getElementById('st_unpaid');
        if (r) r.checked = true;
    }
}

// أزرار المبالغ السريعة
function setPresetPaid(type) {
    const amtInput = document.getElementById('formAmount');
    const paidInput = document.getElementById('formPaidAmount');
    const amt = parseFloat(amtInput.value) || {{ (float)$student->monthlyAmountDue() }};
    amtInput.value = amt.toFixed(2);

    if (type === 'full') {
        paidInput.value = amt.toFixed(2);
    } else if (type === 'half') {
        paidInput.value = (amt / 2).toFixed(2);
    } else if (type === 'zero') {
        paidInput.value = '0.00';
    }
    calcRemainingRealtime();
}

// حفظ بيانات الشهر عبر AJAX وتحديث الصفحة فورياً
function submitMonthForm(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSaveSub');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}';

    const form = document.getElementById('editMonthForm');
    const formData = new FormData(form);

    fetch("{{ route('admin.subscriptions.monthly.update') }}", {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>{{ __('حفظ واعتماد التحديث') }}</span>';

        if (data.success) {
            closeMonthModal();
            // تحديث بطاقة الشهر المستهدف فورياً في الصفحة
            const m = formData.get('month');
            const card = document.getElementById('month_card_' + m);
            if (card) {
                // تحديث كلاسات الحالة
                card.className = 'month-card-classic status-border-' + data.status;
                const amtEl = document.getElementById('card_amt_' + m);
                const paidEl = document.getElementById('card_paid_' + m);
                const remEl = document.getElementById('card_rem_' + m);
                const badgeEl = document.getElementById('status_badge_' + m);

                if (amtEl) amtEl.innerText = parseFloat(data.amount).toFixed(2) + ' ₪';
                if (paidEl) paidEl.innerText = parseFloat(data.paid_amount).toFixed(2) + ' ₪';
                if (remEl) {
                    remEl.innerText = parseFloat(data.remaining_amount).toFixed(2) + ' ₪';
                    remEl.className = 'fig-val font-mono ' + (data.remaining_amount > 0 ? 'text-rose font-bold' : 'text-emerald');
                }
                if (badgeEl) {
                    badgeEl.className = 'status-badge-classic badge-' + data.status;
                    let text = 'غير مسدد';
                    if (data.status === 'paid') text = 'مسدد بالكامل';
                    else if (data.status === 'partial') text = 'سداد جزئي';
                    else if (data.status === 'pending') text = 'قيد المراجعة';
                    else if (data.status === 'waived') text = 'إعفاء / منحة';
                    badgeEl.innerText = text;
                }

                // تحديث خصائص زر التعديل
                const manageBtn = card.querySelector('.btn-manage-month-action');
                if (manageBtn) {
                    manageBtn.dataset.status = data.status;
                    manageBtn.dataset.amount = data.amount;
                    manageBtn.dataset.paidAmount = data.paid_amount;
                    manageBtn.dataset.remainingAmount = data.remaining_amount;
                    manageBtn.dataset.notes = formData.get('notes') || '';
                }
            }

            // تحديث المؤشرات الكبرى عبر إعادة تحميل خفيف
            setTimeout(() => {
                window.location.reload();
            }, 300);
        } else {
            alert(data.message || 'حدث خطأ أثناء الحفظ.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>{{ __('حفظ واعتماد التحديث') }}</span>';
        alert('تعذر الاتصال بالخادم، يرجى المحاولة ثانية.');
    });
}

// نافذة تعديل الرسوم الفردية
function openStudentFeeModal() {
    calcNetFeePreview();
    document.getElementById('studentFeeModal').style.display = 'flex';
}
function closeStudentFeeModal() {
    document.getElementById('studentFeeModal').style.display = 'none';
}

function calcNetFeePreview() {
    const base = parseFloat(document.getElementById('feeInputMonthly').value) || 0;
    const pct = parseFloat(document.getElementById('feeInputPercent').value) || 0;
    const fix = parseFloat(document.getElementById('feeInputFixed').value) || 0;

    let net = base;
    if (pct > 0) net = net * (1 - (pct / 100));
    if (fix > 0) net = net - fix;
    net = Math.max(0, net);

    document.getElementById('feeNetPreview').innerText = Math.round(net) + ' ₪/شهر';
}

function submitStudentFeeForm(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSaveFee');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}';

    const form = document.getElementById('studentFeeForm');
    const formData = new FormData(form);

    fetch("{{ route('admin.subscriptions.monthly.updateStudentFee') }}", {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>{{ __('حفظ وتطبيق الخطة المالية') }}</span>';
        if (data.success) {
            closeStudentFeeModal();
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء الحفظ.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>{{ __('حفظ وتطبيق الخطة المالية') }}</span>';
        alert('تعذر الاتصال بالخادم.');
    });
}

// نافذة كشف الذمة
function openStatementModal() {
    document.getElementById('statementModal').style.display = 'flex';
}
function closeStatementModal() {
    document.getElementById('statementModal').style.display = 'none';
}
</script>
@endsection
