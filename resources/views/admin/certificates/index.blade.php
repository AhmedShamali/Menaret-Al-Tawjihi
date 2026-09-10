@extends('layouts.app')

@section('title', 'إدارة واعتماد الشهادات والنتائج | منارة التوجيهي')

@section('content')
<div class="ed-admin-container">

    <!-- رأس الصفحة الرسمي -->
    <header class="ed-admin-header">
        <div class="ed-admin-title-box">
            <div class="ed-admin-breadcrumbs">
                <i class="fas fa-home"></i>
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">لوحة التحكم</a>
                <i class="fas fa-chevron-left divider"></i>
                <span class="active">إدارة الشهادات والنتائج</span>
            </div>
            <h1>إدارة واعتماد الشهادات والنتائج الأكاديمية</h1>
            <p>إعلان نتائج التخرج، اعتماد المعدلات الفعلية للطلبة، وإصدار وثائق التخرج الرسمية المؤمنة.</p>
        </div>

        <div class="ed-admin-status-wrap">
            <button type="button" onclick="openIssueModal()" class="ed-btn ed-btn-primary">
                <i class="fas fa-award"></i>
                <span>اعتماد ورصد شهادة جديدة</span>
            </button>
        </div>
    </header>

    <!-- شبكة مفاتيح التحكم الكبرى (Master Toggles) والبيانات الإحصائية -->
    <div class="ed-cert-controls-grid">

        <!-- بطاقة 1: إعلان ونشر شهادات نهاية العام -->
        <div class="ed-card ed-cert-control-card">
            <div class="ed-control-top">
                <div class="ed-control-icon-header">
                    <div class="ed-control-icon" style="background: {{ $yearEndPublished ? '#ecfdf5' : '#fef2f2' }}; color: {{ $yearEndPublished ? '#059669' : '#dc2626' }};">
                        <i class="fas {{ $yearEndPublished ? 'fa-lock-open' : 'fa-lock' }}"></i>
                    </div>
                    <div>
                        <h3>إعلان شهادات نهاية العام</h3>
                        <span class="ed-control-status" style="color: {{ $yearEndPublished ? '#059669' : '#dc2626' }};">
                            {{ $yearEndPublished ? 'معلنة ومنشورة رسمياً للطلبة 🎓' : 'محجوبة بقرار الإدارة الأكاديمية 🔒' }}
                        </span>
                    </div>
                </div>
                <p class="ed-control-desc">
                    الشهادات محجوبة طوال العام الدراسي افتراضياً. عند تفعيل هذا الخيار بنهاية العام، ستظهر الشهادات المعتمدة في حسابات الطلبة.
                </p>
            </div>

            <button type="button" onclick="togglePublishState()" id="btnTogglePublish" class="ed-btn {{ $yearEndPublished ? 'ed-btn-outline danger' : 'ed-btn-primary' }}" style="width: 100%; justify-content: center;">
                <i class="fas {{ $yearEndPublished ? 'fa-eye-slash' : 'fa-bullhorn' }}"></i>
                <span>{{ $yearEndPublished ? 'حجب الشهادات وإغلاق الإعلان' : 'إعلان ونشر الشهادات للطلبة الآن' }}</span>
            </button>
        </div>

        <!-- بطاقة 2: حاسبة المعدل النهائي للطلبة -->
        <div class="ed-card ed-cert-control-card">
            <div class="ed-control-top">
                <div class="ed-control-icon-header">
                    <div class="ed-control-icon" style="background: {{ $allowStudentGpa ? '#eff6ff' : '#f8fafc' }}; color: {{ $allowStudentGpa ? '#1d4ed8' : '#64748b' }};">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div>
                        <h3>حساب المعدل النهائي للطلبة</h3>
                        <span class="ed-control-status" style="color: {{ $allowStudentGpa ? '#1d4ed8' : '#64748b' }};">
                            {{ $allowStudentGpa ? 'متاح للطلبة احتساب المعدل ✅' : 'مقيد ومحجوب بقرار الإدارة 🔒' }}
                        </span>
                    </div>
                </div>
                <p class="ed-control-desc">
                    التحكم في إمكانية استخدام الطلبة لحاسبة المعدل واستخراج درجات التخرج، بحيث تتاح فقط عند اعتماد الإدارة للفترة الرسمية.
                </p>
            </div>

            <button type="button" onclick="toggleGpaState()" id="btnToggleGpa" class="ed-btn {{ $allowStudentGpa ? 'ed-btn-outline' : 'ed-btn-primary' }}" style="width: 100%; justify-content: center;">
                <i class="fas {{ $allowStudentGpa ? 'fa-lock' : 'fa-check' }}"></i>
                <span>{{ $allowStudentGpa ? 'قفل حاسبة المعدل عن الطلبة' : 'إتاحة حاسبة المعدل للطلبة' }}</span>
            </button>
        </div>

        <!-- بطاقة 3: إحصائيات الاعتماد والتخرج -->
        <div class="ed-card ed-cert-control-card">
            <div class="ed-control-top">
                <div class="ed-control-icon-header">
                    <div class="ed-control-icon" style="background: #fffbeb; color: #d97706;">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div>
                        <h3>إحصائيات الاعتماد والتخرج</h3>
                        <span class="ed-control-status" style="color: #64748b;">العام الأكاديمي: 2025 / 2026</span>
                    </div>
                </div>

                <div class="ed-cert-stats-flex">
                    <div class="ed-cert-mini-stat">
                        <span class="lbl">الشهادات الصادرة</span>
                        <span class="val amber" id="statCertCount">{{ $stats['total_certificates'] }}</span>
                    </div>
                    <div class="ed-cert-mini-stat">
                        <span class="lbl">إجمالي الطلبة</span>
                        <span class="val">{{ $stats['total_students'] }}</span>
                    </div>
                </div>
            </div>

            <div class="ed-cert-security-hint">
                <i class="fas fa-shield-alt"></i> وثائق رسمية مؤمنة برقم تسلسلي ورمز QR موثق
            </div>
        </div>

    </div>

    <!-- جدول الطلبة ورصد الدرجات والشهادات -->
    <div class="ed-card" style="padding: 0; overflow: hidden;">
        <div class="ed-table-header-bar">
            <div>
                <h2>سجل درجات وشهادات الطلبة</h2>
                <p>قائمة طلبة الثانوية العامة مع رصد المعدلات الفعلية وحالة اعتماد الشهادة الرسمية</p>
            </div>
            <div class="ed-table-counter">
                إجمالي الطلبة: <strong>{{ $students->total() }}</strong>
            </div>
        </div>

        <div class="ed-table-responsive">
            <table class="ed-custom-table">
                <thead>
                    <tr>
                        <th>بيانات الطالب</th>
                        <th>الفرع الأكاديمي</th>
                        <th>رقم الهوية الوطنية</th>
                        <th>المعدل وحالة الاعتماد</th>
                        <th style="text-align: center;">الإجراءات والشهادة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        @php
                            $latestCert = $st->certificates->first();
                        @endphp
                        <tr id="row_student_{{ $st->id }}">
                            <td>
                                <div class="ed-user-cell">
                                    <div class="ed-user-avatar">
                                        {{ mb_substr($st->name_ar, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="ed-user-name">{{ $st->name_ar }}</div>
                                        <div class="ed-user-sub">{{ $st->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="ed-badge ed-badge-blue">
                                    {{ $st->stage?->label_ar ?? 'توجيهي عام' }}
                                </span>
                            </td>
                            <td>
                                <span class="ed-nid-code">{{ $st->nid }}</span>
                            </td>
                            <td>
                                @if($latestCert)
                                    <div class="ed-grade-cell">
                                        <span class="ed-badge ed-badge-amber">
                                            <i class="fas fa-star"></i> المعدل: {{ $latestCert->final_grade }}%
                                        </span>
                                        <span class="ed-subject-tag">
                                            ({{ $latestCert->subject?->name_ar ?? 'شهادة توجيهي عامة' }})
                                        </span>
                                    </div>
                                @else
                                    <span class="ed-badge" style="background: #f1f5f9; color: #94a3b8; border: 1px dashed #cbd5e1;">
                                        <i class="fas fa-hourglass-start"></i> بانتظار الرصد والاعتماد
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="ed-table-actions">
                                    @if($latestCert)
                                        <a href="{{ route('certificates.show', $latestCert->id) }}" target="_blank" class="ed-btn ed-btn-outline" style="font-size: 0.78rem; padding: 6px 10px;" title="معاينة وطباعة الشهادة الأكاديمية">
                                            <i class="fas fa-external-link-alt"></i> معاينة
                                        </a>

                                        <button type="button" onclick="openIssueModal({{ $st->id }}, '{{ addslashes($st->name_ar) }}', {{ $latestCert->final_grade }}, {{ $latestCert->subject_id ?? 'null' }})" class="ed-btn ed-btn-outline" style="font-size: 0.78rem; padding: 6px 10px; color: #d97706; border-color: #fde68a;" title="تعديل المعدل">
                                            <i class="fas fa-pen"></i> تعديل
                                        </button>

                                        <button type="button" onclick="deleteCert({{ $latestCert->id }}, '{{ addslashes($st->name_ar) }}')" class="ed-btn ed-btn-outline danger" style="font-size: 0.78rem; padding: 6px 10px;" title="حذف الشهادة">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @else
                                        <button type="button" onclick="openIssueModal({{ $st->id }}, '{{ addslashes($st->name_ar) }}')" class="ed-btn ed-btn-primary" style="font-size: 0.78rem; padding: 6px 12px;">
                                            <i class="fas fa-award"></i> اعتماد وإصدار
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="ed-empty-cell">
                                <i class="fas fa-inbox"></i>
                                <p>لا يوجد طلبة مسجلون حالياً في النظام.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="ed-pagination-wrap">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>

<!-- نافذة اعتماد ورصد الشهادة للمدير (Modal) -->
<div id="issueModalOverlay" class="ed-modal-backdrop">
    <div class="ed-modal-card">
        <div class="ed-modal-header">
            <div class="ed-modal-title">
                <div class="ed-modal-icon"><i class="fas fa-award"></i></div>
                <div>
                    <h3>اعتماد ورصد الشهادة الأكاديمية</h3>
                    <p>إصدار وتوثيق شهادة إتمام وتفوق رسمية معتمدة من الإدارة</p>
                </div>
            </div>
            <button type="button" onclick="closeIssueModal()" class="ed-modal-close">✕</button>
        </div>

        <form id="issueCertForm" class="ed-modal-body">
            @csrf
            <div class="ed-form-fields-stack">

                <!-- اختيار الطالب -->
                <div class="ed-input-group">
                    <label for="modalStudentSelect">الطالب المراد اعتماد شهادته *</label>
                    <select name="student_id" id="modalStudentSelect" required class="ed-select">
                        <option value="" disabled selected>اختر الطالب...</option>
                        @foreach($students as $stu)
                            <option value="{{ $stu->id }}">{{ $stu->name_ar }} ({{ $stu->stage?->label_ar ?? 'توجيهي' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- اختيار المادة -->
                <div class="ed-input-group">
                    <label for="modalSubjectSelect">المادة أو التخصص الأكاديمي</label>
                    <select name="subject_id" id="modalSubjectSelect" class="ed-select">
                        <option value="">شهادة تفوق وإتمام عامة في الثانوية العامة</option>
                        @foreach($subjects as $sb)
                            <option value="{{ $sb->id }}">{{ $sb->name_ar }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- رصد المعدل الفعلي -->
                <div class="ed-input-group">
                    <label for="modalFinalGrade">المعدل أو النسبة المئوية المعتمدة (من 50 إلى 100) *</label>
                    <input 
                        type="number" 
                        name="final_grade" 
                        id="modalFinalGrade" 
                        min="50" 
                        max="100" 
                        step="0.1" 
                        required 
                        placeholder="مثال: 94.5" 
                        class="ed-input"
                        style="font-size: 1.1rem; font-weight: 800; color: #1d4ed8;"
                    >
                    <span class="ed-input-hint">
                        * يرجى إدخال المعدل الفعلي الحقيقي؛ لن يتم اعتماد أي درجات عشوائية أو غير رسمية.
                    </span>
                </div>

            </div>

            <!-- أزرار المودال -->
            <div class="ed-modal-actions">
                <button type="button" onclick="closeIssueModal()" class="ed-btn ed-btn-outline">إلغاء</button>
                <button type="button" onclick="submitIssueCert()" id="btnSubmitIssue" class="ed-btn ed-btn-primary">
                    <i class="fas fa-check"></i>
                    <span>اعتماد وحفظ الشهادة</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .ed-admin-container {
        padding: 24px 32px 60px;
        direction: rtl;
        font-family: 'Alexandria', 'Tajawal', sans-serif;
    }

    /* Master Controls Grid */
    .ed-cert-controls-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }

    .ed-cert-control-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 20px;
        height: 100%;
    }

    .ed-control-icon-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 12px;
    }

    .ed-control-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .ed-control-icon-header h3 {
        margin: 0 0 3px;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-control-status {
        font-size: 0.78rem;
        font-weight: 700;
    }

    .ed-control-desc {
        font-size: 0.83rem;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }

    .ed-cert-stats-flex {
        display: flex;
        gap: 12px;
        margin-top: 14px;
    }

    .ed-cert-mini-stat {
        flex: 1;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
    }

    .ed-cert-mini-stat .lbl {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 600;
        display: block;
        margin-bottom: 4px;
    }

    .ed-cert-mini-stat .val {
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-cert-mini-stat .val.amber {
        color: #d97706;
    }

    .ed-cert-security-hint {
        font-size: 0.78rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: center;
        background: #f8fafc;
        padding: 10px;
        border-radius: 10px;
    }

    .ed-cert-security-hint i {
        color: #059669;
    }

    /* Table Bar */
    .ed-table-header-bar {
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 12px;
    }

    .ed-table-header-bar h2 {
        margin: 0 0 3px;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-table-header-bar p {
        margin: 0;
        font-size: 0.82rem;
        color: #64748b;
    }

    .ed-table-counter {
        font-size: 0.82rem;
        color: #64748b;
    }

    .ed-table-counter strong {
        color: #1d4ed8;
        background: #eff6ff;
        padding: 2px 8px;
        border-radius: 6px;
    }

    .ed-table-responsive {
        overflow-x: auto;
    }

    .ed-custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }

    .ed-custom-table th {
        background: #f8fafc;
        padding: 14px 20px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
    }

    .ed-custom-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
        vertical-align: middle;
    }

    .ed-custom-table tr:hover td {
        background: #fbfcfe;
    }

    .ed-user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ed-user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #eff6ff;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .ed-user-name {
        font-weight: 700;
        color: #0f172a;
    }

    .ed-user-sub {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .ed-nid-code {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 3px 8px;
        border-radius: 6px;
        font-family: monospace;
        font-size: 0.85rem;
        color: #334155;
    }

    .ed-grade-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ed-subject-tag {
        font-size: 0.75rem;
        color: #64748b;
    }

    .ed-table-actions {
        display: inline-flex;
        gap: 6px;
        align-items: center;
    }

    .ed-btn-outline.danger {
        color: #dc2626;
        border-color: #fee2e2;
    }

    .ed-btn-outline.danger:hover {
        background: #fef2f2;
    }

    .ed-empty-cell {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }

    .ed-empty-cell i {
        font-size: 2rem;
        margin-bottom: 8px;
        display: block;
    }

    .ed-pagination-wrap {
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
    }

    /* Modal Backdrop & Card */
    .ed-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .ed-modal-card {
        background: #ffffff;
        width: 100%;
        max-width: 520px;
        border-radius: 18px;
        box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: edModalFadeIn 0.2s ease-out;
    }

    @keyframes edModalFadeIn {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }

    .ed-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }

    .ed-modal-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ed-modal-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #eff6ff;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .ed-modal-title h3 {
        margin: 0 0 2px;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
    }

    .ed-modal-title p {
        margin: 0;
        font-size: 0.78rem;
        color: #64748b;
    }

    .ed-modal-close {
        background: transparent;
        border: none;
        font-size: 1.1rem;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
    }

    .ed-modal-close:hover {
        color: #0f172a;
    }

    .ed-modal-body {
        padding: 24px;
    }

    .ed-form-fields-stack {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .ed-select,
    .ed-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-family: inherit;
        font-size: 0.9rem;
        color: #0f172a;
        outline: none;
        transition: border-color 0.2s;
    }

    .ed-select:focus,
    .ed-input:focus {
        border-color: #1d4ed8;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
    }

    .ed-input-hint {
        font-size: 0.72rem;
        color: #64748b;
        margin-top: 4px;
        display: block;
    }

    .ed-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #f1f5f9;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .ed-cert-controls-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    function openIssueModal(studentId = null, studentName = null, grade = null, subjectId = null) {
        const modal = document.getElementById('issueModalOverlay');
        const stSelect = document.getElementById('modalStudentSelect');
        const subSelect = document.getElementById('modalSubjectSelect');
        const gradeInput = document.getElementById('modalFinalGrade');

        if (studentId) {
            stSelect.value = studentId;
        }
        if (grade) {
            gradeInput.value = grade;
        } else {
            gradeInput.value = '';
        }
        if (subjectId) {
            subSelect.value = subjectId;
        } else {
            subSelect.value = '';
        }

        modal.style.display = 'flex';
    }

    function closeIssueModal() {
        document.getElementById('issueModalOverlay').style.display = 'none';
    }

    document.getElementById('issueModalOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeIssueModal();
    });

    // تبديل حالة إعلان ونشر الشهادات
    async function togglePublishState() {
        const btn = document.getElementById('btnTogglePublish');
        btn.disabled = true;

        try {
            const res = await axios.post("{{ route('admin.certificates.togglePublish') }}", {
                _token: '{{ csrf_token() }}'
            });

            Swal.fire({
                icon: res.data.icon,
                title: res.data.title,
                text: res.data.message,
                confirmButtonColor: '#1d4ed8',
                confirmButtonText: 'حسناً'
            }).then(() => {
                location.reload();
            });
        } catch (e) {
            btn.disabled = false;
            Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر تحديث حالة إعلان الشهادات.' });
        }
    }

    // تبديل إمكانية حساب المعدل
    async function toggleGpaState() {
        const btn = document.getElementById('btnToggleGpa');
        btn.disabled = true;

        try {
            const res = await axios.post("{{ route('admin.certificates.toggleGpa') }}", {
                _token: '{{ csrf_token() }}'
            });

            Swal.fire({
                icon: res.data.icon,
                title: res.data.title,
                text: res.data.message,
                confirmButtonColor: '#1d4ed8',
                confirmButtonText: 'حسناً'
            }).then(() => {
                location.reload();
            });
        } catch (e) {
            btn.disabled = false;
            Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر تحديث إعدادات حاسبة المعدل.' });
        }
    }

    // إرسال اعتماد الشهادة
    async function submitIssueCert() {
        const form = document.getElementById('issueCertForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const btn = document.getElementById('btnSubmitIssue');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الاعتماد...';

        const formData = new FormData(form);

        try {
            const res = await axios.post("{{ route('admin.certificates.issue') }}", formData);
            Swal.fire({
                icon: 'success',
                title: res.data.title,
                text: res.data.message,
                confirmButtonColor: '#059669',
                confirmButtonText: 'عرض وتحديث'
            }).then(() => {
                location.reload();
            });
        } catch (error) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> اعتماد وحفظ الشهادة';

            let msg = 'حدث خطأ أثناء رصد الشهادة.';
            if (error.response && error.response.data && error.response.data.errors) {
                msg = Object.values(error.response.data.errors).flat().join('<br>');
            }
            Swal.fire({ icon: 'error', title: 'فشل الاعتماد', html: msg });
        }
    }

    // حذف شهادة
    function deleteCert(certId, studentName) {
        Swal.fire({
            title: 'حذف وإلغاء الشهادة',
            text: `هل أنت متأكد من رغبتك في سحب شهادة الطالب (${studentName})؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، حذف الشهادة',
            cancelButtonText: 'تراجع'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    await axios.delete(`{{ url('admin/certificates') }}/${certId}`, {
                        data: { _token: '{{ csrf_token() }}' }
                    });
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحذف بنجاح',
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } catch (e) {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر حذف الشهادة.' });
                }
            }
        });
    }
</script>
@endsection
