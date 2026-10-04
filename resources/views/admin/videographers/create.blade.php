@extends('layouts.app')

@section('title', __('إضافة حساب مصور جديد') . ' | ' . config('app.name', 'Step by Step'))

@section('content')
<div class="admin-create-videographer-wrapper" style="max-width: 900px; margin: 0 auto; padding: 10px 4px 60px 4px;">

    {{-- 1. رأس الصفحة والمسار الأكاديمي الكلاسيكي --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 24px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 1.25rem; border: 1px solid #bfdbfe; flex-shrink: 0;">
                <i class="fa-solid fa-video"></i>
            </div>
            <div>
                <h1 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; font-family: 'Alexandria', 'Cairo', sans-serif;">
                    {{ __('إضافة حساب مصور جديد للمنصة') }}
                </h1>
                <div style="font-size: 0.82rem; color: #64748b; display: flex; align-items: center; gap: 6px;">
                    <a href="{{ route('admin.dashboard') }}" style="color: #64748b; text-decoration: none;">{{ __('الرئيسية') }}</a>
                    <span>/</span>
                    <a href="{{ route('admin.videographers.index') }}" style="color: #64748b; text-decoration: none;">{{ __('كادر المصورين') }}</a>
                    <span>/</span>
                    <span style="color: #1e293b; font-weight: 600;">{{ __('إنشاء حساب جديد') }}</span>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.videographers.index') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 0.84rem; text-decoration: none;">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('سجل المصورين') }}</span>
        </a>
    </div>

    {{-- تنبيهات الأخطاء إن وجدت --}}
    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem;">
                <i class="fa-solid fa-circle-exclamation" style="color: #dc2626;"></i>
                <span>{{ __('يرجى تصحيح الأخطاء التالية:') }}</span>
            </div>
            <ul style="margin: 0; padding-right: 20px; font-size: 0.84rem;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 2. بطاقة النموذج الكلاسيكي الهادئ --}}
    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); overflow: hidden;">
        
        <div style="padding: 14px 22px; background: #f8fafc; border-bottom: 1.5px solid #e2e8f0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-user-plus" style="color: #1d4ed8;"></i>
            <strong style="font-size: 0.92rem; color: #0f172a;">{{ __('بيانات حساب المصور وبيانات الدخول') }}</strong>
        </div>

        <form action="{{ route('admin.videographers.store') }}" method="POST" style="padding: 24px;">
            @csrf

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; margin-bottom: 18px;">
                
                {{-- اسم المصور --}}
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        {{ __('اسم المصور أو وحدة التصوير') }} <span style="color: #dc2626;">*</span>
                    </label>
                    <div style="position: relative;">
                        <i class="fa-solid fa-user" style="position: absolute; right: 12px; top: 12px; color: #94a3b8; font-size: 0.88rem;"></i>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: المصور أحمد شمالي" style="width: 100%; height: 42px; padding: 0 36px 0 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; color: #0f172a; font-weight: 600;">
                    </div>
                    <span style="font-size: 0.74rem; color: #64748b; margin-top: 3px; display: block;">{{ __('يظهر كاسم للمصور تحت الفيديوهات المصورة التي يرفعها') }}</span>
                </div>

                {{-- البريد الإلكتروني --}}
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        {{ __('البريد الإلكتروني (لتسجيل الدخول)') }} <span style="color: #dc2626;">*</span>
                    </label>
                    <div style="position: relative;">
                        <i class="fa-solid fa-envelope" style="position: absolute; right: 12px; top: 12px; color: #94a3b8; font-size: 0.88rem;"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="cameraman@stepvoro.com" style="width: 100%; height: 42px; padding: 0 36px 0 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; color: #0f172a; font-weight: 600; direction: ltr; text-align: right;">
                    </div>
                    <span style="font-size: 0.74rem; color: #64748b; margin-top: 3px; display: block;">{{ __('البريد الذي سيستخدمه المصور لتسجيل الدخول للمنصة') }}</span>
                </div>

                {{-- كلمة المرور --}}
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        {{ __('كلمة المرور الأولية') }} <span style="color: #dc2626;">*</span>
                    </label>
                    <div style="position: relative;">
                        <i class="fa-solid fa-key" style="position: absolute; right: 12px; top: 12px; color: #94a3b8; font-size: 0.88rem;"></i>
                        <input type="text" name="password" value="{{ old('password', 'Pass@123456') }}" required style="width: 100%; height: 42px; padding: 0 36px 0 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; color: #0f172a; font-weight: 700; direction: ltr; text-align: right;">
                    </div>
                    <span style="font-size: 0.74rem; color: #64748b; margin-top: 3px; display: block;">{{ __('يمكنك إعطاؤها للمصور للدخول، ويمكنه تعديلها لاحقاً') }}</span>
                </div>

                {{-- رقم الهاتف / واتساب --}}
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        {{ __('رقم الهاتف / واتساب (اختياري)') }}
                    </label>
                    <div style="position: relative;">
                        <i class="fa-solid fa-phone" style="position: absolute; right: 12px; top: 12px; color: #94a3b8; font-size: 0.88rem;"></i>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="059xxxxxxx" style="width: 100%; height: 42px; padding: 0 36px 0 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; color: #0f172a; direction: ltr; text-align: right;">
                    </div>
                </div>

            </div>

            {{-- نبذة أو ملاحظات --}}
            <div style="margin-bottom: 22px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    {{ __('ملاحظات أو نبذة عن المصور (اختياري)') }}
                </label>
                <textarea name="bio" rows="3" placeholder="ملاحظات حول التعاقد، المعدات، أو الفروع المكلف بتغطيتها..." style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem; color: #0f172a; font-family: inherit;">{{ old('bio') }}</textarea>
            </div>

            {{-- إرشادات صلاحيات المصور --}}
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 12px;">
                <i class="fa-solid fa-circle-info" style="color: #1d4ed8; font-size: 1.15rem; margin-top: 2px;"></i>
                <div style="font-size: 0.82rem; color: #1e3a8a; line-height: 1.6;">
                    <strong style="display: block; margin-bottom: 2px;">{{ __('ماذا يستطيع هذا الحساب أن يفعل فور إنشائه؟') }}</strong>
                    <span>{{ __('فور اعتماد الحساب، يستطيع المصور الدخول عبر شاشة الدخول الرئيسية (تبويب مصور)، ليفتح له استوديو التصوير لرفع الفيديوهات وملفات الـ PDF مع ميزة تحديد الفروع المتعددة (تشيك بوكس) لتوزيع المحاضرة فوراً لكافة المدرسين والطلاب.') }}</span>
                </div>
            </div>

            {{-- أزرار الإجراء --}}
            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 18px;">
                <a href="{{ route('admin.videographers.index') }}" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 9px 18px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; text-decoration: none;">
                    {{ __('إلغاء') }}
                </a>
                <button type="submit" style="display: inline-flex; align-items: center; gap: 8px; background: #1d4ed8; color: #ffffff; border: none; padding: 9px 24px; border-radius: 6px; font-weight: 700; font-size: 0.88rem; cursor: pointer; transition: background 0.15s; box-shadow: 0 1px 2px rgba(29, 78, 216, 0.2);">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('اعتماد وإنشاء حساب المصور فوراً') }}</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
