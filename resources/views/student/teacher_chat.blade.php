@extends('layouts.app')

@section('content')
<style>
    /* إزالة أي قيود على العرض لجعل الشات يملأ المساحة المتاحة */
    .chat-main-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 150px); /* ليأخذ طول الشاشة المتاح */
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #edf2f7;
        margin: 10px;
        overflow: hidden;
    }

    /* هيدر احترافي بدون تداخل */
    .chat-nav {
        padding: 15px 25px;
        background: #fff;
        border-bottom: 2px solid #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: space-between; /* يوزع العناصر على الأطراف */
    }

    .teacher-meta {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .teacher-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .status-badge {
        font-size: 11px;
        color: #2dce89;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .status-badge::before {
        content: '';
        width: 8px;
        height: 8px;
        background: #2dce89;
        border-radius: 50%;
    }

    /* منطقة الرسائل - تملأ الوسط */
    .chat-feed {
        flex: 1;
        padding: 30px;
        background: #fdfdfd;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .bubble {
        max-width: 80%;
        padding: 12px 18px;
        font-size: 0.95rem;
        line-height: 1.5;
        position: relative;
    }

    .student-msg {
        align-self: flex-end;
        background: #4361ee;
        color: white;
        border-radius: 18px 18px 2px 18px;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.1);
    }

    .teacher-msg {
        align-self: flex-start;
        background: #fff;
        color: #2d3748;
        border: 1px solid #e2e8f0;
        border-radius: 18px 18px 18px 2px;
    }

    .msg-date {
        font-size: 10px;
        display: block;
        margin-top: 5px;
        opacity: 0.6;
    }

    /* صندوق الإدخال في الأسفل */
    .chat-input-bar {
        padding: 20px 30px;
        background: #fff;
        border-top: 2px solid #f8f9fa;
    }

    .input-group-custom {
        display: flex;
        align-items: center;
        background: #f1f4f8;
        border-radius: 15px;
        padding: 5px 15px;
    }

    .input-group-custom input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 12px;
        outline: none;
        font-size: 15px;
    }

    .send-trigger {
        background: #4361ee;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .send-trigger:hover {
        background: #3651d1;
    }

    .close-chat-btn {
        text-decoration: none;
        color: #a0aec0;
        font-size: 13px;
        border: 1px solid #e2e8f0;
        padding: 5px 15px;
        border-radius: 8px;
        transition: 0.2s;
    }

    .close-chat-btn:hover {
        background: #fff5f5;
        color: #e53e3e;
        border-color: #feb2b2;
    }
</style>

<div class="container-fluid" dir="rtl">
    <div class="chat-main-wrapper">

        <!-- الهيدر: تم ترتيبه لمنع التداخل -->
        <div class="chat-nav">
            <div class="teacher-meta">
                <div class="teacher-icon">{{ mb_substr($teacher->name, 0, 1) }}</div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">{{ $teacher->name }}</h6>
                    <div class="status-badge">متاح الآن</div>
                </div>
            </div>
            <a href="{{ route('student.teachers.index') }}" class="close-chat-btn">
                إنهاء المحادثة
            </a>
        </div>

        <!-- منطقة الرسائل -->
        <div class="chat-feed" id="chat_messages">
            <div class="text-center my-auto py-5 text-muted">
                <img src="https://cdn-icons-png.flaticon.com/512/5962/5962463.png" width="60" style="opacity: 0.3" class="mb-3">
                <p>ابدأ بمراسلة معلمك الآن..</p>
            </div>
        </div>

        <!-- شريط الإدخال -->
        <div class="chat-input-bar">
            <div class="input-group-custom">
                <input type="text" id="msg_input" placeholder="اكتب رسالتك هنا..." onkeypress="if(event.key === 'Enter') sendReply()">
                <button class="send-trigger" onclick="sendReply()">إرسال</button>
            </div>
        </div>

    </div>
</div>

<script>
    const teacherId = {{ $teacher->id }};
    const feed = document.getElementById('chat_messages');

    function fetchMessages() {
        axios.get(`/student/teachers/${teacherId}/messages`)
            .then(res => {
                const messages = res.data.messages || [];
                if(messages.length === 0) return;

                let html = '';
                messages.forEach(m => {
                    let isMe = m.sender_type === 'student';
                    html += `
                        <div class="bubble ${isMe ? 'student-msg' : 'teacher-msg'}">
                            ${m.message}
                            <span class="msg-date">${m.created_at_formatted}</span>
                        </div>`;
                });

                if(feed.innerHTML !== html) {
                    feed.innerHTML = html;
                    feed.scrollTop = feed.scrollHeight;
                }
            });
    }

    function sendReply() {
        const input = document.getElementById('msg_input');
        if(!input.value.trim()) return;

        let txt = input.value;
        input.value = '';

        axios.post('/student/teachers/send', {
            teacher_id: teacherId,
            message: txt,
            _token: '{{ csrf_token() }}'
        }).then(() => fetchMessages());
    }

    fetchMessages();
    setInterval(fetchMessages, 4000);
</script>
@endsection
