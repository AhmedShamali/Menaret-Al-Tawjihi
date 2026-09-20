@extends('layouts.app')

@section('title', __('تسليمات الطلاب والتقييمات') . ' | ' . __(\App\Models\Setting::get('site_name', 'منارة التوجيهي')))

@section('content')
<div class="submissions-dashboard-clean">

    {{-- 1. رأس الصفحة: العنوان والإحصائيات --}}
    <div class="page-header-clean">
        <div class="header-titles">
            <h1 class="page-title-text">
                <i class="fa-solid fa-file-signature text-primary"></i>
                {{ __('تسليمات الطلاب والتقييمات') }}
                <span class="count-pill" id="visibleSubmissionsCount">{{ isset($submissions) ? count($submissions) : 0 }}</span>
            </h1>
            <p class="page-desc-text">
                {{ __('استعراض إجابات الطلبة، رصد الدرجات الأكاديمية، وإدارة أذونات الإعادة.') }}
            </p>
        </div>
    </div>

    @php
        $totalSubmissions = isset($submissions) ? $submissions->count() : 0;
        $gradedCount = isset($submissions) ? $submissions->where('status', 'graded')->count() : 0;
        $pendingCount = $totalSubmissions - $gradedCount;
        $cheatingCount = isset($submissions) ? $submissions->filter(fn($s) => $s->has_cheating_risk || $s->tab_switches_count > 0 || $s->screenshots_count > 0)->count() : 0;
    @endphp

    {{-- 2. بطاقات المؤشرات الأكاديمية الكلاسيكية --}}
    <div class="stats-row-clean" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="stat-card-clean" style="--card-accent: #1e3a8a;" onclick="setFilterTab('all')">
            <span class="stat-label">{{ __('إجمالي التسليمات المسجلة') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-navy">{{ $totalSubmissions }}</span>
                <i class="fa-solid fa-layer-group stat-icon text-navy"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #d97706;" onclick="setFilterTab('pending')">
            <span class="stat-label">{{ __('بانتظار التقييم والتصحيح') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number {{ $pendingCount > 0 ? 'text-amber' : '' }}">{{ $pendingCount }}</span>
                <i class="fa-solid fa-hourglass-start stat-icon text-amber"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #059669;" onclick="setFilterTab('graded')">
            <span class="stat-label">{{ __('تم تصحيحها واعتمادها') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-emerald">{{ $gradedCount }}</span>
                <i class="fa-solid fa-circle-check stat-icon text-emerald"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #dc2626;" onclick="setFilterTab('cheating')">
            <span class="stat-label">{{ __('تنبيهات اشتباه الغش') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number {{ $cheatingCount > 0 ? 'text-danger' : 'text-slate-400' }}">{{ $cheatingCount }}</span>
                <i class="fa-solid fa-shield-halved stat-icon {{ $cheatingCount > 0 ? 'text-danger' : 'text-slate-400' }}"></i>
            </div>
        </div>
    </div>

    {{-- 3. شريط البحث والفلاتر النظيف --}}
    <div class="toolbar-clean">
        <div class="search-box-clean">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="submissionsSearch" placeholder="{{ __('ابحث باسم الطالب، البريد، أو عنوان الاختبار...') }}" oninput="filterSubmissions()">
            <button type="button" id="clearSearchBtn" onclick="clearSearch()" class="clear-search" style="display: none;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="filter-pills-clean">
            <button type="button" class="filter-pill active" data-filter="all" onclick="setFilterTab('all')">
                {{ __('الكل') }} ({{ $totalSubmissions }})
            </button>
            <button type="button" class="filter-pill" data-filter="pending" onclick="setFilterTab('pending')">
                {{ __('قيد المراجعة') }} ({{ $pendingCount }})
            </button>
            <button type="button" class="filter-pill" data-filter="graded" onclick="setFilterTab('graded')">
                {{ __('تم التصحيح') }} ({{ $gradedCount }})
            </button>
            <button type="button" class="filter-pill {{ $cheatingCount > 0 ? 'text-danger-pill' : '' }}" data-filter="cheating" onclick="setFilterTab('cheating')">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ __('اشتباه غش') }} ({{ $cheatingCount }})
            </button>
        </div>
    </div>

    {{-- 4. الجدول النظيف الموحد بنمط رويال أكاديمي --}}
    <div class="table-card-clean">
        <div class="table-container-clean" style="overflow-x: auto;">
            <table class="data-table-clean">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th>{{ __('الطالب') }}</th>
                        <th>{{ __('الاختبار والمادة') }}</th>
                        <th style="width: 130px; text-align: center;">{{ __('الدرجة والنسبة') }}</th>
                        <th style="width: 140px; text-align: center;">{{ __('النزاهة والمراقبة') }}</th>
                        <th style="width: 110px; text-align: center;">{{ __('ظهور النتيجة') }}</th>
                        <th style="width: 120px; text-align: center;">{{ __('إذن الإعادة') }}</th>
                        <th style="width: 100px; text-align: center;">{{ __('الإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody id="submissionsTableBody">
                    @forelse($submissions ?? [] as $s)
                    @php
                        $studentDispName = (app()->getLocale() === 'en' && !empty($s->student?->name_en)) 
                            ? $s->student->name_en 
                            : ($s->student?->name_ar ?? ($s->student?->name ?? __('طالب')));
                        $examTitle = $s->exam?->title ?? __('اختبار بدون عنوان');

                        $teacherSubject = auth()->user()?->subject;
                        $subjectName = (app()->getLocale() === 'en' && !empty($teacherSubject?->name_en))
                            ? $teacherSubject->name_en
                            : ($teacherSubject?->name_ar ?? ($teacherSubject?->name ?? __('مادة عامة')));
                        $subjectIcon = $teacherSubject?->icon ?? '📚';

                        $totalPoints = $s->exam?->questions_sum_points ?? ($s->exam?->questions ? $s->exam->questions->sum('points') : 0);
                        $percentage = ($totalPoints > 0 && $s->status == 'graded') ? round(($s->total_earned_grade / $totalPoints) * 100) : 0;
                        $gradeRoute = route(auth()->user()->role . '.submissions.grade', $s->id);
                        $hasCheating = ($s->has_cheating_risk || $s->tab_switches_count > 0 || $s->screenshots_count > 0);
                    @endphp
                    <tr id="row_sub_{{ $s->id }}"
                        class="submission-row"
                        data-name="{{ mb_strtolower($studentDispName . ' ' . ($s->student?->name_ar ?? '')) }}"
                        data-email="{{ strtolower($s->student?->email ?? '') }}"
                        data-exam="{{ mb_strtolower($examTitle) }}"
                        data-status="{{ $s->status }}"
                        data-cheating="{{ $hasCheating ? '1' : '0' }}">
                        
                        <td style="text-align: center; color: #94a3b8; font-family: monospace; font-size: 0.8rem; font-weight: 700;">
                            #{{ $s->id }}
                        </td>

                        {{-- الطالب --}}
                        <td>
                            <div class="cell-student-info">
                                <div class="student-avatar-clean">
                                    <span class="avatar-initials">{{ mb_substr($studentDispName, 0, 2) }}</span>
                                </div>
                                <div class="student-details-clean">
                                    <div class="name-line">
                                        <strong style="color: #0f172a; font-size: 0.88rem;">{{ $studentDispName }}</strong>
                                    </div>
                                    <div class="meta-line">
                                        <span class="student-email" dir="ltr">{{ $s->student?->email ?? __('لا يوجد بريد') }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- الاختبار والمادة --}}
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <span style="font-weight: 700; color: #0f172a; font-size: 0.86rem;">
                                    <i class="fa-solid fa-file-signature text-primary" style="font-size: 0.78rem;"></i> {{ $examTitle }}
                                </span>
                                <span style="font-size: 0.72rem; color: #64748b;">
                                    {{ $subjectIcon }} {{ $subjectName }}
                                </span>
                                @if($s->retake_requested)
                                    <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.7rem; color: #c2410c; background: #fff7ed; border: 1px solid #fed7aa; padding: 1px 6px; border-radius: 4px; width: fit-content; margin-top: 2px;">
                                        <i class="fa-solid fa-bell"></i> {{ __('طلب إعادة الاختبار:') }} {{ $s->retake_request_notes ?? __('يرغب الطالب بفرصة إعادة') }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- الدرجة والنسبة --}}
                        <td style="text-align: center;">
                            @if($s->status == 'graded')
                                <div style="display: inline-flex; flex-direction: column; align-items: center; gap: 2px;">
                                    <span style="font-weight: 800; font-family: monospace; font-size: 0.95rem; color: #0f172a;">
                                        {{ $s->total_earned_grade }} <span style="color: #94a3b8; font-size: 0.78rem;">/ {{ $totalPoints }}</span>
                                    </span>
                                    <span class="status-pill {{ $percentage >= 50 ? 'status-active' : 'status-frozen' }}" style="font-size: 0.7rem; padding: 1px 6px;">
                                        {{ $percentage }}%
                                    </span>
                                </div>
                            @else
                                <span class="status-pill status-pending" style="font-size: 0.74rem;">
                                    <span class="dot"></span>
                                    <span>{{ __('بانتظار الرصد') }}</span>
                                </span>
                            @endif
                        </td>

                        {{-- النزاهة والمراقبة --}}
                        <td style="text-align: center;">
                            @if($hasCheating)
                                <div class="cheating-risk-pill" title="{{ __('تم رصد محاولات مغادرة أو تصوير للشاشة') }}">
                                    <span style="font-weight: 800; font-size: 0.72rem; display: inline-flex; align-items: center; gap: 4px;">
                                        <span class="pulse-indicator"></span>
                                        {{ __('اشتباه غش') }}
                                    </span>
                                    <span style="font-size: 0.65rem; color: #991b1b; font-weight: 600; margin-top: 2px;">
                                        {{ $s->tab_switches_count ?? 0 }} {{ __('مغادرة') }} | {{ $s->screenshots_count ?? 0 }} {{ __('لقطة') }}
                                    </span>
                                </div>
                            @else
                                <span class="status-pill status-active" style="font-size: 0.72rem; padding: 2px 8px;">
                                    <i class="fa-solid fa-shield-check" style="font-size: 0.75rem;"></i>
                                    <span>{{ __('جلسة نزيهة') }}</span>
                                </span>
                            @endif
                        </td>

                        {{-- ظهور النتيجة للطالب --}}
                        <td style="text-align: center;">
                            <div style="display: inline-flex; flex-direction: column; align-items: center; gap: 4px;">
                                @if($s->is_published)
                                    <span class="status-pill status-active" style="font-size: 0.7rem; padding: 1px 6px;">
                                        <i class="fa-solid fa-eye"></i> {{ __('معلنة للطالب') }}
                                    </span>
                                    <button type="button" onclick="togglePublishResult({{ $s->id }})" class="tbl-btn-pill btn-pill-muted" title="{{ __('انقر لحجب النتيجة عن الطالب') }}" style="font-size: 0.68rem; padding: 2px 6px;">
                                        <i class="fa-solid fa-eye-slash"></i> {{ __('حجب') }}
                                    </button>
                                @else
                                    <span class="status-pill status-pending" style="font-size: 0.7rem; padding: 1px 6px; background: #fff7ed; border-color: #ffedd5; color: #c2410c;">
                                        <i class="fa-solid fa-lock"></i> {{ __('محجوبة') }}
                                    </span>
                                    <button type="button" onclick="togglePublishResult({{ $s->id }})" class="tbl-btn-pill btn-pill-success" title="{{ __('انقر لإعلان النتيجة للطالب فوراً') }}" style="font-size: 0.68rem; padding: 2px 6px;">
                                        <i class="fa-solid fa-bullhorn"></i> {{ __('إعلان النتيجة') }}
                                    </button>
                                @endif
                            </div>
                        </td>

                        {{-- إذن الإعادة --}}
                        <td style="text-align: center;">
                            @if($s->allow_retake)
                                <button type="button" onclick="denyRetake({{ $s->id }})" title="{{ __('مسموح له بالإعادة حالياً (انقر لإلغاء الإذن)') }}" class="tbl-btn-pill btn-pill-success">
                                    <i class="fa-solid fa-unlock"></i> {{ __('مسموح للإعادة') }}
                                </button>
                            @elseif($s->retake_requested)
                                <button type="button" onclick="allowRetake({{ $s->id }})" title="{{ __('الموافقة على طلب الطالب بإعادة الاختبار') }}" class="tbl-btn-pill btn-pill-warning">
                                    <i class="fa-solid fa-circle-check"></i> {{ __('موافقة للإعادة') }}
                                </button>
                            @else
                                <button type="button" onclick="allowRetake({{ $s->id }})" title="{{ __('منح الطالب فرصة لإعادة الاختبار') }}" class="tbl-btn-pill btn-pill-muted">
                                    <i class="fa-solid fa-rotate-right"></i> {{ __('إتاحة الإعادة') }}
                                </button>
                            @endif
                        </td>

                        {{-- الإجراءات --}}
                        <td style="text-align: center;">
                            <a href="{{ $gradeRoute }}" class="tbl-btn tbl-btn-primary" style="display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
                                <span>{{ $s->status == 'graded' ? __('مراجعة') : __('تصحيح') }}</span>
                                <i class="fa-solid fa-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" style="font-size: 0.7rem;"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="empty-state-cell">
                            <i class="fa-regular fa-folder-open" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                            <span>{{ __('لا توجد أي تسليمات حتى الآن') }}</span>
                        </td>
                    </tr>
                    @endforelse

                    <tr id="noSubmissionsRow" style="display: none;">
                        <td colspan="8" class="empty-state-cell">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                            <span>{{ __('لا توجد نتائج مطابقة لشروط البحث.') }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .submissions-dashboard-clean {
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
    .tbl-btn-pill {
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 0.72rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border: 1px solid transparent;
        transition: all 0.15s;
    }
    .btn-pill-success {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #059669;
    }
    .btn-pill-success:hover {
        background: #d1fae5;
    }
    .btn-pill-warning {
        background: #fff7ed;
        border-color: #fed7aa;
        color: #ea580c;
    }
    .btn-pill-warning:hover {
        background: #ffedd5;
    }
    .btn-pill-muted {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
    }
    .btn-pill-muted:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .tbl-btn-primary {
        background: #1d4ed8;
        color: #ffffff;
        border: none;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
    }
    .tbl-btn-primary:hover {
        background: #1e40af;
    }
    .empty-state-cell {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
        font-size: 0.86rem;
    }
    .cheating-risk-pill {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        line-height: 1.2;
    }
    .text-danger-pill {
        border-color: #fca5a5 !important;
        color: #dc2626 !important;
    }
    .text-danger-pill.active {
        background: #dc2626 !important;
        color: #ffffff !important;
    }
    @keyframes pulseWarning {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.15); }
    }
    .pulse-indicator {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #dc2626;
        animation: pulseWarning 1.4s infinite;
    }
    @media (max-width: 768px) {
        .page-header-clean {
            flex-direction: column;
            align-items: flex-start;
        }
        .toolbar-clean {
            flex-direction: column;
            align-items: stretch;
        }
        .search-box-clean {
            width: 100%;
        }
        .filter-pills-clean {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 6px;
        }
    }
</style>

<script>
    let currentFilter = 'all';

    function setFilterTab(filterKey) {
        currentFilter = filterKey;
        document.querySelectorAll('.filter-pills-clean .filter-pill').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-filter') === filterKey);
        });
        filterSubmissions();
    }

    function clearSearch() {
        const input = document.getElementById('submissionsSearch');
        if (input) {
            input.value = '';
            document.getElementById('clearSearchBtn').style.display = 'none';
            filterSubmissions();
        }
    }

    function filterSubmissions() {
        const query = (document.getElementById('submissionsSearch').value || '').toLowerCase().trim();
        const clearBtn = document.getElementById('clearSearchBtn');
        if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';

        const rows = document.querySelectorAll('.submission-row');
        let count = 0;

        rows.forEach(row => {
            const name = (row.getAttribute('data-name') || '').toLowerCase();
            const email = (row.getAttribute('data-email') || '').toLowerCase();
            const exam = (row.getAttribute('data-exam') || '').toLowerCase();
            const status = row.getAttribute('data-status') || '';
            const cheating = row.getAttribute('data-cheating') || '0';

            const matchQuery = !query || name.includes(query) || email.includes(query) || exam.includes(query);
            let matchFilter = true;

            if (currentFilter === 'pending') {
                matchFilter = (status !== 'graded');
            } else if (currentFilter === 'graded') {
                matchFilter = (status === 'graded');
            } else if (currentFilter === 'cheating') {
                matchFilter = (cheating === '1');
            }

            if (matchQuery && matchFilter) {
                row.style.display = '';
                count++;
            } else {
                row.style.display = 'none';
            }
        });

        const counterEl = document.getElementById('visibleSubmissionsCount');
        if (counterEl) counterEl.innerText = count;

        const emptyRow = document.getElementById('noSubmissionsRow');
        if (emptyRow) {
            emptyRow.style.display = (count === 0 && rows.length > 0) ? '' : 'none';
        }
    }

    async function togglePublishResult(submissionId) {
        if (!window.Swal) return;
        try {
            const role = '{{ auth()->user()->role }}';
            const res = await axios.post(`/${role}/submissions/${submissionId}/toggle-publish`, {
                _token: '{{ csrf_token() }}'
            });
            Swal.fire({
                icon: 'success',
                title: res.data.message || @json(__('تم تحديث حالة الظهور بنجاح')),
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: @json(__('خطأ')),
                text: e.response?.data?.error || @json(__('تعذر تغيير حالة ظهور النتيجة'))
            });
        }
    }

    async function allowRetake(submissionId) {
        if (!window.Swal) return;
        Swal.fire({
            title: @json(__('السماح بإعادة الاختبار')),
            text: @json(__('هل أنت متأكد من منح الطالب فرصة جديدة لتقديم الاختبار؟')),
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#64748b',
            confirmButtonText: @json(__('نعم، اسمح بالإعادة')),
            cancelButtonText: @json(__('إلغاء'))
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const role = '{{ auth()->user()->role }}';
                    const res = await axios.post(`/${role}/submissions/${submissionId}/allow-retake`, {
                        _token: '{{ csrf_token() }}'
                    });
                    Swal.fire({ icon: 'success', title: @json(__('تمت الموافقة')), text: res.data.title, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } catch (e) {
                    Swal.fire({ icon: 'error', title: @json(__('خطأ')), text: e.response?.data?.error || @json(__('تعذر معالجة الطلب')) });
                }
            }
        });
    }

    async function denyRetake(submissionId) {
        if (!window.Swal) return;
        Swal.fire({
            title: @json(__('إلغاء إذن الإعادة')),
            text: @json(__('هل ترغب في قفل الاختبار ومنع إعادة المحاولة لهذا الطالب؟')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: @json(__('نعم، منع الإعادة')),
            cancelButtonText: @json(__('تراجع'))
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const role = '{{ auth()->user()->role }}';
                    const res = await axios.post(`/${role}/submissions/${submissionId}/deny-retake`, {
                        _token: '{{ csrf_token() }}'
                    });
                    Swal.fire({ icon: 'success', title: @json(__('تم إلغاء الإذن')), text: res.data.title, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } catch (e) {
                    Swal.fire({ icon: 'error', title: @json(__('خطأ')), text: e.response?.data?.error || @json(__('تعذر معالجة الطلب')) });
                }
            }
        });
    }
</script>
@endsection
