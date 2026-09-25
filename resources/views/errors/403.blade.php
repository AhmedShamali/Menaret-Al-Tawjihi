<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('غير مصرح بالوصول') }} | {{ config('app.name', 'منارة التوجيهي') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;600;700;800&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-dark: #0f172a;
            --danger: #dc2626;
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
            background: #fef2f2;
            color: var(--danger);
            border: 1px solid #fecaca;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .error-icon {
            font-size: 3.5rem;
            color: var(--danger);
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
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }
        .btn-home {
            background: var(--primary);
            color: #ffffff;
        }
        .btn-home:hover {
            background: #172554;
            transform: translateY(-1px);
        }
        .btn-back {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
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
            <i class="fa-solid fa-shield-halved"></i> {{ __('رمز الخطأ 403 - وصول مقيد') }}
        </div>
        <div class="error-icon">
            <i class="fa-solid fa-lock"></i>
        </div>
        <h1>{{ __('عذراً، غير مصرح لك بالوصول') }}</h1>
        <p>{{ $exception->getMessage() ?: __('هذه الصفحة أو المنطقة مخصصة لأدوار محددة أو تتطلب صلاحيات وصول إضافية. يرجى التأكد من نوع حسابك أو التواصل مع إدارة المنصة.') }}</p>
        <div class="btn-group">
            <a href="javascript:history.back()" class="btn-action btn-back">
                <i class="fa-solid fa-arrow-right"></i> {{ __('الرجوع للخلف') }}
            </a>
            <a href="{{ route('dashboard') }}" class="btn-action btn-home">
                <i class="fa-solid fa-house"></i> {{ __('لوحة التحكم') }}
            </a>
        </div>
        <div class="institution-footer">
            {{ __('منصة منارة التوجيهي - الثانوية العامة | إشراف المهندس أحمد شمالي') }}
        </div>
    </div>
</body>
</html>
