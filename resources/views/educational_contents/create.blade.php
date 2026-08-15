@extends('layouts.app')

@section('title', 'إضافة محتوى تعليمي جديد')

@section('content')
<div class="content-wrapper">

    <div class="page-header">
        <div>
            <h1 class="page-title">➕ إضافة محتوى تعليمي جديد</h1>
            <p class="page-subtitle">يمكنك إضافة فيديو، ملف PDF، أو كلاهما معاً للدرس بضغطة واحدة</p>
        </div>
        <a href="{{ route('teacher.educational_contents.index') }}" class="btn-secondary-custom">إلغاء والعودة</a>
    </div>

    <form id="createForm" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">

            <div class="main-column">

                <!-- البيانات الأساسية -->
                <div class="glass-card">
                    <h3 class="card-title">📦 البيانات الأساسية</h3>

                    <div class="form-group">
                        <label class="f-label">عنوان الدرس / المحتوى <span class="required">*</span></label>
                        <input type="text" name="title" class="f-input" placeholder="مثال: الدرس الثالث - الفيزياء" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="f-label">المرحلة الدراسية <span class="required">*</span></label>
                            <select id="stage_select" class="f-input">
                                <option value="">اختر المرحلة...</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->label_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="f-label">المادة الدراسية <span class="required">*</span></label>
                            <select name="subject_id" id="subject_select" class="f-input" required disabled>
                                <option value="">اختر المرحلة أولاً...</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- المرفقات -->
                <div class="glass-card">
                    <h3 class="card-title">📎 المرفقات المتاحة في هذا الدرس</h3>

                    <div class="attachment-selectors">
                        <label class="selector-card" id="card_video">
                            <input type="checkbox" id="check_video" onchange="toggleAttachmentSections()">
                            <div class="selector-content">
                                <span class="icon">🎥</span>
                                <div>
                                    <strong>فيديو تعليمي</strong>
                                    <small>رابط يوتيوب أو رفع فيديو مباشرة</small>
                                </div>
                            </div>
                        </label>

                        <label class="selector-card" id="card_pdf">
                            <input type="checkbox" id="check_pdf" onchange="toggleAttachmentSections()">
                            <div class="selector-content">
                                <span class="icon">📄</span>
                                <div>
                                    <strong>ملف مرفق (PDF)</strong>
                                    <small>ملف ملخص، واجب، أو كتاب</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div id="video_section" class="attachment-box" style="display: none;">
                        <h4 class="box-title">🎬 تفاصيل الفيديو</h4>
                        <div class="form-group">
                            <label class="f-label">رابط الفيديو (YouTube أو Google Drive)</label>
                            <input type="url" name="video_url" class="f-input" placeholder="https://www.youtube.com/watch?v=...">
                            <small style="color: var(--text-muted);">يرجى التأكد من أن رابط الفيديو متاح للجميع (Public).</small>
                        </div>
                    </div>

                    <!-- تفاصيل الـ PDF -->
                    <div id="pdf_section" class="attachment-box" style="display: none;">
                        <h4 class="box-title">📑 تفاصيل ملف الـ PDF</h4>
                        <div class="form-group">
                            <label class="f-label">رفع ملف المستند (PDF / Document)</label>
                            <input type="file" name="file_upload_pdf" accept=".pdf,.doc,.docx" class="f-input file-input">
                        </div>
                        <div class="form-group">
                            <label class="f-label">أو رابط ملف خارجي (Google Drive)</label>
                            <input type="text" name="pdf_url" class="f-input" placeholder="https://drive.google.com/file/d/...">
                        </div>
                    </div>

                    <!-- شريط التقدم -->
                    <div id="upload_progress_container" class="progress-box" style="display: none;">
                        <div class="progress-header">
                            <span id="progress_status_text">جاري رفع الملفات... 📤</span>
                            <span id="progress_percent_text">0%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div id="progress_bar_fill" class="progress-bar-fill"></div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="side-column">
                <div class="glass-card">
                    <h3 class="card-title">⚙️ تفاصيل إضافية</h3>
                    <div class="form-group">
                        <label class="f-label">اسم القناة / المصدر</label>
                        <input type="text" name="channel_name" class="f-input" placeholder="مثال: أ. معتز اسليم">
                    </div>
                    <div class="form-group">
                        <label class="f-label">ترتيب الدرس</label>
                        <input type="number" name="order" value="1" min="1" class="f-input">
                    </div>
                </div>

                <button type="button" onclick="submitContent()" id="saveBtn" class="btn-submit">
                    حفظ ونشر المحتوى 🚀
                </button>
            </div>

        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const stages = @json($stages);

    document.getElementById('stage_select').addEventListener('change', function() {
        const subSel = document.getElementById('subject_select');
        subSel.innerHTML = '<option value="">اختر المادة...</option>';
        const stage = stages.find(s => s.id == this.value);
        if (stage && stage.subjects && stage.subjects.length > 0) {
            subSel.disabled = false;
            stage.subjects.forEach(sub => {
                subSel.innerHTML += `<option value="${sub.id}">${sub.name_ar}</option>`;
            });
        } else {
            subSel.disabled = true;
        }
    });

    function toggleAttachmentSections() {
        const videoChecked = document.getElementById('check_video').checked;
        const pdfChecked = document.getElementById('check_pdf').checked;

        document.getElementById('video_section').style.display = videoChecked ? 'block' : 'none';
        document.getElementById('card_video').classList.toggle('selected', videoChecked);

        document.getElementById('pdf_section').style.display = pdfChecked ? 'block' : 'none';
        document.getElementById('card_pdf').classList.toggle('selected', pdfChecked);
    }

    function submitContent() {
        const videoChecked = document.getElementById('check_video').checked;
        const pdfChecked = document.getElementById('check_pdf').checked;

        if (!videoChecked && !pdfChecked) {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'يرجى اختيار مرفق واحد على الأقل (فيديو أو ملف PDF) قبل الحفظ!',
            });
            return;
        }

        const btn = document.getElementById('saveBtn');
        const form = document.getElementById('createForm');
        const formData = new FormData(form);

        let contentType = 'video';
        if (pdfChecked && !videoChecked) {
            contentType = 'file';
        } else if (videoChecked && pdfChecked) {
            contentType = 'both';
        }
        formData.append('type', contentType);

        btn.disabled = true;
        btn.textContent = 'جاري الرفع...';

        const progressContainer = document.getElementById('upload_progress_container');
        const progressBarFill = document.getElementById('progress_bar_fill');
        const progressPercentText = document.getElementById('progress_percent_text');

        progressContainer.style.display = 'block';

        axios.post("{{ route('teacher.educational_contents.store') }}", formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onUploadProgress: function(progressEvent) {
                if (progressEvent.lengthComputable) {
                    const percent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    progressBarFill.style.width = percent + '%';
                    progressPercentText.textContent = percent + '%';
                }
            }
        })
        .then(function (response) {
            Swal.fire({
                icon: response.data.icon || 'success',
                title: response.data.title || 'تم الإضافة بنجاح! 🎉',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = "{{ route('teacher.educational_contents.index') }}";
            });
        })
        .catch(function (error) {
            let errorMsg = 'حدث خطأ أثناء حفظ المحتوى';
            if (error.response && error.response.data) {
                errorMsg = error.response.data.title || error.response.data.message || errorMsg;
            }

            Swal.fire({ icon: 'error', title: 'خطأ', text: errorMsg });
            progressContainer.style.display = 'none';
            btn.disabled = false;
            btn.textContent = 'حفظ ونشر المحتوى 🚀';
        });
    }
</script>

<style>
    :root { --primary: #2563eb; --primary-dark: #1d4ed8; --border-color: #e2e8f0; --text-dark: #0f172a; --text-muted: #64748b; }
    .content-wrapper { max-width: 1100px; margin: 0 auto; padding: 20px 0; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .page-title { font-size: 2rem; font-weight: 800; color: var(--primary); margin: 0; }
    .form-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 25px; }
    .main-column, .side-column { display: flex; flex-direction: column; gap: 20px; }
    .glass-card { background: #ffffff; padding: 28px; border-radius: 20px; border: 1px solid var(--border-color); }
    .card-title { font-size: 1.15rem; font-weight: 700; margin-bottom: 15px; }
    .form-group { margin-bottom: 18px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .f-label { display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 8px; }
    .f-input { width: 100%; padding: 12px; border-radius: 12px; border: 1.5px solid var(--border-color); outline: none; }
    .attachment-selectors { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .selector-card { border: 2px solid var(--border-color); padding: 16px; border-radius: 16px; cursor: pointer; position: relative; }
    .selector-card.selected { border-color: var(--primary); background: #eff6ff; }
    .selector-content { display: flex; align-items: center; gap: 12px; }
    .attachment-box { background: #f8fafc; padding: 20px; border-radius: 16px; border: 2px dashed #cbd5e1; margin-top: 15px; }
    .progress-box { background: #eff6ff; padding: 20px; border-radius: 16px; margin-top: 20px; }
    .progress-header { display: flex; justify-content: space-between; font-weight: 700; margin-bottom: 10px; }
    .progress-bar-bg { width: 100%; height: 12px; background: #dbeafe; border-radius: 10px; overflow: hidden; }
    .progress-bar-fill { height: 100%; width: 0%; background: var(--primary); transition: width 0.2s; }
    .btn-submit { background: var(--primary); color: #fff; border: none; padding: 18px; border-radius: 14px; font-weight: 800; cursor: pointer; width: 100%; }
    .btn-secondary-custom { border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 12px; color: var(--text-dark); text-decoration: none; }
</style>
@endsection
