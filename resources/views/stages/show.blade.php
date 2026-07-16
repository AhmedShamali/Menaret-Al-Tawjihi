@extends('layouts.app')

@section('title', $stage->label_ar)

@section('content')
<div style="display: flex; align-items: center; gap: 15px; margin-bottom: 40px;">
    <a href="/stages" style="text-decoration: none; font-size: 1.5rem; color: var(--text-light);">←</a>
    <h1 style="font-size: 2.2rem; font-weight: 700; color: var(--primary);">{{ $stage->label_ar }}</h1>
</div>

<!-- بطاقة تعريفية للمرحلة -->
<div class="glass-card" style="background: var(--primary); color: white; padding: 50px; margin-bottom: 40px; position: relative; overflow: hidden; border: none;">
    <div style="position: absolute; left: -30px; bottom: -40px; font-size: 12rem; opacity: 0.1;">{{ $stage->icon }}</div>
    <div style="position: relative; z-index: 1;">
        <span style="color: var(--accent); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px;">المنهاج الفلسطيني</span>
        <h2 style="font-size: 2.5rem; margin-top: 10px;">استكشف موادك الدراسية</h2>
        <p style="opacity: 0.7; max-width: 500px; margin-top: 15px; font-size: 1.1rem; line-height: 1.6;">بوابتك للتميز، اختر المادة وابدأ رحلة التعلم الرقمي الآن.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
    @forelse($stage->subjects as $subject)
    <a href="/subject/{{ $subject->id }}" class="glass-card" style="text-decoration: none; color: inherit; padding: 30px; display: block; border-right: 5px solid {{ $subject->color ?? 'var(--accent)' }};">
        <div style="font-size: 2.5rem; margin-bottom: 20px;">{{ $subject->icon ?? '📘' }}</div>
        <h3 style="font-size: 1.3rem; font-weight: 700; color: var(--primary); margin-bottom: 10px;">{{ $subject->name_ar }}</h3>
        <p style="color: var(--text-light); font-size: 0.9rem; line-height: 1.6; margin-bottom: 20px;">شرح الوحدات، ملخصات PDF، واختبارات تفاعلية.</p>

        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #f1f5f9;">
            <span style="font-weight: 700; color: var(--tatreez); font-size: 0.85rem;">ابدأ الدرس ←</span>
            <span style="font-size: 0.7rem; color: var(--text-light);">دروس محدثة</span>
        </div>
    </a>
    @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 80px;">
            <p style="color: var(--text-light); font-size: 1.2rem;">قريباً سيتم إضافة المواد..</p>
        </div>
    @endforelse
</div>
@endsection
