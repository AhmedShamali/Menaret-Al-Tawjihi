@extends('layouts.app')

@section('title', __('إدارة الشهادات والنتائج') . ' | ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
<div class="ed-admin-container">

    <!-- رأس الصفحة الرسمي -->
    <header class="ed-admin-header">
        <div class="ed-admin-title-box">
            <div class="ed-admin-breadcrumbs">
                <i class="fas fa-home"></i>
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('لوحة التحكم') }}</a>
                <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} divider"></i>
                <span class="active">{{ __('إدارة الشهادات والنتائج') }}</span>
            </div>
            <h1>{{ __('إدارة واعتماد الشهادات والنتائج الأكاديمية') }}</h1>
            <p>{{ __('إعلان نتائج التخرج، اعتماد المعدلات الفعلية للطلبة، وإصدار وثائق التخرج الرسمية المؤمنة.') }}</p>
        </div>

        <div class="ed-admin-status-wrap">
            <button type="button" onclick="openIssueModal()" class="ed-btn ed-btn-primary">
                <i class="fas fa-award"></i>
                <span>{{ __('اعتماد ورصد شهادة جديدة') }}</span>
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
                        <h3>{{ __('إعلان شهادات نهاية العام') }}</h3>
                        <span class="ed-control-status" style="color: {{ $yearEndPublished ? '#059669' : '#dc2626' }};">
                            {{ $yearEndPublished ? __('معلنة ومنشورة رسمياً للطلبة 🎓') : __('محجوبة بقرار الإدارة الأكاديمية 🔒') }}
                        </span>
                    </div>
                </div>
                <p class="ed-control-desc">
                    {{ __('الشهادات محجوبة طوال العام الدراسي افتراضياً. عند تفعيل هذا الخيار بنهاية العام، ستظهر الشهادات المعتمدة في حسابات الطلبة.') }}
                </p>
            </div>

            <button type="button" onclick="togglePublishState()" id="btnTogglePublish" class="ed-btn {{ $yearEndPublished ? 'ed-btn-outline danger' : 'ed-btn-primary' }}" style="width: 100%; justify-content: center;">
                <i class="fas {{ $yearEndPublished ? 'fa-eye-slash' : 'fa-bullhorn' }}"></i>
                <span>{{ $yearEndPublished ? __('حجب الشهادات وإغلاق الإعلان') : __('إعلان ونشر الشهادات للطلبة الآن') }}</span>
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
                        <h3>{{ __('حساب المعدل النهائي للطلبة') }}</h3>
                        <span class="ed-control-status" style="color: {{ $allowStudentGpa ? '#1d4ed8' : '#64748b' }};">
                            {{ $allowStudentGpa ? __('متاح للطلبة احتساب المعدل ✅') : __('مقيد ومحجوب بقرار الإدارة 🔒') }}
                        </span>
                    </div>
                </div>
                <p class="ed-control-desc">
                    {{ __('التحكم في إمكانية استخدام الطلبة لحاسبة المعدل واستخراج درجات التخرج، بحيث تتاح فقط عند اعتماد الإدارة للفترة الرسمية.') }}
                </p>
            </div>

            <button type="button" onclick="toggleGpaState()" id="btnToggleGpa" class="ed-btn {{ $allowStudentGpa ? 'ed-btn-outline' : 'ed-btn-primary' }}" style="width: 100%; justify-content: center;">
                <i class="fas {{ $allowStudentGpa ? 'fa-lock' : 'fa-check' }}"></i>
                <span>{{ $allowStudentGpa ? __('قفل حاسبة المعدل عن الطلبة') : __('إتاحة حاسبة المعدل للطلبة') }}</span>
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
                        <h3>{{ __('إحصائيات الاعتماد والتخرج') }}</h3>
                        <span class="ed-control-status" style="color: #64748b;">{{ __('العام الأكاديمي:') }} {{ \App\Models\Setting::academicYear() }}</span>
                    </div>
                </div>

                <div class="ed-cert-stats-flex">
                    <div class="ed-cert-mini-stat">
                        <span class="lbl">{{ __('الشهادات الصادرة') }}</span>
                        <span class="val amber" id="statCertCount">{{ $stats['total_certificates'] }}</span>
                    </div>
                    <div class="ed-cert-mini-stat">
                        <span class="lbl">{{ __('إجمالي الطلبة') }}</span>
                        <span class="val">{{ $stats['total_students'] }}</span>
                    </div>
                </div>
            </div>

            <div class="ed-cert-security-hint">
                <i class="fas fa-shield-alt"></i> {{ __('وثائق رسمية مؤمنة برقم تسلسلي ورمز QR موثق') }}
            </div>
        </div>

    </div>

    <!-- جدول الطلبة ورصد الدرجات والشهادات -->
    <div class="ed-card" style="padding: 0; overflow: hidden;">
        <div class="ed-table-header-bar">
            <div>
                <h2>{{ __('سجل درجات وشهادات الطلبة') }}</h2>
                <p>{{ __('قائمة طلبة الثانوية العامة مع رصد المعدلات الفعلية وحالة اعتماد الشهادة الرسمية') }}</p>
            </div>
            <div class="ed-table-counter">
                {{ __('إجمالي الطلبة:') }} <strong>{{ $students->total() }}</strong>
            </div>
        </div>

        <div class="ed-table-responsive">
            <table class="ed-custom-table">
                <thead>
                    <tr>
                        <th>{{ __('بيانات الطالب') }}</th>
                        <th>{{ __('الفرع الأكاديمي') }}</th>
                        <th>{{ __('رقم الهوية الوطنية') }}</th>
                        <th>{{ __('المعدل وحالة الاعتماد') }}</th>
                        <th style="text-align: center;">{{ __('الإجراءات والشهادة') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        @php
                            $latestCert = $st->certificates->first();
                            $stDispName = (app()->getLocale() === 'en' && !empty($st->name_en)) ? $st->name_en : $st->name_ar;
                        @endphp
                        <tr id="row_student_{{ $st->id }}">
                            <td>
                                <div class="ed-user-cell">
                                    <div class="ed-user-avatar">
                                        {{ mb_substr($stDispName, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="ed-user-name">{{ $stDispName }}</div>
                                        <div class="ed-user-sub">{{ $st->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="ed-badge ed-badge-blue">
                                    {{ $st->stage?->label_ar ? __($st->stage->label_ar) : __('توجيهي عام') }}
                                </span>
                            </td>
                            <td>
                                <span class="ed-nid-code">{{ $st->nid }}</span>
                            </td>
                            <td>
                                @if($latestCert)
                                    <div class="ed-grade-cell">
                                        <span class="ed-badge ed-badge-amber">
                                            <i class="fas fa-star"></i> {{ __('المعدل:') }} {{ $latestCert->final_grade }}%
                                        </span>
                                        <span class="ed-subject-tag">
                                            ({{ $latestCert->subject?->name_ar ? __($latestCert->subject->name_ar) : __('شهادة توجيهي عامة') }})
                                        </span>
                                    </div>
                                @else
                                    <span class="ed-badge" style="background: #f1f5f9; color: #94a3b8; border: 1px dashed #cbd5e1;">
                                        <i class="fas fa-hourglass-start"></i> {{ __('بانتظار الرصد والاعتماد') }}
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="ed-table-actions">
                                    <!-- دفتر علامات سريع ومباشر -->
                                    <div style="display: inline-flex; align-items: center; gap: 4px; background: #f8fafc; padding: 3px 6px; border-radius: 8px; border: 1px solid #e2e8f0;" title="{{ __('رصد وتعديل سريع للعلامة') }}">
                                        <input type="number" id="quick_grade_{{ $st->id }}" min="0" max="100" step="0.5" value="{{ $latestCert ? $latestCert->final_grade : '' }}" placeholder="%" style="width: 55px; padding: 4px 6px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 800; font-size: 0.82rem; text-align: center; color: #1d4ed8; background: #fff;">
                                        <button type="button" onclick="quickSaveCertGrade({{ $st->id }}, this)" class="ed-btn ed-btn-primary" style="padding: 5px 8px; font-size: 0.75rem;" title="{{ __('حفظ العلامة فوراً') }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>

                                    @if($latestCert)
                                        <a href="{{ route('certificates.show', $latestCert->id) }}" target="_blank" class="ed-btn ed-btn-outline" style="font-size: 0.78rem; padding: 6px 10px;" title="{{ __('معاينة وطباعة الشهادة الأكاديمية') }}">
                                            <i class="fas fa-external-link-alt"></i> {{ __('معاينة') }}
                                        </a>

                                        <button type="button" onclick="openIssueModal({{ $st->id }}, '{{ addslashes($stDispName) }}', {{ $latestCert->final_grade }}, {{ $latestCert->subject_id ?? 'null' }})" class="ed-btn ed-btn-outline" style="font-size: 0.78rem; padding: 6px 10px; color: #d97706; border-color: #fde68a;" title="{{ __('تعديل تفصيلي') }}">
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <button type="button" onclick="deleteCert({{ $latestCert->id }}, '{{ addslashes($stDispName) }}')" class="ed-btn ed-btn-outline danger" style="font-size: 0.78rem; padding: 6px 10px;" title="{{ __('حذف الشهادة') }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @else
                                        <button type="button" onclick="openIssueModal({{ $st->id }}, '{{ addslashes($stDispName) }}')" class="ed-btn ed-btn-outline" style="font-size: 0.78rem; padding: 6px 10px; color: #1d4ed8; border-color: #bfdbfe;">
                                            <i class="fas fa-award"></i> {{ __('تفصيلي') }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="ed-empty-cell">
                                <i class="fas fa-inbox"></i>
                                <p>{{ __('لا يوجد طلبة مسجلون حالياً في النظام.') }}</p>
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
                    <h3>{{ __('اعتماد ورصد الشهادة الأكاديمية') }}</h3>
                    <p>{{ __('إصدار وتوثيق شهادة إتمام وتفوق رسمية معتمدة من الإدارة') }}</p>
                </div>
            </div>
            <button type="button" onclick="closeIssueModal()" class="ed-modal-close">✕</button>
        </div>

        <form id="issueCertForm" class="ed-modal-body">
            @csrf
            <div class="ed-form-fields-stack">

                <!-- اختيار الطالب -->
                <div class="ed-input-group">
                    <label for="modalStudentSelect">{{ __('الطالب المراد اعتماد شهادته *') }}</label>
                    <select name="student_id" id="modalStudentSelect" required class="ed-select">
                        <option value="" disabled selected>{{ __('اختر الطالب...') }}</option>
                        @foreach($students as $stu)
                            @php $stuDisp = (app()->getLocale() === 'en' && !empty($stu->name_en)) ? $stu->name_en : $stu->name_ar; @endphp
                            <option value="{{ $stu->id }}">{{ $stuDisp }} ({{ $stu->stage?->label_ar ? __($stu->stage->label_ar) : __('توجيهي') }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- اختيار المادة -->
                <div class="ed-input-group">
                    <label for="modalSubjectSelect">{{ __('المادة أو التخصص الأكاديمي') }}</label>
                    <select name="subject_id" id="modalSubjectSelect" class="ed-select">
                        <option value="">{{ __('شهادة تفوق وإتمام عامة في الثانوية العامة') }}</option>
                        @foreach($subjects as $sb)
                            @php $sbDisp = (app()->getLocale() === 'en' && !empty($sb->name_en)) ? $sb->name_en : $sb->name_ar; @endphp
                            <option value="{{ $sb->id }}">{{ $sbDisp }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- رصد المعدل الفعلي -->
                <div class="ed-input-group">
                    <label for="modalFinalGrade">{{ __('المعدل أو النسبة المئوية المعتمدة (من 0 إلى 100) *') }}</label>
                    <input 
                        type="number" 
                        name="final_grade" 
                        id="modalFinalGrade" 
                        min="0" 
                        max="100" 
                        step="0.1" 
                        required 
                        placeholder="{{ __('مثال: 94.5') }}" 
                        class="ed-input"
                        style="font-size: 1.1rem; font-weight: 800; color: #1d4ed8;"
                    >
                    <span class="ed-input-hint">
                        {{ __('* يرجى إدخال المعدل الفعلي الحقيقي؛ لن يتم اعتماد أي درجات عشوائية أو غير رسمية.') }}
                    </span>
                </div>

            </div>

            <!-- أزرار المودال -->
            <div class="ed-modal-actions">
                <button type="button" onclick="closeIssueModal()" class="ed-btn ed-btn-outline">{{ __('إلغاء') }}</button>
                <button type="button" onclick="submitIssueCert()" id="btnSubmitIssue" class="ed-btn ed-btn-primary">
                    <i class="fas fa-check"></i>
                    <span>{{ __('اعتماد وحفظ الشهادة') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .ed-admin-container {
        padding: 24px 32px 60px;
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
        background: #f8fafc !important;
        padding: 12px 16px;
        font-size: 0.82rem;
        font-weight: 800;
        color: #0f172a !important;
        border-bottom: 2px solid #cbd5e1 !important;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .ed-custom-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.86rem;
        vertical-align: middle;
        color: #1e293b;
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
                confirmButtonText: @json(__('حسناً'))
            }).then(() => {
                location.reload();
            });
        } catch (e) {
            btn.disabled = false;
            Swal.fire({ icon: 'error', title: @json(__('خطأ')), text: @json(__('تعذر تحديث حالة إعلان الشهادات.')) });
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
                confirmButtonText: @json(__('حسناً'))
            }).then(() => {
                location.reload();
            });
        } catch (e) {
            btn.disabled = false;
            Swal.fire({ icon: 'error', title: @json(__('خطأ')), text: @json(__('تعذر تحديث إعدادات حاسبة المعدل.')) });
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
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + @json(__('جاري الاعتماد...'));

        const formData = new FormData(form);

        try {
            const res = await axios.post("{{ route('admin.certificates.issue') }}", formData);
            Swal.fire({
                icon: 'success',
                title: res.data.title,
                text: res.data.message,
                confirmButtonColor: '#059669',
                confirmButtonText: @json(__('عرض وتحديث'))
            }).then(() => {
                location.reload();
            });
        } catch (error) {
            let msg = @json(__('حدث خطأ أثناء رصد الشهادة.'));
            if (error.response && error.response.data) {
                if (error.response.data.message) {
                    msg = error.response.data.message;
                }
                if (error.response.data.errors) {
                    msg = Object.values(error.response.data.errors).flat().join('<br>');
                }
            }
            Swal.fire({ icon: 'error', title: @json(__('فشل الاعتماد')), html: msg });
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    // رصد مباشر وسريع للعلامة من الجدول (دفتر العلامات السريع)
    async function quickSaveCertGrade(studentId, btnEl) {
        const input = document.getElementById(`quick_grade_${studentId}`);
        const val = parseFloat(input.value);
        if (isNaN(val) || val < 0 || val > 100) {
            Swal.fire({ icon: 'warning', title: @json(__('تنبيه')), text: @json(__('يرجى إدخال درجة صحيحة بين 0 و 100.')) });
            return;
        }

        const originalHtml = btnEl.innerHTML;
        btnEl.disabled = true;
        btnEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const res = await axios.post("{{ route('admin.certificates.issue') }}", {
                _token: '{{ csrf_token() }}',
                student_id: studentId,
                final_grade: val
            });
            Swal.fire({
                icon: 'success',
                title: @json(__('تم رصد العلامة بنجاح 🌟')),
                text: res.data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } catch (e) {
            btnEl.disabled = false;
            btnEl.innerHTML = originalHtml;
            let errMsg = @json(__('تعذر رصد العلامة.'));
            if (e.response && e.response.data && e.response.data.message) {
                errMsg = e.response.data.message;
            }
            Swal.fire({ icon: 'error', title: @json(__('خطأ')), text: errMsg });
        }
    }

    // حذف شهادة
    function deleteCert(certId, studentName) {
        Swal.fire({
            title: @json(__('حذف وإلغاء الشهادة')),
            text: `${@json(__('هل أنت متأكد من رغبتك في سحب شهادة الطالب'))} (${studentName})؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: @json(__('نعم، حذف الشهادة')),
            cancelButtonText: @json(__('تراجع'))
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    await axios.delete(`{{ url('admin/certificates') }}/${certId}`, {
                        data: { _token: '{{ csrf_token() }}' }
                    });
                    Swal.fire({
                        icon: 'success',
                        title: @json(__('تم الحذف بنجاح')),
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } catch (e) {
                    Swal.fire({ icon: 'error', title: @json(__('خطأ')), text: @json(__('تعذر حذف الشهادة.')) });
                }
            }
        });
    }
</script>
@endsection
