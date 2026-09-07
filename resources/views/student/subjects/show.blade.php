@extends('layouts.app')

@section('title', ($subject->name_ar ?? $subject->name) . ' | الثانوية العامة فلسطين')

@section('content')
<!-- خطوط وأيقونات حديثة -->
<link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="/js/offline-video-manager.js"></script>

<style>
    :root {
        --sub-color: {{ $subject->color ?? '#0284c7' }};
        --sub-color-dark: #0369a1;
        --sub-color-light: #f0f9ff;
        --bg-body: #f8fafc;
        --card-bg: #ffffff;
        --text-dark: #0f172a;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --radius: 20px;
        --shadow-sm: 0 4px 15px rgba(0,0,0,0.03);
        --shadow-md: 0 10px 30px rgba(0,0,0,0.06);
    }

    * { font-family: 'Alexandria', sans-serif; }

    .subject-page-wrapper {
        direction: rtl;
        max-width: 1350px;
        margin: 0 auto;
        padding: 10px 0 40px;
    }

    /* Hero Banner */
    .hero-banner {
        background: linear-gradient(135deg, var(--sub-color) 0%, #1e1b4b 100%);
        border-radius: 24px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(2, 132, 199, 0.15);
    }

    .hero-banner::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: -40px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .stage-badge {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        color: white;
        padding: 6px 16px;
        border-radius: 30px;
        font-size: 0.82rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 15px;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .hero-title {
        font-size: 2.3rem;
        font-weight: 900;
        margin: 0 0 10px;
        line-height: 1.3;
    }

    .hero-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 25px;
    }

    .btn-hero {
        padding: 11px 22px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.88rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: 0.2s;
        border: none;
    }

    .btn-hero-white {
        background: white;
        color: #0f172a;
    }
    .btn-hero-white:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }

    .btn-hero-outline {
        background: rgba(255,255,255,0.12);
        color: white;
        border: 1px solid rgba(255,255,255,0.25);
    }
    .btn-hero-outline:hover {
        background: rgba(255,255,255,0.22);
    }

    /* Layout Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 30px;
    }

    @media (max-width: 1080px) {
        .content-grid { grid-template-columns: 1fr; }
    }

    /* Video Cards */
    .video-card-modern {
        background: white;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        margin-bottom: 25px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .video-card-modern:hover {
        box-shadow: var(--shadow-md);
        border-color: var(--sub-color);
        transform: translateY(-3px);
    }

    .video-player-container {
        position: relative;
        padding-top: 56.25%;
        background: #090d16;
    }

    .video-player-container video, .video-player-container iframe {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        border: 0;
    }

    .video-header-info {
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        border-bottom: 1px solid #f1f5f9;
    }

    .lesson-tag {
        font-size: 0.75rem;
        font-weight: 700;
        background: #f1f5f9;
        color: #475569;
        padding: 4px 10px;
        border-radius: 6px;
        margin-bottom: 6px;
        display: inline-block;
    }

    .video-footer-actions {
        padding: 14px 24px;
        background: #fcfdfe;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    /* Offline download button */
    .btn-offline-save {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
    }

    .btn-offline-save:hover {
        background: #dcfce7;
        transform: scale(1.02);
    }

    .btn-offline-downloaded {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* Locked Card */
    .locked-video-card {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
        padding: 35px 25px;
        text-align: center;
        margin-bottom: 25px;
    }

    .lock-icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #fee2e2;
        color: #ef4444;
        display: grid;
        place-items: center;
        font-size: 1.5rem;
        margin: 0 auto 15px;
    }

    /* Sidebar Cards */
    .side-widget-card {
        background: white;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        padding: 24px;
        margin-bottom: 24px;
    }

    .file-download-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        margin-bottom: 10px;
        text-decoration: none;
        color: inherit;
        transition: 0.2s;
    }

    .file-download-item:hover {
        background: white;
        border-color: #ef4444;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.08);
        transform: translateX(-4px);
    }

    .file-badge-icon {
        width: 44px;
        height: 44px;
        background: #fee2e2;
        color: #dc2626;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    /* Progress bar */
    .offline-progress-bar-wrap {
        display: none;
        width: 100%;
        background: #e2e8f0;
        height: 6px;
        border-radius: 10px;
        overflow: hidden;
        margin-top: 8px;
    }

    .offline-progress-bar-fill {
        height: 100%;
        background: #10b981;
        width: 0%;
        transition: width 0.3s;
    }

    /* Drawer/Offcanvas for Offline Videos */
    .offline-drawer-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
    }

    .offline-drawer {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 420px;
        max-width: 90vw;
        background: white;
        box-shadow: 20px 0 50px rgba(0,0,0,0.15);
        z-index: 10000;
        display: flex;
        flex-direction: column;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .offline-drawer.open {
        transform: translateX(0);
    }
</style>

<div class="subject-page-wrapper">
    <!-- Hero Banner -->
    <header class="hero-banner animate__animated animate__fadeInDown">
        <div class="stage-badge">
            <i class="fa-solid fa-graduation-cap"></i>
            {{ optional($subject->stage)->label_ar ?? optional($subject->stage)->name ?? 'الثانوية العامة - فلسطين' }}
        </div>

        <h1 class="hero-title">{{ $subject->name_ar ?? $subject->name }}</h1>
        <p style="opacity: 0.9; font-size: 0.95rem; margin: 0; max-width: 600px; line-height: 1.6;">
            منهاج التوجيهي الوزاري المعتمد في فلسطين — شروحات تفصيلية، ملازم، أوراق عمل وزارية، وإمكانية المشاهدة بدون إنترنت.
        </p>

        <div class="hero-actions">
            @if($enrollment && $enrollment->access_mode === 'all')
                <span style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #a7f3d0; padding: 8px 18px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-crown"></i> اشتراك كامل المنهج مفعّل
                </span>
            @elseif($enrollment && $enrollment->access_mode === 'custom')
                <span style="background: rgba(99, 102, 241, 0.2); border: 1px solid #818cf8; color: #c7d2fe; padding: 8px 18px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-layer-group"></i> اشتراك باقة مخصصة
                </span>
            @else
                <button onclick="openRedeemModal()" class="btn-hero btn-hero-white">
                    <i class="fa-solid fa-ticket"></i> تفعيل كود المادة / بطاقة شحن
                </button>
            @endif

            <button onclick="openOfflineDrawer()" class="btn-hero btn-hero-outline">
                <i class="fa-solid fa-download"></i> فيديوهاتي بدون إنترنت
                <span id="heroOfflineBadge" style="background: white; color: var(--sub-color); padding: 1px 7px; border-radius: 10px; font-size: 0.75rem; font-weight: 800;">0</span>
            </button>

            @if($subject->teacher)
                <a href="{{ route('student.chat.teacher', $subject->teacher->id) }}" class="btn-hero btn-hero-outline">
                    <i class="fa-solid fa-comment-dots"></i> استفسار من المعلم
                </a>
            @endif
        </div>
    </header>

    <div class="content-grid">
        <!-- قسم الدروس والفيديوهات -->
        <main>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
                <h2 style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-play" style="color: var(--sub-color); font-size: 1.1rem;"></i> الدروس والحصص المرئية
                </h2>
                <span style="background: white; border: 1px solid var(--border-color); color: #475569; padding: 5px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                    {{ $videos->count() }} درس
                </span>
            </div>

            @forelse($videos as $video)
                @if($video->is_unlocked)
                    <!-- بطاقة الفيديو المتاح -->
                    <div class="video-card-modern animate__animated animate__fadeInUp" id="card_video_{{ $video->id }}">
                        <div class="video-player-container">
                            @php
                                $videoUrl = filter_var($video->url_path, FILTER_VALIDATE_URL)
                                            ? $video->url_path
                                            : asset('storage/' . $video->url_path);
                            @endphp

                            @if(strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false)
                                <iframe src="{{ str_replace('watch?v=', 'embed/', $videoUrl) }}" allowfullscreen></iframe>
                            @else
                                <video id="player_{{ $video->id }}" controls preload="metadata">
                                    <source src="{{ $videoUrl }}" type="video/mp4">
                                    متصفحك لا يدعم مشغل الفيديو.
                                </video>
                            @endif
                        </div>

                        <div class="video-header-info">
                            <div>
                                <span class="lesson-tag">{{ $video->channel_name ?? 'الدرس رقم ' . $video->order }}</span>
                                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a;">{{ $video->title }}</h3>
                            </div>
                            <div style="text-align: left;">
                                <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">الترتيب: #{{ $video->order }}</span>
                            </div>
                        </div>

                        <!-- شريط التنزيل الأوفلاين داخل المنصة -->
                        <div class="video-footer-actions">
                            <div style="display: flex; align-items: center; gap: 10px;" id="offline_action_box_{{ $video->id }}">
                                @if(strpos($videoUrl, 'youtube.com') === false && strpos($videoUrl, 'youtu.be') === false)
                                    <button type="button" class="btn-offline-save" id="btn_save_offline_{{ $video->id }}" onclick="downloadVideoOffline('{{ $video->id }}', '{{ $videoUrl }}', '{{ addslashes($video->title) }}', '{{ addslashes($subject->name_ar ?? $subject->name) }}', '{{ $subject->id }}')">
                                        <i class="fa-solid fa-download"></i> حفظ للمشاهدة بدون إنترنت (داخل المنصة)
                                    </button>
                                @else
                                    <span style="font-size: 0.8rem; color: #94a3b8;"><i class="fa-brands fa-youtube"></i> بث يوتيوب مباشر</span>
                                @endif
                            </div>

                            @if(!empty($video->pdf_path))
                                <a href="{{ route('content.download', $video->id) }}" style="color: #dc2626; font-size: 0.82rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-file-arrow-down"></i> ملزمة الدرس (PDF)
                                </a>
                            @endif
                        </div>

                        <!-- شريط تقدم التنزيل -->
                        <div class="offline-progress-bar-wrap" id="progress_wrap_{{ $video->id }}">
                            <div class="offline-progress-bar-fill" id="progress_bar_{{ $video->id }}"></div>
                        </div>
                    </div>
                @else
                    <!-- بطاقة الفيديو المقفل (غير مدرج في اشتراك الطالب الحالي) -->
                    <div class="locked-video-card">
                        <div class="lock-icon-circle">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <h4 style="margin: 0 0 8px; font-size: 1.05rem; font-weight: 800; color: #1e293b;">
                            {{ $video->title }} (محتوى مقفل)
                        </h4>
                        <p style="color: #64748b; font-size: 0.85rem; max-width: 480px; margin: 0 auto 18px; line-height: 1.5;">
                            هذا الدرس متاح ضمن باقة المنهج الكامل أو يتطلب تفعيل خاص من معلّم المادة.
                        </p>
                        <div style="display: flex; justify-content: center; gap: 10px;">
                            @if($subject->teacher)
                                <a href="{{ route('student.chat.teacher', $subject->teacher->id) }}" style="background: white; border: 1px solid var(--border-color); color: #1e293b; padding: 8px 16px; border-radius: 10px; font-size: 0.82rem; font-weight: 700; text-decoration: none;">
                                    <i class="fa-solid fa-comment-dots"></i> طلب تفعيل من المعلم
                                </a>
                            @endif
                            <button onclick="openRedeemModal()" style="background: var(--sub-color); color: white; border: none; padding: 8px 16px; border-radius: 10px; font-size: 0.82rem; font-weight: 700; cursor: pointer;">
                                <i class="fa-solid fa-key"></i> إدخال كود التفعيل
                            </button>
                        </div>
                    </div>
                @endif
            @empty
                <div style="text-align: center; padding: 60px; background: white; border-radius: 20px; border: 1px dashed var(--border-color); color: #94a3b8;">
                    <i class="fa-solid fa-video-slash" style="font-size: 3rem; margin-bottom: 12px; display: block; opacity: 0.4;"></i>
                    <p style="font-weight: 600; font-size: 0.95rem; margin: 0;">لم تتم إضافة فيديوهات لهذه المادة حتى الآن.</p>
                </div>
            @endforelse
        </main>

        <!-- السايدبار الجانبي للمادة -->
        <aside>
            <!-- بطاقة المعلم -->
            <div class="side-widget-card" style="text-align: center;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--sub-color) 0%, #1e1b4b 100%); color: white; display: grid; place-items: center; font-size: 1.8rem; font-weight: 900; margin: 0 auto 15px; border: 4px solid #f0f9ff;">
                    {{ mb_substr(optional($subject->teacher)->name ?? 'م', 0, 1) }}
                </div>
                <h3 style="margin: 0 0 4px; font-size: 1.15rem; font-weight: 800; color: #0f172a;">
                    {{ optional($subject->teacher)->name ?? 'مدرس المادة' }}
                </h3>
                <span style="font-size: 0.8rem; color: #64748b; font-weight: 600; display: block; margin-bottom: 16px;">
                    معلم معتمد لمسار الثانوية العامة
                </span>
                @if($subject->teacher)
                    <a href="{{ route('student.chat.teacher', $subject->teacher->id) }}" style="background: #f1f5f9; color: #1e293b; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; text-decoration: none; transition: 0.2s;">
                        <i class="fa-solid fa-paper-plane" style="color: var(--sub-color);"></i> مراسلة المعلم مباشرة
                    </a>
                @endif
            </div>

            <!-- بطاقة الملازم والملفات القابلة للتنزيل -->
            <div class="side-widget-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h4 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i> الملازم وأوراق العمل
                    </h4>
                    <span style="font-size: 0.78rem; font-weight: 700; background: #fee2e2; color: #dc2626; padding: 3px 10px; border-radius: 10px;">
                        {{ $files->count() }} ملف
                    </span>
                </div>

                @forelse($files as $file)
                    <a href="{{ route('content.download', $file->id) }}" class="file-download-item">
                        <div class="file-badge-icon">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 700; font-size: 0.88rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $file->title }}
                            </div>
                            <span style="font-size: 0.75rem; color: #64748b; display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                <i class="fa-solid fa-cloud-arrow-down" style="color: #10b981;"></i> اضغط للتنزيل المباشر
                            </span>
                        </div>
                        <i class="fa-solid fa-download" style="color: #94a3b8; font-size: 0.85rem;"></i>
                    </a>
                @empty
                    <p style="text-align: center; color: #94a3b8; font-size: 0.85rem; padding: 15px 0; margin: 0;">
                        لا توجد ملفات مرفقة حالياً.
                    </p>
                @endforelse
            </div>
        </aside>
    </div>
</div>

<!-- درج الفيديوهات المحملة أوفلاين (Offcanvas) -->
<div class="offline-drawer-backdrop" id="drawerBackdrop" onclick="closeOfflineDrawer()"></div>
<div class="offline-drawer" id="offlineDrawer">
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
        <div>
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a;">فيديوهاتي بدون إنترنت</h3>
            <span style="font-size: 0.78rem; color: #64748b;">محفوظة محلياً داخل المنصة فقط</span>
        </div>
        <button onclick="closeOfflineDrawer()" style="background: none; border: none; font-size: 1.4rem; color: #94a3b8; cursor: pointer;">&times;</button>
    </div>

    <div style="padding: 20px; overflow-y: auto; flex: 1;" id="offlineVideosList">
        <!-- قائمة الفيديوهات المخزنة محلياً -->
    </div>
</div>

<!-- Modal تفعيل كود الشحن والاشتراك -->
<div class="offline-drawer-backdrop" id="redeemModalBackdrop" style="display: none; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 20px; max-width: 460px; width: 90%; padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 55px; height: 55px; border-radius: 14px; background: #f0fdf4; color: #16a34a; display: grid; place-items: center; font-size: 1.5rem; margin: 0 auto 12px;">
                <i class="fa-solid fa-credit-card"></i>
            </div>
            <h3 style="margin: 0 0 6px; font-weight: 800; color: #0f172a;">تفعيل كود شحن المادة</h3>
            <p style="margin: 0; font-size: 0.85rem; color: #64748b;">أدخل كود الشحن المتوفر في بطاقتك لتفعيل كامل المنهج أو الباقة فوراً.</p>
        </div>

        <form onsubmit="handleRedeemCode(event)">
            <input type="hidden" id="redeemSubjectId" value="{{ $subject->id }}">
            <div style="margin-bottom: 20px;">
                <input type="text" id="voucherCodeInput" required placeholder="مثال: TAW-XXXX-XXXX" style="width: 100%; padding: 14px; border: 2px solid var(--border-color); border-radius: 12px; font-size: 1.1rem; font-weight: 700; text-align: center; letter-spacing: 2px; text-transform: uppercase;">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeRedeemModal()" style="flex: 1; padding: 12px; border: 1px solid var(--border-color); background: white; border-radius: 12px; font-weight: 700; cursor: pointer; color: #64748b;">إلغاء</button>
                <button type="submit" id="btnSubmitRedeem" style="flex: 2; padding: 12px; border: none; background: #10b981; color: white; border-radius: 12px; font-weight: 700; cursor: pointer;">تفعيل الآن 🚀</button>
            </div>
        </form>
    </div>
</div>

<script>
// تحديث أزرار الفيديوهات المحملة أوفلاين عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', async () => {
    refreshOfflineBadges();
});

async function refreshOfflineBadges() {
    if (!window.offlineVideoManager) return;
    const allDownloaded = await window.offlineVideoManager.getAllDownloaded();
    const heroBadge = document.getElementById('heroOfflineBadge');
    if (heroBadge) heroBadge.textContent = allDownloaded.length;

    allDownloaded.forEach(item => {
        markVideoAsDownloadedUI(item.id);
    });
}

function markVideoAsDownloadedUI(videoId) {
    const box = document.getElementById(`offline_action_box_${videoId}`);
    if (!box) return;

    box.innerHTML = `
        <span class="btn-offline-downloaded">
            <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> محفوظ داخل المنصة
        </span>
        <button type="button" onclick="playLocalOfflineVideo('${videoId}')" style="background: #10b981; color: white; border: none; padding: 7px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
            <i class="fa-solid fa-play"></i> تشغيل محلي أوفلاين
        </button>
        <button type="button" onclick="deleteLocalVideo('${videoId}')" title="حذف من الذاكرة المحلية" style="background: none; border: none; color: #ef4444; font-size: 0.9rem; cursor: pointer; padding: 4px;">
            <i class="fa-solid fa-trash-can"></i>
        </button>
    `;
}

// تنزيل الفيديو مشفراً ومحلياً داخل المنصة (IndexedDB)
async function downloadVideoOffline(videoId, videoUrl, title, subjectName, subjectId) {
    const btn = document.getElementById(`btn_save_offline_${videoId}`);
    const progressWrap = document.getElementById(`progress_wrap_${videoId}`);
    const progressBar = document.getElementById(`progress_bar_${videoId}`);

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التحميل داخل المنصة...';
    }
    if (progressWrap) progressWrap.style.display = 'block';

    try {
        await window.offlineVideoManager.downloadVideo(
            videoId,
            videoUrl,
            title,
            subjectName,
            subjectId,
            (percent) => {
                if (percent !== null && progressBar) {
                    progressBar.style.width = percent + '%';
                    if (btn) btn.innerHTML = `<i class="fa-solid fa-download"></i> جاري التحميل ${percent}%`;
                }
            }
        );

        if (progressWrap) progressWrap.style.display = 'none';
        markVideoAsDownloadedUI(videoId);
        refreshOfflineBadges();

        Swal.fire({
            icon: 'success',
            title: 'تم التنزيل بنجاح!',
            text: 'تم حفظ الفيديو داخل مساحتك الآمنة في المنصة، يمكنك مشاهدته في أي وقت بدون إنترنت.',
            confirmButtonText: 'ممتاز'
        });

    } catch (err) {
        if (progressWrap) progressWrap.style.display = 'none';
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> فشل التحميل، أعد المحاولة';
        }
        Swal.fire('خطأ', 'تعذر تحميل الفيديو أوفلاين، تأكد من الاتصال وجرب مجدداً.', 'error');
    }
}

// تشغيل الفيديو المخزن محلياً داخل مشغل الصفحة
async function playLocalOfflineVideo(videoId) {
    const item = await window.offlineVideoManager.getVideo(videoId);
    if (!item || !item.blob) {
        Swal.fire('تنبيه', 'لم يتم العثور على الفيديو محلياً.', 'warning');
        return;
    }

    const player = document.getElementById(`player_${videoId}`);
    if (player) {
        const localBlobUrl = URL.createObjectURL(item.blob);
        player.src = localBlobUrl;
        player.scrollIntoView({ behavior: 'smooth', block: 'center' });
        player.play();

        Swal.fire({
            toast: true,
            position: 'top-start',
            icon: 'info',
            title: 'يتم الآن التشغيل بدون استهلاك إنترنت (من الذاكرة المحلية) ⚡',
            showConfirmButton: false,
            timer: 3500
        });
    }
}

// حذف الفيديو من التخزين المحلي
async function deleteLocalVideo(videoId) {
    const confirm = await Swal.fire({
        title: 'حذف من المشاهدة بدون إنترنت؟',
        text: 'سيتم مسح الفيديو من ذاكرة المنصة المحلية لتوفير مساحة جهازك.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، حذف',
        cancelButtonText: 'إلغاء'
    });

    if (confirm.isConfirmed) {
        await window.offlineVideoManager.deleteVideo(videoId);
        window.location.reload();
    }
}

// فتح وإغلاق درج الفيديوهات المحملة أوفلاين
async function openOfflineDrawer() {
    const drawer = document.getElementById('offlineDrawer');
    const backdrop = document.getElementById('drawerBackdrop');
    const listContainer = document.getElementById('offlineVideosList');

    drawer.classList.add('open');
    backdrop.style.display = 'block';

    const videos = await window.offlineVideoManager.getAllDownloaded();
    if (!videos || videos.length === 0) {
        listContainer.innerHTML = `
            <div style="text-align:center; padding:50px 20px; color:#94a3b8;">
                <i class="fa-solid fa-box-open" style="font-size:2.5rem; margin-bottom:10px; display:block; opacity:0.4;"></i>
                <p style="font-size:0.9rem; font-weight:600;">لا توجد فيديوهات محفوظة بدون إنترنت حتى الآن.</p>
            </div>
        `;
        return;
    }

    let html = '';
    videos.forEach(v => {
        const sizeMb = v.size ? (v.size / (1024 * 1024)).toFixed(1) + ' MB' : '';
        html += `
            <div style="background:#f8fafc; border:1px solid var(--border-color); border-radius:14px; padding:14px; margin-bottom:12px;">
                <div style="font-weight:700; font-size:0.9rem; color:#0f172a; margin-bottom:4px;">${v.title}</div>
                <div style="font-size:0.78rem; color:#64748b; margin-bottom:10px;">${v.subject_name} • ${sizeMb}</div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <button onclick="playLocalOfflineVideo('${v.id}'); closeOfflineDrawer();" style="background:var(--sub-color); color:white; border:none; padding:6px 14px; border-radius:8px; font-size:0.8rem; font-weight:700; cursor:pointer;">
                        <i class="fa-solid fa-play"></i> تشغيل
                    </button>
                    <button onclick="deleteLocalVideo('${v.id}')" style="background:none; border:none; color:#ef4444; font-size:0.8rem; cursor:pointer;">
                        حذف
                    </button>
                </div>
            </div>
        `;
    });
    listContainer.innerHTML = html;
}

function closeOfflineDrawer() {
    document.getElementById('offlineDrawer').classList.remove('open');
    document.getElementById('drawerBackdrop').style.display = 'none';
}

// Modal تفعيل كود الشحن
function openRedeemModal() {
    document.getElementById('redeemModalBackdrop').style.display = 'flex';
}

function closeRedeemModal() {
    document.getElementById('redeemModalBackdrop').style.display = 'none';
}

function handleRedeemCode(e) {
    e.preventDefault();
    const code = document.getElementById('voucherCodeInput').value.trim();
    const subjectId = document.getElementById('redeemSubjectId').value;
    const btn = document.getElementById('btnSubmitRedeem');

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التحقق...';

    axios.post('/student/redeem-code', {
        code: code,
        subject_id: subjectId,
        _token: '{{ csrf_token() }}'
    }).then(res => {
        btn.disabled = false;
        btn.textContent = 'تفعيل الآن 🚀';
        closeRedeemModal();
        Swal.fire({
            icon: 'success',
            title: 'تم التفعيل بنجاح!',
            text: res.data.message,
            confirmButtonText: 'رائع'
        }).then(() => {
            window.location.reload();
        });
    }).catch(err => {
        btn.disabled = false;
        btn.textContent = 'تفعيل الآن 🚀';
        const msg = err.response?.data?.message || 'كود التفعيل غير صالح، يرجى التأكد وإعادة المحاولة.';
        Swal.fire('خطأ في التفعيل', msg, 'error');
    });
}
</script>
@endsection
