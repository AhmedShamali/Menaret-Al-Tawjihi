@extends('layouts.app')

@section('title', __('بنك الاختبارات والتقييمات الأكاديمية') . ' | ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

<!-- استدعاء مكتبة SweetAlert2 للتنبيهات الفاخرة -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
<div class="ed-classic-exams-page">

    @php
        $teacherSub = auth()->user()?->subject;
        $subName = (app()->getLocale() === 'en' && !empty($teacherSub?->name_en)) 
            ? $teacherSub->name_en 
            : ($teacherSub?->name_ar ?? ($teacherSub?->name ?? __('جميع المساقات الأكاديمية')));
        $authName = (app()->getLocale() === 'en' && !empty(auth()->user()?->name_en)) 
            ? auth()->user()->name_en 
            : auth()->user()->name;
        $totalQuestions = $exams->sum('questions_count');
        $totalSubmissions = $exams->sum('submissions_count');
        $avgDuration = round($exams->avg('duration_minutes') ?: 45);
    @endphp

    <!-- 1. الهيدر الأكاديمي الكلاسيكي الملكي -->
    <header class="ed-academic-header">
        <div class="header-main-details">
            <div class="ed-header-breadcrumbs">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('teacher.dashboard') }}">
                    <i class="fa-solid fa-landmark"></i>
                    <span>{{ auth()->user()->role === 'admin' ? __('لوحة الإدارة العامة') : __('بوابة المعلم المعتمد') }}</span>
                </a>
                <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} sep"></i>
                <span class="active">{{ __('بنك الاختبارات والتقييمات المدرسية') }}</span>
            </div>

            <div class="ed-title-wrapper">
                <div class="ed-crest-avatar">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <div class="ed-subject-tag-row">
                        <span class="ed-badge-subject">
                            {{ auth()->user()->subject?->icon ?? '📚' }}
                            {{ __('مساق:') }} {{ $subName }}
                        </span>
                        <span class="ed-badge-role">
                            <i class="fa-solid fa-shield-halved"></i>
                            {{ auth()->user()->role === 'admin' ? __('صلاحيات إدارة كاملة') : __('معلم معتمد') }}
                        </span>
                    </div>
                    <h1 class="ed-page-title">
                        {{ __('إدارة الاختبارات والتقييمات المعتمدة') }}
                    </h1>
                    <p class="ed-page-desc">
                        {{ __('إعداد بنوك الأسئلة، جدولة الامتحانات الإلكترونية والمقالية، مراقبة درجات الطلاب ورصد أداء الشعب المدرسية بدقة.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="header-action-buttons">
            <a href="{{ route(auth()->user()->role . '.exams.create') }}" class="ed-btn-royal-primary">
                <i class="fa-solid fa-file-circle-plus"></i>
                <span>{{ __('بناء اختبار جديد') }}</span>
            </a>
            @if(auth()->user()->role === 'teacher')
                <a href="{{ route('teacher.access.index') }}" class="ed-btn-royal-secondary" title="{{ __('التحكم بظهور الاختبارات والدروس للطلاب') }}">
                    <i class="fa-solid fa-sliders"></i>
                    <span>{{ __('صلاحيات الطلاب [✓]') }}</span>
                </a>
            @endif
        </div>
    </header>

    <!-- تنبيه إداري إذا كان الحساب غير مسند لمادة -->
    @if(!auth()->user()->subject_id && auth()->user()->role === 'admin')
        <div class="ed-classic-alert-warning">
            <div class="alert-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="alert-text">
                <strong>{{ __('تنبيه إداري:') }}</strong>
                <span>{{ __('هذا الحساب الإداري يعرض حالياً جميع اختبارات كافة المساقات. يمكنك تخصيص مادة تعليمية من خلال إعدادات المواد.') }}</span>
            </div>
        </div>
    @endif

    <!-- 2. شريط المؤشرات الأكاديمية الكلاسيكي (KPI Strip) -->
    <section class="ed-kpi-strip">
        <div class="ed-kpi-card">
            <div class="kpi-icon-seal navy">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-value">{{ $exams->count() }}</span>
                <span class="kpi-title">{{ __('اختبارات منشورة ومعتمدة') }}</span>
            </div>
        </div>

        <div class="ed-kpi-card">
            <div class="kpi-icon-seal blue">
                <i class="fa-solid fa-circle-question"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-value">{{ $totalQuestions }}</span>
                <span class="kpi-title">{{ __('أسئلة تقييمية مودعة') }}</span>
            </div>
        </div>

        <div class="ed-kpi-card">
            <div class="kpi-icon-seal emerald">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-value">{{ $totalSubmissions }}</span>
                <span class="kpi-title">{{ __('إجابة وتسليم مرصود') }}</span>
            </div>
        </div>

        <div class="ed-kpi-card">
            <div class="kpi-icon-seal amber">
                <i class="fa-solid fa-stopwatch"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-value">{{ $avgDuration }} {{ __('دقيقة') }}</span>
                <span class="kpi-title">{{ __('متوسط الزمن المحدد') }}</span>
            </div>
        </div>
    </section>

    <!-- 3. شريط البحث والأدوات الأكاديمية (Registry Toolbar) -->
    <div class="ed-classic-toolbar">
        <div class="toolbar-search-box">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="examSearchInput" onkeyup="filterExamsTable()" placeholder="{{ __('بحث سريع باسم الاختبار، المساق، أو التاريخ...') }}" autocomplete="off">
            <button type="button" id="clearSearchBtn" onclick="clearExamSearch()" style="display: none;" title="{{ __('مسح البحث') }}">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="toolbar-stats-pill">
            <i class="fa-solid fa-file-lines"></i>
            <span>{{ __('إجمالي السجلات:') }}</span>
            <strong id="displayedCountBadge">{{ $exams->count() }}</strong>
            <span>{{ __('اختبار') }}</span>
        </div>
    </div>

    <!-- 4. جدول سجل الاختبارات الأكاديمي الكلاسيكي -->
    <main class="ed-classic-card">
        <div class="card-registry-header">
            <div class="registry-title-box">
                <i class="fa-solid fa-table-list"></i>
                <h3>{{ __('سجل التقييمات والامتحانات الرسمية') }}</h3>
            </div>
            <span class="registry-badge-count">{{ $exams->count() }} {{ __('اختبار معتمد') }}</span>
        </div>

        @if($exams->count() > 0)
            <div class="table-responsive">
                <table class="ed-classic-table" id="examsRegistryTable">
                    <thead>
                        <tr>
                            <th style="width: 55px; text-align: center;">#</th>
                            <th>{{ __('اسم الاختبار والتقييم') }}</th>
                            <th>{{ __('المساق الأكاديمي') }}</th>
                            <th style="text-align: center;">{{ __('المدة الزمنية') }}</th>
                            <th style="text-align: center;">{{ __('بنك الأسئلة') }}</th>
                            <th style="text-align: center;">{{ __('التسليمات والنتائج') }}</th>
                            <th style="text-align: center;">{{ __('تاريخ النشر') }}</th>
                            <th style="text-align: center; min-width: 170px;">{{ __('الإجراءات والعمليات') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exams as $exam)
                            @php
                                $examSubName = (app()->getLocale() === 'en' && !empty($exam->subject?->name_en)) 
                                    ? $exam->subject->name_en 
                                    : ($exam->subject?->name_ar ?? ($exam->subject?->name ?? __('عام')));
                                $subCount = $exam->submissions_count ?? 0;
                            @endphp
                            <tr class="exam-row" data-search-text="{{ strtolower($exam->title . ' ' . $examSubName . ' ' . ($exam->created_at ? $exam->created_at->format('Y-m-d') : '')) }}">
                                <td style="text-align: center; font-weight: 800; color: #64748b;">
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    <div class="ed-exam-title-cell">
                                        <i class="fa-solid fa-file-pen exam-title-icon"></i>
                                        <div>
                                            <span class="exam-name">{{ $exam->title }}</span>
                                            @if($exam->stage)
                                                <small class="exam-stage-meta">
                                                    <i class="fa-solid fa-graduation-cap"></i> {{ $exam->stage->label_ar ?? $exam->stage->name }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="ed-subject-badge">
                                        {{ $exam->subject?->icon ?? '📚' }}
                                        {{ $examSubName }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="ed-duration-pill">
                                        <i class="fa-regular fa-clock"></i>
                                        <span>{{ $exam->duration_minutes }} {{ __('دقيقة') }}</span>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="ed-questions-pill">
                                        <i class="fa-solid fa-list-ol"></i>
                                        <span>{{ $exam->questions_count ?? 0 }} {{ __('سؤال') }}</span>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    @if($subCount > 0)
                                        <a href="{{ route(auth()->user()->role . '.exams.submissions', $exam->id) }}" class="ed-subm-badge active" title="{{ __('عرض سجل إجابات ودرجات الطلاب') }}">
                                            <i class="fa-solid fa-user-check"></i>
                                            <span>{{ $subCount }} {{ __('تسليم') }}</span>
                                        </a>
                                    @else
                                        <span class="ed-subm-badge empty" title="{{ __('بانتظار بدء الطلاب بتقديم الاختبار') }}">
                                            <i class="fa-regular fa-hourglass-half"></i>
                                            <span>{{ __('0 تسليم') }}</span>
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center; font-size: 0.82rem; color: #64748b; font-weight: 600;">
                                    <i class="fa-regular fa-calendar" style="margin-left: 4px; color: #94a3b8;"></i>
                                    {{ $exam->created_at ? $exam->created_at->format('Y-m-d') : '-' }}
                                </td>
                                <td>
                                    <div class="ed-actions-group">
                                        <!-- رابط إجابات ونتائج الطلاب -->
                                        <a href="{{ route(auth()->user()->role . '.exams.submissions', $exam->id) }}" class="ed-act-btn results" title="{{ __('سجل علامات وإجابات الطلاب') }}">
                                            <i class="fa-solid fa-chart-column"></i>
                                            <span>{{ __('النتائج') }}</span>
                                        </a>

                                        @if(auth()->user()->role === 'teacher')
                                            <!-- تحديد ظهور الاختبار للطلاب -->
                                            <a href="{{ route('teacher.access.index') }}?type=exam&id={{ $exam->id }}" class="ed-act-btn access" title="{{ __('تحديد ظهور هذا الاختبار للطلاب عبر اختيار صح [✓]') }}">
                                                <i class="fa-solid fa-user-check"></i>
                                            </a>
                                        @endif

                                        <!-- تعديل الاختبار والأسئلة -->
                                        <a href="{{ route(auth()->user()->role . '.exams.edit', $exam->id) }}" class="ed-act-btn edit" title="{{ __('تعديل بيانات الاختبار والأسئلة') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <!-- فورم الحذف مع تأكيد SweetAlert2 -->
                                        <form id="delete-form-{{ $exam->id }}" action="{{ route(auth()->user()->role . '.exams.destroy', $exam->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="ed-act-btn delete" onclick="confirmDelete({{ $exam->id }})" title="{{ __('حذف الاختبار') }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- رسالة عند عدم تطابق نتائج البحث -->
            <div id="noSearchResults" style="display: none; text-align: center; padding: 40px 20px;">
                <i class="fa-solid fa-magnifying-glass" style="font-size: 2.2rem; color: #cbd5e1; margin-bottom: 12px;"></i>
                <h4 style="font-weight: 800; color: #0f172a; margin-bottom: 6px;">{{ __('لا توجد اختبارات مطابقة لبحثك') }}</h4>
                <p style="color: #64748b; font-size: 0.88rem; margin: 0;">{{ __('جرب إدخال كلمات بحثية مختلفة أو اضغط مسح للعودة للقائمة الكاملة.') }}</p>
            </div>
        @else
            <!-- 5. حالة عدم توفر اختبارات (تصميم أكاديمي ملكي كلاسيكي) -->
            <div class="ed-classic-empty-box">
                <div class="empty-seal-shield">
                    <i class="fa-solid fa-file-pen"></i>
                </div>
                <h3>{{ __('لم يتم إعداد أي اختبارات تقييمية لهذا المساق حتى الآن') }}</h3>
                <p>
                    {{ __('يمكنك البدء بإنشاء أول اختبار إلكتروني أو ورقي، وتحديد معايير الوقت والأسئلة ودرجات النجاح واعتمادها للطلبة مباشرة.') }}
                </p>
                <div class="empty-cta-wrap">
                    <a href="{{ route(auth()->user()->role . '.exams.create') }}" class="ed-btn-royal-primary">
                        <i class="fa-solid fa-file-circle-plus"></i>
                        <span>{{ __('بناء اختبار تقييمي الآن') }}</span>
                    </a>
                </div>
            </div>
        @endif
    </main>

</div>

<!-- دوال التفاعل وتأكيد الحذف والبحث الفوري -->
<script>
function filterExamsTable() {
    const input = document.getElementById('examSearchInput');
    const filter = input ? input.value.toLowerCase().trim() : '';
    const rows = document.querySelectorAll('#examsRegistryTable tbody tr.exam-row');
    const clearBtn = document.getElementById('clearSearchBtn');
    const noResults = document.getElementById('noSearchResults');
    const badge = document.getElementById('displayedCountBadge');

    if (clearBtn) {
        clearBtn.style.display = filter ? 'inline-block' : 'none';
    }

    let visibleCount = 0;
    rows.forEach(row => {
        const text = row.getAttribute('data-search-text') || '';
        if (text.includes(filter)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    if (badge) {
        badge.textContent = visibleCount;
    }

    if (noResults) {
        noResults.style.display = (visibleCount === 0 && rows.length > 0) ? 'block' : 'none';
    }
}

function clearExamSearch() {
    const input = document.getElementById('examSearchInput');
    if (input) {
        input.value = '';
        filterExamsTable();
        input.focus();
    }
}

function confirmDelete(id) {
    Swal.fire({
        title: @json(__('هل أنت متأكد من رغبتك في حذف هذا الاختبار؟')),
        text: @json(__('سيتم حذف كافة الأسئلة وإجابات وتسليمات الطلاب المرتبطة به نهائياً!')),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: @json(__('نعم، تأكيد الحذف')),
        cancelButtonText: @json(__('إلغاء')),
        reverseButtons: true,
        customClass: {
            popup: 'swal2-custom-popup'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: @json(__('تم الاعتماد بنجاح!')),
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false,
        customClass: { popup: 'swal2-custom-popup' }
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: @json(__('تنبيه')),
        text: "{{ session('error') }}",
        customClass: { popup: 'swal2-custom-popup' }
    });
@endif
</script>

<style>
    /* المتغيرات الكلاسيكية المؤسسية */
    :root {
        --ed-royal-navy: #1e3a8a;
        --ed-dark-navy: #0f172a;
        --ed-navy-hover: #172554;
        --ed-academic-blue: #0284c7;
        --ed-emerald: #059669;
        --ed-amber: #d97706;
        --ed-crimson: #dc2626;
        --ed-border-slate: #e2e8f0;
        --ed-border-strong: #cbd5e1;
        --ed-bg-body: #f8fafc;
        --ed-text-main: #0f172a;
        --ed-text-muted: #64748b;
    }

    .ed-classic-exams-page {
        max-width: 1440px;
        margin: 0 auto;
        padding: 24px 20px 60px;
    }

    /* 1. الهيدر الأكاديمي الكلاسيكي الملكي */
    .ed-academic-header {
        background: #ffffff;
        border: 1px solid var(--ed-border-slate);
        border-top: 4px solid var(--ed-royal-navy);
        border-radius: 16px;
        padding: 26px 30px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
    }
    .header-main-details {
        flex: 1;
        min-width: 300px;
    }
    .ed-header-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--ed-text-muted);
        margin-bottom: 12px;
    }
    .ed-header-breadcrumbs a {
        color: var(--ed-royal-navy);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color 0.15s;
    }
    .ed-header-breadcrumbs a:hover {
        color: var(--ed-academic-blue);
    }
    .ed-header-breadcrumbs .sep {
        font-size: 0.68rem;
        color: #94a3b8;
    }
    .ed-header-breadcrumbs .active {
        color: #475569;
    }

    .ed-title-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 18px;
    }
    .ed-crest-avatar {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: var(--ed-royal-navy);
        border: 1.5px solid #bfdbfe;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.08);
    }
    .ed-subject-tag-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
        flex-wrap: wrap;
    }
    .ed-badge-subject {
        background: #eff6ff;
        color: var(--ed-royal-navy);
        border: 1px solid #bfdbfe;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .ed-badge-role {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 0.76rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .ed-page-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--ed-dark-navy);
        margin: 0 0 6px;
        letter-spacing: -0.3px;
    }
    .ed-page-desc {
        color: var(--ed-text-muted);
        font-size: 0.88rem;
        margin: 0;
        line-height: 1.55;
        max-width: 640px;
    }

    .header-action-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .ed-btn-royal-primary {
        background: var(--ed-royal-navy);
        color: #ffffff;
        border: 1px solid #1e3a8a;
        padding: 11px 22px;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(30, 58, 138, 0.2);
        transition: all 0.2s ease;
    }
    .ed-btn-royal-primary:hover {
        background: var(--ed-navy-hover);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(30, 58, 138, 0.3);
    }
    .ed-btn-royal-secondary {
        background: #ffffff;
        color: var(--ed-royal-navy);
        border: 1.5px solid #cbd5e1;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 0.86rem;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }
    .ed-btn-royal-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    /* تنبيه إداري */
    .ed-classic-alert-warning {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 14px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #92400e;
        font-size: 0.88rem;
    }
    .ed-classic-alert-warning .alert-icon {
        font-size: 1.2rem;
        color: var(--ed-amber);
    }

    /* 2. شريط المؤشرات الأكاديمية الكلاسيكي (KPI Strip) */
    .ed-kpi-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }
    .ed-kpi-card {
        background: #ffffff;
        border: 1px solid var(--ed-border-slate);
        border-radius: 14px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        transition: all 0.2s ease;
    }
    .ed-kpi-card:hover {
        border-color: var(--ed-border-strong);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
    }
    .kpi-icon-seal {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }
    .kpi-icon-seal.navy {
        background: #eff6ff;
        color: var(--ed-royal-navy);
        border: 1px solid #bfdbfe;
    }
    .kpi-icon-seal.blue {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .kpi-icon-seal.emerald {
        background: #ecfdf5;
        color: var(--ed-emerald);
        border: 1px solid #a7f3d0;
    }
    .kpi-icon-seal.amber {
        background: #fffbeb;
        color: var(--ed-amber);
        border: 1px solid #fde68a;
    }
    .kpi-info {
        display: flex;
        flex-direction: column;
    }
    .kpi-value {
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--ed-dark-navy);
        line-height: 1.1;
        margin-bottom: 4px;
    }
    .kpi-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--ed-text-muted);
    }

    /* 3. شريط الأدوات والبحث الكلاسيكي */
    .ed-classic-toolbar {
        background: #ffffff;
        border: 1px solid var(--ed-border-slate);
        border-radius: 12px;
        padding: 12px 18px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .toolbar-search-box {
        position: relative;
        flex: 1;
        max-width: 440px;
        min-width: 260px;
    }
    .toolbar-search-box .search-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.9rem;
    }
    .toolbar-search-box input {
        width: 100%;
        padding: 9px 38px 9px 34px;
        border-radius: 8px;
        border: 1.5px solid var(--ed-border-strong);
        background: #f8fafc;
        font-size: 0.86rem;
        font-weight: 600;
        color: var(--ed-text-main);
        outline: none;
        transition: all 0.2s ease;
    }
    .toolbar-search-box input:focus {
        background: #ffffff;
        border-color: var(--ed-royal-navy);
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    #clearSearchBtn {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        font-size: 0.85rem;
    }
    #clearSearchBtn:hover {
        color: #ef4444;
    }
    .toolbar-stats-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        color: #475569;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        border: 1px solid var(--ed-border-slate);
    }

    /* 4. كارد وجدول السجل الأكاديمي الكلاسيكي */
    .ed-classic-card {
        background: #ffffff;
        border: 1px solid var(--ed-border-slate);
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }
    .card-registry-header {
        padding: 18px 24px;
        background: #ffffff;
        border-bottom: 1px solid var(--ed-border-slate);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }
    .registry-title-box {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--ed-royal-navy);
    }
    .registry-title-box i {
        font-size: 1.15rem;
    }
    .registry-title-box h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--ed-dark-navy);
    }
    .registry-badge-count {
        background: #eff6ff;
        color: var(--ed-royal-navy);
        border: 1px solid #bfdbfe;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 800;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }
    .ed-classic-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }
    .ed-classic-table thead th {
        background: #0f172a;
        color: #f8fafc;
        font-weight: 800;
        font-size: 0.82rem;
        padding: 13px 18px;
        border: none;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }
    .ed-classic-table tbody td {
        padding: 14px 18px;
        border-bottom: 1px solid var(--ed-border-slate);
        font-size: 0.88rem;
        vertical-align: middle;
        background: #ffffff;
        transition: background 0.15s;
    }
    .ed-classic-table tbody tr:hover td {
        background: #f8fafc;
    }

    .ed-exam-title-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .exam-title-icon {
        color: var(--ed-royal-navy);
        font-size: 1.1rem;
        background: #eff6ff;
        padding: 8px;
        border-radius: 8px;
        border: 1px solid #dbeafe;
    }
    .exam-name {
        font-weight: 800;
        color: var(--ed-dark-navy);
        font-size: 0.94rem;
        display: block;
    }
    .exam-stage-meta {
        font-size: 0.76rem;
        color: #64748b;
        font-weight: 600;
        display: block;
        margin-top: 2px;
    }

    .ed-subject-badge {
        background: #f1f5f9;
        color: #1e293b;
        border: 1px solid #cbd5e1;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .ed-duration-pill {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .ed-questions-pill {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .ed-subm-badge {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    .ed-subm-badge.active {
        background: #eff6ff;
        color: var(--ed-royal-navy);
        border: 1px solid #bfdbfe;
    }
    .ed-subm-badge.active:hover {
        background: #dbeafe;
        color: #172554;
        box-shadow: 0 2px 6px rgba(30, 58, 138, 0.15);
    }
    .ed-subm-badge.empty {
        background: #f8fafc;
        color: #94a3b8;
        border: 1px solid #e2e8f0;
    }

    .ed-actions-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .ed-act-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 0.78rem;
        font-weight: 800;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .ed-act-btn.results {
        background: var(--ed-royal-navy);
        color: #ffffff;
        border-color: #1e3a8a;
    }
    .ed-act-btn.results:hover {
        background: var(--ed-navy-hover);
        color: #ffffff;
    }
    .ed-act-btn.access {
        background: #ecfdf5;
        color: var(--ed-emerald);
        border-color: #a7f3d0;
        padding: 6px 9px;
    }
    .ed-act-btn.access:hover {
        background: #d1fae5;
        color: #047857;
    }
    .ed-act-btn.edit {
        background: #f1f5f9;
        color: #475569;
        border-color: #cbd5e1;
        padding: 6px 9px;
    }
    .ed-act-btn.edit:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .ed-act-btn.delete {
        background: #fef2f2;
        color: var(--ed-crimson);
        border-color: #fecaca;
        padding: 6px 9px;
    }
    .ed-act-btn.delete:hover {
        background: #fee2e2;
        color: #991b1b;
    }

    /* 5. حالة عدم توفر اختبارات */
    .ed-classic-empty-box {
        text-align: center;
        padding: 60px 24px;
        background: #ffffff;
    }
    .empty-seal-shield {
        width: 72px;
        height: 72px;
        border-radius: 18px;
        background: linear-gradient(135deg, #eff6ff 0%, #f1f5f9 100%);
        color: var(--ed-royal-navy);
        border: 1.5px solid #bfdbfe;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        margin: 0 auto 18px;
        box-shadow: 0 4px 14px rgba(30, 58, 138, 0.08);
    }
    .ed-classic-empty-box h3 {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--ed-dark-navy);
        margin: 0 0 8px;
    }
    .ed-classic-empty-box p {
        font-size: 0.9rem;
        color: var(--ed-text-muted);
        max-width: 520px;
        margin: 0 auto 22px;
        line-height: 1.6;
    }
    .empty-cta-wrap {
        display: flex;
        justify-content: center;
    }

    /* تخصيص SweetAlert2 ليتماشى مع الصفحة */
    .swal2-custom-popup {
        font-family: inherit !important;
        border-radius: 16px !important;
        padding: 20px !important;
    }
    .swal2-title {
        font-size: 1.2rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .swal2-html-container {
        font-size: 0.9rem !important;
        color: #64748b !important;
    }
    .swal2-confirm, .swal2-cancel {
        border-radius: 10px !important;
        padding: 8px 20px !important;
        font-weight: 700 !important;
        font-size: 0.85rem !important;
    }

    /* مواءمة الشاشات الصغيرة */
    @media (max-width: 1024px) {
        .ed-kpi-strip {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 768px) {
        .ed-academic-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 18px;
        }
        .header-action-buttons {
            width: 100%;
        }
        .ed-btn-royal-primary, .ed-btn-royal-secondary {
            flex: 1;
            justify-content: center;
        }
        .ed-kpi-strip {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .ed-title-wrapper {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        .ed-crest-avatar {
            width: 48px;
            height: 48px;
            font-size: 1.4rem;
        }
        .ed-page-title {
            font-size: 1.35rem;
        }
    }
</style>
@endsection