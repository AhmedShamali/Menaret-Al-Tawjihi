@extends('layouts.app')

@section('content')

<!-- استدعاء الخطوط والأيقونات -->
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    :root {
        --sub-color: {{ $subject->color ?? '#6366f1' }};
        --sub-color-light: {{ ($subject->color ?? '#6366f1') . '15' }};
        --bg-body: #f1f5f9;
        --card-white: #ffffff;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --radius: 24px;
        --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .subject-show-page {
        direction: rtl;
        font-family: 'Cairo', sans-serif;
        background-color: var(--bg-body);
        min-height: 100vh;
        padding: 40px 20px;
        color: var(--text-main);
    }

    .page-container {
        max-width: 1300px;
        margin: 0 auto;
    }

    /* 1. الهيدر الأنيق */
    .hero-section {
        background: linear-gradient(135deg, var(--sub-color), #4338ca);
        border-radius: var(--radius);
        padding: 40px;
        color: white;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .hero-section::after {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .custom-breadcrumb {
        display: flex;
        gap: 10px;
        list-style: none;
        padding: 0;
        margin-bottom: 15px;
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .custom-breadcrumb a { color: white; text-decoration: none; }
    .custom-breadcrumb li:not(:last-child)::after { content: '←'; margin-right: 10px; }

    .hero-title {
        font-size: 2.8rem;
        font-weight: 900;
        margin: 0;
        text-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    /* 2. تقسيم الصفحة */
    .main-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 30px;
    }

    @media (max-width: 1100px) {
        .main-grid { grid-template-columns: 1fr; }
    }

    /* 3. كروت المعلومات (اليمين) */
    .sidebar-card {
        background: var(--card-white);
        border-radius: var(--radius);
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: var(--shadow);
        border: 1px solid rgba(255,255,255,0.8);
    }

    .teacher-profile {
        text-align: center;
        padding-bottom: 20px;
        border-bottom: 1px dashed #e2e8f0;
    }

    .teacher-avatar-large {
        width: 90px;
        height: 90px;
        background: var(--sub-color-light);
        color: var(--sub-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 900;
        margin: 0 auto 15px;
        border: 4px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .progress-box {
        margin-top: 20px;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }

    .progress-bar-bg {
        background: #f1f5f9;
        height: 12px;
        border-radius: 20px;
        overflow: hidden;
    }

    .progress-bar-inner {
        height: 100%;
        background: linear-gradient(90deg, var(--sub-color), #818cf8);
        border-radius: 20px;
        transition: width 1s ease-in-out;
    }

    /* 4. قسم الفيديوهات (اليسار) */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .section-header h2 {
        font-size: 1.4rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .video-card {
        background: var(--card-white);
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: var(--shadow);
        transition: var(--transition);
        border: 1px solid transparent;
    }

    .video-card:hover {
        transform: translateY(-5px);
        border-color: var(--sub-color);
    }

    .video-wrapper {
        position: relative;
        padding-top: 56.25%; /* 16:9 Aspect Ratio */
        background: #000;
    }

    .video-wrapper iframe, .video-wrapper video {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        border: 0;
    }

    .video-info {
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .video-info h3 {
        font-size: 1.2rem;
        font-weight: 700;
        margin: 0;
    }

    /* 5. الملفات المرفقة */
    .file-row {
        background: #f8fafc;
        padding: 15px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 12px;
        transition: var(--transition);
        border: 1px solid #f1f5f9;
        text-decoration: none;
        color: inherit;
    }

    .file-row:hover {
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-color: var(--sub-color);
        transform: translateX(-5px);
    }

    .file-icon {
        width: 45px;
        height: 45px;
        background: #fee2e2;
        color: #ef4444;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .btn-action {
        background: var(--sub-color);
        color: white;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
    }

    .btn-action:hover {
        background: #4338ca;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        background: #f8fafc;
        border-radius: var(--radius);
        border: 2px dashed #cbd5e1;
    }
</style>

<div class="subject-show-page">
    <div class="page-container">

        <!-- 1. Hero Section -->
        <header class="hero-section animate__animated animate__fadeInDown">
            <ul class="custom-breadcrumb">
                <li><a href="{{ route('student.dashboard') }}">الرئيسية</a></li>
                <li>{{ optional($subject->stage)->name ?? 'المرحلة الدراسية' }}</li>
            </ul>
            <h1 class="hero-title">{{ $subject->name_ar ?? $subject->name }}</h1>
            <p style="margin-top: 10px; opacity: 0.9; font-weight: 600;">
                <i class="fas fa-book-open ms-2"></i>
                أهلاً بك في مساقك التعليمي المتكامل
            </p>
        </header>

        <div class="main-grid">

            <!-- العمود الأيمن (المحتوى الأساسي) -->
            <div class="content-side">

                <div class="section-header">
                    <h2><i class="fas fa-play-circle text-primary"></i> الدروس المرئية</h2>
                    <span class="badge bg-white text-dark shadow-sm rounded-pill px-3 py-2" style="font-size: 0.8rem; font-weight: 700;">
                        {{ $videos->count() }} فيديو
                    </span>
                </div>

                @forelse($videos as $video)
    <div class="video-card animate__animated animate__fadeInUp">
        <div class="video-wrapper">
            @php
                $isUrl = filter_var($video->url_path, FILTER_VALIDATE_URL);
            @endphp

            @if($isUrl)
                @if(strpos($video->url_path, 'youtube.com') !== false || strpos($video->url_path, 'youtu.be') !== false)
                    <iframe src="{{ str_replace('watch?v=', 'embed/', $video->url_path) }}" allowfullscreen></iframe>
                @else
                    <video controls>
                        <source src="{{ $video->url_path }}" type="video/mp4">
                        متصفحك لا يدعم عرض الفيديو.
                    </video>
                @endif
            @else
                <video controls>
                    <source src="{{ asset('storage/' . $video->url_path) }}" type="video/mp4">
                    متصفحك لا يدعم عرض الفيديو.
                </video>
            @endif
        </div>
        <div class="video-info">
            <div>
                <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">الدرس الحالي</span>
                <h3>{{ $video->title }}</h3>
            </div>
            <div class="text-center">
                <i class="fas fa-eye text-muted"></i>
                <span class="d-block text-muted" style="font-size: 0.75rem;">{{ $video->views_count ?? 0 }} مشاهدة</span>
            </div>
        </div>
    </div>
@empty
    <div class="empty-state">
        <img src="https://cdn-icons-png.flaticon.com/512/748/748614.png" width="80" class="mb-3" style="opacity: 0.5;">
        <p class="text-muted">لا توجد دروس فيديو مضافة حالياً.</p>
    </div>
@endforelse
            </div>

            <!-- العمود الأيسر (Sidebar) -->
            <div class="sidebar-side">

                <!-- كارت المدرس والتقدم -->
                <div class="sidebar-card animate__animated animate__fadeInLeft">
                    <div class="teacher-profile">
                        <div class="teacher-avatar-large">
                            {{ strtoupper(substr(optional($subject->teacher)->name ?? 'T', 0, 1)) }}
                        </div>
                        <h4 class="fw-bold mb-1">{{ optional($subject->teacher)->name ?? 'مدرس المادة' }}</h4>
                        <p class="text-muted small">مُعلم معتمد للمادة</p>
                    </div>

                    <div class="progress-box">
                        <div class="progress-label">
                            <span>نسبة الإنجاز</span>
                            <span style="color: var(--sub-color);">35%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-inner" style="width: 35%;"></div>
                        </div>
                        <p class="text-muted mt-3 small text-center">أحسنت! أنت تقترب من منتصف الطريق.</p>
                    </div>
                </div>

                <!-- كارت الملفات (الجزء المعدل) -->
<div class="sidebar-card animate__animated animate__fadeInLeft" style="animation-delay: 0.1s;">
    <div class="section-header mb-3">
        <h5 class="fw-bold m-0"><i class="fas fa-file-download ms-2"></i> المصادر المرفقة</h5>
    </div>

    @forelse($files as $file)
        <!-- إضافة خاصية download هنا تجبر المتصفح على التحميل -->
        <a href="{{ filter_var($file->pdf_path, FILTER_VALIDATE_URL) ? $file->pdf_path : asset('storage/' . $file->pdf_path) }}"
           download
           class="file-row">
            <div class="file-icon">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div style="flex: 1;">
                <div class="fw-bold" style="font-size: 0.9rem;">{{ $file->title ?? 'ملخص الدرس' }}</div>
                <span class="text-muted" style="font-size: 0.75rem;">اضغط للتحميل (PDF)</span>
            </div>
            <i class="fas fa-download text-muted small"></i> <!-- قمت بتغيير الأيقونة لتناسب التحميل -->
        </a>
    @empty
        <p class="text-muted text-center small py-3">لا توجد ملفات مرفقة.</p>
    @endforelse
</div>

                <!-- إحصائيات سريعة -->
                <div class="d-grid grid-2 gap-3" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="sidebar-card text-center p-3 m-0">
                        <h3 class="fw-black m-0" style="color: var(--sub-color);">{{ $videos->count() }}</h3>
                        <span class="small text-muted">فيديو</span>
                    </div>
                    <div class="sidebar-card text-center p-3 m-0">
                        <h3 class="fw-black m-0" style="color: #ef4444;">{{ $files->count() }}</h3>
                        <span class="small text-muted">ملف PDF</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
