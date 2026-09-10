@extends('layouts.app')

@section('content')
<div class="ed-login-container">
    <!-- الجانب الأيسر: هوية المنصة والمحتوى الترحيبي الأكاديمي -->
    <div class="ed-login-banner">
        <div class="ed-banner-overlay"></div>
        <div class="ed-banner-content">
            <div class="ed-banner-badge">
                <i class="fas fa-graduation-cap"></i> منصة التعلم التفاعلية
            </div>
            <h1 class="ed-banner-title">منارة التوجيهي الأكاديمية</h1>
            <p class="ed-banner-desc">
                بيئة تعليمية هادئة ومتكاملة، صُممت لمساعدة طلبة التوجيهي والمعلمين في فلسطين على تحقيق أعلى درجات التميز والإتقان الأكاديمي.
            </p>

            <div class="ed-banner-features">
                <div class="ed-feat-item">
                    <div class="ed-feat-icon"><i class="fas fa-book-open"></i></div>
                    <div>
                        <strong>مناهج معتمدة ومحدثة</strong>
                        <p>شروحات وملخصات تفاعلية متوافقة مع أحدث المعايير</p>
                    </div>
                </div>
                <div class="ed-feat-item">
                    <div class="ed-feat-icon"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <strong>تقييم وتحليل ذكي</strong>
                        <p>امتحانات دورية وقياس مباشر لمستوى التقدم والتحصيل</p>
                    </div>
                </div>
                <div class="ed-feat-item">
                    <div class="ed-feat-icon"><i class="fas fa-certificate"></i></div>
                    <div>
                        <strong>شهادات تميز رسمية</strong>
                        <p>توثيق معتمد لإنجازاتك واجتيازك للمراحل التعليمية</p>
                    </div>
                </div>
            </div>

            <div class="ed-banner-footer">
                <span class="ed-country-tag">
                    <span class="flag-icon">🇵🇸</span> منصة فلسطينية لخدمة طلبة الوطن
                </span>
            </div>
        </div>
    </div>

    <!-- الجانب الأيمن: بطاقة تسجيل الدخول -->
    <div class="ed-login-form-area">
        <div class="ed-login-card">
            
            <!-- الشعار ورأس الصفحة -->
            <div class="ed-login-header">
                <a href="{{ url('/') }}" class="ed-login-brand">
                    <span class="ed-brand-logo-sq"><i class="fas fa-book-reader"></i></span>
                    <div class="ed-brand-text">
                        <strong>{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}</strong>
                        <small>بوابة الدخول الموحدة</small>
                    </div>
                </a>
            </div>

            <!-- مبدل الأدوار الأكاديمي الهادئ -->
            <div class="ed-role-selector">
                <button type="button" class="ed-role-btn active" data-role="student" onclick="switchRole('student')">
                    <i class="fas fa-user-graduate"></i>
                    <span>طالب</span>
                </button>
                <button type="button" class="ed-role-btn" data-role="teacher" onclick="switchRole('teacher')">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>مدرس</span>
                </button>
                <button type="button" class="ed-role-btn" data-role="admin" onclick="switchRole('admin')">
                    <i class="fas fa-shield-alt"></i>
                    <span>إدارة</span>
                </button>
            </div>

            <div class="ed-form-heading">
                <h2 id="role_heading_title">تسجيل دخول الطلبة</h2>
                <p id="role_heading_subtitle">أدخل بيانات حسابك للمتابعة إلى صفحتك الدراسية</p>
            </div>

            @if($errors->has('error'))
                <div class="ed-login-alert error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first('error') }}</span>
                </div>
            @endif

            @if(session('status'))
                <div class="ed-login-alert success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- نموذج الدخول -->
            <form action="{{ route('login.post') }}" method="POST" class="ed-auth-form" autocomplete="on">
                @csrf
                <input type="hidden" name="role" id="role_id" value="{{ old('role', 'student') }}">

                <div class="ed-input-group">
                    <label for="email_field">البريد الإلكتروني أو اسم المستخدم</label>
                    <div class="ed-input-wrapper">
                        <i class="far fa-envelope ed-input-icon"></i>
                        <input 
                            type="email" 
                            id="email_field"
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="student@example.com" 
                            required 
                            autofocus 
                        />
                    </div>
                </div>

                <div class="ed-input-group">
                    <div class="ed-label-flex">
                        <label for="password_field">كلمة المرور</label>
                    </div>
                    <div class="ed-input-wrapper">
                        <i class="fas fa-lock ed-input-icon"></i>
                        <input 
                            type="password" 
                            id="password_field"
                            name="password" 
                            placeholder="••••••••" 
                            required 
                        />
                        <button type="button" class="ed-toggle-pwd" onclick="togglePasswordVisibility()" title="إظهار/إخفاء كلمة المرور">
                            <i class="far fa-eye" id="pwd_eye_icon"></i>
                        </button>
                    </div>
                </div>

                <div class="ed-form-options">
                    <label class="ed-remember-me">
                        <input type="checkbox" name="remember" value="1">
                        <span>تذكر بياناتي في هذا المتصفح</span>
                    </label>
                </div>

                <button type="submit" id="submit_btn" class="ed-submit-btn">
                    <span>تسجيل الدخول</span>
                    <i class="fas fa-arrow-left"></i>
                </button>
            </form>

            <div class="ed-form-footer" id="student_reg_section">
                <span>ليس لديك حساب بعد؟</span>
                <a href="{{ route('students.create') }}" class="ed-register-link">إنشاء حساب طالب جديد</a>
            </div>

            <div class="ed-back-home">
                <a href="{{ url('/') }}"><i class="fas fa-long-arrow-alt-right"></i> العودة للصفحة الرئيسية للمنصة</a>
            </div>

        </div>
    </div>
</div>

<style>
    .ed-login-container {
        display: flex;
        min-height: 100vh;
        background: #f8fafc;
        direction: rtl;
        font-family: 'Alexandria', 'Tajawal', sans-serif;
    }

    /* Left Visual Academic Banner */
    .ed-login-banner {
        flex: 1.1;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1e40af 100%);
        position: relative;
        display: flex;
        align-items: center;
        padding: 60px;
        color: #ffffff;
        overflow: hidden;
    }

    .ed-banner-overlay {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.25), transparent 50%),
                    radial-gradient(circle at 20% 80%, rgba(30, 64, 175, 0.4), transparent 50%);
        pointer-events: none;
    }

    .ed-banner-content {
        position: relative;
        z-index: 2;
        max-width: 520px;
    }

    .ed-banner-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 24px;
        backdrop-filter: blur(8px);
    }

    .ed-banner-title {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1.35;
        margin-bottom: 16px;
        letter-spacing: -0.02em;
    }

    .ed-banner-desc {
        font-size: 1rem;
        line-height: 1.8;
        color: #cbd5e1;
        margin-bottom: 40px;
    }

    .ed-banner-features {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-bottom: 45px;
    }

    .ed-feat-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .ed-feat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        color: #93c5fd;
        flex-shrink: 0;
    }

    .ed-feat-item strong {
        display: block;
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 2px;
        color: #f1f5f9;
    }

    .ed-feat-item p {
        margin: 0;
        font-size: 0.82rem;
        color: #94a3b8;
        line-height: 1.5;
    }

    .ed-country-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #cbd5e1;
        background: rgba(15, 23, 42, 0.4);
        padding: 6px 14px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    /* Right Login Area */
    .ed-login-form-area {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 24px;
    }

    .ed-login-card {
        width: 100%;
        max-width: 440px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 40px 36px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    .ed-login-header {
        margin-bottom: 24px;
    }

    .ed-login-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: #0f172a;
    }

    .ed-brand-logo-sq {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #eff6ff;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .ed-brand-text strong {
        display: block;
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .ed-brand-text small {
        font-size: 0.8rem;
        color: #64748b;
    }

    /* Role Selector Pills */
    .ed-role-selector {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
        background: #f1f5f9;
        padding: 5px;
        border-radius: 14px;
        margin-bottom: 24px;
    }

    .ed-role-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px 6px;
        background: transparent;
        border: none;
        border-radius: 10px;
        font-family: inherit;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .ed-role-btn.active {
        background: #ffffff;
        color: #1d4ed8;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        font-weight: 700;
    }

    .ed-form-heading {
        margin-bottom: 24px;
    }

    .ed-form-heading h2 {
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .ed-form-heading p {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }

    .ed-login-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .ed-login-alert.error {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fee2e2;
    }

    .ed-login-alert.success {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #dcfce7;
    }

    .ed-auth-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .ed-input-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .ed-input-group label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
    }

    .ed-label-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ed-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .ed-input-icon {
        position: absolute;
        right: 14px;
        color: #94a3b8;
        font-size: 1rem;
        pointer-events: none;
    }

    .ed-input-wrapper input {
        width: 100%;
        padding: 12px 40px 12px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        font-family: inherit;
        font-size: 0.95rem;
        color: #0f172a;
        transition: all 0.2s ease;
        outline: none;
    }

    .ed-input-wrapper input:focus {
        border-color: #1d4ed8;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.1);
    }

    .ed-toggle-pwd {
        position: absolute;
        left: 12px;
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        font-size: 1rem;
    }

    .ed-toggle-pwd:hover {
        color: #334155;
    }

    .ed-form-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.82rem;
    }

    .ed-remember-me {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        cursor: pointer;
        font-weight: 500;
    }

    .ed-remember-me input {
        accent-color: #1d4ed8;
        cursor: pointer;
        width: 16px;
        height: 16px;
    }

    .ed-submit-btn {
        width: 100%;
        padding: 13px;
        background: #1d4ed8;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-family: inherit;
        font-size: 0.98rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.2s ease;
        margin-top: 6px;
    }

    .ed-submit-btn:hover {
        background: #1e40af;
        box-shadow: 0 4px 14px rgba(29, 78, 216, 0.25);
    }

    .ed-form-footer {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
        text-align: center;
        font-size: 0.875rem;
        color: #64748b;
    }

    .ed-register-link {
        color: #1d4ed8;
        font-weight: 700;
        text-decoration: none;
        margin-right: 4px;
    }

    .ed-register-link:hover {
        text-decoration: underline;
    }

    .ed-back-home {
        text-align: center;
        margin-top: 16px;
    }

    .ed-back-home a {
        font-size: 0.82rem;
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .ed-back-home a:hover {
        color: #475569;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .ed-login-banner {
            display: none;
        }
        .ed-login-form-area {
            padding: 30px 16px;
        }
        .ed-login-card {
            padding: 30px 22px;
            box-shadow: none;
            border-color: #e2e8f0;
        }
    }
</style>

<script>
    function switchRole(role) {
        // Active role pills
        document.querySelectorAll('.ed-role-btn').forEach(btn => btn.classList.remove('active'));
        const activeBtn = document.querySelector(`.ed-role-btn[data-role="${role}"]`);
        if (activeBtn) activeBtn.classList.add('active');

        // Form role input
        document.getElementById('role_id').value = role;

        // Visual labels
        const title = document.getElementById('role_heading_title');
        const subtitle = document.getElementById('role_heading_subtitle');
        const regSection = document.getElementById('student_reg_section');
        const emailInput = document.getElementById('email_field');
        const submitBtn = document.getElementById('submit_btn');

        if (role === 'student') {
            title.innerText = 'تسجيل دخول الطلبة';
            subtitle.innerText = 'أدخل بيانات حسابك للمتابعة إلى صفحتك الدراسية';
            emailInput.placeholder = 'student@example.com';
            regSection.style.display = 'block';
            submitBtn.style.background = '#1d4ed8';
        } else if (role === 'teacher') {
            title.innerText = 'تسجيل دخول المعلمين';
            subtitle.innerText = 'بوابة المعلمين لإدارة المواد والامتحانات ومتابعة الطلبة';
            emailInput.placeholder = 'teacher@menaret-tawjihi.ps';
            regSection.style.display = 'none';
            submitBtn.style.background = '#0284c7';
        } else if (role === 'admin') {
            title.innerText = 'بوابة الإدارة الأكاديمية';
            subtitle.innerText = 'لوحة التحكم المركزية وإدارة إعدادات المنصة';
            emailInput.placeholder = 'admin@menaret-tawjihi.ps';
            regSection.style.display = 'none';
            submitBtn.style.background = '#0f172a';
        }
    }

    function togglePasswordVisibility() {
        const input = document.getElementById('password_field');
        const icon = document.getElementById('pwd_eye_icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialRole = document.getElementById('role_id').value || 'student';
        switchRole(initialRole);
    });
</script>
@endsection
