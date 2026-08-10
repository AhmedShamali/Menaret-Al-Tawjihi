@extends('layouts.app')

@section('title', 'إعدادات المنصة')

@section('content')
<div style="display: flex; flex-direction: column; gap: 35px; animation: fadeIn 0.6s ease;">

    <div>
        <h1 style="font-size: 2.5rem; font-weight: 900; color: var(--primary); letter-spacing: -1.5px;">إعدادات التحكم ⚙️</h1>
        <p style="color: #64748b;">تغيير هوية {{ \App\Models\Setting::get('site_name') }} وإدارة النظام بشكل كامل.</p>
    </div>

    <form id="settingsForm">
        @csrf
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px;">

            <div style="display: flex; flex-direction: column; gap: 25px;">
                <!-- بطاقة الهوية -->
                <div class="glass-card" style="padding: 40px; border: none;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 30px; color: var(--accent); border-right: 4px solid var(--accent); padding-right: 15px;">🏷️ هوية الموقع</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label class="f-label">اسم الموقع</label>
                            <input type="text" name="site_name" value="{{ $settings['site_name'] }}" class="f-input" style="font-weight: 800; font-size: 1.1rem;">
                        </div>
                        <div>
                            <label class="f-label">البريد الإلكتروني</label>
                            <input type="email" name="contact_email" value="{{ $settings['contact_email'] }}" class="f-input">
                        </div>
                    </div>
                </div>

                <!-- بطاقة التواصل -->
                <div class="glass-card" style="padding: 40px; border: none;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 30px; color: #3b82f6; border-right: 4px solid #3b82f6; padding-right: 15px;">📞 قنوات التواصل</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label class="f-label">رقم الواتساب</label>
                            <input type="text" name="contact_whatsapp" value="{{ $settings['contact_whatsapp'] }}" class="f-input">
                        </div>
                    </div>
                </div>
            </div>

            <!-- الأوامر الجانبية -->
            <aside>
                <div class="glass-card" style="padding: 30px; background: #f8fafc; border: 2px dashed #e2e8f0;">
                    <h3 style="margin-bottom: 15px; font-size: 1rem;">حالة التسجيل</h3>
                    <select name="registration_status" class="f-input" style="background: white;">
                        <option value="open" {{ $settings['registration_status'] == 'open' ? 'selected' : '' }}>🟢 متاح للطلاب</option>
                        <option value="closed" {{ $settings['registration_status'] == 'closed' ? 'selected' : '' }}>🔴 مغلق حالياً</option>
                    </select>
                </div>
                <button type="button" onclick="saveSettings()" id="saveBtn" class="btn-primary" style="width: 100%; margin-top: 25px; padding: 20px; font-size: 1.1rem; border-radius: 20px;">
                    حفظ التغييرات ✅
                </button>
            </aside>
        </div>
    </form>
</div>

<style>
    .f-label { display: block; font-weight: 700; font-size: 0.85rem; color: #475569; margin-bottom: 10px; }
    .f-input { width: 100%; padding: 15px; border-radius: 15px; border: 2px solid #f1f5f9; font-family: inherit; transition: 0.3s; background: #f8fafc; outline: none; }
    .f-input:focus { border-color: var(--accent); background: #fff; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
    function saveSettings() {
        const formData = new FormData(document.getElementById('settingsForm'));
        axios.post('/admin/settings/update', formData).then(res => {
            Swal.fire({ icon: 'success', title: 'تم التحديث بنجاح' }).then(() => location.reload());
        });
    }
</script>
@endsection
