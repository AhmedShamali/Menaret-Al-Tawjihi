@extends('layouts.app')

@php
    $studentDispName = (app()->getLocale() === 'en' && !empty($student->name_en)) ? $student->name_en : ($student->name_ar ?? ($student->name ?? __('طالب')));
    $stageDispName = (app()->getLocale() === 'en' && !empty($student->stage->name_en)) ? $student->stage->name_en : ($student->stage->label_ar ?? ($student->stage->name_ar ?? __('غير محددة')));
@endphp

@section('title', __('الملف الشخصي للطالب') . ' | ' . $studentDispName)

@section('content')
<div style="width: 100%; max-width: 100%; margin: 0 auto; padding-bottom: 50px;">

    <!-- شريط التنقل العلوي وزر العودة -->
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <a href="{{ route('admin.students.index') }}" style="background: #ffffff; color: var(--ed-primary, #1d4ed8); border: 1px solid #cbd5e1; padding: 10px 20px; border-radius: 8px; font-size: 0.88rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
            <i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i> {{ __('عودة لسجل الطلاب') }}
        </a>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" onclick="openDiscountModalDirect()" style="background: #ffffff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 10px 18px; border-radius: 8px; font-size: 0.88rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-tags"></i> {{ __('الخصم والمنحة') }} ({{ $student->discount_label }})
            </button>
            <button type="button" onclick="openSubjectModal()" style="background: var(--ed-primary, #1d4ed8); color: #ffffff; border: 1px solid #1e40af; padding: 10px 20px; border-radius: 8px; font-size: 0.88rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s;">
                <i class="fa-solid fa-book-open"></i> {{ __('إدارة وتعديل مواد الطالب') }}
            </button>
            <a href="{{ route('admin.students.edit', $student->id) }}" style="background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; padding: 10px 18px; border-radius: 8px; font-size: 0.88rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-pen-to-square"></i> {{ __('تعديل الحساب') }}
            </a>
        </div>
    </div>

    <!-- البطاقة الرئيسية لمعلومات الطالب -->
    <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); overflow: hidden; padding: 28px; text-align: start; margin-bottom: 24px; border-inline-start: 5px solid var(--ed-primary, #1d4ed8);">

        <!-- الهيدر الشخصي -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: #eff6ff; color: var(--ed-primary, #1d4ed8); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; border: 2px solid #bfdbfe; overflow: hidden;">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $studentDispName }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="fa-solid fa-user-graduate"></i>
                    @endif
                </div>

                <div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span style="background: #eff6ff; color: #1d4ed8; padding: 3px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 700;">{{ __('طالب مسجل') }}</span>
                        @if($student->status === 'active')
                            <span style="background: #ecfdf5; color: #059669; padding: 3px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 700;">
                                <i class="fa-solid fa-circle-check"></i> {{ __('حساب مفعّل') }}
                            </span>
                        @else
                            <span style="background: #fffbeb; color: #d97706; padding: 3px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 700;">
                                <i class="fa-solid fa-clock"></i> {{ __('بانتظار الموافقة') }}
                            </span>
                        @endif
                    </div>
                    <h1 style="font-size: 1.45rem; font-weight: 800; color: #0f172a; margin: 0;">{{ $studentDispName }}</h1>
                    @if($student->name_en && $student->name_en !== $student->name_ar && app()->getLocale() === 'ar')
                        <span style="font-size: 0.85rem; color: #64748b; font-weight: 600;">{{ $student->name_en }}</span>
                    @endif
                </div>
            </div>
            <div style="background: #f8fafc; padding: 10px 18px; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
                <span style="display: block; font-size: 0.72rem; color: #64748b; font-weight: 700;">{{ __('الرقم التعريفي') }}</span>
                <span class="font-mono" style="font-size: 1.15rem; font-weight: 800; color: var(--ed-primary, #1d4ed8);">#{{ $student->id }}</span>
            </div>
        </div>

        <!-- شبكة تفاصيل المعلومات الشاملة -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px;">

            <!-- البريد الإلكتروني -->
            <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; display: block; margin-bottom: 4px;">{{ __('البريد الإلكتروني') }}</span>
                <span class="font-mono" style="font-size: 0.88rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;" dir="ltr">
                    <i class="fa-solid fa-envelope" style="color: var(--ed-primary, #1d4ed8);"></i> {{ $student->email ?? __('غير متوفر') }}
                </span>
            </div>

            <!-- رقم الجوال -->
            <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; display: block; margin-bottom: 4px;">{{ __('رقم الجوال') }}</span>
                <span class="font-mono" style="font-size: 0.88rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;" dir="ltr">
                    <i class="fa-solid fa-phone" style="color: var(--ed-primary, #1d4ed8);"></i> {{ $student->phone ?? __('غير متوفر') }}
                </span>
            </div>

            <!-- المرحلة والفرع الدراسي -->
            <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; display: block; margin-bottom: 4px;">{{ __('الفرع والمرحلة الأكاديمية') }}</span>
                <span style="font-size: 0.88rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-graduation-cap" style="color: var(--ed-primary, #1d4ed8);"></i> {{ $stageDispName }}
                </span>
            </div>

            <!-- رقم الهوية الوطنية (NID) -->
            <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; display: block; margin-bottom: 4px;">{{ __('رقم الهوية الوطنية (NID)') }}</span>
                <span class="font-mono" style="font-size: 0.88rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-id-card" style="color: var(--ed-primary, #1d4ed8);"></i> <code>{{ $student->nid ?? __('غير متوفر') }}</code>
                </span>
            </div>

            <!-- المحافظة / المدينة -->
            <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; display: block; margin-bottom: 4px;">{{ __('المحافظة / المدينة') }}</span>
                <span style="font-size: 0.88rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-map-marker-alt" style="color: var(--ed-primary, #1d4ed8);"></i> {{ __($student->city ?? 'رام الله والبيرة') }}
                </span>
            </div>

            <!-- اسم المدرسة الثانوية -->
            <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; display: block; margin-bottom: 4px;">{{ __('المدرسة الثانوية') }}</span>
                <span style="font-size: 0.88rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-school" style="color: var(--ed-primary, #1d4ed8);"></i> {{ $student->school_name ?? __('غير مسجلة') }}
                </span>
            </div>

            <!-- هاتف ولي الأمر -->
            <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; display: block; margin-bottom: 4px;">{{ __('هاتف ولي الأمر / واتساب') }}</span>
                <span class="font-mono" style="font-size: 0.88rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;" dir="ltr">
                    <i class="fa-solid fa-user-shield" style="color: var(--ed-primary, #1d4ed8);"></i> {{ $student->guardian_phone ?? $student->whatsapp ?? __('غير متوفر') }}
                </span>
            </div>

            <!-- الجنس والعمر -->
            <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; display: block; margin-bottom: 4px;">{{ __('الجنس والعمر') }}</span>
                <span style="font-size: 0.88rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-user" style="color: var(--ed-primary, #1d4ed8);"></i> {{ __($student->gender ?? 'ذكر') }} ({{ $student->age ? $student->age . ' ' . __('سنة') : '18 ' . __('سنة') }})
                </span>
            </div>

            <!-- تاريخ الانضمام للمنصة -->
            <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; display: block; margin-bottom: 4px;">{{ __('تاريخ الانضمام للمنصة') }}</span>
                <span class="font-mono" style="font-size: 0.88rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-calendar-days" style="color: var(--ed-primary, #1d4ed8);"></i> {{ $student->created_at ? $student->created_at->format('Y-m-d') : __('غير متوفر') }}
                </span>
            </div>

            <!-- الخصم والمنحة الدراسية المعتمدة -->
            <div style="background: {{ $student->hasDiscount() ? '#f5f3ff' : '#f8fafc' }}; padding: 14px 16px; border-radius: 8px; border: 1px solid {{ $student->hasDiscount() ? '#ddd6fe' : '#e2e8f0' }}; grid-column: 1/-1;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <span style="font-size: 0.74rem; font-weight: 700; color: {{ $student->hasDiscount() ? '#7c3aed' : '#64748b' }}; display: block; margin-bottom: 4px;">
                            {{ __('الخصم أو المنحة الأكاديمية المعتمدة من الإدارة للطالب') }}
                        </span>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.05rem; font-weight: 800; color: {{ $student->hasDiscount() ? '#6d28d9' : '#334155' }};">
                                🏷️ {{ $student->discount_label }}
                            </span>
                            @if($student->discount_notes)
                                <span style="background: rgba(124, 58, 237, 0.08); color: #6d28d9; padding: 3px 8px; border-radius: 4px; font-size: 0.78rem; font-weight: 700;">
                                    {{ $student->discount_notes }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <button type="button" onclick="openDiscountModalDirect()" style="background: #7c3aed; color: white; border: none; padding: 6px 14px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-pen-to-square"></i> {{ __('تعديل الخصم والمنحة') }}
                    </button>
                </div>
            </div>

            <!-- الرسوم الشهرية المقررة للطالب ونظام الأقساط -->
            <div style="background: #eff6ff; padding: 14px 16px; border-radius: 8px; border: 1px solid #bfdbfe; grid-column: 1/-1;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <span style="font-size: 0.74rem; font-weight: 700; color: #1d4ed8; display: block; margin-bottom: 4px;">
                            <i class="fa-solid fa-coins"></i> {{ __('الرسوم الشهرية المقررة للطالب (نظام الأقساط الشهرية)') }}
                        </span>
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <span style="font-size: 1.15rem; font-weight: 800; color: #1e3a8a;">
                                <span id="displayMonthlyFee">{{ number_format($student->monthly_fee ?: 150, 2) }}</span> ₪ / {{ __('شهرياً') }}
                            </span>
                            <span style="background: white; border: 1px solid #bfdbfe; padding: 3px 8px; border-radius: 4px; font-size: 0.78rem; font-weight: 700; color: #1e40af;">
                                {{ __('الشهر المستحق حالياً:') }} {{ $student->currentDueMonthName() }} (الشهر {{ $student->currentAcademicMonthIndex() }} من تاريخ الاعتماد)
                            </span>
                            <span style="background: {{ $student->isMonthlyFeeDue() ? '#fef2f2' : '#ecfdf5' }}; color: {{ $student->isMonthlyFeeDue() ? '#b91c1c' : '#047857' }}; border: 1px solid {{ $student->isMonthlyFeeDue() ? '#fecaca' : '#a7f3d0' }}; padding: 3px 8px; border-radius: 4px; font-size: 0.78rem; font-weight: 700;">
                                {{ $student->isMonthlyFeeDue() ? __('يستحق السداد ⚠️') : __('مسدد بالكامل حتى تاريخه ✅') }}
                            </span>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input type="number" id="inputMonthlyFee" min="0" step="5" value="{{ (float)($student->monthly_fee ?: 150) }}" 
                               style="width: 90px; padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 800; font-family: monospace; font-size: 0.9rem; text-align: center;">
                        <button type="button" onclick="saveStudentMonthlyFee({{ $student->id }})" id="btnSaveMonthlyFee"
                                style="background: #1d4ed8; color: white; border: none; padding: 7px 14px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-check"></i> {{ __('حفظ الرسوم') }}
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- بطاقة وثائق وإثبات الهوية الرسمية للطالب -->
    <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); overflow: hidden; padding: 28px; text-align: start; margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 20px; flex-wrap: wrap; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 8px; background: #eff6ff; color: var(--ed-primary, #1d4ed8); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; border: 1px solid #bfdbfe;">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">
                        {{ __('وثائق الهوية الرسمية والصورة الشخصية') }}
                    </h2>
                    <span style="font-size: 0.8rem; color: #64748b;">{{ __('مطابقة بطاقة الهوية الفلسطينية والمستندات المسجلة للطالب') }}</span>
                </div>
            </div>

            <a href="{{ route('admin.students.edit', $student->id) }}" style="background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; padding: 7px 14px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('تحديث / رفع وثائق جديدة') }}
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">

            <!-- 1. وثيقة الهوية الفلسطينية -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-size: 0.84rem; font-weight: 800; color: #1e293b;">
                        <i class="fa-solid fa-address-card" style="color: var(--ed-primary, #1d4ed8);"></i> {{ __('بطاقة الهوية / شهادة الميلاد:') }}
                    </span>
                    @if($student->id_photo)
                        <span style="background: #ecfdf5; color: #047857; font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 4px;">
                            <i class="fa-solid fa-circle-check"></i> {{ __('وثيقة مرفقة') }}
                        </span>
                    @else
                        <span style="background: #fef2f2; color: #991b1b; font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 4px;">
                            <i class="fa-solid fa-triangle-exclamation"></i> {{ __('غير مرفقة بعد') }}
                        </span>
                    @endif
                </div>

                @if($student->id_photo)
                    @php
                        $isIdPdf = \Illuminate\Support\Str::endsWith(strtolower($student->id_photo), '.pdf');
                        $idUrl = asset('storage/' . $student->id_photo);
                    @endphp
                    <div style="background: white; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; text-align: center; margin-bottom: 12px;">
                        @if($isIdPdf)
                            <div style="padding: 20px; color: #ef4444;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 2.5rem; margin-bottom: 6px;"></i>
                                <span style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155;">{{ __('مستند PDF: بطاقة الهوية') }}</span>
                            </div>
                        @else
                            <img src="{{ $idUrl }}" alt="{{ __('بطاقة الهوية') }}" style="max-height: 200px; max-width: 100%; border-radius: 6px; object-fit: contain; cursor: pointer;" onclick="previewIdModal('{{ $idUrl }}', '{{ addslashes($studentDispName) }}', '{{ $student->nid }}')">
                        @endif
                    </div>

                    <div style="display: flex; gap: 8px;">
                        @if(!$isIdPdf)
                            <button type="button" onclick="previewIdModal('{{ $idUrl }}', '{{ addslashes($studentDispName) }}', '{{ $student->nid }}')" style="flex: 1; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 8px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                <i class="fa-solid fa-expand"></i> {{ __('معاينة وتكبير') }}
                            </button>
                        @endif
                        <a href="{{ $idUrl }}" target="_blank" download style="flex: 1; background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; padding: 8px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                            <i class="fa-solid fa-download"></i> {{ __('فتح / تنزيل') }}
                        </a>
                    </div>
                @else
                    <div style="background: white; border: 1.5px dashed #cbd5e1; border-radius: 8px; padding: 24px 16px; text-align: center; color: #94a3b8;">
                        <i class="fa-solid fa-id-badge" style="font-size: 2.2rem; color: #cbd5e1; margin-bottom: 6px;"></i>
                        <p style="margin: 0 0 10px 0; font-size: 0.82rem; font-weight: 600;">{{ __('لم يقم الطالب برفع صورة بطاقة الهوية بعد.') }}</p>
                        <a href="{{ route('admin.students.edit', $student->id) }}" style="background: var(--ed-primary, #1d4ed8); color: white; padding: 6px 14px; border-radius: 6px; font-size: 0.78rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('رفع الوثيقة الآن') }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- 2. بطاقة الصورة الشخصية وبيانات التحقق -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px;">
                <span style="font-size: 0.84rem; font-weight: 800; color: #1e293b; display: block; margin-bottom: 12px;">
                    <i class="fa-solid fa-circle-user" style="color: var(--ed-primary, #1d4ed8);"></i> {{ __('الصورة الشخصية وبيانات الطالب المعتمدة:') }}
                </span>

                <div style="display: flex; gap: 14px; align-items: center; background: white; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                    <div style="width: 68px; height: 68px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; flex-shrink: 0;">
                        <img src="{{ $student->photo_url }}" alt="{{ __('الصورة الشخصية') }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <strong style="font-size: 0.95rem; color: #0f172a; display: block; margin-bottom: 3px;">{{ $studentDispName }}</strong>
                        <span class="font-mono" style="font-size: 0.78rem; color: #64748b; display: block; margin-bottom: 3px;">
                            {{ __('رقم الهوية:') }} <code style="font-size: 0.82rem;">{{ $student->nid }}</code>
                        </span>
                        <span style="font-size: 0.78rem; color: #64748b; display: block;">
                            {{ __('الفرع:') }} {{ $stageDispName }}
                        </span>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 0.8rem; color: #475569;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 5px;">
                        <span>{{ __('المدرسة الثانوية:') }}</span>
                        <strong style="color: #0f172a;">{{ $student->school_name ?? __('غير مسجلة') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 5px;">
                        <span>{{ __('المحافظة:') }}</span>
                        <strong style="color: #0f172a;">{{ __($student->city ?? 'رام الله والبيرة') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>{{ __('هاتف ولي الأمر:') }}</span>
                        <strong class="font-mono" style="color: #0f172a;" dir="ltr">{{ $student->guardian_phone ?? $student->whatsapp ?? __('غير متوفر') }}</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- قسم المواد الدراسية المقيد بها الطالب -->
    <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); overflow: hidden; padding: 28px; text-align: start;">

        <!-- هيدر قسم المواد -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 20px; flex-wrap: wrap; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 8px; background: #eff6ff; color: var(--ed-primary, #1d4ed8); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; border: 1px solid #bfdbfe;">
                    <i class="fa-solid fa-book-bookmark"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                        {{ __('المواد الدراسية المقيد بها الطالب') }}
                        <span style="background: #eff6ff; color: #1d4ed8; font-size: 0.78rem; font-weight: 700; padding: 2px 8px; border-radius: 4px;" id="enrolledCountBadge">
                            {{ $student->enrolledSubjects->count() }} {{ __('مواد') }}
                        </span>
                    </h2>
                    <span style="font-size: 0.8rem; color: #64748b;">{{ __('قائمة المواد المتاحة للطالب في حسابه الدراسي مع إمكانية التعديل والإلغاء') }}</span>
                </div>
            </div>

            <!-- زر إضافة / تعديل المواد -->
            <button type="button" onclick="openSubjectModal()" style="background: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 700; font-size: 0.84rem; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.15s ease;">
                <i class="fa-solid fa-circle-plus"></i> {{ __('تخصيص واختيار المواد') }}
            </button>
        </div>

        <!-- قائمة كروت المواد المشترك بها -->
        @if($student->enrolledSubjects->count() > 0)
            <div id="enrolledSubjectsGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
                @foreach($student->enrolledSubjects as $subject)
                    @php
                        $subName = (app()->getLocale() === 'en' && !empty($subject->name_en)) ? $subject->name_en : $subject->name_ar;
                    @endphp
                    <div id="subject_card_{{ $subject->id }}" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between; gap: 12px;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="font-size: 1.6rem; width: 42px; height: 42px; border-radius: 8px; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                    {{ $subject->icon ?? '📖' }}
                                </div>
                                <div>
                                    <h3 style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin: 0 0 2px 0;">{{ $subName }}</h3>
                                    <span style="font-size: 0.75rem; color: {{ $subject->hasAssignedTeacher() ? '#1d4ed8' : '#b45309' }}; display: block; font-weight: 600;">
                                        <i class="fa-solid fa-chalkboard-user"></i>
                                        {{ $subject->teacher_display_name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- تفاصيل الاشتراك وزر الإلغاء -->
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 0.78rem;">
                            <span style="background: #ecfdf5; color: #047857; padding: 3px 8px; border-radius: 4px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-check"></i> {{ __('مفعّل ونشط') }}
                            </span>

                            <button type="button" onclick="confirmRemoveSubject({{ $student->id }}, {{ $subject->id }}, '{{ addslashes($subName) }}')" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.74rem; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-trash-can"></i> {{ __('إلغاء المادة') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- الحالة الفارغة: الطالب ليس لديه أي مواد مسجلة -->
            <div id="emptySubjectsState" style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 10px; border: 1.5px dashed #cbd5e1;">
                <div style="font-size: 2.5rem; margin-bottom: 8px;">📚</div>
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #334155; margin-bottom: 4px;">{{ __('لا توجد أي مواد دراسية مقيدة لهذا الطالب حتى الآن') }}</h3>
                <p style="font-size: 0.84rem; color: #64748b; max-width: 500px; margin: 0 auto 16px auto;">
                    {{ __('يمكنك اختيار وتحديد المواد المناسبة للطالب لتفعيلها في لوحة دراسته على الفور.') }}
                </p>
                <button type="button" onclick="openSubjectModal()" style="background: var(--ed-primary, #1d4ed8); color: white; border: none; padding: 9px 20px; border-radius: 8px; font-weight: 700; font-size: 0.86rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-circle-plus"></i> {{ __('اختيار وتفعيل مواد الطالب الآن') }}
                </button>
            </div>
        @endif

    </div>

</div>

<!-- النافذة المنبثقة التفاعلية لإدارة وتعديل مواد الطالب (Modal الفاتح) -->
<div id="subjectModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(2px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: #ffffff; width: 100%; max-width: 640px; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; max-height: 90vh; display: flex; flex-direction: column; border: 1px solid #e2e8f0;">

        <!-- هيدر المودال الفاتح -->
        <div style="padding: 18px 24px; background: #ffffff; border-bottom: 1px solid #e2e8f0; color: #0f172a; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.4rem;">📚</span>
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0; color: #0f172a;">{{ __('تخصيص مواد الطالب') }}</h3>
                    <span style="font-size: 0.78rem; color: #64748b;">{{ __('الطالب:') }} {{ $studentDispName }} • {{ $stageDispName }}</span>
                </div>
            </div>
            <button type="button" onclick="closeSubjectModal()" style="background: transparent; border: none; color: #64748b; font-size: 1.3rem; cursor: pointer;">✕</button>
        </div>

        <!-- شريط الإجراءات السريعة في المودال -->
        <div style="padding: 12px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <span style="font-size: 0.82rem; font-weight: 700; color: #475569;">
                {{ __('حدد المواد التي تريد تمكين الطالب من دراستها:') }}
            </span>
            <div style="display: flex; gap: 8px;">
                <button type="button" onclick="toggleModalCheckboxes(true)" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">{{ __('تحديد الكل') }}</button>
                <button type="button" onclick="toggleModalCheckboxes(false)" style="background: #ffffff; color: #64748b; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">{{ __('إلغاء التحديد') }}</button>
            </div>
        </div>

        <!-- قائمة المواد بخانات الاختيار -->
        <form id="syncSubjectsForm" method="POST" action="{{ route('admin.students.syncSubjects', $student->id) }}" style="padding: 20px 24px; overflow-y: auto; flex: 1;">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 10px;">
                @php
                    $enrolledIds = $student->enrolledSubjects->pluck('id')->toArray();
                    $stageSubjects = $student->stage?->subjects ?? collect();
                @endphp

                @if($stageSubjects->count() > 0)
                    @foreach($stageSubjects as $sub)
                        @php 
                            $isChecked = in_array($sub->id, $enrolledIds); 
                            $subTitle = (app()->getLocale() === 'en' && !empty($sub->name_en)) ? $sub->name_en : $sub->name_ar;
                        @endphp
                        <label class="modal-subject-card" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 1.5px solid {{ $isChecked ? '#1d4ed8' : '#e2e8f0' }}; background: {{ $isChecked ? '#eff6ff' : '#ffffff' }}; border-radius: 8px; cursor: pointer; transition: 0.15s; user-select: none;">
                            <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}" class="modal-subject-cb" {{ $isChecked ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: #1d4ed8; cursor: pointer;">
                            <span style="font-size: 1.3rem;">{{ $sub->icon ?? '📖' }}</span>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 700; font-size: 0.86rem; color: #1e293b;">{{ $subTitle }}</div>
                                <div style="font-size: 0.72rem; color: {{ $sub->hasAssignedTeacher() ? '#1d4ed8' : '#b45309' }}; font-weight: 600;">
                                    <i class="fa-solid fa-chalkboard-user" style="font-size: 0.68rem;"></i>
                                    {{ $sub->teacher_display_name }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                @else
                    <div style="grid-column: 1/-1; text-align: center; padding: 20px; color: #94a3b8;">
                        {{ __('لا توجد مواد مسجلة لهذا الفرع حالياً.') }}
                    </div>
                @endif
            </div>
        </form>

        <!-- فوتر المودال -->
        <div style="padding: 14px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" onclick="closeSubjectModal()" style="background: #ffffff; color: #64748b; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; font-weight: 700; font-size: 0.84rem; cursor: pointer;">
                {{ __('إلغاء') }}
            </button>
            <button type="button" onclick="submitSyncSubjects()" id="btnSaveSubjects" style="background: #10b981; color: white; border: none; padding: 8px 20px; border-radius: 6px; font-weight: 700; font-size: 0.84rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-floppy-disk"></i> {{ __('حفظ وتفعيل المواد') }}
            </button>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    const studentShowI18n = {
        saving: "{{ __('جاري الحفظ...') }}",
        connecting: "{{ __('جاري الربط وتحديث السيرفر...') }}",
        savedSuccess: "{{ __('تم الحفظ بنجاح! 🎉') }}",
        savedDesc: "{{ __('تم تحديث قائمة مواد الطالب وتفعيلها.') }}",
        okBtn: "{{ __('حسناً') }}",
        saveBtnText: '<i class="fa-solid fa-floppy-disk"></i> {{ __("حفظ وتفعيل المواد") }}',
        unenrollTitle: "{{ __('إلغاء اشتراك المادة') }}",
        unenrollConfirmText: "{{ __('هل أنت متأكد من رغبتك في إلغاء اشتراك الطالب في مادة') }}",
        unenrollConfirmBtn: "{{ __('نعم، إلغاء الاشتراك') }}",
        cancelBtn: "{{ __('تراجع') }}",
        unenrollDone: "{{ __('تم إلغاء المادة') }}",
        failedTitle: "{{ __('فشلت العملية') }}",
        failedDesc: "{{ __('حدث خطأ أثناء إلغاء المادة.') }}",
        discountSaveSuccess: "{{ __('تم الحفظ بنجاح') }}",
        discountSaveFailed: "{{ __('فشل حفظ الخصم') }}",
        idCardPrefix: "{{ __('بطاقة الهوية:') }}",
        nidPrefix: "{{ __('رقم الهوية الفلسطينية:') }}"
    };

    const studentId = {{ $student->id }};

    function openSubjectModal() {
        const modal = document.getElementById('subjectModalOverlay');
        modal.style.display = 'flex';
    }

    function closeSubjectModal() {
        const modal = document.getElementById('subjectModalOverlay');
        modal.style.display = 'none';
    }

    document.getElementById('subjectModalOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSubjectModal();
        }
    });

    document.querySelectorAll('.modal-subject-card').forEach(card => {
        const cb = card.querySelector('.modal-subject-cb');
        if (cb) {
            cb.addEventListener('change', () => {
                if (cb.checked) {
                    card.style.borderColor = '#1d4ed8';
                    card.style.background = '#eff6ff';
                } else {
                    card.style.borderColor = '#e2e8f0';
                    card.style.background = '#ffffff';
                }
            });
        }
    });

    function toggleModalCheckboxes(selectAll) {
        document.querySelectorAll('.modal-subject-cb').forEach(cb => {
            cb.checked = selectAll;
            const card = cb.closest('.modal-subject-card');
            if (card) {
                card.style.borderColor = selectAll ? '#1d4ed8' : '#e2e8f0';
                card.style.background = selectAll ? '#eff6ff' : '#ffffff';
            }
        });
    }

    async function submitSyncSubjects() {
        const btn = document.getElementById('btnSaveSubjects');
        const form = document.getElementById('syncSubjectsForm');
        const checkedBoxes = document.querySelectorAll('.modal-subject-cb:checked');
        const subjectIds = Array.from(checkedBoxes).map(cb => parseInt(cb.value));

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + studentShowI18n.saving;

        try {
            const syncUrl = "{{ route('admin.students.syncSubjects', $student->id) }}";
            const res = await axios.post(syncUrl, {
                subject_ids: subjectIds,
                _token: '{{ csrf_token() }}'
            });

            // إغلاق نافذة المودال فوراً حتى لا تحجب رسالة النجاح وتمنع استجابة الصفحة
            closeSubjectModal();

            if (typeof Swal !== 'undefined') {
                await Swal.fire({
                    icon: 'success',
                    title: studentShowI18n.savedSuccess,
                    text: (res.data && res.data.message) ? res.data.message : studentShowI18n.savedDesc,
                    timer: 1300,
                    showConfirmButton: false
                });
                window.location.reload();
            } else {
                window.location.reload();
            }
        } catch (error) {
            btn.disabled = false;
            btn.innerHTML = studentShowI18n.saveBtnText;

            if (form) {
                form.submit();
            } else {
                const errMsg = error.response?.data?.message || studentShowI18n.failedDesc;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: studentShowI18n.failedTitle,
                        text: errMsg,
                        confirmButtonText: studentShowI18n.okBtn
                    });
                } else {
                    alert(errMsg);
                }
            }
        }
    }

    function confirmRemoveSubject(sId, subId, subName) {
        Swal.fire({
            title: studentShowI18n.unenrollTitle,
            text: `${studentShowI18n.unenrollConfirmText} (${subName})?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: studentShowI18n.unenrollConfirmBtn,
            cancelButtonText: studentShowI18n.cancelBtn
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await axios.post(`{{ url('admin/students') }}/${sId}/toggle-subject/${subId}`, {
                        _token: '{{ csrf_token() }}'
                    });

                    Swal.fire({
                        icon: 'success',
                        title: response.data.title || studentShowI18n.unenrollDone,
                        text: response.data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: studentShowI18n.failedTitle,
                        text: studentShowI18n.failedDesc,
                        confirmButtonText: studentShowI18n.okBtn
                    });
                }
            }
        });
    }

    function openDiscountModalDirect() {
        const overlay = document.getElementById('studentDiscountModalOverlay');
        if (overlay) {
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDiscountModalDirect() {
        const overlay = document.getElementById('studentDiscountModalOverlay');
        if (overlay) {
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function setShowDiscountType(type) {
        document.getElementById('showDiscountType').value = type;
        const btnPercent = document.getElementById('showTypeBtnPercent');
        const btnFixed = document.getElementById('showTypeBtnFixed');
        const unitLabel = document.getElementById('showDiscountUnitLabel');

        if (type === 'percent') {
            btnPercent.style.background = '#7c3aed';
            btnPercent.style.color = '#ffffff';
            btnPercent.style.borderColor = '#7c3aed';
            btnFixed.style.background = '#ffffff';
            btnFixed.style.color = '#64748b';
            btnFixed.style.borderColor = '#cbd5e1';
            unitLabel.innerText = '{{ __("% (نسبة مئوية)") }}';
            document.getElementById('showDiscountValue').placeholder = '25, 50, 100';
            document.getElementById('showDiscountValue').max = '100';
        } else {
            btnFixed.style.background = '#7c3aed';
            btnFixed.style.color = '#ffffff';
            btnFixed.style.borderColor = '#7c3aed';
            btnPercent.style.background = '#ffffff';
            btnPercent.style.color = '#64748b';
            btnPercent.style.borderColor = '#cbd5e1';
            unitLabel.innerText = '{{ __("₪ (شيكل فلسطيني)") }}';
            document.getElementById('showDiscountValue').placeholder = '50, 100';
            document.getElementById('showDiscountValue').removeAttribute('max');
        }
    }

    function setShowQuickDiscount(percent, notes) {
        setShowDiscountType('percent');
        document.getElementById('showDiscountValue').value = percent;
        if (notes) {
            document.getElementById('showDiscountNotes').value = notes;
        } else if (percent === 0) {
            document.getElementById('showDiscountNotes').value = '';
        }
    }

    async function handleShowDiscountSubmit(e) {
        e.preventDefault();
        const type = document.getElementById('showDiscountType').value;
        const val = parseFloat(document.getElementById('showDiscountValue').value) || 0;
        const notes = document.getElementById('showDiscountNotes').value.trim();

        const btn = document.getElementById('btnSaveShowDiscount');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + studentShowI18n.saving;

        try {
            const res = await axios.post("{{ route('admin.students.discount', $student->id) }}", {
                discount_type: val > 0 ? type : 'none',
                discount_value: val,
                discount_notes: notes
            });

            closeDiscountModalDirect();

            Swal.fire({
                icon: 'success',
                title: res.data.title || studentShowI18n.discountSaveSuccess,
                text: res.data.message || '',
                timer: 1300,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
            setTimeout(() => location.reload(), 1400);
        } catch (err) {
            btn.disabled = false;
            btn.innerHTML = originalText;
            const msg = (err.response && err.response.data && err.response.data.title) ? err.response.data.title : studentShowI18n.discountSaveFailed;
            Swal.fire('Error', msg, 'error');
        }
    }
</script>

<!-- النافذة المنبثقة للخصم في صفحة بروفايل الطالب -->
<div id="studentDiscountModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(2px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: #ffffff; width: 100%; max-width: 500px; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; border: 1px solid #e2e8f0;">
        
        <div style="padding: 16px 20px; background: #ffffff; border-bottom: 1px solid #e2e8f0; color: #0f172a; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.3rem;">🏷️</span>
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0; color: #0f172a;">{{ __('تحديد خصم أو منحة للطالب') }}</h3>
                    <span style="font-size: 0.78rem; color: #64748b;">{{ $studentDispName }}</span>
                </div>
            </div>
            <button type="button" onclick="closeDiscountModalDirect()" style="background: transparent; border: none; color: #64748b; font-size: 1.2rem; cursor: pointer;">✕</button>
        </div>

        <form onsubmit="handleShowDiscountSubmit(event)" style="padding: 20px;">
            <input type="hidden" id="showDiscountType" value="{{ (float)$student->custom_discount_fixed > 0 ? 'fixed' : 'percent' }}">

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">{{ __('نوع الخصم المعتمد:') }}</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <button type="button" id="showTypeBtnPercent" onclick="setShowDiscountType('percent')" style="padding: 8px; border-radius: 6px; border: 1.5px solid {{ (float)$student->custom_discount_fixed > 0 ? '#cbd5e1' : '#7c3aed' }}; background: {{ (float)$student->custom_discount_fixed > 0 ? 'white' : '#7c3aed' }}; color: {{ (float)$student->custom_discount_fixed > 0 ? '#64748b' : 'white' }}; font-weight: 700; font-size: 0.84rem; cursor: pointer;">
                        {{ __('نسبة مئوية (%)') }}
                    </button>
                    <button type="button" id="showTypeBtnFixed" onclick="setShowDiscountType('fixed')" style="padding: 8px; border-radius: 6px; border: 1.5px solid {{ (float)$student->custom_discount_fixed > 0 ? '#7c3aed' : '#cbd5e1' }}; background: {{ (float)$student->custom_discount_fixed > 0 ? '#7c3aed' : 'white' }}; color: {{ (float)$student->custom_discount_fixed > 0 ? 'white' : '#64748b' }}; font-weight: 700; font-size: 0.84rem; cursor: pointer;">
                        {{ __('مبلغ نقدي ثابت (₪)') }}
                    </button>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #64748b; margin-bottom: 6px;">{{ __('خيارات سريعة:') }}</label>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="button" onclick="setShowQuickDiscount(15, '{{ __('15% تشجيعي') }}')" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 4px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">{{ __('15% تشجيعي') }}</button>
                    <button type="button" onclick="setShowQuickDiscount(25, '{{ __('25% تفوق') }}')" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 4px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">{{ __('25% تفوق') }}</button>
                    <button type="button" onclick="setShowQuickDiscount(50, '{{ __('50% نصف منحة') }}')" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 4px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">{{ __('50% نصف منحة') }}</button>
                    <button type="button" onclick="setShowQuickDiscount(100, '{{ __('✨ إعفاء كامل 100%') }}')" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 4px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">{{ __('✨ إعفاء كامل 100%') }}</button>
                    <button type="button" onclick="setShowQuickDiscount(0, '')" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 4px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; cursor: pointer;">{{ __('❌ إلغاء الخصم') }}</button>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155;">{{ __('قيمة الخصم:') }}</label>
                    <span id="showDiscountUnitLabel" style="font-size: 0.74rem; color: #7c3aed; font-weight: 700;">
                        {{ (float)$student->custom_discount_fixed > 0 ? __('₪ (شيكل فلسطيني)') : __('% (نسبة مئوية)') }}
                    </span>
                </div>
                <input type="number" id="showDiscountValue" min="0" step="any" value="{{ (float)$student->custom_discount_fixed > 0 ? (float)$student->custom_discount_fixed : ((float)$student->custom_discount_percent > 0 ? (float)$student->custom_discount_percent : '') }}" placeholder="25, 50" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.95rem; font-weight: 700; font-family: monospace; outline: none; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 4px;">{{ __('سبب الخصم أو ملاحظات المنحة:') }}</label>
                <input type="text" id="showDiscountNotes" value="{{ $student->discount_notes }}" placeholder="{{ __('مثال: منحة تفوق توجيهي / إعفاء خاص') }}" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.84rem; outline: none; box-sizing: border-box;">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" id="btnSaveShowDiscount" style="flex: 1; background: #7c3aed; color: white; border: none; padding: 11px; border-radius: 8px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <span>{{ __('حفظ وتحديث الخصم') }}</span>
                    <i class="fa-solid fa-check"></i>
                </button>
                <button type="button" onclick="closeDiscountModalDirect()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 11px 18px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                    {{ __('إلغاء') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- نافذة تكبير ومعاينة بطاقة الهوية الفلسطينية للتحقق الرسمي -->
<div id="idPhotoModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(2px); z-index: 99999; justify-content: center; align-items: center; padding: 20px;" onclick="closeIdModal()">
    <div style="background: #ffffff; width: 100%; max-width: 720px; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15); overflow: hidden; border: 1px solid #e2e8f0;" onclick="event.stopPropagation()">
        <div style="padding: 16px 20px; background: #ffffff; border-bottom: 1px solid #e2e8f0; color: #0f172a; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: var(--ed-primary, #1d4ed8); display: grid; place-items: center; font-size: 1.1rem; border: 1px solid #bfdbfe;">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 800; margin: 0; color: #0f172a;" id="modalStudentName">{{ __('معاينة بطاقة الهوية الفلسطينية') }}</h3>
                    <span class="font-mono" style="font-size: 0.76rem; color: #64748b;" id="modalStudentNid">{{ __('رقم الهوية:') }} {{ $student->nid }}</span>
                </div>
            </div>
            <button type="button" onclick="closeIdModal()" style="background: transparent; border: none; color: #64748b; font-size: 1.2rem; cursor: pointer;">✕</button>
        </div>
        <div style="padding: 20px; background: #f8fafc; text-align: center; max-height: 70vh; overflow-y: auto;">
            <img id="modalIdImg" src="" alt="{{ __('بطاقة الهوية') }}" style="max-height: 480px; max-width: 100%; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); object-fit: contain;">
        </div>
        <div style="padding: 14px 20px; background: white; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <a id="modalDownloadBtn" href="" target="_blank" download style="background: var(--ed-primary, #1d4ed8); color: white; padding: 8px 18px; border-radius: 6px; font-size: 0.82rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-download"></i> {{ __('تنزيل الوثيقة الرسمية') }}
            </a>
            <button type="button" onclick="closeIdModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; font-weight: 700; font-size: 0.82rem; cursor: pointer;">
                {{ __('إغلاق النافذة') }}
            </button>
        </div>
    </div>
</div>

<script>
function previewIdModal(imgUrl, studentName, studentNid) {
    document.getElementById('modalIdImg').src = imgUrl;
    document.getElementById('modalDownloadBtn').href = imgUrl;
    document.getElementById('modalStudentName').textContent = studentShowI18n.idCardPrefix + ' ' + studentName;
    document.getElementById('modalStudentNid').textContent = studentShowI18n.nidPrefix + ' ' + studentNid;
    const modal = document.getElementById('idPhotoModalOverlay');
    modal.style.display = 'flex';
}

function closeIdModal() {
    document.getElementById('idPhotoModalOverlay').style.display = 'none';
}

function saveStudentMonthlyFee(studentId) {
    const feeVal = document.getElementById('inputMonthlyFee').value;
    const btn = document.getElementById('btnSaveMonthlyFee');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __("جاري الحفظ...") }}';

    fetch(`/admin/students/${studentId}/monthly-fee`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ monthly_fee: feeVal })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = origHtml;
        if (data.success) {
            document.getElementById('displayMonthlyFee').textContent = parseFloat(data.monthly_fee).toFixed(2);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: '{{ __("تم التحديث") }}', text: data.message, timer: 2000, showConfirmButton: false });
            } else {
                alert(data.message);
            }
        } else {
            alert(data.message || '{{ __("حدث خطأ أثناء حفظ الرسوم") }}');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = origHtml;
        alert('{{ __("تعذر الاتصال بالخادم") }}');
    });
}
</script>

@endsection
