@extends('layouts.app')

@section('title', 'إدارة مسير وصرف رواتب المعلمين - منصة منارة التوجيهي')

@section('content')
<div class="admin-payroll-wrapper">
    {{-- 1. الهيدر --}}
    <div class="payroll-header-box">
        <div>
            <div class="badge-tag">
                <i class="fa-solid fa-money-bill-transfer"></i>
                <span>الإدارة المالية وشؤون الكادر التعليمي</span>
            </div>
            <h1 class="page-title">إدارة ومسير رواتب المعلمين لشهر وعام محدد</h1>
            <p class="page-subtitle">تسجيل مستحقات المعلمين، صرف الرواتب الشهرية، وإصدار قسائم الراتب الرسمية</p>
        </div>
        <div class="header-btns">
            <button type="button" class="btn-create-salary" onclick="openCreateSalaryModal()">
                <i class="fa-solid fa-plus-circle"></i> تسجيل / صرف راتب معلم جديد
            </button>
        </div>
    </div>

    {{-- 2. إحصائيات الرواتب الكلاسيكية --}}
    <div class="stats-row-clean">
        <div class="stat-card-clean" style="--card-accent: #059669;">
            <span class="stat-label">إجمالي الرواتب المصروفة (عام {{ $year }})</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-emerald">{{ number_format($stats['total_disbursed'], 2) }} ₪</span>
                <i class="fa-solid fa-vault stat-icon text-emerald"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #d97706;">
            <span class="stat-label">مستحقات قيد الاعتماد والصرف</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-amber">{{ number_format($stats['total_pending'], 2) }} ₪</span>
                <i class="fa-solid fa-hourglass-start stat-icon text-amber"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #1e3a8a;">
            <span class="stat-label">إجمالي المكافآت والحوافز</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-navy">{{ number_format($stats['total_bonus'], 2) }} ₪</span>
                <i class="fa-solid fa-award stat-icon text-navy"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #6366f1;">
            <span class="stat-label">عدد المعلمين المسجلين</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-indigo">{{ $stats['teachers_count'] }} معلماً</span>
                <i class="fa-solid fa-users-rectangle stat-icon text-indigo"></i>
            </div>
        </div>
    </div>

    {{-- 3. شريط الفلاتر والبحث --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.teachers.salaries') }}" class="filter-form">
            <div class="filter-group">
                <label>السنة:</label>
                <select name="year" class="form-select-sm" onchange="this.form.submit()">
                    @for($y = date('Y') + 1; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }} م</option>
                    @endfor
                </select>
            </div>

            <div class="filter-group">
                <label>المعلم:</label>
                <select name="teacher_id" class="form-select-sm" onchange="this.form.submit()">
                    <option value="">كافة المعلمين</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ $teacherId == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ $t->subject->name_ar ?? 'عام' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>الشهر:</label>
                <select name="month" class="form-select-sm" onchange="this.form.submit()">
                    <option value="">كافة أشهر السنة</option>
                    @foreach($monthsNames as $mNum => $mLabel)
                        <option value="{{ $mNum }}" {{ $monthFilter == $mNum ? 'selected' : '' }}>{{ $mLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>الحالة:</label>
                <select name="status" class="form-select-sm" onchange="this.form.submit()">
                    <option value="">الكل</option>
                    <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>تم الصرف ✅</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>قيد الاعتماد ⏳</option>
                </select>
            </div>

            @if($teacherId || $monthFilter || $statusFilter)
                <a href="{{ route('admin.teachers.salaries', ['year' => $year]) }}" class="btn-clear-filter">إلغاء الفلترة</a>
            @endif
        </form>
    </div>

    {{-- 4. جدول وبطاقات الرواتب (خالٍ تماماً من السكرول الأفقي) --}}
    <div class="table-container-card">
        {{-- عرض الجدول للشاشات الكبيرة --}}
        <div class="payroll-table-wrap">
            <table class="payroll-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">المعلم والمادة</th>
                        <th style="width: 15%;">الشهر والعام</th>
                        <th style="width: 22%;">تفاصيل الراتب</th>
                        <th style="width: 14%;">صافي الراتب</th>
                        <th style="width: 14%;">حالة وطريقة الصرف</th>
                        <th style="width: 10%; text-align: center;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaries as $sal)
                        @php
                            $isPaid = $sal->status === 'paid';
                            $monthLabel = $monthsNames[$sal->month] ?? "شهر {$sal->month}";
                        @endphp
                        <tr>
                            <td>
                                <div class="teacher-meta-cell">
                                    <img src="{{ $sal->teacher->photo ? asset('storage/' . $sal->teacher->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($sal->teacher->name) . '&background=0284c7&color=fff&size=80&bold=true' }}" class="teacher-thumb">
                                    <div class="teacher-meta-text">
                                        <strong class="teacher-name-text">{{ $sal->teacher->name }}</strong>
                                        <span class="teacher-subject-pill">{{ $sal->teacher->subject->name_ar ?? 'كادر التدريس' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="month-pill"><i class="fa-regular fa-calendar-check"></i> {{ $monthLabel }} {{ $sal->year }}</span>
                            </td>
                            <td>
                                <div class="salary-breakdown-compact">
                                    <span class="base-badge font-mono">الأساسي: {{ number_format($sal->basic_salary, 2) }} ₪</span>
                                    @if($sal->bonus > 0)
                                        <span class="bonus-badge font-mono">+{{ number_format($sal->bonus, 2) }} ₪ إضافي</span>
                                    @endif
                                    @if($sal->deductions > 0)
                                        <span class="deduct-badge font-mono">-{{ number_format($sal->deductions, 2) }} ₪ خصم</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="font-mono font-bold text-base {{ $isPaid ? 'text-paid' : 'text-pending' }}">
                                    {{ number_format($sal->net_salary, 2) }} ₪
                                </span>
                            </td>
                            <td>
                                <div class="payment-col-wrap">
                                    <span class="status-pill {{ $isPaid ? 'status-paid' : 'status-pending' }}">
                                        {{ $isPaid ? 'تم الصرف ✅' : 'قيد الصرف ⏳' }}
                                    </span>
                                    <small class="payment-method-label">{{ $sal->payment_method ?: 'تحويل بنكي' }} {{ $sal->payment_date ? '(' . $sal->payment_date->format('m/d') . ')' : '' }}</small>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div class="actions-group">
                                    <button type="button" class="btn-action-edit" onclick='openEditSalaryModal(@json($sal))' title="تعديل بيانات الراتب">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn-action-delete" onclick="deleteSalaryRecord({{ $sal->id }})" title="حذف السجل">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                                <i class="fa-solid fa-receipt" style="font-size: 2.5rem; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
                                <strong>لا توجد سجلات رواتب مدخلة تطابق معايير الفلترة المحددة.</strong>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- عرض بطاقات الجوال والأجهزة اللوحية (بدون أي سكرول أفقي نهائياً) --}}
        <div class="payroll-cards-mobile">
            @forelse($salaries as $sal)
                @php
                    $isPaid = $sal->status === 'paid';
                    $monthLabel = $monthsNames[$sal->month] ?? "شهر {$sal->month}";
                @endphp
                <div class="payroll-mob-card">
                    <div class="mob-card-header">
                        <div class="teacher-meta-cell">
                            <img src="{{ $sal->teacher->photo ? asset('storage/' . $sal->teacher->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($sal->teacher->name) . '&background=0284c7&color=fff&size=80&bold=true' }}" class="teacher-thumb">
                            <div>
                                <strong class="teacher-name-text">{{ $sal->teacher->name }}</strong>
                                <span class="teacher-subject-pill">{{ $sal->teacher->subject->name_ar ?? 'كادر التدريس' }}</span>
                            </div>
                        </div>
                        <span class="status-pill {{ $isPaid ? 'status-paid' : 'status-pending' }}">
                            {{ $isPaid ? 'تم الصرف ✅' : 'قيد الصرف ⏳' }}
                        </span>
                    </div>

                    <div class="mob-card-body">
                        <div class="mob-stat-row">
                            <span class="mob-label">الشهر والسنة:</span>
                            <span class="month-pill font-mono">{{ $monthLabel }} {{ $sal->year }}</span>
                        </div>
                        <div class="mob-stat-row">
                            <span class="mob-label">صافي الراتب:</span>
                            <strong class="font-mono text-base {{ $isPaid ? 'text-paid' : 'text-pending' }}">{{ number_format($sal->net_salary, 2) }} ₪</strong>
                        </div>
                        <div class="mob-stat-row">
                            <span class="mob-label">تفاصيل:</span>
                            <div class="salary-breakdown-compact">
                                <span class="base-badge font-mono">أساسي: {{ number_format($sal->basic_salary, 0) }} ₪</span>
                                @if($sal->bonus > 0)
                                    <span class="bonus-badge font-mono">+{{ number_format($sal->bonus, 0) }} ₪</span>
                                @endif
                                @if($sal->deductions > 0)
                                    <span class="deduct-badge font-mono">-{{ number_format($sal->deductions, 0) }} ₪</span>
                                @endif
                            </div>
                        </div>
                        <div class="mob-stat-row">
                            <span class="mob-label">الصرف:</span>
                            <span style="font-size: 0.8rem; color: #64748b;">{{ $sal->payment_method ?: 'تحويل بنكي' }} {{ $sal->payment_date ? '(' . $sal->payment_date->format('Y-m-d') . ')' : '' }}</span>
                        </div>
                    </div>

                    <div class="mob-card-footer">
                        <button type="button" class="btn-mob-edit" onclick='openEditSalaryModal(@json($sal))'>
                            <i class="fa-solid fa-pen"></i> تعديل
                        </button>
                        <button type="button" class="btn-mob-delete" onclick="deleteSalaryRecord({{ $sal->id }})">
                            <i class="fa-solid fa-trash-can"></i> حذف
                        </button>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 30px; color: #64748b;">
                    <i class="fa-solid fa-receipt" style="font-size: 2.2rem; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                    <strong>لا توجد سجلات رواتب مدخلة.</strong>
                </div>
            @endforelse
        </div>

        <div style="margin-top: 20px;">
            {{ $salaries->links() }}
        </div>
    </div>
</div>

{{-- 5. مودال تسجيل أو تعديل راتب معلم --}}
<div id="salaryFormModal" class="modal-overlay" style="display: none;">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalSalaryTitle">تسجيل وصرف راتب معلم</h3>
            <button type="button" class="btn-close-modal" onclick="closeSalaryModal()">&times;</button>
        </div>

        <form id="salarySaveForm" onsubmit="handleSaveSalary(event)">
            @csrf
            <div class="modal-body-form">
                <div class="form-row-2">
                    <div class="form-col">
                        <label class="form-label">المعلم <span class="required">*</span></label>
                        <select name="teacher_id" id="modalTeacherId" class="modal-input" required>
                            <option value="">اختر المعلم</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->subject->name_ar ?? 'عام' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-col">
                        <label class="form-label">السنة المالية <span class="required">*</span></label>
                        <input type="number" name="year" id="modalYear" class="modal-input" value="{{ $year }}" required min="2024" max="2030">
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-col">
                        <label class="form-label">شهر الراتب <span class="required">*</span></label>
                        <select name="month" id="modalMonth" class="modal-input" required>
                            @foreach($monthsNames as $mNum => $mLabel)
                                <option value="{{ $mNum }}" {{ date('n') == $mNum ? 'selected' : '' }}>{{ $mLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-col">
                        <label class="form-label">حالة الصرف <span class="required">*</span></label>
                        <select name="status" id="modalStatus" class="modal-input" required>
                            <option value="paid">تم الصرف والاستلام ✅</option>
                            <option value="pending">قيد الاعتماد والإجراء ⏳</option>
                        </select>
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-col">
                        <label class="form-label">الراتب الأساسي (₪) <span class="required">*</span></label>
                        <input type="number" step="0.01" name="basic_salary" id="modalBasicSalary" class="modal-input" placeholder="0.00" required oninput="calcNetSalary()">
                    </div>

                    <div class="form-col">
                        <label class="form-label">المكافآت والحوافز (₪)</label>
                        <input type="number" step="0.01" name="bonus" id="modalBonus" class="modal-input" value="0.00" oninput="calcNetSalary()">
                    </div>

                    <div class="form-col">
                        <label class="form-label">الخصومات (₪)</label>
                        <input type="number" step="0.01" name="deductions" id="modalDeductions" class="modal-input" value="0.00" oninput="calcNetSalary()">
                    </div>
                </div>

                <div class="net-calc-box">
                    <span>صافي الراتب المستحق تلقائياً:</span>
                    <strong id="modalNetDisplay" class="font-mono">0.00 ₪</strong>
                </div>

                <div class="form-row-2">
                    <div class="form-col">
                        <label class="form-label">طريقة الصرف</label>
                        <select name="payment_method" id="modalPaymentMethod" class="modal-input">
                            <option value="تحويل بنكي">تحويل بنكي</option>
                            <option value="جوال باي (Jawwal Pay)">جوال باي (Jawwal Pay)</option>
                            <option value="بال باي (PalPay)">بال باي (PalPay)</option>
                            <option value="سداد نقدي (كاش)">سداد نقدي (كاش)</option>
                            <option value="شيك بنكي">شيك بنكي</option>
                        </select>
                    </div>

                    <div class="form-col">
                        <label class="form-label">تاريخ الصرف</label>
                        <input type="date" name="payment_date" id="modalPaymentDate" class="modal-input" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="form-row-full">
                    <label class="form-label">رقم السند / المرجع</label>
                    <input type="text" name="reference_no" id="modalReferenceNo" class="modal-input" placeholder="مثال: REF-9842">
                </div>

                <div class="form-row-full">
                    <label class="form-label">ملاحظات وبيان الصرف</label>
                    <textarea name="notes" id="modalNotes" rows="2" class="modal-input" placeholder="أي تفاصيل أو ملاحظات تخص الصرف..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-modal-save" id="btnSaveSalary">
                    <i class="fa-solid fa-check"></i> حفظ واعتماد الراتب
                </button>
                <button type="button" class="btn-modal-cancel" onclick="closeSalaryModal()">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function calcNetSalary() {
        const basic = parseFloat(document.getElementById('modalBasicSalary').value || 0);
        const bonus = parseFloat(document.getElementById('modalBonus').value || 0);
        const deductions = parseFloat(document.getElementById('modalDeductions').value || 0);
        const net = Math.max(0, (basic + bonus) - deductions);
        document.getElementById('modalNetDisplay').innerText = net.toFixed(2) + ' ₪';
    }

    function openCreateSalaryModal() {
        document.getElementById('modalSalaryTitle').innerText = 'تسجيل وصرف راتب معلم جديد';
        document.getElementById('salarySaveForm').reset();
        document.getElementById('modalYear').value = '{{ $year }}';
        document.getElementById('modalPaymentDate').value = '{{ date("Y-m-d") }}';
        document.getElementById('modalBasicSalary').value = '';
        document.getElementById('modalBonus').value = '0.00';
        document.getElementById('modalDeductions').value = '0.00';
        calcNetSalary();
        document.getElementById('salaryFormModal').style.display = 'flex';
    }

    function openEditSalaryModal(salary) {
        document.getElementById('modalSalaryTitle').innerText = 'تعديل مسير راتب المعلم';
        document.getElementById('modalTeacherId').value = salary.teacher_id;
        document.getElementById('modalYear').value = salary.year;
        document.getElementById('modalMonth').value = salary.month;
        document.getElementById('modalStatus').value = salary.status;
        document.getElementById('modalBasicSalary').value = salary.basic_salary;
        document.getElementById('modalBonus').value = salary.bonus;
        document.getElementById('modalDeductions').value = salary.deductions;
        document.getElementById('modalPaymentMethod').value = salary.payment_method || 'تحويل بنكي';
        document.getElementById('modalPaymentDate').value = salary.payment_date ? salary.payment_date.split('T')[0] : '';
        document.getElementById('modalReferenceNo').value = salary.reference_no || '';
        document.getElementById('modalNotes').value = salary.notes || '';
        calcNetSalary();
        document.getElementById('salaryFormModal').style.display = 'flex';
    }

    function closeSalaryModal() {
        document.getElementById('salaryFormModal').style.display = 'none';
    }

    function handleSaveSalary(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveSalary');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...';

        const form = document.getElementById('salarySaveForm');
        const formData = new FormData(form);

        axios.post("{{ route('admin.teachers.salaries.store') }}", Object.fromEntries(formData))
        .then(res => {
            closeSalaryModal();
            Swal.fire({
                icon: 'success',
                title: 'تم الحفظ بنجاح',
                text: res.data.message,
                confirmButtonColor: '#059669'
            }).then(() => location.reload());
        })
        .catch(err => {
            Swal.fire('خطأ', err.response?.data?.message || 'حدث خطأ أثناء حفظ الراتب', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> حفظ واعتماد الراتب';
        });
    }

    function deleteSalaryRecord(id) {
        Swal.fire({
            title: 'هل أنت متأكد من حذف السجل؟',
            text: 'سيتم حذف سجل مسير الراتب نهائياً!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`{{ url('admin/teachers/salaries') }}/${id}`)
                .then(res => {
                    Swal.fire('تم الحذف', res.data.message, 'success').then(() => location.reload());
                })
                .catch(err => Swal.fire('خطأ', 'فشل حذف السجل', 'error'));
            }
        });
    }
</script>

<style>
    .admin-payroll-wrapper {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 10px 0 60px;
        box-sizing: border-box;
        overflow-x: hidden;
    }
    .payroll-header-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
    }
    .badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        color: #475569;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .page-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .page-subtitle {
        margin: 0;
        color: #64748b;
        font-size: 0.92rem;
    }
    .btn-create-salary {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: #fff;
        border: none;
        padding: 12px 22px;
        border-radius: 14px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
    }
    .btn-create-salary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(2, 132, 199, 0.35);
    }

    /* Stats Cards */
    .stats-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 1.5rem;
    }
    .stat-icon.emerald { background: #ecfdf5; color: #059669; }
    .stat-icon.amber { background: #fffbeb; color: #d97706; }
    .stat-icon.blue { background: #f0f9ff; color: #0284c7; }
    .stat-icon.purple { background: #faf5ff; color: #9333ea; }
    .stat-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 700;
        display: block;
        margin-bottom: 4px;
    }
    .stat-value {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    /* Filter */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 16px 20px;
        margin-bottom: 24px;
    }
    .filter-form {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    .filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-group label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #475569;
    }
    .form-select-sm {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 7px 12px;
        font-size: 0.88rem;
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }
    .btn-clear-filter {
        color: #ef4444;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
    }

    /* Table & Container (بدون سكرول أفقي) */
    .table-container-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px;
        width: 100%;
        box-sizing: border-box;
        overflow-x: hidden;
    }
    .payroll-table-wrap {
        width: 100%;
        overflow-x: hidden;
    }
    .payroll-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }
    .payroll-table th {
        background: #f8fafc !important;
        padding: 12px 14px;
        font-size: 0.82rem;
        font-weight: 800;
        color: #0f172a !important;
        border-bottom: 2px solid #cbd5e1 !important;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }
    .payroll-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.86rem;
        vertical-align: middle;
        color: #1e293b;
    }
    .teacher-meta-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .teacher-thumb {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .teacher-meta-text {
        min-width: 0;
        display: flex;
        flex-direction: column;
    }
    .teacher-name-text {
        font-weight: 800;
        color: #0f172a;
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .teacher-subject-pill {
        font-size: 0.72rem;
        color: #0284c7;
        background: #f0f9ff;
        padding: 1px 6px;
        border-radius: 4px;
        width: fit-content;
        margin-top: 2px;
    }
    .month-pill {
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.82rem;
        color: #334155;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .salary-breakdown-compact {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .base-badge { font-size: 0.8rem; color: #334155; font-weight: 600; }
    .bonus-badge { font-size: 0.72rem; color: #059669; background: #ecfdf5; padding: 1px 6px; border-radius: 4px; display: inline-block; width: fit-content; }
    .deduct-badge { font-size: 0.72rem; color: #dc2626; background: #fef2f2; padding: 1px 6px; border-radius: 4px; display: inline-block; width: fit-content; }
    .text-paid { color: #059669; }
    .text-pending { color: #d97706; }
    .payment-col-wrap {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .payment-method-label {
        font-size: 0.74rem;
        color: #64748b;
    }
    .status-pill {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.78rem;
        font-weight: 700;
        width: fit-content;
    }
    .status-paid { background: #ecfdf5; color: #059669; }
    .status-pending { background: #fffbeb; color: #b45309; }

    .actions-group {
        display: flex;
        justify-content: center;
        gap: 8px;
    }
    .btn-action-edit {
        background: #f0f9ff;
        color: #0284c7;
        border: 1px solid #bae6fd;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        cursor: pointer;
    }
    .btn-action-delete {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        cursor: pointer;
    }

    /* بطاقات الجوال بدون سكرول نهائياً */
    .payroll-cards-mobile {
        display: none;
    }

    @media (max-width: 960px) {
        .payroll-table-wrap { display: none; }
        .payroll-cards-mobile {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            width: 100%;
        }
        .payroll-mob-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }
        .mob-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }
        .mob-card-body {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .mob-stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.88rem;
        }
        .mob-label {
            font-weight: 700;
            color: #64748b;
            font-size: 0.82rem;
        }
        .mob-card-footer {
            display: flex;
            gap: 8px;
            border-top: 1px solid #f1f5f9;
            padding-top: 12px;
        }
        .btn-mob-edit {
            flex: 1;
            background: #f0f9ff;
            color: #0284c7;
            border: 1px solid #bae6fd;
            padding: 8px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.84rem;
        }
        .btn-mob-delete {
            flex: 1;
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fecaca;
            padding: 8px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.84rem;
        }
    }

    @media (max-width: 640px) {
        .stats-cards-grid {
            grid-template-columns: 1fr;
        }
        .form-row-2, .form-row-3 {
            grid-template-columns: 1fr;
        }
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-box {
        background: #ffffff;
        border-radius: 20px;
        max-width: 680px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
    }
    .btn-close-modal {
        background: transparent;
        border: none;
        font-size: 1.6rem;
        cursor: pointer;
        color: #64748b;
    }
    .modal-body-form {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .form-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 14px;
    }
    .form-col {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .form-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
    }
    .modal-input {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 0.92rem;
        outline: none;
        font-family: inherit;
        box-sizing: border-box;
    }
    .modal-input:focus {
        border-color: #0284c7;
        background: #fff;
    }
    .net-calc-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        padding: 12px 18px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #166534;
        font-size: 0.92rem;
    }
    .net-calc-box strong {
        font-size: 1.3rem;
        color: #15803d;
    }
    .modal-footer {
        padding: 18px 24px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn-modal-save {
        background: #0284c7;
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
    }
    .btn-modal-cancel {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
    }
</style>
@endsection
