@extends('layouts.app')

@section('content')
<div class="auth-master-wrapper">

    <!-- الجانب الأيسر: الصورة والرسالة الترحيبية -->
    <div class="auth-side-panel">
        <div class="brand-overlay"></div>
        <div class="brand-content">
            <div class="v-logo">J</div>
            <h2>{{ env('PLATFORM_NAME', 'منصة جسر التعليمية') }}</h2>
            <p>نحو مستقبل تعليمي رقمي متطور يجمع بين الطالب والمعلم في بيئة تفاعلية متكاملة.</p>
            <div class="pal-badge">🇵🇸 صنع في فلسطين</div>
        </div>
    </div>

    <!-- الجانب الأيمن: نموذج الدخول -->
    <div class="auth-form-panel">
        <div class="form-container">

            <!-- مبدل الأدوار الذكي -->
            <div class="role-nav">
                <div class="role-item active" data-role="student" onclick="switchUI('student')">
                    <span class="emoji">👨‍🎓</span> طالب
                </div>
                <div class="role-item" data-role="teacher" onclick="switchUI('teacher')">
                    <span class="emoji">👨‍🏫</span> مدرس
                </div>
                <div class="role-item" data-role="admin" onclick="switchUI('admin')">
                    <span class="emoji">⚙️</span> مدير
                </div>
            </div>

            <!-- صندوق تسجيل الدخول -->
            <div id="login_box" class="animated-box">
                <h1 id="dynamic_title" class="alexandria">دخول الطالب</h1>
                <p class="subtitle">يرجى إدخال بياناتك الرسمية للمتابعة</p>

                @if($errors->has('error'))
                    <div class="error-alert">
                        <i class="fas fa-exclamation-circle"></i> {{ $errors->first('error') }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" id="role_id" value="{{ old('role', 'student') }}">

                    <div class="input-field">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="name@jesr.ps" required autofocus>
                    </div>

                    <div class="input-field">
                        <label>كلمة المرور</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>

                    <button type="submit" id="action_btn" class="main-auth-btn">
                        <span>دخول للمنصة</span>
                        <span class="rocket-icon">🚀</span>
                    </button>
                </form>

                <div class="form-links">
                    <span id="reg_text">جديد هنا؟ <a href="{{ route('students.create') }}" class="create-acc">أنشئ حسابك</a></span>
                </div>
            </div>


        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;600;700;800&display=swap');

    :root {
        --student-color: #10b981;
        --teacher-color: #1e3a2b;
        --admin-color: #0f172a;
        --text-dark: #1e293b;
        --text-muted: #64748b;
    }

    * { box-sizing: border-box; }

    .auth-master-wrapper {
        display: flex;
        height: 100vh;
        background: #fff;
        font-family: 'Alexandria', sans-serif;
        overflow: hidden;
        direction: rtl;
    }

    /* الجانب البصري - مخفي في الجوال */
    .auth-side-panel {
        flex: 1;
        background: url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2070') center/cover;
        position: relative;
        display: flex;
        align-items: center;
        padding: 80px;
    }
    @media (max-width: 992px) { .auth-side-panel { display: none; } }

    .brand-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.9), rgba(15, 23, 42, 0.95));
    }
    .brand-content { position: relative; z-index: 2; color: white; }
    .v-logo {
        width: 70px; height: 70px; background: white; color: var(--student-color);
        border-radius: 20px; display: grid; place-items: center;
        font-size: 2.2rem; font-weight: 900; margin-bottom: 30px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .brand-content h2 { font-size: 2.8rem; font-weight: 800; margin-bottom: 20px; }
    .brand-content p { font-size: 1.1rem; line-height: 1.8; opacity: 0.9; max-width: 480px; }
    .pal-badge { margin-top: 40px; font-weight: 600; background: rgba(255,255,255,0.15); padding: 8px 20px; border-radius: 50px; display: inline-block; font-size: 0.85rem; border: 1px solid rgba(255,255,255,0.2); }

    /* الجانب العملي */
    .auth-form-panel {
        flex: 1.1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        background: #fdfdfd;
    }
    .form-container { width: 100%; max-width: 420px; }

    /* مبدل الأدوار */
    .role-nav {
        display: flex;
        gap: 8px;
        background: #f1f5f9;
        padding: 6px;
        border-radius: 20px;
        margin-bottom: 40px;
    }
    .role-item {
        flex: 1;
        padding: 14px;
        text-align: center;
        border-radius: 16px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted);
        transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .role-item.active {
        background: white;
        color: var(--text-dark);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
    }
    .role-item .emoji { font-size: 1.1rem; }

    h1 { font-size: 2.2rem; color: #0f172a; margin-bottom: 10px; font-weight: 800; }
    .subtitle { color: var(--text-muted); margin-bottom: 35px; font-size: 0.95rem; }

    /* الحقول */
    .input-field { margin-bottom: 25px; }
    .input-field label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 10px;
    }
    .input-field input {
        width: 100%;
        padding: 16px 22px;
        border-radius: 16px;
        border: 2px solid #eef2f6;
        background: #f8fafc;
        transition: 0.3s;
        font-family: inherit;
        font-weight: 600;
        outline: none;
        font-size: 1rem;
    }
    .input-field input:focus {
        border-color: var(--student-color);
        background: #fff;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.1);
    }

    /* الزر الرئيسي */
    .main-auth-btn {
        width: 100%;
        padding: 18px;
        border-radius: 18px;
        background: var(--student-color);
        color: white;
        border: none;
        font-weight: 800;
        font-size: 1.1rem;
        cursor: pointer;
        transition: 0.4s;
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }
    .main-auth-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 30px rgba(16, 185, 129, 0.2);
        filter: brightness(1.05);
    }
    .main-auth-btn:active { transform: translateY(0); }

    /* الروابط */
    .form-links { display: flex; justify-content: space-between; margin-top: 30px; font-size: 0.9rem; }
    .forgot-link { color: var(--text-muted); text-decoration: none; transition: 0.3s; }
    .forgot-link:hover { color: var(--student-color); }
    .create-acc { color: var(--student-color); text-decoration: none; font-weight: 800; margin-right: 5px; }
    .create-acc:hover { text-decoration: underline; }

    .error-alert {
        padding: 15px;
        background: #fff1f2;
        color: #be123c;
        border-radius: 14px;
        margin-bottom: 25px;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid #ffe4e6;
    }

    .back-link {
        display: block;
        text-align: center;
        margin-top: 25px;
        color: var(--text-muted) !important;
        text-decoration: none !important;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .back-link:hover { color: var(--text-dark) !important; }

    /* الانيميشن */
    .animated-box { animation: slideUp 0.6s cubic-bezier(0.23, 1, 0.32, 1); }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function switchUI(role) {
        // 1. تحديث شكل التبويب النشط
        document.querySelectorAll('.role-item').forEach(el => el.classList.remove('active'));
        const targetTab = document.querySelector(`[data-role="${role}"]`);
        if (targetTab) targetTab.classList.add('active');

        // 2. تحديث القيمة في الفورم
        document.getElementById('role_id').value = role;

        // 3. تحديث العناصر البصرية (عناوين وألوان)
        const title = document.getElementById('dynamic_title');
        const btn = document.getElementById('action_btn');
        const regText = document.getElementById('reg_text');

        // تغيير لون الحقول عند التركيز بناءً على الدور
        const inputs = document.querySelectorAll('.input-field input');

        if(role === 'student') {
            title.innerText = "دخول الطالب";
            btn.style.background = "var(--student-color)";
            regText.style.display = "inline";
            updateFocusColor("#10b981");
        } else if(role === 'teacher') {
            title.innerText = "دخول المدرس";
            btn.style.background = "var(--teacher-color)";
            regText.style.display = "none";
            updateFocusColor("#1e3a2b");
        } else if(role === 'admin') {
            title.innerText = "بوابة المدير";
            btn.style.background = "var(--admin-color)";
            regText.style.display = "none";
            updateFocusColor("#0f172a");
        }
    }

    function updateFocusColor(color) {
        const style = document.createElement('style');
        style.innerHTML = `.input-field input:focus { border-color: ${color} !important; box-shadow: 0 10px 25px ${color}20 !important; }`;
        document.head.appendChild(style);
    }

    function showSection(sec) {
        const loginBox = document.getElementById('login_box');
        const forgotBox = document.getElementById('forgot_box');

        if(sec === 'forgot') {
            loginBox.style.display = 'none';
            forgotBox.style.display = 'block';
        } else {
            loginBox.style.display = 'block';
            forgotBox.style.display = 'none';
        }
    }

    function requestOTP() {
        const nid = document.getElementById('nid_field').value;
        if(nid.length < 9) return Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى إدخال رقم هوية صحيح مكون من 9 أرقام', confirmButtonText: 'حسناً' });

        // يمكنك ربط هذا المسار مع الـ Controller الخاص بك
        axios.post("{{ route('password.forgot') }}", { nid: nid }).then(res => {
            Swal.fire({
                icon: res.data.success ? 'success' : 'error',
                title: res.data.success ? 'تم الإرسال' : 'عذراً',
                text: res.data.message,
                confirmButtonColor: '#10b981'
            });
        }).catch(err => {
            Swal.fire('خطأ', 'حدث خطأ غير متوقع، يرجى المحاولة لاحقاً.', 'error');
        });
    }

    // تشغيل الحالة الافتراضية عند تحميل الصفحة
    document.addEventListener("DOMContentLoaded", function() {
        const currentRole = document.getElementById('role_id').value || 'student';
        switchUI(currentRole);
    });
</script>
@endsection
