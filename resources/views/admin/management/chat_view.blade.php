@extends('layouts.app')

@section('content')
<div class="chat-container">

    <!-- هيدر المحادثة -->
    <div class="chat-header">
        <div class="header-info">
            <a href="{{ route('admin.messages.index') }}" class="back-btn">←</a>
            <img src="{{ asset('storage/'.$student->photo) }}" class="student-avatar">
            <div>
                <h3 class="student-name">{{ $student->name_ar }}</h3>
                <span class="student-status">طالب في {{ $student->stage->label_ar }}</span>
            </div>
        </div>
        <div class="student-id">رقم الهوية: {{ $student->nid }}</div>
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
        <form id="replyForm" class="reply-form">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <input type="text" name="message" id="reply_input" placeholder="اكتب ردك الأكاديمي هنا..." required autocomplete="off">
            <button type="submit" class="btn-primary send-btn">
                <span class="btn-text">إرسال الرد 🚀</span>
                <span class="btn-icon">🚀</span>
            </button>
        </form>
    </div>
</div>

<style>
    /* الحاوية الرئيسية */
    .chat-container {
        height: calc(100vh - 160px);
        max-width: 1100px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        animation: fadeIn 0.8s ease;
        padding: 0 15px; /* هوامش جانبية للموبايل */
    }

    /* الهيدر */
    .chat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        background: white;
        padding: 15px 25px;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 10px;
    }

    .header-info { display: flex; align-items: center; gap: 15px; }
    .student-avatar { width: 45px; height: 45px; border-radius: 12px; object-fit: cover; }
    .student-name { margin: 0; font-size: 1.1rem; font-weight: 800; }
    .student-status { font-size: 0.75rem; color: #10b981; font-weight: 700; }
    .student-id { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }

    /* منطقة الرسائل */
    .chat-body {
        flex: 1;
        padding: 25px;
        overflow-y: auto;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 15px;
        border-radius: 20px;
        border: none;
    }

    .msg-box { max-width: 85%; display: flex; flex-direction: column; gap: 5px; }
    .msg-box.admin { align-self: flex-end; align-items: flex-end; }
    .msg-box.student { align-self: flex-start; align-items: flex-start; }

    .bubble { padding: 12px 20px; border-radius: 18px; font-size: 0.9rem; line-height: 1.5; word-break: break-word; }
    .admin .bubble { background: var(--primary); color: white; border-bottom-left-radius: 4px; }
    .student .bubble { background: white; color: var(--primary); border-bottom-right-radius: 4px; border: 1px solid #e2e8f0; }
    .time { font-size: 0.65rem; color: #94a3b8; font-weight: 700; }

    /* بار الرد */
    .reply-bar { padding: 20px 0; }
    .reply-form { display: flex; gap: 10px; }
    .reply-bar input {
        flex: 1;
        padding: 15px 20px;
        border-radius: 15px;
        border: 1px solid #e2e8f0;
        font-family: inherit;
        font-weight: 600;
        outline: none;
        transition: 0.3s;
        font-size: 0.9rem;
    }
    .reply-bar input:focus { border-color: var(--accent); box-shadow: 0 5px 15px rgba(16, 185, 129, 0.05); }

    .send-btn { white-space: nowrap; padding: 0 25px; border-radius: 15px; }
    .btn-icon { display: none; }

    .back-btn { width: 35px; height: 35px; background: #f1f5f9; border-radius: 10px; display: grid; place-items: center; color: var(--primary); text-decoration: none; font-weight: 900; }

    /* استعلامات الوسائط (Responsive) */
    @media (max-width: 768px) {
        .chat-container { height: calc(100vh - 120px); }
        .chat-header { padding: 15px; }
        .student-id { width: 100%; border-top: 1px solid #f1f5f9; pt-10px; padding-top: 10px; }
        .chat-body { padding: 15px; }
        .msg-box { max-width: 90%; }
        .bubble { padding: 10px 15px; font-size: 0.85rem; }
        .btn-text { display: none; }
        .btn-icon { display: block; font-size: 1.2rem; }
        .send-btn { padding: 0 15px; }
    }
</style>

<script>
    // وظيفة السكرول لأسفل عند التحميل
    const chatBody = document.getElementById('chat_scroll');
    chatBody.scrollTop = chatBody.scrollHeight;

    document.getElementById('replyForm').onsubmit = function(e) {
        e.preventDefault();
        const input = document.getElementById('reply_input');
        if(!input.value.trim()) return;

        axios.post("{{ route('admin.messages.send') }}", new FormData(this))
        .then(res => {
            const html = `
                <div class="msg-box admin">
                    <div class="bubble">${res.data.message}</div>
                    <span class="time">${res.data.time}</span>
                </div>`;
            chatBody.insertAdjacentHTML('beforeend', html);
            input.value = '';
            chatBody.scrollTop = chatBody.scrollHeight;
        });
    };
</script>
@endsection
