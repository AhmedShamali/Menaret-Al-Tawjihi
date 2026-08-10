@extends('layouts.app')

@section('content')
<div style="max-width: 1100px; margin: 0 auto; animation: fadeIn 0.8s ease;">

    <div style="text-align: center; margin-bottom: 60px;">
        <h1 style="font-size: 3rem; font-weight: 900; color: var(--primary);">مركز المساعدة والدعم 🎧</h1>
        <p style="color: #64748b; font-size: 1.2rem;">إجابات سريعة لاستفساراتك، وتواصل مباشر مع فريقنا التقني.</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 40px;">

        {{-- الأسئلة الشائعة --}}
        <div>
            <h3 style="margin-bottom: 25px;">الأسئلة الأكثر شيوعاً</h3>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <details class="glass-card" style="padding: 5px;">
                    <summary style="padding: 20px; font-weight: 700; cursor: pointer;">كيف يتم توثيق حسابي؟</summary>
                    <div style="padding: 20px; color: #64748b; border-top: 1px solid #f1f5f9;">انتقل لملفك الشخصي وارفع صورة واضحة للهوية الشخصية للمراجعة.</div>
                </details>
                <details class="glass-card" style="padding: 5px;">
                    <summary style="padding: 20px; font-weight: 700; cursor: pointer;">هل يمكنني تغيير تخصصي؟</summary>
                    <div style="padding: 20px; color: #64748b; border-top: 1px solid #f1f5f9;">نعم، من خلال مراسلة الإدارة عبر التذكرة الجانبية في هذه الصفحة.</div>
                </details>
            </div>
        </div>

        {{-- نموذج التذاكر --}}
        <aside>
            <div class="glass-card" style="padding: 35px; background: var(--primary); color: white; border: none;">
                <h3 style="margin-bottom: 15px;">أرسل تذكرة دعم</h3>
                <p style="font-size: 0.8rem; opacity: 0.8; margin-bottom: 25px;">سيرد عليك فريقنا التقني خلال 24 ساعة كحد أقصى.</p>
                <form id="ticketForm">
                    @csrf
                    <input type="text" name="subject" class="f-input" placeholder="موضوع المشكلة" style="background: rgba(255,255,255,0.1); color: white; border: none; margin-bottom: 15px;">
                    <textarea name="message" class="f-input" rows="5" placeholder="اشرح لنا المشكلة..." style="background: rgba(255,255,255,0.1); color: white; border: none;"></textarea>
                    <button type="button" onclick="sendTicket()" class="btn btn-primary" style="width: 100%; background: var(--accent); margin-top: 20px; padding: 15px; font-weight: 800;">إرسال التذكرة 🚀</button>
                </form>
            </div>
        </aside>
    </div>
</div>

<script>
    function sendTicket() {
        const data = new FormData(document.getElementById('ticketForm'));
        axios.post("{{ route('student.support.ticket') }}", data).then(res => {
            Swal.fire('تم!', res.data.title, 'success').then(() => location.reload());
        });
    }
</script>
@endsection
