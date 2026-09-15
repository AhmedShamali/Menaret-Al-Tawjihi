@extends('layouts.app')

@section('title', 'مصفوفة ومتابعة الاشتراكات الشهرية للطلاب (12 شهراً) - منصة منارة التوجيهي')

@section('content')
<div class="subs-matrix-wrapper">
    {{-- 1. الهيدر --}}
    <div class="matrix-header-card">
        <div>
            <div class="badge-tag">
                <i class="fa-solid fa-calendar-days"></i>
                <span>الإدارة المالية ومتابعة الاشتراكات السنوية</span>
            </div>
            <h1 class="page-title">مصفوفة وسجل الاشتراكات الشهرية للطلاب (12 شهراً)</h1>
            <p class="page-subtitle">متابعة دفعات وأقساط الطلاب شهراً بشهر لكامل السنة الدراسية وتحديث الحالات فورياً</p>
        </div>
        <div class="header-tools">
            <form method="GET" action="{{ route('admin.subscriptions.monthly') }}" class="year-form">
                <label>العام الدراسي:</label>
                <select name="year" class="year-select" onchange="this.form.submit()">
                    <option value="2026-2027" {{ $year === '2026-2027' ? 'selected' : '' }}>2026 / 2027 م</option>
                    <option value="2025-2026" {{ $year === '2025-2026' ? 'selected' : '' }}>2025 / 2026 م</option>
                </select>
            </form>
        </div>
    </div>

    {{-- 2. إحصائيات المؤشرات --}}
    <div class="kpi-grid">
        <div class="kpi-card green">
            <div class="kpi-icon"><i class="fa-solid fa-circle-dollar-to-slot"></i></div>
            <div class="kpi-data">
                <span class="kpi-label">المحصل الفعلي المعتمد</span>
                <h3 class="kpi-num font-mono">{{ number_format($stats['total_collected'], 2) }} ₪</h3>
                <small class="kpi-desc">تم تأكيد سداده من الطلاب ({{ $stats['paid_count'] }} دفعة)</small>
            </div>
        </div>

        <div class="kpi-card red">
            <div class="kpi-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="kpi-data">
                <span class="kpi-label">المتأخرات والأقساط غير المسددة</span>
                <h3 class="kpi-num font-mono">{{ number_format($stats['total_unpaid'], 2) }} ₪</h3>
                <small class="kpi-desc">أقساط مستحقة بانتظار الدفع ({{ $stats['unpaid_count'] }} شهر)</small>
            </div>
        </div>

        <div class="kpi-card amber">
            <div class="kpi-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div class="kpi-data">
                <span class="kpi-label">إشعارات سداد قيد المراجعة</span>
                <h3 class="kpi-num font-mono">{{ number_format($stats['total_pending'], 2) }} ₪</h3>
                <small class="kpi-desc">بحاجة لمطابقة الإيصال والاعتماد ({{ $stats['pending_count'] }} إشعار)</small>
            </div>
        </div>

        <div class="kpi-card blue">
            <div class="kpi-icon"><i class="fa-solid fa-percent"></i></div>
            <div class="kpi-data">
                <span class="kpi-label">نسبة التحصيل والالتزام المالي</span>
                <h3 class="kpi-num font-mono">{{ $stats['collection_rate'] }}%</h3>
                <small class="kpi-desc">من إجمالي المستحق السنوي ({{ number_format($stats['total_expected'], 0) }} ₪)</small>
            </div>
        </div>
    </div>

    {{-- 3. الفلاتر والبحث --}}
    <div class="filter-box-card">
        <form method="GET" action="{{ route('admin.subscriptions.monthly') }}" class="filters-wrap">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="search-cell">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="ابحث باسم الطالب، رقم الهاتف، أو رقم الهوية..." class="search-input">
            </div>

            <div class="filter-cell">
                <select name="stage_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">كافة الفروع والمراحل الدراسية</option>
                    @foreach($stages as $st)
                        <option value="{{ $st->id }}" {{ $stageId == $st->id ? 'selected' : '' }}>{{ $st->label_ar ?? $st->name_ar }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-cell">
                <select name="month" class="filter-select">
                    <option value="">كل الأشهر</option>
                    @foreach($monthsNames as $mNum => $mLabel)
                        <option value="{{ $mNum }}" {{ $monthFilter == $mNum ? 'selected' : '' }}>{{ $mLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-cell">
                <select name="status" class="filter-select">
                    <option value="">كافة الحالات</option>
                    <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>مسدد وخالص ✅</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>قيد المراجعة ⏳</option>
                    <option value="unpaid" {{ $statusFilter === 'unpaid' ? 'selected' : '' }}>غير مسدد ❌</option>
                    <option value="waived" {{ $statusFilter === 'waived' ? 'selected' : '' }}>إعفاء / منحة 🏷️</option>
                </select>
            </div>

            <button type="submit" class="btn-filter-submit"><i class="fa-solid fa-filter"></i> تطبيق</button>

            @if($search || $stageId || $monthFilter || $statusFilter)
                <a href="{{ route('admin.subscriptions.monthly', ['year' => $year]) }}" class="btn-reset-filter">تصفير</a>
            @endif
        </form>

        {{-- دليل الألوان التوضيحي --}}
        <div class="legend-strip">
            <span class="legend-title">دليل الحالات:</span>
            <span class="legend-item"><span class="badge-mini bg-paid"></span> خالص ومسدد (انقر لتعديل)</span>
            <span class="legend-item"><span class="badge-mini bg-pending"></span> قيد المراجعة</span>
            <span class="legend-item"><span class="badge-mini bg-unpaid"></span> غير مسدد</span>
            <span class="legend-item"><span class="badge-mini bg-waived"></span> منحة أو إعفاء كامل</span>
        </div>
    </div>

    {{-- 4. جدول المصفوفة الشهرية الـ 12 --}}
    <div class="table-card-wrapper">
        <div class="table-scroll-wrap">
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th class="sticky-col-1">الطالب</th>
                        <th class="sticky-col-2">الفرع</th>
                        @for($m = 1; $m <= 12; $m++)
                            <th class="month-header-cell">
                                <span class="m-num">{{ sprintf('%02d', $m) }}</span>
                                <span class="m-name">{{ substr($monthsNames[$m], 0, 8) }}</span>
                            </th>
                        @endfor
                        <th class="col-summary">الالتزام</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php
                            $subsByMonth = $student->monthlySubscriptions->keyBy('month');
                            $paidCount = $student->monthlySubscriptions->where('status', 'paid')->count();
                            $waivedCount = $student->monthlySubscriptions->where('status', 'waived')->count();
                            $isFull = ($paidCount + $waivedCount) >= 12;
                        @endphp
                        <tr>
                            <td class="sticky-col-1 student-identity-cell">
                                <div class="student-info-flex">
                                    <img src="{{ $student->photo_url }}" class="student-avatar-mini" alt="{{ $student->name_ar }}">
                                    <div>
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="student-name-link">
                                            {{ $student->name_ar }}
                                        </a>
                                        <small class="student-phone font-mono">{{ $student->phone ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="sticky-col-2">
                                <span class="stage-tag">{{ $student->stage->label_ar ?? ($student->stage->name_ar ?? 'عام') }}</span>
                            </td>

                            @for($m = 1; $m <= 12; $m++)
                                @php
                                    $sub = $subsByMonth[$m] ?? null;
                                    $st = $sub ? $sub->status : 'unpaid';
                                    $subId = $sub ? $sub->id : 0;
                                    $amt = $sub ? (float)$sub->amount : 150;
                                    $notes = $sub ? ($sub->notes ?? '') : '';
                                @endphp
                                <td class="cell-month-action">
                                    <div id="badge_{{ $student->id }}_{{ $m }}" 
                                         class="status-cell-badge badge-{{ $st }}"
                                         onclick="openEditMonthModal({{ $student->id }}, '{{ addslashes($student->name_ar) }}', {{ $m }}, '{{ addslashes($monthsNames[$m]) }}', '{{ $st }}', {{ $amt }}, '{{ addslashes($notes) }}')"
                                         title="انقر لتغيير حالة شهر ({{ $monthsNames[$m] }}) للطالب">
                                        @if($st === 'paid')
                                            <i class="fa-solid fa-check"></i>
                                        @elseif($st === 'pending')
                                            <i class="fa-solid fa-hourglass-half"></i>
                                        @elseif($st === 'waived')
                                            <i class="fa-solid fa-tag"></i>
                                        @else
                                            <i class="fa-solid fa-xmark"></i>
                                        @endif
                                        <span class="amt-tag font-mono">{{ round($amt) }}</span>
                                    </div>
                                </td>
                            @endfor

                            <td class="col-summary">
                                <div class="progress-pill {{ $isFull ? 'pill-full' : '' }}">
                                    <span class="font-mono font-bold">{{ $paidCount }}</span> / 12
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" style="text-align: center; padding: 50px; color: #64748b;">
                                <i class="fa-solid fa-users-slash" style="font-size: 2.8rem; color: #cbd5e1; display: block; margin-bottom: 12px;"></i>
                                <strong>لم يتم العثور على أي طلاب مطابقين لشروط البحث والفلترة.</strong>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $students->links() }}
        </div>
    </div>
</div>

{{-- 5. مودال تعديل حالة شهر محدد لطالب --}}
<div id="editMonthModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box">
        <div class="modal-header-row">
            <div>
                <h3 id="modalStudentNameTitle" style="margin: 0 0 4px; font-size: 1.2rem; color: #0f172a;">تحديث حالة الاشتراك الشهري</h3>
                <p id="modalMonthSubtitle" style="margin: 0; font-size: 0.85rem; color: #64748b;">شهر محدد</p>
            </div>
            <button type="button" class="btn-close-x" onclick="closeEditMonthModal()">&times;</button>
        </div>

        <form id="updateMonthForm" onsubmit="saveMonthSubscription(event)">
            @csrf
            <input type="hidden" name="student_id" id="formStudentId">
            <input type="hidden" name="academic_year" value="{{ $year }}">
            <input type="hidden" name="month" id="formMonth">

            <div class="form-body-wrap">
                <div class="form-field-group">
                    <label class="field-label">حالة السداد والاشتراك لهذا الشهر <span class="required">*</span></label>
                    <div class="status-options-grid">
                        <label class="status-option-label opt-paid">
                            <input type="radio" name="status" value="paid" id="optStatusPaid">
                            <div class="opt-content">
                                <i class="fa-solid fa-circle-check"></i>
                                <strong>مسدد وخالص</strong>
                                <small>تم التحويل والاستلام</small>
                            </div>
                        </label>

                        <label class="status-option-label opt-pending">
                            <input type="radio" name="status" value="pending" id="optStatusPending">
                            <div class="opt-content">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <strong>قيد المراجعة</strong>
                                <small>أرسل الطالب إشعاراً</small>
                            </div>
                        </label>

                        <label class="status-option-label opt-unpaid">
                            <input type="radio" name="status" value="unpaid" id="optStatusUnpaid">
                            <div class="opt-content">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <strong>غير مسدد</strong>
                                <small>قسط مستحق متأخر</small>
                            </div>
                        </label>

                        <label class="status-option-label opt-waived">
                            <input type="radio" name="status" value="waived" id="optStatusWaived">
                            <div class="opt-content">
                                <i class="fa-solid fa-award"></i>
                                <strong>إعفاء / منحة</strong>
                                <small>معفى رسمياً من الإدارة</small>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-field-group">
                    <label class="field-label">مبلغ الاشتراك للشهر (₪) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="amount" id="formAmount" class="clean-input font-mono" required>
                </div>

                <div class="form-field-group">
                    <label class="field-label">ملاحظات وبيان الدفعة (اختياري)</label>
                    <input type="text" name="notes" id="formNotes" class="clean-input" placeholder="مثال: تم السداد عبر جوال باي أو خصم إضافي...">
                </div>
            </div>

            <div class="modal-footer-row">
                <button type="submit" class="btn-save-sub" id="btnSaveSub">
                    <i class="fa-solid fa-check"></i> حفظ التحديث فورياً
                </button>
                <button type="button" class="btn-cancel-sub" onclick="closeEditMonthModal()">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openEditMonthModal(studentId, studentName, month, monthLabel, currentStatus, currentAmount, currentNotes) {
        document.getElementById('formStudentId').value = studentId;
        document.getElementById('formMonth').value = month;
        document.getElementById('modalStudentNameTitle').innerText = studentName;
        document.getElementById('modalMonthSubtitle').innerText = `اشتراك ${monthLabel} (${yearString()})`;
        document.getElementById('formAmount').value = currentAmount;
        document.getElementById('formNotes').value = currentNotes || '';

        // تحديد زر الراديو المناسب
        const radio = document.querySelector(`input[name="status"][value="${currentStatus}"]`);
        if (radio) radio.checked = true;

        document.getElementById('editMonthModal').style.display = 'flex';
    }

    function yearString() {
        return '{{ $year }}';
    }

    function closeEditMonthModal() {
        document.getElementById('editMonthModal').style.display = 'none';
    }

    function saveMonthSubscription(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveSub');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...';

        const form = document.getElementById('updateMonthForm');
        const formData = new FormData(form);

        axios.post("{{ route('admin.subscriptions.monthly.update') }}", Object.fromEntries(formData))
        .then(res => {
            closeEditMonthModal();
            const sId = formData.get('student_id');
            const m = formData.get('month');
            const targetBadge = document.getElementById(`badge_${sId}_${m}`);
            if (targetBadge) {
                targetBadge.className = `status-cell-badge badge-${res.data.status}`;
                let icon = '<i class="fa-solid fa-xmark"></i>';
                if (res.data.status === 'paid') icon = '<i class="fa-solid fa-check"></i>';
                else if (res.data.status === 'pending') icon = '<i class="fa-solid fa-hourglass-half"></i>';
                else if (res.data.status === 'waived') icon = '<i class="fa-solid fa-tag"></i>';

                targetBadge.innerHTML = `${icon} <span class="amt-tag font-mono">${Math.round(formData.get('amount'))}</span>`;
            }

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: res.data.message,
                showConfirmButton: false,
                timer: 2500
            });
        })
        .catch(err => {
            Swal.fire('خطأ', err.response?.data?.message || 'فشل تحديث حالة الاشتراك', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> حفظ التحديث فورياً';
        });
    }

    window.onclick = function(e) {
        const modal = document.getElementById('editMonthModal');
        if (e.target === modal) closeEditMonthModal();
    }
</script>

<style>
    .subs-matrix-wrapper {
        max-width: 1540px;
        margin: 0 auto;
        padding: 24px 20px 80px;
    }

    /* Header */
    .matrix-header-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 24px;
        padding: 30px 28px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.2);
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
        font-size: 1.6rem;
        font-weight: 800;
        margin: 0 0 6px;
    }
    .page-subtitle {
        margin: 0;
        color: #cbd5e1;
        font-size: 0.9rem;
    }
    .year-form {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        padding: 6px 14px;
        border-radius: 12px;
    }
    .year-form label {
        font-size: 0.85rem;
        color: #cbd5e1;
    }
    .year-select {
        background: transparent;
        border: none;
        color: #fff;
        font-weight: 800;
        outline: none;
        cursor: pointer;
    }
    .year-select option {
        background: #1e293b;
        color: #fff;
    }

    /* KPI Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }
    .kpi-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .kpi-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        font-size: 1.6rem;
    }
    .kpi-card.green .kpi-icon { background: #ecfdf5; color: #059669; }
    .kpi-card.red .kpi-icon { background: #fef2f2; color: #dc2626; }
    .kpi-card.amber .kpi-icon { background: #fffbeb; color: #d97706; }
    .kpi-card.blue .kpi-icon { background: #f0f9ff; color: #0284c7; }
    .kpi-label {
        font-size: 0.82rem;
        color: #64748b;
        font-weight: 700;
        display: block;
        margin-bottom: 4px;
    }
    .kpi-num {
        font-size: 1.5rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .kpi-desc {
        font-size: 0.76rem;
        color: #94a3b8;
    }

    /* Filters */
    .filter-box-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 18px 22px;
        margin-bottom: 24px;
    }
    .filters-wrap {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 14px;
    }
    .search-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 8px 14px;
        flex: 1;
        min-width: 280px;
    }
    .search-cell i { color: #94a3b8; }
    .search-input {
        border: none;
        background: transparent;
        outline: none;
        width: 100%;
        font-size: 0.9rem;
    }
    .filter-select {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 8px 14px;
        font-size: 0.88rem;
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }
    .btn-filter-submit {
        background: #0284c7;
        color: #fff;
        border: none;
        padding: 9px 18px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.88rem;
        cursor: pointer;
    }
    .btn-reset-filter {
        color: #ef4444;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
    }
    .legend-strip {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
        font-size: 0.8rem;
        color: #475569;
    }
    .legend-title { font-weight: 800; color: #0f172a; }
    .legend-item { display: inline-flex; align-items: center; gap: 6px; }
    .badge-mini {
        width: 12px;
        height: 12px;
        border-radius: 4px;
        display: inline-block;
    }
    .bg-paid { background: #059669; }
    .bg-pending { background: #d97706; }
    .bg-unpaid { background: #dc2626; }
    .bg-waived { background: #4f46e5; }

    /* Matrix Table */
    .table-card-wrapper {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    }
    .table-scroll-wrap {
        overflow-x: auto;
    }
    .matrix-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        text-align: center;
    }
    .matrix-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.82rem;
        font-weight: 800;
        padding: 12px 10px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .matrix-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
        vertical-align: middle;
    }
    .sticky-col-1 {
        position: sticky;
        right: 0;
        background: #fff;
        z-index: 2;
        text-align: right;
        min-width: 220px;
        border-left: 1px solid #e2e8f0;
    }
    .matrix-table tbody tr:hover .sticky-col-1,
    .matrix-table tbody tr:hover .sticky-col-2 {
        background: #f8fafc;
    }
    .sticky-col-2 {
        position: sticky;
        right: 220px;
        background: #fff;
        z-index: 2;
        min-width: 110px;
        border-left: 2px solid #e2e8f0;
    }
    .month-header-cell {
        min-width: 78px;
    }
    .m-num {
        display: block;
        font-family: monospace;
        color: #0284c7;
        font-size: 0.9rem;
    }
    .m-name {
        font-size: 0.72rem;
        color: #64748b;
    }

    .student-identity-cell {
        padding-right: 14px !important;
    }
    .student-info-flex {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .student-avatar-mini {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
    }
    .student-name-link {
        font-weight: 800;
        color: #0f172a;
        text-decoration: none;
        display: block;
    }
    .student-name-link:hover { color: #0284c7; }
    .student-phone {
        font-size: 0.76rem;
        color: #64748b;
    }
    .stage-tag {
        font-size: 0.75rem;
        font-weight: 700;
        background: #f1f5f9;
        padding: 3px 8px;
        border-radius: 6px;
        color: #334155;
    }

    /* Month Badges */
    .status-cell-badge {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 48px;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.15s;
        font-size: 0.95rem;
        gap: 2px;
    }
    .status-cell-badge:hover {
        transform: scale(1.12);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .amt-tag {
        font-size: 0.65rem;
        font-weight: 800;
        opacity: 0.85;
    }
    .badge-paid {
        background: #ecfdf5;
        color: #059669;
        border: 1.5px solid #10b981;
    }
    .badge-pending {
        background: #fffbeb;
        color: #d97706;
        border: 1.5px solid #f59e0b;
    }
    .badge-unpaid {
        background: #fef2f2;
        color: #dc2626;
        border: 1px dashed #fca5a5;
    }
    .badge-waived {
        background: #eef2ff;
        color: #4f46e5;
        border: 1.5px solid #818cf8;
    }

    .col-summary {
        min-width: 100px;
    }
    .progress-pill {
        background: #f1f5f9;
        color: #334155;
        padding: 5px 12px;
        border-radius: 12px;
        font-size: 0.85rem;
        display: inline-block;
    }
    .pill-full {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    /* Modal */
    .modal-overlay {
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
    .modal-card-box {
        background: #fff;
        border-radius: 24px;
        max-width: 580px;
        width: 100%;
        padding: 28px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
    .modal-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 16px;
        margin-bottom: 20px;
    }
    .btn-close-x {
        background: transparent;
        border: none;
        font-size: 1.6rem;
        cursor: pointer;
        color: #64748b;
    }
    .form-body-wrap {
        display: flex;
        flex-direction: column;
        gap: 18px;
        margin-bottom: 24px;
    }
    .form-field-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .field-label {
        font-size: 0.88rem;
        font-weight: 700;
        color: #334155;
    }
    .clean-input {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.92rem;
        outline: none;
        box-sizing: border-box;
        width: 100%;
    }
    .clean-input:focus {
        border-color: #0284c7;
        background: #fff;
    }

    .status-options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .status-option-label {
        display: block;
        cursor: pointer;
    }
    .status-option-label input { display: none; }
    .opt-content {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        transition: 0.2s;
    }
    .opt-content i { font-size: 1.3rem; margin-bottom: 2px; }
    .opt-content strong { font-size: 0.88rem; }
    .opt-content small { font-size: 0.72rem; opacity: 0.8; }

    .status-option-label input:checked + .opt-content {
        border-width: 2px;
        transform: translateY(-2px);
    }
    .opt-paid input:checked + .opt-content { background: #ecfdf5; border-color: #10b981; color: #059669; }
    .opt-pending input:checked + .opt-content { background: #fffbeb; border-color: #f59e0b; color: #b45309; }
    .opt-unpaid input:checked + .opt-content { background: #fef2f2; border-color: #ef4444; color: #dc2626; }
    .opt-waived input:checked + .opt-content { background: #eef2ff; border-color: #6366f1; color: #4338ca; }

    .modal-footer-row {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn-save-sub {
        background: #0284c7;
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
    }
    .btn-cancel-sub {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
    }
</style>
@endsection
