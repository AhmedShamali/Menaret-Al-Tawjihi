@extends('layouts.app')

@section('title', __('موادي ومقرراتي الدراسية') . ' | ' . __('منارة التوجيهي'))

@section('content')
<div class="ed-subjects-catalog">
    {{-- هيدر المقررات الأكاديمي الكلاسيكي --}}
    <header class="ed-catalog-header">
        <div class="ed-catalog-header-main">
            <div class="ed-academic-badge">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>{{ __('الثانوية العامة - فلسطين') }}</span>
                <span class="badge-dot">•</span>
                <span>{{ __('المنهاج الفلسطيني المعتمد - دورة') }} 2026</span>
            </div>
            <h1 class="ed-catalog-title">{{ __('موادي ومقرراتي الدراسية') }}</h1>
            <p class="ed-catalog-desc">
                {{ __('استعراض المقررات المعتمدة والمحاضرات المرئية والاختبارات التقييمية لمرحلتك الدراسية') }}
            </p>
        </div>

        {{-- إحصائيات سريعة كلاسيكية --}}
        <div class="ed-catalog-stats">
            <div class="ed-cstat-item">
                <span class="ed-cstat-num">{{ $subjects->count() }}</span>
                <span class="ed-cstat-label">{{ __('إجمالي المقررات') }}</span>
            </div>
            <div class="ed-cstat-divider"></div>
            <div class="ed-cstat-item">
                <span class="ed-cstat-num">{{ $subjects->sum('educational_contents_count') + $subjects->sum('contents_count') }}</span>
                <span class="ed-cstat-label">{{ __('إجمالي المحاضرات') }}</span>
            </div>
            <div class="ed-cstat-divider"></div>
            <div class="ed-cstat-item">
                <span class="ed-cstat-num">{{ $subjects->sum('exams_count') }}</span>
                <span class="ed-cstat-label">{{ __('إجمالي الاختبارات') }}</span>
            </div>
        </div>
    </header>

    {{-- جدول المقررات الأكاديمية الكلاسيكي (بدون كاردات عائمة - طراز جامعي معتمد) --}}
    <div class="ed-academic-table-container">
        <div class="table-card-head">
            <div class="table-card-title">
                <i class="fa-solid fa-table-list" style="color: #1e3a8a;"></i>
                <h3>{{ __('سجل المقررات والمباحث الدراسية المقررة') }}</h3>
            </div>
            <span class="table-card-sub">{{ __('المنهاج الفلسطيني المعتمد - دورة') }} 2026</span>
        </div>

        <div class="table-responsive">
            <table class="academic-roster-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th style="width: 130px;">{{ __('رمز المساق') }}</th>
                        <th>{{ __('المبحث والمقرر الدراسي') }}</th>
                        <th>{{ __('الفرع الأكاديمي') }}</th>
                        <th style="text-align: center; width: 120px;">{{ __('الدروس') }}</th>
                        <th style="text-align: center; width: 120px;">{{ __('التقييمات') }}</th>
                        <th style="text-align: center; width: 160px;">{{ __('الإجراء الأكاديمي') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $index => $subject)
                        @php
                            $color = $subject->color ?: '#1e3a8a';
                            $lessonsCount = ($subject->educational_contents_count ?? 0) + ($subject->contents_count ?? 0);
                            $examsCount = $subject->exams_count ?? 0;
                            $subjectName = (app()->getLocale() === 'en' && !empty($subject->name_en)) ? $subject->name_en : ($subject->name_ar ?? $subject->name);
                        @endphp
                        <tr>
                            <td style="text-align: center; font-weight: 700; color: #64748b;">{{ $index + 1 }}</td>
                            <td>
                                <span class="subject-code-tag" style="background: {{ $color }}15; color: {{ $color }}; border: 1px solid {{ $color }}35;">
                                    {{ $subject->subject_key ?: 'CRS-' . ($subject->id) }}
                                </span>
                            </td>
                            <td>
                                <div class="subject-main-cell">
                                    <div class="subject-mini-icon" style="color: {{ $color }}; background: {{ $color }}10;">
                                        <i class="{{ $subject->icon ?: 'fa-solid fa-book-bookmark' }}"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('student.subjects.show', $subject->id) }}" class="subject-title-link">
                                            {{ $subjectName }}
                                        </a>
                                        @if(!empty($subject->description))
                                            <p class="subject-mini-desc">{{ Str::limit($subject->description, 80) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="stage-name-badge">
                                    {{ $subject->stage->label_ar ?? ($subject->stage->name_ar ?? __('توجيهي')) }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="metric-pill">
                                    <i class="fa-solid fa-circle-play" style="color: #1e3a8a;"></i>
                                    {{ $lessonsCount }} {{ __('درس') }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="metric-pill">
                                    <i class="fa-solid fa-file-pen" style="color: #059669;"></i>
                                    {{ $examsCount }} {{ __('اختبار') }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('student.subjects.show', $subject->id) }}" class="academic-enter-btn">
                                    <span>{{ __('دخول المقرر') }}</span>
                                    <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="ed-empty-state-table">
                                    <i class="fa-solid fa-folder-open" style="font-size: 2.4rem; color: #94a3b8; margin-bottom: 10px;"></i>
                                    <h4>{{ __('لا توجد مقررات دراسية مسجلة حالياً') }}</h4>
                                    <p>{{ __('يرجى مراجعة إدارة المنصة لربط مواد مرحلتك أو استعراض باقات الاشتراك.') }}</p>
                                    <a href="{{ route('courses.catalog') }}" class="academic-enter-btn" style="display: inline-flex; margin-top: 10px;">
                                        <i class="fa-solid fa-layer-group"></i> {{ __('باقات المواد والاشتراك') }}
                                    </a>
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
/* ==========================================================
   CLASSIC ACADEMIC SUBJECTS CATALOG STYLES (100% RESPONSIVE)
   ========================================================== */
.ed-subjects-catalog {
    width: 100%;
    margin: 0;
    padding: 0 0 40px;
    box-sizing: border-box;
}

/* Header */
.ed-catalog-header {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 24px 28px;
    margin-bottom: 24px;
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
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.badge-dot {
    color: #93c5fd;
}

.ed-catalog-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
    letter-spacing: -0.01em;
}

.ed-catalog-desc {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0;
    max-width: 620px;
    line-height: 1.5;
}

/* Stats */
.ed-catalog-stats {
    display: flex;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 18px;
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
    font-weight: 600;
    margin-top: 2px;
}

.ed-cstat-divider {
    width: 1px;
    height: 28px;
    background: #e2e8f0;
}

/* Grid */
.ed-subjects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
/* Container & Table */
.ed-academic-table-container {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.table-card-head {
    padding: 16px 22px;
    background: #f8fafc;
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
    gap: 10px;
}

.table-card-title h3 {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.table-card-sub {
    font-size: 0.8rem;
    font-weight: 700;
    color: #1e3a8a;
    background: #eff6ff;
    padding: 4px 10px;
    border-radius: 6px;
    border: 1px solid #bfdbfe;
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.academic-roster-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.88rem;
    color: #1e293b;
}

.academic-roster-table th {
    background: #f8fafc;
    color: #334155;
    font-size: 0.82rem;
    font-weight: 800;
    padding: 14px 18px;
    border-bottom: 2px solid #cbd5e1;
    white-space: nowrap;
}

.academic-roster-table td {
    padding: 14px 18px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
}

.academic-roster-table tr:hover {
    background: #f8fafc;
}

.subject-code-tag {
    display: inline-block;
    font-family: monospace;
    font-weight: 800;
    font-size: 0.78rem;
    padding: 4px 8px;
    border-radius: 5px;
    letter-spacing: 0.3px;
    white-space: nowrap;
}

.subject-main-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.subject-mini-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.subject-title-link {
    font-weight: 800;
    color: #0f172a;
    text-decoration: none;
    font-size: 0.95rem;
    display: block;
    line-height: 1.3;
}

.subject-title-link:hover {
    color: #1e3a8a;
    text-decoration: underline;
}

.subject-mini-desc {
    margin: 3px 0 0;
    font-size: 0.76rem;
    color: #64748b;
    line-height: 1.35;
}

.stage-name-badge {
    font-size: 0.8rem;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    padding: 4px 10px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    white-space: nowrap;
}

.metric-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    font-size: 0.82rem;
    color: #334155;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 4px 10px;
    border-radius: 6px;
    white-space: nowrap;
}

.academic-enter-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #1e3a8a;
    color: #ffffff !important;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.82rem;
    padding: 7px 16px;
    border-radius: 6px;
    transition: background 0.15s ease;
    white-space: nowrap;
}

.academic-enter-btn:hover {
    background: #1e40af;
}

.ed-empty-state-table {
    text-align: center;
    padding: 50px 20px;
}

.ed-empty-state-table h4 {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}

.ed-empty-state-table p {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0 0 16px;
}

@media (max-width: 768px) {
    .ed-catalog-header {
        flex-direction: column;
        align-items: flex-start;
        padding: 18px 20px;
    }
    .ed-catalog-stats {
        width: 100%;
        justify-content: space-around;
        box-sizing: border-box;
    }
    .academic-roster-table th,
    .academic-roster-table td {
        padding: 10px 12px;
    }
}
</style>
@endsection
