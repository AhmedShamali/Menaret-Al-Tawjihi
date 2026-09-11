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
    <title>الشهادة الأكاديمية المعتمدة | {{ $studentNameAr }}</title>

    <!-- Google Fonts: Alexandria, Amiri & Tajawal (Authentic Academic Typography) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;600;700;800;900&family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        :root {
            --bg-page: #f1f5f9;
            --cert-bg: #fffdf9;
            --cert-border: #1e3a8a;
            --cert-gold: #b45309;
            --text-dark: #0f172a;
            --text-muted: #475569;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Alexandria', 'Tajawal', sans-serif;
        }

        body {
            background: var(--bg-page);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px 60px;
            color: var(--text-dark);
        }

        /* شريط الأدوات العلوي */
        .actions-toolbar {
            width: 100%;
            max-width: 1060px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 14px 22px;
            border-radius: 14px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            flex-wrap: wrap;
            gap: 14px;
        }

        .brand-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 1rem;
            color: #1e293b;
        }

        .brand-title i {
            color: #1d4ed8;
            font-size: 1.2rem;
        }

        .buttons-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 0.86rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-print {
            background: #1d4ed8;
            color: #ffffff;
            border-color: #1d4ed8;
        }
        .btn-print:hover {
            background: #1e40af;
        }

        .btn-download {
            background: #059669;
            color: #ffffff;
            border-color: #059669;
        }
        .btn-download:hover {
            background: #047857;
        }

        .btn-back {
            background: #f8fafc;
            color: #475569;
            border-color: #cbd5e1;
        }
        .btn-back:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        /* الحاوية الخارجية للشهادة */
        .cert-outer-frame {
            width: 100%;
            display: flex;
            justify-content: center;
            overflow-x: auto;
            padding-bottom: 20px;
        }

        /* ورقة الشهادة الرسمية قياس A4 أفقي */
        .cert-sheet {
            width: 1040px;
            height: 735px;
            min-width: 1040px;
            background: var(--cert-bg);
            border: 12px solid #ffffff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border-radius: 4px;
            position: relative;
            padding: 38px 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        /* الإطار الكلاسيكي المزدوج للشهادات الأكاديمية */
        .cert-double-border {
            position: absolute;
            top: 14px;
            left: 14px;
            right: 14px;
            bottom: 14px;
            border: 3px double #1e3a8a;
            pointer-events: none;
        }

        .cert-inner-line {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 1px solid #d4af37;
            pointer-events: none;
        }

        /* زوايا الزخرفة الهندسية الكلاسيكية */
        .corner-ornament {
            position: absolute;
            width: 32px;
            height: 32px;
            border-style: solid;
            border-color: #b45309;
            pointer-events: none;
        }
        .corner-tl { top: 22px; left: 22px; border-width: 3px 0 0 3px; }
        .corner-tr { top: 22px; right: 22px; border-width: 3px 3px 0 0; }
        .corner-bl { bottom: 22px; left: 22px; border-width: 0 0 3px 3px; }
        .corner-br { bottom: 22px; right: 22px; border-width: 0 3px 3px 0; }

        /* ترويسة الشهادة الرسمية */
        .cert-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 16px;
            margin-bottom: 12px;
        }

        .header-col-ar {
            text-align: right;
        }
        .header-col-ar h3 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #1e3a8a;
            margin-bottom: 2px;
        }
        .header-col-ar p {
            font-size: 0.82rem;
            color: #64748b;
            font-weight: 600;
            margin: 0;
        }

        .header-emblem {
            text-align: center;
        }
        .emblem-icon {
            width: 54px;
            height: 54px;
            margin: 0 auto 4px;
            background: #eff6ff;
            border: 2px solid #bfdbfe;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #1d4ed8;
            font-size: 1.5rem;
        }
        .emblem-tag {
            font-size: 0.72rem;
            font-weight: 700;
            color: #1e3a8a;
            letter-spacing: 0.5px;
        }

        .header-col-en {
            text-align: left;
            direction: ltr;
        }
        .header-col-en h3 {
            font-size: 0.95rem;
            font-weight: 800;
            color: #1e3a8a;
            margin-bottom: 2px;
            font-family: 'Alexandria', sans-serif;
        }
        .header-col-en p {
            font-size: 0.76rem;
            color: #64748b;
            font-weight: 600;
            margin: 0;
        }

        /* عنوان الشهادة الرئيسي */
        .cert-main-title {
            text-align: center;
            margin: 10px 0 16px;
        }
        .cert-main-title h1 {
            font-family: 'Amiri', serif;
            font-size: 2.3rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 1px;
            margin: 0 0 4px;
        }
        .cert-sub-title-en {
            font-size: 0.84rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-family: 'Alexandria', sans-serif;
        }

        /* جسم الشهادة وبيانات الطالب */
        .cert-body-text {
            text-align: center;
            max-width: 820px;
            margin: 0 auto;
            line-height: 1.9;
        }

        .cert-pre-text {
            font-size: 1.05rem;
            color: #334155;
            font-weight: 500;
        }

        .student-name-box {
            margin: 10px auto 12px;
            padding: 6px 28px;
            display: inline-block;
            border-bottom: 2px solid #b45309;
        }
        .student-name-box .name-ar {
            font-family: 'Amiri', serif;
            font-size: 2.1rem;
            font-weight: 700;
            color: #1e3a8a;
            display: block;
            line-height: 1.2;
        }
        .student-name-box .name-en {
            font-size: 0.92rem;
            color: #64748b;
            font-weight: 600;
            font-family: 'Alexandria', sans-serif;
        }

        .cert-detail-text {
            font-size: 0.98rem;
            color: #334155;
            line-height: 1.8;
            margin-bottom: 12px;
        }

        .cert-meta-pills {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin: 12px 0 18px;
            flex-wrap: wrap;
        }

        .cert-pill {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cert-pill.grade-pill {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
            font-size: 0.96rem;
        }

        /* ذيل الشهادة: التواقيع والختم وQR Code */
        .cert-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
        }

        .sig-block {
            text-align: center;
            min-width: 180px;
        }
        .sig-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 24px;
        }
        .sig-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f172a;
            border-top: 1px dashed #94a3b8;
            padding-top: 6px;
            display: block;
        }

        .cert-seal-box {
            text-align: center;
        }
        .official-seal {
            width: 78px;
            height: 78px;
            margin: 0 auto 4px;
            border-radius: 50%;
            border: 3px double #b45309;
            background: #fffbeb;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #92400e;
            font-size: 0.64rem;
            font-weight: 800;
            box-shadow: 0 2px 8px rgba(180, 83, 9, 0.15);
        }
        .official-seal i {
            font-size: 1.2rem;
            margin-bottom: 2px;
        }

        .qr-verify-col {
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: right;
        }
        .qr-box img {
            width: 62px;
            height: 62px;
            border: 1px solid #cbd5e1;
            padding: 3px;
            background: #ffffff;
            border-radius: 6px;
        }
        .qr-info-text small {
            font-size: 0.68rem;
            color: #64748b;
            display: block;
        }
        .qr-info-text code {
            font-family: monospace;
            font-weight: 700;
            font-size: 0.74rem;
            color: #0f172a;
        }

        /* تنسيقات الطباعة الدقيقة A4 Landscape */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .actions-toolbar {
                display: none !important;
            }
            .cert-outer-frame {
                overflow: visible !important;
                padding: 0 !important;
            }
            .cert-sheet {
                width: 100% !important;
                height: 100vh !important;
                min-width: unset !important;
                border: none !important;
                box-shadow: none !important;
                page-break-after: avoid;
                page-break-inside: avoid;
            }
            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- شريط الأدوات -->
    <div class="actions-toolbar">
        <div class="brand-title">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>منظومة الشهادات والاعتماد الأكاديمي الرسمي 🇵🇸</span>
        </div>

        <div class="buttons-group">
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fa-solid fa-print"></i> طباعة الوثيقة (A4 / PDF)
            </button>
            <button onclick="downloadAsImage()" class="btn-action btn-download" id="btnDownloadImg">
                <i class="fa-regular fa-image"></i> حفظ كصورة
            </button>
            <a href="{{ route('student.achievements') }}" class="btn-action btn-back">
                <i class="fa-solid fa-arrow-right"></i> لوحة الإنجازات
            </a>
        </div>
    </div>

    <!-- حاوية ورقة الشهادة الرسمية -->
    <div class="cert-outer-frame">
        <div class="cert-sheet" id="certificateSheet">
            <!-- الإطارات الكلاسيكية -->
            <div class="cert-double-border"></div>
            <div class="cert-inner-line"></div>
            <div class="corner-ornament corner-tl"></div>
            <div class="corner-ornament corner-tr"></div>
            <div class="corner-ornament corner-bl"></div>
            <div class="corner-ornament corner-br"></div>

            <!-- الترويسة -->
            <div class="cert-header">
                <div class="header-col-ar">
                    <h3>دولة فلسطين 🇵🇸</h3>
                    <p>منظومة منارة التوجيهي للتعليم الأكاديمي</p>
                    <p style="font-size: 0.72rem; color: #94a3b8;">إشراف ومتابعة الثانوية العامة</p>
                </div>

                <div class="header-emblem">
                    <div class="emblem-icon">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <span class="emblem-tag">وثيقة تخرج رسمية</span>
                </div>

                <div class="header-col-en">
                    <h3>STATE OF PALESTINE</h3>
                    <p>Menaret Al-Tawjihi Educational Platform</p>
                    <p style="font-size: 0.72rem; color: #94a3b8;">Official Academic Certification</p>
                </div>
            </div>

            <!-- العنوان الرئيسي -->
            <div class="cert-main-title">
                <h1>شهادة إتمام وتفوق أكاديمي</h1>
                <div class="cert-sub-title-en">Certificate of Academic Excellence</div>
            </div>

            <!-- نص الشهادة -->
            <div class="cert-body-text">
                <div class="cert-pre-text">
                    يشهد المشرف العام وإدارة المنصة التعليمية بأن الطالبـ/ـة:
                </div>

                <div class="student-name-box">
                    <span class="name-ar">{{ $studentNameAr }}</span>
                    <span class="name-en">{{ $studentNameEn }}</span>
                </div>

                <div class="cert-detail-text">
                    قد أتمـ/ـت بنجاح واقتدار كافة متطلبات المنهاج والاختبارات الأكاديمية المقررة لمساق:
                    <strong style="color: #1e3a8a; font-weight: 800;">{{ $subjectNameAr }}</strong>
                    ضمن مرحلة <strong style="color: #0f172a;">{{ $stageNameAr }}</strong> للعام الدراسي {{ date('Y') }}.
                </div>

                <div class="cert-meta-pills">
                    <div class="cert-pill grade-pill">
                        <i class="fa-solid fa-star" style="color: #eab308;"></i>
                        <span>المعدل المعتمد: {{ number_format($finalGrade, 1) }}%</span>
                    </div>
                    <div class="cert-pill">
                        <i class="fa-solid fa-medal" style="color: #b45309;"></i>
                        <span>التقدير: {{ $finalGrade >= 90 ? 'ممتاز مع مرتبة الشرف' : ($finalGrade >= 80 ? 'جيد جداً مرتفع' : ($finalGrade >= 70 ? 'جيد' : 'ناجح ومجتاز')) }}</span>
                    </div>
                    <div class="cert-pill">
                        <i class="fa-regular fa-calendar-check" style="color: #059669;"></i>
                        <span>تاريخ الإصدار: {{ $issueDate }}</span>
                    </div>
                </div>
            </div>

            <!-- التوقيعات والختم وQR -->
            <div class="cert-footer">
                <div class="sig-block">
                    <div class="sig-title">معلم المساق الأكاديمي</div>
                    <span class="sig-name">{{ $certificate->subject?->teacher_display_name ?? 'أستاذ المادة المعتمد' }}</span>
                </div>

                <div class="cert-seal-box">
                    <div class="official-seal">
                        <i class="fa-solid fa-stamp"></i>
                        <span>معتمد رسميّاً</span>
                        <span>OFFICIAL</span>
                    </div>
                    <span style="font-size: 0.65rem; color: #94a3b8; font-weight: 600;">ختم التوثيق الأكاديمي</span>
                </div>

                <div class="qr-verify-col">
                    <div class="qr-box">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($verificationUrl) }}" alt="QR Code">
                    </div>
                    <div class="qr-info-text">
                        <small>رمز الوثيقة المعتمد:</small>
                        <code>{{ $certificate->certificate_code }}</code>
                        <small style="color: #059669; font-weight: 700; margin-top: 2px;">
                            <i class="fa-solid fa-circle-check"></i> وثيقة أصلية موثقة
                        </small>
                    </div>
                </div>

                <div class="sig-block">
                    <div class="sig-title">المشرف العام وإدارة المنصة</div>
                    <span class="sig-name">{{ \App\Models\Setting::get('admin_name', 'أ. المشرف العام للمنصة') }}</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadAsImage() {
            const btn = document.getElementById('btnDownloadImg');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جارٍ التحميل...';
            btn.disabled = true;

            const sheet = document.getElementById('certificateSheet');
            html2canvas(sheet, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#fffdf9'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'شهادة_{{ str_replace(" ", "_", $studentNameAr) }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                btn.innerHTML = '<i class="fa-regular fa-image"></i> حفظ كصورة';
                btn.disabled = false;
            }).catch(err => {
                console.error(err);
                alert('تعذر تحميل الصورة، يرجى استخدام زر طباعة الوثيقة لحفظها كـ PDF.');
                btn.innerHTML = '<i class="fa-regular fa-image"></i> حفظ كصورة';
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>
