@extends('layouts.app')

@section('title', 'معاينة المحتوى')

@section('content')
<div class="glass-card" style="max-width: 900px; margin: 0 auto; padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color: var(--primary);">{{ $content->title }}</h2>
        <span class="chip chip-emerald">{{ $content->subject->name_ar }}</span>
    </div>

    @if($content->type == 'video')
        <div style="aspect-ratio: 16/9; background: #000; border-radius: 20px; overflow: hidden;">
            <iframe width="100%" height="100%" src="https://www.youtube.com/embed/{{ $content->url_path }}" frameborder="0" allowfullscreen></iframe>
        </div>
    @else
        <div style="padding: 50px; background: #f8fafc; border-radius: 20px; text-align: center;">
            <div style="font-size: 4rem;">📄</div>
            <h3 style="margin-top: 20px;">ملف PDF متاح للتحميل</h3>
            <p style="color: var(--text-light);">{{ $content->file_size }}</p>
            <a href="#" class="btn btn-primary" style="margin-top: 20px;">فتح الملف للمراجعة</a>
        </div>
    @endif
</div>
@endsection
