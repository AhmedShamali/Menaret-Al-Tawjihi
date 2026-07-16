@extends('layouts.app')

@section('title', 'المكتبة التعليمية الشاملة')

@section('content')
<div style="display: flex; flex-direction: column; gap: 40px;">

    <!-- Hero Header -->
    <div style="background: linear-gradient(135deg, #0f172a, #1e293b); border-radius: 30px; padding: 60px; color: white; position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
        <div style="position: absolute; left: -30px; top: -30px; font-size: 12rem; opacity: 0.05; transform: rotate(-15deg);">📖</div>
        <div style="position: relative; z-index: 1;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <span style="width: 12px; height: 12px; background: var(--accent); border-radius: 50%; box-shadow: 0 0 15px var(--accent);"></span>
                <span style="text-transform: uppercase; letter-spacing: 2px; font-size: 0.8rem; font-weight: 700; color: var(--accent);">المنهاج الفلسطيني المحدث</span>
            </div>
            <h1 style="font-size: 3.2rem; font-weight: 800; line-height: 1.1;">المكتبة الرقمية <br><span style="color: var(--accent);">للمواد الدراسية</span></h1>
            <p style="font-size: 1.1rem; opacity: 0.7; max-width: 550px; margin-top: 20px; line-height: 1.7;">تصفح كافة المساقات التعليمية المصممة لتمكين الطالب الفلسطيني من التفوق والنجاح رقمياً.</p>
        </div>
    </div>

    <!-- قائمة المواد مقسمة حسب الصفوف -->
    @foreach($stages as $stage)
    <div style="display: flex; flex-direction: column; gap: 25px;">
        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="font-size: 2rem;">{{ $stage->icon }}</span>
                <h2 style="font-size: 1.8rem; font-weight: 800; color: var(--primary);">{{ $stage->label_ar }}</h2>
            </div>
            <span style="font-weight: 700; color: var(--text-light); font-size: 0.9rem;">{{ $stage->subjects->count() }} مواد</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">
            @foreach($stage->subjects as $subject)
            <a href="{{ route('subjects.show', $subject->id) }}" class="glass-card subject-card">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="icon-wrap" style="background: {{ $subject->color }}10; color: {{ $subject->color }};">
                        {{ $subject->icon }}
                    </div>
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 700; color: var(--primary); margin-bottom: 2px;">{{ $subject->name_ar }}</h4>
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span style="width: 6px; height: 6px; background: {{ $subject->color }}; border-radius: 50%;"></span>
                            <span style="font-size: 0.7rem; color: var(--text-light); font-weight: 600;">ابدأ الآن</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

<style>
    .subject-card {
        padding: 15px 20px !important;
        transition: 0.4s;
        border-right: 4px solid transparent;
        text-decoration: none;
    }
    .subject-card:hover {
        transform: translateX(-10px);
        border-right-color: var(--accent);
        background: white;
        box-shadow: 0 15px 30px rgba(0,0,0,0.05);
    }
    .icon-wrap {
        width: 50px; height: 50px; border-radius: 14px;
        display: grid; place-items: center; font-size: 1.6rem;
        transition: 0.3s;
    }
    .subject-card:hover .icon-wrap { transform: scale(1.1); }
</style>
@endsection
