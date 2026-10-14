@extends('layouts.app')
@section('title', 'وثيقة سياسة الخصوصية وحماية البيانات الأكاديمية | ' . \App\Models\Setting::get('site_name', 'Step by Step'))

@section('content')
<div class="policy-page-wrapper">
    <div class="policy-card-frame">
        <div class="policy-header">
            <span class="policy-badge"><i class="fa-solid fa-user-shield"></i> {{ __('حماية البيانات والخصوصية الرقمية') }}</span>
            <h1 class="policy-title">{{ __('وثيقة سياسة الخصوصية') }}</h1>
            <p class="policy-subtitle">{{ __('التزام منصة') }} <strong>{{ \App\Models\Setting::get('site_name', 'Step by Step') }}</strong> {{ __('بحماية وتشفير بيانات الطلبة والمعلمين') }}</p>
        </div>

        <div class="policy-body">
            <div class="term-intro-note">
                <i class="fa-solid fa-lock"></i>
                <p>{{ __('في') }} <strong>{{ \App\Models\Setting::get('site_name', 'Step by Step') }}</strong>{{ __('، نعتبر خصوصية الطالب وأمان بياناته الدراسية أولوية عليا؛ نوضح هنا بدقة كيفية جمع البيانات وتأمينها وفق أعلى المعايير الرقمية.') }}</p>
            </div>

            <div class="term-item-box">
                <div class="term-icon"><i class="fa-solid fa-id-card"></i></div>
                <div class="term-content">
                    <h3>{{ __('1. البيانات التي نجمعها والغرض منها') }}</h3>
                    <p>{{ __('نجمع الاسم، رقم الهاتف، والفرع الدراسي بهدف توثيق الحساب الأكاديمي، متابعة مستوى التحصيل، وإرسال إشعارات الاختبارات والدروس الجديدة. كافة صور وثائق التحقق مشفرة رقمياً ومحمية بصلاحيات صارمة.') }}</p>
                </div>
            </div>

            <div class="term-item-box">
                <div class="term-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                <div class="term-content">
                    <h3>{{ __('2. خصوصية محادثات الذكاء الاصطناعي (Step AI)') }}</h3>
                    <p>{{ __('الأسئلة العلمية والمحادثات مع المعلم الأكاديمي الذكي تخضع لمعالجة برمجية مشفرة بهدف تقديم الشروحات والإجابات النموذجية فقط، ولا يتم بيعها أو مشاركتها مع أي جهة تجارية خارجية إطلاقاً.') }}</p>
                </div>
            </div>

            <div class="term-item-box">
                <div class="term-icon"><i class="fa-solid fa-server"></i></div>
                <div class="term-content">
                    <h3>{{ __('3. أمن التخزين وسرية الدفع') }}</h3>
                    <p>{{ __('كافة المعاملات وسجلات التحصيل محمية بقنوات اتصال آمنة ومحدثة (SSL/TLS)، ولا يتم حفظ أي بيانات مصرفية حساسة داخل المنصة.') }}</p>
                </div>
            </div>

            <div class="term-notice-box">
                <i class="fa-solid fa-shield-check"></i>
                <span>{{ __('نحن نلتزم بالمعايير المعتمدة لحماية البيانات والخصوصية الرقمية للطلبة في دولة فلسطين.') }}</span>
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
        border-top: 4px solid #d97706;
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
        background: #eff6ff;
        color: #0b3b6f;
        border: 1px solid #bfdbfe;
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .policy-badge i {
        color: #d97706;
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

    .term-intro-note {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px 20px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        color: #0b3b6f;
        font-size: 0.92rem;
        line-height: 1.8;
    }

    .term-intro-note i {
        font-size: 1.25rem;
        color: #1d4ed8;
        margin-top: 3px;
        flex-shrink: 0;
    }

    .term-intro-note p {
        margin: 0;
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
        background: #ffffff;
        color: #0b3b6f;
        display: grid;
        place-items: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
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
