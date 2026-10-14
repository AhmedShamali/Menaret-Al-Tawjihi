@extends('layouts.app')
@section('title', 'اتفاقية شروط الاستخدام والأمانة الأكاديمية | ' . \App\Models\Setting::get('site_name', 'Step by Step'))

@section('content')
<div class="policy-page-wrapper">
    <div class="policy-card-frame">
        <div class="policy-header">
            <span class="policy-badge"><i class="fa-solid fa-scale-balanced"></i> {{ __('الميثاق الأكاديمي الرسمي') }}</span>
            <h1 class="policy-title">{{ __('اتفاقية شروط الاستخدام') }}</h1>
            <p class="policy-subtitle">{{ __('القواعد المنظمة لاستخدام منصة') }} <strong>{{ \App\Models\Setting::get('site_name', 'Step by Step') }}</strong> {{ __('والتزامات الطلبة الأكاديمية') }}</p>
        </div>

        <div class="policy-body">
            <div class="term-item-box">
                <div class="term-icon"><i class="fa-solid fa-copyright"></i></div>
                <div class="term-content">
                    <h3>{{ __('1. حقوق الملكية الفكرية للمحتوى التعليمي') }}</h3>
                    <p>{{ __('كافة الشروحات المرئية، الملازم الدراسية، الملخصات، بطاقات الاستذكار، وبنوك الأسئلة المنشورة على المنصة هي ملكية فكرية وأكاديمية حصرية لمنصة') }} <strong>{{ \App\Models\Setting::get('site_name', 'Step by Step') }}</strong> {{ __('والمشرف العام م. أحمد شمالي. يحظر قطعياً إعادة نشرها، تصويرها، بيعها، أو تداولها خارج النطاق الشخصي المصرح به للطالب.') }}</p>
                </div>
            </div>

            <div class="term-item-box">
                <div class="term-icon"><i class="fa-solid fa-user-shield"></i></div>
                <div class="term-content">
                    <h3>{{ __('2. الأمانة العلمية والسلوك الأكاديمي') }}</h3>
                    <p>{{ __('يلتزم الطالب بأعلى معايير النزاهة العلمية أثناء تأدية الاختبارات الإلكترونية وبنوك التدريبات. أي محاولة للتلاعب بنظام الاختبارات أو مشاركة بيانات الحساب مع أطراف أخرى تعرض الحساب للإيقاف الفوري دون استرداد لأي رسوم.') }}</p>
                </div>
            </div>

            <div class="term-item-box">
                <div class="term-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div class="term-content">
                    <h3>{{ __('3. حماية الحساب وبيانات الدخول') }}</h3>
                    <p>{{ __('حساب الطالب شخصي وغير قابل للمشاركة أو البيع. تضمن المنصة تشفير بيانات الدخول وأرقام الهواتف وعدم الإفصاح عنها لأي جهة غير معنية.') }}</p>
                </div>
            </div>

            <div class="term-notice-box">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ __('تسجيلك واستخدامك للمنصة يعتبر إقراراً كاملاً وموافقة صريحة على بنود هذا الميثاق الأكاديمي.') }}</span>
            </div>
        </div>
    </div>
</div>

<style>
    .policy-page-wrapper {
        max-width: 920px;
        margin: 35px auto 70px;
        padding: 0 16px;
    }

    .policy-card-frame {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(11, 59, 111, 0.04);
        overflow: hidden;
        border-top: 4px solid #0b3b6f;
    }

    .policy-header {
        padding: 32px 32px 24px;
        text-align: center;
        border-bottom: 1px solid #f1f5f9;
        background: #f8fafc;
    }

    .policy-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .policy-title {
        font-size: clamp(1.5rem, 3.8vw, 2.2rem);
        font-weight: 900;
        color: #072344;
        margin-bottom: 8px;
        line-height: 1.35;
    }

    .policy-subtitle {
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.7;
    }

    .policy-body {
        padding: clamp(20px, 4vw, 36px);
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .term-item-box {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 18px 20px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: border-color 0.2s ease;
    }

    .term-item-box:hover {
        border-color: #bfdbfe;
    }

    .term-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #eff6ff;
        color: #0b3b6f;
        display: grid;
        place-items: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        border: 1px solid #bfdbfe;
    }

    .term-content h3 {
        font-size: 1.02rem;
        font-weight: 800;
        color: #072344;
        margin: 0 0 6px;
    }

    .term-content p {
        margin: 0;
        font-size: 0.9rem;
        color: #475569;
        line-height: 1.85;
    }

    .term-notice-box {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 10px;
        color: #065f46;
        font-size: 0.88rem;
        font-weight: 700;
        margin-top: 8px;
    }

    .term-notice-box i {
        font-size: 1.15rem;
        color: #059669;
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .policy-page-wrapper {
            margin: 20px auto calc(75px + env(safe-area-inset-bottom, 0px));
            padding: 0 12px;
        }

        .policy-header {
            padding: 24px 16px 18px;
        }

        .policy-body {
            padding: 18px 14px;
        }

        .term-item-box {
            flex-direction: column;
            gap: 12px;
            padding: 14px 14px;
        }
    }
</style>
@endsection
