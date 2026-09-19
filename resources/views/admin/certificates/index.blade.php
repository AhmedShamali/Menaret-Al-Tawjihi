@extends('layouts.app')

@section('title', __('إدارة الشهادات والنتائج') . ' | ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
<div class="ed-admin-container">

    <!-- 1. ترويسة الصفحة الأكاديمية الكلاسيكية الراقية -->
    <header class="classic-registry-header">
        <div class="registry-title-wrap">
            <div class="registry-breadcrumb">
                <i class="fa-solid fa-building-columns text-primary"></i>
                <a href="{{ route('admin.dashboard') }}">{{ __('لوحة التحكم') }}</a>
                <span class="bc-divider">/</span>
                <span>{{ __('الشؤون الأكاديمية والامتحانات') }}</span>
                <span class="bc-divider">/</span>
                <span class="bc-current">{{ __('سجل الشهادات والنتائج الوزارية') }}</span>
            </div>
            <h1 class="registry-main-title">
                <i class="fa-solid fa-award" style="color: #1e3a8a;"></i>
                {{ __('إدارة واعتماد الشهادات والنتائج الأكاديمية') }}
            </h1>
            <p class="registry-sub-title">
                {{ __('عمادة القبول والتسجيل • رصد المعدلات الوزارية النهائية، إصدار وثائق التخرج الرسمية، والتحكم في إعلان النتائج لدورة :session (:academic)', [
                    'session' => \App\Models\Setting::tawjihiSession(),
                    'academic' => \App\Models\Setting::academicYear()
                ]) }}
            </p>
        </div>

        <div class="registry-header-actions">
            <button type="button" onclick="openIssueModal()" class="btn-royal-action">
                <i class="fa-solid fa-stamp"></i>
                <span>{{ __('رصد واعتماد شهادة جديدة') }}</span>
            </button>
        </div>
    </header>

    <!-- 2. الشريط الأكاديمي الموحد للمؤشرات والتحكم المركزي (Refined Master Control Ribbon) -->
    <div class="classic-control-ribbon">
        
        <!-- المؤشرات والإحصائيات الأكاديمية -->
        <div class="ribbon-kpis-cluster">
            <div class="ribbon-kpi-box">
                <div class="kpi-icon-wrap blue">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div class="kpi-info">
                    <span class="kpi-label">{{ __('إجمالي طلبة الثانوية') }}</span>
                    <strong class="kpi-number">{{ $stats['total_students'] }}</strong>
                </div>
            </div>

            <div class="ribbon-kpi-divider"></div>

            <div class="ribbon-kpi-box">
                <div class="kpi-icon-wrap amber">
                    <i class="fa-solid fa-certificate"></i>
                </div>
                <div class="kpi-info">
                    <span class="kpi-label">{{ __('الشهادات الصادرة والمعتمدة') }}</span>
                    <strong class="kpi-number text-amber" id="statCertCount">{{ $stats['total_certificates'] }}</strong>
                </div>
            </div>

            <div class="ribbon-kpi-divider"></div>

            <div class="ribbon-kpi-box">
                <div class="kpi-icon-wrap emerald">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <div class="kpi-info">
                    <span class="kpi-label">{{ __('نسبة الإنجاز والاعتماد') }}</span>
                    <strong class="kpi-number text-emerald">
                        {{ $stats['total_students'] > 0 ? round(($stats['total_certificates'] / $stats['total_students']) * 100, 1) : 0 }}%
                    </strong>
                </div>
            </div>
        </div>

        <!-- مفاتيح التحكم المركزية الكلاسيكية -->
        <div class="ribbon-toggles-cluster">

            <!-- مفتاح 1: إعلان شهادات التخرج -->
            <div class="toggle-control-card">
                <div class="toggle-header-row">
                    <span class="toggle-title">{{ __('إعلان شهادات التخرج') }}</span>
                    @if($yearEndPublished)
                        <span class="badge-status-pill success">
                            <i class="fa-solid fa-lock-open"></i> {{ __('معلنة للطلبة 🎓') }}
                        </span>
                    @else
                        <span class="badge-status-pill muted">
                            <i class="fa-solid fa-lock"></i> {{ __('محجوبة بقرار الإدارة 🔒') }}
                        </span>
                    @endif
                </div>
                <div class="toggle-action-row">
                    <button type="button" onclick="togglePublishState()" id="btnTogglePublish" 
                            class="btn-control-switch {{ $yearEndPublished ? 'btn-switch-danger' : 'btn-switch-primary' }}">
                        <i class="fa-solid {{ $yearEndPublished ? 'fa-eye-slash' : 'fa-bullhorn' }}"></i>
                        <span>{{ $yearEndPublished ? __('حجب وإغلاق الإعلان') : __('إعلان ونشر الشهادات الآن') }}</span>
                    </button>
                </div>
            </div>

            <!-- مفتاح 2: حاسبة المعدل النهائي -->
            <div class="toggle-control-card">
                <div class="toggle-header-row">
                    <span class="toggle-title">{{ __('حاسبة المعدل النهائي') }}</span>
                    @if($allowStudentGpa)
                        <span class="badge-status-pill success">
                            <i class="fa-solid fa-calculator"></i> {{ __('متاحة للطلبة ✅') }}
                        </span>
                    @else
                        <span class="badge-status-pill muted">
                            <i class="fa-solid fa-lock"></i> {{ __('مقيدة بقرار الإدارة 🔒') }}
                        </span>
                    @endif
                </div>
                <div class="toggle-action-row">
                    <button type="button" onclick="toggleGpaState()" id="btnToggleGpa" 
                            class="btn-control-switch {{ $allowStudentGpa ? 'btn-switch-outline' : 'btn-switch-primary' }}">
                        <i class="fa-solid {{ $allowStudentGpa ? 'fa-lock' : 'fa-check' }}"></i>
                        <span>{{ $allowStudentGpa ? __('قفل الحاسبة عن الطلبة') : __('إتاحة الحاسبة للطلبة') }}</span>
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- 3. سجل درجات وشهادات الطلبة الأكاديمي الكلاسيكي (Academic Ledger Table) -->
    <div class="classic-ledger-card">
        <div class="ledger-header-bar">
            <div class="ledger-titles">
                <h2>
                    <i class="fa-solid fa-table-list" style="color: #1e3a8a;"></i>
                    {{ __('سجل درجات وشهادات الطلبة') }}
                </h2>
                <p>{{ __('رصد وتدقيق المعدلات الوزارية الرسمية وحالات اعتماد وثائق التخرج') }}</p>
            </div>

            <div class="ledger-tools">
                <div class="ledger-search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="ledgerSearchInput" placeholder="{{ __('بحث بالاسم، رقم الهوية، أو البريد...') }}" onkeyup="filterLedgerTable()">
                </div>
                <div class="ledger-count-badge">
                    <span>{{ __('إجمالي الطلبة:') }}</span>
                    <strong>{{ $students->total() }}</strong>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="academic-ledger-table" id="certificatesLedgerTable">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th>{{ __('بيانات الطالب الأكاديمية') }}</th>
                        <th>{{ __('الفرع والمسار') }}</th>
                        <th>{{ __('رقم الهوية الفلسطينية') }}</th>
                        <th>{{ __('المعدل الوزاري والاعتماد') }}</th>
                        <th style="text-align: center; width: 250px;">{{ __('الرصد السريع والإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $st)
                        @php
                            $latestCert = $st->certificates->first();
                            $stDispName = (app()->getLocale() === 'en' && !empty($st->name_en)) ? $st->name_en : $st->name_ar;
                            $rowNum = ($students->currentPage() - 1) * $students->perPage() + $index + 1;
                        @endphp
                        <tr id="row_student_{{ $st->id }}" class="ledger-row" 
                            data-search="{{ strtolower($stDispName . ' ' . $st->email . ' ' . $st->nid . ' ' . ($st->stage?->label_ar ?? '')) }}">
                            
                            <td style="text-align: center; color: #94a3b8; font-weight: 700;">
                                {{ $rowNum }}
                            </td>

                            <td>
                                <div class="student-profile-cell">
                                    <div class="student-avatar-box">
                                        {{ mb_substr($stDispName, 0, 1) }}
                                    </div>
                                    <div class="student-meta-col">
                                        <a href="{{ route('admin.students.show', $st->id) }}" class="student-name-link" title="{{ __('عرض ملف الطالب الكامل') }}">
                                            {{ $stDispName }}
                                        </a>
                                        <span class="student-email-tag">{{ $st->email }}</span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="classic-academic-pill">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                    {{ $st->stage?->label_ar ? __($st->stage->label_ar) : __('توجيهي عام') }}
                                </span>
                            </td>

                            <td>
                                <span class="national-id-tag font-mono">
                                    {{ $st->nid }}
                                </span>
                            </td>

                            <td>
                                @if($latestCert)
                                    <div class="grade-status-cell">
                                        <span class="grade-royal-badge">
                                            <i class="fa-solid fa-star"></i>
                                            <strong>{{ $latestCert->final_grade }}%</strong>
                                            @if($latestCert->final_grade >= 90)
                                                <small class="grade-tag-text">({{ __('ممتاز') }})</small>
                                            @elseif($latestCert->final_grade >= 80)
                                                <small class="grade-tag-text">({{ __('جيد جداً') }})</small>
                                            @elseif($latestCert->final_grade >= 70)
                                                <small class="grade-tag-text">({{ __('جيد') }})</small>
                                            @else
                                                <small class="grade-tag-text">({{ __('مقبول') }})</small>
                                            @endif
                                        </span>
                                        <span class="cert-status-sub">
                                            <i class="fa-solid fa-circle-check text-emerald"></i>
                                            {{ __('معتمدة رسمياً ومؤرخة') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="pending-grade-pill">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ __('بانتظار الرصد والاعتماد') }}
                                    </span>
                                @endif
                            </td>

                            <td style="text-align: center;">
                                <div class="ledger-actions-group">
                                    <!-- رصد سريع ومباشر للعلامة -->
                                    <div class="quick-grade-widget" title="{{ __('رصد وتعديل سريع للمعدل الوزاري') }}">
                                        <input type="number" id="quick_grade_{{ $st->id }}" min="0" max="100" step="0.5" 
                                               value="{{ $latestCert ? $latestCert->final_grade : '' }}" 
                                               placeholder="%" class="quick-grade-input font-mono">
                                        <button type="button" onclick="quickSaveCertGrade({{ $st->id }}, this)" 
                                                class="btn-quick-save" title="{{ __('حفظ فوري للمعدل') }}">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </div>

                                    @if($latestCert)
                                        <a href="{{ route('certificates.show', $latestCert->id) }}" target="_blank" 
                                           class="btn-tbl-action outline-blue" title="{{ __('معاينة وطباعة وثيقة التخرج الرسمية') }}">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                            <span>{{ __('معاينة') }}</span>
                                        </a>

                                        <button type="button" onclick="openIssueModal({{ $st->id }}, '{{ addslashes($stDispName) }}', {{ $latestCert->final_grade }}, {{ $latestCert->subject_id ?? 'null' }})" 
                                                class="btn-tbl-action outline-amber" title="{{ __('تعديل تفاصيل الشهادة') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <button type="button" onclick="deleteCert({{ $latestCert->id }}, '{{ addslashes($stDispName) }}')" 
                                                class="btn-tbl-action outline-red" title="{{ __('سحب وإلغاء الشهادة') }}">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    @else
                                        <button type="button" onclick="openIssueModal({{ $st->id }}, '{{ addslashes($stDispName) }}')" 
                                                class="btn-tbl-action outline-blue" title="{{ __('رصد واعتماد شهادة تفصيلية') }}">
                                            <i class="fa-solid fa-award"></i>
                                            <span>{{ __('تفصيلي') }}</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="ledger-empty-cell">
                                <i class="fa-solid fa-inbox"></i>
                                <p>{{ __('لا يوجد طلبة مسجلون حالياً في سجل النتائج والشهادات.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="ledger-pagination-wrap">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>

<!-- 4. نافذة رصد واعتماد الشهادة الأكاديمية (Classic Royal Modal) -->
<div id="issueModalOverlay" class="classic-modal-backdrop">
    <div class="classic-modal-dialog">
        <div class="modal-header-classic">
            <div class="modal-header-title">
                <div class="modal-seal-icon">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div>
                    <h3>{{ __('اعتماد ورصد الشهادة الأكاديمية') }}</h3>
                    <p>{{ __('إصدار وتوثيق شهادة إتمام وتفوق رسمية صادرة عن إدارة المنظومة') }}</p>
                </div>
            </div>
            <button type="button" onclick="closeIssueModal()" class="modal-btn-close" aria-label="{{ __('إغلاق') }}">✕</button>
        </div>

        <form id="issueCertForm" class="modal-body-classic">
            @csrf
            <div class="modal-fields-grid">

                <!-- اختيار الطالب -->
                <div class="modal-field-group">
                    <label for="modalStudentSelect">{{ __('الطالب المراد اعتماد شهادته *') }}</label>
                    <select name="student_id" id="modalStudentSelect" class="modal-input-field" required>
                        <option value="">-- {{ __('اختر الطالب من السجل الأكاديمي') }} --</option>
                        @foreach($students as $stOpt)
                            <option value="{{ $stOpt->id }}">
                                {{ (app()->getLocale() === 'en' && !empty($stOpt->name_en)) ? $stOpt->name_en : $stOpt->name_ar }} ({{ $stOpt->nid }})
                            </option>
                        @endforeach
                    </select>
                    <small class="field-help-text">{{ __('يتم سحب التخصص والمرحلة الدراسية للطالب تلقائياً من بياناته.') }}</small>
                </div>

                <!-- المبحث أو المساق -->
                <div class="modal-field-group">
                    <label for="modalSubjectSelect">{{ __('المبحث / المساق الأكاديمي (اختياري)') }}</label>
                    <select name="subject_id" id="modalSubjectSelect" class="modal-input-field">
                        <option value="">{{ __('شهادة عامة / معدل الثانوية العامة العام 🇵🇸') }}</option>
                        @foreach($subjects as $sbOpt)
                            <option value="{{ $sbOpt->id }}">
                                {{ $sbOpt->name_ar ? __($sbOpt->name_ar) : $sbOpt->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="field-help-text">{{ __('اتركه فارغاً لإصدار شهادة التخرج والمعدل الوزاري العام.') }}</small>
                </div>

                <!-- المعدل أو الدرجة المئوية -->
                <div class="modal-field-group">
                    <label for="modalFinalGrade">{{ __('المعدل / النتيجة المئوية (0 - 100) *') }}</label>
                    <div style="position: relative;">
                        <input type="number" step="0.01" min="0" max="100" name="final_grade" id="modalFinalGrade" 
                               class="modal-input-field font-mono" placeholder="95.5" required>
                        <span style="position: absolute; left: 14px; top: 11px; font-weight: 800; color: #94a3b8;">%</span>
                    </div>
                    <small class="field-help-text">{{ __('الدرجة الرسمية المعتمدة التي ستطبع على وثيقة التخرج الرسمية.') }}</small>
                </div>

            </div>

            <div class="modal-footer-classic">
                <button type="button" onclick="closeIssueModal()" class="btn-modal-cancel">
                    {{ __('إلغاء') }}
                </button>
                <button type="button" onclick="submitIssueCert()" id="btnSubmitIssue" class="btn-royal-action">
                    <i class="fa-solid fa-stamp"></i>
                    <span>{{ __('اعتماد وحفظ الشهادة') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* ==========================================================================
       التصميم الأكاديمي الملكي الكلاسيكي لصفحة إدارة الشهادات والنتائج
       (Royal Classic Academic Registry & Certificates View)
       - أسلوب عمادات القبول والتسجيل العريقة
       - حدود ناعمة ومحكمة، خط Tajawal الصغير والواضح
       - إحصائيات مندمجة بدقة بدون بطاقات ضخمة
       ========================================================================== */

    .ed-admin-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 6px 4px 50px;
    }

    /* 1. الترويسة الأكاديمية */
    .classic-registry-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 22px;
        background: #ffffff;
        border: 1px solid var(--ed-border);
        border-radius: var(--radius-sm);
        padding: 18px 24px;
        box-shadow: var(--shadow-card);
    }

    .registry-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        color: var(--ed-text-muted);
        margin-bottom: 6px;
    }

    .registry-breadcrumb a {
        color: var(--ed-text-body);
        font-weight: 600;
        text-decoration: none;
    }

    .registry-breadcrumb a:hover {
        color: var(--ed-primary);
    }

    .bc-divider {
        color: #cbd5e1;
    }

    .bc-current {
        color: var(--ed-primary-dark);
        font-weight: 700;
    }

    .registry-main-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--ed-text-main);
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .registry-sub-title {
        font-size: 0.84rem;
        color: var(--ed-text-muted);
        margin: 0;
        line-height: 1.5;
    }

    .btn-royal-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: var(--ed-primary);
        color: #ffffff !important;
        border: 1px solid var(--ed-primary-hover);
        padding: 9px 18px;
        border-radius: var(--radius-sm);
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 1px 3px rgba(29, 78, 216, 0.2);
    }

    .btn-royal-action:hover {
        background-color: var(--ed-primary-hover);
        transform: translateY(-1px);
    }

    /* 2. الشريط الأكاديمي الموحد للتحكم والمؤشرات */
    .classic-control-ribbon {
        background: #ffffff;
        border: 1px solid var(--ed-border);
        border-radius: var(--radius-sm);
        padding: 16px 20px;
        margin-bottom: 22px;
        box-shadow: var(--shadow-card);
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        align-items: center;
    }

    .ribbon-kpis-cluster {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #f8fafc;
        border: 1px solid var(--ed-border-subtle);
        border-radius: var(--radius-sm);
        padding: 12px 18px;
    }

    .ribbon-kpi-box {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .kpi-icon-wrap {
        width: 38px;
        height: 38px;
        border-radius: var(--radius-sm);
        display: grid;
        place-items: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .kpi-icon-wrap.blue { background: #eff6ff; color: var(--ed-primary); border: 1px solid var(--ed-primary-border); }
    .kpi-icon-wrap.amber { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .kpi-icon-wrap.emerald { background: #ecfdf5; color: #16a34a; border: 1px solid #a7f3d0; }

    .kpi-label {
        font-size: 0.74rem;
        color: var(--ed-text-muted);
        display: block;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .kpi-number {
        font-size: 1.18rem;
        font-weight: 800;
        color: var(--ed-text-main);
        display: block;
        line-height: 1.1;
    }

    .text-amber { color: #d97706 !important; }
    .text-emerald { color: #16a34a !important; }

    .ribbon-kpi-divider {
        width: 1px;
        height: 36px;
        background-color: var(--ed-border);
    }

    /* قسم مفاتيح التحكم الكلاسيكية */
    .ribbon-toggles-cluster {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .toggle-control-card {
        background: #f8fafc;
        border: 1px solid var(--ed-border-subtle);
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 8px;
    }

    .toggle-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }

    .toggle-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--ed-text-main);
    }

    .badge-status-pill {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-status-pill.success { background: #ecfdf5; color: #15803d; border: 1px solid #a7f3d0; }
    .badge-status-pill.muted { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }

    .btn-control-switch {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.77rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        border: 1px solid transparent;
    }

    .btn-switch-primary {
        background-color: var(--ed-primary);
        color: #ffffff;
        border-color: var(--ed-primary-hover);
    }
    .btn-switch-primary:hover { background-color: var(--ed-primary-hover); }

    .btn-switch-danger {
        background-color: #ffffff;
        color: #dc2626;
        border-color: #fca5a5;
    }
    .btn-switch-danger:hover { background-color: #fef2f2; }

    .btn-switch-outline {
        background-color: #ffffff;
        color: var(--ed-primary);
        border-color: var(--ed-primary-border);
    }
    .btn-switch-outline:hover { background-color: #eff6ff; }

    /* 3. سجل درجات وشهادات الطلبة (Ledger Table) */
    .classic-ledger-card {
        background: #ffffff;
        border: 1px solid var(--ed-border);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    .ledger-header-bar {
        padding: 14px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        border-bottom: 1px solid var(--ed-border);
        background: #fafbfc;
    }

    .ledger-titles h2 {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--ed-text-main);
        margin: 0 0 2px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ledger-titles p {
        font-size: 0.78rem;
        color: var(--ed-text-muted);
        margin: 0;
    }

    .ledger-tools {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ledger-search-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .ledger-search-box i {
        position: absolute;
        right: 12px;
        color: #94a3b8;
        font-size: 0.8rem;
        pointer-events: none;
    }

    html[dir="ltr"] .ledger-search-box i { right: auto; left: 12px; }

    .ledger-search-box input {
        width: 250px;
        height: 34px;
        padding: 0 34px 0 12px;
        border: 1px solid var(--ed-border);
        border-radius: 4px;
        font-size: 0.8rem;
        background: #ffffff;
        color: var(--ed-text-main);
        transition: var(--transition);
    }

    html[dir="ltr"] .ledger-search-box input { padding: 0 12px 0 34px; }

    .ledger-search-box input:focus {
        outline: none;
        border-color: var(--ed-primary);
        box-shadow: 0 0 0 2px rgba(29, 78, 216, 0.1);
    }

    .ledger-count-badge {
        font-size: 0.78rem;
        color: var(--ed-text-muted);
        background: #ffffff;
        border: 1px solid var(--ed-border);
        padding: 5px 10px;
        border-radius: 4px;
    }

    .ledger-count-badge strong {
        color: var(--ed-primary-dark);
        font-weight: 800;
        margin-inline-start: 4px;
    }

    /* الجدول الكلاسيكي */
    .academic-ledger-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.84rem;
        text-align: right;
    }

    html[dir="ltr"] .academic-ledger-table { text-align: left; }

    .academic-ledger-table th {
        background-color: #f8fafc;
        color: var(--ed-text-main);
        font-weight: 800;
        font-size: 0.8rem;
        padding: 11px 16px;
        border-bottom: 2px solid var(--ed-border);
        white-space: nowrap;
    }

    .academic-ledger-table td {
        padding: 11px 16px;
        border-bottom: 1px solid var(--ed-border-subtle);
        vertical-align: middle;
        color: var(--ed-text-body);
    }

    .academic-ledger-table tr:nth-child(even) td {
        background-color: #fafbfc;
    }

    .academic-ledger-table tr:hover td {
        background-color: #f0f7ff;
    }

    /* بيانات الطالب في الخلية */
    .student-profile-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .student-avatar-box {
        width: 34px;
        height: 34px;
        border-radius: 4px;
        background: #eff6ff;
        color: var(--ed-primary-dark);
        border: 1px solid var(--ed-primary-border);
        display: grid;
        place-items: center;
        font-weight: 800;
        font-size: 0.88rem;
        flex-shrink: 0;
    }

    .student-meta-col {
        display: flex;
        flex-direction: column;
        line-height: 1.35;
    }

    .student-name-link {
        font-size: 0.86rem;
        font-weight: 700;
        color: var(--ed-text-main);
        text-decoration: none;
    }

    .student-name-link:hover {
        color: var(--ed-primary);
        text-decoration: underline;
    }

    .student-email-tag {
        font-size: 0.73rem;
        color: var(--ed-text-muted);
    }

    /* شارات الفرع الأكاديمي */
    .classic-academic-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 4px;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
    }

    .national-id-tag {
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 2px 7px;
        border-radius: 4px;
    }

    /* نتيجة ومعدل الطالب */
    .grade-status-cell {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .grade-royal-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #b45309;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.82rem;
        font-weight: 800;
        width: fit-content;
    }

    .grade-tag-text {
        font-weight: 600;
        color: #92400e;
        font-size: 0.72rem;
    }

    .cert-status-sub {
        font-size: 0.7rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .pending-grade-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        color: #94a3b8;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* الإجراءات والرصد السريع */
    .ledger-actions-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        justify-content: center;
    }

    .quick-grade-widget {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: #ffffff;
        border: 1px solid var(--ed-border);
        border-radius: 4px;
        padding: 2px 4px;
    }

    .quick-grade-input {
        width: 50px;
        height: 26px;
        border: 1px solid #e2e8f0;
        border-radius: 3px;
        font-size: 0.8rem;
        font-weight: 800;
        text-align: center;
        color: var(--ed-primary-dark);
        outline: none;
    }

    .quick-grade-input:focus {
        border-color: var(--ed-primary);
    }

    .btn-quick-save {
        width: 26px;
        height: 26px;
        background-color: var(--ed-primary);
        color: #ffffff;
        border: none;
        border-radius: 3px;
        font-size: 0.75rem;
        cursor: pointer;
        display: grid;
        place-items: center;
        transition: var(--transition);
    }

    .btn-quick-save:hover {
        background-color: var(--ed-primary-hover);
    }

    .btn-tbl-action {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.76rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: var(--transition);
        border: 1px solid transparent;
        background: #ffffff;
    }

    .btn-tbl-action.outline-blue {
        color: var(--ed-primary);
        border-color: var(--ed-primary-border);
    }
    .btn-tbl-action.outline-blue:hover { background: #eff6ff; }

    .btn-tbl-action.outline-amber {
        color: #b45309;
        border-color: #fde68a;
    }
    .btn-tbl-action.outline-amber:hover { background: #fffbeb; }

    .btn-tbl-action.outline-red {
        color: #dc2626;
        border-color: #fecaca;
    }
    .btn-tbl-action.outline-red:hover { background: #fef2f2; }

    .ledger-empty-cell {
        text-align: center;
        padding: 36px 20px;
        color: #94a3b8;
    }

    .ledger-empty-cell i {
        font-size: 1.8rem;
        margin-bottom: 6px;
        display: block;
    }

    .ledger-pagination-wrap {
        padding: 12px 20px;
        border-top: 1px solid var(--ed-border);
        background: #fafbfc;
    }

    /* 4. النافذة المنبثقة الكلاسيكية (Modal) */
    .classic-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(2px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .classic-modal-dialog {
        background: #ffffff;
        width: 100%;
        max-width: 480px;
        border-radius: var(--radius-sm);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
        border: 1px solid var(--ed-border);
        overflow: hidden;
    }

    .modal-header-classic {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1px solid var(--ed-border);
    }

    .modal-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-seal-icon {
        width: 36px;
        height: 36px;
        border-radius: 4px;
        background: #eff6ff;
        color: var(--ed-primary);
        border: 1px solid var(--ed-primary-border);
        display: grid;
        place-items: center;
        font-size: 1.1rem;
    }

    .modal-header-title h3 {
        margin: 0 0 2px;
        font-size: 1rem;
        font-weight: 800;
        color: var(--ed-text-main);
    }

    .modal-header-title p {
        margin: 0;
        font-size: 0.74rem;
        color: var(--ed-text-muted);
    }

    .modal-btn-close {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #94a3b8;
        cursor: pointer;
    }

    .modal-btn-close:hover { color: #0f172a; }

    .modal-body-classic {
        padding: 20px;
    }

    .modal-fields-grid {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .modal-field-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--ed-text-main);
        margin-bottom: 4px;
    }

    .modal-input-field {
        width: 100%;
        height: 38px;
        padding: 0 12px;
        border: 1px solid var(--ed-border);
        border-radius: 4px;
        font-size: 0.84rem;
        background: #ffffff;
        color: var(--ed-text-main);
        transition: var(--transition);
    }

    .modal-input-field:focus {
        outline: none;
        border-color: var(--ed-primary);
        box-shadow: 0 0 0 2px rgba(29, 78, 216, 0.1);
    }

    .field-help-text {
        font-size: 0.72rem;
        color: var(--ed-text-muted);
        margin-top: 3px;
        display: block;
    }

    .modal-footer-classic {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid var(--ed-border);
    }

    .btn-modal-cancel {
        background: #ffffff;
        border: 1px solid var(--ed-border);
        color: var(--ed-text-body);
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-modal-cancel:hover { background: #f8fafc; }

    /* الريسبونسيف */
    @media (max-width: 1100px) {
        .classic-control-ribbon {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .ribbon-kpis-cluster {
            flex-direction: column;
            align-items: stretch;
        }
        .ribbon-kpi-divider {
            width: 100%;
            height: 1px;
        }
        .ribbon-toggles-cluster {
            grid-template-columns: 1fr;
        }
        .classic-registry-header {
            flex-direction: column;
        }
        .ledger-header-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .ledger-search-box input {
            width: 100%;
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

    // فلترة سريعة للجدول عبر البحث اللحظي
    function filterLedgerTable() {
        const q = document.getElementById('ledgerSearchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.ledger-row');
        rows.forEach(row => {
            const data = row.getAttribute('data-search') || '';
            if (!q || data.includes(q)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

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
                confirmButtonColor: '#1e3a8a',
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
                confirmButtonColor: '#1e3a8a',
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
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + @json(__('جاري الاعتماد...'));

        const formData = new FormData(form);

        try {
            const res = await axios.post("{{ route('admin.certificates.issue') }}", formData);
            Swal.fire({
                icon: 'success',
                title: res.data.title,
                text: res.data.message,
                confirmButtonColor: '#1e3a8a',
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
        btnEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

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
