@extends('layouts.app')

@section('title', 'إعدادات المنصة والهوية وبيانات الدفع')

@section('content')
<div style="max-width: 1300px; margin: 0 auto; padding: 10px 0 40px; animation: fadeIn 0.5s ease;" dir="rtl">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 900; color: #0f172a; margin: 0 0 6px;">إعدادات المنصة وهوية الدفع المركزية ⚙️</h1>
            <p style="color: #64748b; font-size: 1rem; margin: 0;">تحكم باسم المنصة، الشعار، بيانات بوابات الدفع الفلسطينية، وأرقام التواصل الرسمية.</p>
        </div>
        <div class="system-badge" style="background: #ecfdf5; color: #059669; padding: 10px 20px; border-radius: 50px; font-weight: 800; font-size: 0.85rem; display: flex; align-items: center; gap: 10px; border: 1px solid #bbf7d0;">
            <span style="width: 10px; height: 10px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
            المزامنة اللحظية مفعلة
        </div>
    </div>

    <form id="brandForm">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 380px; gap: 30px; align-items: start;">

            <div style="display: flex; flex-direction: column; gap: 25px;">
                
                <!-- 0. بطاقة رفع شعار المنصة وأيقونة المتصفح -->
                <div class="settings-card">
                    <div class="settings-card-header" style="background: #f8fafc; border-bottom: 1px solid #f1f5f9; padding: 18px 25px; display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-image" style="color: #6366f1; font-size: 1.2rem;"></i>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">شعار المنصة الرسمي وأيقونة المتصفح (Logo & Favicon)</h3>
                    </div>
                    <div style="padding: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                        
                        <!-- شعار المنصة الرئيسي -->
                        <div>
                            <label class="field-label">شعار المنصة الرئيسي (يظهر في الهيدر، السايدبار، وصولات الدفع، والشهادات)</label>
                            <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 12px;">
                                <div id="current_logo_preview_box" style="width: 85px; height: 85px; border-radius: 16px; border: 2px dashed #cbd5e1; display: grid; place-items: center; overflow: hidden; background: #f8fafc; flex-shrink: 0;">
                                    @if(\App\Models\Setting::get('site_logo'))
                                        <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" id="logo_img_preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    @else
                                        <span id="logo_placeholder" style="font-size: 2.2rem;">🏛️</span>
                                        <img id="logo_img_preview" style="max-width: 100%; max-height: 100%; object-fit: contain; display: none;">
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" name="site_logo" id="site_logo_input" accept="image/*" class="field-input" onchange="previewLogoFile(this)" style="padding: 8px;">
                                    <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">يدعم PNG, JPG, SVG, WebP (يفضل خلفية شفافة مقاس مربع أو مستطيل).</small>
                                </div>
                            </div>
                            @if(\App\Models\Setting::get('site_logo'))
                                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.8rem; color: #ef4444; cursor: pointer;">
                                    <input type="checkbox" name="remove_logo" value="1">
                                    <span>حذف الشعار الحالي واستعادة الشعار الرمزي الافتراضي</span>
                                </label>
                            @endif
                        </div>

                        <!-- أيقونة المتصفح Favicon -->
                        <div>
                            <label class="field-label">أيقونة المتصفح المصغرة (Favicon - تظهر في شريط التبويب)</label>
                            <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 12px;">
                                <div id="current_fav_preview_box" style="width: 65px; height: 65px; border-radius: 14px; border: 2px dashed #cbd5e1; display: grid; place-items: center; overflow: hidden; background: #f8fafc; flex-shrink: 0;">
                                    @if(\App\Models\Setting::get('site_favicon'))
                                        <img src="{{ asset(\App\Models\Setting::get('site_favicon')) }}" id="fav_img_preview" style="width: 36px; height: 36px; object-fit: contain;">
                                    @else
                                        <span id="fav_placeholder" style="font-size: 1.8rem;">🇵🇸</span>
                                        <img id="fav_img_preview" style="width: 36px; height: 36px; object-fit: contain; display: none;">
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" name="site_favicon" id="site_fav_input" accept="image/*,.ico" class="field-input" onchange="previewFavFile(this)" style="padding: 8px;">
                                    <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">صيغة ICO أو PNG مقاس 32x32 أو 64x64 بكسل.</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 1. بطاقة تسمية وهوية المنصة -->
                <div class="settings-card">
                    <div class="settings-card-header" style="background: #f8fafc; border-bottom: 1px solid #f1f5f9; padding: 18px 25px; display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-signature" style="color: #0284c7; font-size: 1.2rem;"></i>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">تسمية وعنونة المنصة (تتغير تلقائياً في كل الصفحات)</h3>
                    </div>
                    <div style="padding: 25px; display: flex; flex-direction: column; gap: 20px;">
                        <div>
                            <label class="field-label">اسم المنصة الرسمي (يظهر في الشريط العلوي والعناوين والشهادات)</label>
                            <input type="text" name="site_name" id="name_input"
                                   value="{{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}"
                                   class="field-input" oninput="livePreview(this.value)">
                            <small style="color: #94a3b8; font-size: 0.78rem; display: block; margin-top: 6px;">* يتغير فورياً في شريط المتصفح، الفواتير، الإيصالات، ولوحة الطلاب والمعلمين.</small>
                        </div>

                        <div>
                            <label class="field-label">شعار ووصف المنصة (Slogan)</label>
                            <input type="text" name="site_slogan" id="slogan_input"
                                   value="{{ \App\Models\Setting::get('site_slogan', 'المنصة الوطنية الرائدة لطلبة الثانوية العامة في فلسطين 🇵🇸') }}"
                                   class="field-input" oninput="document.getElementById('slogan_preview').innerText = this.value">
                        </div>
                    </div>
                </div>

                <!-- 2. بطاقة بوابات الدفع وحساب التحويل الفلسطيني -->
                <div class="settings-card">
                    <div class="settings-card-header" style="background: #f8fafc; border-bottom: 1px solid #f1f5f9; padding: 18px 25px; display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-wallet" style="color: #059669; font-size: 1.2rem;"></i>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">بيانات الدفع والتحويل الفلسطيني المعتمدة 🇵🇸</h3>
                    </div>
                    <div style="padding: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div style="grid-column: span 2;">
                            <label class="field-label">اسم صاحب الحساب المستفيد المعتمد (الاسم الثلاثي)</label>
                            <input type="text" name="payment_account_name" id="owner_input"
                                   value="{{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}"
                                   class="field-input" oninput="document.getElementById('owner_preview').innerText = this.value">
                            <small style="color: #64748b; font-size: 0.78rem; display: block; margin-top: 4px;">يظهر في صفحة السداد والإيصالات الرسمية لتأكيد التحويل البنكي وجوال باي.</small>
                        </div>

                        <div>
                            <label class="field-label">رقم محفظة التحويل (جوال باي / أوريدو)</label>
                            <input type="text" name="payment_phone" id="phone_input"
                                   value="{{ \App\Models\Setting::get('payment_phone', '0567897212') }}"
                                   class="field-input" style="direction: ltr; text-align: right;"
                                   oninput="document.getElementById('phone_preview').innerText = this.value">
                        </div>

                        <div>
                            <label class="field-label">رمز خدمة بال باي (PalPay Service Code)</label>
                            <input type="text" name="palpay_service_code"
                                   value="{{ \App\Models\Setting::get('palpay_service_code', '99420') }}"
                                   class="field-input" style="direction: ltr; text-align: right;">
                        </div>

                        <div>
                            <label class="field-label">اسم البنك المعتمد</label>
                            <input type="text" name="payment_bank_name"
                                   value="{{ \App\Models\Setting::get('payment_bank_name', 'بنك فلسطين') }}"
                                   class="field-input">
                        </div>

                        <div>
                            <label class="field-label">رقم حساب بنك فلسطين</label>
                            <input type="text" name="payment_account_no"
                                   value="{{ \App\Models\Setting::get('payment_account_no', '2275913') }}"
                                   class="field-input" style="direction: ltr; text-align: right; font-weight: 700; font-family: monospace;">
                        </div>
                    </div>
                </div>

                <!-- 3. بطاقة قنوات التواصل والدعم -->
                <div class="settings-card">
                    <div class="settings-card-header" style="background: #f8fafc; border-bottom: 1px solid #f1f5f9; padding: 18px 25px; display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-headset" style="color: #6366f1; font-size: 1.2rem;"></i>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">قنوات التواصل والدعم الفني</h3>
                    </div>
                    <div style="padding: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label class="field-label">رقم الواتساب الرسمي (للتواصل مع الطلاب)</label>
                            <input type="text" name="contact_whatsapp"
                                   value="{{ \App\Models\Setting::get('contact_whatsapp', '00970597694385') }}"
                                   class="field-input" style="direction: ltr; text-align: right;">
                        </div>
                        <div>
                            <label class="field-label">البريد الإلكتروني المعتمد</label>
                            <input type="email" name="contact_email"
                                   value="{{ \App\Models\Setting::get('contact_email', 'support@tawjihi.ps') }}"
                                   class="field-input" style="direction: ltr; text-align: right;">
                        </div>
                    </div>
                </div>

            </div>

            <!-- الجانب الأيسر: المعاينة الذكية + زر الحفظ -->
            <aside style="display: flex; flex-direction: column; gap: 20px; position: sticky; top: 90px;">

                <!-- بطاقة المعاينة الفورية للهوية -->
                <div class="settings-card" style="background: #0f172a; color: white; padding: 25px; border: none;">
                    <span style="font-size: 0.72rem; font-weight: 800; opacity: 0.6; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 15px;">
                        المعاينة الحية الفورية 👁️
                    </span>

                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 18px;">
                        <div id="logo_letter" style="width: 54px; height: 54px; background: linear-gradient(135deg, #0284c7, #0369a1); border-radius: 16px; display: grid; place-items: center; font-size: 1.6rem; font-weight: 900; color: white; box-shadow: 0 8px 20px rgba(2, 132, 199, 0.4); overflow: hidden; padding: 3px;">
                            @if(\App\Models\Setting::get('site_logo'))
                                <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" id="sidebar_preview_logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 12px; background: white; padding: 2px;">
                                <span id="sidebar_preview_letter" style="display: none;">{{ mb_substr(\App\Models\Setting::get('site_name', 'منارة التوجيهي'), 0, 1) }}</span>
                            @else
                                <span id="sidebar_preview_letter">{{ mb_substr(\App\Models\Setting::get('site_name', 'منارة التوجيهي'), 0, 1) }}</span>
                                <img id="sidebar_preview_logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 12px; background: white; padding: 2px; display: none;">
                            @endif
                        </div>
                        <div>
                            <h3 id="logo_name_preview" style="font-size: 1.25rem; font-weight: 900; margin: 0; color: #f8fafc;">
                                {{ \App\Models\Setting::get('site_name', 'منارة التوجيهي') }}
                            </h3>
                            <small id="slogan_preview" style="color: #94a3b8; font-size: 0.78rem; display: block; margin-top: 2px;">
                                {{ \App\Models\Setting::get('site_slogan', 'المنصة الوطنية الرائدة لطلبة الثانوية العامة في فلسطين 🇵🇸') }}
                            </small>
                        </div>
                    </div>

                    <div style="background: #1e293b; border-radius: 14px; padding: 12px; border: 1px solid #334155;">
                        <div style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 4px;">بيانات التحويل المسجلة حالياً:</div>
                        <div style="font-size: 0.88rem; font-weight: 800; color: #38bdf8;" id="owner_preview">
                            {{ \App\Models\Setting::get('payment_account_name', 'أحمد حسين شمالي') }}
                        </div>
                        <div style="font-family: monospace; font-size: 0.85rem; color: #10b981; direction: ltr; text-align: right;" id="phone_preview">
                            {{ \App\Models\Setting::get('payment_phone', '0567897212') }}
                        </div>
                    </div>
                </div>

                <!-- خيار حالة التسجيل -->
                <div class="settings-card" style="padding: 20px;">
                    <label class="field-label">حالة فتح بوابة تسجيل الطلاب الجدد</label>
                    <select name="registration_status" class="field-select">
                        <option value="open" {{ \App\Models\Setting::get('registration_status', 'open') == 'open' ? 'selected' : '' }}>🟢 متاح للجميع والتسجيل مفتوح</option>
                        <option value="closed" {{ \App\Models\Setting::get('registration_status') == 'closed' ? 'selected' : '' }}>🔴 مغلق مؤقتاً للصيانة</option>
                    </select>
                </div>

                <!-- زر حفظ الإعدادات واعتمادها -->
                <button type="button" onclick="savePlatformSettings()" id="saveBtn" style="width: 100%; padding: 18px; border-radius: 16px; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; border: none; font-size: 1.05rem; font-weight: 800; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(2, 132, 199, 0.35); transition: 0.2s;">
                    <span>حفظ واعتماد التغييرات</span>
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </button>

            </aside>

        </div>
    </form>
</div>

<style>
.settings-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 6px 20px rgba(0,0,0,0.02);
    overflow: hidden;
}

body.dark-theme .settings-card {
    background: #0f172a;
    border-color: #1e293b;
}

.field-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 8px;
}

body.dark-theme .field-label {
    color: #cbd5e1;
}

.field-input {
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1.5px solid #cbd5e1;
    background: #f8fafc;
    font-size: 0.92rem;
    font-weight: 600;
    color: #0f172a;
    outline: none;
    transition: 0.2s;
}

body.dark-theme .field-input {
    background: #1e293b;
    border-color: #334155;
    color: #f8fafc;
}

.field-input:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
}

.field-select {
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1.5px solid #cbd5e1;
    background: #f8fafc;
    font-size: 0.92rem;
    font-weight: 700;
    color: #0f172a;
    outline: none;
    cursor: pointer;
}

body.dark-theme .field-select {
    background: #1e293b;
    border-color: #334155;
    color: #f8fafc;
}
</style>

<script>
    function livePreview(val) {
        if (!val) val = "منارة التوجيهي";
        document.getElementById('logo_name_preview').innerText = val;
        const letterElem = document.getElementById('sidebar_preview_letter');
        if (letterElem) letterElem.innerText = val.charAt(0);
    }

    function previewLogoFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logo_img_preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
                const placeholder = document.getElementById('logo_placeholder');
                if (placeholder) placeholder.style.display = 'none';

                // أيضاً في كرت المعاينة الجانبي
                const sidePreview = document.getElementById('sidebar_preview_logo');
                if (sidePreview) {
                    sidePreview.src = e.target.result;
                    sidePreview.style.display = 'block';
                }
                const sideLetter = document.getElementById('sidebar_preview_letter');
                if (sideLetter) sideLetter.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewFavFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('fav_img_preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
                const placeholder = document.getElementById('fav_placeholder');
                if (placeholder) placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function savePlatformSettings() {
        const btn = document.getElementById('saveBtn');
        const form = document.getElementById('brandForm');

        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> جاري رفع الشعار وحفظ الهوية...`;

        axios.post("{{ route('admin.settings.update') }}", new FormData(form), {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(res => {
            if (res.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: res.data.title || 'تم تحديث الإعدادات بنجاح! 🎉',
                    text: 'تم تعميم الشعار وهوية المنصة وبيانات الدفع على كافة أرجاء النظام.',
                    confirmButtonText: 'ممتاز',
                    confirmButtonColor: '#0284c7'
                }).then(() => location.reload());
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'خطأ في الحفظ',
                text: err.response?.data?.message || 'حدث خطأ أثناء حفظ الإعدادات، يرجى إعادة المحاولة.'
            });
            btn.disabled = false;
            btn.innerHTML = `<span>حفظ واعتماد التغييرات</span> <i class="fa-solid fa-cloud-arrow-up"></i>`;
        });
    }
</script>
@endsection
