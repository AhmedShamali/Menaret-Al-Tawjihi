@extends('layouts.app')

@section('title', 'إدارة ورفع الفيديوهات التعليمية | بوابة المعلم')

@section('content')
<div class="ed-teacher-videos-container" style="max-width: 1300px; margin: 0 auto; padding: 20px 16px 60px; direction: rtl; font-family: 'Alexandria', sans-serif;">

    <!-- الهيدر -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: #64748b; margin-bottom: 6px;">
                <a href="{{ route('teacher.dashboard') }}" style="color: inherit; text-decoration: none;">بوابة المعلم</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 0.7rem;"></i>
                <span style="color: #1d4ed8; font-weight: 700;">المحتوى المرئي والفيديوهات</span>
            </div>
            <h1 style="margin: 0 0 6px; font-size: 1.8rem; font-weight: 900; color: #0f172a;">
                <i class="fa-solid fa-video" style="color: #1d4ed8;"></i> إدارة ورفع الحصص المرئية
            </h1>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                رفع الدروس المصورة، شروحات التوجيهي، وبثوث اليوتيوب المباشرة مع المعالجة السحابية الآمنة.
            </p>
        </div>

        <button type="button" onclick="openUploadVideoModal()" style="background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%); color: white; border: none; padding: 12px 24px; border-radius: 14px; font-weight: 800; font-size: 0.92rem; display: flex; align-items: center; gap: 10px; cursor: pointer; box-shadow: 0 8px 20px rgba(29,78,216,0.3); transition: 0.2s;">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            <span>رفع درس / فيديو جديد</span>
        </button>
    </div>

    <!-- شبكة الفيديوهات -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;">
        @forelse($videos as $vid)
            @php
                $embedUrl = $vid->youtube_embed_url ?? (filter_var($vid->url_path, FILTER_VALIDATE_URL) ? $vid->url_path : asset('storage/' . $vid->url_path));
            @endphp
            <div style="background: var(--ed-surface); border-radius: 16px; border: 1px solid var(--ed-border); overflow: hidden; box-shadow: var(--ed-shadow-card); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="position: relative; padding-top: 56.25%; background: #000;">
                        @if($vid->youtube_id)
                            <iframe src="{{ $vid->youtube_embed_url }}" style="position: absolute; inset: 0; width: 100%; height: 100%; border: none;" allowfullscreen loading="lazy"></iframe>
                        @else
                            <iframe src="{{ $embedUrl }}" style="position: absolute; inset: 0; width: 100%; height: 100%; border: none;" allowfullscreen loading="lazy"></iframe>
                        @endif
                    </div>
                    <div style="padding: 16px 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="background: var(--ed-primary-soft); color: var(--ed-primary); padding: 3px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                                {{ $vid->subject?->name_ar ?? 'عام' }}
                            </span>
                            <span style="font-size: 0.75rem; color: var(--ed-text-dim); font-weight: 600;">الترتيب: #{{ $vid->order }}</span>
                        </div>
                        <h3 style="margin: 0 0 8px; font-size: 1.02rem; font-weight: 800; color: var(--ed-text-main); line-height: 1.4;">{{ $vid->title }}</h3>
                        <p style="margin: 0; font-size: 0.78rem; color: var(--ed-text-muted);">{{ $vid->channel_name ?? 'منارة التوجيهي' }}</p>
                    </div>
                </div>

                <div style="padding: 12px 20px; background: var(--ed-surface-alt); border-top: 1px solid var(--ed-border); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.78rem; font-weight: 700; color: {{ $vid->is_visible ? 'var(--ed-success)' : 'var(--ed-danger)' }};">
                        <i class="fa-solid {{ $vid->is_visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                        {{ $vid->is_visible ? 'متاح للطلبة' : 'محجوب مؤقتاً' }}
                    </span>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('teacher.educational_contents.edit', $vid->id) }}" style="padding: 6px 12px; background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: 8px; color: var(--ed-text-body); text-decoration: none; font-size: 0.78rem; font-weight: 700;">
                            <i class="fa-solid fa-pen"></i> تعديل
                        </a>
                        <button type="button" onclick="deleteContentItem({{ $vid->id }})" style="padding: 6px 10px; background: var(--ed-danger-soft); border: 1px solid #fecaca; border-radius: 8px; color: var(--ed-danger); cursor: pointer; font-size: 0.78rem;">
                            <i class="fa-solid fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: white; border-radius: 20px; border: 2px dashed #cbd5e1; color: #94a3b8;">
                <i class="fa-solid fa-film" style="font-size: 3rem; margin-bottom: 12px; display: block; opacity: 0.4;"></i>
                <h3 style="margin: 0 0 6px; font-weight: 800; color: #475569;">لا توجد فيديوهات مسجلة حالياً</h3>
                <p style="margin: 0 0 20px; font-size: 0.88rem;">اضغط على زر "رفع فيديو جديد" لإضافة أول درس مصور لطلبتك.</p>
                <button type="button" onclick="openUploadVideoModal()" style="background: #1d4ed8; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer;">
                    رفع فيديو جديد الآن
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
    <div style="background: white; border-radius: 20px; max-width: 540px; width: 100%; padding: 26px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-video" style="color: #1d4ed8;"></i> رفع درس فيديو جديد
            </h3>
            <button type="button" onclick="closeUploadVideoModal()" style="background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer;">✕</button>
        </div>

        <form id="uploadVideoForm" onsubmit="submitVideoForm(event)">
            @csrf
            <input type="hidden" name="type" value="video">

            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">المادة الأكاديمية *</label>
                    <select name="subject_id" required style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}" {{ $subjectId == $sub->id ? 'selected' : '' }}>{{ $sub->name_ar }} ({{ $sub->stage?->label_ar ?? 'توجيهي' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">عنوان الدرس المصور *</label>
                    <input type="text" name="title" required placeholder="مثال: شرح قانون كولوم وحل مسائل وزارية" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">رقم ترتيب الدرس *</label>
                        <input type="number" name="order" value="{{ $videos->count() + 1 }}" required min="1" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                    </div>
                    <div>
                        <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">الوحدة / اسم القائمة</label>
                        <input type="text" name="channel_name" placeholder="مثال: الوحدة الأولى" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                    </div>
                </div>

                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: var(--ed-text-main); display: block; margin-bottom: 6px;">
                        <i class="fa-brands fa-youtube" style="color: #ef4444;"></i> رابط درس YouTube *
                    </label>
                    <input type="url" name="video_url" id="videoUrlInput" required placeholder="مثال: https://www.youtube.com/watch?v=... أو https://youtu.be/..." oninput="previewYoutube(this.value)" style="width: 100%; padding: 10px 14px; border: 1.5px solid var(--ed-border); border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none; direction: ltr; text-align: left; background: var(--ed-surface); color: var(--ed-text-main);">
                    <small style="color: var(--ed-text-muted); font-size: 0.74rem; display: block; margin-top: 4px;">
                        يدعم جميع روابط اليوتيوب (العادية، المختصرة youtu.be، والفيديوهات القصيرة Shorts).
                    </small>
                </div>

                <!-- معاينة فورية للفيديو -->
                <div id="ytPreviewContainer" style="display: none; margin-top: 4px;">
                    <label style="font-size: 0.78rem; font-weight: 700; color: var(--ed-text-muted); display: block; margin-bottom: 4px;">معاينة الفيديو المباشرة:</label>
                    <div style="position: relative; padding-top: 56.25%; border-radius: 12px; overflow: hidden; background: #000;">
                        <iframe id="ytPreviewFrame" src="" style="position: absolute; inset: 0; width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="closeUploadVideoModal()" style="padding: 10px 20px; border-radius: 10px; background: #f1f5f9; color: #64748b; border: none; font-weight: 700; cursor: pointer;">إلغاء</button>
                <button type="submit" id="btnSubmitVideo" style="padding: 10px 24px; border-radius: 10px; background: #1d4ed8; color: white; border: none; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-check"></i>
                    <span>حفظ ونشر الدرس</span>
                </button>
            </div>
        </form>
    </div>
</div>

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
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الرفع...';

    const form = document.getElementById('uploadVideoForm');
    const formData = new FormData(form);

    try {
        const res = await axios.post("{{ route('teacher.educational_contents.store') }}", formData);
        Swal.fire({
            icon: 'success',
            title: res.data.title || 'تم الحفظ بنجاح',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#1d4ed8'
        }).then(() => location.reload());
    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = originalText;
        const msg = err.response?.data?.title || err.response?.data?.message || 'حدث خطأ أثناء الرفع';
        Swal.fire({ icon: 'error', title: 'خطأ', text: msg });
    }
}

async function deleteContentItem(id) {
    Swal.fire({
        title: 'حذف الدرس المصور',
        text: 'هل أنت متأكد من رغبتك في حذف هذا الفيديو؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'نعم، حذف',
        cancelButtonText: 'تراجع'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete(`{{ url('teacher/educational_contents') }}/${id}`, {
                    data: { _token: '{{ csrf_token() }}' }
                });
                Swal.fire({ icon: 'success', title: 'تم الحذف', timer: 1200, showConfirmButton: false })
                    .then(() => location.reload());
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر حذف المحتوى.' });
            }
        }
    });
}
</script>
@endsection
