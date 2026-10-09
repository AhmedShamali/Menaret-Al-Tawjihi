@extends('layouts.app')

@section('title', __('مراجعة بيانات الطالب') . ' | ' . ($student->name_ar ?? __('طلب انضمام')))

@section('content')
<div class="ed-review-container">

    <div class="ed-review-header">
        <div class="header-titles">
            <h1 class="page-title">
                <i class="fa-solid fa-user-check" style="color: #1e3a8a; margin-left: 8px;"></i>
                {{ __('مراجعة طلب الانضمام') }}
            </h1>
            <p class="page-subtitle">{{ __('يرجى التأكد من مطابقة وثيقة الهوية مع البيانات والاسم الأكاديمي المدخل.') }}</p>
        </div>
        <div class="header-actions">
            @if($student->whatsapp_url)
                <a href="{{ $student->whatsapp_url }}" target="_blank" rel="noopener noreferrer" style="background: #10b981; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 0.88rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'" title="{{ __('فتح محادثة واتساب مع الطالب قبل الاعتماد') }}">
                    <i class="fa-brands fa-whatsapp" style="font-size: 1.15rem;"></i>
                    <span>{{ __('مراسلة الطالب واتساب') }}</span>
                </a>
            @endif
            <button type="button" onclick="approveStudent({{ $student->id }})" class="btn-approve">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ __('اعتماد وتفعيل الحساب') }}</span>
            </button>
            <button type="button" onclick="rejectStudent({{ $student->id }})" class="btn-reject">
                <i class="fa-solid fa-circle-xmark"></i>
                <span>{{ __('رفض الطلب') }}</span>
            </button>
        </div>
    </div>

    <div class="ed-review-grid">

        <!-- كرت الصور (المرفقات) -->
        <div class="ed-review-card">
            <div class="card-head">
                <i class="fa-solid fa-id-badge" style="color: #1e3a8a;"></i>
                <h3>{{ __('الوثائق الرسمية المرفوعة') }}</h3>
            </div>

            <div class="photo-section">
                <span class="section-label">{{ __('الصورة الشخصية للطالب') }}</span>
                <img src="{{ $student->photo_url }}" 
                     class="student-photo-preview"
                     alt="{{ $student->name_ar ?? 'طالب' }}"
                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($student->name_ar ?? 'طالب') }}&background=0284c7&color=fff&size=200&bold=true';">
            </div>

            <div class="document-section">
                <span class="section-label">{{ __('وثيقة الهوية / شهادة الميلاد') }}</span>
                @if($student->id_photo)
                    @if($student->is_id_pdf)
                        <div class="pdf-document-box">
                            <i class="fa-solid fa-file-pdf pdf-icon"></i>
                            <span class="pdf-label">{{ __('وثيقة رسمية بصيغة PDF') }}</span>
                            <div class="pdf-actions">
                                <a href="{{ route('admin.students.document.view', [$student->id, 'id_photo']) }}" target="_blank" class="btn-doc-view">
                                    <i class="fa-solid fa-eye"></i> {{ __('عرض المستند') }}
                                </a>
                                <a href="{{ route('admin.students.document.download', [$student->id, 'id_photo']) }}" class="btn-doc-dl">
                                    <i class="fa-solid fa-download"></i> {{ __('تنزيل') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="img-document-box">
                            <img src="{{ $student->id_photo_url }}" class="id-photo-preview" onclick="window.open('{{ route('admin.students.document.view', [$student->id, 'id_photo']) }}')" title="{{ __('انقر للمعاينة الكاملة') }}">
                            <div style="margin-top: 10px; text-align: center;">
                                <a href="{{ route('admin.students.document.download', [$student->id, 'id_photo']) }}" class="btn-doc-dl-link">
                                    <i class="fa-solid fa-download"></i> {{ __('تنزيل الوثيقة الرسمية') }}
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="no-document-box">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ __('لم يقم الطالب بإرفاق وثيقة الهوية') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- كرت البيانات النصية -->
        <div class="ed-review-card">
            <div class="card-head">
                <i class="fa-solid fa-file-lines" style="color: #1e3a8a;"></i>
                <h3>{{ __('البيانات الأكاديمية والشخصية') }}</h3>
            </div>

            <div class="info-list">
                <div class="info-row">
                    <span class="lbl"><i class="fa-solid fa-user"></i> {{ __('الاسم العربي:') }}</span>
                    <strong class="val">{{ $student->name_ar }}</strong>
                </div>
                <div class="info-row">
                    <span class="lbl"><i class="fa-regular fa-user"></i> {{ __('الاسم الإنجليزي:') }}</span>
                    <strong class="val">{{ $student->name_en ?: '---' }}</strong>
                </div>
                <div class="info-row">
                    <span class="lbl"><i class="fa-solid fa-id-card"></i> {{ __('رقم الهوية:') }}</span>
                    <strong class="val font-mono">{{ $student->nid }}</strong>
                </div>
                <div class="info-row">
                    <span class="lbl"><i class="fa-solid fa-calendar-day"></i> {{ __('العمر:') }}</span>
                    <strong class="val">{{ $student->age }} {{ __('عام') }}</strong>
                </div>
                <div class="info-row">
                    <span class="lbl"><i class="fa-solid fa-venus-mars"></i> {{ __('الجنس:') }}</span>
                    <strong class="val">{{ $student->gender == 'female' ? __('أنثى') : __('ذكر') }}</strong>
                </div>
                <div class="info-row">
                    <span class="lbl"><i class="fa-solid fa-graduation-cap"></i> {{ __('المرحلة الدراسية:') }}</span>
                    <strong class="val stage-pill">{{ $student->stage->label_ar ?? __('غير محدد') }}</strong>
                </div>
                <div class="info-row">
                    <span class="lbl"><i class="fa-brands fa-whatsapp" style="color: #10b981;"></i> {{ __('الواتساب المعتمد:') }}</span>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <strong class="val font-mono" dir="ltr">{{ $student->display_whatsapp }}</strong>
                        @if($student->whatsapp_url)
                            <a href="{{ $student->whatsapp_url }}" target="_blank" rel="noopener noreferrer" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa-brands fa-whatsapp"></i> {{ __('محادثة فورية') }}
                            </a>
                        @endif
                    </div>
                </div>
                @if($student->guardian_phone)
                    <div class="info-row">
                        <span class="lbl"><i class="fa-solid fa-user-shield"></i> {{ __('هاتف ولي الأمر:') }}</span>
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <strong class="val font-mono" dir="ltr">{{ $student->guardian_phone }}</strong>
                            @if($student->guardian_whatsapp_url)
                                <a href="{{ $student->guardian_whatsapp_url }}" target="_blank" rel="noopener noreferrer" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-brands fa-whatsapp"></i> {{ __('واتساب') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="info-row">
                    <span class="lbl"><i class="fa-solid fa-envelope"></i> {{ __('البريد الإلكتروني:') }}</span>
                    <strong class="val font-mono text-sm">{{ $student->email }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.ed-review-container {
    max-width: 1040px;
    margin: 0 auto;
    width: 100%;
    box-sizing: border-box;
}

.ed-review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.ed-review-header .page-title {
    font-size: 1.55rem;
    font-weight: 900;
    color: #0f172a;
    margin: 0 0 4px;
    display: flex;
    align-items: center;
}

.ed-review-header .page-subtitle {
    font-size: 0.85rem;
    color: #64748b;
    margin: 0;
}

.ed-review-header .header-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-approve {
    background: #059669;
    color: #ffffff;
    border: none;
    padding: 10px 22px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    box-shadow: 0 2px 6px rgba(5, 150, 105, 0.2);
}
.btn-approve:hover {
    background: #047857;
    transform: translateY(-1px);
}

.btn-reject {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}
.btn-reject:hover {
    background: #fee2e2;
}

.ed-review-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    width: 100%;
    box-sizing: border-box;
}

.ed-review-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-top: 4px solid #1e3a8a;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}

.card-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 14px;
    margin-bottom: 20px;
    border-bottom: 1px solid #f1f5f9;
}
.card-head h3 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
}

.photo-section {
    text-align: center;
    margin-bottom: 24px;
}
.section-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 10px;
}

.student-photo-preview {
    width: 150px;
    height: 150px;
    border-radius: 20px;
    object-fit: cover;
    border: 3px solid #e2e8f0;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
}

.document-section {
    text-align: center;
}

.pdf-document-box {
    padding: 20px 14px;
    background: #fef2f2;
    border: 2px dashed #fca5a5;
    border-radius: 12px;
}
.pdf-icon {
    font-size: 2.5rem;
    color: #dc2626;
    margin-bottom: 8px;
    display: block;
}
.pdf-label {
    font-size: 0.82rem;
    font-weight: 800;
    color: #991b1b;
    display: block;
    margin-bottom: 12px;
}
.pdf-actions {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
}
.btn-doc-view {
    background: #dc2626;
    color: white;
    padding: 7px 16px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.8rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-doc-dl {
    background: #1e293b;
    color: white;
    padding: 7px 16px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.8rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.id-photo-preview {
    width: 100%;
    max-height: 220px;
    object-fit: contain;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    cursor: zoom-in;
    background: #f8fafc;
}
.btn-doc-dl-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #1e3a8a;
    text-decoration: none;
}

.no-document-box {
    padding: 24px 14px;
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 12px;
    color: #94a3b8;
    font-size: 0.84rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}
.no-document-box i {
    font-size: 1.8rem;
    color: #f59e0b;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.84rem;
    gap: 10px;
    flex-wrap: wrap;
}
.info-row .lbl {
    color: #64748b;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}
.info-row .lbl i {
    color: #94a3b8;
    font-size: 0.82rem;
}
.info-row .val {
    color: #0f172a;
    word-break: break-word;
}
.stage-pill {
    background: #eff6ff;
    color: #1e3a8a !important;
    border: 1px solid #bfdbfe;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.76rem;
}
.font-mono { font-family: monospace, sans-serif; }
.text-sm { font-size: 0.78rem; }

@media (max-width: 768px) {
    .ed-review-header {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    .header-actions {
        width: 100%;
    }
    .btn-approve, .btn-reject {
        width: 100%;
        justify-content: center;
        box-sizing: border-box;
    }
    .ed-review-grid {
        grid-template-columns: 1fr;
        gap: 14px;
    }
    .ed-review-card {
        padding: 18px 14px;
    }
}
</style>

<script>
function approveStudent(id) {
    const doApprove = () => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'جاري اعتماد الحساب...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }
        axios.post(`/admin/students/${id}/approve`, {
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'تم اعتماد وتفعيل الحساب بنجاح! 🎉',
                    text: res.data?.message || 'تم اعتماد حساب الطالب بنجاح.',
                    confirmButtonText: 'حسناً'
                }).then(() => {
                    window.location.href = "{{ route('admin.students.index') }}";
                });
            } else {
                alert('تم اعتماد وتفعيل الحساب بنجاح!');
                window.location.href = "{{ route('admin.students.index') }}";
            }
        })
        .catch(err => {
            if (typeof Swal !== 'undefined') {
                Swal.fire('خطأ!', err.response?.data?.message || 'حدث خطأ أثناء اعتماد الحساب.', 'error');
            } else {
                alert('حدث خطأ أثناء اعتماد الحساب.');
            }
        });
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'تأكيد اعتماد وتفعيل الحساب؟',
            text: 'سيتم تفعيل حساب الطالب فوراً وتسجيل المواد المقررة لمرحلته وإرسال إشعار له.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، اعتماد الحساب',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then(result => {
            if (result.isConfirmed) doApprove();
        });
    } else {
        if (confirm('هل أنت متأكد من رغبتك باعتماد وتفعيل حساب هذا الطالب؟')) doApprove();
    }
}

function rejectStudent(id) {
    const doReject = (reason) => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'جاري رفض الطلب...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }
        axios.post(`/admin/students/${id}/reject`, {
            _token: '{{ csrf_token() }}',
            reason: reason || 'عدم استيفاء الشروط أو عدم وضوح الوثائق الرسمية.'
        })
        .then(res => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'تم رفض الطلب',
                    text: res.data?.message || 'تم رفض طلب الانضمام بنجاح.',
                    confirmButtonText: 'حسناً'
                }).then(() => {
                    window.location.href = "{{ route('admin.students.index') }}";
                });
            } else {
                alert('تم رفض الطلب بنجاح.');
                window.location.href = "{{ route('admin.students.index') }}";
            }
        })
        .catch(err => {
            if (typeof Swal !== 'undefined') {
                Swal.fire('خطأ!', err.response?.data?.message || 'حدث خطأ أثناء رفض الطلب.', 'error');
            } else {
                alert('حدث خطأ أثناء رفض الطلب.');
            }
        });
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'رفض طلب الانضمام',
            text: 'يرجى كتابة سبب الرفض ليظهر للطالب:',
            input: 'textarea',
            inputPlaceholder: 'مثال: الصورة غير واضحة أو رقم الهوية غير مطابق...',
            inputAttributes: {
                rows: 3
            },
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'تأكيد الرفض',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then(result => {
            if (result.isConfirmed) {
                doReject(result.value);
            }
        });
    } else {
        const reason = prompt('يرجى إدخال سبب الرفض:');
        if (reason !== null) doReject(reason);
    }
}
</script>
@endsection
