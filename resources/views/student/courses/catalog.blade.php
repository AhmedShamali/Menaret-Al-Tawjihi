@extends('layouts.app')

@section('title', __('دليل المقررات والمناهج الدراسية') . ' | ' . config('app.name', 'منارة التوجيهي'))

@section('content')
<div class="ed-catalog-page">

    <!-- الترويسة الأكاديمية الرسمية -->
    <header class="ed-page-header">
        <div class="header-main-info">
            <div class="ed-flag-badge">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>{{ __('المنهاج الفلسطيني المعتمد للتوجيهي') }}</span>
            </div>
            <h1 class="ed-page-title">{{ __('دليل المقررات والمناهج الدراسية المعتمدة') }}</h1>
            <p class="ed-page-desc">
                {{ __('استعراض شامل وتفصيلي لمباحث الثانوية العامة (التوجيهي) لكافة الفروع الأكاديمية مع الشروحات المرئية، الملازم التعليمية، والاختبارات التقييمية المعتمدة.') }}
            </p>
        </div>
    </header>

    <!-- صندوق الإيضاح والتوجيه الأكاديمي -->
    <div class="ed-info-banner">
        <div class="info-banner-icon">
            <i class="fa-solid fa-circle-info"></i>
        </div>
        <div class="info-banner-content">
            <h3>{{ __('تنويه أكاديمي لطلبة الثانوية العامة وأولياء الأمور') }}</h3>
            <p>
                {{ __('يتم تفعيل وتثبيت باقة المواد الدراسية الخاصة بكل طالب تلقائياً فور إتمام إجراءات التسجيل الجديد في المنصة واعتماد الاشتراك الأكاديمي من قِبل الإدارة. هذه الصفحة مخصصة للاطلاع على تفاصيل ومفردات المنهاج ونخبة الأساتذة المشرفين.') }}
            </p>
        </div>
        <div class="info-banner-action">
            @if(Auth::guard('student')->check())
                <a href="{{ route('student.subjects.index') }}" class="btn-banner-action">
                    <i class="fa-solid fa-book-bookmark"></i>
                    <span>{{ __('الانتقال لموادي المقيدة') }}</span>
                </a>
            @else
                <a href="{{ route('students.create') }}" class="btn-banner-action">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>{{ __('تسجيل طالب جديد') }}</span>
                </a>
            @endif
        </div>
    </div>

    <!-- شريط تصفية الفروع الأكاديمية -->
    <div class="ed-stage-filter-bar">
        <div class="filter-label">
            <i class="fa-solid fa-layer-group"></i>
            <span>{{ __('تصفية المقررات حسب الفرع الأكاديمي:') }}</span>
        </div>
        <div class="filter-pills-list">
            <a href="{{ route('courses.catalog') }}" class="filter-pill {{ empty($stageId) ? 'active' : '' }}">
                <i class="fa-solid fa-border-all"></i>
                <span>{{ __('جميع الفروع') }}</span>
            </a>
            @foreach($stages as $stg)
                <a href="{{ route('courses.catalog', ['stage_id' => $stg->id]) }}" class="filter-pill {{ $stageId == $stg->id ? 'active' : '' }}">
                    <span>{{ $stg->label_ar ?? ($stg->name_ar ?? $stg->name) }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- شبكة بطاقات المواد الأكاديمية -->
    <div class="ed-courses-grid">
        @forelse($subjects as $sub)
            @php
                $isEnrolled = in_array($sub->id, $enrolledSubjectIds);
                $isPending = in_array($sub->id, $pendingSubjectIds ?? []);
                $themeColor = $sub->color ?? '#1e3a8a';
            @endphp

            <div class="ed-course-card {{ $isEnrolled ? 'is-enrolled' : '' }}">

                <!-- شارة الحالة الأكاديمية -->
                @if($isEnrolled)
                    <div class="card-status-badge badge-enrolled">
                        <i class="fa-solid fa-circle-check"></i> {{ __('مشمولة في خطتك الدراسية') }}
                    </div>
                @elseif($isPending)
                    <div class="card-status-badge badge-pending">
                        <i class="fa-solid fa-clock-rotate-left"></i> {{ __('بانتظار اعتماد الحساب') }}
                    </div>
                @else
                    <div class="card-status-badge badge-curriculum">
                        <i class="fa-solid fa-shield-halved"></i> {{ __('منهاج وزاري معتمد') }}
                    </div>
                @endif

                <div class="course-card-top">
                    <div class="course-icon-row">
                        <div class="course-icon-sq" style="color: {{ $themeColor }}; background: {{ $themeColor }}15; border: 1px solid {{ $themeColor }}30;">
                            <i class="fa-solid {{ $sub->icon ?? 'fa-book-open' }}"></i>
                        </div>
                        <div class="course-title-block">
                            <span class="course-stage-name">
                                <i class="fa-solid fa-graduation-cap"></i>
                                {{ optional($sub->stage)->label_ar ?? (optional($sub->stage)->name_ar ?? __('توجيهي فلسطين')) }}
                            </span>
                            <h3 class="course-title">{{ $sub->name_ar ?? $sub->name }}</h3>
                        </div>
                    </div>

                    <!-- الأستاذ المشرف -->
                    <div class="course-teacher-badge">
                        <i class="fa-solid fa-chalkboard-user"></i>
                        <span>{{ __('المشرف الأكاديمي:') }} <strong>{{ $sub->teacher_display_name }}</strong></span>
                    </div>

                    <!-- نبذة المنهاج -->
                    <p class="course-desc">
                        {{ $sub->description ?: __('شرح منهجي شامل وتفاعلي لمفردات الكتاب الوزاري الفلسطيني مع تطبيقات عملية، حلول أسئلة السنوات السابقة، ونماذج امتحانات تفاعلية.') }}
                    </p>

                    <!-- المؤشرات الأكاديمية -->
                    <div class="course-stats-line">
                        <span class="stat-pill" title="{{ __('الدروس والشروحات المرئية') }}">
                            <i class="fa-solid fa-circle-play text-primary"></i>
                            <strong>{{ $sub->contents_count ?? 0 }}</strong> {{ __('درس مرئي') }}
                        </span>
                        <span class="stat-pill" title="{{ __('الاختبارات والتدريبات التفاعلية') }}">
                            <i class="fa-solid fa-file-pen text-amber"></i>
                            <strong>{{ $sub->exams_count ?? 0 }}</strong> {{ __('اختبار وبنك أسئلة') }}
                        </span>
                        <span class="stat-pill" title="{{ __('ملازم وتلخيصات PDF') }}">
                            <i class="fa-solid fa-file-pdf text-rose"></i>
                            {{ __('ملازم وتلاخيص') }}
                        </span>
                    </div>
                </div>

                <div class="course-card-bottom">
                    @if($isEnrolled)
                        <a href="{{ route('student.subjects.show', $sub->id) }}" class="btn-card-action btn-enter-subject">
                            <span>{{ __('دخول المادة ومتابعة التعلم') }}</span>
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    @elseif($isPending)
                        <div class="pending-notice-box">
                            <i class="fa-solid fa-hourglass-half"></i>
                            <span>{{ __('قيد المراجعة والاعتماد لدى الإدارة') }}</span>
                        </div>
                    @else
                        <div class="visitor-actions-row">
                            <button type="button" class="btn-card-action btn-outline-info" onclick="openSubjectModal({{ json_encode([
                                'name' => $sub->name_ar ?? $sub->name,
                                'stage' => optional($sub->stage)->label_ar ?? optional($sub->stage)->name_ar ?? __('الثانوية العامة'),
                                'teacher' => $sub->teacher_display_name,
                                'desc' => $sub->description ?: __('شرح منهجي شامل وتفاعلي لمفردات الكتاب الوزاري الفلسطيني مع تطبيقات عملية، حلول أسئلة السنوات السابقة، ونماذج امتحانات تفاعلية.'),
                                'lessons' => $sub->contents_count ?? 0,
                                'exams' => $sub->exams_count ?? 0,
                                'icon' => $sub->icon ?? 'fa-book-open',
                                'color' => $themeColor
                            ]) }})">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>{{ __('تفاصيل المنهاج') }}</span>
                            </button>

                            @if(!Auth::guard('student')->check())
                                <a href="{{ route('students.create') }}" class="btn-card-action btn-register-cta">
                                    <i class="fa-solid fa-user-plus"></i>
                                    <span>{{ __('التسجيل') }}</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

            </div>
        @empty
            <div class="ed-empty-courses">
                <i class="fa-solid fa-folder-open"></i>
                <h3>{{ __('لا توجد مواد مسجلة لهذا الفرع حالياً') }}</h3>
                <p>{{ __('يرجى اختيار فرع دراسي آخر من شريط الفروع بالأعلى للاطلاع على المقررات المتاحة.') }}</p>
                <a href="{{ route('courses.catalog') }}" class="filter-pill active" style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-rotate-right"></i> {{ __('عرض جميع الفروع') }}
                </a>
            </div>
        @endforelse
    </div>

</div>

<!-- نافذة المعاينة السريعة للمنهاج الأكاديمي -->
<div id="subjectModal" class="subject-modal-overlay" onclick="closeSubjectModal(event)">
    <div class="subject-modal-card" onclick="event.stopPropagation()">
        <button type="button" class="modal-close-btn" onclick="closeSubjectModal()">&times;</button>
        <div class="modal-header">
            <div id="modalIconBox" class="modal-icon-sq">
                <i id="modalIcon" class="fa-solid fa-book-open"></i>
            </div>
            <div>
                <span id="modalStage" class="modal-stage-badge"></span>
                <h2 id="modalTitle" class="modal-title"></h2>
            </div>
        </div>
        <div class="modal-body">
            <div class="modal-teacher-box">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>{{ __('إشراف الأستاذ المعتمد:') }} <strong id="modalTeacher"></strong></span>
            </div>

            <div class="modal-section-title">
                <i class="fa-solid fa-align-right"></i>
                <span>{{ __('نظرة عامة على المقرر الدراسي:') }}</span>
            </div>
            <p id="modalDesc" class="modal-desc-text"></p>

            <div class="modal-metrics-grid">
                <div class="modal-metric-card">
                    <i class="fa-solid fa-circle-play text-primary"></i>
                    <div>
                        <strong id="modalLessons">0</strong>
                        <small>{{ __('شروحات مرئية') }}</small>
                    </div>
                </div>
                <div class="modal-metric-card">
                    <i class="fa-solid fa-file-pen text-amber"></i>
                    <div>
                        <strong id="modalExams">0</strong>
                        <small>{{ __('نماذج واختبارات') }}</small>
                    </div>
                </div>
                <div class="modal-metric-card">
                    <i class="fa-solid fa-file-pdf text-rose"></i>
                    <div>
                        <strong>100%</strong>
                        <small>{{ __('تغطية وزارية') }}</small>
                    </div>
                </div>
            </div>

            <div class="modal-enroll-note">
                <i class="fa-solid fa-lightbulb"></i>
                <span>{{ __('للالتحاق بهذا المقرر ومتابعة الدروس والاختبارات التفاعلية، يرجى التسجيل في المنصة أو سداد القسط الشهري المعتمد.') }}</span>
            </div>
        </div>
        <div class="modal-footer">
            @if(Auth::guard('student')->check())
                <a href="{{ route('student.subjects.index') }}" class="btn-modal-primary">
                    <i class="fa-solid fa-book-bookmark"></i>
                    <span>{{ __('الانتقال إلى موادي الدراسية') }}</span>
                </a>
            @else
                <a href="{{ route('students.create') }}" class="btn-modal-primary">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>{{ __('تسجيل طالب جديد الآن') }}</span>
                </a>
            @endif
            <button type="button" class="btn-modal-secondary" onclick="closeSubjectModal()">
                {{ __('إغلاق') }}
            </button>
        </div>
    </div>
</div>

<style>
/* ==========================================================
   ACADEMIC INFORMATIVE COURSE DIRECTORY (RESPONSIVE & CLEAN)
   ========================================================== */
.ed-catalog-page {
    width: 100%;
    margin: 0;
    padding: 0 0 60px;
    box-sizing: border-box;
}

/* Header */
.ed-page-header {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-top: 4px solid #1e3a8a;
    border-radius: 12px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.ed-flag-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.ed-page-title {
    font-size: 1.45rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}

.ed-page-desc {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0;
    max-width: 750px;
    line-height: 1.6;
}

/* Info Banner */
.ed-info-banner {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-right: 4px solid #1e3a8a;
    border-radius: 10px;
    padding: 18px 22px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    flex-wrap: wrap;
}

html[dir="ltr"] .ed-info-banner {
    border-right: 1px solid #cbd5e1;
    border-left: 4px solid #1e3a8a;
}

.info-banner-icon {
    font-size: 1.8rem;
    color: #1e3a8a;
    display: flex;
    align-items: center;
}

.info-banner-content {
    flex: 1;
    min-width: 260px;
}

.info-banner-content h3 {
    margin: 0 0 4px;
    font-size: 0.95rem;
    font-weight: 800;
    color: #0f172a;
}

.info-banner-content p {
    margin: 0;
    font-size: 0.84rem;
    color: #475569;
    line-height: 1.55;
}

.btn-banner-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #1e3a8a;
    color: #ffffff !important;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.84rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-banner-action:hover {
    background: #0f172a;
    transform: translateY(-1px);
}

/* Filter Bar */
.ed-stage-filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 22px;
    background: #ffffff;
    padding: 12px 18px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 0.85rem;
    color: #334155;
}

.filter-label i { color: #1e3a8a; }

.filter-pills-list {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    background: #f1f5f9;
    color: #475569;
    transition: all 0.15s ease;
}

.filter-pill.active {
    background: #1e3a8a;
    color: #ffffff;
}

.filter-pill:hover:not(.active) {
    background: #e2e8f0;
    color: #0f172a;
}

/* Courses Grid */
.ed-courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 22px;
}

.ed-course-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 22px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.ed-course-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
}

.ed-course-card.is-enrolled {
    border-color: #86efac;
    background: #fafffc;
}

.card-status-badge {
    position: absolute;
    top: 14px;
    left: 14px;
    font-size: 0.72rem;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}

html[dir="ltr"] .card-status-badge {
    left: auto;
    right: 14px;
}

.badge-enrolled { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.badge-curriculum { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }

.course-icon-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    padding-top: 12px;
}

.course-icon-sq {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.course-title-block {
    flex: 1;
}

.course-stage-name {
    font-size: 0.72rem;
    font-weight: 700;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 5px;
}

.course-title {
    font-size: 1.12rem;
    font-weight: 800;
    color: #0f172a;
    margin: 2px 0 0;
}

.course-teacher-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.76rem;
    color: #334155;
    margin-bottom: 12px;
}

.course-teacher-badge i {
    color: #1e3a8a;
}

.course-desc {
    font-size: 0.83rem;
    color: #64748b;
    line-height: 1.6;
    margin: 0 0 16px;
    min-height: 45px;
}

.course-stats-line {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    border-top: 1px solid #f1f5f9;
    padding-top: 12px;
    margin-bottom: 16px;
}

.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #475569;
}

.course-card-bottom {
    border-top: 1px solid #f1f5f9;
    padding-top: 14px;
}

.visitor-actions-row {
    display: flex;
    gap: 8px;
    align-items: center;
}

.btn-card-action {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
    border: none;
}

.btn-outline-info {
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
}

.btn-outline-info:hover {
    background: #dbeafe;
    border-color: #93c5fd;
}

.btn-register-cta {
    background: #1e3a8a;
    color: #ffffff !important;
}

.btn-register-cta:hover {
    background: #0f172a;
}

.btn-enter-subject {
    background: #059669;
    color: #ffffff !important;
    width: 100%;
}

.btn-enter-subject:hover {
    background: #047857;
}

.pending-notice-box {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    padding: 8px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
}

/* Empty State */
.ed-empty-courses {
    grid-column: 1 / -1;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 50px 20px;
    text-align: center;
}

.ed-empty-courses i {
    font-size: 2.8rem;
    color: #94a3b8;
    margin-bottom: 12px;
}

.ed-empty-courses h3 {
    font-size: 1.15rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 6px;
}

.ed-empty-courses p {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 16px;
}

/* Modal */
.subject-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.6);
    z-index: 9999;
    backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.subject-modal-card {
    background: #ffffff;
    border-radius: 16px;
    max-width: 540px;
    width: 100%;
    padding: 28px;
    position: relative;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    animation: modalIn 0.2s ease-out;
}

@keyframes modalIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.modal-close-btn {
    position: absolute;
    top: 18px;
    left: 18px;
    background: #f1f5f9;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 1.3rem;
    cursor: pointer;
    display: grid;
    place-items: center;
    color: #64748b;
    transition: 0.15s;
}

html[dir="ltr"] .modal-close-btn {
    left: auto;
    right: 18px;
}

.modal-close-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.modal-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 18px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e2e8f0;
}

.modal-icon-sq {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.modal-stage-badge {
    display: inline-block;
    font-size: 0.74rem;
    font-weight: 700;
    color: #1e3a8a;
    background: #eff6ff;
    padding: 2px 8px;
    border-radius: 4px;
    margin-bottom: 4px;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.modal-teacher-box {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 0.82rem;
    color: #334155;
    margin-bottom: 14px;
}

.modal-teacher-box i {
    color: #1e3a8a;
}

.modal-section-title {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
}

.modal-desc-text {
    font-size: 0.86rem;
    color: #475569;
    line-height: 1.65;
    margin-bottom: 18px;
}

.modal-metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 18px;
}

.modal-metric-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-metric-card i {
    font-size: 1.4rem;
}

.modal-metric-card strong {
    display: block;
    font-size: 1.1rem;
    color: #0f172a;
}

.modal-metric-card small {
    font-size: 0.72rem;
    color: #64748b;
}

.modal-enroll-note {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 0.8rem;
    color: #1e40af;
    line-height: 1.5;
    margin-bottom: 20px;
}

.modal-enroll-note i {
    margin-top: 2px;
}

.modal-footer {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-modal-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #1e3a8a;
    color: #ffffff !important;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    text-decoration: none;
    transition: 0.15s;
}

.btn-modal-primary:hover {
    background: #0f172a;
}

.btn-modal-secondary {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    cursor: pointer;
    transition: 0.15s;
}

.btn-modal-secondary:hover {
    background: #e2e8f0;
}
</style>

<script>
function openSubjectModal(data) {
    document.getElementById('modalTitle').textContent = data.name;
    document.getElementById('modalStage').textContent = data.stage;
    document.getElementById('modalTeacher').textContent = data.teacher;
    document.getElementById('modalDesc').textContent = data.desc;
    document.getElementById('modalLessons').textContent = data.lessons;
    document.getElementById('modalExams').textContent = data.exams;

    const iconBox = document.getElementById('modalIconBox');
    const icon = document.getElementById('modalIcon');
    iconBox.style.color = data.color || '#1e3a8a';
    iconBox.style.background = (data.color || '#1e3a8a') + '15';
    iconBox.style.border = '1px solid ' + (data.color || '#1e3a8a') + '30';
    icon.className = 'fa-solid ' + (data.icon || 'fa-book-open');

    const modal = document.getElementById('subjectModal');
    modal.style.display = 'flex';
}

function closeSubjectModal(e) {
    if (!e || e.target.id === 'subjectModal' || e.target.classList.contains('modal-close-btn') || e.target.classList.contains('btn-modal-secondary')) {
        document.getElementById('subjectModal').style.display = 'none';
    }
}
</script>
@endsection
