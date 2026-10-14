<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <title>التحقق من صحة الشهادة الرقمية | {{ $siteName }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800;900&family=Alexandria:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --royal-navy: #0b3b6f;
            --royal-navy-dark: #072344;
            --royal-navy-hover: #092c55;
            --royal-navy-soft: #eff6ff;
            --royal-gold: #d97706;
            --royal-gold-soft: #fffbeb;
            --royal-gold-border: #fde68a;
            --royal-emerald: #059669;
            --royal-emerald-soft: #ecfdf5;
            --royal-emerald-border: #a7f3d0;
            --academic-bg: #f8fafc;
            --academic-surface: #ffffff;
            --academic-border: #e2e8f0;
            --academic-border-dark: #cbd5e1;
            --academic-text-title: #0f172a;
            --academic-text-body: #334155;
            --academic-text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', 'Alexandria', serif, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background: var(--academic-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: var(--academic-text-body);
        }

        .verify-card {
            background: #ffffff;
            width: 100%;
            max-width: 580px;
            border-radius: 20px;
            border: 1px solid var(--academic-border);
            box-shadow: 0 10px 30px rgba(11, 59, 111, 0.06);
            overflow: hidden;
            text-align: center;
        }

        .verify-header {
            background: linear-gradient(135deg, var(--royal-navy-dark) 0%, var(--royal-navy) 100%);
            color: white;
            padding: 32px 24px;
            position: relative;
            border-bottom: 3px solid var(--royal-gold);
        }

        .header-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 1.85rem;
            margin: 0 auto 14px;
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .verify-header h2 {
            font-size: clamp(1.2rem, 3vw, 1.5rem);
            font-weight: 800;
            letter-spacing: -0.3px;
        }

        .verify-header p {
            opacity: 0.92;
            font-size: 0.86rem;
            margin-top: 4px;
            font-weight: 500;
        }

        .verify-body {
            padding: clamp(20px, 4vw, 32px);
        }

        .badge-verified {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--royal-emerald-soft);
            color: var(--royal-emerald);
            border: 1px solid var(--royal-emerald-border);
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.92rem;
            font-weight: 800;
            margin-bottom: 22px;
        }

        .badge-invalid {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.92rem;
            font-weight: 800;
            margin-bottom: 22px;
        }

        .info-grid {
            background: var(--academic-bg);
            border-radius: 14px;
            padding: 16px 20px;
            border: 1px solid var(--academic-border);
            text-align: right;
            margin-bottom: 24px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--academic-border);
            font-size: 0.9rem;
            gap: 12px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--academic-text-muted);
            font-weight: 600;
            white-space: nowrap;
        }

        .info-value {
            color: var(--academic-text-title);
            font-weight: 800;
            text-align: left;
        }

        .actions-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .btn-home {
            background: var(--royal-navy);
            color: #ffffff;
            text-decoration: none;
            padding: 11px 24px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            border: 1px solid var(--royal-navy-dark);
            box-shadow: 0 2px 8px rgba(11, 59, 111, 0.15);
        }

        .btn-home:hover {
            background: var(--royal-navy-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(11, 59, 111, 0.25);
            color: #ffffff;
        }

        .btn-home-secondary {
            background: #ffffff;
            color: var(--academic-text-body);
            border: 1px solid var(--academic-border-dark);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .btn-home-secondary:hover {
            background: var(--academic-bg);
            color: var(--royal-navy);
            border-color: var(--royal-navy);
        }

        /* زر العودة للأعلى */
        .ed-scroll-top-btn {
            position: fixed;
            bottom: 24px;
            left: 24px;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--royal-navy-dark) 0%, var(--royal-navy) 100%);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 16px rgba(11, 59, 111, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            cursor: pointer;
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(14px) scale(0.92);
            transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s ease;
        }

        .ed-scroll-top-btn.visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }

        .ed-scroll-top-btn:hover {
            background: linear-gradient(135deg, var(--royal-navy) 0%, #1d4ed8 100%);
            box-shadow: 0 6px 20px rgba(11, 59, 111, 0.35);
            transform: translateY(-3px) scale(1.05);
            color: #ffffff;
        }

        html[dir="ltr"] .ed-scroll-top-btn {
            left: auto;
            right: 24px;
        }

        @media (max-width: 480px) {
            .verify-card {
                border-radius: 16px;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .info-value {
                text-align: right;
            }

            .actions-row {
                flex-direction: column;
            }

            .btn-home {
                width: 100%;
                justify-content: center;
            }

            .ed-scroll-top-btn {
                bottom: 20px;
                left: 14px;
                width: 38px;
                height: 38px;
                font-size: 0.92rem;
            }

            html[dir="ltr"] .ed-scroll-top-btn {
                left: auto;
                right: 14px;
            }
        }
    </style>
</head>
<body>

    <div class="verify-card">
        <div class="verify-header">
            @if(\App\Models\Setting::get('site_logo'))
                <div style="margin-bottom: 12px;">
                    <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ $siteName }}" style="max-height: 54px; max-width: 130px; object-fit: contain; background: white; padding: 4px 8px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.15);" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                </div>
            @else
                <div class="header-icon">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
            @endif
            <h2>{{ __('بوابة التحقق الرسمية للشهادات') }}</h2>
            <p>{{ $siteName }} • دولة فلسطين</p>
        </div>

        <div class="verify-body">
            @if($certificate)
                <div class="badge-verified">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ __('شهادة أصلية وموثقة بسجلات المنصة الرسمية') }}</span>
                </div>

                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">{{ __('اسم الطالب / الطالبة:') }}</span>
                        <span class="info-value" style="color: var(--royal-emerald); font-size: 1rem; font-weight: 800;">{{ $certificate->student?->name_ar ?? $certificate->student?->name ?? 'طالب التوجيهي المتميز' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('المادة الأكاديمية:') }}</span>
                        <span class="info-value">{{ $certificate->subject?->name_ar ?? $certificate->subject?->name ?? __('مادة دراسية') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('معدل الاجتياز والتفوق:') }}</span>
                        <span class="info-value" style="color: var(--royal-gold); font-size: 1.05rem;">{{ $certificate->final_grade }}%</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('رمز الشهادة المرجعي:') }}</span>
                        <span class="info-value" style="font-family: monospace; letter-spacing: 0.5px;">{{ $certificate->certificate_code }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('تاريخ الإصدار:') }}</span>
                        <span class="info-value">{{ $certificate->created_at ? $certificate->created_at->format('Y-m-d') : date('Y-m-d') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('المشرف العام المعتمد:') }}</span>
                        <span class="info-value">{{ __('م. أحمد شمالي') }}</span>
                    </div>
                </div>

                <div class="actions-row">
                    <a href="{{ route('student.certificates.show', $certificate->id) }}" class="btn-home">
                        <i class="fa-solid fa-eye"></i>
                        <span>{{ __('استعراض الشهادة') }}</span>
                    </a>
                    <a href="/" class="btn-home btn-home-secondary">
                        <i class="fa-solid fa-house"></i>
                        <span>{{ __('الرئيسية') }}</span>
                    </a>
                </div>
            @else
                <div class="badge-invalid">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ __('عذراً، هذا الرمز غير صالح أو لم يتم العثور على الشهادة') }}</span>
                </div>
                <p style="color: var(--academic-text-muted); font-size: 0.88rem; margin-bottom: 22px; line-height: 1.6;">
                    {{ __('الرمز المدخل:') }} <strong style="color: var(--academic-text-title); font-family: monospace;">{{ $code }}</strong> {{ __('غير مسجل في السجلات الأكاديمية للمنصة.') }}
                </p>
                <div class="actions-row">
                    <a href="/" class="btn-home">
                        <i class="fa-solid fa-house"></i>
                        <span>{{ __('العودة للرئيسية') }}</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function scrollToPageTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        window.addEventListener('scroll', function() {
            const btn = document.getElementById('edScrollTopBtn');
            if (btn) {
                if (window.scrollY > 280) {
                    btn.classList.add('visible');
                } else {
                    btn.classList.remove('visible');
                }
            }
        }, { passive: true });
    </script>

    <!-- زر العودة إلى بداية الصفحة الكلاسيكي الأنيق -->
    <button type="button" class="ed-scroll-top-btn" id="edScrollTopBtn" aria-label="{{ __('العودة إلى بداية الصفحة') }}" title="{{ __('العودة للأعلى') }}" onclick="scrollToPageTop()">
        <i class="fa-solid fa-chevron-up"></i>
    </button>
</body>
</html>
