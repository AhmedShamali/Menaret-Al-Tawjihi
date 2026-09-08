@extends('layouts.app')

@section('title', 'فروع الثانوية العامة (التوجيهي)')

@section('content')
<style>
    :root {
        --card-bg: #ffffff;
        --border-light: #e2e8f0;
        --text-dark: #0f172a;
        --text-slate: #475569;
        --text-dim: #64748b;
        --navy-primary: #1e3a8a;
        --blue-soft: #eff6ff;
        --emerald-soft: #ecfdf5;
        --emerald-dark: #047857;
        --radius: 18px;
    }

    .stages-hero-wrap {
        background: #ffffff;
        border: 1px solid var(--border-light);
        border-radius: var(--radius);
        padding: 36px 40px;
        margin-bottom: 36px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .stages-hero-text h1 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .stages-hero-text p {
        color: var(--text-dim);
        font-size: 1.02rem;
        max-width: 620px;
    }

    .grading-pill-box {
        background: var(--blue-soft);
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .grading-pill-box i {
        color: var(--navy-primary);
        font-size: 1.4rem;
    }

    .grading-pill-box .title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--navy-primary);
    }

    .grading-pill-box .subtitle {
        font-size: 0.78rem;
        color: #1e40af;
    }

    .branches-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 26px;
    }

    .branch-portal-card {
        background: var(--card-bg);
        border: 1px solid var(--border-light);
        border-radius: var(--radius);
        padding: 32px;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }

    .branch-portal-card:hover {
        border-color: #93c5fd;
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(30, 58, 138, 0.08);
    }

    .branch-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 22px;
    }

    .branch-icon-box {
        width: 64px;
        height: 64px;
        background: #f8fafc;
        border: 1px solid var(--border-light);
        border-radius: 16px;
        display: grid;
        place-items: center;
        font-size: 2.2rem;
    }

    .branch-count-badge {
        background: var(--emerald-soft);
        color: var(--emerald-dark);
        border: 1px solid #a7f3d0;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 999px;
    }

    .branch-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 10px;
    }

    .branch-description {
        color: var(--text-dim);
        font-size: 0.92rem;
        line-height: 1.65;
        margin-bottom: 20px;
    }

    .subject-chips-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 24px;
    }

    .subject-chip {
        background: #f8fafc;
        border: 1px solid var(--border-light);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-slate);
    }

    .branch-card-action {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 18px;
        border-top: 1px solid var(--border-light);
        color: var(--navy-primary);
        font-weight: 700;
        font-size: 0.92rem;
    }

    .branch-card-action i {
        transition: transform 0.2s;
    }

    .branch-portal-card:hover .branch-card-action i {
        transform: translateX(-4px);
    }
</style>

<!-- Hero Section -->
<div class="stages-hero-wrap">
    <div class="stages-hero-text">
        <h1>فروع الثانوية العامة (التوجيهي) 🇵🇸</h1>
        <p>اختر فرعك التعليمي للوصول إلى كافة المقررات والكتب والدروس والشروحات المعتمدة وفق المنهاج الفلسطيني الرسمي.</p>
    </div>
    <div class="grading-pill-box">
        <i class="fas fa-scale-balanced"></i>
        <div>
            <div class="title">سلم احتساب التوجيهي (700 علامة)</div>
            <div class="subtitle">العلمي: رياضيات 200 | الأدبي: عربي 150، إنجليزي 150 | الباقي 100</div>
        </div>
    </div>
</div>

<!-- Branches Grid -->
<div class="branches-grid">
    @forelse($stages as $stage)
    <a href="{{ route('stages.show', $stage->id) }}" class="branch-portal-card">
        <div>
            <div class="branch-card-header">
                <div class="branch-icon-box">
                    {{ $stage->icon ?? '🎓' }}
                </div>
                <span class="branch-count-badge">
                    <i class="fas fa-book-bookmark"></i> {{ $stage->subjects_count ?? $stage->subjects->count() }} مباحث وزارية
                </span>
            </div>

            <h2 class="branch-title">{{ $stage->label_ar }}</h2>
            
            <p class="branch-description">
                @if(str_contains($stage->label_ar, 'علمي'))
                    منهاج الفرع العلمي الكامل: الرياضيات (200 علامة)، الفيزياء، الكيمياء، العلوم الحياتية، والمواد الإجبارية.
                @elseif(str_contains($stage->label_ar, 'أدبي'))
                    منهاج الفرع الأدبي التخصصي: اللغة العربية (150 علامة)، اللغة الإنجليزية (150 علامة)، التاريخ، الجغرافيا، والاختياري.
                @else
                    منهاج فرع الريادة والأعمال: المشاريع الريادية، المحاسبة، الإدارة والاقتصاد، والتطبيقات التجارية.
                @endif
            </p>

            <div class="subject-chips-wrap">
                @foreach($stage->subjects->take(5) as $sub)
                    <span class="subject-chip">{{ $sub->name_ar }}</span>
                @endforeach
                @if($stage->subjects_count > 5)
                    <span class="subject-chip">+{{ $stage->subjects_count - 5 }} مواد إضافية</span>
                @endif
            </div>
        </div>

        <div class="branch-card-action">
            <span>تصفح منهاج ومواد الفرع</span>
            <i class="fas fa-arrow-left"></i>
        </div>
    </a>
    @empty
    <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: #fff; border-radius: 18px;">
        <p style="color: var(--text-dim); font-size: 1.1rem;">جاري تهيئة فروع الثانوية العامة...</p>
    </div>
    @endforelse
</div>

@endsection
