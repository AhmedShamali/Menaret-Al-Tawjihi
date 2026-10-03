@extends('layouts.app')

@section('title', 'إضافة محتوى تعليمي جديد')

@section('content')
<div class="content-wrapper">

    <div class="page-header">
        <div>
            <h1 class="page-title">➕ إضافة محتوى تعليمي جديد</h1>
            <p class="page-subtitle">يمكنك إضافة فيديو، ملف PDF، أو كلاهما معاً للدرس بضغطة واحدة، أو استخدام الواجهات المخصصة أدناه</p>
        </div>
        <a href="{{ route('teacher.educational_contents.index') }}" class="btn-secondary-custom">{{ __('إلغاء والعودة') }}</a>
    </div>

    <!-- بطاقات الوصول المباشر للواجهات المتخصصة -->
    <div class="quick-mode-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px; margin-bottom: 25px;">
        <div style="background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1.5px solid #bfdbfe; border-radius: 16px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 14px; box-shadow: 0 4px 12px rgba(37,99,235,0.06);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #1e40af; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                    <i class="fa-solid fa-video"></i>
                </div>
                <div>
                    <strong style="display: block; color: #1e3a8a; font-size: 0.95rem; margin-bottom: 2px;">{{ __('واجهة مخصصة للفيديوهات فقط') }}</strong>
                    <span style="color: #3b82f6; font-size: 0.8rem;">{{ __('رفع وتنظيم دروس الفيديو عالية الدقة مع دعم المشاهدة الأوفلاين') }}</span>
                </div>
            </div>
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.videos') : route('teacher.videos') }}" style="background: #1e40af; color: #fff; text-decoration: none; padding: 9px 15px; border-radius: 10px; font-weight: 700; font-size: 0.82rem; white-space: nowrap;">
                {{ __('استوديو الفيديوهات 🚀') }}
            </a>
        </div>

        <div style="background: linear-gradient(135deg, #fef2f2, #fee2e2); border: 1.5px solid #fecaca; border-radius: 16px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 14px; box-shadow: 0 4px 12px rgba(220,38,38,0.06);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #dc2626; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                    <i class="fa-solid fa-file-pdf"></i>
                </div>
                <div>
                    <strong style="display: block; color: #991b1b; font-size: 0.95rem; margin-bottom: 2px;">{{ __('واجهة مخصصة للملازم والدوسيات') }}</strong>
                    <span style="color: #ef4444; font-size: 0.8rem;">{{ __('رفع وتصنيف ملفات PDF والدوسيات وأوراق العمل') }}</span>
                </div>
            </div>
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.files') : route('teacher.files') }}" style="background: #dc2626; color: #fff; text-decoration: none; padding: 9px 15px; border-radius: 10px; font-weight: 700; font-size: 0.82rem; white-space: nowrap;">
                {{ __('مستودع الدوسيات 📚') }}
            </a>
        </div>
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
                        <input type="text" name="title" class="f-input" placeholder="{{ __('مثال: الدرس الثالث - الفيزياء') }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="f-label">{{ __('المرحلة الدراسية') }}<span class="required">*</span></label>
                            <select id="stage_select" class="f-input">
                                <option value="">{{ __('اختر المرحلة...') }}</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->label_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="f-label">{{ __('المادة الدراسية') }}<span class="required">*</span></label>
                            <select name="subject_id" id="subject_select" class="f-input" required disabled>
                                <option value="">{{ __('اختر المرحلة أولاً...') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- توجيه المحتوى حسب المنطقة التعليمية (غزة / الضفة) -->
                    <div class="form-group" style="margin-top: 8px;">
                        <label class="f-label" style="display: flex; justify-content: space-between; align-items: center;">
                            <span>{{ __('الجمهور والمنهاج المستهدف') }} <span class="required">*</span></span>
                            <small style="color: #64748b; font-weight: normal;">{{ __('يحدد من يظهر له هذا الدرس من الطلبة المسجلين') }}</small>
                        </label>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 6px;">
                            <label style="cursor: pointer; margin: 0;">
                                <input type="radio" name="target_region" value="gaza" style="display: none;" onchange="updateCreateRegionUI(this)">
                                <div id="cre_card_gaza" style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 10px 8px; text-align: center; background: #fff; transition: all 0.2s;">
                                    <div style="font-size: 1.25rem; margin-bottom: 2px;">🌿</div>
                                    <strong style="display: block; font-size: 0.85rem; color: #065f46;">{{ __('قطاع غزة') }}</strong>
                                    <small style="font-size: 0.72rem; color: #64748b;">{{ __('لطلبة غزة فقط') }}</small>
                                </div>
                            </label>
                            <label style="cursor: pointer; margin: 0;">
                                <input type="radio" name="target_region" value="west_bank" style="display: none;" onchange="updateCreateRegionUI(this)">
                                <div id="cre_card_west_bank" style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 10px 8px; text-align: center; background: #fff; transition: all 0.2s;">
                                    <div style="font-size: 1.25rem; margin-bottom: 2px;">🏛️</div>
                                    <strong style="display: block; font-size: 0.85rem; color: #1e40af;">{{ __('الضفة والقدس') }}</strong>
                                    <small style="font-size: 0.72rem; color: #64748b;">{{ __('لطلبة الضفة فقط') }}</small>
                                </div>
                            </label>
                            <label style="cursor: pointer; margin: 0;">
                                <input type="radio" name="target_region" value="all" checked style="display: none;" onchange="updateCreateRegionUI(this)">
                                <div id="cre_card_all" style="border: 2px solid #2563eb; border-radius: 12px; padding: 10px 8px; text-align: center; background: #eff6ff; transition: all 0.2s;">
                                    <div style="font-size: 1.25rem; margin-bottom: 2px;">🌐</div>
                                    <strong style="display: block; font-size: 0.85rem; color: #1e3a8a;">{{ __('منهاج مشترك') }}</strong>
                                    <small style="font-size: 0.72rem; color: #3b82f6;">{{ __('لكافة طلبة الوطن') }}</small>
                                </div>
                            </label>
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
                                    <strong>{{ __('فيديو تعليمي (MP4)') }}</strong>
                                    <small>{{ __('رفع محلي مباشر (يدعم الأوفلاين ⚡)') }}</small>
                                </div>
                            </div>
                        </label>

                        <label class="selector-card" id="card_pdf">
                            <input type="checkbox" id="check_pdf" onchange="toggleAttachmentSections()">
                            <div class="selector-content">
                                <span class="icon">📄</span>
                                <div>
                                    <strong>ملف مرفق (PDF)</strong>
                                    <small>{{ __('ملف ملخص، واجب، أو كتاب') }}</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div id="video_section" class="attachment-box" style="display: none;">
                        <h4 class="box-title">
                            <i class="fa-solid fa-cloud-arrow-up" style="color: #2563eb;"></i> 
                            {{ __('رفع ملف الفيديو محلياً (MP4 / WebM)') }}
                            <span style="font-size: 0.75rem; background: #ecfdf5; color: #059669; padding: 2px 8px; border-radius: 6px; margin-inline-start: 8px;">⚡ جاهز للأوفلاين</span>
                        </h4>
                        
                        <div class="form-group">
                            <label class="f-label">{{ __('ملف الفيديو المعتمد') }} *</label>
                            <input type="file" name="video_file" id="input_video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-m4v" class="f-input" style="padding: 10px; background: #ffffff;">
                            <small style="color: #166534; font-weight: 600; display: block; margin-top: 6px;">
                                <i class="fa-solid fa-circle-check"></i> {{ __('يتم رفع الملف بنظام التجزئة السريع ودعمه تلقائياً للتحميل والمشاهدة بدون إنترنت للطلبة داخل تطبيق المنصة.') }}
                            </small>
                        </div>
                    </div>

                    <!-- تفاصيل الملف المرفق -->
                    <div id="pdf_section" class="attachment-box" style="display: none;">
                        <h4 class="box-title">📑 ملف المادة المرفق (كافة الصيغ)</h4>
                        <div class="form-group">
                            <label class="f-label">رفع ملف المستند أو الملزمة (PDF, Word, Excel, PowerPoint, صور, أرشيف)</label>
                            <input type="file" name="file_upload_pdf" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.png,.jpg,.jpeg,.webp,.zip,.rar,.txt" class="f-input file-input">
                            <small style="color: var(--ed-text-muted, #64748b);">{{ __('يدعم كافة الامتدادات التعليمية حتى 100 ميجابايت.') }}</small>
                        </div>
                        <div class="form-group">
                            <label class="f-label">{{ __('أو رابط ملف خارجي سحابي مباشر') }}</label>
                            <input type="text" name="pdf_url" class="f-input" placeholder="https://...">
                        </div>
                    </div>

                    <!-- شريط التقدم للرفع المباشر بالأجزاء للملفات الضخمة بالجيجابايت -->
                    <div id="upload_progress_container" class="progress-box" style="display: none; margin-top: 14px; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 14px 16px;">
                        <div class="progress-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span id="progress_status_text" style="font-size: 0.86rem; font-weight: 800; color: #1e40af; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <span>جاري بدء تجهيز ورفع أجزاء الفيديو...</span>
                            </span>
                            <span id="progress_percent_text" style="font-size: 0.95rem; font-weight: 900; color: #1e3a8a; font-family: monospace;">0%</span>
                        </div>
                        <div class="progress-bar-bg" style="width: 100%; height: 10px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                            <div id="progress_bar_fill" class="progress-bar-fill" style="width: 0%; height: 100%; background: linear-gradient(90deg, #2563eb, #3b82f6, #059669); transition: width 0.2s ease;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px; font-size: 0.76rem; color: #64748b; flex-wrap: wrap; gap: 6px;">
                            <span id="progress_file_meta" style="font-weight: 700; color: #334155;">-- / --</span>
                            <span id="progress_speed_meta" style="font-weight: 700; color: #059669;"><i class="fa-solid fa-gauge-high"></i> --</span>
                            <span id="progress_eta_meta" style="font-weight: 700; color: #d97706;"><i class="fa-solid fa-clock"></i> --</span>
                            <span id="progress_part_meta" style="font-weight: 700; color: #64748b;">--</span>
                        </div>
                    </div>

                </div>

            </div>

            <div class="side-column">
                <div class="glass-card">
                    <h3 class="card-title">⚙️ تفاصيل إضافية</h3>
                    <div class="form-group">
                        <label class="f-label">اسم القناة / المصدر</label>
                        <input type="text" name="channel_name" class="f-input" placeholder="{{ __('مثال: أ. معتز اسليم') }}">
                    </div>
                    <div class="form-group">
                        <label class="f-label">{{ __('ترتيب الدرس') }}</label>
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
<script src="{{ asset('js/resumable-uploader.js') }}"></script>

<script>
    const stages = @json($stages);

    document.getElementById('stage_select').addEventListener('change', function() {
        const subSel = document.getElementById('subject_select');
        subSel.innerHTML = '<option value="">{{ __('اختر المادة...') }}</option>';
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

    function updateCreateRegionUI(radio) {
        ['cre_card_gaza', 'cre_card_west_bank', 'cre_card_all'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.borderColor = '#e2e8f0';
                el.style.background = '#ffffff';
            }
        });
        if (radio.value === 'gaza') {
            const c = document.getElementById('cre_card_gaza');
            if (c) { c.style.borderColor = '#059669'; c.style.background = '#ecfdf5'; }
        } else if (radio.value === 'west_bank') {
            const c = document.getElementById('cre_card_west_bank');
            if (c) { c.style.borderColor = '#1e40af'; c.style.background = '#eff6ff'; }
        } else {
            const c = document.getElementById('cre_card_all');
            if (c) { c.style.borderColor = '#2563eb'; c.style.background = '#eff6ff'; }
        }
    }

    function toggleAttachmentSections() {
        const videoChecked = document.getElementById('check_video').checked;
        const pdfChecked = document.getElementById('check_pdf').checked;

        document.getElementById('video_section').style.display = videoChecked ? 'block' : 'none';
        document.getElementById('card_video').classList.toggle('selected', videoChecked);

        document.getElementById('pdf_section').style.display = pdfChecked ? 'block' : 'none';
        document.getElementById('card_pdf').classList.toggle('selected', pdfChecked);
    }

    let activeUploader = null;

    async function submitContent() {
        const videoChecked = document.getElementById('check_video').checked;
        const pdfChecked = document.getElementById('check_pdf').checked;
        const videoFileInput = document.getElementById('input_video_file');

        if (!videoChecked && !pdfChecked) {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'يرجى اختيار مرفق واحد على الأقل (فيديو أو ملف) قبل الحفظ!',
            });
            return;
        }

        if (videoChecked && (!videoFileInput || !videoFileInput.files || videoFileInput.files.length === 0)) {
            Swal.fire({
                icon: 'warning',
                title: 'ملف الفيديو مطلوب',
                text: 'يرجى اختيار ملف الفيديو (MP4 / WebM) لرفعه للطلبة.',
            });
            return;
        }

        const btn = document.getElementById('saveBtn');
        const originalText = btn.innerHTML;
        const form = document.getElementById('createForm');

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري فحص وتجهيز الرفع بالجيجاوات...';

        const progressContainer = document.getElementById('upload_progress_container');
        const progressBarFill = document.getElementById('progress_bar_fill');
        const progressPercentText = document.getElementById('progress_percent_text');
        const progressStatusText = document.getElementById('progress_status_text');
        const progressFileMeta = document.getElementById('progress_file_meta');
        const progressSpeedMeta = document.getElementById('progress_speed_meta');
        const progressEtaMeta = document.getElementById('progress_eta_meta');
        const progressPartMeta = document.getElementById('progress_part_meta');

        progressContainer.style.display = 'block';

        const preventTabClose = (ev) => {
            ev.preventDefault();
            ev.returnValue = 'جاري رفع فيديو بالخلفية، هل أنت متأكد من مغادرة الصفحة وإلغاء الرفع؟';
        };
        window.addEventListener('beforeunload', preventTabClose);

        let uploadedVideoPath = null;
        let formattedVideoSize = null;

        try {
            // رفع الفيديو بنظام التجزئة والاستئناف التلقائي فائق الاستقرار عند انقطاع الإنترنت
            if (videoChecked && videoFileInput.files.length > 0) {
                const file = videoFileInput.files[0];
                
                activeUploader = new ResumableUploader({
                    chunkUrl: "{{ Route::has('educational_contents.upload_chunk') ? route('educational_contents.upload_chunk') : url('/educational-contents/upload-chunk') }}",
                    checkStatusUrl: "{{ Route::has('educational_contents.check_chunk_status') ? route('educational_contents.check_chunk_status') : url('/educational-contents/check-chunk-status') }}",
                    pingUrl: "{{ route('system.ping') }}",
                    csrfToken: '{{ csrf_token() }}',
                    onProgress: (pct) => {
                        const scaledPct = Math.round(pct * 0.90);
                        progressBarFill.style.width = scaledPct + '%';
                        progressPercentText.textContent = scaledPct + '%';
                    },
                    onStatus: (status) => {
                        progressStatusText.innerHTML = status.html;
                    },
                    onSpeed: (speed) => {
                        if (progressSpeedMeta) progressSpeedMeta.innerHTML = `<i class="fa-solid fa-gauge-high"></i> ` + speed;
                    },
                    onEta: (eta) => {
                        if (progressEtaMeta) progressEtaMeta.innerHTML = `<i class="fa-solid fa-clock"></i> ` + eta;
                    },
                    onMeta: (meta) => {
                        if (progressFileMeta) progressFileMeta.textContent = meta;
                    },
                    onPart: (part) => {
                        if (progressPartMeta) progressPartMeta.textContent = part;
                    },
                    onNetworkStateChange: (isOnline, pct) => {
                        if (!isOnline) {
                            progressBarFill.style.background = 'linear-gradient(90deg, #d97706, #f59e0b)';
                            btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation fa-beat"></i> الرفع معلّق (بانتظار الإنترنت)...';
                        } else {
                            progressBarFill.style.background = 'linear-gradient(90deg, #2563eb, #3b82f6, #059669)';
                            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري استئناف الرفع...';
                        }
                    }
                });

                const uploadRes = await activeUploader.upload(file);
                uploadedVideoPath = uploadRes.uploaded_video_path;
                formattedVideoSize = uploadRes.formatted_size;
            }

            // إرسال النموذج وحفظ المحتوى
            progressStatusText.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #10b981;"></i> اكتمل رفع وحفظ الفيديو بنجاح! جاري تثبيت الدرس... 🚀';
            progressBarFill.style.width = '95%';
            progressPercentText.textContent = '95%';

            const formData = new FormData(form);
            formData.delete('video_file');

            let contentType = 'video';
            if (pdfChecked && !videoChecked) {
                contentType = 'file';
            } else if (videoChecked && pdfChecked) {
                contentType = 'both';
            }
            formData.append('type', contentType);

            if (uploadedVideoPath) {
                formData.append('uploaded_video_path', uploadedVideoPath);
                if (formattedVideoSize) {
                    formData.append('formatted_size', formattedVideoSize);
                }
            }

            const storeUrl = "{{ auth()->user()->role === 'admin' ? route('admin.educational_contents.store') : route('teacher.educational_contents.store') }}";
            const response = await axios.post(storeUrl, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            window.removeEventListener('beforeunload', preventTabClose);
            progressBarFill.style.width = '100%';
            progressPercentText.textContent = '100%';

            Swal.fire({
                icon: response.data.icon || 'success',
                title: response.data.title || 'تم حفظ ونشر المحتوى بنجاح! 🎉',
                text: 'أصبح المحتوى متاحاً للطلبة ويدعم البث والتشغيل أوفلاين.',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = "{{ auth()->user()->role === 'admin' ? route('admin.educational_contents.index') : route('teacher.educational_contents.index') }}";
            });

        } catch (error) {
            window.removeEventListener('beforeunload', preventTabClose);
            let errorMsg = 'حدث خطأ أثناء حفظ المحتوى أو رفع الفيديو';
            if (error.response && error.response.data) {
                errorMsg = error.response.data.title || error.response.data.message || error.response.data.error || errorMsg;
            } else if (error.message) {
                errorMsg = error.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'خطأ في الرفع',
                html: errorMsg + '<br><small style="color: #64748b; display: block; margin-top: 8px;">ملاحظة: يمكنك إعادة المحاولة وسيتم استئناف الأجزاء المتبقية تلقائياً دون إعادة رفع ما اكتمل.</small>'
            });
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
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
