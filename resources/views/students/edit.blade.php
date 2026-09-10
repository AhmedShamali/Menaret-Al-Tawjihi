@extends('layouts.app')

@section('title', 'تعديل ملف الطالب')

@section('content')
<div class="edit-student-wrapper">

    <!-- Header & Breadcrumb -->
    <div class="page-header">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.students.index') }}"><i class="fas fa-users"></i> إدارة الطلاب</a>
            <span class="sep">/</span>
            <span class="current">تعديل البيانات</span>
        </nav>
        <div class="header-title-box">
            <h1>✏️ تعديل بيانات الطالب</h1>
            <p>أنت تقوم الآن بتحديث ملف: <mark>{{ $student->name_ar }}</mark></p>
        </div>
    </div>

    <form id="editStudentForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <!-- العمود الأيمن: البيانات المكتوبة -->
            <div class="grid-main">

                <!-- 1. البيانات الشخصية -->
                <div class="form-card">
                    <div class="card-header">
                        <div class="card-icon"><i class="fas fa-user"></i></div>
                        <h3>البيانات الشخصية</h3>
                    </div>
                    <div class="card-body grid-2">
                        <div class="f-group">
                            <label class="f-label">الاسم الرباعي (عربي) <span class="req">*</span></label>
                            <input type="text" name="name_ar" value="{{ $student->name_ar }}" class="f-input" required placeholder="أدخل الاسم بالعربية">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Full Name (English) <span class="req">*</span></label>
                            <input type="text" name="name_en" value="{{ $student->name_en }}" class="f-input" required placeholder="Enter full English name">
                        </div>
                        <div class="f-group">
                            <label class="f-label">رقم الهوية <span class="req">*</span></label>
                            <input type="text" name="nid" value="{{ $student->nid }}" maxlength="9" class="f-input" required placeholder="9 أرقام">
                        </div>
                        <div class="f-group">
                            <label class="f-label">العمر <span class="req">*</span></label>
                            <input type="number" name="age" value="{{ $student->age }}" class="f-input" required placeholder="مثال: 18">
                        </div>
                        <div class="f-group">
                            <label class="f-label">المحافظة / المدينة</label>
                            <select name="city" class="f-input">
                                @php
                                    $cities = ['القدس', 'رام الله والبيرة', 'غزة', 'نابلس', 'الخليل', 'جنين', 'طولكرم', 'قلقيلية', 'بيت لحم', 'سلفيت', 'أريحا', 'طوباس', 'خان يونس', 'رفح', 'شمال غزة', 'دير البلح', 'أخرى'];
                                @endphp
                                @foreach($cities as $c)
                                    <option value="{{ $c }}" {{ ($student->city ?? 'رام الله والبيرة') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="f-group">
                            <label class="f-label">اسم المدرسة الثانوية</label>
                            <input type="text" name="school_name" value="{{ $student->school_name }}" class="f-input" placeholder="اسم المدرسة">
                        </div>
                    </div>
                </div>

                <!-- 2. التواصل والحساب -->
                <div class="form-card">
                    <div class="card-header">
                        <div class="card-icon"><i class="fas fa-envelope-open-text"></i></div>
                        <h3>التواصل والحساب</h3>
                    </div>
                    <div class="card-body grid-2">
                        <div class="f-group full-width">
                            <label class="f-label">البريد الإلكتروني <span class="req">*</span></label>
                            <input type="email" name="email" value="{{ $student->email }}" class="f-input" required placeholder="example@domain.com">
                        </div>
                        <div class="f-group">
                            <label class="f-label">رقم الجوال <span class="req">*</span></label>
                            <input type="tel" name="phone" value="{{ $student->phone }}" class="f-input" required placeholder="05XXXXXXXX">
                        </div>
                        <div class="f-group">
                            <label class="f-label">رقم الواتساب <span class="req">*</span></label>
                            <input type="tel" name="whatsapp" value="{{ $student->whatsapp }}" class="f-input" required placeholder="05XXXXXXXX">
                        </div>
                        <div class="f-group full-width">
                            <label class="f-label">رقم جوال ولي الأمر (للمتابعة الأكاديمية)</label>
                            <input type="tel" name="guardian_phone" value="{{ $student->guardian_phone }}" class="f-input" placeholder="05XXXXXXXX">
                        </div>
                        <div class="f-group full-width">
                            <label class="f-label">كلمة المرور الجديدة <span class="opt">(اتركها فارغة إذا لم ترد التغيير)</span></label>
                            <input type="password" name="password" class="f-input" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <!-- 3. المواد الدراسية المشترك بها -->
                <div class="form-card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="card-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h3>المواد الدراسية المشترك بها الطالب</h3>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" onclick="toggleEditSubjects(true)" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">تحديد الكل</button>
                            <button type="button" onclick="toggleEditSubjects(false)" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">إلغاء التحديد</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="manage_subjects" value="1">
                        <p style="font-size: 0.83rem; color: #64748b; margin-top: 0; margin-bottom: 14px;">
                            حدد المواد المعتمدة لهذا الطالب في حسابه، سيتم تحديث وتفعيل اشتراكاته فور حفظ البيانات:
                        </p>
                        <div id="editSubjectsList" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px;">
                            @php
                                $studentStage = $stages->firstWhere('id', $student->stage_id) ?? $stages->first();
                                $currentSubIds = $student->enrolledSubjects->pluck('id')->toArray();
                            @endphp

                            @if($studentStage && $studentStage->subjects->count() > 0)
                                @foreach($studentStage->subjects as $sub)
                                    @php $isChecked = in_array($sub->id, $currentSubIds); @endphp
                                    <label class="edit-subject-label" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border: 1.5px solid {{ $isChecked ? '#4f46e5' : '#e2e8f0' }}; background: {{ $isChecked ? '#eef2ff' : '#f8fafc' }}; border-radius: 12px; cursor: pointer; transition: 0.2s; user-select: none;">
                                        <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}" class="edit-sub-cb" {{ $isChecked ? 'checked' : '' }} style="width: 17px; height: 17px; accent-color: #4f46e5;">
                                        <span style="font-size: 1.3rem;">{{ $sub->icon ?? '📖' }}</span>
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-weight: 700; font-size: 0.85rem; color: #1e293b;">{{ $sub->name_ar }}</div>
                                            <div style="font-size: 0.72rem; color: #64748b;">{{ $sub->teacher?->name_ar ?? $sub->teacher?->name ?? 'مدرس المادة' }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            @else
                                <div style="color: #94a3b8; font-size: 0.85rem; padding: 15px; text-align: center; grid-column: 1/-1;">
                                    لا توجد مواد متاحة حالياً لهذه المرحلة.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- العمود الأيسر: الإعدادات والمرفقات -->
            <div class="grid-side">

                <!-- 3. المسار والتصنيف -->
                <div class="form-card">
                    <div class="card-header">
                        <div class="card-icon"><i class="fas fa-graduation-cap"></i></div>
                        <h3>المسار الدراسي</h3>
                    </div>
                    <div class="card-body">
                        <div class="f-group">
                            <label class="f-label">الصف / المرحلة الحالية <span class="req">*</span></label>
                            <select name="stage_id" class="f-input">
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}" {{ $student->stage_id == $stage->id ? 'selected' : '' }}>
                                        {{ $stage->label_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="f-group">
                            <label class="f-label">الجنس <span class="req">*</span></label>
                            <select name="gender" class="f-input">
                                <option value="ذكر" {{ $student->gender == 'ذكر' ? 'selected' : '' }}>ذكر</option>
                                <option value="أنثى" {{ $student->gender == 'أنثى' ? 'selected' : '' }}>أنثى</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 4. المرفقات والصور -->
                <div class="form-card">
                    <div class="card-header">
                        <div class="card-icon"><i class="fas fa-id-card"></i></div>
                        <h3>الصور والوثائق</h3>
                    </div>
                    <div class="card-body">

                        <!-- الصورة الشخصية -->
                        <div class="media-upload-item">
                            <label class="f-label">الصورة الشخصية للطالب</label>
                            <div class="preview-box">
                                <img id="photo-preview" src="{{ $student->photo_url }}" alt="الشخصية" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                            </div>
                            <input type="file" name="photo" id="p_file" class="file-input-hidden" accept="image/*" onchange="previewImage(this, 'photo-preview')">
                            <label for="p_file" class="btn-upload-trigger">
                                <span>🔄 رفع / تغيير الصورة الشخصية</span>
                            </label>
                        </div>

                        <hr class="card-divider">

                        <!-- صورة الهوية -->
                        <div class="media-upload-item">
                            <label class="f-label">صورة الهوية الفلسطينية / شهادة الميلاد</label>
                            <div class="preview-box" style="position: relative;">
                                @if($student->id_photo)
                                    <img id="id-photo-preview" src="{{ asset('storage/'.$student->id_photo) }}" alt="الهوية" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                                @else
                                    <div id="id-photo-placeholder" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #94a3b8; font-size: 0.8rem; gap: 6px;">
                                        <i class="fas fa-id-card" style="font-size: 2.2rem; color: #cbd5e1;"></i>
                                        <span>لم تُرفع هوية بعد</span>
                                    </div>
                                    <img id="id-photo-preview" src="" alt="الهوية" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px; display: none;">
                                @endif
                            </div>

                            @if($student->id_photo)
                                <a href="{{ asset('storage/'.$student->id_photo) }}" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; width: 100%; padding: 8px; margin-top: 8px; background: #e0f2fe; color: #0369a1; border-radius: 10px; font-size: 0.78rem; font-weight: 700; text-decoration: none;">
                                    <i class="fas fa-expand"></i> معاينة الوثيقة بالحجم الكامل
                                </a>
                            @endif

                            <input type="file" name="id_photo" id="i_file" class="file-input-hidden" accept="image/*,application/pdf" onchange="previewImage(this, 'id-photo-preview')">
                            <label for="i_file" class="btn-upload-trigger" style="margin-top: 8px;">
                                <span>🔄 رفع / تحديث صورة الهوية</span>
                            </label>
                        </div>

                    </div>
                </div>

                <!-- 5. الخصومات والمنح (خاص بإدارة المنصة) -->
                @if(Auth::guard('web')->check() && Auth::user()->role === 'admin')
                <div class="form-card" style="border: 1.5px solid #ddd6fe; background: #faf5ff;">
                    <div class="card-header" style="background: #f3e8ff;">
                        <div class="card-icon" style="background: #7c3aed; color: white;"><i class="fas fa-tags"></i></div>
                        <h3 style="color: #6b21a8;">المنح والخصومات الأكاديمية (إدارة)</h3>
                    </div>
                    <div class="card-body">
                        <div class="f-group">
                            <label class="f-label">نسبة الخصم المئوية (%)</label>
                            <input type="number" name="custom_discount_percent" value="{{ (float)($student->custom_discount_percent ?? 0) }}" min="0" max="100" step="any" class="f-input" placeholder="مثال: 25 أو 50 (100% تعني إعفاء كامل)">
                            <small style="color: #7c3aed; font-size: 0.75rem; display: block; margin-top: 4px;">100% تمنح الطالب إعفاءً كاملاً بدون الحاجة لدفع أي رسوم.</small>
                        </div>
                        <div class="f-group" style="margin-top: 12px;">
                            <label class="f-label">أو خصم بمبلغ نقدي ثابت (₪)</label>
                            <input type="number" name="custom_discount_fixed" value="{{ (float)($student->custom_discount_fixed ?? 0) }}" min="0" step="any" class="f-input" placeholder="مثال: 50 أو 100">
                        </div>
                        <div class="f-group" style="margin-top: 12px;">
                            <label class="f-label">سبب الخصم أو ملاحظات المنحة</label>
                            <input type="text" name="discount_notes" value="{{ $student->discount_notes }}" class="f-input" placeholder="مثال: منحة تفوق دراسي / رعاية خاصة">
                        </div>
                    </div>
                </div>
                @endif

                <!-- الأزرار -->
                <div class="actions-box">
                    <button type="button" onclick="performUpdate({{ $student->id }})" id="saveBtn" class="btn-submit">
                        <span id="btnText">حفظ التغييرات ✅</span>
                        <span id="btnSpinner" class="spinner" style="display:none;"></span>
                    </button>
                    <a href="{{ route('admin.students.index') }}" class="btn-cancel">إلغاء</a>
                </div>

            </div>

        </div>
    </form>
</div>

<style>
    :root {
        --card-bg: rgba(255, 255, 255, 0.85);
        --input-bg: #f8fafc;
        --border-color: #e2e8f0;
        --primary-color: #3b82f6;
        --primary-hover: #2563eb;
        --text-main: #1e293b;
        --text-sub: #64748b;
    }

    .edit-student-wrapper {
        max-width: 1100px;
        margin: 0 auto;
        padding: 20px 10px;
        animation: fadeIn 0.4s ease-out;
    }

    /* Header Styles */
    .page-header { margin-bottom: 25px; }
    .breadcrumb-nav { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-sub); margin-bottom: 8px; }
    .breadcrumb-nav a { color: var(--text-sub); text-decoration: none; transition: 0.2s; }
    .breadcrumb-nav a:hover { color: var(--primary-color); }
    .breadcrumb-nav .current { color: var(--primary-color); font-weight: 700; }
    .header-title-box h1 { font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin: 0; }
    .header-title-box p { color: var(--text-sub); margin-top: 5px; font-size: 0.95rem; }
    .header-title-box mark { background: rgba(59, 130, 246, 0.1); color: var(--primary-color); padding: 2px 8px; border-radius: 6px; font-weight: 700; }

    /* Layout Grid */
    .form-grid { display: grid; grid-template-columns: 1.7fr 1fr; gap: 25px; }
    @media (max-width: 900px) { .form-grid { grid-template-columns: 1fr; } }

    /* Cards */
    .form-card {
        background: var(--card-bg);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 25px;
    }
    .card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; }
    .card-icon { width: 36px; height: 36px; background: rgba(59, 130, 246, 0.1); color: var(--primary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
    .card-header h3 { font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin: 0; }

    /* Form Inputs */
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .full-width { grid-column: span 2; }
    @media (max-width: 600px) { .grid-2 { grid-template-columns: 1fr; } .full-width { grid-column: span 1; } }

    .f-group { display: flex; flex-direction: column; }
    .f-label { font-weight: 700; font-size: 0.85rem; color: var(--text-main); margin-bottom: 6px; }
    .f-label .req { color: #ef4444; }
    .f-label .opt { color: var(--text-sub); font-weight: 400; font-size: 0.75rem; }

    .f-input {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1.5px solid var(--border-color);
        background: var(--input-bg);
        font-family: inherit;
        font-size: 0.95rem;
        color: var(--text-main);
        transition: all 0.25s ease;
        outline: none;
        box-sizing: border-box;
    }
    .f-input:focus {
        border-color: var(--primary-color);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
    }

    /* Media Upload Section */
    .media-upload-item { display: flex; flex-direction: column; align-items: center; gap: 10px; }
    .preview-box { width: 100%; height: 120px; border-radius: 14px; overflow: hidden; background: #f1f5f9; border: 1px dashed var(--border-color); display: flex; align-items: center; justify-content: center; }
    .preview-box img { width: 100%; height: 100%; object-fit: cover; }
    .file-input-hidden { display: none; }
    .btn-upload-trigger {
        width: 100%;
        text-align: center;
        padding: 10px;
        background: #f1f5f9;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-sub);
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-upload-trigger:hover { background: rgba(59, 130, 246, 0.1); color: var(--primary-color); }
    .card-divider { border: 0; height: 1px; background: var(--border-color); margin: 20px 0; }

    /* Action Buttons */
    .actions-box { display: flex; flex-direction: column; gap: 10px; }
    .btn-submit {
        width: 100%;
        padding: 16px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 14px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .btn-submit:hover { background: var(--primary-hover); transform: translateY(-1px); }
    .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

    .btn-cancel {
        display: block;
        text-align: center;
        padding: 12px;
        color: var(--text-sub);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        border-radius: 12px;
        transition: 0.2s;
    }
    .btn-cancel:hover { background: #f1f5f9; color: var(--text-main); }

    /* Spinner */
    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid #ffffff;
        border-bottom-color: transparent;
        border-radius: 50%;
        display: inline-block;
        animation: rotation 1s linear infinite;
    }
    @keyframes rotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // معاينة الصور فور اختيارها من الجهاز
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // تبديل وتلوين كروت المواد في التعديل
    document.querySelectorAll('.edit-subject-label').forEach(card => {
        const cb = card.querySelector('.edit-sub-cb');
        if (cb) {
            cb.addEventListener('change', () => {
                if (cb.checked) {
                    card.style.borderColor = '#4f46e5';
                    card.style.background = '#eef2ff';
                } else {
                    card.style.borderColor = '#e2e8f0';
                    card.style.background = '#f8fafc';
                }
            });
        }
    });

    function toggleEditSubjects(selectAll) {
        document.querySelectorAll('.edit-sub-cb').forEach(cb => {
            cb.checked = selectAll;
            const card = cb.closest('.edit-subject-label');
            if (card) {
                card.style.borderColor = selectAll ? '#4f46e5' : '#e2e8f0';
                card.style.background = selectAll ? '#eef2ff' : '#f8fafc';
            }
        });
    }

    // إرسال طلب التحديث
    function performUpdate(id) {
        const btn = document.getElementById('saveBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const form = document.getElementById('editStudentForm');

        const formData = new FormData(form);

        // تفعيل وضع التحميل
        btn.disabled = true;
        btnText.textContent = 'جاري التحديث...';
        btnSpinner.style.display = 'inline-block';

        axios.post(`{{ url('admin/students') }}/${id}`, formData)
        .then(function (res) {
            Swal.fire({
                icon: 'success',
                title: 'تم التحديث بنجاح!',
                text: res.data.message || 'تمت تحديث بيانات الطالب بنجاح.',
                timer: 1800,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "{{ route('admin.students.index') }}";
            });
        })
        .catch(function (error) {
            let errorMessage = 'حدث خطأ غير متوقع أثناء التحديث.';

            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                errorMessage = Object.values(errors).flat().join('<br>');
            } else if (error.response && error.response.data.message) {
                errorMessage = error.response.data.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'فشل التحديث',
                html: errorMessage,
                confirmButtonText: 'حسناً'
            });

            btn.disabled = false;
            btnText.textContent = 'حفظ التغييرات ✅';
            btnSpinner.style.display = 'none';
        });
    }
</script>
@endsection
