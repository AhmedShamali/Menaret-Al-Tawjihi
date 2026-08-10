@extends('layouts.app')
@section('content')
<div style="max-width: 1200px; margin: 80px auto; display: grid; grid-template-columns: 1fr 1.2fr; gap: 80px;">
    <div>
        <h1 style="font-size: 3.5rem; font-weight: 900; margin-bottom: 30px;">تحدث معنا، <br><span style="color: var(--primary-blue);">نحن نسمعك</span></h1>
        <p style="color: #64748b; font-size: 1.2rem; line-height: 1.8;">سواء كنت طالباً يحتاج مساعدة أو مدرساً يود الانضمام، فريقنا جاهز للتواصل معك.</p>

        <div style="margin-top: 60px; display: grid; gap: 30px;">
            <div class="contact-box"><span>📍</span> <div><h4>الموقع</h4><p>فلسطين - قطاع غزة</p></div></div>
            <div class="contact-box" style="background: #ecfdf5;"><span>🟢</span> <div><h4>واتساب</h4><p dir="ltr">{{ \App\Models\Setting::get('contact_whatsapp') }}</p></div></div>
        </div>
    </div>

    <div class="glass-card" style="padding: 50px; background: white; border: none; box-shadow: 0 40px 100px rgba(0,0,0,0.05);">
        <form style="display: grid; gap: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <input type="text" placeholder="الاسم" class="u-input">
                <input type="email" placeholder="البريد" class="u-input">
            </div>
            <select class="u-input"><option>استفسار أكاديمي</option><option>مشكلة تقنية</option></select>
            <textarea placeholder="رسالتك..." rows="5" class="u-input"></textarea>
            <button class="btn-primary" style="width: 100%; border: none; padding: 20px;">إرسال الرسالة 🚀</button>
        </form>
    </div>
</div>

<style>
    .contact-box { display: flex; gap: 20px; padding: 25px; border-radius: 25px; background: #f8fafc; align-items: center; }
    .contact-box span { font-size: 2rem; }
    .contact-box h4 { margin-bottom: 5px; }
    .u-input { width: 100%; padding: 18px; border-radius: 15px; border: 2px solid #f1f5f9; background: #f8fafc; font-family: inherit; }
</style>
@endsection
