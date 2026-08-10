@extends('layouts.app')

@section('title', 'بناء ملف مدرس')

@section('content')
<div class="teacher-builder-wrapper">

    {{-- رأس الصفحة والمسار --}}
    <div class="page-header">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> الرئيسية</a>
            <span class="sep"><i class="fa-solid fa-chevron-left"></i></span>
            <span>إدارة الكادر</span>
            <span class="sep"><i class="fa-solid fa-chevron-left"></i></span>
            <span class="current">إضافة مدرس</span>
        </nav>
        <div class="header-title-group">
            <h1 class="page-title">
                إنشاء ملف أكاديمي متكامل
                <span class="title-badge"><i class="fa-solid fa-sparkles"></i> جديد</span>
            </h1>
            <p class="page-subtitle">قم بتعبئة بيانات المعلم لإسناد المواد وإتاحة الحساب على المنصة مباشرة</p>
        </div>
    </div>

    <form id="fullTeacherForm" enctype="multipart/form-data" onsubmit="event.preventDefault();">
        @csrf
        <div class="main-layout-grid">

            {{-- العمود الأيمن: البيانات التفصيلية --}}
            <div class="content-column">

                <!-- كرت البيانات الأساسية -->
                <div class="uni-card">
                    <div class="uni-card-header">
                        <div class="header-icon primary-bg">
                            <i class="fa-solid fa-address-card"></i>
                        </div>
                        <div>
                            <h3>المعلومات الأساسية والتواصل</h3>
                            <span class="header-desc">البيانات التعريفية الخاصة بحساب المدرس</span>
                        </div>
                    </div>
                    <div class="uni-card-body">
                        <div class="form-grid-2">
                            <div class="f-group">
                                <label>الاسم الرباعي للمدرس <span class="req">*</span></label>
                                <div class="input-icon-wrapper">
                                    <i class="fa-regular fa-user icon"></i>
                                    <input type="text" name="name" placeholder="أدخل الاسم الكامل" class="uni-input" required>
                                </div>
                            </div>
                            <div class="f-group">
                                <label>رقم الجوال / واتساب</label>
                                <div class="input-icon-wrapper">
                                    <i class="fa-solid fa-phone icon"></i>
                                    <input type="tel" name="phone" placeholder="059xxxxxxx" class="uni-input">
                                </div>
                            </div>
                            <div class="f-group">
                                <label>الدرجة العلمية / التخصص</label>
                                <div class="input-icon-wrapper">
                                    <i class="fa-solid fa-user-graduate icon"></i>
                                    <input type="text" name="major" placeholder="مثلاً: بكالوريوس تربية رياضية" class="uni-input">
                                </div>
                            </div>
                            <div class="f-group">
                                <label>البريد الإلكتروني (الدخول) <span class="req">*</span></label>
                                <div class="input-icon-wrapper">
                                    <i class="fa-regular fa-envelope icon"></i>
                                    <input type="email" name="email" placeholder="teacher@jesr.ps" class="uni-input" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- كرت النبذة المهنية -->
                <div class="uni-card">
                    <div class="uni-card-header">
                        <div class="header-icon warning-bg">
                            <i class="fa-solid fa-pen-nib"></i>
                        </div>
                        <div>
                            <h3>النبذة التعريفية (Bio)</h3>
                            <span class="header-desc">سيرة ذاتية مختصرة تظهر في ملفه أمام الطلاب</span>
                        </div>
                    </div>
                    <div class="uni-card-body">
                        <div class="f-group">
                            <textarea name="bio" rows="4" class="uni-input uni-textarea" placeholder="اكتب نبذة مختصرة عن الخبرات والمهارات لتظهر للطلاب..."></textarea>
                        </div>
                    </div>
                </div>

            </div>

            {{-- العمود الأيسر: الإعدادات والصورة --}}
            <aside class="sidebar-column">

                <!-- كرت الصورة الشخصية -->
                <div class="uni-card">
                    <div class="uni-card-header">
                        <div class="header-icon info-bg">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                        <div>
                            <h3>الصورة الشخصية</h3>
                            <span class="header-desc">صورة الملف الشخصي للمدرس</span>
                        </div>
                    </div>
                    <div class="uni-card-body text-center">
                        <div class="photo-upload-zone" id="uploadZone">
                            <input type="file" name="photo" id="teacher_photo" accept="image/*" hidden onchange="previewImg(this)">
                            <label for="teacher_photo" id="photo_label">
                                <div class="preview-circle">
                                    <i class="fa-solid fa-cloud-arrow-up cloud-icon"></i>
                                </div>
                                <span class="upload-title">انقر لرفع صورة أو اسحبها هنا</span>
                                <span class="upload-hint">PNG, JPG, WEBP حتى 5MB</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- كرت التخصيص الأكاديمي -->
                <div class="uni-card accent-border">
                    <div class="uni-card-header">
                        <div class="header-icon accent-bg">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h3>المساق الدراسي</h3>
                            <span class="header-desc">إسناد الصف والمادة الأكاديمية</span>
                        </div>
                    </div>
                    <div class="uni-card-body">
                        <div class="f-group mb-20">
                            <label>اختر الصف / المرحلة <span class="req">*</span></label>
                            <div class="select-wrapper">
                                <select id="stage_select" class="uni-select" required>
                                    <option value="" selected disabled>اختر المرحلة...</option>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->label_ar }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>
                        <div class="f-group">
                            <label>اختر المادة المسندة <span class="req">*</span></label>
                            <div class="select-wrapper">
                                <select name="subject_id" id="subject_select" class="uni-select" disabled required>
                                    <option value="">اختر الصف أولاً...</option>
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- كرت كلمة المرور -->
                <div class="uni-card">
                    <div class="uni-card-header">
                        <div class="header-icon danger-bg">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h3>الأمان والدخول</h3>
                            <span class="header-desc">كلمة المرور الابتدائية للحساب</span>
                        </div>
                    </div>
                    <div class="uni-card-body">
                        <div class="f-group">
                            <label>كلمة المرور المؤقتة <span class="req">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-key icon"></i>
                                <input type="password" name="password" placeholder="••••••••" class="uni-input" required>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="saveTeacherProfile()" id="saveBtn" class="uni-btn-submit">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>اعتماد ونشر ملف المدرس</span>
                </button>

            </aside>

        </div>
    </form>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --primary-color: #6366f1;
        --primary-hover: #4f46e5;
        --primary-light: #eeefef;
        --accent-color: #06b6d4;
        --bg-body: #f8fafc;
        --bg-card: #ffffff;
        --bg-input: #f8fafc;
        --border-color: #e2e8f0;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --radius-lg: 18px;
        --radius-md: 12px;
        --shadow-card: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        --shadow-hover: 0 12px 30px -4px rgba(99, 102, 241, 0.12);
        --shadow-button: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
    }

    .teacher-builder-wrapper {
        padding-bottom: 60px;
        animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* رأس الصفحة */
    .page-header {
        margin-bottom: 28px;
        padding-bottom: 16px;
    }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 12px;
        font-weight: 600;
    }

    .breadcrumb-nav a {
        color: var(--text-muted);
        text-decoration: none;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .breadcrumb-nav a:hover { color: var(--primary-color); }
    .breadcrumb-nav .sep { font-size: 0.65rem; color: #cbd5e1; }
    .breadcrumb-nav .current { color: var(--primary-color); font-weight: 700; }

    .page-title {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 0 6px 0;
        letter-spacing: -0.5px;
    }

    .title-badge {
        font-size: 0.75rem;
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: var(--primary-hover);
        padding: 4px 12px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .page-subtitle { color: var(--text-muted); font-size: 0.95rem; margin: 0; }

    /* تخطيط الصفحة الشبكي */
    .main-layout-grid {
        display: grid;
        grid-template-columns: 1fr 370px;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 1024px) {
        .main-layout-grid { grid-template-columns: 1fr; }
    }

    .content-column, .sidebar-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* البطاقات */
    .uni-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        transition: all 0.25s ease;
    }

    .uni-card:hover {
        box-shadow: var(--shadow-hover);
        border-color: #cbd5e1;
    }

    .uni-card.accent-border {
        border-top: 4px solid var(--accent-color);
    }

    .uni-card-header {
        padding: 20px 24px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .header-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .primary-bg { background: #e0e7ff; color: var(--primary-hover); }
    .warning-bg { background: #fef3c7; color: #d97706; }
    .info-bg { background: #e0f2fe; color: #0284c7; }
    .accent-bg { background: #cff4fc; color: #055160; }
    .danger-bg { background: #ffe4e6; color: #e11d48; }

    .uni-card-header h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }

    .header-desc {
        display: block;
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .uni-card-body { padding: 24px; }

    /* شبكة الحقول */
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 640px) {
        .form-grid-2 { grid-template-columns: 1fr; }
    }

    .f-group { display: flex; flex-direction: column; }
    .f-group.mb-20 { margin-bottom: 20px; }

    .f-group label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }

    .req { color: #ef4444; }

    /* تخصيص مدخلات البيانات بالأيقونات */
    .input-icon-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon-wrapper .icon {
        position: absolute;
        right: 14px;
        color: #94a3b8;
        font-size: 0.95rem;
        transition: color 0.2s;
        pointer-events: none;
    }

    .uni-input {
        width: 100%;
        padding: 12px 42px 12px 16px;
        border-radius: var(--radius-md);
        border: 1.5px solid var(--border-color);
        background: var(--bg-input);
        color: var(--text-main);
        font-family: inherit;
        font-size: 0.9rem;
        font-weight: 500;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .uni-input:focus {
        border-color: var(--primary-color);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
    }

    .uni-input:focus + .icon, .input-icon-wrapper:focus-within .icon {
        color: var(--primary-color);
    }

    .uni-textarea {
        padding: 14px 16px;
        resize: vertical;
        min-height: 110px;
    }

    /* القوائم المنسدلة */
    .select-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .uni-select {
        width: 100%;
        padding: 12px 16px;
        border-radius: var(--radius-md);
        border: 1.5px solid var(--border-color);
        background: var(--bg-input);
        color: var(--text-main);
        font-size: 0.9rem;
        font-weight: 500;
        outline: none;
        cursor: pointer;
        appearance: none;
        transition: all 0.2s ease;
    }

    .uni-select:focus {
        border-color: var(--primary-color);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
    }

    .select-arrow {
        position: absolute;
        left: 14px;
        font-size: 0.8rem;
        color: #94a3b8;
        pointer-events: none;
    }

    /* منطقة رفع الصورة التفاعلية */
    .photo-upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: var(--radius-lg);
        padding: 24px 16px;
        cursor: pointer;
        transition: all 0.25s ease;
        background: #f8fafc;
        position: relative;
    }

    .photo-upload-zone:hover {
        border-color: var(--primary-color);
        background: #f0f3ff;
    }

    .photo-upload-zone label {
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .preview-circle {
        width: 86px;
        height: 86px;
        background: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        color: var(--primary-color);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        border: 3px solid #ffffff;
        outline: 2px solid var(--border-color);
        transition: all 0.2s;
    }

    .preview-circle .cloud-icon { font-size: 2rem; }

    .preview-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .upload-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .upload-hint {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 4px;
    }

    /* زر الحفظ */
    .uni-btn-submit {
        width: 100%;
        padding: 16px;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        color: #ffffff;
        border: none;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: var(--shadow-button);
        transition: all 0.25s ease;
    }

    .uni-btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px -5px rgba(99, 102, 241, 0.5);
    }

    .uni-btn-submit:active { transform: translateY(0); }

    .text-center { text-align: center; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    const stagesData = @json($stages);

    document.getElementById('stage_select').onchange = function() {
        const subPicker = document.getElementById('subject_select');
        subPicker.innerHTML = '<option value="" selected disabled>اختر المادة...</option>';

        const selected = stagesData.find(s => s.id == this.value);
        if(selected && selected.subjects && selected.subjects.length > 0) {
            selected.subjects.forEach(sub => {
                subPicker.innerHTML += `<option value="${sub.id}">${sub.name_ar}</option>`;
            });
            subPicker.disabled = false;
        } else {
            subPicker.innerHTML = '<option value="">لا توجد مواد لهذه المرحلة</option>';
            subPicker.disabled = true;
        }
    };

    function previewImg(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.preview-circle').innerHTML = `<img src="${e.target.result}">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function saveTeacherProfile() {
        const btn = document.getElementById('saveBtn');
        const form = document.getElementById('fullTeacherForm');

        // التحقق المبدئي من صحة مدخلات النموذج قبل الإرسال
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>جاري اعتماد الحساب...</span>';

        axios.post("{{ route('admin.teachers.store') }}", formData)
            .then(res => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم إنشاء الملف بنجاح!',
                    text: 'تمت إتاحة حساب المدرس وإسناد المادة له.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.href = "{{ route('admin.dashboard') }}";
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'فشل الحفظ!',
                    text: err.response?.data?.message || 'يرجى مراجعة كافة البيانات المطلوبة والإدخال بشكل صحيح.',
                });
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>اعتماد ونشر ملف المدرس</span>';
            });
    }
</script>
@endsection
