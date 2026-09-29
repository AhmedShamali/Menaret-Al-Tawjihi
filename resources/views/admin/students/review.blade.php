@extends('layouts.app')

@section('title', 'مراجعة بيانات الطالب')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; animation: fadeIn 0.8s ease;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px;">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--primary);">مراجعة طلب الانضمام 🔍</h1>
            <p style="color: var(--text-muted);">{{ __('يرجى التأكد من مطابقة صورة الهوية مع البيانات المدخلة.') }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="approveStudent({{ $student->id }})" class="btn btn-primary" style="background: #059669; padding: 12px 30px;">✅ اعتماد وتفعيل</button>
            <button onclick="rejectStudent({{ $student->id }})" class="btn" style="background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 12px 30px;">❌ رفض الطلب</button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">

        <!-- كرت الصور (المرفقات) -->
        <div class="glass-card" style="padding: 35px; text-align: center;">
            <h3 style="font-size: 1.1rem; margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;">🖼️ الوثائق المرفوعة</h3>

            <div style="margin-bottom: 30px;">
                <span style="display: block; font-size: 0.8rem; color: #94a3b8; margin-bottom: 10px;">{{ __('الصورة الشخصية') }}</span>
                <img src="{{ $student->photo_url }}" style="width: 200px; height: 200px; border-radius: 30px; object-fit: cover; border: 5px solid #f8fafc; box-shadow: 0 10px 30px rgba(0,0,0,0.1);" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($student->name_ar ?? 'طالب') }}&background=0284c7&color=fff&size=200&bold=true';">
            </div>

            <div>
                <span style="display: block; font-size: 0.8rem; color: #94a3b8; margin-bottom: 10px;">{{ __('وثيقة الهوية / شهادة الميلاد') }}</span>
                @if($student->id_photo)
                    @if($student->is_id_pdf)
                        <div style="padding: 25px 15px; background: #fef2f2; border: 2px dashed #fca5a5; border-radius: 15px; text-align: center;">
                            <i class="fa-solid fa-file-pdf" style="font-size: 3rem; color: #dc2626; margin-bottom: 8px; display: block;"></i>
                            <span style="font-size: 0.85rem; font-weight: 800; color: #991b1b; display: block; margin-bottom: 12px;">{{ __('وثيقة هوية رسمية بصيغة PDF') }}</span>
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.students.document.view', [$student->id, 'id_photo']) }}" target="_blank" class="btn btn-sm" style="background: #dc2626; color: white; padding: 6px 14px; border-radius: 8px; font-weight: 700; text-decoration: none;">
                                    <i class="fa-solid fa-eye"></i> {{ __('عرض المستند') }}
                                </a>
                                <a href="{{ route('admin.students.document.download', [$student->id, 'id_photo']) }}" class="btn btn-sm" style="background: #1e293b; color: white; padding: 6px 14px; border-radius: 8px; font-weight: 700; text-decoration: none;">
                                    <i class="fa-solid fa-download"></i> {{ __('تنزيل') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <img src="{{ $student->id_photo_url }}" style="width: 100%; border-radius: 15px; border: 1px solid #e2e8f0; cursor: zoom-in; max-height: 250px; object-fit: contain;" onclick="window.open('{{ route('admin.students.document.view', [$student->id, 'id_photo']) }}')" title="{{ __('انقر للمعاينة الكاملة') }}">
                        <div style="margin-top: 8px; text-align: center;">
                            <a href="{{ route('admin.students.document.download', [$student->id, 'id_photo']) }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 700; color: var(--primary); text-decoration: none;">
                                <i class="fa-solid fa-download"></i> {{ __('تنزيل الوثيقة الرسمية') }}
                            </a>
                        </div>
                    @endif
                @else
                    <div style="padding: 25px 15px; background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 15px; color: #94a3b8; font-size: 0.85rem; text-align: center;">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 2rem; color: #f59e0b; margin-bottom: 8px; display: block;"></i>
                        <span>{{ __('لم يقم الطالب بإرفاق وثيقة الهوية') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- كرت البيانات النصية -->
        <div class="glass-card" style="padding: 35px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;">📑 البيانات الرسمية</h3>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="info-box"><span>{{ __('الاسم العربي:') }}</span> <strong>{{ $student->name_ar }}</strong></div>
                <div class="info-box"><span>{{ __('الاسم الإنجليزي:') }}</span> <strong>{{ $student->name_en }}</strong></div>
                <div class="info-box"><span>{{ __('رقم الهوية:') }}</span> <strong style="font-family: monospace; letter-spacing: 2px;">{{ $student->nid }}</strong></div>
                <div class="info-box"><span>{{ __('العمر:') }}</span> <strong>{{ $student->age }} عام</strong></div>
                <div class="info-box"><span>{{ __('الجنس:') }}</span> <strong>{{ $student->gender }}</strong></div>
                <div class="info-box"><span>{{ __('المرحلة:') }}</span> <strong class="chip" style="background: var(--accent); color: white;">{{ $student->stage->label_ar }}</strong></div>
                <div class="info-box"><span>{{ __('الجوال:') }}</span> <strong dir="ltr">{{ $student->phone }}</strong></div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-box { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8fafc; border-radius: 15px; font-size: 0.9rem; }
    .info-box span { color: #64748b; font-weight: 600; }
    .info-box strong { color: var(--primary); }
</style>
@endsection
