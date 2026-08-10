@extends('layouts.app')

@section('title', $subject->name_ar ?? 'عرض المادة')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-bg: #f8f9fa;
    }
    .subject-header {
        background: var(--primary-gradient);
        color: white;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }
    .subject-header::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    .video-card {
        transition: all 0.3s ease;
        border: 1px solid #eee;
        overflow: hidden;
    }
    .video-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .video-thumbnail {
        height: 160px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .play-icon {
        font-size: 3rem;
        color: rgba(255, 255, 255, 0.8);
        transition: 0.3s;
    }
    .video-card:hover .play-icon {
        color: #fff;
        transform: scale(1.2);
    }
    .pdf-item {
        transition: 0.3s;
        border-right: 4px solid #dc3545;
    }
    .pdf-item:hover {
        background-color: #fff5f5;
    }
    .section-title {
        border-right: 5px solid #764ba2;
        padding-right: 15px;
    }
</style>

<div class="container py-5">

    <!-- ترويسة المادة (Header) -->
    <div class="subject-header p-5 mb-5 shadow-lg border-0 text-right">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-white text-primary mb-3 px-3 py-2">
                    <i class="fas fa-graduation-cap me-1"></i> {{ $subject->stage->label_ar ?? 'المرحلة الدراسية' }}
                </span>
                <h1 class="fw-bold display-5 mb-2">{{ $subject->name_ar ?? $subject->title }}</h1>
                <p class="lead opacity-75">مرحباً بك في منصة التعلم. هنا تجد كافة الدروس والمصادر التعليمية المنظمة.</p>
            </div>
            <div class="col-md-4 text-md-start mt-4 mt-md-0">
                <a href="{{ route('educational_contents.create', ['subject' => $subject->id]) }}" class="btn btn-light btn-lg rounded-pill px-4 shadow">
                    <i class="fas fa-plus-circle text-primary"></i> إضافة محتوى جديد
                </a>
            </div>
        </div>
    </div>

    <!-- قسم الدروس والمرئيات -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-4">
            <h3 class="fw-bold section-title mb-0">🎬 الدروس والمرئيات</h3>
            <span class="badge bg-dark ms-3 rounded-pill">{{ count($videos) }} فيديو</span>
        </div>

        <div class="row g-4">
            @forelse($videos as $video)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 video-card border-0 rounded-4 bg-white shadow-sm">
                        <!-- منطقة الفيديو التخيلية -->
                        <div class="video-thumbnail bg-dark">
                            <i class="fas fa-play-circle play-icon"></i>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-primary">درس #{{ $video->order }}</span>
                        </div>

                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted"><i class="fas fa-tv"></i> {{ $video->channel_name ?? 'المنصة التعليمية' }}</small>
                            </div>
                            <h5 class="fw-bold text-dark mb-3 line-clamp-2">{{ $video->title }}</h5>

                            @php
                                $videoUrl = filter_var($video->url_path, FILTER_VALIDATE_URL)
                                    ? $video->url_path
                                    : asset('storage/' . $video->url_path);
                            @endphp

                            <div class="d-grid gap-2">
                                <a href="{{ $videoUrl }}" target="_blank" class="btn btn-primary rounded-3">
                                     مشاهدة الآن <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                                @if($video->pdf_path)
                                    <a href="{{ asset('storage/' . $video->pdf_path) }}" class="btn btn-outline-danger btn-sm border-0">
                                        <i class="fas fa-file-pdf"></i> ملخص الدرس PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-light rounded-4">
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-50">
                        <p class="text-muted">لا توجد دروس مرفوعة حالياً.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- قسم المصادر الورقية -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-4">
            <h3 class="fw-bold section-title mb-0">📚 المكتبة الرقمية (PDF)</h3>
        </div>

        <div class="row g-3">
            @forelse($files as $file)
                <div class="col-md-6">
                    <div class="card pdf-item border-0 shadow-sm rounded-3 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-danger-subtle text-danger p-3 rounded-3 me-3">
                                    <i class="fas fa-file-pdf fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $file->title }}</h6>
                                    <span class="text-muted small">
                                        <i class="fas fa-weight-hanging"></i> {{ $file->file_size ?? 'متوفر' }}
                                    </span>
                                </div>
                            </div>

                            @php
                                $pdfUrl = filter_var($file->pdf_path, FILTER_VALIDATE_URL)
                                    ? $file->pdf_path
                                    : asset('storage/' . $file->pdf_path);
                            @endphp

                            <div class="d-flex gap-2">
                                <a href="{{ $pdfUrl }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                                    عرض
                                </a>
                                <a href="{{ $pdfUrl }}" download class="btn btn-danger btn-sm rounded-pill px-3">
                                    تحميل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-4 text-center text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3 opacity-20"></i>
                    <p>لا توجد ملفات متوفرة لهذه المادة.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- إضافة FontAwesome للأيقونات -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
