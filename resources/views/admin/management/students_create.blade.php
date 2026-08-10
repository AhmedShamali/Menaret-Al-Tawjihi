@extends('layouts.app')

@section('title', 'تسجيل إداري لطالب')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap');

    :root {
        --primary-gradient: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        --accent-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --soft-bg: #f1f5f9;
        --card-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }

    body { font-family: 'Tajawal', sans-serif; background-color: var(--soft-bg); color: #1e293b; }

    .page-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }

    .header-section { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }

    .title-badge { background: #e2e8f0; padding: 5px 15px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; color: #475569; display: inline-block; margin-bottom: 10px; }

    .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 24px; padding: 30px; border: 1px solid rgba(255, 255, 255, 0.6); box-shadow: var(--card-shadow); height: 100%; }

    .section-title { font-size: 1.15rem; font-weight: 800; color: #1e293b; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }
    .section-title i { color: #10b981; background: #ecfdf5; padding: 8px; border-radius: 10px; font-style: normal; }

    .form-label { font-weight: 700; color: #475569; margin-bottom: 8px; display: block; font-size: 0.85rem; }
    .form-label span { color: #ef4444; margin-right: 4px; } /* للنجوم الحمراء */

    .modern-input {
        width: 100%; padding: 12px 16px; border-radius: 12px; border: 2px solid #e2e8f0;
        background: #f8fafc; font-weight: 600; color: #1e293b; outline: none; transition: 0.3s;
        box-sizing: border-box; font-family: 'Tajawal', sans-serif;
    }
    .modern-input:focus { border-color: #10b981; background: #fff; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }

    .btn-submit {
        background: var(--accent-gradient); color: white; width: 100%; padding: 16px;
        border-radius: 15px; border: none; font-size: 1rem; font-weight: 800;
        cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
    }
    .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 12px 22px rgba(16, 185, 129, 0.3); }
    .btn-submit:disabled { background: #cbd5e1; cursor: not-allowed; transform: none; }

    .btn-cancel { color: #64748b; text-decoration: none; font-weight: 700; display: flex; align-items: center; gap: 5px; }

    /* تحسين صناديق الرفع */
    .upload-box {
        border: 2px dashed #cbd5e1; border-radius: 16px; padding: 15px;
        text-align: center; cursor: pointer; transition: 0.3s; background: #f8fafc;
        position: relative; overflow: hidden; min-height: 100px; display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .upload-box:hover { border-color: #10b981; background: #f0fdf4; }
    .upload-box img { max-height: 80px; border-radius: 8px; margin-bottom: 10px; display: none; }

    .main-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 30px; }

    @media (max-width: 992px) {
        .main-grid { grid-template-columns: 1fr; }
        .sidebar-sticky { position: static; }
    }
</style>

<div class="page-container">
    <div class="header-section">
        <div>
            <span class="title-badge">إدارة الأنظمة • الطلاب</span>
            <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0;">تسجيل طالب جديد <span style="color: #10b981;">.</span></h1>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn-cancel">✕ إلغاء العملية</a>
    </div>

    <form id="studentRegistrationForm">
        @csrf
        <div class="main-grid">
            <!-- العمود الأيمن: البيانات -->
            <div style="display: flex; flex-direction: column; gap: 25px;">
                <!-- البيانات الشخصية -->
                <div class="glass-card">
                    <div class="section-title"><i>👤</i> البيانات الأساسية</div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <div>
                            <label class="form-label">الاسم رباعي (بالعربية) <span>*</span></label>
                            <input type="text" name="name_ar" class="modern-input" placeholder="مثال: أحمد محمد علي" required>
                        </div>
                        <div>
                            <label class="form-label">الاسم بالإنجليزية <span>*</span></label>
                            <input type="text" name="name_en" class="modern-input" placeholder="Full Name" required style="text-align: left; direction: ltr;">
                        </div>
                        <div>
                            <label class="form-label">رقم الهوية/الجواز <span>*</span></label>
                            <input type="text" name="nid" maxlength="9" class="modern-input" placeholder="9 أرقام" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div>
                            <label class="form-label">العمر <span>*</span></label>
                            <input type="number" name="age" min="5" max="100" class="modern-input" placeholder="18" required>
                        </div>
                    </div>
                </div>

                <!-- حساب الدخول -->
                <div class="glass-card">
                    <div class="section-title"><i style="color: #3b82f6; background: #eff6ff;">🔐</i> إعدادات الحساب</div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <div style="grid-column: span 2;">
                            <label class="form-label">البريد الإلكتروني <span>*</span></label>
                            <input type="email" name="email" class="modern-input" placeholder="student@example.com" required>
                        </div>
                        <div>
                            <label class="form-label">رقم التواصل <span>*</span></label>
                            <input type="tel" name="phone" class="modern-input" placeholder="05xxxxxxxx" required>
                        </div>
                        <div>
                            <label class="form-label">كلمة المرور <span>*</span></label>
                            <input type="password" name="password" class="modern-input" placeholder="8 رموز على الأقل" required minlength="8">
                        </div>
                    </div>
                </div>
            </div>

            <!-- العمود الأيسر: الجانب -->
            <div class="sidebar-sticky" style="display: flex; flex-direction: column; gap: 25px;">
                <!-- التصنيف -->
                <div class="glass-card" style="background: var(--primary-gradient); color: white;">
                    <div class="section-title" style="color: white;"><i style="background: rgba(255,255,255,0.1); color: white;">🎓</i> التصنيف الأكاديمي</div>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div>
                            <label class="form-label" style="color: rgba(255,255,255,0.8);">المرحلة الدراسية <span>*</span></label>
                            <select name="stage_id" class="modern-input" required>
                                <option value="" selected disabled>اختر المرحلة...</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->label_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label" style="color: rgba(255,255,255,0.8);">الجنس <span>*</span></label>
                            <select name="gender" class="modern-input" required>
                                <option value="ذكر">ذكر</option>
                                <option value="أنثى">أنثى</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- المرفقات -->
                <div class="glass-card">
                    <div class="section-title"><i style="color: #f59e0b; background: #fffbeb;">📂</i> المرفقات المطلوبة</div>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div class="upload-box" id="box-photo" onclick="document.getElementById('p_file').click()">
                            <img id="preview-photo" src="" alt="preview">
                            <input type="file" name="photo" id="p_file" accept="image/*" hidden>
                            <span class="icon">📸</span>
                            <span class="text">الصورة الشخصية</span>
                        </div>

                        <div class="upload-box" id="box-id" onclick="document.getElementById('i_file').click()">
                            <img id="preview-id" src="" alt="preview">
                            <input type="file" name="id_photo" id="i_file" accept="image/*" hidden>
                            <span class="icon">🪪</span>
                            <span class="text">صورة الهوية</span>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="handleRegistration()" id="submitBtn" class="btn-submit">حفظ بيانات الطالب</button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    // وظيفة معالجة اختيار الملفات والمعاينة
    function setupFilePreview(inputId, previewId, boxId, text) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const box = document.getElementById(boxId);
        const textSpan = box.querySelector('.text');
        const iconSpan = box.querySelector('.icon');

        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    iconSpan.style.display = 'none';
                    textSpan.innerText = 'تم اختيار: ' + input.files[0].name.substring(0, 15) + '...';
                    box.style.borderColor = '#10b981';
                    box.style.background = '#f0fdf4';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    setupFilePreview('p_file', 'preview-photo', 'box-photo');
    setupFilePreview('i_file', 'preview-id', 'box-id');

    // وظيفة الإرسال
    async function handleRegistration() {
        const form = document.getElementById('studentRegistrationForm');
        const btn = document.getElementById('submitBtn');

        // 1. التحقق من الحقول المطلوبة
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // 2. تحقق إضافي للملفات (بما أنها hidden فـ required المتصفح أحياناً يفشل)
        if (!document.getElementById('p_file').files[0] || !document.getElementById('i_file').files[0]) {
            Swal.fire({ icon: 'warning', title: 'نواقص', text: 'يرجى رفع الصورة الشخصية وصورة الهوية.' });
            return;
        }

        // 3. تجهيز البيانات
        btn.disabled = true;
        btn.innerHTML = '<span style="opacity: 0.8">جاري معالجة البيانات...</span>';

        let formData = new FormData(form);

        try {
            const response = await axios.post("{{ route('admin.students.save') }}", formData);

            Swal.fire({
                icon: 'success',
                title: 'تمت العملية!',
                text: response.data.title || 'تم تسجيل الطالب بنجاح',
                confirmButtonColor: '#10b981'
            }).then(() => {
                location.href = "{{ route('admin.students.index') }}";
            });

        } catch (error) {
            btn.disabled = false;
            btn.innerHTML = 'حفظ بيانات الطالب';

            let message = 'حدث خطأ غير متوقع.';
            if (error.response && error.response.data) {
                // عرض أول خطأ قادم من Laravel Validator
                message = error.response.data.title || error.response.data.message || message;
            }

            Swal.fire({ icon: 'error', title: 'فشل الحفظ', text: message });
        }
    }
</script>
@endsection
