@extends('layouts.app')

@section('title', $subject->name_ar)

@section('content')
<div class="subject-dashboard-container">

    <!-- Top Navigation & Header -->
    <div class="subject-header-bar">
        <div class="header-titles">
            <nav class="breadcrumb-nav">
                <a>المراحل</a> /
                <a href="/stages/{{ $subject->stage_id }}">{{ $subject->stage->label_ar ?? 'المرحلة' }}</a> /
                <span class="active">{{ $subject->name_ar }}</span>
            </nav>
            <h1 class="subject-title">
                <span class="subject-icon">{{ $subject->icon ?? '📐' }}</span>
                مادة {{ $subject->name_ar }}
            </h1>
        </div>

        <div class="header-actions">
            <!-- التعديل هنا: زر يوجه مباشرة لصفحة الملفات والملخصات المستقلة -->
            <a href="{{ route('subject.files', $subject->id) }}" class="btn-action btn-outline">
                <span>📑</span> الملفات والملخصات
            </a>


        </div>
    </div>

    <!-- Teacher Info & Quick Stats Card -->
    <div class="teacher-hero-card">
        <div class="teacher-profile">
            <div class="teacher-avatar">
                {{ mb_substr($subject->teacher->name ?? 'معتز اسليم', 0, 1) }}
            </div>
            <div class="teacher-info">
                <span class="badge-teacher">المدرس المسؤول</span>
                <h3 class="teacher-name">{{ $subject->teacher->name ?? 'أ. معتز اسليم' }}</h3>
                <p class="teacher-desc">مدرس مساق {{ $subject->name_ar }} - {{ $subject->stage->label_ar ?? 'الصف الثاني عشر' }}</p>
            </div>
        </div>

        <div class="hero-stats">
            <div class="stat-box">
                <span class="stat-number">{{ $videos->count() }}</span>
                <span class="stat-label">دروس فيديو</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">{{ $files->count() }}</span>
                <span class="stat-label">ملفات مرفقة</span>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (Grid) -->
    <div class="main-grid-layout">

        <!-- Column Right: Videos -->
        <div class="primary-column">

            <div class="section-title-wrapper">
                <h3 class="section-title">
                    <span class="title-icon">🎥</span> دروس الفيديو الشارحة
                </h3>
                <span class="chip-count">{{ $videos->count() }} فيديو متوفر</span>
            </div>

            <!-- Videos Grid -->
            <div class="videos-grid">
                @forelse($videos as $index => $video)
                    <div class="video-card">
                        <div class="custom-video-wrapper">
                            @php
                                $url = $video->url_path;
                                $isYoutube = \Illuminate\Support\Str::contains($url, ['youtube.com', 'youtu.be']);
                            @endphp

                            @if($isYoutube)
                                @php
                                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
                                    $ytId = $matches[1] ?? $url;
                                @endphp
                                <iframe class="custom-iframe" src="https://www.youtube.com/embed/{{ $ytId }}" frameborder="0" allowfullscreen></iframe>
                            @else
                                <video class="custom-video-element" preload="metadata" controlsList="nodownload">
                                    <source src="{{ route('video.stream', ['filename' => $video->url_path]) }}" type="video/mp4">
                                </video>

                                <!-- شريط التحكم المخصص المانع للـ IDM والمُعزّز للتقديم والتأخير الدقيق -->
                                <div class="custom-player-controls">
                                    <button type="button" class="btn-play-pause">▶</button>
                                    <span class="time-display current-time">00:00</span>

                                    <!-- شريط السحب والدقائق -->
                                    <input type="range" class="seek-slider" value="0" min="0" max="100" step="0.1">

                                    <span class="time-display total-duration">00:00</span>
                                    <button type="button" class="btn-fullscreen">⛶</button>
                                </div>
                            @endif
                        </div>

                        <div class="video-card-body">
                            <h4 class="video-title">{{ $video->title }}</h4>
                            <p class="video-channel">
                                <span>📺</span> {{ $video->channel_name ?? 'القناة التعليمية' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-box">
                        <div class="empty-icon">📂</div>
                        <h4>لا توجد دروس فيديو مضافة حالياً</h4>
                        <p>لم يقم المعلم بفرز أو إضافة دروس فيديو لهذه المادة حتى الآن.</p>
                    </div>
                @endforelse
            </div>

        </div>

        <!-- Column Left: Files & Progress -->
        <div class="sidebar-column" id="summariesSection">



            <!-- Progress Tracker Card -->
            <div class="sidebar-card progress-card">
                <div class="sidebar-card-header">
                    <h3 class="progress-title">📊 حالة التقدّم في المساق</h3>
                </div>

                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: 35%;"></div>
                </div>

                <p class="progress-info-text">لقد أكملت <strong>35%</strong> من متطلبات هذه المادة</p>
            </div>

        </div>

    </div>

</div>

<!-- AI Assistant Modal Popup -->
<div id="aiModal" class="ai-modal-overlay" style="display: none;">
    <div class="ai-modal-container">

        <div class="ai-modal-body" id="aiChatBox">
            <div class="ai-message system">
                أهلاً بك! أنا مساعدك الذكي لمادة <strong>{{ $subject->name_ar }}</strong>. كيف يمكنني مساعدتك اليوم؟
            </div>
        </div>
        <div class="ai-modal-footer">
            <input type="text" id="aiInput" placeholder="اكتب سؤالك هنا..." onkeypress="handleAiKeyPress(event)">
            <button type="button" class="btn-send-ai" onclick="sendAiMessage()">إرسال</button>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
    html {
        scroll-behavior: smooth;
    }

    :root {
        --primary-color: #2563eb;
        --primary-dark: #1d4ed8;
        --text-dark: #0f172a;
        --text-muted: #64748b;
        --bg-light: #f8fafc;
        --card-bg: #ffffff;
        --border-color: #e2e8f0;
    }

    .subject-dashboard-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
        max-width: 1280px;
        margin: 0 auto;
        padding: 10px 15px;
    }

    /* Header Bar */
    .subject-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 15px;
    }

    .breadcrumb-nav {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 8px;
    }

    .breadcrumb-nav a { color: var(--text-muted); text-decoration: none; }
    .breadcrumb-nav .active { color: var(--primary-color); font-weight: 700; }

    .subject-title {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-actions { display: flex; gap: 12px; }

    .btn-action {
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.15s ease, background-color 0.2s ease;
        text-decoration: none;
    }

    .btn-action:active { transform: scale(0.97); }

    .btn-outline { background-color: var(--card-bg); color: var(--text-dark); border: 1px solid var(--border-color); }
    .btn-outline:hover { background-color: #f1f5f9; }

    .btn-ai { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #ffffff; }
    .btn-ai:hover { opacity: 0.95; }

    /* Teacher Card */
    .teacher-hero-card {
        background: linear-gradient(120deg, #1e293b 0%, #0f172a 100%);
        color: #ffffff;
        padding: 22px 28px;
        border-radius: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .teacher-profile { display: flex; align-items: center; gap: 16px; }

    .teacher-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb 0%, #38bdf8 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        font-weight: 800;
    }

    .badge-teacher {
        background: rgba(56, 189, 248, 0.15);
        color: #38bdf8;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
    }

    .teacher-name { margin: 0; font-size: 1.25rem; font-weight: 800; }
    .teacher-desc { margin: 2px 0 0 0; font-size: 0.85rem; color: #94a3b8; }

    .hero-stats { display: flex; gap: 12px; }
    .stat-box {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 10px 20px;
        border-radius: 14px;
        text-align: center;
    }
    .stat-number { display: block; font-size: 1.4rem; font-weight: 800; color: #38bdf8; }
    .stat-label { font-size: 0.75rem; color: #cbd5e1; }

    /* Layout */
    .main-grid-layout { display: grid; grid-template-columns: 1fr; gap: 25px; }
    @media (min-width: 992px) { .main-grid-layout { grid-template-columns: 2fr 1fr; } }

    .primary-column, .sidebar-column { display: flex; flex-direction: column; gap: 22px; }

    .section-title-wrapper { display: flex; justify-content: space-between; align-items: center; }
    .section-title { font-size: 1.25rem; font-weight: 800; color: var(--text-dark); margin: 0; }
    .chip-count { background-color: #e0f2fe; color: #0369a1; font-size: 0.8rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; }

    /* Video Player Styling */
    .videos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }

    .video-card {
        background: var(--card-bg);
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    .custom-video-wrapper {
        position: relative;
        aspect-ratio: 16/9;
        background-color: #000;
        overflow: hidden;
    }

    .custom-video-element, .custom-iframe {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Custom Controls Bar */
    .custom-player-controls {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        z-index: 10;
        direction: ltr;
    }

    .btn-play-pause, .btn-fullscreen {
        background: none;
        border: none;
        color: #fff;
        font-size: 1rem;
        cursor: pointer;
        padding: 0;
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .time-display {
        color: #e2e8f0;
        font-size: 0.75rem;
        font-family: monospace;
    }

    .seek-slider {
        flex: 1;
        -webkit-appearance: none;
        appearance: none;
        height: 5px;
        border-radius: 5px;
        background: rgba(255, 255, 255, 0.3);
        outline: none;
        cursor: pointer;
        transition: height 0.1s ease;
    }

    .seek-slider:hover { height: 8px; }

    .seek-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #2563eb;
        cursor: pointer;
        box-shadow: 0 0 6px rgba(37, 99, 235, 0.8);
    }

    .seek-slider::-moz-range-thumb {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #2563eb;
        cursor: pointer;
    }

    .video-card-body { padding: 16px; }
    .video-title { font-size: 1rem; font-weight: 700; color: var(--text-dark); margin: 0 0 6px 0; }
    .video-channel { font-size: 0.8rem; color: var(--text-muted); margin: 0; }

    /* Sidebar Cards */
    .sidebar-card { background: var(--card-bg); border-radius: 20px; padding: 20px; border: 1px solid var(--border-color); }
    .sidebar-card-header h3 { font-size: 1.1rem; font-weight: 800; color: var(--text-dark); margin: 0 0 16px 0; }
    .files-list { display: flex; flex-direction: column; gap: 12px; }
    .file-card-item { display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: var(--bg-light); border-radius: 12px; }
    .file-meta { flex: 1; margin: 0 10px; }
    .file-title { margin: 0; font-size: 0.88rem; font-weight: 700; color: var(--text-dark); }
    .file-size-badge { font-size: 0.72rem; color: var(--text-muted); }
    .btn-download-file { background-color: var(--primary-color); color: #fff; padding: 6px 14px; border-radius: 8px; text-decoration: none; font-size: 0.75rem; font-weight: 700; }

    .view-all-files-link {
        display: block;
        margin-top: 14px;
        text-align: center;
        font-size: 0.82rem;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 700;
        transition: opacity 0.2s;
    }
    .view-all-files-link:hover { opacity: 0.85; }

    .progress-card { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0; }
    .progress-title { color: #166534 !important; }
    .progress-bar-container { background-color: #fff; height: 10px; border-radius: 10px; overflow: hidden; margin: 14px 0 10px 0; }
    .progress-bar-fill { background-color: #16a34a; height: 100%; }
    .progress-info-text { font-size: 0.8rem; color: #15803d; margin: 0; }
    .empty-state-box { grid-column: 1 / -1; background: var(--card-bg); border: 2px dashed var(--border-color); border-radius: 16px; padding: 40px; text-align: center; color: var(--text-muted); }
    .empty-icon { font-size: 2.5rem; margin-bottom: 8px; }
    .empty-text { text-align: center; color: var(--text-muted); font-size: 0.85rem; margin: 10px 0; }

    /* AI Modal Styles */
    .ai-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
    }

    .ai-modal-container {
        background: #ffffff;
        width: 100%;
        max-width: 480px;
        height: 540px;
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
    }

    .ai-modal-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        color: #fff;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 700;
    }

    .btn-close-modal {
        background: none;
        border: none;
        color: #fff;
        font-size: 1.2rem;
        cursor: pointer;
    }

    .ai-modal-body {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background-color: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .ai-message {
        padding: 12px 16px;
        border-radius: 14px;
        font-size: 0.9rem;
        line-height: 1.5;
        max-width: 85%;
    }

    .ai-message.system {
        background: #e0e7ff;
        color: #3730a3;
        align-self: flex-start;
    }

    .ai-message.user {
        background: #2563eb;
        color: #ffffff;
        align-self: flex-end;
    }

    .ai-modal-footer {
        padding: 12px 16px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 10px;
        background: #fff;
    }

    .ai-modal-footer input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        outline: none;
    }

    .btn-send-ai {
        background: #2563eb;
        color: white;
        border: none;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
    }
</style>

<!-- Scripts -->
<script>

    document.addEventListener('DOMContentLoaded', () => {
        const wrappers = document.querySelectorAll('.custom-video-wrapper');

        wrappers.forEach(wrapper => {
            const video = wrapper.querySelector('.custom-video-element');
            if (!video) return;

            const playBtn = wrapper.querySelector('.btn-play-pause');
            const seekSlider = wrapper.querySelector('.seek-slider');
            const currentTimeEl = wrapper.querySelector('.current-time');
            const durationEl = wrapper.querySelector('.total-duration');
            const fullscreenBtn = wrapper.querySelector('.btn-fullscreen');

            const formatTime = (seconds) => {
                const mins = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${mins < 10 ? '0' : ''}${mins}:${secs < 10 ? '0' : ''}${secs}`;
            };

            playBtn.addEventListener('click', () => {
                if (video.paused) {
                    video.play();
                    playBtn.textContent = '❚❚';
                } else {
                    video.pause();
                    playBtn.textContent = '▶';
                }
            });

            video.addEventListener('loadedmetadata', () => {
                durationEl.textContent = formatTime(video.duration);
            });

            video.addEventListener('timeupdate', () => {
                if (video.duration && !seekSlider.isDragging) {
                    const percentage = (video.currentTime / video.duration) * 100;
                    seekSlider.value = percentage;
                    currentTimeEl.textContent = formatTime(video.currentTime);
                }
            });

            seekSlider.addEventListener('mousedown', () => seekSlider.isDragging = true);
            seekSlider.addEventListener('mouseup', () => seekSlider.isDragging = false);

            seekSlider.addEventListener('input', () => {
                if (video.duration) {
                    const seekTo = (seekSlider.value / 100) * video.duration;
                    video.currentTime = seekTo;
                    currentTimeEl.textContent = formatTime(seekTo);
                }
            });

            fullscreenBtn.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    wrapper.requestFullscreen().catch(err => console.log(err));
                } else {
                    document.exitFullscreen();
                }
            });
        });
    });
</script>
@endsection
