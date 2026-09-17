@extends('layouts.app')

@section('title', __('Video Lessons & Lectures Management') . ' | ' . config('app.name'))

@section('content')
<div class="ed-teacher-videos-container">

    <!-- الهيدر الأكاديمي -->
    <header class="ed-teacher-header">
        <div>
            <div class="ed-teacher-breadcrumbs">
                <a href="{{ route('teacher.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Teacher Portal') }}</a>
                <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" style="font-size: 0.7rem;"></i>
                <span class="active">{{ __('Video Content') }}</span>
            </div>
            <h1>
                <i class="fa-solid fa-video" style="color: #1e3a8a;"></i> {{ __('Manage & Upload Video Lessons') }}
            </h1>
            <p>
                {{ __('Upload recorded lessons, Tawjihi explanations, and YouTube streams with secure cloud processing.') }}
            </p>
        </div>

        <button type="button" onclick="openUploadVideoModal()" class="ed-btn-upload">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            <span>{{ __('Upload New Lesson / Video') }}</span>
        </button>
    </header>

    <!-- شبكة الفيديوهات -->
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
                                {{ $vid->subject?->name_ar ?? __('General') }}
                            </span>
                            <span class="order-badge">{{ __('Order:') }} #{{ $vid->order }}</span>
                        </div>
                        <h3 class="video-title">{{ $vid->title }}</h3>
                        <p class="video-channel">{{ $vid->channel_name ?? config('app.name') }}</p>
                    </div>
                </div>

                <div class="video-card-footer">
                    <span class="visibility-status {{ $vid->is_visible ? 'visible' : 'hidden' }}">
                        <i class="fa-solid {{ $vid->is_visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                        {{ $vid->is_visible ? __('Available for students') : __('Temporarily hidden') }}
                    </span>
                    <div class="action-btns">
                        <a href="{{ route('teacher.educational_contents.edit', $vid->id) }}" class="btn-edit">
                            <i class="fa-solid fa-pen"></i> {{ __('Edit') }}
                        </a>
                        <button type="button" onclick="deleteContentItem({{ $vid->id }})" class="btn-delete" title="{{ __('Delete') }}">
                            <i class="fa-solid fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="ed-empty-videos">
                <i class="fa-solid fa-film"></i>
                <h3>{{ __('No videos registered currently') }}</h3>
                <p>{{ __('Click "Upload New Video" to add your first recorded lesson for your students.') }}</p>
                <button type="button" onclick="openUploadVideoModal()" class="ed-btn-upload" style="margin: 0 auto;">
                    {{ __('Upload New Lesson / Video') }}
                </button>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px;">
        {{ $videos->links() }}
    </div>

</div>

<!-- Modal رفع فيديو جديد -->
<div id="uploadVideoModal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; max-width: 560px; width: 100%; padding: 26px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-video" style="color: #1e3a8a;"></i> {{ __('Upload New Video Lesson') }}
            </h3>
            <button type="button" onclick="closeUploadVideoModal()" style="background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer;">✕</button>
        </div>

        <form id="uploadVideoForm" onsubmit="submitVideoForm(event)">
            @csrf
            <input type="hidden" name="type" value="video">

            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">{{ __('Academic Subject *') }}</label>
                    <select name="subject_id" required style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}" {{ $subjectId == $sub->id ? 'selected' : '' }}>{{ $sub->name_ar }} ({{ $sub->stage?->label_ar ?? __('High School') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">{{ __('Lesson Title *') }}</label>
                    <input type="text" name="title" required placeholder="{{ __('e.g. Explaining Coulombs Law and Exercises') }}" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">{{ __('Lesson Order *') }}</label>
                        <input type="number" name="order" value="{{ $videos->count() + 1 }}" required min="1" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                    </div>
                    <div>
                        <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">{{ __('Unit / Topic Name') }}</label>
                        <input type="text" name="channel_name" placeholder="{{ __('e.g. Unit 1') }}" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                    </div>
                </div>

                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #0f172a; display: block; margin-bottom: 6px;">
                        <i class="fa-brands fa-youtube" style="color: #ef4444;"></i> {{ __('YouTube Lesson URL *') }}
                    </label>
                    <input type="url" name="video_url" id="videoUrlInput" required placeholder="https://www.youtube.com/watch?v=..." oninput="previewYoutube(this.value)" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none; direction: ltr; text-align: left; background: #ffffff; color: #0f172a;">
                    <small style="color: #64748b; font-size: 0.74rem; display: block; margin-top: 4px;">
                        {{ __('Supports regular YouTube URLs, youtu.be, and Shorts.') }}
                    </small>
                </div>

                <!-- معاينة فورية للفيديو -->
                <div id="ytPreviewContainer" style="display: none; margin-top: 4px;">
                    <label style="font-size: 0.78rem; font-weight: 700; color: #64748b; display: block; margin-bottom: 4px;">{{ __('Live Video Preview:') }}</label>
                    <div style="position: relative; padding-top: 56.25%; border-radius: 12px; overflow: hidden; background: #000;">
                        <iframe id="ytPreviewFrame" src="" style="position: absolute; inset: 0; width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="closeUploadVideoModal()" style="padding: 10px 20px; border-radius: 10px; background: #f1f5f9; color: #64748b; border: none; font-weight: 700; cursor: pointer;">{{ __('Cancel') }}</button>
                <button type="submit" id="btnSubmitVideo" style="padding: 10px 24px; border-radius: 10px; background: #1e3a8a; color: white; border: none; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('Save and Publish Lesson') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.ed-teacher-videos-container {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    padding: 0 0 60px;
    box-sizing: border-box;
}

.ed-teacher-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
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

.ed-teacher-breadcrumbs .active {
    color: #1e3a8a;
    font-weight: 700;
}

.ed-teacher-header h1 {
    margin: 0 0 6px;
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
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
    border-radius: 12px;
    font-weight: 800;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(30, 58, 138, 0.25);
    transition: all 0.2s;
}

.ed-btn-upload:hover {
    background: #172554;
}

.ed-videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 22px;
}

.ed-video-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s;
}

.ed-video-card:hover {
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
}

.video-frame-wrap {
    position: relative;
    padding-top: 56.25%;
    background: #000000;
}

.video-info-box {
    padding: 16px 20px;
}

.video-meta-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.subject-badge {
    background: #eff6ff;
    color: #1e3a8a;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
}

.order-badge {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 600;
}

.video-title {
    margin: 0 0 8px;
    font-size: 1.02rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.4;
}

.video-channel {
    margin: 0;
    font-size: 0.78rem;
    color: #64748b;
}

.video-card-footer {
    padding: 12px 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.visibility-status {
    font-size: 0.78rem;
    font-weight: 700;
}

.visibility-status.visible { color: #16a34a; }
.visibility-status.hidden { color: #dc2626; }

.action-btns {
    display: flex;
    gap: 8px;
}

.btn-edit {
    padding: 6px 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    color: #475569;
    text-decoration: none;
    font-size: 0.78rem;
    font-weight: 700;
}

.btn-edit:hover {
    border-color: #1e3a8a;
    color: #1e3a8a;
}

.btn-delete {
    padding: 6px 10px;
    background: #fee2e2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    color: #dc2626;
    cursor: pointer;
    font-size: 0.78rem;
}

.ed-empty-videos {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 20px;
    border: 2px dashed #cbd5e1;
    color: #94a3b8;
}

.ed-empty-videos i {
    font-size: 3rem;
    margin-bottom: 12px;
    display: block;
    opacity: 0.4;
}

.ed-empty-videos h3 {
    margin: 0 0 6px;
    font-weight: 800;
    color: #475569;
}

.ed-empty-videos p {
    margin: 0 0 20px;
    font-size: 0.88rem;
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
    if (!url) return null;
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|shorts\/)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
}

function previewYoutube(url) {
    const videoId = parseYouTubeId(url.trim());
    const container = document.getElementById('ytPreviewContainer');
    const frame = document.getElementById('ytPreviewFrame');
    if (videoId) {
        frame.src = `https://www.youtube.com/embed/${videoId}?rel=0`;
        container.style.display = 'block';
    } else {
        frame.src = '';
        container.style.display = 'none';
    }
}

async function submitVideoForm(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitVideo');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __("Uploading...") }}';

    const form = document.getElementById('uploadVideoForm');
    const formData = new FormData(form);

    try {
        const res = await axios.post("{{ route('teacher.educational_contents.store') }}", formData);
        Swal.fire({
            icon: 'success',
            title: res.data.title || '{{ __("Saved successfully") }}',
            confirmButtonText: '{{ __("OK") }}',
            confirmButtonColor: '#1e3a8a'
        }).then(() => location.reload());
    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = originalText;
        const msg = err.response?.data?.title || err.response?.data?.message || '{{ __("Error uploading content") }}';
        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: msg });
    }
}

async function deleteContentItem(id) {
    Swal.fire({
        title: '{{ __("Delete Video Lesson?") }}',
        text: '{{ __("Are you sure you want to delete this video?") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: '{{ __("Yes, delete") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete(`{{ url('teacher/educational_contents') }}/${id}`, {
                    data: { _token: '{{ csrf_token() }}' }
                });
                Swal.fire({ icon: 'success', title: '{{ __("Deleted successfully") }}', timer: 1200, showConfirmButton: false })
                    .then(() => location.reload());
            } catch (e) {
                Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Could not delete content.") }}' });
            }
        }
    });
}
</script>
@endsection
