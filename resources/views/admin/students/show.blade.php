@extends('layouts.app')

@section('title', 'الملف الشخصي للطالب')

@section('content')
<div style="max-width: 950px; margin: 0 auto;" dir="rtl">

    <!-- زر العودة -->
    <div style="margin-bottom: 20px; text-align: right;">
        <a href="{{ url()->previous() }}" style="background: #eef2ff; color: #4f46e5; padding: 8px 16px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s;" onmouseover="this.style.background='#4f46e5';this.style.color='#fff'" onmouseout="this.style.background='#eef2ff';this.style.color='#4f46e5'">
            <i class="fa-solid fa-arrow-right"></i> عودة للقائمة
        </a>
    </div>

    <!-- البطاقة الرئيسية -->
    <div style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.02); overflow: hidden; padding: 40px; text-align: right;">

        <!-- الهيدر -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 30px; margin-bottom: 30px; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 20px;">

                <!-- أيقونة الطالب -->
                <div style="width: 85px; height: 85px; border-radius: 22px; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>

                <div>
                    <span style="background: #eef2ff; color: #4f46e5; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: inline-block; margin-bottom: 8px;">طالب مسجل</span>
                    <h1 style="font-size: 1.6rem; font-weight: 800; color: #1e293b; margin: 0;">{{ $student->name_ar ?? $student->name ?? 'غير متوفر' }}</h1>
                </div>
            </div>
            <div style="background: #f8fafc; padding: 12px 20px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center;">
                <span style="display: block; font-size: 0.75rem; color: #94a3b8; font-weight: 700;">الرقم التعريفي</span>
                <span style="font-size: 1.1rem; font-weight: 800; color: #4f46e5;">#{{ $student->id }}</span>
            </div>
        </div>

        <!-- تفاصيل المعلومات الشاملة -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px;">

            <!-- البريد الإلكتروني -->
            <div style="background: #f8fafc; padding: 20px; border-radius: 14px; border: 1px solid #e2e8f0; text-align: right;">
                <span style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 6px;">البريد الإلكتروني</span>
                <span style="font-size: 0.95rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 10px; direction: ltr; justify-content: flex-end;">
                    {{ $student->email ?? 'غير متوفر' }} <i class="fa-solid fa-envelope" style="color: #4f46e5;"></i>
                </span>
            </div>

            <!-- رقم الجوال -->
            <div style="background: #f8fafc; padding: 20px; border-radius: 14px; border: 1px solid #e2e8f0; text-align: right;">
                <span style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 6px;">رقم الجوال</span>
                <span style="font-size: 0.95rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 10px; direction: ltr; justify-content: flex-end;">
                    {{ $student->phone ?? 'غير متوفر' }} <i class="fa-solid fa-phone" style="color: #4f46e5;"></i>
                </span>
            </div>

            <!-- المرحلة الدراسية -->
            <div style="background: #f8fafc; padding: 20px; border-radius: 14px; border: 1px solid #e2e8f0; text-align: right;">
                <span style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 6px;">المرحلة الدراسية</span>
                <span style="font-size: 0.95rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-graduation-cap" style="color: #4f46e5;"></i> {{ $student?->stage?->label_ar ?? $student?->stage?->name ?? 'غير محددة' }}
                </span>
            </div>

            <!-- رقم الهوية (معتمد على العمود nid الصحيح من الميجريشن) -->
            <div style="background: #f8fafc; padding: 20px; border-radius: 14px; border: 1px solid #e2e8f0; text-align: right;">
                <span style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 6px;">رقم الهوية (NID)</span>
                <span style="font-size: 0.95rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-id-card" style="color: #4f46e5;"></i> {{ $student->nid ?? 'غير متوفر' }}
                </span>
            </div>

            <!-- حالة الحساب (حسب القيم المعرفة في الميجريشن: pending, draft, published أو active) -->
            <div style="background: #f8fafc; padding: 20px; border-radius: 14px; border: 1px solid #e2e8f0; text-align: right;">
                <span style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 6px;">حالة الحساب</span>
                <span style="font-size: 0.95rem; font-weight: 600; color: #059669; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-check" style="color: #059669;"></i>
                    @if($student->status == 'published')
                        منشور / نشط
                    @elseif($student->status == 'draft')
                        مسودة
                    @else
                        قيد الانتظار (Pending)
                    @endif
                </span>
            </div>

            <!-- تاريخ الانضمام للمنصة -->
            <div style="background: #f8fafc; padding: 20px; border-radius: 14px; border: 1px solid #e2e8f0; text-align: right;">
                <span style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 6px;">تاريخ الانضمام للمنصة</span>
                <span style="font-size: 0.95rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-calendar-days" style="color: #4f46e5;"></i> {{ $student->created_at ? $student->created_at->format('Y-m-d') : 'غير متوفر' }}
                </span>
            </div>

        </div>

    </div>
</div>
@endsection
