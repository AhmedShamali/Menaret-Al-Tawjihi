<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق من صحة الشهادة الرقمية | {{ $siteName }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Alexandria', sans-serif; }
        body {
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #0f172a;
        }

        .verify-card {
            background: #ffffff;
            width: 100%;
            max-width: 580px;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            text-align: center;
        }

        .verify-header {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: white;
            padding: 35px 25px;
            position: relative;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 2rem;
            margin: 0 auto 15px;
            backdrop-filter: blur(8px);
        }

        .verify-body {
            padding: 35px 30px;
        }

        .badge-verified {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 800;
            margin-bottom: 25px;
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
            font-size: 0.95rem;
            font-weight: 800;
            margin-bottom: 25px;
        }

        .info-grid {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            text-align: right;
            margin-bottom: 25px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.92rem;
        }
        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-weight: 600;
        }
        .info-value {
            color: #0f172a;
            font-weight: 800;
        }

        .btn-home {
            background: #0284c7;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .btn-home:hover {
            background: #0369a1;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="verify-card">
        <div class="verify-header">
            @if(\App\Models\Setting::get('site_logo'))
                <div style="margin-bottom: 12px;">
                    <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ $siteName }}" style="max-height: 60px; max-width: 130px; object-fit: contain; background: white; padding: 6px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                </div>
            @else
                <div class="header-icon">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
            @endif
            <h2>بوابة التحقق الرسمية</h2>
            <p style="opacity: 0.9; font-size: 0.88rem; margin-top: 4px;">{{ $siteName }} • دولة فلسطين 🇵🇸</p>
        </div>

        <div class="verify-body">
            @if($certificate)
                <div class="badge-verified">
                    <i class="fa-solid fa-circle-check"></i> شهادة أصلية وموثقة بسجلات المنصة
                </div>

                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">اسم الطالب / الطالبة:</span>
                        <span class="info-value" style="color: #007a3d; font-size: 1.05rem;">{{ $certificate->student->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">المادة الأكاديمية:</span>
                        <span class="info-value">{{ $certificate->subject->name_ar ?? $certificate->subject->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">معدل الاجتياز والتفوق:</span>
                        <span class="info-value" style="color: #d97706; font-size: 1.1rem;">{{ $certificate->final_grade }}%</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">رمز الشهادة المرجعي:</span>
                        <span class="info-value" style="font-family: monospace;">{{ $certificate->certificate_code }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">تاريخ الإصدار:</span>
                        <span class="info-value">{{ $certificate->created_at ? $certificate->created_at->format('Y-m-d') : date('Y-m-d') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">المشرف العام المعتمد:</span>
                        <span class="info-value">أحمد حسين شمالي</span>
                    </div>
                </div>

                <a href="{{ route('student.certificates.show', $certificate->id) }}" class="btn-home" style="margin-left: 10px;">
                    <i class="fa-solid fa-eye"></i> استعراض الشهادة
                </a>
                <a href="/" class="btn-home" style="background: #e2e8f0; color: #475569;">
                    <i class="fa-solid fa-house"></i> الرئيسية
                </a>
            @else
                <div class="badge-invalid">
                    <i class="fa-solid fa-triangle-exclamation"></i> عذراً، هذا الرمز غير صالح أو لم يتم العثور على الشهادة
                </div>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 25px;">
                    الرمز المدخل: <code>{{ $code }}</code> غير مسجل في السجلات الأكاديمية للمنصة.
                </p>
                <a href="/" class="btn-home">
                    <i class="fa-solid fa-house"></i> العودة للرئيسية
                </a>
            @endif
        </div>
    </div>

</body>
</html>
