@extends('layouts.app')
@section('title', 'الأسئلة الشائعة ومركز المساعدة | ' . \App\Models\Setting::get('site_name', 'Step by Step'))

@section('content')
<div class="faq-page-wrapper">
    <div class="faq-hero-section">
        <span class="faq-crest-badge"><i class="fa-solid fa-circle-question"></i> {{ __('مركز الإرشاد والدعم الأكاديمي') }}</span>
        <h1 class="faq-title">{{ __('الأسئلة الشائعة') }} <span>{{ __('ودليل الطالب') }}</span></h1>
        <p class="faq-desc">{{ __('كل ما تحتاج لمعرفته حول تجربة التعلم، متابعة الدروس، واستخدام أدوات منصة') }} <strong>{{ \App\Models\Setting::get('site_name', 'Step by Step') }}</strong>.</p>
    </div>

    <div class="faq-list-grid">
        @php
            $faqs = [
                [
                    'q' => 'كيف أبدأ دراسة مباحث الثانوية العامة على المنصة؟',
                    'a' => 'بعد تسجيل حسابك الجديد أو تسجيل الدخول، يمكنك الانتقال إلى دليل المقررات واختيار فرعك الأكاديمي (علمي، أدبي، ريادة وأعمال، صناعي، شرعي)، وستجد الشروحات المرئية مرتبة بالوحدات والدروس وفق المنهاج الفلسطيني المعتمد مع الملازم والتدريبات.'
                ],
                [
                    'q' => 'كيف تعمل حاسبة معدل التوجيهي والقبول الجامعي؟',
                    'a' => 'حاسبة معدل التوجيهي مصممة وفق أحدث القواعد الرسمية لوزارة التربية والتعليم الفلسطينية؛ تقوم باحتساب المواد الإجبارية وأعلى المواد الاختيارية تلقائياً وتقديم قائمة بالتخصصات الجامعية المتاحة لمعدلك في جامعات الوطن.'
                ],
                [
                    'q' => 'هل يمكنني مشاهدة الدروس عند انقطاع الإنترنت (أوفلاين)؟',
                    'a' => 'نعم، المنصة تدعم ميزة قبو الفيديوهات الأوفلاين (Offline Video Vault) وتطبيق الويب التقدمي (PWA)، حيث يمكنك تنزيل الفيديوهات التعليمية داخل التطبيق ومشاهدتها في أي وقت دون استهلاك للإنترنت.'
                ],
                [
                    'q' => 'ماذا أفعل إذا نسيت كلمة المرور الخاصة بحسابي؟',
                    'a' => 'يمكنك الانتقال إلى صفحة تسجيل الدخول والضغط على "نسيت كلمة المرور"، ثم إدخال رقم هاتفك أو بريدك الإلكتروني لاستلام رابط أو كود إعادة التعيين عبر الواتساب أو البريد فورياً.'
                ],
                [
                    'q' => 'كيف أستفيد من المعلم الأكاديمي الذكي (Step AI)؟',
                    'a' => 'المعلم الذكي متاح لمساعدتك على مدار الساعة؛ يمكنك سؤاله عن أي مسألة علمية أو مفهوم في المنهاج، كتابة المعادلات والرموز باللغتين العربية والإنجليزية، والحصول على إجابات نموذجية تفصيلية.'
                ],
                [
                    'q' => 'كيف أتواصل مع الدعم الفني أو المشرف العام؟',
                    'a' => 'يمكنك إرسال تذكرة مباشرة من صفحة "الدعم والشكاوى" أو مراسلة المشرف العام م. أحمد شمالي مباشرة عبر الواتساب المعتمد على المنصة.'
                ],
            ];
        @endphp

        @foreach($faqs as $f)
        <details class="faq-item-card">
            <summary class="faq-summary">
                <span class="faq-q-text">{{ $f['q'] }}</span>
                <span class="faq-chevron"><i class="fa-solid fa-chevron-down"></i></span>
            </summary>
            <div class="faq-answer">
                <p>{{ $f['a'] }}</p>
            </div>
        </details>
        @endforeach
    </div>
</div>

<style>
    .faq-page-wrapper {
        max-width: 960px;
        margin: 35px auto 70px;
        padding: 0 16px;
    }

    .faq-hero-section {
        text-align: center;
        margin-bottom: 40px;
    }

    .faq-crest-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eff6ff;
        color: #0b3b6f;
        border: 1px solid #bfdbfe;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.84rem;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .faq-crest-badge i {
        color: #d97706;
    }

    .faq-title {
        font-size: clamp(1.6rem, 4vw, 2.4rem);
        font-weight: 900;
        color: #072344;
        margin-bottom: 12px;
        line-height: 1.35;
    }

    .faq-title span {
        color: #0b3b6f;
    }

    .faq-desc {
        color: #64748b;
        font-size: clamp(0.92rem, 2vw, 1.05rem);
        max-width: 680px;
        margin: 0 auto;
        line-height: 1.8;
    }

    .faq-list-grid {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .faq-item-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(11, 59, 111, 0.03);
        transition: all 0.2s ease;
        overflow: hidden;
    }

    .faq-item-card:hover {
        border-color: #bfdbfe;
        box-shadow: 0 6px 18px rgba(11, 59, 111, 0.06);
    }

    .faq-item-card[open] {
        border-color: #0b3b6f;
        box-shadow: 0 8px 24px rgba(11, 59, 111, 0.08);
    }

    .faq-summary {
        padding: 18px 22px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        list-style: none;
        user-select: none;
    }

    .faq-summary::-webkit-details-marker {
        display: none;
    }

    .faq-q-text {
        font-weight: 800;
        color: #0f172a;
        font-size: 1rem;
        line-height: 1.5;
        flex: 1;
    }

    .faq-item-card[open] .faq-q-text {
        color: #0b3b6f;
    }

    .faq-chevron {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #f8fafc;
        color: #64748b;
        display: grid;
        place-items: center;
        font-size: 0.85rem;
        flex-shrink: 0;
        transition: transform 0.25s ease, background 0.2s ease, color 0.2s ease;
    }

    .faq-item-card[open] .faq-chevron {
        transform: rotate(180deg);
        background: #eff6ff;
        color: #0b3b6f;
    }

    .faq-answer {
        padding: 0 22px 20px;
        color: #475569;
        font-size: 0.92rem;
        line-height: 1.85;
        border-top: 1px solid #f1f5f9;
        margin-top: -4px;
        padding-top: 14px;
    }

    @media (max-width: 768px) {
        .faq-page-wrapper {
            margin: 20px auto calc(75px + env(safe-area-inset-bottom, 0px));
            padding: 0 12px;
        }

        .faq-summary {
            padding: 14px 16px;
        }

        .faq-q-text {
            font-size: 0.92rem;
        }

        .faq-answer {
            padding: 12px 16px 16px;
            font-size: 0.86rem;
        }
    }
</style>
@endsection
