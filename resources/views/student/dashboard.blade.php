@extends('layouts.app')

@section('content')
<style>
    /* Scope Dashboard UI to prevent Layout Contamination */
    .st-dashboard {
        --st-primary: #2563eb;
        --st-primary-dark: #1d4ed8;
        --st-primary-soft: #eff6ff;
        --st-accent: #06b6d4;
        --st-success: #10b981;
        --st-success-soft: #ecfdf5;
        --st-warning: #f59e0b;
        --st-text-dark: #0f172a;
        --st-text-muted: #64748b;
        --st-bg-surface: #ffffff;
        --st-border: #e2e8f0;
        --st-radius-lg: 24px;
        --st-radius-md: 16px;
        --st-shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.03), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        --st-shadow-hover: 0 20px 30px -10px rgba(37, 99, 235, 0.12);

        display: flex;
        flex-direction: column;
        gap: 32px;
        color: var(--st-text-dark);
        font-family: inherit;
    }

    /* 1. Hero Welcome Card */
    .st-welcome-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%);
        border-radius: var(--st-radius-lg);
        padding: 40px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(37, 99, 235, 0.35);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
    }

    .st-welcome-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, rgba(255,255,255,0) 70%);
        pointer-events: none;
    }

    .st-welcome-content {
        position: relative;
        z-index: 2;
        max-width: 580px;
    }

    .st-welcome-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 600;
        margin-bottom: 16px;
        color: #e0f2fe;
    }

    .st-welcome-title {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1.25;
        margin: 0 0 12px 0;
        letter-spacing: -0.5px;
    }

    .st-welcome-desc {
        font-size: 1rem;
        opacity: 0.9;
        margin: 0;
        line-height: 1.6;
        color: #cbd5e1;
    }

    /* Stats Section */
    .st-stats-wrapper {
        display: flex;
        gap: 16px;
        position: relative;
        z-index: 2;
    }

    .st-stat-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 18px 24px;
        border-radius: 20px;
        min-width: 150px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: transform 0.3s ease;
    }

    .st-stat-card:hover {
        transform: translateY(-3px);
        background: rgba(255, 255, 255, 0.15);
    }

    .st-stat-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #93c5fd;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .st-stat-value {
        font-size: 1.8rem;
        font-weight: 900;
        color: #ffffff;
        line-height: 1;
    }

    /* Layout Grid */
    .st-main-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 32px;
    }

    .st-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .st-section-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--st-text-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .st-icon-pill {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 1.1rem;
    }

    /* Available Exams Cards */
    .st-exams-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    .st-exam-card {
        background: var(--st-bg-surface);
        border: 1px solid var(--st-border);
        border-radius: var(--st-radius-md);
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--st-shadow-subtle);
        position: relative;
        overflow: hidden;
    }

    .st-exam-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--st-primary), var(--st-accent));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .st-exam-card:hover {
        transform: translateY(-5px);
        border-color: rgba(37, 99, 235, 0.3);
        box-shadow: var(--st-shadow-hover);
    }

    .st-exam-card:hover::before {
        opacity: 1;
    }

    .st-exam-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--st-primary-soft);
        color: var(--st-primary-dark);
        font-weight: 700;
        font-size: 0.75rem;
        padding: 6px 14px;
        border-radius: 50px;
    }

    .st-exam-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--st-text-dark);
        margin: 14px 0 16px 0;
        line-height: 1.4;
    }

    .st-exam-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
        font-size: 0.85rem;
        color: var(--st-text-muted);
        margin-bottom: 20px;
    }

    .st-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }

    .st-btn-primary {
        background: linear-gradient(135deg, var(--st-primary) 0%, var(--st-primary-dark) 100%);
        color: #ffffff !important;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        border: none;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .st-btn-primary:hover {
        opacity: 0.95;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        transform: scale(1.01);
    }

    /* Completed Exams List */
    .st-completed-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .st-completed-card {
        background: var(--st-bg-surface);
        border: 1px solid var(--st-border);
        border-radius: var(--st-radius-md);
        padding: 18px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
        box-shadow: var(--st-shadow-subtle);
    }

    .st-completed-card:hover {
        border-color: #cbd5e1;
        background-color: #f8fafc;
    }

    .st-completed-title {
        font-weight: 700;
        font-size: 1rem;
        color: var(--st-text-dark);
        margin-bottom: 4px;
    }

    .st-completed-date {
        font-size: 0.8rem;
        color: var(--st-text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .st-score-badge {
        background: var(--st-success-soft);
        color: var(--st-success);
        font-weight: 800;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 1rem;
        border: 1px solid rgba(16, 185, 129, 0.2);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Empty States */
    .st-empty-box {
        background: var(--st-bg-surface);
        border: 2px dashed var(--st-border);
        border-radius: var(--st-radius-md);
        padding: 40px 20px;
        text-align: center;
        color: var(--st-text-muted);
    }

    .st-empty-icon {
        width: 60px;
        height: 60px;
        background: #f1f5f9;
        border-radius: 50%;
        display: grid;
        place-items: center;
        margin: 0 auto 16px auto;
        font-size: 1.5rem;
        color: var(--st-text-muted);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .st-welcome-card {
            padding: 28px 20px;
        }

        .st-welcome-title {
            font-size: 1.6rem;
        }

        .st-stats-wrapper {
            width: 100%;
        }

        .st-stat-card {
            flex: 1;
            min-width: 0;
            padding: 14px;
        }

        .st-stat-value {
            font-size: 1.4rem;
        }

        .st-exams-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="st-dashboard">

    {{-- 1. Hero Banner --}}
    <div class="st-welcome-card">
        <div class="st-welcome-content">
            <div class="st-welcome-badge">
                <i class="fas fa-graduation-cap"></i>
                <span>{{ $student->stage->name_ar ?? 'الثانوية العامة فلسطين 🇵🇸' }}</span>
            </div>
            <h1 class="st-welcome-title">أهلاً بك يا {{ $student->name_ar ?? 'بطل التوجيهي' }}! 👋</h1>
            <p class="st-welcome-desc">طريقك نحو الـ 99% يبدأ من هنا. تابع دروسك واختباراتك المقررة وتواصل مع معلميك مباشرة.</p>
        </div>

        <div class="st-stats-wrapper">
            @php $currentStudent = Auth::guard('student')->user() ?? $student; @endphp
            <div class="st-stat-card" style="border-color: rgba(249, 115, 22, 0.4); background: rgba(249, 115, 22, 0.15);">
                <span class="st-stat-label" style="color: #fdba74;"><i class="fas fa-fire"></i> التزامك اليومي</span>
                <span class="st-stat-value" style="color: #ffedd5;">{{ $currentStudent->streak_count ?? 1 }} أيام 🔥</span>
            </div>
            <div class="st-stat-card">
                <span class="st-stat-label">المعدل العام</span>
                <span class="st-stat-value">{{ number_format($my_stats['avg_grade'] ?? 0, 1) }}%</span>
            </div>
            <div class="st-stat-card">
                <span class="st-stat-label">المكتملة</span>
                <span class="st-stat-value">{{ $my_stats['completed_exams'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- تنبيه العمليات المالية وإشعارات الدفع قيد المراجعة --}}
    @if(isset($pendingPayments) && $pendingPayments->isNotEmpty())
        @foreach($pendingPayments as $pendingPay)
            <div style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1.5px solid #fde68a; border-radius: 20px; padding: 20px 26px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 18px; box-shadow: 0 4px 18px rgba(245, 158, 11, 0.12);">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 48px; height: 48px; border-radius: 14px; background: #fef3c7; color: #b45309; display: grid; place-items: center; font-size: 1.4rem; flex-shrink: 0; border: 1.5px solid #fde68a;">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="background: #b45309; color: white; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 6px;">قيد التدقيق والمراجعة</span>
                            <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #78350f;">
                                تم استلام إشعار السداد بنجاح وجارٍ تدقيقه وتفعيل موادك من قِبل المشرف
                            </h3>
                        </div>
                        <p style="margin: 0; font-size: 0.86rem; color: #92400e; line-height: 1.5;">
                            معاملة رقم <strong style="font-family: monospace;">{{ $pendingPay->transaction_number }}</strong> بمبلغ <strong>{{ number_format($pendingPay->amount, 0) }} ₪</strong> عبر {{ $pendingPay->gateway_name_ar }}. يقوم المشرف الآن بمطابقة الإشعار واعتماد المواد لحسابك رسمياً.
                        </p>
                    </div>
                </div>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <a href="{{ route('student.checkout.receipt', $pendingPay->id) }}" style="background: #0284c7; color: white; text-decoration: none; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);">
                        <i class="fa-solid fa-file-invoice"></i>
                        <span>عرض الإيصال والإشعار</span>
                    </a>
                    @php $adminWhatsapp = \App\Models\Setting::get('contact_whatsapp', \App\Models\Setting::get('payment_phone', '0567897212')); @endphp
                    <a href="https://wa.me/972{{ ltrim($adminWhatsapp, '0') }}?text={{ urlencode('مرحباً إدارة منارة التوجيهي، قمت برفع إشعار دفع برقم: ' . $pendingPay->transaction_number . ' للاعتماد.') }}" target="_blank" style="background: #25d366; color: white; text-decoration: none; padding: 10px 18px; border-radius: 12px; font-weight: 700; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-brands fa-whatsapp" style="font-size: 1.1rem;"></i>
                        <span>متابعة مع المشرف</span>
                    </a>
                </div>
            </div>
        @endforeach
    @endif

    {{-- عد تنازلي لامتحانات الثانوية العامة في فلسطين --}}
    <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); border-radius: 20px; padding: 22px 28px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; box-shadow: 0 10px 30px -5px rgba(49, 46, 129, 0.3); border: 1px solid rgba(255,255,255,0.1);">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 50px; height: 50px; border-radius: 14px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #a5b4fc; flex-shrink: 0;">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                    <span style="background: #ef4444; color: white; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 6px;">دورة فلسطين الوزارية 🇵🇸</span>
                    <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0; color: white;">العد التنازلي لامتحانات الثانوية العامة (التوجيهي)</h3>
                </div>
                <p style="margin: 0; font-size: 0.82rem; color: #c7d2fe;" id="dailyTipText">💡 نصيحة اليوم: ركز على حل تدريبات وأسئلة معلميك والتأكد من فهم كل خطوة.</p>
            </div>
        </div>

        <div style="display: flex; gap: 10px; align-items: center;" id="tawjihiCountdownBoxes">
            <div style="background: rgba(0, 0, 0, 0.25); border: 1px solid rgba(255,255,255,0.15); border-radius: 14px; padding: 8px 12px; text-align: center; min-width: 60px;">
                <span style="font-size: 1.5rem; font-weight: 900; font-family: monospace; display: block; color: #38bdf8;" id="cntDays">--</span>
                <span style="font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">يوم</span>
            </div>
            <div style="font-size: 1.1rem; font-weight: 800; color: rgba(255,255,255,0.3);">:</div>
            <div style="background: rgba(0, 0, 0, 0.25); border: 1px solid rgba(255,255,255,0.15); border-radius: 14px; padding: 8px 12px; text-align: center; min-width: 60px;">
                <span style="font-size: 1.5rem; font-weight: 900; font-family: monospace; display: block; color: #38bdf8;" id="cntHours">--</span>
                <span style="font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">ساعة</span>
            </div>
            <div style="font-size: 1.1rem; font-weight: 800; color: rgba(255,255,255,0.3);">:</div>
            <div style="background: rgba(0, 0, 0, 0.25); border: 1px solid rgba(255,255,255,0.15); border-radius: 14px; padding: 8px 12px; text-align: center; min-width: 60px;">
                <span style="font-size: 1.5rem; font-weight: 900; font-family: monospace; display: block; color: #38bdf8;" id="cntMinutes">--</span>
                <span style="font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">دقيقة</span>
            </div>
            <div style="font-size: 1.1rem; font-weight: 800; color: rgba(255,255,255,0.3);">:</div>
            <div style="background: rgba(0, 0, 0, 0.25); border: 1px solid rgba(255,255,255,0.15); border-radius: 14px; padding: 8px 12px; text-align: center; min-width: 60px;">
                <span style="font-size: 1.5rem; font-weight: 900; font-family: monospace; display: block; color: #fbbf24;" id="cntSeconds">--</span>
                <span style="font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">ثانية</span>
            </div>
        </div>
    </div>

    {{-- Tawjihi Super Toolkit Grid --}}
    <div style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                <span style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </span>
                صندوق أدوات التوجيهي والتفوق 🇵🇸
            </h2>
            <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">خدمات تفاعلية لمتابعة دراستك</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 14px;">
            <a href="{{ route('student.courses.catalog') }}" style="background: white; border: 1px solid var(--st-border); border-radius: 16px; padding: 18px; text-decoration: none; display: flex; flex-direction: column; gap: 8px; transition: 0.2s; box-shadow: var(--st-shadow-subtle);">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fas fa-cart-shopping"></i>
                </div>
                <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a;">باقات المواد والاشتراك</div>
                <span style="font-size: 0.78rem; color: #64748b;">تفعيل فوري مع جوال باي وبال باي 💳</span>
            </a>

            <a href="{{ route('student.teachers.index') }}" style="background: white; border: 1px solid var(--st-border); border-radius: 16px; padding: 18px; text-decoration: none; display: flex; flex-direction: column; gap: 8px; transition: 0.2s; box-shadow: var(--st-shadow-subtle);">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fas fa-comments"></i>
                </div>
                <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a;">مراسلة المعلمين</div>
                <span style="font-size: 0.78rem; color: #64748b;">محادثة ذكية بنمط تيليجرام 💬</span>
            </a>

            <a href="{{ route('tawjihi.calculator') }}" target="_blank" style="background: white; border: 1px solid var(--st-border); border-radius: 16px; padding: 18px; text-decoration: none; display: flex; flex-direction: column; gap: 8px; transition: 0.2s; box-shadow: var(--st-shadow-subtle);">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #fef3c7; color: #b45309; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fas fa-calculator"></i>
                </div>
                <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a;">حاسبة المعدل</div>
                <span style="font-size: 0.78rem; color: #64748b;">واحتساب التنسيق والقبول 🧮</span>
            </a>

            <a href="{{ route('tawjihi.formulas') }}" target="_blank" style="background: white; border: 1px solid var(--st-border); border-radius: 16px; padding: 18px; text-decoration: none; display: flex; flex-direction: column; gap: 8px; transition: 0.2s; box-shadow: var(--st-shadow-subtle);">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fas fa-square-root-variable"></i>
                </div>
                <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a;">دليل القوانين الذهبية</div>
                <span style="font-size: 0.78rem; color: #64748b;">ملخص المنهاج العلمي 📐</span>
            </a>

            <a href="{{ route('student.subjects.index') }}" style="background: white; border: 1px solid var(--st-border); border-radius: 16px; padding: 18px; text-decoration: none; display: flex; flex-direction: column; gap: 8px; transition: 0.2s; box-shadow: var(--st-shadow-subtle);">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; color: #1e40af; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fas fa-book-open"></i>
                </div>
                <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a;">مناهجي ومقرراتي</div>
                <span style="font-size: 0.78rem; color: #64748b;">شروحات الدروس والملخصات 📚</span>
            </a>

            <a href="{{ route('student.leaderboard') }}" style="background: white; border: 1px solid var(--st-border); border-radius: 16px; padding: 18px; text-decoration: none; display: flex; flex-direction: column; gap: 8px; transition: 0.2s; box-shadow: var(--st-shadow-subtle);">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #ffe4e6; color: #e11d48; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fas fa-fire"></i>
                </div>
                <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a;">لوحة الشرف والالتزام</div>
                <span style="font-size: 0.78rem; color: #64748b;">تنافس وتصدر قائمة الأبطال 🔥</span>
            </a>

            <a href="{{ route('student.achievements') }}" style="background: white; border: 1px solid var(--st-border); border-radius: 16px; padding: 18px; text-decoration: none; display: flex; flex-direction: column; gap: 8px; transition: 0.2s; box-shadow: var(--st-shadow-subtle);">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #fef9c3; color: #a16207; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fas fa-medal"></i>
                </div>
                <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a;">الشهادات الملكية وبومودورو</div>
                <span style="font-size: 0.78rem; color: #64748b;">أوسمة معتمدة ومؤقت التركيز 🏆</span>
            </a>
        </div>
    </div>

    {{-- 2. Content Grid --}}
    <div class="st-main-grid">

        {{-- قسم الاختبارات المتاحة --}}
        <div>
            <div class="st-section-header">
                <h2 class="st-section-title">
                    <span class="st-icon-pill" style="background: var(--st-primary-soft); color: var(--st-primary);">
                        <i class="fas fa-hourglass-half"></i>
                    </span>
                    <span>اختبارات بانتظارك</span>
                </h2>
            </div>

            <div class="st-exams-grid">
                @forelse($available_exams as $ex)
                    <div class="st-exam-card">
                        <div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="st-exam-tag">
                                    <i class="fas fa-book-open"></i>
                                    {{ $ex->subject->name_ar ?? 'المادة الدراسية' }}
                                </span>
                            </div>

                            <h3 class="st-exam-title">{{ $ex->title }}</h3>

                            <div class="st-exam-meta">
                                <div class="st-meta-item">
                                    <i class="far fa-question-circle text-primary"></i>
                                    <span>{{ $ex->questions_count ?? ($ex->questions ? $ex->questions->count() : 10) }} أسئلة</span>
                                </div>
                                <div class="st-meta-item">
                                    <i class="far fa-clock text-primary"></i>
                                    <span>{{ $ex->duration_minutes ?? '30' }} دقيقة</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('student.exams.take', $ex->id) }}" class="st-btn-primary">
                            <span>بدء الاختبار الآن</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1;">
                        <div class="st-empty-box">
                            <div class="st-empty-icon" style="color: var(--st-success); background: var(--st-success-soft);">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1 fs-6">أحسنت العمل!</h4>
                            <p class="mb-0 small">لا توجد اختبارات معلقة حالياً، عد لاحقاً لمتابعة الجديد.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- قسم الاختبارات المكتملة --}}
        <div>
            <div class="st-section-header">
                <h2 class="st-section-title">
                    <span class="st-icon-pill" style="background: var(--st-success-soft); color: var(--st-success);">
                        <i class="fas fa-award"></i>
                    </span>
                    <span>الاختبارات التي أكملتها</span>
                </h2>
            </div>

            <div class="st-completed-list">
                @forelse($completed_exams ?? [] as $done_exam)
                    <div class="st-completed-card">
                        <div>
                            <div class="st-completed-title">{{ $done_exam->exam->title ?? $done_exam->title }}</div>
                            <div class="st-completed-date">
                                <i class="far fa-calendar-check text-muted"></i>
                                <span>تاريخ الإنجاز: {{ $done_exam->created_at ? $done_exam->created_at->format('Y/m/d') : 'مؤخراً' }}</span>
                            </div>
                        </div>
                        <div class="st-score-badge" style="{{ $done_exam->status == 'pending' ? 'background: #fef3c7; color: #d97706; border-color: rgba(217, 119, 6, 0.2);' : '' }}">
                            <i class="fas {{ $done_exam->status == 'pending' ? 'fa-clock' : 'fa-star' }} fs-7"></i>
                            <span>
                                @if($done_exam->status == 'graded')
                                    {{ number_format($done_exam->total_earned_grade, 1) }}%
                                @else
                                    قيد التصحيح
                                @endif
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="st-empty-box">
                        <div class="st-empty-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <p class="mb-0 small">لم تقم بإكمال أي اختبارات بعد.</p>
                    </div>
                @endforelse
            </div>
        </div>

</div>

<script>
    function initTawjihiCountdown() {
        const now = new Date();
        let currentYear = now.getFullYear();
        let targetExamDate = new Date(currentYear, 5, 7, 9, 0, 0); // June 7 at 9:00 AM
        if (now > targetExamDate) {
            targetExamDate = new Date(currentYear + 1, 5, 7, 9, 0, 0);
        }

        const tips = [
            "💡 نصيحة اليوم: ركز على حل نماذج امتحانات الإنجاز الوزارية والأسئلة الشاملة لضبط إدارة الوقت في القاعة.",
            "💡 نصيحة اليوم: استخدم بطاقات الاستذكار لحفظ القوانين والمصطلحات الصعبة قبل النوم لتثبيتها في الذاكرة طويلة المدى.",
            "💡 نصيحة اليوم: خصص استراحة 5 دقائق لكل 25 دقيقة دراسة (تقنية بومودورو) لتحافظ على نشاط عقلك.",
            "💡 نصيحة اليوم: تأكد من مراجعة أسئلة نهاية كل وحدة في الكتب الوزارية فهي مصدر أساسي للأسئلة.",
            "💡 نصيحة اليوم: لا تتردد في سؤال معلمك في المنصة عن أي مسألة أو قانون تجد فيه صعوبة."
        ];
        const dayOfYear = Math.floor((now - new Date(now.getFullYear(), 0, 0)) / 1000 / 60 / 60 / 24);
        const tipEl = document.getElementById('dailyTipText');
        if (tipEl) tipEl.innerText = tips[dayOfYear % tips.length];

        function updateTimer() {
            const diff = targetExamDate - new Date();
            if (diff <= 0) {
                document.getElementById('cntDays').innerText = '0';
                document.getElementById('cntHours').innerText = '0';
                document.getElementById('cntMinutes').innerText = '0';
                document.getElementById('cntSeconds').innerText = '0';
                return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
            const minutes = Math.floor((diff / 1000 / 60) % 60);
            const seconds = Math.floor((diff / 1000) % 60);

            const dEl = document.getElementById('cntDays');
            const hEl = document.getElementById('cntHours');
            const mEl = document.getElementById('cntMinutes');
            const sEl = document.getElementById('cntSeconds');

            if (dEl) dEl.innerText = days;
            if (hEl) hEl.innerText = String(hours).padStart(2, '0');
            if (mEl) mEl.innerText = String(minutes).padStart(2, '0');
            if (sEl) sEl.innerText = String(seconds).padStart(2, '0');
        }

        updateTimer();
        setInterval(updateTimer, 1000);
    }
    document.addEventListener('DOMContentLoaded', initTawjihiCountdown);
</script>
@endsection
