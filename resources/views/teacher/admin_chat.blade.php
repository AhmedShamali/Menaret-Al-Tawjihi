@extends('layouts.app')

@section('content')
<style>
    .chat-main-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 150px);
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #edf2f7;
        margin: 10px;
        overflow: hidden;
    }
    .chat-nav {
        padding: 15px 25px;
        background: #fff;
        border-bottom: 2px solid #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
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
    .teacher-msg {
        align-self: flex-end;
        background: #0284c7;
        color: white;
        border-radius: 18px 18px 2px 18px;
    }
    .admin-msg {
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
        background: #0284c7;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
    }
</style>

<div class="container-fluid" dir="rtl">
    <div class="chat-main-wrapper">
        <div class="chat-nav">
            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user-shield"></i> مراسلة الإدارة العامة</h6>
        </div>

        <div class="chat-feed" id="chat_messages">
            <div class="text-center my-auto py-5 text-muted">
                <p>ابدأ المحادثة مع الإدارة...</p>
            </div>
        </div>

        <div class="chat-input-bar">
            <div class="input-group-custom">
                <input type="text" id="msg_input" placeholder="اكتب رسالتك للإدارة..." onkeypress="if(event.key === 'Enter') sendReply()">
                <button class="send-trigger" onclick="sendReply()">إرسال</button>
            </div>
        </div>
    </div>
</div>

<script>
    const feed = document.getElementById('chat_messages');

    function fetchMessages() {
        axios.get("{{ route('teacher.admin.chat.messages') }}")
            .then(res => {
                const messages = res.data.messages || [];
                if(messages.length === 0) return;

                let html = '';
                messages.forEach(m => {
                    let isMe = m.sender_type === 'teacher';
                    html += `
                        <div class="bubble ${isMe ? 'teacher-msg' : 'admin-msg'}">
                            ${m.message}
                            <span class="msg-date">${m.created_at_formatted}</span>
                        </div>`;
                });

                if(feed.innerHTML !== html) {
                    feed.innerHTML = html;
                    feed.scrollTop = feed.scrollHeight;
                }
            })
            .catch(err => console.error('Fetch error:', err));
    }

    function sendReply() {
        const input = document.getElementById('msg_input');
        if(!input.value.trim()) return;

        let txt = input.value;
        input.value = '';

        // إضافة الرسالة محلياً فوراً لسرعة الاستجابة (Optimistic Update)
        const timeNow = "الآن";
        const newBubble = `
            <div class="bubble teacher-msg">
                ${txt}
                <span class="msg-date">${timeNow}</span>
            </div>`;

        // إذا كان هناك رسالة "ابدأ المحادثة"، قم بإزالتها أولاً
        if(feed.querySelector('.text-muted')) {
            feed.innerHTML = '';
        }

        feed.innerHTML += newBubble;
        feed.scrollTop = feed.scrollHeight;

        // إرسالها للسيرفر في الخلفية
        axios.post("{{ route('teacher.admin.chat.send') }}", {
            message: txt,
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            // يمكن استدعاء fetchMessages لضمان المزامنة
            fetchMessages();
        })
        .catch(err => {
            console.error('Send error:', err);
            alert('حدث خطأ أثناء إرسال الرسالة');
        });
    }

    fetchMessages();
    setInterval(fetchMessages, 4000);
</script>
@endsection
