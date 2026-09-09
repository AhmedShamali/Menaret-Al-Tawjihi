<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $studentObj = $certificate->student ?? $student ?? Auth::guard('student')->user();
        $studentName = $studentObj->name_ar ?? $studentObj->name ?? $studentObj->name_en ?? 'طالب التوجيهي المتميز';
        $studentNid = $studentObj->nid ?? null;
        $stageName = $studentObj->stage->name_ar ?? $studentObj->stage->name ?? optional($certificate->subject->stage)->name_ar ?? 'الثانوية العامة (التوجيهي)';
        $subjectName = $certificate->subject->name_ar ?? $certificate->subject->name ?? 'مساق التوجيهي المعتمد';
        $issueDate = $certificate->created_at ? $certificate->created_at->format('Y/m/d') : date('Y/m/d');
    @endphp
    <title>شهادة تفوق واجتياز أكاديمي | {{ $studentName }}</title>
    
    <!-- خطوط عربية ملكية فاخرة -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;600;700;800;900&family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=Aref+Ruqaa:wght@400;700&family=Cinzel:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        :root {
            --gold-light: #fef08a;
            --gold-mid: #d4af37;
            --gold-dark: #9a7217;
            --pal-green: #007a3d;
            --pal-red: #e4312b;
            --pal-black: #0f172a;
            --emerald-deep: #064e3b;
            --bg-parchment: #fffdf9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #090d16;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 24px 15px 50px;
            font-family: 'Alexandria', sans-serif;
            color: #1e293b;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(212, 175, 55, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(0, 122, 61, 0.08) 0%, transparent 40%);
        }

        /* شريط الإجراءات العلوي الفاخر */
        .actions-toolbar {
            width: 100%;
            max-width: 1040px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            padding: 12px 22px;
            border-radius: 18px;
            border: 1px solid rgba(212, 175, 55, 0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            flex-wrap: wrap;
            gap: 12px;
        }

        .actions-toolbar .brand-tag {
            color: #ffffff;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }

        .actions-toolbar .brand-tag i {
            color: var(--gold-mid);
            font-size: 1.2rem;
            filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.6));
        }

        .actions-toolbar .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-tool {
            padding: 9px 18px;
            border-radius: 12px;
            font-size: 0.86rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            transition: all 0.25s ease;
        }

        .btn-print {
            background: linear-gradient(135deg, #fef08a 0%, #d4af37 50%, #aa7c11 100%);
            color: #1a1300;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(212, 175, 55, 0.6);
        }

        .btn-download {
            background: #0284c7;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }
        .btn-download:hover {
            background: #0369a1;
            transform: translateY(-2px);
        }

        .btn-share {
            background: #25d366;
            color: #ffffff;
        }
        .btn-share:hover {
            background: #1da851;
            transform: translateY(-2px);
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .btn-back:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        /* حاوية الشهادة الملكية الفاخرة */
        .cert-outer-wrapper {
            width: 100%;
            max-width: 1040px;
            display: flex;
            justify-content: center;
        }

        .certificate-container {
            width: 100%;
            background: var(--bg-parchment);
            position: relative;
            padding: 16px;
            border-radius: 6px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(212, 175, 55, 0.5);
            overflow: hidden;
        }

        /* الإطار الخارجي الذهبي المضاعف */
        .cert-outer-gold-frame {
            border: 5px solid #d4af37;
            padding: 10px;
            position: relative;
            background: linear-gradient(135deg, rgba(254, 240, 138, 0.15) 0%, transparent 50%, rgba(212, 175, 55, 0.15) 100%);
        }

        .cert-inner-gold-frame {
            border: 2px solid #b38728;
            padding: 6px;
            position: relative;
        }

        /* جسم الشهادة الداخلي */
        .cert-main-card {
            border: 1px solid #e6ca65;
            padding: 40px 50px 35px;
            text-align: center;
            background: radial-gradient(circle at center, #ffffff 50%, #fffef8 100%);
            position: relative;
            z-index: 2;
        }

        /* نقش الزخرفة الهندسية في الخلفية (Guilloché Background) */
        .cert-guilloche-bg {
            position: absolute;
            inset: 0;
            opacity: 0.04;
            background-image: 
                radial-gradient(#d4af37 1.5px, transparent 1.5px),
                radial-gradient(#007a3d 1.5px, transparent 1.5px);
            background-size: 24px 24px;
            background-position: 0 0, 12px 12px;
            pointer-events: none;
            z-index: 1;
        }

        /* العلامة المائية الراقية */
        .cert-watermark-text {
            position: absolute;
            top: 52%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 13rem;
            font-weight: 900;
            color: rgba(212, 175, 55, 0.038);
            font-family: 'Amiri', serif;
            user-select: none;
            pointer-events: none;
            z-index: 1;
            white-space: nowrap;
        }

        /* زوايا الزخرفة الأرابيسك الملكية (SVG Corner Flourishes) */
        .corner-flourish {
            position: absolute;
            width: 70px;
            height: 70px;
            z-index: 5;
            pointer-events: none;
        }
        .corner-flourish svg {
            width: 100%;
            height: 100%;
            fill: #b38728;
        }
        .corner-top-right { top: 12px; right: 12px; }
        .corner-top-left { top: 12px; left: 12px; transform: scaleX(-1); }
        .corner-bottom-right { bottom: 12px; right: 12px; transform: scaleY(-1); }
        .corner-bottom-left { bottom: 12px; left: 12px; transform: scale(-1, -1); }

        /* شريط علم فلسطين في الزاوية العلوية اليمنى */
        .palestine-corner-ribbon {
            position: absolute;
            top: 0;
            right: 0;
            width: 130px;
            height: 130px;
            overflow: hidden;
            z-index: 20;
        }
        .palestine-corner-ribbon::before {
            content: "فِلَسْطِين 🇵🇸";
            position: absolute;
            top: 28px;
            right: -32px;
            transform: rotate(45deg);
            width: 160px;
            background: linear-gradient(90deg, #000000 25%, #e4312b 25%, #e4312b 50%, #ffffff 50%, #ffffff 75%, #007a3d 75%);
            color: #ffffff;
            text-shadow: 0 1px 3px rgba(0,0,0,0.8);
            text-align: center;
            font-size: 0.78rem;
            font-weight: 900;
            padding: 5px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            border-top: 1.5px solid rgba(255,255,255,0.8);
            border-bottom: 1.5px solid #d4af37;
            letter-spacing: 1px;
        }

        /* رأس الشهادة والديباجة الرسمية */
        .cert-header {
            position: relative;
            z-index: 3;
            margin-bottom: 20px;
        }

        .cert-emblem-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }

        .cert-emblem-badge {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fef08a 0%, #d4af37 50%, #aa7c11 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a1300;
            font-size: 1.6rem;
            box-shadow: 0 6px 18px rgba(212, 175, 55, 0.4), inset 0 0 6px rgba(255,255,255,0.8);
            border: 2px solid #ffffff;
        }

        .cert-state-title {
            font-family: 'Amiri', serif;
            font-size: 1.55rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }

        .cert-platform-name {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--pal-green);
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .cert-subtitle {
            font-size: 0.82rem;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* الوشاح الرئيسي لعنوان الشهادة */
        .cert-title-ribbon {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin: 16px auto 22px;
            padding: 9px 42px;
            background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
            color: #ffffff;
            border-radius: 50px;
            box-shadow: 0 6px 20px rgba(6, 78, 59, 0.35), inset 0 1px 1px rgba(255,255,255,0.4);
            border: 2px solid #d4af37;
            position: relative;
        }

        .cert-title-ribbon h2 {
            font-family: 'Aref Ruqaa', 'Amiri', serif;
            font-size: 1.65rem;
            font-weight: 700;
            letter-spacing: 1px;
            color: #fef9c3;
            text-shadow: 0 2px 4px rgba(0,0,0,0.4);
            margin: 0;
        }

        .cert-title-ribbon .cert-title-sub {
            font-family: 'Cinzel', serif;
            font-size: 0.65rem;
            color: #e2e8f0;
            letter-spacing: 2px;
            display: block;
            margin-top: 2px;
            opacity: 0.9;
        }

        /* نص الشهادة وإبراز اسم الطالب */
        .cert-body-section {
            position: relative;
            z-index: 3;
            margin: 15px 0 25px;
        }

        .cert-declaration-text {
            font-family: 'Amiri', serif;
            font-size: 1.3rem;
            color: #334155;
            margin-bottom: 12px;
            line-height: 1.6;
        }

        /* صندوق اسم الطالب الملكي الأسطوري */
        .student-hero-container {
            display: inline-block;
            position: relative;
            margin: 6px 0 14px;
            padding: 0 35px;
        }

        .student-hero-name {
            font-family: 'Amiri', serif;
            font-size: 2.85rem;
            font-weight: 700;
            color: var(--emerald-deep);
            display: inline-block;
            padding: 4px 20px 10px;
            position: relative;
            text-shadow: 0 1px 2px rgba(0,0,0,0.06);
            letter-spacing: -0.5px;
            line-height: 1.3;
        }

        /* خط التوشيح الذهبي أسفل الاسم */
        .student-hero-decor {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: -6px;
            margin-bottom: 10px;
        }

        .student-hero-decor .decor-line {
            height: 2px;
            width: 140px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
        }

        .student-hero-decor .decor-icon {
            color: #b38728;
            font-size: 0.9rem;
        }

        /* بيانات الطالب الإضافية */
        .student-meta-pills {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .student-meta-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .student-meta-pill i {
            color: var(--gold-mid);
        }

        /* نص الإنجاز والمساق */
        .cert-achievement-paragraph {
            font-size: 1.08rem;
            color: #1e293b;
            max-width: 820px;
            margin: 0 auto 16px;
            line-height: 2;
        }

        .subject-highlight-pill {
            display: inline-block;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 1.5px solid #d4af37;
            color: #0f172a;
            font-weight: 800;
            font-size: 1.15rem;
            padding: 2px 18px;
            border-radius: 8px;
            margin: 0 4px;
            box-shadow: 0 2px 6px rgba(212, 175, 55, 0.15);
        }

        /* وسام التقدير والمعدل المئوي */
        .honors-ribbon-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            padding: 6px 20px;
            border-radius: 30px;
            margin: 6px auto 20px;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.12);
        }

        .honors-ribbon-badge .grade-val {
            font-family: 'Alexandria', sans-serif;
            font-weight: 900;
            font-size: 1.25rem;
            color: var(--pal-green);
        }

        .honors-ribbon-badge .honor-text {
            font-weight: 800;
            font-size: 0.95rem;
            color: #14532d;
        }

        .honors-ribbon-badge .stars {
            color: #eab308;
            font-size: 0.85rem;
            letter-spacing: 2px;
        }

        /* القسم السفلي: التواقيع والختم ورمز التحقق QR */
        .cert-footer-section {
            position: relative;
            z-index: 3;
            display: grid;
            grid-template-columns: 1.1fr 1fr 1.1fr;
            align-items: center;
            padding-top: 24px;
            border-top: 1.5px solid rgba(212, 175, 55, 0.35);
            margin-top: 20px;
            gap: 15px;
        }

        /* 1. بلوك التحقق الإلكتروني (اليمين) */
        .footer-qr-side {
            display: flex;
            align-items: center;
            gap: 14px;
            text-align: right;
        }

        .qr-frame {
            width: 82px;
            height: 82px;
            border: 2px solid var(--gold-mid);
            border-radius: 10px;
            padding: 4px;
            background: #ffffff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            flex-shrink: 0;
        }

        .qr-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .qr-meta {
            font-size: 0.75rem;
            color: #64748b;
            line-height: 1.5;
        }

        .qr-meta .code-badge {
            font-family: monospace;
            font-weight: 800;
            color: #0f172a;
            font-size: 0.84rem;
            background: #f1f5f9;
            padding: 1px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 2px;
        }

        .qr-meta .verified-tag {
            color: var(--pal-green);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 3px;
        }

        /* 2. ختم التميز الذهبي ثلاثي الأبعاد (الوسط) */
        .footer-seal-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .golden-embossed-seal {
            width: 98px;
            height: 98px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #fef9c3 0%, #eab308 40%, #ca8a04 75%, #854d0e 100%);
            border: 3px dashed #713f12;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #451a03;
            box-shadow: 0 10px 25px rgba(202, 138, 4, 0.45), inset 0 2px 6px rgba(255,255,255,0.7);
            position: relative;
            z-index: 2;
            transform: rotate(-5deg);
        }

        .golden-embossed-seal i {
            font-size: 1.75rem;
            color: #78350f;
            margin-bottom: 2px;
            filter: drop-shadow(0 1px 1px rgba(255,255,255,0.6));
        }

        .golden-embossed-seal .seal-txt-top {
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.5px;
            color: #451a03;
        }

        .golden-embossed-seal .seal-txt-sub {
            font-family: 'Cinzel', serif;
            font-size: 0.52rem;
            font-weight: 900;
            letter-spacing: 1.5px;
            color: #78350f;
        }

        /* شريطة الختم الحريرية المنسدلة */
        .seal-ribbon-tails {
            position: absolute;
            top: 70px;
            display: flex;
            gap: 6px;
            z-index: 1;
        }

        .seal-ribbon-tail {
            width: 24px;
            height: 44px;
            clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 80%, 0 100%);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .tail-green {
            background: linear-gradient(180deg, #007a3d, #064e3b);
            border-left: 1px solid #d4af37;
            transform: rotate(-10deg);
        }

        .tail-red {
            background: linear-gradient(180deg, #e4312b, #991b1b);
            border-right: 1px solid #d4af37;
            transform: rotate(10deg);
        }

        /* 3. التوقيع الرسمي المعتمد (اليسار) */
        .footer-signature-side {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .sig-authority-title {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .sig-autograph-box {
            position: relative;
            margin-bottom: 4px;
        }

        .sig-autograph-svg {
            font-family: 'Aref Ruqaa', 'Amiri', cursive;
            font-size: 1.85rem;
            font-weight: 700;
            color: #1e3a8a;
            transform: rotate(-4deg);
            display: inline-block;
            text-shadow: 0 1px 1px rgba(30, 58, 138, 0.2);
            line-height: 1.2;
        }

        .sig-platform-stamp {
            position: absolute;
            top: -12px;
            left: -25px;
            width: 72px;
            height: 72px;
            border: 2px dashed rgba(2, 132, 199, 0.45);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: rgba(2, 132, 199, 0.6);
            font-size: 0.55rem;
            font-weight: 900;
            transform: rotate(15deg);
            pointer-events: none;
        }

        .sig-name-real {
            font-size: 1.02rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .sig-underline {
            width: 140px;
            height: 1.5px;
            background: linear-gradient(90deg, transparent, #b38728, transparent);
            margin: 3px auto 4px;
        }

        .sig-sub-location {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 600;
        }

        /* تحسينات الطباعة الصارمة والـ PDF (A4 Landscape Perfect Fit) */
        @media print {
            @page {
                size: A4 landscape;
                margin: 0;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .actions-toolbar {
                display: none !important;
            }
            .cert-outer-wrapper {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .certificate-container {
                box-shadow: none !important;
                max-width: 100% !important;
                width: 100% !important;
                height: 100vh !important;
                padding: 8px !important;
                border-radius: 0 !important;
                page-break-inside: avoid !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
            }
            .cert-main-card {
                padding: 25px 35px !important;
            }
            .student-hero-name {
                font-size: 2.5rem !important;
            }
            .cert-title-ribbon {
                margin: 10px auto 16px !important;
                padding: 6px 35px !important;
            }
        }

        /* التجاوب مع شاشات الجوال */
        @media (max-width: 768px) {
            .cert-main-card {
                padding: 30px 18px 25px;
            }
            .student-hero-name {
                font-size: 2.1rem;
            }
            .cert-title-ribbon h2 {
                font-size: 1.3rem;
            }
            .cert-achievement-paragraph {
                font-size: 0.95rem;
                line-height: 1.8;
            }
            .cert-footer-section {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            .footer-qr-side {
                justify-content: center;
            }
            .actions-toolbar {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <!-- شريط الإجراءات والتحكم العلوي -->
    <div class="actions-toolbar">
        <div class="brand-tag">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>{{ $siteName }} • وثيقة التفوق والاجتياز الأكاديمي الرسمية</span>
        </div>
        <div class="btn-group">
            <button onclick="window.print()" class="btn-tool btn-print" title="طباعة أو حفظ الشهادة بصيغة PDF عالية الدقة">
                <i class="fa-solid fa-print"></i>
                <span>طباعة / حفظ PDF</span>
            </button>
            <button onclick="downloadAsImage()" class="btn-tool btn-download" title="تحميل الشهادة كصورة PNG فاخرة لمشاركتها على وسائل التواصل">
                <i class="fa-solid fa-download"></i>
                <span>تحميل صورة PNG</span>
            </button>
            <button onclick="shareCertificate()" class="btn-tool btn-share" title="مشاركة الشهادة عبر واتساب أو الشبكات">
                <i class="fa-brands fa-whatsapp"></i>
                <span>مشاركة إنجازي</span>
            </button>
            <a href="{{ route('student.achievements') }}" class="btn-tool btn-back">
                <i class="fa-solid fa-arrow-left"></i>
                <span>لوحة إنجازاتي</span>
            </a>
        </div>
    </div>

    <!-- الإطار الخارجي للشهادة -->
    <div class="cert-outer-wrapper">
        <div class="certificate-container" id="printableCertificate">
            
            <!-- شريط علم فلسطين الفاخر في الزاوية -->
            <div class="palestine-corner-ribbon"></div>

            <div class="cert-outer-gold-frame">
                <div class="cert-inner-gold-frame">
                    
                    <!-- الزوايا الأرابيسك الملكية (SVG Corner Flourishes) -->
                    <div class="corner-flourish corner-top-right">
                        <svg viewBox="0 0 100 100">
                            <path d="M0,0 L100,0 L100,20 C60,20 20,60 20,100 L0,100 Z M10,10 L80,10 C50,25 25,50 10,80 Z M30,10 C45,25 25,45 10,30 Z"/>
                        </svg>
                    </div>
                    <div class="corner-flourish corner-top-left">
                        <svg viewBox="0 0 100 100">
                            <path d="M0,0 L100,0 L100,20 C60,20 20,60 20,100 L0,100 Z M10,10 L80,10 C50,25 25,50 10,80 Z M30,10 C45,25 25,45 10,30 Z"/>
                        </svg>
                    </div>
                    <div class="corner-flourish corner-bottom-right">
                        <svg viewBox="0 0 100 100">
                            <path d="M0,0 L100,0 L100,20 C60,20 20,60 20,100 L0,100 Z M10,10 L80,10 C50,25 25,50 10,80 Z M30,10 C45,25 25,45 10,30 Z"/>
                        </svg>
                    </div>
                    <div class="corner-flourish corner-bottom-left">
                        <svg viewBox="0 0 100 100">
                            <path d="M0,0 L100,0 L100,20 C60,20 20,60 20,100 L0,100 Z M10,10 L80,10 C50,25 25,50 10,80 Z M30,10 C45,25 25,45 10,30 Z"/>
                        </svg>
                    </div>

                    <div class="cert-main-card">
                        
                        <!-- نقش الـ Guilloché والعلامة المائية -->
                        <div class="cert-guilloche-bg"></div>
                        <div class="cert-watermark-text">مَنَارَة التَّوْجِيهِي</div>

                        <!-- 1. رأس الشهادة الرسمي والترويسة -->
                        <div class="cert-header">
                            <div class="cert-emblem-wrap">
                                <div class="cert-emblem-badge">
                                    <i class="fa-solid fa-crown"></i>
                                </div>
                            </div>
                            <div class="cert-state-title">دَوْلَةُ فِلَسْطِين</div>
                            <div class="cert-platform-name">
                                <i class="fa-solid fa-book-open-reader"></i>
                                <span>{{ $siteName }}</span>
                            </div>
                            <div class="cert-subtitle">
                                {{ $siteSlogan ?? 'المنصة الوطنية الرائدة لطلبة الثانوية العامة (التوجيهي) في فلسطين' }}
                            </div>

                            <!-- الوشاح الملكي لعنوان الوثيقة -->
                            <div class="cert-title-ribbon">
                                <div>
                                    <h2>شَهَادَةُ تَفَوُّقٍ وَاجْتِيَازٍ أَكَادِيمِيٍّ</h2>
                                    <span class="cert-title-sub">CERTIFICATE OF ACADEMIC EXCELLENCE & HONORS</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. جسم الشهادة وبيانات الطالب الحقيقية الفاخرة -->
                        <div class="cert-body-section">
                            <p class="cert-declaration-text">
                                تَشْهَدُ إِدَارَةُ الْمَنَصَّةِ التَّعْلِيمِيَّةِ وَهَيْئَتُهَا التَّدْرِيسِيَّةُ بِأَنَّ الطَّالِبَ(ة) الْمُتَمَيِّز(ة):
                            </p>

                            <!-- اسم الطالب الحقيقي البارز بوضوح فائق -->
                            <div class="student-hero-container">
                                <div class="student-hero-name">
                                    {{ $studentName }}
                                </div>
                                <div class="student-hero-decor">
                                    <span class="decor-line"></span>
                                    <i class="fa-solid fa-star decor-icon"></i>
                                    <i class="fa-solid fa-award decor-icon" style="font-size: 1.15rem; color: #d4af37;"></i>
                                    <i class="fa-solid fa-star decor-icon"></i>
                                    <span class="decor-line"></span>
                                </div>
                            </div>

                            <!-- تفاصيل المسار والفرع الدراسي للطالب -->
                            <div class="student-meta-pills">
                                @if($studentNid)
                                    <div class="student-meta-pill">
                                        <i class="fa-solid fa-id-card"></i>
                                        <span>رقم الهوية الوزاري: {{ substr($studentNid, 0, 3) }}****{{ substr($studentNid, -2) }}</span>
                                    </div>
                                @endif
                                <div class="student-meta-pill">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                    <span>الفرع الأكاديمي: {{ $stageName }}</span>
                                </div>
                                <div class="student-meta-pill">
                                    <i class="fa-solid fa-award"></i>
                                    <span>دفعة الثانوية العامة 2026/2027</span>
                                </div>
                            </div>

                            <!-- نص الإنجاز والمساق الأكاديمي -->
                            <p class="cert-achievement-paragraph">
                                قَدِ اجْتَازَ(تْ) بِنَجَاحٍ وَاقْتِدَارٍ رَفِيعٍ كَافَّةَ مُتَطَلَّبَاتِ وَاخْتِبَارَاتِ وَشُرُوحَاتِ مَسَاقِ:
                                <span class="subject-highlight-pill">{{ $subjectName }}</span>
                                طِبْقاً لِمَعَايِيرِ وَكِفَايَاتِ الْمِنْهَاجِ الْوِزَارِيِّ الْفِلَسْطِينِيِّ الْمُعْتَمَدِ، وَاسْتَحَقَّ(تْ) هَذِهِ الشَّهَادَةَ تَقْدِيراً لِعَطَائِهِ(هَا) وَتَمَيُّزِهِ(هَا) الْمُسْتَمِرِّ.
                            </p>

                            <!-- وسام التقدير والمعدل مع النجوم -->
                            <div class="honors-ribbon-badge">
                                <span class="honor-text">بتقدير: امتياز وتفوق وزاري</span>
                                <span class="grade-val">({{ $certificate->final_grade ?? 96 }}%)</span>
                                <span class="stars">★★★★★</span>
                            </div>
                        </div>

                        <!-- 3. القسم السفلي: التواقيع والختم ورمز التحقق QR -->
                        <div class="cert-footer-section">
                            
                            <!-- اليمين: رمز التحقق الرقمي المعتمد QR -->
                            <div class="footer-qr-side">
                                <div class="qr-frame">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($verificationUrl) }}" 
                                         alt="QR Verification">
                                </div>
                                <div class="qr-meta">
                                    <div>الرمز الرقمي المعتمد:</div>
                                    <div class="code-badge">{{ $certificate->certificate_code }}</div>
                                    <div>تاريخ الاعتماد: {{ $issueDate }} م</div>
                                    <div class="verified-tag">
                                        <i class="fa-solid fa-shield-halved"></i>
                                        <span>وثيقة معتمدة وموثقة رقمياً</span>
                                    </div>
                                </div>
                            </div>

                            <!-- الوسط: الختم الذهبي الملكي النافر مع الأشرطة الحريرية -->
                            <div class="footer-seal-center">
                                <div class="golden-embossed-seal">
                                    <i class="fa-solid fa-certificate"></i>
                                    <span class="seal-txt-top">مُعْتَمَدٌ رَسْمِيّاً</span>
                                    <span class="seal-txt-sub">TAWJIHI EXCELLENCE</span>
                                </div>
                                <div class="seal-ribbon-tails">
                                    <div class="seal-ribbon-tail tail-green"></div>
                                    <div class="seal-ribbon-tail tail-red"></div>
                                </div>
                            </div>

                            <!-- اليسار: التوقيع الرسمي لإدارة المنصة -->
                            <div class="footer-signature-side">
                                <div class="sig-authority-title">المشرف العام وإدارة المنصة</div>
                                <div class="sig-autograph-box">
                                    <div class="sig-autograph-svg">أحمد حسين شمالي</div>
                                    <div class="sig-platform-stamp">
                                        <span>منارة</span>
                                        <span>التوجيهي</span>
                                        <span>معتمد</span>
                                    </div>
                                </div>
                                <div class="sig-name-real">أ. أحمد حسين شمالي</div>
                                <div class="sig-underline"></div>
                                <div class="sig-sub-location">منارة التوجيهي - القدس، فلسطين 🇵🇸</div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- دوال تصدير الصورة والمشاركة -->
    <script>
        function downloadAsImage() {
            const certElement = document.getElementById('printableCertificate');
            const studentCleanName = "{{ addslashes($studentName) }}".replace(/\s+/g, '_');
            
            const originalBtn = event.currentTarget;
            const originalText = originalBtn.innerHTML;
            originalBtn.disabled = true;
            originalBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري إعداد الصورة...';

            html2canvas(certElement, {
                scale: 2.5,
                useCORS: true,
                backgroundColor: '#fffdf9'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `شهادة_تفوق_${studentCleanName}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
                originalBtn.disabled = false;
                originalBtn.innerHTML = originalText;
            }).catch(err => {
                alert('حدث خطأ أثناء إعداد الصورة، يرجى استخدام زر طباعة / حفظ PDF كبديل مباشر.');
                originalBtn.disabled = false;
                originalBtn.innerHTML = originalText;
            });
        }

        function shareCertificate() {
            const title = "شهادة تفوق واجتياز أكاديمي | {{ addslashes($studentName) }}";
            const text = "بفضل الله وتوفيقه، حصلت على شهادة التفوق والاجتياز الأكاديمي في مساق ({{ addslashes($subjectName) }}) عبر {{ addslashes($siteName) }} بمعدل {{ $certificate->final_grade ?? 96 }}% 🎓🇵🇸";
            const url = "{{ $verificationUrl }}";

            if (navigator.share) {
                navigator.share({ title, text, url }).catch(() => {});
            } else {
                const waUrl = `https://wa.me/?text=${encodeURIComponent(text + '\n' + url)}`;
                window.open(waUrl, '_blank');
            }
        }
    </script>
</body>
</html>
