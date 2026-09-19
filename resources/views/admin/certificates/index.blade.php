@extends('layouts.app')

@section('title', __('ديوان الامتحانات وسجل الشهادات الرسمية') . ' | ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
<div class="ed-admin-container">

    <!-- 1. ترويسة ديوان الامتحانات العامة والشهادات الرسمية الأكاديمية الكلاسيكية -->
    <header class="classic-registry-header">
        <div class="registry-title-wrap">
            <div class="registry-breadcrumb">
                <i class="fa-solid fa-building-columns text-primary"></i>
                <a href="{{ route('admin.dashboard') }}">{{ __('لوحة التحكم') }}</a>
                <span class="bc-divider">/</span>
                <span>{{ __('ديوان الامتحانات العامة') }}</span>
                <span class="bc-divider">/</span>
                <span class="bc-current">{{ __('سجل واعتماد شهادات التوجيهي') }}</span>
            </div>
            
            <div class="registry-authority-tag">
                <i class="fa-solid fa-landmark text-amber"></i>
                <span>{{ __('دولة فلسطين • وزارة التربية والتعليم العالي • ديوان الامتحانات العامة والسجل الأكاديمي المركزي') }}</span>
            </div>

            <h1 class="registry-main-title">
                <span class="royal-crest-icon"><i class="fa-solid fa-award"></i></span>
                <span>{{ __('سجل اعتماد وتوثيق شهادات الثانوية العامة (التوجيهي)') }}</span>
            </h1>

            <p class="registry-sub-title">
                {{ __('السجل المركزي المعتمد لرصد المعدلات الوزارية النهائية، تدقيق كشوف الدرجات، وإصدار وثائق التخرج الرسمية الموثقة برقم تسلسلي ورمز الاستجابة السريع (QR Code).') }}
            </p>
        </div>

        <div class="registry-header-actions">
            <button type="button" onclick="window.print()" class="btn-classic-outline" title="{{ __('طباعة السجل الأكاديمي الرسمي') }}">
                <i class="fa-solid fa-print"></i>
                <span>{{ __('طباعة السجل الرسمي') }}</span>
            </button>

            <button type="button" onclick="openIssueModal()" class="btn-royal-action" title="{{ __('رصد واعتماد وثيقة تخرج جديدة') }}">
                <i class="fa-solid fa-stamp"></i>
                <span>{{ __('رصد واعتماد شهادة جديدة') }}</span>
            </button>
        </div>
    </header>

    <!-- 2. منصة الضبط والرقابة الأكاديمية الكلاسيكية الموحدة (Master Institutional Console) -->
    <div class="classic-registry-console">
        
        <!-- الجانب الأيمن: المؤشرات الرسمية للسجل الأكاديمي -->
        <div class="console-metrics-section">
            <div class="console-section-heading">
                <i class="fa-solid fa-chart-line-up text-amber"></i>
                <span>{{ __('مؤشرات السجل العام والاعتماد الوزاري:') }}</span>
            </div>

            <div class="console-kpi-row">
                <!-- إجمالي الطلبة -->
                <div class="metric-kpi-item">
                    <span class="metric-kpi-label">{{ __('إجمالي المقيدين بالسجل') }}</span>
                    <strong class="metric-kpi-val">{{ $stats['total_students'] }} <small>{{ __('طالباً') }}</small></strong>
                </div>

                <div class="metric-kpi-divider"></div>

                <!-- الشهادات المعتمدة -->
                <div class="metric-kpi-item">
                    <span class="metric-kpi-label">{{ __('الشهادات المعتمدة والمختومة') }}</span>
                    <strong class="metric-kpi-val text-amber" id="statCertCount">{{ $stats['total_certificates'] }} <small>{{ __('وثيقة') }}</small></strong>
                </div>

                <div class="metric-kpi-divider"></div>

                <!-- نسبة الإنجاز -->
                <div class="metric-kpi-item">
                    <span class="metric-kpi-label">{{ __('نسبة الاعتماد الوزاري') }}</span>
                    <strong class="metric-kpi-val text-emerald">
                        {{ $stats['total_students'] > 0 ? round(($stats['total_certificates'] / $stats['total_students']) * 100, 1) : 0 }}%
                    </strong>
                </div>

                <div class="metric-kpi-divider"></div>

                <!-- الدورة الامتحانية المعتمدة -->
                <div class="metric-kpi-item">
                    <span class="metric-kpi-label">{{ __('الدورة الامتحانية') }}</span>
                    <strong class="metric-kpi-val text-navy">
                        {{ __('معتمدة وزارياً') }}
                    </strong>
                </div>
            </div>
        </div>

        <!-- فاصل عمودي كلاسيكي -->
        <div class="console-vertical-divider"></div>

        <!-- الجانب الأيسر: القرارات السيادية وإعلان النتائج للطلبة -->
        <div class="console-governance-section">
            <div class="console-section-heading">
                <i class="fa-solid fa-scale-balanced text-primary"></i>
                <span>{{ __('القرارات السيادية والتحكم في النشر:') }}</span>
            </div>

            <div class="governance-controls-grid">

                <!-- 1. إعلان ونشر وثائق التخرج -->
                <div class="governance-control-unit">
                    <div class="gov-info-col">
                        <div class="gov-title-line">
                            <strong>{{ __('إعلان شهادات التخرج للطلبة:') }}</strong>
                            <span id="badgePublishStatus" class="gov-status-tag {{ $yearEndPublished ? 'active' : 'locked' }}">
                                <i class="fa-solid {{ $yearEndPublished ? 'fa-lock-open' : 'fa-lock' }}"></i>
                                <span>{{ $yearEndPublished ? __('معلنة رسمياً للطلبة 🎓') : __('محجوبة بقرار الإدارة 🔒') }}</span>
                            </span>
                        </div>
                        <p class="gov-desc-note">
                            {{ __('عند الإعلان تظهر وثيقة التخرج ورمز QR المعتمد في حسابات الطلبة.') }}
                        </p>
                    </div>
                    <div class="gov-action-col">
                        <button type="button" onclick="togglePublishState()" id="btnTogglePublish" 
                                class="btn-gov-switch {{ $yearEndPublished ? 'btn-switch-warn' : 'btn-switch-primary' }}">
                            <i class="fa-solid {{ $yearEndPublished ? 'fa-eye-slash' : 'fa-bullhorn' }}"></i>
                            <span>{{ $yearEndPublished ? __('حجب وإغلاق الشهادات') : __('إعلان ونشر الشهادات للطلبة الآن') }}</span>
                        </button>
                    </div>
                </div>

                <!-- 2. حاسبة المعدل الوزاري للطلبة -->
                <div class="governance-control-unit">
                    <div class="gov-info-col">
                        <div class="gov-title-line">
                            <strong>{{ __('حاسبة المعدل الوزاري للطلبة:') }}</strong>
                            <span id="badgeGpaStatus" class="gov-status-tag {{ $allowStudentGpa ? 'active' : 'locked' }}">
                                <i class="fa-solid {{ $allowStudentGpa ? 'fa-calculator' : 'fa-lock' }}"></i>
                                <span>{{ $allowStudentGpa ? __('متاحة للطلبة ✅') : __('مقيدة ومحجوبة 🔒') }}</span>
                            </span>
                        </div>
                        <p class="gov-desc-note">
                            {{ __('السماح للطلبة بحساب معدل الثانوية واستخراج الدرجات بأنفسهم.') }}
                        </p>
                    </div>
                    <div class="gov-action-col">
                        <button type="button" onclick="toggleGpaState()" id="btnToggleGpa" 
                                class="btn-gov-switch {{ $allowStudentGpa ? 'btn-switch-muted' : 'btn-switch-outline' }}">
                            <i class="fa-solid {{ $allowStudentGpa ? 'fa-lock' : 'fa-check' }}"></i>
                            <span>{{ $allowStudentGpa ? __('قفل الحاسبة عن الطلبة') : __('إتاحة الحاسبة للطلبة') }}</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- 3. دفتر السجل العام وقيد الدرجات والشهادات (Imperial Academic Registry Ledger) -->
    <div class="classic-ledger-card">
        
        <!-- شريط أدوات السجل الأكاديمي -->
        <div class="ledger-header-bar">
            <div class="ledger-titles">
                <h2>
                    <i class="fa-solid fa-book-bookmark text-primary"></i>
                    <span>{{ __('دفتر السجل العام وقيد الدرجات والشهادات') }}</span>
                </h2>
                <p>{{ __('رصد وتدقيق المعدلات الوزارية الرسمية وحالات اعتماد وثائق التخرج.') }}</p>
            </div>

            <div class="ledger-tools-cluster">
                <!-- شريط البحث السريع في السجل -->
                <div class="ledger-search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="ledgerSearchInput" placeholder="{{ __('بحث بالاسم، رقم الهوية، أو البريد...') }}" onkeyup="filterLedgerTable()">
                    <button type="button" class="btn-search-clear" onclick="clearLedgerSearch()" title="{{ __('مسح') }}">✕</button>
                </div>

                <!-- تصفية حسب الفرع الأكاديمي -->
                <div class="ledger-filter-select-wrap">
                    <select id="branchFilterSelect" onchange="filterLedgerTable()" class="ledger-select">
                        <option value="">{{ __('كافة الفروع الأكاديمية') }}</option>
                        <option value="علمي">{{ __('الفرع العلمي') }}</option>
                        <option value="أدبي">{{ __('الفرع الأدبي') }}</option>
                        <option value="ريادي">{{ __('فرع ريادة الأعمال') }}</option>
                        <option value="شرعي">{{ __('الفرع الشرعي') }}</option>
                        <option value="صناعي">{{ __('الفرع الصناعي') }}</option>
                    </select>
                </div>

                <!-- تصفية حسب حالة الاعتماد -->
                <div class="ledger-filter-select-wrap">
                    <select id="statusFilterSelect" onchange="filterLedgerTable()" class="ledger-select">
                        <option value="">{{ __('كافة الحالات') }}</option>
                        <option value="معتمد">{{ __('معتمد ومختوم رسمياً') }}</option>
                        <option value="بانتظار">{{ __('بانتظار الرصد والاعتماد') }}</option>
                    </select>
                </div>

                <!-- شارة عدد السجلات المقيدة -->
                <div class="ledger-count-badge">
                    <span>{{ __('إجمالي القيود:') }}</span>
                    <strong id="ledgerRecordsCount">{{ $students->total() }}</strong>
                </div>
            </div>
        </div>

        <!-- جدول السجل الأكاديمي الرصين -->
        <div class="table-responsive">
            <table class="academic-ledger-table" id="certificatesLedgerTable">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th style="min-width: 240px;">{{ __('الطالب وبيانات القيد الأكاديمي') }}</th>
                        <th style="min-width: 170px;">{{ __('الفرع والمسار الأكاديمي') }}</th>
                        <th style="min-width: 150px;">{{ __('رقم الهوية الفلسطينية') }}</th>
                        <th style="min-width: 190px;">{{ __('المعدل الوزاري والتقدير') }}</th>
                        <th style="min-width: 170px;">{{ __('حالة وثيقة التخرج') }}</th>
                        <th style="text-align: center; min-width: 260px;">{{ __('الرصد السريع والإجراءات الرسمية') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $st)
                        @php
                            $latestCert = $st->certificates->first();
                            $stDispName = (app()->getLocale() === 'en' && !empty($st->name_en)) ? $st->name_en : $st->name_ar;
                            $rowNum = ($students->currentPage() - 1) * $students->perPage() + $index + 1;
                            $stageLabel = $st->stage?->label_ar ? __($st->stage->label_ar) : __('توجيهي عام');
                            $certStatusWord = $latestCert ? 'معتمد' : 'بانتظار';
                        @endphp
                        <tr id="row_student_{{ $st->id }}" class="ledger-row" 
                            data-search="{{ strtolower($stDispName . ' ' . $st->email . ' ' . $st->nid . ' ' . $stageLabel) }}"
                            data-branch="{{ strtolower($stageLabel) }}"
                            data-status="{{ $certStatusWord }}">
                            
                            <!-- الرقم التسلسلي بالسجل -->
                            <td style="text-align: center; color: #64748b; font-weight: 700; font-family: monospace;">
                                {{ sprintf('%02d', $rowNum) }}
                            </td>

                            <!-- اسم الطالب وبياناته -->
                            <td>
                                <div class="student-profile-cell">
                                    <div class="student-avatar-box">
                                        {{ mb_substr($stDispName, 0, 1) }}
                                    </div>
                                    <div class="student-meta-col">
                                        <a href="{{ route('admin.students.show', $st->id) }}" class="student-name-link" title="{{ __('عرض ملف الطالب الكامل') }}">
                                            {{ $stDispName }}
                                        </a>
                                        <span class="student-email-tag" dir="ltr">{{ $st->email }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- الفرع والمسار -->
                            <td>
                                <span class="classic-academic-pill">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                    <span>{{ $stageLabel }}</span>
                                </span>
                            </td>

                            <!-- رقم الهوية الوطنية الفلسطينية -->
                            <td>
                                <span class="national-id-tag font-mono">
                                    <i class="fa-solid fa-id-card text-muted"></i>
                                    <span>{{ $st->nid }}</span>
                                </span>
                            </td>

                            <!-- المعدل الوزاري والتقدير -->
                            <td>
                                @if($latestCert)
                                    <div class="grade-status-cell">
                                        <span class="grade-royal-badge">
                                            <i class="fa-solid fa-certificate"></i>
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
                                    </div>
                                @else
                                    <span class="pending-grade-pill">
                                        <i class="fa-regular fa-clock"></i>
                                        <span>{{ __('بانتظار الرصد والاعتماد') }}</span>
                                    </span>
                                @endif
                            </td>

                            <!-- حالة وثيقة التخرج -->
                            <td>
                                @if($latestCert)
                                    <div class="cert-status-col">
                                        <span class="status-pill-certified">
                                            <i class="fa-solid fa-seal-exclamation"></i>
                                            <span>{{ __('معتمدة ومختومة رسمياً') }}</span>
                                        </span>
                                        <small class="cert-code-tag font-mono" title="{{ __('الرقم التسلسلي للوثيقة') }}">
                                            {{ $latestCert->certificate_code }}
                                        </small>
                                    </div>
                                @else
                                    <span class="status-pill-pending">
                                        <i class="fa-solid fa-hourglass-start"></i>
                                        <span>{{ __('مسودة قيد التدقيق') }}</span>
                                    </span>
                                @endif
                            </td>

                            <!-- الإجراءات والرصد السريع -->
                            <td style="text-align: center;">
                                <div class="ledger-actions-group">
                                    <!-- رصد سريع ومباشر للعلامة -->
                                    <div class="quick-grade-widget" title="{{ __('رصد وتعديل فوري للمعدل الوزاري') }}">
                                        <input type="number" id="quick_grade_{{ $st->id }}" min="0" max="100" step="0.5" 
                                               value="{{ $latestCert ? $latestCert->final_grade : '' }}" 
                                               placeholder="%" class="quick-grade-input font-mono">
                                        <button type="button" onclick="quickSaveCertGrade({{ $st->id }}, this)" 
                                                class="btn-quick-save" title="{{ __('حفظ فوري للمعدل في السجل') }}">
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
                                                class="btn-tbl-action outline-amber" title="{{ __('تعديل بيانات واعتماد الشهادة') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <button type="button" onclick="deleteCert({{ $latestCert->id }}, '{{ addslashes($stDispName) }}')" 
                                                class="btn-tbl-action outline-red" title="{{ __('سحب وإلغاء الشهادة من السجل') }}">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    @else
                                        <button type="button" onclick="openIssueModal({{ $st->id }}, '{{ addslashes($stDispName) }}')" 
                                                class="btn-tbl-action outline-blue" title="{{ __('رصد واعتماد شهادة تفصيلية') }}">
                                            <i class="fa-solid fa-award"></i>
                                            <span>{{ __('رصد تفصيلي') }}</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="ledger-empty-cell">
                                <div class="empty-registry-box">
                                    <i class="fa-solid fa-folder-open"></i>
                                    <h3>{{ __('لا يوجد طلبة مقيدون حالياً في سجل النتائج والشهادات') }}</h3>
                                    <p>{{ __('عند تسجيل وقبول طلبة جدد في المنظومة، ستظهر بياناتهم هنا تلقائياً للرصد والاعتماد الأكاديمي.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- تذييل السجل الأكاديمي الرسمي وختم الاعتماد -->
        <div class="ledger-footer-seal-strip">
            <div class="seal-note">
                <i class="fa-solid fa-stamp text-amber"></i>
                <span>{{ __('ختم السجل العام الرسمي • كافة الدرجات والوثائق الصادرة عن هذا السجل معتمدة بموجب بروتوكول الامتحانات العامة، وموثقة برمز استجابة سريع QR مشفر وخاضعة للتدقيق الوزاري المباشر.') }}</span>
            </div>

            @if($students->hasPages())
                <div class="ledger-pagination-wrap">
                    {{ $students->links() }}
                </div>
            @endif
        </div>

    </div>

</div>

<!-- 4. محضر رصد واعتماد الشهادة الأكاديمية (Official Accreditation Modal) -->
<div id="issueModalOverlay" class="classic-modal-backdrop">
    <div class="classic-modal-dialog">
        
        <div class="modal-header-classic">
            <div class="modal-header-title">
                <div class="modal-seal-icon">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div>
                    <h3>{{ __('محضر رصد واعتماد شهادة الثانوية العامة') }}</h3>
                    <p>{{ __('إصدار وتوثيق شهادة إتمام وتفوق رسمية صادرة عن ديوان الامتحانات العامة') }}</p>
                </div>
            </div>
            <button type="button" onclick="closeIssueModal()" class="modal-btn-close" aria-label="{{ __('إغلاق') }}">✕</button>
        </div>

        <form id="issueCertForm" class="modal-body-classic">
            @csrf
            <div class="modal-fields-grid">

                <!-- اختيار الطالب -->
                <div class="modal-field-group">
                    <label for="modalStudentSelect">{{ __('الطالب المقيد في السجل الأكاديمي *') }}</label>
                    <select name="student_id" id="modalStudentSelect" class="modal-input-field" required>
                        <option value="">-- {{ __('اختر الطالب من السجل الأكاديمي') }} --</option>
                        @foreach($students as $stOpt)
                            <option value="{{ $stOpt->id }}">
                                {{ (app()->getLocale() === 'en' && !empty($stOpt->name_en)) ? $stOpt->name_en : $stOpt->name_ar }} ({{ $stOpt->nid }})
                            </option>
                        @endforeach
                    </select>
                    <small class="field-help-text">{{ __('يتم جلب الفرع الأكاديمي والمرحلة الدراسية للطالب تلقائياً.') }}</small>
                </div>

                <!-- المبحث أو المساق -->
                <div class="modal-field-group">
                    <label for="modalSubjectSelect">{{ __('نوع الشهادة / المبحث الأكاديمي') }}</label>
                    <select name="subject_id" id="modalSubjectSelect" class="modal-input-field">
                        <option value="">{{ __('شهادة التخرج العامة / المعدل الوزاري العام 🇵🇸') }}</option>
                        @foreach($subjects as $sbOpt)
                            <option value="{{ $sbOpt->id }}">
                                {{ $sbOpt->name_ar ? __($sbOpt->name_ar) : $sbOpt->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="field-help-text">{{ __('اتركه على الخيار الافتراضي لإصدار وثيقة التخرج العامة والمعدل النهائي.') }}</small>
                </div>

                <!-- المعدل أو الدرجة المئوية -->
                <div class="modal-field-group">
                    <label for="modalFinalGrade">{{ __('المعدل الوزاري المعتمد (0 - 100%) *') }}</label>
                    <div style="position: relative;">
                        <input type="number" step="0.01" min="0" max="100" name="final_grade" id="modalFinalGrade" 
                               class="modal-input-field font-mono" placeholder="95.5" required oninput="previewHonorRank(this.value)">
                        <span class="input-pct-mark">%</span>
                    </div>
                    <small class="field-help-text" id="honorRankPreviewText">
                        {{ __('الدرجة الرسمية التي ستطبع على وثيقة التخرج وتحفظ في السجل العام.') }}
                    </small>
                </div>

            </div>

            <div class="modal-footer-classic">
                <button type="button" onclick="closeIssueModal()" class="btn-modal-cancel">
                    {{ __('إلغاء') }}
                </button>
                <button type="button" onclick="submitIssueCert()" id="btnSubmitIssue" class="btn-royal-action">
                    <i class="fa-solid fa-stamp"></i>
                    <span>{{ __('اعتماد وحفظ الشهادة بالختم الرسمي') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* ==========================================================================
       التصميم الأكاديمي الكلاسيكي الملكي الرفيع - ديوان الامتحانات والسجل العام
       (Royal Classic Academic Registry & Examination Board Design)
       - باليت الألوان الأكاديمية العريقة: الكحلي الملكي (#0d1b2a, #1e3a8a)، والذهبي (#b45309, #d97706)
       - خط Tajawal الأكاديمي الرصين مع أحجام محكمة 13-14px
       - منصة ضبط مدمجة (لا بطاقات ضخمة مفرغة)
       - جدول سجل تاريخي عالي الكثافة يحاكي أسلوب عمادات القبول المرموقة
       ========================================================================== */

    :root {
        --reg-navy: #0d1b2a;
        --reg-navy-light: #1e3a8a;
        --reg-gold: #b45309;
        --reg-gold-soft: #fef3c7;
        --reg-gold-border: #fde68a;
        --reg-surface: #ffffff;
        --reg-surface-alt: #f8fafc;
        --reg-border: #cbd5e1;
        --reg-border-subtle: #e2e8f0;
        --reg-text-main: #0f172a;
        --reg-text-body: #334155;
        --reg-text-muted: #64748b;
        --reg-success: #15803d;
        --reg-danger: #b91c1c;
    }

    .ed-admin-container {
        max-width: 1440px;
        margin: 0 auto;
        padding: 8px 10px 60px;
    }

    /* 1. ترويسة ديوان الامتحانات العامة الأكاديمية */
    .classic-registry-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
        background: #ffffff;
        border: 1px solid var(--reg-border);
        border-top: 4px solid var(--reg-navy-light);
        border-radius: 6px;
        padding: 20px 24px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        position: relative;
    }

    .registry-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.78rem;
        color: var(--reg-text-muted);
        margin-bottom: 6px;
    }

    .registry-breadcrumb a {
        color: var(--reg-text-body);
        font-weight: 600;
        text-decoration: none;
    }

    .registry-breadcrumb a:hover {
        color: var(--reg-navy-light);
        text-decoration: underline;
    }

    .bc-divider { color: #cbd5e1; }
    .bc-current { color: var(--reg-navy-light); font-weight: 700; }

    .registry-authority-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.76rem;
        font-weight: 700;
        color: var(--reg-gold);
        background: var(--reg-gold-soft);
        border: 1px solid var(--reg-gold-border);
        padding: 3px 9px;
        border-radius: 4px;
        margin-bottom: 8px;
    }

    .registry-main-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--reg-text-main);
        margin: 0 0 6px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .royal-crest-icon {
        color: var(--reg-navy-light);
        font-size: 1.3rem;
    }

    .session-year-stamp {
        font-size: 0.8rem;
        font-weight: 800;
        color: #1e40af;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .registry-sub-title {
        font-size: 0.84rem;
        color: var(--reg-text-muted);
        margin: 0;
        line-height: 1.55;
        max-width: 950px;
    }

    .registry-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        align-self: flex-start;
    }

    .btn-royal-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: var(--reg-navy-light);
        color: #ffffff !important;
        border: 1px solid #1e3a8a;
        padding: 9px 18px;
        border-radius: 5px;
        font-size: 0.86rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(30, 58, 138, 0.2);
    }

    .btn-royal-action:hover {
        background-color: #1e40af;
        box-shadow: 0 4px 10px rgba(30, 58, 138, 0.3);
    }

    .btn-classic-outline {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background-color: #ffffff;
        color: var(--reg-text-body);
        border: 1px solid var(--reg-border);
        padding: 9px 16px;
        border-radius: 5px;
        font-size: 0.84rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-classic-outline:hover {
        background-color: #f8fafc;
        border-color: #94a3b8;
    }

    /* 2. منصة الضبط والرقابة الأكاديمية الكلاسيكية الموحدة (Master Console) */
    .classic-registry-console {
        display: grid;
        grid-template-columns: 1fr auto 1.25fr;
        gap: 20px;
        background: #ffffff;
        border: 1px solid var(--reg-border);
        border-radius: 6px;
        padding: 18px 22px;
        margin-bottom: 22px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        align-items: center;
    }

    .console-section-heading {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--reg-text-main);
        margin-bottom: 12px;
    }

    .console-kpi-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #f8fafc;
        border: 1px solid var(--reg-border-subtle);
        border-radius: 5px;
        padding: 12px 16px;
    }

    .metric-kpi-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .metric-kpi-label {
        font-size: 0.72rem;
        color: var(--reg-text-muted);
        font-weight: 600;
    }

    .metric-kpi-val {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--reg-navy);
        font-family: 'Tajawal', sans-serif;
    }

    .metric-kpi-val small {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--reg-text-muted);
    }

    .metric-kpi-divider {
        width: 1px;
        height: 32px;
        background-color: #e2e8f0;
    }

    .console-vertical-divider {
        width: 1px;
        height: 100%;
        min-height: 80px;
        background-color: var(--reg-border-subtle);
    }

    .governance-controls-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .governance-control-unit {
        background: #fcfdfd;
        border: 1px solid var(--reg-border-subtle);
        border-radius: 5px;
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 10px;
    }

    .gov-title-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        font-size: 0.8rem;
        color: var(--reg-text-main);
        margin-bottom: 4px;
        flex-wrap: wrap;
    }

    .gov-status-tag {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .gov-status-tag.active {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .gov-status-tag.locked {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .gov-desc-note {
        font-size: 0.72rem;
        color: var(--reg-text-muted);
        margin: 0;
        line-height: 1.4;
    }

    .btn-gov-switch {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .btn-switch-primary {
        background-color: var(--reg-navy-light);
        color: #ffffff;
        border-color: var(--reg-navy-light);
    }
    .btn-switch-primary:hover {
        background-color: #1e40af;
    }

    .btn-switch-warn {
        background-color: #fef2f2;
        color: var(--reg-danger);
        border-color: #fecaca;
    }
    .btn-switch-warn:hover {
        background-color: #fee2e2;
    }

    .btn-switch-outline {
        background-color: #ffffff;
        color: var(--reg-navy-light);
        border-color: #bfdbfe;
    }
    .btn-switch-outline:hover {
        background-color: #eff6ff;
    }

    .btn-switch-muted {
        background-color: #f1f5f9;
        color: #334155;
        border-color: #cbd5e1;
    }
    .btn-switch-muted:hover {
        background-color: #e2e8f0;
    }

    /* 3. دفتر السجل العام وقيد الدرجات (Ledger Card) */
    .classic-ledger-card {
        background: #ffffff;
        border: 1px solid var(--reg-border);
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .ledger-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        padding: 16px 20px;
        background: #ffffff;
        border-bottom: 1px solid var(--reg-border-subtle);
    }

    .ledger-titles h2 {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--reg-text-main);
        margin: 0 0 3px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ledger-titles p {
        font-size: 0.78rem;
        color: var(--reg-text-muted);
        margin: 0;
    }

    .ledger-tools-cluster {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ledger-search-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .ledger-search-box i {
        position: absolute;
        right: 11px;
        color: #94a3b8;
        font-size: 0.8rem;
        pointer-events: none;
    }

    .ledger-search-box input {
        width: 250px;
        height: 34px;
        padding: 0 32px 0 28px;
        font-size: 0.8rem;
        border: 1px solid var(--reg-border);
        border-radius: 4px;
        outline: none;
        background: #f8fafc;
        color: var(--reg-text-main);
        transition: all 0.2s ease;
    }

    .ledger-search-box input:focus {
        background: #ffffff;
        border-color: var(--reg-navy-light);
        box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.1);
    }

    .btn-search-clear {
        position: absolute;
        left: 8px;
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 0.75rem;
        cursor: pointer;
        display: none;
    }

    .ledger-select {
        height: 34px;
        padding: 0 10px;
        font-size: 0.8rem;
        border: 1px solid var(--reg-border);
        border-radius: 4px;
        background: #ffffff;
        color: var(--reg-text-body);
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }

    .ledger-select:focus {
        border-color: var(--reg-navy-light);
    }

    .ledger-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 0 10px;
        height: 34px;
        border-radius: 4px;
        font-size: 0.78rem;
        color: var(--reg-text-body);
    }

    .ledger-count-badge strong {
        color: var(--reg-navy-light);
        font-weight: 800;
    }

    /* جدول السجل الأكاديمي */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .academic-ledger-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
        font-size: 0.82rem;
    }

    .academic-ledger-table thead th {
        background-color: var(--reg-navy);
        color: #f8fafc;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 12px 14px;
        border-bottom: 2px solid var(--reg-gold);
        letter-spacing: 0.2px;
        white-space: nowrap;
    }

    .academic-ledger-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid var(--reg-border-subtle);
        vertical-align: middle;
        color: var(--reg-text-body);
        background: #ffffff;
        transition: background 0.15s ease;
    }

    .academic-ledger-table tbody tr:nth-child(even) td {
        background-color: #fbfcfd;
    }

    .academic-ledger-table tbody tr:hover td {
        background-color: #f1f5f9;
    }

    /* خلايا الطالب والبيانات */
    .student-profile-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .student-avatar-box {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        background: #eff6ff;
        color: var(--reg-navy-light);
        border: 1px solid #bfdbfe;
        display: grid;
        place-items: center;
        font-weight: 800;
        font-size: 0.85rem;
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
        color: var(--reg-text-main);
        text-decoration: none;
    }

    .student-name-link:hover {
        color: var(--reg-navy-light);
        text-decoration: underline;
    }

    .student-email-tag {
        font-size: 0.72rem;
        color: var(--reg-text-muted);
    }

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
        white-space: nowrap;
    }

    .national-id-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 2px 7px;
        border-radius: 4px;
        white-space: nowrap;
    }

    /* خلايا المعدل والشهادة */
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
        font-weight: 700;
        color: #92400e;
        font-size: 0.72rem;
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
        white-space: nowrap;
    }

    .cert-status-col {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .status-pill-certified {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #047857;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        padding: 2px 7px;
        border-radius: 4px;
        width: fit-content;
    }

    .cert-code-tag {
        font-size: 0.68rem;
        color: #64748b;
        letter-spacing: 0.5px;
    }

    .status-pill-pending {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 2px 7px;
        border-radius: 4px;
        width: fit-content;
    }

    /* خلايا الإجراءات والرصد السريع */
    .ledger-actions-group {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        justify-content: center;
    }

    .quick-grade-widget {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: #ffffff;
        border: 1px solid var(--reg-border);
        border-radius: 4px;
        padding: 2px 3px;
    }

    .quick-grade-input {
        width: 48px;
        height: 25px;
        border: 1px solid #e2e8f0;
        border-radius: 3px;
        font-size: 0.8rem;
        font-weight: 800;
        text-align: center;
        color: var(--reg-navy);
        outline: none;
        background: #f8fafc;
    }

    .quick-grade-input:focus {
        border-color: var(--reg-navy-light);
        background: #ffffff;
    }

    .btn-quick-save {
        width: 25px;
        height: 25px;
        background-color: var(--reg-navy-light);
        color: #ffffff;
        border: none;
        border-radius: 3px;
        font-size: 0.72rem;
        cursor: pointer;
        display: grid;
        place-items: center;
        transition: background 0.15s ease;
    }

    .btn-quick-save:hover {
        background-color: #1e40af;
    }

    .btn-tbl-action {
        height: 27px;
        padding: 0 8px;
        border-radius: 3px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        text-decoration: none;
        border: 1px solid transparent;
        transition: all 0.15s ease;
    }

    .btn-tbl-action.outline-blue {
        background: #ffffff;
        color: var(--reg-navy-light);
        border-color: #bfdbfe;
    }
    .btn-tbl-action.outline-blue:hover {
        background: #eff6ff;
        border-color: var(--reg-navy-light);
    }

    .btn-tbl-action.outline-amber {
        background: #ffffff;
        color: var(--reg-gold);
        border-color: #fde68a;
    }
    .btn-tbl-action.outline-amber:hover {
        background: #fef3c7;
        border-color: var(--reg-gold);
    }

    .btn-tbl-action.outline-red {
        background: #ffffff;
        color: var(--reg-danger);
        border-color: #fecaca;
    }
    .btn-tbl-action.outline-red:hover {
        background: #fef2f2;
        border-color: var(--reg-danger);
    }

    .ledger-empty-cell {
        padding: 40px 20px !important;
        text-align: center;
    }

    .empty-registry-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: #94a3b8;
    }

    .empty-registry-box i {
        font-size: 2.2rem;
        color: #cbd5e1;
    }

    .empty-registry-box h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #475569;
        margin: 0;
    }

    .empty-registry-box p {
        font-size: 0.8rem;
        color: #94a3b8;
        max-width: 480px;
        margin: 0;
    }

    /* 4. تذييل السجل وختم الاعتماد الرسمي */
    .ledger-footer-seal-strip {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        padding: 14px 20px;
        background: #f8fafc;
        border-top: 1px solid var(--reg-border-subtle);
    }

    .seal-note {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.74rem;
        color: var(--reg-text-muted);
        line-height: 1.45;
        max-width: 850px;
    }

    .ledger-pagination-wrap {
        display: flex;
        align-items: center;
    }

    /* 5. نافذة الرصد والاعتماد الأكاديمي الكلاسيكية (Modal) */
    .classic-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(13, 27, 42, 0.65);
        backdrop-filter: blur(2px);
        z-index: 99999;
        justify-content: center;
        align-items: center;
        padding: 16px;
    }

    .classic-modal-dialog {
        background: #ffffff;
        width: 100%;
        max-width: 560px;
        border-radius: 6px;
        border: 1px solid var(--reg-border);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: modalScaleUp 0.2s ease-out;
    }

    @keyframes modalScaleUp {
        from { transform: scale(0.96); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .modal-header-classic {
        padding: 16px 20px;
        background: var(--reg-navy);
        color: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid var(--reg-gold);
    }

    .modal-header-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-seal-icon {
        width: 36px;
        height: 36px;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fef08a;
        display: grid;
        place-items: center;
        font-size: 1.1rem;
    }

    .modal-header-title h3 {
        font-size: 0.98rem;
        font-weight: 800;
        margin: 0 0 2px;
        color: #ffffff;
    }

    .modal-header-title p {
        font-size: 0.72rem;
        color: #94a3b8;
        margin: 0;
    }

    .modal-btn-close {
        background: transparent;
        border: none;
        color: #cbd5e1;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 3px;
    }

    .modal-btn-close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }

    .modal-body-classic {
        padding: 20px;
    }

    .modal-fields-grid {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .modal-field-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .modal-field-group label {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--reg-text-main);
    }

    .modal-input-field {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--reg-border);
        border-radius: 4px;
        font-size: 0.85rem;
        color: var(--reg-text-main);
        background: #f8fafc;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .modal-input-field:focus {
        background: #ffffff;
        border-color: var(--reg-navy-light);
        box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.1);
    }

    .input-pct-mark {
        position: absolute;
        left: 14px;
        top: 8px;
        font-weight: 800;
        color: #94a3b8;
    }

    .field-help-text {
        font-size: 0.72rem;
        color: var(--reg-text-muted);
    }

    .modal-footer-classic {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        padding-top: 18px;
        margin-top: 18px;
        border-top: 1px solid var(--reg-border-subtle);
    }

    .btn-modal-cancel {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-modal-cancel:hover {
        background: #e2e8f0;
    }

    /* ألوان وأدوات مساعدة */
    .text-primary { color: var(--reg-navy-light) !important; }
    .text-amber { color: var(--reg-gold) !important; }
    .text-emerald { color: var(--reg-success) !important; }
    .text-navy { color: var(--reg-navy) !important; }
    .font-mono { font-family: monospace, sans-serif; }

    /* استجابة الشاشات الصغيرة */
    @media (max-width: 1024px) {
        .classic-registry-console {
            grid-template-columns: 1fr;
        }
        .console-vertical-divider {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .classic-registry-header {
            flex-direction: column;
        }
        .governance-controls-grid {
            grid-template-columns: 1fr;
        }
        .console-kpi-row {
            flex-wrap: wrap;
        }
        .metric-kpi-divider {
            display: none;
        }
    }

    /* أنماط الطباعة الرسمية للديوان */
    @media print {
        .registry-header-actions,
        .governance-controls-grid,
        .ledger-tools-cluster,
        .ledger-actions-group,
        .btn-quick-save,
        .btn-tbl-action,
        .classic-modal-backdrop {
            display: none !important;
        }

        .ed-admin-container {
            max-width: 100% !important;
            padding: 0 !important;
        }

        .classic-registry-header,
        .classic-registry-console,
        .classic-ledger-card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }

        .academic-ledger-table thead th {
            background-color: #eee !important;
            color: #000 !important;
            border-bottom: 2px solid #000 !important;
        }
    }
</style>

<script>
    // ترجمات ورسائل الواجهة
    const certI18n = {
        savePrompt: "{{ __('حفظ فوري للمعدل في السجل') }}",
        saving: "{{ __('جاري الحفظ...') }}",
        confirmDeleteTitle: "{{ __('سحب الشهادة الأكاديمية') }}",
        confirmDeleteText: "{{ __('هل أنت متأكد من رغبتك في سحب وإلغاء اعتماد شهادة الطالب: ') }}",
        deleteConfirmBtn: "{{ __('نعم، اسحب الشهادة') }}",
        cancelBtn: "{{ __('إلغاء') }}",
        certIssuedTitle: "{{ __('تم اعتماد الشهادة بنجاح! 🏆') }}",
        certDeletedTitle: "{{ __('تم سحب الشهادة بنجاح') }}",
        errorNetwork: "{{ __('تعذر الاتصال بالخادم، يرجى إعادة المحاولة.') }}",
        honorExcellent: "{{ __('ممتاز مع مرتبة الشرف 🏆') }}",
        honorVGood: "{{ __('جيد جداً 🎖️') }}",
        honorGood: "{{ __('جيد 📘') }}",
        honorPass: "{{ __('مقبول 📄') }}"
    };

    // 1. فتح نافذة اعتماد الشهادة
    function openIssueModal(studentId = null, studentName = '', grade = null, subjectId = null) {
        const modal = document.getElementById('issueModalOverlay');
        const stSelect = document.getElementById('modalStudentSelect');
        const gradeInput = document.getElementById('modalFinalGrade');
        const subSelect = document.getElementById('modalSubjectSelect');

        if (studentId) {
            stSelect.value = studentId;
        } else {
            stSelect.selectedIndex = 0;
        }

        gradeInput.value = grade !== null ? grade : '';
        if (subjectId) {
            subSelect.value = subjectId;
        } else {
            subSelect.selectedIndex = 0;
        }

        previewHonorRank(gradeInput.value);
        modal.style.display = 'flex';
    }

    function closeIssueModal() {
        document.getElementById('issueModalOverlay').style.display = 'none';
    }

    // إغلاق النافذة عند الضغط على الخلفية
    document.getElementById('issueModalOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            closeIssueModal();
        }
    });

    // 2. معاينة تقدير المعدل فورياً داخل النافذة
    function previewHonorRank(gradeVal) {
        const preview = document.getElementById('honorRankPreviewText');
        const num = parseFloat(gradeVal);
        if (isNaN(num) || num <= 0) {
            preview.textContent = "{{ __('الدرجة الرسمية التي ستطبع على وثيقة التخرج وتحفظ في السجل العام.') }}";
            return;
        }

        let rankText = certI18n.honorPass;
        if (num >= 90) rankText = certI18n.honorExcellent;
        else if (num >= 80) rankText = certI18n.honorVGood;
        else if (num >= 70) rankText = certI18n.honorGood;

        preview.innerHTML = `<strong style="color: #b45309;">التقدير التلقائي: ${rankText}</strong>`;
    }

    // 3. إرسال نموذج اعتماد الشهادة
    function submitIssueCert() {
        const studentId = document.getElementById('modalStudentSelect').value;
        const grade = document.getElementById('modalFinalGrade').value;
        const subjectId = document.getElementById('modalSubjectSelect').value;
        const btn = document.getElementById('btnSubmitIssue');

        if (!studentId) {
            alert('{{ __("يرجى اختيار الطالب من السجل أولاً.") }}');
            return;
        }
        if (!grade || grade < 0 || grade > 100) {
            alert('{{ __("يرجى إدخال معدل صحيح بين 0 و 100%.") }}');
            return;
        }

        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> ${certI18n.saving}`;

        fetch('{{ route("admin.certificates.issue") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                student_id: studentId,
                final_grade: grade,
                subject_id: subjectId || null
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = origHtml;

            if (data.success) {
                closeIssueModal();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: data.title || certI18n.certIssuedTitle,
                        text: data.message,
                        showConfirmButton: true,
                        confirmButtonText: '{{ __("حسناً") }}'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.message);
                    window.location.reload();
                }
            } else {
                alert(data.message || '{{ __("حدث خطأ أثناء اعتماد الشهادة.") }}');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            alert(certI18n.errorNetwork);
        });
    }

    // 4. الحفظ السريع للمعدل من الجدول مباشرة
    function quickSaveCertGrade(studentId, btnElem) {
        const input = document.getElementById(`quick_grade_${studentId}`);
        const grade = input.value;

        if (!grade || grade < 0 || grade > 100) {
            alert('{{ __("يرجى كتابة معدل صحيح بين 0 و 100%.") }}');
            return;
        }

        const origHtml = btnElem.innerHTML;
        btnElem.disabled = true;
        btnElem.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        fetch('{{ route("admin.certificates.issue") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                student_id: studentId,
                final_grade: grade
            })
        })
        .then(res => res.json())
        .then(data => {
            btnElem.disabled = false;
            btnElem.innerHTML = origHtml;

            if (data.success) {
                btnElem.style.backgroundColor = '#16a34a';
                setTimeout(() => { btnElem.style.backgroundColor = ''; }, 1200);

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("تم رصد وحفظ المعدل") }}',
                        text: data.message,
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    window.location.reload();
                }
            } else {
                alert(data.message || '{{ __("تعذر رصد المعدل") }}');
            }
        })
        .catch(err => {
            btnElem.disabled = false;
            btnElem.innerHTML = origHtml;
            alert(certI18n.errorNetwork);
        });
    }

    // 5. سحب وإلغاء اعتماد الشهادة
    function deleteCert(certId, studentName) {
        const confirmMsg = `${certI18n.confirmDeleteText} (${studentName})؟`;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: certI18n.confirmDeleteTitle,
                text: confirmMsg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b91c1c',
                cancelButtonColor: '#64748b',
                confirmButtonText: certI18n.deleteConfirmBtn,
                cancelButtonText: certI18n.cancelBtn
            }).then((result) => {
                if (result.isConfirmed) {
                    executeCertDelete(certId);
                }
            });
        } else {
            if (confirm(confirmMsg)) {
                executeCertDelete(certId);
            }
        }
    }

    function executeCertDelete(certId) {
        fetch(`/admin/certificates/${certId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: certI18n.certDeletedTitle,
                        text: data.message,
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    window.location.reload();
                }
            } else {
                alert(data.message || '{{ __("تعذر سحب الشهادة") }}');
            }
        })
        .catch(err => {
            alert(certI18n.errorNetwork);
        });
    }

    // 6. تبديل حالة إعلان الشهادات للطلبة
    function togglePublishState() {
        const btn = document.getElementById('btnTogglePublish');
        btn.disabled = true;

        fetch('{{ route("admin.certificates.togglePublish") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: data.icon,
                        title: data.title,
                        text: data.message,
                        confirmButtonText: '{{ __("حسناً") }}'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.title + "\n" + data.message);
                    window.location.reload();
                }
            }
        })
        .catch(err => {
            btn.disabled = false;
            alert(certI18n.errorNetwork);
        });
    }

    // 7. تبديل حالة حاسبة المعدل
    function toggleGpaState() {
        const btn = document.getElementById('btnToggleGpa');
        btn.disabled = true;

        fetch('{{ route("admin.certificates.toggleGpa") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: data.icon,
                        title: data.title,
                        text: data.message,
                        confirmButtonText: '{{ __("حسناً") }}'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.title + "\n" + data.message);
                    window.location.reload();
                }
            }
        })
        .catch(err => {
            btn.disabled = false;
            alert(certI18n.errorNetwork);
        });
    }

    // 8. تصفية وبحث لحظي في جدول السجل الأكاديمي
    function filterLedgerTable() {
        const searchVal = document.getElementById('ledgerSearchInput').value.toLowerCase().trim();
        const branchVal = document.getElementById('branchFilterSelect').value.toLowerCase().trim();
        const statusVal = document.getElementById('statusFilterSelect').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#certificatesLedgerTable tbody tr.ledger-row');
        const clearBtn = document.querySelector('.btn-search-clear');

        clearBtn.style.display = searchVal ? 'inline-block' : 'none';

        let visibleCount = 0;
        rows.forEach(row => {
            const rowSearch = row.getAttribute('data-search') || '';
            const rowBranch = row.getAttribute('data-branch') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchSearch = !searchVal || rowSearch.includes(searchVal);
            const matchBranch = !branchVal || rowBranch.includes(branchVal);
            const matchStatus = !statusVal || rowStatus.includes(statusVal);

            if (matchSearch && matchBranch && matchStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const countBadge = document.getElementById('ledgerRecordsCount');
        if (countBadge) {
            countBadge.textContent = visibleCount;
        }
    }

    function clearLedgerSearch() {
        document.getElementById('ledgerSearchInput').value = '';
        filterLedgerTable();
    }
</script>
@endsection
