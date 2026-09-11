@extends('layouts.app')

@section('title', 'الملف الشخصي للطالب | ' . ($student->name_ar ?? $student->name ?? 'طالب'))

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding-bottom: 50px;" dir="rtl">

    <!-- شريط التنقل العلوي وزر العودة -->
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <a href="{{ route('admin.students.index') }}" style="background: #ffffff; color: #4f46e5; border: 1.5px solid #e0e7ff; padding: 10px 20px; border-radius: 12px; font-size: 0.9rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 2px 6px rgba(79, 70, 229, 0.08);" onmouseover="this.style.background='#4f46e5';this.style.color='#fff'" onmouseout="this.style.background='#ffffff';this.style.color='#4f46e5'">
            <i class="fa-solid fa-arrow-right"></i> عودة لسجل الطلاب
        </a>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" onclick="openDiscountModalDirect()" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: #ffffff; border: none; padding: 10px 18px; border-radius: 12px; font-size: 0.9rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.25);">
                <i class="fa-solid fa-tags"></i> الخصم والمنحة ({{ $student->discount_label }})
            </button>
            <button type="button" onclick="openSubjectModal()" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: #ffffff; border: none; padding: 10px 20px; border-radius: 12px; font-size: 0.9rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3); transition: 0.2s;">
                <i class="fa-solid fa-book-open"></i> إدارة وتعديل مواد الطالب
            </button>
            <a href="{{ route('admin.students.edit', $student->id) }}" style="background: #f8fafc; color: #334155; border: 1.5px solid #cbd5e1; padding: 10px 18px; border-radius: 12px; font-size: 0.9rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-pen-to-square"></i> تعديل الحساب
            </a>
        </div>
    </div>

    <!-- البطاقة الرئيسية لمعلومات الطالب -->
    <div style="background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.03); overflow: hidden; padding: 35px; text-align: right; margin-bottom: 30px;">

        <!-- الهيدر الشخصي -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 25px; margin-bottom: 25px; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <!-- صورة أو أيقونة الطالب -->
                <div style="width: 85px; height: 85px; border-radius: 22px; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25); overflow: hidden;">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name_ar }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="fa-solid fa-user-graduate"></i>
                    @endif
                </div>

                <div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <span style="background: #eef2ff; color: #4f46e5; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 800;">طالب مسجل</span>
                        @if($student->status === 'active')
                            <span style="background: #ecfdf5; color: #059669; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 800;">
                                <i class="fa-solid fa-circle-check"></i> حساب مفعّل
                            </span>
                        @else
                            <span style="background: #fffbeb; color: #d97706; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 800;">
                                <i class="fa-solid fa-clock"></i> بانتظار الموافقة
                            </span>
                        @endif
                    </div>
                    <h1 style="font-size: 1.65rem; font-weight: 900; color: #0f172a; margin: 0;">{{ $student->name_ar ?? $student->name ?? 'غير متوفر' }}</h1>
                    @if($student->name_en && $student->name_en !== $student->name_ar)
                        <span style="font-size: 0.9rem; color: #64748b; font-weight: 600;">{{ $student->name_en }}</span>
                    @endif
                </div>
            </div>
            <div style="background: #f8fafc; padding: 12px 22px; border-radius: 14px; border: 1.5px solid #e2e8f0; text-align: center;">
                <span style="display: block; font-size: 0.75rem; color: #94a3b8; font-weight: 700;">الرقم التعريفي</span>
                <span style="font-size: 1.2rem; font-weight: 900; color: #4f46e5;">#{{ $student->id }}</span>
            </div>
        </div>

        <!-- شبكة تفاصيل المعلومات الشاملة -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">

            <!-- البريد الإلكتروني -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">البريد الإلكتروني</span>
                <span style="font-size: 0.92rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px; direction: ltr; justify-content: flex-end;">
                    {{ $student->email ?? 'غير متوفر' }} <i class="fa-solid fa-envelope" style="color: #4f46e5;"></i>
                </span>
            </div>

            <!-- رقم الجوال -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">رقم الجوال</span>
                <span style="font-size: 0.92rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px; direction: ltr; justify-content: flex-end;">
                    {{ $student->phone ?? 'غير متوفر' }} <i class="fa-solid fa-phone" style="color: #4f46e5;"></i>
                </span>
            </div>

            <!-- المرحلة والفرع الدراسي -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">الفرع والمرحلة الأكاديمية</span>
                <span style="font-size: 0.92rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-graduation-cap" style="color: #4f46e5;"></i> {{ $student?->stage?->label_ar ?? $student?->stage?->name ?? 'غير محددة' }}
                </span>
            </div>

            <!-- رقم الهوية الوطنية (NID) -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">رقم الهوية الوطنية (NID)</span>
                <span style="font-size: 0.92rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-id-card" style="color: #4f46e5;"></i> <code>{{ $student->nid ?? 'غير متوفر' }}</code>
                </span>
            </div>

            <!-- المحافظة / المدينة -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">المحافظة / المدينة</span>
                <span style="font-size: 0.92rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-map-marker-alt" style="color: #4f46e5;"></i> {{ $student->city ?? 'رام الله والبيرة' }}
                </span>
            </div>

            <!-- اسم المدرسة الثانوية -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">المدرسة الثانوية</span>
                <span style="font-size: 0.92rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-school" style="color: #4f46e5;"></i> {{ $student->school_name ?? 'غير مسجلة' }}
                </span>
            </div>

            <!-- هاتف ولي الأمر -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">هاتف ولي الأمر / واتساب</span>
                <span style="font-size: 0.92rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px; direction: ltr; justify-content: flex-end;">
                    {{ $student->guardian_phone ?? $student->whatsapp ?? 'غير متوفر' }} <i class="fa-solid fa-user-shield" style="color: #4f46e5;"></i>
                </span>
            </div>

            <!-- الجنس والعمر -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">الجنس والعمر</span>
                <span style="font-size: 0.92rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-user" style="color: #4f46e5;"></i> {{ $student->gender ?? 'ذكر' }} ({{ $student->age ? $student->age . ' سنة' : '18 سنة' }})
                </span>
            </div>

            <!-- تاريخ الانضمام للمنصة -->
            <div style="background: #f8fafc; padding: 16px 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 6px;">تاريخ الانضمام للمنصة</span>
                <span style="font-size: 0.92rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-calendar-days" style="color: #4f46e5;"></i> {{ $student->created_at ? $student->created_at->format('Y-m-d') : 'غير متوفر' }}
                </span>
            </div>

            <!-- الخصم والمنحة الدراسية المعتمدة -->
            <div style="background: {{ $student->hasDiscount() ? '#f5f3ff' : '#f8fafc' }}; padding: 16px 20px; border-radius: 14px; border: 1.5px solid {{ $student->hasDiscount() ? '#ddd6fe' : '#e2e8f0' }}; grid-column: 1/-1;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: {{ $student->hasDiscount() ? '#7c3aed' : '#94a3b8' }}; display: block; margin-bottom: 4px;">
                            الخصم أو المنحة الأكاديمية المعتمدة من الإدارة للطالب
                        </span>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.15rem; font-weight: 900; color: {{ $student->hasDiscount() ? '#6d28d9' : '#475569' }};">
                                🏷️ {{ $student->discount_label }}
                            </span>
                            @if($student->discount_notes)
                                <span style="background: rgba(124, 58, 237, 0.1); color: #6d28d9; padding: 3px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700;">
                                    {{ $student->discount_notes }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <button type="button" onclick="openDiscountModalDirect()" style="background: #7c3aed; color: white; border: none; padding: 7px 16px; border-radius: 10px; font-weight: 700; font-size: 0.82rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-pen-to-square"></i> تعديل الخصم والمنحة
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- بطاقة وثائق وإثبات الهوية الرسمية للطالب -->
    <div style="background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.03); overflow: hidden; padding: 35px; text-align: right; margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 18px; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 900; color: #0f172a; margin: 0;">
                        وثائق الهوية الرسمية والصورة الشخصية
                    </h2>
                    <span style="font-size: 0.82rem; color: #64748b;">مطابقة بطاقة الهوية الفلسطينية والمستندات المسجلة للطالب</span>
                </div>
            </div>

            <a href="{{ route('admin.students.edit', $student->id) }}" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 0.82rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-cloud-arrow-up"></i> تحديث / رفع وثائق جديدة
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">

            <!-- 1. وثيقة الهوية الفلسطينية -->
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 18px; padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                    <span style="font-size: 0.88rem; font-weight: 800; color: #1e293b;">
                        <i class="fa-solid fa-address-card" style="color: #0284c7;"></i> بطاقة الهوية / شهادة الميلاد:
                    </span>
                    @if($student->id_photo)
                        <span style="background: #dcfce7; color: #166534; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 20px;">
                            <i class="fa-solid fa-circle-check"></i> وثيقة مرفقة
                        </span>
                    @else
                        <span style="background: #fef2f2; color: #991b1b; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 20px;">
                            <i class="fa-solid fa-triangle-exclamation"></i> غير مرفقة بعد
                        </span>
                    @endif
                </div>

                @if($student->id_photo)
                    @php
                        $isIdPdf = \Illuminate\Support\Str::endsWith(strtolower($student->id_photo), '.pdf');
                        $idUrl = asset('storage/' . $student->id_photo);
                    @endphp
                    <div style="background: white; border: 1px solid #cbd5e1; border-radius: 14px; padding: 12px; text-align: center; margin-bottom: 14px;">
                        @if($isIdPdf)
                            <div style="padding: 25px; color: #ef4444;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 3rem; margin-bottom: 8px;"></i>
                                <span style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155;">مستند PDF: بطاقة الهوية</span>
                            </div>
                        @else
                            <img src="{{ $idUrl }}" alt="بطاقة الهوية" style="max-height: 220px; max-width: 100%; border-radius: 10px; object-fit: contain; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.06);" onclick="previewIdModal('{{ $idUrl }}', '{{ addslashes($student->name_ar) }}', '{{ $student->nid }}')">
                        @endif
                    </div>

                    <div style="display: flex; gap: 8px;">
                        @if(!$isIdPdf)
                            <button type="button" onclick="previewIdModal('{{ $idUrl }}', '{{ addslashes($student->name_ar) }}', '{{ $student->nid }}')" style="flex: 1; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; padding: 9px; border-radius: 10px; font-weight: 800; font-size: 0.82rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                <i class="fa-solid fa-expand"></i> معاينة وتكبير
                            </button>
                        @endif
                        <a href="{{ $idUrl }}" target="_blank" download style="flex: 1; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 9px; border-radius: 10px; font-weight: 800; font-size: 0.82rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                            <i class="fa-solid fa-download"></i> فتح / تنزيل
                        </a>
                    </div>
                @else
                    <div style="background: white; border: 1.5px dashed #cbd5e1; border-radius: 14px; padding: 30px 20px; text-align: center; color: #94a3b8;">
                        <i class="fa-solid fa-id-badge" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 8px;"></i>
                        <p style="margin: 0 0 12px 0; font-size: 0.85rem; font-weight: 600;">لم يقم الطالب برفع صورة بطاقة الهوية بعد.</p>
                        <a href="{{ route('admin.students.edit', $student->id) }}" style="background: #4f46e5; color: white; padding: 7px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-cloud-arrow-up"></i> رفع الوثيقة الآن
                        </a>
                    </div>
                @endif
            </div>

            <!-- 2. بطاقة الصورة الشخصية وبيانات التحقق -->
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 18px; padding: 20px;">
                <span style="font-size: 0.88rem; font-weight: 800; color: #1e293b; display: block; margin-bottom: 14px;">
                    <i class="fa-solid fa-circle-user" style="color: #4f46e5;"></i> الصورة الشخصية وبيانات الطالب المعتمدة:
                </span>

                <div style="display: flex; gap: 16px; align-items: center; background: white; border: 1px solid #cbd5e1; border-radius: 14px; padding: 14px; margin-bottom: 14px;">
                    <div style="width: 80px; height: 80px; border-radius: 16px; overflow: hidden; border: 2px solid #e2e8f0; flex-shrink: 0;">
                        <img src="{{ $student->photo_url }}" alt="الصورة الشخصية" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <strong style="font-size: 1rem; color: #0f172a; display: block; margin-bottom: 4px;">{{ $student->name_ar }}</strong>
                        <span style="font-size: 0.8rem; color: #64748b; display: block; margin-bottom: 4px;">
                            رقم الهوية: <code style="font-size: 0.85rem; font-family: monospace;">{{ $student->nid }}</code>
                        </span>
                        <span style="font-size: 0.8rem; color: #64748b; display: block;">
                            الفرع: {{ $student?->stage?->label_ar ?? 'توجيهي' }}
                        </span>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.82rem; color: #475569;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 6px;">
                        <span>المدرسة الثانوية:</span>
                        <strong style="color: #0f172a;">{{ $student->school_name ?? 'غير مسجلة' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 6px;">
                        <span>المحافظة:</span>
                        <strong style="color: #0f172a;">{{ $student->city ?? 'غير محددة' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>هاتف ولي الأمر:</span>
                        <strong style="color: #0f172a; direction: ltr;">{{ $student->guardian_phone ?? $student->whatsapp ?? 'غير متوفر' }}</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- قسم المواد الدراسية المقيد بها الطالب -->
    <div style="background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.03); overflow: hidden; padding: 35px; text-align: right;">

        <!-- هيدر قسم المواد -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                    <i class="fa-solid fa-book-bookmark"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.3rem; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
                        المواد الدراسية المقيد بها الطالب
                        <span style="background: #eef2ff; color: #4f46e5; font-size: 0.85rem; font-weight: 800; padding: 3px 10px; border-radius: 20px;" id="enrolledCountBadge">
                            {{ $student->enrolledSubjects->count() }} مواد
                        </span>
                    </h2>
                    <span style="font-size: 0.82rem; color: #64748b;">قائمة المواد المتاحة للطالب في حسابه الدراسي مع إمكانية التعديل والإلغاء</span>
                </div>
            </div>

            <!-- زر إضافة / تعديل المواد -->
            <button type="button" onclick="openSubjectModal()" style="background: #10b981; color: white; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 0.88rem; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                <i class="fa-solid fa-plus-circle"></i> تخصيص واختيار المواد
            </button>
        </div>

        <!-- قائمة كروت المواد المشترك بها -->
        @if($student->enrolledSubjects->count() > 0)
            <div id="enrolledSubjectsGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                @foreach($student->enrolledSubjects as $subject)
                    <div id="subject_card_{{ $subject->id }}" style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 18px; display: flex; flex-direction: column; justify-content: space-between; gap: 14px; transition: 0.2s;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="font-size: 2rem; width: 48px; height: 48px; border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                    {{ $subject->icon ?? '📖' }}
                                </div>
                                <div>
                                    <h3 style="font-size: 1.05rem; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">{{ $subject->name_ar }}</h3>
                                    <span style="font-size: 0.78rem; color: {{ $subject->hasAssignedTeacher() ? '#4f46e5' : '#b45309' }}; display: block; font-weight: 600;">
                                        <i class="fa-solid fa-chalkboard-user"></i>
                                        {{ $subject->teacher_display_name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- تفاصيل الاشتراك وزر الإلغاء -->
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #edf2f7; padding-top: 12px; font-size: 0.78rem;">
                            <span style="background: #ecfdf5; color: #059669; padding: 4px 10px; border-radius: 6px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fa-solid fa-check"></i> مفعّل ونشط
                            </span>

                            <button type="button" onclick="confirmRemoveSubject({{ $student->id }}, {{ $subject->id }}, '{{ addslashes($subject->name_ar) }}')" style="background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; padding: 5px 10px; border-radius: 8px; font-weight: 700; font-size: 0.75rem; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                                <i class="fa-solid fa-trash-can"></i> إلغاء المادة
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- الحالة الفارغة: الطالب ليس لديه أي مواد مسجلة -->
            <div id="emptySubjectsState" style="text-align: center; padding: 45px 20px; background: #f8fafc; border-radius: 18px; border: 2px dashed #cbd5e1;">
                <div style="font-size: 3rem; margin-bottom: 12px;">📚</div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #334155; margin-bottom: 6px;">لا توجد أي مواد دراسية مقيدة لهذا الطالب حتى الآن</h3>
                <p style="font-size: 0.88rem; color: #64748b; max-width: 500px; margin: 0 auto 20px auto;">
                    يمكنك اختيار وتحديد المواد المناسبة للطالب لتفعيلها في لوحة دراسته على الفور.
                </p>
                <button type="button" onclick="openSubjectModal()" style="background: #4f46e5; color: white; border: none; padding: 11px 24px; border-radius: 12px; font-weight: 800; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.25);">
                    <i class="fa-solid fa-plus-circle"></i> اختيار وتفعيل مواد الطالب الآن
                </button>
            </div>
        @endif

    </div>

</div>

<!-- النافذة المنبثقة التفاعلية لإدارة وتعديل مواد الطالب (Modal) -->
<div id="subjectModalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;" dir="rtl">
    <div style="background: #ffffff; width: 100%; max-width: 680px; border-radius: 22px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; max-height: 90vh; display: flex; flex-direction: column; animation: modalIn 0.25s ease-out;">

        <!-- هيدر المودال -->
        <div style="padding: 22px 28px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 1.6rem;">📚</span>
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0;">تخصيص مواد الطالب</h3>
                    <span style="font-size: 0.8rem; color: #94a3b8;">الطالب: {{ $student->name_ar }} • {{ $student?->stage?->label_ar }}</span>
                </div>
            </div>
            <button type="button" onclick="closeSubjectModal()" style="background: rgba(255,255,255,0.1); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 1rem; display: flex; align-items: center; justify-content: center;">✕</button>
        </div>

        <!-- شريط الإجراءات السريعة في المودال -->
        <div style="padding: 14px 28px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <span style="font-size: 0.85rem; font-weight: 700; color: #475569;">
                حدد المواد التي تريد تمكين الطالب من دراستها:
            </span>
            <div style="display: flex; gap: 8px;">
                <button type="button" onclick="toggleModalCheckboxes(true)" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">تحديد الكل</button>
                <button type="button" onclick="toggleModalCheckboxes(false)" style="background: #ffffff; color: #64748b; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">إلغاء التحديد</button>
            </div>
        </div>

        <!-- قائمة المواد بخانات الاختيار -->
        <form id="syncSubjectsForm" style="padding: 24px 28px; overflow-y: auto; flex: 1;">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px;">
                @php
                    $enrolledIds = $student->enrolledSubjects->pluck('id')->toArray();
                    $stageSubjects = $student->stage?->subjects ?? collect();
                @endphp

                @if($stageSubjects->count() > 0)
                    @foreach($stageSubjects as $sub)
                        @php $isChecked = in_array($sub->id, $enrolledIds); @endphp
                        <label class="modal-subject-card" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 2px solid {{ $isChecked ? '#4f46e5' : '#e2e8f0' }}; background: {{ $isChecked ? '#eef2ff' : '#ffffff' }}; border-radius: 14px; cursor: pointer; transition: 0.2s; user-select: none;">
                            <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}" class="modal-subject-cb" {{ $isChecked ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #4f46e5; cursor: pointer;">
                            <span style="font-size: 1.5rem;">{{ $sub->icon ?? '📖' }}</span>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 800; font-size: 0.9rem; color: #1e293b;">{{ $sub->name_ar }}</div>
                                <div style="font-size: 0.75rem; color: {{ $sub->hasAssignedTeacher() ? '#4f46e5' : '#b45309' }}; font-weight: 600;">
                                    <i class="fa-solid fa-chalkboard-user" style="font-size: 0.7rem;"></i>
                                    {{ $sub->teacher_display_name }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                @else
                    <div style="grid-column: 1/-1; text-align: center; padding: 25px; color: #94a3b8;">
                        لا توجد مواد مسجلة لهذا الفرع حالياً.
                    </div>
                @endif
            </div>
        </form>

        <!-- فوتر المودال -->
        <div style="padding: 16px 28px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px;">
            <button type="button" onclick="closeSubjectModal()" style="background: #ffffff; color: #64748b; border: 1.5px solid #cbd5e1; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer;">
                إلغاء
            </button>
            <button type="button" onclick="submitSyncSubjects()" id="btnSaveSubjects" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 800; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                <i class="fa-solid fa-floppy-disk"></i> حفظ وتفعيل المواد
            </button>
        </div>

    </div>
</div>

<style>
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    const studentId = {{ $student->id }};

    function openSubjectModal() {
        const modal = document.getElementById('subjectModalOverlay');
        modal.style.display = 'flex';
    }

    function closeSubjectModal() {
        const modal = document.getElementById('subjectModalOverlay');
        modal.style.display = 'none';
    }

    // إغلاق المودال عند النقر في الخارج
    document.getElementById('subjectModalOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSubjectModal();
        }
    });

    // تبديل وتلوين كروت المواد في المودال
    document.querySelectorAll('.modal-subject-card').forEach(card => {
        const cb = card.querySelector('.modal-subject-cb');
        if (cb) {
            cb.addEventListener('change', () => {
                if (cb.checked) {
                    card.style.borderColor = '#4f46e5';
                    card.style.background = '#eef2ff';
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
                card.style.borderColor = selectAll ? '#4f46e5' : '#e2e8f0';
                card.style.background = selectAll ? '#eef2ff' : '#ffffff';
            }
        });
    }

    // حفظ ومزامنة المواد عبر AJAX
    async function submitSyncSubjects() {
        const btn = document.getElementById('btnSaveSubjects');
        const form = document.getElementById('syncSubjectsForm');
        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...';

        try {
            const response = await axios.post("{{ route('admin.students.syncSubjects', $student->id) }}", formData, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                timeout: 20000
            });
            Swal.fire({
                icon: 'success',
                title: 'تم الحفظ بنجاح! 🎉',
                text: response.data.message || 'تم تحديث قائمة مواد الطالب وتفعيلها.',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'حسناً'
            }).then(() => {
                location.reload();
            });
        } catch (error) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> حفظ وتفعيل المواد';
            const errorMsg = error.response?.data?.message || (error.code === 'ECONNABORTED' ? 'استغرق الخادم وقتاً أطول للاستجابة، يرجى إعادة المحاولة.' : 'تعذر حفظ مواد الطالب، يرجى المحاولة مرة أخرى.');
            Swal.fire({
                icon: 'error',
                title: 'خطأ أثناء الحفظ',
                text: errorMsg,
                confirmButtonText: 'حسناً'
            });
        }
    }

    // تأكيد وإلغاء مادة فردية فورياً
    function confirmRemoveSubject(sId, subId, subName) {
        Swal.fire({
            title: 'إلغاء اشتراك المادة',
            text: `هل أنت متأكد من رغبتك في إلغاء اشتراك الطالب في مادة (${subName})؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، إلغاء الاشتراك',
            cancelButtonText: 'تراجع'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await axios.post(`{{ url('admin/students') }}/${sId}/toggle-subject/${subId}`, {
                        _token: '{{ csrf_token() }}'
                    });

                    Swal.fire({
                        icon: 'success',
                        title: response.data.title || 'تم إلغاء المادة',
                        text: response.data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'فشلت العملية',
                        text: 'حدث خطأ أثناء إلغاء المادة.',
                        confirmButtonText: 'حسناً'
                    });
                }
            }
        });
    }

    // --- وظائف تخصيص الخصم والمنحة ---
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
            unitLabel.innerText = '% (نسبة مئوية)';
            document.getElementById('showDiscountValue').placeholder = 'مثال: 25 أو 50 أو 100';
            document.getElementById('showDiscountValue').max = '100';
        } else {
            btnFixed.style.background = '#7c3aed';
            btnFixed.style.color = '#ffffff';
            btnFixed.style.borderColor = '#7c3aed';
            btnPercent.style.background = '#ffffff';
            btnPercent.style.color = '#64748b';
            btnPercent.style.borderColor = '#cbd5e1';
            unitLabel.innerText = '₪ (شيكل فلسطيني)';
            document.getElementById('showDiscountValue').placeholder = 'مثال: 50 أو 100';
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
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...';

        try {
            const res = await axios.post("{{ route('admin.students.discount', $student->id) }}", {
                discount_type: val > 0 ? type : 'none',
                discount_value: val,
                discount_notes: notes
            });

            Swal.fire({
                icon: 'success',
                title: res.data.title || 'تم الحفظ بنجاح',
                text: res.data.message || '',
                timer: 1600,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } catch (err) {
            btn.disabled = false;
            btn.innerHTML = originalText;
            const msg = (err.response && err.response.data && err.response.data.title) ? err.response.data.title : 'فشل حفظ الخصم';
            Swal.fire('خطأ', msg, 'error');
        }
    }
</script>

<!-- النافذة المنبثقة للخصم في صفحة بروفايل الطالب -->
<div id="studentDiscountModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;" dir="rtl">
    <div style="background: #ffffff; width: 100%; max-width: 520px; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: modalIn 0.25s ease-out;">
        
        <div style="padding: 20px 24px; background: linear-gradient(135deg, #4c1d95 0%, #6d28d9 100%); color: white; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 1.5rem;">🏷️</span>
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0;">تحديد خصم أو منحة للطالب</h3>
                    <span style="font-size: 0.82rem; color: #e9d5ff;">{{ $student->name_ar }}</span>
                </div>
            </div>
            <button type="button" onclick="closeDiscountModalDirect()" style="background: rgba(255,255,255,0.15); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;">✕</button>
        </div>

        <form onsubmit="handleShowDiscountSubmit(event)" style="padding: 24px;">
            <input type="hidden" id="showDiscountType" value="{{ (float)$student->custom_discount_fixed > 0 ? 'fixed' : 'percent' }}">

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 8px;">نوع الخصم المعتمد:</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <button type="button" id="showTypeBtnPercent" onclick="setShowDiscountType('percent')" style="padding: 10px; border-radius: 12px; border: 2px solid {{ (float)$student->custom_discount_fixed > 0 ? '#cbd5e1' : '#7c3aed' }}; background: {{ (float)$student->custom_discount_fixed > 0 ? 'white' : '#7c3aed' }}; color: {{ (float)$student->custom_discount_fixed > 0 ? '#64748b' : 'white' }}; font-weight: 800; font-size: 0.85rem; cursor: pointer;">
                        نسبة مئوية (%)
                    </button>
                    <button type="button" id="showTypeBtnFixed" onclick="setShowDiscountType('fixed')" style="padding: 10px; border-radius: 12px; border: 2px solid {{ (float)$student->custom_discount_fixed > 0 ? '#7c3aed' : '#cbd5e1' }}; background: {{ (float)$student->custom_discount_fixed > 0 ? '#7c3aed' : 'white' }}; color: {{ (float)$student->custom_discount_fixed > 0 ? 'white' : '#64748b' }}; font-weight: 800; font-size: 0.85rem; cursor: pointer;">
                        مبلغ نقدي ثابت (₪)
                    </button>
                </div>
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #64748b; margin-bottom: 6px;">خيارات سريعة:</label>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="button" onclick="setShowQuickDiscount(15, 'خصم تشجيعي')" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">15% تشجيعي</button>
                    <button type="button" onclick="setShowQuickDiscount(25, 'منحة تفوق دراسي')" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">25% تفوق</button>
                    <button type="button" onclick="setShowQuickDiscount(50, 'نصف منحة دراسية')" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">50% نصف منحة</button>
                    <button type="button" onclick="setShowQuickDiscount(100, 'إعفاء كامل - منحة شاملة 100%')" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">✨ إعفاء كامل 100%</button>
                    <button type="button" onclick="setShowQuickDiscount(0, '')" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">❌ إلغاء الخصم</button>
                </div>
            </div>

            <div style="margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label style="font-size: 0.82rem; font-weight: 700; color: #334155;">قيمة الخصم:</label>
                    <span id="showDiscountUnitLabel" style="font-size: 0.75rem; color: #7c3aed; font-weight: 700;">
                        {{ (float)$student->custom_discount_fixed > 0 ? '₪ (شيكل فلسطيني)' : '% (نسبة مئوية)' }}
                    </span>
                </div>
                <input type="number" id="showDiscountValue" min="0" step="any" value="{{ (float)$student->custom_discount_fixed > 0 ? (float)$student->custom_discount_fixed : ((float)$student->custom_discount_percent > 0 ? (float)$student->custom_discount_percent : '') }}" placeholder="مثال: 25 أو 50" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1.5px solid #cbd5e1; font-size: 1.05rem; font-weight: 800; font-family: monospace; outline: none; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 22px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">سبب الخصم أو ملاحظات المنحة:</label>
                <input type="text" id="showDiscountNotes" value="{{ $student->discount_notes }}" placeholder="مثال: منحة تفوق توجيهي / إعفاء خاص" style="width: 100%; padding: 10px 14px; border-radius: 12px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; outline: none; box-sizing: border-box;">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" id="btnSaveShowDiscount" style="flex: 1; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: white; border: none; padding: 13px; border-radius: 12px; font-weight: 800; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <span>حفظ وتحديث الخصم</span>
                    <i class="fa-solid fa-check"></i>
                </button>
                <button type="button" onclick="closeDiscountModalDirect()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 13px 20px; border-radius: 12px; font-weight: 700; font-size: 0.9rem; cursor: pointer;">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<!-- نافذة تكبير ومعاينة بطاقة الهوية الفلسطينية للتحقق الرسمي -->
<div id="idPhotoModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(6px); z-index: 99999; justify-content: center; align-items: center; padding: 20px;" onclick="closeIdModal()" dir="rtl">
    <div style="background: #ffffff; width: 100%; max-width: 760px; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); overflow: hidden; animation: modalIn 0.25s ease-out;" onclick="event.stopPropagation()">
        <div style="padding: 18px 24px; background: #0f172a; color: white; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(56, 189, 248, 0.2); color: #38bdf8; display: grid; place-items: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0;" id="modalStudentName">معاينة بطاقة الهوية الفلسطينية</h3>
                    <span style="font-size: 0.78rem; color: #94a3b8;" id="modalStudentNid">رقم الهوية: {{ $student->nid }}</span>
                </div>
            </div>
            <button type="button" onclick="closeIdModal()" style="background: rgba(255,255,255,0.15); border: none; color: white; width: 34px; height: 34px; border-radius: 50%; cursor: pointer; font-size: 1rem; display: grid; place-items: center;">✕</button>
        </div>
        <div style="padding: 24px; background: #f8fafc; text-align: center; max-height: 70vh; overflow-y: auto;">
            <img id="modalIdImg" src="" alt="بطاقة الهوية" style="max-height: 520px; max-width: 100%; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.15); object-fit: contain;">
        </div>
        <div style="padding: 16px 24px; background: white; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <a id="modalDownloadBtn" href="" target="_blank" download style="background: #0284c7; color: white; padding: 10px 22px; border-radius: 10px; font-size: 0.85rem; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-download"></i> تنزيل الوثيقة الرسمية
            </a>
            <button type="button" onclick="closeIdModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                إغلاق النافذة
            </button>
        </div>
    </div>
</div>

<script>
function previewIdModal(imgUrl, studentName, studentNid) {
    document.getElementById('modalIdImg').src = imgUrl;
    document.getElementById('modalDownloadBtn').href = imgUrl;
    document.getElementById('modalStudentName').textContent = 'بطاقة الهوية: ' + studentName;
    document.getElementById('modalStudentNid').textContent = 'رقم الهوية الفلسطينية: ' + studentNid;
    const modal = document.getElementById('idPhotoModalOverlay');
    modal.style.display = 'flex';
}

function closeIdModal() {
    document.getElementById('idPhotoModalOverlay').style.display = 'none';
}
</script>

@endsection
