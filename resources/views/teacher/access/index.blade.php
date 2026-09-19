@extends('layouts.app')

@section('title', __('تحكم صلاحيات وظهور المحتوى والاختبارات للطلاب'))

@section('content')
<div class="access-page-wrapper">

    <!-- كرت الهيدر والأزرار السريعة للمعلم (نظام أكاديمي فاتح) -->
    <div class="access-hero-card">
        <div class="hero-text-block">
            <span class="hero-badge">
                <i class="fa-solid fa-shield-halved"></i> {{ __('مساحة تحكم المعلم الأكاديمية') }}
            </span>
            <h1 class="hero-headline">{{ __('التحكم بظهور الفيديوهات والملفات والاختبارات للطلاب') }} 🎯</h1>
            <p class="hero-desc">
                {{ __('حدد ما يظهر لكل طالب عبر خانات الاختيار [✓]: المنهج كاملاً أو جزئيات واختبارات محددة وفق اشتراكه.') }}
            </p>
        </div>

        <div class="hero-quick-actions">
            <a href="{{ route('teacher.exams.create') }}" class="btn-hero-action exam-action">
                <i class="fa-solid fa-plus-circle"></i>
                <span>{{ __('بناء اختبار جديد') }}</span>
            </a>
            <a href="{{ route('teacher.educational_contents.create') }}" class="btn-hero-action content-action">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>{{ __('رفع فيديو / ملخص PDF') }}</span>
            </a>
            <button type="button" onclick="openQuickEnrollModal()" class="btn-hero-action enroll-action">
                <i class="fa-solid fa-user-plus"></i>
                <span>{{ __('تفعيل طالب سريعاً') }}</span>
            </button>
        </div>
    </div>

    <!-- شريط اختيار المادة الأكاديمية -->
    @if($subjects->count() > 1)
        <div class="subjects-filter-lane">
            <span class="filter-label"><i class="fa-solid fa-book-bookmark"></i> {{ __('اختر المادة:') }}</span>
            <div class="subjects-scroll">
                @foreach($subjects as $sub)
                    <a href="{{ route('teacher.access.index', ['subject_id' => $sub->id]) }}" 
                       class="sub-pill {{ $selectedSubject && $selectedSubject->id == $sub->id ? 'active' : '' }}">
                        <span>{{ (app()->getLocale() === 'en' && !empty($sub->name_en)) ? $sub->name_en : __($sub->name_ar ?? $sub->name) }}</span>
                        <span class="sub-stage-tag">{{ (app()->getLocale() === 'en' && !empty($sub->stage->name_en)) ? $sub->stage->name_en : ($sub->stage->name_ar ?? __('توجيهي')) }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- أزرار التبديل بين طريقتي التحكم -->
    <div class="control-tabs-bar">
        <button type="button" class="tab-btn active" id="tabBtnStudents" onclick="switchTab('students')">
            <i class="fa-solid fa-users"></i>
            <span>{{ __('التحكم بحسب الطالب (عرض صلاحيات كل طالب)') }}</span>
        </button>
        <button type="button" class="tab-btn" id="tabBtnItems" onclick="switchTab('items')">
            <i class="fa-solid fa-list-check"></i>
            <span>{{ __('التحكم بحسب الدرس أو الاختبار (اختيار صح بجانب اسم الطالب [✓])') }}</span>
        </button>
    </div>

    <!-- ============================================================== -->
    <!-- التبويب الأول: جدول الطلاب مع تحديد الصلاحية لكل طالب -->
    <!-- ============================================================== -->
    <div id="view_students" class="tab-view-section">
        <div class="table-outer-card">
            <div class="table-header-row">
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 4px;">
                        {{ __('الطلاب المشتركون في') }} ({{ (app()->getLocale() === 'en' && !empty($selectedSubject->name_en)) ? $selectedSubject->name_en : __($selectedSubject->name_ar ?? $selectedSubject->name ?? 'المادة') }})
                    </h3>
                    <p style="font-size: 0.82rem; color: #64748b; margin: 0;">
                        {{ __('إجمالي الطلاب:') }} <strong>{{ $enrollments->total() }}</strong> • {{ __('إجمالي الدروس:') }} <strong>{{ $totalContentsCount }}</strong> • {{ __('إجمالي الاختبارات:') }} <strong>{{ $totalExamsCount }}</strong>
                    </p>
                </div>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <button type="button" onclick="window.print()" class="btn-print-roster">
                        <i class="fa-solid fa-print"></i> {{ __('طباعة الكشف') }} 🖨️
                    </button>
                    <div class="table-search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="studentSearchInput" placeholder="{{ __('بحث باسم الطالب أو البريد...') }}" onkeyup="filterStudentsTable()">
                    </div>
                </div>
            </div>

            <div class="table-responsive-wrapper">
                <table class="access-data-table" id="studentsTable">
                    <thead>
                        <tr>
                            <th>{{ __('الطالب') }}</th>
                            <th>{{ __('الفرع الأكاديمي') }}</th>
                            <th>{{ __('نوع الصلاحية الحالية') }}</th>
                            <th>{{ __('حالة الحساب') }}</th>
                            <th>{{ __('تاريخ التفعيل') }}</th>
                            <th style="text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">{{ __('إجراءات الصلاحية') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enr)
                            @php
                                $stName = (app()->getLocale() === 'en' && !empty($enr->student->name_en)) ? $enr->student->name_en : ($enr->student->name_ar ?? $enr->student->name ?? __('طالب توجيهي'));
                                $isAll = ($enr->access_mode === 'all');
                                $customContentsCount = $enr->contentAssignments->where('is_visible', true)->count();
                                $customExamsCount = $enr->examAssignments->where('is_visible', true)->count();
                            @endphp
                            <tr class="student-row" data-search="{{ mb_strtolower($stName . ' ' . ($enr->student->email ?? '')) }}">
                                <td>
                                    <div class="student-cell">
                                        <div class="student-avatar-letter">
                                            {{ mb_substr($stName, 0, 1) }}
                                        </div>
                                        <div>
                                            <strong class="st-name">{{ $stName }}</strong>
                                            <div class="st-email">{{ $enr->student->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="stage-tag-badge">{{ (app()->getLocale() === 'en' && !empty($enr->student->stage->name_en)) ? $enr->student->stage->name_en : ($enr->student->stage->name_ar ?? __('توجيهي')) }}</span>
                                </td>
                                <td>
                                    @if($isAll)
                                        <span class="badge-access-all">
                                            <i class="fa-solid fa-circle-check"></i> {{ __('كامل المنهج والاختبارات') }}
                                        </span>
                                    @else
                                        <span class="badge-access-custom">
                                            <i class="fa-solid fa-sliders"></i> {{ __('مخصص') }} ({{ $customContentsCount }} {{ __('دروس') }}، {{ $customExamsCount }} {{ __('اختبارات') }})
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-active-pill">{{ __('نشط ومفعل') }}</span>
                                </td>
                                <td>
                                    <span style="font-size: 0.8rem; color: #64748b;">
                                        {{ $enr->activated_at ? $enr->activated_at->format('Y-m-d') : ($enr->created_at ? $enr->created_at->format('Y-m-d') : '-') }}
                                    </span>
                                </td>
                                <td style="text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
                                    <button type="button" class="btn-manage-access" onclick="openStudentPermissionsModal({{ $enr->id }})">
                                        <i class="fa-solid fa-list-check"></i>
                                        <span>{{ __('تحديد الدروس والاختبارات المسموحة') }}</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-user-slash fa-3x mb-3" style="opacity: 0.3;"></i>
                                    <h5>{{ __('لا يوجد طلاب مشتركون في هذه المادة بعد') }}</h5>
                                    <p style="font-size: 0.85rem;">{{ __('بإمكانك تفعيل اشتراك أي طالب يدوياً عبر زر "تفعيل طالب سريعاً" بالأعلى.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($enrollments->hasPages())
                <div class="table-pagination-footer">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- التبويب الثاني: التحكم بحسب العنصر مع اختيار صح [✓] بجانب كل طالب -->
    <!-- ============================================================== -->
    <div id="view_items" class="tab-view-section" style="display: none;">
        <div class="item-selector-panel">
            <div class="selector-header">
                <h3><i class="fa-solid fa-sliders text-primary"></i> {{ __('اختر الدرس أو الاختبار لتحديد الطلاب المتاح لهم:') }}</h3>
                <p>{{ __('اختر الفيديو أو الملف أو الاختبار، وستظهر لك قائمة الطلاب لتحديد من يحق له مشاهدته بوضع إشارة صح [✓] بجانب اسمه.') }}</p>
            </div>

            <div class="selector-grid">
                <div>
                    <label class="select-label">{{ __('نوع العنصر الأكاديمي:') }}</label>
                    <select id="itemTypeSelect" class="form-select-modern" onchange="populateItemDropdown()">
                        <option value="content">{{ __('فيديو أو ملف / دوسية PDF') }} ({{ $totalContentsCount }})</option>
                        <option value="exam">{{ __('اختبار إلكتروني وزاري') }} ({{ $totalExamsCount }})</option>
                    </select>
                </div>

                <div>
                    <label class="select-label">{{ __('اختر العنصر المحدد:') }}</label>
                    <select id="itemIdSelect" class="form-select-modern" onchange="loadItemStudents()">
                        <!-- يتم ملؤها ديناميكياً بواسطة JavaScript -->
                    </select>
                </div>
            </div>
        </div>

        <!-- ساحة كروت الطلاب مع الـ Checkboxes [✓] -->
        <div id="itemStudentsContainer" class="item-students-card" style="display: none;">
            <div class="item-students-header">
                <div>
                    <span class="badge-item-name" id="selectedItemBadge">{{ __('فيديو تعليمي') }}</span>
                    <h3 id="selectedItemTitleText" style="margin: 6px 0 2px; font-size: 1.25rem; font-weight: 800; color: #0f172a;">{{ __('اسم الدرس') }}</h3>
                    <p style="margin: 0; font-size: 0.82rem; color: #64748b;">{{ __('ضع علامة صح [✓] بجانب اسم الطالب ليظهر له هذا المحتوى في حسابه فوراً.') }}</p>
                </div>

                <div class="bulk-actions-lane">
                    <button type="button" class="btn-bulk check-all" onclick="bulkToggleCurrentItem(true)">
                        <i class="fa-solid fa-check-double"></i> {{ __('إتاحة لجميع الطلاب (تحديد الكل)') }}
                    </button>
                    <button type="button" class="btn-bulk uncheck-all" onclick="bulkToggleCurrentItem(false)">
                        <i class="fa-solid fa-ban"></i> {{ __('حجب عن جميع الطلاب (إلغاء التحديد)') }}
                    </button>
                </div>
            </div>

            <div id="itemStudentsList" class="students-checkbox-grid">
                <!-- قائمة الطلاب مع خانات الاختيار تحقن هنا -->
            </div>
        </div>
    </div>

</div>

<!-- ============================================================== -->
<!-- Modal 1: نافذة تحديد الدروس والاختبارات المسموحة لطالب محدد -->
<!-- ============================================================== -->
<div id="studentPermissionsModal" class="modal-backdrop-overlay" style="display: none;">
    <div class="modal-card-container">
        <div class="modal-card-header">
            <div>
                <span style="background: #eff6ff; color: #1d4ed8; padding: 2px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; border: 1px solid #bfdbfe;">
                    {{ __('صلاحيات الطالب') }}
                </span>
                <h3 id="modalStudentName" style="margin: 6px 0 0; font-size: 1.2rem; font-weight: 800; color: #0f172a;">{{ __('اسم الطالب') }}</h3>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeStudentPermissionsModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="modal-card-body">
            <!-- خيار نمط الصلاحية -->
            <div class="access-mode-selector-box">
                <label class="mode-radio-card" onclick="toggleModalMode('all')">
                    <input type="radio" name="modal_access_mode" value="all" id="mode_all_radio">
                    <div>
                        <strong>🟢 {{ __('إتاحة كامل المنهج والاختبارات') }}</strong>
                        <small>{{ __('يستطيع الطالب الوصول لجميع الفيديوهات والملفات والاختبارات في المادة بلا استثناء.') }}</small>
                    </div>
                </label>

                <label class="mode-radio-card" onclick="toggleModalMode('custom')">
                    <input type="radio" name="modal_access_mode" value="custom" id="mode_custom_radio">
                    <div>
                        <strong>🟡 {{ __('باقة مخصصة من الدروس والاختبارات') }}</strong>
                        <small>{{ __('اختر الدروس والاختبارات المحددة التي يحق للطالب فتحها من القائمة بالأسفل عبر خانات [✓].') }}</small>
                    </div>
                </label>
            </div>

            <!-- قائمة الدروس والاختبارات مع الـ Checkboxes -->
            <div id="customCheckboxesSection" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin: 15px 0 10px;">
                    <h4 style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin: 0;">
                        <i class="fa-solid fa-film text-primary"></i> {{ __('الدروس والفيديوهات والملفات:') }}
                    </h4>
                    <button type="button" class="btn-text-action" onclick="checkAllContents(true)">{{ __('تحديد الكل') }}</button>
                </div>
                <div id="modalContentsList" class="modal-items-scroll"></div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0 10px;">
                    <h4 style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin: 0;">
                        <i class="fa-solid fa-file-pen text-warning"></i> {{ __('الاختبارات الإلكترونية:') }}
                    </h4>
                    <button type="button" class="btn-text-action" onclick="checkAllExams(true)">{{ __('تحديد الكل') }}</button>
                </div>
                <div id="modalExamsList" class="modal-items-scroll"></div>
            </div>
        </div>

        <div class="modal-card-footer">
            <button type="button" class="btn-secondary" onclick="closeStudentPermissionsModal()">{{ __('إلغاء') }}</button>
            <button type="button" class="btn-primary-save" id="btnSaveStudentAccess" onclick="saveStudentAccess()">
                <span>{{ __('حفظ التعديلات') }}</span>
                <i class="fa-solid fa-check"></i>
            </button>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- Modal 2: نافذة تفعيل اشتراك طالب سريعاً -->
<!-- ============================================================== -->
<div id="quickEnrollModal" class="modal-backdrop-overlay" style="display: none;">
    <div class="modal-card-container" style="max-width: 520px;">
        <div class="modal-card-header">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a;">
                <i class="fa-solid fa-user-plus text-primary"></i> {{ __('تفعيل طالب سريعاً') }}
            </h3>
            <button type="button" class="btn-close-modal" onclick="closeQuickEnrollModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body" style="padding: 20px 24px;">
            <div style="margin-bottom: 15px;">
                <label class="select-label">{{ __('المادة المراد تفعيلها:') }}</label>
                <input type="text" value="{{ (app()->getLocale() === 'en' && !empty($selectedSubject->name_en)) ? $selectedSubject->name_en : __($selectedSubject->name_ar ?? $selectedSubject->name) }}" class="form-select-modern" disabled>
                <input type="hidden" id="quickSubjectId" value="{{ $selectedSubject->id }}">
            </div>
            <div style="margin-bottom: 15px;">
                <label class="select-label">{{ __('رقم الهوية الفلسطينية (9 أرقام) أو البريد الإلكتروني أو الهاتف:') }}</label>
                <input type="text" id="quickStudentIdentifier" class="form-select-modern" placeholder="{{ __('مثال: 405123456 أو student@email.com') }}">
            </div>
            <div style="margin-bottom: 15px;">
                <label class="select-label">{{ __('نوع الصلاحية الأولية:') }}</label>
                <select id="quickAccessMode" class="form-select-modern">
                    <option value="all">🟢 {{ __('كامل المنهج والاختبارات مباشرة') }}</option>
                    <option value="custom">🟡 {{ __('باقة مخصصة (سأحدد الدروس لاحقاً)') }}</option>
                </select>
            </div>
        </div>
        <div class="modal-card-footer">
            <button type="button" class="btn-secondary" onclick="closeQuickEnrollModal()">{{ __('إلغاء') }}</button>
            <button type="button" class="btn-primary-save" id="btnConfirmQuickEnroll" onclick="confirmQuickEnroll()">
                <span>{{ __('تفعيل الحساب فوراً') }}</span>
            </button>
        </div>
    </div>
</div>

<style>
.access-page-wrapper {
    max-width: 1350px;
    margin: 1rem auto 3rem;
    padding: 0 1rem;
}

/* كرت الهيدر الأكاديمي الفاتح */
.access-hero-card {
    background: #ffffff;
    border: 1px solid var(--ed-border, #e2e8f0);
    border-radius: 16px;
    padding: 22px 28px;
    color: var(--ed-text-main, #0f172a);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 24px;
    box-shadow: var(--ed-shadow-card, 0 1px 3px rgba(0,0,0,0.05));
}

.hero-badge {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
}

.hero-headline {
    font-size: 1.4rem;
    font-weight: 800;
    margin: 0 0 6px;
    color: #0f172a;
}

.hero-desc {
    font-size: 0.86rem;
    color: #64748b;
    margin: 0;
    max-width: 650px;
    line-height: 1.5;
}

.hero-quick-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-hero-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all 0.2s ease;
}

.exam-action { 
    background: #fffbeb; 
    color: #b45309; 
    border-color: #fde68a; 
}
.exam-action:hover { 
    background: #fef3c7; 
    color: #92400e; 
    transform: translateY(-1px); 
}

.content-action { 
    background: #eff6ff; 
    color: #1d4ed8; 
    border-color: #bfdbfe; 
}
.content-action:hover { 
    background: #dbeafe; 
    color: #1e40af; 
    transform: translateY(-1px); 
}

.enroll-action { 
    background: #ecfdf5; 
    color: #047857; 
    border-color: #a7f3d0; 
}
.enroll-action:hover { 
    background: #d1fae5; 
    color: #065f46; 
    transform: translateY(-1px); 
}

/* Subjects Filter */
.subjects-filter-lane {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #ffffff;
    padding: 12px 20px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    margin-bottom: 20px;
}

.filter-label {
    font-size: 0.85rem;
    font-weight: 800;
    color: #475569;
    white-space: nowrap;
}

.subjects-scroll {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
}

.sub-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    border-radius: 50px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 700;
    white-space: nowrap;
    transition: 0.2s;
}

.sub-pill:hover {
    border-color: #bfdbfe;
    color: #1d4ed8;
}

.sub-pill.active {
    background: #1d4ed8;
    color: white;
    border-color: #1d4ed8;
}

.sub-stage-tag {
    font-size: 0.7rem;
    background: rgba(0,0,0,0.06);
    padding: 1px 6px;
    border-radius: 10px;
}

.sub-pill.active .sub-stage-tag {
    background: rgba(255,255,255,0.25);
    color: white;
}

/* Control Tabs */
.control-tabs-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.tab-btn {
    flex: 1;
    min-width: 260px;
    padding: 12px 18px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: 0.2s;
}

.tab-btn:hover {
    border-color: #bfdbfe;
    color: #1d4ed8;
}

.tab-btn.active {
    background: #1d4ed8;
    border-color: #1d4ed8;
    color: white;
    box-shadow: 0 4px 12px rgba(29, 78, 216, 0.2);
}

/* Tables & Cards */
.table-outer-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: var(--ed-shadow-card, 0 1px 3px rgba(0,0,0,0.05));
}

.table-header-row {
    padding: 18px 22px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.btn-print-roster {
    background: #ffffff; 
    border: 1px solid #cbd5e1; 
    padding: 8px 14px; 
    border-radius: 8px; 
    font-weight: 700; 
    font-size: 0.82rem; 
    cursor: pointer; 
    display: inline-flex; 
    align-items: center; 
    gap: 6px; 
    color: #334155;
    transition: 0.2s;
}
.btn-print-roster:hover {
    background: #f1f5f9;
}

.table-search-box {
    position: relative;
    width: 260px;
}

.table-search-box i {
    position: absolute;
    inset-inline-start: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}

.table-search-box input {
    width: 100%;
    padding: 8px 14px 8px 34px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    outline: none;
    font-size: 0.84rem;
}

[dir="rtl"] .table-search-box input {
    padding: 8px 34px 8px 14px;
}

.access-data-table {
    width: 100%;
    border-collapse: collapse;
}

.access-data-table th {
    background: #f8fafc !important;
    padding: 12px 18px;
    font-size: 0.82rem;
    font-weight: 800;
    color: #0f172a !important;
    border-bottom: 2px solid #e2e8f0 !important;
    letter-spacing: 0.3px;
    white-space: nowrap;
}

.access-data-table td {
    padding: 12px 18px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.student-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.student-avatar-letter {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #eff6ff;
    color: #1d4ed8;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    border: 1px solid #bfdbfe;
    flex-shrink: 0;
}

.st-name {
    font-size: 0.88rem;
    color: #0f172a;
    display: block;
}

.st-email {
    font-size: 0.75rem;
    color: #64748b;
    direction: ltr;
    text-align: start;
}

.stage-tag-badge {
    background: #f1f5f9;
    color: #475569;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.76rem;
    font-weight: 600;
    border: 1px solid #e2e8f0;
}

.badge-access-all {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.76rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.badge-access-custom {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.76rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.status-active-pill {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.74rem;
    font-weight: 700;
}

.btn-manage-access {
    background: #ffffff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: 0.2s;
}

.btn-manage-access:hover {
    background: #eff6ff;
    border-color: #1d4ed8;
}

/* Tab 2: Item Centric */
.item-selector-panel {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 20px;
    margin-bottom: 20px;
}

.selector-header h3 {
    margin: 0 0 4px;
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
}

.selector-header p {
    margin: 0 0 16px;
    font-size: 0.84rem;
    color: #64748b;
}

.selector-grid {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 16px;
}

@media (max-width: 768px) {
    .selector-grid {
        grid-template-columns: 1fr;
    }
}

.select-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
}

.form-select-modern {
    width: 100%;
    padding: 9px 12px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    font-size: 0.88rem;
    font-weight: 600;
    color: #0f172a;
    outline: none;
}

.form-select-modern:focus {
    border-color: #1d4ed8;
    box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
}

.item-students-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 20px;
}

.item-students-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 16px;
}

.badge-item-name {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 2px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 800;
}

.bulk-actions-lane {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-bulk {
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    border: 1px solid transparent;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: 0.15s;
}

.btn-bulk.check-all {
    background: #eff6ff;
    color: #1d4ed8;
    border-color: #bfdbfe;
}
.btn-bulk.check-all:hover {
    background: #dbeafe;
}

.btn-bulk.uncheck-all {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}
.btn-bulk.uncheck-all:hover {
    background: #fee2e2;
}

.students-checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 12px;
}

.student-checkbox-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: 0.15s;
}

.student-checkbox-card:hover {
    border-color: #cbd5e1;
    background: #ffffff;
}

.student-checkbox-card.checked {
    background: #eff6ff;
    border-color: #93c5fd;
}

.custom-checkbox-input {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #1d4ed8;
}

/* Modals */
.modal-backdrop-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(4px);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
}

.modal-card-container {
    background: #ffffff;
    border-radius: 14px;
    width: 100%;
    max-width: 620px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border: 1px solid #e2e8f0;
}

.modal-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-close-modal {
    background: none;
    border: none;
    color: #64748b;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 4px;
}
.btn-close-modal:hover {
    color: #0f172a;
}

.modal-card-body {
    padding: 20px;
    overflow-y: auto;
}

.access-mode-selector-box {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 16px;
}

.mode-radio-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 12px 14px;
    border-radius: 8px;
    cursor: pointer;
}

.mode-radio-card input {
    margin-top: 3px;
    accent-color: #1d4ed8;
}

.mode-radio-card strong {
    display: block;
    font-size: 0.88rem;
    color: #0f172a;
}

.mode-radio-card small {
    display: block;
    font-size: 0.78rem;
    color: #64748b;
    margin-top: 2px;
}

.modal-items-scroll {
    max-height: 180px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.item-check-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 0.82rem;
    cursor: pointer;
    margin: 0;
}

.item-check-row input {
    accent-color: #1d4ed8;
}

.modal-card-footer {
    padding: 14px 20px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.btn-secondary {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.84rem;
    cursor: pointer;
}

.btn-primary-save {
    background: #1d4ed8;
    color: white;
    border: none;
    padding: 8px 18px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.84rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-primary-save:hover {
    background: #1e40af;
}

.btn-text-action {
    background: none;
    border: none;
    color: #1d4ed8;
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
}
</style>

<script>
    const accessI18n = {
        noItemsAdded: @json(__('لا توجد عناصر مضافة بعد')),
        loadingStudents: @json(__('جاري تحميل قائمة الطلاب...')),
        videoOrPdf: @json(__('فيديو أو ملف PDF')),
        ministerialExam: @json(__('اختبار وزاري')),
        noStudentsEnrolled: @json(__('لا يوجد طلاب مسجلون في هذه المادة بعد.')),
        errorLoadingStudents: @json(__('حدث خطأ أثناء جلب الطلاب.')),
        accessUpdated: @json(__('تم تحديث الإتاحة بنجاح')),
        errorUpdatingAccess: @json(__('حدث خطأ أثناء تحديث الصلاحية.')),
        noLessons: @json(__('لا توجد دروس في المادة')),
        noExams: @json(__('لا توجد اختبارات مضافة في المادة')),
        saving: @json(__('جاري الحفظ...')),
        saveChanges: @json(__('حفظ التعديلات')),
        errorSaving: @json(__('حدث خطأ أثناء الحفظ.')),
        enterIdOrEmail: @json(__('يرجى إدخال رقم الهوية أو البريد الإلكتروني للطالب.')),
        activating: @json(__('جاري التفعيل...')),
        activateImmediately: @json(__('تفعيل الحساب فوراً')),
        ok: @json(__('حسناً')),
        studentNotFound: @json(__('لم يتم العثور على الطالب.'))
    };

    const contentsData = @json($contents);
    const examsData = @json($exams);
    let activeEnrollmentId = null;

    // تبديل التبويبات
    function switchTab(tab) {
        document.getElementById('view_students').style.display = (tab === 'students') ? 'block' : 'none';
        document.getElementById('view_items').style.display = (tab === 'items') ? 'block' : 'none';
        
        document.getElementById('tabBtnStudents').classList.toggle('active', tab === 'students');
        document.getElementById('tabBtnItems').classList.toggle('active', tab === 'items');

        if (tab === 'items') {
            populateItemDropdown();
        }
    }

    // تعبئة قائمة العناصر (فيديوهات / اختبارات)
    function populateItemDropdown() {
        const type = document.getElementById('itemTypeSelect').value;
        const select = document.getElementById('itemIdSelect');
        select.innerHTML = '';

        const list = (type === 'content') ? contentsData : examsData;
        if (list.length === 0) {
            select.innerHTML = `<option value="">${accessI18n.noItemsAdded}</option>`;
            document.getElementById('itemStudentsContainer').style.display = 'none';
            return;
        }

        list.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.title + (item.channel_name ? ` (${item.channel_name})` : '');
            select.appendChild(opt);
        });

        loadItemStudents();
    }

    // جلب قائمة الطلاب وعلامات الصح [✓] للعنصر المحدد
    function loadItemStudents() {
        const type = document.getElementById('itemTypeSelect').value;
        const itemId = document.getElementById('itemIdSelect').value;

        if (!itemId) return;

        const container = document.getElementById('itemStudentsContainer');
        const listDiv = document.getElementById('itemStudentsList');
        listDiv.innerHTML = `<div class="text-center py-4 w-100 text-muted"><i class="fa-solid fa-spinner fa-spin fa-2x"></i><p class="mt-2">${accessI18n.loadingStudents}</p></div>`;
        container.style.display = 'block';

        axios.get(`/teacher/access/item-students?item_type=${type}&item_id=${itemId}`)
            .then(res => {
                const data = res.data;
                document.getElementById('selectedItemTitleText').innerText = data.item_title;
                document.getElementById('selectedItemBadge').innerText = (type === 'content' ? accessI18n.videoOrPdf : accessI18n.ministerialExam);

                if (!data.students || data.students.length === 0) {
                    listDiv.innerHTML = `<p class="text-muted text-center py-4 w-100">${accessI18n.noStudentsEnrolled}</p>`;
                    return;
                }

                let html = '';
                data.students.forEach(st => {
                    const isChecked = st.is_allowed ? 'checked' : '';
                    html += `
                        <label class="student-checkbox-card ${isChecked}" id="st_card_${st.enrollment_id}">
                            <div>
                                <strong style="display: block; font-size: 0.9rem; color: #0f172a;">${st.student_name}</strong>
                                <small style="color: #64748b; font-size: 0.75rem;">${st.stage_name} • ${st.student_email}</small>
                            </div>
                            <input type="checkbox" class="custom-checkbox-input" 
                                   ${isChecked} 
                                   onchange="toggleSingleStudent(${st.enrollment_id}, '${type}', ${itemId}, this.checked)">
                        </label>
                    `;
                });
                listDiv.innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                listDiv.innerHTML = `<p class="text-danger text-center py-4">${accessI18n.errorLoadingStudents}</p>`;
            });
    }

    // تبديل إتاحة عنصر لطالب واحد فورياً عند الضغط على [✓]
    function toggleSingleStudent(enrollmentId, itemType, itemId, isAllowed) {
        const card = document.getElementById(`st_card_${enrollmentId}`);
        if (card) {
            card.classList.toggle('checked', isAllowed);
        }

        axios.post('/teacher/access/toggle-item-student', {
            enrollment_id: enrollmentId,
            item_type: itemType,
            item_id: itemId,
            is_allowed: isAllowed ? 1 : 0,
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            Swal.fire({
                toast: true,
                position: 'top-start',
                icon: 'success',
                title: res.data.message || accessI18n.accessUpdated,
                showConfirmButton: false,
                timer: 1500
            });
        })
        .catch(err => {
            console.error(err);
            alert(accessI18n.errorUpdatingAccess);
            if (card) {
                const chk = card.querySelector('input');
                if (chk) chk.checked = !isAllowed;
            }
        });
    }

    // تحديد الكل أو إلغاء تحديد الكل للعنصر المحدد
    function bulkToggleCurrentItem(isAllowed) {
        const type = document.getElementById('itemTypeSelect').value;
        const itemId = document.getElementById('itemIdSelect').value;

        if (!itemId) return;

        axios.post('/teacher/access/bulk-toggle-item-students', {
            item_type: type,
            item_id: itemId,
            is_allowed: isAllowed ? 1 : 0,
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            Swal.fire({
                toast: true,
                position: 'top-start',
                icon: 'success',
                title: res.data.message,
                showConfirmButton: false,
                timer: 2000
            });
            loadItemStudents();
        });
    }

    // فلترة جدول الطلاب
    function filterStudentsTable() {
        const query = document.getElementById('studentSearchInput').value.toLowerCase().trim();
        document.querySelectorAll('.student-row').forEach(row => {
            const data = row.getAttribute('data-search') || '';
            row.style.display = data.includes(query) ? '' : 'none';
        });
    }

    // نافذة تحديد الدروس والاختبارات لطالب محدد
    function openStudentPermissionsModal(enrollmentId) {
        activeEnrollmentId = enrollmentId;
        const modal = document.getElementById('studentPermissionsModal');
        modal.style.display = 'flex';

        axios.get(`/teacher/access/${enrollmentId}/contents`)
            .then(res => {
                const d = res.data;
                document.getElementById('modalStudentName').innerText = `${d.enrollment.student_name} (${d.enrollment.subject_name})`;

                const isAll = (d.enrollment.access_mode === 'all');
                document.getElementById('mode_all_radio').checked = isAll;
                document.getElementById('mode_custom_radio').checked = !isAll;
                toggleModalMode(d.enrollment.access_mode);

                // بناء قائمة الدروس
                const cList = document.getElementById('modalContentsList');
                let cHtml = '';
                d.contents.forEach(c => {
                    const chk = c.is_unlocked ? 'checked' : '';
                    cHtml += `
                        <label class="item-check-row">
                            <span><i class="fa-solid fa-play text-primary me-1"></i> ${c.title}</span>
                            <input type="checkbox" class="modal-content-check" value="${c.id}" ${chk}>
                        </label>
                    `;
                });
                cList.innerHTML = cHtml || `<p class="text-muted p-2">${accessI18n.noLessons}</p>`;

                // بناء قائمة الاختبارات
                const eList = document.getElementById('modalExamsList');
                let eHtml = '';
                d.exams.forEach(e => {
                    const chk = e.is_unlocked ? 'checked' : '';
                    eHtml += `
                        <label class="item-check-row">
                            <span><i class="fa-solid fa-file-pen text-warning me-1"></i> ${e.title}</span>
                            <input type="checkbox" class="modal-exam-check" value="${e.id}" ${chk}>
                        </label>
                    `;
                });
                eList.innerHTML = eHtml || `<p class="text-muted p-2">${accessI18n.noExams}</p>`;
            });
    }

    function toggleModalMode(mode) {
        document.getElementById('customCheckboxesSection').style.display = (mode === 'custom') ? 'block' : 'none';
    }

    function closeStudentPermissionsModal() {
        document.getElementById('studentPermissionsModal').style.display = 'none';
    }

    function checkAllContents(val) {
        document.querySelectorAll('.modal-content-check').forEach(chk => chk.checked = val);
    }

    function checkAllExams(val) {
        document.querySelectorAll('.modal-exam-check').forEach(chk => chk.checked = val);
    }

    function saveStudentAccess() {
        if (!activeEnrollmentId) return;

        const mode = document.querySelector('input[name="modal_access_mode"]:checked').value;
        const allowedContents = [];
        const allowedExams = [];

        if (mode === 'custom') {
            document.querySelectorAll('.modal-content-check:checked').forEach(chk => allowedContents.push(chk.value));
            document.querySelectorAll('.modal-exam-check:checked').forEach(chk => allowedExams.push(chk.value));
        }

        const btn = document.getElementById('btnSaveStudentAccess');
        btn.disabled = true;
        btn.innerHTML = accessI18n.saving;

        axios.post(`/teacher/access/${activeEnrollmentId}/update`, {
            access_mode: mode,
            allowed_contents: allowedContents,
            allowed_exams: allowedExams,
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: res.data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => location.reload());
        })
        .catch(err => {
            console.error(err);
            alert(accessI18n.errorSaving);
            btn.disabled = false;
            btn.innerHTML = accessI18n.saveChanges;
        });
    }

    // Modal تفعيل طالب سريعاً
    function openQuickEnrollModal() {
        document.getElementById('quickEnrollModal').style.display = 'flex';
    }
    function closeQuickEnrollModal() {
        document.getElementById('quickEnrollModal').style.display = 'none';
    }

    function confirmQuickEnroll() {
        const iden = document.getElementById('quickStudentIdentifier').value.trim();
        const subId = document.getElementById('quickSubjectId').value;
        const mode = document.getElementById('quickAccessMode').value;

        if (!iden) {
            alert(accessI18n.enterIdOrEmail);
            return;
        }

        const btn = document.getElementById('btnConfirmQuickEnroll');
        btn.disabled = true;
        btn.innerText = accessI18n.activating;

        axios.post('/teacher/access/quick-enroll', {
            subject_id: subId,
            student_identifier: iden,
            access_mode: mode,
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: res.data.message,
                confirmButtonText: accessI18n.ok
            }).then(() => location.reload());
        })
        .catch(err => {
            alert(err.response?.data?.message || accessI18n.studentNotFound);
            btn.disabled = false;
            btn.innerText = accessI18n.activateImmediately;
        });
    }

    // التعرف التلقائي على العناصر عند التوجيه المباشر من صفحة الاختبارات أو الدروس
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const itemType = urlParams.get('type');
        const itemId = urlParams.get('id');

        if (itemType && (itemType === 'content' || itemType === 'exam')) {
            const typeSelect = document.getElementById('itemTypeSelect');
            if (typeSelect) {
                typeSelect.value = itemType;
                switchTab('items');
                if (itemId) {
                    const itemSelect = document.getElementById('itemIdSelect');
                    if (itemSelect) {
                        itemSelect.value = itemId;
                        loadItemStudents();
                    }
                }
            }
        }
    });
</script>
@endsection
