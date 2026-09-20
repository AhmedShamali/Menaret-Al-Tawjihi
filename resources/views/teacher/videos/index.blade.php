@extends('layouts.app')

@section('title', __('إدارة ورفع الفيديوهات والشروحات') . ' | ' . config('app.name', 'منارة التوجيهي'))

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
                {{ __('رفع وإدارة حصص وشروحات المنهاج الفلسطيني عبر روابط YouTube السريعة، مع تنظيم ترتيب الدروس والتحكم بظهورها للطلبة فورياً.') }}
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
    @if(auth()->user()->role === 'admin' || !auth()->user()->subject_id)
        <div class="ed-filter-bar">
            <div class="filter-label">
                <i class="fa-solid fa-filter"></i>
                <span>{{ __('تصفية حسب المادة الدراسية:') }}</span>
            </div>
            <div class="filter-pills">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.videos') : route('teacher.videos') }}" class="filter-chip {{ empty(request('subject_id')) ? 'active' : '' }}">
                    {{ __('جميع المواد') }}
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
                $embedUrl = $vid->youtube_embed_url ?? (filter_var($vid->url_path, FILTER_VALIDATE_URL) ? $vid->url_path : asset('storage/' . $vid->url_path));
            @endphp
            <div class="ed-video-card">
                <div>
                    <div class="video-frame-wrap">
                        @if($vid->youtube_id)
                            <iframe src="{{ $vid->youtube_embed_url }}" style="position: absolute; inset: 0; width: 100%; height: 100%; border: none;" allowfullscreen loading="lazy"></iframe>
                        @else
                            <iframe src="{{ $embedUrl }}" style="position: absolute; inset: 0; width: 100%; height: 100%; border: none;" allowfullscreen loading="lazy"></iframe>
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
                            <i class="fa-brands fa-youtube" style="color: #ef4444;"></i>
                            <span>{{ $vid->channel_name ?? config('app.name', 'منارة التوجيهي') }}</span>
                        </p>
                    </div>
                </div>

                <div class="video-card-footer">
                    <button type="button" onclick="toggleVisibility({{ $vid->id }}, this)" class="visibility-toggle-btn {{ $vid->is_visible ? 'is-visible' : 'is-hidden' }}" title="{{ __('انقر لتبديل الظهور للطلبة') }}">
                        <i class="fa-solid {{ $vid->is_visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                        <span>{{ $vid->is_visible ? __('متاح للطلبة') : __('محجوب') }}</span>
                    </button>

                    <div class="action-btns">
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
                <i class="fa-solid fa-video text-primary"></i>
                <span>{{ __('إضافة درس أو شرح فيديو جديد (YouTube)') }}</span>
            </h3>
            <button type="button" onclick="closeUploadVideoModal()" class="btn-close-modal">&times;</button>
        </div>

        <form id="uploadVideoForm" onsubmit="submitVideoForm(event)">
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

                <!-- اختيار نوع المصدر: يوتيوب أو ملف فيديو مباشر -->
                <div class="f-group">
                    <label class="f-label">{{ __('طريقة إضافة الفيديو *') }}</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <button type="button" id="tabSourceYt" onclick="switchVideoSourceType('youtube')" style="padding: 10px; border-radius: 8px; border: 2px solid #1e3a8a; background: #eff6ff; color: #1e3a8a; font-weight: 800; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i class="fa-brands fa-youtube" style="color: #ef4444; font-size: 1.1rem;"></i>
                            <span>{{ __('رابط YouTube') }}</span>
                        </button>
                        <button type="button" id="tabSourceFile" onclick="switchVideoSourceType('file')" style="padding: 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #64748b; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i class="fa-solid fa-cloud-arrow-up" style="color: #2563eb; font-size: 1.1rem;"></i>
                            <span>{{ __('رفع ملف للمنصة (MP4)') }}</span>
                        </button>
                    </div>
                </div>

                <!-- حقل رابط YouTube -->
                <div class="f-group" id="groupVideoUrl">
                    <label class="f-label">
                        <i class="fa-brands fa-youtube" style="color: #ef4444;"></i>
                        <span>{{ __('رابط فيديو YouTube *') }}</span>
                    </label>
                    <input type="url" name="video_url" id="videoUrlInput" placeholder="https://www.youtube.com/watch?v=... أو https://youtu.be/..." oninput="previewYoutube(this.value)" class="f-control font-mono text-ltr">
                    <small class="f-hint">{{ __('يدعم كافة صيغ روابط YouTube (الروابط الكاملة، الروابط المختصرة youtu.be، ومقاطع Shorts).') }}</small>
                </div>

                <!-- حقل رفع ملف فيديو مباشر للمنصة -->
                <div class="f-group" id="groupVideoFile" style="display: none;">
                    <label class="f-label">
                        <i class="fa-solid fa-file-video" style="color: #2563eb;"></i>
                        <span>{{ __('اختر ملف الفيديو من جهازك * (MP4 / WebM / MOV)') }}</span>
                    </label>
                    <input type="file" name="video_file" id="videoFileInput" accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-matroska" onchange="previewLocalVideo(this)" class="f-control">
                    <small class="f-hint">{{ __('يتم رفع وتخزين الفيديو مباشرة على المنصة مع دعم تنزيله وسرعات المشاهدة المتعددة (حتى 500 ميغابايت).') }}</small>
                </div>

                <!-- معاينة فورية للفيديو -->
                <div id="ytPreviewContainer" style="display: none; margin-top: 6px;">
                    <label class="f-label" style="color: #64748b;">{{ __('معاينة مشغل الفيديو:') }}</label>
                    <div style="position: relative; padding-top: 56.25%; border-radius: 12px; overflow: hidden; background: #000;">
                        <iframe id="ytPreviewFrame" src="" style="position: absolute; inset: 0; width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
                        <video id="localPreviewVideo" controls style="display: none; position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain;"></video>
                    </div>
                </div>

                <div class="f-row">
                    <div class="f-group" style="flex: 1;">
                        <label class="f-label">{{ __('اسم القناة / المصدر الأكاديمي') }}</label>
                        <input type="text" name="channel_name" value="{{ auth()->user()->name_ar ?? auth()->user()->name }}" placeholder="{{ __('مثال: م.أحمد شمالي') }}" class="f-control">
                    </div>

                    <div class="f-group" style="width: 130px;">
                        <label class="f-label">{{ __('ترتيب الدرس') }}</label>
                        <input type="number" name="order" value="1" min="1" class="f-control font-mono">
                    </div>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" onclick="closeUploadVideoModal()" class="btn-modal-cancel">{{ __('إلغاء') }}</button>
                <button type="submit" id="btnSubmitVideo" class="btn-modal-submit">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('حفظ ونشر الفيديو للطلبة') }}</span>
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

.modal-card {
    background: #ffffff;
    border-radius: 16px;
    max-width: 580px;
    width: 100%;
    padding: 26px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    animation: modalScale 0.2s ease-out;
}

@keyframes modalScale {
    from { opacity: 0; transform: scale(0.96); }
    to { opacity: 1; transform: scale(1); }
}

.modal-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 14px;
    margin-bottom: 18px;
}

.modal-head h3 {
    margin: 0;
    font-size: 1.18rem;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #94a3b8;
    cursor: pointer;
    line-height: 1;
}

.modal-form-body {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.f-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.f-label {
    font-size: 0.82rem;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.f-control {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    font-family: inherit;
    font-size: 0.88rem;
    outline: none;
    background: #ffffff;
    color: #0f172a;
    box-sizing: border-box;
    transition: border-color 0.15s;
}

.f-control:focus {
    border-color: #1e3a8a;
}

.f-hint {
    color: #64748b;
    font-size: 0.74rem;
}

.f-row {
    display: flex;
    gap: 12px;
}

.modal-foot {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
}

.btn-modal-cancel {
    padding: 10px 20px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #64748b;
    border: none;
    font-weight: 700;
    cursor: pointer;
}

.btn-modal-submit {
    padding: 10px 24px;
    border-radius: 8px;
    background: #1e3a8a;
    color: white;
    border: none;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.15s;
}

.btn-modal-submit:hover {
    background: #0f172a;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
function openUploadVideoModal() {
    document.getElementById('uploadVideoModal').style.display = 'flex';
}

function closeUploadVideoModal() {
    document.getElementById('uploadVideoModal').style.display = 'none';
}

function parseYouTubeId(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|shorts\/)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
}

let currentVideoSourceType = 'youtube';

function switchVideoSourceType(type) {
    currentVideoSourceType = type;
    const tabYt = document.getElementById('tabSourceYt');
    const tabFile = document.getElementById('tabSourceFile');
    const groupUrl = document.getElementById('groupVideoUrl');
    const groupFile = document.getElementById('groupVideoFile');
    const urlInput = document.getElementById('videoUrlInput');
    const fileInput = document.getElementById('videoFileInput');
    const container = document.getElementById('ytPreviewContainer');
    const ytFrame = document.getElementById('ytPreviewFrame');
    const localVideo = document.getElementById('localPreviewVideo');

    if (type === 'youtube') {
        tabYt.style.border = '2px solid #1e3a8a';
        tabYt.style.background = '#eff6ff';
        tabYt.style.color = '#1e3a8a';
        tabFile.style.border = '1.5px solid #cbd5e1';
        tabFile.style.background = '#ffffff';
        tabFile.style.color = '#64748b';

        groupUrl.style.display = 'flex';
        groupFile.style.display = 'none';
        fileInput.value = '';

        localVideo.style.display = 'none';
        localVideo.pause();
        previewYoutube(urlInput.value);
    } else {
        tabFile.style.border = '2px solid #1e3a8a';
        tabFile.style.background = '#eff6ff';
        tabFile.style.color = '#1e3a8a';
        tabYt.style.border = '1.5px solid #cbd5e1';
        tabYt.style.background = '#ffffff';
        tabYt.style.color = '#64748b';

        groupFile.style.display = 'flex';
        groupUrl.style.display = 'none';
        urlInput.value = '';

        ytFrame.style.display = 'none';
        ytFrame.src = '';
        if (fileInput.files && fileInput.files[0]) {
            previewLocalVideo(fileInput);
        } else {
            container.style.display = 'none';
        }
    }
}

function previewLocalVideo(input) {
    const file = input.files ? input.files[0] : null;
    const container = document.getElementById('ytPreviewContainer');
    const ytFrame = document.getElementById('ytPreviewFrame');
    const localVideo = document.getElementById('localPreviewVideo');

    if (file) {
        ytFrame.style.display = 'none';
        ytFrame.src = '';
        localVideo.src = URL.createObjectURL(file);
        localVideo.style.display = 'block';
        container.style.display = 'block';
    } else {
        localVideo.src = '';
        localVideo.style.display = 'none';
        container.style.display = 'none';
    }
}

function previewYoutube(url) {
    const videoId = parseYouTubeId(url.trim());
    const container = document.getElementById('ytPreviewContainer');
    const frame = document.getElementById('ytPreviewFrame');
    const localVideo = document.getElementById('localPreviewVideo');
    if (videoId) {
        localVideo.style.display = 'none';
        frame.style.display = 'block';
        frame.src = `https://www.youtube.com/embed/${videoId}?rel=0`;
        container.style.display = 'block';
    } else {
        frame.src = '';
        frame.style.display = 'none';
        if (currentVideoSourceType === 'youtube') {
            container.style.display = 'none';
        }
    }
}

async function submitVideoForm(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitVideo');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __("جاري الحفظ والرفع...") }}';

    const form = document.getElementById('uploadVideoForm');
    const formData = new FormData(form);

    const storeUrl = "{{ auth()->user()->role === 'admin' ? route('admin.educational_contents.store') : route('teacher.educational_contents.store') }}";

    try {
        const res = await axios.post(storeUrl, formData);
        Swal.fire({
            icon: 'success',
            title: res.data.title || '{{ __("تم حفظ ونشر درس الفيديو بنجاح 🎉") }}',
            confirmButtonText: '{{ __("حسناً") }}',
            confirmButtonColor: '#1e3a8a'
        }).then(() => location.reload());
    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = originalText;
        const msg = err.response?.data?.title || err.response?.data?.message || '{{ __("حدث خطأ أثناء حفظ الفيديو") }}';
        Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: msg });
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
</script>
@endsection
