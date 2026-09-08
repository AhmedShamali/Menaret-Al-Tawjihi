@extends('layouts.app')

@section('title', $stage->label_ar)

@section('content')
<style>
    .stage-show-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 28px;
    }

    .btn-back-link {
        width: 42px;
        height: 42px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        display: grid;
        place-items: center;
        color: #0f172a;
        text-decoration: none;
        font-size: 1.1rem;
        transition: all 0.2s;
    }

    .btn-back-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .stage-banner {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 36px 40px;
        margin-bottom: 36px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        position: relative;
        overflow: hidden;
    }

    .banner-content {
        position: relative;
        z-index: 2;
    }

    .banner-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 999px;
        margin-bottom: 12px;
    }

    .banner-content h1 {
        font-size: 2rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .banner-content p {
        color: #64748b;
        font-size: 1rem;
        max-width: 580px;
        line-height: 1.6;
    }

    .banner-big-icon {
        font-size: 5.5rem;
        opacity: 0.9;
        margin-left: 20px;
    }

    .subjects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
        gap: 24px;
    }

    .subject-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 26px;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.25s ease;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .subject-card:hover {
        border-color: #93c5fd;
        transform: translateY(-3px);
        box-shadow: 0 10px 22px rgba(30, 58, 138, 0.06);
    }

    .subject-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .subject-icon-box {
        width: 52px;
        height: 52px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 1.8rem;
    }

    .grade-weight-pill {
        font-size: 0.78rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #dbeafe;
    }

    .subject-card h3 {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .subject-card p {
        color: #64748b;
        font-size: 0.88rem;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .subject-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e40af;
    }
</style>

<div class="stage-show-header">
    <a href="{{ route('stages.index') }}" class="btn-back-link" title="الرجوع لكافة الفروع">
        <i class="fas fa-arrow-right"></i>
    </a>
    <h2 style="font-size: 1.3rem; font-weight: 700; color: #0f172a;">فروع التوجيهي / {{ $stage->label_ar }}</h2>
</div>

<!-- Banner -->
<div class="stage-banner">
    <div class="banner-content">
        <div class="banner-badge">
            <i class="fas fa-graduation-cap"></i>
            <span>المنهاج الفلسطيني الوزاري المعتمد</span>
        </div>
        <h1>{{ $stage->label_ar }}</h1>
        <p>استكشف المقررات الوزارية وشروحات الدروس التفاعلية، والملخصات، والامتحانات التدريبية الخاصة بهذا الفرع.</p>
    </div>
    <div class="banner-big-icon">
        {{ $stage->icon ?? '🎓' }}
    </div>
</div>

<!-- Subjects List -->
<div class="subjects-grid">
    @forelse($stage->subjects as $subject)
    <a href="{{ url('/subject/' . $subject->id) }}" class="subject-card">
        <div>
            <div class="subject-card-top">
                <div class="subject-icon-box">
                    {{ $subject->icon ?? '📘' }}
                </div>
                
                @if(str_contains($subject->subject_key, 'math_12_sci'))
                    <span class="grade-weight-pill">من 200 علامة</span>
                @elseif(str_contains($subject->subject_key, 'arabic_12_lit') || str_contains($subject->subject_key, 'english_12_lit'))
                    <span class="grade-weight-pill">من 150 علامة</span>
                @else
                    <span class="grade-weight-pill">من 100 علامة</span>
                @endif
            </div>

            <h3>{{ $subject->name_ar }}</h3>
            <p>{{ $subject->description ?? 'شروحات مرئية تفاعلية، ملخصات بصيغة PDF، واختبارات قياس مستوى.' }}</p>
        </div>

        <div class="subject-card-footer">
            <span>ابدأ المذاكرة</span>
            <i class="fas fa-arrow-left"></i>
        </div>
    </a>
    @empty
    <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: #fff; border-radius: 16px;">
        <p style="color: #64748b;">جاري إضافة وتحديث المباحث لهذا الفرع...</p>
    </div>
    @endforelse
</div>
@endsection
