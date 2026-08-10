@extends('layouts.app')

@section('title', 'تعديل المحتوى التعليمي')

@section('content')
<!-- CSS مخصص تماماً للمنصة -->
<style>
    /* الإعدادات العامة للحاوية */
    .editor-wrapper {
        direction: rtl;
        font-family: 'Segoe UI', Roboto, sans-serif;
        max-width: 1100px;
        margin: 20px auto;
        padding: 0 15px;
        color: #334155;
    }

    /* الهيدر */
    .editor-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .editor-header h2 { font-weight: 800; color: #1e293b; margin: 0; }
    
    .btn-exit {
        text-decoration: none;
        color: #64748b;
        border: 1.5px solid #e2e8f0;
        padding: 8px 20px;
        border-radius: 10px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-exit:hover { background: #f1f5f9; }

    /* شبكة التصميم الرئيسية */
    .editor-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 25px;
    }

    /* البطاقات */
    .editor-card {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 25px;
        border: 1px solid #f1f5f9;
    }

    .card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 1.1rem;
        color: #0f172a;
        margin-bottom: 25px;
        border-bottom: 2px solid #f8fafc;
        padding-bottom: 10px;
    }

    /* الحقول والإدخال */
    .field-group { margin-bottom: 20px; }
    
    .field-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        color: #475569;
    }

    .input-style {
        width: 100%;
        padding: 12px 15px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-size: 0.95rem;
        transition: 0.3s;
        box-sizing: border-box; /* هام جداً لمنع التمدد */
    }

    .input-style:focus {
        outline: none;
        border-color: #3b82f6;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* صفوف مرنة */
    .flex-row {
        display: flex;
        gap: 15px;
    }
    .flex-row > div { flex: 1; }

    /* أقسام الرفع */
    .upload-section {
        background: #f1f5f9;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 20px;
    }

    .upload-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .badge-present {
        background: #dcfce7;
        color: #166534;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
    }

    /* زر الحفظ */
    .btn-submit {
        width: 100%;
        padding: 15px;
        border-radius: 15px;
        border: none;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        font-weight: 700;
        font-size: 1.05rem;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.4); }
    .btn-submit:disabled { background: #94a3b8; cursor: not-allowed; transform: none; }

    /* شريط التقدم */
    .progress-box {
        display: none;
        margin-top: 20px;
    }
    .progress-bar-bg { background: #e2e8f0; height: 10px; border-radius: 10px; overflow: hidden; }
    .progress-bar-fill { background: #3b82f6; height: 100%; width: 0%; transition: width 0.3s; }

    /* التجاوب مع الجوال */
    @media (max-width: 850px) {
        .editor-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="editor-wrapper">
    
    <!-- رأس الصفحة -->
    <header class="editor-header">
        <div>
            <h2>تعديل الدرس التعليمي 📝</h2>
        </div>
        <a href="{{ route('educational_contents.index') }}" class="btn-exit">إلغاء والتراجع</a>
    </header>

    <form id="editForm">
        @csrf
        @method('PUT')

        <div class="editor-grid">
            
            <!-- العمود الأيمن الكبير -->
            <main>
                <!-- بطاقة البيانات -->
                <div class="editor-card">
                    <div class="card-title">📖 المعلومات الأساسية</div>
                    
                    <div class="field-group">
                        <label class="field-label">عنوان المحتوى</label>
                        <input type="text" name="title" value="{{ $content->title }}" class="input-style" placeholder="أدخل اسم الدرس..." required>
                    </div>

                    <div class="flex-row">
                        <div class="field-group">
                            <label class="field-label">المرحلة الدراسية</label>
                            <select id="stage_select" class="input-style">
                                <option value="">اختر...</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}" {{ optional($content->subject)->stage_id == $stage->id ? 'selected' : '' }}>
                                        {{ $stage->label_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label">المادة الدراسية</label>
                            <select name="subject_id" id="subject_select" class="input-style" required>
                                @if($content->subject)
                                    <option value="{{ $content->subject_id }}" selected>{{ $content->subject->name_ar }}</option>
                                @else
                                    <option value="">اختر المرحلة أولاً</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <!-- بطاقة المرفقات -->
                <div class="editor-card">
                    <div class="card-title">🔗 الروابط والمرفقات</div>

                    <!-- قسم الفيديو -->
                    <div class="upload-section">
                        <div class="upload-header">
                            <span class="field-label" style="margin:0">🎬 فيديو الدرس</span>
                            @if($content->url_path) <span class="badge-present">مرفق مسبقاً ✅</span> @endif
                        </div>
                        <div class="flex-row">
                            <input type="text" name="video_url" value="{{ filter_var($content->url_path, FILTER_VALIDATE_URL) ? $content->url_path : '' }}" class="input-style" placeholder="رابط يوتيوب">
                            <input type="file" name="file_upload_video" class="input-style" accept="video/*">
                        </div>
                    </div>

                    <!-- قسم الـ PDF -->
                    <div class="upload-section" style="background:#fef2f2">
                        <div class="upload-header">
                            <span class="field-label" style="margin:0; color:#991b1b">📄 مستند PDF</span>
                            @if($content->pdf_path) <span class="badge-present" style="background:#fee2e2; color:#991b1b">مرفق مسبقاً ✅</span> @endif
                        </div>
                        <div class="flex-row">
                            <input type="file" name="file_upload_pdf" class="input-style" accept=".pdf">
                            <input type="text" name="pdf_url" value="{{ filter_var($content->pdf_path, FILTER_VALIDATE_URL) ? $content->pdf_path : '' }}" class="input-style" placeholder="رابط خارجي">
                        </div>
                    </div>

                    <!-- التقدم -->
                    <div class="progress-box" id="progress_box">
                        <div style="display:flex; justify-content:space-between; margin-bottom:5px; font-size:0.8rem; font-weight:700">
                            <span>جاري حفظ البيانات...</span>
                            <span id="percent_text">0%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" id="bar_fill"></div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- العمود الأيسر الصغير -->
            <aside>
                <div class="editor-card">
                    <div class="card-title">⚙️ الضبط</div>
                    
                    <div class="field-group">
                        <label class="field-label">اسم القناة/المصدر</label>
                        <input type="text" name="channel_name" value="{{ $content->channel_name }}" class="input-style">
                    </div>

                    <div class="field-group">
                        <label class="field-label">الترتيب</label>
                        <input type="number" name="order" value="{{ $content->order }}" class="input-style">
                    </div>

                    <div class="field-group">
                        <label class="field-label">حجم الملف</label>
                        <input type="text" name="file_size" value="{{ $content->file_size }}" class="input-style">
                    </div>

                    <button type="button" onclick="handleUpdate()" id="submitBtn" class="btn-submit">حفظ التغييرات</button>
                </div>
                
                <p style="text-align:center; font-size:0.8rem; color:#94a3b8">آخر تحديث: {{ $content->updated_at->format('d/m/Y') }}</p>
            </aside>

        </div>
    </form>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // منطق اختيار المواد بناء على المرحلة
    const stageData = @json($stages);
    document.getElementById('stage_select').addEventListener('change', function() {
        const subSel = document.getElementById('subject_select');
        subSel.innerHTML = '<option value="">اختر المادة...</option>';
        const stage = stageData.find(s => s.id == this.value);
        if (stage && stage.subjects) {
            stage.subjects.forEach(sub => {
                subSel.innerHTML += `<option value="${sub.id}">${sub.name_ar}</option>`;
            });
        }
    });

    // إرسال البيانات
    function handleUpdate() {
        const btn = document.getElementById('submitBtn');
        const form = document.getElementById('editForm');
        const progressBox = document.getElementById('progress_box');
        const barFill = document.getElementById('bar_fill');
        const percentText = document.getElementById('percent_text');

        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerText = 'يرجى الانتظار...';
        progressBox.style.display = 'block';

        axios.post("{{ route('educational_contents.update', $content->id) }}", formData, {
            onUploadProgress: (p) => {
                let percent = Math.round((p.loaded * 100) / p.total);
                barFill.style.width = percent + '%';
                percentText.innerText = percent + '%';
            }
        })
        .then(res => {
            Swal.fire({ icon: 'success', title: 'تم التحديث بنجاح!', showConfirmButton: false, timer: 1500 })
            .then(() => window.location.href = "{{ route('educational_contents.index') }}");
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerText = 'حفظ التغييرات';
            progressBox.style.display = 'none';
            Swal.fire({ icon: 'error', title: 'خطأ في الرفع', text: 'تأكد من الحقول وحجم الملفات' });
        });
    }
</script>
@endsection