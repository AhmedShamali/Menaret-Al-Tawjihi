@extends('layouts.app')

@section('title', 'مركز التحكم بالهوية')

@section('content')
<div style="max-width: 1300px; margin: 0 auto; animation: fadeIn 0.8s ease;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 2px solid #e2e8f0; padding-bottom: 30px;">
        <div>
            <h1 style="font-size: 2.8rem; font-weight: 900; color: #0f172a; letter-spacing: -2px;">إعدادات الهوية المركزية ⚙️</h1>
            <p style="color: #64748b; font-size: 1.1rem; margin-top: 10px;">تحكم في العلامة التجارية للمنصة وبيانات التواصل الرسمية.</p>
        </div>
        <div class="system-badge">
            <span class="pulse-green"></span>
            نظام التحكم جاهز
        </div>
    </div>

    <form id="brandForm">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 400px; gap: 40px; align-items: start;">

            <div style="display: flex; flex-direction: column; gap: 30px;">
                <!-- بطاقة تسمية المنصة -->
                <div class="grand-card">
                    <div class="grand-header"><i class="fa-solid fa-signature"></i> <h3>تسمية وعنونة المنصة</h3></div>
                    <div style="padding: 40px;">
                        <label class="grand-label">اسم الموقع (يظهر في كل الصفحات)</label>
                        <input type="text" name="site_name" id="name_input"
                               value="{{ \App\Models\Setting::get('site_name', 'جسر') }}"
                               class="grand-input" oninput="livePreview(this.value)">
                        <p style="margin-top: 15px; font-size: 0.8rem; color: #94a3b8;">* سيتم استخدام أول حرف من هذا الاسم كشعار (Icon) تلقائي للمنصة.</p>
                    </div>
                </div>

                <!-- بطاقة الدعم والتواصل -->
                <div class="grand-card">
                    <div class="grand-header" style="--bg: #3b82f6;"><i class="fa-solid fa-headset"></i> <h3>قنوات التواصل والدعم</h3></div>
                    <div style="padding: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                        <div>
                            <label class="grand-label">البريد الإلكتروني الرسمي</label>
                            <input type="email" name="contact_email" value="{{ \App\Models\Setting::get('contact_email') }}" class="grand-input">
                        </div>
                        <div>
                            <label class="grand-label">رقم الواتساب (الإشعارات)</label>
                            <input type="text" name="contact_whatsapp" value="{{ \App\Models\Setting::get('contact_whatsapp') }}" class="grand-input">
                        </div>
                    </div>
                </div>
            </div>

            <!-- الجانب الأيسر: المعاينة الذكية + الحفظ -->
            <aside style="display: flex; flex-direction: column; gap: 25px; position: sticky; top: 110px;">

                <!-- معاينة الشعار الذكي -->
                <div class="grand-card dark-preview">
                    <span class="preview-tag">المعاينة الحية للهوية</span>
                    <div class="dynamic-logo-wrapper">
                        <div class="dynamic-logo-box" id="logo_letter">
                            {{ mb_substr(\App\Models\Setting::get('site_name', 'ج'), 0, 1) }}
                        </div>
                        <div class="dynamic-logo-text">
                            <span id="logo_name_preview">{{ \App\Models\Setting::get('site_name', 'جسر') }}</span>
                            <small>بوابة التعلم الرقمي</small>
                        </div>
                    </div>
                </div>

                <!-- حالة التسجيل -->
                <div class="grand-card" style="padding: 30px;">
                    <label class="grand-label">بوابة التسجيل للطلاب</label>
                    <select name="registration_status" class="grand-select">
                        <option value="open" {{ \App\Models\Setting::get('registration_status') == 'open' ? 'selected' : '' }}>🟢 متاح للجميع</option>
                        <option value="closed" {{ \App\Models\Setting::get('registration_status') == 'closed' ? 'selected' : '' }}>🔴 مغلق للصيانة</option>
                    </select>
                </div>

                <button type="button" onclick="saveGrandSettings()" id="saveBtn" class="grand-btn">
                    <span>حفظ واعتماد الهوية</span>
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </button>
            </aside>

        </div>
    </form>
</div>

<style>
    .grand-card { background: white; border-radius: 35px; border: 1px solid #f1f5f9; box-shadow: 0 10px 40px rgba(0,0,0,0.02); overflow: hidden; }
    .grand-header { padding: 25px 40px; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px; }
    .grand-header i { color: var(--bg, var(--accent)); font-size: 1.2rem; }
    .grand-header h3 { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0; }

    .grand-label { display: block; font-size: 0.85rem; font-weight: 800; color: #64748b; margin-bottom: 12px; }
    .grand-input { width: 100%; padding: 18px 25px; border-radius: 20px; border: 2px solid #f1f5f9; background: #f8fafc; font-family: inherit; font-weight: 700; color: #0f172a; outline: none; transition: 0.3s; }
    .grand-input:focus { border-color: var(--accent); background: white; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.05); }

    .grand-select { width: 100%; padding: 18px; border-radius: 18px; border: 2px solid #e2e8f0; background: white; color: #0f172a; font-weight: 900; cursor: pointer; appearance: none; font-family: inherit; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%230f172a'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: left 20px center; background-size: 18px; }

    /* معاينة الشعار الذكي */
    .dark-preview { background: #0f172a; color: white; padding: 45px 35px; border: none; }
    .preview-tag { font-size: 0.7rem; font-weight: 800; opacity: 0.5; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 25px; display: block; }
    .dynamic-logo-wrapper { display: flex; align-items: center; gap: 20px; }
    .dynamic-logo-box { width: 65px; height: 65px; background: var(--accent); border-radius: 20px; display: grid; place-items: center; font-size: 2rem; font-weight: 900; color: white; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3); }
    .dynamic-logo-text span { display: block; font-size: 1.8rem; font-weight: 900; letter-spacing: -1px; }
    .dynamic-logo-text small { opacity: 0.5; font-weight: 600; }

    .grand-btn { width: 100%; padding: 25px; border-radius: 25px; background: var(--primary); color: white; border: none; font-size: 1.2rem; font-weight: 900; cursor: pointer; transition: 0.3s; display: flex; justify-content: center; align-items: center; gap: 15px; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.2); }
    .grand-btn:hover { transform: translateY(-5px); filter: brightness(1.2); }

    .system-badge { background: #ecfdf5; color: #059669; padding: 12px 25px; border-radius: 50px; font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; gap: 10px; border: 1px solid #bbf7d0; }
    .pulse-green { width: 10px; height: 10px; background: #10b981; border-radius: 50%; animation: blink 1.5s infinite; }
.grand-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 14px 32px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff;
    font-size: 1rem;
    font-weight: 700;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    overflow: hidden;
    box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.grand-btn span {
    z-index: 1;
}

.grand-btn i {
    z-index: 1;
    font-size: 1.15rem;
    transition: transform 0.3s ease;
}

/* تأثير التمرير (Hover) */
.grand-btn:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    transform: translateY(-3px);
    box-shadow: 0 15px 25px -5px rgba(37, 99, 235, 0.5);
}

.grand-btn:hover i {
    transform: translateY(-3px); /* ارتفاع الأيقونة للأعلى إيحاءً بالرفع للشفرة (Upload) */
}

/* تأثير الضغط (Click) */
.grand-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 5px 12px -3px rgba(37, 99, 235, 0.4);
}
    </style>

<script>
    // دالة المعاينة الحية واستخراج أول حرف
    function livePreview(val) {
        if (!val) val = "جسر";
        document.getElementById('logo_name_preview').innerText = val;
        document.getElementById('logo_letter').innerText = val.charAt(0);
    }

    function saveGrandSettings() {
        const btn = document.getElementById('saveBtn');
        const form = document.getElementById('brandForm');

        btn.disabled = true;
        btn.innerHTML = 'جاري المزامنة... ⏳';

        axios.post("{{ route('admin.settings.update') }}", new FormData(form))
        .then(res => {
            if(res.data.success) {
                Swal.fire({ icon: 'success', title: 'تم الحفظ!', text: res.data.title })
                .then(() => location.reload());
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('خطأ!', 'فشل في الاتصال بالسيرفر، تأكد من الكنترولر والراوت.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<span>حفظ واعتماد الهوية</span><i class="fa-solid fa-cloud-arrow-up"></i>';
        });
    }
</script>
@endsection
