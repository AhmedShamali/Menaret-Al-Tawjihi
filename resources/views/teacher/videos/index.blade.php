@extends('layouts.app')

@section('title', __('إدارة ورفع الفيديوهات والشروحات') . ' | ' . config('app.name', 'Step by Step'))

@section('content')
<div class="ed-teacher-videos-container">

    <!-- الهيدر الأكاديمي الرسمي -->
    <header class="ed-teacher-header">
        <div>
            <div class="ed-teacher-breadcrumbs">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('teacher.dashboard') }}">
                    {{ auth()->user()->role === 'admin' ? __('لوحة الإدارة') : __('بوابة المعلم المعتمد') }}
                </a>
                <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                <span class="active">{{ __('المحتوى المرئي والفيديوهات') }}</span>
            </div>
            <h1>
                <i class="fa-solid fa-video" style="color: #1e3a8a;"></i>
                {{ __('إدارة ورفع الفيديوهات والشروحات المرئية') }}
            </h1>
            <p>
                {{ __('رفع وإدارة حصص وشروحات المنهاج الفلسطيني عبر رفع ملفات الفيديو المباشرة عالية الدقة، مع تنظيم ترتيب الدروس والتحكم بظهورها ودعم المشاهدة الأوفلاين للطلبة.') }}
            </p>
        </div>

        <div class="header-actions">
            <button type="button" onclick="openUploadVideoModal()" class="ed-btn-upload">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>{{ __('إضافة فيديو / شرح جديد') }}</span>
            </button>
        </div>
    </header>

    <!-- شريط الإحصائيات والمؤشرات السريعة -->
    <div class="ed-stats-strip">
        <div class="stat-box">
            <div class="stat-icon-wrap" style="background: #eff6ff; color: #1e40af;">
                <i class="fa-solid fa-film"></i>
            </div>
            <div>
                <span class="stat-num">{{ $stats['total'] ?? $videos->total() }}</span>
                <span class="stat-label">{{ __('إجمالي الفيديوهات والشروحات') }}</span>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-icon-wrap" style="background: #ecfdf5; color: #059669;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="stat-num">{{ $stats['visible'] ?? 0 }}</span>
                <span class="stat-label">{{ __('شروحات مفعلة ومتاحة للطلبة') }}</span>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-icon-wrap" style="background: #fffbeb; color: #d97706;">
                <i class="fa-solid fa-eye-slash"></i>
            </div>
            <div>
                <span class="stat-num">{{ $stats['hidden'] ?? 0 }}</span>
                <span class="stat-label">{{ __('شروحات محجوبة مؤقتاً') }}</span>
            </div>
        </div>
    </div>

    <!-- شريط التصفية حسب المادة -->
    @if(auth()->user()->role === 'admin' || count($subjects) > 1)
        <div class="ed-filter-bar">
            <div class="filter-label">
                <i class="fa-solid fa-filter"></i>
                <span>{{ __('تصفية حسب المادة الدراسية:') }}</span>
            </div>
            <div class="filter-pills">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.videos') : route('teacher.videos') }}" class="filter-chip {{ empty(request('subject_id')) ? 'active' : '' }}">
                    {{ auth()->user()->role === 'admin' ? __('جميع المواد') : __('جميع موادي') }}
                </a>
                @foreach($subjects as $sub)
                    <a href="{{ (auth()->user()->role === 'admin' ? route('admin.videos') : route('teacher.videos')) . '?subject_id=' . $sub->id }}" class="filter-chip {{ request('subject_id') == $sub->id ? 'active' : '' }}">
                        {{ $sub->name_ar ?? $sub->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- شبكة بطاقات الفيديوهات -->
    <div class="ed-videos-grid">
        @forelse($videos as $vid)
            @php
                $isDirectVid = (bool) preg_match('/\.(mp4|webm|ogg|mov|m4v)($|\?)/i', $vid->url_path ?? '') || str_contains($vid->url_path ?? '', 'educational/videos');
                $directVidUrl = $isDirectVid ? \App\Support\MediaHelper::url($vid->url_path) : null;
                $embedUrl = $vid->youtube_embed_url ?? ($directVidUrl ?? \App\Support\MediaHelper::url($vid->url_path));
            @endphp
            <div class="ed-video-card">
                <div>
                    <div class="video-frame-wrap" id="wrap_vid_{{ $vid->id }}" oncontextmenu="event.preventDefault(); return false;">
                        @if($isDirectVid && $directVidUrl)
                            <video id="vid_direct_{{ $vid->id }}" controls preload="metadata" playsinline controlsList="nodownload" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; background: #000;">
                                <source src="{{ $directVidUrl }}" type="video/mp4">
                            </video>
                        @else
                            @php
                                $ytTargetUrl = $vid->youtube_embed_url ?? $embedUrl;
                                if (!empty($ytTargetUrl)) {
                                    $ytTargetUrl = str_replace(['https://www.youtube.com/embed/', 'http://www.youtube.com/embed/'], 'https://www.youtube-nocookie.com/embed/', $ytTargetUrl);
                                    if (!str_contains($ytTargetUrl, 'enablejsapi=1')) {
                                        $ytTargetUrl .= (str_contains($ytTargetUrl, '?') ? '&' : '?') . 'enablejsapi=1&rel=0&modestbranding=1&iv_load_policy=3&controls=0&showinfo=0&fs=0&disablekb=1&playsinline=1';
                                    } else {
                                        $ytTargetUrl = str_replace(['controls=1', 'fs=1'], ['controls=0', 'fs=0'], $ytTargetUrl);
                                    }
                                }
                            @endphp

                            <iframe id="iframe_teacher_{{ $vid->id }}" 
                                    src="{{ $ytTargetUrl }}" 
                                    style="position: absolute; inset: 0; width: 100%; height: 100%; border: none; pointer-events: none !important; z-index: 1;" 
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
                                    sandbox="allow-scripts allow-same-origin allow-presentation allow-forms"
                                    loading="lazy">
                            </iframe>

                            <!-- واجهة التشغيل المركزية التفاعلية للمنصة -->
                            <div class="ed-screen-shield" id="shield_vid_{{ $vid->id }}" onclick="toggleTeacherPlayback('{{ $vid->id }}')" style="z-index: 20;">
                                <button type="button" class="ed-center-play-btn" id="center_play_{{ $vid->id }}" aria-label="{{ __('تشغيل') }}">
                                    <i class="fa-solid fa-play"></i>
                                </button>
                            </div>

                            <!-- دروع إضافية للأركان والعنوان لمنع أي تسريب تفاعلي للروابط -->
                            <div class="ed-shield-corner-bl" onclick="toggleTeacherPlayback('{{ $vid->id }}')"></div>
                            <div class="ed-shield-corner-br" onclick="toggleTeacherPlayback('{{ $vid->id }}')"></div>
                            <div class="ed-shield-top-band" onclick="toggleTeacherPlayback('{{ $vid->id }}')"></div>

                            <!-- شريط التحكم السفلي المدمج الخاص بالمنصة -->
                            <div class="ed-inline-player-bar" onclick="event.stopPropagation();" oncontextmenu="event.preventDefault(); return false;">
                                <button type="button" class="btn-ctrl-action" onclick="toggleTeacherPlayback('{{ $vid->id }}')" id="bar_btn_{{ $vid->id }}" title="{{ __('تشغيل / إيقاف') }}">
                                    <i class="fa-solid fa-play"></i>
                                </button>
                                <span class="ctrl-time-text" id="time_txt_{{ $vid->id }}">00:00 / --:--</span>
                                <input type="range" class="ctrl-seek-range" id="seek_range_{{ $vid->id }}" min="0" max="100" value="0" step="0.1" oninput="seekTeacherPlayback('{{ $vid->id }}', this.value)">
                                <button type="button" class="btn-ctrl-action" onclick="toggleTeacherFullscreen('wrap_vid_{{ $vid->id }}')" title="{{ __('ملء الشاشة') }}">
                                    <i class="fa-solid fa-expand"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="video-info-box">
                        <div class="video-meta-row">
                            <span class="subject-badge">
                                <i class="fa-solid fa-graduation-cap"></i>
                                {{ $vid->subject?->name_ar ?? __('عام') }}
                            </span>
                            <span class="order-badge">
                                <i class="fa-solid fa-arrow-down-1-9"></i>
                                {{ __('ترتيب الدرس:') }} #{{ $vid->order }}
                            </span>
                        </div>
                        <h3 class="video-title">{{ $vid->title }}</h3>
                        <p class="video-channel">
                            @if($isDirectVid)
                                <i class="fa-solid fa-file-video" style="color: #2563eb;"></i>
                                <span style="color: #1e40af; font-weight: 700;">{{ __('ملف فيديو محلي مرفوع على المنصة') }}</span>
                            @else
                                <i class="fa-solid fa-circle-play" style="color: #2563eb;"></i>
                                <span style="color: #1e3a8a; font-weight: 700;">{{ __('مشغل الفيديو الآمن للمنصة') }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="video-card-footer">
                    <button type="button" onclick="toggleVisibility({{ $vid->id }}, this)" class="visibility-toggle-btn {{ $vid->is_visible ? 'is-visible' : 'is-hidden' }}" title="{{ __('انقر لتبديل الظهور للطلبة') }}">
                        <i class="fa-solid {{ $vid->is_visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                        <span>{{ $vid->is_visible ? __('متاح للطلبة') : __('محجوب') }}</span>
                    </button>

                    <div class="action-btns">
                        @if($isDirectVid && $directVidUrl)
                            <a href="{{ route('content.downloadVideo', $vid->id) }}" class="btn-edit" title="{{ __('تحميل ملف الفيديو') }}" style="background: #f0fdf4; color: #166534; border-color: #bbf7d0;">
                                <i class="fa-solid fa-cloud-arrow-down"></i>
                            </a>
                        @endif

                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.educational_contents.edit', $vid->id) }}" class="btn-edit" title="{{ __('تعديل') }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @else
                            <a href="{{ route('teacher.educational_contents.edit', $vid->id) }}" class="btn-edit" title="{{ __('تعديل') }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @endif

                        <button type="button" onclick="deleteVideoItem({{ $vid->id }})" class="btn-delete" title="{{ __('حذف') }}">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="ed-empty-videos">
                <i class="fa-solid fa-film"></i>
                <h3>{{ __('لا توجد شروحات فيديو مسجلة حالياً') }}</h3>
                <p>{{ __('انقر على زر "إضافة فيديو / شرح جديد" لإضافة أول درس مرئي لطلبتك في هذه المادة.') }}</p>
                <button type="button" onclick="openUploadVideoModal()" class="ed-btn-upload" style="margin: 0 auto;">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ __('إضافة فيديو / شرح جديد الآن') }}</span>
                </button>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px;">
        {{ $videos->links() }}
    </div>

</div>

<!-- نافذة Modal إضافة فيديو جديد -->
<div id="uploadVideoModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-head">
            <h3>
                <div class="modal-head-icon">
                    <i class="fa-solid fa-film"></i>
                </div>
                <span>{{ __('إضافة درس أو شرح مرئي جديد') }}</span>
            </h3>
            <button type="button" onclick="closeUploadVideoModal()" class="btn-close-modal" title="{{ __('إغلاق النافذة') }}">&times;</button>
        </div>

        <form id="uploadVideoForm" onsubmit="submitVideoForm(event)" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="video">

            <div class="modal-form-body">
                <!-- المادة الدراسية -->
                <div class="f-group">
                    <label class="f-label">{{ __('المادة الدراسية والمرحلة *') }}</label>
                    <select name="subject_id" required class="f-control">
                        <option value="">{{ __('اختر المادة الدراسية...') }}</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}" {{ (isset($subjectId) && $subjectId == $sub->id) ? 'selected' : '' }}>
                                {{ $sub->name_ar ?? $sub->name }} {{ optional($sub->stage)->label_ar ? ' - (' . optional($sub->stage)->label_ar . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- عنوان الفيديو -->
                <div class="f-group">
                    <label class="f-label">{{ __('عنوان الدرس / الشرح المرئي *') }}</label>
                    <input type="text" name="title" required placeholder="{{ __('مثال: شرح الوحدة الأولى - الدرس الأول: القوانين الأساسية') }}" class="f-control">
                </div>

                <!-- رفع ملف الفيديو محلياً بنظام الأجزاء السريع والآمن -->
                <div class="f-group" id="groupVideoFile">
                    <label class="f-label" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>
                            <i class="fa-solid fa-cloud-arrow-up" style="color: #2563eb;"></i>
                            {{ __('ملف الفيديو للدرس (MP4 / WebM) *') }}
                        </span>
                        <span style="font-size: 0.78rem; font-weight: 700; color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 6px;">
                            ⚡ {{ __('رفع محلي سريع + يدعم الأوفلاين للطلبة') }}
                        </span>
                    </label>

                    <div class="ed-video-dropzone" id="videoDropzone" onclick="document.getElementById('videoFileInput').click()">
                        <input type="file" name="video_file" id="videoFileInput" accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-m4v" style="display: none;" onchange="handleVideoFileSelection(this)">
                        <div class="dropzone-inner" id="dropzoneContent">
                            <div class="dropzone-icon">
                                <i class="fa-solid fa-film"></i>
                            </div>
                            <div class="dropzone-text">
                                <strong id="dropzoneTitle">{{ __('اسحب ملف الفيديو وأفلته هنا، أو اضغط للاختيار') }}</strong>
                                <span id="dropzoneSubtitle">{{ __('يدعم ملفات الفيديو بأي حجم مهما كانت ضخمة بالجيجاوات (1GB, 5GB, 10GB+) مع التجزئة والاستئناف التلقائي.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- شريط تقدم الرفع المباشر بالأجزاء للملفات الضخمة بالجيجابايت -->
                    <div id="chunkProgressWrap" style="display: none; margin-top: 14px; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 14px 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span id="chunkProgressStatus" style="font-size: 0.85rem; font-weight: 800; color: #1e40af; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <span>{{ __('جاري بدء تجهيز ورفع أجزاء الفيديو...') }}</span>
                            </span>
                            <span id="chunkProgressPct" style="font-size: 0.95rem; font-weight: 900; color: #1e3a8a; font-family: monospace;">0%</span>
                        </div>
                        <div style="width: 100%; height: 10px; background: #e2e8f0; border-radius: 999px; overflow: hidden; position: relative;">
                            <div id="chunkProgressBar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #2563eb, #3b82f6, #059669); transition: width 0.2s ease;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px; font-size: 0.76rem; color: #64748b; flex-wrap: wrap; gap: 6px;">
                            <span id="chunkFileMeta" style="font-weight: 700; color: #334155;">-- / --</span>
                            <span id="chunkSpeedMeta" style="font-weight: 700; color: #059669;"><i class="fa-solid fa-gauge-high"></i> --</span>
                            <span id="chunkEtaMeta" style="font-weight: 700; color: #d97706;"><i class="fa-solid fa-clock"></i> --</span>
                            <span id="chunkPartMeta" style="font-weight: 700; color: #64748b;">--</span>
                        </div>
                    </div>
                </div>

                <div class="f-row" style="margin-top: 14px;">
                    <div class="f-group" style="flex: 1;">
                        <label class="f-label">{{ __('اسم المعلم / مقدم الشرح') }}</label>
                        <input type="text" name="channel_name" value="{{ auth()->user()->name_ar ?? auth()->user()->name }}" placeholder="{{ __('مثال: م.أحمد شمالي') }}" class="f-control">
                    </div>

                    <div class="f-group" style="width: 130px;">
                        <label class="f-label">{{ __('ترتيب الدرس') }}</label>
                        <input type="number" name="order" value="1" min="1" class="f-control font-mono">
                    </div>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" onclick="closeUploadVideoModal()" class="btn-modal-cancel" id="btnCancelUpload">{{ __('إلغاء') }}</button>
                <button type="submit" id="btnSubmitVideo" class="btn-modal-submit">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ __('بدء رفع ونشر الفيديو') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* ==========================================================
   ACADEMIC VIDEO MANAGEMENT STYLES
   ========================================================== */
.ed-teacher-videos-container {
    width: 100%;
    margin: 0 auto;
    padding: 0 0 60px;
    box-sizing: border-box;
}

.ed-teacher-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 20px;
}

.ed-teacher-breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
    color: #64748b;
    margin-bottom: 6px;
}

.ed-teacher-breadcrumbs a {
    color: inherit;
    text-decoration: none;
    font-weight: 600;
}

.ed-teacher-breadcrumbs a:hover {
    color: #1e3a8a;
}

.ed-teacher-breadcrumbs .active {
    color: #1e3a8a;
    font-weight: 800;
}

.ed-teacher-header h1 {
    margin: 0 0 6px;
    font-size: 1.55rem;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ed-teacher-header p {
    margin: 0;
    color: #64748b;
    font-size: 0.88rem;
    max-width: 780px;
    line-height: 1.6;
}

.ed-btn-upload {
    background: #1e3a8a;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 800;
    font-size: 0.88rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15);
}

.ed-btn-upload:hover {
    background: #0f172a;
    transform: translateY(-1px);
}

/* Stats Strip */
.ed-stats-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.stat-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.stat-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.stat-num {
    display: block;
    font-size: 1.45rem;
    font-weight: 900;
    color: #0f172a;
    font-family: inherit;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.78rem;
    font-weight: 700;
    color: #64748b;
}

/* Filter Bar */
.ed-filter-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 18px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.filter-label {
    font-size: 0.82rem;
    font-weight: 700;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 6px;
}

.filter-pills {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filter-chip {
    text-decoration: none;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 6px;
    background: #f1f5f9;
    color: #475569;
    transition: all 0.15s ease;
}

.filter-chip.active {
    background: #1e3a8a;
    color: #ffffff;
}

.filter-chip:hover:not(.active) {
    background: #e2e8f0;
    color: #0f172a;
}

/* Videos Grid */
.ed-videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 22px;
}

.ed-video-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.ed-video-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
}

.video-frame-wrap {
    position: relative;
    padding-top: 56.25%;
    background: #0f172a;
    overflow: hidden;
    user-select: none;
    -webkit-user-select: none;
}

/* واجهة المشغل الآمن والمخصص للمنصة */
.ed-screen-shield {
    position: absolute;
    inset: 0;
    z-index: 20;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    transition: background 0.2s ease;
}

.ed-center-play-btn {
    width: 62px;
    height: 62px;
    border-radius: 50%;
    background: rgba(15, 23, 42, 0.85);
    border: 2px solid rgba(255, 255, 255, 0.9);
    color: #ffffff;
    font-size: 1.35rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    pointer-events: none;
}

.ed-screen-shield:hover .ed-center-play-btn {
    transform: scale(1.1);
    background: #2563eb;
    border-color: #60a5fa;
}

/* دروع حماية إضافية للأركان والعنوان لمنع أي تسريب لأزرار يوتيوب */
.ed-shield-corner-bl {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 140px;
    height: 100px;
    z-index: 25;
    background: transparent;
    cursor: pointer;
}

.ed-shield-corner-br {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 140px;
    height: 70px;
    z-index: 25;
    background: transparent;
    cursor: pointer;
}

.ed-shield-top-band {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 80px;
    z-index: 25;
    background: transparent;
    cursor: pointer;
}

.ed-inline-player-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 44px;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0) 0%, rgba(15, 23, 42, 0.95) 100%);
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 12px;
    z-index: 30;
    user-select: none;
}

.btn-ctrl-action {
    background: none;
    border: none;
    color: #ffffff;
    font-size: 0.95rem;
    cursor: pointer;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: background 0.15s;
}

.btn-ctrl-action:hover {
    background: rgba(255, 255, 255, 0.2);
}

.ctrl-time-text {
    font-family: monospace;
    font-size: 0.76rem;
    font-weight: 700;
    color: #cbd5e1;
    white-space: nowrap;
    direction: ltr;
}

.ctrl-seek-range {
    flex: 1;
    accent-color: #38bdf8;
    cursor: pointer;
    height: 5px;
    border-radius: 3px;
}

.video-frame-wrap:fullscreen {
    width: 100vw !important;
    height: 100vh !important;
    padding-top: 0 !important;
    background: #000 !important;
}

.video-frame-wrap:fullscreen iframe {
    width: 100% !important;
    height: 100% !important;
}

.video-frame-wrap:fullscreen .ed-inline-player-bar {
    position: fixed;
    bottom: 24px;
    left: 40px;
    right: 40px;
    height: 52px;
    padding: 0 20px;
    border-radius: 14px;
    background: rgba(15, 23, 42, 0.88);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.video-info-box {
    padding: 16px;
}

.video-meta-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}

.subject-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.72rem;
    font-weight: 700;
}

.order-badge {
    font-size: 0.72rem;
    font-weight: 700;
    color: #64748b;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 3px 8px;
    border-radius: 4px;
}

.video-title {
    font-size: 0.98rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
    line-height: 1.5;
}

.video-channel {
    font-size: 0.78rem;
    color: #64748b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.video-card-footer {
    border-top: 1px solid #f1f5f9;
    padding: 12px 16px;
    background: #fafafa;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.visibility-toggle-btn {
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 0.76rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 8px;
    border-radius: 6px;
    transition: 0.15s;
}

.visibility-toggle-btn.is-visible {
    background: #ecfdf5;
    color: #059669;
}

.visibility-toggle-btn.is-hidden {
    background: #fef2f2;
    color: #dc2626;
}

.action-btns {
    display: flex;
    gap: 6px;
    align-items: center;
}

.btn-edit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: #eff6ff;
    color: #1d4ed8;
    text-decoration: none;
    font-size: 0.85rem;
    transition: 0.15s;
}

.btn-edit:hover {
    background: #dbeafe;
}

.btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    background: #fef2f2;
    color: #dc2626;
    cursor: pointer;
    font-size: 0.85rem;
    transition: 0.15s;
}

.btn-delete:hover {
    background: #fee2e2;
}

/* Empty State */
.ed-empty-videos {
    grid-column: 1 / -1;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 60px 20px;
    text-align: center;
}

.ed-empty-videos i {
    font-size: 3rem;
    color: #94a3b8;
    margin-bottom: 12px;
}

.ed-empty-videos h3 {
    font-size: 1.2rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 6px;
}

.ed-empty-videos p {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 20px;
}

/* Modal */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 99999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

/* ==========================================================
   CLASSIC ROYAL ACADEMIC UPLOAD MODAL & DROPZONE
   ========================================================== */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-card {
    background: #ffffff;
    border-radius: 18px;
    max-width: 620px;
    width: 100%;
    padding: 28px 30px;
    box-shadow: 0 24px 60px rgba(11, 59, 111, 0.22);
    border: 1px solid #e2e8f0;
    border-top: 4px solid #0b3b6f;
    animation: modalScale 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-sizing: border-box;
}

@keyframes modalScale {
    from { opacity: 0; transform: scale(0.95) translateY(8px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.modal-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1.5px solid #f1f5f9;
    padding-bottom: 16px;
    margin-bottom: 20px;
}

.modal-head h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 800;
    color: #0b3b6f;
    display: flex;
    align-items: center;
    gap: 12px;
    letter-spacing: -0.01em;
}

.modal-head-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #eff6ff;
    color: #0b3b6f;
    border: 1px solid #bfdbfe;
    display: grid;
    place-items: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.btn-close-modal {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    font-size: 1.3rem;
    color: #64748b;
    cursor: pointer;
    line-height: 1;
    display: grid;
    place-items: center;
    transition: all 0.2s ease;
}

.btn-close-modal:hover {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
    transform: rotate(90deg);
}

.modal-form-body {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.f-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.f-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 6px;
}

.f-control {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    font-family: inherit;
    font-size: 0.9rem;
    outline: none;
    background: #ffffff;
    color: #0f172a;
    box-sizing: border-box;
    transition: all 0.2s ease;
}

.f-control:focus {
    border-color: #1d4ed8;
    box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
    background: #ffffff;
}

.f-row {
    display: flex;
    gap: 14px;
}

/* صندوق سحب وإفلات الفيديو الملكي الكلاسيكي الفاخر */
.ed-video-dropzone {
    border: 2px dashed #93c5fd;
    background: linear-gradient(180deg, #f8fafc 0%, #eff6ff 100%);
    border-radius: 14px;
    padding: 26px 18px;
    text-align: center;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-sizing: border-box;
    position: relative;
}

.ed-video-dropzone:hover, .ed-video-dropzone.dragover {
    border-color: #0b3b6f;
    background: #e0f2fe;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(11, 59, 111, 0.1);
}

.dropzone-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.dropzone-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: #ffffff;
    border: 1.5px solid #bfdbfe;
    color: #0b3b6f;
    display: grid;
    place-items: center;
    font-size: 1.55rem;
    box-shadow: 0 4px 14px rgba(11, 59, 111, 0.08);
    transition: all 0.2s ease;
}

.ed-video-dropzone:hover .dropzone-icon {
    transform: scale(1.08);
    color: #1e40af;
    border-color: #93c5fd;
}

.dropzone-text strong {
    display: block;
    color: #0b3b6f;
    font-size: 0.96rem;
    font-weight: 800;
    margin-bottom: 4px;
}

.dropzone-text span {
    font-size: 0.8rem;
    color: #64748b;
    line-height: 1.5;
    max-width: 480px;
    display: block;
    margin: 0 auto;
}

.modal-foot {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 22px;
    padding-top: 18px;
    border-top: 1px solid #f1f5f9;
}

.btn-modal-cancel {
    padding: 11px 22px;
    border-radius: 10px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-modal-cancel:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.btn-modal-submit {
    padding: 11px 28px;
    border-radius: 10px;
    background: linear-gradient(135deg, #0b3b6f 0%, #1e40af 100%);
    color: #ffffff;
    border: 1px solid #1e40af;
    font-weight: 800;
    font-size: 0.92rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(11, 59, 111, 0.25);
    transition: all 0.2s ease;
}

.btn-modal-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(11, 59, 111, 0.35);
    background: linear-gradient(135deg, #072547 0%, #0b3b6f 100%);
}

@media (max-width: 768px) {
    .ed-teacher-header {
        flex-direction: column;
        align-items: stretch;
        gap: 14px;
    }
    .header-actions {
        width: 100%;
    }
    .ed-btn-upload {
        width: 100%;
        justify-content: center;
    }
    .ed-stats-strip {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    .ed-videos-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .ed-filter-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }
    .filter-pills {
        overflow-x: auto;
        flex-wrap: nowrap;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 4px;
    }
    .filter-chip {
        white-space: nowrap;
        flex-shrink: 0;
    }
    .modal-overlay {
        padding: 10px !important;
        align-items: flex-start !important;
    }
    .modal-card {
        width: 100% !important;
        max-width: 100% !important;
        max-height: calc(100dvh - 24px) !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        padding: 20px 16px;
        border-radius: 14px;
        box-sizing: border-box;
    }
    .f-row {
        flex-direction: column;
        gap: 10px;
    }
    .modal-foot {
        flex-direction: column;
        gap: 8px;
    }
    .btn-modal-cancel, .btn-modal-submit {
        width: 100%;
        justify-content: center;
        box-sizing: border-box;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
function openUploadVideoModal() {
    document.getElementById('uploadVideoModal').style.display = 'flex';
}

function closeUploadVideoModal() {
    const chunkWrap = document.getElementById('chunkProgressWrap');
    if (chunkWrap && chunkWrap.style.display !== 'none' && !chunkWrap.dataset.completed) {
        if (!confirm('{{ __("هناك عملية رفع فيديو جارية حالياً، هل أنت متأكد من الإلغاء؟") }}')) {
            return;
        }
    }
    document.getElementById('uploadVideoModal').style.display = 'none';
}

function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function handleVideoFileSelection(input) {
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];
    const allowed = ['mp4', 'webm', 'ogg', 'mov', 'm4v'];
    const ext = file.name.split('.').pop().toLowerCase();
    
    if (!allowed.includes(ext)) {
        Swal.fire({
            icon: 'error',
            title: '{{ __("صيغة غير مدعومة") }}',
            text: '{{ __("يرجى اختيار ملف فيديو بصيغة MP4 أو WebM أو MOV.") }}',
            confirmButtonText: '{{ __("حسناً") }}'
        });
        input.value = '';
        return;
    }

    const titleEl = document.getElementById('dropzoneTitle');
    const subEl = document.getElementById('dropzoneSubtitle');
    const iconEl = document.querySelector('#videoDropzone .dropzone-icon');

    if (titleEl) {
        titleEl.textContent = file.name;
        titleEl.style.color = '#15803d';
    }
    if (subEl) {
        subEl.textContent = `✅ {{ __("الملف جاهز للرفع:") }} ${formatBytes(file.size)} (${ext.toUpperCase()})`;
        subEl.style.color = '#166534';
        subEl.style.fontWeight = '700';
    }
    if (iconEl) {
        iconEl.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #16a34a;"></i>';
        iconEl.style.borderColor = '#86efac';
        iconEl.style.background = '#f0fdf4';
    }
}

// دعم السحب والإفلات
const dropArea = document.getElementById('videoDropzone');
if (dropArea) {
    ['dragenter', 'dragover'].forEach(name => {
        dropArea.addEventListener(name, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropArea.classList.add('dragover');
        }, false);
    });
    ['dragleave', 'drop'].forEach(name => {
        dropArea.addEventListener(name, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropArea.classList.remove('dragover');
        }, false);
    });
    dropArea.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length > 0) {
            const fileInput = document.getElementById('videoFileInput');
            fileInput.files = files;
            handleVideoFileSelection(fileInput);
        }
    }, false);
}

// دالة تنسيق السرعة ومعدل النقل
function formatUploadSpeed(bytesPerSec) {
    if (bytesPerSec <= 0) return '-- MB/s';
    const mbps = bytesPerSec / (1024 * 1024);
    if (mbps >= 1) return mbps.toFixed(1) + ' MB/s';
    return (bytesPerSec / 1024).toFixed(0) + ' KB/s';
}

// دالة حساب وتنسيق الوقت المتبقي
function formatEtaTime(seconds) {
    if (!seconds || seconds <= 0 || !isFinite(seconds)) return '--';
    if (seconds < 60) return seconds + ' {{ __("ثانية") }}';
    const minutes = Math.floor(seconds / 60);
    const remSec = seconds % 60;
    if (minutes < 60) {
        return `${minutes} {{ __("د") }} ${remSec > 0 ? remSec + ' {{ __("ث") }}' : ''}`;
    }
    const hours = Math.floor(minutes / 60);
    const remMin = minutes % 60;
    return `${hours} {{ __("ساعة") }} ${remMin > 0 ? remMin + ' {{ __("د") }}' : ''}`;
}

// نظام الرفع فائق السرعة والموثوقية للملفات الضخمة بالجيجابايت (Gigabyte Chunked & Resumable Upload)
async function submitVideoForm(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitVideo');
    const btnCancel = document.getElementById('btnCancelUpload');
    const originalText = btn.innerHTML;
    const form = document.getElementById('uploadVideoForm');
    const fileInput = document.getElementById('videoFileInput');

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: '{{ __("يرجى اختيار ملف الفيديو") }}',
            text: '{{ __("قم باختيار ملف الفيديو (MP4 / WebM) لرفعه للطلبة.") }}',
            confirmButtonText: '{{ __("حسناً") }}',
            confirmButtonColor: '#1e3a8a'
        });
        return;
    }

    const file = fileInput.files[0];
    // ضبط حجم القطعة: 5 ميغابايت للملفات الضخمة أكبر من 1GB لتقليل عدد الطلبات وتسريع الرفع، و4 ميغابايت لغيرها
    const CHUNK_SIZE = file.size > (1024 * 1024 * 1024) ? (5 * 1024 * 1024) : (4 * 1024 * 1024);
    const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
    
    // بصمة ثابتة وفريدة للملف تتيح الاستئناف التلقائي في حال انقطاع الاتصال أو إعادة الرفع
    const cleanName = encodeURIComponent(file.name).replace(/[^a-zA-Z0-9]/g, '').slice(0, 24);
    const fileId = 'vid_' + cleanName + '_' + file.size;

    // عناصر واجهة التقدم
    const progressWrap = document.getElementById('chunkProgressWrap');
    const progressBar = document.getElementById('chunkProgressBar');
    const progressPct = document.getElementById('chunkProgressPct');
    const progressStatus = document.getElementById('chunkProgressStatus');
    const fileMeta = document.getElementById('chunkFileMeta');
    const speedMeta = document.getElementById('chunkSpeedMeta');
    const etaMeta = document.getElementById('chunkEtaMeta');
    const partMeta = document.getElementById('chunkPartMeta');

    progressWrap.style.display = 'block';
    progressWrap.dataset.completed = '';
    btn.disabled = true;
    if (btnCancel) btnCancel.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __("جاري فحص وتجهيز الرفع بالجيجاوات...") }}';

    // حماية من إغلاق الصفحة بالخطأ أثناء رفع ملفات الجيجابايت
    const preventTabClose = (ev) => {
        ev.preventDefault();
        ev.returnValue = '{{ __("جاري رفع فيديو ضخم بالخلفية، هل أنت متأكد من مغادرة الصفحة وإلغاء الرفع؟") }}';
    };
    window.addEventListener('beforeunload', preventTabClose);

    let uploadedPath = null;
    let formattedSize = null;

    try {
        const chunkUploadUrl = "{{ Route::has('educational_contents.upload_chunk') ? route('educational_contents.upload_chunk') : url('/educational-contents/upload-chunk') }}";
        const checkStatusUrl = "{{ Route::has('educational_contents.check_chunk_status') ? route('educational_contents.check_chunk_status') : url('/educational-contents/check-chunk-status') }}";

        // فحص الأجزاء التي تم رفعها مسبقاً لاستئناف الرفع فوراً (Resumable Upload)
        let alreadyUploaded = new Set();
        try {
            const statusRes = await axios.post(checkStatusUrl, {
                file_id: fileId,
                total_chunks: totalChunks,
                _token: '{{ csrf_token() }}'
            });
            if (statusRes.data && statusRes.data.uploaded_chunks && statusRes.data.uploaded_chunks.length > 0) {
                alreadyUploaded = new Set(statusRes.data.uploaded_chunks);
                if (alreadyUploaded.size > 0 && alreadyUploaded.size < totalChunks) {
                    progressStatus.innerHTML = `<i class="fa-solid fa-bolt" style="color: #d97706;"></i> {{ __('تم العثور على أجزاء مرفوعة سابقاً') }} (${alreadyUploaded.size} {{ __('جزء') }})! {{ __('جاري استئناف الرفع فوراً ⚡') }}`;
                }
            }
        } catch (e) {
            console.warn('Check chunk status skipped:', e);
        }

        // متغيرات تتبع السرعة ومعدل النقل والوقت المتبقي
        const startTime = Date.now();
        let bytesUploadedThisSession = 0;

        // دالة الرفع المرنة مع 5 محاولات تلقائية عند انقطاع الإنترنت أو تذبذب الشبكة
        async function uploadChunkWithRetry(formData, chunkIdx, maxRetries = 5) {
            for (let attempt = 1; attempt <= maxRetries; attempt++) {
                try {
                    return await axios.post(chunkUploadUrl, formData, {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'multipart/form-data',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        timeout: 120000 // مهلة دقيقتين لكل جزء
                    });
                } catch (err) {
                    if (attempt === maxRetries) throw err;
                    const delay = attempt * 2000;
                    progressStatus.innerHTML = `<i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b;"></i> {{ __("تذبذب في الشبكة بالجزء") }} ${chunkIdx + 1}. {{ __("إعادة المحاولة تلقائياً") }} (${attempt}/${maxRetries})...`;
                    await new Promise(r => setTimeout(r, delay));
                }
            }
        }

        for (let i = 0; i < totalChunks; i++) {
            const start = i * CHUNK_SIZE;
            const end = Math.min(file.size, start + CHUNK_SIZE);
            const chunkLength = end - start;

            // إذا كان هذا الجزء مرفوعاً مسبقاً على السيرفر، نتخطاه فوراً لتوفير الوقت والبيانات
            if (alreadyUploaded.has(i) && (i < totalChunks - 1)) {
                const currentPct = Math.round(((i + 1) / totalChunks) * 100);
                progressBar.style.width = currentPct + '%';
                progressPct.textContent = currentPct + '%';
                fileMeta.textContent = `${formatBytes(end)} / ${formatBytes(file.size)}`;
                partMeta.textContent = `{{ __("الجزء") }} ${i + 1} {{ __("من") }} ${totalChunks} ({{ __("مستأنف") }})`;
                continue;
            }

            const chunkBlob = file.slice(start, end);
            const chunkFormData = new FormData();
            chunkFormData.append('file_id', fileId);
            chunkFormData.append('chunk_index', i);
            chunkFormData.append('total_chunks', totalChunks);
            chunkFormData.append('file_name', file.name);
            chunkFormData.append('chunk', chunkBlob, 'part_' + i);
            chunkFormData.append('_token', '{{ csrf_token() }}');

            const chunkRes = await uploadChunkWithRetry(chunkFormData, i);
            bytesUploadedThisSession += chunkLength;

            // حساب السرعة والوقت المتبقي بدقة
            const elapsedSec = (Date.now() - startTime) / 1000;
            const speedBps = elapsedSec > 0 ? (bytesUploadedThisSession / elapsedSec) : 0;
            const remainingBytes = file.size - end;
            const etaSec = speedBps > 0 ? Math.round(remainingBytes / speedBps) : 0;

            const currentPct = Math.round(((i + 1) / totalChunks) * 100);
            progressBar.style.width = currentPct + '%';
            progressPct.textContent = currentPct + '%';
            progressStatus.innerHTML = `<i class="fa-solid fa-cloud-arrow-up fa-fade"></i> {{ __("جاري رفع ومعالجة الفيديو الضخم:") }} ${currentPct}%`;
            fileMeta.textContent = `${formatBytes(end)} / ${formatBytes(file.size)}`;
            if (speedMeta) speedMeta.innerHTML = `<i class="fa-solid fa-gauge-high"></i> ${formatUploadSpeed(speedBps)}`;
            if (etaMeta) etaMeta.innerHTML = `<i class="fa-solid fa-clock"></i> ${formatEtaTime(etaSec)}`;
            partMeta.textContent = `{{ __("الجزء") }} ${i + 1} {{ __("من") }} ${totalChunks}`;

            if (chunkRes.data && chunkRes.data.done) {
                uploadedPath = chunkRes.data.uploaded_video_path;
                formattedSize = chunkRes.data.formatted_size;
            }
        }

        if (!uploadedPath) {
            throw new Error('{{ __("لم يتم استلام مسار الفيديو النهائي من السيرفر.") }}');
        }

        // إرسال بيانات الدرس النهائية وحفظه بالمنصة
        progressStatus.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #10b981;"></i> {{ __("اكتمل دمج وحفظ الفيديو بالجيجاوات بنجاح! جاري النشر...") }}';
        if (etaMeta) etaMeta.innerHTML = '<i class="fa-solid fa-check"></i> {{ __("مكتمل") }}';

        const storeUrl = "{{ auth()->user()->role === 'admin' ? route('admin.educational_contents.store') : route('teacher.educational_contents.store') }}";
        const contentFormData = new FormData(form);
        // استبدال حقل الملف المباشر بالمسار المرفوع لتفادي إعادة إرساله
        contentFormData.delete('video_file');
        contentFormData.append('uploaded_video_path', uploadedPath);
        if (formattedSize) {
            contentFormData.append('formatted_size', formattedSize);
        }

        const saveRes = await axios.post(storeUrl, contentFormData, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'multipart/form-data'
            }
        });

        window.removeEventListener('beforeunload', preventTabClose);
        progressWrap.dataset.completed = '1';
        Swal.fire({
            icon: 'success',
            title: saveRes.data.title || '{{ __("تم رفع ونشر درس الفيديو بنجاح 🎉") }}',
            text: '{{ __("أصبح الفيديو متاحاً لجميع الطلبة ومفعلاً للبث والمشاهدة والتحميل أوفلاين!") }}',
            confirmButtonText: '{{ __("حسناً") }}',
            confirmButtonColor: '#1e3a8a'
        }).then(() => location.reload());

    } catch (err) {
        window.removeEventListener('beforeunload', preventTabClose);
        btn.disabled = false;
        if (btnCancel) btnCancel.disabled = false;
        btn.innerHTML = originalText;

        let msg = '{{ __("حدث خطأ أثناء رفع ملف الفيديو أو حفظ الدرس") }}';
        if (err.response && err.response.data) {
            if (err.response.data.title) {
                msg = err.response.data.title;
            } else if (err.response.data.message) {
                msg = err.response.data.message;
            } else if (err.response.data.error) {
                msg = err.response.data.error;
            } else if (err.response.data.errors) {
                msg = Object.values(err.response.data.errors).flat().join('<br>');
            }
        } else if (err.message) {
            msg = err.message;
        }

        progressStatus.innerHTML = `<i class="fa-solid fa-circle-xmark" style="color: #ef4444;"></i> ${msg}`;
        Swal.fire({
            icon: 'error',
            title: '{{ __("خطأ في رفع الفيديو") }}',
            html: msg + '<br><small style="color: #64748b; display: block; margin-top: 8px;">{{ __("ملاحظة: يمكنك إعادة الضغط على زر الرفع وسيتم استئناف الأجزاء المتبقية تلقائياً دون إعادة رفع الأجزاء السابقة.") }}</small>',
            confirmButtonText: '{{ __("حسناً") }}',
            confirmButtonColor: '#ef4444'
        });
    }
}

async function toggleVisibility(id, btn) {
    const toggleUrl = "{{ auth()->user()->role === 'admin' ? url('admin/visibility/toggle') : url('teacher/visibility/toggle') }}/" + id;
    try {
        const res = await axios.post(toggleUrl, { _token: '{{ csrf_token() }}' });
        if (res.data.success) {
            const isVis = res.data.is_visible;
            btn.className = 'visibility-toggle-btn ' + (isVis ? 'is-visible' : 'is-hidden');
            btn.innerHTML = `<i class="fa-solid ${isVis ? 'fa-eye' : 'fa-eye-slash'}"></i> <span>${isVis ? '{{ __("متاح للطلبة") }}' : '{{ __("محجوب") }}'}</span>`;
            Swal.fire({ icon: 'success', title: res.data.message, timer: 1000, showConfirmButton: false });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: '{{ __("تعذر تعديل حالة الظهور.") }}' });
    }
}

async function deleteVideoItem(id) {
    const deleteUrl = "{{ auth()->user()->role === 'admin' ? url('admin/educational-contents') : url('teacher/educational_contents') }}/" + id;
    Swal.fire({
        title: '{{ __("حذف هذا الشرح المرئي؟") }}',
        text: '{{ __("هل أنت متأكد من حذف هذا الدرس؟ لن يتمكن الطلاب من مشاهدته بعد الحذف.") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: '{{ __("نعم، احذف") }}',
        cancelButtonText: '{{ __("إلغاء") }}'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete(deleteUrl, {
                    data: { _token: '{{ csrf_token() }}' }
                });
                Swal.fire({ icon: 'success', title: '{{ __("تم حذف الفيديو بنجاح") }}', timer: 1200, showConfirmButton: false })
                    .then(() => location.reload());
            } catch (e) {
                Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: '{{ __("تعذر حذف المحتوى.") }}' });
            }
        }
    });
}

// نظام مشغل الفيديو الآمن الداخلي المخصص للمنصة (منع الوصول إلى YouTube كلياً)
let teacherPlayers = {};
let teacherIntervals = {};

function onYouTubeIframeAPIReady() {
    document.querySelectorAll('iframe[id^="iframe_teacher_"]').forEach(iframe => {
        initTeacherPlayer(iframe.id);
    });
}

function initTeacherPlayer(iframeId) {
    const vidId = iframeId.replace('iframe_teacher_', '');
    if (teacherPlayers[vidId]) return teacherPlayers[vidId];
    if (window.YT && window.YT.Player) {
        try {
            teacherPlayers[vidId] = new YT.Player(iframeId, {
                events: {
                    'onStateChange': function(e) {
                        handleTeacherStateChange(vidId, e.data);
                    }
                }
            });
            return teacherPlayers[vidId];
        } catch(e) {}
    }
    return null;
}

function handleTeacherStateChange(vidId, state) {
    const centerBtn = document.getElementById(`center_play_${vidId}`);
    const barBtn = document.getElementById(`bar_btn_${vidId}`);
    if (state === 1) { // مشتغل
        if (centerBtn) centerBtn.style.opacity = '0';
        if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
        startTeacherProgressTracking(vidId);
    } else { // متوقف
        if (centerBtn) {
            centerBtn.style.opacity = '1';
            centerBtn.innerHTML = (state === 0) ? '<i class="fa-solid fa-rotate-right"></i>' : '<i class="fa-solid fa-play"></i>';
        }
        if (barBtn) barBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
        stopTeacherProgressTracking(vidId);
    }
}

function toggleTeacherPlayback(vidId) {
    const yt = teacherPlayers[vidId] || initTeacherPlayer(`iframe_teacher_${vidId}`);
    if (yt && typeof yt.getPlayerState === 'function') {
        const state = yt.getPlayerState();
        if (state === 1) {
            yt.pauseVideo();
        } else {
            yt.playVideo();
        }
        return;
    }
    const iframe = document.getElementById(`iframe_teacher_${vidId}`);
    if (iframe && iframe.contentWindow) {
        const centerBtn = document.getElementById(`center_play_${vidId}`);
        const isPlaying = centerBtn && centerBtn.style.opacity === '0';
        iframe.contentWindow.postMessage(JSON.stringify({
            event: 'command',
            func: isPlaying ? 'pauseVideo' : 'playVideo',
            args: []
        }), '*');
        if (centerBtn) centerBtn.style.opacity = isPlaying ? '1' : '0';
        const barBtn = document.getElementById(`bar_btn_${vidId}`);
        if (barBtn) barBtn.innerHTML = isPlaying ? '<i class="fa-solid fa-play"></i>' : '<i class="fa-solid fa-pause"></i>';
        if (!isPlaying) startTeacherProgressTracking(vidId); else stopTeacherProgressTracking(vidId);
    }
}

function formatDuration(sec) {
    if (!sec || isNaN(sec)) return '00:00';
    sec = Math.floor(sec);
    const m = Math.floor(sec / 60);
    const s = sec % 60;
    return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
}

function startTeacherProgressTracking(vidId) {
    stopTeacherProgressTracking(vidId);
    teacherIntervals[vidId] = setInterval(() => {
        const yt = teacherPlayers[vidId];
        if (yt && typeof yt.getCurrentTime === 'function') {
            const cur = yt.getCurrentTime() || 0;
            const dur = yt.getDuration() || 0;
            const timeTxt = document.getElementById(`time_txt_${vidId}`);
            const seekRange = document.getElementById(`seek_range_${vidId}`);
            if (timeTxt) timeTxt.textContent = `${formatDuration(cur)} / ${formatDuration(dur)}`;
            if (seekRange && dur > 0) {
                seekRange.value = (cur / dur) * 100;
            }
        }
    }, 500);
}

function stopTeacherProgressTracking(vidId) {
    if (teacherIntervals[vidId]) {
        clearInterval(teacherIntervals[vidId]);
        delete teacherIntervals[vidId];
    }
}

function seekTeacherPlayback(vidId, pct) {
    const yt = teacherPlayers[vidId] || initTeacherPlayer(`iframe_teacher_${vidId}`);
    if (yt && typeof yt.getDuration === 'function') {
        const dur = yt.getDuration() || 0;
        const targetSec = (pct / 100) * dur;
        yt.seekTo(targetSec, true);
    } else {
        const iframe = document.getElementById(`iframe_teacher_${vidId}`);
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage(JSON.stringify({
                event: 'command',
                func: 'seekTo',
                args: [parseFloat(pct), true]
            }), '*');
        }
    }
}

function toggleTeacherFullscreen(containerId) {
    const el = document.getElementById(containerId);
    if (!el) return;
    if (document.fullscreenElement) {
        document.exitFullscreen();
    } else {
        if (el.requestFullscreen) {
            el.requestFullscreen();
        } else if (el.webkitRequestFullscreen) {
            el.webkitRequestFullscreen();
        } else if (el.mozRequestFullScreen) {
            el.mozRequestFullScreen();
        }
    }
}

// تعطيل القائمة المنبثقة بالزر الأيمن لمنع أي وصول لروابط يوتيوب
document.addEventListener('contextmenu', function(e) {
    if (e.target.closest('.video-frame-wrap')) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
}, true);
</script>
<script src="https://www.youtube.com/iframe_api"></script>
@endsection
