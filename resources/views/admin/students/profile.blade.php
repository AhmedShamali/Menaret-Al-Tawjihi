@extends('layouts.app')

@section('title', __('الملف الشخصي للطالب') . ' - ' . __('إدارة المنصة'))

@section('content')
<div class="profile-wrapper">
    <!-- رأس الصفحة الكلاسيكي الأكاديمي -->
    <div class="academic-header-card">
        <div>
            <div class="badge-tag">
                <i class="fa-solid fa-id-card"></i>
                <span>{{ __('السجل الأكاديمي للطالب') }}</span>
            </div>
            <h1 class="header-title">{{ __('بيانات الطالب:') }} {{ $student->name_ar ?? $student->name }}</h1>
            <p class="header-subtitle">{{ __('نظرة عامة على البيانات الشخصية والأكاديمية المسجلة في النظام.') }}</p>
        </div>
        <a href="{{ route('admin.students.index') }}" class="btn-classic-nav">
            <i class="fa-solid fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i> {{ __('العودة لسجل الطلاب') }}
        </a>
    </div>

    <div class="profile-grid">
        <!-- الكارت الجانبي: الصورة والمعلومات الأساسية -->
        <div class="side-card">
            <div class="avatar-container">
                @if($student->photo && !str_contains($student->photo, 'C:'))
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name_ar }}" class="main-avatar">
                @else
                    <div class="avatar-placeholder">
                        {{ $student ? mb_substr($student->name_ar, 0, 1) : 'S' }}
                    </div>
                @endif
                <div class="status-indicator {{ $student->status == 'active' ? 'active' : 'pending' }}"></div>
            </div>

            <h2 class="name-display">{{ $student->name_ar ?? __('غير متوفر') }}</h2>
            <p class="email-display font-mono">{{ $student->email ?? __('لا يوجد بريد إلكتروني') }}</p>

            <div class="badge-group">
                <span class="badge badge-primary">{{ __('حساب طالب') }}</span>
                <span class="badge {{ $student->status == 'active' ? 'badge-success' : 'badge-warning' }}">
                    {{ $student->status == 'active' ? __('حساب نشط') : __('قيد المراجعة') }}
                </span>
            </div>

            <!-- صورة الهوية -->
            <div class="id-card-preview">
                <p class="id-title">{{ __('بطاقة الهوية الوطنية') }}</p>
                @if($student->id_photo && !str_contains($student->id_photo, 'C:'))
                    <a href="{{ asset('storage/' . $student->id_photo) }}" target="_blank" title="{{ __('اضغط للتكبير') }}">
                        <img src="{{ asset('storage/' . $student->id_photo) }}" alt="{{ __('الهوية') }}">
                    </a>
                @else
                    <div class="no-id">
                        <i class="fa-solid fa-id-card"></i>
                        <span>{{ __('لا توجد صورة هوية مرفقة') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- الكارت الرئيسي: التفاصيل -->
        <div class="details-content">
            <!-- قسم المعلومات الشخصية -->
            <div class="info-section">
                <div class="section-title">
                    <span class="icon"><i class="fa-solid fa-user"></i></span>
                    <h3>{{ __('المعلومات الشخصية') }}</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>{{ __('الاسم بالكامل (عربي)') }}</label>
                        <p>{{ $student->name_ar ?? '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>{{ __('الاسم بالإنجليزية') }}</label>
                        <p class="font-mono">{{ $student->name_en ?? '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>{{ __('الرقم الوطني / الهوية') }}</label>
                        <p class="font-mono">{{ $student->nid ?? '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>{{ __('رقم الهاتف') }}</label>
                        <p class="font-mono">{{ $student->phone ?? '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>{{ __('البريد الإلكتروني الرسمي') }}</label>
                        <p class="font-mono" dir="ltr">{{ $student->email ?? '—' }}</p>
                    </div>
                    <div class="info-item">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <label style="margin: 0;">{{ __('كلمة المرور وحساب الدخول') }}</label>
                            <span style="font-size: 0.7rem; color: #0284c7; background: #e0f2fe; padding: 1px 6px; border-radius: 4px; font-weight: 700;">
                                <i class="fa-solid fa-shield-halved"></i> {{ __('خاص بالإدارة') }}
                            </span>
                        </div>
                        <div id="studentPassContainer" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            @if(!empty($student->plain_password))
                                <span class="font-mono" dir="ltr">
                                    <span id="studentProfilePassPlain" style="background: #fef3c7; color: #92400e; padding: 3px 10px; border-radius: 6px; border: 1px solid #fde68a; font-weight: 800; font-size: 0.95rem;">{{ $student->plain_password }}</span>
                                    <span id="studentProfilePassMasked" style="display: none; letter-spacing: 2px; color: #64748b; font-size: 1rem;">••••••••</span>
                                </span>
                                <button type="button" onclick="toggleStudentProfilePass()" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; font-size: 0.75rem; cursor: pointer; color: #475569;" title="{{ __('إظهار / إخفاء كلمة المرور') }}">
                                    <i id="studentProfilePassIcon" class="fa-solid fa-eye-slash"></i>
                                </button>
                                <button type="button" onclick="copyStudentProfilePass('{{ $student->plain_password }}')" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; font-size: 0.75rem; cursor: pointer; color: #475569;" title="{{ __('نسخ كلمة المرور') }}">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            @else
                                <span id="studentProfilePassPlain" style="background: #f1f5f9; color: #475569; padding: 3px 9px; border-radius: 6px; border: 1px dashed #cbd5e1; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="fa-solid fa-shield-halved text-slate"></i> {{ __('مشفرة بأمان في النظام') }}
                                </span>
                            @endif
                            <button type="button" onclick="quickResetStudentPass({{ $student->id }}, '{{ addslashes($student->name_ar ?? $student->name_en) }}')" style="background: #0284c7; color: #ffffff; border: none; border-radius: 6px; padding: 5px 10px; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: 0.2s;" title="{{ __('تعيين أو إعادة تعيين كلمة مرور جديدة') }}">
                                <i class="fa-solid fa-key"></i> {{ __('تعيين جديدة') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- قسم المعلومات الأكاديمية -->
            <div class="info-section">
                <div class="section-title">
                    <span class="icon"><i class="fa-solid fa-graduation-cap"></i></span>
                    <h3>{{ __('المسار الأكاديمي') }}</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>{{ __('المرحلة الدراسية') }}</label>
                        <p>{{ $student->stage->label_ar ?? ($student->stage->name_ar ?? __('غير محدد')) }}</p>
                    </div>
                    <div class="info-item">
                        <label>{{ __('تاريخ التسجيل في النظام') }}</label>
                        <p class="font-mono">{{ $student->created_at ? $student->created_at->format('Y/m/d') : '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>{{ __('الجنس') }}</label>
                        <p>{{ $student->gender ? __($student->gender) : '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>{{ __('العمر') }}</label>
                        <p>{{ $student->age ? $student->age . ' ' . __('سنة') : '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-wrapper {
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

    /* شبكة العرض */
    .profile-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 24px;
        align-items: start;
    }

    /* الكارت الجانبي */
    .side-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .avatar-container {
        position: relative;
        width: 110px;
        height: 110px;
        margin: 0 auto 16px;
    }
    .main-avatar {
        width: 100%; height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e2e8f0;
    }
    .avatar-placeholder {
        width: 100%; height: 100%;
        background: #1e3a8a;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.4rem;
        font-weight: bold;
    }
    .status-indicator {
        position: absolute; bottom: 4px; inset-inline-end: 4px;
        width: 18px; height: 18px;
        border-radius: 50%;
        border: 2px solid #ffffff;
    }
    .status-indicator.active { background: #10b981; }
    .status-indicator.pending { background: #f59e0b; }

    .name-display { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 8px 0 4px; }
    .email-display { color: #64748b; font-size: 0.85rem; margin-bottom: 16px; word-break: break-all; }

    .badge-group { display: flex; gap: 8px; justify-content: center; margin-bottom: 20px; flex-wrap: wrap; }
    .badge { padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
    .badge-primary { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
    .badge-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .badge-warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }

    .id-card-preview {
        padding-top: 18px;
        border-top: 1px dashed #e2e8f0;
    }
    .id-title { font-size: 0.82rem; color: #64748b; margin-bottom: 10px; font-weight: 700; }
    .id-card-preview img { width: 100%; border-radius: 8px; cursor: pointer; border: 1px solid #e2e8f0; }
    .no-id {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        padding: 16px;
        border-radius: 8px;
        color: #94a3b8;
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 0.82rem;
    }

    /* كروت التفاصيل */
    .details-content {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .info-section {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .section-title { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .section-title .icon {
        background: #eff6ff; width: 36px; height: 36px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 8px; font-size: 1rem; color: #1e40af;
    }
    .section-title h3 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #0f172a; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .info-item label { display: block; color: #64748b; font-size: 0.8rem; margin-bottom: 6px; font-weight: 600; }
    .info-item p { margin: 0; font-weight: 700; color: #0f172a; font-size: 0.95rem; }

    /* التجاوب مع الشاشات الصغيرة */
    @media (max-width: 900px) {
        .profile-grid { grid-template-columns: 1fr; }
        .info-grid { grid-template-columns: 1fr; }
    }
</style>

<script>
function toggleStudentProfilePass() {
    const masked = document.getElementById('studentProfilePassMasked');
    const plain = document.getElementById('studentProfilePassPlain');
    const icon = document.getElementById('studentProfilePassIcon');
    if (!masked || !plain || !icon) return;
    if (plain.style.display === 'none') {
        masked.style.display = 'none';
        plain.style.display = 'inline-flex';
        icon.className = 'fa-solid fa-eye-slash';
    } else {
        plain.style.display = 'none';
        masked.style.display = 'inline-flex';
        icon.className = 'fa-solid fa-eye';
    }
}

function copyStudentProfilePass(text) {
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

function quickResetStudentPass(studentId, studentName) {
    const defaultPass = 'Tawjihi@' + Math.floor(1000 + Math.random() * 9000);
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '{{ __("تعيين كلمة مرور جديدة") }}',
            html: `
                <div style="text-align: right; margin-bottom: 12px; font-size: 0.88rem; color: #475569;">
                    {{ __("سيتم تعيين كلمة مرور جديدة للطالب:") }} <strong>${studentName}</strong>
                </div>
                <div style="text-align: right; margin-bottom: 6px;">
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155;">{{ __("كلمة المرور الجديدة:") }}</label>
                </div>
                <input id="swalNewPassInput" class="swal2-input" style="width: 85%; margin: 0 auto; font-family: monospace; font-weight: 700;" value="${defaultPass}" placeholder="{{ __('أدخل كلمة مرور (6 خانات على الأقل)') }}">
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0284c7',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-save"></i> {{ __("حفظ وتعيين الآن") }}',
            cancelButtonText: '{{ __("إلغاء") }}',
            preConfirm: () => {
                const pass = document.getElementById('swalNewPassInput').value.trim();
                if (!pass || pass.length < 6) {
                    Swal.showValidationMessage('{{ __("يجب أن تتكون كلمة المرور من 6 خانات على الأقل.") }}');
                    return false;
                }
                return pass;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const newPass = result.value;
                Swal.fire({
                    title: '{{ __("جاري التعيين...") }}',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                fetch(`{{ url('admin/students') }}/${studentId}/reset-password`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ new_password: newPass })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        copyStudentProfilePass(data.plain_password);
                        const container = document.getElementById('studentPassContainer');
                        if (container) {
                            container.innerHTML = `
                                <span class="font-mono" dir="ltr">
                                    <span id="studentProfilePassPlain" style="background: #dcfce7; color: #166534; padding: 3px 10px; border-radius: 6px; border: 1px solid #86efac; font-weight: 800; font-size: 0.95rem;">${data.plain_password}</span>
                                    <span id="studentProfilePassMasked" style="display: none; letter-spacing: 2px; color: #64748b; font-size: 1rem;">••••••••</span>
                                </span>
                                <button type="button" onclick="toggleStudentProfilePass()" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; font-size: 0.75rem; cursor: pointer; color: #475569;" title="{{ __('إظهار / إخفاء') }}">
                                    <i id="studentProfilePassIcon" class="fa-solid fa-eye-slash"></i>
                                </button>
                                <button type="button" onclick="copyStudentProfilePass('${data.plain_password}')" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; font-size: 0.75rem; cursor: pointer; color: #475569;" title="{{ __('نسخ') }}">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                                <button type="button" onclick="quickResetStudentPass(${studentId}, '${studentName}')" style="background: #0284c7; color: #ffffff; border: none; border-radius: 6px; padding: 5px 10px; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="fa-solid fa-key"></i> {{ __('تعديل') }}
                                </button>
                            `;
                        }
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("تم تعيين كلمة المرور بنجاح ✅") }}',
                            html: `<strong>${data.plain_password}</strong><br><small style="color:#64748b;">{{ __("تم نسخ كلمة المرور الجديدة إلى الحافظة تلقائياً.") }}</small>`,
                            confirmButtonColor: '#059669',
                            confirmButtonText: '{{ __("حسناً") }}'
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: data.message || '{{ __("تعذر تعيين كلمة المرور.") }}' });
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: '{{ __("حدث خطأ أثناء الاتصال بالخادم.") }}' });
                });
            }
        });
    } else {
        const pass = prompt('{{ __("أدخل كلمة المرور الجديدة للطالب:") }}', defaultPass);
        if (pass && pass.length >= 6) {
            fetch(`{{ url('admin/students') }}/${studentId}/reset-password`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ new_password: pass })
            }).then(() => location.reload());
        }
    }
}
</script>
@endsection
