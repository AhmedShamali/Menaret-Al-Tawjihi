@extends('layouts.app')

@section('title', __('تعديل ملف المعلم') . ' - ' . __('إدارة المنصة'))

@section('content')
<div class="teacher-edit-wrapper">

    {{-- رسالة النجاح --}}
    @if(session('success'))
    <div class="academic-alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- الرأس الأكاديمي الكلاسيكي --}}
    <div class="academic-header-card">
        <div>
            <div class="badge-tag">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>{{ __('إدارة الكادر التعليمي') }}</span>
            </div>
            <h1 class="header-title">{{ __('تعديل ملف المعلم') }}</h1>
            <p class="header-subtitle">{{ __('تحديث المعلومات الشخصية والمهنية للمستخدم:') }} <strong>{{ $teacher->name }}</strong></p>
        </div>
        <a href="{{ route('admin.teachers.info') }}" class="btn-classic-return">
            <i class="fa-solid fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i> {{ __('رجوع للسجل') }}
        </a>
    </div>

    {{-- بطاقة النموذج --}}
    <div class="academic-form-card">
        <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="input-fields-grid">
                <div class="field-item">
                    <label class="academic-label">{{ __('اسم المعلم') }} <span class="req-star">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $teacher->name) }}" class="academic-input" required>
                    @error('name') <span class="field-error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field-item">
                    <label class="academic-label">{{ __('البريد الإلكتروني') }} <span class="req-star">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $teacher->email) }}" class="academic-input font-mono" required>
                    @error('email') <span class="field-error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field-item">
                    <label class="academic-label">{{ __('رقم الهاتف') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" class="academic-input font-mono">
                    @error('phone') <span class="field-error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field-item">
                    <label class="academic-label">{{ __('التخصص الأكاديمي') }}</label>
                    <input type="text" name="major" value="{{ old('major', $teacher->major) }}" class="academic-input">
                    @error('major') <span class="field-error-text">{{ $message }}</span> @enderror
                </div>

                {{-- حقل المادة الدراسية --}}
                <div class="field-item">
                    <label class="academic-label">{{ __('المادة الدراسية') }}</label>
                    <select name="subject_id" class="academic-select">
                        <option value="">{{ __('اختر المادة الدراسية') }}</option>
                        @foreach($stages as $stage)
                            <optgroup label="{{ $stage->label_ar ?? $stage->name }}">
                                @foreach($stage->subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $teacher->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name_ar ?? $subject->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('subject_id') <span class="field-error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field-item">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label class="academic-label" style="margin: 0;">{{ __('كلمة المرور الحالية المسجلة') }}</label>
                        <span style="font-size: 0.7rem; color: #0284c7; background: #e0f2fe; padding: 1px 6px; border-radius: 4px; font-weight: 700;">
                            <i class="fa-solid fa-shield-halved"></i> {{ __('خاص بالإدارة') }}
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        @if(!empty($teacher->plain_password))
                            <span class="academic-input font-mono" style="background: #f8fafc; display: flex; align-items: center; justify-content: space-between; flex: 1; min-height: 42px;" dir="ltr">
                                <span id="editTeacherPassPlain" style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 6px; border: 1px solid #fde68a; font-weight: 800;">{{ $teacher->plain_password }}</span>
                                <span id="editTeacherPassMasked" style="display: none; letter-spacing: 2px; color: #64748b;">••••••••</span>
                                <button type="button" onclick="toggleEditTeacherPass()" style="background: none; border: none; cursor: pointer; color: #64748b; padding: 4px;" title="{{ __('إظهار / إخفاء') }}">
                                    <i id="editTeacherPassIcon" class="fa-solid fa-eye-slash"></i>
                                </button>
                            </span>
                            <button type="button" onclick="copyEditTeacherPass('{{ $teacher->plain_password }}')" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 0.85rem; font-weight: 700; color: #334155; cursor: pointer;" title="{{ __('نسخ كلمة المرور') }}">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        @else
                            <span style="background: #f1f5f9; color: #475569; padding: 10px 14px; border-radius: 8px; border: 1px dashed #cbd5e1; font-size: 0.84rem; font-weight: 700; flex: 1; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-shield-halved text-slate"></i> {{ __('مشفرة بأمان في النظام (يمكنك كتابة كلمة جديدة في الحقل أدناه)') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="field-item">
                    <label class="academic-label">{{ __('كلمة المرور الجديدة (اختياري)') }}</label>
                    <input type="password" name="password" placeholder="{{ __('اتركها فارغة إذا لم ترغب بتغييرها') }}" class="academic-input">
                </div>

                <div class="field-item full-width">
                    <label class="academic-label">{{ __('الصورة الشخصية للمعلم') }}</label>
                    <div class="photo-upload-box">
                        @if($teacher->photo)
                            <img src="{{ asset('storage/' . $teacher->photo) }}" class="teacher-preview-thumb" alt="{{ $teacher->name }}">
                        @else
                            <div class="teacher-placeholder-thumb">
                                <i class="fa-solid fa-user"></i>
                            </div>
                        @endif
                        <div class="upload-field-wrap">
                            <input type="file" name="photo" accept="image/*" class="file-input-clean">
                            <small class="upload-hint">{{ __('يدعم صيغ الصور (JPG, PNG, WebP). يفضل صورة شخصية مربعة عالية الوضوح.') }}</small>
                        </div>
                    </div>
                </div>

                <div class="field-item full-width">
                    <label class="academic-label">{{ __('نبذة تعريفية قصيرة (Bio)') }}</label>
                    <textarea name="bio" rows="4" class="academic-textarea" placeholder="{{ __('اكتب نبذة عن مؤهلات المعلم وخبرته الأكاديمية...') }}">{{ old('bio', $teacher->bio) }}</textarea>
                </div>
            </div>

            <div class="form-actions-bar">
                <button type="submit" class="btn-save-primary">
                    <i class="fa-solid fa-check"></i> {{ __('حفظ التغييرات الآن') }}
                </button>
                <a href="{{ route('admin.teachers.info') }}" class="btn-cancel-secondary">
                    {{ __('إلغاء الأمر') }}
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    .teacher-edit-wrapper {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 10px 0 60px;
        box-sizing: border-box;
    }

    .academic-alert-success {
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-inline-start: 5px solid #10b981;
        background: #ecfdf5;
        color: #065f46;
        font-weight: 600;
        font-size: 0.9rem;
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
    .btn-classic-return {
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
    .btn-classic-return:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    .academic-form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        padding: 28px;
    }

    .input-fields-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 22px;
    }
    .field-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .field-item.full-width {
        grid-column: span 2;
    }

    .academic-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
    }
    .req-star { color: #ef4444; }

    .academic-input, .academic-select, .academic-textarea {
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 0.9rem;
        background: #f8fafc;
        outline: none;
        transition: border-color 0.15s;
        box-sizing: border-box;
        font-family: inherit;
    }
    .academic-input:focus, .academic-select:focus, .academic-textarea:focus {
        border-color: #1e3a8a;
        background: #ffffff;
    }

    .photo-upload-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        padding: 16px 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
    }
    .teacher-preview-thumb {
        width: 70px;
        height: 70px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid #e2e8f0;
        flex-shrink: 0;
    }
    .teacher-placeholder-thumb {
        width: 70px;
        height: 70px;
        border-radius: 10px;
        background: #e2e8f0;
        color: #94a3b8;
        display: grid;
        place-items: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }
    .upload-field-wrap { flex: 1; min-width: 240px; }
    .file-input-clean { font-size: 0.85rem; color: #475569; }
    .upload-hint {
        display: block;
        color: #64748b;
        font-size: 0.75rem;
        margin-top: 6px;
    }

    .form-actions-bar {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .btn-save-primary {
        background: #1e3a8a;
        color: #ffffff;
        border: none;
        padding: 11px 26px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.15s;
    }
    .btn-save-primary:hover { background: #172554; }
    .btn-cancel-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 11px 22px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .btn-cancel-secondary:hover { background: #e2e8f0; color: #0f172a; }

    .field-error-text {
        color: #dc2626;
        font-size: 0.78rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .input-fields-grid { grid-template-columns: 1fr; }
        .field-item.full-width { grid-column: span 1; }
        .form-actions-bar { flex-direction: column; width: 100%; }
        .btn-save-primary, .btn-cancel-secondary { width: 100%; justify-content: center; text-align: center; }
    }
</style>

<script>
function toggleEditTeacherPass() {
    const masked = document.getElementById('editTeacherPassMasked');
    const plain = document.getElementById('editTeacherPassPlain');
    const icon = document.getElementById('editTeacherPassIcon');
    if (!masked || !plain || !icon) return;
    if (plain.style.display === 'none') {
        masked.style.display = 'none';
        plain.style.display = 'inline';
        icon.className = 'fa-solid fa-eye-slash';
    } else {
        plain.style.display = 'none';
        masked.style.display = 'inline';
        icon.className = 'fa-solid fa-eye';
    }
}

function copyEditTeacherPass(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text);
    } else {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        textArea.remove();
    }
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ __("تم نسخ كلمة المرور إلى الحافظة") }}',
            showConfirmButton: false,
            timer: 1800
        });
    } else {
        alert('{{ __("تم نسخ كلمة المرور بنجاح") }}');
    }
}
</script>
@endsection
