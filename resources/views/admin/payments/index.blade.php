@extends('layouts.app')

@section('title', __('إدارة الاشتراكات والمدفوعات المالية') . ' - ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
<style>
    .payments-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px 24px;
        border-inline-start: 5px solid var(--ed-primary, #1d4ed8);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .payments-header h1 {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 4px;
    }

    .stats-row-clean {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
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

    /* Filters Bar */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }

    .filter-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-tab {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        transition: 0.15s ease;
    }

    .filter-tab.active {
        background: var(--ed-primary, #1d4ed8);
        color: #ffffff;
        border-color: #1e40af;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 12px;
    }

    .search-box input {
        border: none;
        background: transparent;
        outline: none;
        font-family: inherit;
        font-size: 0.84rem;
        width: 220px;
    }

    /* Payments Table */
    .table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .table-responsive {
        overflow-x: auto;
    }

    table.payments-tbl {
        width: 100%;
        border-collapse: collapse;
        text-align: start;
        font-size: 0.88rem;
    }

    table.payments-tbl th {
        background: #f8fafc;
        padding: 12px 14px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    table.payments-tbl td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        vertical-align: middle;
    }

    table.payments-tbl tr:hover {
        background: #f8fafc;
    }

    .gateway-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.76rem;
        font-weight: 600;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .status-completed {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .status-pending {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .status-cancelled {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .actions-cell {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .btn-action {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: 0.15s ease;
    }

    .btn-approve {
        background: #10b981;
        color: white;
    }
    .btn-approve:hover { background: #059669; }

    .btn-reject {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .btn-reject:hover { background: #fca5a5; }

    .filter-tab .badge-count {
        background: #ef4444;
        color: white;
        border-radius: 999px;
        padding: 1px 6px;
        font-size: 0.7rem;
        margin-inline-end: 4px;
        font-weight: 800;
        display: inline-block;
    }

    .row-pending {
        background: #fffbeb !important;
        border-inline-start: 4px solid #f59e0b;
    }
    .row-pending:hover {
        background: #fef3c7 !important;
    }

    .receipt-btn-preview {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        color: #0284c7;
        border: 1px solid #bfdbfe;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.76rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.15s;
        text-decoration: none;
    }
    .receipt-btn-preview:hover {
        background: #0284c7;
        color: white;
        border-color: #0284c7;
    }

    .receipt-thumb {
        width: 34px;
        height: 34px;
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid #cbd5e1;
        cursor: pointer;
        transition: transform 0.15s;
    }
    .receipt-thumb:hover {
        transform: scale(1.1);
        border-color: #0284c7;
    }

    /* Modal Styling */
    .receipt-modal-backdrop {
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
    .receipt-modal-content {
        background: #ffffff;
        border-radius: 12px;
        width: 100%;
        max-width: 640px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }
    .receipt-modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
    }
    .btn-close-modal {
        background: transparent;
        border: none;
        font-size: 1.3rem;
        color: #64748b;
        cursor: pointer;
    }
    .receipt-modal-body {
        padding: 18px 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .receipt-info-pill-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 8px;
        background: #f8fafc;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        font-size: 0.82rem;
    }
    .receipt-viewer-box {
        text-align: center;
        background: #0f172a;
        border-radius: 8px;
        padding: 8px;
        min-height: 260px;
        max-height: 440px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .receipt-viewer-box img {
        max-height: 420px;
        max-width: 100%;
        object-fit: contain;
        border-radius: 4px;
    }
    .receipt-viewer-box iframe {
        width: 100%;
        height: 420px;
        border: none;
        border-radius: 4px;
    }
    .receipt-modal-footer {
        padding: 14px 20px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
</style>

<div class="payments-header">
    <div>
        <h1><i class="fas fa-wallet" style="color: var(--ed-primary, #1d4ed8);"></i> {{ __('إدارة الاشتراكات والمدفوعات') }}</h1>
        <p style="color: #64748b; font-size: 0.88rem; margin-top: 4px;">{{ __('متابعة عمليات سداد الطلاب عبر جوال باي، بال باي، وبنك فلسطين واعتماد التفعيل.') }}</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.subjects.pricing') }}" class="filter-tab" style="background: #ffffff; border-color: var(--ed-primary, #1d4ed8); color: var(--ed-primary, #1d4ed8);">
            <i class="fas fa-tags"></i> {{ __('تسعير المواد') }}
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="stats-row-clean">
    <div class="stat-card-clean" style="--card-accent: #059669;">
        <span class="stat-label">{{ __('إجمالي الإيرادات المعتمدة') }}</span>
        <div class="stat-value-wrap">
            <span class="stat-number text-emerald font-mono">{{ number_format($stats['total_revenue']) }} ₪</span>
            <i class="fas fa-coins stat-icon text-emerald"></i>
        </div>
    </div>

    <div class="stat-card-clean" style="--card-accent: #1e3a8a;">
        <span class="stat-label">{{ __('إجمالي العمليات المسجلة') }}</span>
        <div class="stat-value-wrap">
            <span class="stat-number text-navy font-mono">{{ $stats['total_count'] }}</span>
            <i class="fas fa-receipt stat-icon text-navy"></i>
        </div>
    </div>

    <div class="stat-card-clean" style="--card-accent: #d97706;">
        <span class="stat-label">{{ __('بانتظار التأكيد والاعتماد') }}</span>
        <div class="stat-value-wrap">
            <span class="stat-number font-mono {{ $stats['pending_count'] > 0 ? 'text-amber' : '' }}">{{ $stats['pending_count'] }}</span>
            <i class="fas fa-clock stat-icon text-amber"></i>
        </div>
    </div>

    <div class="stat-card-clean" style="--card-accent: #6366f1;">
        <span class="stat-label">{{ __('إيرادات جوال باي / بال باي') }}</span>
        <div class="stat-value-wrap">
            <span class="stat-number text-indigo font-mono">{{ number_format($stats['jawwal_pay_revenue'] + $stats['palpay_revenue']) }} ₪</span>
            <i class="fas fa-mobile-screen stat-icon text-indigo"></i>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-card">
    <div class="filter-tabs">
        <a href="{{ route('admin.payments.index') }}" class="filter-tab {{ empty($status) ? 'active' : '' }}">{{ __('الكل') }} ({{ $stats['total_count'] }})</a>
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="filter-tab {{ $status === 'pending' ? 'active' : '' }}" style="{{ $stats['pending_count'] > 0 && $status !== 'pending' ? 'border-color: #f59e0b; color: #b45309; background: #fffbeb;' : '' }}">
            {{ __('قيد المراجعة') }} 
            @if($stats['pending_count'] > 0)
                <span class="badge-count font-mono">{{ $stats['pending_count'] }}</span>
            @else
                (0)
            @endif
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'completed']) }}" class="filter-tab {{ $status === 'completed' ? 'active' : '' }}">{{ __('معتمد ومفعل') }} ({{ $stats['completed_count'] }})</a>
        <a href="{{ route('admin.payments.index', ['status' => 'cancelled']) }}" class="filter-tab {{ $status === 'cancelled' ? 'active' : '' }}">{{ __('ملغي') }}</a>
    </div>

    <form method="GET" action="{{ route('admin.payments.index') }}">
        <div class="search-box">
            <i class="fas fa-search" style="color: #94a3b8;"></i>
            <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('ابحث برقم العملية أو الطالب...') }}">
            @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="payments-tbl">
            <thead>
                <tr>
                    <th>{{ __('رقم العملية') }}</th>
                    <th>{{ __('الطالب') }}</th>
                    <th>{{ __('بوابة السداد') }}</th>
                    <th>{{ __('المواد المشتركة') }}</th>
                    <th>{{ __('المبلغ الإجمالي') }}</th>
                    <th>{{ __('التاريخ') }}</th>
                    <th>{{ __('إشعار السداد') }}</th>
                    <th>{{ __('الحالة') }}</th>
                    <th>{{ __('إجراء') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                @php
                    $studentDisplayName = (app()->getLocale() === 'en' && !empty($p->student->name_en)) ? $p->student->name_en : ($p->student->name_ar ?? ($p->student->name ?? __('طالب غير محدد')));
                    $gatewayName = __($p->gateway_name_ar);
                @endphp
                <tr class="{{ $p->status === 'pending' ? 'row-pending' : '' }}">
                    <td>
                        <strong class="font-mono" style="color: var(--ed-primary, #1d4ed8); font-size: 0.9rem;">{{ $p->transaction_number }}</strong>
                        @if($p->status === 'pending')
                            <span style="display: block; font-size: 0.72rem; color: #d97706; font-weight: 800; margin-top: 2px;">
                                <i class="fas fa-exclamation-circle"></i> {{ __('بحاجة لاعتمادك') }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">{{ $studentDisplayName }}</div>
                        <div class="font-mono" style="font-size: 0.76rem; color: #64748b;" dir="ltr">{{ $p->student->phone ?? '-' }}</div>
                    </td>
                    <td>
                        <span class="gateway-badge" style="background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0;">
                            {{ $gatewayName }}
                        </span>
                    </td>
                    <td>
                        @if(is_array($p->items))
                            @foreach($p->items as $it)
                                @php
                                    $itName = (app()->getLocale() === 'en' && !empty($it['name_en'])) ? $it['name_en'] : ($it['name_ar'] ?? __('مادة'));
                                @endphp
                                <span style="display: inline-block; background: #f8fafc; border: 1px solid #e2e8f0; font-size: 0.74rem; padding: 2px 7px; border-radius: 4px; margin: 2px; font-weight: 600; color: #334155;">
                                    {{ $itName }}
                                </span>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <strong class="font-mono" style="color: #0f172a; font-size: 1rem;">{{ $p->amount }} ₪</strong>
                    </td>
                    <td class="font-mono" style="font-size: 0.78rem; color: #64748b;">
                        {{ $p->created_at ? $p->created_at->format('Y-m-d H:i') : '-' }}
                    </td>
                    <td>
                        @if($p->receipt_path)
                            @php
                                $isPdf = \Illuminate\Support\Str::endsWith(strtolower($p->receipt_path), '.pdf');
                                $receiptUrl = route('admin.payments.receipt', $p->id);
                                $txNum = $p->transaction_number;
                                $amountFormatted = $p->amount . ' ₪';
                            @endphp
                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if(!$isPdf)
                                    <img src="{{ $receiptUrl }}" alt="{{ __('إشعار') }}" class="receipt-thumb" onclick="previewReceiptModal('{{ $receiptUrl }}', '{{ addslashes($studentDisplayName) }}', '{{ $txNum }}', '{{ $amountFormatted }}', '{{ addslashes($gatewayName) }}', '{{ $p->status }}', {{ $p->id }}, 'image')" title="{{ __('انقر للتكبير والمراجعة') }}">
                                @else
                                    <div style="width: 34px; height: 34px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; display: grid; place-items: center; color: #ef4444; cursor: pointer;" onclick="previewReceiptModal('{{ $receiptUrl }}', '{{ addslashes($studentDisplayName) }}', '{{ $txNum }}', '{{ $amountFormatted }}', '{{ addslashes($gatewayName) }}', '{{ $p->status }}', {{ $p->id }}, 'pdf')">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                @endif
                                <button type="button" class="receipt-btn-preview" onclick="previewReceiptModal('{{ $receiptUrl }}', '{{ addslashes($studentDisplayName) }}', '{{ $txNum }}', '{{ $amountFormatted }}', '{{ addslashes($gatewayName) }}', '{{ $p->status }}', {{ $p->id }}, '{{ $isPdf ? 'pdf' : 'image' }}')">
                                    <i class="fas fa-eye"></i> {{ __('فحص الإشعار') }}
                                </button>
                            </div>
                        @else
                            <span style="color: #94a3b8; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fas fa-times-circle"></i> {{ __('لا يوجد إشعار') }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($p->status === 'completed')
                            <span class="status-badge status-completed"><i class="fas fa-check-circle"></i> {{ __('معتمد ومفعل') }}</span>
                        @elseif($p->status === 'pending')
                            <span class="status-badge status-pending"><i class="fas fa-clock"></i> {{ __('قيد المراجعة') }}</span>
                        @else
                            <span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> {{ __('ملغي') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions-cell">
                            @if($p->status === 'pending')
                                <button onclick="changePaymentStatus({{ $p->id }}, 'completed')" class="btn-action btn-approve" title="{{ __('اعتماد إشعار السداد وتفعيل المواد والحساب فوراً') }}">
                                    <i class="fas fa-check-circle"></i> {{ __('اعتماد وتفعيل') }}
                                </button>
                                <button onclick="changePaymentStatus({{ $p->id }}, 'cancelled')" class="btn-action btn-reject" title="{{ __('رفض الإشعار') }}">
                                    <i class="fas fa-times-circle"></i> {{ __('رفض') }}
                                </button>
                            @elseif($p->status === 'completed')
                                <span style="color: #059669; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-shield-check"></i> {{ __('تم التفعيل') }}
                                </span>
                                <button onclick="changePaymentStatus({{ $p->id }}, 'cancelled')" class="btn-action btn-reject" style="padding: 3px 8px; font-size: 0.72rem; margin-inline-start: 6px;" title="{{ __('إلغاء التفعيل') }}">
                                    {{ __('إلغاء') }}
                                </button>
                            @else
                                <span style="color: #b91c1c; font-weight: 700; font-size: 0.8rem; margin-inline-end: 6px;">{{ __('ملغي') }}</span>
                                <button onclick="changePaymentStatus({{ $p->id }}, 'completed')" class="btn-action btn-approve" style="padding: 4px 10px; font-size: 0.75rem;" title="{{ __('إعادة اعتماد') }}">
                                    {{ __('تفعيل') }}
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-receipt" style="font-size: 2.2rem; opacity: 0.3; margin-bottom: 10px; display: block;"></i>
                        {{ __('لا توجد عمليات دفع مسجلة حتى الآن.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
    <div style="padding: 14px 20px; border-top: 1px solid #e2e8f0;">
        {{ $payments->links() }}
    </div>
    @endif
</div>

<!-- Modal معاينة إشعار السداد البنكي -->
<div id="receiptModal" class="receipt-modal-backdrop" style="display: none;" onclick="handleBackdropClick(event)">
    <div class="receipt-modal-content" onclick="event.stopPropagation()">
        <div class="receipt-modal-header">
            <div>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a;" id="modalStudentName">{{ __('معاينة إشعار السداد') }}</h3>
                <div class="font-mono" style="font-size: 0.8rem; color: #64748b;" id="modalTxNumber"></div>
            </div>
            <button type="button" onclick="closeReceiptModal()" class="btn-close-modal" title="{{ __('إغلاق') }}"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="receipt-modal-body">
            <div class="receipt-info-pill-grid">
                <div><span style="color: #64748b;">{{ __('المبلغ:') }}</span> <strong id="modalAmount" class="font-mono" style="color: var(--ed-primary, #1d4ed8); font-size: 0.92rem;">-</strong></div>
                <div><span style="color: #64748b;">{{ __('بوابة السداد:') }}</span> <strong id="modalGateway" style="color: #1e293b;">-</strong></div>
                <div><span style="color: #64748b;">{{ __('الحالة:') }}</span> <span id="modalStatusBadge">-</span></div>
            </div>

            <div id="modalReceiptContainer" class="receipt-viewer-box"></div>
        </div>

        <div class="receipt-modal-footer">
            <div>
                <a id="modalDownloadBtn" href="#" target="_blank" class="btn-action" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-external-link-alt"></i> {{ __('فتح بنافذة مستقلة') }}
                </a>
            </div>
            <div style="display: flex; gap: 8px;" id="modalActionButtons"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const paymentsI18n = {
        receiptNotice: "{{ __('إشعار سداد:') }}",
        txNumber: "{{ __('رقم المعاملة:') }}",
        completedBadge: '<span class="status-badge status-completed"><i class="fas fa-check-circle"></i> {{ __("معتمد ومفعل") }}</span>',
        pendingBadge: '<span class="status-badge status-pending"><i class="fas fa-clock"></i> {{ __("قيد المراجعة") }}</span>',
        cancelledBadge: '<span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> {{ __("ملغي") }}</span>',
        approveBtnText: '<i class="fas fa-check-circle"></i> {{ __("اعتماد وتفعيل المواد فوراً") }}',
        rejectBtnText: '<i class="fas fa-times"></i> {{ __("رفض العملية") }}',
        approvePromptTitle: "{{ __('اعتماد إشعار السداد وتفعيل المواد؟') }}",
        rejectPromptTitle: "{{ __('إلغاء / رفض عملية الدفع؟') }}",
        approvePromptText: "{{ __('سيتم تفعيل حساب الطالب وكافة المواد المشترك بها فورياً، وإرسال إشعار رسمي له بالتهنئة.') }}",
        rejectPromptText: "{{ __('سيتم إلغاء أو تعطيل تفعيل مواد الطالب المرتبطة بهذه العملية.') }}",
        approveConfirmBtn: "{{ __('نعم، اعتماد وتفعيل الآن ✅') }}",
        rejectConfirmBtn: "{{ __('نعم، إلغاء الدفعة') }}",
        cancelPromptBtn: "{{ __('تراجع') }}",
        processingTitle: "{{ __('جارٍ التنفيذ...') }}",
        processingText: "{{ __('يتم تحديث الحالة وتفعيل الاشتراكات...') }}",
        successTitle: "{{ __('تم بنجاح!') }}",
        successDefault: "{{ __('تم تحديث حالة الدفع وتفعيل المواد بنجاح.') }}",
        errorTitle: "{{ __('خطأ') }}",
        errorDefault: "{{ __('تعذر التحديث، يرجى المحاولة لاحقاً.') }}",
        okBtn: "{{ __('حسناً') }}",
        imgLoadFail: "{{ __('تعذر تحميل الصورة مباشرة،') }}",
        imgOpenLink: "{{ __('اضغط هنا لفتحها') }}"
    };

    function previewReceiptModal(url, studentName, txNumber, amount, gateway, status, paymentId, fileType) {
        document.getElementById('modalStudentName').textContent = paymentsI18n.receiptNotice + ' ' + studentName;
        document.getElementById('modalTxNumber').textContent = paymentsI18n.txNumber + ' ' + txNumber;
        document.getElementById('modalAmount').textContent = amount;
        document.getElementById('modalGateway').textContent = gateway;
        document.getElementById('modalDownloadBtn').href = url;

        const statusBadge = document.getElementById('modalStatusBadge');
        if (status === 'completed') {
            statusBadge.innerHTML = paymentsI18n.completedBadge;
        } else if (status === 'pending') {
            statusBadge.innerHTML = paymentsI18n.pendingBadge;
        } else {
            statusBadge.innerHTML = paymentsI18n.cancelledBadge;
        }

        const container = document.getElementById('modalReceiptContainer');
        if (fileType === 'pdf') {
            container.innerHTML = `<iframe src="${url}"></iframe>`;
        } else {
            container.innerHTML = `<img src="${url}" alt="{{ __('إشعار السداد') }}" onerror="this.onerror=null; this.parentElement.innerHTML='<div style=\\'color:white; padding: 40px;\\'><i class=\\'fas fa-exclamation-triangle\\' style=\\'font-size: 2rem; color: #f59e0b; margin-bottom: 10px; display:block;\\'></i>' + paymentsI18n.imgLoadFail + ' <a href=\\''+url+'\\' target=\\'_blank\\' style=\\'color:#38bdf8; font-weight:bold;\\'>' + paymentsI18n.imgOpenLink + '</a></div>';">`;
        }

        const actionsContainer = document.getElementById('modalActionButtons');
        actionsContainer.innerHTML = '';
        if (status !== 'completed') {
            const approveBtn = document.createElement('button');
            approveBtn.className = 'btn-action btn-approve';
            approveBtn.innerHTML = paymentsI18n.approveBtnText;
            approveBtn.onclick = function() {
                closeReceiptModal();
                changePaymentStatus(paymentId, 'completed');
            };
            actionsContainer.appendChild(approveBtn);
        }
        if (status !== 'cancelled') {
            const rejectBtn = document.createElement('button');
            rejectBtn.className = 'btn-action btn-reject';
            rejectBtn.innerHTML = paymentsI18n.rejectBtnText;
            rejectBtn.onclick = function() {
                closeReceiptModal();
                changePaymentStatus(paymentId, 'cancelled');
            };
            actionsContainer.appendChild(rejectBtn);
        }

        document.getElementById('receiptModal').style.display = 'flex';
    }

    function closeReceiptModal() {
        document.getElementById('receiptModal').style.display = 'none';
        document.getElementById('modalReceiptContainer').innerHTML = '';
    }

    function handleBackdropClick(e) {
        if (e.target.id === 'receiptModal') {
            closeReceiptModal();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeReceiptModal();
    });

    function changePaymentStatus(paymentId, status) {
        const isApprove = status === 'completed';
        const title = isApprove ? paymentsI18n.approvePromptTitle : paymentsI18n.rejectPromptTitle;
        const text = isApprove ? paymentsI18n.approvePromptText : paymentsI18n.rejectPromptText;

        Swal.fire({
            title: title,
            text: text,
            icon: isApprove ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#10b981' : '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: isApprove ? paymentsI18n.approveConfirmBtn : paymentsI18n.rejectConfirmBtn,
            cancelButtonText: paymentsI18n.cancelPromptBtn
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: paymentsI18n.processingTitle,
                    text: paymentsI18n.processingText,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                axios.post(`/admin/payments/${paymentId}/status`, { status: status })
                    .then(res => {
                        Swal.fire({
                            title: paymentsI18n.successTitle,
                            text: res.data.message || paymentsI18n.successDefault,
                            icon: 'success',
                            confirmButtonText: paymentsI18n.okBtn
                        }).then(() => location.reload());
                    })
                    .catch(err => {
                        Swal.fire(paymentsI18n.errorTitle, err.response?.data?.message || paymentsI18n.errorDefault, 'error');
                    });
            }
        });
    }
</script>
@endsection
