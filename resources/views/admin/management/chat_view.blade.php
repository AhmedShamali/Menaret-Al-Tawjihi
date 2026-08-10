@extends('layouts.app')
@section('content')
<div style="height: calc(100vh - 160px); max-width: 1100px; margin: 0 auto; display: flex; flex-direction: column; animation: fadeIn 0.8s ease;">

    <!-- هيدر المحادثة -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: white; padding: 20px 35px; border-radius: 20px; border: 1px solid #f1f5f9;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('admin.messages.index') }}" class="back-btn">←</a>
            <img src="{{ asset('storage/'.$student->photo) }}" style="width: 45px; height: 45px; border-radius: 12px; object-fit: cover;">
            <div>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800;">{{ $student->name_ar }}</h3>
                <span style="font-size: 0.75rem; color: #10b981; font-weight: 700;">طالب في {{ $student->stage->label_ar }}</span>
            </div>
        </div>
        <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">رقم الهوية: {{ $student->nid }}</div>
    </div>

    <!-- منطقة الرسائل -->
    <div class="glass-card chat-body" id="chat_scroll">
        @foreach($messages as $m)
            <div class="msg-box {{ $m->sender_type == 'admin' ? 'admin' : 'student' }}">
                <div class="bubble">{{ $m->message }}</div>
                <span class="time">{{ $m->created_at->format('H:i') }}</span>
            </div>
        @endforeach
    </div>

    <!-- بار الرد -->
    <div class="reply-bar">
        <form id="replyForm" style="display: flex; gap: 15px;">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <input type="text" name="message" id="reply_input" placeholder="اكتب ردك الأكاديمي هنا..." required autocomplete="off">
            <button type="submit" class="btn-primary">إرسال الرد 🚀</button>
        </form>
    </div>
</div>

<style>
    .chat-body { flex: 1; padding: 40px; overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column; gap: 15px; border: none; }
    .msg-box { max-width: 70%; display: flex; flex-direction: column; gap: 5px; }
    .msg-box.admin { align-self: flex-end; align-items: flex-end; }
    .msg-box.student { align-self: flex-start; align-items: flex-start; }

    .bubble { padding: 15px 25px; border-radius: 20px; font-size: 0.95rem; line-height: 1.6; }
    .admin .bubble { background: var(--primary); color: white; border-bottom-left-radius: 4px; }
    .student .bubble { background: white; color: var(--primary); border-bottom-right-radius: 4px; border: 1px solid #e2e8f0; }
    .time { font-size: 0.6rem; color: #94a3b8; font-weight: 800; }

    .reply-bar { padding: 25px 0; }
    .reply-bar input { flex: 1; padding: 18px 25px; border-radius: 18px; border: 1px solid #e2e8f0; font-family: inherit; font-weight: 600; outline: none; transition: 0.3s; }
    .reply-bar input:focus { border-color: var(--accent); box-shadow: 0 10px 25px rgba(16, 185, 129, 0.05); }
    .back-btn { width: 35px; height: 35px; background: #f1f5f9; border-radius: 10px; display: grid; place-items: center; color: var(--primary); text-decoration: none; font-weight: 900; }
</style>

<script>
    document.getElementById('replyForm').onsubmit = function(e) {
        e.preventDefault();
        const input = document.getElementById('reply_input');
        if(!input.value) return;

        axios.post("{{ route('admin.messages.send') }}", new FormData(this))
        .then(res => {
            const html = `
                <div class="msg-box admin">
                    <div class="bubble">${res.data.message}</div>
                    <span class="time">${res.data.time}</span>
                </div>`;
            document.getElementById('chat_scroll').insertAdjacentHTML('beforeend', html);
            input.value = '';
            document.getElementById('chat_scroll').scrollTop = document.getElementById('chat_scroll').scrollHeight;
        });
    };
</script>
@endsection
