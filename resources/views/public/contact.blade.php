@extends('layouts.app')

@section('title', 'مركز الدعم الفني والشكاوى الأكاديمية | ' . \App\Models\Setting::get('site_name', 'منارة التوجيهي'))

@section('content')
<div class="contact-page-wrapper">
    
    <!-- الترويسة الرئيسية -->
    <div class="contact-hero-card">
        <div class="hero-text-side">
            <span class="palestine-badge">{{ __('صوتك مسموع ومحل اهتمامنا دائماً') }}</span>
            <h1 class="hero-title">{{ __('مركز خدمة المستفيدين') }}<br><span class="gradient-text">{{ __('والدعم والشكاوى المباشر') }}</span></h1>
            <p class="hero-desc">{{ __('سواء كنت طالباً، ولي أمر، أو معلماً؛ إدارة منصة') }}<strong>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</strong>{{ __('والمشرف العام حريصون على متابعة استفساراتك وشكاواك وحلها فورياً لضمان تجربة تعليمية متميزة.') }}</p>

            <div class="quick-contacts-grid">
                <div class="contact-card-box">
                    <div class="contact-icon email-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
                    <div>
                        <h4>{{ __('البريد الرسمي للشكاوى') }}</h4>
                        <p dir="ltr"><a href="mailto:{{ \App\Models\Setting::get('contact_email', 'support@tawjihi-gaza.ps') }}">{{ \App\Models\Setting::get('contact_email', 'support@tawjihi-gaza.ps') }}</a></p>
                    </div>
                </div>

                @php
                    $rawWa = \App\Models\Setting::get('contact_whatsapp', '00970597694385');
                    $cleanWa = preg_replace('/[^0-9]/', '', $rawWa);
                    if (str_starts_with($cleanWa, '00')) $cleanWa = substr($cleanWa, 2);
                    elseif (str_starts_with($cleanWa, '0')) $cleanWa = '970' . substr($cleanWa, 1);
                    $waLink = "https://wa.me/" . ($cleanWa ?: '970597694385') . "?text=" . urlencode("مرحباً إدارة منارة التوجيهي، أحتاج إلى مساعدة / لدي استفسار وشكوى.");
                @endphp
                <div class="contact-card-box wa-card-box">
                    <div class="contact-icon wa-icon"><i class="fa-brands fa-whatsapp"></i></div>
                    <div>
                        <h4>{{ __('واتساب المشرف العام المباشر') }}</h4>
                        <p dir="ltr"><a href="{{ $waLink }}" target="_blank">{{ $rawWa }}</a></p>
                    </div>
                </div>

                <div class="contact-card-box">
                    <div class="contact-icon loc-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <h4>{{ __('الموقع الجغرافي') }}</h4>
                        <p>فلسطين - قطاع غزة والضفة الغربية</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج تقديم الشكوى / الاستفسار -->
        <div class="form-container-side">
            <div class="form-glass-panel">
                <div class="form-head">
                    <div class="head-icon"><i class="fa-solid fa-paper-plane"></i></div>
                    <div>
                        <h3>{{ __('إرسال تذكرة شكوى أو استفسار') }}</h3>
                        <small>{{ __('يتم الرد خلال مدة لا تتجاوز ساعتين خلال أوقات الدوام الرسمي') }}</small>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert-success-box">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-error-box">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form id="contactComplaintsForm" action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    
                    <div class="input-row-grid">
                        <div class="input-field-wrap">
                            <label><i class="fa-regular fa-user"></i>{{ __('الاسم الرباعي') }}<span class="req">*</span></label>
                            <input type="text" name="name" class="custom-input" placeholder="{{ __('مثال: أحمد محمد خليل') }}" required value="{{ old('name', auth('student')->user()?->name_ar ?? auth()->user()?->name ?? '') }}">
                        </div>

                        <div class="input-field-wrap">
                            <label><i class="fa-regular fa-envelope"></i>{{ __('البريد الإلكتروني') }}<span class="req">*</span></label>
                            <input type="email" name="email" class="custom-input" placeholder="username@tawjihi-gaza.ps" required value="{{ old('email', auth('student')->user()?->email ?? auth()->user()?->email ?? '') }}">
                        </div>
                    </div>

                    <div class="input-row-grid">
                        <div class="input-field-wrap">
                            <label><i class="fa-solid fa-phone"></i> رقم الهاتف / واتساب للتواصل</label>
                            <input type="text" name="phone" class="custom-input" placeholder="059xxxxxxx" value="{{ old('phone', auth('student')->user()?->phone ?? '') }}">
                        </div>

                        <div class="input-field-wrap">
                            <label><i class="fa-solid fa-list-check"></i>{{ __('نوع وتصنيف الرسالة') }}<span class="req">*</span></label>
                            <select name="type" class="custom-select" required>
                                <option value="شكوى خاصة بالحساب وتفعيله" selected>⚠️ شكوى خاصة بالحساب والاعتماد والتفعيل</option>
                                <option value="مشكلة في الدفع وإشعار السداد">💳 مشكلة في الدفع ورسوم الاشتراك</option>
                                <option value="استفسار أكاديمي عن المساقات">📚 استفسار أكاديمي عن المواد والدروس</option>
                                <option value="مشكلة تقنية في تشغيل الاختبارات">⚙️ مشكلة تقنية في الاختبارات أو الموقع</option>
                                <option value="اقتراح تطويري للإدارة">💡 اقتراح أو فكرة لتطوير المنصة</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-field-wrap">
                        <label><i class="fa-regular fa-message"></i>{{ __('تفاصيل الشكوى أو الرسالة') }}<span class="req">*</span></label>
                        <textarea name="message" rows="5" class="custom-textarea" placeholder="{{ __('يرجى كتابة تفاصيل ما تواجهه بدقة لمساعدتك بأسرع شكل ممكن...') }}" required>{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="submit-complaint-btn" id="btnSubmitComplaint">
                        <span>{{ __('إرسال التذكرة الآن') }}</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .contact-page-wrapper {
        max-width: 1280px;
        margin: 40px auto 60px;
        padding: 0 16px;
    }

    .contact-hero-card {
        display: grid;
        grid-template-columns: 1fr 1.25fr;
        gap: 40px;
        align-items: start;
    }

    @media (max-width: 992px) {
        .contact-hero-card {
            grid-template-columns: 1fr;
        }
    }

    .palestine-badge {
        display: inline-block;
        background: #ecfdf5;
        color: #059669;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 6px 14px;
        border-radius: 9999px;
        border: 1px solid #a7f3d0;
        margin-bottom: 16px;
    }

    .hero-title {
        font-size: 2.3rem;
        font-weight: 900;
        color: var(--ed-text-main, #0f172a);
        line-height: 1.35;
        margin-bottom: 18px;
    }

    .gradient-text {
        background: linear-gradient(135deg, #1d4ed8 0%, #0284c7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-desc {
        color: var(--ed-text-muted, #64748b);
        font-size: 1.05rem;
        line-height: 1.8;
        margin-bottom: 30px;
    }

    .quick-contacts-grid {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .contact-card-box {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        border-radius: 16px;
        background: var(--ed-surface, #ffffff);
        border: 1px solid var(--ed-border, #e2e8f0);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        transition: 0.2s ease;
    }

    .contact-card-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }

    .wa-card-box {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .contact-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .email-icon { background: #eff6ff; color: #1d4ed8; }
    .wa-icon { background: #22c55e; color: #ffffff; }
    .loc-icon { background: #fef2f2; color: #ef4444; }

    .contact-card-box h4 {
        margin: 0 0 3px;
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--ed-text-main, #0f172a);
    }

    .contact-card-box p {
        margin: 0;
        font-size: 0.88rem;
        color: var(--ed-text-muted, #64748b);
    }

    .contact-card-box a {
        color: inherit;
        text-decoration: none;
        font-weight: 600;
    }

    .form-glass-panel {
        background: var(--ed-surface, #ffffff);
        border-radius: 24px;
        padding: 35px;
        border: 1px solid var(--ed-border, #e2e8f0);
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.06);
    }

    .form-head {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 24px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--ed-border, #f1f5f9);
    }

    .head-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #eff6ff;
        color: #1d4ed8;
        display: grid;
        place-items: center;
        font-size: 1.25rem;
    }

    .form-head h3 {
        margin: 0 0 3px;
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--ed-text-main, #0f172a);
    }

    .form-head small {
        color: var(--ed-text-muted, #64748b);
        font-size: 0.8rem;
    }

    .input-row-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    @media (max-width: 600px) {
        .input-row-grid {
            grid-template-columns: 1fr;
        }
    }

    .input-field-wrap {
        margin-bottom: 16px;
    }

    .input-field-wrap label {
        display: block;
        font-size: 0.84rem;
        font-weight: 700;
        color: var(--ed-text-main, #334155);
        margin-bottom: 8px;
    }

    .input-field-wrap .req {
        color: #ef4444;
    }

    .custom-input, .custom-select, .custom-textarea {
        width: 100%;
        padding: 13px 16px;
        border-radius: 12px;
        border: 1.5px solid var(--ed-border, #e2e8f0);
        background: var(--ed-bg, #f8fafc);
        color: var(--ed-text-main, #0f172a);
        font-size: 0.9rem;
        outline: none;
        transition: 0.2s ease;
        box-sizing: border-box;
    }

    .custom-input:focus, .custom-select:focus, .custom-textarea:focus {
        border-color: #1d4ed8;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.1);
    }

    .submit-complaint-btn {
        width: 100%;
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        color: white;
        border: none;
        padding: 16px;
        border-radius: 14px;
        font-size: 1rem;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: 0.2s ease;
        box-shadow: 0 10px 20px -5px rgba(29, 78, 216, 0.35);
    }

    .submit-complaint-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -5px rgba(29, 78, 216, 0.45);
    }

    .alert-success-box {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.88rem;
        font-weight: 700;
    }

    .alert-error-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.88rem;
        font-weight: 700;
    }
</style>
@endsection
