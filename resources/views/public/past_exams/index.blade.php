@extends('layouts.app')

@section('title', __('أرشيف الامتحانات الوزارية السابقة') . ' | ' . config('app.name', 'منارة التوجيهي'))

@section('content')
<div class="ed-archive-container">

    {{-- الترويسة الوزارية الكلاسيكية --}}
    <header class="ed-archive-header">
        <div class="ed-archive-header-content">
            <div class="header-crest">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div>
                <span class="header-badge">{{ __('المكتبة الأكاديمية المركزية') }}</span>
                <h1 class="header-title">{{ __('أرشيف امتحانات الثانوية العامة (توجيهي فلسطين)') }}</h1>
                <p class="header-desc">{{ __('بنك أوراق الامتحانات الوزارية الرسمية ونماذج الإجابة المعتمدة للسنوات السابقة لكافة الفروع التعليمية.') }}</p>
            </div>
        </div>
    </header>

    {{-- بطاقة التصفية والبحث --}}
    <section class="ed-filter-card">
        <form method="GET" action="{{ route('public.past_exams.index') }}" class="ed-filter-form">
            <div class="filter-field">
                <label><i class="fa-solid fa-calendar"></i> {{ __('سنة الامتحان') }}</label>
                <select name="year" class="ed-select" onchange="this.form.submit()">
                    <option value="">{{ __('جميع السنوات') }}</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-field">
                <label><i class="fa-solid fa-diagram-project"></i> {{ __('الفرع الأكاديمي') }}</label>
                <select name="branch" class="ed-select" onchange="this.form.submit()">
                    <option value="all">{{ __('كافة الفروع') }}</option>
                    <option value="scientific" {{ request('branch') == 'scientific' ? 'selected' : '' }}>{{ __('الفرع العلمي') }}</option>
                    <option value="literary" {{ request('branch') == 'literary' ? 'selected' : '' }}>{{ __('الفرع الأدبي') }}</option>
                    <option value="business" {{ request('branch') == 'business' ? 'selected' : '' }}>{{ __('الريادة والأعمال') }}</option>
                    <option value="industrial" {{ request('branch') == 'industrial' ? 'selected' : '' }}>{{ __('الفرع الصناعي') }}</option>
                </select>
            </div>

            <div class="filter-field">
                <label><i class="fa-solid fa-clock-rotate-left"></i> {{ __('الدورة الامتحانية') }}</label>
                <select name="session" class="ed-select" onchange="this.form.submit()">
                    <option value="">{{ __('كافة الدورات') }}</option>
                    <option value="first" {{ request('session') == 'first' ? 'selected' : '' }}>{{ __('الدورة الأولى (يونيو)') }}</option>
                    <option value="second" {{ request('session') == 'second' ? 'selected' : '' }}>{{ __('الدورة الثانية (أغسطس)') }}</option>
                    <option value="completion" {{ request('session') == 'completion' ? 'selected' : '' }}>{{ __('الدورة الاستكمالية') }}</option>
                </select>
            </div>

            <div class="filter-field search-field">
                <label><i class="fa-solid fa-magnifying-glass"></i> {{ __('البحث بالاسم والمادة') }}</label>
                <div class="search-input-wrap">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ابحث عن مادة مثل: الرياضيات، الفيزياء...') }}" class="ed-input">
                    <button type="submit" class="btn-search"><i class="fa-solid fa-arrow-left"></i></button>
                </div>
            </div>
        </form>
    </section>

    {{-- شبكة عرض كروت الامتحانات --}}
    @if($exams->count() > 0)
        <div class="ed-exams-grid">
            @foreach($exams as $exam)
                <article class="ed-exam-card">
                    <div class="card-top">
                        <span class="badge-year">{{ $exam->year }}</span>
                        <span class="badge-branch">{{ $exam->branch_label }}</span>
                    </div>

                    <h3 class="card-subject-title">{{ $exam->subject_name }}</h3>
                    <p class="card-session-meta">
                        <i class="fa-solid fa-calendar-day"></i> {{ $exam->session_label }}
                    </p>

                    @if($exam->notes)
                        <p class="card-notes">{{ $exam->notes }}</p>
                    @endif

                    <div class="card-footer">
                        @if($exam->exam_paper_url)
                            <a href="{{ route('public.past_exams.download', $exam->id) }}" class="btn-download" target="_blank">
                                <i class="fa-solid fa-file-pdf"></i>
                                <span>{{ __('تحميل ورقة الامتحان') }}</span>
                            </a>
                        @else
                            <button type="button" class="btn-download disabled" disabled title="{{ __('قيد الإعداد من الوزارة') }}">
                                <i class="fa-solid fa-hourglass-half"></i>
                                <span>{{ __('قيد التجهيز الوزاري') }}</span>
                            </button>
                        @endif

                        @if($exam->answer_key_url)
                            <a href="{{ $exam->answer_key_url }}" target="_blank" class="btn-key" title="{{ __('نموذج الإجابة الرسمي') }}">
                                <i class="fa-solid fa-check-double"></i>
                                <span>{{ __('الإجابة') }}</span>
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="ed-pagination-wrap">
            {{ $exams->links() }}
        </div>
    @else
        <div class="ed-empty-state">
            <div class="empty-icon"><i class="fa-solid fa-box-archive"></i></div>
            <h3>{{ __('لم يتم العثور على نماذج امتحانات مطابقة للبحث') }}</h3>
            <p>{{ __('يرجى تغيير خيارات التصفية أو اختيار سنة وفرع آخر للوصول للأوراق الوزارية.') }}</p>
            <a href="{{ route('public.past_exams.index') }}" class="btn-reset-filter">
                <i class="fa-solid fa-rotate-right"></i> {{ __('إعادة ضبط التصفية') }}
            </a>
        </div>
    @endif

</div>

<style>
.ed-archive-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px 80px;
}

.ed-archive-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    border-radius: 16px;
    padding: 36px 32px;
    color: #ffffff;
    margin-bottom: 28px;
    box-shadow: 0 4px 20px rgba(30, 58, 138, 0.15);
}

.ed-archive-header-content {
    display: flex;
    align-items: center;
    gap: 24px;
}

.header-crest {
    width: 68px;
    height: 68px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #fbbf24;
    border: 1px solid rgba(255, 255, 255, 0.2);
    flex-shrink: 0;
}

.header-badge {
    display: inline-block;
    background: rgba(251, 191, 36, 0.2);
    color: #fde68a;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 8px;
    border: 1px solid rgba(251, 191, 36, 0.3);
}

.header-title {
    font-size: 1.6rem;
    font-weight: 800;
    margin: 0 0 6px;
    letter-spacing: -0.5px;
}

.header-desc {
    margin: 0;
    font-size: 0.92rem;
    color: #e2e8f0;
    line-height: 1.6;
    max-width: 700px;
}

.ed-filter-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 24px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}

.ed-filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    align-items: end;
}

.filter-field label {
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 6px;
}

.ed-select, .ed-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 0.88rem;
    background: #f8fafc;
    color: #1e293b;
    font-family: inherit;
    box-sizing: border-box;
}

.ed-select:focus, .ed-input:focus {
    border-color: #2563eb;
    background: #ffffff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.search-input-wrap {
    display: flex;
    gap: 6px;
}

.btn-search {
    background: #1e40af;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 0 16px;
    cursor: pointer;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

.btn-search:hover {
    background: #1d4ed8;
}

.ed-exams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 36px;
}

.ed-exam-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s, box-shadow 0.2s;
}

.ed-exam-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
}

.card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.badge-year {
    background: #eff6ff;
    color: #1d4ed8;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 800;
    font-family: monospace;
}

.badge-branch {
    background: #f1f5f9;
    color: #475569;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.76rem;
    font-weight: 700;
}

.card-subject-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}

.card-session-meta {
    font-size: 0.8rem;
    color: #64748b;
    margin: 0 0 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.card-notes {
    font-size: 0.82rem;
    color: #475569;
    background: #f8fafc;
    padding: 8px 12px;
    border-radius: 6px;
    margin: 0 0 16px;
    line-height: 1.5;
}

.card-footer {
    display: flex;
    gap: 8px;
    margin-top: auto;
}

.btn-download {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: #0284c7;
    color: #ffffff;
    padding: 9px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 700;
    transition: 0.15s;
    border: none;
}

.btn-download:hover:not(.disabled) {
    background: #0369a1;
}

.btn-download.disabled {
    background: #e2e8f0;
    color: #94a3b8;
    cursor: not-allowed;
}

.btn-key {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: #10b981;
    color: #ffffff;
    padding: 9px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 700;
    transition: 0.15s;
}

.btn-key:hover {
    background: #059669;
}

.ed-empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 14px;
}

.empty-icon {
    font-size: 3rem;
    color: #94a3b8;
    margin-bottom: 14px;
}

.ed-empty-state h3 {
    font-size: 1.2rem;
    color: #1e293b;
    margin: 0 0 6px;
}

.ed-empty-state p {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0 0 20px;
}

.btn-reset-filter {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.84rem;
    font-weight: 700;
    text-decoration: none;
}

.btn-reset-filter:hover {
    background: #e2e8f0;
}
</style>
@endsection
