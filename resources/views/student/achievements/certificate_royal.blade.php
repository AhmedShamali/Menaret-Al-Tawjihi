<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شهادة تفوق واجتياز أكاديمي | {{ $certificate->student->name ?? 'طالب التوجيهي' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;600;700;800;900&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --gold-main: #d4af37;
            --gold-dark: #aa7c11;
            --gold-light: #fef08a;
            --palestine-green: #007a3d;
            --palestine-red: #e4312b;
            --palestine-black: #0f172a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #0b1120;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
            font-family: 'Alexandria', sans-serif;
            color: #1e293b;
        }

        /* شريط الإجراءات العلوي */
        .actions-toolbar {
            width: 100%;
            max-width: 960px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            padding: 14px 24px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .actions-toolbar .brand-tag {
            color: #ffffff;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .actions-toolbar .btn-group {
            display: flex;
            gap: 12px;
        }

        .btn-tool {
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-print {
            background: linear-gradient(135deg, #d4af37, #b8860b);
            color: #000000;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.35);
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.5);
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }
        .btn-back:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* برواز الشهادة الملكية */
        .certificate-container {
            width: 100%;
            max-width: 960px;
            background: #ffffff;
            position: relative;
            padding: 24px;
            border-radius: 4px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .cert-outer-border {
            border: 8px double #d4af37;
            padding: 16px;
            position: relative;
        }

        .cert-inner-frame {
            border: 2px solid #ecd688;
            padding: 40px 45px;
            text-align: center;
            background: radial-gradient(circle at center, #ffffff 60%, #fffdf6 100%);
            position: relative;
            z-index: 2;
        }

        /* العلامة المائية */
        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 16rem;
            color: rgba(212, 175, 55, 0.035);
            font-family: 'Amiri', serif;
            user-select: none;
            pointer-events: none;
            z-index: 1;
        }

        /* شريط علم فلسطين في الزوايا */
        .flag-ribbon {
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 120px;
            overflow: hidden;
            z-index: 10;
        }
        .flag-ribbon::before {
            content: "فلسطين 🇵🇸";
            position: absolute;
            top: 26px;
            right: -28px;
            transform: rotate(45deg);
            width: 150px;
            background: #007a3d;
            color: #fff;
            text-align: center;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            border-top: 2px solid #e4312b;
            border-bottom: 2px solid #000;
        }

        /* رأس الشهادة */
        .cert-header {
            margin-bottom: 25px;
        }
        .cert-logo-name {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .cert-slogan {
            font-size: 0.88rem;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .cert-title-badge {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 30px;
            background: linear-gradient(135deg, #d4af37, #996515);
            color: #ffffff;
            font-size: 1.4rem;
            font-weight: 900;
            border-radius: 50px;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        /* نص الشهادة الرئيسي */
        .cert-body {
            margin: 30px 0;
            line-height: 2;
        }
        .cert-declaration {
            font-family: 'Amiri', serif;
            font-size: 1.4rem;
            color: #334155;
            margin-bottom: 15px;
        }
        .student-name-box {
            font-size: 2.3rem;
            font-weight: 900;
            color: #007a3d;
            border-bottom: 2px dashed #cbd5e1;
            display: inline-block;
            padding: 0 40px 8px;
            margin-bottom: 20px;
            font-family: 'Amiri', serif;
        }
        .cert-achievement-text {
            font-size: 1.1rem;
            color: #1e293b;
            max-width: 750px;
            margin: 0 auto 25px;
            line-height: 1.9;
        }
        .highlight-subject {
            font-weight: 800;
            color: #0f172a;
            background: #fef08a;
            padding: 2px 10px;
            border-radius: 6px;
        }

        /* بطاقة الدرجة والباركود */
        .cert-meta-grid {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 25px;
            border-top: 1px solid #f1f5f9;
        }

        .cert-qr-block {
            display: flex;
            align-items: center;
            gap: 15px;
            text-align: right;
        }
        .cert-qr-img {
            width: 85px;
            height: 85px;
            border: 2px solid #d4af37;
            border-radius: 8px;
            padding: 4px;
            background: #fff;
        }
        .cert-qr-info {
            font-size: 0.78rem;
            color: #64748b;
            line-height: 1.6;
        }
        .cert-code {
            font-family: monospace;
            font-weight: 800;
            color: #0f172a;
            font-size: 0.88rem;
        }

        /* الختم الذهبي */
        .cert-golden-seal {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: radial-gradient(circle, #fef08a, #d4af37 70%, #aa7c11);
            border: 3px dashed #996515;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #583c07;
            box-shadow: 0 6px 18px rgba(212, 175, 55, 0.4);
            transform: rotate(-10deg);
        }
        .cert-golden-seal i {
            font-size: 1.8rem;
            margin-bottom: 2px;
        }
        .cert-golden-seal span {
            font-size: 0.65rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        /* التواقيع */
        .cert-signatures {
            text-align: center;
        }
        .signature-title {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 6px;
        }
        .signature-name {
            font-weight: 800;
            font-size: 1.05rem;
            color: #0f172a;
        }
        .signature-line {
            width: 150px;
            height: 2px;
            background: #cbd5e1;
            margin: 8px auto 0;
        }

        /* نمط الطباعة الصارمة */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .actions-toolbar {
                display: none !important;
            }
            .certificate-container {
                box-shadow: none !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body>

    <!-- شريط التحكم العلوي -->
    <div class="actions-toolbar">
        <div class="brand-tag">
            <i class="fa-solid fa-graduation-cap" style="color: #d4af37;"></i>
            <span>{{ $siteName }} • شهادة التفوق الرسمية</span>
        </div>
        <div class="btn-group">
            <button onclick="window.print()" class="btn-tool btn-print">
                <i class="fa-solid fa-print"></i> طباعة / حفظ الشهادة PDF
            </button>
            <a href="{{ route('student.dashboard') }}" class="btn-tool btn-back">
                <i class="fa-solid fa-arrow-left"></i> العودة للرئيسية
            </a>
        </div>
    </div>

    <!-- بطاقة الشهادة الرسمية -->
    <div class="certificate-container">
        <div class="flag-ribbon"></div>
        <div class="cert-outer-border">
            <div class="cert-inner-frame">
                <div class="cert-watermark">منارة</div>

                <div class="cert-header">
                    @if(\App\Models\Setting::get('site_logo'))
                        <div style="margin-bottom: 12px;">
                            <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ $siteName }}" style="max-height: 75px; max-width: 150px; object-fit: contain;">
                        </div>
                    @endif
                    <div class="cert-logo-name">{{ $siteName }}</div>
                    <div class="cert-slogan">{{ $siteSlogan }}</div>
                    <div class="cert-title-badge">شهادة إتمام وتفوق أكاديمي 🏆</div>
                </div>

                <div class="cert-body">
                    <p class="cert-declaration">تشهد إدارة المنصة التعليمية بأن الطالب / الطالبة المتميز(ة):</p>
                    <div class="student-name-box">
                        {{ $certificate->student->name ?? 'طالب التوجيهي المتميز' }}
                    </div>
                    <p class="cert-achievement-text">
                        قد اجتاز(ت) بنجاح واقتدار متطلبات ودراسة مساق:
                        <span class="highlight-subject">{{ $certificate->subject->name_ar ?? $certificate->subject->name ?? 'منهاج الثانوية العامة' }}</span>
                        وفق أحدث معايير المنهاج الفلسطيني وبمعدل تميز بلغ 
                        <strong>({{ $certificate->final_grade }}%)</strong>.
                    </p>
                </div>

                <div class="cert-meta-grid">
                    <!-- التحقق الرقمي عبر QR -->
                    <div class="cert-qr-block">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($verificationUrl) }}" 
                             alt="QR Code" class="cert-qr-img">
                        <div class="cert-qr-info">
                            <div>رمز الشهادة الرسمي:</div>
                            <div class="cert-code">{{ $certificate->certificate_code }}</div>
                            <div>تاريخ الإصدار: {{ $certificate->created_at ? $certificate->created_at->format('Y-m-d') : date('Y-m-d') }}</div>
                            <div style="color: #007a3d; font-weight: 700;">شهادة معتمدة وموثقة رقمياً ✅</div>
                        </div>
                    </div>

                    <!-- الختم الذهبي للمنصة -->
                    <div class="cert-golden-seal">
                        <i class="fa-solid fa-certificate"></i>
                        <span>معتمد رسمياً</span>
                        <small style="font-size: 0.55rem; font-weight: 800;">EXCELLENCE</small>
                    </div>

                    <!-- التوقيع الرسمي المعتمد -->
                    <div class="cert-signatures">
                        <div class="signature-title">المشرف العام وإدارة المنصة</div>
                        <div class="signature-name">أحمد حسين شمالي</div>
                        <div class="signature-line"></div>
                        <small style="color: #94a3b8; font-size: 0.7rem;">منارة التوجيهي - فلسطين 🇵🇸</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
