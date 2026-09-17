@extends('layouts.app')

@section('title', __('مصفوفة الاشتراكات الشهرية للطلاب (12 شهراً)') . ' - ' . __('منارة التوجيهي'))

@section('content')
<div class="subs-matrix-wrapper">
    {{-- 1. الهيدر --}}
    <div class="matrix-header-card">
        <div class="header-info">
            <div class="badge-tag">
                <i class="fa-solid fa-calendar-days"></i>
                <span>{{ __('الإدارة المالية ومتابعة الاشتراكات السنوية') }}</span>
            </div>
            <h1 class="page-title">{{ __('مصفوفة الاشتراكات الشهرية للطلاب (12 شهراً)') }}</h1>
            <p class="page-subtitle">{{ __('متابعة دفعات وأقساط الطلاب شهراً بشهر لكامل السنة الدراسية وتحديث الحالات فورياً بدون تعقيد') }}</p>
        </div>
        <div class="header-tools">
            <form method="GET" action="{{ route('admin.subscriptions.monthly') }}" class="year-form">
                <label>{{ __('العام الدراسي:') }}</label>
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
                <span class="kpi-label">{{ __('المحصل الفعلي المعتمد') }}</span>
                <h3 class="kpi-num font-mono">{{ number_format($stats['total_collected'], 2) }} ₪</h3>
                <small class="kpi-desc">{{ __('تم تأكيد سداده') }} ({{ $stats['paid_count'] }} قسط)</small>
            </div>
        </div>

        <div class="kpi-card red">
            <div class="kpi-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="kpi-data">
                <span class="kpi-label">{{ __('المتأخرات غير المسددة') }}</span>
                <h3 class="kpi-num font-mono">{{ number_format($stats['total_unpaid'], 2) }} ₪</h3>
                <small class="kpi-desc">{{ __('أقساط مستحقة') }} ({{ $stats['unpaid_count'] }} {{ __('شهراً') }})</small>
            </div>
        </div>

        <div class="kpi-card amber">
            <div class="kpi-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div class="kpi-data">
                <span class="kpi-label">{{ __('إشعارات قيد المراجعة') }}</span>
                <h3 class="kpi-num font-mono">{{ number_format($stats['total_pending'], 2) }} ₪</h3>
                <small class="kpi-desc">{{ __('بحاجة لاعتمادك') }} ({{ $stats['pending_count'] }} {{ __('إشعار') }})</small>
            </div>
        </div>

        <div class="kpi-card blue">
            <div class="kpi-icon"><i class="fa-solid fa-percent"></i></div>
            <div class="kpi-data">
                <span class="kpi-label">{{ __('نسبة الالتزام المالي') }}</span>
                <h3 class="kpi-num font-mono">{{ $stats['collection_rate'] }}%</h3>
                <small class="kpi-desc">{{ __('المستحق:') }} {{ number_format($stats['total_expected'], 0) }} ₪</small>
            </div>
        </div>
    </div>

    {{-- 3. الفلاتر ودليل الألوان --}}
    <div class="filter-box-card">
        <form method="GET" action="{{ route('admin.subscriptions.monthly') }}" class="filters-wrap">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="search-cell">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('ابحث باسم الطالب، الهاتف، أو الهوية...') }}" class="search-input">
            </div>

            <div class="filter-cell">
                <select name="stage_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">{{ __('كافة الفروع والمراحل') }}</option>
                    @foreach($stages as $st)
                        <option value="{{ $st->id }}" {{ $stageId == $st->id ? 'selected' : '' }}>{{ $st->label_ar ?? $st->name_ar }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-cell">
                <select name="month" class="filter-select">
                    <option value="">{{ __('كل الأشهر') }}</option>
                    @foreach($monthsNames as $mNum => $mLabel)
                        <option value="{{ $mNum }}" {{ $monthFilter == $mNum ? 'selected' : '' }}>{{ $mLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-cell">
                <select name="status" class="filter-select">
                    <option value="">{{ __('كافة الحالات') }}</option>
                    <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>{{ __('مسدد وخالص') }} ✅</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>{{ __('قيد المراجعة') }} ⏳</option>
                    <option value="unpaid" {{ $statusFilter === 'unpaid' ? 'selected' : '' }}>{{ __('غير مسدد') }} ❌</option>
                    <option value="waived" {{ $statusFilter === 'waived' ? 'selected' : '' }}>{{ __('إعفاء / منحة') }} 🏷️</option>
                </select>
            </div>

            <button type="submit" class="btn-filter-submit"><i class="fa-solid fa-filter"></i> {{ __('تطبيق') }}</button>

            @if($search || $stageId || $monthFilter || $statusFilter)
                <a href="{{ route('admin.subscriptions.monthly', ['year' => $year]) }}" class="btn-reset-filter">{{ __('تصفير') }}</a>
            @endif
        </form>

        <div class="legend-strip">
            <span class="legend-title">{{ __('دليل الحالات:') }}</span>
            <span class="legend-item"><span class="badge-mini bg-paid"></span> {{ __('خالص ومسدد (انقر لتعديل أي شهر)') }}</span>
            <span class="legend-item"><span class="badge-mini bg-pending"></span> {{ __('قيد المراجعة') }}</span>
            <span class="legend-item"><span class="badge-mini bg-unpaid"></span> {{ __('غير مسدد') }}</span>
            <span class="legend-item"><span class="badge-mini bg-waived"></span> {{ __('إعفاء / منحة كاملة') }}</span>
        </div>
    </div>

    {{-- 4. قائمة الطلاب الانسيابية بدون سكرول أفقي نهائياً --}}
    <div class="students-list-wrapper">
        <div class="list-header-row">
            <span class="col-head-student">{{ __('بيانات الطالب والمرحلة') }}</span>
            <span class="col-head-timeline">{{ __('مسير الشهور الـ 12 (انقر على أي شهر لتغيير حالته فورياً)') }}</span>
            <span class="col-head-progress">{{ __('نسبة السداد') }}</span>
        </div>

        @forelse($students as $student)
            @php
                $subsByMonth = $student->monthlySubscriptions->keyBy('month');
                $paidCount = $student->monthlySubscriptions->where('status', 'paid')->count();
                $waivedCount = $student->monthlySubscriptions->where('status', 'waived')->count();
                $pendingCount = $student->monthlySubscriptions->where('status', 'pending')->count();
                $isFull = ($paidCount + $waivedCount) >= 12;
                $percent = round((($paidCount + $waivedCount) / 12) * 100);
            @endphp
            <div class="student-matrix-row">
                {{-- تعريف الطالب --}}
                <div class="student-profile-block">
                    <img src="{{ $student->photo_url }}" class="student-avatar" alt="{{ $student->name_ar }}">
                    <div class="student-text">
                        <a href="{{ route('admin.students.show', $student->id) }}" class="student-name">
                            {{ $student->name_ar }}
                        </a>
                        <div class="student-sub-line">
                            <span class="branch-pill">{{ $student->stage->label_ar ?? ($student->stage->name_ar ?? 'عام') }}</span>
                            <span class="phone-text font-mono" dir="ltr">{{ $student->phone ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- شريط الشهور الـ 12 المنساب --}}
                <div class="months-strip-grid">
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $sub = $subsByMonth[$m] ?? null;
                            $st = $sub ? $sub->status : 'unpaid';
                            $amt = $sub ? (float)$sub->amount : 150;
                            $notes = $sub ? ($sub->notes ?? '') : '';
                            $mTitle = $monthsNames[$m] ?? "شهر $m";
                        @endphp
                        <div id="badge_{{ $student->id }}_{{ $m }}" 
                             class="month-micro-badge badge-{{ $st }}"
                             onclick="openEditMonthModal({{ $student->id }}, '{{ addslashes($student->name_ar) }}', {{ $m }}, '{{ addslashes($mTitle) }}', '{{ $st }}', {{ $amt }}, '{{ addslashes($notes) }}')"
                             title="{{ $mTitle }} ({{ round($amt) }} ₪) - {{ __('انقر للتعديل') }}">
                            <span class="m-digit font-mono">{{ $m }}</span>
                            @if($st === 'paid')
                                <i class="fa-solid fa-check badge-icon"></i>
                            @elseif($st === 'pending')
                                <i class="fa-solid fa-hourglass-half badge-icon"></i>
                            @elseif($st === 'waived')
                                <i class="fa-solid fa-tag badge-icon"></i>
                            @else
                                <i class="fa-solid fa-xmark badge-icon"></i>
                            @endif
                        </div>
                    @endfor
                </div>

                {{-- إحصائية الالتزام --}}
                <div class="student-progress-block">
                    <div class="progress-ratio font-mono">
                        <strong>{{ $paidCount }}</strong> / 12 {{ __('شهراً') }}
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill {{ $isFull ? 'bg-full' : '' }}" style="width: {{ $percent }}%;"></div>
                    </div>
                    <span class="progress-percent-label font-mono">{{ $percent }}%</span>
                </div>
            </div>
        @empty
            <div class="empty-matrix-card">
                <i class="fa-solid fa-users-slash"></i>
                <p>{{ __('لم يتم العثور على أي طلاب مطابقين لشروط البحث والفلترة.') }}</p>
            </div>
        @endforelse

        <div style="margin-top: 24px;">
            {{ $students->links() }}
        </div>
    </div>
</div>

{{-- مودال تعديل حالة شهر محدد لطالب --}}
<div id="editMonthModal" class="modal-overlay" style="display: none;">
    <div class="modal-card-box">
        <div class="modal-header-row">
            <div>
                <h3 id="modalStudentNameTitle" style="margin: 0 0 4px; font-size: 1.2rem; color: #0f172a;">{{ __('تحديث حالة الاشتراك الشهري') }}</h3>
                <p id="modalMonthSubtitle" style="margin: 0; font-size: 0.85rem; color: #64748b;">{{ __('شهر محدد') }}</p>
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
                    <label class="field-label">{{ __('حالة السداد والاشتراك لهذا الشهر') }} <span class="required">*</span></label>
                    <div class="status-options-grid">
                        <label class="status-option-label opt-paid">
                            <input type="radio" name="status" value="paid" id="optStatusPaid">
                            <div class="opt-content">
                                <i class="fa-solid fa-circle-check"></i>
                                <strong>{{ __('مسدد وخالص') }}</strong>
                                <small>{{ __('تم التحويل والاستلام') }}</small>
                            </div>
                        </label>

                        <label class="status-option-label opt-pending">
                            <input type="radio" name="status" value="pending" id="optStatusPending">
                            <div class="opt-content">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <strong>{{ __('قيد المراجعة') }}</strong>
                                <small>{{ __('أرسل الطالب إشعاراً') }}</small>
                            </div>
                        </label>

                        <label class="status-option-label opt-unpaid">
                            <input type="radio" name="status" value="unpaid" id="optStatusUnpaid">
                            <div class="opt-content">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <strong>{{ __('غير مسدد') }}</strong>
                                <small>{{ __('قسط مستحق متأخر') }}</small>
                            </div>
                        </label>

                        <label class="status-option-label opt-waived">
                            <input type="radio" name="status" value="waived" id="optStatusWaived">
                            <div class="opt-content">
                                <i class="fa-solid fa-award"></i>
                                <strong>{{ __('إعفاء / منحة') }}</strong>
                                <small>{{ __('معفى رسمياً من الإدارة') }}</small>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-field-group">
                    <label class="field-label">{{ __('مبلغ الاشتراك للشهر (₪)') }} <span class="required">*</span></label>
                    <input type="number" step="0.01" name="amount" id="formAmount" class="clean-input font-mono" required>
                </div>

                <div class="form-field-group">
                    <label class="field-label">{{ __('ملاحظات وبيان الدفعة (اختياري)') }}</label>
                    <input type="text" name="notes" id="formNotes" class="clean-input" placeholder="{{ __('مثال: تم السداد عبر جوال باي أو خصم إضافي...') }}">
                </div>
            </div>

            <div class="modal-footer-row">
                <button type="submit" class="btn-save-sub" id="btnSaveSub">
                    <i class="fa-solid fa-check"></i> {{ __('حفظ التحديث فورياً') }}
                </button>
                <button type="button" class="btn-cancel-sub" onclick="closeEditMonthModal()">{{ __('إلغاء') }}</button>
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
        document.getElementById('modalMonthSubtitle').innerText = '{{ __('اشتراك') }} ' + monthLabel + ' (' + yearString() + ')';
        document.getElementById('formAmount').value = currentAmount;
        document.getElementById('formNotes').value = currentNotes || '';

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
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}';

        const form = document.getElementById('updateMonthForm');
        const formData = new FormData(form);

        axios.post("{{ route('admin.subscriptions.monthly.update') }}", Object.fromEntries(formData))
        .then(res => {
            closeEditMonthModal();
            const sId = formData.get('student_id');
            const m = formData.get('month');
            const targetBadge = document.getElementById(`badge_${sId}_${m}`);
            if (targetBadge) {
                targetBadge.className = `month-micro-badge badge-${res.data.status}`;
                let icon = '<i class="fa-solid fa-xmark badge-icon"></i>';
                if (res.data.status === 'paid') icon = '<i class="fa-solid fa-check badge-icon"></i>';
                else if (res.data.status === 'pending') icon = '<i class="fa-solid fa-hourglass-half badge-icon"></i>';
                else if (res.data.status === 'waived') icon = '<i class="fa-solid fa-tag badge-icon"></i>';

                targetBadge.innerHTML = `<span class="m-digit font-mono">${m}</span> ${icon}`;
            }

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: res.data.message,
                showConfirmButton: false,
                timer: 2200
            });
        })
        .catch(err => {
            Swal.fire('{{ __('خطأ') }}', err.response?.data?.message || '{{ __('فشل تحديث حالة الاشتراك') }}', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> {{ __('حفظ التحديث فورياً') }}';
        });
    }

    window.onclick = function(e) {
        const modal = document.getElementById('editMonthModal');
        if (e.target === modal) closeEditMonthModal();
    }
</script>

<style>
    /* الحاوية العامة بدون أي سكرول أفقي إطلاقاً */
    .subs-matrix-wrapper {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 10px 0 60px;
        box-sizing: border-box;
        overflow-x: hidden;
    }

    /* Header */
    .matrix-header-card {
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
        border-inline-start: 5px solid var(--ed-primary, #1e3a8a);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        color: #1e40af;
        margin-bottom: 6px;
        font-weight: 700;
    }
    .page-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .page-subtitle {
        margin: 0;
        color: #64748b;
        font-size: 0.85rem;
    }
    .year-form {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 6px 12px;
        border-radius: 8px;
    }
    .year-form label { font-size: 0.82rem; color: #475569; font-weight: 600; }
    .year-select { background: transparent; border: none; color: #0f172a; font-weight: 700; outline: none; cursor: pointer; }
    .year-select option { background: #ffffff; color: #0f172a; }

    /* KPI Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    .kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        display: grid;
        place-items: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .kpi-label { font-size: 0.78rem; color: #64748b; font-weight: 500; display: block; margin-bottom: 2px; }
    .kpi-num { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0 0 2px; }
    .kpi-desc { font-size: 0.72rem; color: #94a3b8; }

    /* Filter Box */
    .filter-box-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }
    .filters-wrap {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }
    .search-cell {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 7px 12px;
        flex: 1;
        min-width: 220px;
    }
    .search-cell i { color: #94a3b8; }
    .search-input { border: none; background: transparent; outline: none; width: 100%; font-size: 0.88rem; }
    .filter-select {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 7px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }
    .btn-filter-submit {
        background: var(--ed-primary, #1d4ed8);
        color: #fff;
        border: none;
        padding: 8px 18px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-filter-submit:hover {
        background: #1e40af;
    }
    .btn-reset-filter { color: #ef4444; font-size: 0.82rem; font-weight: 700; text-decoration: none; }
    .legend-strip {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
        font-size: 0.78rem;
        color: #475569;
    }
    .legend-title { font-weight: 800; color: #0f172a; }
    .legend-item { display: inline-flex; align-items: center; gap: 5px; }
    .badge-mini { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }
    .bg-paid { background: #059669; }
    .bg-pending { background: #d97706; }
    .bg-unpaid { background: #dc2626; }
    .bg-waived { background: #4f46e5; }

    /* قائمة الطلاب الانسيابية - بدون سكرول أفقي نهائياً */
    .students-list-wrapper {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        width: 100%;
        box-sizing: border-box;
    }
    .list-header-row {
        display: grid;
        grid-template-columns: 230px minmax(0, 1fr) 110px;
        gap: 14px;
        padding: 10px 16px;
        background: linear-gradient(135deg, #172554 0%, #1e3a8a 100%);
        border-bottom: 2px solid #f59e0b;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 12px;
    }
    .col-head-student { text-align: right; }
    .col-head-timeline { text-align: center; }
    .col-head-progress { text-align: center; }

    .student-matrix-row {
        display: grid;
        grid-template-columns: 230px minmax(0, 1fr) 110px;
        gap: 14px;
        align-items: center;
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        transition: 0.15s;
        min-width: 0;
    }
    .student-matrix-row:hover {
        background: #f8fafc;
        border-radius: 12px;
    }

    /* تعريف الطالب */
    .student-profile-block {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .student-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .student-text {
        min-width: 0;
        overflow: hidden;
    }
    .student-name {
        font-weight: 800;
        font-size: 0.92rem;
        color: #0f172a;
        text-decoration: none;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .student-name:hover { color: #0284c7; }
    .student-sub-line {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 2px;
    }
    .branch-pill {
        font-size: 0.7rem;
        font-weight: 700;
        background: #f1f5f9;
        color: #334155;
        padding: 2px 6px;
        border-radius: 4px;
        white-space: nowrap;
    }
    .phone-text {
        font-size: 0.74rem;
        color: #64748b;
        white-space: nowrap;
    }

    /* شبكة الشهور الـ 12 المنسابة لملء العرض بدون أي سكرول */
    .months-strip-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 4px;
        width: 100%;
        min-width: 0;
    }
    .month-micro-badge {
        aspect-ratio: 1 / 1;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.15s;
        min-width: 0;
        padding: 2px;
    }
    .month-micro-badge:hover {
        transform: scale(1.18);
        z-index: 10;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .m-digit {
        font-size: 0.72rem;
        font-weight: 900;
        line-height: 1;
    }
    .badge-icon {
        font-size: 0.65rem;
        margin-top: 2px;
    }
    .badge-paid { background: #ecfdf5; color: #059669; border: 1.5px solid #10b981; }
    .badge-pending { background: #fffbeb; color: #d97706; border: 1.5px solid #f59e0b; }
    .badge-unpaid { background: #fef2f2; color: #dc2626; border: 1px dashed #fca5a5; }
    .badge-waived { background: #eef2ff; color: #4f46e5; border: 1.5px solid #818cf8; }

    /* شريط التقدم والنسبة */
    .student-progress-block {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    .progress-ratio {
        font-size: 0.8rem;
        color: #334155;
    }
    .progress-bar-bg {
        width: 100%;
        height: 6px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background: #0284c7;
        border-radius: 10px;
        transition: width 0.3s;
    }
    .progress-bar-fill.bg-full {
        background: #059669;
    }
    .progress-percent-label {
        font-size: 0.72rem;
        font-weight: 800;
        color: #64748b;
    }

    .empty-matrix-card {
        text-align: center;
        padding: 50px 20px;
        color: #64748b;
    }
    .empty-matrix-card i {
        font-size: 2.6rem;
        color: #cbd5e1;
        margin-bottom: 12px;
        display: block;
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
        border-radius: 20px;
        max-width: 520px;
        width: 100%;
        padding: 24px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
    .modal-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 14px;
        margin-bottom: 18px;
    }
    .btn-close-x { background: transparent; border: none; font-size: 1.6rem; cursor: pointer; color: #64748b; }
    .form-body-wrap { display: flex; flex-direction: column; gap: 16px; margin-bottom: 20px; }
    .form-field-group { display: flex; flex-direction: column; gap: 6px; }
    .field-label { font-size: 0.85rem; font-weight: 700; color: #334155; }
    .clean-input {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 9px 12px;
        font-size: 0.9rem;
        outline: none;
        width: 100%;
        box-sizing: border-box;
    }
    .clean-input:focus { border-color: #0284c7; background: #fff; }

    .status-options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .status-option-label { display: block; cursor: pointer; }
    .status-option-label input { display: none; }
    .opt-content {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        transition: 0.2s;
    }
    .opt-content i { font-size: 1.2rem; margin-bottom: 2px; }
    .opt-content strong { font-size: 0.82rem; }
    .opt-content small { font-size: 0.68rem; opacity: 0.8; }
    .status-option-label input:checked + .opt-content { border-width: 2px; transform: translateY(-2px); }
    .opt-paid input:checked + .opt-content { background: #ecfdf5; border-color: #10b981; color: #059669; }
    .opt-pending input:checked + .opt-content { background: #fffbeb; border-color: #f59e0b; color: #b45309; }
    .opt-unpaid input:checked + .opt-content { background: #fef2f2; border-color: #ef4444; color: #dc2626; }
    .opt-waived input:checked + .opt-content { background: #eef2ff; border-color: #6366f1; color: #4338ca; }

    .modal-footer-row { display: flex; justify-content: flex-end; gap: 8px; }
    .btn-save-sub { background: #0284c7; color: #fff; border: none; padding: 9px 20px; border-radius: 10px; font-weight: 700; cursor: pointer; }
    .btn-cancel-sub { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 9px 16px; border-radius: 10px; font-weight: 700; cursor: pointer; }

    /* Responsive Design للأجهزة المتوسطة والصغيرة لمنع أي سكرول نهائياً */
    @media (max-width: 1180px) {
        .list-header-row { display: none; }
        .student-matrix-row {
            grid-template-columns: 1fr;
            gap: 14px;
            padding: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            margin-bottom: 14px;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .student-progress-block {
            flex-direction: row;
            justify-content: space-between;
            width: 100%;
            background: #f8fafc;
            padding: 8px 14px;
            border-radius: 10px;
        }
        .progress-bar-bg { width: 60%; }
    }

    @media (max-width: 768px) {
        .months-strip-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 6px;
        }
        .month-micro-badge {
            min-height: 38px;
        }
        .matrix-header-card {
            padding: 18px 16px;
        }
        .page-title {
            font-size: 1.25rem;
        }
        .filters-wrap {
            flex-direction: column;
            align-items: stretch;
        }
    }

    @media (max-width: 480px) {
        .months-strip-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 6px;
        }
        .progress-bar-bg { width: 45%; }
    }
</style>
@endsection
