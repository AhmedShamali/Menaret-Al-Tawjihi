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

    {{-- شبكة بطاقات المواد الأكاديمية الكلاسيكية --}}
    <div class="ed-subjects-grid">
        @forelse($subjects as $subject)
            @php
                $color = $subject->color ?: '#1e3a8a';
                $lessonsCount = ($subject->educational_contents_count ?? 0) + ($subject->contents_count ?? 0);
                $examsCount = $subject->exams_count ?? 0;
            @endphp
            <article class="ed-subject-card">
                <div class="ed-card-accent-bar" style="background-color: {{ $color }};"></div>

                <div class="ed-card-header">
                    <div class="ed-card-meta">
                        @if(!empty($subject->subject_key))
                            <span class="ed-subject-code">{{ $subject->subject_key }}</span>
                        @else
                            <span class="ed-subject-code">{{ __('مقرر معتمد') }}</span>
                        @endif

                        @if($subject->stage)
                            <span class="ed-stage-pill">{{ $subject->stage->label_ar ?? ($subject->stage->name_ar ?? $subject->stage->name) }}</span>
                        @endif
                    </div>

                    <div class="ed-subject-icon-box" style="color: {{ $color }}; background-color: {{ $color }}15; border-color: {{ $color }}30;">
                        <i class="{{ $subject->icon ?: 'fa-solid fa-book-bookmark' }}"></i>
                    </div>
                </div>

                <div class="ed-card-body">
                    <h2 class="ed-subject-name">
                        <a href="{{ route('student.subjects.show', $subject->id) }}">{{ $subject->name_ar ?? $subject->name }}</a>
                    </h2>

                    @if(!empty($subject->description))
                        <p class="ed-subject-desc">{{ Str::limit($subject->description, 95) }}</p>
                    @endif

                    <div class="ed-metrics-row">
                        <div class="ed-metric-cell">
                            <i class="fa-solid fa-circle-play"></i>
                            <div>
                                <span class="ed-m-num">{{ $lessonsCount }}</span>
                                <span class="ed-m-label">{{ __('دروس') }}</span>
                            </div>
                        </div>

                        <div class="ed-metric-cell">
                            <i class="fa-solid fa-file-pen"></i>
                            <div>
                                <span class="ed-m-num">{{ $examsCount }}</span>
                                <span class="ed-m-label">{{ __('اختبارات') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ed-card-footer">
                    <a href="{{ route('student.subjects.show', $subject->id) }}" class="ed-btn-enter">
                        <span>{{ __('دخول المقرر والدروس') }}</span>
                        <i class="fa-solid fa-arrow-left arrow-icon"></i>
                    </a>
                </div>
            </article>
        @empty
            <div class="ed-empty-catalog">
                <div class="ed-empty-icon">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h3>{{ __('لا توجد مواد مسجلة حالياً') }}</h3>
                <p>{{ __('لا توجد مقررات دراسية مسجلة لحسابك حالياً، يرجى مراجعة إدارة المنصة لربط مواد مرحلتك.') }}</p>
                <a href="{{ route('student.courses.catalog') }}" class="ed-btn-catalog">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>{{ __('باقات المواد والاشتراك') }}</span>
                </a>
            </div>
        @endforelse
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
    gap: 20px;
    width: 100%;
}

/* Card */
.ed-subject-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.ed-subject-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
}

.ed-card-accent-bar {
    height: 4px;
    width: 100%;
}

.ed-card-header {
    padding: 20px 20px 12px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
}

.ed-card-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.ed-subject-code {
    display: inline-block;
    background: #f1f5f9;
    color: #475569;
    font-size: 0.75rem;
    font-weight: 700;
    font-family: monospace;
    padding: 3px 8px;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    width: fit-content;
}

.ed-stage-pill {
    font-size: 0.74rem;
    color: #0284c7;
    font-weight: 600;
}

.ed-subject-icon-box {
    width: 46px;
    height: 46px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 1.25rem;
    border: 1px solid;
    flex-shrink: 0;
}

.ed-card-body {
    padding: 0 20px 16px;
    flex: 1;
}

.ed-subject-name {
    font-size: 1.15rem;
    font-weight: 800;
    margin: 0 0 8px;
    line-height: 1.35;
}

.ed-subject-name a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.15s ease;
}

.ed-subject-name a:hover {
    color: #1e3a8a;
}

.ed-subject-desc {
    font-size: 0.82rem;
    color: #64748b;
    line-height: 1.55;
    margin: 0 0 16px;
}

.ed-metrics-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 8px;
    padding: 10px 14px;
}

.ed-metric-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.ed-metric-cell i {
    font-size: 1rem;
    color: #1e3a8a;
}

.ed-m-num {
    font-size: 0.95rem;
    font-weight: 800;
    color: #0f172a;
    font-family: monospace;
    display: block;
    line-height: 1.1;
}

.ed-m-label {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 600;
}

.ed-card-footer {
    padding: 14px 20px;
    background: #fafafa;
    border-top: 1px solid #f1f5f9;
}

.ed-btn-enter {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #1e3a8a;
    color: #ffffff;
    text-decoration: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 0.86rem;
    font-weight: 700;
    transition: background 0.15s ease, transform 0.15s ease;
}

.ed-btn-enter:hover {
    background: #172554;
    color: #ffffff;
}

html[dir="ltr"] .arrow-icon {
    transform: rotate(180deg);
}

/* Empty State */
.ed-empty-catalog {
    grid-column: 1 / -1;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 60px 24px;
    text-align: center;
}

.ed-empty-icon {
    font-size: 3rem;
    color: #94a3b8;
    margin-bottom: 16px;
}

.ed-empty-catalog h3 {
    font-size: 1.25rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 8px;
}

.ed-empty-catalog p {
    font-size: 0.88rem;
    color: #64748b;
    max-width: 480px;
    margin: 0 auto 20px;
    line-height: 1.6;
}

.ed-btn-catalog {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #1e3a8a;
    color: #ffffff;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.88rem;
    font-weight: 700;
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
    .ed-subjects-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
