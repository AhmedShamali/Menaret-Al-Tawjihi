@extends('layouts.app')

@section('title', __('معاينة الاختبار') . ': ' . $exam->title . ' | ' . config('app.name'))

@section('content')
<div class="ed-exam-preview-container">

    {{-- الترويسة الرئيسية --}}
    <div class="ed-preview-header">
        <div>
            <div class="meta-tags">
                <span class="badge-stage">{{ $exam->stage->label_ar ?? $exam->stage->name_ar ?? __('توجيهي فلسطين') }}</span>
                <span class="badge-subject">{{ $exam->subject->name_ar ?? __('مادة تعليمية') }}</span>
                @if($exam->is_active ?? true)
                    <span class="badge-status active"><i class="fa-solid fa-circle-check"></i> {{ __('نشط ومتاح') }}</span>
                @else
                    <span class="badge-status inactive"><i class="fa-solid fa-circle-pause"></i> {{ __('موقوف مؤقتاً') }}</span>
                @endif
            </div>
            <h1 class="exam-title">{{ $exam->title }}</h1>
            <p class="exam-desc">{{ $exam->description ?: __('اختبار تقييمي رسمي معتمد من الكادر التعليمي.') }}</p>
        </div>

        <div class="header-actions">
            <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn-action primary">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>{{ __('تعديل الاختبار') }}</span>
            </a>
            <a href="{{ route('admin.exams.submissions', $exam->id) }}" class="btn-action secondary">
                <i class="fa-solid fa-users-viewfinder"></i>
                <span>{{ __('إجابات الطلاب') }}</span>
            </a>
            <a href="{{ route('admin.exams.stats', $exam->id) }}" class="btn-action outline">
                <i class="fa-solid fa-chart-pie"></i>
                <span>{{ __('الإحصائيات') }}</span>
            </a>
            <a href="{{ route('admin.exams.index') }}" class="btn-action back">
                <i class="fa-solid fa-arrow-right"></i>
                <span>{{ __('العودة') }}</span>
            </a>
        </div>
    </div>

    {{-- شريط بطاقات المعلومات --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon blue"><i class="fa-solid fa-clock"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('مدة الامتحان') }}</span>
                <strong class="kpi-val">{{ $exam->duration_minutes }} {{ __('دقيقة') }}</strong>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon green"><i class="fa-solid fa-list-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('عدد الأسئلة') }}</span>
                <strong class="kpi-val">{{ $exam->questions->count() }} {{ __('سؤال') }}</strong>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon amber"><i class="fa-solid fa-star"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('الدرجة الكلية') }}</span>
                <strong class="kpi-val">{{ $exam->questions->sum('points') ?: ($exam->total_marks ?? 100) }} {{ __('علامة') }}</strong>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon purple"><i class="fa-solid fa-eye"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('إعلان النتيجة') }}</span>
                <strong class="kpi-val" style="font-size: 0.95rem;">
                    {{ $exam->show_result_immediately ? __('مباشر بعد التسليم') : __('بعد المراجعة والاعتماد') }}
                </strong>
            </div>
        </div>
    </div>

    {{-- ورقة بنك الأسئلة --}}
    <div class="questions-paper">
        <div class="paper-header">
            <h3><i class="fa-solid fa-file-lines text-primary"></i> {{ __('ورقة أسئلة الاختبار الرسمية') }}</h3>
            <span class="text-muted text-sm">{{ __('معاينة نموذج الأسئلة والإجابات المعتمدة') }}</span>
        </div>

        @if($exam->questions->count() > 0)
            <div class="questions-list">
                @foreach($exam->questions as $idx => $q)
                    <div class="question-block">
                        <div class="q-top">
                            <span class="q-num">{{ __('السؤال') }} {{ $idx + 1 }}</span>
                            <span class="q-type-badge {{ $q->type }}">
                                {{ $q->type === 'mcq' ? __('اختيار من متعدد') : __('سؤال مقالي / نصي') }}
                            </span>
                            <span class="q-points-badge">{{ $q->points }} {{ __('علامات') }}</span>
                        </div>

                        <div class="q-body">
                            <p class="q-text">{{ $q->question_text }}</p>
                            @if($q->image)
                                <div class="q-img-wrap">
                                    <img src="{{ asset('storage/' . $q->image) }}" alt="Question Image">
                                </div>
                            @endif

                            @if($q->type === 'mcq')
                                <div class="options-grid">
                                    @foreach(['a' => 'أ', 'b' => 'ب', 'c' => 'ج', 'd' => 'د'] as $key => $letter)
                                        @if(!empty($q->$key) || !empty($q->{$key . '_image'}))
                                            <div class="option-item {{ $q->correct_answer === $key ? 'is-correct' : '' }}">
                                                <span class="opt-medallion">{{ $letter }}</span>
                                                <span class="opt-content">
                                                    {{ $q->$key }}
                                                    @if(!empty($q->{$key . '_image'}))
                                                        <img src="{{ asset('storage/' . $q->{$key . '_image'}) }}" class="opt-img" alt="Option Image">
                                                    @endif
                                                </span>
                                                @if($q->correct_answer === $key)
                                                    <span class="correct-tag"><i class="fa-solid fa-check"></i> {{ __('صحيحة') }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-questions">
                <i class="fa-solid fa-inbox"></i>
                <h4>{{ __('لم يتم إضافة أسئلة لهذا الاختبار بعد.') }}</h4>
                <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn-add-q">
                    <i class="fa-solid fa-plus"></i> {{ __('إضافة أسئلة الآن') }}
                </a>
            </div>
        @endif
    </div>

</div>

<style>
.ed-exam-preview-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 24px 20px 80px;
}

.ed-preview-header {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 24px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.meta-tags {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}

.badge-stage {
    background: #f1f5f9;
    color: #334155;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
}

.badge-subject {
    background: #eff6ff;
    color: #1d4ed8;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
}

.badge-status.active {
    background: #ecfdf5;
    color: #059669;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
}

.badge-status.inactive {
    background: #fef2f2;
    color: #dc2626;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
}

.exam-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}

.exam-desc {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 700;
    text-decoration: none;
    transition: 0.15s;
}

.btn-action.primary {
    background: #0284c7;
    color: #ffffff;
}

.btn-action.primary:hover {
    background: #0369a1;
}

.btn-action.secondary {
    background: #10b981;
    color: #ffffff;
}

.btn-action.secondary:hover {
    background: #059669;
}

.btn-action.outline {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
}

.btn-action.outline:hover {
    background: #f8fafc;
}

.btn-action.back {
    background: #f1f5f9;
    color: #475569;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.kpi-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.kpi-icon.blue { background: #eff6ff; color: #2563eb; }
.kpi-icon.green { background: #ecfdf5; color: #10b981; }
.kpi-icon.amber { background: #fef3c7; color: #d97706; }
.kpi-icon.purple { background: #f3e8ff; color: #9333ea; }

.kpi-label {
    display: block;
    font-size: 0.78rem;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 2px;
}

.kpi-val {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
}

.questions-paper {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 28px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.paper-header {
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 16px;
    margin-bottom: 24px;
}

.paper-header h3 {
    margin: 0 0 4px;
    font-size: 1.25rem;
    font-weight: 800;
    color: #0f172a;
}

.question-block {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.q-top {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.q-num {
    background: #1e3a8a;
    color: #ffffff;
    padding: 2px 10px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 800;
}

.q-type-badge {
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.74rem;
    font-weight: 700;
}

.q-type-badge.mcq { background: #e0f2fe; color: #0284c7; }
.q-type-badge.essay { background: #fef3c7; color: #b45309; }

.q-points-badge {
    margin-right: auto;
    font-weight: 800;
    font-size: 0.82rem;
    color: #059669;
}

.q-text {
    font-size: 1.02rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 12px;
    line-height: 1.6;
}

.q-img-wrap img {
    max-width: 100%;
    max-height: 320px;
    border-radius: 8px;
    margin-bottom: 14px;
    border: 1px solid #e2e8f0;
}

.options-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 10px;
}

.option-item {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.option-item.is-correct {
    border-color: #10b981;
    background: #f0fdf4;
}

.opt-medallion {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    font-weight: 800;
    flex-shrink: 0;
}

.option-item.is-correct .opt-medallion {
    background: #10b981;
    color: #ffffff;
}

.correct-tag {
    margin-right: auto;
    background: #dcfce7;
    color: #15803d;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.72rem;
    font-weight: 800;
}

.empty-questions {
    text-align: center;
    padding: 40px 20px;
    color: #64748b;
}

.empty-questions i {
    font-size: 2.5rem;
    margin-bottom: 12px;
}

.btn-add-q {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #0284c7;
    color: #ffffff;
    padding: 8px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 700;
    margin-top: 14px;
}
</style>
@endsection
