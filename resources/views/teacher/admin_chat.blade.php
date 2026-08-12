@extends('layouts.app')

@section('content')
<!-- إضافة FontAwesome للأيقونات -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --admin-blue: #0284c7;
        --admin-hover: #0369a1;
        --light-bg: #f8fafc;
        --border-color: #e2e8f0;
    }

    .chat-main-wrapper {
        display: flex;
        flex-direction: column;
        /* ارتفاع ذكي يراعي الجوال والديسك توب */
        height: 80vh;
        height: calc(100dvh - 120px);
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid var(--border-color);
        margin: 10px auto;
        max-width: 1000px; /* تحديد عرض أقصى للديسك توب */
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .chat-nav {
        padding: 15px 20px;
        background: #fff;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .chat-nav h6 {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        color: #1e293b;
    }

    .chat-nav i {
        color: var(--admin-blue);
    }

    .chat-feed {
        flex: 1;
        padding: 20px;
        background: var(--light-bg);
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        scrollbar-width: thin;
    }

    .bubble {
        max-width: 85%;
        padding: 10px 16px;
        font-size: 0.92rem;
        line-height: 1.5;
        position: relative;
        word-wrap: break-word;
    }

    /* رسائل المعلم (أنا) - تظهر جهة اليمين باللون الأزرق */
    .teacher-msg {
        align-self: flex-end;
        background: var(--admin-blue);
        color: white;
        border-radius: 18px 18px 2px 18px;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
    }

    /* رسائل الإدارة - تظهر جهة اليسار باللون الأبيض */
    .admin-msg {
        align-self: flex-start;
        background: #fff;
        color: #1e293b;
        border: 1px solid var(--border-color);
        border-radius: 18px 18px 18px 2px;
    }

    .msg-date {
        font-size: 9px;
        display: block;
        margin-top: 4px;
        opacity: 0.7;
        text-align: left;
    }
    .teacher-msg .msg-date { text-align: right; color: #e0f2fe; }

    .chat-input-bar {
        padding: 15px 20px;
        background: #fff;
        border-top: 2px solid #f1f5f9;
        flex-shrink: 0;
    }

    .input-group-custom {
        display: flex;
        align-items: center;
        background: #f1f5f9;
        border-radius: 25px;
        padding: 5px 10px 5px 5px;
        gap: 8px;
    }

    .input-group-custom input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 10px 15px;
        outline: none;
        font-size: 0.95rem;
    }

    .send-trigger {
        background: var(--admin-blue);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
        flex-shrink: 0;
    }

    .send-trigger:hover {
        background: var(--admin-hover);
        transform: scale(1.05);
    }

    /* === Responsive Rules === */
    @media (max-width: 768px) {
        .chat-main-wrapper {
            margin: 0;
            border-radius: 0;
            height: calc(100dvh - 70px); /* ملء الشاشة في الجوال */
            border: none;
        }

        .chat-feed {
            padding: 15px;
        }

        .bubble {
            max-width: 90%;
            font-size: 0.9rem;
        }

        .chat-input-bar {
            padding: 10px;
        }

        .chat-nav h6 {
            font-size: 0.9rem;
        }
    }

    /* تحسين شكل السكرول بار */
    .chat-feed::-webkit-scrollbar { width: 5px; }
    .chat-feed::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<div class="container-fluid p-0" dir="rtl">
    <div class="chat-main-wrapper">
        <!-- الهيدر -->
        <div class="chat-nav">
            <h6 class="fw-bold mb-0">
                <i class="fas fa-shield-halved"></i>
                مراسلة الإدارة العامة
            </h6>
            <div class="status-indicator d-flex align-items-center gap-2">
                <span style="font-size: 11px; color: #64748b;">الرد غالباً خلال ساعات</span>
                <div style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%;"></div>
            </div>
        </div>

        <!-- منطقة الرسائل -->
        <div class="chat-feed" id="chat_messages">
            <div class="text-center my-auto py-5 text-muted">
                <div class="mb-3">
                    <i class="fas fa-comments-alt fa-3x" style="opacity: 0.2;"></i>
                </div>
                <p>ابدأ المحادثة مع الإدارة...</p>
            </div>
        </div>

        <!-- شريط الإدخال -->
        <div class="chat-input-bar">
            <div class="input-group-custom">
                <input type="text" id="msg_input" placeholder="اكتب رسالتك للإدارة..." onkeypress="if(event.key === 'Enter') sendReply()">
                <button class="send-trigger" onclick="sendReply()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const feed = document.getElementById('chat_messages');
    const msgInput = document.getElementById('msg_input');
    let lastContent = "";

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
                            ${escapeHtml(m.message)}
                            <span class="msg-date">${m.created_at_formatted}</span>
                        </div>`;
                });

                if(lastContent !== html) {
                    // التحقق إذا كان المستخدم في أسفل الشات قبل التحديث
                    const isAtBottom = feed.scrollHeight - feed.scrollTop <= feed.clientHeight + 100;

                    feed.innerHTML = html;
                    lastContent = html;

                    if(isAtBottom) {
                        feed.scrollTop = feed.scrollHeight;
                    }
                }
            })
            .catch(err => console.error('Fetch error:', err));
    }

    function sendReply() {
        const text = msgInput.value.trim();
        if(!text) return;

        msgInput.value = '';

        // إضافة الرسالة فوراً (Optimistic Update)
        const timeNow = "الآن";
        const newBubble = `
            <div class="bubble teacher-msg">
                ${escapeHtml(text)}
                <span class="msg-date">${timeNow}</span>
            </div>`;

        if(feed.querySelector('.text-muted')) {
            feed.innerHTML = '';
        }

        feed.innerHTML += newBubble;
        feed.scrollTop = feed.scrollHeight;

        axios.post("{{ route('teacher.admin.chat.send') }}", {
            message: text,
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            fetchMessages();
        })
        .catch(err => {
            console.error('Send error:', err);
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    fetchMessages();
    setInterval(fetchMessages, 5000);
</script>
@endsection
