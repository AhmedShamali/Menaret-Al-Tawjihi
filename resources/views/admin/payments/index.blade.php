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
        <a href="{{ route('admin.payments.index', ['status' => 'completed']) }}" class="filter-tab {{ $status === 'completed' ? 'active' : '' }}">معتمد ({{ $stats['completed_count'] }})</a>
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="filter-tab {{ $status === 'pending' ? 'active' : '' }}">قيد المراجعة ({{ $stats['pending_count'] }})</a>
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
                    <th>الإيصال</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                <tr>
                    <td>
                        <strong style="font-family: monospace; color: #0284c7;">{{ $p->transaction_number }}</strong>
                    </td>
                    <td>
                        <div style="font-weight: 700;">{{ $p->student->name_ar ?? 'طالب غير محدد' }}</div>
                        <div style="font-size: 0.78rem; color: #64748b;">{{ $p->student->phone ?? '-' }}</div>
                    </td>
                    <td>
                        <span class="gateway-badge" style="background: #f1f5f9; color: #334155;">
                            {{ $p->gateway_name_ar }}
                        </span>
                    </td>
                    <td>
                        @if(is_array($p->items))
                            @foreach($p->items as $it)
                                <span style="display: inline-block; background: #f8fafc; border: 1px solid #e2e8f0; font-size: 0.75rem; padding: 2px 6px; border-radius: 4px; margin: 2px;">
                                    {{ $it['name_ar'] ?? 'مادة' }}
                                </span>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <strong style="color: #0f172a; font-size: 1rem;">{{ $p->amount }} ₪</strong>
                    </td>
                    <td style="font-size: 0.8rem; color: #64748b;">
                        {{ $p->created_at ? $p->created_at->format('Y-m-d H:i') : '-' }}
                    </td>
                    <td>
                        @if($p->receipt_path)
                            <a href="{{ asset('storage/' . $p->receipt_path) }}" target="_blank" class="receipt-link">
                                <i class="fas fa-paperclip"></i> عرض الإشعار
                            </a>
                        @else
                            <span style="color: #94a3b8; font-size: 0.78rem;">لا يوجد ملف</span>
                        @endif
                    </td>
                    <td>
                        @if($p->status === 'completed')
                            <span class="status-badge status-completed"><i class="fas fa-check-circle"></i> معتمد ومفعل</span>
                        @elseif($p->status === 'pending')
                            <span class="status-badge status-pending"><i class="fas fa-clock"></i> قيد المراجعة</span>
                        @else
                            <span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> ملغي</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions-cell">
                            @if($p->status !== 'completed')
                                <button onclick="changePaymentStatus({{ $p->id }}, 'completed')" class="btn-action btn-approve" title="اعتماد وتفعيل المواد فورياً">
                                    <i class="fas fa-check"></i> اعتماد
                                </button>
                            @endif
                            @if($p->status !== 'cancelled')
                                <button onclick="changePaymentStatus({{ $p->id }}, 'cancelled')" class="btn-action btn-reject" title="إلغاء">
                                    <i class="fas fa-times"></i>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function changePaymentStatus(paymentId, status) {
        const title = status === 'completed' ? 'اعتماد عملية الدفع وتفعيل المواد؟' : 'إلغاء عملية الدفع؟';
        const text = status === 'completed' 
            ? 'سيتم تفعيل كافة مواد الطالب المشترك بها فورياً وإرسال إشعار له.' 
            : 'سيتم إلغاء تفعيل اشتراك الطالب في هذه المواد.';

        Swal.fire({
            title: title,
            text: text,
            icon: status === 'completed' ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: status === 'completed' ? '#10b981' : '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: status === 'completed' ? 'نعم، اعتماد الآن' : 'نعم، إلغاء الدفعة',
            cancelButtonText: 'تراجع'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post(`/admin/payments/${paymentId}/status`, { status: status })
                    .then(res => {
                        Swal.fire('تم بنجاح!', res.data.message || 'تم تحديث حالة الدفع.', 'success')
                            .then(() => location.reload());
                    })
                    .catch(err => {
                        Swal.fire('خطأ', err.response?.data?.message || 'تعذر التحديث.', 'error');
                    });
            }
        });
    }
</script>
@endsection
