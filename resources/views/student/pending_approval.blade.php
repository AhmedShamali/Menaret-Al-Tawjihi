@extends('layouts.app')

@section('title', 'بانتظار موافقة الإدارة وتفعيل الاشتراك | منارة التوجيهي')

@section('content')
<div class="pending-approval-wrapper" dir="rtl">

    <div class="pending-approval-card">
        <!-- أيقونة الحالة المتحركة -->
        <div class="pending-icon-bubble">
            <div class="pulse-ring"></div>
            <i class="fa-solid fa-hourglass-half"></i>
        </div>

        <!-- شارات الحالة -->
        <div class="status-badges-row">
            <span class="badge-tag pending"><i class="fa-solid fa-clock"></i> بانتظار موافقة المدير</span>
            <span class="badge-tag palestine">🇵🇸 منارة التوجيهي</span>
        </div>

        <h1 class="card-title">طلبك قيد المراجعة والاعتماد الأكاديمي ⏳</h1>
        
        <p class="card-desc">
            أهلاً بك يا <strong>{{ $student->name_ar ?? $student->name ?? 'بطل التوجيهي' }}</strong>! تم استلام طلب تسجيلك واشتراكك بنجاح.
            حرصاً من إدارة المنصة على جودة المتابعة وتنظيم مقاعد الطلبة في مساقات التوجيهي، يقوم المشرف العام <strong>(أ. أحمد حسين شمالي)</strong> بمراجعة بياناتك وتفعيل حسابك واشتراكك يدوياً خلال وقت قصير.
        </p>

        <!-- بطاقة تفاصيل الطالب المسجلة -->
        <div class="student-info-strip">
            <div class="info-cell">
                <small>اسم الطالب المسجل</small>
                <strong>{{ $student->name_ar ?? $student->name }}</strong>
            </div>
            <div class="info-cell">
                <small>المرحلة / الفرع الدراسي</small>
                <strong>{{ optional($student->stage)->name ?? optional($student->stage)->name_ar ?? 'الثانوية العامة (التوجيهي)' }}</strong>
            </div>
            <div class="info-cell">
                <small>رقم الهاتف</small>
                <strong dir="ltr">{{ $student->phone ?? '0590000000' }}</strong>
            </div>
            <div class="info-cell">
                <small>حالة الحساب</small>
                <span class="text-warning font-bold">بانتظار الموافقة ⏳</span>
            </div>
        </div>

        <!-- المواد المطلوبة قيد المراجعة إن وجدت -->
        @if(isset($pendingEnrollments) && $pendingEnrollments->count() > 0)
            <div class="pending-subjects-box">
                <h4><i class="fa-solid fa-book-bookmark text-primary"></i> المواد المطلوب تفعيل الاشتراك بها:</h4>
                <div class="subject-pills-wrap">
                    @foreach($pendingEnrollments as $enr)
                        <span class="sub-pill">
                            <i class="fa-solid fa-check"></i> {{ $enr->subject->name_ar ?? $enr->subject->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- مسار التفعيل بالخطوات -->
        <div class="activation-steps-container">
            <div class="step-box done">
                <div class="step-num"><i class="fa-solid fa-check"></i></div>
                <div class="step-content">
                    <h5>1. تسجيل الطلب</h5>
                    <p>تم إدخال بياناتك بنجاح في النظام</p>
                </div>
            </div>
            <div class="step-box current">
                <div class="step-num">2</div>
                <div class="step-content">
                    <h5>2. موافقة المشرف العام</h5>
                    <p>فحص وتأكيد تسجيل الدخول والاشتراك ⏳</p>
                </div>
            </div>
            <div class="step-box upcoming">
                <div class="step-num"><i class="fa-solid fa-lock"></i></div>
                <div class="step-content">
                    <h5>3. فتح كامل المنهاج</h5>
                    <p>انطلاق الدروس والاختبارات التفاعلية</p>
                </div>
            </div>
        </div>

        <!-- أزرار الإجراء والتواصل السريع -->
        <div class="pending-actions-wrap">
            @php
                $waMsg = urlencode("مرحباً أستاذ أحمد شمالي، أنا الطالب (" . ($student->name_ar ?? $student->name) . ") ورقم هاتفي (" . $student->phone . ")، قمت بإنشاء حسابي في منارة التوجيهي وأرجو من حضرتك التكرم باعتماد وتفعيل دخولي واشتراكي.");
            @endphp
            <a href="https://wa.me/970599000000?text={{ $waMsg }}" target="_blank" class="btn-action-primary whatsapp">
                <i class="fa-brands fa-whatsapp"></i> تواصل مع المشرف العام لاعتماد الحساب فوراً
            </a>

            <button type="button" class="btn-action-secondary" onclick="checkStatusRefresh()">
                <i class="fa-solid fa-rotate-right"></i> فحص حالة الحساب وتحديث الصفحة
            </button>

            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-action-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
                </button>
            </form>
        </div>

        <div class="pending-footer-note">
            <i class="fa-solid fa-shield-halved text-success"></i>
            <span>بياناتك محفوظة بأعلى درجات الأمان والخصوصية وفق معايير وزارة التربية والتعليم الفلسطينية.</span>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function checkStatusRefresh() {
        Swal.fire({
            title: 'جاري فحص حالة الحساب...',
            text: 'يرجى الانتظار ثانية واحدة',
            timer: 1000,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        }).then(() => {
            window.location.reload();
        });
    }
</script>

<style>
    .pending-approval-wrapper {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }

    .pending-approval-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 28px;
        padding: 45px 35px;
        max-width: 780px;
        width: 100%;
        text-align: center;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08);
        position: relative;
        animation: fadeIn 0.4s ease;
    }

    /* الأيقونة النباضة */
    .pending-icon-bubble {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin-bottom: 20px;
        position: relative;
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.35);
    }
    .pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 3px solid #f59e0b;
        animation: pulseRing 2s infinite ease-out;
    }
    @keyframes pulseRing {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(1.4); opacity: 0; }
    }

    .status-badges-row {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 16px;
    }
    .badge-tag {
        font-size: 0.82rem;
        font-weight: 800;
        padding: 5px 14px;
        border-radius: 50px;
    }
    .badge-tag.pending {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-tag.palestine {
        background: #0f172a;
        color: #f8fafc;
    }

    .card-title {
        font-size: 1.75rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 12px;
        line-height: 1.35;
    }
    .card-desc {
        color: #475569;
        font-size: 0.98rem;
        line-height: 1.8;
        margin-bottom: 28px;
    }

    /* تفاصيل الطالب */
    .student-info-strip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 18px 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
        text-align: right;
    }
    .info-cell small {
        display: block;
        color: #64748b;
        font-size: 0.76rem;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .info-cell strong {
        font-size: 0.95rem;
        color: #0f172a;
        font-weight: 800;
    }

    /* المواد المطلوبة */
    .pending-subjects-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 25px;
        text-align: right;
    }
    .pending-subjects-box h4 {
        font-size: 0.92rem;
        font-weight: 800;
        color: #166534;
        margin-bottom: 10px;
    }
    .subject-pills-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .sub-pill {
        background: #ffffff;
        border: 1px solid #86efac;
        color: #15803d;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* مسار الخطوات */
    .activation-steps-container {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 15px;
        margin-bottom: 30px;
        text-align: right;
    }
    @media (max-width: 650px) {
        .activation-steps-container {
            grid-template-columns: 1fr;
        }
    }
    .step-box {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: 0.2s;
    }
    .step-box.done {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }
    .step-box.done .step-num {
        background: #10b981;
        color: white;
    }
    .step-box.current {
        border-color: #f59e0b;
        background: #fffbeb;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.15);
    }
    .step-box.current .step-num {
        background: #f59e0b;
        color: white;
    }
    .step-box.upcoming {
        opacity: 0.6;
    }
    .step-num {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .step-content h5 {
        font-size: 0.88rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 2px;
    }
    .step-content p {
        font-size: 0.74rem;
        color: #64748b;
        margin: 0;
    }

    /* أزرار الإجراء */
    .pending-actions-wrap {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px;
    }
    .btn-action-primary.whatsapp {
        background: #25d366;
        color: white;
        text-decoration: none;
        padding: 14px 24px;
        border-radius: 14px;
        font-weight: 800;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 6px 20px rgba(37, 211, 102, 0.3);
        transition: 0.2s;
    }
    .btn-action-primary.whatsapp:hover {
        background: #1eb956;
        transform: translateY(-2px);
    }
    .btn-action-secondary {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: 0.2s;
    }
    .btn-action-secondary:hover {
        background: #e2e8f0;
    }
    .btn-action-logout {
        background: none;
        border: none;
        color: #ef4444;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        padding: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-action-logout:hover {
        text-decoration: underline;
    }

    .pending-footer-note {
        font-size: 0.78rem;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-top: 1px solid #f1f5f9;
        padding-top: 18px;
    }
</style>
@endsection
