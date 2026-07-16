@extends('layouts.app')
@section('content')
<div style="display: flex; flex-direction: column; gap: 35px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 style="font-size: 2.2rem; font-weight: 800; color: #0f172a;">مركز الاختبارات الأكاديمي 📑</h1>
        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">➕ بناء اختبار جديد</a>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px;">
        @foreach($exams as $exam)
        <div class="glass-card" style="padding: 35px; border-top: 6px solid {{ $exam->subject->color }};">
            <span class="chip" style="background: {{ $exam->subject->color }}10; color: {{ $exam->subject->color }};">{{ $exam->subject->name_ar }}</span>
            <h3 style="margin: 15px 0 10px;">{{ $exam->title }}</h3>
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #64748b; margin-bottom: 25px;">
                <span>👥 {{ $exam->submissions_count }} طالب</span>
                <span>⏱ {{ $exam->duration_minutes }} دقيقة</span>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <a href="{{ route('admin.exams.stats', $exam->id) }}" class="btn btn-sm" style="background: #f1f5f9;">📊 إحصائيات</a>
                <a href="{{ route('admin.submissions.index', ['exam_id' => $exam->id]) }}" class="btn btn-sm btn-primary">🔍 التسليمات</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
