@extends('layouts.app')

@section('title', 'سجل اشتراكاتي الشهرية - منصة منارة التوجيهي')

@section('content')
<div class="student-subs-container">
    {{-- هيدر الصفحة --}}
    <div class="student-subs-header">
        <div class="header-text-block">
            <div class="subs-badge">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>{{ __('Academic Student Subscriptions') }}</span>
            </div>
            <h1 class="subs-title">{{ __('Monthly Subscriptions Record') }} ({{ $year }})</h1>
            <p class="subs-subtitle">{{ __('Welcome, :name! Track your subscription status and monthly installments throughout the academic year.', ['name' => $student->name_ar ?? $student->name]) }}</p>
        </div>
        <div class="header-action-block">
            <a href="{{ route('student.pendingPayment.show') }}" class="btn-pay-new-month">
                <i class="fa-solid fa-receipt"></i> {{ __('Submit New Payment Notice') }}
            </a>
        </div>
    </div>

    {{-- بطاقات الملخص الكلاسيكية للطالب --}}
    <div class="stats-row-clean">
        <div class="stat-card-clean" style="--card-accent: #059669;">
            <span class="stat-label">{{ __('Paid Months') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-emerald">{{ $paidCount }} <small style="font-size: 0.85rem; color: #64748b;">/ 12</small></span>
                <i class="fa-solid fa-circle-check stat-icon text-emerald"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">{{ __('Active and approved enrollment in subjects') }}</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #1e3a8a;">
            <span class="stat-label">{{ __('Total Paid Amount') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-navy">{{ number_format($totalPaidAmount, 2) }} ₪</span>
                <i class="fa-solid fa-wallet stat-icon text-navy"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">{{ __('Officially verified payments') }}</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #d97706;">
            <span class="stat-label">{{ __('Under Verification') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number {{ $pendingCount > 0 ? 'text-amber' : '' }}">{{ $pendingCount }}</span>
                <i class="fa-solid fa-hourglass-half stat-icon text-amber"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">{{ __('Awaiting supervisor verification') }}</small>
        </div>

        <div class="stat-card-clean" style="--card-accent: #6366f1;">
            <span class="stat-label">{{ __('Remaining Due Months') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-indigo">{{ $unpaidCount }}</span>
                <i class="fa-solid fa-calendar-xmark stat-icon text-indigo"></i>
            </div>
            <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">{{ __('Annual installments during the school year') }}</small>
        </div>
    </div>

    {{-- شبكة الشهور الـ 12 التفاعلية --}}
    <div class="months-timeline-card">
        <div class="timeline-head">
            <div>
                <h2 class="timeline-title"><i class="fa-solid fa-calendar-check text-primary"></i> {{ __('12 Months Schedule for the Academic Year') }} ({{ $year }})</h2>
                <p class="timeline-sub">{{ __('Click on any month to view installment details or submit proof of payment') }}</p>
            </div>
            <div class="academic-branch-pill">
                <i class="fa-solid fa-book-bookmark"></i>
                <span>{{ $student->stage->label_ar ?? ($student->stage->name_ar ?? __('Academic Stage')) }}</span>
            </div>
        </div>

        <div class="months-cards-grid">
            @foreach($subscriptions as $sub)
                @php
                    $isPaid = $sub->status === 'paid';
                    $isPending = $sub->status === 'pending';
                    $isWaived = $sub->status === 'waived';
                    $badgeInfo = $sub->status_badge;
                @endphp
                <div class="month-card {{ $isPaid ? 'card-paid' : ($isPending ? 'card-pending' : ($isWaived ? 'card-waived' : 'card-unpaid')) }}">
                    <div class="month-card-header">
                        <span class="month-number font-mono">{{ sprintf('%02d', $sub->month) }}</span>
                        <span class="month-state-pill" style="background: {{ $badgeInfo['bg'] }}; color: {{ $badgeInfo['color'] }};">
                            {{ __($badgeInfo['label']) }}
                        </span>
                    </div>

                    <div class="month-card-body">
                        <h4 class="month-name">{{ app()->getLocale() == 'ar' ? $sub->month_name_ar : ($sub->month_name_en ?? date('F', mktime(0, 0, 0, $sub->month, 10))) }}</h4>
                        <div class="month-amount-row">
                            <span class="amount-label">{{ __('Installment Amount:') }}</span>
                            <strong class="amount-val font-mono">{{ number_format($sub->amount, 2) }} ₪</strong>
                        </div>
                        @if($isPaid && $sub->paid_at)
                            <div class="paid-date-note font-mono">
                                <i class="fa-regular fa-calendar-check"></i> {{ __('Payment Date:') }} {{ $sub->paid_at->format('Y-m-d') }}
                            </div>
                        @elseif($sub->notes)
                            <div class="paid-date-note">
                                <i class="fa-regular fa-note-sticky"></i> {{ $sub->notes }}
                            </div>
                        @endif
                    </div>

                    <div class="month-card-footer">
                        @if($isPaid)
                            <span class="btn-month-status done">
                                <i class="fa-solid fa-circle-check"></i> {{ __('Paid Successfully') }}
                            </span>
                        @elseif($isPending)
                            <span class="btn-month-status waiting">
                                <i class="fa-solid fa-clock-rotate-left"></i> {{ __('Pending Supervisor Approval') }}
                            </span>
                        @elseif($isWaived)
                            <span class="btn-month-status waived">
                                <i class="fa-solid fa-tag"></i> {{ __('Scholarship / Fully Waived') }}
                            </span>
                        @else
                            <div class="unpaid-actions-row">
                                <a href="{{ route('student.pendingPayment.show') }}" class="btn-month-status pay" style="flex: 1;">
                                    <i class="fa-solid fa-receipt"></i> {{ __('Pay this installment') }}
                                </a>
                                <a href="https://wa.me/970567897212?text={{ urlencode('مرحباً، أود الاستفسار وسداد قسط (' . $sub->month_name_ar . ') لحساب الطالب ' . ($student->name_ar ?? $student->name)) }}" target="_blank" class="btn-month-status whatsapp" title="{{ __('Pay or inquire via WhatsApp') }}">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- جدول كشف الأقساط الكلاسيكي المعتمد --}}
    <div class="table-card-clean" style="margin-top: 24px;">
        <div class="table-container-clean">
            <table class="data-table-clean">
                <thead>
                    <tr>
                        <th style="width: 70px; text-align: center;">#</th>
                        <th>{{ __('الشهر الدراسي') }}</th>
                        <th style="width: 140px;">{{ __('قيمة القسط') }}</th>
                        <th>{{ __('تاريخ السداد / الملاحظات') }}</th>
                        <th style="width: 150px; text-align: center;">{{ __('الحالة') }}</th>
                        <th style="width: 140px; text-align: center;">{{ __('الإجراء') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptions as $sub)
                        @php
                            $isPaid = $sub->status === 'paid';
                            $isPending = $sub->status === 'pending';
                            $isWaived = $sub->status === 'waived';
                            $monthTitle = app()->getLocale() == 'ar' ? $sub->month_name_ar : ($sub->month_name_en ?? date('F', mktime(0, 0, 0, $sub->month, 10)));
                        @endphp
                        <tr>
                            <td style="text-align: center; color: #94a3b8; font-family: monospace; font-size: 0.8rem; font-weight: 700;">
                                {{ sprintf('%02d', $sub->month) }}
                            </td>
                            <td>
                                <strong>{{ $monthTitle }}</strong>
                            </td>
                            <td style="font-family: monospace; font-weight: 700; color: #0f172a;">
                                {{ number_format($sub->amount, 2) }} ₪
                            </td>
                            <td style="color: #64748b; font-size: 0.82rem;">
                                @if($isPaid && $sub->paid_at)
                                    <i class="fa-regular fa-calendar-check text-emerald"></i> {{ $sub->paid_at->format('Y-m-d') }}
                                @elseif($sub->notes)
                                    {{ $sub->notes }}
                                @else
                                    —
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($isPaid)
                                    <span class="status-pill status-active"><span class="dot"></span> {{ __('مسدد وخالص') }}</span>
                                @elseif($isPending)
                                    <span class="status-pill status-pending"><span class="dot"></span> {{ __('قيد المراجعة') }}</span>
                                @elseif($isWaived)
                                    <span class="status-pill status-info"><span class="dot"></span> {{ __('إعفاء / منحة') }}</span>
                                @else
                                    <span class="status-pill status-frozen"><span class="dot"></span> {{ __('مستحق') }}</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if(!$isPaid && !$isWaived)
                                    <a href="{{ route('student.pendingPayment.show') }}" class="tbl-btn" style="background: #1e3a8a;">
                                        <i class="fa-solid fa-receipt"></i> {{ __('رفع إشعار') }}
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
</div>

<style>
    .student-subs-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 10px 0 60px;
        box-sizing: border-box;
        overflow-x: hidden;
    }
    .student-subs-header {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        border-radius: 24px;
        padding: 32px 28px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
    }
    .subs-badge {
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
    .subs-title {
        font-size: 1.55rem;
        font-weight: 800;
        margin: 0 0 6px;
    }
    .subs-subtitle {
        margin: 0;
        color: #cbd5e1;
        font-size: 0.92rem;
    }
    .btn-pay-new-month {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        text-decoration: none;
        padding: 12px 22px;
        border-radius: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
    }
    .btn-pay-new-month:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
    }

    /* Summary Grid */
    .student-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }
    .student-sum-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 22px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .sum-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        font-size: 1.6rem;
    }
    .student-sum-card.green .sum-icon { background: #ecfdf5; color: #059669; }
    .student-sum-card.blue .sum-icon { background: #f0f9ff; color: #0284c7; }
    .student-sum-card.amber .sum-icon { background: #fffbeb; color: #d97706; }
    .student-sum-card.purple .sum-icon { background: #faf5ff; color: #9333ea; }
    .sum-label { font-size: 0.8rem; color: #64748b; font-weight: 700; display: block; margin-bottom: 4px; }
    .sum-val { font-size: 1.55rem; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
    .sum-sub { font-size: 0.76rem; color: #94a3b8; }

    /* Timeline Card */
    .months-timeline-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    }
    .timeline-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 24px;
    }
    .timeline-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .timeline-sub {
        margin: 0;
        color: #64748b;
        font-size: 0.88rem;
    }
    .academic-branch-pill {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Months Cards Grid */
    .months-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
    }
    .month-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 20px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: 0.2s;
    }
    .month-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.06);
    }
    .card-paid { border-color: #10b981; background: #f0fdf4; }
    .card-pending { border-color: #f59e0b; background: #fffbeb; }
    .card-waived { border-color: #818cf8; background: #eef2ff; }
    .card-unpaid { border-color: #fca5a5; background: #fff; }

    .month-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }
    .month-number {
        font-size: 1.3rem;
        font-weight: 900;
        color: #0284c7;
    }
    .month-state-pill {
        font-size: 0.74rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 8px;
    }
    .month-name {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 8px;
    }
    .month-amount-row {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }
    .amount-label { color: #64748b; }
    .amount-val { color: #0f172a; font-size: 1.1rem; }
    .paid-date-note {
        font-size: 0.76rem;
        color: #64748b;
        background: rgba(0,0,0,0.03);
        padding: 4px 8px;
        border-radius: 6px;
    }
    .month-card-footer {
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid rgba(0,0,0,0.06);
    }
    .btn-month-status {
        display: block;
        text-align: center;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
    }
    .btn-month-status.done { background: #d1fae5; color: #065f46; }
    .btn-month-status.waiting { background: #fef3c7; color: #92400e; }
    .btn-month-status.waived { background: #e0e7ff; color: #3730a3; }
    .btn-month-status.pay { background: #0284c7; color: #fff; transition: 0.2s; }
    .btn-month-status.pay:hover { background: #0369a1; }
    .unpaid-actions-row { display: flex; align-items: center; gap: 8px; }
    .btn-month-status.whatsapp {
        background: #25d366;
        color: #fff;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 1.15rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }
    .btn-month-status.whatsapp:hover { background: #128c7e; }
</style>
@endsection
