<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>انتهت صلاحية الجلسة | منارة التوجيهي 🇵🇸</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;600;700;800&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-dark: #0f172a;
            --amber: #d97706;
            --bg: #f8fafc;
            --surface: #ffffff;
            --border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Alexandria', 'Tajawal', sans-serif; }
        body {
            background: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .error-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            max-width: 540px;
            width: 100%;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }
        .error-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .error-icon {
            font-size: 3rem;
            color: var(--amber);
            margin-bottom: 16px;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 10px;
        }
        p {
            font-size: 0.92rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 28px;
        }
        .btn-refresh {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: var(--primary);
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }
        .btn-refresh:hover {
            background: #172554;
            transform: translateY(-1px);
        }
        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            margin-top: 16px;
        }
        .btn-home:hover {
            color: var(--primary);
        }
        .institution-footer {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid var(--border);
            font-size: 0.8rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-badge">
            <i class="fa-solid fa-clock-rotate-left"></i> رمز الخطأ 419: انتهاء الجلسة المؤقتة
        </div>
        <div class="error-icon">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h1>انتهت صلاحية الصفحة المؤقتة</h1>
        <p>
            حرصاً على أمان بياناتك الأكاديمية تم إنهاء الجلسة بسبب عدم النشاط لفترة.
            يرجى النقر على زر التحديث للمتابعة واستكمال عمليتك بشكل طبيعي.
        </p>
        <div>
            <button type="button" onclick="window.location.reload()" class="btn-refresh">
                <i class="fa-solid fa-rotate-right"></i> تحديث الصفحة والمتابعة
            </button>
        </div>
        <div>
            <a href="/" class="btn-home">
                <i class="fa-solid fa-arrow-right"></i> العودة للصفحة الرئيسية للمنصة
            </a>
        </div>
        <div class="institution-footer">
            منصة منارة التوجيهي - الثانوية العامة 🇵🇸 | إشراف الأستاذ أحمد حسين شمالي
        </div>
    </div>
</body>
</html>
