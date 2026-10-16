@extends('layouts.app')

@section('title', $subject->name_ar)

@section('content')
<div class="subject-dashboard-container">

    <!-- Top Navigation & Header -->
    <div class="subject-header-bar">
        <div class="header-titles">
            <nav class="breadcrumb-nav">
                <a>{{ __('المراحل') }}</a> /
                <a href="/stages/{{ $subject->stage_id }}">{{ $subject->stage?->label_ar ?? 'المرحلة' }}</a> /
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
                <span>📑</span>{{ __('الملفات والملخصات') }}
            </a>
            <!-- زر المساعد الذكي -->
            <button type="button" class="btn-action" onclick="openAiModal()" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;" title="{{ __('مساعد المادة الذكي') }}">
                <span>🤖</span>{{ __('المساعد الذكي') }}
            </button>
        </div>
    </div>

    <!-- Teacher Info & Quick Stats Card -->
    <div class="teacher-hero-card">
        <div class="teacher-profile">
            @if($subject->hasAssignedTeacher())
                <div class="teacher-avatar">
                    {{ mb_substr($subject->teacher_display_name, 0, 1) }}
                </div>
                <div class="teacher-info">
                    <span class="badge-teacher">{{ __('معلّم المادة المعتمد') }}</span>
                    <h3 class="teacher-name">{{ $subject->teacher_display_name }}</h3>
                    <p class="teacher-desc">مدرس مساق {{ $subject->name_ar }} - {{ $subject->stage?->label_ar ?? 'الصف الثاني عشر' }}</p>
                </div>
            @else
                <div class="teacher-avatar" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                    ⏳
                </div>
                <div class="teacher-info">
                    <span class="badge-teacher" style="background: #fef3c7; color: #b45309;">{{ __('قريباً بإذن الله') }}</span>
                    <h3 class="teacher-name" style="color: #92400e;">{{ __('نخبة من خيرة معلّمي التوجيهي قريباً') }}</h3>
                    <p class="teacher-desc">نعمل حالياً على اعتماد أفضل الكفاءات التعليمية لمساق {{ $subject->name_ar }} لتوفير تجربة تعليمية استثنائية وشاملة.</p>
                </div>
            @endif
        </div>

        <div class="hero-stats">
            <div class="stat-box">
                <span class="stat-number">{{ $videos->count() }}</span>
                <span class="stat-label">{{ __('دروس فيديو') }}</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">{{ $files->count() }}</span>
                <span class="stat-label">{{ __('ملفات مرفقة') }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (Grid) -->
    <div class="main-grid-layout">

        <!-- Column Right: Videos -->
        <div class="primary-column">

            <div class="section-title-wrapper">
                <h3 class="section-title">
                    <span class="title-icon">🎥</span>{{ __('دروس الفيديو الشارحة') }}</h3>
                <span class="chip-count">{{ $videos->count() }} فيديو متوفر</span>
            </div>

            <!-- Videos Grid -->
            <div class="videos-grid">
                @forelse($videos as $index => $video)
                    <div class="video-card">
                        <div class="custom-video-wrapper" id="custom_wrap_{{ $video->id }}">
                            @php
                                $url = trim((string)$video->url_path);
                                if (in_array(strtolower($url), ['undefined', 'null', 'none', 'false', '0', 'undefined.mp4', 'null.mp4'])) {
                                    $url = '';
                                }
                                $isYoutube = !empty($url) && \Illuminate\Support\Str::contains($url, ['youtube.com', 'youtube-nocookie.com', 'youtu.be', 'youtube']);
                            @endphp

                            @if($isYoutube)
                                @php
                                    preg_match('/(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:watch\?.*v=|embed\/|shorts\/|live\/|v\/)|[?&]v=)([^"&?\/\s]{11})/i', $url, $matches);
                                    $ytId = $matches[1] ?? $video->youtube_id ?? $url;
                                    $ytSafeSrc = "https://www.youtube-nocookie.com/embed/{$ytId}?enablejsapi=1&rel=0&modestbranding=1&playsinline=1&controls=1";
                                @endphp
                                <iframe class="custom-iframe" 
                                        id="pub_yt_{{ $video->id }}"
                                        src="{{ $ytSafeSrc }}" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                        allowfullscreen
                                        referrerpolicy="strict-origin-when-cross-origin"
                                        loading="lazy"
                                        style="position: absolute; inset: 0; width: 100%; height: 100%; border: none; pointer-events: auto !important;">
                                </iframe>
                            @elseif(!empty($url))
                                @php
                                    $directUrl = \App\Support\MediaHelper::videoStreamUrl($url);
                                    $directFallback = \App\Support\MediaHelper::url($url);
                                @endphp
                                @if($directUrl)
                                    <video class="custom-video-element" id="pub_vid_{{ $video->id }}" controls preload="metadata" controlsList="nodownload" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain;">
                                        <source src="{{ $directUrl }}">
                                        @if($directFallback && $directFallback !== $directUrl)
                                            <source src="{{ $directFallback }}">
                                        @endif
                                    </video>
                                @else
                                    <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #0f172a; color: #94a3b8; padding: 20px; text-align: center;">
                                        <i class="fa-solid fa-video-slash" style="font-size: 2rem; color: #64748b; margin-bottom: 8px;"></i>
                                        <span style="font-size: 0.85rem; color: #cbd5e1; font-weight: 700;">{{ __('جاري تجهيز بث الفيديو') }}</span>
                                    </div>
                                @endif
                            @else
                                <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #0f172a; color: #94a3b8; padding: 20px; text-align: center;">
                                    <i class="fa-solid fa-video-slash" style="font-size: 2rem; color: #64748b; margin-bottom: 8px;"></i>
                                    <span style="font-size: 0.85rem; color: #cbd5e1; font-weight: 700;">{{ __('جاري تجهيز ورفع الفيديو من المعلم') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="video-card-body">
                            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; padding: 4px 12px; border-radius: 9999px; font-size: 0.78rem; font-weight: 800;">
                                        <i class="fa-solid fa-play"></i> {{ __('الدرس') }} #{{ $video->order ?? ($index + 1) }}
                                    </span>
                                    <span style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">
                                        🎓 {{ $video->channel_name ?? $subject->name_ar ?? __('شرح معتمد') }}
                                    </span>
                                </div>
                                <span style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; padding: 3px 9px; border-radius: 9999px; font-size: 0.72rem; font-weight: 800;">
                                    1080p HD
                                </span>
                            </div>

                            <h4 class="video-title" style="margin: 0 0 12px; font-size: 1.15rem; font-weight: 800; color: #0f172a;">{{ $video->title }}</h4>

                            @if(!empty($video->pdf_path))
                                <div style="margin-top: 8px; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                                    <a href="{{ route('content.download', $video->id) }}" class="ed-btn-lecture-pdf">
                                        <i class="fa-solid fa-file-pdf"></i>
                                        <span>{{ __('تحميل الملزمة الدراسية (PDF)') }}</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state-box">
                        <div class="empty-icon">📂</div>
                        <h4>{{ __('لا توجد دروس فيديو مضافة حالياً') }}</h4>
                        <p>{{ __('لم يقم المعلم بفرز أو إضافة دروس فيديو لهذه المادة حتى الآن.') }}</p>
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

                <p class="progress-info-text">{{ __('لقد أكملت') }}<strong>35%</strong>{{ __('من متطلبات هذه المادة') }}</p>
            </div>

        </div>

    </div>

</div>

<!-- AI Assistant Modal Popup -->
<div id="aiModal" class="ai-modal-overlay" style="display: none;" onclick="if(event.target===this) closeAiModal()">
    <div class="ai-modal-container">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-bottom: 1px solid #e2e8f0; background: #fafafa; border-radius: 16px 16px 0 0;">
            <div style="font-weight: 800; font-size: 1rem; color: #1e3a8a; display: flex; align-items: center; gap: 8px;">
                <span>🤖</span> {{ __('المساعد الذكي لمادة') }} {{ $subject->name_ar }}
            </div>
            <button type="button" onclick="closeAiModal()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #64748b; line-height: 1;">✕</button>
        </div>
        <div class="ai-modal-body" id="aiChatBox">
            <div class="ai-message system">{{ __('أهلاً بك! أنا مساعدك الذكي لمادة') }} <strong>{{ $subject->name_ar }}</strong>{{ __('. كيف يمكنني مساعدتك اليوم؟ اسألني أي سؤال يخص المنهاج وسأجيبك فوراً!') }}</div>
        </div>
        <div class="ai-modal-footer">
            <input type="text" id="aiInput" placeholder="{{ __('اكتب سؤالك هنا...') }}" onkeypress="handleAiKeyPress(event)">
            <button type="button" class="btn-send-ai" onclick="sendAiMessage()">{{ __('إرسال') }}</button>
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
        user-select: none;
        -webkit-user-select: none;
    }

    .custom-video-wrapper:fullscreen,
    .custom-video-wrapper:-webkit-full-screen {
        width: 100vw !important;
        height: 100vh !important;
        background: #000 !important;
    }

    .custom-video-element, .custom-iframe {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border: none;
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

    @media (max-width: 768px) {
        .subject-header-bar {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .header-actions {
            width: 100%;
        }
        .header-actions .btn-action {
            width: 100%;
            justify-content: center;
        }
        .teacher-hero-card {
            flex-direction: column;
            align-items: stretch;
            padding: 16px 14px;
            gap: 14px;
        }
        .hero-stats {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .stat-box {
            padding: 8px 12px;
        }
        .videos-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }
        .ai-chat-modal {
            width: 95vw;
            max-height: 85vh;
        }
    .ed-btn-lecture-video-download,
    [data-download-video-external] {
        display: none !important;
    }
    video::-internal-media-controls-download-button {
        display: none !important;
    }
    video::-webkit-media-controls-download-button {
        display: none !important;
    }
    .ed-btn-lecture-pdf {
        color: #b91c1c;
        font-size: 0.82rem;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fef2f2;
        padding: 7px 16px;
        border-radius: 8px;
        border: 1px solid #fecaca;
        transition: all 0.2s ease;
    }
    .ed-btn-lecture-pdf:hover {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #991b1b;
    }
</style>

<!-- Scripts: مشغل فيديو المنصة الآمن كلياً -->
<script>
    let pubYtPlayers = {};
    let pubYtIntervals = {};

    function onYouTubeIframeAPIReady() {
        document.querySelectorAll('iframe[id^="pub_yt_"]').forEach(iframe => {
            initPubYtPlayer(iframe.id);
        });
    }

    function initPubYtPlayer(iframeId) {
        const vidId = iframeId.replace('pub_yt_', '');
        if (pubYtPlayers[vidId]) return pubYtPlayers[vidId];
        if (window.YT && window.YT.Player) {
            try {
                pubYtPlayers[vidId] = new YT.Player(iframeId, {
                    events: {
                        'onStateChange': function(e) {
                            handlePubYtStateChange(vidId, e.data);
                        }
                    }
                });
                return pubYtPlayers[vidId];
            } catch(e) {}
        }
        return null;
    }

    function handlePubYtStateChange(vidId, state) {
        const icon = document.getElementById(`pub_play_icon_${vidId}`);
        const btn = document.getElementById(`pub_btn_${vidId}`);
        if (state === 1) { // مشتغل
            if (icon) icon.style.opacity = '0';
            if (btn) btn.textContent = '❚❚';
            startPubYtTracking(vidId);
        } else { // متوقف
            if (icon) {
                icon.style.opacity = '1';
                icon.innerHTML = (state === 0) ? '<i class="fa-solid fa-rotate-right"></i>' : '<i class="fa-solid fa-play"></i>';
            }
            if (btn) btn.textContent = '▶';
            stopPubYtTracking(vidId);
        }
    }

    function toggleSubjectVideo(vidId, isYt) {
        if (isYt) {
            const yt = pubYtPlayers[vidId] || initPubYtPlayer(`pub_yt_${vidId}`);
            if (yt && typeof yt.getPlayerState === 'function') {
                const state = yt.getPlayerState();
                if (state === 1) yt.pauseVideo(); else yt.playVideo();
                return;
            }
            const iframe = document.getElementById(`pub_yt_${vidId}`);
            if (iframe && iframe.contentWindow) {
                const icon = document.getElementById(`pub_play_icon_${vidId}`);
                const isPlaying = icon && icon.style.opacity === '0';
                iframe.contentWindow.postMessage(JSON.stringify({
                    event: 'command',
                    func: isPlaying ? 'pauseVideo' : 'playVideo',
                    args: []
                }), '*');
                if (icon) icon.style.opacity = isPlaying ? '1' : '0';
                const btn = document.getElementById(`pub_btn_${vidId}`);
                if (btn) btn.textContent = isPlaying ? '▶' : '❚❚';
                if (!isPlaying) startPubYtTracking(vidId); else stopPubYtTracking(vidId);
            }
        } else {
            const video = document.getElementById(`pub_vid_${vidId}`);
            const icon = document.getElementById(`pub_play_icon_${vidId}`);
            const btn = document.getElementById(`pub_btn_${vidId}`);
            if (!video) return;
            if (video.paused) {
                video.play();
                if (btn) btn.textContent = '❚❚';
                if (icon) icon.style.opacity = '0';
            } else {
                video.pause();
                if (btn) btn.textContent = '▶';
                if (icon) icon.style.opacity = '1';
            }
        }
    }

    function startPubYtTracking(vidId) {
        stopPubYtTracking(vidId);
        pubYtIntervals[vidId] = setInterval(() => {
            const yt = pubYtPlayers[vidId];
            if (yt && typeof yt.getCurrentTime === 'function' && typeof yt.getDuration === 'function') {
                const cur = yt.getCurrentTime() || 0;
                const dur = yt.getDuration() || 0;
                const curEl = document.getElementById(`pub_cur_time_${vidId}`);
                const durEl = document.getElementById(`pub_dur_time_${vidId}`);
                const seekSlider = document.getElementById(`pub_seek_${vidId}`);
                if (curEl) curEl.textContent = formatPubTime(cur);
                if (durEl && dur > 0) durEl.textContent = formatPubTime(dur);
                if (seekSlider && dur > 0) seekSlider.value = (cur / dur) * 100;
            }
        }, 500);
    }

    function stopPubYtTracking(vidId) {
        if (pubYtIntervals[vidId]) {
            clearInterval(pubYtIntervals[vidId]);
            delete pubYtIntervals[vidId];
        }
    }

    function seekSubjectVideo(vidId, isYt, percentage) {
        if (isYt) {
            const yt = pubYtPlayers[vidId] || initPubYtPlayer(`pub_yt_${vidId}`);
            if (yt && typeof yt.getDuration === 'function') {
                const dur = yt.getDuration() || 0;
                yt.seekTo((percentage / 100) * dur, true);
            } else {
                const iframe = document.getElementById(`pub_yt_${vidId}`);
                if (iframe && iframe.contentWindow) {
                    iframe.contentWindow.postMessage(JSON.stringify({
                        event: 'command',
                        func: 'seekTo',
                        args: [parseFloat(percentage), true]
                    }), '*');
                }
            }
        } else {
            const video = document.getElementById(`pub_vid_${vidId}`);
            if (video && video.duration) {
                video.currentTime = (percentage / 100) * video.duration;
            }
        }
    }

    function toggleSubjectFullscreen(btn) {
        const wrap = btn.closest('.custom-video-wrapper');
        if (!wrap) return;
        if (!document.fullscreenElement && !document.webkitFullscreenElement) {
            if (wrap.requestFullscreen) wrap.requestFullscreen();
            else if (wrap.webkitRequestFullscreen) wrap.webkitRequestFullscreen();
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
            else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
        }
    }

    function formatPubTime(seconds) {
        if (!seconds || isNaN(seconds)) return '00:00';
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins < 10 ? '0' : ''}${mins}:${secs < 10 ? '0' : ''}${secs}`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.custom-video-element').forEach(video => {
            const vidId = video.id.replace('pub_vid_', '');
            const curEl = document.getElementById(`pub_cur_time_${vidId}`);
            const durEl = document.getElementById(`pub_dur_time_${vidId}`);
            const seekSlider = document.getElementById(`pub_seek_${vidId}`);
            
            video.addEventListener('loadedmetadata', () => {
                if (durEl) durEl.textContent = formatPubTime(video.duration);
            });
            video.addEventListener('timeupdate', () => {
                if (video.duration) {
                    if (seekSlider) seekSlider.value = (video.currentTime / video.duration) * 100;
                    if (curEl) curEl.textContent = formatPubTime(video.currentTime);
                }
            });
            video.addEventListener('ended', () => {
                const icon = document.getElementById(`pub_play_icon_${vidId}`);
                const btn = document.getElementById(`pub_btn_${vidId}`);
                if (icon) {
                    icon.style.opacity = '1';
                    icon.innerHTML = '<i class="fa-solid fa-rotate-right"></i>';
                }
                if (btn) btn.textContent = '▶';
            });
        });
    });

    // منع النقر بالزر الأيمن على مشغل الفيديو نهائياً
    document.addEventListener('contextmenu', function(e) {
        if (e.target.closest('.custom-video-wrapper, iframe')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);



    // دوال المساعد الذكي لمادة المساق
    function openAiModal() {
        const modal = document.getElementById('aiModal');
        if (modal) modal.style.display = 'flex';
        const inp = document.getElementById('aiInput');
        if (inp) setTimeout(() => inp.focus(), 100);
    }

    function closeAiModal() {
        const modal = document.getElementById('aiModal');
        if (modal) modal.style.display = 'none';
    }

    function handleAiKeyPress(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendAiMessage();
        }
    }

    function sendAiMessage() {
        const inp = document.getElementById('aiInput');
        const box = document.getElementById('aiChatBox');
        if (!inp || !box) return;
        const text = inp.value.trim();
        if (!text) return;

        // إضافة رسالة الطالب
        const userMsg = document.createElement('div');
        userMsg.className = 'ai-message user';
        userMsg.textContent = text;
        box.appendChild(userMsg);
        inp.value = '';
        box.scrollTop = box.scrollHeight;

        // مؤشر التفكير
        const typing = document.createElement('div');
        typing.className = 'ai-message system';
        typing.id = 'aiTypingIndicator';
        typing.textContent = 'المساعد الذكي يفكّر في الإجابة... 💭';
        box.appendChild(typing);
        box.scrollTop = box.scrollHeight;

        setTimeout(() => {
            const ind = document.getElementById('aiTypingIndicator');
            if (ind) ind.remove();

            const reply = document.createElement('div');
            reply.className = 'ai-message system';
            const escaped = text.replace(/[&<>"']/g, function(m) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
            });
            reply.innerHTML = `أهلاً بك يا بطل! بخصوص استفسارك حول: "<strong>${escaped}</strong>"، ننصحك بمراجعة شروحات الدروس المرئية لهذه الوحدة في القائمة، والاطلاع على بنوك الأسئلة الوزارية المحلولة المرفقة. كما يمكنك مراسلة أستاذ المادة مباشرة للحصول على شرح إضافي! 🌟`;
            box.appendChild(reply);
            box.scrollTop = box.scrollHeight;
        }, 500);
    // إزالة أي أزرار تحميل خارجي للملفات الخام لضمان الحفظ حصرياً داخل المنصة
    function purgeExternalDownloadButtons() {
        document.querySelectorAll('.ed-btn-lecture-video-download, a[download][href*="downloadVideo"], a[href*="force_download=1"]').forEach(el => el.remove());
    }
    purgeExternalDownloadButtons();
    document.addEventListener('DOMContentLoaded', purgeExternalDownloadButtons);
</script>
<script src="https://www.youtube.com/iframe_api"></script>
@endsection
