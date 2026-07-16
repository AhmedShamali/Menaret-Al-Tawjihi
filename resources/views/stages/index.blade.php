@extends('layouts.app')

@section('title', 'تصفح المراحل')

@section('content')
<div style="margin-bottom: 50px;">
    <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--primary); letter-spacing: -1px;">المراحل الدراسية</h1>
    <p style="color: var(--text-light); font-size: 1.1rem; margin-top: 10px;">اختر مرحلتك التعليمية للوصول إلى الدروس والاختبارات.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;">
    @foreach($stages as $stage)
    <a href="{{ route('stages.show', $stage->id) }}" class="glass-card" style="text-decoration: none; color: inherit; display: block; overflow: hidden;">
        <div style="padding: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
                <div style="width: 70px; height: 70px; background: #f1f5f9; border-radius: 20px; display: grid; place-items: center; font-size: 2.5rem;">
                    {{ $stage->icon }}
                </div>
                <span class="chip chip-emerald">{{ $stage->subjects_count ?? 0 }} مواد</span>
            </div>

            <h2 style="font-size: 1.6rem; font-weight: 700; color: var(--primary); margin-bottom: 12px;">{{ $stage->label_ar }}</h2>
            <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.7; margin-bottom: 25px;">
                محتوى تعليمي شامل ومبسط يغطي المنهاج الفلسطيني المقرر لهذه المرحلة الدراسية.
            </p>

            <div style="display: flex; align-items: center; gap: 10px; color: var(--accent); font-weight: 700; font-size: 0.95rem;">
                دخول المحتوى
                <span style="font-size: 1.2rem;">←</span>
            </div>
        </div>
        <div style="height: 6px; background: var(--accent); width: 40%; border-radius: 0 10px 10px 0;"></div>
    </a>
    @endforeach
</div>
@endsection
