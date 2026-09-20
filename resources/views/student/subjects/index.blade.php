@extends('layouts.app')

@section('title', __('موادي ومقرراتي الدراسية') . ' | ' . __('منارة التوجيهي'))

@section('content')
<div class="ed-subjects-catalog">

    <!-- 1. الترويسة الأكاديمية لمقررات الطالب -->
    <header class="ed-catalog-header">
        <div class="ed-catalog-header-main">
            <div class="ed-academic-badge">
                <i class="fa-solid fa-graduation-cap text-amber"></i>
                <span>{{ __('الثانوية العامة - فلسطين') }}</span>
                <span class="badge-dot">•</span>
                <span>{{ __('المنهاج الفلسطيني المعتمد') }}</span>
            </div>

            <h1 class="ed-catalog-title">
                <i class="fa-solid fa-book-open-reader text-primary" style="margin-left: 8px;"></i>
                {{ __('موادي ومقرراتي الدراسية') }}
            </h1>

            <p class="ed-catalog-desc">
                {{ __('المساقات والمناهج التعليمية المعتمدة لحسابك الدراسي، مع إمكانية الوصول المباشر لكافة الدروس والمحاضرات المرئية، الملازم الوزارية، وبنك الاختبارات التقييمية.') }}
            </p>
        </div>

        <!-- إحصائيات سريعة كلاسيكية لمقررات الطالب -->
        <div class="ed-catalog-stats">
            <div class="ed-cstat-item">
                <span class="ed-cstat-num">{{ $subjects->count() }}</span>
                <span class="ed-cstat-label">{{ __('المقررات المعتمدة') }}</span>
            </div>
            <div class="ed-cstat-divider"></div>
            <div class="ed-cstat-item">
                <span class="ed-cstat-num">{{ $subjects->sum('educational_contents_count') + $subjects->sum('contents_count') }}</span>
                <span class="ed-cstat-label">{{ __('إجمالي المحاضرات') }}</span>
            </div>
            <div class="ed-cstat-divider"></div>
            <div class="ed-cstat-item">
                <span class="ed-cstat-num">{{ $subjects->sum('exams_count') }}</span>
                <span class="ed-cstat-label">{{ __('بنك الاختبارات') }}</span>
            </div>
        </div>
    </header>

    <!-- 2. شريط الأدوات والتبديل بين طريقة العرض (بطاقات فاخرة / سجل أكاديمي) -->
    <div class="ed-catalog-toolbar">
        <div class="toolbar-search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="subjectSearchInput" placeholder="{{ __('بحث في موادي الدراسية...') }}" onkeyup="filterSubjectsCatalog()">
        </div>

        <div class="toolbar-view-switcher">
            <span class="switcher-label">{{ __('نمط العرض:') }}</span>
            <div class="switcher-buttons">
                <button type="button" class="btn-switch-view" id="btnViewCards" onclick="setCatalogViewMode('cards')" title="{{ __('عرض البطاقات الأكاديمية') }}">
                    <i class="fa-solid fa-grid-2"></i>
                    <span>{{ __('بطاقات المقررات') }}</span>
                </button>
                <button type="button" class="btn-switch-view active" id="btnViewTable" onclick="setCatalogViewMode('table')" title="{{ __('عرض السجل الأكاديمي') }}">
                    <i class="fa-solid fa-table-list"></i>
                    <span>{{ __('جدول السجل') }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 3. النمط الأول: بطاقات المقررات الأكاديمية الملكية الفاخرة (Cards View) -->
    <div id="catalogCardsContainer" class="ed-subjects-grid" style="display: none;">
        @forelse($subjects as $index => $subject)
            @php
                $color = $subject->color ?: '#1e3a8a';
                $lessonsCount = ($subject->educational_contents_count ?? 0) + ($subject->contents_count ?? 0);
                $examsCount = $subject->exams_count ?? 0;
                $subjectName = (app()->getLocale() === 'en' && !empty($subject->name_en)) ? $subject->name_en : ($subject->name_ar ?? $subject->name);
                $stageLabel = $subject->stage?->label_ar ? __($subject->stage->label_ar) : __('توجيهي عام');
                $teacherName = $subject->teacher?->name ?: __('أ. أحمد حسين شمالي (المشرف العام)');
                $subjectKey = $subject->subject_key ?: ('CRS-' . $subject->id);
            @endphp
            <div class="ed-subject-card" data-search="{{ strtolower($subjectName . ' ' . $subjectKey . ' ' . $stageLabel . ' ' . ($subject->description ?? '')) }}">
                <!-- شريط التمييز اللوني للمساق -->
                <div class="card-accent-strip" style="background: linear-gradient(90deg, {{ $color }} 0%, {{ $color }}cc 100%);"></div>

                <div class="card-inner-body">
                    <!-- الترويسة العلوية للبطاقة -->
                    <div class="card-top-row">
                        <span class="card-code-badge font-mono" style="color: {{ $color }}; background: {{ $color }}12; border: 1px solid {{ $color }}30;">
                            {{ $subjectKey }}
                        </span>

                        <span class="card-stage-pill">
                            <i class="fa-solid fa-graduation-cap"></i>
                            <span>{{ $stageLabel }}</span>
                        </span>
                    </div>

                    <!-- أيقونة وعنوان المقرر -->
                    <div class="card-main-info">
                        <div class="card-icon-box" style="color: {{ $color }}; background: {{ $color }}10; border: 1px solid {{ $color }}25;">
                            <i class="{{ $subject->icon ?: 'fa-solid fa-book-bookmark' }}"></i>
                        </div>

                        <div class="card-titles-wrap">
                            <h3 class="card-subject-title">
                                <a href="{{ route('student.subjects.show', $subject->id) }}">
                                    {{ $subjectName }}
                                </a>
                            </h3>
                            <span class="card-sub-curriculum">
                                <i class="fa-solid fa-certificate text-amber"></i>
                                {{ __('المنهاج الفلسطيني المعتمد') }}
                            </span>
                        </div>
                    </div>

                    <!-- الوصف الأكاديمي للمقرر -->
                    <p class="card-desc-snippet">
                        {{ !empty($subject->description) ? Str::limit($subject->description, 110) : __('المساق الوزاري الشامل، ويشمل الدروس المشروحة، الملازم، وبنك الأسئلة والاختبارات الوزارية.') }}
                    </p>

                    <!-- المعلم أو المشرف الأكاديمي -->
                    <div class="card-instructor-chip">
                        <i class="fa-solid fa-chalkboard-user" style="color: {{ $color }};"></i>
                        <span>{{ $teacherName }}</span>
                    </div>

                    <!-- شريط المؤشرات (المحاضرات + الاختبارات) -->
                    <div class="card-metrics-grid">
                        <div class="metric-cell">
                            <i class="fa-solid fa-circle-play text-primary"></i>
                            <div class="metric-text-wrap">
                                <strong class="metric-val">{{ $lessonsCount }}</strong>
                                <span class="metric-name">{{ __('محاضرة ودرس') }}</span>
                            </div>
                        </div>

                        <div class="metric-divider"></div>

                        <div class="metric-cell">
                            <i class="fa-solid fa-file-signature text-emerald"></i>
                            <div class="metric-text-wrap">
                                <strong class="metric-val">{{ $examsCount }}</strong>
                                <span class="metric-name">{{ __('اختبار تقييمي') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- زر الإجراء الرئيسي لدخول المقرر -->
                <div class="card-footer-strip">
                    <a href="{{ route('student.subjects.show', $subject->id) }}" class="btn-enter-subject" style="background-color: {{ $color }};">
                        <span>{{ __('دخول المقرر والبدء بالدراسة') }}</span>
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="ed-empty-catalog-box">
                <i class="fa-solid fa-folder-open"></i>
                <h3>{{ __('لا توجد مقررات دراسية مسجلة بحسابك حالياً') }}</h3>
                <p>{{ __('يرجى التواصل مع المشرف العام أو مراجعة إدارة المنصة لربط مواد مسارك الأكاديمي، أو استعراض دليل المناهج والمقررات المعتمدة.') }}</p>
                <a href="{{ route('courses.catalog') }}" class="btn-browse-catalog">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>{{ __('دليل المقررات والمناهج المعتمدة') }}</span>
                </a>
            </div>
        @endforelse
    </div>

    <!-- 4. النمط الثاني: السجل الأكاديمي للمقررات (Table View - الافتراضي) -->
    <div id="catalogTableContainer" class="ed-academic-table-container" style="display: block;">
        <div class="table-card-head">
            <div class="table-card-title">
                <i class="fa-solid fa-table-list text-primary"></i>
                <h3>{{ __('سجل المقررات والمباحث الدراسية المعتمدة') }}</h3>
            </div>
            <span class="table-card-sub">
                {{ __('المنهاج الفلسطيني المعتمد') }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="academic-roster-table" id="subjectsRosterTable">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th style="width: 140px;">{{ __('رمز المساق') }}</th>
                        <th style="min-width: 260px;">{{ __('المبحث والمقرر الدراسي') }}</th>
                        <th style="min-width: 160px;">{{ __('الفرع الأكاديمي') }}</th>
                        <th style="text-align: center; width: 120px;">{{ __('الدروس') }}</th>
                        <th style="text-align: center; width: 120px;">{{ __('التقييمات') }}</th>
                        <th style="text-align: center; width: 180px;">{{ __('الإجراء الأكاديمي') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $index => $subject)
                        @php
                            $color = $subject->color ?: '#1e3a8a';
                            $lessonsCount = ($subject->educational_contents_count ?? 0) + ($subject->contents_count ?? 0);
                            $examsCount = $subject->exams_count ?? 0;
                            $subjectName = (app()->getLocale() === 'en' && !empty($subject->name_en)) ? $subject->name_en : ($subject->name_ar ?? $subject->name);
                            $stageLabel = $subject->stage?->label_ar ? __($subject->stage->label_ar) : __('توجيهي عام');
                            $subjectKey = $subject->subject_key ?: ('CRS-' . $subject->id);
                        @endphp
                        <tr class="table-subject-row" data-search="{{ strtolower($subjectName . ' ' . $subjectKey . ' ' . $stageLabel . ' ' . ($subject->description ?? '')) }}">
                            <!-- الرقم التسلسلي -->
                            <td style="text-align: center; font-weight: 700; color: #64748b; font-family: monospace;">
                                {{ sprintf('%02d', $index + 1) }}
                            </td>

                            <!-- رمز المساق -->
                            <td>
                                <span class="tbl-code-badge font-mono" style="color: {{ $color }}; background: {{ $color }}12; border: 1px solid {{ $color }}30;">
                                    {{ $subjectKey }}
                                </span>
                            </td>

                            <!-- المبحث والمقرر -->
                            <td>
                                <div class="tbl-subject-cell">
                                    <div class="tbl-subject-icon" style="color: {{ $color }}; background: {{ $color }}10; border: 1px solid {{ $color }}25;">
                                        <i class="{{ $subject->icon ?: 'fa-solid fa-book-bookmark' }}"></i>
                                    </div>
                                    <div class="tbl-subject-meta">
                                        <a href="{{ route('student.subjects.show', $subject->id) }}" class="tbl-subject-name">
                                            {{ $subjectName }}
                                        </a>
                                        @if(!empty($subject->description))
                                            <p class="tbl-subject-desc">{{ Str::limit($subject->description, 75) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- الفرع الأكاديمي -->
                            <td>
                                <span class="tbl-stage-badge">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                    <span>{{ $stageLabel }}</span>
                                </span>
                            </td>

                            <!-- عدد الدروس -->
                            <td style="text-align: center;">
                                <span class="tbl-metric-pill blue">
                                    <i class="fa-solid fa-circle-play"></i>
                                    <span>{{ $lessonsCount }} {{ __('درس') }}</span>
                                </span>
                            </td>

                            <!-- عدد التقييمات -->
                            <td style="text-align: center;">
                                <span class="tbl-metric-pill emerald">
                                    <i class="fa-solid fa-file-pen"></i>
                                    <span>{{ $examsCount }} {{ __('اختبار') }}</span>
                                </span>
                            </td>

                            <!-- زر الدخول -->
                            <td style="text-align: center;">
                                <a href="{{ route('student.subjects.show', $subject->id) }}" class="tbl-enter-btn" style="background-color: {{ $color }};">
                                    <span>{{ __('دخول المقرر') }}</span>
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px 20px;">
                                <div class="ed-empty-catalog-box">
                                    <i class="fa-solid fa-folder-open"></i>
                                    <h3>{{ __('لا توجد مقررات دراسية مسجلة حالياً') }}</h3>
                                    <p>{{ __('يرجى مراجعة إدارة المنصة لربط مواد مرحلتك الدراسية.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    /* ==========================================================================
       التصميم الأكاديمي الملكي لمقررات الطالب (Royal Classic Student Subjects Hub)
       - ألوان كلاسيكية رصينة: كحلي ملكي (#0d1b2a, #1e3a8a) ورمادي فاتح (#f8fafc)
       - إمكانية التبديل السلس بين عرض البطاقات الأكاديمية الفاخرة وعرض السجل
       - متوافق 100% مع كافة الشاشات وأجهزة الهاتف
       ========================================================================== */

    .ed-subjects-catalog {
        max-width: 1440px;
        margin: 0 auto;
        padding: 4px 6px 60px;
        box-sizing: border-box;
    }

    /* 1. ترويسة الصفحة */
    .ed-catalog-header {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-top: 4px solid #1e3a8a;
        border-radius: 8px;
        padding: 22px 26px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .ed-academic-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
        padding: 4px 12px;
        border-radius: 5px;
        font-size: 0.78rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .badge-dot {
        color: #d97706;
    }

    .ed-catalog-title {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
        display: flex;
        align-items: center;
    }

    .ed-catalog-desc {
        font-size: 0.85rem;
        color: #64748b;
        margin: 0;
        max-width: 720px;
        line-height: 1.55;
    }

    .ed-catalog-stats {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 20px;
        gap: 16px;
    }

    .ed-cstat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .ed-cstat-num {
        font-size: 1.35rem;
        font-weight: 800;
        color: #1e3a8a;
        font-family: monospace;
        line-height: 1.1;
    }

    .ed-cstat-label {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 700;
        margin-top: 3px;
    }

    .ed-cstat-divider {
        width: 1px;
        height: 32px;
        background: #cbd5e1;
    }

    /* 2. شريط الأدوات والتبديل */
    .ed-catalog-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 18px;
        margin-bottom: 22px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
    }

    .toolbar-search-box {
        position: relative;
        display: flex;
        align-items: center;
        flex: 1;
        max-width: 380px;
    }

    .toolbar-search-box i {
        position: absolute;
        right: 12px;
        color: #94a3b8;
        font-size: 0.85rem;
        pointer-events: none;
    }

    .toolbar-search-box input {
        width: 100%;
        height: 36px;
        padding: 0 34px 0 14px;
        font-size: 0.84rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: #f8fafc;
        color: #0f172a;
        outline: none;
        transition: all 0.2s ease;
    }

    .toolbar-search-box input:focus {
        background: #ffffff;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.1);
    }

    .toolbar-view-switcher {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .switcher-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
    }

    .switcher-buttons {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 3px;
    }

    .btn-switch-view {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: transparent;
        color: #475569;
        border: none;
        padding: 5px 12px;
        border-radius: 4px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-switch-view:hover {
        color: #1e3a8a;
    }

    .btn-switch-view.active {
        background: #ffffff;
        color: #1e3a8a;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
    }

    /* 3. شبكة بطاقات المقررات الأكاديمية (Cards Grid) */
    .ed-subjects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 22px;
        width: 100%;
    }

    .ed-subject-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .ed-subject-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        border-color: #94a3b8;
    }

    .card-accent-strip {
        height: 5px;
        width: 100%;
    }

    .card-inner-body {
        padding: 18px 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .card-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }

    .card-code-badge {
        font-size: 0.75rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 4px;
        letter-spacing: 0.4px;
    }

    .card-stage-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.74rem;
        font-weight: 700;
        color: #334155;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .card-main-info {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .card-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .card-titles-wrap {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .card-subject-title {
        margin: 0;
        font-size: 1.12rem;
        font-weight: 800;
        line-height: 1.35;
    }

    .card-subject-title a {
        color: #0f172a;
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .card-subject-title a:hover {
        color: #1e3a8a;
    }

    .card-sub-curriculum {
        font-size: 0.74rem;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .card-desc-snippet {
        font-size: 0.82rem;
        color: #475569;
        line-height: 1.5;
        margin: 0 0 14px;
        min-height: 38px;
    }

    .card-instructor-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.76rem;
        font-weight: 700;
        color: #334155;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 5px 10px;
        border-radius: 5px;
        margin-bottom: 14px;
        width: fit-content;
    }

    .card-metrics-grid {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 10px 14px;
        margin-top: auto;
    }

    .metric-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .metric-cell i {
        font-size: 1.1rem;
    }

    .metric-text-wrap {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .metric-val {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        font-family: monospace;
    }

    .metric-name {
        font-size: 0.7rem;
        color: #64748b;
        font-weight: 600;
    }

    .metric-divider {
        width: 1px;
        height: 24px;
        background: #cbd5e1;
    }

    .card-footer-strip {
        padding: 12px 20px;
        background: #fafafa;
        border-top: 1px solid #f1f5f9;
    }

    .btn-enter-subject {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #ffffff !important;
        text-decoration: none;
        padding: 9px 16px;
        border-radius: 5px;
        font-size: 0.84rem;
        font-weight: 700;
        transition: opacity 0.15s ease, transform 0.15s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .btn-enter-subject:hover {
        opacity: 0.92;
        transform: translateX(-2px);
    }

    /* 4. نمط السجل الأكاديمي (Table View) */
    .ed-academic-table-container {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .table-card-head {
        padding: 16px 20px;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .table-card-title {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .table-card-title h3 {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .table-card-sub {
        font-size: 0.78rem;
        font-weight: 700;
        color: #1e3a8a;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 3px 9px;
        border-radius: 4px;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .academic-roster-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
        font-size: 0.84rem;
    }

    .academic-roster-table thead th {
        background-color: #0d1b2a;
        color: #f8fafc;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 12px 14px;
        border-bottom: 2px solid #b45309;
        white-space: nowrap;
    }

    .academic-roster-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
        background: #ffffff;
        transition: background 0.15s ease;
    }

    .academic-roster-table tbody tr:nth-child(even) td {
        background-color: #fbfcfd;
    }

    .academic-roster-table tbody tr:hover td {
        background-color: #f1f5f9;
    }

    .tbl-code-badge {
        font-size: 0.75rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 4px;
        white-space: nowrap;
    }

    .tbl-subject-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .tbl-subject-icon {
        width: 34px;
        height: 34px;
        border-radius: 6px;
        display: grid;
        place-items: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .tbl-subject-meta {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .tbl-subject-name {
        font-size: 0.88rem;
        font-weight: 800;
        color: #0f172a;
        text-decoration: none;
    }

    .tbl-subject-name:hover {
        color: #1e3a8a;
        text-decoration: underline;
    }

    .tbl-subject-desc {
        font-size: 0.74rem;
        color: #64748b;
        margin: 0;
        line-height: 1.35;
    }

    .tbl-stage-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #334155;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 3px 8px;
        border-radius: 4px;
        white-space: nowrap;
    }

    .tbl-metric-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.76rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        white-space: nowrap;
    }

    .tbl-metric-pill.blue {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    .tbl-metric-pill.emerald {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .tbl-enter-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #ffffff !important;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 6px 14px;
        border-radius: 4px;
        transition: opacity 0.15s ease;
        white-space: nowrap;
    }

    .tbl-enter-btn:hover {
        opacity: 0.9;
    }

    /* حالة الفراغ */
    .ed-empty-catalog-box {
        grid-column: 1 / -1;
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 50px 24px;
        text-align: center;
        color: #64748b;
    }

    .ed-empty-catalog-box i {
        font-size: 2.5rem;
        color: #94a3b8;
        margin-bottom: 12px;
    }

    .ed-empty-catalog-box h3 {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 6px;
    }

    .ed-empty-catalog-box p {
        font-size: 0.84rem;
        color: #64748b;
        max-width: 480px;
        margin: 0 auto 16px;
        line-height: 1.55;
    }

    .btn-browse-catalog {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #1e3a8a;
        color: #ffffff !important;
        text-decoration: none;
        padding: 9px 18px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 700;
    }

    /* فئات مساعدة */
    .text-primary { color: #1e3a8a !important; }
    .text-amber { color: #b45309 !important; }
    .text-emerald { color: #15803d !important; }
    .font-mono { font-family: monospace, sans-serif; }

    /* استجابة الشاشات */
    @media (max-width: 768px) {
        .ed-catalog-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 16px 18px;
        }
        .ed-catalog-stats {
            width: 100%;
            justify-content: space-around;
            box-sizing: border-box;
        }
        .ed-catalog-toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        .toolbar-search-box {
            max-width: 100%;
        }
        .toolbar-view-switcher {
            justify-content: space-between;
        }
        .ed-subjects-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // 1. التبديل بين نمط البطاقات الفاخرة ونمط السجل والجدول
    function setCatalogViewMode(mode) {
        const cardsCont = document.getElementById('catalogCardsContainer');
        const tableCont = document.getElementById('catalogTableContainer');
        const btnCards = document.getElementById('btnViewCards');
        const btnTable = document.getElementById('btnViewTable');

        if (mode === 'table') {
            cardsCont.style.display = 'none';
            tableCont.style.display = 'block';
            btnCards.classList.remove('active');
            btnTable.classList.add('active');
            localStorage.setItem('student_subjects_view_mode', 'table');
        } else {
            cardsCont.style.display = 'grid';
            tableCont.style.display = 'none';
            btnCards.classList.add('active');
            btnTable.classList.remove('active');
            localStorage.setItem('student_subjects_view_mode', 'cards');
        }
    }

    // استعادة تفضيل العرض المحفوظ لدى الطالب (الافتراضي: جدول أكاديمي)
    document.addEventListener('DOMContentLoaded', function () {
        const savedMode = localStorage.getItem('student_subjects_view_mode');
        if (savedMode === 'cards') {
            setCatalogViewMode('cards');
        } else {
            setCatalogViewMode('table');
        }
    });

    // 2. تصفية وبحث لحظي في المقررات
    function filterSubjectsCatalog() {
        const query = document.getElementById('subjectSearchInput').value.toLowerCase().trim();

        // فلترة البطاقات
        const cards = document.querySelectorAll('#catalogCardsContainer .ed-subject-card');
        cards.forEach(card => {
            const searchData = card.getAttribute('data-search') || '';
            card.style.display = (!query || searchData.includes(query)) ? 'flex' : 'none';
        });

        // فلترة الجدول
        const rows = document.querySelectorAll('#subjectsRosterTable tbody tr.table-subject-row');
        rows.forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            row.style.display = (!query || searchData.includes(query)) ? '' : 'none';
        });
    }
</script>
@endsection
