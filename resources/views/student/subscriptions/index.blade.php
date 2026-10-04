@extends('layouts.app')

@section('title', __('سجل الاشتراكات والرسوم الفصلية | Step by Step'))

@section('content')
<div class="student-subs-container">
    {{-- هيدر الصفحة الأكاديمي المطور --}}
    <div class="student-subs-header">
        <div class="header-text-block">
            <div class="subs-badge">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>{{ __('نظام الاشتراكات والرسوم الفصلية المعتمد') }}</span>
            </div>
            <h1 class="subs-title">{{ __('سجل الاشتراكات والرسوم الفصلية للمقررات') }} ({{ $year }})</h1>
            <p class="subs-subtitle">
                {{ __('أهلاً بك يا :name! يتم احتساب الرسوم في المنصة وفق نظام الفصول الدراسية (فصل أول / فصل ثاني) لكل مادة مسجلة دون أي أقساط شهرية.', ['name' => $student->name_ar ?? $student->name]) }}
            </p>
        </div>
        <div class="header-action-block">
            <a href="{{ route('student.pendingPayment.show') }}" class="btn-pay-new-month">
                <i class="fa-solid fa-receipt"></i> {{ __('رفع إشعار سداد جديد') }}
            </a>
        </div>
    </div>

    {{-- شريط تحديد المنطقة والتسعيرة الإقليمية المطبقة --}}
    <div class="regional-pricing-alert-banner">
        <div class="alert-content-wrap">
            <div class="region-flag-box">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div>
                <strong class="region-title">
                    {{ __('التسعيرة المعتمدة لحسابك:') }} 
                    <span class="region-badge-pill">{{ $student->region_label }}</span>
                </strong>
                <p class="region-desc">
                    {{ __('رسوم المقررات محددة ومخصصة بحسب منطقتك التعليمية (:region). الرسوم تدفع فصلياً وتتيح لك الوصول الكامل لكافة دروس وشروحات وبنوك أسئلة الفصل المعتمد.', ['region' => $student->region_label]) }}
                </p>
            </div>
        </div>
        <div class="academic-system-tag">
            <i class="fa-solid fa-calendar-week"></i>
            <span>{{ __('نظام فصلي حصراً (Term-Based)') }}</span>
        </div>
    </div>

    {{-- بطاقات الملخص المالي الأكاديمية للطالب (المستحق / المسدد / الرصيد المتبقي) --}}
    <div class="stats-row-clean">
        <div class="stat-card-clean" style="--card-accent: #1e3a8a;">
            <span class="stat-label">{{ __('إجمالي الرسوم المطلوبة') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-navy">{{ number_format($totalDueAmount, 2) }} ₪</span>
                <i class="fa-solid fa-file-invoice-dollar stat-icon text-navy"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">{{ __('إجمالي الرسوم الفصلية المقررة لموادك') }}</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #059669;">
            <span class="stat-label">{{ __('المبلغ المسدد المعتمد') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-emerald">{{ number_format($totalPaidAmount, 2) }} ₪</span>
                <i class="fa-solid fa-circle-check stat-icon text-emerald"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">{{ __('المبالغ المقبوضة والمعتمدة رسمياً بسجلات المنصة') }}</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #dc2626;">
            <span class="stat-label">{{ __('الرصيد المتبقي بذمتك') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number {{ ($totalRemainingAmount ?? 0) > 0 ? 'text-rose' : 'text-emerald' }}">
                    {{ number_format($totalRemainingAmount ?? 0, 2) }} ₪
                </span>
                <i class="fa-solid {{ ($totalRemainingAmount ?? 0) > 0 ? 'fa-triangle-exclamation text-rose' : 'fa-badge-check text-emerald' }} stat-icon"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">
                @if(($totalRemainingAmount ?? 0) > 0)
                    {{ __('متبقي بذمتك مطلوب استكمال سداده') }}
                @else
                    {{ __('ذمتك المالية مسددة بالكامل ومبرأة ✅') }}
                @endif
            </small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #6366f1;">
            <span class="stat-label">{{ __('المواد المعتمدة والمسددة') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-indigo">{{ $paidCount }} <small style="font-size: 0.85rem; color: #64748b;">/ {{ max(1, count($semesterSubscriptions ?? [])) }}</small></span>
                <i class="fa-solid fa-book-bookmark stat-icon text-indigo"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">
                @if($partialCount > 0)
                    {{ __(':count مواد دفع جزئي', ['count' => $partialCount]) }} • 
                @endif
                {{ __('الاشتراك مفعل في المساقات المعتمدة') }}
            </small>
        </div>
    </div>

    {{-- كشف الموقف المالي الفوري والأقساط المستحقة --}}
    @if(($totalRemainingAmount ?? 0) > 0)
        <div class="active-due-summary-card has-due">
            <div class="due-card-header">
                <div class="due-badge-pill">
                    <i class="fa-solid fa-bell text-rose"></i>
                    <span>{{ __('الموقف المالي: يوجد رصيد متبقي مستحق السداد لموادك الفصلية') }}</span>
                </div>
                <div class="due-month-tag">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>{{ __('مستحق السداد') }}</span>
                </div>
            </div>

            <div class="due-figures-grid">
                <div class="due-fig-item">
                    <span class="fig-label">{{ __('إجمالي المطلوب سداده الآن:') }}</span>
                    <strong class="fig-amt text-rose font-mono">{{ number_format($totalRemainingAmount, 2) }} ₪</strong>
                    <small class="fig-sub">{{ __('الرصيد المتبقي لتسوية وتفعيل اشتراكاتك بالكامل') }}</small>
                </div>

                <div class="due-fig-item grand-due-item">
                    <span class="fig-label">{{ __('الإجراء المطلوب:') }}</span>
                    <a href="{{ route('student.pendingPayment.show', ['amount' => $totalRemainingAmount, 'type' => 'due']) }}" class="btn-pay-now-action">
                        <i class="fa-solid fa-receipt"></i>
                        <span>{{ __('سداد المستحق الآن (:amt ₪)', ['amt' => number_format($totalRemainingAmount, 0)]) }}</span>
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="active-due-summary-card all-clear">
            <div class="due-card-header">
                <div class="due-badge-pill">
                    <i class="fa-solid fa-circle-check text-emerald"></i>
                    <span>{{ __('الموقف المالي: كافة الرسوم الفصلية لموادك مسددة ومعتمدة بنجاح ✅') }}</span>
                </div>
                <div class="due-month-tag">
                    <i class="fa-solid fa-shield-check"></i>
                    <span>{{ __('الحساب سليم ومبرأ') }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- شبكة بطاقات المواد والاشتراكات الفصلية --}}
    <div class="subs-grid-wrapper" style="margin-top: 24px;">
        <div class="grid-section-header">
            <div class="sec-title-block">
                <i class="fa-solid fa-layer-group text-primary"></i>
                <div>
                    <h2 class="sec-title">{{ __('تفاصيل الاشتراكات والرسوم الفصلية حسب المواد') }}</h2>
                    <p class="sec-desc">{{ __('جدول يوضح حالة كل مادة مشترَك بها، الفصل الدراسي، الرسم الإقليمي، والمدفوع والمتبقي.') }}</p>
                </div>
            </div>
        </div>

        <div class="semester-cards-grid">
            @forelse($semesterSubscriptions ?? [] as $sub)
                @php
                    $isPaid = $sub->status === 'paid';
                    $isPartial = $sub->status === 'partial';
                    $isPending = $sub->status === 'pending';
                    $isWaived = $sub->status === 'waived';
                    $paidAmt = (float)($sub->paid_amount ?? 0);
                    if ($isPaid && $paidAmt <= 0) $paidAmt = (float)$sub->amount;
                    $remAmt = max(0, (float)$sub->remaining_amount);
                    $subName = optional($sub->subject)->name_ar ?? optional($sub->subject)->name ?? __('مادة دراسية');
                    $subStage = optional(optional($sub->subject)->stage)->label_ar ?? optional(optional($sub->subject)->stage)->name_ar ?? __('توجيهي');
                    $themeColor = optional($sub->subject)->color ?? '#1e3a8a';
                @endphp

                <div class="semester-sub-card status-{{ $sub->status }}">
                    <div class="card-top-head">
                        <div class="card-icon-title">
                            <div class="sub-icon-sq" style="color: {{ $themeColor }}; background: {{ $themeColor }}15; border: 1px solid {{ $themeColor }}30;">
                                <i class="fa-solid {{ optional($sub->subject)->icon ?? 'fa-book-open' }}"></i>
                            </div>
                            <div>
                                <h3 class="subject-title">{{ $subName }}</h3>
                                <span class="subject-stage-badge">{{ $subStage }}</span>
                            </div>
                        </div>

                        <!-- شارة الفصل الدراسي المعتمد -->
                        <span class="semester-pill-badge">
                            <i class="fa-solid fa-calendar-day"></i>
                            {{ $sub->semester_label }}
                        </span>
                    </div>

                    <div class="card-pricing-block">
                        <div class="pricing-row">
                            <span class="pr-label">{{ __('التسعيرة المطبقة:') }}</span>
                            <span class="pr-val region-tag">{{ $student->region_label }}</span>
                        </div>
                        <div class="pricing-row">
                            <span class="pr-label">{{ __('الرسم الفصلي المقرر:') }}</span>
                            <strong class="pr-val font-mono">{{ number_format($sub->amount, 2) }} ₪</strong>
                        </div>
                        <div class="pricing-row text-emerald">
                            <span class="pr-label">{{ __('المبلغ المسدد:') }}</span>
                            <strong class="pr-val font-mono">{{ number_format($paidAmt, 2) }} ₪</strong>
                        </div>
                        <div class="pricing-row {{ $remAmt > 0 ? 'text-rose' : 'text-emerald' }}">
                            <span class="pr-label">{{ __('الرصيد المتبقي:') }}</span>
                            <strong class="pr-val font-mono">{{ number_format($remAmt, 2) }} ₪</strong>
                        </div>

                        @if($sub->notes)
                            <div class="sub-notes-row">
                                <i class="fa-regular fa-note-sticky"></i> {{ $sub->notes }}
                            </div>
                        @endif
                    </div>

                    <div class="card-footer-strip">
                        @if($isPaid)
                            <div class="status-btn-box status-done">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>{{ __('مسدد ومعتمد بالكامل') }}</span>
                            </div>
                            @if($sub->subject_id)
                                <a href="{{ route('student.subjects.show', $sub->subject_id) }}" class="btn-enter-course">
                                    <span>{{ __('دخول المادة') }}</span>
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                            @endif
                        @elseif($isWaived)
                            <div class="status-btn-box status-waived">
                                <i class="fa-solid fa-gift"></i>
                                <span>{{ __('إعفاء ومنحة كاملة ✨') }}</span>
                            </div>
                        @elseif($isPending)
                            <div class="status-btn-box status-pending">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span>{{ __('قيد المراجعة والاعتماد لدى الإدارة') }}</span>
                            </div>
                        @else
                            <div class="unpaid-actions-cluster">
                                <a href="{{ route('student.pendingPayment.show', ['amount' => $remAmt > 0 ? $remAmt : $sub->amount, 'type' => 'semester', 'subject_id' => $sub->subject_id]) }}" class="btn-card-pay">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span>{{ $isPartial ? __('سداد المتبقي') : __('سداد رسوم المادة') }} ({{ number_format($remAmt > 0 ? $remAmt : $sub->amount, 0) }} ₪)</span>
                                </a>
                                @php
                                    $waMsg = urlencode("مرحباً إدارة المنصة، أود الاستفسار وسداد رسوم مادة ({$subName}) - ({$sub->semester_label}) بمبلغ " . number_format($remAmt > 0 ? $remAmt : $sub->amount, 0) . " ₪ لحساب الطالب: " . ($student->name_ar ?? $student->name));
                                @endphp
                                <a href="https://wa.me/970597694385?text={{ $waMsg }}" target="_blank" class="btn-card-whatsapp" title="{{ __('تواصل عبر واتساب') }}">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-subs-placeholder">
                    <i class="fa-solid fa-folder-open"></i>
                    <h3>{{ __('لا توجد مواد مقيدة بحسابك حالياً') }}</h3>
                    <p>{{ __('يمكنك تصفح دليل المقررات واختيار المواد والفصول التي ترغب بالدراسة فيها.') }}</p>
                    <a href="{{ route('student.courses.catalog') }}" class="btn-go-catalog">
                        <i class="fa-solid fa-book-open"></i> {{ __('استعراض دليل المقررات') }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- جدول تفصيلي منظم للاشتراكات الفصلية --}}
    @if(!empty($semesterSubscriptions) && count($semesterSubscriptions) > 0)
        <div class="table-card-clean" style="margin-top: 24px;">
            <div class="table-card-header">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-table-list" style="color: #1e3a8a;"></i>
                    <strong style="font-size: 0.95rem; color: #0f172a;">{{ __('كشف الحساب الفصلي المعتمد للمواد المسجلة') }}</strong>
                </div>
                <span style="font-size: 0.78rem; color: #64748b;">
                    {{ __('نظام فصلي (Term 1 / Term 2) - تسعيرة :region', ['region' => $student->region_label]) }}
                </span>
            </div>
            <div class="table-container-clean">
                <table class="data-table-clean">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">#</th>
                            <th>{{ __('المادة الدراسية') }}</th>
                            <th style="width: 140px;">{{ __('الفصل الدراسي') }}</th>
                            <th style="width: 120px;">{{ __('التسعيرة المعتمدة') }}</th>
                            <th style="width: 110px;">{{ __('الرسم المقرر') }}</th>
                            <th style="width: 110px;">{{ __('المسدد') }}</th>
                            <th style="width: 110px;">{{ __('المتبقي') }}</th>
                            <th style="width: 130px; text-align: center;">{{ __('الحالة') }}</th>
                            <th style="width: 130px; text-align: center;">{{ __('الإجراء') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($semesterSubscriptions as $idx => $sub)
                            @php
                                $isPaid = $sub->status === 'paid';
                                $isPartial = $sub->status === 'partial';
                                $isPending = $sub->status === 'pending';
                                $isWaived = $sub->status === 'waived';
                                $paidAmt = (float)($sub->paid_amount ?? 0);
                                if ($isPaid && $paidAmt <= 0) $paidAmt = (float)$sub->amount;
                                $remAmt = max(0, (float)$sub->remaining_amount);
                                $subName = optional($sub->subject)->name_ar ?? optional($sub->subject)->name ?? __('مادة');
                            @endphp
                            <tr>
                                <td style="text-align: center; color: #94a3b8; font-family: monospace; font-size: 0.8rem; font-weight: 700;">
                                    {{ $idx + 1 }}
                                </td>
                                <td>
                                    <strong>{{ $subName }}</strong>
                                </td>
                                <td>
                                    <span class="badge-sem-tbl">{{ $sub->semester_label }}</span>
                                </td>
                                <td>
                                    <span class="badge-reg-tbl">{{ $student->region_label }}</span>
                                </td>
                                <td style="font-family: monospace; font-weight: 700; color: #0f172a;">
                                    {{ number_format($sub->amount, 2) }} ₪
                                </td>
                                <td style="font-family: monospace; font-weight: 700; color: #059669;">
                                    {{ number_format($paidAmt, 2) }} ₪
                                </td>
                                <td style="font-family: monospace; font-weight: 700; color: {{ $remAmt > 0 ? '#dc2626' : '#059669' }};">
                                    {{ number_format($remAmt, 2) }} ₪
                                </td>
                                <td style="text-align: center;">
                                    @if($isPaid)
                                        <span class="status-pill status-active"><span class="dot"></span> {{ __('مسدد بالكامل') }}</span>
                                    @elseif($isPartial)
                                        <span class="status-pill" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;"><span class="dot" style="background: #b45309;"></span> {{ __('دفع جزئي') }}</span>
                                    @elseif($isPending)
                                        <span class="status-pill status-pending"><span class="dot"></span> {{ __('قيد المراجعة') }}</span>
                                    @elseif($isWaived)
                                        <span class="status-pill status-info"><span class="dot"></span> {{ __('إعفاء / منحة') }}</span>
                                    @else
                                        <span class="status-pill status-frozen"><span class="dot"></span> {{ __('مستحق') }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($remAmt > 0)
                                        <a href="{{ route('student.pendingPayment.show', ['amount' => $remAmt, 'type' => 'semester', 'subject_id' => $sub->subject_id]) }}" class="tbl-btn" style="background: #1e3a8a;">
                                            <i class="fa-solid fa-receipt"></i> {{ __('سداد') }}
                                        </a>
                                    @else
                                        <span style="color: #16a34a; font-size: 0.8rem; font-weight: 700;">
                                            <i class="fa-solid fa-check-double"></i> {{ __('معتمد') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<style>
    .student-subs-container {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        padding-bottom: 60px;
    }

    /* هيدر الصفحة */
    .student-subs-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-top: 4px solid #1e3a8a;
        border-radius: 12px;
        padding: 22px 28px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .subs-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eff6ff;
        color: #1e3a8a;
        border: 1px solid #bfdbfe;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .subs-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .subs-subtitle {
        font-size: 0.88rem;
        color: #64748b;
        margin: 0;
        max-width: 700px;
        line-height: 1.6;
    }

    .btn-pay-new-month {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #1e3a8a;
        color: #ffffff !important;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        transition: 0.2s ease;
    }

    .btn-pay-new-month:hover {
        background: #172554;
    }

    /* شريط التسعيرة الإقليمية */
    .regional-pricing-alert-banner {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-right: 4px solid #0284c7;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }

    .alert-content-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .region-flag-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #e0f2fe;
        color: #0284c7;
        display: grid;
        place-items: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .region-title {
        font-size: 0.94rem;
        color: #0f172a;
        display: block;
        margin-bottom: 3px;
    }

    .region-badge-pill {
        background: #0284c7;
        color: #ffffff;
        font-size: 0.76rem;
        padding: 2px 10px;
        border-radius: 20px;
        font-weight: 700;
        margin-right: 6px;
    }

    .region-desc {
        font-size: 0.82rem;
        color: #64748b;
        margin: 0;
        line-height: 1.5;
    }

    .academic-system-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #475569;
    }

    /* بطاقات الإحصاءات */
    .stats-row-clean {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .stat-card-clean {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        border-top: 3px solid var(--card-accent, #1e3a8a);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
    }

    .stat-label {
        font-size: 0.82rem;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .stat-value-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-number {
        font-size: 1.45rem;
        font-weight: 800;
        font-family: monospace;
    }

    .stat-icon {
        font-size: 1.3rem;
        opacity: 0.85;
    }

    .text-navy { color: #1e3a8a; }
    .text-emerald { color: #059669; }
    .text-rose { color: #dc2626; }
    .text-indigo { color: #4f46e5; }

    /* كرت الموقف المالي الفوري */
    .active-due-summary-card {
        border-radius: 10px;
        padding: 18px 22px;
        margin-bottom: 20px;
    }

    .active-due-summary-card.has-due {
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .active-due-summary-card.all-clear {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    .due-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .due-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        font-weight: 700;
        color: #0f172a;
    }

    .due-month-tag {
        font-size: 0.76rem;
        font-weight: 700;
        background: #ffffff;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .due-figures-grid {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }

    .fig-label {
        font-size: 0.82rem;
        color: #64748b;
        display: block;
        margin-bottom: 2px;
    }

    .fig-amt {
        font-size: 1.35rem;
        display: block;
    }

    .fig-sub {
        font-size: 0.74rem;
        color: #64748b;
    }

    .btn-pay-now-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #dc2626;
        color: #ffffff !important;
        text-decoration: none;
        padding: 10px 22px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.88rem;
        transition: 0.2s;
    }

    .btn-pay-now-action:hover {
        background: #b91c1c;
    }

    /* شبكة بطاقات المواد الفصلية */
    .grid-section-header {
        margin-bottom: 14px;
    }

    .sec-title-block {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sec-title {
        font-size: 1.12rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 2px;
    }

    .sec-desc {
        font-size: 0.82rem;
        color: #64748b;
        margin: 0;
    }

    .semester-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 16px;
    }

    .semester-sub-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .semester-sub-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
    }

    .card-top-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 12px;
    }

    .card-icon-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sub-icon-sq {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .subject-title {
        font-size: 1.02rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 2px;
    }

    .subject-stage-badge {
        font-size: 0.72rem;
        color: #64748b;
    }

    .semester-pill-badge {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-size: 0.74rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .card-pricing-block {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 16px;
    }

    .pricing-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
    }

    .pr-label {
        color: #64748b;
    }

    .region-tag {
        background: #f1f5f9;
        color: #475569;
        font-size: 0.74rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .sub-notes-row {
        background: #f8fafc;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 4px;
    }

    .card-footer-strip {
        border-top: 1px dashed #e2e8f0;
        padding-top: 12px;
    }

    .status-btn-box {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .status-done {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .status-waived {
        background: #faf5ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
    }

    .status-pending {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .btn-enter-course {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        margin-top: 6px;
        background: #1e3a8a;
        color: #ffffff !important;
        text-decoration: none;
        padding: 7px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        transition: 0.2s;
    }

    .btn-enter-course:hover {
        background: #172554;
    }

    .unpaid-actions-cluster {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-card-pay {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: #1e3a8a;
        color: #ffffff !important;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        transition: 0.2s;
    }

    .btn-card-pay:hover {
        background: #172554;
    }

    .btn-card-whatsapp {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        background: #16a34a;
        color: #ffffff !important;
        display: grid;
        place-items: center;
        font-size: 1.1rem;
        text-decoration: none;
        flex-shrink: 0;
        transition: 0.2s;
    }

    .btn-card-whatsapp:hover {
        background: #15803d;
    }

    .empty-subs-placeholder {
        grid-column: 1 / -1;
        background: #ffffff;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        color: #64748b;
    }

    .empty-subs-placeholder i {
        font-size: 2.2rem;
        color: #94a3b8;
        margin-bottom: 12px;
    }

    .empty-subs-placeholder h3 {
        font-size: 1.15rem;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .btn-go-catalog {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 14px;
        background: #1e3a8a;
        color: #ffffff !important;
        text-decoration: none;
        padding: 9px 18px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.84rem;
    }

    /* جدول الكشف */
    .table-card-clean {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .table-card-header {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .table-container-clean {
        width: 100%;
        overflow-x: auto;
    }

    .data-table-clean {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.86rem;
    }

    .data-table-clean th {
        background: #ffffff;
        padding: 12px 14px;
        font-weight: 700;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
        text-align: right;
    }

    .data-table-clean td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .data-table-clean tbody tr:hover {
        background: #f8fafc;
    }

    .badge-sem-tbl {
        background: #eff6ff;
        color: #1d4ed8;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.74rem;
        font-weight: 700;
    }

    .badge-reg-tbl {
        background: #f1f5f9;
        color: #475569;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.74rem;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.74rem;
        font-weight: 700;
    }

    .status-pill .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-active { background: #dcfce7; color: #166534; }
    .status-active .dot { background: #16a34a; }

    .status-pending { background: #fef3c7; color: #92400e; }
    .status-pending .dot { background: #d97706; }

    .status-info { background: #f3e8ff; color: #6b21a8; }
    .status-info .dot { background: #9333ea; }

    .status-frozen { background: #fee2e2; color: #991b1b; }
    .status-frozen .dot { background: #dc2626; }

    .tbl-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #ffffff !important;
        text-decoration: none;
        padding: 4px 10px;
        border-radius: 5px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    /* ==========================================================
       SEMESTER SUBSCRIPTIONS RESPONSIVENESS (MOBILE <= 768px)
       ========================================================== */
    @media (max-width: 768px) {
        .student-subs-header {
            flex-direction: column;
            align-items: stretch;
            padding: 16px 18px;
            gap: 14px;
        }
        .btn-pay-new-month {
            width: 100%;
            justify-content: center;
            box-sizing: border-box;
        }
        .regional-pricing-alert-banner {
            flex-direction: column;
            align-items: stretch;
            padding: 14px 16px;
            gap: 12px;
        }
        .stats-row-clean {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .active-due-summary-card {
            padding: 14px 16px;
        }
        .due-figures-grid {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .btn-pay-now-action {
            width: 100%;
            justify-content: center;
            box-sizing: border-box;
        }
        .semester-cards-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }
        .semester-sub-card {
            padding: 14px 16px;
        }
        .data-table-clean {
            min-width: 580px;
        }
    }

    @media (max-width: 480px) {
        .stats-row-clean {
            grid-template-columns: 1fr;
        }
        .card-top-head {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .semester-pill-badge {
            align-self: flex-start;
        }
    }
</style>
@endsection
