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
                        <div class="custom-video-wrapper" id="custom_wrap_{{ $video->id }}" onmousemove="registerPubPlayerActivity('{{ $video->id }}')" ontouchstart="registerPubPlayerActivity('{{ $video->id }}')" onmouseleave="handlePubPlayerMouseLeave('{{ $video->id }}')">
                            @php
                                $url = trim((string)$video->url_path);
                                if (in_array(strtolower($url), ['undefined', 'null', 'none', 'false', '0', 'undefined.mp4', 'null.mp4'])) {
                                    $url = '';
                                }
                                $isYoutube = !empty($url) && \Illuminate\Support\Str::contains($url, ['youtube.com', 'youtube-nocookie.com', 'youtu.be', 'youtube']);
                            @endphp

                            @if($isYoutube)
                                @php
                                    preg_match('/(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:watch\?.*v=|embed\/|shorts\/|live\/)|[?&]v=)([^"&?\/\s]{11})/i', $url, $matches);
                                    $ytId = $matches[1] ?? $video->youtube_id ?? $url;
                                    $ytSafeSrc = "https://www.youtube-nocookie.com/embed/{$ytId}?enablejsapi=1&rel=0&modestbranding=1&iv_load_policy=3&controls=0&showinfo=0&fs=0&disablekb=1&playsinline=1";
                                @endphp
                                <div class="ed-yt-frame-container" id="shield_wrap_{{ $video->id }}">
                                    <iframe class="custom-iframe" 
                                            id="pub_yt_{{ $video->id }}"
                                            src="{{ $ytSafeSrc }}" 
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                            allowfullscreen
                                            referrerpolicy="strict-origin-when-cross-origin"
                                            loading="lazy">
                                    </iframe>
                                </div>

                                {{-- درع النقر التفاعلي الصافي --}}
                                <div class="ed-clean-click-shield" id="click_shield_{{ $video->id }}" onclick="toggleSubjectVideo('{{ $video->id }}', true)">
                                    <div class="ed-pulse-icon" id="pulse_{{ $video->id }}">
                                        <i class="fa-solid fa-play"></i>
                                    </div>
                                </div>

                                {{-- غلاف البداية الراقي الذي يحجب أي أزرار أو عناوين خارجية قبل التشغيل --}}
                                <div class="ed-cover-start-banner" id="cover_{{ $video->id }}" onclick="startPubPlaybackFirstTime('{{ $video->id }}')">
                                    <div class="ed-cover-play-glow">
                                        <i class="fa-solid fa-play"></i>
                                    </div>
                                    <h4 class="ed-cover-title">{{ $video->title }}</h4>
                                    <span class="ed-cover-sub">
                                        <i class="fa-solid fa-wand-magic-sparkles" style="color: #60a5fa;"></i>
                                        {{ __('انقر للمشاهدة بأعلى جودة فائقة الدقة 1080p') }}
                                    </span>
                                </div>

                                {{-- شريط تحكم المنصة المدمج والفخم بنظام الاستوديو --}}
                                <div class="ed-platform-controls-bar" id="controls_bar_{{ $video->id }}">
                                    <button type="button" class="ed-ctrl-btn" id="pub_btn_{{ $video->id }}" onclick="toggleSubjectVideo('{{ $video->id }}', true)" title="{{ __('تشغيل / إيقاف') }}">
                                        <i class="fa-solid fa-play"></i>
                                    </button>
                                    <button type="button" class="ed-ctrl-btn" onclick="seekPubRelative('{{ $video->id }}', -10)" title="{{ __('تأخير 10 ثوانٍ') }}">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                    <button type="button" class="ed-ctrl-btn" onclick="seekPubRelative('{{ $video->id }}', 10)" title="{{ __('تقديم 10 ثوانٍ') }}">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </button>
                                    <span class="ed-ctrl-time" id="time_txt_{{ $video->id }}">00:00 / 00:00</span>
                                    <div class="ed-ctrl-seek-wrap">
                                        <input type="range" class="ed-ctrl-seeker" id="seek_range_{{ $video->id }}" min="0" max="100" step="0.1" value="0" oninput="seekSubjectVideo('{{ $video->id }}', true, this.value)">
                                    </div>
                                    <div class="ed-volume-cluster">
                                        <button type="button" class="ed-ctrl-btn" id="mute_btn_{{ $video->id }}" onclick="togglePubMute('{{ $video->id }}')" title="{{ __('كتم / تشغيل الصوت') }}">
                                            <i class="fa-solid fa-volume-high"></i>
                                        </button>
                                        <input type="range" class="ed-volume-range" id="vol_range_{{ $video->id }}" min="0" max="100" value="100" oninput="setPubVolume('{{ $video->id }}', this.value)" title="{{ __('مستوى الصوت') }}">
                                    </div>

                                    {{-- قائمة الجودات المتعددة --}}
                                    <div class="ed-quality-dropdown-wrap">
                                        <button type="button" class="ed-quality-btn" onclick="togglePubQualityMenu('{{ $video->id }}')" title="{{ __('تحديد دقة وجودة العرض') }}">
                                            <i class="fa-solid fa-gear"></i>
                                            <span id="active_quality_lbl_{{ $video->id }}">1080p HD</span>
                                        </button>
                                        <div class="ed-quality-menu" id="quality_menu_{{ $video->id }}" style="display: none;">
                                            <div class="ed-quality-title">{{ __('دقة وجودة العرض:') }}</div>
                                            <button type="button" class="ed-quality-item active" onclick="selectPubVideoQuality('{{ $video->id }}', 'hd1080', '1080p HD', this)">
                                                <span>1080p Full HD</span>
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button type="button" class="ed-quality-item" onclick="selectPubVideoQuality('{{ $video->id }}', 'hd720', '720p HD', this)">
                                                <span>720p HD</span>
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button type="button" class="ed-quality-item" onclick="selectPubVideoQuality('{{ $video->id }}', 'large', '480p SD', this)">
                                                <span>480p SD</span>
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button type="button" class="ed-quality-item" onclick="selectPubVideoQuality('{{ $video->id }}', 'medium', '360p', this)">
                                                <span>360p (توفير البيانات)</span>
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button type="button" class="ed-quality-item" onclick="selectPubVideoQuality('{{ $video->id }}', 'default', 'تلقائي', this)">
                                                <span>تلقائي (حسب الاتصال)</span>
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <button type="button" class="ed-ctrl-btn" onclick="toggleSubjectFullscreen(this)" title="{{ __('تكبير العرض بملء الشاشة') }}">
                                        <i class="fa-solid fa-expand"></i>
                                    </button>
                                </div>
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
                                <span class="card-hd-pill" id="card_hd_pill_{{ $video->id }}" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; padding: 3px 9px; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;"><i class="fa-solid fa-circle-check"></i> <span>1080p Full HD</span></span>
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
        background-color: #020617;
        overflow: hidden !important;
        border-radius: 16px 16px 0 0;
        user-select: none;
        -webkit-user-select: none;
    }

    .custom-video-wrapper:fullscreen,
    .custom-video-wrapper:-webkit-full-screen {
        width: 100vw !important;
        height: 100vh !important;
        aspect-ratio: auto !important;
        background: #000 !important;
    }

    .ed-yt-frame-container {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 100%;
        transform: translate(-50%, -50%) scale(1.36);
        transform-origin: center center;
        background: #020617;
        overflow: hidden;
        pointer-events: none;
        z-index: 1;
    }

    .custom-video-element, .custom-iframe {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        border: none;
        pointer-events: none !important;
    }

    .ed-clean-click-shield {
        position: absolute;
        inset: 0;
        z-index: 18;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
    }

    .ed-pulse-icon {
        width: 66px;
        height: 66px;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.4);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        opacity: 0;
        transform: scale(0.7);
        transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        pointer-events: none;
    }

    .ed-clean-click-shield.show-pulse .ed-pulse-icon {
        opacity: 1;
        transform: scale(1);
    }

    .ed-cover-start-banner {
        position: absolute;
        inset: 0;
        z-index: 28;
        background: radial-gradient(circle at center, rgba(15, 23, 42, 0.92) 0%, rgba(2, 6, 23, 0.98) 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 24px;
        text-align: center;
        transition: opacity 0.35s ease, visibility 0.35s ease;
    }

    .ed-cover-start-banner.is-hidden {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    .ed-cover-play-glow {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 1.8rem;
        box-shadow: 0 0 35px rgba(37, 99, 235, 0.65);
        margin-bottom: 14px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border: 2px solid rgba(255, 255, 255, 0.35);
    }

    .ed-cover-start-banner:hover .ed-cover-play-glow {
        transform: scale(1.12);
        box-shadow: 0 0 50px rgba(37, 99, 235, 0.95);
    }

    .ed-cover-title {
        color: #ffffff;
        font-size: 1.15rem;
        font-weight: 800;
        margin: 0 0 6px;
        max-width: 520px;
        line-height: 1.4;
    }

    .ed-cover-sub {
        color: #94a3b8;
        font-size: 0.82rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .ed-platform-controls-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(180deg, transparent 0%, rgba(2, 6, 23, 0.75) 25%, rgba(2, 6, 23, 0.95) 100%);
        padding: 14px 18px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 25;
        direction: ltr;
        opacity: 1;
        visibility: visible;
        transition: opacity 0.35s ease, visibility 0.35s ease, transform 0.35s ease;
    }

    .custom-video-wrapper.is-playing.user-idle .ed-platform-controls-bar {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
        transform: translateY(8px);
    }

    .custom-video-wrapper.is-playing.user-idle {
        cursor: none;
    }

    .ed-ctrl-btn {
        background: transparent;
        border: none;
        color: #ffffff;
        font-size: 0.95rem;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: background 0.15s, color 0.15s, transform 0.1s;
    }

    .ed-ctrl-btn:hover {
        background: rgba(255, 255, 255, 0.18);
        color: #38bdf8;
    }

    .ed-ctrl-btn:active {
        transform: scale(0.92);
    }

    .ed-ctrl-time {
        font-family: monospace;
        font-size: 0.78rem;
        font-weight: 700;
        color: #cbd5e1;
        white-space: nowrap;
        flex-shrink: 0;
        user-select: none;
    }

    .ed-ctrl-seek-wrap {
        flex: 1;
        display: flex;
        align-items: center;
        position: relative;
        padding: 4px 0;
    }

    .ed-ctrl-seeker {
        width: 100%;
        height: 5px;
        -webkit-appearance: none;
        appearance: none;
        background: rgba(255, 255, 255, 0.25);
        border-radius: 4px;
        outline: none;
        cursor: pointer;
        transition: height 0.15s ease;
    }

    .ed-ctrl-seeker:hover {
        height: 7px;
    }

    .ed-ctrl-seeker::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: #38bdf8;
        box-shadow: 0 0 8px rgba(56, 189, 248, 0.8);
        cursor: pointer;
        transition: transform 0.1s;
    }

    .ed-volume-cluster {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .ed-volume-range {
        width: 55px;
        height: 4px;
        -webkit-appearance: none;
        appearance: none;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
        outline: none;
        cursor: pointer;
    }

    .ed-volume-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #ffffff;
        cursor: pointer;
    }

    .ed-quality-dropdown-wrap {
        position: relative;
        flex-shrink: 0;
    }

    .ed-quality-btn {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        padding: 4px 10px;
        border-radius: 7px;
        font-size: 0.74rem;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.18s;
    }

    .ed-quality-btn:hover {
        background: rgba(37, 99, 235, 0.35);
        border-color: #38bdf8;
        color: #38bdf8;
    }

    .ed-quality-menu {
        position: absolute;
        bottom: calc(100% + 10px);
        right: 0;
        min-width: 180px;
        background: rgba(15, 23, 42, 0.96);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 12px;
        padding: 6px;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.65);
        z-index: 50;
        direction: rtl;
        animation: edFadeMenu 0.18s ease;
    }

    @keyframes edFadeMenu {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .ed-quality-title {
        font-size: 0.72rem;
        font-weight: 800;
        color: #94a3b8;
        padding: 6px 10px 4px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        margin-bottom: 4px;
    }

    .ed-quality-item {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 10px;
        border: none;
        background: transparent;
        color: #e2e8f0;
        font-size: 0.78rem;
        font-weight: 700;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s;
        text-align: right;
    }

    .ed-quality-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #38bdf8;
    }

    .ed-quality-item.active {
        background: rgba(37, 99, 235, 0.25);
        color: #38bdf8;
        font-weight: 800;
    }

    .ed-quality-item i {
        font-size: 0.75rem;
        opacity: 0;
    }

    .ed-quality-item.active i {
        opacity: 1;
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
    let pubActivityTimers = {};

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
                        'onReady': function() {
                            updatePubTimeDisplay(vidId);
                        },
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

    function startPubPlaybackFirstTime(vidId) {
        const cover = document.getElementById(`cover_${vidId}`);
        if (cover) cover.classList.add('is-hidden');
        toggleSubjectVideo(vidId, true);
    }

    function registerPubPlayerActivity(vidId) {
        const wrap = document.getElementById(`custom_wrap_${vidId}`);
        if (!wrap) return;
        wrap.classList.remove('user-idle');
        clearTimeout(pubActivityTimers[vidId]);
        if (wrap.classList.contains('is-playing')) {
            pubActivityTimers[vidId] = setTimeout(() => {
                wrap.classList.add('user-idle');
            }, 3000);
        }
    }

    function handlePubPlayerMouseLeave(vidId) {
        const wrap = document.getElementById(`custom_wrap_${vidId}`);
        if (wrap && wrap.classList.contains('is-playing')) {
            wrap.classList.add('user-idle');
        }
    }

    function handlePubYtStateChange(vidId, state) {
        const wrap = document.getElementById(`custom_wrap_${vidId}`);
        const btn = document.getElementById(`pub_btn_${vidId}`);
        const pulse = document.getElementById(`pulse_${vidId}`);
        const shield = document.getElementById(`click_shield_${vidId}`);

        if (state === 1) { // مشتغل
            if (wrap) {
                wrap.classList.add('is-playing');
                wrap.classList.remove('user-idle');
            }
            if (btn) btn.innerHTML = '<i class="fa-solid fa-pause"></i>';
            if (pulse) {
                pulse.innerHTML = '<i class="fa-solid fa-play"></i>';
                if (shield) {
                    shield.classList.add('show-pulse');
                    setTimeout(() => shield.classList.remove('show-pulse'), 500);
                }
            }
            startPubYtTracking(vidId);
            registerPubPlayerActivity(vidId);
        } else { // متوقف
            if (wrap) wrap.classList.remove('is-playing', 'user-idle');
            if (btn) btn.innerHTML = (state === 0) ? '<i class="fa-solid fa-rotate-right"></i>' : '<i class="fa-solid fa-play"></i>';
            if (pulse) {
                pulse.innerHTML = (state === 0) ? '<i class="fa-solid fa-rotate-right"></i>' : '<i class="fa-solid fa-pause"></i>';
                if (shield) {
                    shield.classList.add('show-pulse');
                    setTimeout(() => shield.classList.remove('show-pulse'), 500);
                }
            }
            stopPubYtTracking(vidId);
            clearTimeout(pubActivityTimers[vidId]);
        }
    }

    function toggleSubjectVideo(vidId, isYt) {
        const cover = document.getElementById(`cover_${vidId}`);
        if (cover && !cover.classList.contains('is-hidden')) {
            cover.classList.add('is-hidden');
        }

        const wrap = document.getElementById(`custom_wrap_${vidId}`);

        if (isYt) {
            const yt = pubYtPlayers[vidId] || initPubYtPlayer(`pub_yt_${vidId}`);
            if (yt && typeof yt.getPlayerState === 'function') {
                try {
                    const state = yt.getPlayerState();
                    if (state === 1) {
                        yt.pauseVideo();
                        handlePubYtStateChange(vidId, 2);
                    } else {
                        yt.playVideo();
                        handlePubYtStateChange(vidId, 1);
                    }
                    return;
                } catch(e) {}
            }
            const iframe = document.getElementById(`pub_yt_${vidId}`);
            if (iframe && iframe.contentWindow) {
                const isPlaying = wrap && wrap.classList.contains('is-playing');
                iframe.contentWindow.postMessage(JSON.stringify({
                    event: 'command',
                    func: isPlaying ? 'pauseVideo' : 'playVideo',
                    args: []
                }), '*');
                handlePubYtStateChange(vidId, isPlaying ? 2 : 1);
            }
        } else {
            const video = document.getElementById(`pub_vid_${vidId}`);
            if (!video) return;
            if (video.paused) {
                video.play().catch(() => {});
                handlePubYtStateChange(vidId, 1);
            } else {
                video.pause();
                handlePubYtStateChange(vidId, 2);
            }
        }
    }

    function updatePubTimeDisplay(vidId) {
        const yt = pubYtPlayers[vidId];
        const timeTxt = document.getElementById(`time_txt_${vidId}`);
        const seekRange = document.getElementById(`seek_range_${vidId}`);
        if (!timeTxt) return;

        let cur = 0;
        let dur = 0;

        if (yt && typeof yt.getCurrentTime === 'function' && typeof yt.getDuration === 'function') {
            cur = yt.getCurrentTime() || 0;
            dur = yt.getDuration() || 0;
        }

        timeTxt.textContent = `${formatPubTime(cur)} / ${formatPubTime(dur)}`;
        if (seekRange && dur > 0) {
            seekRange.value = (cur / dur) * 100;
        }
    }

    function startPubYtTracking(vidId) {
        stopPubYtTracking(vidId);
        pubYtIntervals[vidId] = setInterval(() => {
            updatePubTimeDisplay(vidId);
        }, 500);
    }

    function stopPubYtTracking(vidId) {
        if (pubYtIntervals[vidId]) {
            clearInterval(pubYtIntervals[vidId]);
            delete pubYtIntervals[vidId];
        }
    }

    function seekPubRelative(vidId, secondsOffset) {
        const yt = pubYtPlayers[vidId] || initPubYtPlayer(`pub_yt_${vidId}`);
        if (yt && typeof yt.getCurrentTime === 'function') {
            try {
                const cur = yt.getCurrentTime() || 0;
                const dur = yt.getDuration() || 0;
                const target = Math.max(0, Math.min(dur, cur + secondsOffset));
                yt.seekTo(target, true);
                yt.playVideo();
                handlePubYtStateChange(vidId, 1);
                return;
            } catch(e) {}
        }
    }

    function seekSubjectVideo(vidId, isYt, percentage) {
        if (isYt) {
            const yt = pubYtPlayers[vidId] || initPubYtPlayer(`pub_yt_${vidId}`);
            if (yt && typeof yt.getDuration === 'function') {
                const dur = yt.getDuration() || 0;
                yt.seekTo((percentage / 100) * dur, true);
                yt.playVideo();
                handlePubYtStateChange(vidId, 1);
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

    function togglePubMute(vidId) {
        const yt = pubYtPlayers[vidId] || initPubYtPlayer(`pub_yt_${vidId}`);
        const muteBtn = document.getElementById(`mute_btn_${vidId}`);
        const volRange = document.getElementById(`vol_range_${vidId}`);
        if (yt && typeof yt.isMuted === 'function') {
            try {
                if (yt.isMuted()) {
                    yt.unMute();
                    if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
                    if (volRange) volRange.value = yt.getVolume() || 100;
                } else {
                    yt.mute();
                    if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-xmark"></i>';
                    if (volRange) volRange.value = 0;
                }
            } catch(e) {}
        }
    }

    function setPubVolume(vidId, value) {
        const val = parseInt(value, 10);
        const muteBtn = document.getElementById(`mute_btn_${vidId}`);
        const yt = pubYtPlayers[vidId] || initPubYtPlayer(`pub_yt_${vidId}`);
        if (yt && typeof yt.setVolume === 'function') {
            try {
                if (val === 0) yt.mute(); else { yt.unMute(); yt.setVolume(val); }
            } catch(e) {}
        }
        if (muteBtn) {
            muteBtn.innerHTML = (val === 0) ? '<i class="fa-solid fa-volume-xmark"></i>' : '<i class="fa-solid fa-volume-high"></i>';
        }
    }

    function togglePubQualityMenu(vidId) {
        const menu = document.getElementById(`quality_menu_${vidId}`);
        if (!menu) return;
        const isShown = (menu.style.display === 'block');
        document.querySelectorAll('.ed-quality-menu').forEach(m => m.style.display = 'none');
        menu.style.display = isShown ? 'none' : 'block';
    }

    function selectPubVideoQuality(vidId, qCode, qLabel, btnEl) {
        const menu = document.getElementById(`quality_menu_${vidId}`);
        if (menu) {
            menu.querySelectorAll('.ed-quality-item').forEach(b => b.classList.remove('active'));
            if (btnEl) btnEl.classList.add('active');
            menu.style.display = 'none';
        }

        const lbl = document.getElementById(`active_quality_lbl_${vidId}`);
        if (lbl) lbl.textContent = qLabel;

        const cardBadge = document.getElementById(`card_hd_pill_${vidId}`);
        if (cardBadge) cardBadge.innerHTML = `<i class="fa-solid fa-circle-check"></i> <span>${qLabel}</span>`;

        const yt = pubYtPlayers[vidId] || initPubYtPlayer(`pub_yt_${vidId}`);
        if (yt && typeof yt.setPlaybackQuality === 'function') {
            try { yt.setPlaybackQuality(qCode); } catch(e) {}
        }

        const iframe = document.getElementById(`pub_yt_${vidId}`);
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage(JSON.stringify({
                event: 'command',
                func: 'setPlaybackQuality',
                args: [qCode]
            }), '*');
        }
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.ed-quality-dropdown-wrap')) {
            document.querySelectorAll('.ed-quality-menu').forEach(m => m.style.display = 'none');
        }
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
