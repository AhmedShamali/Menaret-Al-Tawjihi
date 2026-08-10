@extends('layouts.app')

@section('title', 'إدارة بيانات الطالب')

@section('content')
<div class="form-container">

    {{-- رأس الصفحة --}}
    <div class="form-header">
<a href="{{ route('admin.students.index') }}" class="back-btn" title="العودة للقائمة">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="form-title">
                {{ isset($student) ? 'تعديل بيانات الطالب' : 'تسجيل طالب جديد' }}
            </h1>
            <p class="form-subtitle">قم بتعبئة البيانات المطلوبة بدقة لضمان تفعيل ودقة الحساب الأكاديمي.</p>
        </div>
    </div>

    <form id="studentForm" enctype="multipart/form-data">
        @csrf
        @if(isset($student))
            @method('PUT')
        @endif

        <div class="form-grid">

            {{-- العمود الرئيسي (البيانات الشخصية والتواصل) --}}
            <div class="main-column">

                {{-- قسم البيانات الشخصية --}}
                <div class="form-card">
                    <div class="card-header">
                        <div class="icon-box primary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <h3>البيانات الشخصية</h3>
                    </div>

                    <div class="inputs-grid">
                        <div class="input-group">
                            <label class="f-label">الاسم الرباعي (بالعربية) <span class="required">*</span></label>
                            <input type="text" name="name_ar" value="{{ $student->name_ar ?? '' }}" class="f-input" placeholder="مثال: أحمد محمد عبد الله علي" required>
                        </div>

                        <div class="input-group">
                            <label class="f-label">Full Name (English) <span class="required">*</span></label>
                            <input type="text" name="name_en" value="{{ $student->name_en ?? '' }}" class="f-input" placeholder="e.g. Ahmed Mohammad Ali" required>
                        </div>

                        <div class="input-group">
                            <label class="f-label">رقم الهوية <span class="required">*</span></label>
                            <input type="text" name="nid" value="{{ $student->nid ?? '' }}" maxlength="9" class="f-input" placeholder="9 أرقام" required>
                        </div>

                        <div class="input-group">
                            <label class="f-label">العمر <span class="required">*</span></label>
                            <input type="number" name="age" value="{{ $student->age ?? '' }}" class="f-input" placeholder="مثال: 18" required>
                        </div>
                    </div>
                </div>

                {{-- قسم التواصل والمعلومات الأساسية --}}
                <div class="form-card">
                    <div class="card-header">
                        <div class="icon-box accent">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <h3>التواصل والحساب</h3>
                    </div>

                    <div class="inputs-grid">
                        <div class="input-group full-width">
                            <label class="f-label">البريد الإلكتروني <span class="required">*</span></label>
                            <input type="email" name="email" value="{{ $student->email ?? '' }}" class="f-input" placeholder="student@jisr.edu.ps" required>
                        </div>

                        <div class="input-group">
                            <label class="f-label">رقم الجوال <span class="required">*</span></label>
                            <input type="tel" name="phone" value="{{ $student->phone ?? '' }}" class="f-input" placeholder="059XXXXXXX" required>
                        </div>

                        <div class="input-group">
                            <label class="f-label">رقم الواتساب <span class="required">*</span></label>
                            <input type="tel" name="whatsapp" value="{{ $student->whatsapp ?? '' }}" class="f-input" placeholder="059XXXXXXX" required>
                        </div>

                        <div class="input-group full-width">
                            <label class="f-label">
                                كلمة المرور
                                @if(isset($student))
                                    <span class="hint">(اتركها فارغة إذا لم ترد التغيير)</span>
                                @endif
                            </label>
                            <input type="password" name="password" class="f-input" placeholder="••••••••">
                        </div>
                    </div>
                </div>

            </div>

            {{-- العمود الجانبي (المسار الدراسي والمرفقات) --}}
            <div class="side-column">

                {{-- المسار الدراسي --}}
                <div class="form-card">
                    <div class="card-header">
                        <div class="icon-box info">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                        </div>
                        <h3>المسار الدراسي</h3>
                    </div>

                    <div class="side-inputs">
                        <div class="input-group">
                            <label class="f-label">الصف / المرحلة <span class="required">*</span></label>
                            <select name="stage_id" class="f-input select-input" required>
                                <option value="">اختر الصف...</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}" {{ (isset($student) && $student->stage_id == $stage->id) ? 'selected' : '' }}>
                                        {{ $stage->label_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group">
                            <label class="f-label">الجنس <span class="required">*</span></label>
                            <select name="gender" class="f-input select-input">
                                <option value="ذكر" {{ (isset($student) && $student->gender == 'ذكر') ? 'selected' : '' }}>ذكر</option>
                                <option value="أنثى" {{ (isset($student) && $student->gender == 'أنثى') ? 'selected' : '' }}>أنثى</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- الوثائق والمرفقات --}}
                <div class="form-card">
                    <div class="card-header">
                        <div class="icon-box warning">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        </div>
                        <h3>الصور والوثائق</h3>
                    </div>

                    <div class="side-inputs">
                        {{-- الصورة الشخصية --}}
                        <div class="upload-box">
                            <input type="file" name="photo" id="p" accept="image/*" hidden onchange="previewImage(this, 'photoPreview')">
                            <label for="p" class="upload-label">
                                <div class="preview-zone">
                                    <img id="photoPreview" src="{{ isset($student->photo) ? asset('storage/'.$student->photo) : '' }}" class="{{ isset($student->photo) ? '' : 'd-none' }}" alt="الصورة الشخصية">
                                    <div class="placeholder-icon {{ isset($student->photo) ? 'd-none' : '' }}" id="photoPlaceholder">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                        <span>رفع الصورة الشخصية</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        {{-- صورة الهوية --}}
                        <div class="upload-box">
                            <input type="file" name="id_photo" id="i" accept="image/*" hidden onchange="previewImage(this, 'idPreview')">
                            <label for="i" class="upload-label">
                                <div class="preview-zone">
                                    <img id="idPreview" src="{{ isset($student->id_photo) ? asset('storage/'.$student->id_photo) : '' }}" class="{{ isset($student->id_photo) ? '' : 'd-none' }}" alt="صورة الهوية">
                                    <div class="placeholder-icon {{ isset($student->id_photo) ? 'd-none' : '' }}" id="idPlaceholder">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="M7 15h0"></path><path d="M2 9.5h20"></path></svg>
                                        <span>رفع صورة الهوية</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- زر الإرسال --}}
                <button type="button" onclick="saveStudent()" id="saveBtn" class="submit-btn">
                    <span>{{ isset($student) ? 'حفظ التعديلات' : 'إضافة الطالب الآن' }}</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>

            </div>

        </div>
    </form>
</div>

<style>
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --bg-main: #f8fafc;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --radius-lg: 16px;
        --radius-md: 10px;
        --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .form-container {
        max-width: 1080px;
        margin: 0 auto;
        animation: fadeIn 0.4s ease-out;
    }

    .form-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 28px;
    }

    .back-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-main);
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: var(--shadow-sm);
    }

    .back-btn:hover {
        background: #f1f5f9;
        transform: translateX(3px);
    }

    .form-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }

    .form-subtitle {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-top: 4px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
    }

    .main-column, .side-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .form-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        padding: 24px;
        box-shadow: var(--shadow-sm);
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .card-header h3 {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }

    .icon-box {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-box.primary { background: #eeef2; color: #4f46e5; }
    .icon-box.accent { background: #ecfdf5; color: #10b981; }
    .icon-box.info { background: #e0f2fe; color: #0284c7; }
    .icon-box.warning { background: #fff7ed; color: #ea580c; }

    .inputs-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .side-inputs {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .full-width {
        grid-column: span 2;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .f-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
    }

    .f-label .required {
        color: #ef4444;
    }

    .f-label .hint {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: normal;
    }

    .f-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        background: #f8fafc;
        font-size: 0.9rem;
        color: var(--text-main);
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .f-input:focus {
        outline: none;
        border-color: var(--primary-color);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .select-input {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%64748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: left 12px center;
        background-size: 16px;
    }

    /* أسلوب رفع الملفات المعاين */
    .upload-box {
        position: relative;
    }

    .upload-label {
        display: block;
        cursor: pointer;
    }

    .preview-zone {
        border: 2px dashed var(--border-color);
        border-radius: var(--radius-md);
        padding: 12px;
        min-height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        transition: all 0.2s ease;
        overflow: hidden;
    }

    .preview-zone:hover {
        border-color: var(--primary-color);
        background: #f5f3ff;
    }

    .preview-zone img {
        max-width: 100%;
        max-height: 100px;
        border-radius: 6px;
        object-fit: cover;
    }

    .placeholder-icon {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 600;
    }

    .d-none {
        display: none !important;
    }

    .submit-btn {
        width: 100%;
        padding: 14px 20px;
        border-radius: var(--radius-md);
        border: none;
        background: var(--primary-color);
        color: #ffffff;
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    .submit-btn:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
    }

    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 850px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .inputs-grid {
            grid-template-columns: 1fr;
        }
        .full-width {
            grid-column: span 1;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // معاينة الصور فور الاختيار
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(previewId.replace('Preview', 'Placeholder'));

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if(placeholder) placeholder.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // حفظ / تعديل بيانات الطالب
    function saveStudent() {
        const btn = document.getElementById('saveBtn');
        const form = document.getElementById('studentForm');
        const formData = new FormData(form);

        @if(isset($student))
            formData.append('_method', 'PUT');
        @endif

        btn.disabled = true;

        axios.post("{{ isset($student) ? route('admin.students.update', $student->id) : route('admin.students.store') }}", formData)
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: res.data.title || 'تم الحفظ بنجاح',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
    location.href = "{{ route('admin.students.index') }}";
            });
        })
        .catch(err => {
            const errorMessage = err.response?.data?.title || err.response?.data?.message || 'حدث خطأ غير متوقع، يرجى التثبت من الحقول';
            Swal.fire({
                icon: 'error',
                title: 'خطأ في الحفظ',
                text: errorMessage
            });
            btn.disabled = false;
        });
    }
</script>
@endsection
