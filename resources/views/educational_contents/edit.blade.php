@extends('layouts.app')

@section('title', __('تعديل المحتوى التعليمي') . ' - ' . __('إدارة المنصة'))

@section('content')
<div class="editor-wrapper">
    <div class="academic-header-card">
        <div>
            <div class="badge-tag">
                <i class="fa-solid fa-book-open"></i>
                <span>{{ __('إدارة المناهج والمحتوى') }}</span>
            </div>
            <h1 class="header-title">{{ __('تعديل الدرس التعليمي') }}</h1>
            <p class="header-subtitle">{{ __('تحديث تفاصيل المحتوى، روابط الشرح، ومرفقات الدرس الأكاديمي.') }}</p>
        </div>
        <a href="{{ route('educational_contents.index') }}" class="btn-classic-nav">
            <i class="fa-solid fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i> {{ __('إلغاء والتراجع') }}
        </a>
    </div>

    <form id="editForm">
        @csrf
        @method('PUT')

        <div class="editor-grid">
            <!-- العمود الرئيسي الكبير -->
            <main>
                <!-- بطاقة البيانات -->
                <div class="editor-card">
                    <div class="card-title">
                        <i class="fa-solid fa-circle-info text-navy"></i>
                        <span>{{ __('المعلومات الأساسية') }}</span>
                    </div>

                    <div class="field-group">
                        <label class="field-label">{{ __('عنوان المحتوى / الدرس') }} <span class="req-star">*</span></label>
                        <input type="text" name="title" value="{{ $content->title }}" class="input-style" placeholder="{{ __('أدخل اسم الدرس...') }}" required>
                    </div>

                    <div class="flex-row">
                        <div class="field-group">
                            <label class="field-label">{{ __('المرحلة الدراسية') }}</label>
                            <select id="stage_select" class="input-style">
                                <option value="">{{ __('اختر المرحلة...') }}</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}" {{ optional($content->subject)->stage_id == $stage->id ? 'selected' : '' }}>
                                        {{ $stage->label_ar ?? $stage->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label">{{ __('المادة الدراسية') }} <span class="req-star">*</span></label>
                            <select name="subject_id" id="subject_select" class="input-style" required>
                                @if($content->subject)
                                    <option value="{{ $content->subject_id }}" selected>{{ $content->subject->name_ar ?? $content->subject->name }}</option>
                                @else
                                    <option value="">{{ __('اختر المرحلة أولاً') }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <!-- قسم المرفقات -->
                <div class="editor-card">
                    <div class="card-title">
                        <i class="fa-solid fa-paperclip text-navy"></i>
                        <span>{{ __('الروابط والمرفقات الأكاديمية') }}</span>
                    </div>

                    <!-- قسم الفيديو -->
                    <div class="upload-section">
                        <div class="upload-header">
                            <span class="field-label" style="margin:0; color: #1e40af;">
                                <i class="fa-solid fa-cloud-arrow-up" style="color: #2563eb;"></i> {{ __('ملف فيديو الدرس (MP4 / WebM)') }}
                            </span>
                            @if($content->url_path)
                                <span class="badge-present">
                                    {{ __('يوجد فيديو مسجل للدرس ✅') }}
                                </span>
                            @endif
                        </div>

                        <div id="edit_box_video">
                            <input type="file" name="video_file" id="editVideoFileInput" accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-m4v" class="input-style" style="padding: 10px; background: #ffffff;">
                            <small class="upload-hint" style="color: #166534; font-weight: 600; margin-top: 6px;">
                                <i class="fa-solid fa-circle-check"></i> {{ __('رفع محلي مباشر بنظام الأجزاء السريع. يدعم الأوفلاين للطلبة بدون إنترنت.') }}
                            </small>
                            @if($content->url_path)
                                <div style="margin-top: 8px; font-size: 0.8rem; color: #64748b;">
                                    <i class="fa-solid fa-link"></i> {{ __('المسار الحالي:') }} <code style="direction: ltr; display: inline-block;">{{ Str::limit($content->url_path, 60) }}</code>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- قسم الملف -->
                    <div class="upload-section">
                        <div class="upload-header">
                            <span class="field-label" style="margin:0; color:#1e3a8a">
                                <i class="fa-solid fa-file-pdf"></i> {{ __('ملف المادة أو الملزمة المرفقة') }}
                            </span>
                            @if($content->pdf_path) 
                                <span class="badge-present">
                                    {{ __('مرفق حالياً') }} ({{ strtoupper($content->file_extension ?? 'ملف') }}) ✅
                                </span> 
                            @endif
                        </div>
                        <div class="flex-row">
                            <input type="file" name="file_upload_pdf" class="input-style" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.png,.jpg,.jpeg,.webp,.zip,.rar,.txt">
                        </div>
                        <small class="upload-hint">{{ __('يدعم ملفات PDF، Word، Excel، PowerPoint، الصور، والأرشيف المضغوط حتى 100 ميجابايت.') }}</small>
                    </div>

                    <!-- شريط التقدم للرفع المباشر بالأجزاء للملفات الضخمة بالجيجابايت -->
                    <div class="progress-box" id="progress_box" style="display: none; margin-top: 14px; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 14px 16px;">
                        <div class="progress-label-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span id="progress_status_text" style="font-size: 0.86rem; font-weight: 800; color: #1e40af; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <span>{{ __('جاري بدء تجهيز ورفع أجزاء الفيديو...') }}</span>
                            </span>
                            <span id="percent_text" class="font-mono" style="font-size: 0.95rem; font-weight: 900; color: #1e3a8a;">0%</span>
                        </div>
                        <div class="progress-bar-bg" style="width: 100%; height: 10px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                            <div class="progress-bar-fill" id="bar_fill" style="width: 0%; height: 100%; background: linear-gradient(90deg, #2563eb, #3b82f6, #059669); transition: width 0.2s ease;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px; font-size: 0.76rem; color: #64748b; flex-wrap: wrap; gap: 6px;">
                            <span id="edit_file_meta" style="font-weight: 700; color: #334155;">-- / --</span>
                            <span id="edit_speed_meta" style="font-weight: 700; color: #059669;"><i class="fa-solid fa-gauge-high"></i> --</span>
                            <span id="edit_eta_meta" style="font-weight: 700; color: #d97706;"><i class="fa-solid fa-clock"></i> --</span>
                            <span id="edit_part_meta" style="font-weight: 700; color: #64748b;">--</span>
                        </div>
                    </div>
                </div>
            </main>

            <!-- العمود الجانبي -->
            <aside>
                <div class="editor-card">
                    <div class="card-title">
                        <i class="fa-solid fa-sliders text-navy"></i>
                        <span>{{ __('الضبط والنشر') }}</span>
                    </div>

                    <div class="field-group">
                        <label class="field-label">{{ __('اسم القناة / المصدر') }}</label>
                        <input type="text" name="channel_name" value="{{ $content->channel_name }}" class="input-style" placeholder="{{ __('مثال: أكاديمية فلسطين...') }}">
                    </div>

                    <div class="field-group">
                        <label class="field-label">{{ __('الترتيب') }}</label>
                        <input type="number" name="order" value="{{ $content->order }}" class="input-style font-mono">
                    </div>

                    <div class="field-group">
                        <label class="field-label">{{ __('حجم الملف الظاهر') }}</label>
                        <input type="text" name="file_size" value="{{ $content->file_size }}" class="input-style font-mono">
                    </div>

                    <button type="button" onclick="handleUpdate()" id="submitBtn" class="btn-submit">
                        <i class="fa-solid fa-check"></i> {{ __('حفظ التغييرات') }}
                    </button>
                </div>

                <p class="update-stamp font-mono">{{ __('آخر تحديث:') }} {{ $content->updated_at ? $content->updated_at->format('Y-m-d') : '—' }}</p>
            </aside>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const stageData = @json($stages);
    document.getElementById('stage_select').addEventListener('change', function() {
        const subSel = document.getElementById('subject_select');
        subSel.innerHTML = '<option value="">{{ __('اختر المادة...') }}</option>';
        const stage = stageData.find(s => s.id == this.value);
        if (stage && stage.subjects) {
            stage.subjects.forEach(sub => {
                subSel.innerHTML += `<option value="${sub.id}">${sub.name_ar || sub.name}</option>`;
            });
        }
    });

    async function handleUpdate() {
        const btn = document.getElementById('submitBtn');
        const originalText = btn.innerHTML;
        const form = document.getElementById('editForm');
        const progressBox = document.getElementById('progress_box');
        const barFill = document.getElementById('bar_fill');
        const percentText = document.getElementById('percent_text');
        const progressStatusText = document.getElementById('progress_status_text');
        const editFileMeta = document.getElementById('edit_file_meta');
        const editSpeedMeta = document.getElementById('edit_speed_meta');
        const editEtaMeta = document.getElementById('edit_eta_meta');
        const editPartMeta = document.getElementById('edit_part_meta');
        const videoInput = document.getElementById('editVideoFileInput');

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري فحص وتجهيز الرفع بالجيجاوات...') }}';
        progressBox.style.display = 'block';

        const preventTabClose = (ev) => {
            ev.preventDefault();
            ev.returnValue = '{{ __('جاري رفع وتعديل فيديو ضخم، هل أنت متأكد من مغادرة الصفحة؟') }}';
        };
        window.addEventListener('beforeunload', preventTabClose);

        let uploadedVideoPath = null;
        let formattedVideoSize = null;

        try {
            // إذا اختار المعلم ملف فيديو جديد للتعديل، يتم رفعه بنظام الأجزاء والاستئناف
            if (videoInput && videoInput.files && videoInput.files.length > 0) {
                const file = videoInput.files[0];
                const CHUNK_SIZE = file.size > (1024 * 1024 * 1024) ? (5 * 1024 * 1024) : (4 * 1024 * 1024);
                const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
                const cleanName = encodeURIComponent(file.name).replace(/[^a-zA-Z0-9]/g, '').slice(0, 24);
                const fileId = 'vid_' + cleanName + '_' + file.size;

                const chunkUrl = "{{ route('educational_contents.upload_chunk') }}";
                const checkStatusUrl = "{{ route('educational_contents.check_chunk_status') }}";

                // فحص الأجزاء السابقة للاستئناف
                let alreadyUploaded = new Set();
                try {
                    const statusRes = await axios.post(checkStatusUrl, {
                        file_id: fileId,
                        total_chunks: totalChunks,
                        _token: '{{ csrf_token() }}'
                    });
                    if (statusRes.data && statusRes.data.uploaded_chunks && statusRes.data.uploaded_chunks.length > 0) {
                        alreadyUploaded = new Set(statusRes.data.uploaded_chunks);
                        if (alreadyUploaded.size > 0 && alreadyUploaded.size < totalChunks && progressStatusText) {
                            progressStatusText.innerHTML = `<i class="fa-solid fa-bolt" style="color: #d97706;"></i> {{ __('تم العثور على أجزاء مرفوعة مسبقاً! جاري الاستئناف 🚀') }}`;
                        }
                    }
                } catch (e) {
                    console.warn('Check chunk status skipped:', e);
                }

                const startTime = Date.now();
                let bytesUploadedThisSession = 0;

                async function sendChunkWithRetry(formData, chunkIdx, maxRetries = 5) {
                    for (let attempt = 1; attempt <= maxRetries; attempt++) {
                        try {
                            return await axios.post(chunkUrl, formData, {
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'multipart/form-data',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                timeout: 120000
                            });
                        } catch (err) {
                            if (attempt === maxRetries) throw err;
                            const delay = attempt * 2000;
                            if (progressStatusText) {
                                progressStatusText.innerHTML = `<i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b;"></i> {{ __('تذبذب شبكة بالجزء') }} ${chunkIdx + 1} (${attempt}/${maxRetries})...`;
                            }
                            await new Promise(r => setTimeout(r, delay));
                        }
                    }
                }

                for (let i = 0; i < totalChunks; i++) {
                    const start = i * CHUNK_SIZE;
                    const end = Math.min(file.size, start + CHUNK_SIZE);
                    const chunkLength = end - start;

                    if (alreadyUploaded.has(i) && (i < totalChunks - 1)) {
                        const pct = Math.round(((i + 1) / totalChunks) * 88);
                        barFill.style.width = pct + '%';
                        percentText.innerText = pct + '%';
                        if (editFileMeta) editFileMeta.textContent = `${(end / (1024*1024)).toFixed(1)}MB / ${(file.size / (1024*1024)).toFixed(1)}MB`;
                        if (editPartMeta) editPartMeta.textContent = `{{ __('الجزء') }} ${i + 1}/${totalChunks} ({{ __('مستأنف') }})`;
                        continue;
                    }

                    const chunkBlob = file.slice(start, end);
                    const chunkData = new FormData();
                    chunkData.append('file_id', fileId);
                    chunkData.append('chunk_index', i);
                    chunkData.append('total_chunks', totalChunks);
                    chunkData.append('file_name', file.name);
                    chunkData.append('chunk', chunkBlob, 'part_' + i);
                    chunkData.append('_token', '{{ csrf_token() }}');

                    const chunkRes = await sendChunkWithRetry(chunkData, i);
                    bytesUploadedThisSession += chunkLength;

                    const elapsedSec = (Date.now() - startTime) / 1000;
                    const speedBps = elapsedSec > 0 ? (bytesUploadedThisSession / elapsedSec) : 0;
                    const remainingBytes = file.size - end;
                    const etaSec = speedBps > 0 ? Math.round(remainingBytes / speedBps) : 0;

                    const pct = Math.round(((i + 1) / totalChunks) * 88);
                    barFill.style.width = pct + '%';
                    percentText.innerText = pct + '%';
                    if (progressStatusText) {
                        progressStatusText.innerHTML = `<i class="fa-solid fa-cloud-arrow-up fa-fade"></i> {{ __('جاري رفع ومعالجة الفيديو الضخم:') }} ${pct}%`;
                    }
                    if (editFileMeta) editFileMeta.textContent = `${(end / (1024*1024)).toFixed(1)}MB / ${(file.size / (1024*1024)).toFixed(1)}MB`;
                    if (editSpeedMeta) editSpeedMeta.innerHTML = `<i class="fa-solid fa-gauge-high"></i> ${(speedBps / (1024*1024)).toFixed(1)} MB/s`;
                    if (editEtaMeta) editEtaMeta.innerHTML = `<i class="fa-solid fa-clock"></i> ${etaSec > 60 ? Math.ceil(etaSec/60) + ' د' : etaSec + ' ث'}`;
                    if (editPartMeta) editPartMeta.textContent = `{{ __('الجزء') }} ${i + 1}/${totalChunks}`;

                    if (chunkRes.data && chunkRes.data.done) {
                        uploadedVideoPath = chunkRes.data.uploaded_video_path;
                        formattedVideoSize = chunkRes.data.formatted_size;
                    }
                }
            }

            const formData = new FormData(form);
            formData.delete('video_file');

            if (uploadedVideoPath) {
                formData.append('uploaded_video_path', uploadedVideoPath);
                if (formattedVideoSize) {
                    formData.append('formatted_size', formattedVideoSize);
                }
            }

            if (progressStatusText) {
                progressStatusText.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #10b981;"></i> {{ __('اكتمل رفع الفيديو بالجيجاوات! جاري حفظ التعديلات...') }}';
            }

            const updateUrl = "{{ auth()->user()->role === 'admin' ? route('admin.educational_contents.update', $content->id) : route('teacher.educational_contents.update', $content->id) }}";
            const res = await axios.post(updateUrl, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            window.removeEventListener('beforeunload', preventTabClose);
            barFill.style.width = '100%';
            percentText.innerText = '100%';

            Swal.fire({
                icon: res.data.icon || 'success',
                title: res.data.title || '{{ __('تم التحديث بنجاح!') }}',
                text: '{{ __('تم حفظ التعديلات وأصبح الفيديو جاهزاً للبث والمشاهدة أوفلاين.') }}',
                showConfirmButton: false,
                timer: 1800
            }).then(() => {
                window.location.href = "{{ auth()->user()->role === 'admin' ? route('admin.educational_contents.index') : route('teacher.educational_contents.index') }}";
            });

        } catch (err) {
            window.removeEventListener('beforeunload', preventTabClose);
            btn.disabled = false;
            btn.innerHTML = originalText;

            let msg = '{{ __('حدث خطأ أثناء حفظ التعديلات أو رفع الفيديو') }}';
            if (err.response && err.response.data) {
                msg = err.response.data.title || err.response.data.message || err.response.data.error || msg;
            } else if (err.message) {
                msg = err.message;
            }

            Swal.fire({
                icon: 'error',
                title: '{{ __('خطأ') }}',
                html: msg + '<br><small style="color: #64748b; display: block; margin-top: 8px;">{{ __('ملاحظة: يمكنك إعادة المحاولة وسيتم استئناف الأجزاء المتبقية تلقائياً دون إعادة رفع ما اكتمل.') }}</small>'
            });
        }
    }
</script>

<style>
    .editor-wrapper {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 10px 0 60px;
        box-sizing: border-box;
    }

    .academic-header-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 22px 26px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
        border-inline-start: 5px solid var(--ed-primary, #1e3a8a);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .header-title {
        font-size: 1.45rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .header-subtitle {
        color: #64748b;
        font-size: 0.88rem;
        margin: 0;
    }
    .btn-classic-nav {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
        padding: 9px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.15s;
    }
    .btn-classic-nav:hover { background: #f8fafc; color: #0f172a; }

    .editor-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
        align-items: start;
    }

    .editor-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        margin-bottom: 22px;
    }
    .card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 1rem;
        color: #0f172a;
        margin-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 12px;
    }
    .text-navy { color: #1e3a8a; }
    .text-danger { color: #dc2626; }

    .field-group { margin-bottom: 18px; }
    .field-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 700;
        font-size: 0.85rem;
        color: #334155;
    }
    .req-star { color: #ef4444; }

    .input-style {
        width: 100%;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        font-size: 0.9rem;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.15s;
        font-family: inherit;
    }
    .input-style:focus {
        border-color: #1e3a8a;
        background: #ffffff;
    }

    .flex-row {
        display: flex;
        gap: 15px;
    }
    .flex-row > div { flex: 1; }

    .upload-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 18px;
        border-radius: 10px;
        margin-bottom: 18px;
    }
    .upload-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .badge-present {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        font-size: 0.75rem;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: 700;
    }
    .upload-hint {
        color: #64748b;
        font-size: 0.78rem;
        display: block;
        margin-top: 4px;
    }

    .progress-box { display: none; margin-top: 16px; }
    .progress-label-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #1e3a8a;
    }
    .progress-bar-bg { background: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden; }
    .progress-bar-fill { background: #1e3a8a; height: 100%; width: 0%; transition: width 0.2s; }

    .btn-submit {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: none;
        background: #1e3a8a;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.15s;
    }
    .btn-submit:hover { background: #172554; }
    .btn-submit:disabled { background: #94a3b8; cursor: not-allowed; }

    .update-stamp {
        text-align: center;
        font-size: 0.78rem;
        color: #94a3b8;
        margin-top: 10px;
    }

    @media (max-width: 900px) {
        .editor-grid { grid-template-columns: 1fr; }
        .flex-row { flex-direction: column; gap: 0; }
    }
</style>
@endsection
