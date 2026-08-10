@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto; text-align: center; animation: fadeIn 0.8s ease;">

    <div style="margin-bottom: 50px;">
        <div style="font-size: 4rem; margin-bottom: 20px;">📡</div>
        <h1 style="font-size: 2.5rem; font-weight: 900; color: var(--primary);">قنوات التواصل التفاعلية</h1>
        <p style="color: #64748b; font-size: 1.1rem; max-width: 600px; margin: 0 auto;">انضم لزملائك في القنوات الرسمية لمرحلتك الدراسية تحت إشراف المدرسين.</p>
    </div>

    <div class="glass-card" style="padding: 50px; border: none; background: white;">
        <div style="background: #f8fafc; padding: 30px; border-radius: 25px; margin-bottom: 35px;">
            <h3 style="color: var(--primary); margin-bottom: 10px;">قناتك المخصصة:</h3>
            <div style="font-size: 1.8rem; font-weight: 900; color: var(--accent);">
                قناة {{ $student->stage->label_ar }} - {{ $student->gender == 'ذكر' ? 'طلاب' : 'طالبات' }}
            </div>
            <p style="font-size: 0.85rem; color: #94a3b8; margin-top: 10px;">* يتم فصل القنوات تماماً لضمان بيئة تعليمية آمنة وخصوصية كاملة.</p>
        </div>

        @if($request_exists)
            <div style="padding: 20px; background: #ecfdf5; color: #059669; border-radius: 15px; font-weight: 700;">
                ✅ لقد قمت بإرسال طلب انضمام مسبقاً، سيتم تزويدك بالرابط فور موافقة المدرس.
            </div>
        @else
            <button onclick="requestJoin()" id="joinBtn" class="btn btn-primary" style="padding: 20px 60px; font-size: 1.2rem; border-radius: 20px;">
                إرسال طلب انضمام للقناة 🚀
            </button>
        @endif
    </div>
</div>

<script>
    function requestJoin() {
        axios.post("{{ route('student.channels.join') }}").then(res => {
            Swal.fire({ icon: res.data.icon, title: res.data.title }).then(() => location.reload());
        });
    }
</script>
@endsection
