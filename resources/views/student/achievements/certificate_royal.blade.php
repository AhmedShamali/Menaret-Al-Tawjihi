<!DOCTYPE html>
<html lang="ar" dir="rtl" id="certHtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $studentObj = $certificate->student ?? $student ?? Auth::guard('student')->user();
        $studentNameAr = $studentObj->name_ar ?? $studentObj->name ?? 'طالب التوجيهي المتميز';
        $studentNameEn = $studentObj->name_en ?? $studentObj->name_ar ?? 'Distinguished Student';
        $studentNid = $studentObj->nid ?? null;
        $stageNameAr = $studentObj->stage->name_ar ?? $studentObj->stage->label_ar ?? optional($certificate->subject->stage)->name_ar ?? 'الثانوية العامة (التوجيهي)';
        $stageNameEn = 'General Secondary Education (Tawjihi)';
        $subjectNameAr = $certificate->subject->name_ar ?? $certificate->subject->name ?? 'شهادة إتمام وتفوق عامة';
        $subjectNameEn = $certificate->subject->name ?? $certificate->subject->name_ar ?? 'General Academic Excellence';
        $issueDate = $certificate->created_at ? $certificate->created_at->format('Y/m/d') : date('Y/m/d');
        $issueDateEn = $certificate->created_at ? $certificate->created_at->format('F d, Y') : date('F d, Y');
        $finalGrade = (float) ($certificate->final_grade ?? 90);
    @endphp
    <title>الشهادة الأكاديمية الملكية المعتمدة | {{ $studentNameAr }}</title>

    <!-- استدعاء خطوط الشهادات الملكية العالمية الرسمية -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;600;700;800;900&family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=Aref+Ruqaa:wght@400;700&family=Cinzel+Decorative:wght@700;900&family=Cinzel:wght@600;700;800;900&family=Playfair+Display:ital,wght@0,600;0,700;0,800;0,900;1,600&family=Pinyon+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        :root {
            --gold-light: #fef08a;
            --gold-mid: #d4af37;
            --gold-dark: #9a7217;
            --gold-metallic: linear-gradient(135deg, #bf953f 0%, #fcf6ba 25%, #b38728 50%, #fbf5b7 75%, #aa771c 100%);
            --pal-green: #007a3d;
            --pal-red: #e4312b;
            --pal-black: #0f172a;
            --emerald-deep: #064e3b;
            --bg-parchment: #fffdf9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #0b0f19;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 24px 15px 60px;
            font-family: 'Alexandria', sans-serif;
            color: #1e293b;
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(212, 175, 55, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(0, 122, 61, 0.08) 0%, transparent 40%);
        }

        /* شريط الأدوات العلوي الفاخر */
        .actions-toolbar {
            width: 100%;
            max-width: 1100px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(16px);
            padding: 14px 24px;
            border-radius: 20px;
            border: 1px solid rgba(212, 175, 55, 0.35);
            box-shadow: 0 10px 35px rgba(0,0,0,0.5);
            flex-wrap: wrap;
            gap: 15px;
        }

        .brand-tag {
            color: #ffffff;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1rem;
        }

        .brand-tag .crest-icon {
            color: var(--gold-mid);
            font-size: 1.4rem;
            filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.7));
        }

        .btn-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-tool {
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            transition: all 0.25s ease;
        }

        .btn-lang {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid rgba(212, 175, 55, 0.4);
        }
        .btn-lang:hover {
            background: var(--gold-mid);
            color: #000;
        }

        .btn-print {
            background: #ffffff;
            color: #0f172a;
        }
        .btn-print:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
        }

        .btn-download {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
        }
        .btn-back:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }

        /* حاوية الشهادة الملكية الرئيسية (A4 Landscape) */
        .certificate-outer-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            overflow-x: auto;
            padding-bottom: 20px;
        }

        .certificate-sheet {
            width: 1060px;
            height: 750px;
            min-width: 1060px;
            background: var(--bg-parchment);
            position: relative;
            padding: 30px;
            box-shadow: 
                0 25px 60px rgba(0, 0, 0, 0.45),
                0 0 0 1px rgba(212, 175, 55, 0.3);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            background-image: 
                radial-gradient(circle at center, #ffffff 0%, #fffbf2 80%, #fbf5e6 100%);
        }

        /* خلفية الأمان والعلامة المائية Guilloche */
        .security-guilloche-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: 
                radial-gradient(circle at 50% 50%, rgba(212, 175, 55, 0.04) 2px, transparent 2px),
                radial-gradient(circle at 50% 50%, rgba(0, 122, 61, 0.02) 2px, transparent 2px);
            background-size: 28px 28px, 56px 56px;
            pointer-events: none;
            opacity: 0.85;
        }

        /* الإطار الملكي المزدوج المذهب */
        .royal-frame-border {
            position: absolute;
            top: 15px; left: 15px; right: 15px; bottom: 15px;
            border: 3px solid #d4af37;
            pointer-events: none;
            border-radius: 6px;
        }

        .royal-inner-border {
            position: absolute;
            top: 22px; left: 22px; right: 22px; bottom: 22px;
            border: 1px solid #c59b27;
            outline: 1px dashed rgba(197, 155, 39, 0.4);
            outline-offset: -5px;
            pointer-events: none;
            border-radius: 4px;
        }

        /* زوايا الزخرفة الملكية المذهبة */
        .corner-ornament {
            position: absolute;
            width: 50px;
            height: 50px;
            border: 3px solid #b38728;
            pointer-events: none;
            z-index: 5;
        }
        .corner-tl { top: 22px; left: 22px; border-right: none; border-bottom: none; }
        .corner-tr { top: 22px; right: 22px; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 22px; left: 22px; border-right: none; border-top: none; }
        .corner-br { bottom: 22px; right: 22px; border-left: none; border-top: none; }

        /* ترويسة الشهادة الرسمية */
        .cert-header {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px 0;
            border-bottom: 2px solid rgba(212, 175, 55, 0.25);
            padding-bottom: 15px;
        }

        .header-col-side {
            width: 260px;
            font-size: 0.85rem;
            color: #475569;
            line-height: 1.5;
        }

        .header-col-center {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .official-logo-box {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border: 2px solid var(--gold-mid);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold-mid);
            font-size: 2rem;
            margin-bottom: 8px;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .academy-title-main {
            font-family: 'Amiri', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .academy-title-sub {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* عنوان الشهادة الملكي */
        .cert-title-section {
            text-align: center;
            margin: 15px 0 10px;
            position: relative;
            z-index: 10;
        }

        .cert-bismillah {
            font-family: 'Aref Ruqaa', serif;
            font-size: 1.25rem;
            color: #9a7217;
            margin-bottom: 6px;
        }

        .cert-grand-title {
            font-family: 'Amiri', serif;
            font-size: 2.3rem;
            font-weight: 700;
            color: #0f172a;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .cert-grand-title-en {
            font-family: 'Cinzel Decorative', serif;
            font-size: 1.8rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 2px;
            margin: 0;
            display: none;
        }

        .cert-diploma-ribbon {
            display: inline-block;
            height: 3px;
            width: 220px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
            margin-top: 6px;
        }

        /* جسم الشهادة والنصوص الأكاديمية */
        .cert-body-section {
            text-align: center;
            padding: 0 45px;
            position: relative;
            z-index: 10;
        }

        .cert-intro-text {
            font-family: 'Amiri', serif;
            font-size: 1.15rem;
            color: #475569;
            margin-bottom: 8px;
        }

        .student-name-display {
            font-family: 'Amiri', serif;
            font-size: 2.4rem;
            font-weight: 700;
            color: #007a3d;
            margin: 6px 0;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 2px rgba(0, 122, 61, 0.15);
        }

        .student-name-display-en {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: #007a3d;
            margin: 6px 0;
            display: none;
        }

        .student-meta-nid {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .cert-statement-text {
            font-family: 'Amiri', serif;
            font-size: 1.18rem;
            color: #1e293b;
            line-height: 1.8;
            max-width: 900px;
            margin: 0 auto 15px;
        }

        .subject-pill-highlight {
            display: inline-block;
            background: rgba(212, 175, 55, 0.15);
            color: #854d0e;
            padding: 2px 14px;
            border-radius: 6px;
            font-weight: 800;
            border-bottom: 2px solid #d4af37;
        }

        /* وسام التقدير والمعدل */
        .grade-honors-ribbon {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            background: #ffffff;
            border: 2px solid #d4af37;
            padding: 6px 25px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.15);
            margin-top: 5px;
        }

        .honor-title-badge {
            font-size: 0.95rem;
            font-weight: 800;
            color: #065f46;
        }

        .official-score-number {
            font-size: 1.3rem;
            font-weight: 900;
            color: #b45309;
            font-family: 'Alexandria', sans-serif;
        }

        .stars-gold {
            color: #f59e0b;
            font-size: 0.95rem;
            letter-spacing: 2px;
        }

        /* القسم السفلي: الأختام والتواقيع وكود التحقق */
        .cert-footer-section {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 40px 10px;
        }

        /* اليمين: كود التحقق الرقمي المعتمد QR */
        .footer-qr-side {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 280px;
        }

        .qr-frame-box {
            width: 76px;
            height: 76px;
            background: white;
            padding: 5px;
            border: 1.5px solid #d4af37;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
        }

        .qr-frame-box img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .qr-meta-box {
            font-size: 0.74rem;
            color: #64748b;
            line-height: 1.5;
        }

        .serial-code-pill {
            font-family: monospace;
            font-size: 0.8rem;
            font-weight: 800;
            color: #4f46e5;
            background: #eef2ff;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin: 2px 0;
        }

        /* الوسط: الختم الذهبي الملكي النافر ثلاثي الأبعاد */
        .footer-seal-center {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .golden-3d-seal {
            width: 105px;
            height: 105px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, #fff5cc 0%, #d4af37 50%, #996515 90%);
            box-shadow: 
                0 6px 18px rgba(154, 114, 23, 0.4),
                inset 0 2px 5px rgba(255, 255, 255, 0.8),
                inset 0 -2px 5px rgba(0, 0, 0, 0.3);
            border: 3px dashed #ffffff;
            outline: 2px solid #b38728;
            outline-offset: -6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #451a03;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .seal-crest-icon {
            font-size: 1.5rem;
            margin-bottom: 2px;
            color: #78350f;
            filter: drop-shadow(0 1px 1px rgba(255,255,255,0.6));
        }

        .seal-arabic-txt {
            font-family: 'Amiri', serif;
            font-weight: 700;
            font-size: 0.72rem;
            line-height: 1;
            color: #451a03;
            text-shadow: 0 1px 1px rgba(255,255,255,0.6);
        }

        .seal-english-txt {
            font-family: 'Cinzel', serif;
            font-size: 0.55rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #78350f;
            margin-top: 2px;
        }

        .seal-ribbon-tails {
            position: absolute;
            top: 75px;
            display: flex;
            gap: 8px;
            z-index: 1;
        }

        .seal-tail {
            width: 22px;
            height: 48px;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .tail-green { background: #007a3d; }
        .tail-red { background: #e4312b; }

        /* اليسار: التوقيع الحبري الملكي */
        .footer-sig-side {
            width: 280px;
            text-align: center;
        }

        .sig-director-title {
            font-size: 0.86rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .cursive-signature-art {
            font-family: 'Pinyon Script', cursive;
            font-size: 2.8rem;
            color: #0f172a;
            line-height: 0.9;
            transform: rotate(-3deg);
            filter: drop-shadow(0 1px 1px rgba(15, 23, 42, 0.2));
            user-select: none;
        }

        .sig-subline {
            font-size: 0.74rem;
            color: #64748b;
            border-top: 1px solid #d4af37;
            padding-top: 4px;
            width: 200px;
            margin: 4px auto 0;
            font-weight: 700;
        }

        /* وضع الطباعة التلقائي القياسي A4 Landscape */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .actions-toolbar { display: none !important; }
            .certificate-outer-wrapper { padding: 0 !important; }
            .certificate-sheet {
                box-shadow: none !important;
                border: none !important;
                width: 100vw !important;
                height: 100vh !important;
                min-width: 100% !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- شريط الأدوات العلوي الفاخر -->
    <div class="actions-toolbar">
        <div class="brand-tag">
            <i class="fa-solid fa-crown crest-icon"></i>
            <div>
                <span>الشهادة الأكاديمية الملكية المعتمدة</span>
                <span style="display: block; font-size: 0.72rem; color: #94a3b8; font-weight: 600;">منظومة التخرج والاعتماد الرسمي لطلبة الثانوية العامة</span>
            </div>
        </div>

        <div class="btn-group">
            <!-- زر التبديل بين اللغتين العربية والإنجليزية -->
            <button type="button" onclick="toggleLanguage()" id="btnLangToggle" class="btn-tool btn-lang" title="Switch Language / تبديل لغة الشهادة">
                <i class="fa-solid fa-globe"></i>
                <span id="langBtnText">🇬🇧 English Version</span>
            </button>

            <!-- زر الطباعة -->
            <button type="button" onclick="window.print()" class="btn-tool btn-print">
                <i class="fa-solid fa-print"></i>
                <span id="printBtnText">طباعة الشهادة</span>
            </button>

            <!-- زر التحميل كصورة عالية الدقة HD -->
            <button type="button" onclick="downloadCertificateHD()" id="btnDownload" class="btn-tool btn-download">
                <i class="fa-solid fa-file-arrow-down"></i>
                <span id="downloadBtnText">تحميل نسخة HD</span>
            </button>

            <!-- زر العودة -->
            <a href="{{ route('student.achievements') }}" class="btn-tool btn-back">
                <i class="fa-solid fa-arrow-left"></i>
                <span>عودة للإنجازات</span>
            </a>
        </div>
    </div>

    <!-- لوح الشهادة الملكية الرسمية -->
    <div class="certificate-outer-wrapper">
        <div class="certificate-sheet" id="certificateCanvas">

            <!-- خلفيات الأمان والزخارف الملكية المذهبة -->
            <div class="security-guilloche-bg"></div>
            <div class="royal-frame-border"></div>
            <div class="royal-inner-border"></div>
            <div class="corner-ornament corner-tl"></div>
            <div class="corner-ornament corner-tr"></div>
            <div class="corner-ornament corner-bl"></div>
            <div class="corner-ornament corner-br"></div>

            <!-- 1. الترويسة الرسمية للشهادة -->
            <div class="cert-header">
                <div class="header-col-side" style="text-align: right;" id="headerSideAr">
                    <strong>دَوْلَةُ فِلَسْطِينَ</strong><br>
                    مَنْظُومَةُ التَّعْلِيمِ وَالتَّوْجِيهِيِّ الْمُعْتَمَدَةُ<br>
                    <span style="color: #9a7217; font-weight: 700;">سِجِلُّ التَّفَوُّقِ الْأَكَادِيمِيِّ</span>
                </div>
                <div class="header-col-side" style="text-align: left; display: none;" id="headerSideEn">
                    <strong>STATE OF PALESTINE</strong><br>
                    Official Tawjihi Education Board<br>
                    <span style="color: #9a7217; font-weight: 700;">Academic Honors Registry</span>
                </div>

                <!-- شعار المنصة الرسمي -->
                <div class="header-col-center">
                    <div class="official-logo-box">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h2 class="academy-title-main" id="brandTitle">مَنَارَةُ التَّوْجِيهِيِّ التَّعْلِيمِيَّةُ</h2>
                    <span class="academy-title-sub" id="brandSub">MENARET AL-TAWJIHI ACADEMY</span>
                </div>

                <div class="header-col-side" style="text-align: left;" id="headerMetaAr">
                    رَقْمُ التَّوْثِيقِ: <strong style="color: #4f46e5;">{{ $certificate->certificate_code }}</strong><br>
                    تَارِيخُ الصُّدُورِ: <strong>{{ $issueDate }} م</strong><br>
                    الْحَالَةُ: <span style="color: #059669; font-weight: 800;">مُعْتَمَدٌ رَسْمِيّاً ✅</span>
                </div>
                <div class="header-col-side" style="text-align: right; display: none;" id="headerMetaEn">
                    Doc Serial: <strong style="color: #4f46e5;">{{ $certificate->certificate_code }}</strong><br>
                    Date of Issue: <strong>{{ $issueDateEn }}</strong><br>
                    Accreditation: <span style="color: #059669; font-weight: 800;">Officially Certified ✅</span>
                </div>
            </div>

            <!-- 2. عنوان الشهادة الملكي -->
            <div class="cert-title-section">
                <div class="cert-bismillah" id="bismillahTxt">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
                <h1 class="cert-grand-title" id="titleAr">شَهَادَةُ تَفَوُّقٍ وَاجْتِيَازٍ أَكَادِيمِيّ</h1>
                <h1 class="cert-grand-title-en" id="titleEn">CERTIFICATE OF ACADEMIC EXCELLENCE & HONORS</h1>
                <div class="cert-diploma-ribbon"></div>
            </div>

            <!-- 3. جسم الشهادة والنصوص والدرجات -->
            <div class="cert-body-section">
                <p class="cert-intro-text" id="introTxtAr">
                    تَشْهَدُ إِدَارَةُ وَهَيْئَةُ التَّدْرِيسِ فِي مَنَارَةِ التَّوْجِيهِيِّ بِأَنَّ الطَّالِبَ(ةَ):
                </p>
                <p class="cert-intro-text" id="introTxtEn" style="display: none; font-family: 'Playfair Display', serif; font-size: 1.1rem;">
                    The Academic Board of Menaret Al-Tawjihi Academy hereby proudly certifies that:
                </p>

                <!-- اسم الطالب البارز -->
                <div class="student-name-display" id="studentNameDisplayAr">{{ $studentNameAr }}</div>
                <div class="student-name-display-en" id="studentNameDisplayEn">{{ $studentNameEn }}</div>

                <!-- رقم الهوية الوطنية -->
                @if($studentNid)
                    <div class="student-meta-nid" id="nidTxtAr">رَقْمُ الْهُوِيَّةِ الْوَطَنِيَّةِ: <code>{{ $studentNid }}</code></div>
                    <div class="student-meta-nid" id="nidTxtEn" style="display: none;">National Identification No: <code>{{ $studentNid }}</code></div>
                @endif

                <!-- متن شهادة التخرج والمساق -->
                <p class="cert-statement-text" id="statementTxtAr">
                    قَدِ اجْتَازَ(تْ) بِنَجَاحٍ بَاهِرٍ وَاقْتِدَارٍ رَفِيعٍ كَافَّةَ كِفَايَاتِ وَاخْتِبَارَاتِ مَسَاقِ:
                    <span class="subject-pill-highlight">{{ $subjectNameAr }}</span>
                    لِفَرْعِ: <strong>({{ $stageNameAr }})</strong>
                    وَفْقاً لِمَعَايِيرِ وَمُخْرَجَاتِ التَّعَلُّمِ الْمُعْتَمَدَةِ فِي امْتِحَانَاتِ الثَّانَوِيَّةِ الْعَامَّةِ الْفِلَسْطِينِيَّةِ.
                </p>
                <p class="cert-statement-text" id="statementTxtEn" style="display: none; font-family: 'Playfair Display', serif; font-size: 1.08rem; line-height: 1.7;">
                    has successfully accomplished with exemplary distinction all requirements, curricular standards, and ministerial evaluations for the course of:
                    <span class="subject-pill-highlight">{{ $subjectNameEn }}</span>
                    Academic Division: <strong>{{ $stageNameEn }}</strong>
                    in full accordance with the official accreditation criteria of the General Secondary Education Board.
                </p>

                <!-- وسام المعدل والتقدير -->
                <div class="grade-honors-ribbon">
                    <span class="stars-gold">★★★★★</span>
                    <span class="honor-title-badge" id="honorBadgeAr">
                        بِتَقْدِيرِ: امْتِيَازٌ مَعَ مَرْتَبَةِ الشَّرَفِ
                    </span>
                    <span class="honor-title-badge" id="honorBadgeEn" style="display: none;">
                        Classification: High Honors with Distinction
                    </span>
                    <span class="official-score-number" id="gradeNum">
                        ({{ $finalGrade }}%)
                    </span>
                    <span class="stars-gold">★★★★★</span>
                </div>
            </div>

            <!-- 4. تذييل الشهادة: الختم الذهبي + التوقيع + رمز التحقق QR -->
            <div class="cert-footer-section">

                <!-- اليمين: رمز التحقق الرقمي المعتمد QR -->
                <div class="footer-qr-side">
                    <div class="qr-frame-box">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($verificationUrl) }}" 
                             alt="QR Verification">
                    </div>
                    <div class="qr-meta-box">
                        <span id="qrLabelAr">الرَّمْزُ الرَّقْمِيُّ الْمُعْتَمَدُ:</span>
                        <span id="qrLabelEn" style="display: none;">Digital Verification ID:</span><br>
                        <span class="serial-code-pill">{{ $certificate->certificate_code }}</span><br>
                        <span id="verifiedBadgeAr" style="color: #059669; font-weight: 700;">
                            <i class="fa-solid fa-shield-halved"></i> وَثِيقَةٌ مُعْتَمَدَةٌ وَمُوَثَّقَةٌ
                        </span>
                        <span id="verifiedBadgeEn" style="color: #059669; font-weight: 700; display: none;">
                            <i class="fa-solid fa-shield-halved"></i> Digitally Signed Document
                        </span>
                    </div>
                </div>

                <!-- الوسط: الختم الذهبي الملكي النافر 3D -->
                <div class="footer-seal-center">
                    <div class="golden-3d-seal">
                        <i class="fa-solid fa-stamp seal-crest-icon"></i>
                        <span class="seal-arabic-txt">مُعْتَمَدٌ رَسْمِيّاً</span>
                        <span class="seal-english-txt">OFFICIAL SEAL</span>
                    </div>
                    <div class="seal-ribbon-tails">
                        <div class="seal-tail tail-green"></div>
                        <div class="seal-tail tail-red"></div>
                    </div>
                </div>

                <!-- اليسار: التوقيع الرسمي لإدارة المنصة والمشرف العام -->
                <div class="footer-sig-side">
                    <div class="sig-director-title" id="sigTitleAr">الْمُشْرِفُ الْعَامُّ وَإِدَارَةُ الْمَنَصَّةِ</div>
                    <div class="sig-director-title" id="sigTitleEn" style="display: none;">General Director & Academic Board</div>
                    
                    <!-- توقيع بالريشة الحبرية -->
                    <div class="cursive-signature-art">
                        Ahmed Shamali
                    </div>
                    
                    <div class="sig-subline" id="sigSubAr">مَنَارَةُ التَّوْجِيهِيِّ - فِلَسْطِين</div>
                    <div class="sig-subline" id="sigSubEn" style="display: none;">Menaret Al-Tawjihi - Palestine</div>
                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let isEnglish = false;

        // تبديل لغة الشهادة فورياً بين العربية والإنجليزية
        function toggleLanguage() {
            isEnglish = !isEnglish;
            const htmlTag = document.getElementById('certHtml');

            if (isEnglish) {
                htmlTag.setAttribute('dir', 'ltr');
                htmlTag.setAttribute('lang', 'en');

                // التبديل للغة الإنجليزية
                document.getElementById('headerSideAr').style.display = 'none';
                document.getElementById('headerSideEn').style.display = 'block';
                document.getElementById('headerMetaAr').style.display = 'none';
                document.getElementById('headerMetaEn').style.display = 'block';

                document.getElementById('bismillahTxt').style.display = 'none';
                document.getElementById('titleAr').style.display = 'none';
                document.getElementById('titleEn').style.display = 'block';

                document.getElementById('introTxtAr').style.display = 'none';
                document.getElementById('introTxtEn').style.display = 'block';

                document.getElementById('studentNameDisplayAr').style.display = 'none';
                document.getElementById('studentNameDisplayEn').style.display = 'block';

                const nidAr = document.getElementById('nidTxtAr');
                const nidEn = document.getElementById('nidTxtEn');
                if (nidAr) nidAr.style.display = 'none';
                if (nidEn) nidEn.style.display = 'block';

                document.getElementById('statementTxtAr').style.display = 'none';
                document.getElementById('statementTxtEn').style.display = 'block';

                document.getElementById('honorBadgeAr').style.display = 'none';
                document.getElementById('honorBadgeEn').style.display = 'inline';

                document.getElementById('qrLabelAr').style.display = 'none';
                document.getElementById('qrLabelEn').style.display = 'inline';
                document.getElementById('verifiedBadgeAr').style.display = 'none';
                document.getElementById('verifiedBadgeEn').style.display = 'inline';

                document.getElementById('sigTitleAr').style.display = 'none';
                document.getElementById('sigTitleEn').style.display = 'block';
                document.getElementById('sigSubAr').style.display = 'none';
                document.getElementById('sigSubEn').style.display = 'block';

                document.getElementById('langBtnText').textContent = '🇸🇦 النسخة العربية';
                document.getElementById('printBtnText').textContent = 'Print Diploma';
                document.getElementById('downloadBtnText').textContent = 'Download HD';
            } else {
                htmlTag.setAttribute('dir', 'rtl');
                htmlTag.setAttribute('lang', 'ar');

                // العودة للنسخة العربية
                document.getElementById('headerSideAr').style.display = 'block';
                document.getElementById('headerSideEn').style.display = 'none';
                document.getElementById('headerMetaAr').style.display = 'block';
                document.getElementById('headerMetaEn').style.display = 'none';

                document.getElementById('bismillahTxt').style.display = 'block';
                document.getElementById('titleAr').style.display = 'block';
                document.getElementById('titleEn').style.display = 'none';

                document.getElementById('introTxtAr').style.display = 'block';
                document.getElementById('introTxtEn').style.display = 'none';

                document.getElementById('studentNameDisplayAr').style.display = 'block';
                document.getElementById('studentNameDisplayEn').style.display = 'none';

                const nidAr = document.getElementById('nidTxtAr');
                const nidEn = document.getElementById('nidTxtEn');
                if (nidAr) nidAr.style.display = 'block';
                if (nidEn) nidEn.style.display = 'none';

                document.getElementById('statementTxtAr').style.display = 'block';
                document.getElementById('statementTxtEn').style.display = 'none';

                document.getElementById('honorBadgeAr').style.display = 'inline';
                document.getElementById('honorBadgeEn').style.display = 'none';

                document.getElementById('qrLabelAr').style.display = 'inline';
                document.getElementById('qrLabelEn').style.display = 'none';
                document.getElementById('verifiedBadgeAr').style.display = 'inline';
                document.getElementById('verifiedBadgeEn').style.display = 'none';

                document.getElementById('sigTitleAr').style.display = 'block';
                document.getElementById('sigTitleEn').style.display = 'none';
                document.getElementById('sigSubAr').style.display = 'block';
                document.getElementById('sigSubEn').style.display = 'none';

                document.getElementById('langBtnText').textContent = '🇬🇧 English Version';
                document.getElementById('printBtnText').textContent = 'طباعة الشهادة';
                document.getElementById('downloadBtnText').textContent = 'تحميل نسخة HD';
            }
        }

        // تحميل الشهادة بجودة استوديو فائقة HD 3x
        function downloadCertificateHD() {
            const btn = document.getElementById('btnDownload');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التوليد بدقة فائقة...';

            const certElement = document.getElementById('certificateCanvas');

            html2canvas(certElement, {
                scale: 3,
                useCORS: true,
                backgroundColor: null,
                logging: false,
            }).then(canvas => {
                const link = document.createElement('a');
                const langSuffix = isEnglish ? 'English' : 'Arabic';
                link.download = `Tawjihi-Royal-Certificate-{{ $certificate->certificate_code }}-${langSuffix}.png`;
                link.href = canvas.toDataURL('image/png', 1.0);
                link.click();

                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-file-arrow-down"></i> <span id="downloadBtnText">' + (isEnglish ? 'Download HD' : 'تحميل نسخة HD') + '</span>';

                Swal.fire({
                    icon: 'success',
                    title: isEnglish ? 'Certificate Downloaded Successfully! 🏆' : 'تم تحميل الشهادة بنجاح! 🏆',
                    text: isEnglish ? 'Your official high-resolution diploma is saved.' : 'تم تصدير الشهادة الرسمية بأعلى دقة طباعة واحترافية.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-file-arrow-down"></i> <span id="downloadBtnText">تحميل نسخة HD</span>';
                Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر تصدير الصورة، يمكنك استخدام زر الطباعة المباشرة.' });
            });
        }
    </script>
</body>
</html>
