@extends('layouts.app')

@section('content')
<style>
    /* Scope Dashboard UI to prevent Layout Contamination */
    .st-dashboard {
        --st-primary: #2563eb;
        --st-primary-dark: #1d4ed8;
        --st-primary-soft: #eff6ff;
        --st-accent: #06b6d4;
        --st-success: #10b981;
        --st-success-soft: #ecfdf5;
        --st-warning: #f59e0b;
        --st-text-dark: #0f172a;
        --st-text-muted: #64748b;
        --st-bg-surface: #ffffff;
        --st-border: #e2e8f0;
        --st-radius-lg: 24px;
        --st-radius-md: 16px;
        --st-shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.03), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        --st-shadow-hover: 0 20px 30px -10px rgba(37, 99, 235, 0.12);

        display: flex;
        flex-direction: column;
        gap: 32px;
        color: var(--st-text-dark);
        font-family: inherit;
    }

    /* 1. Hero Welcome Card */
    .st-welcome-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%);
        border-radius: var(--st-radius-lg);
        padding: 40px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(37, 99, 235, 0.35);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
    }

    .st-welcome-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, rgba(255,255,255,0) 70%);
        pointer-events: none;
    }

    .st-welcome-content {
        position: relative;
        z-index: 2;
        max-width: 580px;
    }

    .st-welcome-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 600;
        margin-bottom: 16px;
        color: #e0f2fe;
    }

    .st-welcome-title {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1.25;
        margin: 0 0 12px 0;
        letter-spacing: -0.5px;
    }

    .st-welcome-desc {
        font-size: 1rem;
        opacity: 0.9;
        margin: 0;
        line-height: 1.6;
        color: #cbd5e1;
    }

    /* Stats Section */
    .st-stats-wrapper {
        display: flex;
        gap: 16px;
        position: relative;
        z-index: 2;
    }

    .st-stat-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 18px 24px;
        border-radius: 20px;
        min-width: 150px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: transform 0.3s ease;
    }

    .st-stat-card:hover {
        transform: translateY(-3px);
        background: rgba(255, 255, 255, 0.15);
    }

    .st-stat-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #93c5fd;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .st-stat-value {
        font-size: 1.8rem;
        font-weight: 900;
        color: #ffffff;
        line-height: 1;
    }

    /* Layout Grid */
    .st-main-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 32px;
    }

    .st-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .st-section-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--st-text-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .st-icon-pill {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 1.1rem;
    }

    /* Available Exams Cards */
    .st-exams-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    .st-exam-card {
        background: var(--st-bg-surface);
        border: 1px solid var(--st-border);
        border-radius: var(--st-radius-md);
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--st-shadow-subtle);
        position: relative;
        overflow: hidden;
    }

    .st-exam-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--st-primary), var(--st-accent));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .st-exam-card:hover {
        transform: translateY(-5px);
        border-color: rgba(37, 99, 235, 0.3);
        box-shadow: var(--st-shadow-hover);
    }

    .st-exam-card:hover::before {
        opacity: 1;
    }

    .st-exam-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--st-primary-soft);
        color: var(--st-primary-dark);
        font-weight: 700;
        font-size: 0.75rem;
        padding: 6px 14px;
        border-radius: 50px;
    }

    .st-exam-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--st-text-dark);
        margin: 14px 0 16px 0;
        line-height: 1.4;
    }

    .st-exam-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
        font-size: 0.85rem;
        color: var(--st-text-muted);
        margin-bottom: 20px;
    }

    .st-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }

    .st-btn-primary {
        background: linear-gradient(135deg, var(--st-primary) 0%, var(--st-primary-dark) 100%);
        color: #ffffff !important;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        border: none;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .st-btn-primary:hover {
        opacity: 0.95;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        transform: scale(1.01);
    }

    /* Completed Exams List */
    .st-completed-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .st-completed-card {
        background: var(--st-bg-surface);
        border: 1px solid var(--st-border);
        border-radius: var(--st-radius-md);
        padding: 18px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
        box-shadow: var(--st-shadow-subtle);
    }

    .st-completed-card:hover {
        border-color: #cbd5e1;
        background-color: #f8fafc;
    }

    .st-completed-title {
        font-weight: 700;
        font-size: 1rem;
        color: var(--st-text-dark);
        margin-bottom: 4px;
    }

    .st-completed-date {
        font-size: 0.8rem;
        color: var(--st-text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .st-score-badge {
        background: var(--st-success-soft);
        color: var(--st-success);
        font-weight: 800;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 1rem;
        border: 1px solid rgba(16, 185, 129, 0.2);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Empty States */
    .st-empty-box {
        background: var(--st-bg-surface);
        border: 2px dashed var(--st-border);
        border-radius: var(--st-radius-md);
        padding: 40px 20px;
        text-align: center;
        color: var(--st-text-muted);
    }

    .st-empty-icon {
        width: 60px;
        height: 60px;
        background: #f1f5f9;
        border-radius: 50%;
        display: grid;
        place-items: center;
        margin: 0 auto 16px auto;
        font-size: 1.5rem;
        color: var(--st-text-muted);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .st-welcome-card {
            padding: 28px 20px;
        }

        .st-welcome-title {
            font-size: 1.6rem;
        }

        .st-stats-wrapper {
            width: 100%;
        }

        .st-stat-card {
            flex: 1;
            min-width: 0;
            padding: 14px;
        }

        .st-stat-value {
            font-size: 1.4rem;
        }

        .st-exams-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="st-dashboard">

    {{-- 1. Hero Banner --}}
    <div class="st-welcome-card">
        <div class="st-welcome-content">
            <div class="st-welcome-badge">
                <i class="fas fa-sparkles"></i>
                <span>لوحة المتابعة اليومية</span>
            </div>
            <h1 class="st-welcome-title">أهلاً بك مجدداً! 👋</h1>
            <p class="st-welcome-desc">جاهز لمتابعة رحلتك التعليمية؟ استكمل اختباراتك اليوم وراقب تطور أدائك المتميز.</p>
        </div>

        <div class="st-stats-wrapper">
            <div class="st-stat-card">
                <span class="st-stat-label">المعدل العام</span>
                <span class="st-stat-value">{{ number_format($my_stats['avg_grade'] ?? 0, 1) }}%</span>
            </div>
            <div class="st-stat-card">
                <span class="st-stat-label">المكتملة</span>
                <span class="st-stat-value">{{ $my_stats['completed_exams'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- 2. Content Grid --}}
    <div class="st-main-grid">

        {{-- قسم الاختبارات المتاحة --}}
        <div>
            <div class="st-section-header">
                <h2 class="st-section-title">
                    <span class="st-icon-pill" style="background: var(--st-primary-soft); color: var(--st-primary);">
                        <i class="fas fa-hourglass-half"></i>
                    </span>
                    <span>اختبارات بانتظارك</span>
                </h2>
            </div>

            <div class="st-exams-grid">
                @forelse($available_exams as $ex)
                    <div class="st-exam-card">
                        <div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="st-exam-tag">
                                    <i class="fas fa-book-open"></i>
                                    {{ $ex->subject->name_ar ?? 'المادة الدراسية' }}
                                </span>
                            </div>

                            <h3 class="st-exam-title">{{ $ex->title }}</h3>

                            <div class="st-exam-meta">
                                <div class="st-meta-item">
                                    <i class="far fa-question-circle text-primary"></i>
                                    <span>{{ $ex->questions_count ?? ($ex->questions ? $ex->questions->count() : 10) }} أسئلة</span>
                                </div>
                                <div class="st-meta-item">
                                    <i class="far fa-clock text-primary"></i>
                                    <span>{{ $ex->duration_minutes ?? '30' }} دقيقة</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('student.exams.take', $ex->id) }}" class="st-btn-primary">
                            <span>بدء الاختبار الآن</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1;">
                        <div class="st-empty-box">
                            <div class="st-empty-icon" style="color: var(--st-success); background: var(--st-success-soft);">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1 fs-6">أحسنت العمل!</h4>
                            <p class="mb-0 small">لا توجد اختبارات معلقة حالياً، عد لاحقاً لمتابعة الجديد.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- قسم الاختبارات المكتملة --}}
        <div>
            <div class="st-section-header">
                <h2 class="st-section-title">
                    <span class="st-icon-pill" style="background: var(--st-success-soft); color: var(--st-success);">
                        <i class="fas fa-award"></i>
                    </span>
                    <span>الاختبارات التي أكملتها</span>
                </h2>
            </div>

            <div class="st-completed-list">
                @forelse($completed_exams ?? [] as $done_exam)
                    <div class="st-completed-card">
                        <div>
                            <div class="st-completed-title">{{ $done_exam->exam->title ?? $done_exam->title }}</div>
                            <div class="st-completed-date">
                                <i class="far fa-calendar-check text-muted"></i>
                                <span>تاريخ الإنجاز: {{ $done_exam->created_at ? $done_exam->created_at->format('Y/m/d') : 'مؤخراً' }}</span>
                            </div>
                        </div>
                        <div class="st-score-badge" style="{{ $done_exam->status == 'pending' ? 'background: #fef3c7; color: #d97706; border-color: rgba(217, 119, 6, 0.2);' : '' }}">
                            <i class="fas {{ $done_exam->status == 'pending' ? 'fa-clock' : 'fa-star' }} fs-7"></i>
                            <span>
                                @if($done_exam->status == 'graded')
                                    {{ number_format($done_exam->total_earned_grade, 1) }}%
                                @else
                                    قيد التصحيح
                                @endif
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="st-empty-box">
                        <div class="st-empty-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <p class="mb-0 small">لم تقم بإكمال أي اختبارات بعد.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
