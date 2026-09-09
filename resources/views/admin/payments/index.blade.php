@extends('layouts.app')

@section('title', 'إدارة الاشتراكات والمدفوعات المالية')

@section('content')
<style>
    :root {
        --pay-emerald: #10b981;
        --pay-blue: #0284c7;
        --pay-amber: #f59e0b;
        --pay-rose: #ef4444;
    }

    .payments-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .payments-header h1 {
        font-size: 1.8rem;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .kpi-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .kpi-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 1.5rem;
    }

    .kpi-info h4 {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .kpi-info .val {
        font-size: 1.5rem;
        font-weight: 900;
        color: #0f172a;
    }

    /* Filters Bar */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .filter-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-tab {
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: 0.2s;
    }

    .filter-tab.active {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 6px 14px;
    }

    .search-box input {
        border: none;
        background: transparent;
        outline: none;
        font-family: inherit;
        font-size: 0.88rem;
        width: 200px;
    }

    /* Payments Table */
    .table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .table-responsive {
        overflow-x: auto;
    }

    table.payments-tbl {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }

    table.payments-tbl th {
        background: #f8fafc;
        padding: 14px 18px;
        font-size: 0.82rem;
        font-weight: 800;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
    }

    table.payments-tbl td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
        color: #1e293b;
    }

    table.payments-tbl tr:hover {
        background: #f8fafc;
    }

    .gateway-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
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
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: 0.2s;
    }

    .btn-approve {
        background: #10b981;
        color: white;
    }
    .btn-approve:hover { background: #059669; }

    .btn-reject {
        background: #fee2e2;
        color: #dc2626;
    }
    .btn-reject:hover { background: #fca5a5; }

    .receipt-link {
        color: #0284c7;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .receipt-link:hover { text-decoration: underline; }

    .filter-tab .badge-count {
        background: #ef4444;
        color: white;
        border-radius: 999px;
        padding: 2px 7px;
        font-size: 0.72rem;
        margin-right: 4px;
        font-weight: 800;
        display: inline-block;
        animation: pulseCount 2s infinite;
    }
    @keyframes pulseCount {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .row-pending {
        background: #fffbeb !important;
        border-left: 4px solid #f59e0b;
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
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
        text-decoration: none;
    }
    .receipt-btn-preview:hover {
        background: #0284c7;
        color: white;
        border-color: #0284c7;
    }

    .receipt-thumb {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #cbd5e1;
        cursor: pointer;
        transition: transform 0.2s, border-color 0.2s;
    }
    .receipt-thumb:hover {
        transform: scale(1.15);
        border-color: #0284c7;
    }

    /* Modal Styling */
    .receipt-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(5px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .receipt-modal-content {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 680px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        animation: modalPop 0.25s ease-out;
    }
    @keyframes modalPop {
        0% { opacity: 0; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
    }
    .receipt-modal-header {
        padding: 16px 22px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
    }
    .btn-close-modal {
        background: transparent;
        border: none;
        font-size: 1.25rem;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 8px;
        transition: 0.2s;
    }
    .btn-close-modal:hover {
        color: #0f172a;
        background: #e2e8f0;
    }
    .receipt-modal-body {
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .receipt-info-pill-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 10px;
        background: #f8fafc;
        padding: 12px;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        font-size: 0.82rem;
    }
    .receipt-viewer-box {
        text-align: center;
        background: #0f172a;
        border-radius: 12px;
        padding: 10px;
        min-height: 280px;
        max-height: 480px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .receipt-viewer-box img {
        max-height: 460px;
        max-width: 100%;
        object-fit: contain;
        border-radius: 6px;
    }
    .receipt-viewer-box iframe {
        width: 100%;
        height: 460px;
        border: none;
        border-radius: 6px;
    }
    .receipt-modal-footer {
        padding: 14px 22px;
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
        <h1><i class="fas fa-wallet" style="color: #0284c7;"></i> إدارة الاشتراكات والمدفوعات 🇵🇸</h1>
        <p style="color: #64748b; font-size: 0.92rem; margin-top: 4px;">متابعة عمليات سداد الطلاب عبر جوال باي، بال باي، وبنك فلسطين واعتماد التفعيل.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.subjects.pricing') }}" class="filter-tab" style="background: #ffffff; border-color: #0284c7; color: #0284c7;">
            <i class="fas fa-tags"></i> تسعير المواد
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon-wrap" style="background: #ecfdf5; color: #10b981;"><i class="fas fa-coins"></i></div>
        <div class="kpi-info">
            <h4>إجمالي الإيرادات المعتمدة</h4>
            <div class="val">{{ number_format($stats['total_revenue']) }} ₪</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon-wrap" style="background: #eff6ff; color: #0284c7;"><i class="fas fa-receipt"></i></div>
        <div class="kpi-info">
            <h4>إجمالي العمليات</h4>
            <div class="val">{{ $stats['total_count'] }} عملية</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon-wrap" style="background: #fffbeb; color: #f59e0b;"><i class="fas fa-clock"></i></div>
        <div class="kpi-info">
            <h4>بانتظار التأكيد</h4>
            <div class="val">{{ $stats['pending_count'] }} عملية</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon-wrap" style="background: #f5f3ff; color: #8b5cf6;"><i class="fas fa-mobile-screen"></i></div>
        <div class="kpi-info">
            <h4>جوال باي / بال باي</h4>
            <div class="val">{{ number_format($stats['jawwal_pay_revenue'] + $stats['palpay_revenue']) }} ₪</div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-card">
    <div class="filter-tabs">
        <a href="{{ route('admin.payments.index') }}" class="filter-tab {{ empty($status) ? 'active' : '' }}">الكل ({{ $stats['total_count'] }})</a>
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="filter-tab {{ $status === 'pending' ? 'active' : '' }}" style="{{ $stats['pending_count'] > 0 && $status !== 'pending' ? 'border-color: #f59e0b; color: #b45309; background: #fffbeb;' : '' }}">
            قيد المراجعة 
            @if($stats['pending_count'] > 0)
                <span class="badge-count">{{ $stats['pending_count'] }}</span>
            @else
                (0)
            @endif
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'completed']) }}" class="filter-tab {{ $status === 'completed' ? 'active' : '' }}">معتمد ومفعل ({{ $stats['completed_count'] }})</a>
        <a href="{{ route('admin.payments.index', ['status' => 'cancelled']) }}" class="filter-tab {{ $status === 'cancelled' ? 'active' : '' }}">ملغي</a>
    </div>

    <form method="GET" action="{{ route('admin.payments.index') }}">
        <div class="search-box">
            <i class="fas fa-search" style="color: #94a3b8;"></i>
            <input type="text" name="search" value="{{ $search }}" placeholder="ابحث برقم العملية أو الطالب...">
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
                    <th>رقم العملية</th>
                    <th>الطالب</th>
                    <th>بوابة السداد</th>
                    <th>المواد المشتركة</th>
                    <th>المبلغ الإجمالي</th>
                    <th>التاريخ</th>
                    <th>إشعار السداد</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                <tr class="{{ $p->status === 'pending' ? 'row-pending' : '' }}">
                    <td>
                        <strong style="font-family: monospace; color: #0284c7; font-size: 0.92rem;">{{ $p->transaction_number }}</strong>
                        @if($p->status === 'pending')
                            <span style="display: block; font-size: 0.72rem; color: #d97706; font-weight: 800; margin-top: 2px;">
                                <i class="fas fa-exclamation-circle"></i> بحاجة لاعتمادك
                            </span>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 800; color: #0f172a;">{{ $p->student->name_ar ?? 'طالب غير محدد' }}</div>
                        <div style="font-size: 0.78rem; color: #64748b; direction: ltr; text-align: right;">{{ $p->student->phone ?? '-' }}</div>
                    </td>
                    <td>
                        <span class="gateway-badge" style="background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0;">
                            {{ $p->gateway_name_ar }}
                        </span>
                    </td>
                    <td>
                        @if(is_array($p->items))
                            @foreach($p->items as $it)
                                <span style="display: inline-block; background: #f8fafc; border: 1px solid #e2e8f0; font-size: 0.75rem; padding: 2px 8px; border-radius: 6px; margin: 2px; font-weight: 600; color: #334155;">
                                    {{ $it['name_ar'] ?? 'مادة' }}
                                </span>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <strong style="color: #0f172a; font-size: 1.05rem;">{{ $p->amount }} ₪</strong>
                    </td>
                    <td style="font-size: 0.8rem; color: #64748b;">
                        {{ $p->created_at ? $p->created_at->format('Y-m-d H:i') : '-' }}
                    </td>
                    <td>
                        @if($p->receipt_path)
                            @php
                                $isPdf = \Illuminate\Support\Str::endsWith(strtolower($p->receipt_path), '.pdf');
                                $receiptUrl = route('admin.payments.receipt', $p->id);
                                $studentName = addslashes($p->student->name_ar ?? 'طالب');
                                $txNum = $p->transaction_number;
                                $amountFormatted = $p->amount . ' ₪';
                                $gwName = addslashes($p->gateway_name_ar);
                            @endphp
                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if(!$isPdf)
                                    <img src="{{ $receiptUrl }}" alt="إشعار" class="receipt-thumb" onclick="previewReceiptModal('{{ $receiptUrl }}', '{{ $studentName }}', '{{ $txNum }}', '{{ $amountFormatted }}', '{{ $gwName }}', '{{ $p->status }}', {{ $p->id }}, 'image')" title="انقر للتكبير والمراجعة">
                                @else
                                    <div style="width: 36px; height: 36px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; display: grid; place-items: center; color: #ef4444; cursor: pointer;" onclick="previewReceiptModal('{{ $receiptUrl }}', '{{ $studentName }}', '{{ $txNum }}', '{{ $amountFormatted }}', '{{ $gwName }}', '{{ $p->status }}', {{ $p->id }}, 'pdf')">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                @endif
                                <button type="button" class="receipt-btn-preview" onclick="previewReceiptModal('{{ $receiptUrl }}', '{{ $studentName }}', '{{ $txNum }}', '{{ $amountFormatted }}', '{{ $gwName }}', '{{ $p->status }}', {{ $p->id }}, '{{ $isPdf ? 'pdf' : 'image' }}')">
                                    <i class="fas fa-eye"></i> فحص الإشعار
                                </button>
                            </div>
                        @else
                            <span style="color: #94a3b8; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fas fa-times-circle"></i> لا يوجد إشعار
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($p->status === 'completed')
                            <span class="status-badge status-completed"><i class="fas fa-check-circle"></i> معتمد ومفعل</span>
                        @elseif($p->status === 'pending')
                            <span class="status-badge status-pending" style="animation: pulseCount 2s infinite;"><i class="fas fa-clock"></i> قيد المراجعة</span>
                        @else
                            <span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> ملغي</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions-cell">
                            @if($p->status === 'pending')
                                <button onclick="changePaymentStatus({{ $p->id }}, 'completed')" class="btn-action btn-approve" title="اعتماد إشعار السداد وتفعيل المواد والحساب فوراً">
                                    <i class="fas fa-check-circle"></i> اعتماد وتفعيل
                                </button>
                                <button onclick="changePaymentStatus({{ $p->id }}, 'cancelled')" class="btn-action btn-reject" title="رفض الإشعار">
                                    <i class="fas fa-times-circle"></i> رفض
                                </button>
                            @elseif($p->status === 'completed')
                                <span style="color: #059669; font-weight: 800; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-shield-check"></i> تم التفعيل
                                </span>
                                <button onclick="changePaymentStatus({{ $p->id }}, 'cancelled')" class="btn-action btn-reject" style="padding: 3px 8px; font-size: 0.72rem; margin-right: 6px;" title="إلغاء التفعيل">
                                    إلغاء
                                </button>
                            @else
                                <span style="color: #b91c1c; font-weight: 700; font-size: 0.8rem; margin-left: 6px;">ملغي</span>
                                <button onclick="changePaymentStatus({{ $p->id }}, 'completed')" class="btn-action btn-approve" style="padding: 4px 10px; font-size: 0.75rem;" title="إعادة اعتماد">
                                    تفعيل
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 50px; color: #64748b;">
                        <i class="fas fa-receipt" style="font-size: 2.5rem; opacity: 0.3; margin-bottom: 12px; display: block;"></i>
                        لا توجد عمليات دفع مسجلة حتى الآن.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
    <div style="padding: 16px 20px; border-top: 1px solid #e2e8f0;">
        {{ $payments->links() }}
    </div>
    @endif
</div>

<!-- Modal معاينة إشعار السداد البنكي -->
<div id="receiptModal" class="receipt-modal-backdrop" style="display: none;" onclick="handleBackdropClick(event)">
    <div class="receipt-modal-content" onclick="event.stopPropagation()">
        <div class="receipt-modal-header">
            <div>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a;" id="modalStudentName">معاينة إشعار السداد</h3>
                <div style="font-size: 0.82rem; color: #64748b; font-family: monospace;" id="modalTxNumber"></div>
            </div>
            <button type="button" onclick="closeReceiptModal()" class="btn-close-modal" title="إغلاق"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="receipt-modal-body">
            <div class="receipt-info-pill-grid">
                <div><span style="color: #64748b;">المبلغ:</span> <strong id="modalAmount" style="color: #0284c7; font-size: 0.95rem;">-</strong></div>
                <div><span style="color: #64748b;">بوابة السداد:</span> <strong id="modalGateway" style="color: #1e293b;">-</strong></div>
                <div><span style="color: #64748b;">الحالة:</span> <span id="modalStatusBadge">-</span></div>
            </div>

            <div id="modalReceiptContainer" class="receipt-viewer-box">
                <!-- يتم إدراج الصورة أو الـ PDF هنا -->
            </div>
        </div>

        <div class="receipt-modal-footer">
            <div>
                <a id="modalDownloadBtn" href="#" target="_blank" class="btn-action" style="background: #f1f5f9; color: #334155; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-external-link-alt"></i> فتح بنافذة مستقلة
                </a>
            </div>
            <div style="display: flex; gap: 8px;" id="modalActionButtons">
                <!-- أزرار الاعتماد أو الرفض -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function previewReceiptModal(url, studentName, txNumber, amount, gateway, status, paymentId, fileType) {
        document.getElementById('modalStudentName').textContent = 'إشعار سداد: ' + studentName;
        document.getElementById('modalTxNumber').textContent = 'رقم المعاملة: ' + txNumber;
        document.getElementById('modalAmount').textContent = amount;
        document.getElementById('modalGateway').textContent = gateway;
        document.getElementById('modalDownloadBtn').href = url;

        const statusBadge = document.getElementById('modalStatusBadge');
        if (status === 'completed') {
            statusBadge.innerHTML = '<span class="status-badge status-completed"><i class="fas fa-check-circle"></i> معتمد ومفعل</span>';
        } else if (status === 'pending') {
            statusBadge.innerHTML = '<span class="status-badge status-pending"><i class="fas fa-clock"></i> قيد المراجعة</span>';
        } else {
            statusBadge.innerHTML = '<span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> ملغي</span>';
        }

        const container = document.getElementById('modalReceiptContainer');
        if (fileType === 'pdf') {
            container.innerHTML = `<iframe src="${url}"></iframe>`;
        } else {
            container.innerHTML = `<img src="${url}" alt="إشعار السداد" onerror="this.onerror=null; this.parentElement.innerHTML='<div style=\\'color:white; padding: 40px;\\'><i class=\\'fas fa-exclamation-triangle\\' style=\\'font-size: 2rem; color: #f59e0b; margin-bottom: 10px; display:block;\\'></i>تعذر تحميل الصورة مباشرة، <a href=\\''+url+'\\' target=\\'_blank\\' style=\\'color:#38bdf8; font-weight:bold;\\'>اضغط هنا لفتحها</a></div>';">`;
        }

        const actionsContainer = document.getElementById('modalActionButtons');
        actionsContainer.innerHTML = '';
        if (status !== 'completed') {
            const approveBtn = document.createElement('button');
            approveBtn.className = 'btn-action btn-approve';
            approveBtn.innerHTML = '<i class="fas fa-check-circle"></i> اعتماد وتفعيل المواد فوراً';
            approveBtn.onclick = function() {
                closeReceiptModal();
                changePaymentStatus(paymentId, 'completed');
            };
            actionsContainer.appendChild(approveBtn);
        }
        if (status !== 'cancelled') {
            const rejectBtn = document.createElement('button');
            rejectBtn.className = 'btn-action btn-reject';
            rejectBtn.innerHTML = '<i class="fas fa-times"></i> رفض العملية';
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
        const title = isApprove ? 'اعتماد إشعار السداد وتفعيل المواد؟' : 'إلغاء / رفض عملية الدفع؟';
        const text = isApprove 
            ? 'سيتم تفعيل حساب الطالب وكافة المواد المشترك بها فورياً، وإرسال إشعار رسمي له بالتهنئة.' 
            : 'سيتم إلغاء أو تعطيل تفعيل مواد الطالب المرتبطة بهذه العملية.';

        Swal.fire({
            title: title,
            text: text,
            icon: isApprove ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#10b981' : '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: isApprove ? 'نعم، اعتماد وتفعيل الآن ✅' : 'نعم، إلغاء الدفعة',
            cancelButtonText: 'تراجع'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'جارٍ التنفيذ...',
                    text: 'يتم تحديث الحالة وتفعيل الاشتراكات...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                axios.post(`/admin/payments/${paymentId}/status`, { status: status })
                    .then(res => {
                        Swal.fire({
                            title: 'تم بنجاح!',
                            text: res.data.message || 'تم تحديث حالة الدفع وتفعيل المواد بنجاح.',
                            icon: 'success',
                            confirmButtonText: 'حسناً'
                        }).then(() => location.reload());
                    })
                    .catch(err => {
                        Swal.fire('خطأ', err.response?.data?.message || 'تعذر التحديث، يرجى المحاولة لاحقاً.', 'error');
                    });
            }
        });
    }
</script>
@endsection
