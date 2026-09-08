@extends('layouts.app')

@section('title', 'تحكم صلاحيات وظهور المحتوى والاختبارات للطلاب')

@section('content')
<div class="access-page-wrapper" dir="rtl">

    <!-- كرت الهيدر والأزرار السريعة للمعلم -->
    <div class="access-hero-card">
        <div class="hero-text-block">
            <span class="hero-badge">
                <i class="fa-solid fa-shield-halved"></i> مساحة تحكم المعلم الأكاديمية
            </span>
            <h1 class="hero-headline">التحكم بظهور الفيديوهات والملفات والاختبارات للطلاب 🎯</h1>
            <p class="hero-desc">
                حدد ما يظهر لكل طالب عبر خانات الاختيار [✓]: المنهج كاملاً أو جزئيات واختبارات محددة وفق اشتراكه.
            </p>
        </div>

        <div class="hero-quick-actions">
            <a href="{{ route('teacher.exams.create') }}" class="btn-hero-action exam-action">
                <i class="fa-solid fa-plus-circle"></i>
                <span>بناء اختبار جديد</span>
            </a>
            <a href="{{ route('teacher.educational_contents.create') }}" class="btn-hero-action content-action">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>رفع فيديو / ملخص PDF</span>
            </a>
            <button type="button" onclick="openQuickEnrollModal()" class="btn-hero-action enroll-action">
                <i class="fa-solid fa-user-plus"></i>
                <span>تفعيل طالب سريعاً</span>
            </button>
        </div>
    </div>

    <!-- شريط اختيار المادة الأكاديمية -->
    @if($subjects->count() > 1)
        <div class="subjects-filter-lane">
            <span class="filter-label"><i class="fa-solid fa-book-bookmark"></i> اختر المادة:</span>
            <div class="subjects-scroll">
                @foreach($subjects as $sub)
                    <a href="{{ route('teacher.access.index', ['subject_id' => $sub->id]) }}" 
                       class="sub-pill {{ $selectedSubject && $selectedSubject->id == $sub->id ? 'active' : '' }}">
                        <span>{{ $sub->name_ar ?? $sub->name }}</span>
                        <span class="sub-stage-tag">{{ $sub->stage->name_ar ?? 'توجيهي' }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- أزرار التبديل بين طريقتي التحكم -->
    <div class="control-tabs-bar">
        <button type="button" class="tab-btn active" id="tabBtnStudents" onclick="switchTab('students')">
            <i class="fa-solid fa-users"></i>
            <span>التحكم بحسب الطالب (عرض صلاحيات كل طالب)</span>
        </button>
        <button type="button" class="tab-btn" id="tabBtnItems" onclick="switchTab('items')">
            <i class="fa-solid fa-list-check"></i>
            <span>التحكم بحسب الدرس أو الاختبار (اختيار صح بجانب اسم الطالب [✓])</span>
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
                        الطلاب المشتركون في ({{ $selectedSubject->name_ar ?? $selectedSubject->name ?? 'المادة' }})
                    </h3>
                    <p style="font-size: 0.82rem; color: #64748b; margin: 0;">
                        إجمالي الطلاب: <strong>{{ $enrollments->total() }}</strong> • إجمالي الدروس: <strong>{{ $totalContentsCount }}</strong> • إجمالي الاختبارات: <strong>{{ $totalExamsCount }}</strong>
                    </p>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="button" onclick="window.print()" style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 9px 14px; border-radius: 12px; font-weight: 700; font-size: 0.82rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; color: #334155;">
                        <i class="fa-solid fa-print"></i> طباعة الكشف 🖨️
                    </button>
                    <div class="table-search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="studentSearchInput" placeholder="بحث باسم الطالب أو البريد..." onkeyup="filterStudentsTable()">
                    </div>
                </div>
            </div>

            <div class="table-responsive-wrapper">
                <table class="access-data-table" id="studentsTable">
                    <thead>
                        <tr>
                            <th>الطالب</th>
                            <th>الفرع الأكاديمي</th>
                            <th>نوع الصلاحية الحالية</th>
                            <th>حالة الحساب</th>
                            <th>تاريخ التفعيل</th>
                            <th style="text-align: left;">إجراءات الصلاحية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enr)
                            @php
                                $stName = $enr->student->name_ar ?? $enr->student->name ?? 'طالب توجيهي';
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
                                    <span class="stage-tag-badge">{{ $enr->student->stage->name_ar ?? 'توجيهي' }}</span>
                                </td>
                                <td>
                                    @if($isAll)
                                        <span class="badge-access-all">
                                            <i class="fa-solid fa-circle-check"></i> كامل المنهج والاختبارات
                                        </span>
                                    @else
                                        <span class="badge-access-custom">
                                            <i class="fa-solid fa-sliders"></i> مخصص ({{ $customContentsCount }} دروس، {{ $customExamsCount }} اختبارات)
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-active-pill">نشط ومفعل</span>
                                </td>
                                <td>
                                    <span style="font-size: 0.8rem; color: #64748b;">
                                        {{ $enr->activated_at ? $enr->activated_at->format('Y-m-d') : ($enr->created_at ? $enr->created_at->format('Y-m-d') : '-') }}
                                    </span>
                                </td>
                                <td style="text-align: left;">
                                    <button type="button" class="btn-manage-access" onclick="openStudentPermissionsModal({{ $enr->id }})">
                                        <i class="fa-solid fa-list-check"></i>
                                        <span>تحديد الدروس والاختبارات المسموحة</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-user-slash fa-3x mb-3" style="opacity: 0.3;"></i>
                                    <h5>لا يوجد طلاب مشتركون في هذه المادة بعد</h5>
                                    <p style="font-size: 0.85rem;">بإمكانك تفعيل اشتراك أي طالب يدوياً عبر زر "تفعيل طالب سريعاً" بالأعلى.</p>
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
                <h3><i class="fa-solid fa-sliders text-primary"></i> اختر الدرس أو الاختبار لتحديد الطلاب المتاح لهم:</h3>
                <p>اختر الفيديو أو الملف أو الاختبار، وستظهر لك قائمة الطلاب لتحديد من يحق له مشاهدته بوضع إشارة صح [✓] بجانب اسمه.</p>
            </div>

            <div class="selector-grid">
                <div>
                    <label class="select-label">نوع العنصر الأكاديمي:</label>
                    <select id="itemTypeSelect" class="form-select-modern" onchange="populateItemDropdown()">
                        <option value="content">فيديو أو ملف / دوسية PDF ({{ $totalContentsCount }})</option>
                        <option value="exam">اختبار إلكتروني وزاري ({{ $totalExamsCount }})</option>
                    </select>
                </div>

                <div>
                    <label class="select-label">اختر العنصر المحدد:</label>
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
                    <span class="badge-item-name" id="selectedItemBadge">فيديو تعليمي</span>
                    <h3 id="selectedItemTitleText" style="margin: 6px 0 2px; font-size: 1.25rem; font-weight: 800; color: #0f172a;">اسم الدرس</h3>
                    <p style="margin: 0; font-size: 0.82rem; color: #64748b;">ضع علامة صح [✓] بجانب اسم الطالب ليظهر له هذا المحتوى في حسابه فوراً.</p>
                </div>

                <div class="bulk-actions-lane">
                    <button type="button" class="btn-bulk check-all" onclick="bulkToggleCurrentItem(true)">
                        <i class="fa-solid fa-check-double"></i> إتاحة لجميع الطلاب (تحديد الكل)
                    </button>
                    <button type="button" class="btn-bulk uncheck-all" onclick="bulkToggleCurrentItem(false)">
                        <i class="fa-solid fa-ban"></i> حجب عن جميع الطلاب (إلغاء التحديد)
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
                <span style="background: #eff6ff; color: #0284c7; padding: 2px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 800;">
                    صلاحيات الطالب
                </span>
                <h3 id="modalStudentName" style="margin: 6px 0 0; font-size: 1.2rem; font-weight: 800; color: #0f172a;">اسم الطالب</h3>
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
                        <strong>🟢 إتاحة كامل المنهج والاختبارات</strong>
                        <small>يستطيع الطالب الوصول لجميع الفيديوهات والملفات والاختبارات في المادة بلا استثناء.</small>
                    </div>
                </label>

                <label class="mode-radio-card" onclick="toggleModalMode('custom')">
                    <input type="radio" name="modal_access_mode" value="custom" id="mode_custom_radio">
                    <div>
                        <strong>🟡 باقة مخصصة من الدروس والاختبارات</strong>
                        <small>اختر الدروس والاختبارات المحددة التي يحق للطالب فتحها من القائمة بالأسفل عبر خانات [✓].</small>
                    </div>
                </label>
            </div>

            <!-- قائمة الدروس والاختبارات مع الـ Checkboxes -->
            <div id="customCheckboxesSection" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin: 15px 0 10px;">
                    <h4 style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin: 0;">
                        <i class="fa-solid fa-film text-primary"></i> الدروس والفيديوهات والملفات:
                    </h4>
                    <button type="button" class="btn-text-action" onclick="checkAllContents(true)">تحديد الكل</button>
                </div>
                <div id="modalContentsList" class="modal-items-scroll"></div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0 10px;">
                    <h4 style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin: 0;">
                        <i class="fa-solid fa-file-pen text-warning"></i> الاختبارات الإلكترونية:
                    </h4>
                    <button type="button" class="btn-text-action" onclick="checkAllExams(true)">تحديد الكل</button>
                </div>
                <div id="modalExamsList" class="modal-items-scroll"></div>
            </div>
        </div>

        <div class="modal-card-footer">
            <button type="button" class="btn-secondary" onclick="closeStudentPermissionsModal()">إلغاء</button>
            <button type="button" class="btn-primary-save" id="btnSaveStudentAccess" onclick="saveStudentAccess()">
                <span>حفظ التعديلات</span>
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
                <i class="fa-solid fa-user-plus text-primary"></i> تفعيل اشتراك طالب سريعاً
            </h3>
            <button type="button" class="btn-close-modal" onclick="closeQuickEnrollModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body" style="padding: 20px 24px;">
            <div style="margin-bottom: 15px;">
                <label class="select-label">المادة المراد تفعيلها:</label>
                <input type="text" value="{{ $selectedSubject->name_ar ?? $selectedSubject->name }}" class="form-select-modern" disabled>
                <input type="hidden" id="quickSubjectId" value="{{ $selectedSubject->id }}">
            </div>
            <div style="margin-bottom: 15px;">
                <label class="select-label">رقم الهوية الفلسطينية (9 أرقام) أو البريد الإلكتروني أو الهاتف:</label>
                <input type="text" id="quickStudentIdentifier" class="form-select-modern" placeholder="مثال: 405123456 أو student@email.com">
            </div>
            <div style="margin-bottom: 15px;">
                <label class="select-label">نوع الصلاحية الأولية:</label>
                <select id="quickAccessMode" class="form-select-modern">
                    <option value="all">🟢 كامل المنهج والاختبارات مباشرة</option>
                    <option value="custom">🟡 باقة مخصصة (سأحدد الدروس لاحقاً)</option>
                </select>
            </div>
        </div>
        <div class="modal-card-footer">
            <button type="button" class="btn-secondary" onclick="closeQuickEnrollModal()">إلغاء</button>
            <button type="button" class="btn-primary-save" id="btnConfirmQuickEnroll" onclick="confirmQuickEnroll()">
                <span>تفعيل الحساب فوراً</span>
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

/* Hero Card */
.access-hero-card {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 24px;
    padding: 28px 34px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 24px;
    box-shadow: 0 16px 36px rgba(0,0,0,0.12);
}

.hero-badge {
    background: rgba(2, 132, 199, 0.25);
    color: #38bdf8;
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
    font-size: 1.55rem;
    font-weight: 900;
    margin: 0 0 6px;
    color: #f8fafc;
}

.hero-desc {
    font-size: 0.9rem;
    color: #94a3b8;
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
    padding: 11px 18px;
    border-radius: 12px;
    font-size: 0.86rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: 0.2s;
}

.exam-action { background: #d97706; color: white; }
.exam-action:hover { background: #b45309; color: white; transform: translateY(-2px); }

.content-action { background: #0284c7; color: white; }
.content-action:hover { background: #0369a1; color: white; transform: translateY(-2px); }

.enroll-action { background: #059669; color: white; }
.enroll-action:hover { background: #047857; color: white; transform: translateY(-2px); }

/* Subjects Filter */
.subjects-filter-lane {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #ffffff;
    padding: 12px 20px;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    margin-bottom: 20px;
}

body.dark-theme .subjects-filter-lane {
    background: #0f172a;
    border-color: #1e293b;
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
    padding: 8px 16px;
    border-radius: 50px;
    background: #f1f5f9;
    color: #334155;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 700;
    white-space: nowrap;
    transition: 0.2s;
}

.sub-pill.active {
    background: #0284c7;
    color: white;
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
    padding: 14px 20px;
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    font-size: 0.95rem;
    font-weight: 800;
    color: #475569;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: 0.2s;
}

body.dark-theme .tab-btn {
    background: #0f172a;
    border-color: #1e293b;
    color: #94a3b8;
}

.tab-btn.active {
    background: #0284c7;
    border-color: #0284c7;
    color: white;
    box-shadow: 0 6px 18px rgba(2, 132, 199, 0.25);
}

/* Tables & Cards */
.table-outer-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}

body.dark-theme .table-outer-card {
    background: #0f172a;
    border-color: #1e293b;
}

.table-header-row {
    padding: 20px 24px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

body.dark-theme .table-header-row {
    background: #1e293b;
    border-color: #334155;
}

.table-search-box {
    position: relative;
    width: 280px;
}

.table-search-box i {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.table-search-box input {
    width: 100%;
    padding: 9px 38px 9px 14px;
    border-radius: 12px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    outline: none;
    font-size: 0.85rem;
}

.access-data-table {
    width: 100%;
    border-collapse: collapse;
    text-align: right;
}

.access-data-table th {
    background: #f1f5f9;
    padding: 12px 20px;
    font-size: 0.82rem;
    font-weight: 800;
    color: #475569;
    border-bottom: 1px solid #e2e8f0;
}

body.dark-theme .access-data-table th {
    background: #1e293b;
    color: #94a3b8;
    border-color: #334155;
}

.access-data-table td {
    padding: 14px 20px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

body.dark-theme .access-data-table td {
    border-color: #1e293b;
}

.student-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.student-avatar-letter {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #e0f2fe;
    color: #0284c7;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
}

.st-name {
    font-size: 0.92rem;
    color: #0f172a;
    display: block;
}

body.dark-theme .st-name { color: #f8fafc; }

.st-email {
    font-size: 0.75rem;
    color: #64748b;
    direction: ltr;
    text-align: right;
}

.stage-tag-badge {
    background: #f1f5f9;
    color: #475569;
    padding: 3px 10px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 600;
}

.badge-access-all {
    background: #ecfdf5;
    color: #047857;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.badge-access-custom {
    background: #fffbeb;
    color: #b45309;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.status-active-pill {
    background: #eff6ff;
    color: #0284c7;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 700;
}

.btn-manage-access {
    background: #0284c7;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: 0.2s;
}

.btn-manage-access:hover {
    background: #0369a1;
    transform: translateY(-1px);
}

/* Tab 2: Item Centric */
.item-selector-panel {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    padding: 24px;
    margin-bottom: 20px;
}

body.dark-theme .item-selector-panel {
    background: #0f172a;
    border-color: #1e293b;
}

.selector-header h3 {
    margin: 0 0 4px;
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
}

body.dark-theme .selector-header h3 { color: #f8fafc; }

.selector-header p {
    margin: 0 0 16px;
    font-size: 0.85rem;
    color: #64748b;
}

.selector-grid {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 16px;
}

.select-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
}

body.dark-theme .select-label { color: #cbd5e1; }

.form-select-modern {
    width: 100%;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1.5px solid #cbd5e1;
    background: #f8fafc;
    font-size: 0.9rem;
    font-weight: 600;
    color: #0f172a;
    outline: none;
}

body.dark-theme .form-select-modern {
    background: #1e293b;
    border-color: #334155;
    color: #f8fafc;
}

.item-students-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    padding: 24px;
}

body.dark-theme .item-students-card {
    background: #0f172a;
    border-color: #1e293b;
}

.item-students-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 18px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

body.dark-theme .item-students-header { border-color: #1e293b; }

.badge-item-name {
    background: #e0f2fe;
    color: #0284c7;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 800;
}

.bulk-actions-lane {
    display: flex;
    gap: 10px;
}

.btn-bulk {
    border: none;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: 0.2s;
}

.btn-bulk.check-all { background: #ecfdf5; color: #047857; }
.btn-bulk.check-all:hover { background: #10b981; color: white; }

.btn-bulk.uncheck-all { background: #fef2f2; color: #b91c1c; }
.btn-bulk.uncheck-all:hover { background: #ef4444; color: white; }

.students-checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 12px;
}

.student-checkbox-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

body.dark-theme .student-checkbox-card {
    background: #1e293b;
    border-color: #334155;
}

.student-checkbox-card:hover {
    border-color: #0284c7;
    background: #ffffff;
}

body.dark-theme .student-checkbox-card:hover { background: #0f172a; }

.student-checkbox-card.checked {
    border-color: #10b981;
    background: #f0fdf4;
}

body.dark-theme .student-checkbox-card.checked {
    background: #064e3b30;
    border-color: #059669;
}

.custom-checkbox-input {
    width: 22px;
    height: 22px;
    accent-color: #10b981;
    cursor: pointer;
}

/* Modals */
.modal-backdrop-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-card-container {
    background: #ffffff;
    border-radius: 24px;
    width: 100%;
    max-width: 680px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
    animation: modalScale 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

body.dark-theme .modal-card-container {
    background: #0f172a;
    color: #f8fafc;
}

@keyframes modalScale {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.modal-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

body.dark-theme .modal-card-header { border-color: #1e293b; }

.btn-close-modal {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f1f5f9;
    border: none;
    color: #64748b;
    cursor: pointer;
}

.modal-card-body {
    padding: 20px 24px;
    overflow-y: auto;
    flex: 1;
}

.access-mode-selector-box {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 15px;
}

.mode-radio-card {
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px;
    display: flex;
    gap: 10px;
    cursor: pointer;
    transition: 0.2s;
}

body.dark-theme .mode-radio-card { border-color: #334155; }

.mode-radio-card:hover { border-color: #0284c7; }

.mode-radio-card input:checked + div strong { color: #0284c7; }

.modal-items-scroll {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 8px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

body.dark-theme .modal-items-scroll { border-color: #334155; }

.item-check-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: #f8fafc;
    border-radius: 8px;
    font-size: 0.85rem;
}

body.dark-theme .item-check-row { background: #1e293b; }

.modal-card-footer {
    padding: 16px 24px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

body.dark-theme .modal-card-footer { border-color: #1e293b; }

.btn-secondary {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    padding: 10px 18px;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
}

.btn-primary-save {
    background: #0284c7;
    border: none;
    color: white;
    padding: 10px 22px;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-text-action {
    background: none;
    border: none;
    color: #0284c7;
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
}
</style>

<script>
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
            select.innerHTML = '<option value="">لا توجد عناصر مضافة بعد</option>';
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
        listDiv.innerHTML = '<div class="text-center py-4 w-100 text-muted"><i class="fa-solid fa-spinner fa-spin fa-2x"></i><p class="mt-2">جاري تحميل قائمة الطلاب...</p></div>';
        container.style.display = 'block';

        axios.get(`/teacher/access/item-students?item_type=${type}&item_id=${itemId}`)
            .then(res => {
                const data = res.data;
                document.getElementById('selectedItemTitleText').innerText = data.item_title;
                document.getElementById('selectedItemBadge').innerText = (type === 'content' ? 'فيديو أو ملف PDF' : 'اختبار وزاري');

                if (!data.students || data.students.length === 0) {
                    listDiv.innerHTML = '<p class="text-muted text-center py-4 w-100">لا يوجد طلاب مسجلون في هذه المادة بعد.</p>';
                    return;
                }

                let html = '';
                data.students.forEach(st => {
                    const isChecked = st.is_allowed ? 'checked' : '';
                    html += `
                        <label class="student-checkbox-card ${isChecked}" id="st_card_${st.enrollment_id}">
                            <div>
                                <strong style="display: block; font-size: 0.92rem; color: #0f172a;">${st.student_name}</strong>
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
                listDiv.innerHTML = '<p class="text-danger text-center py-4">حدث خطأ أثناء جلب الطلاب.</p>';
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
                title: res.data.message || 'تم تحديث الإتاحة بنجاح',
                showConfirmButton: false,
                timer: 1500
            });
        })
        .catch(err => {
            console.error(err);
            alert('حدث خطأ أثناء تحديث الصلاحية.');
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
                cList.innerHTML = cHtml || '<p class="text-muted p-2">لا توجد دروس في المادة</p>';

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
                eList.innerHTML = eHtml || '<p class="text-muted p-2">لا توجد اختبارات مضافة في المادة</p>';
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
        btn.innerHTML = 'جاري الحفظ...';

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
            alert('حدث خطأ أثناء الحفظ.');
            btn.disabled = false;
            btn.innerHTML = 'حفظ التعديلات';
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
            alert('يرجى إدخال رقم الهوية أو البريد الإلكتروني للطالب.');
            return;
        }

        const btn = document.getElementById('btnConfirmQuickEnroll');
        btn.disabled = true;
        btn.innerText = 'جاري التفعيل...';

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
                confirmButtonText: 'حسناً'
            }).then(() => location.reload());
        })
        .catch(err => {
            alert(err.response?.data?.message || 'لم يتم العثور على الطالب.');
            btn.disabled = false;
            btn.innerText = 'تفعيل الحساب فوراً';
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
