@extends('layouts.app')

@section('title', __('Study Booklets & Documents Management') . ' | ' . config('app.name'))

@section('content')
<div class="ed-teacher-files-container">

    <!-- الهيدر الأكاديمي -->
    <header class="ed-teacher-header">
        <div>
            <div class="ed-teacher-breadcrumbs">
                <a href="{{ route('teacher.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Teacher Portal') }}</a>
                <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" style="font-size: 0.7rem;"></i>
                <span class="active">{{ __('Booklets & Documents') }}</span>
            </div>
            <h1>
                <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i> {{ __('Manage & Upload Booklets and Worksheets') }}
            </h1>
            <p>
                {{ __('Upload Tawjihi booklets, worksheets, and ministerial summaries in approved PDF format for instant student download.') }}
            </p>
        </div>

        <button type="button" onclick="openUploadFileModal()" class="ed-btn-upload-file">
            <i class="fa-solid fa-file-arrow-up"></i>
            <span>{{ __('Upload New Booklet / File') }}</span>
        </button>
    </header>

    <!-- شبكة الملفات -->
    <div class="ed-files-grid">
        @forelse($files as $file)
            @php
                $meta = $file->file_meta ?? [
                    'icon' => 'fa-solid fa-file-pdf',
                    'color' => '#ef4444',
                    'bg' => '#fef2f2',
                    'label' => 'PDF'
                ];
            @endphp
            <div class="ed-file-card">
                <div>
                    <div class="file-card-header">
                        <div class="file-icon-box" style="background: {{ $meta['bg'] }}; color: {{ $meta['color'] }};">
                            <i class="{{ $meta['icon'] }}"></i>
                        </div>
                        <div style="overflow: hidden;">
                            <span class="file-tag">
                                {{ $file->subject?->name_ar ?? __('General') }} • {{ $meta['label'] }}
                            </span>
                            <h3 class="file-title" title="{{ $file->title }}">
                                {{ $file->title }}
                            </h3>
                        </div>
                    </div>

                    <div class="file-meta-box">
                        <div><i class="fa-solid fa-folder" style="color: #f59e0b;"></i> <strong>{{ __('Section:') }}</strong> {{ $file->channel_name ?? __('General Booklet') }}</div>
                        <div><i class="fa-solid fa-weight-hanging" style="color: #1e3a8a;"></i> <strong>{{ __('Size:') }}</strong> {{ $file->file_size ?? __('Unspecified') }}</div>
                    </div>
                </div>

                <div class="file-card-footer">
                    <a href="{{ route('content.download', $file->id) }}" target="_blank" class="btn-download">
                        <i class="fa-solid fa-cloud-arrow-down"></i> {{ __('Download File') }}
                    </a>

                    <div class="action-btns">
                        <a href="{{ route('teacher.educational_contents.edit', $file->id) }}" class="btn-edit" title="{{ __('Edit') }}">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <button type="button" onclick="deleteFileItem({{ $file->id }})" class="btn-delete" title="{{ __('Delete') }}">
                            <i class="fa-solid fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="ed-empty-files">
                <i class="fa-solid fa-folder-open"></i>
                <h3>{{ __('No booklets or files uploaded currently') }}</h3>
                <p>{{ __('Click "Upload New Booklet" to share study materials with your students.') }}</p>
                <button type="button" onclick="openUploadFileModal()" class="ed-btn-upload-file" style="margin: 0 auto;">
                    {{ __('Upload New Booklet / File') }}
                </button>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px;">
        {{ $files->links() }}
    </div>

</div>

<!-- Modal رفع ملزمة / ملف PDF -->
<div id="uploadFileModal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; max-width: 540px; width: 100%; padding: 26px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i> {{ __('Upload Booklet / Ministerial Worksheet') }}
            </h3>
            <button type="button" onclick="closeUploadFileModal()" style="background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer;">✕</button>
        </div>

        <form id="uploadDocForm" onsubmit="submitDocForm(event)">
            @csrf
            <input type="hidden" name="type" value="pdf">

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
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">{{ __('Booklet / Worksheet Title *') }}</label>
                    <input type="text" name="title" required placeholder="{{ __('e.g. Honors Exam Solutions Booklet') }}" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">{{ __('Order Number *') }}</label>
                        <input type="number" name="order" value="{{ $files->count() + 1 }}" required min="1" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                    </div>
                    <div>
                        <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">{{ __('Booklet Category') }}</label>
                        <input type="text" name="channel_name" placeholder="{{ __('e.g. Unit 1 Worksheets') }}" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none;">
                    </div>
                </div>

                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">{{ __('File / Study Booklet (Direct Upload) *') }}</label>
                    <input type="file" name="file_upload_pdf" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.png,.jpg,.jpeg,.webp,.zip,.rar,.txt" required style="width: 100%; padding: 10px; border: 1.5px dashed #cbd5e1; border-radius: 10px; font-family: inherit; font-size: 0.85rem; background: #f8fafc;">
                    <small style="color: #64748b; font-size: 0.72rem; display: block; margin-top: 4px;">{{ __('Supports PDF, Word, Excel, PowerPoint, Images, and ZIP archives (up to 100MB).') }}</small>
                </div>

                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">{{ __('Or direct cloud PDF link (optional)') }}</label>
                    <input type="url" name="pdf_url" placeholder="https://..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.9rem; outline: none; direction: ltr; text-align: left;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="closeUploadFileModal()" style="padding: 10px 20px; border-radius: 10px; background: #f1f5f9; color: #64748b; border: none; font-weight: 700; cursor: pointer;">{{ __('Cancel') }}</button>
                <button type="submit" id="btnSubmitDoc" style="padding: 10px 24px; border-radius: 10px; background: #dc2626; color: white; border: none; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ __('Save and Publish Booklet') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.ed-teacher-files-container {
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
    color: #dc2626;
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

.ed-btn-upload-file {
    background: #dc2626;
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
    box-shadow: 0 4px 14px rgba(220, 38, 38, 0.25);
    transition: all 0.2s;
}

.ed-btn-upload-file:hover {
    background: #b91c1c;
}

.ed-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 22px;
}

.ed-file-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    padding: 22px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 16px;
    transition: all 0.2s;
}

.ed-file-card:hover {
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
}

.file-card-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 14px;
}

.file-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.file-tag {
    background: #eff6ff;
    color: #1e3a8a;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 700;
    display: inline-block;
    margin-bottom: 4px;
}

.file-title {
    margin: 0;
    font-size: 1.02rem;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-meta-box {
    font-size: 0.8rem;
    color: #64748b;
    line-height: 1.6;
    background: #f8fafc;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

.file-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}

.btn-download {
    padding: 8px 16px;
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
    border-radius: 10px;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.btn-download:hover {
    background: #1e3a8a;
    color: #ffffff;
    border-color: #1e3a8a;
}

.action-btns {
    display: flex;
    gap: 6px;
}

.btn-edit {
    padding: 8px 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    color: #475569;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 700;
}

.btn-edit:hover {
    border-color: #1e3a8a;
    color: #1e3a8a;
}

.btn-delete {
    padding: 8px 12px;
    background: #fee2e2;
    border: 1px solid #fecaca;
    border-radius: 10px;
    color: #dc2626;
    cursor: pointer;
    font-size: 0.8rem;
}

.ed-empty-files {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 20px;
    border: 2px dashed #cbd5e1;
    color: #94a3b8;
}

.ed-empty-files i {
    font-size: 3rem;
    margin-bottom: 12px;
    display: block;
    opacity: 0.4;
}

.ed-empty-files h3 {
    margin: 0 0 6px;
    font-weight: 800;
    color: #475569;
}

.ed-empty-files p {
    margin: 0 0 20px;
    font-size: 0.88rem;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
function openUploadFileModal() {
    document.getElementById('uploadFileModal').style.display = 'flex';
}
function closeUploadFileModal() {
    document.getElementById('uploadFileModal').style.display = 'none';
}

async function submitDocForm(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitDoc');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __("Uploading...") }}';

    const form = document.getElementById('uploadDocForm');
    const formData = new FormData(form);

    try {
        const res = await axios.post("{{ route('teacher.educational_contents.store') }}", formData);
        Swal.fire({
            icon: 'success',
            title: res.data.title || '{{ __("Saved successfully") }}',
            confirmButtonText: '{{ __("OK") }}',
            confirmButtonColor: '#dc2626'
        }).then(() => location.reload());
    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = originalText;
        const msg = err.response?.data?.title || err.response?.data?.message || '{{ __("Error uploading content") }}';
        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: msg });
    }
}

async function deleteFileItem(id) {
    Swal.fire({
        title: '{{ __("Delete Booklet?") }}',
        text: '{{ __("Are you sure you want to delete this file?") }}',
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
                Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Could not delete file.") }}' });
            }
        }
    });
}
</script>
@endsection
