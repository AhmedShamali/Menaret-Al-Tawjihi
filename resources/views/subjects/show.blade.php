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
                                $streamUrl = $video->stream_url ?? (!empty($url) ? \App\Support\MediaHelper::videoStreamUrl($url, null, $video->id) : null);
                            @endphp

                            @if($isYoutube)
                                @php
                                    preg_match('/(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:watch\?.*v=|embed\/|shorts\/|live\/|v\/)|[?&]v=)([^"&?\/\s]{11})/i', $url, $matches);
                                    $ytId = $matches[1] ?? $video->youtube_id ?? $url;
                                    $ytSafeSrc = "https://www.youtube-nocookie.com/embed/{$ytId}?enablejsapi=1&controls=1&fs=1&rel=0&iv_load_policy=3&playsinline=1&modestbranding=1&origin=" . urlencode(request()->getSchemeAndHttpHost());
                                    $posterThumb = $ytId ? "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg" : asset('images/video-placeholder.jpg');
                                @endphp

                                {{-- عرض يوتيوب بكامل دقته النظيفة مع إتاحة التحكم والتكبير --}}
                                <div class="ed-yt-viewport">
                                    <iframe class="custom-iframe" 
                                            id="pub_yt_{{ $video->id }}"
                                            src="{{ $ytSafeSrc }}" 
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen" 
                                            allowfullscreen
                                            referrerpolicy="strict-origin-when-cross-origin"
                                            tabindex="-1"
                                            loading="lazy">
                                    </iframe>
                                </div>
                            @elseif(!empty($streamUrl))
                                {{-- فيديو مباشر محمي عبر ستريم مشفر يخفي مسار السيرفر والسحابة بالكامل --}}
                                <div class="ed-yt-viewport">
                                    <video class="custom-video-element" 
                                           id="pub_vid_{{ $video->id }}" 
                                           preload="metadata" 
                                           playsinline 
                                           webkit-playsinline
                                           controlslist="nodownload noremoteplayback" 
                                           disablepictureinpicture
                                           oncontextmenu="return false;"
                                           style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; background: #000;">
                                        <source src="{{ $streamUrl }}" type="video/mp4">
                                    </video>
                                    <div class="ed-yt-top-shield">
                                        <div class="ed-pth-title">
                                            <span class="ed-pth-badge"><i class="fa-solid fa-graduation-cap"></i> {{ __('الدرس') }} #{{ $video->order ?? ($index + 1) }}</span>
                                            <span class="ed-pth-name">{{ $video->title }}</span>
                                        </div>
                                        <div class="ed-pth-quality-tag" id="pub_top_quality_tag_{{ $video->id }}">
                                            <i class="fa-solid fa-circle-check"></i> 1080p Full HD
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #0f172a; color: #94a3b8; padding: 20px; text-align: center;">
                                    <i class="fa-solid fa-video-slash" style="font-size: 2rem; color: #64748b; margin-bottom: 8px;"></i>
                                    <span style="font-size: 0.85rem; color: #cbd5e1; font-weight: 700;">{{ __('جاري تجهيز ورفع الفيديو من المعلم') }}</span>
                                </div>
                            @endif

                            @if(!empty($streamUrl))
                                {{-- شاشة التوقف الأكاديمية لحجب أي اقتراحات خارجية --}}
                                <div class="ed-video-pause-overlay" id="pub_pause_overlay_{{ $video->id }}" onclick="toggleSubjectVideo('{{ $video->id }}')">
                                    <div class="ed-pause-card">
                                        <div class="ed-pause-icon"><i class="fa-solid fa-pause"></i></div>
                                        <div class="ed-pause-text">{{ __('تم إيقاف الدرس مؤقتاً') }}</div>
                                        <button type="button" class="ed-pause-resume-btn">
                                            <i class="fa-solid fa-play"></i> {{ __('متابعة المشاهدة') }}
                                        </button>
                                    </div>
                                </div>

                                {{-- طبقة المشغل الأكاديمي الموحد الخاص بالمنصة --}}
                                <div class="ed-pure-player-layer" id="pub_player_layer_{{ $video->id }}" onmousemove="registerPubPlayerActivity('{{ $video->id }}')" onmouseleave="handlePubPlayerMouseLeave('{{ $video->id }}')">
                                    <div class="ed-click-surface" onclick="toggleSubjectVideo('{{ $video->id }}')" ondblclick="toggleSubjectFullscreen(this)">
                                        <div class="ed-center-pulse-btn" id="pub_center_pulse_{{ $video->id }}">
                                            <i class="fa-solid fa-play"></i>
                                        </div>
                                    </div>

                                    <div class="ed-player-top-header">
                                        <div class="ed-pth-title">
                                            <span class="ed-pth-badge"><i class="fa-solid fa-graduation-cap"></i> {{ __('الدرس') }} #{{ $video->order ?? ($index + 1) }}</span>
                                            <span class="ed-pth-name">{{ $video->title }}</span>
                                        </div>
                                        <div class="ed-pth-quality-tag" id="pub_top_quality_tag_panel_{{ $video->id }}">
                                            <i class="fa-solid fa-circle-check"></i> 1080p Full HD
                                        </div>
                                    </div>

                                    <div class="ed-player-bottom-panel" onclick="event.stopPropagation()">
                                        <div class="ed-scrubber-wrapper" id="pub_scrubber_wrap_{{ $video->id }}" onclick="handlePubScrubberClick(event, '{{ $video->id }}')" onmousemove="handlePubScrubberHover(event, '{{ $video->id }}')" onmouseleave="hidePubScrubberTooltip('{{ $video->id }}')">
                                            <div class="ed-scrub-track">
                                                <div class="ed-scrub-buffer" id="pub_buffer_bar_{{ $video->id }}"></div>
                                                <div class="ed-scrub-fill" id="pub_progress_bar_{{ $video->id }}">
                                                    <div class="ed-scrub-thumb"></div>
                                                </div>
                                            </div>
                                            <div class="ed-scrub-tooltip" id="pub_scrub_tip_{{ $video->id }}">00:00</div>
                                        </div>

                                        <div class="ed-controls-main-row">
                                            <div class="ed-ctrl-cluster left">
                                                <button type="button" class="ed-ctrl-btn play" id="pub_btn_{{ $video->id }}" onclick="toggleSubjectVideo('{{ $video->id }}')" title="{{ __('تشغيل / إيقاف (مسافة)') }}">
                                                    <i class="fa-solid fa-play"></i>
                                                </button>
                                                <button type="button" class="ed-ctrl-btn skip" onclick="seekPubRelative('{{ $video->id }}', -10)" title="{{ __('تراجع 10 ثوانٍ') }}">
                                                    <i class="fa-solid fa-rotate-left"></i>
                                                    <span class="ed-skip-num">10</span>
                                                </button>
                                                <button type="button" class="ed-ctrl-btn skip" onclick="seekPubRelative('{{ $video->id }}', 10)" title="{{ __('تقديم 10 ثوانٍ') }}">
                                                    <i class="fa-solid fa-rotate-right"></i>
                                                    <span class="ed-skip-num">10</span>
                                                </button>
                                                <div class="ed-time-counter" id="pub_time_txt_{{ $video->id }}">
                                                    00:00 / 00:00
                                                </div>
                                            </div>

                                            <div class="ed-ctrl-cluster right">
                                                <div class="ed-volume-group">
                                                    <button type="button" class="ed-ctrl-btn vol" id="pub_mute_btn_{{ $video->id }}" onclick="togglePubMute('{{ $video->id }}')" title="{{ __('كتم / تشغيل الصوت (M)') }}">
                                                        <i class="fa-solid fa-volume-high"></i>
                                                    </button>
                                                    <input type="range" class="ed-volume-slider" id="pub_vol_slider_{{ $video->id }}" min="0" max="100" value="100" oninput="setPubVolume('{{ $video->id }}', this.value)">
                                                </div>

                                                <div class="ed-speed-dropdown">
                                                    <button type="button" class="ed-ctrl-pill" onclick="toggleDropdownMenu('pub_speed_menu_{{ $video->id }}')">
                                                        <span id="pub_active_speed_{{ $video->id }}">1x</span>
                                                    </button>
                                                    <div class="ed-popover-menu" id="pub_speed_menu_{{ $video->id }}">
                                                        <div class="ed-pop-header">{{ __('سرعة التشغيل') }}</div>
                                                        <button type="button" class="ed-pop-opt" onclick="setPubVideoSpeed('{{ $video->id }}', 0.75, this)">0.75x</button>
                                                        <button type="button" class="ed-pop-opt active" onclick="setPubVideoSpeed('{{ $video->id }}', 1, this)">1x ({{ __('عادي') }})</button>
                                                        <button type="button" class="ed-pop-opt" onclick="setPubVideoSpeed('{{ $video->id }}', 1.25, this)">1.25x</button>
                                                        <button type="button" class="ed-pop-opt" onclick="setPubVideoSpeed('{{ $video->id }}', 1.5, this)">1.5x</button>
                                                        <button type="button" class="ed-pop-opt" onclick="setPubVideoSpeed('{{ $video->id }}', 2, this)">2x</button>
                                                    </div>
                                                </div>

                                                <div class="ed-quality-dropdown">
                                                    <button type="button" class="ed-ctrl-pill hd" onclick="toggleDropdownMenu('pub_quality_menu_{{ $video->id }}')">
                                                        <i class="fa-solid fa-sliders"></i>
                                                        <span id="pub_active_quality_{{ $video->id }}">1080p</span>
                                                    </button>
                                                    <div class="ed-popover-menu" id="pub_quality_menu_{{ $video->id }}">
                                                        <div class="ed-pop-header">{{ __('دقة الفيديو') }}</div>
                                                        <button type="button" class="ed-pop-opt active" onclick="setPubVideoQuality('{{ $video->id }}', '1080p', this, '1080p Full HD')">1080p Full HD</button>
                                                        <button type="button" class="ed-pop-opt" onclick="setPubVideoQuality('{{ $video->id }}', '720p', this, '720p HD')">720p HD</button>
                                                        <button type="button" class="ed-pop-opt" onclick="setPubVideoQuality('{{ $video->id }}', '480p', this, '480p SD')">480p SD</button>
                                                        <button type="button" class="ed-pop-opt" onclick="setPubVideoQuality('{{ $video->id }}', '360p', this, '360p')">360p</button>
                                                        <button type="button" class="ed-pop-opt" onclick="setPubVideoQuality('{{ $video->id }}', 'auto', this, '{{ __("تلقائي (Auto)") }}')">{{ __('تلقائي (Auto)') }}</button>
                                                    </div>
                                                </div>

                                                <button type="button" class="ed-ctrl-btn fs" onclick="toggleSubjectFullscreen(this)" title="{{ __('ملء الشاشة (F)') }}">
                                                    <i class="fa-solid fa-expand"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- بوستر المنصة الأكاديمي الحصري --}}
                                @php
                                    $posterThumb = $isYoutube && !empty($ytId) ? "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg" : asset('images/video-placeholder.jpg');
                                @endphp
                                <div class="ed-video-poster" id="pub_poster_{{ $video->id }}" onclick="startPlayingPubVideo('{{ $video->id }}')">
                                    <img src="{{ $posterThumb }}" alt="{{ $video->title }}" class="ed-poster-bg-img" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/video-placeholder.jpg') }}';">
                                    <div class="ed-poster-overlay"></div>
                                    <div class="ed-poster-top-bar">
                                        <span class="ed-poster-badge">
                                            <i class="fa-solid fa-graduation-cap"></i> {{ __('الدرس') }} #{{ $video->order ?? ($index + 1) }}
                                        </span>
                                        <span class="ed-poster-badge hd">
                                            <i class="fa-solid fa-sparkles"></i> 1080p Full HD
                                        </span>
                                    </div>
                                    <div class="ed-poster-center-content">
                                        <button type="button" class="ed-poster-play-btn" aria-label="{{ __('تشغيل الدرس') }}">
                                            <i class="fa-solid fa-play"></i>
                                        </button>
                                        <span class="ed-poster-play-label">{{ __('انقر لبدء مشاهدة الحصة') }}</span>
                                    </div>
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
    .custom-video-wrapper:-webkit-full-screen,
    .custom-video-wrapper:-moz-full-screen,
    .custom-video-wrapper:-ms-fullscreen,
    .custom-video-wrapper.is-platform-fullscreen {
        position: fixed !important;
        inset: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        padding-top: 0 !important;
        margin: 0 !important;
        border-radius: 0 !important;
        z-index: 9999999 !important;
        background: #000000 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .custom-video-wrapper:fullscreen .ed-yt-viewport,
    .custom-video-wrapper:-webkit-full-screen .ed-yt-viewport,
    .custom-video-wrapper.is-platform-fullscreen .ed-yt-viewport,
    .custom-video-wrapper:fullscreen iframe,
    .custom-video-wrapper:-webkit-full-screen iframe,
    .custom-video-wrapper.is-platform-fullscreen iframe,
    .custom-video-wrapper:fullscreen video,
    .custom-video-wrapper:-webkit-full-screen video,
    .custom-video-wrapper.is-platform-fullscreen video {
        width: 100% !important;
        height: 100% !important;
        max-width: 100% !important;
        max-height: 100% !important;
    }

    .ed-fs-exit-floating-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 99999999;
        background: rgba(15, 23, 42, 0.88);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 8px 18px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 800;
        cursor: pointer;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.6);
        transition: all 0.2s ease;
    }
    .ed-fs-exit-floating-btn:hover {
        background: #dc2626;
        border-color: #ef4444;
    }

    .ed-yt-viewport {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background: #000;
    }

    .custom-iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        z-index: 2;
        pointer-events: auto !important;
    }

    .ed-video-pause-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.92);
        backdrop-filter: blur(10px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 22;
        cursor: pointer;
    }

    .ed-pause-card {
        background: rgba(30, 41, 59, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 24px 32px;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
    }

    .ed-pause-icon {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 12px;
    }

    .ed-pause-text {
        color: #f8fafc;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 14px;
    }

    .ed-pause-resume-btn {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
    }

    .ed-player-toast {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        background: rgba(15, 23, 42, 0.95);
        color: #ffffff;
        padding: 10px 22px;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.88rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.15);
        z-index: 99999;
        pointer-events: none;
        opacity: 0;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .ed-player-toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .custom-video-element {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border: none;
    }

    /* طبقة المشغل الأكاديمي الصافي */
    .ed-pure-player-layer {
        position: absolute;
        inset: 0;
        z-index: 10;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: opacity 0.3s ease;
        user-select: none;
    }

    .custom-video-wrapper.controls-hidden .ed-pure-player-layer {
        opacity: 0;
        pointer-events: none;
    }

    .custom-video-wrapper.controls-hidden {
        cursor: none;
    }

    .ed-click-surface {
        position: absolute;
        inset: 0;
        z-index: 2;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ed-center-pulse-btn {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        font-size: 1.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
        opacity: 0;
        transform: scale(0.8);
        transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        pointer-events: none;
    }

    .ed-center-pulse-btn.show-pulse {
        opacity: 1;
        transform: scale(1);
    }

    .ed-player-top-header {
        position: relative;
        z-index: 5;
        padding: 14px 18px;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.35) 70%, transparent 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        pointer-events: none;
    }

    .ed-pth-title {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.92rem;
    }

    .ed-pth-badge {
        background: #2563eb;
        color: #ffffff;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 800;
    }

    .ed-pth-quality-tag {
        background: rgba(16, 185, 129, 0.2);
        border: 1px solid rgba(16, 185, 129, 0.4);
        color: #34d399;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 800;
    }

    .ed-player-bottom-panel {
        position: relative;
        z-index: 5;
        padding: 8px 16px 12px;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.92) 0%, rgba(0, 0, 0, 0.5) 60%, transparent 100%);
        display: flex;
        flex-direction: column;
        gap: 6px;
        pointer-events: auto;
    }

    .ed-scrubber-wrapper {
        position: relative;
        width: 100%;
        height: 16px;
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .ed-scrub-track {
        position: relative;
        width: 100%;
        height: 5px;
        background: rgba(255, 255, 255, 0.25);
        border-radius: 4px;
        transition: height 0.15s ease;
    }

    .ed-scrubber-wrapper:hover .ed-scrub-track {
        height: 7px;
    }

    .ed-scrub-buffer {
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        background: rgba(255, 255, 255, 0.4);
        border-radius: 4px;
        width: 0%;
    }

    .ed-scrub-fill {
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 4px;
        width: 0%;
    }

    .ed-scrub-thumb {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #ffffff;
        box-shadow: 0 0 6px rgba(0, 0, 0, 0.6);
        transform: scale(0);
        transition: transform 0.15s ease;
        margin-right: -6px;
    }

    .ed-scrubber-wrapper:hover .ed-scrub-thumb {
        transform: scale(1);
    }

    .ed-scrub-tooltip {
        position: absolute;
        bottom: 20px;
        background: rgba(15, 23, 42, 0.95);
        color: #ffffff;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        pointer-events: none;
        display: none;
        transform: translateX(50%);
        white-space: nowrap;
    }

    .ed-controls-main-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .ed-ctrl-cluster {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ed-ctrl-btn {
        background: none;
        border: none;
        color: #ffffff;
        font-size: 1.1rem;
        cursor: pointer;
        padding: 5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: color 0.2s ease, transform 0.15s ease;
    }

    .ed-ctrl-btn:hover {
        color: #60a5fa;
        transform: scale(1.08);
    }

    .ed-ctrl-btn.play {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        font-size: 1.15rem;
    }

    .ed-ctrl-btn.play:hover {
        background: #2563eb;
        color: #ffffff;
    }

    .ed-ctrl-btn.skip {
        position: relative;
        font-size: 1.05rem;
    }

    .ed-skip-num {
        font-size: 0.52rem;
        font-weight: 900;
        position: absolute;
        top: 52%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-family: sans-serif;
    }

    .ed-time-counter {
        color: #cbd5e1;
        font-size: 0.78rem;
        font-weight: 700;
        font-family: 'Outfit', sans-serif, system-ui;
        direction: ltr;
        min-width: 90px;
    }

    .ed-volume-group {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .ed-volume-slider {
        width: 50px;
        height: 4px;
        -webkit-appearance: none;
        appearance: none;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
        outline: none;
        cursor: pointer;
    }

    .ed-volume-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #ffffff;
        cursor: pointer;
    }

    .ed-speed-dropdown,
    .ed-quality-dropdown {
        position: relative;
    }

    .ed-ctrl-pill {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
    }

    .ed-ctrl-pill:hover,
    .ed-ctrl-pill.active {
        background: #2563eb;
        border-color: #3b82f6;
        color: #ffffff;
    }

    .ed-ctrl-pill.hd {
        color: #34d399;
        border-color: rgba(52, 211, 153, 0.4);
    }

    .ed-ctrl-pill.hd:hover {
        background: #059669;
        color: #ffffff;
    }

    .ed-popover-menu {
        position: absolute;
        bottom: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%);
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        padding: 6px;
        display: none;
        flex-direction: column;
        gap: 3px;
        min-width: 130px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.6);
        z-index: 50;
    }

    .ed-popover-menu.show {
        display: flex;
    }

    .ed-pop-header {
        font-size: 0.7rem;
        font-weight: 800;
        color: #94a3b8;
        padding: 3px 6px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 2px;
    }

    .ed-pop-opt {
        background: none;
        border: none;
        color: #e2e8f0;
        padding: 5px 8px;
        border-radius: 5px;
        font-size: 0.75rem;
        font-weight: 700;
        text-align: right;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ed-pop-opt:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
    }

    .ed-pop-opt.active {
        background: #2563eb;
        color: #ffffff;
        font-weight: 800;
    }

    /* بوستر الفيديو الأكاديمي */
    .ed-video-poster {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 12;
        cursor: pointer;
        overflow: hidden;
        user-select: none;
        transition: opacity 0.35s ease, visibility 0.35s ease;
    }

    .ed-poster-bg-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.8);
        transition: transform 0.4s ease, filter 0.4s ease;
    }

    .ed-video-poster:hover .ed-poster-bg-img {
        transform: scale(1.03);
        filter: brightness(0.9);
    }

    .ed-poster-overlay {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at center, rgba(15, 23, 42, 0.4) 0%, rgba(15, 23, 42, 0.8) 100%);
    }

    .ed-poster-center-content {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        z-index: 2;
    }

    .ed-poster-play-btn {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
        color: #ffffff;
        font-size: 1.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 10px 30px rgba(37, 99, 235, 0.55), 0 0 0 8px rgba(255, 255, 255, 0.18);
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease;
    }

    .ed-video-poster:hover .ed-poster-play-btn {
        transform: scale(1.1);
        box-shadow: 0 14px 40px rgba(37, 99, 235, 0.75), 0 0 0 12px rgba(255, 255, 255, 0.28);
    }

    .ed-poster-play-label {
        color: #ffffff;
        font-size: 0.88rem;
        font-weight: 800;
        text-shadow: 0 2px 6px rgba(0, 0, 0, 0.8);
        letter-spacing: 0.3px;
        background: rgba(15, 23, 42, 0.6);
        padding: 4px 14px;
        border-radius: 9999px;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .ed-poster-top-bar {
        position: absolute;
        top: 12px;
        right: 12px;
        left: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 3;
    }

    .ed-poster-badge {
        background: rgba(15, 23, 42, 0.78);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .ed-poster-badge.hd {
        color: #4ade80;
        border-color: rgba(74, 222, 128, 0.35);
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
    let pubPlayerHideTimers = {};

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

    function getPubPlayerContext(vidId) {
        const direct = document.getElementById(`pub_vid_${vidId}`);
        if (direct) {
            return { isDirect: true, direct: direct, yt: null };
        }
        const yt = pubYtPlayers[vidId] || initPubYtPlayer(`pub_yt_${vidId}`);
        return { isDirect: false, direct: null, yt: yt };
    }

    function showPlayerToast(message) {
        let toast = document.getElementById('edPlayerToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'edPlayerToast';
            toast.className = 'ed-player-toast';
            document.body.appendChild(toast);
        }
        toast.innerHTML = `<i class="fa-solid fa-circle-check" style="color: #60a5fa;"></i> <span>${message}</span>`;
        toast.classList.add('show');
        clearTimeout(window._edToastTimer);
        window._edToastTimer = setTimeout(() => {
            toast.classList.remove('show');
        }, 2800);
    }

    function registerPubPlayerActivity(vidId) {
        const wrap = document.getElementById(`custom_wrap_${vidId}`);
        if (!wrap) return;
        wrap.classList.remove('controls-hidden');
        clearTimeout(pubPlayerHideTimers[vidId]);

        const ctx = getPubPlayerContext(vidId);
        const isPlayingYt = ctx.yt && typeof ctx.yt.getPlayerState === 'function' && ctx.yt.getPlayerState() === 1;
        const isPlayingDirect = ctx.direct && !ctx.direct.paused && !ctx.direct.ended;

        if (isPlayingYt || isPlayingDirect) {
            pubPlayerHideTimers[vidId] = setTimeout(() => {
                wrap.classList.add('controls-hidden');
            }, 2600);
        }
    }

    function handlePubPlayerMouseLeave(vidId) {
        const wrap = document.getElementById(`custom_wrap_${vidId}`);
        if (!wrap) return;
        const ctx = getPubPlayerContext(vidId);
        const isPlayingYt = ctx.yt && typeof ctx.yt.getPlayerState === 'function' && ctx.yt.getPlayerState() === 1;
        const isPlayingDirect = ctx.direct && !ctx.direct.paused && !ctx.direct.ended;

        if (isPlayingYt || isPlayingDirect) {
            wrap.classList.add('controls-hidden');
        }
    }

    function handlePubYtStateChange(vidId, state) {
        const poster = document.getElementById(`pub_poster_${vidId}`);
        const btn = document.getElementById(`pub_btn_${vidId}`);
        const wrap = document.getElementById(`custom_wrap_${vidId}`);
        const pauseOverlay = document.getElementById(`pub_pause_overlay_${vidId}`);

        if (state === 1) { // playing
            if (poster) {
                poster.style.opacity = '0';
                poster.style.pointerEvents = 'none';
                setTimeout(() => { poster.style.display = 'none'; }, 300);
            }
            if (btn) btn.innerHTML = '<i class="fa-solid fa-pause"></i>';
            if (pauseOverlay) pauseOverlay.style.display = 'none';
            startPubTracking(vidId);
            registerPubPlayerActivity(vidId);
        } else if (state === 2) { // paused
            if (btn) btn.innerHTML = '<i class="fa-solid fa-play"></i>';
            if (pauseOverlay) pauseOverlay.style.display = 'flex';
            if (wrap) wrap.classList.remove('controls-hidden');
            clearTimeout(pubPlayerHideTimers[vidId]);
            stopPubTracking(vidId);
        } else if (state === 0) { // ended
            if (poster) {
                poster.style.display = 'block';
                poster.style.pointerEvents = 'auto';
                setTimeout(() => { poster.style.opacity = '1'; }, 50);
                const playLabel = poster.querySelector('.ed-poster-play-label');
                if (playLabel) playLabel.textContent = '{{ __("إعادة مشاهدة الحصة") }} ↺';
            }
            if (btn) btn.innerHTML = '<i class="fa-solid fa-rotate-right"></i>';
            if (pauseOverlay) pauseOverlay.style.display = 'flex';
            if (wrap) wrap.classList.remove('controls-hidden');
            stopPubTracking(vidId);
        } else {
            stopPubTracking(vidId);
        }
    }

    function startPlayingPubVideo(vidId) {
        const poster = document.getElementById(`pub_poster_${vidId}`);
        if (poster) {
            poster.style.opacity = '0';
            poster.style.pointerEvents = 'none';
            setTimeout(() => { poster.style.display = 'none'; }, 300);
        }
        const ctx = getPubPlayerContext(vidId);
        if (ctx.isDirect && ctx.direct) {
            ctx.direct.play().catch(() => {});
            return;
        }

        if (ctx.yt && typeof ctx.yt.playVideo === 'function') {
            try { ctx.yt.playVideo(); return; } catch(e) {}
        }
        const iframe = document.getElementById(`pub_yt_${vidId}`);
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage(JSON.stringify({
                event: 'command',
                func: 'playVideo',
                args: []
            }), '*');
        }
    }

    function toggleSubjectVideo(vidId) {
        const pulse = document.getElementById(`pub_center_pulse_${vidId}`);
        const btn = document.getElementById(`pub_btn_${vidId}`);
        const pauseOverlay = document.getElementById(`pub_pause_overlay_${vidId}`);
        const ctx = getPubPlayerContext(vidId);

        if (ctx.isDirect && ctx.direct) {
            if (ctx.direct.paused) {
                ctx.direct.play().catch(() => {});
                if (btn) btn.innerHTML = '<i class="fa-solid fa-pause"></i>';
                if (pauseOverlay) pauseOverlay.style.display = 'none';
                if (pulse) {
                    pulse.innerHTML = '<i class="fa-solid fa-play"></i>';
                    pulse.classList.add('show-pulse');
                    setTimeout(() => pulse.classList.remove('show-pulse'), 500);
                }
                startPubTracking(vidId);
                registerPubPlayerActivity(vidId);
            } else {
                ctx.direct.pause();
                if (btn) btn.innerHTML = '<i class="fa-solid fa-play"></i>';
                if (pauseOverlay) pauseOverlay.style.display = 'flex';
                if (pulse) {
                    pulse.innerHTML = '<i class="fa-solid fa-pause"></i>';
                    pulse.classList.add('show-pulse');
                    setTimeout(() => pulse.classList.remove('show-pulse'), 500);
                }
                stopPubTracking(vidId);
            }
            return;
        }

        // YouTube
        const yt = ctx.yt;
        if (yt && typeof yt.getPlayerState === 'function') {
            const state = yt.getPlayerState();
            if (state === 1) { // playing -> pause
                yt.pauseVideo();
                if (btn) btn.innerHTML = '<i class="fa-solid fa-play"></i>';
                if (pauseOverlay) pauseOverlay.style.display = 'flex';
                if (pulse) {
                    pulse.innerHTML = '<i class="fa-solid fa-pause"></i>';
                    pulse.classList.add('show-pulse');
                    setTimeout(() => pulse.classList.remove('show-pulse'), 500);
                }
                stopPubTracking(vidId);
            } else { // paused -> play
                yt.playVideo();
                if (btn) btn.innerHTML = '<i class="fa-solid fa-pause"></i>';
                if (pauseOverlay) pauseOverlay.style.display = 'none';
                if (pulse) {
                    pulse.innerHTML = '<i class="fa-solid fa-play"></i>';
                    pulse.classList.add('show-pulse');
                    setTimeout(() => pulse.classList.remove('show-pulse'), 500);
                }
                startPubTracking(vidId);
                registerPubPlayerActivity(vidId);
            }
        } else {
            const iframe = document.getElementById(`pub_yt_${vidId}`);
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.postMessage(JSON.stringify({
                    event: 'command',
                    func: 'playVideo',
                    args: []
                }), '*');
                if (btn) btn.innerHTML = '<i class="fa-solid fa-pause"></i>';
                if (pauseOverlay) pauseOverlay.style.display = 'none';
            }
        }
    }

    function startPubTracking(vidId) {
        stopPubTracking(vidId);
        pubYtIntervals[vidId] = setInterval(() => {
            const ctx = getPubPlayerContext(vidId);
            let cur = 0;
            let dur = 0;
            let loaded = 0;

            if (ctx.isDirect && ctx.direct) {
                cur = ctx.direct.currentTime || 0;
                dur = ctx.direct.duration || 0;
                if (ctx.direct.buffered && ctx.direct.buffered.length > 0 && dur > 0) {
                    loaded = ctx.direct.buffered.end(ctx.direct.buffered.length - 1) / dur;
                }
            } else if (ctx.yt && typeof ctx.yt.getCurrentTime === 'function' && typeof ctx.yt.getDuration === 'function') {
                try {
                    cur = ctx.yt.getCurrentTime() || 0;
                    dur = ctx.yt.getDuration() || 0;
                    loaded = typeof ctx.yt.getVideoLoadedFraction === 'function' ? ctx.yt.getVideoLoadedFraction() : 0;
                } catch(e) {}
            }

            const timeEl = document.getElementById(`pub_time_txt_${vidId}`);
            const progBar = document.getElementById(`pub_progress_bar_${vidId}`);
            const bufBar = document.getElementById(`pub_buffer_bar_${vidId}`);

            if (timeEl) timeEl.textContent = `${formatPubTime(cur)} / ${formatPubTime(dur)}`;
            if (progBar && dur > 0) progBar.style.width = `${(cur / dur) * 100}%`;
            if (bufBar) bufBar.style.width = `${Math.min(100, loaded * 100)}%`;
        }, 350);
    }

    function stopPubTracking(vidId) {
        if (pubYtIntervals[vidId]) {
            clearInterval(pubYtIntervals[vidId]);
            delete pubYtIntervals[vidId];
        }
    }

    function handlePubScrubberClick(e, vidId) {
        const wrap = document.getElementById(`pub_scrubber_wrap_${vidId}`);
        if (!wrap) return;
        const rect = wrap.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const isRtl = window.getComputedStyle(wrap).direction === 'rtl';
        const pct = Math.max(0, Math.min(1, clickX / rect.width));
        const percentage = isRtl ? (1 - pct) : pct;

        const ctx = getPubPlayerContext(vidId);
        if (ctx.isDirect && ctx.direct) {
            const dur = ctx.direct.duration || 0;
            if (dur > 0) {
                ctx.direct.currentTime = percentage * dur;
                ctx.direct.play().catch(() => {});
            }
            return;
        }

        const yt = ctx.yt;
        if (yt && typeof yt.getDuration === 'function') {
            const dur = yt.getDuration() || 0;
            if (dur > 0) {
                yt.seekTo(percentage * dur, true);
                yt.playVideo();
            }
        }
    }

    function handlePubScrubberHover(e, vidId) {
        const wrap = document.getElementById(`pub_scrubber_wrap_${vidId}`);
        const tip = document.getElementById(`pub_scrub_tip_${vidId}`);
        if (!wrap || !tip) return;
        const rect = wrap.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const isRtl = window.getComputedStyle(wrap).direction === 'rtl';
        const pct = Math.max(0, Math.min(1, clickX / rect.width));
        const percentage = isRtl ? (1 - pct) : pct;

        const ctx = getPubPlayerContext(vidId);
        let dur = 0;
        if (ctx.isDirect && ctx.direct) dur = ctx.direct.duration || 0;
        else if (ctx.yt && typeof ctx.yt.getDuration === 'function') dur = ctx.yt.getDuration() || 0;

        if (dur > 0) {
            tip.textContent = formatPubTime(percentage * dur);
            tip.style.display = 'block';
            tip.style.left = `${clickX}px`;
        }
    }

    function hidePubScrubberTooltip(vidId) {
        const tip = document.getElementById(`pub_scrub_tip_${vidId}`);
        if (tip) tip.style.display = 'none';
    }

    function seekPubRelative(vidId, offset) {
        const ctx = getPubPlayerContext(vidId);
        if (ctx.isDirect && ctx.direct) {
            const cur = ctx.direct.currentTime || 0;
            const dur = ctx.direct.duration || 0;
            ctx.direct.currentTime = Math.max(0, Math.min(dur || 999999, cur + offset));
            ctx.direct.play().catch(() => {});
            return;
        }

        const yt = ctx.yt;
        if (yt && typeof yt.getCurrentTime === 'function' && typeof yt.getDuration === 'function') {
            try {
                const cur = yt.getCurrentTime() || 0;
                const dur = yt.getDuration() || 0;
                yt.seekTo(Math.max(0, Math.min(dur, cur + offset)), true);
                yt.playVideo();
            } catch(e) {}
        }
    }

    function setPubVolume(vidId, val) {
        val = parseInt(val, 10);
        const ctx = getPubPlayerContext(vidId);
        const muteBtn = document.getElementById(`pub_mute_btn_${vidId}`);

        if (ctx.isDirect && ctx.direct) {
            ctx.direct.volume = val / 100;
            ctx.direct.muted = (val === 0);
        } else if (ctx.yt && typeof ctx.yt.setVolume === 'function') {
            try {
                if (val === 0) ctx.yt.mute(); else { ctx.yt.unMute(); ctx.yt.setVolume(val); }
            } catch(e) {}
        }

        if (muteBtn) {
            if (val === 0) muteBtn.innerHTML = '<i class="fa-solid fa-volume-xmark"></i>';
            else if (val < 50) muteBtn.innerHTML = '<i class="fa-solid fa-volume-low"></i>';
            else muteBtn.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
        }
    }

    function togglePubMute(vidId) {
        const ctx = getPubPlayerContext(vidId);
        const muteBtn = document.getElementById(`pub_mute_btn_${vidId}`);
        const slider = document.getElementById(`pub_vol_slider_${vidId}`);

        if (ctx.isDirect && ctx.direct) {
            ctx.direct.muted = !ctx.direct.muted;
            if (muteBtn) {
                muteBtn.innerHTML = ctx.direct.muted ? '<i class="fa-solid fa-volume-xmark"></i>' : '<i class="fa-solid fa-volume-high"></i>';
            }
            if (slider && !ctx.direct.muted && ctx.direct.volume === 0) {
                ctx.direct.volume = 1;
                slider.value = 100;
            }
            return;
        }

        const yt = ctx.yt;
        if (yt && typeof yt.isMuted === 'function') {
            try {
                if (yt.isMuted()) {
                    yt.unMute();
                    if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
                } else {
                    yt.mute();
                    if (muteBtn) muteBtn.innerHTML = '<i class="fa-solid fa-volume-xmark"></i>';
                }
            } catch(e) {}
        }
    }

    function setPubVideoSpeed(vidId, speed, btn) {
        const ctx = getPubPlayerContext(vidId);
        if (ctx.isDirect && ctx.direct) {
            ctx.direct.playbackRate = speed;
        } else if (ctx.yt && typeof ctx.yt.setPlaybackRate === 'function') {
            try { ctx.yt.setPlaybackRate(speed); } catch(e) {}
        }

        const active = document.getElementById(`pub_active_speed_${vidId}`);
        if (active) active.textContent = `${speed}x`;
        if (btn) {
            btn.closest('.ed-popover-menu').querySelectorAll('.ed-pop-opt').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }
        document.querySelectorAll('.ed-popover-menu').forEach(m => m.classList.remove('show'));
        showPlayerToast(`{{ __('تم ضبط سرعة العرض على') }} ${speed}x ⚡`);
    }

    function setPubVideoQuality(vidId, quality, btn, label) {
        const ctx = getPubPlayerContext(vidId);
        localStorage.setItem('platform_preferred_quality', quality);

        if (ctx.isDirect && ctx.direct) {
            const curTime = ctx.direct.currentTime || 0;
            const wasPaused = ctx.direct.paused;
            const newSrc = `/video-stream/content/${vidId}?quality=${encodeURIComponent(quality)}&t=${Date.now()}`;
            ctx.direct.src = newSrc;
            ctx.direct.load();
            ctx.direct.currentTime = curTime;
            if (!wasPaused) {
                ctx.direct.play().catch(() => {});
            }
        } else if (ctx.yt) {
            const ytQualityMap = {
                '1080p': 'hd1080',
                '720p': 'hd720',
                '480p': 'large',
                '360p': 'medium',
                'auto': 'default'
            };
            const ytQ = ytQualityMap[quality] || quality;
            if (typeof ctx.yt.setPlaybackQuality === 'function') {
                try {
                    ctx.yt.setPlaybackQuality(ytQ);
                    if (typeof ctx.yt.setPlaybackQualityRange === 'function') {
                        ctx.yt.setPlaybackQualityRange(ytQ, ytQ);
                    }
                } catch(e) {}
            }
        }

        const active = document.getElementById(`pub_active_quality_${vidId}`);
        if (active) active.textContent = quality;
        const topTag = document.getElementById(`pub_top_quality_tag_${vidId}`);
        if (topTag) topTag.innerHTML = `<i class="fa-solid fa-circle-check"></i> ${label || quality}`;
        const topTagPanel = document.getElementById(`pub_top_quality_tag_panel_${vidId}`);
        if (topTagPanel) topTagPanel.innerHTML = `<i class="fa-solid fa-circle-check"></i> ${label || quality}`;

        if (btn) {
            btn.closest('.ed-popover-menu').querySelectorAll('.ed-pop-opt').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }
        document.querySelectorAll('.ed-popover-menu').forEach(m => m.classList.remove('show'));
        showPlayerToast(`{{ __('تم تحويل الجودة إلى') }} ${label || quality} ✨`);
    }

    function toggleDropdownMenu(menuId) {
        const menu = document.getElementById(menuId);
        if (!menu) return;
        const isShown = menu.classList.contains('show');
        document.querySelectorAll('.ed-popover-menu').forEach(m => m.classList.remove('show'));
        if (!isShown) menu.classList.add('show');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.ed-speed-dropdown') && !e.target.closest('.ed-quality-dropdown')) {
            document.querySelectorAll('.ed-popover-menu').forEach(m => m.classList.remove('show'));
        }
    });

    function toggleSubjectFullscreen(btn) {
        let wrap = null;
        if (typeof btn === 'string' || typeof btn === 'number') {
            wrap = document.getElementById(`custom_wrap_${btn}`) || document.getElementById(`card_video_${btn}`);
        } else if (btn && btn.closest) {
            wrap = btn.closest('.custom-video-wrapper') || btn.closest('.video-card');
        }
        if (!wrap) return;

        const isFs = !!(
            document.fullscreenElement || 
            document.webkitFullscreenElement || 
            document.mozFullScreenElement || 
            document.msFullscreenElement || 
            wrap.classList.contains('is-platform-fullscreen')
        );

        if (!isFs) {
            if (wrap.requestFullscreen) wrap.requestFullscreen().catch(() => enterFallbackPubFullscreen(wrap));
            else if (wrap.webkitRequestFullscreen) wrap.webkitRequestFullscreen();
            else if (wrap.mozRequestFullScreen) wrap.mozRequestFullScreen();
            else if (wrap.msRequestFullscreen) wrap.msRequestFullscreen();
            else enterFallbackPubFullscreen(wrap);
        } else {
            if (document.exitFullscreen) document.exitFullscreen().catch(() => {});
            else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
            else if (document.mozCancelFullScreen) document.mozCancelFullScreen();
            else if (document.msExitFullscreen) document.msExitFullscreen();
            exitFallbackPubFullscreen(wrap);
        }
    }

    function enterFallbackPubFullscreen(wrap) {
        const vid = wrap.querySelector('video');
        if (vid && vid.webkitEnterFullscreen) {
            vid.webkitEnterFullscreen();
            return;
        }
        wrap.classList.add('is-platform-fullscreen');
        document.body.style.overflow = 'hidden';

        let exitBtn = wrap.querySelector('.ed-fs-exit-floating-btn');
        if (!exitBtn) {
            exitBtn = document.createElement('button');
            exitBtn.className = 'ed-fs-exit-floating-btn';
            exitBtn.innerHTML = '<i class="fa-solid fa-compress"></i> <span>' + '{{ __("إلغاء ملء الشاشة") }}' + '</span>';
            exitBtn.onclick = function(e) {
                e.stopPropagation();
                toggleSubjectFullscreen(wrap);
            };
            wrap.appendChild(exitBtn);
        }
        exitBtn.style.display = 'inline-flex';
    }

    function exitFallbackPubFullscreen(wrap) {
        if (wrap) wrap.classList.remove('is-platform-fullscreen');
        document.body.style.overflow = '';
        const exitBtn = wrap ? wrap.querySelector('.ed-fs-exit-floating-btn') : null;
        if (exitBtn) exitBtn.style.display = 'none';
    }

    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement && !document.webkitFullscreenElement) {
            document.querySelectorAll('.custom-video-wrapper.is-platform-fullscreen').forEach(w => exitFallbackPubFullscreen(w));
        }
    });

    function formatPubTime(seconds) {
        if (!seconds || isNaN(seconds)) return '00:00';
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins < 10 ? '0' : ''}${mins}:${secs < 10 ? '0' : ''}${secs}`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.custom-video-element').forEach(video => {
            const vidId = video.id.replace('pub_vid_', '');
            video.addEventListener('play', () => {
                const btn = document.getElementById(`pub_btn_${vidId}`);
                if (btn) btn.innerHTML = '<i class="fa-solid fa-pause"></i>';
                const pauseOverlay = document.getElementById(`pub_pause_overlay_${vidId}`);
                if (pauseOverlay) pauseOverlay.style.display = 'none';
                const poster = document.getElementById(`pub_poster_${vidId}`);
                if (poster) poster.style.display = 'none';
                startPubTracking(vidId);
                registerPubPlayerActivity(vidId);
            });
            video.addEventListener('pause', () => {
                const btn = document.getElementById(`pub_btn_${vidId}`);
                if (btn) btn.innerHTML = '<i class="fa-solid fa-play"></i>';
                const pauseOverlay = document.getElementById(`pub_pause_overlay_${vidId}`);
                if (pauseOverlay) pauseOverlay.style.display = 'flex';
                stopPubTracking(vidId);
            });
            video.addEventListener('ended', () => {
                const btn = document.getElementById(`pub_btn_${vidId}`);
                if (btn) btn.innerHTML = '<i class="fa-solid fa-rotate-right"></i>';
                const pauseOverlay = document.getElementById(`pub_pause_overlay_${vidId}`);
                if (pauseOverlay) pauseOverlay.style.display = 'flex';
                stopPubTracking(vidId);
            });

            // تطبيق تفضيل الجودة المحفوظ
            const storedQ = localStorage.getItem('platform_preferred_quality');
            if (storedQ) {
                const qPill = document.getElementById(`pub_active_quality_${vidId}`);
                if (qPill) qPill.textContent = storedQ;
            }
        });
    });

    // منع النقر بالزر الأيمن على مشغل الفيديو نهائياً
    document.addEventListener('contextmenu', function(e) {
        if (e.target.closest('.custom-video-wrapper, iframe, video')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);

    // اختصارات لوحة المفاتيح
    document.addEventListener('keydown', function(e) {
        if (['INPUT', 'TEXTAREA'].includes(e.target.tagName)) return;
        const activeWrap = document.querySelector('.custom-video-wrapper:hover, .custom-video-wrapper:fullscreen');
        if (!activeWrap) return;
        const vidId = activeWrap.id.replace('custom_wrap_', '');
        if (!vidId) return;

        if (e.code === 'Space') {
            e.preventDefault();
            toggleSubjectVideo(vidId);
        } else if (e.code === 'ArrowRight') {
            e.preventDefault();
            seekPubRelative(vidId, 10);
        } else if (e.code === 'ArrowLeft') {
            e.preventDefault();
            seekPubRelative(vidId, -10);
        } else if (e.code === 'KeyM') {
            e.preventDefault();
            togglePubMute(vidId);
        } else if (e.code === 'KeyF') {
            e.preventDefault();
            toggleSubjectFullscreen(activeWrap);
        }
    });

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
    }

    // منع القائمة المنبثقة بالزر الأيمن على مشغل الفيديو نهائياً
    document.addEventListener('contextmenu', function(e) {
        if (e.target.closest('.custom-video-wrapper, .ed-yt-viewport, .ed-yt-top-shield, .ed-yt-corner-shield, .ed-pure-player-layer, iframe, video')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);

    // حظر فتح أي روابط لليوتيوب نهائياً
    try {
        const _origWinOpenPub = window.open;
        window.open = function(url, target, features) {
            if (typeof url === 'string' && (url.includes('youtube.com') || url.includes('youtu.be') || url.includes('youtube-nocookie.com'))) {
                console.warn('[Security] YouTube direct access blocked.');
                return null;
            }
            return _origWinOpenPub.call(window, url, target, features);
        };
    } catch(e) {}

    // إزالة أي أزرار تحميل خارجي للملفات الخام لضمان الحفظ حصرياً داخل المنصة
    function purgeExternalDownloadButtons() {
        document.querySelectorAll('.ed-btn-lecture-video-download, a[download][href*="downloadVideo"], a[href*="force_download=1"]').forEach(el => el.remove());
    }
    purgeExternalDownloadButtons();
    document.addEventListener('DOMContentLoaded', purgeExternalDownloadButtons);
</script>
<script src="https://www.youtube.com/iframe_api"></script>
@endsection
