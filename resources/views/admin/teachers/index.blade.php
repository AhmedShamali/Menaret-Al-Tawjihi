@extends('layouts.app')

@section('title', __('قائمة المعلمين') . ' | ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
<div class="teachers-dashboard-clean">

    {{-- 1. رأس الصفحة: العنوان والإجراءات --}}
    <div class="page-header-clean">
        <div class="header-titles">
            <h1 class="page-title-text">
                <i class="fa-solid fa-chalkboard-user" style="color: #1e3a8a;"></i>
                {{ __('سجل الكادر التعليمي') }}
                <span class="count-pill" id="visibleTeachersCount">{{ count($teachers) }}</span>
            </h1>
            <p class="page-desc-text">
                {{ __('إدارة ومتابعة المعلمين، المواد المسندة، وقنوات التواصل المباشر.') }}
            </p>
        </div>

        <div class="header-actions-group">
            <a href="{{ route('admin.teachers.salaries') }}" class="btn-clean btn-outline" title="{{ __('رواتب ومستحقات المعلمين') }}">
                <i class="fa-solid fa-money-bill-wave text-emerald"></i>
                <span>{{ __('مسير الرواتب') }}</span>
            </a>
            <a href="{{ route('admin.teachers.info') }}" class="btn-clean btn-outline" title="{{ __('الدليل الموسع للكادر') }}">
                <i class="fa-solid fa-address-book text-indigo"></i>
                <span>{{ __('دليل المعلمين') }}</span>
            </a>
            <a href="{{ route('admin.teachers.create') }}" class="btn-clean btn-primary">
                <i class="fa-solid fa-plus"></i>
                <span>{{ __('إضافة معلم جديد') }}</span>
            </a>
        </div>
    </div>

    {{-- 2. بطاقات المؤشرات الأكاديمية الكلاسيكية (KPIs) --}}
    <div class="stats-row-clean">
        <div class="stat-card-clean" style="--card-accent: #1e3a8a;" onclick="setTeacherFilter('all')">
            <span class="stat-label">{{ __('إجمالي المعلمين المسجلين') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-navy">{{ count($teachers) }}</span>
                <i class="fa-solid fa-user-tie stat-icon text-navy"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #059669;" onclick="setTeacherFilter('assigned')">
            <span class="stat-label">{{ __('معلمون بمهام تدريسية نشطة') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-emerald">{{ $teachers->whereNotNull('subject_id')->count() }}</span>
                <i class="fa-solid fa-book-open-reader stat-icon text-emerald"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #d97706;" onclick="setTeacherFilter('general')">
            <span class="stat-label">{{ __('كادر عام وإشراف') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-amber">{{ $teachers->whereNull('subject_id')->count() }}</span>
                <i class="fa-solid fa-clipboard-user stat-icon text-amber"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #6366f1;" onclick="setTeacherFilter('all')">
            <span class="stat-label">{{ __('انضموا خلال هذا العام') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-indigo">{{ $teachers->where('created_at', '>=', now()->startOfYear())->count() }}</span>
                <i class="fa-solid fa-calendar-check stat-icon text-indigo"></i>
            </div>
        </div>
    </div>

    {{-- 3. شريط البحث والفلاتر النظيف --}}
    <div class="toolbar-clean">
        <div class="search-box-clean">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="teacherSearchInput" placeholder="{{ __('بحث باسم المعلم، التخصص، أو البريد الإلكتروني...') }}" oninput="filterTeachers()">
            <button type="button" id="clearTeacherSearchBtn" onclick="clearTeacherSearch()" class="clear-search" style="display: none;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="filter-pills-clean">
            <button type="button" class="filter-pill active" data-filter="all" onclick="setTeacherFilter('all')">
                {{ __('كافة المعلمين') }} ({{ count($teachers) }})
            </button>
            <button type="button" class="filter-pill" data-filter="assigned" onclick="setTeacherFilter('assigned')">
                {{ __('مواد مخصصة') }} ({{ $teachers->whereNotNull('subject_id')->count() }})
            </button>
            <button type="button" class="filter-pill" data-filter="general" onclick="setTeacherFilter('general')">
                {{ __('إشراف عام') }} ({{ $teachers->whereNull('subject_id')->count() }})
            </button>
        </div>
    </div>

    {{-- 4. الجدول النظيف الموحد بنمط رويال أكاديمي --}}
    <div class="table-card-clean">
        <div class="table-container-clean">
            <table class="data-table-clean">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">#</th>
                        <th>{{ __('المعلم') }}</th>
                        <th style="width: 180px;">{{ __('المادة التعليمية') }}</th>
                        <th style="width: 220px;">{{ __('البريد الإلكتروني') }}</th>
                        <th style="width: 140px;">{{ __('تاريخ الانضمام') }}</th>
                        <th style="width: 110px; text-align: center;">{{ __('الحالة') }}</th>
                        <th style="width: 130px; text-align: center;">{{ __('الإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody id="teachersTableBody">
                    @forelse($teachers as $teacher)
                    @php
                        $subjectTitle = (app()->getLocale() === 'en' && !empty($teacher->subject?->name_en)) ? $teacher->subject->name_en : ($teacher->subject?->name_ar ?? ($teacher->subject_name ?? __('إشراف عام')));
                        $hasSubject = !empty($teacher->subject_id);
                        $teacherDispName = (app()->getLocale() === 'en' && !empty($teacher->name_en)) ? $teacher->name_en : $teacher->name;
                    @endphp
                    <tr id="row_teacher_{{ $teacher->id }}"
                        class="teacher-row"
                        data-name="{{ mb_strtolower($teacher->name . ' ' . ($teacher->name_en ?? '')) }}"
                        data-email="{{ strtolower($teacher->email) }}"
                        data-subject="{{ mb_strtolower($subjectTitle) }}"
                        data-has-subject="{{ $hasSubject ? '1' : '0' }}">
                        
                        <td style="text-align: center; color: #94a3b8; font-family: monospace; font-size: 0.8rem; font-weight: 700;">
                            #{{ $teacher->id }}
                        </td>

                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                @if(!empty($teacher->photo))
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacherDispName }}" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0;">
                                @else
                                    <div style="width: 34px; height: 34px; border-radius: 50%; background: #eff6ff; color: #1e3a8a; border: 1px solid #bfdbfe; display: grid; place-items: center; font-size: 0.8rem; font-weight: 800; flex-shrink: 0;">
                                        {{ mb_substr($teacherDispName, 0, 1) }}
                                    </div>
                                @endif
                                <div style="display: flex; flex-direction: column;">
                                    <a href="{{ route('admin.teachers.show', $teacher->id) }}" style="color: #0f172a; font-weight: 700; text-decoration: none; font-size: 0.88rem;">
                                        {{ $teacherDispName }}
                                    </a>
                                    @if(!empty($teacher->phone))
                                        <span style="font-size: 0.72rem; color: #64748b; font-family: monospace;">{{ $teacher->phone }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td>
                            @if($hasSubject)
                                <span class="status-pill status-info">
                                    <span class="dot"></span>
                                    {{ __($subjectTitle) }}
                                </span>
                            @else
                                <span class="status-pill status-pending">
                                    <span class="dot"></span>
                                    {{ __('إشراف عام') }}
                                </span>
                            @endif
                        </td>

                        <td style="color: #475569; font-size: 0.82rem;">
                            {{ $teacher->email }}
                        </td>

                        <td style="color: #64748b; font-size: 0.82rem;">
                            <i class="fa-regular fa-calendar" style="color: #94a3b8; margin-inline-end: 4px;"></i>
                            {{ $teacher->created_at ? $teacher->created_at->format('Y-m-d') : '—' }}
                        </td>

                        <td style="text-align: center;">
                            <span class="status-pill status-active">
                                <span class="dot"></span>
                                {{ __('نشط') }}
                            </span>
                        </td>

                        <td>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <a href="{{ route('admin.teachers.show', $teacher->id) }}"
                                   class="tbl-btn-icon"
                                   title="{{ __('الملف الشخصي') }}">
                                    <i class="fa-regular fa-folder-open"></i>
                                </a>

                                <a href="{{ route('admin.teachers.direct_chat', $teacher->id) }}"
                                   class="tbl-btn-icon"
                                   title="{{ __('مراسلة المعلم') }}">
                                    <i class="fa-regular fa-comment-dots text-indigo"></i>
                                </a>

                                <a href="{{ route('admin.teachers.salaries', ['teacher_id' => $teacher->id]) }}"
                                   class="tbl-btn-icon"
                                   title="{{ __('مستحقات وراتب المعلم') }}">
                                    <i class="fa-solid fa-money-bill-wave text-emerald"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 0.88rem;">
                            <i class="fa-solid fa-chalkboard-user" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                            <span>{{ __('لا يوجد معلمون مسجلون حالياً في المنصة.') }}</span>
                        </td>
                    </tr>
                    @endforelse

                    <tr id="noTeacherResultsRow" style="display: none;">
                        <td colspan="7" style="text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 0.88rem;">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                            <span>{{ __('لا توجد نتائج مطابقة لشروط البحث.') }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let teacherFilter = 'all';

    function setTeacherFilter(filterKey) {
        teacherFilter = filterKey;
        document.querySelectorAll('.filter-pills-clean .filter-pill').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-filter') === filterKey);
        });
        filterTeachers();
    }

    function filterTeachers() {
        const query = (document.getElementById('teacherSearchInput').value || '').trim().toLowerCase();
        const clearBtn = document.getElementById('clearTeacherSearchBtn');
        if (clearBtn) {
            clearBtn.style.display = query ? 'block' : 'none';
        }

        const rows = document.querySelectorAll('.teacher-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const email = row.getAttribute('data-email') || '';
            const subject = row.getAttribute('data-subject') || '';
            const hasSubject = row.getAttribute('data-has-subject');

            const matchesQuery = !query || name.includes(query) || email.includes(query) || subject.includes(query);

            let matchesFilter = true;
            if (teacherFilter === 'assigned') {
                matchesFilter = hasSubject === '1';
            } else if (teacherFilter === 'general') {
                matchesFilter = hasSubject === '0';
            }

            if (matchesQuery && matchesFilter) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noTeacherResultsRow');
        if (noResults) {
            noResults.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }

        const countBadge = document.getElementById('visibleTeachersCount');
        if (countBadge) {
            countBadge.innerText = visibleCount;
        }
    }

    function clearTeacherSearch() {
        const input = document.getElementById('teacherSearchInput');
        if (input) {
            input.value = '';
            filterTeachers();
        }
    }
</script>

<style>
    .teachers-dashboard-clean {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    .page-header-clean {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }
    .page-title-text {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .count-pill {
        font-size: 0.78rem;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 2px 8px;
        border-radius: 12px;
    }
    .page-desc-text {
        font-size: 0.84rem;
        color: #64748b;
        margin: 3px 0 0;
    }
    .header-actions-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
</style>
@endsection
