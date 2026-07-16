@extends('layouts.app')

@section('title', 'نشر محتوى ذكي')

@section('content')
<!-- المكتبات الضرورية -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div style="max-width: 900px; margin: 0 auto; padding-bottom: 50px;">
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: #1e293b;">نشر محتوى جديد 🎓</h1>
        <p style="color: #64748b;">ارفع ملفاتك وتابع عملية الرفع لحظة بلحظة.</p>
    </div>

    <form id="uploadForm">
        @csrf
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px;">
            <!-- القسم الأيمن -->
            <div style="display: flex; flex-direction: column; gap: 25px;">
                <div class="glass-card">
                    <h3 style="margin-bottom: 25px; font-size: 1.2rem;">📦 بيانات المحتوى</h3>

                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <!-- المرحلة والمادة -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label class="f-label">المرحلة</label>
                                <select id="stage_select" class="f-input" name="stage_select">
                                    <option value="">اختر الصف...</option>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->label_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="f-label">المادة</label>
                                <select name="subject_id" id="subject_select" class="f-input" disabled>
                                    <option value="">اختر الصف أولاً</option>
                                </select>
                            </div>
                        </div>

                        <!-- العنوان -->
                        <div>
                            <label class="f-label">العنوان</label>
                            <input type="text" id="title" placeholder="عنوان الدرس..." class="f-input">
                        </div>

                        <!-- تبديل النوع -->
                        <div class="type-switcher">
                            <label class="sw-btn">
                                <input type="radio" name="type" value="video" checked hidden>
                                <div class="sw-design">🎥 فيديو</div>
                            </label>
                            <label class="sw-btn">
                                <input type="radio" name="type" value="file" hidden>
                                <div class="sw-design">📄 ملف PDF</div>
                            </label>
                        </div>

                        <!-- حقول الفيديو -->
                        <div id="video_fields">
                            <label class="f-label">مصدر الفيديو</label>
                            <select id="upload_method" class="f-input" style="margin-bottom: 15px;">
                                <option value="link">🔗 رابط (YouTube/Drive)</option>
                                <option value="local">📤 رفع فيديو من الجهاز</option>
                            </select>
                            <input type="text" id="url_path" placeholder="ضع الرابط هنا..." class="f-input">
                            <input type="file" id="file_upload_video" class="f-input" style="display: none; background: #fff;">
                        </div>

                        <!-- حقول الملف -->
                        <div id="file_fields" style="display: none;">
                            <label class="f-label">ارفاق PDF</label>
                            <input type="file" id="file_upload_pdf" accept=".pdf" class="f-input" style="background: #fff;">
                        </div>

                        <!-- شريط التقدم -->
                        <div id="progress_wrapper" style="display: none;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span id="progress_status" style="font-size: 0.85rem; font-weight: 700; color: #2563eb;">جاري الرفع...</span>
                                <span id="progress_percent" style="font-size: 0.85rem; font-weight: 800;">0%</span>
                            </div>
                            <div class="progress-container">
                                <div id="progress_bar" class="progress-fill"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- القسم الأيسر -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="glass-card" style="padding: 25px;">
                    <label class="f-label">اسم القناة</label>
                    <input type="text" id="channel_name" class="f-input" style="margin-bottom: 15px;">

                    <label class="f-label">حجم الملف</label>
                    <input type="text" id="file_size" class="f-input" style="margin-bottom: 15px;">

                    <label class="f-label">الترتيب</label>
                    <input type="number" id="order" value="1" class="f-input">
                </div>

                <button type="button" onclick="prformStore()" id="submitBtn" class="submit-btn">
                    نشر المحتوى الآن ✅
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .glass-card { background: white; padding: 30px; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .f-label { display: block; font-weight: 700; font-size: 0.85rem; color: #475569; margin-bottom: 8px; }
    .f-input { width: 100%; padding: 12px; border-radius: 12px; border: 2px solid #f1f5f9; background: #f8fafc; font-family: inherit; }
    .type-switcher { background: #f1f5f9; padding: 6px; border-radius: 12px; display: flex; gap: 5px; }
    .sw-btn { flex: 1; cursor: pointer; }
    .sw-design { padding: 10px; text-align: center; border-radius: 10px; font-weight: 700; color: #64748b; transition: 0.3s; }
    .sw-btn input:checked + .sw-design { background: white; color: #2563eb; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .progress-container { height: 12px; background: #e2e8f0; border-radius: 10px; overflow: hidden; }
    .progress-fill { width: 0%; height: 100%; background: #2563eb; transition: width 0.2s; }
    .submit-btn { width: 100%; padding: 18px; font-size: 1.1rem; border-radius: 15px; background: #2563eb; color: white; border: none; cursor: pointer; font-weight: 700; }
    .submit-btn:disabled { background: #94a3b8; }
</style>

<script>
    const stages = @json($stages);

    // 1. منطق السلكت المعتمد عندك
    document.getElementById('stage_select').addEventListener('change', function () {
        const subSel = document.getElementById('subject_select');
        subSel.innerHTML = '<option value="">اختر المادة...</option>';
        if (this.value) {
            const stage = stages.find(s => s.id == this.value);
            if (stage && stage.subjects) {
                stage.subjects.forEach(s => subSel.innerHTML += `<option value="${s.id}">${s.name_ar}</option>`);
                subSel.disabled = false;
            }
        }
    });

    // 2. التبديل بين فيديو و PDF
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function () {
            document.getElementById('video_fields').style.display = this.value === 'video' ? 'block' : 'none';
            document.getElementById('file_fields').style.display = this.value === 'file' ? 'block' : 'none';
        });
    });

    // 3. طريقة الرفع (رابط أو ملف)
    document.getElementById('upload_method').addEventListener('change', function () {
        document.getElementById('url_path').style.display = this.value === 'link' ? 'block' : 'none';
        document.getElementById('file_upload_video').style.display = this.value === 'local' ? 'block' : 'none';
    });

    // ==========================================
    // 4. الدالة الأساسية prformStore
    // ==========================================
    function prformStore() {
        let formData = new FormData();

        // جلب القيم يدوياً
        formData.append('subject_id', document.getElementById('subject_select').value);
        formData.append('title', document.getElementById('title').value);
        formData.append('type', document.querySelector('input[name="type"]:checked').value);
        formData.append('channel_name', document.getElementById('channel_name').value);
        formData.append('file_size', document.getElementById('file_size').value);
        formData.append('order', document.getElementById('order').value);
        formData.append('upload_method', document.getElementById('upload_method').value);
        formData.append('url_path', document.getElementById('url_path').value);

        // إلحاق الملفات
        let videoFile = document.getElementById('file_upload_video').files[0];
        let pdfFile = document.getElementById('file_upload_pdf').files[0];
        if (videoFile) formData.append('file_upload_video', videoFile);
        if (pdfFile) formData.append('file_upload_pdf', pdfFile);

        // إظهار شريط التقدم
        document.getElementById('progress_wrapper').style.display = 'block';
        let btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerText = "جاري الرفع...";

        // الإرسال عبر Axios مع تتبع التقدم
        axios.post("{{ route('educational_contents.store') }}", formData, {
            onUploadProgress: (progressEvent) => {
                let percent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                document.getElementById('progress_bar').style.width = percent + '%';
                document.getElementById('progress_percent').innerText = percent + '%';
                if (percent === 100) document.getElementById('progress_status').innerText = "جاري المعالجة...";
            }
        })
        .then(res => {
            Swal.fire({ icon: 'success', title: res.data.tittle });
            location.reload(); // إعادة تحميل بعد النجاح
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerText = "نشر المحتوى الآن ✅";

            let msg = "الملف حجمه كبير";

            if (err.response && err.response.data && err.response.data.tittle) {
                msg = err.response.data.tittle;
            }

            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: msg
            });

            document.getElementById('progress_wrapper').style.display = 'none';
        });
    }
</script>
@endsection
