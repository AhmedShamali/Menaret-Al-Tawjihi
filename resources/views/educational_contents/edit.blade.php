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
                            <span class="field-label" style="margin:0">
                                <i class="fa-brands fa-youtube text-danger"></i> {{ __('رابط فيديو الدرس (YouTube)') }}
                            </span>
                            @if($content->url_path)
                                <span class="badge-present">{{ __('موجود حالياً') }} ✅</span>
                            @endif
                        </div>
                        <input type="url" name="video_url" id="editVideoUrl" value="{{ $content->url_path }}" class="input-style font-mono" placeholder="https://www.youtube.com/watch?v=..." oninput="previewEditYt(this.value)">
                        <small class="upload-hint">{{ __('يدعم روابط YouTube العادية والمختصرة و Shorts.') }}</small>
                        
                        <div id="editYtPreview" style="{{ $content->youtube_id ? 'display:block;' : 'display:none;' }} margin-top:12px; position:relative; padding-top:56.25%; background:#000; border-radius:10px; overflow:hidden;">
                            <iframe id="editYtFrame" src="{{ $content->youtube_embed_url ?? '' }}" style="position:absolute; inset:0; width:100%; height:100%; border:none;" allowfullscreen></iframe>
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

                    <!-- شريط التقدم -->
                    <div class="progress-box" id="progress_box">
                        <div class="progress-label-row">
                            <span>{{ __('جاري حفظ الملفات...') }}</span>
                            <span id="percent_text" class="font-mono">0%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" id="bar_fill"></div>
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

    function handleUpdate() {
        const btn = document.getElementById('submitBtn');
        const form = document.getElementById('editForm');
        const progressBox = document.getElementById('progress_box');
        const barFill = document.getElementById('bar_fill');
        const percentText = document.getElementById('percent_text');

        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('يرجى الانتظار...') }}';
        progressBox.style.display = 'block';

        axios.post("{{ route('educational_contents.update', $content->id) }}", formData, {
            onUploadProgress: (p) => {
                if (p.total) {
                    let percent = Math.round((p.loaded * 100) / p.total);
                    barFill.style.width = percent + '%';
                    percentText.innerText = percent + '%';
                }
            }
        })
        .then(res => {
            Swal.fire({ icon: 'success', title: '{{ __('تم التحديث بنجاح!') }}', showConfirmButton: false, timer: 1500 })
            .then(() => window.location.href = "{{ route('educational_contents.index') }}");
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> {{ __('حفظ التغييرات') }}';
            progressBox.style.display = 'none';
            Swal.fire({ icon: 'error', title: '{{ __('خطأ في الرفع') }}', text: err.response?.data?.message || '{{ __('تأكد من الحقول وحجم الملفات') }}' });
        });
    }

    function previewEditYt(url) {
        if (!url) {
            document.getElementById('editYtPreview').style.display = 'none';
            return;
        }
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|shorts\/)([^#\&\?]*).*/;
        const match = url.match(regExp);
        const id = (match && match[2].length === 11) ? match[2] : null;
        const preview = document.getElementById('editYtPreview');
        const frame = document.getElementById('editYtFrame');
        if (id) {
            frame.src = 'https://www.youtube.com/embed/' + id + '?rel=0';
            preview.style.display = 'block';
        } else {
            frame.src = '';
            preview.style.display = 'none';
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
